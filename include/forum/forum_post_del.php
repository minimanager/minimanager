<?php

// #######################################################################################################
// Delete Post
// #######################################################################################################
function forum_delete_post(&$sqlm)
{
	global	$enablesidecheck, $forum_skeleton,
			$forum_lang,
			$maxqueries,
			$user_lvl, $user_id,
			$output,
			$mmfpm_db;

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

//==========================$_GET and SECURE=================================
	if(!isset($_GET['id']))
		error($forum_lang['no_such_post']);
	else
		$id = $sqlm->quote_smart($_GET['id']);
//==========================$_GET and SECURE end=============================

	$topic = $sqlm->query('
		SELECT id, topic, authorid, forum
		FROM mm_forum_posts
		WHERE id = '.$id.'');
	if($sqlm->num_rows($topic)==0)
		error($forum_lang['no_such_post']);
	$topic = $sqlm->fetch_assoc($topic);
	if($user_lvl == 0 && $topic['authorid'] != $user_id)
		error($forum_lang["no_access"]);
	$fid = $topic['forum'];

	$topic2 = $sqlm->query('
		SELECT name
		FROM mm_forum_posts
		WHERE id = '.$topic['topic'].'');
	$name = $sqlm->fetch_assoc($topic2);

	$cat = 0;
	foreach($forum_skeleton as $cid => $category)
	{
		foreach($category['forums'] as $fid_ => $forum)
		{
			if($fid_ == $fid) $cat = $cid;
		}
	}

	if(empty($forum_skeleton[$cat]['forums'][$fid])) // No such forum..
		error($forum_lang['no_such_forum']);
	$forum = $forum_skeleton[$cat]['forums'][$fid];
$output .= '
<div class="top">
	<h1>'.$forum_lang['forums'].'</h1>
</div>
<center>
<table class="lined">';
	if($topic['id']==$topic['topic'])
$output .= '
	<tr>
		<td>'.$forum_lang['delete_topic'].'</td>
	</tr>
</table>
<table class="flat">
	<tr>
		<td align="left">
			<a href="forum.php">'.$forum_lang['forum_index'].'</a> ->
			<a href="forum.php?action=view_forum&amp;id='.$fid.'">'.$forum['name'].'</a> ->
			<a href="forum.php?action=view_topic&amp;id='.$topic['topic'].'">'.$name['name'].'</a> ->
			'.$forum_lang['delete'].'!
		</td>
	</tr>
</table>
<table class="hidden">
	<tr>
		<td>';
	else
$output .= '
	<tr>
		<td>'.$forum_lang['delete_post'].'</td>
	</tr>
</table>
<table width="300" class="hidden" align="center">
	<tr>
		<td>';
			makebutton($forum_lang['back'], "javascript:window.history.back()\" type=\"def",120);
			makebutton($forum_lang['confirm'], 'forum.php?action=do_delete_post&amp;id='.$topic['id'].'" type="wrn', 120);
$output .= '
		</td>
	</tr>
</table>
</center>';
  $sqlm->close();
  // Queries : 1
}

// #######################################################################################################
// Do Delete Post
// #######################################################################################################
function forum_do_delete_post(&$sqlm)
{
	global 	$forum_lang,
			$forum_skeleton, $maxqueries,
			$user_lvl, $user_id,
			$output,
			$mmfpm_db;

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

//==========================$_GET and SECURE=================================
	if(!isset($_GET['id']))
		error($forum_lang['no_such_post']);
	else
		$id = $sqlm->quote_smart($_GET['id']);
//==========================$_GET and SECURE end=============================

	$topic = $sqlm->query('
		SELECT id, topic, name, authorid, forum
		FROM mm_forum_posts
		WHERE id = '.$id.'');

	if($sqlm->num_rows($topic)==0) error($forum_lang['no_such_post']);
	$topic = $sqlm->fetch_assoc($topic);
	if($user_lvl == 0 && $topic['authorid'] != $user_id)
		error($forum_lang['no_access']);
	$fid = $topic['forum'];

	if($id==$topic['topic'])
	{
		$sqlm->query('
			DELETE FROM mm_forum_posts
			WHERE topic = '.$id.'');
		redirect('forum.php?action=view_forum&id='.$fid.'');
	}
	else
	{
	$sqlm->query('
		DELETE FROM mm_forum_posts
		WHERE id = '.$id.'');
	$result = $sqlm->query('
		SELECT id
		FROM mm_forum_posts
		WHERE topic = '.$topic['topic'].'
		ORDER BY id DESC LIMIT 1'); // get last post id
	$lastpostid = $sqlm->fetch_assoc($result);
	$lastpostid = $lastpostid['id'];
	$sqlm->query('
		UPDATE mm_forum_posts
		SET lastpost = '.$lastpostid.'
		WHERE id = '.$topic['topic'].''); // update topic' s last post id
		redirect('forum.php?action=view_topic&id='.$topic['topic'].'');
	}
	// Queries : 1 (if delete topic) || 4 if delete post
}

?>
