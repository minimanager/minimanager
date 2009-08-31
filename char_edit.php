<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/item_lib.php';
require_once 'libs/map_zone_lib.php';
valid_login($action_permission['delete']);

//########################################################################################################################
//  PRINT  EDIT FORM
//########################################################################################################################
function edit_char() {
 global $lang_global, $lang_char, $lang_item, $output, $realm_db, $characters_db, $realm_id, $mmfpm_db, $action_permission, $user_lvl,
    $item_datasite;
  wowhead_tt();

valid_login($action_permission['delete']);
if (empty($_GET['id'])) error($lang_global['empty_fields']);

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

$id = $sql->quote_smart($_GET['id']);

$result = $sql->query("SELECT account FROM `characters` WHERE guid = '$id'");

if ($sql->num_rows($result)){
  //resrict by owner's gmlvl
  $owner_acc_id = $sql->result($result, 0, 'account');
  $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $query = $sql->query("SELECT gmlevel,username FROM account WHERE id ='$owner_acc_id'");
  $owner_gmlvl = $sql->result($query, 0, 'gmlevel');
  $owner_name = $sql->result($query, 0, 'username');
  $owner_check = $sql->result($query, 0, 'username');

 if ($user_lvl >= $owner_gmlvl){
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $result = $sql->query("SELECT guid,account,data,name,race,class,position_x,position_y,map,online,totaltime,position_z,zone, level, gender  FROM `characters` WHERE guid = '$id'");
  $char = $sql->fetch_row($result);
  $char_data = explode(' ',$char[2]);

  if($char[9]) $online = "<font class=\"error\">{$lang_char['online']}</font>{$lang_char['edit_offline_only_char']}";
    else $online = $lang_char['offline'];

  if($char_data[CHAR_DATA_OFFSET_GUILD_ID]){
    $query = $sql->query("SELECT name FROM guild WHERE guildid ='{$char_data[CHAR_DATA_OFFSET_GUILD_ID]}'");
    $guild_name = $sql->result($query, 0, 'name');
    if ($user_lvl > 0 ) $guild_name = "<a href=\"guild.php?action=view_guild&amp;error=3&amp;id={$char_data[CHAR_DATA_OFFSET_GUILD_ID]}\" >$guild_name</a>";
    if ($char_data[CHAR_DATA_OFFSET_GUILD_RANK]){
      $guild_rank_query = $sql->query("SELECT rname FROM guild_rank WHERE guildid ='{$char_data[CHAR_DATA_OFFSET_GUILD_ID]}' AND rid='{$char_data[CHAR_DATA_OFFSET_GUILD_RANK]}'");
      $guild_rank = $sql->result($guild_rank_query, 0, 'rname');
      } else $guild_rank = $lang_char['guild_leader'];
  } else {
    $guild_name = $lang_global['none'];
    $guild_rank = $lang_global['none'];
    }

  $block = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_BLOCK]));
  $block = round($block[1],4);
  $dodge = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_DODGE]));
  $dodge = round($dodge[1],4);
  $parry = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_PARRY]));
  $parry = round($parry[1],4);
  $crit = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_MELEE_CRIT]));
  $crit = round($crit[1],4);
  $range_crit = unpack("f", pack("L", $char_data[CHAR_DATA_OFFSET_RANGE_CRIT]));
  $range_crit = round($range_crit[1],4);

$output .= "<center>
 <form method=\"get\" action=\"char_edit.php\" name=\"form\">
  <input type=\"hidden\" name=\"action\" value=\"do_edit_char\" />
  <input type=\"hidden\" name=\"id\" value=\"$id\" />
  <table class=\"lined\">
  <tr>
    <td colspan=\"8\"><font class=\"bold\"><input type=\"text\" name=\"name\" size=\"14\" maxlength=\"12\" value=\"$char[3]\" /> - <img src='img/c_icons/{$char[4]}-{$char[14]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[4])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> <img src='img/c_icons/{$char[5]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[5])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> - lvl ".char_get_level_color($char[13])."</font><br />$online</td>
