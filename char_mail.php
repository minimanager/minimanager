<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/mail_lib.php';
require_once 'libs/item_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS MAILS
//########################################################################################################################
function char_mail(&$sqlr, &$sqlc)
{
	global	$output, $lang_global, $lang_char, $lang_mail,
			$realm_id, $realm_db, $mmfpm_db, $characters_db,
			$action_permission, $user_lvl, $user_name,
			$item_datasite, $itemperpage;

			// this page uses wowhead tooltops
			wowhead_tt();

//==========================$_GET and SECURE=================================

// id and multi realm security to prevent sql injection
require_once './include/char/include/char_multi_realm_security.php';

	$start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
	if (is_numeric($start));
	else
		$start = 0;

	$order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'id';
	if (preg_match('/^[_[:lower:]]{1,12}$/', $order_by));
	else
		$order_by = 'id';

	$dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
	if (preg_match('/^[01]{1}$/', $dir));
	else
		$dir = 1;

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
	<h1>'.$lang_char['mail'].'</h1>
	<br />';
	
// character menu tab
require_once './include/char/include/char_header.php';
// character info
require_once './include/char/include/char_info.php';

//---------------Page Specific Data Starts Here--------------------------

$output .= '
	<div id="tab_content2">
		<table class="lined" style="width: 100%">';

			$query = $sqlc->query('
				SELECT a.id as id, a.messageType as messagetype, a.sender as sender,
						a.subject as subject, a.body as body, a.has_items as hasitems, a.money as money, a.cod as cod, a.checked as checked,
						b.item_template as itemtemplate
				FROM mail a
				INNER JOIN mail_items b ON a.id = b.mail_id
				WHERE a.receiver = '.$id .'
				LIMIT '.$start.', '.$itemperpage.'');

			$total_mail = $sqlc->result($sqlc->query('
				SELECT count(*)
				FROM mail
				WHERE receiver= '.$id .''), 0);

$output .= '
			<tr>
				<td align="left">
					Total Mails: '.$total_mail.'
				</td>
				<td align="right" width="45%">';
$output .=
					generate_pagination('char_mail.php?start='.$start.'&amp;order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $total_mail, $itemperpage, $start);
$output .= '
				</td>
			</tr>
		</table>
		<table class="lined" style="width: 100%">
			<tr>
				<th width="5%">'.$lang_mail['mail_type'].'</th>
				<th width="10%">'.$lang_mail['sender'].'</th>
				<th width="15%">'.$lang_mail['subject'].'</th>
				<th width="5%">'.$lang_mail['has_items'].'</th>
				<th width="25%">'.$lang_mail['text'].'</th>
				<th width="20%">'.$lang_mail['money'].'</th>
				<th width="5%">'.$lang_mail['checked'].'</th>
			</tr>';
				
			while ($mail = $sqlc->fetch_assoc($query))
			{
$output .= '
			<tr valign=top>
				<td>'.get_mail_source($mail['messagetype']).'</td>
				<td><a href="char.php?id='.$mail['sender'].'">'.get_char_name($mail['sender']).'</a></td>
				<td>'.$mail['subject'].'</td>
				<td>
					<a style="padding:2px;" href="'.$item_datasite.$mail['itemtemplate'].'" target="_blank">
						<img class="bag_icon" src="'.get_item_icon($mail['itemtemplate'], $sqlm).'" alt="" />
					</a>
				</td>
				<td>'.$mail['body'].'</td>
				<td>
					'.substr($mail['money'],  0, -4).'<img src="img/gold.gif" alt="" align="middle" />
					'.substr($mail['money'], -4, -2).'<img src="img/silver.gif" alt="" align="middle" />
					'.substr($mail['money'], -2).'<img src="img/copper.gif" alt="" align="middle" />
				</td>
				<td>'.get_check_state($mail['checked']).'</td>
			</tr>';
			}
$output .= '
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


//########################################################################################################################
// MAIN
//########################################################################################################################

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// load language
$lang_char = lang_char();
$lang_mail = lang_mail();

$output .= '
<div class="top">
	<h1>'.$lang_char['character'].'</h1>
</div>';

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
char_mail($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);
unset($lang_mail);

require_once 'footer.php';


?>
