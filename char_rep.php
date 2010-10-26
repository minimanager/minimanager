<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHAR REPUTATION
//########################################################################################################################
function char_rep(&$sqlr, &$sqlc)
{
	global	$output, $lang_global, $lang_char,
			$realm_id, $characters_db, $mmfpm_db,
			$action_permission, $user_lvl, $user_name;

require_once 'libs/fact_lib.php';

	$reputation_rank = fact_get_reputation_rank_arr();
	$reputation_rank_length = fact_get_reputation_rank_length();

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
	<h1>'.$lang_char['reputation'].'</h1>
	<br />';
	
// character menu tab
require_once './include/char/include/char_header.php';
// character info
require_once './include/char/include/char_info.php';

//---------------Page Specific Data Starts Here--------------------------

			$result = $sqlc->query('
				SELECT faction, standing
				FROM character_reputation
				WHERE guid = '.$id.' AND (flags & 1 = 1)');

$output .= '
	<div id="tab_content2">';
$temp_out = array
(
	1 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi1" onclick="expand(\'i1\', this, \'Alliance\')">[-] Alliance</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i1" class="lined" style="width: 535px; display: table;">',0),
	2 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi2" onclick="expand(\'i2\', this, \'Horde\')">[-] Horde</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i2" class="lined" style="width: 535px; display: table;">',0),
	3 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi3" onclick="expand(\'i3\', this, \'Alliance Forces\')">[-] Alliance Forces</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i3" class="lined" style="width: 535px; display: table;">',0),
	4 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi4" onclick="expand(\'i4\', this, \'Horde Forces\')">[-] Horde Forces</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i4" class="lined" style="width: 535px; display: table;">',0),
	5 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi5" onclick="expand(\'i5\', this, \'Steamwheedle Cartels\')">[-] Steamwheedle Cartel</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i5" class="lined" style="width: 535px; display: table;">',0),
	6 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi6" onclick="expand(\'i6\', this, \'The Burning Crusade\')">[-] The Burning Crusade</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i6" class="lined" style="width: 535px; display: table;">',0),
	7 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi7" onclick="expand(\'i7\', this, \'Shattrath City\')">[-] Shattrath City</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i7" class="lined" style="width: 535px; display: table;">',0),
	8 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi8" onclick="expand(\'i8\', this, \'Alliance Vanguard\')">[-] Alliance Vanguard</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i8" class="lined" style="width: 535px; display: table;">',0),
	9 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi9" onclick="expand(\'i9\', this, \'Horde Expedition \')">[-] Horde Expedition </div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i9" class="lined" style="width: 535px; display: table;">',0),
	10 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi10" onclick="expand(\'i10\', this, \'Sholazar Basin\')">[-] Sholazar Basin</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i10" class="lined" style="width: 535px; display: table;">',0),
	11 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi11" onclick="expand(\'i11\', this, \'Wrath of the Lich King\')">[-] Wrath of the Lich King</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i11" class="lined" style="width: 535px; display: table;">',0),
	12 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi12" onclick="expand(\'i12\', this, \'Other\')">[-] Other</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i12" class="lined" style="width: 535px; display: table;">',0),
	0 => array('
		<table class="lined" style="width: 550px;">
			<tr>
				<th colspan="3" align="left">
					<div id="divi13" onclick="expand(\'i13\', this, \'Unknown\')">[-] Unknown</div>
				</th>
			</tr>
			<tr>
				<td>
					<table id="i13" class="lined" style="width: 535px; display: table;">',0),
);

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

			if ($sqlc->num_rows($result))
			{
				while ($fact = $sqlc->fetch_assoc($result))
				{
					$faction  = $fact['faction'];
					$standing = $fact['standing'];

					$rep_rank      = fact_get_reputation_rank($faction, $standing, $char['race'], $sqlm);
					$rep_rank_name = $reputation_rank[$rep_rank];
					$rep_cap       = $reputation_rank_length[$rep_rank];
					$rep           = fact_get_reputation_at_rank($faction, $standing, $char['race'], $sqlm);
					$faction_name  = fact_get_faction_name($faction, $sqlm);
					$ft            = fact_get_faction_tree($faction);

					// not show alliance rep for horde and vice versa:
					if ((((1 << ($char['race'] - 1)) & 690) && ($ft == 1 || $ft == 3))
					|| ( ((1 << ($char['race'] - 1)) & 1101) && ($ft == 2 || $ft == 4)));
					else
					{
						$temp_out[$ft][0] .= '
						<tr>
							<td width="30%" align="left">'.$faction_name.'</td>
							<td width="55%" valign="top">
								<div class="faction-bar">
									<div class="rep'.$rep_rank.'">
										<span class="rep-data">'.$rep.'/'.$rep_cap.'</span>
										<div class="bar-color" style="width:'.(100*$rep/$rep_cap).'%"></div>
									</div>
								</div>
							</td>
							<td width="15%" align="left" class="rep'.$rep_rank.'">'.$rep_rank_name.'</td>
						</tr>';
						$temp_out[$ft][1] = 1;
					}
				}
			}
			else
$output .= '
						<tr>
							<td colspan="2"><br /><br />'.$lang_global['err_no_records_found'].'<br /><br /></td>
						</tr>';

			foreach ($temp_out as $out)
			if ($out[1])
				$output .= $out[0].'
					</table>
				</td>
			</tr>
		</table>';
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
char_rep($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
