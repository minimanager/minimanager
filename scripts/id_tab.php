<?php
/*
 * Project Name: MiniManager for Mangos Server
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
$skill_id = Array(
	773 => array(773,$lang_id_tab['SKILL_INSCRIPTION']),
	762 => array(762,$lang_id_tab['SKILL_RIDING']),
	759 => array(759,$lang_id_tab['SKILL_LANG_DRAENEI']),
	755 => array(755,$lang_id_tab['SKILL_JEWELCRAFTING']),
	713 => array(713,$lang_id_tab['SKILL_RIDING_KODO']),
	673 => array(673,$lang_id_tab['SKILL_LANG_GUTTERSPEAK']),
	633 => array(633,$lang_id_tab['SKILL_LOCKPICKING']),
	613 => array(613,$lang_id_tab['SKILL_DISCIPLINE']),
	593 => array(593,$lang_id_tab['SKILL_DESTRUCTION']),
	574 => array(574,$lang_id_tab['SKILL_BALANCE']),
	554 => array(554,$lang_id_tab['SKILL_RIDING_UNDEAD_HORSE']),
	553 => array(553,$lang_id_tab['SKILL_RIDING_MECHANOSTRIDER']),
	533 => array(533,$lang_id_tab['SKILL_RIDING_RAPTOR']),
	473 => array(473,$lang_id_tab['SKILL_FIST_WEAPONS']),
	433 => array(433,$lang_id_tab['SKILL_SHIELD']),
	415 => array(415,$lang_id_tab['SKILL_CLOTH']),
	414 => array(414,$lang_id_tab['SKILL_LEATHER']),
	413 => array(413,$lang_id_tab['SKILL_MAIL']),
	393 => array(393,$lang_id_tab['SKILL_SKINNING']),
	375 => array(375,$lang_id_tab['SKILL_ELEMENTAL_COMBAT']),
	374 => array(374,$lang_id_tab['SKILL_RESTORATION']),
	373 => array(373,$lang_id_tab['SKILL_ENHANCEMENT']),
	356 => array(356,$lang_id_tab['SKILL_FISHING']),
	355 => array(355,$lang_id_tab['SKILL_AFFLICTION']),
	354 => array(354,$lang_id_tab['SKILL_DEMONOLOGY']),
	333 => array(333,$lang_id_tab['SKILL_ENCHANTING']),
	315 => array(315,$lang_id_tab['SKILL_LANG_TROLL']),
	313 => array(313,$lang_id_tab['SKILL_LANG_GNOMISH']),
	293 => array(293,$lang_id_tab['SKILL_PLATE_MAIL']),
	270 => array(270,$lang_id_tab['SKILL_PET_TALENTS']),
	261 => array(261,$lang_id_tab['SKILL_BEAST_TRAINING']),
	257 => array(257,$lang_id_tab['SKILL_PROTECTION']),
	256 => array(256,$lang_id_tab['SKILL_FURY']),
	253 => array(253,$lang_id_tab['SKILL_ASSASSINATION']),
	237 => array(237,$lang_id_tab['SKILL_ARCANE']),
	229 => array(229,$lang_id_tab['SKILL_POLEARMS']),
	228 => array(228,$lang_id_tab['SKILL_WANDS']),
	227 => array(227,$lang_id_tab['SKILL_SPEARS']),
	226 => array(226,$lang_id_tab['SKILL_CROSSBOWS']),
	222 => array(222,$lang_id_tab['SKILL_WEAPON_TALENTS']),
	202 => array(202,$lang_id_tab['SKILL_ENGINERING']),
	197 => array(197,$lang_id_tab['SKILL_TAILORING']),
	186 => array(186,$lang_id_tab['SKILL_MINING']),
	185 => array(185,$lang_id_tab['SKILL_COOKING']),
	184 => array(184,$lang_id_tab['SKILL_RETRIBUTION']),
	182 => array(182,$lang_id_tab['SKILL_HERBALISM']),
	176 => array(176,$lang_id_tab['SKILL_THROWN']),
	173 => array(173,$lang_id_tab['SKILL_DAGGERS']),
	172 => array(172,$lang_id_tab['SKILL_2H_AXES']),
	171 => array(171,$lang_id_tab['SKILL_ALCHEMY']),
	165 => array(165,$lang_id_tab['SKILL_LEATHERWORKING']),
	164 => array(164,$lang_id_tab['SKILL_BLACKSMITHING']),
	163 => array(163,$lang_id_tab['SKILL_MARKSMANSHIP']),
	162 => array(162,$lang_id_tab['SKILL_UNARMED']),
	160 => array(160,$lang_id_tab['SKILL_2H_MACES']),
	150 => array(150,$lang_id_tab['SKILL_RIDING_TIGER']),
	152 => array(152,$lang_id_tab['SKILL_RIDING_RAM']),
	149 => array(149,$lang_id_tab['SKILL_RIDING_WOLF']),
	148 => array(148,$lang_id_tab['SKILL_RIDING_HORSE']),
	141 => array(141,$lang_id_tab['SKILL_LANG_OLD_TONGUE']),
	140 => array(140,$lang_id_tab['SKILL_LANG_TITAN']),
	139 => array(139,$lang_id_tab['SKILL_LANG_DEMON_TONGUE']),
	138 => array(138,$lang_id_tab['SKILL_LANG_DRACONIC']),
	137 => array(137,$lang_id_tab['SKILL_LANG_THALASSIAN']),
	136 => array(136,$lang_id_tab['SKILL_STAVES']),
	134 => array(134,$lang_id_tab['SKILL_FERAL_COMBAT']),
	129 => array(129,$lang_id_tab['SKILL_FIRST_AID']),
	118 => array(118,$lang_id_tab['SKILL_DUAL_WIELD']),
	115 => array(115,$lang_id_tab['SKILL_LANG_TAURAHE']),
	113 => array(113,$lang_id_tab['SKILL_LANG_DARNASSIAN']),
	111 => array(111,$lang_id_tab['SKILL_LANG_DWARVEN']),
	109 => array(109,$lang_id_tab['SKILL_LANG_ORCISH']),
	98 => array(98,$lang_id_tab['SKILL_LANG_COMMON']),
	95 => array(95,$lang_id_tab['SKILL_DEFENSE']),
	78 => array(78,$lang_id_tab['SKILL_SHADOW']),
	55 => array(55,$lang_id_tab['SKILL_2H_SWORDS']),
	56 => array(56,$lang_id_tab['SKILL_HOLY']),
	54 => array(54,$lang_id_tab['SKILL_MACES']),
	51 => array(51,$lang_id_tab['SKILL_SURVIVAL']),
	50 => array(50,$lang_id_tab['SKILL_BEAST_MASTERY']),
	46 => array(46,$lang_id_tab['SKILL_GUNS']),
	45 => array(45,$lang_id_tab['SKILL_BOWS']),
	44 => array(44,$lang_id_tab['SKILL_AXES']),
	43 => array(43,$lang_id_tab['SKILL_SWORDS']),
	40 => array(40,$lang_id_tab['SKILL_POISONS']),
	39 => array(39,$lang_id_tab['SKILL_SUBTLETY']),
	38 => array(38,$lang_id_tab['SKILL_COMBAT']),
	26 => array(26,$lang_id_tab['SKILL_ARMS']),
	8 => array(8,$lang_id_tab['SKILL_FIRE']),
	6 => array(6,$lang_id_tab['SKILL_FROST'])
);

function get_skill_name($id){
global $lang_id_tab, $skill_id;
	if( isset($skill_id[$id])) return $skill_id[$id][1];
		else return 0;
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
	1 => array($lang_id_tab['human'], 0),
	2 => array($lang_id_tab['orc'], 1),
	3 => array($lang_id_tab['dwarf'],  0),
	4 => array($lang_id_tab['nightelf'], 0),
	5 => array($lang_id_tab['undead'], 1),
	6 => array($lang_id_tab['tauren'], 1),
	7 => array($lang_id_tab['gnome'],  0),
	8 => array($lang_id_tab['troll'], 1),
	10 => array($lang_id_tab['bloodelf'], 1),
	11 => array($lang_id_tab['draenei'],  0)
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
