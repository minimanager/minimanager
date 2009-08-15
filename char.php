<?php


require_once("header.php");
require_once("scripts/defines.php");
require_once("libs/char_lib.php");
require_once("libs/item_lib.php");
require_once("libs/spell_lib.php");
require_once("libs/map_zone_lib.php");
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW GENERAL CHARACTERS INFO
//########################################################################################################################
function char_main(&$sqlr, &$sqlc)
{
  global $lang_global, $lang_char, $lang_item, $output, $realm_id, $realm_db, $characters_db, $server, $mmfpm_db,
    $action_permission, $user_lvl, $user_name, $user_id, $item_datasite, $spell_datasite , $showcountryflag;
  wowhead_tt();

  if (empty($_GET['id']))
    error($lang_global['empty_fields']);

  if (empty($_GET['realm']))
    $realmid = $realm_id;
  else
  {
    $realmid = $sqlr->quote_smart($_GET['realm']);
    if (!is_numeric($realmid)) $realmid = $realm_id;
  }

  $id = $sqlc->quote_smart($_GET['id']);
  if (!is_numeric($id))
    $id = 0;

  $result = $sqlc->query("SELECT account, race FROM `characters` WHERE guid = $id LIMIT 1");

  if ($sqlc->num_rows($result))
  {
    //resrict by owner's gmlvl
    $owner_acc_id = $sqlc->result($result, 0, 'account');
    $query = $sqlr->query("SELECT gmlevel,username FROM account WHERE id = $owner_acc_id");
    $owner_gmlvl = $sqlr->result($query, 0, 'gmlevel');
    $owner_name = $sqlr->result($query, 0, 'username');

    if(!$user_lvl && !$server[$realmid]['both_factions'])
    {
      $side_p = (in_array($sqlc->result($result, 0, 'race'),array(2,5,6,8,10))) ? 1 : 2;
      $result_1 = $sqlc->query("SELECT race FROM `characters` WHERE account = $user_id LIMIT 1");
      if ($sqlc->num_rows($result))
        $side_v = (in_array($sqlc->result($result_1, 0, 'race'), array(2,5,6,8,10))) ? 1 : 2;
      else
        $side_v = 0;
      unset($result_1);
    }
    else
    {
        $side_v = 0;
        $side_p = 0;
    }

    if ($user_lvl >= $owner_gmlvl && (($side_v == $side_p) || !$side_v))
    {
      $result = $sqlc->query("SELECT data, name, race, class, zone, map, online, totaltime,
        mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender,
        account FROM `characters` WHERE guid = $id");
      $char = $sqlc->fetch_row($result);
      $char_data = explode(' ',$char[0]);

      $online = ($char[6]) ? $lang_char['online'] : $lang_char['offline'];

      if($char_data[CHAR_DATA_OFFSET_GUILD_ID])
      {
        $query = $sqlc->query("SELECT name FROM guild WHERE guildid ='{$char_data[CHAR_DATA_OFFSET_GUILD_ID]}'");
        $guild_name = $sqlc->result($query, 0, 'name');
        $guild_name = "<a href=\"guild.php?action=view_guild&amp;realm=$realmid&amp;error=3&amp;id={$char_data[CHAR_DATA_OFFSET_GUILD_ID]}\" >$guild_name</a>";
        $mrank = $char_data[CHAR_DATA_OFFSET_GUILD_RANK] + 1;
        $guild_rank_query = $sqlc->query("SELECT rname FROM guild_rank WHERE guildid ='{$char_data[CHAR_DATA_OFFSET_GUILD_ID]}' AND rid='{$mrank}'");
        $guild_rank = $sqlc->result($guild_rank_query, 0, 'rname');
      }
      else
      {
        $guild_name = $lang_global['none'];
        $guild_rank = $lang_global['none'];
      }

      $block = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_BLOCK]));
      $block = round($block[1],2);
      $dodge = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_DODGE]));
      $dodge = round($dodge[1],2);
      $parry = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_PARRY]));
      $parry = round($parry[1],2);
      $crit = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_CRIT]));
      $crit = round($crit[1],2);
      $range_crit = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_RANGE_CRIT]));
      $range_crit = round($range_crit[1],2);
      $rage = round($char_data[CHAR_DATA_OFFSET_RAGE] / 10);
      $maxrage = round($char_data[CHAR_DATA_OFFSET_MAX_RAGE] / 10);
      $resilience = round($char_data[CHAR_DATA_OFFSET_FOCUS] / 10);
      $expertise = "{$char_data[CHAR_DATA_OFFSET_EXPERTISE]}"." / "."{$char_data[CHAR_DATA_OFFSET_OFFHAND_EXPERTISE]}";

      $EQU_HEAD      = $char_data[CHAR_DATA_OFFSET_EQU_HEAD];
      $EQU_NECK      = $char_data[CHAR_DATA_OFFSET_EQU_NECK];
      $EQU_SHOULDER  = $char_data[CHAR_DATA_OFFSET_EQU_SHOULDER];
      $EQU_SHIRT     = $char_data[CHAR_DATA_OFFSET_EQU_SHIRT];
      $EQU_CHEST     = $char_data[CHAR_DATA_OFFSET_EQU_CHEST];
      $EQU_BELT      = $char_data[CHAR_DATA_OFFSET_EQU_BELT];
      $EQU_LEGS      = $char_data[CHAR_DATA_OFFSET_EQU_LEGS];
      $EQU_FEET      = $char_data[CHAR_DATA_OFFSET_EQU_FEET];
      $EQU_WRIST     = $char_data[CHAR_DATA_OFFSET_EQU_WRIST];
      $EQU_GLOVES    = $char_data[CHAR_DATA_OFFSET_EQU_GLOVES];
      $EQU_FINGER1   = $char_data[CHAR_DATA_OFFSET_EQU_FINGER1];
      $EQU_FINGER2   = $char_data[CHAR_DATA_OFFSET_EQU_FINGER2];
      $EQU_TRINKET1  = $char_data[CHAR_DATA_OFFSET_EQU_TRINKET1];
      $EQU_TRINKET2  = $char_data[CHAR_DATA_OFFSET_EQU_TRINKET2];
      $EQU_BACK      = $char_data[CHAR_DATA_OFFSET_EQU_BACK];
      $EQU_MAIN_HAND = $char_data[CHAR_DATA_OFFSET_EQU_MAIN_HAND];
      $EQU_OFF_HAND  = $char_data[CHAR_DATA_OFFSET_EQU_OFF_HAND];
      $EQU_RANGED    = $char_data[CHAR_DATA_OFFSET_EQU_RANGED];
      $EQU_TABARD    = $char_data[CHAR_DATA_OFFSET_EQU_TABARD];
