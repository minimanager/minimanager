<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/item_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTER EXTRA INV ARROWS
//########################################################################################################################

require_once './include/char/char_extra_arrows.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV BULLETS
//########################################################################################################################

require_once './include/char/char_extra_bullets.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV COMPANIONS
//########################################################################################################################

require_once './include/char/char_extra_companion.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV ENCHANTING
//########################################################################################################################

require_once './include/char/char_extra_enchanting.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV ENGINEERING
//########################################################################################################################

require_once './include/char/char_extra_engineering.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV GEMS
//########################################################################################################################

require_once './include/char/char_extra_gems.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV HERBS
//########################################################################################################################

require_once './include/char/char_extra_herbs.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV KEYS
//########################################################################################################################

require_once './include/char/char_extra_keys.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV LEATHER
//########################################################################################################################

require_once './include/char/char_extra_leather.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV MINING
//########################################################################################################################

require_once './include/char/char_extra_mining.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV QUEST ITEMS
//########################################################################################################################

require_once './include/char/char_extra_quest_items.php';

//########################################################################################################################
// SHOW CHARACTER EXTRA INV TOKENS
//########################################################################################################################

require_once './include/char/char_extra_tokens.php';

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
if ('char_arrows' == $action)
	char_arrows($sqlr, $sqlc, $sqlw);
elseif ('char_bullets' == $action)
	char_bullets($sqlr, $sqlc, $sqlw);
elseif ('char_companions' == $action)
	char_companions($sqlr, $sqlc, $sqlw);
elseif ('char_enchanting' == $action)
	char_enchanting($sqlr, $sqlc, $sqlw);
elseif ('char_engineering' == $action)
	char_engineering($sqlr, $sqlc, $sqlw);
elseif ('char_gems' == $action)
	char_gems($sqlr, $sqlc, $sqlw);
elseif ('char_herbs' == $action)
	char_herbs($sqlr, $sqlc, $sqlw);
elseif ('char_keys' == $action)
	char_keys($sqlr, $sqlc, $sqlw);
elseif ('char_mining' == $action)
	char_mining($sqlr, $sqlc, $sqlw);
elseif ('char_quest_items' == $action)
	char_quest_items($sqlr, $sqlc, $sqlw);
elseif ('char_tokens' == $action)
	char_tokens($sqlr, $sqlc, $sqlw);
else
	char_arrows($sqlr, $sqlc, $sqlw);

// close whats not needed anymore
unset($action);
unset($action_permission);
unset($lang_char);

// page footer
require_once 'footer.php';

?>
