<?php

// #######################################################################################################
// Edit Post
// #######################################################################################################
function forum_edit_post(&$sqlm)
{
	global	$forum_skeleton,
			$forum_lang,
			$maxqueries, $minfloodtime,
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

	$post = $sqlm->query('
		SELECT id, topic, authorid, forum, name, text
		FROM mm_forum_posts
		WHERE id = '.$id.'');
	if($sqlm->num_rows($post)==0)
		error($forum_lang['no_such_post']);
	$post = $sqlm->fetch_assoc($post);

	if($user_lvl == 0 && $user_id != $post['authorid'])
		error($forum_lang['no_access']);

	$cat = 0;
	foreach($forum_skeleton as $cid => $category)
	{
		foreach($category["forums"] as $fid_ => $forum)
		{
			if($fid_ == $post['forum']) $cat = $cid;
		}
	}
	if(empty($forum_skeleton[$cat]['forums'][$post['forum']])) // No such forum..
		error($forum_lang['no_such_forum']);
	$forum = $forum_skeleton[$cat]['forums'][$post['forum']];

$output .= '
<div class="top">
	<h1>'.$forum_lang['forums'].'</h1>
</div>
<form action="forum.php?action=do_edit_post" method="POST" name="form">
<center>
<fieldset>
	<legend>
		<a href="forum.php">'.$forum_lang['forum_index'].'</a> -> 
		<a href="forum.php?action=view_forum&amp;id='.$post['forum'].'">'.$forum['name'].'</a> -> 
		<a href="forum.php?action=view_topic&amp;id='.$post['topic'].'">'.$post['name'].'</a> -> 
		'.$forum_lang['edit'].'
	</legend>';

$output .= '
<table class="lined">
	<tr>';
	if($post['id'] = $post['id'])
$output .= '
		<td align="left"><input type="hidden" name="topic" value="1">
			'.$forum_lang['topic_name'].': <input name="name" SIZE="50" value="'.$post['name'].'">
		</td>
	</tr>';
	else
$output .= '
		</td>
		<td align="left">'.$post['name'].'</td>
	</tr>';

	$post['text'] = str_replace('<br />', chr(10), $post['text']);

$output .= '
	<tr>
		<td align="left" colspan="3">';
			bbcode_add_editor();
$output .= '
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<TEXTAREA ID="msg" NAME="msg" ROWS=8 COLS=93>'.$post['text'].'</TEXTAREA>
			<input type="hidden" name="forum" value="'.$post['forum'].'">
			<input type="hidden" name="post" value="'.$post['id'].'">
		</td>
	</tr>
	<tr>
		<td align="left">';
			makebutton($forum_lang['post'], "javascript:do_submit()",100);
$output .= '
		</td>
	</tr>
</table>
</fieldset>';

$output .= '
</center>
</form>
<br/>';
$sqlm->close();
// Queries : 1
}

// #######################################################################################################
// Do Edit Post
// #######################################################################################################
function forum_do_edit_post(&$sqlm)
{
	global 	$forum_lang,
			$user_lvl, $user_name, $user_id,
			$mmfpm_db;

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

//==========================$_POST and SECURE=================================
	if(!isset($_POST['forum']))
		error($forum_lang["no_such_forum"]);
	else
		$forum = $sqlm->quote_smart($_POST['forum']);
	if(!isset($_POST['post']))
		error($forum_lang["no_such_post"]);
	else
		$post = $sqlm->quote_smart($_POST['post']);

	if(!isset($_POST['name']))
		$topic = 0;
	else
	{
		$topic = 1;
		//    htmlspecialchars($_POST['name']);
		$name = $sqlm->quote_smart($_POST['name']);
		if (strlen($name) > 49)
		{
			$sqlm->close();
				error($forum_lang["name_too_long"]);
		}
		if (strlen($name) < 5)
		{
			$sqlm->close();
				error($forum_lang["name_too_short"]);
		}
	}

	//  $_POST['msg'] = htmlspecialchars($_POST['msg']);
	$msg = trim($sqlm->quote_smart($_POST['msg']), " ");

	if (strlen($msg) < 5)
	{
		$sqlm->close();
			error($forum_lang["msg_too_short"]);
	}
//==========================$_POST and SECURE end==============================

	$msg = str_replace('\n', '<br />', $msg);
	//  $msg = str_replace('\r', '<br />', $msg);

	$result = $sqlm->query('
		SELECT topic
		FROM mm_forum_posts
		WHERE id = '.$post.'');
	$topicid = $sqlm->fetch_assoc($result);

	$sqlm->query('
		UPDATE mm_forum_posts
		SET text = \''.$msg.'\'
		WHERE id = '.$post.'');

	if($topic == 1)
	{
		$sqlm->query('
			UPDATE mm_forum_posts
			SET name = \''.$name.'\'
			WHERE topic = '.$topicid['topic'].'');
	}

	$result = $sqlm->query('
		SELECT topic
		FROM mm_forum_posts
		WHERE id = '.$post.'');
	$topicid = $sqlm->fetch_assoc($result);

	$sqlm->close();
		redirect('forum.php?action=view_topic&id='.$topicid['topic'].'');
	// Queries : 3 (+1 if topic)
}

?>
