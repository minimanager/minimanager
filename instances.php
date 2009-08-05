<?php

require_once("header.php");
valid_login($action_permission['read']);


//#############################################################################
// SEARCH
//#############################################################################
function do_search()
{
  global $lang_instances, $output, $world_db, $realm_id, $server_type;

  $sql = new SQL;
  $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

  $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;

  $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) :"level_min";
  $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;

  if ($server_type)
    $result = $sql->query("SELECT `map`, `level_min`, `level_max`, `maxPlayers`, `reset_delay` FROM `instance_template` JOIN access_requirement ON access_requirement.id = instance_template.access_id ORDER BY $order_by $order_dir;");
  else
    $result = $sql->query("SELECT `map`, `levelMin` as level_min, `levelMax` as level_max, `maxPlayers`, `reset_delay` FROM `instance_template` ORDER BY $order_by $order_dir;");

  $total_found = $sql->num_rows($result);

  $output .= "
        <center>
          <table class=\"top_hidden\">
            <tr>
              <td>
              </td>
              <td align=\"right\">Total: $total_found</td>
            </tr>
          </table>
          <table class=\"lined\">
            <tr>
              <th width=\"40%\"><a href=\"instances.php?order_by=map&amp;start=$start&amp;dir=$dir\">".($order_by=='map' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['map']}</a></th>
              <th width=\"15%\"><a href=\"instances.php?order_by=level_min&amp;start=$start&amp;dir=$dir\">".($order_by=='level_min' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['level_min']}</a></th>
              <th width=\"15%\"><a href=\"instances.php?order_by=level_max&amp;start=$start&amp;dir=$dir\">".($order_by=='level_max' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['level_max']}</a></th>
              <th width=\"15%\"><a href=\"instances.php?order_by=maxPlayers&amp;start=$start&amp;dir=$dir\">".($order_by=='maxPlayers' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['max_players']}</a></th>
              <th width=\"15%\"><a href=\"instances.php?order_by=reset_delay&amp;start=$start&amp;dir=$dir\">".($order_by=='reset_delay' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_instances['reset_delay']}</a></th>
            </tr>";

  for ($i=1; $i<=$total_found; $i++)
  {
    $instances = $sql->fetch_array($result);

    $days = floor(round($instances[4] / 3600)/24);
    $hours = round($instances[4] / 3600) - ($days * 24);
    $reset = "";
    if ($days > 0)
    {
      $reset .= $days;
      $reset .= " days ";
    }
    if ($hours > 0)
    {
      $reset .= $hours;
      $reset .= " hours";
    }

    $output .= "
            <tr valign=top>
              <td>".get_map_name($instances[0])."</td>
              <td>$instances[1]</td>
              <td>$instances[2]</td>
              <td>$instances[3]</td>
              <td>$reset</td>
            </tr>";
  }
  $output .= "
          </table>
        </center>
        <br />";

  $sql->close();
  unset($sql);
}


//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;
$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;
$output .= "
        <div class=\"top\">
          <h1>Instances</h1>
        </div>";

switch ($action)
{
  case "do_search":
    do_search();
    break;
  default:
    do_search();
}

require_once("footer.php");

?>