</tr>
<tr>
 <td colspan=\"8\">".get_map_name($char[9], $sqlm)." - ".get_zone_name($char[12], $sqlm)."</td>
</tr>
<tr>
 <td colspan=\"8\">{$lang_char['username']}: <input type=\"text\" name=\"owner_name\" size=\"20\" maxlength=\"25\" value=\"$owner_name\" /> | {$lang_char['acc_id']}: $owner_acc_id</td>
</tr>
<tr>
 <td colspan=\"8\">{$lang_char['guild']}: $guild_name | {$lang_char['rank']}: $guild_rank</td>
</tr>
<tr>
 <td colspan=\"8\">{$lang_char['honor_points']}: <input type=\"text\" name=\"honor_points\" size=\"8\" maxlength=\"6\" value=\"{$char_data[CHAR_DATA_OFFSET_HONOR_POINTS]}\" />/
 <input type=\"text\" name=\"arena_points\" size=\"8\" maxlength=\"6\" value=\"{$char_data[CHAR_DATA_OFFSET_ARENA_POINTS]}\" /> - {$lang_char['honor_kills']}: <input type=\"text\" name=\"total_kills\" size=\"8\" maxlength=\"6\" value=\"{$char_data[CHAR_DATA_OFFSET_HONOR_KILL]}\" /></td>
