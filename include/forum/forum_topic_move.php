<?php

// #######################################################################################################
// Move Topic
// #######################################################################################################
function forum_move_topic(&$sqlm)
{
	global 	$forum_skeleton,
			$forum_lang,
			$maxqueries,
			$user_lvl, $user_id,
			$output,
			$mmfpm_db;


$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if(!isset($_GET['id']))
		error($forum_lang['no_such_topic']);
	else
		$id = $sqlm->quote_smart($_GET['id']);

	$topic = $sqlm->query('
		SELECT id, topic, authorid, forum, name
		FROM mm_forum_posts
		WHERE id = '.$id.'');

	if($sqlm->num_rows($topic)==0)
		error($forum_lang['no_such_topic']);
	$topic = $sqlm->fetch_assoc($topic);
	if($user_lvl == 0)
		error($forum_lang['no_access']);

	$fid = $topic['forum'];

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
	$forum = $forum_skeleton[$cat]["forums"][$fid];

$output .= '
<div class="top">
	<h1>'.$forum_lang['forums'].'</h1>
</div>
<center>
<table class="flat">
	<tr>
		<td align="left">
			<a href="forum.php">'.$forum_lang['forum_index'].'</a> -> 
			<a href="forum.php?action=view_forum&amp;id='.$fid.'">'.$forum['name'].'</a> -> 
			<a href="forum.php?action=view_topic&amp;id='.$topic['topic'].'">'.$topic['name'].'</a> -> 
			'.$forum_lang["move"].'!
		</td>
	</tr>
</table>
<table class="lined">
	<tr>
		<td>'.$forum_lang['where'].' : 
		<form action="forum.php?action=do_move_topic" method="POST" name="form">
			<select name="forum">';

	foreach($forum_skeleton as $category)
	{
		foreach($category['forums'] as $fid_ => $forum)
		{
			if($fid_ != $fid)
$output .= '
				<option value='.$fid_.'>'.$forum['name'].'</option>';
			else
$output .= '
				<option value='.$fid_.' selected>'.$forum['name'].'</option>';
		}
	}

$output .= '
			</select>
		<input type="hidden" name="id" value="'.$id.'">
		</form>
		</td>
	</tr>
</table>
<table class="hidden">
	<tr>
		<td>';
			makebutton($forum_lang['back'], "javascript:window.history.back()", 120);
			makebutton($forum_lang['confirm'], "javascript:do_submit()", 120);
$output .= '
		</td>
	</tr>
</table>
</center>';
$sqlm->close();
// Queries : 1
}

// #######################################################################################################
// Do Move Topic
// #######################################################################################################
function forum_do_move_topic(&$sqlm)
{
	global 	$forum_lang, 
			$forum_skeleton, $maxqueries,
			$user_lvl, $user_id,
			$output,
			$mmfpm_db;

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

//==========================$_POST and SECURE=================================
	if(!isset($_POST['forum']))
		error($forum_lang['no_such_forum']);
	else
		$forum = $sqlm->quote_smart($_POST['forum']);
	if(!isset($_POST['id']))
		error($forum_lang["no_such_topic"]);
	else
		$id = $sqlm->quote_smart($_POST['id']);
//==========================$_POST and SECURE end=============================

	$sqlm->query('
		UPDATE mm_forum_posts
		SET forum = '.$forum.'
		WHERE topic = '.$id.''); // update topic' s last post id
	redirect('forum.php?action=view_topic&id='.$id.'');
// Queries : 1
}

?>
