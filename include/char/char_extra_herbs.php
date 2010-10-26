<?php

//########################################################################################################################
// SHOW CHARACTER EXTRA INV HERBS
//########################################################################################################################
function char_herbs(&$sqlr, &$sqlc, &$sqlw)
{
	global	$output, $lang_global, $lang_char,
			$realm_id, $characters_db, $world_db,
			$action_permission, $user_lvl, $user_name,
			$item_datasite;
			
			// this page uses wowhead tooltops
			wowhead_tt();

//==========================$_GET and SECURE=================================

// id and multi realm security to prevent sql injection
require_once './include/char/include/char_multi_realm_security.php';

//==========================$_GET and SECURE end=============================

	// getting character data from database
	$result = $sqlc->query('
		SELECT account, name, race, class, gender, level
		FROM characters
		WHERE guid = '.$id.' 
		LIMIT 1');

	// no point going further if character does not exist
	if ($sqlc->num_rows($result))
	{
		$char = $sqlc->fetch_assoc($result);

		// we get user permissions first
		$owner_acc_id = $sqlc->result($result, 0, 'account');
		$result = $sqlr->query('
			SELECT gmlevel, username 
			FROM account 
			WHERE id = '.$char['account'].'');

		$owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
		$owner_name = $sqlr->result($result, 0, 'username');

		// check user permission
		if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
		{

// character sub header
$output .= '
<center>
<div id="tab_content">
	<h1>'.$lang_char['extra'].'</h1>
	<br />';
	
// character menu tab
require_once './include/char/include/char_header.php';
// character info
require_once './include/char/include/char_info.php';

//---------------Page Specific Data Starts Here--------------------------

$output .= '
	<h1>'.$lang_char['char_herbs'].'</h1>
	<br />';

// character extra menu tab
require_once './include/char/include/char_extra_header.php';

$output .= '
	<div id="tab_content2">
		<table class="lined" style="width: 450px;">
			<tr>
				<th width="15%">'.$lang_char['icon'].'</th>
				<th width="15%">'.$lang_char['quantity'].'</th>
				<th width="70%">'.$lang_char['name'].'</th>
			</tr>';

$sqlw = new SQL;
$sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
			  
			$item = $sqlw->query('
				SELECT entry, description
				FROM item_template
				WHERE BagFamily = 32');
			while($template = $sqlw->fetch_assoc($item))
			{
				$character = $sqlc->query('
					SELECT item, item_template
					FROM character_inventory
					WHERE guid = '.$id.' AND item_template = '.$template['entry'].' ');
				while ($inventory = $sqlc->fetch_assoc($character))
				{
					$instance = $sqlc->query('
						SELECT CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, " ", 15), " ", -1) AS UNSIGNED) AS item
						FROM item_instance
						WHERE guid = '.$inventory['item'].' ');
$output .= '
			<tr valign="center">
				<td >
					<a style="padding:2px;" href="'.$item_datasite.$inventory['item_template'].'" target="_blank">
						<img src="'.get_item_icon($inventory['item_template'], $sqlm).'" alt="'.$inventory['item_template'].'" class="icon_border_0" />
					</a>
				</td>
				<td>
					'.$instance['item'].'
				</td>
				<td>
					<span onmousemove="toolTip(\''.$template['description'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_item_name($inventory['item_template'], $sqlw).'</span>
				</td>
			</tr>';
				}
			}
$output .= '
		</table>';
unset($template);
unset($inventory);
unset($item);
unset($character);
unset($instance);
	
//---------------Page Specific Data Ends Here--------------------------

$output .= '
	</div>
</div>
<br />';

require_once './include/char/include/char_ footer.php';

$output .= '
<br />
</center>';

		}
		else
			error($lang_char['no_permission']);
	}
	else
		error($lang_char['no_char_found']);
}
unset($char);

?>