</tr>
  <tr>
    <td width=\"2%\"><input type=\"checkbox\" name=\"check[]\" value=\"a0\" /></td><td width=\"18%\">{$lang_item['head']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_HEAD]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_HEAD])."</a></td>
    <td width=\"15%\">{$lang_item['health']}:</td><td width=\"15%\"><input type=\"text\" name=\"health\" size=\"10\" maxlength=\"6\" value=\"{$char_data[CHAR_DATA_OFFSET_HEALTH]}\" /></td>
    <td width=\"15%\">{$lang_item['res_holy']}:</td><td width=\"15%\"><input type=\"text\" name=\"res_holy\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_RES_HOLY]}\" /></td>
    <td width=\"18%\">{$lang_item['gloves']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_GLOVES]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_GLOVES])."</a></td><td width=\"2%\"><input type=\"checkbox\" name=\"check[]\" value=\"a9\" /></td>
  </tr>
  <tr>
    <td><input type=\"checkbox\" name=\"check[]\" value=\"a1\" /></td><td>{$lang_item['neck']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_NECK]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_NECK])."</a></td>
    <td>{$lang_item['mana']}:</td><td><input type=\"text\" name=\"mana\" size=\"10\" maxlength=\"6\" value=\"{$char_data[CHAR_DATA_OFFSET_MANA]}\" /></td>
    <td>{$lang_item['res_arcane']}:</td><td><input type=\"text\" name=\"res_arcane\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_RES_ARCANE]}\" /></td>
    <td>{$lang_item['belt']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_BELT]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_BELT])."</a></td> <td><input type=\"checkbox\" name=\"check[]\" value=\"a5\" /></td>
  </tr>
  <tr>
    <td><input type=\"checkbox\" name=\"check[]\" value=\"a2\" /></td><td>{$lang_item['shoulder']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_SHOULDER]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_SHOULDER])."</a></td>
    <td>{$lang_item['strength']}:</td><td><input type=\"text\" name=\"str\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_STR]}\" /></td>
    <td>{$lang_item['res_fire']}:</td><td><input type=\"text\" name=\"res_fire\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_RES_FIRE]}\" /></td>
    <td>{$lang_item['legs']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_LEGS]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_LEGS])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a6\" /></td>
  </tr>
  <tr>
    <td><input type=\"checkbox\" name=\"check[]\" value=\"a14\" /></td><td>{$lang_item['back']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_BACK]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_BACK])."</a></td>
    <td>{$lang_item['agility']}:</td><td><input type=\"text\" name=\"agi\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_AGI]}\" /></td>
    <td>{$lang_item['res_nature']}:</td><td><input type=\"text\" name=\"res_nature\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_RES_NATURE]}\" /></td>
    <td>{$lang_item['feet']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_FEET]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_FEET])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a7\" /></td>
  </tr>
  <tr>
    <td><input type=\"checkbox\" name=\"check[]\" value=\"a4\" /></td><td>{$lang_item['chest']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_CHEST]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_CHEST])."</a></td>
    <td>{$lang_item['stamina']}:</td><td><input type=\"text\" name=\"sta\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_STA]}\" /></td>
    <td>{$lang_item['res_frost']}:</td><td><input type=\"text\" name=\"res_frost\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_RES_FROST]}\" /></td>
    <td>{$lang_item['finger']} 1<br /><a href=\"$item_datasite{$char_data[380]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_FINGER1])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a10\" /></td>
  </tr>
  <tr>
    <td><input type=\"checkbox\" name=\"check[]\" value=\"a3\" /></td><td>{$lang_item['shirt']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_SHIRT]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_SHIRT])."</a></td>
    <td>{$lang_item['intellect']}:</td><td><input type=\"text\" name=\"int\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_INT]}\" /></td>
    <td>{$lang_item['res_shadow']}:</td><td><input type=\"text\" name=\"res_shadow\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_RES_SHADOW]}\" /></td>
    <td>{$lang_item['finger']} 2<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_FINGER2]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_FINGER2])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a11\" /></td>
  </tr>
  <tr>
    <td><input type=\"checkbox\" name=\"check[]\" value=\"a18\" /></td><td>{$lang_item['tabard']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_TABARD]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_TABARD])."</a></td>
    <td>{$lang_item['spirit']}:</td><td><input type=\"text\" name=\"spi\" size=\"10\" maxlength=\"4\" value=\"{$char_data[CHAR_DATA_OFFSET_SPI]}\" /></td>
    <td>{$lang_char['exp']}:</td><td><input type=\"text\" name=\"exp\" size=\"10\" maxlength=\"8\" value=\"{$char_data[CHAR_DATA_OFFSET_EXP]}\" /></td>
    <td>{$lang_item['trinket']} 1<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_TRINKET1]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_TRINKET1])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a12\" /></td>
  </tr>
  <tr>
    <td><input type=\"checkbox\" name=\"check[]\" value=\"a8\" /></td><td>{$lang_item['wrist']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_WRIST]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_WRIST])."</a></td>
    <td>{$lang_item['armor']}:</td><td><input type=\"text\" name=\"armor\" size=\"10\" maxlength=\"6\" value=\"{$char_data[CHAR_DATA_OFFSET_ARMOR]}\" /></td>
    <td>{$lang_char['melee_ap']}: <input type=\"text\" name=\"attack_power\" size=\"10\" maxlength=\"6\" value=\"{$char_data[CHAR_DATA_OFFSET_AP]}\" /></td><td>{$lang_char['ranged_ap']}: <input type=\"text\" name=\"range_attack_power\" size=\"10\" maxlength=\"6\" value=\"{$char_data[CHAR_DATA_OFFSET_RANGED_AP]}\" /></td>
    <td>{$lang_item['trinket']} 2<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_TRINKET2]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_TRINKET2])."</a></td><td><input type=\"checkbox\" name=\"check[]\" value=\"a13\" /></td>
  </tr>
  <tr>
    <td><input type=\"checkbox\" name=\"check[]\" value=\"a15\" /></td>
    <td colspan=\"2\">{$lang_item['main_hand']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_MAIN_HAND]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_MAIN_HAND])."</a></td>
    <td colspan=\"2\"><input type=\"checkbox\" name=\"check[]\" value=\"a16\" />&nbsp;{$lang_item['off_hand']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_OFF_HAND]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_OFF_HAND])."</a></td>
    <td colspan=\"2\">{$lang_item['ranged']}<br /><a href=\"$item_datasite{$char_data[CHAR_DATA_OFFSET_EQU_RANGED]}\" target=\"_blank\">".get_item_name($char_data[CHAR_DATA_OFFSET_EQU_RANGED])."</a></td>
    <td><input type=\"checkbox\" name=\"check[]\" value=\"a17\" /></td>