/*

// reserved incase we want to use back minimanagers' built in tooltip, instead of wowheads'
// minimanagers' item tooltip needs updating, but it can show enchantments and sockets.

      $equiped_items = array(
         1 => array(($EQU_HEAD      ? get_item_tooltip($EQU_HEAD)      : 0),($EQU_HEAD      ? get_item_icon($EQU_HEAD)      : 0),($EQU_HEAD      ? get_item_border($EQU_HEAD)      : 0)),
         2 => array(($EQU_NECK      ? get_item_tooltip($EQU_NECK)      : 0),($EQU_NECK      ? get_item_icon($EQU_NECK)      : 0),($EQU_NECK      ? get_item_border($EQU_NECK)      : 0)),
         3 => array(($EQU_SHOULDER  ? get_item_tooltip($EQU_SHOULDER)  : 0),($EQU_SHOULDER  ? get_item_icon($EQU_SHOULDER)  : 0),($EQU_SHOULDER  ? get_item_border($EQU_SHOULDER)  : 0)),
         4 => array(($EQU_SHIRT     ? get_item_tooltip($EQU_SHIRT)     : 0),($EQU_SHIRT     ? get_item_icon($EQU_SHIRT)     : 0),($EQU_SHIRT     ? get_item_border($EQU_SHIRT)     : 0)),
         5 => array(($EQU_CHEST     ? get_item_tooltip($EQU_CHEST)     : 0),($EQU_CHEST     ? get_item_icon($EQU_CHEST)     : 0),($EQU_CHEST     ? get_item_border($EQU_CHEST)     : 0)),
         6 => array(($EQU_BELT      ? get_item_tooltip($EQU_BELT)      : 0),($EQU_BELT      ? get_item_icon($EQU_BELT)      : 0),($EQU_BELT      ? get_item_border($EQU_BELT)      : 0)),
         7 => array(($EQU_LEGS      ? get_item_tooltip($EQU_LEGS)      : 0),($EQU_LEGS      ? get_item_icon($EQU_LEGS)      : 0),($EQU_LEGS      ? get_item_border($EQU_LEGS)      : 0)),
         8 => array(($EQU_FEET      ? get_item_tooltip($EQU_FEET)      : 0),($EQU_FEET      ? get_item_icon($EQU_FEET)      : 0),($EQU_FEET      ? get_item_border($EQU_FEET)      : 0)),
         9 => array(($EQU_WRIST     ? get_item_tooltip($EQU_WRIST)     : 0),($EQU_WRIST     ? get_item_icon($EQU_WRIST)     : 0),($EQU_WRIST     ? get_item_border($EQU_WRIST)     : 0)),
        10 => array(($EQU_GLOVES    ? get_item_tooltip($EQU_GLOVES)    : 0),($EQU_GLOVES    ? get_item_icon($EQU_GLOVES)    : 0),($EQU_GLOVES    ? get_item_border($EQU_GLOVES)    : 0)),
        11 => array(($EQU_FINGER1   ? get_item_tooltip($EQU_FINGER1)   : 0),($EQU_FINGER1   ? get_item_icon($EQU_FINGER1)   : 0),($EQU_FINGER1   ? get_item_border($EQU_FINGER1)   : 0)),
        12 => array(($EQU_FINGER2   ? get_item_tooltip($EQU_FINGER2)   : 0),($EQU_FINGER2   ? get_item_icon($EQU_FINGER2)   : 0),($EQU_FINGER2   ? get_item_border($EQU_FINGER2)   : 0)),
        13 => array(($EQU_TRINKET1  ? get_item_tooltip($EQU_TRINKET1)  : 0),($EQU_TRINKET1  ? get_item_icon($EQU_TRINKET1)  : 0),($EQU_TRINKET1  ? get_item_border($EQU_TRINKET1)  : 0)),
        14 => array(($EQU_TRINKET2  ? get_item_tooltip($EQU_TRINKET2)  : 0),($EQU_TRINKET2  ? get_item_icon($EQU_TRINKET2)  : 0),($EQU_TRINKET2  ? get_item_border($EQU_TRINKET2)  : 0)),
        15 => array(($EQU_BACK      ? get_item_tooltip($EQU_BACK)      : 0),($EQU_BACK      ? get_item_icon($EQU_BACK)      : 0),($EQU_BACK      ? get_item_border($EQU_BACK)      : 0)),
        16 => array(($EQU_MAIN_HAND ? get_item_tooltip($EQU_MAIN_HAND) : 0),($EQU_MAIN_HAND ? get_item_icon($EQU_MAIN_HAND) : 0),($EQU_MAIN_HAND ? get_item_border($EQU_MAIN_HAND) : 0)),
        17 => array(($EQU_OFF_HAND  ? get_item_tooltip($EQU_OFF_HAND)  : 0),($EQU_OFF_HAND  ? get_item_icon($EQU_OFF_HAND)  : 0),($EQU_OFF_HAND  ? get_item_border($EQU_OFF_HAND)  : 0)),
        18 => array(($EQU_RANGED    ? get_item_tooltip($EQU_RANGED)    : 0),($EQU_RANGED    ? get_item_icon($EQU_RANGED)    : 0),($EQU_RANGED    ? get_item_border($EQU_RANGED)    : 0)),
        19 => array(($EQU_TABARD    ? get_item_tooltip($EQU_TABARD)    : 0),($EQU_TABARD    ? get_item_icon($EQU_TABARD)    : 0),($EQU_TABARD    ? get_item_border($EQU_TABARD)    : 0))
      );
*/
      $equiped_items = array(
         1 => array("",($EQU_HEAD      ? get_item_icon($EQU_HEAD)      : 0),($EQU_HEAD      ? get_item_border($EQU_HEAD)      : 0)),
         2 => array("",($EQU_NECK      ? get_item_icon($EQU_NECK)      : 0),($EQU_NECK      ? get_item_border($EQU_NECK)      : 0)),
         3 => array("",($EQU_SHOULDER  ? get_item_icon($EQU_SHOULDER)  : 0),($EQU_SHOULDER  ? get_item_border($EQU_SHOULDER)  : 0)),
         4 => array("",($EQU_SHIRT     ? get_item_icon($EQU_SHIRT)     : 0),($EQU_SHIRT     ? get_item_border($EQU_SHIRT)     : 0)),
         5 => array("",($EQU_CHEST     ? get_item_icon($EQU_CHEST)     : 0),($EQU_CHEST     ? get_item_border($EQU_CHEST)     : 0)),
         6 => array("",($EQU_BELT      ? get_item_icon($EQU_BELT)      : 0),($EQU_BELT      ? get_item_border($EQU_BELT)      : 0)),
         7 => array("",($EQU_LEGS      ? get_item_icon($EQU_LEGS)      : 0),($EQU_LEGS      ? get_item_border($EQU_LEGS)      : 0)),
         8 => array("",($EQU_FEET      ? get_item_icon($EQU_FEET)      : 0),($EQU_FEET      ? get_item_border($EQU_FEET)      : 0)),
         9 => array("",($EQU_WRIST     ? get_item_icon($EQU_WRIST)     : 0),($EQU_WRIST     ? get_item_border($EQU_WRIST)     : 0)),
        10 => array("",($EQU_GLOVES    ? get_item_icon($EQU_GLOVES)    : 0),($EQU_GLOVES    ? get_item_border($EQU_GLOVES)    : 0)),
        11 => array("",($EQU_FINGER1   ? get_item_icon($EQU_FINGER1)   : 0),($EQU_FINGER1   ? get_item_border($EQU_FINGER1)   : 0)),
        12 => array("",($EQU_FINGER2   ? get_item_icon($EQU_FINGER2)   : 0),($EQU_FINGER2   ? get_item_border($EQU_FINGER2)   : 0)),
        13 => array("",($EQU_TRINKET1  ? get_item_icon($EQU_TRINKET1)  : 0),($EQU_TRINKET1  ? get_item_border($EQU_TRINKET1)  : 0)),
        14 => array("",($EQU_TRINKET2  ? get_item_icon($EQU_TRINKET2)  : 0),($EQU_TRINKET2  ? get_item_border($EQU_TRINKET2)  : 0)),
        15 => array("",($EQU_BACK      ? get_item_icon($EQU_BACK)      : 0),($EQU_BACK      ? get_item_border($EQU_BACK)      : 0)),
        16 => array("",($EQU_MAIN_HAND ? get_item_icon($EQU_MAIN_HAND) : 0),($EQU_MAIN_HAND ? get_item_border($EQU_MAIN_HAND) : 0)),
        17 => array("",($EQU_OFF_HAND  ? get_item_icon($EQU_OFF_HAND)  : 0),($EQU_OFF_HAND  ? get_item_border($EQU_OFF_HAND)  : 0)),
        18 => array("",($EQU_RANGED    ? get_item_icon($EQU_RANGED)    : 0),($EQU_RANGED    ? get_item_border($EQU_RANGED)    : 0)),
        19 => array("",($EQU_TABARD    ? get_item_icon($EQU_TABARD)    : 0),($EQU_TABARD    ? get_item_border($EQU_TABARD)    : 0))
      );

      $output .= "
        <center>
          <div id=\"tab\">
            <ul>
              <li id=\"selected\"><a href=\"char.php?id=$id&amp;realm=$realmid\">{$lang_char['char_sheet']}</a></li>";

      if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
      {
        $output .= "
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
                <li id=\"selected\"><a href=\"char.php?id=$id&amp;realm=$realmid\">{$lang_char['char_sheet']}</a></li>";
        if (char_get_class_name($char[3]) == 'Hunter' )
          $output .= "
                <li><a href=\"char_pets.php?id=$id&amp;realm=$realmid\">{$lang_char['pets']}</a></li>";
        $output .= "
                <li><a href=\"char_rep.php?id=$id&amp;realm=$realmid\">{$lang_char['reputation']}</a></li>
                <li><a href=\"char_skill.php?id=$id&amp;realm=$realmid\">{$lang_char['skills']}</a></li>";
      }
      else
        $output .="
             </ul>
          </div>
          <div id=\"tab_content\">
            <div id=\"tab\">
              <ul>";
      $output .="
              </ul>
            </div>
            <div id=\"tab_content2\">
            <table class=\"lined\" style=\"width: 580px;\">
              <tr>
                <td colspan=\"2\">
                  <div>
                    <img src=\"".char_get_avatar_img($char_data[CHAR_DATA_OFFSET_LEVEL],$char[8],$char[2],$char[3],0)."\" alt=\"avatar\" />
                  </div>
                  <div>";

      $a_results = $sqlc->query("SELECT DISTINCT spell FROM `character_aura` WHERE guid = $id");
      if ($sqlc->num_rows($a_results))
      {
        while ($aura = $sqlc->fetch_row($a_results))
        {
           $output .= "
                    <a style=\"padding:2px;\" href=\"$spell_datasite$aura[0]\" target=\"_blank\">
                      <img src=\"".get_spell_icon($aura[0])."\" alt=\"".$aura[0]."\" width=\"24\" height=\"24\" />
                    </a>";
        }
      }

      $sqlm = new SQL;
      $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

      if ($showcountryflag)
      {
        $loc = $sqlr->query("SELECT `last_ip` FROM `account` WHERE `id`='$char[9]';");
        $location = $sqlr->fetch_row($loc);
        $ip = $location[0];

        $nation = $sqlm->query("SELECT c.code, c.country FROM ip2nationCountries c, ip2nation i WHERE i.ip < INET_ATON('".$ip."') AND c.code = i.country ORDER BY i.ip DESC LIMIT 0,1;");
        $country = $sqlm->fetch_row($nation);
      }

      $output .="
                  </div>
                </td>
                <td colspan=\"4\">
                  <font class=\"bold\">
                    ".htmlentities($char[1])." - <img src='img/c_icons/{$char[2]}-{$char[8]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> - lvl ".char_get_level_color($char_data[CHAR_DATA_OFFSET_LEVEL])."
                  </font>
                  <br />{$lang_char['guild']}: $guild_name | {$lang_char['rank']}: ".htmlentities($guild_rank)."
                  <br />".(($char[6]) ? "<img src=\"img/up.gif\" onmousemove='toolTip(\"Online\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"online\" />" : "<img src=\"img/down.gif\" onmousemove='toolTip(\"Offline\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"offline\" />");
      if ($showcountryflag)
        $output .=" - ".(($country[0]) ? "<img src='img/flags/".$country[0].".png' onmousemove='toolTip(\"".($country[1])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />" : "-");
      $output .="
                </td>
              </tr>
              <tr>
                <td width=\"6%\">";
      if (!empty($equiped_items[1][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_HEAD\" target=\"_blank\">
                    <img src=\"{$equiped_items[1][1]}\" class=\"{$equiped_items[1][2]}\" alt=\"Head\" />
                  </a>";
      else
        $output .= "
                  <img src=\"img/INV/INV_empty_head.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td class=\"half_line\" colspan=\"4\">
                ".get_map_name($char[5], $sqlm)." - ".get_zone_name($char[4], $sqlm)."
                </td>
                <td width=\"6%\">";
      if (!empty($equiped_items[10][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_GLOVES\" target=\"_blank\">
                    <img src=\"{$equiped_items[10][1]}\" class=\"{$equiped_items[10][2]}\" alt=\"Gloves\" />
                  </a>";
      else
        $output .= "
                  <img src=\"img/INV/INV_empty_gloves.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
              </tr>
              <tr>
              <td>";
      if (!empty($equiped_items[2][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_NECK\" target=\"_blank\">
                    <img src=\"{$equiped_items[2][1]}\" class=\"{$equiped_items[2][2]}\" alt=\"Neck\" />
                  </a>";
      else
        $output .= "
                  <img src=\"img/INV/INV_empty_neck.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td class=\"half_line\" colspan=\"4\">
                  {$lang_char['honor_points']}: {$char_data[CHAR_DATA_OFFSET_HONOR_POINTS]} / {$char_data[CHAR_DATA_OFFSET_ARENA_POINTS]} - {$lang_char['honor_kills']}: {$char_data[CHAR_DATA_OFFSET_HONOR_KILL]}
                </td>
                <td>";
      if (!empty($equiped_items[6][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_BELT\" target=\"_blank\">
                    <img src=\"{$equiped_items[6][1]}\" class=\"{$equiped_items[6][2]}\" alt=\"Belt\" />
                  </a>";
      else
        $output .= "
                  <img src=\"img/INV/INV_empty_waist.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
              </tr>
              <tr>
                <td>";
      if (!empty($equiped_items[3][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_SHOULDER\" target=\"_blank\">
                    <img src=\"{$equiped_items[3][1]}\" class=\"{$equiped_items[3][2]}\" alt=\"Shoulder\" />
                  </a>";
      else
        $output .= "
                  <img src=\"img/INV/INV_empty_shoulder.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td class=\"half_line\" colspan=\"2\">
                  <div class=\"gradient_p\">{$lang_item['health']}:</div>
                  <div class=\"gradient_pp\">{$char_data[CHAR_DATA_OFFSET_HEALTH]}</div>";
      if ($char[3] == 11) //druid
        $output .="
                  </br>
                  <div class=\"gradient_p\">{$lang_item['energy']}:</div>
                  <div class=\"gradient_pp\">{$char_data[CHAR_DATA_OFFSET_ENERGY]}/{$char_data[CHAR_DATA_OFFSET_MAX_ENERGY]}</div>";
      $output .= "
                </td>
                <td class=\"half_line\" colspan=\"2\" align=\"center\" width=\"50%\">";

      if ($char[3] == 1) // warrior
      {
        $output .= "
                  <div class=\"gradient_p\">{$lang_item['rage']}:</div>
                  <div class=\"gradient_pp\">{$rage}/{$maxrage}</div>";
      }
      elseif ($char[3] == 4) // rogue
      {
        $output .= "
                  <div class=\"gradient_p\">{$lang_item['energy']}:</div>
                  <div class=\"gradient_pp\">{$char_data[CHAR_DATA_OFFSET_ENERGY]}/{$char_data[CHAR_DATA_OFFSET_MAX_ENERGY]}</div>";
      }
      elseif ($char[3] == 6) // death knight
      {
        // Don't know if FOCUS is the right one need to verify with Death Knight player.
        $output .= "
                  <div class=\"gradient_p\">{$lang_item['runic']}:</div>
                  <div class=\"gradient_pp\">{$char_data[CHAR_DATA_OFFSET_FOCUS]}/{$char_data[CHAR_DATA_OFFSET_MAX_FOCUS]}</div>";
      }
      elseif ($char[3] == 11) // druid
      {
        $output .= "
                  <div class=\"gradient_p\">{$lang_item['mana']}:</div>
                  <div class=\"gradient_pp\">{$char_data[CHAR_DATA_OFFSET_MAX_MANA]}</div>
                  </br>
                  <div class=\"gradient_p\">{$lang_item['rage']}:</div>
                  <div class=\"gradient_pp\">{$rage}/{$maxrage}</div>";
      }
      elseif ($char[3] == 2 || // paladin
              $char[3] == 3 || // hunter
              $char[3] == 5 || // priest
              $char[3] == 7 || // shaman
              $char[3] == 8 || // mage
              $char[3] == 9)   // warlock
      {
        $output .= "
                  <div class=\"gradient_p\">{$lang_item['mana']}:</div>
                  <div class=\"gradient_pp\">{$char_data[CHAR_DATA_OFFSET_MAX_MANA]}</div>";
      }
      $output .= "
                </td>
                <td>";

      if (!empty($equiped_items[7][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_LEGS\" target=\"_blank\">
                    <img src=\"{$equiped_items[7][1]}\" class=\"{$equiped_items[7][2]}\" alt=\"Legs\" />
                  </a>";
      else
        $output .= "
                  <img src=\"img/INV/INV_empty_legs.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
              </tr>
              <tr>
                <td>";
      if (!empty($equiped_items[15][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_BACK\" target=\"_blank\">
                    <img src=\"{$equiped_items[15][1]}\" class=\"{$equiped_items[15][2]}\" alt=\"Back\" />
                  </a>";
      else
        $output .= "
                  <img src=\"img/INV/INV_empty_chest_back.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td class=\"half_line\" colspan=\"2\" rowspan=\"3\" align=\"center\">
                  <div class=\"gradient_p\">
                    {$lang_item['strength']}:<br />
                    {$lang_item['agility']}:<br />
                    {$lang_item['stamina']}:<br />
                    {$lang_item['intellect']}:<br />
                    {$lang_item['spirit']}:<br />
                    {$lang_item['armor']}:
                  </div>
                  <div class=\"gradient_pp\">
                    {$char_data[CHAR_DATA_OFFSET_STR]}<br />
                    {$char_data[CHAR_DATA_OFFSET_AGI]}<br />
                    {$char_data[CHAR_DATA_OFFSET_STA]}<br />
                    {$char_data[CHAR_DATA_OFFSET_INT]}<br />
                    {$char_data[CHAR_DATA_OFFSET_SPI]}<br />
                    {$char_data[CHAR_DATA_OFFSET_ARMOR]}
                  </div>
                </td>
                <td class=\"half_line\" colspan=\"2\" rowspan=\"3\" align=\"center\">
                  <div class=\"gradient_p\">
                    {$lang_item['res_holy']}:<br />
                    {$lang_item['res_arcane']}:<br />
                    {$lang_item['res_fire']}:<br />
                    {$lang_item['res_nature']}:<br />
                    {$lang_item['res_frost']}:<br />
                    {$lang_item['res_shadow']}:
                  </div>
                  <div class=\"gradient_pp\">
                    {$char_data[CHAR_DATA_OFFSET_RES_HOLY]}<br />
                    {$char_data[CHAR_DATA_OFFSET_RES_ARCANE]}<br />
                    {$char_data[CHAR_DATA_OFFSET_RES_FIRE]}<br />
                    {$char_data[CHAR_DATA_OFFSET_RES_NATURE]}<br />
                    {$char_data[CHAR_DATA_OFFSET_RES_FROST]}<br />
                    {$char_data[CHAR_DATA_OFFSET_RES_SHADOW]}
                  </div>
                </td>
                <td>";
      if (!empty($equiped_items[8][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_FEET\" target=\"_blank\">
                    <img src=\"{$equiped_items[8][1]}\" class=\"{$equiped_items[8][2]}\" alt=\"Feet\" />
                  </a>";
      else
        $output .= "
                  <img src=\"img/INV/INV_empty_feet.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
              </tr>
              <tr>
                <td>";
      if (!empty($equiped_items[5][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_CHEST\" target=\"_blank\">
                    <img src=\"{$equiped_items[5][1]}\" class=\"{$equiped_items[5][2]}\" alt=\"Chest\" />
                  </a>";
      else
        $output .= "<img src=\"img/INV/INV_empty_chest_back.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td>";
      if (!empty($equiped_items[11][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_FINGER1\" target=\"_blank\">
                    <img src=\"{$equiped_items[11][1]}\" class=\"{$equiped_items[11][2]}\" alt=\"Finger1\" />
                  </a>";
      else
        $output .= "<img src=\"img/INV/INV_empty_finger.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
              </tr>
              <tr>
                <td>";
      if (!empty($equiped_items[4][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_SHIRT\" target=\"_blank\">
                    <img src=\"{$equiped_items[4][1]}\" class=\"{$equiped_items[4][2]}\" alt=\"Shirt\" />
                  </a>";
      else
        $output .= "<img src=\"img/INV/INV_empty_shirt.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td>";
      if (!empty($equiped_items[12][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_FINGER2\" target=\"_blank\">
                    <img src=\"{$equiped_items[12][1]}\" class=\"{$equiped_items[12][2]}\" alt=\"Finger2\" />
                  </a>";
      else $output .= "<img src=\"img/INV/INV_empty_finger.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
              </tr>
              <tr>
                <td>";
      if (!empty($equiped_items[19][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_TABARD\" target=\"_blank\">
                    <img src=\"{$equiped_items[19][1]}\" class=\"{$equiped_items[19][2]}\" alt=\"Tabard\" />
                  </a>";
      else $output .= "<img src=\"img/INV/INV_empty_tabard.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td class=\"half_line\" colspan=\"2\" rowspan=\"2\" align=\"center\">
                  <div class=\"gradient_p\">
                    {$lang_char['expertise']}:<br />
                    {$lang_char['block']}:<br />
                    {$lang_char['dodge']}:<br />
                    {$lang_char['parry']}:<br />";
      if ($char[3] == 6) //death knight
        $output .= "
                    {$lang_char['resilience']}:";
      $output .= "
                  </div>
                  <div class=\"gradient_pp\">
                    $expertise<br />
                    $block%<br />
                    $dodge%<br />
                    $parry%<br />";
      if ($char[3] == 6) //death knight
        $output .= "
                    $resilience";
      $output .= "
                  </div>
                </td>
                <td class=\"half_line\" colspan=\"2\" rowspan=\"2\" align=\"center\">
                  <div class=\"gradient_p\">
                    {$lang_char['melee_ap']}:<br />
                    {$lang_char['ranged_ap']}:<br />
                    {$lang_char['crit']}:<br />
                    {$lang_char['range_crit']}:
                  </div>
                  <div class=\"gradient_pp\">
                    {$char_data[CHAR_DATA_OFFSET_AP]}<br />
                    {$char_data[CHAR_DATA_OFFSET_RANGED_AP]}<br />
                    $crit%<br />
                    $range_crit%
                  </div>
                </td>
                <td>";
      if (!empty($equiped_items[13][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_TRINKET1\" target=\"_blank\">
                    <img src=\"{$equiped_items[13][1]}\" class=\"{$equiped_items[13][2]}\" alt=\"Trinket1\" />
                  </a>";
      else
        $output .= "<img src=\"img/INV/INV_empty_trinket.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
              </tr>
              <tr>
                <td>";
      if (!empty($equiped_items[9][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_WRIST\" target=\"_blank\">
                    <img src=\"{$equiped_items[9][1]}\" class=\"{$equiped_items[9][2]}\" alt=\"Wrist\" />
                  </a>";
      else
        $output .= "<img src=\"img/INV/INV_empty_wrist.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td>";
      if (!empty($equiped_items[14][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_TRINKET2\" target=\"_blank\">
                    <img src=\"{$equiped_items[14][1]}\" class=\"{$equiped_items[14][2]}\" alt=\"Trinket2\" />
                  </a>";
      else
        $output .= "<img src=\"img/INV/INV_empty_trinket.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
              </tr>
              <tr>
                <td></td>
                <td width=\"15%\">";
      if (!empty($equiped_items[16][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_MAIN_HAND\" target=\"_blank\">
                    <img src=\"{$equiped_items[16][1]}\" class=\"{$equiped_items[16][2]}\" alt=\"MainHand\" />
                  </a>";
      else
        $output .= "<img src=\"img/INV/INV_empty_main_hand.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td width=\"15%\">";
      if (!empty($equiped_items[17][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_OFF_HAND\" target=\"_blank\">
                    <img src=\"{$equiped_items[17][1]}\" class=\"{$equiped_items[17][2]}\" alt=\"OffHand\" />
                  </a>";
      else
        $output .= "<img src=\"img/INV/INV_empty_off_hand.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td width=\"15%\">";
      if (!empty($equiped_items[18][1]))
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite$EQU_RANGED\" target=\"_blank\">
                    <img src=\"{$equiped_items[18][1]}\" class=\"{$equiped_items[18][2]}\" alt=\"Ranged\" />
                  </a>";
      else
        $output .= "<img src=\"img/INV/INV_empty_ranged.png\" class=\"icon_border_0\" alt=\"empty\" />";
      $output .= "
                </td>
                <td width=\"15%\"></td>
                <td></td>
              </tr>
            </table>";
      if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
      {
        //total time played
        $tot_time = $char[7];
        $tot_days = (int)($tot_time/86400);
        $tot_time = $tot_time - ($tot_days*86400);
        $total_hours = (int)($tot_time/3600);
        $tot_time = $tot_time - ($total_hours*3600);
        $total_min = (int)($tot_time/60);

        $output .= "
            <table class=\"lined\" style=\"width: 580px;\">
              <tr>
                <td colspan=\"10\">
                  {$lang_char['tot_paly_time']}: $tot_days {$lang_char['days']} $total_hours {$lang_char['hours']} $total_min {$lang_char['min']}
                </td>
              </tr>
            </table>";
     }
     $output .= "
          </div>
          <br />
          </div>
          <br />
          <table class=\"hidden\">
            <tr>
              <td>";
      if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
      {
              makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$owner_acc_id",130);
      $output .= "
            </td>
            <td>";
      }
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
      //end of admin options
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
    char_main($sqlr, $sqlc);
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");

?>
