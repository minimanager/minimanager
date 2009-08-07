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
// SHOW CHARACTERS QUESTS
//########################################################################################################################
function char_quest()
{
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $world_db, $characters_db, $itemperpage,
    $action_permission, $user_lvl, $user_name, $quest_datasite;

  if (empty($_GET['id'])) error($lang_global['empty_fields']);

  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $id = $sql->quote_smart($_GET['id']);
  if (!is_numeric($id)) $id = 0;

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $start)) $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : 1;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $order_by)) $order_by=1;

  $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 0;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=0;

  //$order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================


  $result = $sql->query("SELECT account, name, race, class, CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level, mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender FROM `characters` WHERE guid = $id LIMIT 1");

  if ($sql->num_rows($result))
  {
    $char = $sql->fetch_row($result);

    $owner_acc_id = $sql->result($result, 0, 'account');
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    $result = $sql->query("SELECT gmlevel,username FROM account WHERE id ='$char[0]'");
    $owner_gmlvl = $sql->result($result, 0, 'gmlevel');
    $owner_name = $sql->result($result, 0, 'username');

    if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
    {
      $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
      $result = $sql->query("SELECT quest,status FROM character_queststatus WHERE guid =$id AND ( status = 3 OR status = 1 ) ORDER BY status DESC");
      $output .= "
        <center>
          <div id=\"tab\">
            <ul>
              <li><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>
              <li><a href=\"char_inv.php?id=$id\">{$lang_char['inventory']}</a></li>
              <li id=\"selected\"><a href=\"char_quest.php?id=$id\">{$lang_char['quests']}</a></li>
              <li><a href=\"char_achieve.php?id=$id\">{$lang_char['achievements']}</a></li>
              <li><a href=\"char_skill.php?id=$id\">{$lang_char['skills']}</a></li>
              <li><a href=\"char_talent.php?id=$id\">{$lang_char['talents']}</a></li>
              <li><a href=\"char_rep.php?id=$id\">{$lang_char['reputation']}</a></li>";
      if( get_player_class($char[3]) == 'Hunter' )
        $output .= "
              <li><a href=\"char_pets.php?id=$id\">{$lang_char['pets']}</a></li>";
      $output .= "
            </ul>
          </div>
          <div id=\"tab_content\">
            <font class=\"bold\">".htmlentities($char[1])." - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' /> <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' /> - lvl ".get_level_with_color($char[4])."</font>
            <br /><br />
            <table class=\"lined\" style=\"width: 550px;\">
              <tr>";
      if ($user_lvl)
        $output .= "
                <th width=\"10%\"><a href=\"char_quest.php?id=$id&amp;start=$start&amp;order_by=0&amp;dir=$dir\">".($order_by==0 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_char['quest_id']}</a></th>";
      $output .= "
                <th width=\"7%\"><a href=\"char_quest.php?id=$id&amp;start=$start&amp;order_by=1&amp;dir=$dir\">".($order_by==1 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_char['quest_level']}</a></th>
                <th width=\"78%\"><a href=\"char_quest.php?id=$id&amp;start=$start&amp;order_by=2&amp;dir=$dir\">".($order_by==2 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_char['quest_title']}</a></th>
                <th width=\"5%\"><img src=\"img/aff_qst.png\" width=\"14\" height=\"14\" border=\"0\" /></a></th>
              </tr>";
      $quests_1 = array();
      $quests_3 = array();

      if ($sql->num_rows($result))
      {
        while ($quest = $sql->fetch_row($result))
        {
          $deplang = get_lang_id();
          $query1 = $sql->query("SELECT QuestLevel,IFNULL(".($deplang<>0?"title_loc$deplang":"NULL").",`title`) as Title FROM `".$world_db[$realm_id]['name']."`.`quest_template` LEFT JOIN `".$world_db[$realm_id]['name']."`.`locales_quest` ON `quest_template`.`entry` = `locales_quest`.`entry` WHERE `quest_template`.`entry` ='$quest[0]'");
          $quest_info = $sql->fetch_row($query1);
          if($quest[1]==1)
            array_push($quests_1, array($quest[0], $quest_info[0], $quest_info[1]));
          else
            array_push($quests_3, array($quest[0], $quest_info[0], $quest_info[1]));
        }
        aasort($quests_1, $order_by, $dir);
        aasort($quests_3, $order_by, $dir);
        $all_record = count($quests_1);

        foreach ($quests_3 as $data)
        {
          $output .= "
                <tr>";
          if($user_lvl)
            $output .= "
                  <td>$data[0]</td>";
          $output .= "
                  <td>($data[1])</td>
                  <td align=\"left\"><a href=\"$quest_datasite$data[0]\" target=\"_blank\">".htmlentities($data[2])."</a></td>
                  <td><img src=\"img/aff_qst.png\" width=\"14\" height=\"14\" /></td>
                </tr>";
        }
        if(count($quests_1))
          $output .= "
              </table>
              <table class=\"hidden\">
                <tr>
                  <td colspan=\"".($user_lvl ? "4" : "3")."\" align=\"right\">";
                    $output .= generate_pagination("char_quest.php?id=$id&amp;start=$start&amp;order_by=$order_by&amp;dir=".!$dir."", $all_record, $itemperpage, $start);
          $output .= "
                  </td>
                </tr>
              </table>
              <table class=\"lined\" style=\"width: 550px;\">";
        $i=0;
        foreach ($quests_1 as $data)
        {
          if($i<$start)
          {
          }
          elseif($i<$start+$itemperpage)
          {
            $output .= "
                <tr>";
            if($user_lvl)
              $output .= "
                  <td>$data[0]</td>";
            $output .= "
                  <td>($data[1])</td>
                  <td align=\"left\"><a href=\"$quest_datasite$data[0]\" target=\"_blank\">".htmlentities($data[2])."</a></td>
                  <td><img src=\"img/aff_tick.png\" width=\"14\" height=\"14\" /></td>
                </tr>";
          }
          $i++;
        }
      }
      else
        $output .= "
                <tr>
                  <td colspan=\"".($user_lvl ? "4" : "3")."\"><p>{$lang_char['no_act_quests']}</p></td>
                </tr>";
      $output .= "
              </table>
            </div><br />
            <table class=\"hidden\">
              <tr>
                <td>";
                  makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$owner_acc_id",130);
      $output .= "
                </td>
                <td>";
      if (($user_lvl >= $action_permission['delete']))
      {
        makebutton($lang_char['edit_button'], "char_edit.php?id=$id",130);
        $output .= "
                </td>
                <td>";
      }
      if (($user_lvl >= $action_permission['delete'])||($owner_name == $user_name))
      {
        makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id\" type=\"wrn",130);
        $output .= "
                </td>
                <td>";
      }
      if (($user_lvl >= $action_permission['update'])||($owner_name == $user_name))
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
    {
      $sql->close();
      unset($sql);
      error($lang_char['no_permission']);
    }
  }
  else
    error($lang_char['no_char_found']);

  $sql->close();
  unset($sql);
}


//########################################################################################################################
// MAIN
//########################################################################################################################

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "unknown":
    break;
  default:
    char_quest();
}

require_once("footer.php");

?>
