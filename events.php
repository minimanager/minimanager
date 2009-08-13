<?php


require_once("header.php");
valid_login($action_permission['read']);

//#############################################################################
// SEARCH
//#############################################################################
function do_search()
{
  global $lang_events, $output, $world_db, $realm_id, $itemperpage;

  $sqlw = new SQL;
  $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sqlw->quote_smart($_GET['start']) : 0;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $start)) $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlw->quote_smart($_GET['order_by']) : "description";
  if (!preg_match("/^[_[:lower:]]{1,12}$/", $order_by)) $order_by="description";

  $dir = (isset($_GET['dir'])) ? $sqlw->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  $query_1 = $sqlw->query("SELECT count(*) FROM game_event");
  $result = $sqlw->query("SELECT description, start_time, end_time, occurence, length
    FROM game_event WHERE start_time <> end_time ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
  $all_record = $sqlw->result($query_1,0);
  unset($query_1);

  $output .= "
        <center>
          <table class=\"top_hidden\">
            <tr>
              <td width=\"25%\" align=\"right\">";
  $output .= generate_pagination("events.php?order_by=$order_by&amp;dir=".(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
  $output .= "
              </td>
            </tr>
          </table>
          <table class=\"lined\">
            <tr>
               <th width=\"35%\"><a href=\"events.php?order_by=description&amp;start=$start&amp;dir=$dir\">".($order_by=='description' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_events['descr']}</a></th>
               <th width=\"25%\"><a href=\"events.php?order_by=start_time&amp;start=$start&amp;dir=$dir\">".($order_by=='start_time' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_events['start']}</a></th>
               <th width=\"20%\"><a href=\"events.php?order_by=occurence&amp;start=$start&amp;dir=$dir\">".($order_by=='occurence' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_events['occur']}</a></th>
               <th width=\"20%\"><a href=\"events.php?order_by=length&amp;start=$start&amp;dir=$dir\">".($order_by=='length' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_events['length']}</a></th>
            </tr>";
  while ($events = $sqlw->fetch_row($result))
  {
    $days = floor(round($events[3] / 60)/24);
    $hours = round($events[3] / 60) - ($days * 24);
    $event_occurance = "";
    if ($days > 0)
    {
      $event_occurance .= $days;
      $event_occurance .= " days ";
    }
    if ($hours > 0)
    {
      $event_occurance .= $hours;
      $event_occurance .= " hours";
    }
    $days = floor(round($events[4] / 60)/24);
    $hours = round($events[4] / 60) - ($days * 24);
    $event_duration = "";
    if ($days > 0)
    {
      $event_duration .= $days;
      $event_duration .= " days ";
    }
    if ($hours > 0)
    {
      $event_duration .= $hours;
      $event_duration .= " hours";
    }
    $output .= "
            <tr valign='top'>
              <td align='left'>$events[0]</td>
              <td>".$events[1]."</td>
              <td>$event_occurance</td>
              <td>$event_duration</td>
            </tr>";
  }
  $output .= "
            <tr>
              <td colspan=\"4\" class=\"hidden\" align=\"right\" width=\"25%\">";
  $output .= generate_pagination("events.php?order_by=$order_by&amp;dir=".(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
  $output .= "
              </td>
            </tr>
            <tr>
              <td colspan=\"4\" class=\"hidden\" align=\"right\">{$lang_events['total']} : $all_record</td>
            </tr>
          </table>
        </center>
";


}


//#############################################################################
// MAIN
//#############################################################################

$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

unset($err);

$lang_events = lang_events();

$output .= "
        <div class=\"top\"><h1>$lang_events[events]</h1></div>";


$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "do_search":
   do_search();
   break;
  default:
   do_search();
}

unset($action);
unset($action_permission);
unset($lang_events);

require_once("footer.php");

?>
