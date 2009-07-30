<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA (thanks to mirage666 for the original idea)
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

require_once ("pomm_lib.php");
require_once ("../js/ajax/Php.php");

$JsHttpRequest =& new JsHttpRequest($site_encoding);

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

if( !$user_lvl && !$server[$realm_id]['both_factions']){
	$result = $sql->query("SELECT race FROM `characters` WHERE account = '$user_id' AND totaltime = (SELECT MAX(totaltime) FROM `characters` WHERE account = '$user_id') LIMIT 1");
	if ($sql->num_rows($result)){
		$order_side = (in_array($sql->result($result, 0, 'race'),array(2,5,6,8,10))) ?
		" AND race IN (2,5,6,8,10) " : " AND race IN (1,3,4,7,11) ";
	} else $order_side = "";
} else $order_side = "";

$result = $sql->query("SELECT name,race,class,position_x,position_y,map,SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1),
		SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 37), ' ', -1),zone
		FROM `characters` WHERE `online`= 1 $order_side");

$i = 0;
while($char = $sql->fetch_row($result)){
	$char_gender = str_pad(dechex($char[7]),8, 0, STR_PAD_LEFT);

	$pos = get_player_position($char[3],$char[4],$char[5],$char[8]);
 	$arr[$i]['x'] = $pos['x'];
	$arr[$i]['y'] = $pos['y'];
	$arr[$i]['name'] = $char[0];
	if (($char[5] == 1)||($char[5] == 0)||($char[5] == 530)) $arr[$i]['zone'] = ereg_replace("'", "`", get_zone_name($char[8]));
		else $arr[$i]['zone'] = ereg_replace("'", "`", get_map_name($char[5]));
	$arr[$i]['cl'] = $char[2];
	$arr[$i]['race'] = $char[1];
	$arr[$i]['level']= $lang_index['level']." - ".$char[6];
	$arr[$i]['gender'] = $char_gender[3];
	$i++;
 	}

$sql->close();

$_RESULT =$arr;
?>
