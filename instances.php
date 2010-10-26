<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/map_zone_lib.php';
require_once 'libs/char_lib.php'; // to get char_get_level_color, need to move that function to a more general file
// minimum permission to view page
valid_login($action_permission['read']);

// YEA YEA, i know this page looks like carnival, but i was basicly testing stuff and dont want to revert
//#############################################################################
// INSTANCES
//#############################################################################
function instances()
{
	global 	$output, $lang_instances,
			$realm_id,
			$world_db, $mmfpm_db,
			$itemperpage;

$sqlw = new SQL;
$sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

//-------------------SQL Injection Prevention--------------------------------
// this page has multipage support and field ordering, so we need these
$start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
if (is_numeric($start)); else $start=0;

$order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : 'levelMin';
if (preg_match('/^[_[:lower:]]{1,11}$/', $order_by)); else $order_by='levelMin';

$dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

$order_dir = ($dir) ? 'ASC' : 'DESC';
$dir = ($dir) ? 0 : 1;
//-------------------SQL Injection Prevention--------------------------------

	// for multipage support
	$all_record = $sqlw->result($sqlw->query('SELECT count(*) FROM instance_template'), 0);

	// main data that we need for this page, instances
	$result = $sqlw->query('
		SELECT map, levelMin, levelMax
		FROM instance_template
		ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.';');

//---------------Page Specific Data Starts Here--------------------------

$output .= '
<center>
<table class="top_hidden">
	<tr>
		<td width="25%" align="right">';

			// multi page links
$output .=
			$lang_instances['total'].' : '.$all_record.'<br /><br />'.
			generate_pagination('instances.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $all_record, $itemperpage, $start);

			// column headers, with links for sorting
$output .= '
		</td>
	</tr>
</table>
<table class="lined">
	<tr>
		<th width="20%"><a href="instances.php?order_by=map&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='map' ? ' class="'.$order_dir.'"' : '').'>'.$lang_instances['map'].'</a></th>
		<th width="5%"><a href="instances.php?order_by=levelMin&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='levelMin' ? ' class="'.$order_dir.'"' : '').'>'.$lang_instances['level_min'].'</a></th>
		<th width="5%"><a href="instances.php?order_by=levelMax&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='levelMax' ? ' class="'.$order_dir.'"' : '').'>'.$lang_instances['level_max'].'</a></th>
		<th width="20%">'.$lang_instances['area'].'</th>
		<th width="10%">'.$lang_instances['type'].'</th>
		<th width="5%">'.$lang_instances['expansion'].'</th>
		<th width="5%">'.$lang_instances['ppl'].'</th>
	</tr>';

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
	while ($instances = $sqlw->fetch_assoc($result))
	{
	
$output .= '
	<tr valign="top">
		<td>'.get_map_name($instances['map'], $sqlm).'</td>
		<td>'.char_get_level_color($instances['levelMin']).'</td>
		<td>'.char_get_level_color($instances['levelMax']).'</td>
		<td>'.get_map_zone($instances['map'], $sqlm).'</td>
		<td>'.get_map_type($instances['map'], $sqlm).'</td>
		<td>'.get_map_exp($instances['map'], $sqlm).'</td>
		<td>'.get_map_ppl($instances['map'], $sqlm).'</td>
	</tr>';
	}
unset($reset);
unset($hours);
unset($days);
unset($instances);
unset($result);

$output .= '
</table>
<table class="top_hidden">
	<tr>
		<td width="25%" align="right">';

			// multi page links
$output .=
			$lang_instances['total'].' : '.$all_record.'<br /><br />'.
			generate_pagination('instances.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
unset($start);
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

$lang_instances = lang_instances();

$output .= '
	<div class="top">
		<h1>'.$lang_instances['instances'].'</h1>
	</div>';

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

instances();

//unset($action);
unset($action_permission);
unset($lang_instances);

require_once 'footer.php';

?>
