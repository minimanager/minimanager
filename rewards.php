<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/item_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//#############################################################################
// REWARDS INO
//#############################################################################
function info()
{
global 	$output, $lang_rewards;

$output .= '
<center>
	<div id="tab_content">
		<div id="tab">
			<ul>
				<li id="selected"><a href="rewards.php">INFO</a></li>
				<li><a href="rewards.php?action=char_select">SELECT CHAR</a></li>
				<li><a href="rewards.php?action=show_reward_gold">GOLD</a></li>
				<li><a href="rewards.php?action=show_reward_item">ITEM</a></li>
			</ul>
		</div>
		<div id="tab_content2">
			Rewards to be exchanged from vote points<BR>
			choose one of above tabs (GOLD< ITEM) and claim your reward<BR>
			Dont Forget to vote everyday to get more points and help promote server
		</div>
	</div>
</center>';
}

//#############################################################################
// REWARDS SELECT CHAR
//#############################################################################
function char_select(&$sqlr, &$sqlm, &$sqlc)
{
	global 	$output, $lang_rewards, $lang_global,
			$characters_db, $mmfpm_db, $realm_id,
			$user_id;
			
require_once 'libs/char_lib.php';

	$sqlc = new SQL;
	$sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$output .= '
<center>
	<div id="tab_content">
		<div id="tab">
			<ul>
				<li><a href="rewards.php">INFO</a></li>
				<li id="selected"><a href="rewards.php?action=char_select">SELECT CHAR</a></li>
				<li><a href="rewards.php?action=show_reward_gold">GOLD</a></li>
				<li><a href="rewards.php?action=show_reward_item">ITEM</a></li>
			</ul>
		</div>
		<div id="tab_content2">
			<table class="lined" style="width: 200px;">
				<tr>
					<th align="center">
						Select Character
					</th>
				</tr>';

	$result = $sqlc->query('SELECT account, name, race, class, gender, level
							FROM characters
							WHERE account = '.$user_id.'');

	while ($char = $sqlc->fetch_assoc($result))
	{
$output .= '
				<tr>
					<td align="right">
						<font>
							'.htmlentities($char['name']).' -
							<img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif"
							onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
							<img src="img/c_icons/'.$char['class'].'.gif"
							onmousemove="toolTip(\''.char_get_class_name($char['class']).'\',\'item_tooltip\')" onmouseout="toolTip()" alt="" /> - lvl '.char_get_level_color($char['level']).'
						</font>
					</td>
				</tr>
				<BR />';
	}
unset($char);

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	$result = $sqlm->query('SELECT *
							FROM mm_reward_char
							WHERE account = '.$user_id.' 
							LIMIT 1');

	while ($char = $sqlc->fetch_assoc($result))
		{
$output .= '
				<tr>
					<th align="center">
						Selected Character
					</th>
				</tr>
				<tr>
					<td align="right">
						<font>
							'.htmlentities($char['name']).' -
							<img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif"
							onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
							<img src="img/c_icons/'.$char['class'].'.gif"
							onmousemove="toolTip(\''.char_get_class_name($char['class']).'\',\'item_tooltip\')" onmouseout="toolTip()" alt="" /> - lvl '.char_get_level_color($char['level']).'
						</font>
					</td>
				</tr>';
		}
$output .= '
			</table>
		</div>
		<br />
	</div>
</center>';
}

//#############################################################################
// GOLD REWARDS
//#############################################################################
function show_reward_gold(&$sqlm)
{
global 	$output, $lang_global, $lang_rewards,
		$mmfpm_db, $world_db, $realm_id,
		$action_permission, $user_lvl;

$output .= '
<center>
	<div id="tab_content">
		<div id="tab">
			<ul>
				<li><a href="rewards.php">INFO</a></li>
				<li><a href="rewards.php?action=char_select">SELECT CHAR</a></li>
				<li id="selected"><a href="rewards.php?action=show_reward_gold">GOLD</a></li>
				<li><a href="rewards.php?action=show_reward_item">ITEM</a></li>
			</ul>
		</div>
		<div id="tab_content2">
			<table class="lined" style="width: 450px;">
				<tr>';
	if($user_lvl >= $action_permission['delete'])
$output .= '
					<th width="1%">'.$lang_global['delete'].'</th>
					<th width="1%">'.$lang_global['edit'].'</th>';
$output .= '
					<th width="50%">Amount</th>
					<th width="49%">points</th>
				</tr>';

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

$result = $sqlm->query('SELECT value, points FROM mm_reward_gold');

	while($gold = $sqlm->fetch_assoc($result))
	{
$output .= '
				<tr valign="top">';
	if($user_lvl >= $action_permission['delete'])
$output .= '
					<td><a href="rewards.php?action=delete_reward_gold&amp;value='.$gold['value'].'"><img src="img/aff_cross.png" alt="" /></a></td>
					<td><a href="rewards.php?action=edit_reward_gold&amp;value='.$gold['value'].'"><img src="img/edit.png" alt="" /></a></td>';
$output .= '
					<td>
						<b>
							'.substr($gold['value'],  0, -4).'<img src="img/gold.gif" alt="" />
							'.substr($gold['value'], -4, -2).'<img src="img/silver.gif" alt="" />
							'.substr($gold['value'], -2).'<img src="img/copper.gif" alt="" />
						</b>
					</td>
					<td>
						'.$gold['points'].'
					</td>
				</tr>';
	}
$output .= '
			</table>
		</div>
	</div>
	<br />';
$output .= '
		<table class="hidden">
			<tr>
				<td>';
					if ( ($user_lvl >= $action_permission['insert']) )
					{
					makebutton('Add Gold', 'rewards.php?action=add_reward_gold', 130);
$output .= '
				</td>';
					}
$output .= '
		</table>
</center>';
unset($gold);
}

//#############################################################################
// ADD GOLD REWARDS
//#############################################################################
function add_reward_gold(&$sqlm)
{
	global 	$output, $lang_global, $lang_rewards,
			$action_permission, $user_lvl;

	valid_login($action_permission['insert']);

$output .= '
<center>
	<fieldset class="half_frame">
		<legend>ADD GOLD</legend>
			<form action="rewards.php?action=do_add_reward_gold" method="post" name="form">
			<table class="flat">
				<tr>
					<td>Gold</td>
					<td><input type="text" name="value" size="40" maxlength="12" value="" /></td>
				</tr>
				<tr>
					<td>Points</td>
					<td><input type="text" name="points" size="40" maxlength="10" value="" /></td>
				</tr>';
$output .= '
		<table class="hidden">
			<tr>
				<td>';
                      makebutton('Submit', 'javascript:do_submit()', 130);
$output .= '
				</td>				  
			</table>
			</form>
		</fieldset>
		<br /><br />';
}

//#############################################################################
// DO ADD GOLD REWARDS
//#############################################################################
function do_add_reward_gold(&$sqlm)
{
	global 	$action_permission,
			$mmfpm_db;
	
	valid_login($action_permission['insert']);

	if
	(
		empty($_POST['value']) ||
		empty($_POST['points'])
	)
	redirect('rewards.php?error=1');

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	$gold   = $sqlm->quote_smart($_POST['value']);
	$points = $sqlm->quote_smart($_POST['points']);

	$query = $sqlm->query('INSERT INTO mm_reward_gold SET value=\''.$gold.'\', points =\''.$points.'\'');

unset($gold);
unset($points);

	if ($sqlm->affected_rows())
		redirect('rewards.php?action=show_reward_gold');
	else
		redirect('rewards.php?error=2');
}

//#############################################################################
// EDIT GOLD REWARDS
//#############################################################################
function edit_reward_gold(&$sqlm)
{
	global 	$output, $lang_rewards, $lang_global,
			$mmfpm_db,
			$action_permission;

			valid_login($action_permission['update']);

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if(empty($_GET['value'])) redirect('rewards.php?error=1');
	$gold = $sqlm->quote_smart($_GET['value']);
	if(is_numeric($gold)); else redirect('rewards.php?error=1');

	if($gold = $sqlm->fetch_assoc($sqlm->query('SELECT value, points FROM mm_reward_gold WHERE value = '.$gold.'')))
	{
$output .= '
<center>
	<fieldset class="half_frame">
		<legend>EDIT GOLD</legend>
			<form action="rewards.php?action=do_edit_reward_gold" method="post" name="form">
			<table class="flat">
				<tr>
					<td>Gold</td>
					<td><input type="text" name="value" size="40" maxlength="12" value="'.$gold['value'].'" /></td>
				</tr>
				<tr>
					<td>Points</td>
					<td><input type="text" name="points" size="40" maxlength="10" value="'.$gold['points'].'" /></td>
				</tr>';
$output .= '
		<table class="hidden">
			<tr>
				<td>';
                      makebutton('Submit', 'javascript:do_submit()', 130);
$output .= '
				</td>				  
			</table>
			</form>
		</fieldset>
		<br /><br />';
	}
}

//#############################################################################
// DO EDIT GOLD REWARDS
//#############################################################################
function do_edit_reward_gold(&$sqlm)
{
	global 	$action_permission,
			$mmfpm_db;
	
	valid_login($action_permission['insert']);

	if
	(
		empty($_POST['value']) ||
		empty($_POST['points'])
	)
	redirect('rewards.php?error=1');

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	$gold   = $sqlm->quote_smart($_POST['value']);
	$points = $sqlm->quote_smart($_POST['points']);

	$query = $sqlm->query('UPDATE mm_reward_gold SET value=\''.$gold.'\', points =\''.$points.'\' WHERE value = '.$gold.'');

unset($gold);
unset($points);

	if ($sqlm->affected_rows())
		redirect('rewards.php?action=show_reward_gold');
	else
		redirect('rewards.php?error=2');
}

//#############################################################################
// DELETE GOLD REWARDS
//#############################################################################
function delete_reward_gold(&$sqlm)
{
	global 	$output, $lang_rewards, $lang_global,
			$mmfpm_db,
			$action_permission;

			valid_login($action_permission['delete']);

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if(empty($_GET['value'])) redirect('rewards.php?error=1');
	$gold = $sqlm->quote_smart($_GET['value']);
	if(is_numeric($gold)); else redirect('rewards.php?error=1');

$output .= '
<center>
<h1><font class="error">'.$lang_global['are_you_sure'].'</font></h1>
<br />
<font class="bold">'.$gold.'<br />'.$lang_global['will_be_erased'].'</font>
<br /><br />
	<table width="300" class="hidden">
		<tr>
			<td>';
				makebutton($lang_global['yes'], 'rewards.php?action=do_delete_reward_gold&amp;value='.$gold.'" type ="wrn', 130);
				makebutton($lang_global['no'], 'rewards.php" type="def', 130);
unset($gold);
$output .= '
			</td>
		</tr>
	</table>
</center>';
}

//#############################################################################
// DO DELETE GOLD REWARDS
//#############################################################################
function do_delete_reward_gold(&$sqlm)
{
	global 	$action_permission, $mmfpm_db;

	valid_login($action_permission['delete']);

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if(empty($_GET['value'])) redirect('rewards.php?error=1');
	$gold = $sqlm->quote_smart($_GET['value']);
	if(is_numeric($gold)); else redirect('rewards.php?error=1');

	$sqlm->query('DELETE FROM mm_reward_gold WHERE value = '.$gold.'');
unset($gold);

	if ($sqlm->affected_rows())
		redirect('rewards.php?action=show_reward_gold');
	else
		redirect('rewards.php?error=2');
}

//#############################################################################
// ITEM REWARDS
//#############################################################################
function show_reward_item(&$sqlm, &$sqlw)
{
global 	$output, $lang_global, $lang_rewards,
		$mmfpm_db, $world_db, $realm_id,
		$action_permission, $user_lvl,
		$item_datasite;
		
		// this page uses wowhead tooltops
		wowhead_tt();

$output .= '
<center>
	<div id="tab_content">
		<div id="tab">
			<ul>
				<li><a href="rewards.php">INFO</a></li>
				<li><a href="rewards.php?action=char_select">SELECT CHAR</a></li>
				<li><a href="rewards.php?action=show_reward_gold">GOLD</a></li>
				<li  id="selected"><a href="rewards.php?action=show_reward_item">ITEM</a></li>
			</ul>
		</div>
		<div id="tab_content2">
			<table class="lined" style="width: 450px;">
				<tr>';
	if($user_lvl >= $action_permission['delete'])
$output .= '
					<th width="1%">'.$lang_global['delete'].'</th>
					<th width="1%">'.$lang_global['edit'].'</th>';
$output .= '
					<th width="15%">item</th>
					<th width="69%">name</th>
					<th width="10%">quantity</th>
					<th width="10%">points</th>
				</tr>';

// main data that we need for this page

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

$result = $sqlm->query('SELECT item, quantity, points FROM mm_reward_item');

	while($items = $sqlm->fetch_assoc($result))
	{
$output .= '
				<tr valign="middle">';
	if($user_lvl >= $action_permission['delete'])
$output .= '
					<td><a href="rewards.php?action=delete_reward_item&amp;item='.$items['item'].'"><img src="img/aff_cross.png" alt="" /></a></td>
					<td><a href="rewards.php?action=edit_reward_item&amp;item='.$items['item'].'"><img src="img/edit.png" alt="" /></a></td>';
$output .= '
					<td>
						<a style="padding:2px;" href="'.$item_datasite.$items['item'].'" target="_blank">
							<img src="'.get_item_icon($items['item'], $sqlm, $sqlw).'" class="'.get_item_border($items['item'], $sqlw).'" alt="'.$items['item'].'" />
						</a>
					</td>
					<td>
						'.get_item_name($items['item'], $sqlw).'
					</td>
					<td>
						'.$items['quantity'].'
					</td>
					<td>
						'.$items['points'].'
					</td>
				</tr>';
	}
$output .= '
			</table>
		</div>
	</div>
	<br />';
$output .= '
		<table class="hidden">
			<tr>
				<td>';
					if ( ($user_lvl >= $action_permission['insert']) )
					{
					makebutton('Add Item', 'rewards.php?action=add_reward_item', 130);
$output .= '
				</td>';
					}
$output .= '
		</table>
</center>';
unset($items);
}

//#############################################################################
// ADD ITEM REWARDS
//#############################################################################
function add_reward_item(&$sqlm)
{
	global 	$output, $lang_global, $lang_rewards,
			$action_permission, $user_lvl;

	valid_login($action_permission['insert']);

    $output .= '
<center>
	<fieldset class="half_frame">
		<legend>ADD ITEM</legend>
			<form action="rewards.php?action=do_add_reward_item" method="post" name="form">
			<table class="flat">
				<tr>
					<td>Item</td>
					<td><input type="text" name="item" size="40" maxlength="12" value="" /></td>
				</tr>
				<tr>
					<td>Quantity</td>
					<td><input type="text" name="quantity" size="40" maxlength="3" value="" /></td>
				</tr>
				<tr>
					<td>Points</td>
					<td><input type="text" name="points" size="40" maxlength="10" value="" /></td>
				</tr>';
$output .= '
		<table class="hidden">
			<tr>
				<td>';
                      makebutton('Update', 'javascript:do_submit()', 130);
$output .= '
				</td>				  
			</table>
			</form>
		</fieldset>
		<br /><br />';
}

//#############################################################################
// DO ADD ITEM REWARDS
//#############################################################################
function do_add_reward_item(&$sqlm)
{
	global 	$action_permission,
			$mmfpm_db;
	
	valid_login($action_permission['insert']);

	if
	(
		empty($_POST['item']) ||
		empty($_POST['quantity']) ||
		empty($_POST['points'])
	)
	redirect('rewards.php?error=1');

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	$item      = $sqlm->quote_smart($_POST['item']);
	$quantity  = $sqlm->quote_smart($_POST['quantity']);
	$points    = $sqlm->quote_smart($_POST['points']);

	$query = $sqlm->query('INSERT INTO mm_reward_item SET item=\''.$item.'\', quantity=\''.$quantity.'\', points =\''.$points.'\'');

unset($item);
unset($quantity);
unset($points);

	if ($sqlm->affected_rows())
		redirect('rewards.php?action=show_reward_item');
	else
		redirect('rewards.php?error=2');
}

//#############################################################################
// EDIT ITEM REWARDS
//#############################################################################
function edit_reward_item(&$sqlm)
{
	global 	$output, $lang_rewards, $lang_global,
			$mmfpm_db,
			$action_permission;

			valid_login($action_permission['update']);

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if(empty($_GET['item'])) redirect('rewards.php?error=1');
	$item = $sqlm->quote_smart($_GET['item']);
	if(is_numeric($item)); else redirect('rewards.php?error=1');

	if($item = $sqlm->fetch_assoc($sqlm->query('SELECT item, quantity, points FROM mm_reward_item WHERE item = '.$item.'')))
	{
$output .= '
<center>
	<fieldset class="half_frame">
		<legend>EDIT ITEM</legend>
			<form action="rewards.php?action=do_edit_reward_item" method="post" name="form">
			<table class="flat">
				<tr>
					<td>item</td>
					<td><input type="text" name="item" size="40" maxlength="12" value="'.$item['item'].'" /></td>
				</tr>
				<tr>
					<td>Quantity</td>
					<td><input type="text" name="quantity" size="40" maxlength="3" value="'.$item['quantity'].'" /></td>
				</tr>
				<tr>
					<td>Points</td>
					<td><input type="text" name="points" size="40" maxlength="10" value="'.$item['points'].'" /></td>
				</tr>';
$output .= '
		<table class="hidden">
			<tr>
				<td>';
                      makebutton('Submit', 'javascript:do_submit()', 130);
$output .= '
				</td>				  
			</table>
			</form>
		</fieldset>
		<br /><br />';
	}
}

//#############################################################################
// DO EDIT ITEM REWARDS
//#############################################################################
function do_edit_reward_item(&$sqlm)
{
	global 	$action_permission,
			$mmfpm_db;
	
	valid_login($action_permission['insert']);

	if
	(
		empty($_POST['item']) ||
		empty($_POST['quantity']) ||
		empty($_POST['points'])
	)
	redirect('rewards.php?error=1');

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	$item      = $sqlm->quote_smart($_POST['item']);
	$quantity  = $sqlm->quote_smart($_POST['quantity']);
	$points    = $sqlm->quote_smart($_POST['points']);

	$query = $sqlm->query('UPDATE mm_reward_item SET item=\''.$item.'\', quantity=\''.$quantity.'\', points =\''.$points.'\' WHERE item = '.$item.'');

unset($item);
unset($quantity);
unset($points);

	if ($sqlm->affected_rows())
		redirect('rewards.php?action=show_reward_item');
	else
		redirect('rewards.php?error=2');
}

//#############################################################################
// DELETE ITEM REWARDS
//#############################################################################
function delete_reward_item(&$sqlm)
{
	global 	$output, $lang_rewards, $lang_global,
			$action_permission, $mmfpm_db;

			valid_login($action_permission['delete']);

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if(empty($_GET['item'])) redirect('rewards.php?error=1');
	$items = $sqlm->quote_smart($_GET['item']);
	if(is_numeric($items)); else redirect('rewards.php?error=1');

$output .= '
<center>
<h1><font class="error">'.$lang_global['are_you_sure'].'</font></h1>
<br />
<font class="bold">'.$items.'<br />'.$lang_global['will_be_erased'].'</font>
<br /><br />
	<table width="300" class="hidden">
		<tr>
			<td>';
				makebutton($lang_global['yes'], 'rewards.php?action=do_delete_reward_item&amp;item='.$items.'" type ="wrn', 130);
				makebutton($lang_global['no'], 'rewards.php" type="def', 130);
unset($items);
$output .= '
			</td>
		</tr>
	</table>
</center>';
}

//#############################################################################
// DO DELETE ITEM REWARDS
//#############################################################################
function do_delete_reward_item(&$sqlm)
{
	global 	$action_permission, $mmfpm_db;

	valid_login($action_permission['delete']);

	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if(empty($_GET['item'])) redirect('rewards.php?error=1');
	$items = $sqlm->quote_smart($_GET['item']);
	if(is_numeric($items)); else redirect('rewards.php?error=1');

	$sqlm->query('DELETE FROM mm_reward_item WHERE item = '.$items.'');
unset($items);

	if ($sqlm->affected_rows())
		redirect('rewards.php?action=show_reward_item');
	else
		redirect('rewards.php?error=2');
}

//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
	<div class="top">';

// $lang_rewards = lang_rewards();

if (1 == $err)
$output .= '
	<h1><font class="error">'.$lang_global['empty_fields'].'</font></h1>';
elseif (2 == $err)
  $output .= '
            <h1><font class="error">Didnt Work</font></h1>';
else
$output .= '
	<h1>Rewards</h1>';

unset($err);

$output .= '
	</div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('info' == $action)
info();
elseif ('char_select' == $action)
char_select($sqlr, $sqlm, $sqlc);
elseif ('show_reward_gold' == $action)
show_reward_gold($sqlm);
elseif ('add_reward_gold' == $action)
add_reward_gold($sqlm);
elseif ('do_add_reward_gold' == $action)
do_add_reward_gold($sqlm);
elseif ('edit_reward_gold' == $action)
edit_reward_gold($sqlm);
elseif ('do_edit_reward_gold' == $action)
do_edit_reward_gold($sqlm);
elseif ('delete_reward_gold' == $action)
delete_reward_gold($sqlm);
elseif ('do_delete_reward_gold' == $action)
do_delete_reward_gold($sqlm);
elseif ('show_reward_item' == $action)
show_reward_item($sqlm, $sqlw);
elseif ('add_reward_item' == $action)
add_reward_item($sqlm);
elseif ('do_add_reward_item' == $action)
do_add_reward_item($sqlm);
elseif ('edit_reward_item' == $action)
edit_reward_item($sqlm);
elseif ('do_edit_reward_item' == $action)
do_edit_reward_item($sqlm);
elseif ('delete_reward_item' == $action)
delete_reward_item($sqlm);
elseif ('do_delete_reward_item' == $action)
do_delete_reward_item($sqlm);
else
info();

unset($action);
unset($action_permission);
// unset($lang_rewards);

require_once 'footer.php';


?>
