<?php

// #######################################################################################################
// Add Topic
// #######################################################################################################
function forum_add_topic(&$sqlm)
{
	global 	$enablesidecheck,
			$forum_lang,
			$forum_skeleton, $maxqueries, $minfloodtime,
			$user_lvl, $user_id,
			$output,
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
				error($forum_lang["please_wait"]);
		}
	}

//==========================$_GET and SECURE=================================
	if(!isset($_GET["id"]))
		error($forum_lang['no_such_forum']);
	else
		$id = $sqlm->quote_smart($_GET['id']);
//==========================$_GET and SECURE end=============================

	$cat = 0;
	foreach($forum_skeleton as $cid => $category)
	{
		foreach($category['forums'] as $fid => $forum)
		{
			if($fid == $id) $cat = $cid;
		}
	}

	if(empty($forum_skeleton[$cat]['forums'][$id]))
		error($forum_lang['no_such_forum']);
	$forum = $forum_skeleton[$cat]['forums'][$id];
	if($forum_skeleton[$cat]['level_post_topic'] > $user_lvl || $forum['level_post_topic'] > $user_lvl)
		error($forum_lang['no_access']);

	if($user_lvl == 0 && $enablesidecheck)
	{
		if($forum_skeleton[$cat]['side_access'] != 'ALL')
		{ // Not an all side forum
			if($side == 'NO') // No char
				continue;
			else if($forum_skeleton[$cat]['side_access'] != $side) // Forumside different of the user side
				continue;
		}
		if($forum['side_access'] != 'ALL')
		{ // Not an all side forum
			if($side == 'NO') // No char
				continue;
			else if($forum['side_access'] != $side) // Forumside different of the user side
				continue;
		}
	}

$output .= '
<div class="top">
	<h1>'.$forum_lang['forums'].'</h1>
</div>
<center>
<fieldset>
	<legend>
			<a href="forum.php">'.$forum_lang['forum_index'].'</a> ->
			<a href="forum.php?action=view_forum&amp;id='.$id.'">'.$forum['name'].'</a> ->
			'.$forum_lang["new_topic"].'
	</legend>';

$output .= '
<form action="forum.php?action=do_add_topic" method="POST" name="form">
<table class="lined">
	<tr>
		<td align="left">'.$forum_lang['topic_name'].': <input name="name" SIZE="40">
		</td>
	</tr>';
$output .= '
	<tr>
		<td align="left" colspan="3">';
			bbcode_add_editor();
$output .= '
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<TEXTAREA ID="msg" NAME="msg" ROWS=8 COLS=93></TEXTAREA><br/>
			<input type="hidden" name="forum" value="'.$id.'">
		</td>
	</tr>
	<tr>
		<td align="left">';
			makebutton($forum_lang['post'], "javascript:do_submit()",100);
$output .= '
		</td>
	</tr>
</table>
</form>
</fieldset>';
$output .= '
</center>
<br/>';
$sqlm->close();
// Queries : 1
}


// #######################################################################################################
// Do Add Topic
// #######################################################################################################
function forum_do_add_topic(&$sqlm)
{
	global 	$enablesidecheck, $forum_skeleton,
			$forum_lang,
			$user_lvl, $user_name, $user_id,
			$mmfpm_db,
			$minfloodtime;

	if($enablesidecheck)
		$side = get_side(); // Better to use it here instead of call it many time in the loop :)

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

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
			if($fid == $forum)
				$cat = $cid;
		}
	}
	if(empty($forum_skeleton[$cat]['forums'][$forum]))
		error($forum_lang['no_such_forum']);
	$forum_ = $forum_skeleton[$cat]['forums'][$forum];
	if($forum_skeleton[$cat]['level_post_topic'] > $user_lvl || $forum_['level_post_topic'] > $user_lvl)
		error($forum_lang['no_access']);

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
	//  $_POST['msg'] = htmlspecialchars($_POST['msg']);
	$msg = trim($sqlm->quote_smart($_POST['msg']), " ");
	//  $_POST['name'] = htmlspecialchars($_POST['name']);
	$name = trim($sqlm->quote_smart($_POST['name']), " ");
//==========================$_POST and SECURE end=============================

	if (strlen($name) > 49)
	{
	$sqlm->close();
		error($forum_lang['name_too_long']);
	}

	if (strlen($name) < 5)
	{
	$sqlm->close();
		error($forum_lang['name_too_short']);
	}

	if (strlen($msg) < 5)
	{
	$sqlm->close();
		error($forum_lang['msg_too_short']);
	}

	$msg = str_replace('\n', '<br />', $msg);
	//  $msg = str_replace('\r', '<br />', $msg);

	$time = date("m/d/y H:i:s");

	$sqlm->query('
		INSERT INTO mm_forum_posts
			(authorid, authorname, forum, name, text, time)
		VALUES
			(\''.$user_id.'\', \''.$user_name.'\', \''.$forum.'\', \''.$name.'\', \''.$msg.'\', \''.$time.'\')');
	$id = $sqlm->insert_id();
	$sqlm->query('
		UPDATE mm_forum_posts
		SET topic = '.$id.', lastpost = '.$id.'
		WHERE id = '.$id.'');

	$sqlm->close();

	redirect('forum.php?action=view_topic&id='.$id.'');
	// Queries : 3
}

?>