<tr>
<td colspan=\"8\">{$lang_char['block']} : <input type=\"text\" name=\"block\" size=\"5\" maxlength=\"3\" value=\"$block\" />%
| {$lang_char['dodge']}: <input type=\"text\" name=\"dodge\" size=\"5\" maxlength=\"3\" value=\"$dodge\" />%
| {$lang_char['parry']}: <input type=\"text\" name=\"parry\" size=\"5\" maxlength=\"3\" value=\"$parry\" />%
| {$lang_char['melee_crit']}: <input type=\"text\" name=\"crit\" size=\"5\" maxlength=\"3\" value=\"$crit\" />%
| {$lang_char['ranged_crit']}: <input type=\"text\" name=\"range_crit\" size=\"3\" maxlength=\"14\" value=\"$range_crit\" />%</td>
 </tr>
 <tr>
<td colspan=\"4\">{$lang_char['gold']}: <input type=\"text\" name=\"money\" size=\"10\" maxlength=\"8\" value=\"{$char_data[CHAR_DATA_OFFSET_GOLD]}\" /></td>
  <td colspan=\"4\">{$lang_char['tot_paly_time']}: <input type=\"text\" name=\"tot_time\" size=\"8\" maxlength=\"14\" value=\"{$char[10]}\" /></td>
</tr>
<tr>
  <td colspan=\"5\">{$lang_char['location']}:
  X:<input type=\"text\" name=\"x\" size=\"10\" maxlength=\"8\" value=\"{$char[6]}\" />
  Y:<input type=\"text\" name=\"y\" size=\"8\" maxlength=\"16\" value=\"{$char[7]}\" />
  Z:<input type=\"text\" name=\"z\" size=\"8\" maxlength=\"16\" value=\"{$char[11]}\" />
  Map:<input type=\"text\" name=\"map\" size=\"8\" maxlength=\"16\" value=\"{$char[8]}\" />
  </td>
  <td colspan=\"3\">{$lang_char['move_to']}:<input type=\"text\" name=\"tp_to\" size=\"24\" maxlength=\"64\" value=\"\" /></td>
</tr>

</table><br />";

//inventory+bank items
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
  $query2 = $sql->query("SELECT bag,slot,item,item_template FROM character_inventory WHERE guid = '$id' ORDER BY bag,slot");

  $inv = array();
  $count = 0;

  while ($slot = $sql->fetch_row($query2)){
    if ($slot[0] == 0) {
      if($slot[1] >= 23 && $slot[1] <= 62) {
        $count++;
        $inv[$count][0] = $slot[3];
        $inv[$count][1] = $slot[2];
        }
      } else {
        $count++;
        $inv[$count][0] = $slot[3];
        $inv[$count][1] = $slot[2];
        }
    }

$output .= "<table class=\"lined\">
  <tr><td>{$lang_char['inv_bank']}</td></tr>
  <tr><td height=\"100\" align=\"center\">
  <table><tr align=\"center\">";
  $j = 0;
   for ($i=1; $i<=$count; $i++){
     $j++;
     $output .= "<td><a href=\"$item_datasite{$inv[$i][0]}\" target=\"_blank\">{$inv[$i][0]}</a><br /><input type=\"checkbox\" name=\"check[]\" value=\"{$inv[$i][1]}\" /></td>";
   if ($j == 15) {
    $output .= "</tr><tr align=\"center\">";
    $j = 0;
    }
     }
 $output .= "</tr></table></td></tr></table>
      <br />
      <table class=\"hidden\">
      <tr><td>";
        makebutton($lang_char['update'], "javascript:do_submit()",190);
        makebutton($lang_char['to_char_view'], "char.php?id=$id",160);
        makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id",160);
        makebutton($lang_global['back'], "javascript:window.history.back()",160);
 $output .= "</td></tr>
        </table><br />
    </form></center>";


 //case of non auth request
 } else {
    $sql->close();
    unset($sql);
    error($lang_char['no_permission']);
    exit();
    }

} else error($lang_char['no_char_found']);

}


