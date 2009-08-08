<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

require_once("header.php");
require_once("scripts/defines.php");
valid_login($action_permission['read']);

$race = Array
(
  1  => array(1, 'Human',"",""),
  2  => array(2, 'Orc',"",""),
  3  => array(3, 'Dwarf',"",""),
  4  => array(4, 'Nightelf',"",""),
  5  => array(5, 'Undead',"",""),
  6  => array(6, 'Tauren',"",""),
  7  => array(7, 'Gnome',"",""),
  8  => array(8, 'Troll',"",""),
  10 => array(10,'Bloodelf',"",""),
  11 => array(11,'Draenei',"","")
);

$class = Array
(
  1  => array(1, 'Warrior',"",""),
  2  => array(2, 'Paladin',"",""),
  3  => array(3, 'Hunter',"",""),
  4  => array(4, 'Rogue',"",""),
  5  => array(5, 'Priest',"",""),
  6  => array(6, 'Death Knight',"",""),
  7  => array(7, 'Shaman',"",""),
  8  => array(8, 'Mage',"",""),
  9  => array(9, 'Warlock',"",""),
  11 => array(11,'Druid',"","")
);

$level = Array
(
  1 => array(1,1,9,"",""),
  2 => array(2,10,19,"",""),
  3 => array(3,20,29,"",""),
  4 => array(4,30,39,"",""),
  5 => array(5,40,49,"",""),
  6 => array(6,50,59,"",""),
  7 => array(7,60,69,"",""),
  8 => array(8,70,79,"",""),
  9 => array(9,80,80,"","")
);

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$query = $sql->query("SELECT count(*) FROM `characters` WHERE `online`= 1");
$total_chars = $sql->result($query,0);

