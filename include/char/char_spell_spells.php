<?php

//########################################################################################################################
// SHOW CHARACTER SPELLS
//########################################################################################################################
function char_spell(&$sqlr, &$sqlc)
{
	global	$output, $lang_global, $lang_char,
			$realm_id, $characters_db, $mmfpm_db,
			$action_permission, $user_lvl, $user_name,
			$spell_datasite, $itemperpage;

			// this page uses wowhead tooltops
			wowhead_tt();

//==========================$_GET and SECURE=================================

// id and multi realm security to prevent sql injection
require_once './include/char/include/char_multi_realm_security.php';
	
	$start = (isset($_GET['start'])) ? $sqlr->quote_smart($_GET['start']) : 0;
	if (is_numeric($start));
	else
		$start=0;

	$order_by = (isset($_GET['order_by'])) ? $sqlr->quote_smart($_GET['order_by']) : 'guid';
	if (preg_match('/^[_[:lower:]]{1,12}$/', $order_by));
	else
		$order_by = 'guid';

	$dir = (isset($_GET['dir'])) ? $sqlr->quote_smart($_GET['dir']) : 1;
	if (preg_match('/^[01]{1}$/', $dir));
	else
		$dir=1;

	$order_dir = ($dir) ? 'ASC' : 'DESC';
	$dir = ($dir) ? 0 : 1;

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
	<h1>'.$lang_char['spells'].'</h1>
	<br />';

// character extra menu tab
require_once './include/char/include/char_spell_header.php';

			$all_record = $sqlc->result($sqlc->query('
				SELECT count(spell) 
				FROM character_spell 
				WHERE guid = '.$id.' and active = 1'), 0);

			$result = $sqlc->query('
				SELECT spell 
				FROM character_spell 
				WHERE guid = '.$id.' and active = 1 
				ORDER BY spell ASC
				LIMIT '.$start.', '.$itemperpage.'');

$output .= '
	<div id="tab_content2">
		<table class="lined" style="width: 550px;">
			<tr align="right">
				<td colspan="4">';
$output .= 
					generate_pagination('char_spell.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'', $all_record, $itemperpage, $start);
$output .= '
				</td>
			</tr>
			<tr>
				<th>'.$lang_char['icon'].'</th>
				<th>'.$lang_char['name'].'</th>
				<th>'.$lang_char['icon'].'</th>
				<th>'.$lang_char['name'].'</th>
			</tr>';

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

			while ($spell = $sqlc->fetch_assoc($result))
			{
$output .= '
			<tr>
				<td><a href="'.$spell_datasite.$spell['spell'].'"><img src="'.spell_get_icon($spell['spell'], $sqlm).'" class="icon_border_0" /></a></td>
				<td align="left"><a href="'.$spell_datasite.$spell['spell'].'">'.spell_get_name($spell['spell'], $sqlm).'</a></td>';
				if($spell = $sqlc->fetch_assoc($result))
$output .='
				<td><a href="'.$spell_datasite.$spell['spell'].'"><img src="'.spell_get_icon($spell['spell'], $sqlm).'" class="icon_border_0" /></a></td>
				<td align="left"><a href="'.$spell_datasite.$spell['spell'].'">'.spell_get_name($spell['spell'], $sqlm).'</a></td>
			</tr>';
				else
$output .='
				<td></td>
				<td></td>
			</tr>';
			}
$output .= '
			<tr align="right">
				<td colspan="4">';
$output .=
					generate_pagination('char_spell.php?id='.$id.'&amp;realm='.$realmid.'&amp;start='.$start.'', $all_record, $itemperpage, $start);
$output .= '
				</td>
			</tr>
		</table>
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

?>
