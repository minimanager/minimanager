<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'config/forum.conf.php';
require_once 'libs/forum_lib.php';
require_once 'libs/bbcode_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

$cid = $sqlm->query('
	SELECT category, name, description, side_access, level_post_topic, level_read, level_post
	FROM mm_forum_categories');

while ($category = $sqlm->fetch_assoc($cid))
{
	if(!isset($category['level_read']))
		$category['level_read'] = 0;
	if(!isset($category['level_post']))
		$category['level_post'] = 0;
	if(!isset($category['level_post_topic']))
		$category['level_post_topic'] = 0;
	if(!isset($category['side_access']))
		$category['side_access'] = 'ALL';

	$fid = $sqlm->query('
		SELECT forum, category, name, description, side_access, level_post_topic, level_read, level_post
		FROM mm_forum_forums
		WHERE category = '.$category['category'].'');

	while ($forum = $sqlm->fetch_assoc($fid))
	{
		if(!isset($forum['level_read']))
			$forum['level_read'] = 0;
		if(!isset($forum['level_post']))
			$forum['level_post'] = 0;
		if(!isset($forum['level_post_topic']))
			$forum['level_post_topic'] = 0;
		if(!isset($forum['side_access']))
			$forum['side_access'] = 'ALL';
	}
}

// #######################################################################################################
// Forum_Index : Display the forums in categories
// #######################################################################################################

require_once './include/forum/forum_index.php';

// #######################################################################################################
// Display each forums
// #######################################################################################################

require_once './include/forum/forum_forum_view.php';

// #######################################################################################################
// Display Topic
// #######################################################################################################

require_once './include/forum/forum_topic_view.php';

// #######################################################################################################
// Add Topic AND Do Add Topic
// #######################################################################################################

require_once './include/forum/forum_topic_add.php';

// #######################################################################################################
// Edit  Topic
// Close / Announce / Stick
// #######################################################################################################

require_once './include/forum/forum_topic_edit.php';

// #######################################################################################################
// Move Topic AND Do Move Topic
// #######################################################################################################

require_once './include/forum/forum_topic_move.php';

// #######################################################################################################
// Add Post
// #######################################################################################################

require_once './include/forum/forum_post_add.php';

// #######################################################################################################
// Delete Post AND Do Delete Post
// #######################################################################################################

require_once './include/forum/forum_post_del.php';

// #######################################################################################################
// Edit Post AND Do Edit Post
// #######################################################################################################

require_once './include/forum/forum_post_edit.php';

//#############################################################################
// MAIN
//#############################################################################

// load language
$forum_lang = lang_forum();

// $_GET and SECURE
$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// define functions to be called by actions
if ('index' == $action)
	forum_index($sqlr, $sqlm);
elseif ('view_forum' == $action)
	forum_view_forum($sqlm);
elseif ('view_topic' == $action)
	forum_view_topic($sqlr, $sqlc, $sqlm);
elseif ('add_topic' == $action)
	forum_add_topic($sqlm);
elseif ('do_add_topic' == $action)
	forum_do_add_topic($sqlm);
elseif ('do_edit_close' == $action)
	forum_do_edit_close($sqlm);
elseif ('do_edit_announce' == $action)
	forum_do_edit_announce($sqlm);
elseif ('do_edit_stick' == $action)
	forum_do_edit_stick($sqlm);
elseif ('move_topic' == $action)
	forum_move_topic($sqlm);
elseif ('do_move_topic' == $action)
	forum_do_move_topic($sqlm);
elseif ('do_add_post' == $action)
	forum_do_add_post($sqlm);
elseif ('delete_post' == $action)
	forum_delete_post($sqlm);
elseif ('do_delete_post' == $action)
	forum_do_delete_post($sqlm);
elseif ('edit_post' == $action)
	forum_edit_post($sqlm);
elseif ('do_edit_post' == $action)
	forum_do_edit_post($sqlm);
else
	forum_index($sqlr, $sqlm);

// close whats not needed anymore
unset($action);
unset($action_permission);
unset($forum_lang);

// page footer
require_once  'footer.php';

?>
