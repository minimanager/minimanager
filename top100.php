<?php

require_once("header.php");
require_once("scripts/defines.php");
require_once("scripts/get_lib.php");
valid_login($action_permission['read']);

function top100($realmid)
{
  global $lang_top, $output, $realm_db, $characters_db, $server, $itemperpage, $developer_test_mode;
  $realm_id = $realmid;

  $sqlr = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  //==========================$_GET and SECURE========================
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $start)) $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : "honor";
  if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by)) $order_by="honor";

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "DESC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================

  $result = $sqlc->query("SELECT guid, name, race, class, account, totaltime, online,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_POINTS+1)."), ' ', -1) AS UNSIGNED) AS honor,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_KILL+1)."), ' ', -1) AS UNSIGNED) AS kills,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_ARENA_POINTS+1)."), ' ', -1) AS UNSIGNED) AS arena,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_GUILD_ID+1)."), ' ', -1) AS UNSIGNED) as GNAME,
    mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_GOLD+1)."), ' ', -1) AS UNSIGNED) as money
    FROM `characters` ORDER BY $order_by $order_dir LIMIT  $start, $itemperpage");

  $query_1 = $sqlc->query("SELECT count(*) FROM characters");
  $all_record = $sqlc->result($query_1,0);
  unset($query_1);
  $all_record = (($all_record < 100) ? $all_record : 100);

  //==========================top tage navigaion starts here========================
  $output .="
        <script type=\"text/javascript\" src=\"js/check.js\"></script>
        <center>
          <table class=\"top_hidden\">";
  if($developer_test_mode)
  {
    $realms = $sqlr->query("SELECT count(*) FROM `realmlist`");
    $tot_realms = $sqlr->result($realms, 0);
    if ($tot_realms > 1 && (count($server) >1))
    {
      $output .= "

            <tr>
              <td colspan=\"2\" align=\"left\">";
                makebutton('View', "javascript:do_submit('form".$realm_id."',0)",130);
      $output .= "
                <form action=\"top100.php\" method=\"get\" name=\"form".$realm_id."\">
                  Number of Realms :
                  <input type=\"hidden\" name=\"action\" value=\"realms\" />
                  <select name=\"n_realms\">";
      for($i=1;$i<=$tot_realms;$i++)
        $output .= "
                    <option value=\"$i\">".htmlentities($i)."</option>";
      $output .= "
                  </select>
                </form>
              </td>
            </tr>";
    }
  }
  $output .= "
            <tr>
              <td align=\"right\">Total: ";
  $output .= $all_record;
  $output .= "
              </td>
              <td align=\"right\" width=\"25%\">";
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
    $char = $sqlc->fetch_array($result);
    $guild_name = $sqlc->fetch_row($sqlc->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char[11].";"));

    $money_gold = (int)($char[13]/10000);
    $money_silver = (int)(($char[13]-$money_gold*10000)/100);
    $money_cooper = (int)($char[13]-$money_gold*10000-$money_silver*100);

    $money = $money_gold."<img src=\"./img/gold.gif\" alt=\"gold\" /> ".$money_silver."<img src=\"./img/silver.gif\" alt=\"silver\" /> ".$money_cooper."<img src=\"./img/copper.gif\" alt=\"copper\" /> ";

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
    $CHAR_RACE = id_get_char_race();
    $CHAR_RANK = id_get_char_rank();
    $output .= "
            <tr valign='top'>
              <td><a href=\"char.php?id=$char[0]\">".htmlentities($char[1])."</a></td>
              <td><img src='img/c_icons/{$char[2]}-{$char[12]}.gif' alt=\"".get_player_race($char[2])."\" onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
              <td><img src='img/c_icons/{$char[3]}.gif' alt=\"".get_player_class($char[3])."\" onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
              <td>".get_level_with_color($char[9])."</td>
              <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[11]\">".htmlentities($guild_name[0])."</a></td>
              <td>$money</td>
              <td><span onmouseover='toolTip(\"".$CHAR_RANK[$CHAR_RACE[$char[2]][1]][pvp_ranks($char[7])]."\",\"item_tooltip\")' onmouseout='toolTip()' style='color: white;'><img src='img/ranks/rank".pvp_ranks($char[7],$CHAR_RACE[$char[2]][1]).".gif' alt=\"\"></img></span></td>
              <td>$char[7]</td>
              <td>$char[8]</td>
              <td>$char[10]</td>
              <td>$time</td>
              <td>".(($char[6]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
            </tr>";
  }
  $output .= "
            </tr>
              <td colspan=\"12\" class=\"hidden\" align=\"right\" width=\"25%\">";
    $output .= generate_pagination("top100.php?order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
    unset($all_record);
    $output .= "
              </td>
            </tr>
          </table>
        </center>
        <br />
";


}

//#############################################################################
// MAIN
//#############################################################################

//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//$output .= "
//        <div class=\"top\">";

$lang_top = lang_top();

//switch ($err)
//{
//  case 1:
//    break;
//  default:
    //$output .= "
    //      <h1>{$lang_top['top100']}</h1>";
//}

//unset($err);

//$output .= "
//        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "realms" :
    if (isset($_GET['n_realms']))
    {
      $n_realms = $_GET['n_realms'];

      $sqlr=new sql;
      $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
      $realms = $sqlr->query("SELECT `id`, `name` FROM `realmlist` LIMIT 10");

      if ($sqlr->num_rows($realms) > 1 && (count($server) >1))
      {
        for($i=1;$i<=$n_realms;$i++)
        {
          $realm = $sqlr->fetch_row($realms);
          if(isset($server[$realm[0]]))
          {
            $output .= "<div class=\"top\"><h1>Top 100 of $realm[1]</h1></div>";
            top100($realm[0]);
          }
        }
      }
      else
      {
        $output .= "<div class=\"top\"><h1>{$lang_top['top100']}]</h1></div>";
        top100($realm_id);
      }
    }
    else
    {
      $output .= "<div class=\"top\"><h1>{$lang_top['top100']}</h1></div>";
      top100($realm_id);
    }
    unset($sql);
    break;
  default :
    $output .= "<div class=\"top\"><h1>{$lang_top['top100']}</h1></div>";
    top100($realm_id);
}

unset($action);
unset($action_permission);
unset($lang_top);

require_once("footer.php");

?>
