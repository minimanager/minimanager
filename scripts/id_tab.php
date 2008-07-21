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
	0 => array(0,"Player","-"),
	1 => array(1,"Moderator","Mod"),
	2 => array(2,"Gamemaster","GM"),
	3 => array(3,"BugTracker","BT"),
	4 => array(4,"Admin","ADM"),
	5 => array(5,"Sys OP","SYS")
);

function get_gm_level($id){
 global $lang_id_tab, $gm_level_arr;
	if(isset($gm_level_arr[$id])) return $gm_level_arr[$id][1];
		else return($lang_id_tab['unknown']);
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get map name by its id
//covered up to 2.0.6
$map_id = Array(
	0 => array(0,$lang_id_tab['azeroths']),
	1 => array(1,$lang_id_tab['kalimdor']),
	13 => array(13,$lang_id_tab['test_zone']),
	17 => array(17,$lang_id_tab['kalidar']),
	30 => array(30,$lang_id_tab['alterac_valley']),
	33 => array(33,$lang_id_tab['shadowfang_keep_instance']),
	34 => array(34,$lang_id_tab['the_stockade_instance']),
	35 => array(35,$lang_id_tab['stormwind_prison']),
	36 => array(36,$lang_id_tab['deadmines_instance']),
	37 => array(37,$lang_id_tab['plains_of_snow']),
	43 => array(43,$lang_id_tab['wailing_caverns_instance']),
	44 => array(44,$lang_id_tab['monastery_interior']),
	47 => array(47,$lang_id_tab['razorfen_kraul_instance']),
	48 => array(48,$lang_id_tab['blackfathom_deeps_instance']),
	70 => array(70,$lang_id_tab['uldaman_instance']),
	90 => array(90,$lang_id_tab['gnomeregan_instance']),
	109 => array(109,$lang_id_tab['sunken_temple_instance']),
	129 => array(129,$lang_id_tab['razorfen_downs_instance']),
	150 => array(150,$lang_id_tab['outland']),
	169 => array(169,$lang_id_tab['emerald_forest']),
	189 => array(189,$lang_id_tab['scarlet_monastery_instance']),
	209 => array(209,$lang_id_tab['zul_farrak_instance']),
	229 => array(229,$lang_id_tab['blackrock_spire_instance']),
	230 => array(230,$lang_id_tab['blackrock_depths_instance']),
	249 => array(249,$lang_id_tab['onyxia_s_lair_instance']),
	269 => array(269,$lang_id_tab['cot_black_morass']),
	289 => array(289,$lang_id_tab['scholomance_instance']),
	309 => array(309,$lang_id_tab['zul_gurub_instance']),
	329 => array(329,$lang_id_tab['stratholme_instance']),
	349 => array(349,$lang_id_tab['maraudon_instance']),
	369 => array(369,$lang_id_tab['deeprun_tram']),
	389 => array(389,$lang_id_tab['ragefire_chasm_instance']),
	409 => array(409,$lang_id_tab['the_molten_core_instance']),
	429 => array(429,$lang_id_tab['dire_maul_instance']),
	449 => array(449,$lang_id_tab['alliance_pvp_barracks']),
	450 => array(450,$lang_id_tab['horde_pvp_barracks']),
	451 => array(451,$lang_id_tab['development_land']),
	469 => array(469,$lang_id_tab['blackwing_lair_instance']),
	489 => array(489,$lang_id_tab['warsong_gulch']),
	509 => array(509,$lang_id_tab['ruins_of_ahn_qiraj_instance']),
	529 => array(529,$lang_id_tab['arathi_basin']),
	530 => array(530,$lang_id_tab['outland']),
	531 => array(531,$lang_id_tab['temple_of_ahn_qiraj_instance']),
	532 => array(532,$lang_id_tab['karazahn']),
	533 => array(533,$lang_id_tab['naxxramas_instance']),
	534 => array(534,$lang_id_tab['cot_hyjal_past']),
	540 => array(540,$lang_id_tab['hellfire_military']),
	542 => array(542,$lang_id_tab['hellfire_demon']),
	543 => array(543,$lang_id_tab['hellfire_rampart']),
	544 => array(544,$lang_id_tab['hellfire_raid']),
	545 => array(545,$lang_id_tab['coilfang_pumping']),
	546 => array(546,$lang_id_tab['coilfang_marsh']),
	547 => array(547,$lang_id_tab['coilfang_draenei']),
	548 => array(548,$lang_id_tab['coilfang_raid']),
	550 => array(550,$lang_id_tab['tempest_keep_raid']),
	552 => array(552,$lang_id_tab['tempest_keep_arcane']),
	553 => array(553,$lang_id_tab['tempest_keep_atrium']),
	554 => array(554,$lang_id_tab['tempest_keep_factory']),
	555 => array(555,$lang_id_tab['auchindoun_shadow']),
	556 => array(556,$lang_id_tab['auchindoun_arakkoa']),
	557 => array(557,$lang_id_tab['auchindoun_ethereal']),
	558 => array(558,$lang_id_tab['auchindoun_draenei']),
	559 => array(559,$lang_id_tab['nagrand_arena']),
	560 => array(560,$lang_id_tab['cot_hillsbrad_past']),
	562 => array(562,$lang_id_tab['blades_edge_arena']),
	564 => array(564,$lang_id_tab['black_temple']),
	565 => array(565,$lang_id_tab['gruuls_lair']),
	566 => array(566,$lang_id_tab['netherstorm_arena']),
	568 => array(568,$lang_id_tab['zulaman']),
	571 => array(571,$lang_id_tab['northrend']),
	574 => array(574,$lang_id_tab['utgarde_keep']),
	575 => array(575,$lang_id_tab['utgarde_pinnacle']),
	576 => array(576,$lang_id_tab['nexus']),
	578 => array(578,$lang_id_tab['oculus']),
	580 => array(580,$lang_id_tab['sunwell_plateau']),
	585 => array(585,$lang_id_tab['magisters_terrace']),
	595 => array(595,$lang_id_tab['cot_stratholme_past']),
	599 => array(599,$lang_id_tab['halls_of_stone']),
	600 => array(600,$lang_id_tab['draktheron_keep']),
	601 => array(601,$lang_id_tab['azjol_nerub']),
	602 => array(602,$lang_id_tab['halls_of_lightning']),
	603 => array(603,$lang_id_tab['ulduar']),
	604 => array(604,$lang_id_tab['gundrak'])
	
	
);

function get_map_name($id){
global $lang_id_tab, $map_id;
	if( isset($map_id[$id])) return $map_id[$id][1];
		else return($lang_id_tab['unknown']);
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get player class by its id
function get_player_class($class_id){
global $lang_id_tab;
switch ($class_id) {
case 1:
   return($lang_id_tab['warrior']);
   break;
case 2:
   return($lang_id_tab['paladin']);
   break;
case 3:
   return($lang_id_tab['hunter']);
   break;
case 4:
   return($lang_id_tab['rogue']);
   break;
case 5:
   return($lang_id_tab['priest']);
   break;
case 6:
   return($lang_id_tab['death_knight']);
   break;
case 7:
   return($lang_id_tab['shaman']);
   break;
case 8:
   return($lang_id_tab['mage']);
   break;
case 9:
   return($lang_id_tab['warlock']);
   break;
case 11:
   return($lang_id_tab['druid']);
   break;
default:
    return($lang_id_tab['unknown']);
 }
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get player race by its id
function get_player_race($race_id){
global $lang_id_tab;
switch ($race_id) {
case 1:
   return($lang_id_tab['human']);
   break;
case 2:
   return($lang_id_tab['orc']);
   break;
case 3:
   return($lang_id_tab['dwarf']);
   break;
case 4:
   return($lang_id_tab['nightelf']);
   break;
case 5:
   return($lang_id_tab['undead']);
   break;
case 6:
   return($lang_id_tab['tauren']);
   break;
case 7:
   return($lang_id_tab['gnome']);
   break;
case 8:
   return($lang_id_tab['troll']);
   break;
case 9:
   return($lang_id_tab['goblin']);
   break;
case 10:
   return($lang_id_tab['bloodelf']);
   break;
case 11:
   return($lang_id_tab['draenei']);
   break;
default:
    return($lang_id_tab['unknown']);
 }
}

////////////////////////////////////////////////////////////////////////////////////////////////
//get zone name by mapid and players x,y
$zone_id = Array(
 //Azeroth
	1497 => Array($lang_id_tab['undercity'],1497),
	1537 => Array($lang_id_tab['ironforge'],1537),
	1519 => Array($lang_id_tab['stormwind_city'],1519),
	3 => Array($lang_id_tab['badlands'],3),
	11 => Array($lang_id_tab['wetlands'],11),
	33 => Array($lang_id_tab['stranglethorn_vale'],33),
	44 => Array($lang_id_tab['redridge_mountains'],44),
	38 => Array($lang_id_tab['loch_modan'],38),
	10 => Array($lang_id_tab['duskwood'],10),
	41 => Array($lang_id_tab['deadwind_pass'],41),
	12 => Array($lang_id_tab['elwynn_forest'],12),
	46 => Array($lang_id_tab['burning_steppes'],46),
	51 => Array($lang_id_tab['searing_gorge'],51),
	1 => Array($lang_id_tab['dun_morogh'],1),
	47 => Array($lang_id_tab['the_hinterlands'],47),
	40 => Array($lang_id_tab['westfall'],40),
	267 => Array($lang_id_tab['hillsbrad_foothills'],267),
	139 => Array($lang_id_tab['eastern_plaguelands'],139),
	28 => Array($lang_id_tab['western_plaguelands'],28),
	130 => Array($lang_id_tab['silverpine_forest'],130),
	85 => Array($lang_id_tab['tirisfal_glades'],85),
	4 => Array($lang_id_tab['blasted_lands'],4),
	8 => Array($lang_id_tab['swamp_of_sorrows'],8),
	45 => Array($lang_id_tab['arathi_highlands'],45),
	36 => Array($lang_id_tab['alterac_mountains'],36),
 //Kalimdor
	1657 => Array($lang_id_tab['darnassus'],1657),
	1638 => Array($lang_id_tab['thunder_bluff'],1638),
	1637 => Array($lang_id_tab['orgrimmar'],1637),
	493 => Array($lang_id_tab['moonglade'],493),
	1377 => Array($lang_id_tab['silithus'],1377),
	618 => Array($lang_id_tab['winterspring'],618),
	490 => Array($lang_id_tab['un_goro_crater'],490),
	361 => Array($lang_id_tab['felwood'],361),
	16 => Array($lang_id_tab['azshara'],16),
	440 => Array($lang_id_tab['tanaris'],440),
	15 => Array($lang_id_tab['dustwallow_marsh'],15),
	215 => Array($lang_id_tab['mulgore'],215),
	357 => Array($lang_id_tab['feralas'],357),
	405 => Array($lang_id_tab['desolace'],405),
	400 => Array($lang_id_tab['thousand_needles'],400),
	14 => Array($lang_id_tab['durotar'],14),
	331 => Array($lang_id_tab['ashenvale'],331),
	148 => Array($lang_id_tab['darkshore'],148),
	141 => Array($lang_id_tab['teldrassil'],141),
	406 => Array($lang_id_tab['stonetalon_mountains'],406),
	17 => Array($lang_id_tab['the_barrens'],17),
 //Outland
	3703 => Array($lang_id_tab['shattrath_city'],3703),
	3487 => Array($lang_id_tab['silvermoon_city'],3487),
	3523 => Array($lang_id_tab['netherstorm'],3523),
	3519 => Array($lang_id_tab['terokkar_forest'],3519),
	3518 => Array($lang_id_tab['nagrand'],3518),
	3525 => Array($lang_id_tab['bloodmyst_isle'],3525),
	3522 => Array($lang_id_tab['blades_edge_mountains'],3522),
	3520 => Array($lang_id_tab['shadowmoon_valley'],3520),
	3557 => Array($lang_id_tab['the_exodar'],3557),
	3521 => Array($lang_id_tab['zangarmarsh'],3521),
	3483 => Array($lang_id_tab['hellfire_peninsula'],3483),
	3524 => Array($lang_id_tab['azuremyst_isle'],3524),
	3433 => Array($lang_id_tab['ghostlands'],3433),
	3430 => Array($lang_id_tab['eversong_woods'],3430)
 	);

function get_zone_name($id){
 global $zone_id;
	if( isset($zone_id[$id])) return $zone_id[$id][0];
		else return(" ");
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
