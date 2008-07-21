<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */
require_once("header.php");
valid_login($action_permission['read']);
require_once("scripts/id_tab.php");
require_once("scripts/get_lib.php");
require_once("scripts/talents.php");
require_once("scripts/pets.php");
require_once("scripts/defines.php");
require_once("scripts/char_aura.php");

//########################################################################################################################
// SHOW GENERAL CHARACTERS INFO
//########################################################################################################################
function char_main() 
{
    global $lang_global, $lang_char, $lang_item, $output, $realm_db, $characters_db, $realm_id, $user_lvl,$mangos_db,
        $user_name, $item_datasite, $server, $user_id, $char_aura, $talent_datasite;

    if (empty($_GET['id'])) error($lang_global['empty_fields']);

    $sql = new SQL;
    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $id = $sql->quote_smart($_GET['id']);
    
    $result = $sql->query("SELECT account, race FROM `characters` WHERE guid = $id LIMIT 1");

    if ($sql->num_rows($result))
    {
        //resrict by owner's gmlvl
        $owner_acc_id = $sql->result($result, 0, 'account');
        $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
        $query = $sql->query("SELECT gmlevel,username FROM account WHERE id = $owner_acc_id");
        $owner_gmlvl = $sql->result($query, 0, 'gmlevel');
        $owner_name = $sql->result($query, 0, 'username');

        $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

        if(!$user_lvl && !$server[$realm_id]['both_factions'])
        {
            $side_p = (in_array($sql->result($result, 0, 'race'),array(2,5,6,8,10))) ? 1 : 2;
            $result_1 = $sql->query("SELECT race FROM `characters` WHERE account = $user_id LIMIT 1");
            if ($sql->num_rows($result))
                $side_v = (in_array($sql->result($result_1, 0, 'race'), array(2,5,6,8,10))) ? 1 : 2;
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

        $result = $sql->query("SELECT data,name,race,class,zone,map,online,totaltime, mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(36+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender FROM `characters` WHERE guid = $id");
        $char = $sql->fetch_row($result);
        $char_data = explode(' ',$char[0]);

        $online = ($char[6]) ? $lang_char['online'] : $lang_char['offline'];

        if($char_data[CHAR_DATA_OFFSET_GUILD_ID])
        {
            $query = $sql->query("SELECT name FROM guild WHERE guildid ='{$char_data[CHAR_DATA_OFFSET_GUILD_ID]}'");
            $guild_name = $sql->result($query, 0, 'name');
            $guild_name = "<a href=\"guild.php?action=view_guild&amp;error=3&amp;id={$char_data[CHAR_DATA_OFFSET_GUILD_ID]}\" >$guild_name</a>";
            $mrank = $char_data[CHAR_DATA_OFFSET_GUILD_RANK] + 1;
            $guild_rank_query = $sql->query("SELECT rname FROM guild_rank WHERE guildid ='{$char_data[CHAR_DATA_OFFSET_GUILD_ID]}' AND rid='{$mrank}'");
            $guild_rank = $sql->result($guild_rank_query, 0, 'rname');
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

        $EQU_HEAD = $char_data[CHAR_DATA_OFFSET_EQU_HEAD];
        $EQU_NECK = $char_data[CHAR_DATA_OFFSET_EQU_NECK];
        $EQU_SHOULDER = $char_data[CHAR_DATA_OFFSET_EQU_SHOULDER];
        $EQU_SHIRT = $char_data[CHAR_DATA_OFFSET_EQU_SHIRT];
        $EQU_CHEST = $char_data[CHAR_DATA_OFFSET_EQU_CHEST];
        $EQU_BELT = $char_data[CHAR_DATA_OFFSET_EQU_BELT];
        $EQU_LEGS = $char_data[CHAR_DATA_OFFSET_EQU_LEGS];
        $EQU_FEET = $char_data[CHAR_DATA_OFFSET_EQU_FEET];
        $EQU_WRIST = $char_data[CHAR_DATA_OFFSET_EQU_WRIST];
        $EQU_GLOVES = $char_data[CHAR_DATA_OFFSET_EQU_GLOVES];
        $EQU_FINGER1 = $char_data[CHAR_DATA_OFFSET_EQU_FINGER1];
        $EQU_FINGER2 = $char_data[CHAR_DATA_OFFSET_EQU_FINGER2];
        $EQU_TRINKET1 = $char_data[CHAR_DATA_OFFSET_EQU_TRINKET1];
        $EQU_TRINKET2 = $char_data[CHAR_DATA_OFFSET_EQU_TRINKET2];
        $EQU_BACK = $char_data[CHAR_DATA_OFFSET_EQU_BACK];
        $EQU_MAIN_HAND = $char_data[CHAR_DATA_OFFSET_EQU_MAIN_HAND];
        $EQU_OFF_HAND = $char_data[CHAR_DATA_OFFSET_EQU_OFF_HAND];
        $EQU_RANGED = $char_data[CHAR_DATA_OFFSET_EQU_RANGED];
        $EQU_TABARD = $char_data[CHAR_DATA_OFFSET_EQU_TABARD];

        $equiped_items = array(
            1 => array(($EQU_HEAD       ? get_item_tooltip($EQU_HEAD)      : 0), ($EQU_HEAD      ? get_icon($EQU_HEAD)      : 0),($EQU_HEAD      ? get_item_border($EQU_HEAD)      : 0)),
            2 => array(($EQU_NECK       ? get_item_tooltip($EQU_NECK)      : 0), ($EQU_NECK      ? get_icon($EQU_NECK)      : 0),($EQU_NECK      ? get_item_border($EQU_NECK)      : 0)),
            3 => array(($EQU_SHOULDER   ? get_item_tooltip($EQU_SHOULDER)  : 0), ($EQU_SHOULDER  ? get_icon($EQU_SHOULDER)  : 0),($EQU_SHOULDER  ? get_item_border($EQU_SHOULDER)  : 0)),
            4 => array(($EQU_SHIRT      ? get_item_tooltip($EQU_SHIRT)     : 0), ($EQU_SHIRT     ? get_icon($EQU_SHIRT)     : 0),($EQU_SHIRT     ? get_item_border($EQU_SHIRT)     : 0)),
            5 => array(($EQU_CHEST      ? get_item_tooltip($EQU_CHEST)     : 0), ($EQU_CHEST     ? get_icon($EQU_CHEST)     : 0),($EQU_CHEST     ? get_item_border($EQU_CHEST)     : 0)),
            6 => array(($EQU_BELT       ? get_item_tooltip($EQU_BELT)      : 0), ($EQU_BELT      ? get_icon($EQU_BELT)      : 0),($EQU_BELT      ? get_item_border($EQU_BELT)      : 0)),
            7 => array(($EQU_LEGS       ? get_item_tooltip($EQU_LEGS)      : 0), ($EQU_LEGS      ? get_icon($EQU_LEGS)      : 0),($EQU_LEGS      ? get_item_border($EQU_LEGS)      : 0)),
            8 => array(($EQU_FEET       ? get_item_tooltip($EQU_FEET)      : 0), ($EQU_FEET      ? get_icon($EQU_FEET)      : 0),($EQU_FEET      ? get_item_border($EQU_FEET)      : 0)),
            9 => array(($EQU_WRIST      ? get_item_tooltip($EQU_WRIST)     : 0), ($EQU_WRIST     ? get_icon($EQU_WRIST)     : 0),($EQU_WRIST     ? get_item_border($EQU_WRIST)     : 0)),
            10 => array(($EQU_GLOVES    ? get_item_tooltip($EQU_GLOVES)    : 0), ($EQU_GLOVES    ? get_icon($EQU_GLOVES)    : 0),($EQU_GLOVES    ? get_item_border($EQU_GLOVES)    : 0)),
            11 => array(($EQU_FINGER1   ? get_item_tooltip($EQU_FINGER1)   : 0), ($EQU_FINGER1   ? get_icon($EQU_FINGER1)   : 0),($EQU_FINGER1   ? get_item_border($EQU_FINGER1)   : 0)),
            12 => array(($EQU_FINGER2   ? get_item_tooltip($EQU_FINGER2)   : 0), ($EQU_FINGER2   ? get_icon($EQU_FINGER2)   : 0),($EQU_FINGER2   ? get_item_border($EQU_FINGER2)   : 0)),
            13 => array(($EQU_TRINKET1  ? get_item_tooltip($EQU_TRINKET1)  : 0), ($EQU_TRINKET1  ? get_icon($EQU_TRINKET1)  : 0),($EQU_TRINKET1  ? get_item_border($EQU_TRINKET1)  : 0)),
            14 => array(($EQU_TRINKET2  ? get_item_tooltip($EQU_TRINKET2)  : 0), ($EQU_TRINKET2  ? get_icon($EQU_TRINKET2)  : 0),($EQU_TRINKET2  ? get_item_border($EQU_TRINKET2)  : 0)),
            15 => array(($EQU_BACK      ? get_item_tooltip($EQU_BACK)      : 0), ($EQU_BACK      ? get_icon($EQU_BACK)      : 0),($EQU_BACK      ? get_item_border($EQU_BACK)      : 0)),
            16 => array(($EQU_MAIN_HAND ? get_item_tooltip($EQU_MAIN_HAND) : 0), ($EQU_MAIN_HAND ? get_icon($EQU_MAIN_HAND) : 0),($EQU_MAIN_HAND ? get_item_border($EQU_MAIN_HAND) : 0)),
            17 => array(($EQU_OFF_HAND  ? get_item_tooltip($EQU_OFF_HAND)  : 0), ($EQU_OFF_HAND  ? get_icon($EQU_OFF_HAND)  : 0),($EQU_OFF_HAND  ? get_item_border($EQU_OFF_HAND)  : 0)),
            18 => array(($EQU_RANGED    ? get_item_tooltip($EQU_RANGED)    : 0), ($EQU_RANGED    ? get_icon($EQU_RANGED)    : 0),($EQU_RANGED    ? get_item_border($EQU_RANGED)    : 0)),
            19 => array(($EQU_TABARD    ? get_item_tooltip($EQU_TABARD)    : 0), ($EQU_TABARD    ? get_icon($EQU_TABARD)    : 0),($EQU_TABARD    ? get_item_border($EQU_TABARD)    : 0))
          );


        $output .= "<center><div id=\"tab\"><ul><li id=\"selected\"><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>";

        if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
        {  
            $output .= "<li><a href=\"char.php?id=$id&amp;action=char_inv\">{$lang_char['inventory']}</a></li>
            <li><a href=\"char.php?id=$id&amp;action=char_quest\">{$lang_char['quests']}</a></li>
            <li><a href=\"char.php?id=$id&amp;action=char_skill\">{$lang_char['skills']}</a></li>
            <li><a href=\"char.php?id=$id&amp;action=char_talent\">{$lang_char['talents']}</a></li>
            <li><a href=\"char.php?id=$id&amp;action=char_rep\">{$lang_char['reputation']}</a></li>";
            if (get_player_class($char[3]) == 'Hunter' ) { $output .= "  <li><a href=\"char.php?id=$id&amp;action=char_pets\">{$lang_char['pets']}</a></li>"; }
        }
        $output .= "</ul></div><div id=\"tab_content\"><table class=\"lined\" style=\"width: 580px;\">
            <tr><td colspan=\"6\"><div><img src=".get_image_dir($char_data[CHAR_DATA_OFFSET_LEVEL],$char[8],$char[2],$char[3],0)."></div><div>";

        $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
        $a_results = $sql->query("SELECT DISTINCT spell,remaintime FROM `character_aura` WHERE guid = $id");
        if ($sql->num_rows($a_results))
        {
            while ($aura = $sql->fetch_row($a_results))
            {
                if (isset($char_aura[$aura[0]]))
                    $output .= "<a style=\"padding:2px;\" onmouseover=\"toolTip('<font color=\'white\'>".get_char_aura_name($aura[0])."<br />Id: $aura[0]</font>','item_tooltip')\" onmouseout=\"toolTip()\" href=\"$talent_datasite$aura[0]\"><img src=\"img/Char_AURA/".get_char_aura_image($aura[0])."\" width=24 height=24></a>";
                else  
                    $output .= ": <a href=\"$talent_datasite$aura[0]\">aura $aura[0]</a> :"; 
            }
        }

  $output .="</div><font class=\"bold\">$char[1] - ".get_player_race($char[2])." ".get_player_class($char[3])." (lvl {$char_data[CHAR_DATA_OFFSET_LEVEL]})</font><br />
  {$lang_char['guild']}: $guild_name | {$lang_char['rank']}: $guild_rank<br />$online</td></tr>
  <tr>
    <td width=\"6%\">";
    if (!empty($equiped_items[1][1])) $output .= maketooltip("<img src=\"{$equiped_items[1][1]}\" class=\"{$equiped_items[1][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_HEAD]}", $equiped_items[1][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_head.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
    <td class=\"half_line\" colspan=\"4\">".get_map_name($char[5])." - ".get_zone_name($char[4])."</td>
    <td width=\"6%\">";
    if (!empty($equiped_items[10][1])) $output .= maketooltip("<img src=\"{$equiped_items[10][1]}\" class=\"{$equiped_items[10][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_GLOVES]}", $equiped_items[10][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_gloves.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
  </tr>
  <tr>
    <td>";
    if (!empty($equiped_items[2][1])) $output .= maketooltip("<img src=\"{$equiped_items[2][1]}\" class=\"{$equiped_items[2][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_NECK]}", $equiped_items[2][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_neck.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
    <td class=\"half_line\" colspan=\"4\">
    {$lang_char['honor_points']}: {$char_data[CHAR_DATA_OFFSET_HONOR_POINTS]}/{$char_data[CHAR_DATA_OFFSET_ARENA_POINTS]} - {$lang_char['honor_kills']}: {$char_data[CHAR_DATA_OFFSET_HONOR_KILL]}</td>
    <td>";
    if (!empty($equiped_items[6][1])) $output .= maketooltip("<img src=\"{$equiped_items[6][1]}\" class=\"{$equiped_items[6][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_BELT]}", $equiped_items[6][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_waist.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
  </tr>
  <tr>
    <td>";
    if (!empty($equiped_items[3][1])) $output .= maketooltip("<img src=\"{$equiped_items[3][1]}\" class=\"{$equiped_items[3][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_SHOULDER]}", $equiped_items[3][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_shoulder.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
    <td class=\"half_line\" colspan=\"2\">
      <div class=\"gradient_p\">{$lang_item['health']}:</div>
      <div class=\"gradient_pp\">{$char_data[CHAR_DATA_OFFSET_HEALTH]}</div>
    </td>
      <td class=\"half_line\" colspan=\"2\" align=\"center\" width=\"50%\">
      <div class=\"gradient_p\">{$lang_item['mana']}:</div>
      <div class=\"gradient_pp\">{$char_data[CHAR_DATA_OFFSET_MANA]}</div>
    </td>
    <td>";
    if (!empty($equiped_items[7][1])) $output .= maketooltip("<img src=\"{$equiped_items[7][1]}\" class=\"{$equiped_items[7][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_LEGS]}", $equiped_items[7][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_legs.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
  </tr>
  <tr>
    <td>";
    if (!empty($equiped_items[15][1])) $output .= maketooltip("<img src=\"{$equiped_items[15][1]}\" class=\"{$equiped_items[15][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_BACK]}", $equiped_items[15][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_chest_back.png\" class=\"icon_border_0\" alt=\"\" />";
    $output .= "</td>
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
    if (!empty($equiped_items[8][1])) $output .= maketooltip("<img src=\"{$equiped_items[8][1]}\" class=\"{$equiped_items[8][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_FEET]}", $equiped_items[8][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_feet.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
  </tr>
  <tr>
    <td>";
    if (!empty($equiped_items[5][1])) $output .= maketooltip("<img src=\"{$equiped_items[5][1]}\" class=\"{$equiped_items[5][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_CHEST]}", $equiped_items[5][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_chest_back.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
    <td>";
    if (!empty($equiped_items[11][1])) $output .= maketooltip("<img src=\"{$equiped_items[11][1]}\" class=\"{$equiped_items[11][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_FINGER1]}", $equiped_items[11][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_finger.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
  </tr>
  <tr>
    <td>";
    if (!empty($equiped_items[4][1])) $output .= maketooltip("<img src=\"{$equiped_items[4][1]}\" class=\"{$equiped_items[4][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_SHIRT]}", $equiped_items[4][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_shirt.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
    <td>";
    if (!empty($equiped_items[12][1])) $output .= maketooltip("<img src=\"{$equiped_items[12][1]}\" class=\"{$equiped_items[12][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_FINGER2]}", $equiped_items[12][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_finger.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
  </tr>
  <tr>
    <td>";
    if (!empty($equiped_items[19][1])) $output .= maketooltip("<img src=\"{$equiped_items[19][1]}\" class=\"{$equiped_items[19][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_TABARD]}", $equiped_items[19][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_tabard.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
    <td class=\"half_line\" colspan=\"2\" rowspan=\"2\" align=\"center\">
      <div class=\"gradient_p\">
        {$lang_char['exp']}:<br />
        {$lang_char['block']}:<br />
        {$lang_char['dodge']}:<br />
        {$lang_char['parry']}:
      </div>
      <div class=\"gradient_pp\">
        {$char_data[CHAR_DATA_OFFSET_EXP]}<br />
        $block%<br />
        $dodge%<br />
        $parry%
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
    if (!empty($equiped_items[13][1])) $output .= maketooltip("<img src=\"{$equiped_items[13][1]}\" class=\"{$equiped_items[13][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_TRINKET1]}", $equiped_items[13][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_trinket.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
  </tr>
  <tr>
    <td>";
    if (!empty($equiped_items[9][1])) $output .= maketooltip("<img src=\"{$equiped_items[9][1]}\" class=\"{$equiped_items[9][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_WRIST]}", $equiped_items[9][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_wrist.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>

    <td>";
    if (!empty($equiped_items[14][1])) $output .= maketooltip("<img src=\"{$equiped_items[14][1]}\" class=\"{$equiped_items[14][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_TRINKET2]}", $equiped_items[14][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_trinket.png\" class=\"icon_border_0\" alt=\"\" />";
    $output .= "</td>
  </tr>
  <tr>
    <td></td>
    <td width=\"15%\">";
    if (!empty($equiped_items[16][1])) $output .= maketooltip("<img src=\"{$equiped_items[16][1]}\" class=\"{$equiped_items[16][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_MAIN_HAND]}", $equiped_items[16][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_main_hand.png\" class=\"icon_border_0\" alt=\"\" />";
    $output .= "</td>
    <td width=\"15%\">";
    if (!empty($equiped_items[17][1])) $output .= maketooltip("<img src=\"{$equiped_items[17][1]}\" class=\"{$equiped_items[17][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_OFF_HAND]}", $equiped_items[17][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_off_hand.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
    <td width=\"15%\">";
    if (!empty($equiped_items[18][1])) $output .= maketooltip("<img src=\"{$equiped_items[18][1]}\" class=\"{$equiped_items[18][2]}\" alt=\"\" />", "$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_RANGED]}", $equiped_items[18][0], "item_tooltip", "target=\"_blank\"");
      else $output .= "<img src=\"img/Char_INV/INV_empty_ranged.png\" class=\"icon_border_0\" alt=\"\" />";
$output .= "</td>
<td width=\"15%\"></td>
    <td></td></tr>
</table><br />";

if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name)){
  //total time played
  $tot_time = $char[7];
  $tot_days = (int)($tot_time/86400);
  $tot_time = $tot_time - ($tot_days*86400);
  $total_hours = (int)($tot_time/3600);
  $tot_time = $tot_time - ($total_hours*3600);
  $total_min = (int)($tot_time/60);

 $output .= "<table class=\"lined\" style=\"width: 580px;\">
  <tr><td colspan=\"10\">{$lang_char['tot_paly_time']}: $tot_days {$lang_char['days']} $total_hours {$lang_char['hours']} $total_min {$lang_char['min']}</td></tr>
</table></div><br />";

$output .= "<table class=\"hidden\">
          <tr><td>";
    if ($user_lvl > $owner_gmlvl){
      makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$owner_acc_id",140);
      makebutton($lang_char['edit_button'], "char_edit.php?id=$id",140);
      }
    if (($user_lvl > 0)&&(($user_lvl > $owner_gmlvl)||($owner_name == $user_name))){
      makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id",140);
      makebutton($lang_char['send_mail'], "mail.php?type=ingame_mail&amp;to=$char[1]",140);
      }
    makebutton($lang_global['back'], "javascript:window.history.back()",140);
 $output .= "</td></tr>
        </table><br /></center>";

  //end of admin options
  } else {
      $output .= "<table class=\"hidden\">
        <tr><td>";
          makebutton($lang_global['back'], "javascript:window.history.back()",200);
      $output .= "</td></tr>
        </table><br /></center>";
      }
 } else {
    $sql->close();
    error($lang_char['no_permission']);
    }

} else error($lang_char['no_char_found']);
$sql->close();
}


//########################################################################################################################
// SHOW INV. AND BANK ITEMS
//########################################################################################################################
function char_inv() {
 global $lang_global, $lang_char, $lang_item, $output, $realm_db, $characters_db, $realm_id, $user_lvl,$mangos_db,
    $user_name,$item_datasite;

if (empty($_GET['id'])) error($lang_global['empty_fields']);

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$id = $sql->quote_smart($_GET['id']);

$result = $sql->query("SELECT account FROM `characters` WHERE guid = $id LIMIT 1");

if ($sql->num_rows($result)){
  $owner_acc_id = $sql->result($result, 0, 'account');
  $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $query = $sql->query("SELECT gmlevel,username FROM account WHERE id ='$owner_acc_id'");
  $owner_gmlvl = $sql->result($query, 0, 'gmlevel');
  $owner_name = $sql->result($query, 0, 'username');

if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name)){

  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $result = $sql->query("SELECT name,race,class,SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_GOLD+1)."), ' ', -1), SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1), mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(36+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender FROM `characters` WHERE guid = $id");

  $char = $sql->fetch_row($result);

  $result = $sql->query("SELECT ci.bag,ci.slot,ci.item,ci.item_template, SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 15), ' ', -1) as stack_count FROM character_inventory ci INNER JOIN item_instance ii on ii.guid = ci.item WHERE ci.guid = $id ORDER BY ci.bag,ci.slot");
  $bag = array(
    0=>array(),
    1=>array(),
    2=>array(),
    3=>array(),
    4=>array()
    );

  $bank = array(
    0=>array(),
    1=>array(),
    2=>array(),
    3=>array(),
    4=>array(),
    5=>array(),
    6=>array(),
    7=>array()
    );

  $bank_bag_id = array();
  $bag_id = array();
  $equiped_bag_id = array(0,0,0,0,0);
  $equip_bnk_bag_id = array(0,0,0,0,0,0,0,0);


  while ($slot = $sql->fetch_row($result))
  {
    if ($slot[0] == 0 && $slot[1] > 18)
    {
      if($slot[1] < 23) // SLOT 19 TO 22 (Bags)
      {
        $bag_id[$slot[2]] = ($slot[1]-18);
				$equiped_bag_id[$slot[1]-18] = array($slot[3], $sql->result($sql->query("SELECT ContainerSlots FROM `".$mangos_db[$realm_id]['name']."`.`item_template` WHERE entry ='{$slot[3]}'"), 0, 'ContainerSlots'), $slot[4]);
      }
      elseif($slot[1] < 39) // SLOT 23 TO 38 (BackPack)
      {
        if(isset($bag[0][$slot[1]-23]))
          $bag[0][$slot[1]-23][0]++;
				else $bag[0][$slot[1]-23] = array($slot[3],0,$slot[4]);
      }
      elseif($slot[1] < 67) // SLOT 39 TO 66 (Bank)
      {
			  $bank[0][$slot[1]-39] = array($slot[3],0,$slot[4]);
      }
      elseif($slot[1] < 74) // SLOT 67 TO 73 (Bank Bags)
      {
        $bank_bag_id[$slot[2]] = ($slot[1]-66);
        $equip_bnk_bag_id[$slot[1]-66] = array($slot[3], $sql->result($sql->query("SELECT ContainerSlots FROM `".$mangos_db[$realm_id]['name']."`.`item_template` WHERE entry ='{$slot[3]}'"), 0, 'ContainerSlots'), $slot[4]);
      }
    }
    else
    {
      // Bags
      if (isset($bag_id[$slot[0]]))
      {
        if(isset($bag[$bag_id[$slot[0]]][$slot[1]]))
          $bag[$bag_id[$slot[0]]][$slot[1]][1]++;
        else $bag[$bag_id[$slot[0]]][$slot[1]] = array($slot[3],0,$slot[4]);
      }
      // Bank Bags
      elseif (isset($bank_bag_id[$slot[0]]))
      {
        $bank[$bank_bag_id[$slot[0]]][$slot[1]] = array($slot[3],0,$slot[4]);
      }
    }
  }

$output .= "<center>
<div id=\"tab\">
<ul>
  <li><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>
  <li id=\"selected\"><a href=\"char.php?id=$id&amp;action=char_inv\">{$lang_char['inventory']}</a></li>
  <li><a href=\"char.php?id=$id&amp;action=char_quest\">{$lang_char['quests']}</a></li>
  <li><a href=\"char.php?id=$id&amp;action=char_skill\">{$lang_char['skills']}</a></li>
  <li><a href=\"char.php?id=$id&amp;action=char_talent\">{$lang_char['talents']}</a></li>
  <li><a href=\"char.php?id=$id&amp;action=char_rep\">{$lang_char['reputation']}</a></li>";
  if( get_player_class($char[2]) == 'Hunter' ) { $output .= "  <li><a href=\"char.php?id=$id&amp;action=char_pets\">{$lang_char['pets']}</a></li>"; }
$output .= "</ul>
</div>

<div id=\"tab_content\">
 <img src=".get_image_dir($char[4],$char[5],$char[1],$char[2],0).">
 <br>
 <font class=\"bold\">$char[0] - ".get_player_race($char[1])." ".get_player_class($char[2])." (lvl {$char[4]})
 <br>
 <br>

<table class=\"lined\" style=\"width: 700px;\">
  <tr>
   <th>";
  if($equiped_bag_id[1]){
    $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equiped_bag_id[1][0])."\" alt=\"\" />", "$item_datasite{$equiped_bag_id[1][0]}", get_item_tooltip($equiped_bag_id[1][0]), "item_tooltip", "target=\"_blank\"");
    $output .= "{$lang_item['bag']} I<br /><font class=\"small\">({$equiped_bag_id[1][1]} {$lang_item['slots']})</font";
  }
$output .= "</th><th>";
  if($equiped_bag_id[2]){
    $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equiped_bag_id[2][0])."\" alt=\"\" />", "$item_datasite{$equiped_bag_id[2][0]}", get_item_tooltip($equiped_bag_id[2][0]), "item_tooltip", "target=\"_blank\"");
    $output .= "{$lang_item['bag']} II<br /><font class=\"small\">({$equiped_bag_id[2][1]} {$lang_item['slots']})</font>";
  }
$output .= "</th><th>";
  if($equiped_bag_id[3]){
      $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equiped_bag_id[3][0])."\" alt=\"\" />", "$item_datasite{$equiped_bag_id[3][0]}", get_item_tooltip($equiped_bag_id[3][0]), "item_tooltip", "target=\"_blank\"");
      $output .= "{$lang_item['bag']} III<br /><font class=\"small\">({$equiped_bag_id[3][1]} {$lang_item['slots']})</font>";
    }
$output .= "</th><th>";
  if($equiped_bag_id[4]){
      $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equiped_bag_id[4][0])."\" alt=\"\" />", "$item_datasite{$equiped_bag_id[4][0]}", get_item_tooltip($equiped_bag_id[4][0]), "item_tooltip", "target=\"_blank\"");
      $output .= "{$lang_item['bag']} IV<br /><font class=\"small\">({$equiped_bag_id[4][1]} {$lang_item['slots']})</font>";
    }
$output .= "</th>
  </tr>
  <tr>";

  for($t = 1; $t < count($bag); $t++){
    $output .= "<td class=\"bag\" valign=\"bottom\" align=\"center\">
    <div style=\"width:".(4*43)."px;height:".(ceil($equiped_bag_id[$t][1]/4)*41)."px;\">";

  $dsp = $equiped_bag_id[$t][1]%4;
  if ($dsp) $output .= "<div class=\"no_slot\" /></div>";
  foreach ($bag[$t] as $pos => $item){
    $output .= "<div style=\"left:".(($pos+$dsp)%4*42)."px;top:".(floor(($pos+$dsp)/4)*41)."px;\">";
    $output .= maketooltip("<img src=\"".get_icon($item[0])."\" alt=\"\" />".($item[1] ? ($item[1]+1) : ""), "$item_datasite{$item[0]}", get_item_tooltip($item[0]), "item_tooltip", "target=\"_blank\"");
    $item[2] = $item[2] == 1 ? '' : $item[2];
    $output .= "<div style=\"width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px\">$item[2]</div><div style=\"width:25px;margin:-21px 0px 0px 17px;font-size:14px\">$item[2]</div></div>";
  }
    $output .= "</td>";
  }

$output .= "</tr>
  <tr>
    <th colspan=\"2\" align=\"left\">
      <img class=\"bag_icon\" src=\"".get_icon(3960)."\" alt=\"\" align=\"absmiddle\" style=\"margin-left:100px;\" />
      <font style=\"margin-left:30px;\">{$lang_char['backpack']}</font>
    </th>
    <th colspan=\"2\">
      {$lang_char['bank_items']}
    </th>
  </tr>
  <tr>
    <td colspan=\"2\" class=\"bag\" align=\"center\" height=\"220px\">
    <div style=\"width:".(4*43)."px;height:".(ceil(16/4)*41)."px;\">";

    foreach ($bag[0] as $pos => $item){
      $output .= "<div style=\"left:".($pos%4*42)."px;top:".(floor($pos/4)*41)."px;\">";
      $output .= maketooltip("<img src=\"".get_icon($item[0])."\" alt=\"\" />".($item[1] ? ($item[1]+1) : ""), "$item_datasite{$item[0]}", get_item_tooltip($item[0]), "item_tooltip", "target=\"_blank\"");
      $item[2] = $item[2] == 1 ? '' : $item[2];
      $output .= "<div style=\"width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px\">$item[2]</div><div style=\"width:25px;margin:-21px 0px 0px 17px;font-size:14px\">$item[2]</div></div>";
    }

  $money_gold = (int)($char[3]/10000);
  $money_silver = (int)(($char[3]-$money_gold*10000)/100);
  $money_cooper = (int)($char[3]-$money_gold*10000-$money_silver*100);

$output .= "</div>
      <div style=\"text-align:right;width:168px;background-image:none;background-color:#393936;padding:2px;\">
        <b>
        $money_gold <img src=\"img/gold.gif\" alt=\"\" align=\"absmiddle\" />
        $money_silver <img src=\"img/silver.gif\" alt=\"\" align=\"absmiddle\" />
        $money_cooper <img src=\"img/copper.gif\" alt=\"\" align=\"absmiddle\" />
        </b>
      ";

$output .= "</div>
    </td>
    <td colspan=\"2\" class=\"bank\" align=\"center\">
    <div style=\"width:".(7*43)."px;height:".(ceil(24/7)*41)."px;\">";

    foreach ($bank[0] as $pos => $item){
      $output .= "<div style=\"left:".($pos%7*43)."px;top:".(floor($pos/7)*41)."px;\">";
      $output .= maketooltip("<img src=\"".get_icon($item[0])."\" class=\"inv_icon\" alt=\"\" />", "$item_datasite{$item[0]}", get_item_tooltip($item[0]), "item_tooltip", "target=\"_blank\"");
      $item[2] = $item[2] == 1 ? '' : $item[2];
      $output .= "<div style=\"width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px\">$item[2]</div><div style=\"width:25px;margin:-21px 0px 0px 17px;font-size:14px\">$item[2]</div></div>";
    }

$output .= "</div>
    </td>
  </tr>
  <tr>
    <th>";
  if($equip_bnk_bag_id[1]){
    $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equip_bnk_bag_id[1][0])."\" alt=\"\" />", "$item_datasite{$equip_bnk_bag_id[1][0]}", get_item_tooltip($equip_bnk_bag_id[1][0]), "item_tooltip", "target=\"_blank\"");
    $output .= "{$lang_item['bag']} I<br /><font class=\"small\">({$equip_bnk_bag_id[1][1]} {$lang_item['slots']})</font>";
  }
$output .= "</th><th>";
  if($equip_bnk_bag_id[2]){
    $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equip_bnk_bag_id[2][0])."\" alt=\"\" />", "$item_datasite{$equip_bnk_bag_id[2][0]}", get_item_tooltip($equip_bnk_bag_id[2][0]), "item_tooltip", "target=\"_blank\"");
    $output .= "{$lang_item['bag']} II<br /><font class=\"small\">({$equip_bnk_bag_id[2][1]} {$lang_item['slots']})</font>";
  }
$output .= "</th><th>";
  if($equip_bnk_bag_id[3]){
    $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equip_bnk_bag_id[3][0])."\" alt=\"\" />", "$item_datasite{$equip_bnk_bag_id[3][0]}", get_item_tooltip($equip_bnk_bag_id[3][0]), "item_tooltip", "target=\"_blank\"");
    $output .= "{$lang_item['bag']} III<br /><font class=\"small\">({$equip_bnk_bag_id[3][1]} {$lang_item['slots']})</font>";
  }
$output .= "</th><th>";
  if($equip_bnk_bag_id[4]){
    $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equip_bnk_bag_id[4][0])."\" alt=\"\" />", "$item_datasite{$equip_bnk_bag_id[4][0]}", get_item_tooltip($equip_bnk_bag_id[4][0]), "item_tooltip", "target=\"_blank\"");
    $output .= "{$lang_item['bag']} IV<br /><font class=\"small\">({$equip_bnk_bag_id[4][1]} {$lang_item['slots']})</font>";
  }
$output .= "</th>
  </tr>
  <tr>";

  for($t=1; $t < count($bank); $t++){
  if($t==5){
    $output .= "</tr>
    <tr>
      <th>";
    if($equip_bnk_bag_id[5]){
      $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equip_bnk_bag_id[5][0])."\" alt=\"\" />", "$item_datasite{$equip_bnk_bag_id[5][0]}", get_item_tooltip($equip_bnk_bag_id[5][0]), "item_tooltip", "target=\"_blank\"");
      $output .= "{$lang_item['bag']} V<br /><font class=\"small\">({$equip_bnk_bag_id[5][1]} {$lang_item['slots']})</font>";
    }
    $output .= "</th><th>";
    if($equip_bnk_bag_id[6]){
      $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equip_bnk_bag_id[6][0])."\" alt=\"\" />", "$item_datasite{$equip_bnk_bag_id[6][0]}", get_item_tooltip($equip_bnk_bag_id[6][0]), "item_tooltip", "target=\"_blank\"");
      $output .= "{$lang_item['bag']} VI<br /><font class=\"small\">({$equip_bnk_bag_id[6][1]} {$lang_item['slots']})</font>";
    }
    $output .= "</th><th>";
    if($equip_bnk_bag_id[7]){
      $output .= maketooltip("<img class=\"bag_icon\" src=\"".get_icon($equip_bnk_bag_id[7][0])."\" alt=\"\" />", "$item_datasite{$equip_bnk_bag_id[7][0]}", get_item_tooltip($equip_bnk_bag_id[7][0]), "item_tooltip", "target=\"_blank\"");
      $output .= "{$lang_item['bag']} VII<br /><font class=\"small\">({$equip_bnk_bag_id[7][1]} {$lang_item['slots']})</font>";
    }
    $output .= "</th>
      <th></th>
    </tr>
    <tr>";
  }

  $output .= "<td class=\"bank\" align=\"center\">
    <div style=\"width:".(4*43)."px;height:".(ceil($equip_bnk_bag_id[$t][1]/4)*41)."px;\">";

  $dsp=$equip_bnk_bag_id[$t][1]%4;
  if ($dsp) $output .= "<div class=\"no_slot\" /></div>";
  foreach ($bank[$t] as $pos => $item){
    $output .= "<div style=\"left:".(($pos+$dsp)%4*43)."px;top:".(floor(($pos+$dsp)/4)*41)."px;\">";
    $output .= maketooltip("<img src=\"".get_icon($item[0])."\" alt=\"\" />", "$item_datasite{$item[0]}", get_item_tooltip($item[0]), "item_tooltip", "target=\"_blank\"");
    $item[2] = $item[2] == 1 ? '' : $item[2];
    $output .= "<div style=\"width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px\">$item[2]</div><div style=\"width:25px;margin:-21px 0px 0px 17px;font-size:14px\">$item[2]</div></div>";
	}
  $output .= "</td>";
  }

$output .= "<td class=\"bank\"></td></tr>
    </table>
    </div><br />
    <table class=\"hidden\">
          <tr><td>";
    if ($user_lvl > $owner_gmlvl){
      makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$owner_acc_id",140);
      makebutton($lang_char['edit_button'], "char_edit.php?id=$id",140);
      }
    if (($user_lvl > 0)&&(($user_lvl > $owner_gmlvl)||($owner_name == $user_name))){
      makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id",140);
      makebutton($lang_char['send_mail'], "mail.php?type=ingame_mail&amp;to=$char[0]",140);
      }
    makebutton($lang_global['back'], "javascript:window.history.back()",140);
 $output .= "</td></tr>
        </table><br /></center>";

 } else {
    $sql->close();
    error($lang_char['no_permission']);
    }

} else error($lang_char['no_char_found']);
$sql->close();
}


//########################################################################################################################
// SHOW CHARACTERS QUESTS
//########################################################################################################################
function char_quest(){
 global $lang_global, $lang_char, $lang_item, $output, $realm_db, $characters_db, $realm_id, $user_lvl,$mangos_db,
    $user_name, $quest_datasite, $language;

 if (empty($_GET['id'])) error($lang_global['empty_fields']);

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $id = $sql->quote_smart($_GET['id']);
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : 1;
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 0;
 $dir = ($dir) ? 0 : 1;
 $result = $sql->query("SELECT account,name,race,class FROM `characters` WHERE guid = $id LIMIT 1");

 if ($sql->num_rows($result)){
  $char = $sql->fetch_row($result);

  $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $result = $sql->query("SELECT gmlevel,username FROM account WHERE id ='$char[0]'");
  $owner_gmlvl  = $sql->result($result, 0, 'gmlevel');
  $owner_name    = $sql->result($result, 0, 'username');

  if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name)){
    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $result = $sql->query("SELECT quest,status FROM character_queststatus WHERE guid =$id AND ( status = 3 OR status = 1 ) ORDER BY status DESC");
    $output .= "<center>
    <div id=\"tab\">
    <ul>
    <li><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_inv\">{$lang_char['inventory']}</a></li>
    <li id=\"selected\"><a href=\"char.php?id=$id&amp;action=char_quest\">{$lang_char['quests']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_skill\">{$lang_char['skills']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_talent\">{$lang_char['talents']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_rep\">{$lang_char['reputation']}</a></li>";
    if( get_player_class($char[3]) == 'Hunter' ) { $output .= "    <li><a href=\"char.php?id=$id&amp;action=char_pets\">{$lang_char['pets']}</a></li>"; }
    $output .= "</ul>
    </div>
    <div id=\"tab_content\">
    <font class=\"bold\">$char[1] - ".get_player_race($char[2])." ".get_player_class($char[3])."</font><br /><br />

    <table class=\"lined\" style=\"width: 550px;\">
    <tr>";
    if ($user_lvl) $output .= "<th width=\"10%\"><a href=\"char.php?id=$id&amp;action=char_quest&amp;order_by=0&amp;dir=$dir\">".($order_by==0 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_char['quest_id']}</a></th>";
    $output .= "<th width=\"7%\"><a href=\"char.php?id=$id&amp;action=char_quest&amp;order_by=1&amp;dir=$dir\">".($order_by==1 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_char['quest_level']}</a></th>
      <th width=\"78%\"><a href=\"char.php?id=$id&amp;action=char_quest&amp;order_by=2&amp;dir=$dir\">".($order_by==2 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_char['quest_title']}</a></th>
      <th width=\"5%\"><img src=\"img/aff_qst.png\" width=\"14\" height=\"14\" border=\"0\" /></a></th>";

    $quests_1 = array();
    $quests_3 = array();

    if ($sql->num_rows($result)){
    while ($quest = $sql->fetch_row($result)){
      $deplang = get_lang_id();
      $query1 = $sql->query("SELECT QuestLevel,IFNULL(".($deplang<>0?"title_loc$deplang":"NULL").",`title`) as Title FROM `".$mangos_db[$realm_id]['name']."`.`quest_template` LEFT JOIN `".$mangos_db[$realm_id]['name']."`.`locales_quest` ON `quest_template`.`entry` = `locales_quest`.`entry` WHERE `quest_template`.`entry` ='$quest[0]'");
      $quest_info = $sql->fetch_row($query1);
      if($quest[1]==1)
      array_push($quests_1, array($quest[0], $quest_info[0], $quest_info[1]));
      else
      array_push($quests_3, array($quest[0], $quest_info[0], $quest_info[1]));
      }
    aasort($quests_1, $order_by, $dir);
    aasort($quests_3, $order_by, $dir);

    foreach ($quests_3 as $data){
      $output .= "<tr>";
      if($user_lvl) $output .= "<td>$data[0]</td>";
      $output .= "<td>($data[1])</td>
      <td align=\"left\"><a href=\"$quest_datasite$data[0]\" target=\"_blank\">$data[2]</a></td>
      <td><img src=\"img/aff_qst.png\" width=\"14\" height=\"14\" /></td></tr>";
    }

    if(count($quests_1)) $output .= "<tr><th class=\"title\" colspan=\"".($user_lvl ? "4" : "3")."\" align=\"left\"></th></tr>";
    foreach ($quests_1 as $data){
      $output .= "<tr>";
      if($user_lvl) $output .= "<td>$data[0]</td>";
      $output .= "<td>($data[1])</td>
      <td align=\"left\"><a href=\"$quest_datasite$data[0]\" target=\"_blank\">$data[2]</a></td>
      <td><img src=\"img/aff_tick.png\" width=\"14\" height=\"14\" /></td></tr>";
    }

    } else $output .= "<tr><td colspan=\"".($user_lvl ? "4" : "3")."\"><p>{$lang_char['no_act_quests']}</p></td></tr>";

    $output .= "</table></div><br />
    <table class=\"hidden\">
          <tr><td>";
    if ($user_lvl > $owner_gmlvl)
    {
    makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$char[0]",140);
    makebutton($lang_char['edit_button'], "char_edit.php?id=$id",140);
    }
    if ( ($user_lvl) && (($user_lvl > $owner_gmlvl) || ($owner_name == $user_name)) )
    {
    makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id",140);
    makebutton($lang_char['send_mail'], "mail.php?type=ingame_mail&amp;to=$char[1]",140);
    }
    makebutton($lang_global['back'], "javascript:window.history.back()",140);
    $output .= "</td></tr>
        </table><br /></center>";
  }
  else
  {
    $sql->close();
    error($lang_char['no_permission']);
  }
 }
 else error($lang_char['no_char_found']);
 $sql->close();
}


//########################################################################################################################
// SHOW CHAR REPUTATION
//########################################################################################################################
function char_rep() {
 global $lang_global, $lang_char, $lang_item, $output, $realm_db, $characters_db, $realm_id, $user_lvl,$mangos_db,
    $user_name, $fact_id, $reputation_rank_length, $reputation_cap, $reputation_bottom, $reputation_rank, $MIN_REPUTATION_RANK, $MAX_REPUTATION_RANK;

if (empty($_GET['id'])) error($lang_global['empty_fields']);

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$id = $sql->quote_smart($_GET['id']);
$result = $sql->query("SELECT account,name,race,class FROM `characters` WHERE guid = $id LIMIT 1");

if ($sql->num_rows($result)){
  $char = $sql->fetch_row($result);
  $race = $char[2];
  $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $result = $sql->query("SELECT gmlevel,username FROM account WHERE id ='$char[0]'");
  $owner_gmlvl = $sql->result($result, 0, 'gmlevel');
  $owner_name = $sql->result($result, 0, 'username');

 if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name)){

  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
  $result = $sql->query("SELECT faction, standing, flags FROM character_reputation WHERE guid =$id AND (flags & 1 = 1)");

 $output .= "<center>
 <div id=\"tab\">
 <ul>
  <li><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_inv\">{$lang_char['inventory']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_quest\">{$lang_char['quests']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_skill\">{$lang_char['skills']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_talent\">{$lang_char['talents']}</a></li>
    <li id=\"selected\"><a href=\"char.php?id=$id&amp;action=char_rep\">{$lang_char['reputation']}</a></li>";
    if( get_player_class($char[3]) == 'Hunter' ) { $output .= "<li><a href=\"char.php?id=$id&amp;action=char_pets\">{$lang_char['pets']}</a></li>"; }
 $output .= " </ul>
 </div>
 <div id=\"tab_content\">
  <font class=\"bold\">$char[1] - ".get_player_race($char[2])." ".get_player_class($char[3])."</font><br /><br />";

 require_once("scripts/fact_tab.php");

 $temp_out = array(
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
    <tr><th colspan=\"3\" align=\"left\">Other</th></tr>",0),
  0 => array("<table class=\"lined\" style=\"width: 550px;\">
    <tr><th colspan=\"3\" align=\"left\">Unknown</th></tr>",0)
 );
 
  if ($sql->num_rows($result))
  {
    while ($fact = $sql->fetch_row($result))
    {
      $faction = $fact[0];
      $standing = $fact[1];      
      
      $rep_rank = get_reputation_rank($faction, $standing, $race);
      $rep_rank_name = $reputation_rank[$rep_rank];
      $rep_cap = $reputation_rank_length[$rep_rank];
      $rep = get_reputation_at_rank($faction, $standing, $race);
      $faction_name = get_faction_name($faction);
      
      $ft = get_faction_tree($faction);
      
      // not show alliance rep for horde and vice versa:
      if (!((((1 << ($race - 1)) & 690) && ($ft == 1 || $ft == 3)) || (((1 << ($race - 1)) & 1101) && ($ft == 2 || $ft == 4))))
      {
            $temp_out[$ft][0] .= "<tr><td width=\"30%\" align=\"left\">$faction_name</td>
                          <td width=\"55%\" valign=\"top\"><div class=\"faction-bar\"><div class=\"rep$rep_rank\"><span class=\"rep-data\">$rep/$rep_cap</span><div class=\"bar-color\" style=\"width:".(100*$rep/$rep_cap)."%\"></div></div></div></td>
                          <td width=\"15%\" align=\"left\" class=\"rep$rep_rank\">$rep_rank_name</td></tr>";                          
            $temp_out[$ft][1] = 1;
      }
    }
  } else $output .= "<tr><td colspan=\"2\"><br /><br />{$lang_global['err_no_records_found']}<br /><br /></td></tr>";

  foreach ($temp_out as $out) if ($out[1]) $output .= $out[0]."</table>";

  $output .= "</div><br />
    <table class=\"hidden\">
          <tr><td>";
    if ($user_lvl > $owner_gmlvl){
      makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$char[0]",140);
      makebutton($lang_char['edit_button'], "char_edit.php?id=$id",140);
      }
    if (($user_lvl > 0)&&(($user_lvl > $owner_gmlvl)||($owner_name == $user_name))){
      makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id",140);
      makebutton($lang_char['send_mail'], "mail.php?type=ingame_mail&amp;to=$char[1]",140);
      }
    makebutton($lang_global['back'], "javascript:window.history.back()",140);
  $output .= "</td></tr>
        </table><br /></center>";

 } else {
    $sql->close();
    error($lang_char['no_permission']);
    }

} else error($lang_char['no_char_found']);
$sql->close();

}


//########################################################################################################################
// SHOW CHARACTERS SKILLS
//########################################################################################################################
function char_skill() {
 global $lang_global, $lang_char, $lang_item, $output, $realm_db, $characters_db, $realm_id, $user_lvl,$mangos_db,
    $user_name, $skill_datasite;

if (empty($_GET['id'])) error($lang_global['empty_fields']);

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$id = $sql->quote_smart($_GET['id']);
$order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : 1;
$dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
$dir = ($dir) ? 0 : 1;

$result = $sql->query("SELECT account FROM `characters` WHERE guid = $id");

if ($sql->num_rows($result) == 1){
  $owner_acc_id = $sql->result($result, 0, 'account');
  $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $result = $sql->query("SELECT gmlevel,username FROM account WHERE id ='$owner_acc_id'");
  $owner_gmlvl = $sql->result($result, 0, 'gmlevel');
  $owner_name = $sql->result($result, 0, 'username');

 if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name)){

  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $result = $sql->query("SELECT data,name,race,class,CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level FROM `characters` WHERE guid = $id");
  $char = $sql->fetch_row($result);
  $char_data = explode(' ',$char[0]);

  $output .= "<center>
  <div id=\"tab\">
  <ul>
    <li><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_inv\">{$lang_char['inventory']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_quest\">{$lang_char['quests']}</a></li>
    <li id=\"selected\"><a href=\"char.php?id=$id&amp;action=char_skill\">{$lang_char['skills']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_talent\">{$lang_char['talents']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_rep\">{$lang_char['reputation']}</a></li>";
    if( get_player_class($char[3]) == 'Hunter' ) { $output .= "  <li><a href=\"char.php?id=$id&amp;action=char_pets\">{$lang_char['pets']}</a></li>"; }
  $output .= " </ul>
  </div>
  <div id=\"tab_content\">
  <font class=\"bold\">$char[1] - ".get_player_race($char[2])." ".get_player_class($char[3])."</font><br /><br />

  <table class=\"lined\" style=\"width: 600px;\">
  <tr>
    <th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['skills']}</th></tr>
  <tr>"
    .($user_lvl ? "<th><a href=\"char.php?id=$id&amp;action=char_skill&amp;order_by=0&amp;dir=$dir\">".($order_by==0 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_char['skill_id']}</a></th>" : "")
    ."<th align=\"right\"><a href=\"char.php?id=$id&amp;action=char_skill&amp;order_by=1&amp;dir=$dir\">".($order_by==1 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\"  /> " : "")."{$lang_char['skill_name']}</a></th>
    <th><a href=\"char.php?id=$id&amp;action=char_skill&amp;order_by=2&amp;dir=$dir\">".($order_by==2 ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_char['skill_value']}</a></th>
  </tr>";
  require_once("scripts/id_tab.php");

 $prof_1_array = array();
 $prof_2_array = array();
 $skill_array = array();

 $skill_rank_array = array(
  75 => $lang_char['apprentice'],
  150 => $lang_char['journeyman'],
  225 => $lang_char['expert'],
  300 => $lang_char['artisan'],
  350 => $lang_char['master'],
  375 => $lang_char['inherent'],
  385 => $lang_char['wise']
  );

 for ($i = CHAR_DATA_OFFSET_SKILL_DATA; $i <= CHAR_DATA_OFFSET_SKILL_DATA+384 ; $i+=3){
  if (($char_data[$i])&&(get_skill_name($char_data[$i] & 0x0000FFFF ))){
    $temp = unpack("S", pack("L", $char_data[$i+1]));
    $skill = ($char_data[$i] & 0x0000FFFF);

    if( $skill == 185 || $skill == 129 || $skill == 356 || $skill == 762)
    {
      array_push($prof_2_array , array(($user_lvl ? $skill : ''), get_skill_name($skill), $temp[1]));
    }
    else if( $skill == 171 || $skill == 182 || $skill == 186 ||
         $skill == 197 || $skill == 202 || $skill == 333 ||
         $skill == 393 || $skill == 755 || $skill == 164 ||
         $skill == 165)
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
 aasort($prof_1_array, $order_by, $dir);
 aasort($prof_2_array, $order_by, $dir);

 foreach ($skill_array as $data){
  $max = ($data[2] < $char[4]*5) ? $char[4]*5 : $data[2];
  $output .= "<tr>"
        .($user_lvl ? "<td>$data[0]</td>" : "")
        ."  <td align=\"right\">$data[1]</td>
          <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(385*$data[2]/$max)-385)."px;\">
            <span>$data[2]/$max</span>
          </td>
        </tr>";
  }

 if(count($prof_1_array)) $output .= "<tr><th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['professions']} 1º</th></tr>";
 foreach ($prof_1_array as $data){
  $max = ($data[2]<76 ? 75 : ($data[2]<151 ? 150 : ($data[2]<226 ? 225 : ($data[2]<301 ? 300 : ($data[2]<351 ? 350 : ($data[2]<376 ? 375 : 385))))));
  $output .= "<tr>"
        .($user_lvl ? "<td>$data[0]</td>" : "")
        ."  <td align=\"right\"><a href=\"{$skill_datasite}11.$data[0]\" target=\"_blank\">$data[1]</a></td>
          <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(385*$data[2]/$max)-385)."px;\">
            <span>$data[2]/$max ({$skill_rank_array[$max]})</span>
          </td>
        </tr>";
 }
 if(count($prof_2_array)) $output .= "<tr><th class=\"title\" colspan=\"".($user_lvl ? "3" : "2")."\" align=\"left\">{$lang_char['professions']} 2º</th></tr>";
 foreach ($prof_2_array as $data){
  $max = ($data[2]<76 ? 75 : ($data[2]<151 ? 150 : ($data[2]<226 ? 225 : ($data[2]<301 ? 300 : ($data[2]<351 ? 350 : ($data[2]<376 ? 375 : 385))))));
  $output .= "<tr>"
        .($user_lvl ? "<td>$data[0]</td>" : "")
        ."  <td align=\"right\"><a href=\"{$skill_datasite}9.$data[0]\" target=\"_blank\">$data[1]</a></td>
          <td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(385*$data[2]/$max)-385)."px;\">
            <span>$data[2]/$max ({$skill_rank_array[$max]})</span>
          </td>
        </tr>";
 }
  $output .= "</table></div><br />
    <table class=\"hidden\">
          <tr><td>";
    if ($user_lvl > $owner_gmlvl){
      makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$owner_acc_id",140);
      makebutton($lang_char['edit_button'], "char_edit.php?id=$id",140);
      }
    if (($user_lvl > 0)&&(($user_lvl > $owner_gmlvl)||($owner_name == $user_name))){
      makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id",140);
      makebutton($lang_char['send_mail'], "mail.php?type=ingame_mail&amp;to=$char[1]",140);
      }
    makebutton($lang_global['back'], "javascript:window.history.back()",140);
  $output .= "</td></tr>
        </table><br /></center>";

 } else {
    $sql->close();
    error($lang_char['no_permission']);
    }

} else error($lang_char['no_char_found']);
$sql->close();
}

//########################################################################################################################
// SHOW CHARACTER TALENTS
//########################################################################################################################
function char_talent() {
 global $lang_global, $lang_char, $lang_item, $output, $realm_db, $characters_db, $realm_id, $user_lvl,$mangos_db,
        $user_name, $talent_datasite, $talent_calculator_datasite;

if (empty($_GET['id'])) error($lang_global['empty_fields']);

//check for php gmp extension
if (extension_loaded('gmp')) { $GMP=1; }
else { $GMP=0; }
//end of gmp check

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$id = $sql->quote_smart($_GET['id']);
$order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : 1;
$dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
$dir = ($dir) ? 0 : 1;

$result = $sql->query("SELECT account FROM `characters` WHERE guid = $id");

if ($sql->num_rows($result) == 1){
    $owner_acc_id = $sql->result($result, 0, 'account');
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    $result = $sql->query("SELECT gmlevel,username FROM account WHERE id ='$owner_acc_id'");
    $owner_gmlvl = $sql->result($result, 0, 'gmlevel');
    $owner_name = $sql->result($result, 0, 'username');

 if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name)){

    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $result = $sql->query("SELECT data,name,race,class,CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level FROM `characters` WHERE guid = $id");
    $char = $sql->fetch_row($result);
    $char_data = explode(' ',$char[0]);

    $output .= "<center>
  <div id=\"tab\">
    <ul>
        <li><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>
        <li><a href=\"char.php?id=$id&amp;action=char_inv\">{$lang_char['inventory']}</a></li>
        <li><a href=\"char.php?id=$id&amp;action=char_quest\">{$lang_char['quests']}</a></li>
        <li><a href=\"char.php?id=$id&amp;action=char_skill\">{$lang_char['skills']}</a></li>
        <li id=\"selected\"><a href=\"char.php?id=$id&amp;action=char_talent\">{$lang_char['talents']}</a></li>
        <li><a href=\"char.php?id=$id&amp;action=char_rep\">{$lang_char['reputation']}</a></li>";
    if( get_player_class($char[3]) == 'Hunter' ) { $output .= "  <li><a href=\"char.php?id=$id&amp;action=char_pets\">{$lang_char['pets']}</a></li>"; }
    $output .= "  </ul>
    </div>
    <div id=\"tab_content\">
    <font class=\"bold\">$char[1] - ".get_player_race($char[2])." ".get_player_class($char[3])." <br /><br /> ".get_player_class($char[3])." Talents </font><br /><br />

    <table class=\"lined\" style=\"width: 550px;\">
    <tr>"
        ."<th>{$lang_char['talent_id']}</th>"
        ."<th align=left>{$lang_char['talent_name']}</th>
    </tr>";

    $result = $sql->query("SELECT spell FROM `character_spell` WHERE guid = $id AND active = 1");

    if ($sql->num_rows($result)){
        if ($GMP) { $talent_sum = gmp_init(0); }
        while ($talent = $sql->fetch_row($result)){
            if( get_talent_value($talent[0]) )
            {
                $output .= "<tr>";
                $output .= "<td>$talent[0]</td>";
         $output .= "<td align=left><a href=\"$talent_datasite$talent[0]\">".get_talent_name($talent[0])."</a></td>";
                if ($GMP) { $talent_sum = gmp_add($talent_sum,sprintf('%s',get_talent_value($talent[0]))); }
                $output .= "</tr>";
            }
        }
        $playerclass = strtolower(get_player_class($char[3]));
        switch ($playerclass) {
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
        if ($GMP) { $output .= "<tr><td><a href=\"$talent_calculator_datasite/$playerclass/talents.html?".str_pad(sprintf('%s',gmp_strval($talent_sum)), $padlength, "0", STR_PAD_LEFT)."\">Talent Calculator</a></td></tr>"; }

    }

    $output .= "</table></div><br />
        <table class=\"hidden\">
          <tr><td>";
        if ($user_lvl > $owner_gmlvl){
            makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$owner_acc_id",140);
            makebutton($lang_char['edit_button'], "char_edit.php?id=$id",140);
            }
        if (($user_lvl > 0)&&(($user_lvl > $owner_gmlvl)||($owner_name == $user_name))){
            makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id",140);
            makebutton($lang_char['send_mail'], "mail.php?type=ingame_mail&amp;to=$char[1]",140);
            }
        makebutton($lang_global['back'], "javascript:window.history.back()",140);
    $output .= "</td></tr>
        </table><br /></center>";

 } else {
        $sql->close();
        error($lang_char['no_permission']);
        }

} else error($lang_char['no_char_found']);
$sql->close();
}


//########################################################################################################################^M
// SHOW CHARACTER PETS
//########################################################################################################################^M
function char_pets() {
 global $lang_global, $lang_char, $lang_item, $output, $realm_db, $characters_db, $realm_id, $user_lvl,$mangos_db,
        $user_name, $pet_ability, $talent_datasite;

if (empty($_GET['id'])) error($lang_global['empty_fields']);

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$id = $sql->quote_smart($_GET['id']);

$result = $sql->query("SELECT account FROM `characters` WHERE guid = $id");

if ($sql->num_rows($result) == 1){
    $owner_acc_id = $sql->result($result, 0, 'account');
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    $result = $sql->query("SELECT gmlevel,username FROM account WHERE id ='$owner_acc_id'");
    $owner_gmlvl = $sql->result($result, 0, 'gmlevel');
    $owner_name = $sql->result($result, 0, 'username');

 if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name)){

    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $result = $sql->query("SELECT id,level,exp,loyaltypoints,loyalty,trainpoint,name,curhappiness,TeachSpelldata FROM `character_pet` WHERE owner = $id");

      $output .= "<center>
      <div id=\"tab\">
      <ul>
        <li><a href=\"char.php?id=$id\">{$lang_char['char_sheet']}</a></li>
        <li><a href=\"char.php?id=$id&amp;action=char_inv\">{$lang_char['inventory']}</a></li>
        <li><a href=\"char.php?id=$id&amp;action=char_quest\">{$lang_char['quests']}</a></li>
        <li><a href=\"char.php?id=$id&amp;action=char_skill\">{$lang_char['skills']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_talent\">{$lang_char['talents']}</a></li>
    <li><a href=\"char.php?id=$id&amp;action=char_rep\">{$lang_char['reputation']}</a></li>
        <li id=\"selected\"><a href=\"char.php?id=$id&amp;action=char_pet\">{$lang_char['pets']}</a></li>
      </ul>
      </div>
      <div id=\"tab_content\">";

  if ($sql->num_rows($result)){
  while($pet = $sql->fetch_row($result)){
        $happiness = floor($pet[7]/333000);

        switch ($happiness) {
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
    
    $pet_next_lvl_xp = floor(xp_to_level($pet[1])/4); 

      $output .= "  <font class=\"bold\">$pet[6] (lvl$pet[1])
    <a style=\"padding:2px;\" onmouseover=\"toolTip('<font color=\'white\'>$hap_text</font>','item_tooltip')\" onmouseout=\"toolTip()\"><img src=\"img/pet/happiness_$hap_val.jpg\"></a>
    <br /><br /></font>
    <table class=\"lined\" style=\"width: 550px;\">
    <tr><td align=right>Exp:</td><td valign=\"top\" class=\"bar skill_bar\" style=\"background-position: ".(round(385*$pet[2]/$pet_next_lvl_xp)-385)."px;\">
    <span>$pet[2]/$pet_next_lvl_xp</span>
    </td></tr>
    <tr><td align=right>Loyalty:</td><td align=left>Level $pet[4] ($pet[3] pts)</td></tr>
    <tr><td align=right>Training Points:</td><td align=left>$pet[5]</td></tr>";
    $output .= "<tr><td align=right>Pet Abilities:</td><td align=left>";
    $ability_results = $sql->query("SELECT spell FROM `pet_spell` WHERE guid = '$pet[0]'");
    if ($sql->num_rows($ability_results)){
        while ($ability = $sql->fetch_row($ability_results)){
              if( isset($pet_ability[$ability[0]]) )
              {
                  $output .= "<a style=\"padding:2px;\" onmouseover=\"toolTip('<font color=\'white\'>".get_pet_ability_name($ability[0])."<br />Training Points: ".get_pet_ability_trainvalue($ability[0])."<br />Id: $ability[0]</font>','item_tooltip')\" onmouseout=\"toolTip()\" href=\"$talent_datasite$ability[0]\"><img src=\"img/pet/".get_pet_ability_image($ability[0])."\"></a>";
              }
          }
    }
    $output .= "</td></tr>";
    $output .= "</table><br /><br />";
  }
  }
  
      $output .= "</div>
      <table class=\"hidden\">
          <tr><td>";
        if ($user_lvl > $owner_gmlvl){
            makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$owner_acc_id",140);
            makebutton($lang_char['edit_button'], "char_edit.php?id=$id",140);
            }
        if (($user_lvl > 0)&&(($user_lvl > $owner_gmlvl)||($owner_name == $user_name))){
            makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id",140);
            makebutton($lang_char['send_mail'], "mail.php?type=ingame_mail&amp;to=$char[1]",140);
            }
        makebutton($lang_global['back'], "javascript:window.history.back()",140);
    $output .= "</td></tr>
        </table><br /></center>";
 } else {
        $sql->close();
        error($lang_char['no_permission']);
        }
} else error($lang_char['no_char_found']);
$sql->close();
}

//########################################################################################################################
// MAIN
//########################################################################################################################
$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "char_main":
   char_main();
   break;
case "char_inv":
   char_inv();
   break;
case "char_quest":
   char_quest();
   break;
case "char_rep":
   char_rep();
   break;
case "char_skill":
   char_skill();
   break;
case "char_talent":
   char_talent();
   break;
case "char_pets":
   char_pets();
   break;
default:
    char_main();
}

require_once("footer.php");
?>