<?php
/* 
	POMM  v1.3
	Player Online Map for MangOs

	Show online players position on map. Update without refresh.
	Show tooltip with location, race, class and level of player.
	Show realm status.

	16.09.2006		http://pomm.da.ru/
	
	Created by mirage666 (c) (mailto:mirage666@pisem.net icq# 152263154)
	2006-2009 Modified by killdozer.
*/

require_once "pomm_conf.php";
require_once "lang_".$lang.".php";

$mangos_db = new DBLayer($host, $user, $password, $db);
$mangos_db->query("SET NAMES $database_encoding");

$Horde_races = 0x2B2;
$Alliance_races = 0x44D;
$outland_inst   = array(540,542,543,544,545,546,547,548,550,552,553,554,555,556,557,558,562,564,565);
$northrend_inst = array(533,574,575,576,578,599,600,601,602,604,608,615,616,619,624);

require_once "JsHttpRequest/Php.php";
$JsHttpRequest = new Subsys_JsHttpRequest_Php("utf-8");

if(test_realm()) {
	$groups = array();
	$query = $mangos_db->query("SELECT `leaderGuid`,`memberGuid` FROM `group_member` WHERE `memberGuid` IN(SELECT `guid` FROM `characters` WHERE `online`='1')");
	if($query)
		while($result = $mangos_db->fetch_assoc($query))
			$groups[$result['memberGuid']] = $result['leaderGuid'];

	$Count = array();
	for($i = 0; $i < $maps_count; $i++) {
		$Count[$i] = array(0,0);
		}
	$arr = array();
	$i=$maps_count;
	$query = $mangos_db->query("SELECT `data`,`name`,`class`,`race`,`position_x`,`position_y`,`map`,`zone`,`extra_flags` FROM `characters` WHERE `online`='1' ORDER BY `name`");
	while($result = $mangos_db->fetch_assoc($query)) {
		if($result['map'] == 530 && $result['position_y'] > -1000 || in_array($result['map'], $outland_inst))
			$Extention = 1;
		else if($result['map'] == 571 || in_array($result['map'], $northrend_inst))
			$Extention = 2;
		else
			$Extention = 0;
		if($Horde_races & (0x1 << ($result['race']-1)))
			$Count[$Extention][1]++;
		else if($Alliance_races & (0x1 << ($result['race']-1)))
			$Count[$Extention][0]++;

		if($result['extra_flags'] & 0x1) {
			if($show_gm_online == 0)
				continue;
			if($add_gm_suffix)
				$result['name'] = $result['name'].' <small style="color: #EABA28;">{GM}</small>';
			}
		$char_data = explode(' ',$result['data']);
		$char_flags = $char_data[$PLAYER_FLAGS];
		$char_dead = ($char_flags & 0x11)?1:0;
		$char_gender = dechex($char_data[$UNIT_FIELD_BYTES_0]);
		$char_gender = str_pad($char_gender,8, 0, STR_PAD_LEFT);
		$arr[$i]['x'] = $result['position_x'];
		$arr[$i]['y'] = $result['position_y'];
		$arr[$i]['dead'] = $char_dead;
		$arr[$i]['name']=$result['name'];
		$arr[$i]['map']=$result['map'];
		$arr[$i]['zone']=get_zone_name($result['zone']);
		$arr[$i]['cl'] = $result['class'];
		$arr[$i]['race'] = $result['race'];
		$arr[$i]['level']=$char_data[$UNIT_FIELD_LEVEL];
		$arr[$i]['gender'] = $char_gender{3};
		$arr[$i]['Extention'] = $Extention;
		$arr[$i]['leaderGuid'] = isset($groups[$char_data[0]]) ? $groups[$char_data[0]] : 0;
		$i++;
		}

	$mangos_db->close();
	usort($arr, "sort_players");
	$arr = array_merge($Count, $arr);
	$res['online'] = $arr;
	}
else
	$res['online'] = NULL;

if($show_status) {
	$mangos_db = new DBLayer($hostw, $userw, $passwordw, $dbw);
	$mangos_db->query("SET NAMES $database_encoding");
	$query = $mangos_db->query("SELECT `starttime`,`maxplayers` FROM `uptime` WHERE `starttime`=(SELECT MAX(`starttime`) FROM `uptime`)");
	if($result = $mangos_db->fetch_assoc($query)) {
		$status['uptime'] = time() - $result['starttime'];
		$status['maxplayers'] = $result['maxplayers'];
		$status['online'] = test_realm() ? 1 : 0;
		}
	$mangos_db->close();
	}
else
	$status = NULL;
unset($mangos_db);

$res['status'] = $status;
$_RESULT = $res;
?>
