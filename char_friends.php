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
require_once("scripts/get_lib.php");
require_once("scripts/defines.php");
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS ACHIEVEMENTS
//########################################################################################################################
function char_friends()
{
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $characters_db,
    $action_permission, $user_lvl, $user_name;

  if (empty($_GET['id']))
    error($lang_global['empty_fields']);

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
  $sqlr = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

  $id = $sqlc->quote_smart($_GET['id']);
  if (!is_numeric($id))
    $id = 0;

  //==========================$_GET and SECURE========================
  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : "name";
  if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by)) $order_by="name";

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================

  $result = $sqlc->query("SELECT account, name, race, class, CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level, mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender FROM `characters` WHERE guid = $id LIMIT 1");

  if ($sqlc->num_rows($result))
  {
    $char = $sqlc->fetch_row($result);

    $owner_acc_id = $sqlc->result($result, 0, 'account');
    $result = $sqlr->query("SELECT gmlevel,username FROM account WHERE id ='$char[0]'");
    $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
    $owner_name = $sqlr->result($result, 0, 'username');

    if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
    {
      $output .= "
      <center>
        <div id=\"tab\">
          <ul>
            <li><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>
            <li><a href=\"char_inv.php?id=$id\">{$lang_char['inventory']}</a></li>
            <li><a href=\"char_talent.php?id=$id\">{$lang_char['talents']}</a></li>
            <li><a href=\"char_achieve.php?id=$id\">{$lang_char['achievements']}</a></li>
            <li><a href=\"char_quest.php?id=$id\">{$lang_char['quests']}</a></li>
            <li id=\"selected\"><a href=\"char_friends.php?id=$id\">{$lang_char['friends']}</a></li>
          </ul>
        </div>
        <div id=\"tab_content\">
          <font class=\"bold\">".htmlentities($char[1])." - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' /> <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' /> - lvl ".get_level_with_color($char[4])."</font>
          <br /><br />
          <table class=\"hidden\"  style=\"width: 1%;\">
            <tr valign=\"top\">
              <td>
                <table class=\"lined\" style=\"width: 1%;\">";

      $result = $sqlc->query("SELECT name, race, class, map, zone,
      CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
      mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender, online
      FROM `characters` WHERE guid in (SELECT friend FROM character_social WHERE guid =$id and flags <= 1) ORDER BY $order_by $order_dir");

    if($sqlc->num_rows($result))
    {
      $output .="
                  <tr>
                    <th colspan=\"7\" align=\"left\">{$lang_char['friends']}</th>
                  </tr>
                  <tr>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=name&amp;dir=$dir\">".($order_by=='name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['name']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=race&amp;dir=$dir\">".($order_by=='race' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['race']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=class&amp;dir=$dir\">".($order_by=='class' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['class']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=level&amp;dir=$dir\">".($order_by=='level' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['level']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=map&amp;dir=$dir\">".($order_by=='map' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['map']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=zone&amp;dir=$dir\">".($order_by=='zone' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['zone']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=online&amp;dir=$dir\">".($order_by=='online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['online']}</a></th>
                  </tr>";

      while ($data = $sqlc->fetch_row($result))
      {
        $output .="
                  <tr>
                    <td>".$data[0]."</td>
                    <td><img src='img/c_icons/{$data[1]}-{$data[6]}.gif' onmousemove='toolTip(\"".get_player_race($data[1])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                    <td><img src='img/c_icons/{$data[2]}.gif' onmousemove='toolTip(\"".get_player_class($data[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                    <td>".get_level_with_color($data[5])."</td>
                    <td class=\"small\"><span onmousemove='toolTip(\"MapID:".$data[3]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_map_name($data[3])."</span></td>
                    <td class=\"small\"><span onmousemove='toolTip(\"ZoneID:".$data[4]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_zone_name($data[4])."</span></td>
                    <td>".(($data[7]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
                  </tr>";
      }
    }

      $result = $sqlc->query("SELECT name, race, class, map, zone,
      CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
      mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender, online
      FROM `characters` WHERE guid in (SELECT guid FROM character_social WHERE friend =$id and flags <= 1) ORDER BY $order_by $order_dir");


    if($sqlc->num_rows($result))
    {
      $output .= "
                  <tr>
                    <th colspan=\"7\" align=\"left\">{$lang_char['friendof']}</th>
                  </tr>
                  <tr>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=name&amp;dir=$dir\">".($order_by=='name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['name']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=race&amp;dir=$dir\">".($order_by=='race' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['race']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=class&amp;dir=$dir\">".($order_by=='class' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['class']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=level&amp;dir=$dir\">".($order_by=='level' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['level']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=map&amp;dir=$dir\">".($order_by=='map' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['map']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=zone&amp;dir=$dir\">".($order_by=='zone' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['zone']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=online&amp;dir=$dir\">".($order_by=='online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['online']}</a></th>
                  </tr>";

      while ($data = $sqlc->fetch_row($result))
      {
        $output .="
                  <tr>
                    <td>".$data[0]."</td>
                    <td><img src='img/c_icons/{$data[1]}-{$data[6]}.gif' onmousemove='toolTip(\"".get_player_race($data[1])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                    <td><img src='img/c_icons/{$data[2]}.gif' onmousemove='toolTip(\"".get_player_class($data[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                    <td>".get_level_with_color($data[5])."</td>
                    <td class=\"small\"><span onmousemove='toolTip(\"MapID:".$data[3]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_map_name($data[3])."</span></td>
                    <td class=\"small\"><span onmousemove='toolTip(\"ZoneID:".$data[4]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_zone_name($data[4])."</span></td>
                    <td>".(($data[7]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
                  </tr>";
      }
    }

      $output .= "
                </table>
                <script type=\"text/javascript\">
                  if (getBrowserWidth() > 1024)
                    document.write(\"</td><td>\");
                </script>
                <table class=\"lined\" style=\"width: 1%;\">";

      $result = $sqlc->query("SELECT name, race, class, map, zone,
      CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
      mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender, online
      FROM `characters` WHERE guid in (SELECT friend FROM character_social WHERE guid =$id and flags > 1) ORDER BY $order_by $order_dir");

    if($sqlc->num_rows($result))
    {
      $output .= "
                  <tr>
                    <th colspan=\"7\" align=\"left\">{$lang_char['ignored']}</th>
                  </tr>
                  <tr>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=name&amp;dir=$dir\">".($order_by=='name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['name']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=race&amp;dir=$dir\">".($order_by=='race' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['race']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=class&amp;dir=$dir\">".($order_by=='class' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['class']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=level&amp;dir=$dir\">".($order_by=='level' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['level']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=map&amp;dir=$dir\">".($order_by=='map' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['map']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=zone&amp;dir=$dir\">".($order_by=='zone' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['zone']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=online&amp;dir=$dir\">".($order_by=='online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['online']}</a></th>
                  </tr>";

      while ($data = $sqlc->fetch_row($result))
      {
        $output .="
                  <tr>
                    <td>".$data[0]."</td>
                    <td><img src='img/c_icons/{$data[1]}-{$data[6]}.gif' onmousemove='toolTip(\"".get_player_race($data[1])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                    <td><img src='img/c_icons/{$data[2]}.gif' onmousemove='toolTip(\"".get_player_class($data[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                    <td>".get_level_with_color($data[5])."</td>
                    <td class=\"small\"><span onmousemove='toolTip(\"MapID:".$data[3]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_map_name($data[3])."</span></td>
                    <td class=\"small\"><span onmousemove='toolTip(\"ZoneID:".$data[4]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_zone_name($data[4])."</span></td>
                    <td>".(($data[7]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
                  </tr>";
      }
    }

      $result = $sqlc->query("SELECT name, race, class, map, zone,
      CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
      mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender, online
      FROM `characters` WHERE guid in (SELECT guid FROM character_social WHERE friend =$id and flags > 1) ORDER BY $order_by $order_dir");

    if($sqlc->num_rows($result))
    {
      $output .= "
                  <tr>
                    <th colspan=\"7\" align=\"left\">{$lang_char['ignoredby']}</th>
                  </tr>
                  <tr>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=name&amp;dir=$dir\">".($order_by=='name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['name']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=race&amp;dir=$dir\">".($order_by=='race' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['race']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=class&amp;dir=$dir\">".($order_by=='class' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['class']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=level&amp;dir=$dir\">".($order_by=='level' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['level']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=map&amp;dir=$dir\">".($order_by=='map' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['map']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=zone&amp;dir=$dir\">".($order_by=='zone' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['zone']}</a></th>
                    <th width=\"1%\"><a href=\"char_friends.php?id=$id&amp;order_by=online&amp;dir=$dir\">".($order_by=='online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['online']}</a></th>
                  </tr>";

      while ($data = $sqlc->fetch_row($result))
      {
        $output .="
                  <tr>
                    <td>".$data[0]."</td>
                    <td><img src='img/c_icons/{$data[1]}-{$data[6]}.gif' onmousemove='toolTip(\"".get_player_race($data[1])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                    <td><img src='img/c_icons/{$data[2]}.gif' onmousemove='toolTip(\"".get_player_class($data[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /></td>
                    <td>".get_level_with_color($data[5])."</td>
                    <td class=\"small\"><span onmousemove='toolTip(\"MapID:".$data[3]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_map_name($data[3])."</span></td>
                    <td class=\"small\"><span onmousemove='toolTip(\"ZoneID:".$data[4]."\",\"item_tooltip\")' onmouseout='toolTip()'>".get_zone_name($data[4])."</span></td>
                    <td>".(($data[7]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
                  </tr>";
      }
    }
      $output .= "
                </table>
              </td>
            </tr>
          </table>
        </div>
        <br />
        <table class=\"hidden\">
          <tr>
            <td>";
                makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$owner_acc_id",130);
      $output .= "
            </td>
            <td>";
      if (($user_lvl > $owner_gmlvl)&&($user_lvl >= $action_permission['delete']))
      {
        makebutton($lang_char['edit_button'], "char_edit.php?id=$id",130);
        $output .= "
            </td>
            <td>";
      }
      if ((($user_lvl > $owner_gmlvl)&&($user_lvl >= $action_permission['delete']))||($owner_name == $user_name))
      {
        makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id\" type=\"wrn",130);
        $output .= "
            </td>
            <td>";
      }
      if ($user_lvl >= $action_permission['update'])
      {
        makebutton($lang_char['send_mail'], "mail.php?type=ingame_mail&amp;to=$char[1]",130);
        $output .= "
            </td>
            <td>";
      }
      makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);
      //end of admin options
      $output .= "
            </td>
          </tr>
        </table>
        <br />
      </center>
";
    }
    else
      error($lang_char['no_permission']);
  }
  else
    error($lang_char['no_char_found']);

}


//########################################################################################################################
// MAIN
//########################################################################################################################

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$lang_char = lang_char();

switch ($action)
{
  case "unknown":
    break;
  default:
    char_friends();
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");

?>
