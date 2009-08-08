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
// SHOW CHAR REPUTATION
//########################################################################################################################
function char_rep()
{
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $characters_db,
    $action_permission, $user_lvl, $user_name;

  require_once("libs/fact_lib.php");
  $reputation_rank = fact_get_reputation_rank_arr();
  $reputation_rank_length = fact_get_reputation_rank_length();

  if (empty($_GET['id']))
    error($lang_global['empty_fields']);

  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'],
    $characters_db[$realm_id]['name']);

  $id = $sql->quote_smart($_GET['id']);
  if (!is_numeric($id))
    $id = 0;

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
      $result = $sql->query("SELECT faction, standing, flags FROM character_reputation WHERE guid =$id AND (flags & 1 = 1)");

      $output .= "
        <center>
          <div id=\"tab\">
            <ul>
              <li id=\"selected\"><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>
              <li><a href=\"char_inv.php?id=$id\">{$lang_char['inventory']}</a></li>
              <li><a href=\"char_talent.php?id=$id\">{$lang_char['talents']}</a></li>
              <li><a href=\"char_achieve.php?id=$id\">{$lang_char['achievements']}</a></li>
              <li><a href=\"char_quest.php?id=$id\">{$lang_char['quests']}</a></li>
              <li><a href=\"char_friends.php?id=$id\">{$lang_char['friends']}</a></li>
             </ul>
          </div>
          <div id=\"tab_content\">
            <div id=\"tab\">
              <ul>";
      if( get_player_class($char[3]) == 'Hunter' )
        $output .= "
                <li><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>";
      $output .= "
                <li><a href=\"char_pets.php?id=$id\">{$lang_char['pets']}</a></li>
                <li id=\"selected\"><a href=\"char_rep.php?id=$id\">{$lang_char['reputation']}</a></li>
                <li><a href=\"char_skill.php?id=$id\">{$lang_char['skills']}</a></li>
              </ul>
            </div>
            <div id=\"tab_content2\">
          <font class=\"bold\">".htmlentities($char[1])." - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' /> <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' /> - lvl ".get_level_with_color($char[4])."</font>
          <br /><br />";

      $temp_out = array
      (
        1 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Alliance</th></tr>",0),
        2 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Horde</th></tr>",0),
        3 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Alliance Forces</th></tr>",0),
        4 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Horde Forces</th></tr>",0),
        5 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Steamwheedle Cartel</th></tr>",0),
        6 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Outland</th></tr>",0),
        7 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Shattrath City</th></tr>",0),
        8 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Alliance Vanguard</th></tr>",0),
        9 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Horde Expedition</th></tr>",0),
       10 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Sholazar Basin</th></tr>",0),
       11 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Northrend</th></tr>",0),
       12 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Other</th></tr>",0),
        0 => array("<table class=\"lined\" style=\"width: 550px;\">
             <tr><th colspan=\"3\" align=\"left\">Unknown</th></tr>",0)
      );

      if ($sql->num_rows($result))
      {
        while ($fact = $sql->fetch_row($result))
        {
          $faction  = $fact[0];
          $standing = $fact[1];

          $rep_rank      = fact_get_reputation_rank($faction, $standing, $char[2]);
          $rep_rank_name = $reputation_rank[$rep_rank];
          $rep_cap       = $reputation_rank_length[$rep_rank];
          $rep           = fact_get_reputation_at_rank($faction, $standing, $char[2]);
          $faction_name  = fact_get_faction_name($faction);
          $ft            = fact_get_faction_tree($faction);

          // not show alliance rep for horde and vice versa:
          if (!((((1 << ($char[2] - 1)) & 690) && ($ft == 1 || $ft == 3))
            || (((1 << ($char[2] - 1)) & 1101) && ($ft == 2 || $ft == 4))))
          {
            $temp_out[$ft][0] .= "
             <tr>
               <td width=\"30%\" align=\"left\">$faction_name</td>
               <td width=\"55%\" valign=\"top\">
                 <div class=\"faction-bar\">
                   <div class=\"rep$rep_rank\">
                     <span class=\"rep-data\">$rep/$rep_cap</span>
                     <div class=\"bar-color\" style=\"width:".(100*$rep/$rep_cap)."%\"></div>
                   </div>
                 </div>
               </td>
               <td width=\"15%\" align=\"left\" class=\"rep$rep_rank\">$rep_rank_name</td>
             </tr>";
            $temp_out[$ft][1] = 1;
          }
        }
      }
      else
        $output .= "
             <tr>
               <td colspan=\"2\"><br /><br />{$lang_global['err_no_records_found']}<br /><br /></td>
             </tr>";

      foreach ($temp_out as $out)
        if ($out[1])
          $output .= $out[0]."
           </table>";
      $output .= "
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
    char_rep();
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");

?>
