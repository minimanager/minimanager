<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

function get_group_type($id)
{
	$type = array
    (
		0 => 'Party',
		1 => 'BG',
		2 => 'Raid',
		4 => 'LFD'
    );

  return $type[$id];
}

function get_loot_method($id)
{
	$loot = array
    (
		0 => 'Free For All',
		1 => 'Round Robin',
		2 => 'Master Loot',
		3 => 'Group Loot',
		4 => 'Need Before Greed'
    );

  return $loot[$id];
}

//#############################################################################
// groups
//#############################################################################
function groups(&$sqlc)
{
	global 	$output,
			$lang_group,
			$itemperpage;

$start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
if (is_numeric($start)); else $start=0;

$order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : 'groupId';
if (preg_match('/^[_[:lower:]]{1,11}$/', $order_by)); else $order_by='groupId';

$dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

$order_dir = ($dir) ? 'ASC' : 'DESC';
$dir = ($dir) ? 0 : 1;

// for multipage support
$all_record = $sqlc->result($sqlc->query('SELECT count(*) FROM groups'), 0);

// main data that we need for this page, instances
	$result = $sqlc->query('
		SELECT groupId, leaderGuid, mainTank, mainAssistant, lootMethod, groupType, difficulty, raiddifficulty
		FROM groups
		ORDER BY '.$order_by.' '.$order_dir.'
		LIMIT '.$start.', '.$itemperpage.';');

$output .= '
<center>
<table class="top_hidden">
	<tr>
		<td width="25%" align="right">';
		// multi page links
		$output .= '
		'.$lang_group['tot_group'].': '.$all_record.'<br /><br />'.
		generate_pagination('groups.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
		// column headers, with links for sorting
		$output .= '
		</td>
	</tr>
</table>
<table class="lined">
	<tr>
		<th width="1%"><a href="groups.php?order_by=groupId&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='groupId' ? ' class="'.$order_dir.'"' : '').'>'.$lang_group['id'].'</a></th>
		<th width="10%"><a href="groups.php?order_by=leaderGuid&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='leaderGuid' ? ' class="'.$order_dir.'"' : '').'>'.$lang_group['leader'].'</a></th>
		<th width="10%"><a href="groups.php?order_by=mainTank&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='mainTank' ? ' class="'.$order_dir.'"' : '').'>'.$lang_group['mtank'].'</a></th>
		<th width="10%"><a href="groups.php?order_by=mainAssistant&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='mainAssistant' ? ' class="'.$order_dir.'"' : '').'>'.$lang_group['massistant'].'</a></th>
		<th width="10%"><a href="groups.php?order_by=lootMethod&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='lootMethod' ? ' class="'.$order_dir.'"' : '').'>'.$lang_group['loot'].'</a></th>
		<th width="10%"><a href="groups.php?order_by=groupType&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='groupType' ? ' class="'.$order_dir.'"' : '').'>'.$lang_group['type'].'</a></th>
	</tr>';

	while ($groups = $sqlc->fetch_assoc($result))
	{
$output .= '
	<tr valign="top">
		<td><a href="groups.php?action=party&amp;id='.$groups['groupId'].'">'.$groups['groupId'].'</td>
		<td>'.get_char_name($groups['leaderGuid']).'</td>
		<td>'.get_char_name($groups['mainTank']).'</td>
		<td>'.get_char_name($groups['mainAssistant']).'</td>
		<td>'.get_loot_method($groups['lootMethod']).'</td>
		<td>'.get_group_type($groups['groupType']).'</td>
	</tr>';
	}
unset($groups);
unset($result);

$output .= '
	<tr>
</table>
<table class="top_hidden">
	<tr>
		<td width="25%" align="right">';
		// multi page links
		$output .= '
		'.$lang_group['tot_group'].': '.$all_record.'<br /><br />'.
		generate_pagination('groups.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
unset($start);
		// column headers, with links for sorting
		$output .= '
		</td>
	</tr>
</table>
</center>';

}

//#############################################################################
// Party
//#############################################################################
function party(&$sqlc)
{
	global 	$output,
			$lang_group;

//==========================$_GET and SECURE=================================
	$id = $sqlc->quote_smart($_GET['id']);
//==========================$_GET and SECURE end=============================

$output .= '
<center>';

// main data that we need for this page, instances
	$result = $sqlc->query('
		SELECT groupId, leaderGuid, mainTank, mainAssistant, groupType, difficulty, raiddifficulty
		FROM groups');
	while ($index = $sqlc->fetch_assoc($result))
	{
		$result_1 = $sqlc->query('
			SELECT DISTINCT (subgroup)
			FROM group_member
			WHERE groupId = '.$id.' AND groupId = '.$index['groupId'].'');
			
		while ($group = $sqlc->fetch_assoc($result_1))
		{
$output .= '
<table class="flat" align="left">
	<tr>
		<td align="left">
			'.$lang_group['group'].' '.$group['subgroup'].'
		</td>
	</tr>
</table>
<table class="lined">
	<tr>
		<th width="10%">'.$lang_group['member'].'</th>
		<th width="1%">'.$lang_group['race'].'</th>
		<th width="1%">'.$lang_group['class'].'</th>
		<th width="1%">'.$lang_group['level'].'</th>
		<th width="10%">'.$lang_group['assistant'].'</th>
	</tr>';

			$result_2 = $sqlc->query('
				SELECT groupId, memberGuid, assistant
				FROM group_member
				WHERE groupId = '.$id.' AND groupId = '.$index['groupId'].' AND subgroup = '.$group['subgroup'].'');
				
			while ($party = $sqlc->fetch_assoc($result_2))
			{

				$result_3 = $sqlc->query('
					SELECT guid, name, race, class, level, gender
					FROM characters 
					WHERE guid = '.$party['memberGuid'].'');

				while ($member = $sqlc->fetch_assoc($result_3))
				{

$output .= '
	<tr valign="top">
		<td>'.$member['name'].'</td>
		<td><img src="img/c_icons/'.$member['race'].'-'.$member['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($member['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
		<td><img src="img/c_icons/'.$member['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($member['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
		<td>'.char_get_level_color($member['level']).'</td>
		<td>'.$party['assistant'].'</td>
	</tr>';
				}
			}
		}
	}
unset($index);
unset($group);
unset($party);
unset($member);
unset($result);
unset($result_1);
unset($result_2);
unset($result_3);

$output .= '
	<tr>
		<td colspan="3" class="hidden" align="right" width="25%">';
$output .= '
		</td>
	</tr>
</table>
</center>';

}

//#############################################################################
// MAIN
//#############################################################################

// error variable reserved for future use
//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//unset($err);

$lang_group = lang_group();

$output .= '
	<div class="top">
		<h1>'.$lang_group['groups'].'</h1>
	</div>';

// $_GET and SECURE
$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// define functions to be called by actions
if ('groups' == $action)
	groups($sqlc);
elseif ('party' == $action)
	party($sqlc);
else
	groups($sqlc);

unset($action);
unset($action_permission);
unset($lang_group);

require_once 'footer.php';

?>
