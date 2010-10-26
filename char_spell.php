<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/spell_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTER SPELLS
//########################################################################################################################

require_once './include/char/char_spell_spells.php';

//########################################################################################################################
// SHOW CHARACTER COMPANIONS
//########################################################################################################################

require_once './include/char/char_spell_companion.php';

//########################################################################################################################
// SHOW CHARACTER MOUNTS
//########################################################################################################################

require_once './include/char/char_spell_mount.php';

//########################################################################################################################
// MAIN
//########################################################################################################################

// load language
$lang_char = lang_char();

$output .= '
<div class="top">
	<h1>'.$lang_char['character'].'</h1>
</div>';

// $_GET and SECURE
$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// define functions to be called by actions
if ('char_spell' == $action)
	char_spell($sqlr, $sqlc);
elseif ('char_companion' == $action)
	char_companion($sqlr, $sqlc, $sqlm);
elseif ('char_mounts' == $action)
	char_mounts($sqlr, $sqlc, $sqlm);
else
	char_spell($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

// page footer
require_once 'footer.php';

?>
