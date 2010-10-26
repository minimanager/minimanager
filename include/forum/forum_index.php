<?php

// #######################################################################################################
// Forum_Index : Display the forums in categories
// #######################################################################################################
function forum_index(&$sqlr, &$sqlm)
{
	global	$enablesidecheck, $forum_skeleton, 
			$forum_lang,
			$user_lvl,
			$output,
			$realm_db, $mmfpm_db;

	if($enablesidecheck)
		$side = get_side();
	
$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	$result = $sqlm->query('
		SELECT authorname, id, name, time, forum
		FROM mm_forum_posts
		WHERE id IN 
			(SELECT MAX(id)
			FROM mm_forum_posts
			GROUP BY forum)
		ORDER BY forum;');
    $lasts = array();
    if($sqlm->num_rows($result) > 0)
	{
		while($row = $sqlm->fetch_assoc($result))
			$lasts[$row['forum']] = $row;
    }
$output .= '
<div class="top">
	<h1>'.$forum_lang['forums'].'</h1>
</div>
<center>
<fieldset>
	<legend><a href="forum.php">'.$forum_lang['forum_index'].'</a></legend>
	<table class="lined">';

	$cid = $sqlm->query('
		SELECT category, name, description, side_access, level_post_topic, level_read, level_post
		FROM mm_forum_categories');

	while ($category = $sqlm->fetch_assoc($cid))
	{
		if(($category['level_read'] > $user_lvl))
			continue;
		if($user_lvl == 0 && $enablesidecheck)
		{
			if($category['side_access'] != 'ALL')
			{ // Not an all side forum
				if($side == 'NO') // No char
					continue;
				else if($category['side_access'] != $side) // Forumside different of the user side
					continue;
			}
		}
$output .= '
		<tr>
			<th class="head" align="left">'.$category['name'].'<br />'.$category['description'].'</th>
			<th class="head">'.$forum_lang['topics'].'</th>
			<th class="head">'.$forum_lang['replies'].'</th>
			<th class="head" align="right">'.$forum_lang['last_post'].'</th>
		</tr>';

	$fid = $sqlm->query('
		SELECT forum, category, name, description, side_access, level_post_topic, level_read, level_post
		FROM mm_forum_forums
		WHERE category = '.$category['category'].'');

		while ($forum = $sqlm->fetch_assoc($fid))
		{
			if($forum['level_read'] > $user_lvl)
				continue;
			if($user_lvl == 0 && $enablesidecheck)
			{
				if($forum['side_access'] != 'ALL')
				{ // Not an all side forum
					if($side == 'NO') // No char
						continue;
				else if($forum['side_access'] != $side) // Forumside different of the user side
					continue;
				}
			}
			$totaltopics = $sqlm->query('
				SELECT id
				FROM mm_forum_posts
				WHERE forum = '.$forum['forum'].' AND id = topic');
			$numtopics = $sqlm->num_rows($totaltopics);
			$totalreplies = $sqlm->query('
				SELECT id
				FROM mm_forum_posts
				WHERE forum = '.$forum['forum'].'');
			$numreplies = $sqlm->num_rows($totalreplies);
$output .= '
		<tr>
			<td align="left"><a href="forum.php?action=view_forum&amp;id='.$forum['forum'].'">'.$forum['name'].'</a><br />'.$forum['description'].'</td>
			<td>'.$numtopics.'</td>
			<td>'.$numreplies.'</td>';
			if(isset($lasts[$forum['forum']]))
			{
				$lasts[$forum['forum']]['name'] = htmlspecialchars($lasts[$forum['forum']]['name']);
$output .= '
			<td align="right">
				<a href="forum.php?action=view_topic&amp;postid='.$lasts[$forum['forum']]['id'].'">'.$lasts[$forum['forum']]['name'].'</a>
				<br />by '.$lasts[$forum['forum']]['authorname'].'
				<br /> '.$lasts[$forum['forum']]['time'].'
			</td>
		</tr>';
			}
			else
			{
$output .= '
			<td align="right">'.$forum_lang['no_topics'].'</td>
		</tr>';
			}
		}
	}
$output .= '
		<tr>
			<td align="right" class="hidden"></td>
		</tr>
	</table>
</fieldset>
</center>
<br/>';
$sqlm->close();
// Queries : 1
}

?>
