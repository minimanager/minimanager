<?php

// #######################################################################################################
// Display Topic
// #######################################################################################################
function forum_view_topic(&$sqlr, &$sqlc, &$sqlm)
{
	global 	$enablesidecheck, $forum_skeleton,  $maxqueries,
			$forum_lang,
			$user_lvl, $user_id,
			$output,
			$realm_db, $characters_db, $mmfpm_db,
			$realm_id;

	if($enablesidecheck)
		$side = get_side(); // Better to use it here instead of call it many time in the loop :)

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

//==========================$_GET and SECURE=================================
	if(isset($_GET['id']))
	{
		$id = $sqlm->quote_smart($_GET['id']);
		$post = false;
	}
	else
	{
		if(isset($_GET['postid']))
		{
			$id = $sqlm->quote_smart($_GET['postid']);
			$post = true;
		}
		else
			error($forum_lang['no_such_topic']);
	}

	if(!isset($_GET['page']))
		$page = 0;
	else
		$page = $sqlm->quote_smart($_GET['page']); // Fok you mathafoker haxorz
//==========================$_GET and SECURE end=============================

	$start = ($maxqueries * $page);

	if(!$post)
	{
		$posts = $sqlm->query('
			SELECT id, authorid, authorname, forum, name, text, time, annouced, sticked, closed
			FROM mm_forum_posts
			WHERE topic = '.$id.'
			ORDER BY id ASC
			LIMIT '.$start.', '.$maxqueries.'');

$sqlr = new SQL;
$sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

		// need to update this query to use ' instead of "
		$query = "
			SELECT account, name, gender, race, class, level,
				(SELECT gmlevel
				FROM `{$realm_db['name']}`.account
				WHERE `{$realm_db['name']}`.account.id = `{$characters_db[$realm_id]['name']}`.characters.account) as gmlevel
			FROM `{$characters_db[$realm_id]['name']}`.characters
			WHERE totaltime IN 
				(SELECT MAX(totaltime)
				FROM `{$characters_db[$realm_id]['name']}`.characters
				WHERE account IN (";
		while($post = $sqlm->fetch_row($posts))
		{
			$query .= "$post[1],";
		}
		mysql_data_seek($posts,0);
		$query .= "
					0)
				GROUP BY account);";

$sqlc = new SQL;
$sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

		$results = $sqlc->query($query);

		while($avatar = $sqlc->fetch_assoc($results))
		{
			$char_gender = str_pad(dechex($avatar['gender']),8, 0, STR_PAD_LEFT);
			$avatars[$avatar['account']]['name'] = $avatar['name'];
			$avatars[$avatar['account']]['sex'] = $char_gender['race'];
			$avatars[$avatar['account']]['race'] = $avatar['race'];
			$avatars[$avatar['account']]['class'] = $avatar['class'];
			$avatars[$avatar['account']]['level'] = $avatar['level'];
			$avatars[$avatar['account']]['gm'] = $avatar['gmlevel'];
		}

		$replies = $sqlm->num_rows($posts);
		if($replies==0)
			error($forum_lang['no_such_topic']);
		$post = $sqlm->fetch_assoc($posts);
		$fid = $post['forum'];
		$cat = 0;

		$cid = $sqlm->query('
			SELECT category, name, description, side_access, level_post_topic, level_read, level_post
			FROM mm_forum_categories');

		while ($category = $sqlm->fetch_assoc($cid))
		{
			$fid_ = $sqlm->query('
				SELECT forum, category, name, description, side_access, level_post_topic, level_read, level_post
				FROM mm_forum_forums
				WHERE category = '.$category['category'].'');

			while ($forum = $sqlm->fetch_assoc($fid_))
			{
				if($forum['forum'] == $fid) $cat = $forum['category'];

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

		$post['name'] = htmlspecialchars($post['name']);
		$post['text'] = htmlspecialchars($post['text']);
		$post['text'] = bbcode_parse1($post['text']);

$output .= '
<div class="top">
	<h1>'.$forum_lang['forums'].'</h1>
</div>
<center>
<fieldset>
	<legend>
		<a href="forum.php">'.$forum_lang['forum_index'].'</a> ->
		<a href="forum.php?action=view_forum&amp;id='.$forum['forum'].'">'.$forum['name'].'</a> -> 
		<a href="forum.php?action=view_topic&amp;id='.$id.'">'.$post['name'].'</a>
	</legend>
<table class="lined">
	<tr>
		<th style="width:15%;">'.$forum_lang['info'].'</th>
		<th style="text-align:left;">'.$forum_lang['text'].'</th>';
		if($user_lvl > 0)
		{
$output .= '
		<th style="width:50%;text-align:right;">';
			if($post['sticked']=="1")
			{
				if($post['annouced']=="1")
				{
					// Annoucement
$output .= '
			'.$forum_lang['annoucement'].'';
				}
				else
				{
					// Sticky
$output .= '
			'.$forum_lang['sticky'].'';
				}
			}
			else
			{
				if($post['annouced']=="1")
				{
					// Annoucement
$output .= '
			'.$forum_lang['annoucement'].'';
				}
				else
				{
					// Normal Topic
$output .= '
			'.$forum_lang['normal'].'';
				}
			}
			if($post['closed']=="1")
$output .= '
		</th>';
		}
		if(isset($avatars[$post['authorid']]))
			$avatar = gen_avatar_panel
			(
				$avatars[$post['authorid']]['level'],
				$avatars[$post['authorid']]['sex'],
				$avatars[$post['authorid']]['race'],
				$avatars[$post['authorid']]['class'],1,
				$avatars[$post['authorid']]['gm']
			);
		else
			$avatar = "";
$output .= '
	<tr>
		<td colspan="3" align="left">
			'.$post['time'].'
		</td>
	</tr>	
	</tr>';
$output .= '
	<tr>
		<td style="width:15%;text-align:center;"><center>'.$avatar.'</center>'.$forum_lang['author'].' : ';
		if($user_lvl > 0)
$output .= '
			<a href="user.php?action=edit_user&error=11&id='.$post['authorid'].'">';
		if(isset($avatars[$post['authorid']]))
$output .= 
			$avatars[$post['authorid']]['name'];
		else
$output .=
			$post['authorname'];
		if($user_lvl > 0)
$output .= '
			</a>';
$output .= '
		</td>
		<td colspan="2" style="text-align:left">'.$post['text'].'<br />
			<div style="text-align:right\">
		</td>
	</tr>';
		if($user_lvl > 0)
		{
$output .= '
	<tr>
		<th colspan="3" align="right">';
			if($post['sticked']=="1")
			{
				if($post['annouced']=="1")
				{
					// Annoucement
$output .= '
			<a href="forum.php?action=edit_announce&amp;id='.$post['id'].'&amp;state=0"><img src="img/forums/unannounce.png" border="0" alt="'.$forum_lang['down'].'" /></a>';
				}
				else
				{
					// Sticky
$output .= '
			<a href="forum.php?action=edit_stick&amp;id='.$post['id'].'&amp;state=0"><img src="img/forums/unstick.png" border="0" alt="'.$forum_lang['down'].'" /></a>
			<a href="forum.php?action=edit_announce&amp;id='.$post['id'].'&amp;state=1"><img src="img/forums/announce.png" border="0" alt="'.$forum_lang["up"].'" /></a>';
				}
			}
			else
			{
				if($post['annouced']=="1")
				{
					// Annoucement
$output .= '
			<a href="forum.php?action=edit_announce&amp;id='.$post['id'].'&amp;state=0"><img src="img/forums/unannounce.png" border="0" alt="'.$forum_lang['down'].'" /></a>';
				}
				else
				{
					// Normal Topic
$output .= '
			<a href="forum.php?action=edit_stick&amp;id='.$post['id'].'&amp;state=1"><img src="img/forums/stick.png" border="0" alt="'.$forum_lang['up'].'" /></a>';
				}
			}

			if($post['closed']=="1")
$output .= '
			<a href="forum.php?action=edit_close&amp;id='.$post['id'].'&amp;state=0"><img src="img/forums/lock.png" border="0" alt=\"'.$forum_lang['open'].'" /></a>';
			else
$output .= '
			<a href="forum.php?action=edit_close&amp;id='.$post['id'].'&amp;state=1"><img src="img/forums/unlock.png" border="0" alt="'.$forum_lang['close'].'" /></a>';
$output .= '
			<a href="forum.php?action=move_topic&amp;id='.$post['id'].'"><img src="img/forums/move.png" border="0" alt="'.$forum_lang['move'].'" /></a>
			<a href="forum.php?action=edit_post&amp;id='.$post['id'].'"><img src="img/forums/edit.png" border="0" alt="'.$forum_lang["edit"].'" /></a>
			<a href="forum.php?action=delete_post&amp;id='.$post['id'].'"><img src="img/forums/delete.png" border="0" alt="'.$forum_lang["delete"].'" /></a>
		</th>
	</tr>';
		}
		$closed = $post['closed'];

		while($post = $sqlm->fetch_assoc($posts))
		{
			$post['text'] = htmlspecialchars($post['text']);
			$post['text'] = bbcode_parse1($post['text']);

			if(isset($avatars[$post['authorid']]))
				$avatar = gen_avatar_panel
				(
					$avatars[$post['authorid']]['level'],
					$avatars[$post['authorid']]['sex'],
					$avatars[$post['authorid']]['race'],
					$avatars[$post['authorid']]['class'],1,
					$avatars[$post['authorid']]['gm']
				);
			else
				$avatar = "";
$output .= '
	<tr>
		<td colspan="3" align="left">
			'.$post['time'].'
		</td>
	</tr>		
	<tr>
		<td style="width:15%;text-align:center;">
			<center>'.$avatar.'</center>'.$forum_lang['author'].' : ';
			if($user_lvl > 0)
$output .= '
			<a href="user.php?action=edit_user&error=11&id='.$post['authorid'].'">';
			if(isset($avatars[$post['authorid']]))
$output .=
			$avatars[$post['authorid']]['name'];
			else
$output .=
			$post['authorname'];
$output .= '
			</a>';
$output .= '
		</td>
		<td colspan="2" style="text-align:left;">'.$post['text'].'<br />';
$output .= '
		</td>
	</tr>';
			if($user_lvl > 0 || $user_id == $post['authorid'])
$output .= '
				<tr>
					<th colspan="3" align="right">
						<a href="forum.php?action=edit_post&amp;id='.$post['id'].'"><img src="img/forums/edit.png" border="0" alt="'.$forum_lang['edit'].'"></a>
						<a href="forum.php?action=delete_post&amp;id='.$post['id'].'"><img src="img/forums/delete.png" border="0" alt="'.$forum_lang['delete'].'"></a>
					</th>
				</tr>';
		}

	$totalposts = $sqlm->query('
		SELECT id
		FROM mm_forum_posts
		WHERE topic = '.$id.'');
		$totalposts = $sqlm->num_rows($totalposts);

		$pages = ceil($totalposts/$maxqueries);
$output .= '
	<tr>
		<td align="right" colspan="3">'.$forum_lang['pages'].' : ';
		for($x = 1; $x <= $pages; $x++)
		{
			$y = $x-1;
$output .= '
			<a href="forum.php?action=view_topic&amp;id='.$id.'&amp;page='.$y.'">'.$x.'</a>';
		}
$output .= '
		</td>
	</tr>
</table>
</fieldset>
<br />';

		$category = $sqlm->query('
			SELECT category, name, description, side_access, level_post_topic, level_read, level_post
			FROM mm_forum_categories');

	// Quick reply form
		if((($user_lvl > 0)||!$closed)&&($category['level_post'] <= $user_lvl && $forum['level_post'] <= $user_lvl))
		{
$output .= '
<form action="forum.php?action=do_add_post" method="POST" name="form">
<fieldset>
	<legend>
		'.$forum_lang['quick_reply'].'
	</legend>
<table class="lined">
	<tr>
		<td align="left" colspan="3">';
			bbcode_add_editor();
$output .= '
		</td>
	</tr>
	<tr>
		<td colspan="3">
			<TEXTAREA ID="msg" NAME="msg" ROWS=8 COLS=93></TEXTAREA><br/>
			<input type="hidden" name="forum" value="'.$fid.'">
			<input type="hidden" name="topic" value="'.$id.'">
		</td>
	</tr>
	<tr>
		<td align="left">';
			makebutton($forum_lang['post'], "javascript:do_submit()",100);
$output .= '
		</td>
	</tr>
</table>
</fieldset>
</form>';
		}

$output .= '
</center>';

$sqlm->close();
	}
	else
	{
$output .= '
<div class="top">
	<h1>Stand by...</h1>
</div>';
		// Get post id
		$post = $sqlm->query('
			SELECT topic, id
			FROM mm_forum_posts
			WHERE id = '.$id.'');
		if($sqlm->num_rows($post)==0)
			error($forum_lang['no_such_topic']);
		$post = $sqlm->fetch_assoc($post);
		if($post['id']==$post['authorid'])
			redirect('forum.php?action=view_topic&id='.$id.'');
		$topic = $post['id'];
		 // Get posts in topic
		$posts = $sqlm->query('
			SELECT id
			FROM mm_forum_posts
			WHERE topic = '.$topic.'');
		$replies = $sqlm->num_rows($posts);
		if($replies==0)
			error($forum_lang['no_such_topic']);
		$row = 0;
		// Find the row of our post, so we could have his ratio (topic x/total topics) and knew the page to show
		while($post = $sqlm->fetch_row($posts))
		{
			$row++;
			if($topic==$id)
				break;
		}
		$page = 0;
		while(($page * $maxqueries) < $row)
		{
			$page++;
		};
		$page--;
		$sqlm->close();
			redirect('forum.php?action=view_topic&id='.$topic.'&page='.$page.'');
	}
	// Queries : 2 with id || 2 (+2) with postid
}

?>
