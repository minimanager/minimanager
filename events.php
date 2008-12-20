<?php
/*
 * Project Name: Event Module for MiniManager for Mangos Server
 * Date: 17.11.2007 version (0.0.2)
 * Author: Den Wailhorn
 * Edited by Shnappie to
 *         show days/hours instead of hours only
 *         make clickable orderings
 *         remove events with same start and end date
 */
require_once("header.php");
valid_login($action_permission['read']);

/*--------------------------------------------------*/

function do_search() {
 global $lang_global, $lang_events, $output, $world_db, $realm_id;

$sql = new SQL;
$sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) :"description";
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

 $result = $sql->query("SELECT `description`, `start_time`, `end_time`, `occurence`, `length` FROM `game_event` WHERE `start_time` <> `end_time` ORDER BY $order_by $order_dir");
 $total_found = $sql->num_rows($result);

  $output .= "<center><table class=\"top_hidden\">
       <tr><td>";
  $output .= "</td>
     <td align=\"right\">{$lang_events['total']}: $total_found</td>
 </tr></table>";

  $output .= "<table class=\"lined\">
   <tr>
   	<th width=\"35%\"><a href=\"events.php?order_by=description&amp;start=$start&amp;dir=$dir\">".($order_by=='description' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_events['descr']}</a></th>
   	<th width=\"25%\"><a href=\"events.php?order_by=start_time&amp;start=$start&amp;dir=$dir\">".($order_by=='start' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_events['start']}</a></th>
   	<th width=\"20%\"><a href=\"events.php?order_by=occurence&amp;start=$start&amp;dir=$dir\">".($order_by=='occurence' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_events['occur']}</a></th>
   	<th width=\"20%\"><a href=\"events.php?order_by=length&amp;start=$start&amp;dir=$dir\">".($order_by=='length' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_events['length']}</a></th>
  </tr>";

 for ($i=1; $i<=$total_found; $i++){
  $events = $sql->fetch_array($result);

  $days = floor(round($events['occurence'] / 60)/24);
  $hours = round($events['occurence'] / 60) - ($days * 24);
  $event_occurance = "";
  if ($days > 0) {
	$event_occurance .= $days;
	$event_occurance .= " days ";
	}
  if ($hours > 0){
  	$event_occurance .= $hours;
  	$event_occurance .= " hours";
  }

  $days = floor(round($events['length'] / 60)/24);
  $hours = round($events['length'] / 60) - ($days * 24);
  $event_duration = "";
  if ($days > 0) {
  	$event_duration .= $days;
  	$event_duration .= " days ";
  	}
  if ($hours > 0){
   	$event_duration .= $hours;
   	$event_duration .= " hours";
  }

  $output .= "<tr valign=top>
	<td align=left>$events[description]</td>
	<td>".$events['start_time']."</td>
	<td>$event_occurance</td>
	<td>$event_duration</td>
</tr>";
  }
  $output .= "</table></center><br />";

 $sql->close();
}

/*--------------------------------------------------*/

$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "<div class=\"top\"><h1>$lang_events[events]</h1></div>";

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
