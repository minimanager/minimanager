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
require_once("libs/char_lib.php");
require_once("libs/archieve_lib.php");
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS ACHIEVEMENTS
//########################################################################################################################
function char_achievements()
{
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $characters_db, $itemperpage,
    $action_permission, $user_lvl, $user_name, $achievement_datasite;
  wowhead_tt();

  if (empty($_GET['id']))
    error($lang_global['empty_fields']);

  $sqlr = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

  if (empty($_GET['realm']))
    $realmid = $realm_id;
  else
  {
    $realmid = $sqlr->quote_smart($_GET['realm']);
    if (!is_numeric($realmid)) $realmid = $realm_id;
  }

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);

  $id = $sqlc->quote_smart($_GET['id']);
  if (!is_numeric($id))
    $id = 0;

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $start)) $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : "date";
  if (!preg_match("/^[_[:lower:]]{1,12}$/", $order_by)) $order_by="id";

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 0;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  $result = $sqlc->query("SELECT account, name, race, class, CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
    mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender
    FROM `characters` WHERE guid = $id LIMIT 1");

  if ($sqlc->num_rows($result))
  {
    $char = $sqlc->fetch_row($result);

    $owner_acc_id = $sqlc->result($result, 0, 'account');
    $result = $sqlr->query("SELECT gmlevel,username FROM account WHERE id ='$char[0]'");
    $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
    $owner_name = $sqlr->result($result, 0, 'username');

    if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
    {
      $result = $sqlc->query("SELECT achievement,date FROM character_achievement WHERE guid =$id ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
      $query_1 = $sqlc->query("SELECT count(*) FROM character_achievement WHERE guid =$id");
      $all_record = $sqlc->result($query_1,0);
      unset($query_1);

      $output .= "
      <center>
        <div id=\"tab\">
          <ul>
            <li><a href=\"char.php?id=$id&amp;realm=$realmid\">{$lang_char['char_sheet']}</a></li>
            <li><a href=\"char_inv.php?id=$id&amp;realm=$realmid\">{$lang_char['inventory']}</a></li>
            <li><a href=\"char_talent.php?id=$id&amp;realm=$realmid\">{$lang_char['talents']}</a></li>
            <li id=\"selected\"><a href=\"char_achieve.php?id=$id&amp;realm=$realmid\">{$lang_char['achievements']}</a></li>
            <li><a href=\"char_quest.php?id=$id&amp;realm=$realmid\">{$lang_char['quests']}</a></li>
            <li><a href=\"char_friends.php?id=$id&amp;realm=$realmid\">{$lang_char['friends']}</a></li>
          </ul>
        </div>
        <div id=\"tab_content\">
          <font class=\"bold\">".htmlentities($char[1])." - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> - lvl ".char_get_level_color($char[4])."</font>
          <br /><br />
          <table class=\"lined\" style=\"width: 550px;\">
            <tr>
              <td width=\"100%\" align=\"right\" colspan=\"4\">";
      $output .= generate_pagination("char_achieve.php?id=$id&amp;realm=$realmid&amp;order_by=$order_by&amp;dir=".(($dir) ? 0 : 1), $all_record, $itemperpage, $start);
      $output .= "
              </td>
            </tr>
            <tr>";
      $output .= "
              <th width=\"25%\">{$lang_char['achievement_category']}</th>
              <th width=\"60%\">{$lang_char['achievement_title']}</th>
              <th width=\"5%\">{$lang_char['achievement_points']}</th>
              <th width=\"10%\"><a href=\"char_achieve.php?id=$id&amp;realm=$realmid&amp;order_by=date&amp;start=$start&amp;dir=$dir\"".($order_by=='date' ? " class=\"$order_dir\"" : "").">{$lang_char['achievement_date']}</a></th>
            </tr>";

      while ($data = $sqlc->fetch_row($result))
      {
        $output .="
            <tr>
              <td>".achieve_get_category($data[0])."</td>
              <td align=\"left\"><a href=\"".$achievement_datasite.$data[0]."\" target=\"_blank\">".achieve_get_name($data[0])."</a><br />".achieve_get_reward($data[0])."</td>
              <td>".achieve_get_points($data[0])." <img src=\"img/money_achievement.gif\" alt=\"\" /></td>
              <td>".date("o-m-d", $data['1'])."</td>
            </tr>";
      }
      $output .= "
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
                makebutton($lang_char['edit_button'], "char_edit.php?id=$id&amp;realm=$realmid",130);
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
    char_achievements();
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");

?>
