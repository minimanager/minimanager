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
// SHOW CHARACTERS SKILLS
//########################################################################################################################
function char_skill()
{
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $characters_db,
    $action_permission, $user_lvl, $user_name, $skill_datasite;

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
  $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'],
    $characters_db[$realmid]['name']);

  $id = $sqlc->quote_smart($_GET['id']);
  if (!is_numeric($id))
    $id = 0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 1;
  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  $dir = ($dir) ? 0 : 1;

  $result = $sqlc->query("SELECT account FROM `characters` WHERE guid = $id LIMIT 1");

  if ($sqlc->num_rows($result))
  {
    $char = $sqlc->fetch_row($result);

    $owner_acc_id = $sqlc->result($result, 0, 'account');
    $result = $sqlr->query("SELECT gmlevel,username FROM account WHERE id ='$char[0]'");
    $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
    $owner_name = $sqlr->result($result, 0, 'username');

    if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
    {
      $result = $sqlc->query("SELECT data, name, race, class, CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level, mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender FROM `characters` WHERE guid = $id");
      $char = $sqlc->fetch_row($result);
      $char_data = explode(' ',$char[0]);

      $output .= "
        <center>
          <div id=\"tab\">
            <ul>
              <li id=\"selected\"><a href=\"char.php?id=$id&amp;realm=$realmid\">{$lang_char['char_sheet']}</a></li>
              <li><a href=\"char_inv.php?id=$id&amp;realm=$realmid\">{$lang_char['inventory']}</a></li>
              <li><a href=\"char_talent.php?id=$id&amp;realm=$realmid\">{$lang_char['talents']}</a></li>
              <li><a href=\"char_achieve.php?id=$id&amp;realm=$realmid\">{$lang_char['achievements']}</a></li>
              <li><a href=\"char_quest.php?id=$id&amp;realm=$realmid\">{$lang_char['quests']}</a></li>
              <li><a href=\"char_friends.php?id=$id&amp;realm=$realmid\">{$lang_char['friends']}</a></li>
             </ul>
          </div>
          <div id=\"tab_content\">
            <div id=\"tab\">
              <ul>";
      if( get_player_class($char[3]) == 'Hunter' )
        $output .= "
                <li><a href=\"char.php?id=$id&amp;realm=$realmid\">{$lang_char['char_sheet']}</a></li>";
      $output .= "
                <li><a href=\"char_pets.php?id=$id&amp;realm=$realmid\">{$lang_char['pets']}</a></li>
                <li><a href=\"char_rep.php?id=$id&amp;realm=$realmid\">{$lang_char['reputation']}</a></li>
                <li id=\"selected\"><a href=\"char_skill.php?id=$id&amp;realm=$realmid\">{$lang_char['skills']}</a></li>
              </ul>
            </div>
            <div id=\"tab_content2\">
              <font class=\"bold\">".htmlentities($char[1])." - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> - lvl ".get_level_with_color($char[4])."</font>
              <br /><br />
              <table class=\"lined\" style=\"width: 600px;\">
                <tr>
                  <th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['skills']}</th>
                </tr>
                <tr>
                  ".($user_lvl ? "<th><a href=\"char_skill.php?id=$id&amp;realm=$realmid&amp;order_by=0&amp;dir=$dir\">".($order_by==0 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['skill_id']}</a></th>" : "")."
                  <th align=\"right\"><a href=\"char_skill.php?id=$id&amp;realm=$realmid&amp;order_by=1&amp;dir=$dir\">".($order_by==1 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['skill_name']}</a></th>
                  <th><a href=\"char_skill.php?id=$id&amp;realm=$realmid&amp;order_by=2&amp;dir=$dir\">".($order_by==2 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['skill_value']}</a></th>
                </tr>";

      $skill_array = array();
      $class_array = array();
      $prof_1_array = array();
      $prof_2_array = array();
      $weapon_array = array();
      $armor_array = array();
      $language_array = array();

      $skill_rank_array = array(
         75 => $lang_char['apprentice'],
        150 => $lang_char['journeyman'],
        225 => $lang_char['expert'],
        300 => $lang_char['artisan'],
        375 => $lang_char['master'],
        450 => $lang_char['inherent'],
        385 => $lang_char['wise']
      );

      for ($i = CHAR_DATA_OFFSET_SKILL_DATA; $i <= CHAR_DATA_OFFSET_SKILL_DATA+384 ; $i+=3)
      {
        if (($char_data[$i])&&(get_skill_name($char_data[$i] & 0x0000FFFF )))
        {
          $temp = unpack("S", pack("L", $char_data[$i+1]));
          $skill = ($char_data[$i] & 0x0000FFFF);

          if (get_skill_type($skill) == 6)
          {
            array_push($weapon_array , array(($user_lvl ? $skill : ''), get_skill_name($skill), $temp[1]));
          }
          elseif (get_skill_type($skill) == 7)
          {
            array_push($class_array , array(($user_lvl ? $skill : ''), get_skill_name($skill), $temp[1]));
          }
          elseif (get_skill_type($skill) == 8)
          {
            array_push($armor_array , array(($user_lvl ? $skill : ''), get_skill_name($skill), $temp[1]));
          }
          elseif (get_skill_type($skill) == 9)
          {
            array_push($prof_2_array , array(($user_lvl ? $skill : ''), get_skill_name($skill), $temp[1]));
          }
          elseif (get_skill_type($skill) == 10)
          {
            array_push($language_array , array(($user_lvl ? $skill : ''), get_skill_name($skill), $temp[1]));
          }
          elseif (get_skill_type($skill) == 11)
          {
            array_push($prof_1_array , array(($user_lvl ? $skill : ''), get_skill_name($skill), $temp[1]));
          }
          else
          {
            array_push($skill_array , array(($user_lvl ? $skill : ''), get_skill_name($skill), $temp[1]));
          }
        }
      }
      unset($char_data);

      aasort($skill_array, $order_by, $dir);
      aasort($class_array, $order_by, $dir);
      aasort($prof_1_array, $order_by, $dir);
      aasort($prof_2_array, $order_by, $dir);
      aasort($weapon_array, $order_by, $dir);
      aasort($armor_array, $order_by, $dir);
      aasort($language_array, $order_by, $dir);

      foreach ($skill_array as $data)
      {
        $max = ($data[2] < $char[4]*5) ? $char[4]*5 : $data[2];
        $output .= "
                <tr>
                  ".($user_lvl ? "<td>$data[0]</td>" : "")."
                  <td align=\"right\">$data[1]</td>
                  <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(450*$data[2]/$max)-450)."px;\">
                    <span>$data[2]/$max</span>
                  </td>
                </tr>";
      }

      if(count($class_array))
        $output .= "
                <tr><th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['classskills']}</th></tr>";
      foreach ($class_array as $data)
      {
        $max = ($data[2] < $char[4]*5) ? $char[4]*5 : $data[2];
        $output .= "
                <tr>
                  ".($user_lvl ? "<td>$data[0]</td>" : "")."
                  <td align=\"right\">$data[1]</td>
                  <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: 0px;\">
                  </td>
                </tr>";
      }

      if(count($prof_1_array))
        $output .= "
                <tr><th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['professions']}</th></tr>";
      foreach ($prof_1_array as $data)
      {
        $max = ($data[2]<76 ? 75 : ($data[2]<151 ? 150 : ($data[2]<226 ? 225 : ($data[2]<301 ? 300 : ($data[2]<376 ? 375 : ($data[2]<376 ? 375 : 450))))));
        $output .= "
                <tr>
                  ".($user_lvl ? "<td>$data[0]</td>" : "")."
                  <td align=\"right\"><a href=\"{$skill_datasite}11.$data[0]\" target=\"_blank\">$data[1]</a></td>
                  <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(450*$data[2]/$max)-450)."px;\">
                  <span>$data[2]/$max ({$skill_rank_array[$max]})</span>
                  </td>
                </tr>";
      }

      if(count($prof_2_array))
        $output .= "
                <tr><th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['secondaryskills']}</th></tr>";
      foreach ($prof_2_array as $data)
      {
        $max = ($data[2]<76 ? 75 : ($data[2]<151 ? 150 : ($data[2]<226 ? 225 : ($data[2]<301 ? 300 : ($data[2]<376 ? 375 : ($data[2]<376 ? 375 : 450))))));
        $output .= "
                <tr>
                  ".($user_lvl ? "<td>$data[0]</td>" : "")."
                  <td align=\"right\"><a href=\"{$skill_datasite}9.$data[0]\" target=\"_blank\">$data[1]</a></td>
                  <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(450*$data[2]/$max)-450)."px;\">
                    <span>$data[2]/$max ({$skill_rank_array[$max]})</span>
                  </td>
                </tr>";
      }

      if(count($weapon_array))
        $output .= "
                <tr><th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['weaponskills']}</th></tr>";
      foreach ($weapon_array as $data)
      {
        $max = ($data[2] < $char[4]*5) ? $char[4]*5 : $data[2];
        $output .= "
                <tr>
                  ".($user_lvl ? "<td>$data[0]</td>" : "")."
                  <td align=\"right\">$data[1]</td>
                  <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(450*$data[2]/$max)-450)."px;\">
                    <span>$data[2]/$max</span>
                  </td>
                </tr>";
      }

      if(count($armor_array))
        $output .= "
                <tr><th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['armorproficiencies']}</th></tr>";
      foreach ($armor_array as $data)
      {
        $max = ($data[2] < $char[4]*5) ? $char[4]*5 : $data[2];
        $output .= "
                <tr>
                  ".($user_lvl ? "<td>$data[0]</td>" : "")."
                  <td align=\"right\">$data[1]</td>
                  <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: 0px;\">
                  </td>
                </tr>";
      }

      if(count($language_array))
        $output .= "
                <tr><th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['languages']}</th></tr>";
      foreach ($language_array as $data)
      {
        $max = ($data[2] < $char[4]*5) ? $char[4]*5 : $data[2];
        $output .= "
                <tr>
                  ".($user_lvl ? "<td>$data[0]</td>" : "")."
                  <td align=\"right\">$data[1]</td>
                  <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(450*$data[2]/$max)-450)."px;\">
                    <span>$data[2]/$max</span>
                  </td>
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
      $output .= "
                </td>
              </tr>
            </table>
            <br />
          </div>
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
    char_skill();
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");

?>
