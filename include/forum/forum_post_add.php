<?php

// #######################################################################################################
// Add Post
// #######################################################################################################
function forum_do_add_post(&$sqlm)
{
	global 	$enablesidecheck, $forum_skeleton,
			$forum_lang,
			$minfloodtime,
			$user_lvl, $user_name, $user_id,
			$mmfpm_db;

	if($enablesidecheck)
		$side = get_side(); // Better to use it here instead of call it many time in the loop :)

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if($minfloodtime > 0)
	{
		$userposts = $sqlm->query('
			SELECT time 
			FROM mm_forum_posts 
			WHERE authorid = '.$user_id.' 
			ORDER BY id DESC
			LIMIT 1');
	if($sqlm->num_rows($userposts) != 0)
	{
		$mintimeb4post = $sqlm->fetch_assoc($userposts);
		$mintimeb4post = time() - strtotime($mintimeb4post['time']);

		if($mintimeb4post < $minfloodtime)
			error($forum_lang['please_wait']);
	}
}
//==========================$_POST and SECURE=================================
	if(!isset($_POST['forum']))
		error($forum_lang['no_such_forum']);
	else
		$forum = $sqlm->quote_smart($_POST['forum']);
//==========================$_POST and SECURE end=============================

	$cat = 0;
	foreach($forum_skeleton as $cid => $category)
	{
		foreach($category['forums'] as $fid => $forum_)
		{
			if($fid == $forum) $cat = $cid;
		}
	}

	if(empty($forum_skeleton[$cat]['forums'][$forum]))
		error($forum_lang['no_such_forum']);
	$forum_ = $forum_skeleton[$cat]['forums'][$forum];
	if((($user_lvl > 0)||!$closed)&&($forum_skeleton[$cat]['level_post'] > $user_lvl || $forum_['level_post'] > $user_lvl)) error($forum_lang['no_access']);

	if($user_lvl == 0 && $enablesidecheck)
	{
		if($forum_skeleton[$cat]['side_access'] != 'ALL')
		{ // Not an all side forum
			if($side == 'NO') // No char
				continue;
			else if($forum_skeleton[$cat]['side_access'] != $side) // Forumside different of the user side
				continue;
		}
		if($forum_['side_access'] != 'ALL')
		{ // Not an all side forum
			if($side == 'NO') // No char
				continue;
			else if($forum_['side_access'] != $side) // Forumside different of the user side
				continue;
		}
	}

//==========================$_POST and SECURE=================================
	if(!isset($_POST['topic']))
		error($forum_lang['no_such_topic']);
	else
		$topic = $sqlm->quote_smart($_POST['topic']);

	//  $_POST['msg'] = htmlspecialchars($_POST['msg']);
	$msg = trim($sqlm->quote_smart($_POST['msg']), " ");
//==========================$_POST and SECURE end=============================

	$msg = str_replace('\n', '<br />', $msg);
	//  $msg = str_replace('\r', '<br />', $msg);

	if (strlen($msg) < 5)
	{
		$sqlm->close();
			error($forum_lang['msg_too_short']);
	}

	$name = $sqlm->query('
		SELECT name
		FROM mm_forum_posts
		WHERE id = '.$topic.'');
	$name = $sqlm->fetch_row($name);
	$name = $sqlm->quote_smart($name[0]);

	$time = date("m/d/y H:i:s");

	$sqlm->query('
		INSERT INTO mm_forum_posts
			(authorid, authorname, forum, topic, name, text, time)
		VALUES
			(\''.$user_id.'\', \''.$user_name.'\', \''.$forum.'\', \''.$topic.'\', \''.$name.'\', \''.$msg.'\', \''.$time.'\')');
	$id = $sqlm->insert_id();
	$sqlm->query('
		UPDATE mm_forum_posts
		SET lastpost = '.$id.'
		WHERE id = '.$topic.'');

	$sqlm->close();

		redirect('forum.php?action=view_topic&id='.$topic.'');
	// Queries : 4
}

?>
