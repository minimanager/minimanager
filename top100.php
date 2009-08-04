<?php

require_once("header.php");
require_once("scripts/defines.php");
require_once("scripts/get_lib.php");
valid_login($action_permission['read']);

function top100()
{
  global $lang_top, $output, $realm_id, $characters_db, $itemperpage, $CHAR_RANK, $CHAR_RACE;
  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  //==========================$_GET and SECURE========================
  $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $start)) $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "honor";
  if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by)) $order_by="guid";

  $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "DESC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================

  $result = $sql->query("SELECT guid, name, race, class, account, totaltime, online,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_POINTS+1)."), ' ', -1) AS UNSIGNED) AS honor,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_KILL+1)."), ' ', -1) AS UNSIGNED) AS kills,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_ARENA_POINTS+1)."), ' ', -1) AS UNSIGNED) AS arena,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_GUILD_ID+1)."), ' ', -1) AS UNSIGNED) as GNAME,
    mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_GOLD+1)."), ' ', -1) AS UNSIGNED) as money
    FROM `characters` ORDER BY $order_by $order_dir LIMIT  $start, $itemperpage");

  $query_1 = $sql->query("SELECT count(*) FROM characters");
  $all_record = $sql->result($query_1,0);
  $all_record = (($all_record < 100) ? $all_record : 100);

  //==========================top tage navigaion starts here========================
  $output .="
        <script type=\"text/javascript\" src=\"js/check.js\"></script>
        <center>
          <table class=\"top_hidden\">
            <tr>
              <td align=\"right\">Total: ";
  $output .= $all_record;
  $output .= "
              </td>
              <td align=\"right\" width=\"30%\">";
  $output .= generate_pagination("top100.php?order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
  $output .= "
              </td>
            </tr>
          </table>";
  //==========================top tage navigaion ENDS here ========================

  $output .= "
          <table class=\"lined\">
            <tr>
              <th width=\"1%\">{$lang_top['name']}</th>
              <th width=\"1%\">{$lang_top['race']}</th>
              <th width=\"1%\">{$lang_top['class']}</th>
              <th width=\"1%\"><a href=\"top100.php?order_by=level&amp;start=$start&amp;dir=$dir\"".($order_by=='level' ? " class=\"$order_dir\"" : "").">{$lang_top['level']}</a></th>
              <th width=\"10%\">{$lang_top['guild']}</th>
              <th width=\"10%\"><a href=\"top100.php?order_by=money&amp;start=$start&amp;dir=$dir\"".($order_by=='money' ? " class=\"$order_dir\"" : "").">{$lang_top['money']}</a></th>
              <th width=\"1%\"><a href=\"top100.php?order_by=honor&amp;start=$start&amp;dir=$dir\"".($order_by=='honor' ? " class=\"$order_dir\"" : "").">{$lang_top['rank']}</a></th>
              <th width=\"1%\">{$lang_top['honor_points']}</th>
              <th width=\"1%\"><a href=\"top100.php?order_by=kills&amp;start=$start&amp;dir=$dir\"".($order_by=='kills' ? " class=\"$order_dir\"" : "").">{$lang_top['kills']}</a></th>
              <th width=\"1%\"><a href=\"top100.php?order_by=arena&amp;start=$start&amp;dir=$dir\"".($order_by=='arena' ? " class=\"$order_dir\"" : "").">{$lang_top['arena_points']}</a></th>
              <th width=\"10%\"><a href=\"top100.php?order_by=totaltime&amp;start=$start&amp;dir=$dir\"".($order_by=='totaltime' ? " class=\"$order_dir\"" : "").">{$lang_top['time_played']}</a></th>
              <th width=\"1%\">{$lang_top['online']}</th>
            </tr>";

  for ($i=0; $i<$itemperpage; $i++)
  {
    $char = $sql->fetch_array($result);
    $guild_name = $sql->fetch_row($sql->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char[11].";"));

    $money_gold = (int)($char[13]/10000);
	$money_silver = (int)(($char[13]-$money_gold*10000)/100);
    $money_cooper = (int)($char[13]-$money_gold*10000-$money_silver*100);

    $money = $money_gold."<img src=\"./img/gold.gif\" /> ".$money_silver."<img src=\"./img/silver.gif\" /> ".$money_cooper."<img src=\"./img/copper.gif\" /> ";

    $days = floor(round($char[5] / 3600)/24);
    $hours = round($char[5] / 3600) - ($days * 24);
    $time = "";
    if ($days > 0)
    {
      $time .= $days;
      $time .= " days ";
    }
    if ($hours > 0)
    {
      $time .= $hours;
      $time .= " hours";
    }

    $output .= "
            <tr valign=top>
              <td><a href=\"char.php?id=$char[0]\">".htmlentities($char[1])."</a></td>
              <td><img src='img/c_icons/{$char[2]}-{$char[12]}.gif' onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
              <td><img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
              <td>".get_level_with_color($char[9])."</td>
              <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[11]\">".htmlentities($guild_name[0])."</a></td>
              <td>$money</td>
              <td><span onmouseover='toolTip(\"".$CHAR_RANK[$CHAR_RACE[$char[2]][1]][pvp_ranks($char[7])]."\",\"item_tooltip\")' onmouseout='toolTip()' style='color: white;'><img src='img/ranks/rank".pvp_ranks($char[7],$CHAR_RACE[$char[2]][1]).".gif'></span></td>
              <td>$char[7]</td>
              <td>$char[8]</td>
              <td>$char[10]</td>
              <td>$time</td>
              <td>".(($char[6]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
            </tr>";

  }

  $output .= "
            </table>
          </center>
          <br />
";

  $sql->close();
}

//########################################################################################################################
// MAIN
//########################################################################################################################

$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;
$output .= "
        <div class=\"top\">";
switch ($err)
{
  case 1:
    break;
  default:
    $output .= "
          <h1>{$lang_top['top100']}</h1>";
}
$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "unknown" :
    break;
  default :
    top100();
}

require_once("footer.php");

?>