if ($total_chars)
{
  $order_race = (isset($_GET['race'])) ? "AND race =".$sql->quote_smart($_GET['race']) : "";
  $order_class = (isset($_GET['class'])) ? "AND class =".$sql->quote_smart($_GET['class']) : "";

  if(isset($_GET['level']))
  {
    $lvl_min = $sql->quote_smart($_GET['level']);
    $lvl_max = $lvl_min + 4;
    $order_level = "AND SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) >= $lvl_min AND SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) <= $lvl_max";
  }
  else
    $order_level = "";

  if(isset($_GET['side']))
  {
    if ($sql->quote_smart($_GET['side']) == "h")
      $order_side = "AND race IN(2,5,6,8,10)";
    elseif($sql->quote_smart($_GET['side']) == "a")
      $order_side = "AND race IN (1,3,4,7,11)";
  }
  else
    $order_side = "";

  $output .= "
          <center>
            <div id=\"tab\">
              <ul>
                <li>
                  <a href=\"stat.php\">
                    {$lang_stat['srv_statistics']}
                  </a>
                </li>
                <li id=\"selected\">
                  <a href=\"stat_on.php\">
                    {$lang_stat['on_statistics']}
                  </a>
                </li>
              </ul>
            </div>
            <div id=\"tab_content\">";
  $output .= "
              <div class=\"top\"><h1>{$lang_stat['on_statistics']}</h1></div>";
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  //there is always less hordies
  $query = $sql->query("SELECT count(guid) FROM `characters` WHERE race IN(2,5,6,8,10) AND `online`= 1");
  $horde_chars = $sql->result($query,0);
  $horde_pros = round(($horde_chars*100)/$total_chars ,1);
  $allies_chars = $total_chars - $horde_chars;
  $allies_pros = 100 - $horde_pros;

  $output .= "
              <center>
                <table class=\"hidden\">
                  <tr>
                    <td align=\"left\">
                      <h1>{$lang_stat['general_info']}</h1>
                    </td>
                  </tr>
                  <tr align=\"left\">
                    <td class=\"large\">
                      <font class=\"bold\">{$lang_index['tot_users_online']} : $total_chars</font><br /><br />
                      <table class=\"tot_bar\">
                        <tr>
                          <td width=\"$horde_pros%\" background=\"./img/bar_horde.gif\" height=\"40\"><a href=\"stat_on.php?side=h\">{$lang_stat['horde']}: $horde_chars ($horde_pros%)</a></td>
                          <td width=\"$allies_pros%\" background=\"./img/bar_allie.gif\" height=\"40\"><a href=\"stat_on.php?side=a\">{$lang_stat['alliance']}: $allies_chars ($allies_pros%)</a></td>
                        </tr>
                      </table>
                      <hr/>
                    </td>
                  </tr>";

  // RACE
  foreach ($race as $id)
  {
    $query = $sql->query("SELECT count(guid) FROM `characters` WHERE race = $id[0] $order_class $order_level $order_side AND `online`= 1");
    $race[$id[0]][2] = $sql->result($query,0);
    $race[$id[0]][3] = round((($race[$id[0]][2])*100)/$total_chars,1);
  }

  $output .= "
                  <tr align=\"left\">
                    <td><h1>{$lang_stat['chars_by_race']}</h1></td>
                  </tr>
                <tr>
                  <td>
                    <table class=\"bargraph\">
                    <tr>";
  foreach ($race as $id)
  {
    $height = ($race[$id[0]][3])*4;
    $output .= "
                      <td><a href=\"stat_on.php?race={$id[0]}\" class=\"graph_link\">{$race[$id[0]][3]}%<img src=\"./themes/$theme/column.gif\" width=\"69\" height=\"$height\" alt=\"{$race[$id[0]][2]}\" /></a></td>";
  }
  $output .= "
                    </tr>
                    <tr>";
  foreach ($race as $id)
  {
    $output .= "
                      <th>{$race[$id[0]][1]}<br />{$race[$id[0]][2]}</th>";
  }
  $output .= "
                    </tr>
                  </table>
                  <br />
                </td>
              </tr>";
  // RACE END

  // CLASS
  foreach ($class as $id)
  {
    $query = $sql->query("SELECT count(guid) FROM `characters` WHERE class = $id[0] $order_race $order_level $order_side AND `online`= 1");
    $class[$id[0]][2] = $sql->result($query,0);
    $class[$id[0]][3] = round((($class[$id[0]][2])*100)/$total_chars,1);
  }

  $output .= "
              <tr align=\"left\">
                <td><h1>{$lang_stat['chars_by_class']}</h1></td>
              </tr>
              <tr>
                <td>
                  <table class=\"bargraph\">
                    <tr>";
  foreach ($class as $id)
  {
    $height = ($class[$id[0]][3])*4;
    $output .= "
                      <td><a href=\"stat_on.php?class={$id[0]}\" class=\"graph_link\">{$class[$id[0]][3]}%<img src=\"./themes/$theme/column.gif\" width=\"69\" height=\"$height\" alt=\"{$class[$id[0]][2]}\" /></a></td>";
  }
  $output .= "
                    </tr>
                    <tr>";
  foreach ($class as $id)
  {
    $output .= "
                      <th>{$class[$id[0]][1]}<br />{$class[$id[0]][2]}</th>";
  }
  $output .= "
                    </tr>
                  </table>
                  <br />
                </td>
              </tr>";
  // CLASS END

  // LEVEL
  foreach ($level as $id)
  {
    $query = $sql->query("SELECT count(guid) FROM `characters` WHERE SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) >= $id[1]
      AND SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) <= $id[2] $order_race $order_class $order_side AND `online`= 1");
    $level[$id[0]][3] = $sql->result($query,0);
    $level[$id[0]][4] = round((($level[$id[0]][3])*100)/$total_chars,1);
  }

  $output .= "
              <tr align=\"left\">
                <td><h1>{$lang_stat['chars_by_level']}</h1></td>
              </tr>
              <tr>
                <td>
                  <table class=\"bargraph\">
                    <tr>";
  foreach ($level as $id)
  {
    $height = ($level[$id[0]][4])*4;
    $output .= "
                      <td><a href=\"stat_on.php?level={$id[1]}\" class=\"graph_link\">{$level[$id[0]][4]}%<img src=\"./themes/$theme/column.gif\" width=\"77\" height=\"$height\" alt=\"{$level[$id[0]][3]}\" /></a></td>";
  }
  $output .= "
                    </tr>
                    <tr>";
  foreach ($level as $id)
  {
    $output .= "
                      <th>{$level[$id[0]][1]}-{$level[$id[0]][2]}<br />{$level[$id[0]][3]}</th>";
  }
  $output .= "
                    </tr>
                  </table>
                  <br />
                  <hr/>
                </td>
              </tr>
              <tr>
                <td>";
  // LEVEL END

  makebutton($lang_stat['reset'], "stat_on.php", 720);
  $output .= "
                </td>
              </tr>
            </table>
          </center>
        </div>";

$sql->close();

}
else
{
  $sql->close();
  error($lang_global['err_no_result']);
}

require_once("footer.php");
?>
