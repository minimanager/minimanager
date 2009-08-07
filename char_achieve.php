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
function char_achievements()
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
            <li><a href=\"char_quest.php?id=$id\">{$lang_char['quests']}</a></li>
            <li id=\"selected\"><a href=\"char_achieve.php?id=$id\">{$lang_char['achievements']}</a></li>
            <li><a href=\"char_skill.php?id=$id\">{$lang_char['skills']}</a></li>
            <li><a href=\"char_talent.php?id=$id\">{$lang_char['talents']}</a></li>
            <li><a href=\"char_rep.php?id=$id\">{$lang_char['reputation']}</a></li>";
      if (get_player_class($char[3]) == 'Hunter')
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
      $output .= "
              <th width=\"78%\">{$lang_char['achievement_title']}</th>
              <th width=\"22%\">{$lang_char['achievement_date']}</th>
            </tr>";

      $result = $sqlc->query("SELECT achievement,date FROM character_achievement WHERE guid =$id");

      while ($data = $sqlc->fetch_row($result))
      {
        $output .="
             <tr>
               <td align=\"left\"><a href=\"http://www.wowhead.com/?achievement=".$data[0]."\" target=\"_blank\">".get_achievement_name($data[0])."</a></td>
               <td>".date("n-j-o", $data['1'])."</td>
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
      error($lang_char['no_permission']);
    }
  }
  else
    error($lang_char['no_char_found']);

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
    char_achievements();
}

require_once("footer.php");

?>
