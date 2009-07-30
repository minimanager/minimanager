<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA (edted by thorazi to support multi-language)
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

$gm_level_arr = Array(
	0 => array(0,"Player",""),
	1 => array(1,"Moderator","Mod"),
	2 => array(2,"Gamemaster","GM"),
	3 => array(3,"BugTracker","BT"),
	4 => array(4,"Admin","Admin"),
	5 => array(5,"SysOp","SysOp")
);

$exp_lvl_arr = Array(
	0 => array(0,"Classic",""),
	1 => array(1,"The Burning Crusade","TBC"),
	2 => array(2,"Wrath of the Lich King","WotLK")
);

function get_gm_level($id){
 global $lang_id_tab, $gm_level_arr;
	if(isset($gm_level_arr[$id])) return $gm_level_arr[$id][1];
		else return($lang_id_tab['unknown']);
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get map name by its id
function get_map_name($id){
 global $mmfpm_db;
        $sql = new SQL;
        $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
		$map_name = $sql->fetch_row($sql->query("SELECT `name01` FROM `map` WHERE `id`={$id}"));

 $sql->close();
 		return $map_name[0];
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get zone name by its id
function get_zone_name($id){
 global $mmfpm_db;
        $sql = new SQL;
        $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
		$zone_name = $sql->fetch_row($sql->query("SELECT `field_3` FROM `worldmaparea` WHERE `field_2`={$id}"));

 $sql->close();
 return $zone_name[0];
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get player class by its id
function get_player_class($id){
 global $mmfpm_db;
        $sql = new SQL;
        $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
		$class_name = $sql->fetch_row($sql->query("SELECT `field_4` FROM `chrclasses` WHERE `id`={$id}"));

 $sql->close();
 return $class_name[0];
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get player race by its id
function get_player_race($id){
 global $mmfpm_db;
        $sql = new SQL;
        $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
		$race_name = $sql->fetch_row($sql->query("SELECT `field_12` FROM `chrraces` WHERE `id`={$id}"));

 $sql->close();
 return $race_name[0];
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get skill name by its id
function get_skill_name($id){
 global $mmfpm_db;
        $sql = new SQL;
        $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
		$skill_name = $sql->fetch_row($sql->query("SELECT `field_3` FROM `skillline` WHERE `id`={$id}"));

 $sql->close();
 return $skill_name[0];
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get achievement name by its id
function get_achievement_name($id){
 global $mmfpm_db;
        $sql = new SQL;
        $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
		$achievement_name = $sql->fetch_row($sql->query("SELECT `name01` FROM `achievement` WHERE `id`={$id}"));

 $sql->close();
 return $achievement_name[0];
}

$user_level = array(
	0 => $lang_id_tab['Player'],
	1 => $lang_id_tab['Moderator'],
	2 => $lang_id_tab['Game_Master'],
	3 => $lang_id_tab['BugTracker'],
	4 => $lang_id_tab['Administrator'],
	5 => $lang_id_tab['SysOP']
);

function get_player_user_level($id){
global $user_level;
 return $user_level[$id] ;
}

$CHAR_FACTION = array(
	0 => $lang_id_tab['Alliance'],
	1 => $lang_id_tab['Horde']
);

$CHAR_RANK = array(
    0 => array(
		'00' => $lang_id_tab['None'],
		'01' => $lang_id_tab['None'],
		0 => $lang_id_tab['None'],
		1 => $lang_id_tab['Private'],
		2 => $lang_id_tab['Corporal'],
		3 => $lang_id_tab['Sergeant'],
		4 => $lang_id_tab['Master_Sergeant'],
		5 => $lang_id_tab['Sergeant_Major'],
		6 => $lang_id_tab['Knight'],
		7 => $lang_id_tab['Knight-Lieutenant'],
		8 => $lang_id_tab['Knight-Captain'],
		9 => $lang_id_tab['Knight-Champion'],
		10 => $lang_id_tab['Lieutenant_Commander'],
		11 => $lang_id_tab['Commander'],
		12 => $lang_id_tab['Marshal'],
		13 => $lang_id_tab['Field_Marshal'],
		14 => $lang_id_tab['Grand_Marshal']
    ),
    1 => array(
		'00' => $lang_id_tab['None'],
		'01' => $lang_id_tab['None'],
		0 => $lang_id_tab['None'],
		1 => $lang_id_tab['Scout'],
		2 => $lang_id_tab['Grunt'],
		3 => $lang_id_tab['Sergeant'],
		4 => $lang_id_tab['Senior_Sergeant'],
		5 => $lang_id_tab['First_Sergeant'],
		6 => $lang_id_tab['Stone_Guard'],
		7 => $lang_id_tab['Blood_Guard'],
		8 => $lang_id_tab['Legionnare'],
		9 => $lang_id_tab['Centurion'],
		10 => $lang_id_tab['Champion'],
		11 => $lang_id_tab['Lieutenant_General'],
		12 => $lang_id_tab['General'],
		13 => $lang_id_tab['Warlord'],
		14 => $lang_id_tab['High_Warlord']
    )
);

$CHAR_RACE = array(
	1 => array('human', 0),
	2 => array('orc', 1),
	3 => array('dwarf', 0),
	4 => array('nightelf', 0),
	5 => array('undead', 1),
	6 => array('tauren', 1),
	7 => array('gnome', 0),
	8 => array('troll', 1),
	10 => array('bloodelf', 1),
	11 => array('draenei', 0)
);

function pvp_ranks($honor=0, $faction=0){
    $rank = '0'.$faction;
    if($honor > 0){
        if($honor < 2000) $rank = 1;
        else $rank = ceil($honor / 5000) + 1;
    }

	if ($rank>14) { $rank = 14; }
    return $rank;
};

function get_image_dir($level,$sex,$race,$class,$gm=0){
	$return = "";
	if ($gm>0 && file_exists("img/avatars/bliz/$gm.gif"))
			$return .= "img/avatars/bliz/$gm.gif";
		else if ($gm>0 && file_exists("img/avatars/bliz/$gm.gif"))
			$return .= "img/avatars/bliz/$gm.gif";
		else if ($gm>0 && file_exists("img/avatars/bliz/$gm.jpg"))
			$return .= "img/avatars/bliz/$gm.jpg";
		else {
			if($level >= 60){
				if($level >= 70)
					$return .= "img/avatars/70/$sex-$race-$class.gif";
				else
					$return .= "img/avatars/60/$sex-$race-$class.gif";
			}
			else
				$return .= "img/avatars/np/$sex-$race-$class.gif";
	}
	return $return;
};


?>
