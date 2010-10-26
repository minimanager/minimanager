<?php

//########################################################################################################################
// SHOW CHARACTER MOUNTS
//########################################################################################################################
function char_mounts(&$sqlr, &$sqlc, &$sqlm)
{
	global	$output, $lang_global, $lang_char,
			$realm_id, $characters_db, $mmfpm_db,
			$action_permission, $user_lvl, $user_name,
			$spell_datasite;

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
	<h1>'.$lang_char['spells'].'</h1>
	<br />';
	
// character menu tab
require_once './include/char/include/char_header.php';
// character info
require_once './include/char/include/char_info.php';

//---------------Page Specific Data Starts Here--------------------------

$output .= '
	<h1>'.$lang_char['mounts'].'</h1>
	<br />';

// character extra menu tab
require_once './include/char/include/char_spell_header.php';

$output .= '
	<div id="tab_content2">
		<table class="lined" style="width: 450px;">
			<tr>
				<th width="15%">'.$lang_char['icon'].'</th>
				<th width="85%">'.$lang_char['name'].'</th>
			</tr>';

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

			// SkillLine 777 = mount
			$skilllineability = $sqlm->query('
				SELECT field_2 
				FROM dbc_skilllineability
				WHERE field_1 = 777');
				
			while($spells = $sqlm->fetch_assoc($skilllineability))
			{
				// to get from char spells just spell that we want
				$spell = $sqlc->query('
					SELECT spell 
					FROM character_spell 
					WHERE guid = '.$id.' AND spell = '.$spells['field_2'].' ');
					
				while ($character = $sqlc->fetch_assoc($spell))
				{
$output .= '
			<tr valign="center">
				<td >
					<a style="padding:2px;" href="'.$spell_datasite.$character['spell'].'" target="_blank">
						<img src="'.spell_get_icon($character['spell'], $sqlm).'" alt="'.$character['spell'].'" class="icon_border_0" />
					</a>
				</td>
				<td width="90%" align="center">
					'.spell_get_name($character['spell'], $sqlm).'
				</td>
			</tr>';
				}
			}
$output .= '
		</table>
	</div>
</div>
<br />';
unset($skilllineability);
unset($spell);
unset($spells);
unset($character);
	
//---------------Page Specific Data Ends Here--------------------------

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

?>
