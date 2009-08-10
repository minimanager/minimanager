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
// SHOW CHARACTER TALENTS
//########################################################################################################################
function char_talent()
{
  require_once("libs/talent_lib.php");
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $characters_db, $mmfpm_db,
    $action_permission, $user_lvl, $user_name, $spell_datasite, $talent_calculator_datasite;

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

  //check for php gmp extension
  if (extension_loaded('gmp'))
    $GMP=1;
  else
    $GMP=0;
  //end of gmp check

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);

  $id = $sqlc->quote_smart($_GET['id']);
  if (!is_numeric($id))
    $id = 0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 1;
  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 0;
  $dir = ($dir) ? 0 : 1;

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
      $result = $sqlc->query("SELECT spell FROM `character_spell` WHERE guid = $id AND active = 1 ORDER BY spell DESC");

      $output .= "
        <center>
            <div id=\"tab\">
            <ul>
              <li><a href=\"char.php?id=$id&amp;realm=$realmid\">{$lang_char['char_sheet']}</a></li>
              <li><a href=\"char_inv.php?id=$id&amp;realm=$realmid\">{$lang_char['inventory']}</a></li>
              <li id=\"selected\"><a href=\"char_talent.php?id=$id&amp;realm=$realmid\">{$lang_char['talents']}</a></li>
              <li><a href=\"char_achieve.php?id=$id&amp;realm=$realmid\">{$lang_char['achievements']}</a></li>
              <li><a href=\"char_quest.php?id=$id&amp;realm=$realmid\">{$lang_char['quests']}</a></li>
              <li><a href=\"char_friends.php?id=$id&amp;realm=$realmid\">{$lang_char['friends']}</a></li>
            </ul>
          </div>
          <div id=\"tab_content\">
            <font class=\"bold\">".htmlentities($char[1])." - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> - lvl ".get_level_with_color($char[4])."</font>
            <br /><br />
            <table class=\"lined\" style=\"width: 550px;\">";

// This is WIP for talent tabs
//            <tr>
//              <div id=\"tab\">
//                <ul>";
//                  $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
//                  $tabs = $sql->query("SELECT `name_loc0` FROM `dbc_talenttab` WHERE `classes` = $char[3] ORDER BY `order` ASC");
//                  $tab1 = $sql->fetch_row($tabs);
//                  $tab2 = $sql->fetch_row($tabs);
//                  $tab3 = $sql->fetch_row($tabs);
//                  $output .="
//                    <li id=\"selected\"><a href=\"#\">".$tab1[0]."</a></li>
//                    <li><a href=\"#\">".$tab2[0]."</a></li>
//                    <li><a href=\"#\">".$tab3[0]."</a></li>
//                  </ul>
//                </div>
//              </tr>

      $output .="
              <tr>
                <th><a href=\"char_talent.php?id=$id&amp;realm=$realmid&amp;order_by=0&amp;dir=$dir\">".($order_by==0 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['talent_id']}</a></th>
                <th align=\"left\"><a href=\"char_talent.php?id=$id&amp;realm=$realmid&amp;order_by=1&amp;dir=$dir\">".($order_by==1 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_char['talent_name']}</a></th>
              </tr>";

      $talents_1 = array();

      if ($sqlc->num_rows($result))
      {
        while ($talent = $sqlc->fetch_row($result))
        {
          if(get_talent_value($talent[0]))
            array_push($talents_1, array($talent[0], get_spell_name($talent[0])));
        }
        aasort($talents_1, $order_by, $dir);

        if ($GMP)
          $talent_sum = gmp_init(0);

        foreach ($talents_1 as $data)
        {
          $output .= "
              <tr>
                <td>$data[0]</td>
                <td align=\"left\">
                  <a style=\"padding:2px;\" href=\"$spell_datasite$data[0]\" target=\"_blank\">
                    <img src=\"".get_spell_icon($data[0])."\" alt=\"\" />
                  </a>
                  <a href=\"$spell_datasite$data[0]\" target=\"_blank\">$data[1]</a>
                </td>";
          if ($GMP)
            $talent_sum = gmp_add($talent_sum,sprintf('%s',get_talent_value($data[0])));
          $output .= "
              </tr>";
        }

        $playerclass = strtolower(get_player_class($char[3]));
        /*

        // reserved till we get to calculate talent points using the new data we have in db

        switch ($playerclass)
        {
          case "shaman":
            $padlength = 61;
            break;
          case "druid":
            $padlength = 62;
            break;
          case "warlock":
          case "paladin":
          case "hunter":
          case "priest":
            $padlength = 64;
            break;
          case "warrior":
            $padlength = 66;
            break;
          case "rogue":
          case "mage":
            $padlength = 67;
            break;
        }

        if ($GMP)
          $output .= "
                <tr>
                  <td>
                    <a href=\"".$talent_calculator_datasite.$char[3]."&tal=".str_pad(sprintf('%s',gmp_strval($talent_sum)), "0", "0", STR_PAD_LEFT)."\" target=\"_blank\">Talent Calculator</a>
                  </td>
                </tr>";
        */
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
    char_talent();
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");

?>
