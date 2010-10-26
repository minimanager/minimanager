<?php


// #######################################################################################################
// Edit Close Topic
// #######################################################################################################
function forum_do_edit_close(&$sqlm)
{
	global 	$forum_lang,
			$user_lvl,
			$mmfpm_db;

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

//==========================$_GET and SECURE=================================
	if($user_lvl == 0)
		error($forum_lang['no_access']);

	if(!isset($_GET['id']))
		error($forum_lang['no_such_topic']);
	else
		$id = $sqlm->quote_smart($_GET['id']);

	if(!isset($_GET['state']))
		error('Bad request, please mail admin and describe what you did to get this error.');
	else
		$state = $sqlm->quote_smart($_GET['state']);
//==========================$_GET and SECURE end=============================

	$sqlm->query('
		UPDATE mm_forum_posts
		SET closed = '.$state.'
		WHERE id = '.$id.'');
	$sqlm->close();
		redirect('forum.php?action=view_topic&id='.$id.'');
  // Queries : 1
}

// #######################################################################################################
// Edit Announce Topic
// #######################################################################################################
function forum_do_edit_announce(&$sqlm)
{
	global	$forum_lang,
			$user_lvl,
			$mmfpm_db;

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

//==========================$_GET and SECURE=================================
	if($user_lvl == 0)
		error($forum_lang['no_access']);

	if(!isset($_GET['id']))
		error($forum_lang['no_such_topic']);
	else
		$id = $sqlm->quote_smart($_GET['id']);

	if(!isset($_GET['state']))
		error('Bad request, please mail admin and describe what you did to get this error.');
	else
		$state = $sqlm->quote_smart($_GET['state']);
//==========================$_GET and SECURE end=============================

	$sqlm->query('
		UPDATE mm_forum_posts
		SET annouced = '.$state.'
		WHERE id = '.$id.'');
	$sqlm->close();
		redirect('forum.php?action=view_topic&id='.$id.'');
	// Queries : 1
}

// #######################################################################################################
// Edit Stick Topic
// #######################################################################################################
function forum_do_edit_stick(&$sqlm)
{
	global 	$forum_lang,
			$user_lvl,
			$mmfpm_db;

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if($user_lvl == 0)
		error($forum_lang['no_access']);

	if(!isset($_GET['id']))
		error($forum_lang['no_such_topic']);
	else
		$id = $sqlm->quote_smart($_GET['id']);

	if(!isset($_GET['state']))
		error('Bad request, please mail admin and describe what you did to get this error.');
	else
		$state = $sqlm->quote_smart($_GET['state']);

	$sqlm->query('
		UPDATE mm_forum_posts
		SET sticked = '.$state.'
		WHERE id = '.$id.'');
	$sqlm->close();
		redirect('forum.php?action=view_topic&id='.$id.'');
	// Queries : 1
}

?>
