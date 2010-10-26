<?php

require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/spell_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################^M
// SHOW CHARACTER PETS
//########################################################################################################################^M
function char_pets(&$sqlr, &$sqlc)
{
	global	$output, $lang_global, $lang_char,
			$realm_id, $characters_db, $mmfpm_db,
			$action_permission, $user_lvl, $user_name, 
			$spell_datasite, $pet_ability;

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
	<h1>'.$lang_char['pets'].'</h1>
	<br />';
	
// character menu tab
require_once './include/char/include/char_header.php';
// character info
require_once './include/char/include/char_info.php';

//---------------Page Specific Data Starts Here--------------------------

$output .= '
	<div id="tab_content2">';

		$result = $sqlc->query('
			SELECT id, level, exp, name, curhappiness
			FROM character_pet
			WHERE owner = '.$id.'');

		if ($sqlc->num_rows($result))
		{
			$sqlm = new SQL;
			$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
			while($pet = $sqlc->fetch_assoc($result))
			{
				$happiness = floor($pet['curhappiness']/333000);
				if (1 == $happiness)
				{
					$hap_text = 'Content';
					$hap_val = 1;
				}
				elseif (2 == $happiness)
				{
					$hap_text = 'Happy';
					$hap_val = 2;
				}
				else
				{
					$hap_text = 'Unhappy';
					$hap_val = 0;
				}
				$pet_next_lvl_xp = floor(char_get_xp_to_level($pet['level'])/4);
$output .= '
		<font class="bold">'.$pet['name'].' - lvl '.char_get_level_color($pet['level']).'
			<a style="padding:2px;" onmouseover="toolTip(\''.$hap_text.'\', \'item_tooltip\')" onmouseout="toolTip()"><img src="img/pet/happiness_'.$hap_val.'.jpg" alt="" /></a>
			<br /><br />
		</font>
		<table class="lined" style="width: 550px;">
			<tr>
				<td align="right">Exp:</td>
				<td valign="top" class="bar skill_bar" style="background-position: '.(round(385*$pet['exp']/$pet_next_lvl_xp)-385).'px;">
						<span>'.$pet['exp'].'/'.$pet_next_lvl_xp.'</span>
				</td>
			</tr>
			<tr>
				<td align="right">Pet Abilities:</td>
				<td align="left">';
				// active = 0 is unused and active = 1 probably some passive auras, i dont know diference between values 129 and 193, need to check mangos source
				$ability_results = $sqlc->query('
					SELECT spell
					FROM pet_spell
					WHERE guid = '.$pet['id'].' and active > 1'); 
					
				if ($sqlc->num_rows($ability_results))
				{
					while ($ability = $sqlc->fetch_assoc($ability_results))
					{
$output .= '
					<a style="padding:2px;" href="'.$spell_datasite.$ability['spell'].'" target="_blank">
						<img src="'.spell_get_icon($ability['spell'], $sqlm).'" alt="'.$ability['spell'].'" class="icon_border_0" />
					</a>';
					}
				}
$output .= '
				</td>
			</tr>
		</table>
		<br /><br />';
			}
unset($ability_results);
unset($pet_next_lvl_xp);
unset($happiness);
unset($pet);
		}
$output .= '
	</div>
</div>
<br />';
//---------------Page Specific Data Ends here----------------------------

// character sub footer
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

//########################################################################################################################
// MAIN
//########################################################################################################################
// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// load language
$lang_char = lang_char();

$output .= '
<div class="top">
	<h1>'.$lang_char['character'].'</h1>
</div>';

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
char_pets($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';

?>
