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
valid_login($action_permission['read']);

//########################################################################################################################^M
// SHOW CHARACTER PETS
//########################################################################################################################^M
function char_pets()
{
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $characters_db,
    $action_permission, $user_lvl, $user_name, $spell_datasite, $pet_ability;
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
  if (!is_numeric($id))
    $id = 0;

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
      $result = $sqlc->query("SELECT id,level,exp,name,curhappiness FROM `character_pet` WHERE owner = $id");
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
              <ul>
                <li><a href=\"char.php?id=$id&amp;realm=$realmid\">{$lang_char['char_sheet']}</a></li>
                <li id=\"selected\"><a href=\"char_pets.php?id=$id&amp;realm=$realmid\">{$lang_char['pets']}</a></li>
                <li><a href=\"char_rep.php?id=$id&amp;realm=$realmid\">{$lang_char['reputation']}</a></li>
                <li><a href=\"char_skill.php?id=$id&amp;realm=$realmid\">{$lang_char['skills']}</a></li>
              </ul>
            </div>
            <div id=\"tab_content2\">";

      if ($sqlc->num_rows($result))
      {
        while($pet = $sqlc->fetch_row($result))
        {
          $happiness = floor($pet[4]/333000);
          switch ($happiness)
          {
            case 3:
            case 2:
              $hap_text = "Happy";
              $hap_val = 2;
              break;
            case 1:
              $hap_text = "Content";
              $hap_val = 1;
              break;
            default:
              $hap_text = "Unhappy";
              $hap_val = 0;
          }
          $pet_next_lvl_xp = floor(get_xp_to_level($pet[1])/4);
          $output .= "
              <font class=\"bold\">$pet[3] - lvl ".get_level_with_color($pet[1])."
                <a style=\"padding:2px;\" onmouseover=\"toolTip('&lt;font color=\'white\'&gt;$hap_text&lt;/font&gt;','item_tooltip')\" onmouseout=\"toolTip()\"><img src=\"img/pet/happiness_$hap_val.jpg\" alt=\"\" /></a>
                <br />
                <br />
              </font>
              <table class=\"lined\" style=\"width: 550px;\">
                <tr>
                  <td align=\"right\">Exp:</td>
                  <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(385*$pet[2]/$pet_next_lvl_xp)-385)."px;\">
                    <span>$pet[2]/$pet_next_lvl_xp</span>
                  </td>
                </tr>
                <tr>
                  <td align=\"right\">Pet Abilities:</td>
                  <td align=\"left\">";
          $ability_results = $sqlc->query("SELECT spell FROM `pet_spell` WHERE guid = '$pet[0]' and active > 1"); // active = 0 is unused and active = 1 probably some passive auras, i dont know diference between values 129 and 193, need to check mangos source
          if ($sqlc->num_rows($ability_results))
          {
            while ($ability = $sqlc->fetch_row($ability_results))
            {
              $output .= "
                    <a style=\"padding:2px;\" href=\"$spell_datasite$ability[0]\" target=\"_blank\">
                      <img src=\"".get_spell_icon($ability[0])."\" alt=\"".$ability[0]."\" />
                    </a>";
            }
          }
          $output .= "
                  </td>
                </tr>
              </table>
              <br /><br />";
        }
      }

      $output .= "
            </div>
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
        makebutton($lang_char['send_mail'], "mail.php?type=ingame_mail",130);
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
    char_pets();
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");

?>