//########################################################################################################################
//  DO EDIT CHARACTER
//########################################################################################################################
function do_edit_char() {
 global $lang_global, $lang_char, $output, $realm_db,
    $characters_db, $realm_id, $action_permission, $user_lvl, $world_db;
valid_login($action_permission['delete']);
if ( empty($_GET['id']) || empty($_GET['name']) ) error($lang_global['empty_fields']);

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$id = $sql->quote_smart($_GET['id']);

$result = $sql->query("SELECT account,online FROM `characters` WHERE guid = '$id'");

if ($sql->num_rows($result)){
//we cannot edit online chars
 if(!$sql->result($result, 0, 'online')){
  //resrict by owner's gmlvl
  $owner_acc_id = $sql->result($result, 0, 'account');
  $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $query = $sql->query("SELECT gmlevel FROM account WHERE id ='$owner_acc_id'");
  $owner_gmlvl = $sql->result($query, 0, 'gmlevel');
  $new_owner_name = $_GET['owner_name'];
  $query = $sql->query("SELECT id FROM account WHERE username ='$new_owner_name'");
  $new_owner_acc_id = $sql->result($query, 0, 'id');
    if ($owner_acc_id != $new_owner_acc_id)  {
    $max_players = $sql->query("SELECT numchars FROM realmcharacters WHERE acctid ='$new_owner_acc_id'");
    $max_players = $max_players[0];
      if($max_players <= 9)
    $result = $sql->query("UPDATE `{$characters_db[$realm_id]['name']}`.`characters` SET account = $new_owner_acc_id WHERE guid = '$id'");
      else redirect("char_edit.php?action=edit_char&id=$id&error=5");
  }
  if ($user_lvl > $owner_gmlvl){

  if(isset($_GET['check'])) $check = $sql->quote_smart($_GET['check']);
    else $check = NULL;

  $new_name = $sql->quote_smart($_GET['name']);

  if (isset($_GET['tot_time'])) $new_tot_time = $sql->quote_smart($_GET['tot_time']);
    else $new_tot_time =  0;
  if (isset($_GET['res_holy'])) $new_res_holy = $sql->quote_smart($_GET['res_holy']);
    else $new_res_holy =  0;
  if (isset($_GET['res_arcane'])) $new_res_arcane = $sql->quote_smart($_GET['res_arcane']);
    else $new_res_arcane =  0;
  if (isset($_GET['res_fire'])) $new_res_fire = $sql->quote_smart($_GET['res_fire']);
    else $new_res_fire =  0;
  if (isset($_GET['res_nature'])) $new_res_nature = $sql->quote_smart($_GET['res_nature']);
    else $new_res_nature =  0;
  if (isset($_GET['res_frost'])) $new_res_frost = $sql->quote_smart($_GET['res_frost']);
    else $new_res_frost =  0;
  if (isset($_GET['res_shadow'])) $new_res_shadow = $sql->quote_smart($_GET['res_shadow']);
    else $new_res_shadow =  0;
  if (isset($_GET['attack_power'])) $new_attack_power = $sql->quote_smart($_GET['attack_power']);
    else $new_attack_power =  0;
  if (isset($_GET['range_attack_power'])) $new_range_attack_power = $sql->quote_smart($_GET['range_attack_power']);
    else $new_range_attack_power =  0;
  if (isset($_GET['money'])) $new_money = $sql->quote_smart($_GET['money']);
    else $new_money =  0;
  if (isset($_GET['arena_points'])) $new_arena_points = $sql->quote_smart($_GET['arena_points']);
    else $new_arena_points =  0;
  if (isset($_GET['honor_points'])) $new_honor_points = $sql->quote_smart($_GET['honor_points']);
    else $new_honor_points =  0;
  if (isset($_GET['total_kills'])) $new_total_kills = $sql->quote_smart($_GET['total_kills']);
    else $new_total_kills =  0;

  if ((!is_numeric($new_tot_time))||(!is_numeric($new_res_holy))||(!is_numeric($new_res_arcane))||(!is_numeric($new_res_fire))
  ||(!is_numeric($new_res_nature))||(!is_numeric($new_res_frost))||(!is_numeric($new_res_shadow))||(!is_numeric($new_attack_power))
  ||(!is_numeric($new_range_attack_power))||(!is_numeric($new_money))||(!is_numeric($new_arena_points))||(!is_numeric($new_honor_points)))
    error($lang_char['use_numeric']);

  if (isset($_GET['health'])) $new_health = $sql->quote_smart($_GET['health']);
    else $new_health =  1;
  if (isset($_GET['mana'])) $new_mana = $sql->quote_smart($_GET['mana']);
    else $new_mana =  0;
  if (isset($_GET['str'])) $new_str = $sql->quote_smart($_GET['str']);
    else $new_str =  1;
  if (isset($_GET['agi'])) $new_agi = $sql->quote_smart($_GET['agi']);
    else $new_agi =  1;
  if (isset($_GET['sta'])) $new_sta = $sql->quote_smart($_GET['sta']);
    else $new_sta =  1;
  if (isset($_GET['int'])) $new_int = $sql->quote_smart($_GET['int']);
    else $new_int =  1;
  if (isset($_GET['spi'])) $new_spi = $sql->quote_smart($_GET['spi']);
    else $new_spi =  1;
  if (isset($_GET['exp'])) $new_exp = $sql->quote_smart($_GET['exp']);
    else $new_exp =  0;
  if (isset($_GET['armor'])) $new_armor = $sql->quote_smart($_GET['armor']);
    else $new_armor =  0;
  if (isset($_GET['block'])) $new_block = $sql->quote_smart($_GET['block']);
    else $new_block =  0;
  if (isset($_GET['dodge'])) $new_dodge = $sql->quote_smart($_GET['dodge']);
    else $new_dodge =  0;
  if (isset($_GET['parry'])) $new_parry = $sql->quote_smart($_GET['parry']);
    else $new_parry =  0;
  if (isset($_GET['crit'])) $new_crit = $sql->quote_smart($_GET['crit']);
    else $new_crit =  0;
  if (isset($_GET['range_crit'])) $new_range_crit = $sql->quote_smart($_GET['range_crit']);
    else $new_range_crit =  0;

  if ((!is_numeric($new_health))||(!is_numeric($new_mana))||(!is_numeric($new_str))||(!is_numeric($new_agi))
  ||(!is_numeric($new_sta))||(!is_numeric($new_int))||(!is_numeric($new_spi))||(!is_numeric($new_exp))
  ||(!is_numeric($new_armor))||(!is_numeric($new_block))||(!is_numeric($new_dodge))||(!is_numeric($new_parry))
  ||(!is_numeric($new_crit))||(!is_numeric($new_range_crit))) error($lang_char['use_numeric']);

  $x = (isset($_GET['x'])) ? $sql->quote_smart($_GET['x']) : 0;
  $y = (isset($_GET['y'])) ? $sql->quote_smart($_GET['y']) : 0;
  $z = (isset($_GET['z'])) ? $sql->quote_smart($_GET['z']) : 0;
  $map = (isset($_GET['map'])) ? $sql->quote_smart($_GET['map']) : 0;
  $tp_to = (isset($_GET['tp_to'])) ? $sql->quote_smart($_GET['tp_to']) : 0;

  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $result = $sql->query("SELECT data FROM `characters` WHERE guid = '$id'");
  $char = $sql->fetch_row($result);
  $char_data = explode(' ',$char[0]);

  $char_data[CHAR_DATA_OFFSET_AP] = $new_attack_power;
  $char_data[CHAR_DATA_OFFSET_RANGED_AP] = $new_range_attack_power;
  $char_data[CHAR_DATA_OFFSET_EXP] = $new_exp;
  $char_data[CHAR_DATA_OFFSET_GOLD] = $new_money;
  $char_data[CHAR_DATA_OFFSET_ARENA_POINTS] = $new_arena_points;
  $char_data[CHAR_DATA_OFFSET_HONOR_POINTS] = $new_honor_points;
  $char_data[CHAR_DATA_OFFSET_HONOR_KILL] = $new_total_kills;
  $char_data[CHAR_DATA_OFFSET_HEALTH] = $new_health;
  $char_data[CHAR_DATA_OFFSET_MANA] = $new_mana;
  $char_data[CHAR_DATA_OFFSET_STR] = $new_str;
  $char_data[CHAR_DATA_OFFSET_AGI] = $new_agi;
  $char_data[CHAR_DATA_OFFSET_STA] = $new_sta;
  $char_data[CHAR_DATA_OFFSET_INT] = $new_int;
  $char_data[CHAR_DATA_OFFSET_SPI] = $new_spi;
  $char_data[CHAR_DATA_OFFSET_ARMOR] = $new_armor;
  $char_data[CHAR_DATA_OFFSET_RES_HOLY] = $new_res_holy;
  $char_data[CHAR_DATA_OFFSET_RES_ARCANE] = $new_res_arcane;
  $char_data[CHAR_DATA_OFFSET_RES_FIRE] = $new_res_fire;
  $char_data[CHAR_DATA_OFFSET_RES_NATURE] = $new_res_nature;
  $char_data[CHAR_DATA_OFFSET_RES_FROST] = $new_res_frost;
  $char_data[CHAR_DATA_OFFSET_RES_SHADOW] = $new_res_shadow;

  $new_block = unpack("L",pack("f", $new_block));
  $char_data[CHAR_DATA_OFFSET_BLOCK] = $new_block[1];
  $new_dodge = unpack("L",pack("f", $new_dodge));
  $char_data[CHAR_DATA_OFFSET_DODGE] = $new_dodge[1];
  $new_parry = unpack("L",pack("f", $new_parry));
  $char_data[CHAR_DATA_OFFSET_PARRY] = $new_parry[1];
  $new_crit = unpack("L",pack("f", $new_crit));
  $char_data[CHAR_DATA_OFFSET_MELEE_CRIT] = $new_crit[1];
  $new_range_crit = unpack("L",pack("f", $new_range_crit));
  $char_data[CHAR_DATA_OFFSET_RANGE_CRIT] = $new_range_crit[1];

  //some items need to be deleted
  if($check){
   $item_offset = array(
    "a0" => CHAR_DATA_OFFSET_EQU_HEAD,
      "a1" => CHAR_DATA_OFFSET_EQU_NECK,
      "a2" => CHAR_DATA_OFFSET_EQU_SHOULDER,
      "a3" => CHAR_DATA_OFFSET_EQU_SHIRT,
      "a4" => CHAR_DATA_OFFSET_EQU_CHEST,
      "a5" => CHAR_DATA_OFFSET_EQU_BELT,
      "a6" => CHAR_DATA_OFFSET_EQU_LEGS,
      "a7" => CHAR_DATA_OFFSET_EQU_FEET,
      "a8" => CHAR_DATA_OFFSET_EQU_WRIST,
      "a9" => CHAR_DATA_OFFSET_EQU_GLOVES,
      "a10" => CHAR_DATA_OFFSET_EQU_FINGER1,
      "a11" => CHAR_DATA_OFFSET_EQU_FINGER2,
      "a12" => CHAR_DATA_OFFSET_EQU_TRINKET1,
      "a13" => CHAR_DATA_OFFSET_EQU_TRINKET2,
      "a14" => CHAR_DATA_OFFSET_EQU_BACK,
      "a15" => CHAR_DATA_OFFSET_EQU_MAIN_HAND,
      "a16" => CHAR_DATA_OFFSET_EQU_OFF_HAND,
      "a17" => CHAR_DATA_OFFSET_EQU_RANGED,
      "a18" => CHAR_DATA_OFFSET_EQU_TABARD
    );

     foreach ($check as $item_num) {
        //deleting equiped items
        if ($item_num[0] == "a"){
          $char_data[$item_offset[$item_num]] = 0;

          sscanf($item_num, "a%d",$item_num);
          $result = $sql->query("SELECT item FROM character_inventory WHERE guid = '$id' AND slot = $item_num AND bag = 0");
          $item_inst_id = $sql->result($result, 0, 'item');

          $sql->query("DELETE FROM character_inventory WHERE guid = '$id' AND slot = $item_num AND bag = 0");
          $sql->query("DELETE FROM item_instance WHERE guid = '$item_inst_id' AND owner_guid = '$id'");
          $sql->query("DELETE FROM item_text WHERE id = '$item_inst_id'");
          } else { //deleting inv/bank items
              $sql->query("DELETE FROM character_inventory WHERE guid = '$id' AND item = '$item_num'");
              $sql->query("DELETE FROM item_instance WHERE guid = '$item_num' AND owner_guid = '$id'");
              $sql->query("DELETE FROM item_text WHERE id = '$item_num'");
          }
      }
    }

  $data = implode(" ",$char_data);

  if ($tp_to){
    $query = $sql->query("SELECT map, position_x, position_y, position_z, orientation FROM `".$world_db[$realm_id]['name']."`.`game_tele` WHERE LOWER(name) = '".strtolower($tp_to)."'");
    $tele = $sql->fetch_row($query);
    if($tele) $teleport = "map='$tele[0]', position_x='$tele[1]', position_y='$tele[2]', position_z='$tele[3]', orientation='$tele[4]',";
      else error($lang_char['no_tp_location']);
  } else $teleport = "map='$map', position_x='$x', position_y='$y', position_z='$z',";

  $result = $sql->query("UPDATE `characters` SET data = '$data', name = '$new_name', $teleport totaltime = '$new_tot_time' WHERE guid = '$id'");
  $sql->close();
  unset($sql);

  if ($result) redirect("char_edit.php?action=edit_char&id=$id&error=3");
    else redirect("char_edit.php?action=edit_char&id=$id&error=4");
  } else {
    $sql->close();
    unset($sql);
    error($lang_char['no_permission']);
    }
 } else {
    $sql->close();
    unset($sql);
    redirect("char_edit.php?action=edit_char&id=$id&error=2");
    }
} else error($lang_char['no_char_found']);
$sql->close();
unset($sql);
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$lang_char = lang_char();

$output .= "<div class=\"top\">";
switch ($err) {
case 1:
   $output .= "<h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
   break;
case 2:
   $output .= "<h1><font class=\"error\">{$lang_char['err_edit_online_char']}</font></h1>";
   break;
case 3:
   $output .= "<h1><font class=\"error\">{$lang_char['updated']}</font></h1>";
   break;
case 4:
   $output .= "<h1><font class=\"error\">{$lang_char['update_err']}</font></h1>";
   break;
case 5:
   $output .= "<h1><font class=\"error\">{$lang_char['max_acc']}</font></h1>";
   break;
default: //no error
    $output .= "<h1>{$lang_char['edit_char']}</h1><br />{$lang_char['check_to_delete']}";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "edit_char":
   edit_char();
   break;
case "do_edit_char":
   do_edit_char();
   break;
default:
    edit_char();
}

unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");
?>
