<?php


require_once("header.php");
require_once("libs/char_lib.php");
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS QUESTS
//########################################################################################################################
function char_quest()
{
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $world_db, $characters_db, $itemperpage,
    $action_permission, $user_lvl, $user_name, $quest_datasite;
  wowhead_tt();

  if (empty($_GET['id'])) error($lang_global['empty_fields']);

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
  if (!is_numeric($id)) $id = 0;

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $start)) $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 1;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $order_by)) $order_by=1;

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 0;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=0;

  //$order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  $result = $sqlc->query("SELECT account, name, race, class, CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
    mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender
    FROM `characters` WHERE guid = $id LIMIT 1");

  if ($sqlc->num_rows($result))
  {
    $char = $sqlc->fetch_row($result);

    $owner_acc_id = $sqlr->result($result, 0, 'account');
    $result = $sqlr->query("SELECT gmlevel,username FROM account WHERE id ='$char[0]'");
    $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
    $owner_name = $sqlr->result($result, 0, 'username');

    if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
    {
      $result = $sqlc->query("SELECT quest,status FROM character_queststatus WHERE guid =$id AND ( status = 3 OR status = 1 ) ORDER BY status DESC");
      $output .= "
        <center>
          <div id=\"tab\">
            <ul>
            <li><a href=\"char.php?id=$id&amp;realm=$realmid\">{$lang_char['char_sheet']}</a></li>
            <li><a href=\"char_inv.php?id=$id&amp;realm=$realmid\">{$lang_char['inventory']}</a></li>
            <li><a href=\"char_talent.php?id=$id&amp;realm=$realmid\">{$lang_char['talents']}</a></li>
            <li><a href=\"char_achieve.php?id=$id&amp;realm=$realmid\">{$lang_char['achievements']}</a></li>
            <li id=\"selected\"><a href=\"char_quest.php?id=$id&amp;realm=$realmid\">{$lang_char['quests']}</a></li>
            <li><a href=\"char_friends.php?id=$id&amp;realm=$realmid\">{$lang_char['friends']}</a></li>
            </ul>
          </div>
          <div id=\"tab_content\">
            <font class=\"bold\">".htmlentities($char[1])." - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> - lvl ".char_get_level_color($char[4])."</font>
            <br /><br />
            <table class=\"lined\" style=\"width: 550px;\">
              <tr>";
      if ($user_lvl)
        $output .= "
                <th width=\"10%\"><a href=\"char_quest.php?id=$id&amp;realm=$realmid&amp;start=$start&amp;order_by=0&amp;dir=$dir\">".($order_by==0 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['quest_id']}</a></th>";
      $output .= "
                <th width=\"7%\"><a href=\"char_quest.php?id=$id&amp;realm=$realmid&amp;start=$start&amp;order_by=1&amp;dir=$dir\">".($order_by==1 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['quest_level']}</a></th>
                <th width=\"78%\"><a href=\"char_quest.php?id=$id&amp;realm=$realmid&amp;start=$start&amp;order_by=2&amp;dir=$dir\">".($order_by==2 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['quest_title']}</a></th>
                <th width=\"5%\"><img src=\"img/aff_qst.png\" width=\"14\" height=\"14\" border=\"0\" alt=\"\" /></th>
              </tr>";
      $quests_1 = array();
      $quests_3 = array();

      if ($sqlc->num_rows($result))
      {
        while ($quest = $sqlc->fetch_row($result))
        {
          $deplang = get_lang_id();
          $query1 = $sqlc->query("SELECT QuestLevel,IFNULL(".($deplang<>0?"title_loc$deplang":"NULL").",`title`) as Title FROM `".$world_db[$realmid]['name']."`.`quest_template` LEFT JOIN `".$world_db[$realmid]['name']."`.`locales_quest` ON `quest_template`.`entry` = `locales_quest`.`entry` WHERE `quest_template`.`entry` ='$quest[0]'");
          $quest_info = $sqlc->fetch_row($query1);
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
                <td><img src=\"img/aff_qst.png\" width=\"14\" height=\"14\" alt=\"\" /></td>
              </tr>";
        }
        if(count($quests_1))
          $output .= "
            </table>
            <table class=\"hidden\" style=\"width: 510px;\">
              <tr align=\"right\">
                <td colspan=\"".($user_lvl ? "4" : "3")."\">";
                  $output .= generate_pagination("char_quest.php?id=$id&amp;realm=$realmid&amp;start=$start&amp;order_by=$order_by&amp;dir=".!$dir."", $all_record, $itemperpage, $start);
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
                <td><img src=\"img/aff_tick.png\" width=\"14\" height=\"14\" alt=\"\" /></td>
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
      //---------------Page Specific Data Ends here----------------------------
      //---------------Character Tabs Footer-----------------------------------
      $output .= '
              </table>
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
                  // button to user account page, user account page has own security
                  makebutton($lang_char['chars_acc'], 'user.php?action=edit_user&amp;id='.$owner_acc_id.'', 130);
      $output .= '
                </td>
                <td>';

      // only higher level GM with delete access can edit character
      //  character edit allows removal of character items, so delete permission is needed
      if ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) )
      {
                  makebutton($lang_char['edit_button'], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) ) || ($owner_name === $user_name) )
      {
                  makebutton($lang_char['del_char'], 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ($user_lvl >= $action_permission['update'])
      {
                  makebutton($lang_char['send_mail'], 'mail.php?type=ingame_mail&amp;to='.$char[0].'', 130);
        $output .= '
                </td>
                <td>';
      }
                  makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
      $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of char_quest.php -->';
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
    char_quest();
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");

?>
