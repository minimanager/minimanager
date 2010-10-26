<?php

// #######################################################################################################
// Display each forums
// #######################################################################################################
function forum_view_forum(&$sqlm)
{
	global	$enablesidecheck, $forum_skeleton,  $maxqueries,
			$forum_lang,
			$user_lvl,
			$output,
			$mmfpm_db;

	if($enablesidecheck)
		$side = get_side();

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

//==========================$_GET and SECURE=================================
	if(!isset($_GET['id']))
		error($forum_lang['no_such_forum']);
	else 
		$id = $sqlm->quote_smart($_GET['id']);
	if(!isset($_GET['page']))
		$page = 0;
	else
		$page = $sqlm->quote_smart($_GET['page']);
	$cat = 0;
//==========================$_GET and SECURE end=============================

	$cid = $sqlm->query('
		SELECT category, name, description, side_access, level_post_topic, level_read, level_post
		FROM mm_forum_categories');

	while ($category = $sqlm->fetch_assoc($cid))
	{
		$fid = $sqlm->query('
			SELECT forum, category, name, description, side_access, level_post_topic, level_read, level_post
			FROM mm_forum_forums
			WHERE category = '.$category['category'].'');

		while ($forum = $sqlm->fetch_assoc($fid))
		{
			if($forum['forum'] == $id) $cat = $forum['category'];

			if(empty($forum['forum']))
				error($forum_lang['no_such_forum']);

			if(($category['level_read'] > $user_lvl) || ($forum['level_read'] > $user_lvl))
				error($forum_lang['no_access']);

			if($user_lvl == 0 && $enablesidecheck)
			{
				if($category['side_access'] != 'ALL')
				{ // Not an all side forum
					if($side == 'NO') // No char
						continue;
					else if($category['side_access'] != $side) // Forumside different of the user side
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
		}
	}
	$start = ($maxqueries * $page);
$output .= '
<div class="top">
	<h1>'.$forum_lang['forums'].'</h1>
</div>
';
	if($forum[$category]['level_post_topic'] <= $user_lvl && $forum['level_post_topic'] <= $user_lvl)
$output .= '
<table class="hidden">
	<tr>
		<td>';
			makebutton($forum_lang['new_topic'], 'forum.php?action=add_topic&amp;id='.$id.'" type="def' ,130);
$output .= '
		</td>
	</tr>
</table>
<center>
<fieldset>
	<legend>
		<a href="forum.php">'.$forum_lang['forum_index'].'</a> -> 
		<a href="forum.php">'.$category['name'].'</a> -> 
		<a href="forum.php?action=view_forum&amp;id='.$id.'">'.$forum['name'].'</a>
	</legend>';
	$topics = $sqlm->query('
		SELECT id, authorid, authorname, name, annouced, sticked, closed
		FROM mm_forum_posts
		WHERE (forum = '.$id.' AND id = topic) OR annouced = 1 AND id = topic
		ORDER BY annouced DESC, sticked DESC, lastpost DESC
		LIMIT '.$start.', '.$maxqueries.'');
	$result = $sqlm->query('
		SELECT topic as curtopic,
			(SELECT count(id)-1
			FROM mm_forum_posts
			WHERE topic = curtopic) AS replies,	lastpost as curlastpost,
			(SELECT authorname
			FROM mm_forum_posts
			WHERE id = curlastpost) as authorname,
			(SELECT time
			FROM mm_forum_posts
			WHERE id = curlastpost) as time
		FROM mm_forum_posts
		WHERE (forum = '.$id.' AND topic = id ) OR annouced = 1');
	$lasts = array();
	if($sqlm->num_rows($result) > 0)
	{
		while($row = $sqlm->fetch_assoc($result))
		$lasts[$row['curtopic']] = $row;
	}
	if($sqlm->num_rows($topics)!=0)
	{
$output .= '
	<table class="lined">
		<tr>
			<th style="width:35%;text-align:left;">'.$forum_lang['title'].'</th>
			<th style="width:15%;">'.$forum_lang['author'].'</th>
			<th>'.$forum_lang['replies'].'</th>
			<th>'.$forum_lang['last_post'].'</th>
		</tr>';
		while($topic = $sqlm->fetch_assoc($topics))
		{
$output .= '
		<tr>
			<td style="text-align:left;">';
			if($topic['annouced']=="1")
$output .= '
				<img src="img/forums/announce.png" border="0" alt="'.$forum_lang['annoucement'].'" /> : ';
			else
			{
				if($topic['sticked']=="1")
$output .= '
				<img src="img/forums/stick.png" border="0" alt="'.$forum_lang['sticky'].'" /> : ';
                else
				{
					if($topic['closed']=="1")
$output .= '
				<img src="img/forums/lock.png" border="0" alt="'.$forum_lang['closed'].'" /> : ';
				}
			}
			$topic['name'] = htmlspecialchars($topic['name']);
$output .= '
				<a href="forum.php?action=view_topic&amp;id='.$topic['id'].'">'.$topic['name'].'</a>
			</td>
			<td>'.$topic['authorname'].'</td>
			<td>'.$lasts[$topic['id']]['replies'].'</td>
			<td>'.$forum_lang['last_post_by'].' '.$lasts[$topic['id']]['authorname'].', '.$lasts[$topic['id']]['time'].'</td>
		</tr>';
		}
		$totaltopics = $sqlm->query('
			SELECT id
			FROM mm_forum_posts
			WHERE forum = '.$id.' AND id = topic'); //My page system is so roxing, i can' t break this query xD
		$pages = ceil($sqlm->num_rows($totaltopics)/$maxqueries);
$output .= '
		<tr>
			<td align="right" class="hidden"></td>
		</tr>
		<tr>
			<td align="right" colspan="4">'.$forum_lang['pages'].' : ';
		for($x = 1; $x <= $pages; $x++)
		{
			$y = $x-1;
$output .= '
				<a href="forum.php?action=view_forum&amp;id='.$id.'&amp;page='.$y.'">'.$x.'</a> ';
		}
$output .= '
			</td>
		</tr>';
	}
	else
$output .= '
		<tr>
			<td>'.$forum_lang['no_topics'].'</td>
		</tr>';
$sqlm->close();
$output .= '
	</table>
</fieldset>
</center>
<br/>';
// Queries : 3
}

?>
