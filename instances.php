<?php

require_once("header.php");
require_once("scripts/id_tab.php");
valid_login($action_permission['read']);

/*--------------------------------------------------*/

function do_search() {
 global $lang_instances, $lang_global, $output, $mangos_db, $realm_id;

$sql = new SQL;
$sql->connect($mangos_db[$realm_id]['addr'], $mangos_db[$realm_id]['user'], $mangos_db[$realm_id]['pass'], $mangos_db[$realm_id]['name']);

 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) :"levelMin";
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

$result = $sql->query("SELECT `map`, `levelMin`, `levelMax`, `maxPlayers`, `reset_delay` FROM `instance_template` ORDER BY $order_by $order_dir;");
$total_found = $sql->num_rows($result);

  $output .= "<center><table class=\"top_hidden\">
       <tr><td>";
  $output .= "</td>
     <td align=\"right\">Total: $total_found</td>
 </tr></table>";

  $output .= "<table class=\"lined\">
   <tr>
   	<th width=\"40%\"><a href=\"instances.php?order_by=map&amp;start=$start&amp;dir=$dir\">".($order_by=='map' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['map']}</a></th>
   	<th width=\"15%\"><a href=\"instances.php?order_by=levelMin&amp;start=$start&amp;dir=$dir\">".($order_by=='levelMin' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['level_min']}</a></th>
   	<th width=\"15%\"><a href=\"instances.php?order_by=levelMax&amp;start=$start&amp;dir=$dir\">".($order_by=='levelMax' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['level_max']}</a></th>
   	<th width=\"15%\"><a href=\"instances.php?order_by=maxPlayers&amp;start=$start&amp;dir=$dir\">".($order_by=='maxPlayers' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['max_players']}</a></th>
   	<th width=\"15%\"><a href=\"instances.php?order_by=reset_delay&amp;start=$start&amp;dir=$dir\">".($order_by=='reset_delay' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['reset_delay']}</a></th>
  </tr>";

  for ($i=1; $i<=$total_found; $i++){
  $instances = $sql->fetch_array($result);

  $days = floor(round($instances[4] / 3600)/24);
  $hours = round($instances[4] / 3600) - ($days * 24);
  $reset = "";
  if ($days > 0) {
  	$reset .= $days;
  	$reset .= " days ";
  	}
  if ($hours > 0){
   	$reset .= $hours;
   	$reset .= " hours";
  }

  $output .= "<tr valign=top>
	<td>".get_map_name($instances[0])."</td>
	<td>$instances[1]</td>
	<td>$instances[2]</td>
	<td>$instances[3]</td>
	<td>$reset</td>
</tr>";
  }
  $output .= "</table></center><br />";

 $sql->close();
}

/*--------------------------------------------------*/

$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "<div class=\"top\"><h1>Instances</h1></div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "do_search":
   do_search();
   break;
default:
    do_search();
}

require_once("footer.php");
?>
