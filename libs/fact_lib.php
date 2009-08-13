<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */


//#############################################################################
// faction id and rep table

function fact_get_fact_id()
{
  $fact_id = Array
  ( //              0    1  2       3               4            5            6            7            8             9             10            11
    //id => array(name,team,n,reputationListID,BaseRepMask1,BaseRepMask2,BaseRepMask3,BaseRepMask4,BaseRepValue1,BaseRepValue2,BaseRepValue3,BaseRepValue4)

      54 => array("Gnomeregan Exiles", "Alliance",1,18,1037,690,64,0,3100,-42000,4000,0),
      72 => array("Stormwind",         "Alliance",1,19,1100,690,1,0,3100,-42000,4000,0),
      47 => array("Ironforge",         "Alliance",1,20,1097,690,4,0,3100,-42000,4000,0),
      69 => array("Darnassus",         "Alliance",1,21,1093,690,8,0,3100,-42000,4000,0),
     930 => array("Exodar",            "Alliance",1,49,77,946,1024,0,3000,-42000,4000,0),

      76 => array("Orgrimmar",        "Horde",2,14,160,1101,2,528,3100,-42000,4000,500),
     530 => array("Darkspear Trolls", "Horde",2,15,34,1101,528,128,3100,-42000,500,4000),
      81 => array("Thunder Bluff",    "Horde",2,16,130,1101,528,32,3100,-42000,500,4000),
      68 => array("Undercity",        "Horde",2,17,162,1101,16,512,500,-42000,4000,3100),
     911 => array("Silvermoon City",  "Horde",2,55,162,1101,512,16,400,-42000,4000,3100),

     730 => array("Stormpike Guard",       "Alliance Forces",3,40,1101,690,0,0,0,-42000,0,0),
     890 => array("Silverwing Sentinels",  "Alliance Forces",3,45,1101,690,0,0,0,-42000,0,0),
     509 => array("The League of Arathor", "Alliance Forces",3,53,1101,690,0,0,0,-42000,0,0),

     729 => array("Frostwolf Clan",    "Horde Forces",4,41,690,1101,0,0,0,-42000,0,0),
     889 => array("Warsong Outriders", "Horde Forces",4,46,690,1101,0,0,0,-42000,0,0),
     510 => array("The Defilers",      "Horde Forces",4,52,690,1101,0,0,0,-42000,0,0),

      21 => array("Booty Bay", "Steamwheedle Cartel",5,1,1791,0,0,0,500,0,0,0),
     369 => array("Gadgetzan", "Steamwheedle Cartel",5,7,1791,0,0,0,500,0,0,0),
     470 => array("Ratchet",   "Steamwheedle Cartel",5,9,1791,0,0,0,500,0,0,0),
     577 => array("Everlook",  "Steamwheedle Cartel",5,28,1791,0,0,0,500,0,0,0),

     947 => array("Thrallmar",            "Outland",6,37,690,1101,0,0,0,-42000,0,0),
     946 => array("Honor Hold",           "Outland",6,38,1101,690,0,0,0,-42000,0,0),
     933 => array("The Consortium",       "Outland",6,60,2047,0,0,0,0,0,0,0),
     941 => array("The Mag'har",          "Outland",6,61,690,1101,0,0,-500,-42000,0,0),
     942 => array("Cenarion Expedition",  "Outland",6,64,2047,0,0,0,0,0,0,0),
     970 => array("Sporeggar",            "Outland",6,65,2047,0,0,0,-2500,0,0,0),
     978 => array("Kurenai",              "Outland",6,66,1101,690,0,0,-1200,-42000,0,0),
    1012 => array("Ashtongue Deathsworn", "Outland",6,70,1791,0,0,0,0,0,0,0),
    1015 => array("Netherwing",           "Outland",6,71,1791,0,0,0,-42000,0,0,0),
    1038 => array("Ogri'la",              "Outland",6,73,1791,0,0,0,0,0,0,0),

     935 => array("The Sha'tar",             "Shattrath City",7,39,1791,0,0,0,0,0,0,0),
     932 => array("The Aldor",               "Shattrath City",7,58,255,1024,512,0,0,3500,-3500,0),
     934 => array("The Scryers",             "Shattrath City",7,62,255,1024,512,0,0,-3500,3500,0),
    1011 => array("Lower City",              "Shattrath City",7,69,32767,0,0,0,0,0,0,0),
    1031 => array("Sha'tari Skyguard",       "Shattrath City",7,72,1791,0,0,0,0,0,0,0),
    1077 => array("Shattered Sun Offensive", "Shattrath City",7,73,1791,0,0,0,0,0,0,0),

    1050 => array("Valiance Expedition", "Alliance Vanguard",8,74,1101,690,0,0,0,-42000,0,0),
    1068 => array("Explorers' League",   "Alliance Vanguard",8,78,1101,690,0,0,0,-42000,0,0),
    1094 => array("The Silver Covenant", "Alliance Vanguard",8,90,1101,690,0,0,0,-42000,0,0),
    1126 => array("The Frostborn",       "Alliance Vanguard",8,99,1101,690,0,0,0,-42000,0,0),

    1064 => array("The Taunka",            "Horde Expedition",9,76,690,1101,0,0,0,-42000,0,0),
    1067 => array("The Hand of Vengeance", "Horde Expedition",9,77,690,1101,0,0,0,-42000,0,0),
    1085 => array("Warsong Offensive",     "Horde Expedition",9,81,1101,690,0,0,-42000,0,0,0),
    1124 => array("The Sunreavers",        "Horde Expedition",9,98,690,1101,0,0,0,-42000,0,0),

    1104 => array("Frenzyheart Tribe", "Sholazar Basin",10,92,1791,0,0,0,0,0,0,0),
    1105 => array("The Oracles",       "Sholazar Basin",10,93,1791,0,00,0,0,0,0,0),

    1073 => array("The Kalu'ak",               "Northrend",11,79,1791,0,0,0,0,0,0,0),
    1091 => array("The Wyrmrest Accord",       "Northrend",11,83,1791,0,0,0,0,0,0,0),
    1090 => array("Kirin Tor",                 "Northrend",11,84,1229,690,1101,690,0,0,3000,3000),
    1098 => array("Knights of the Ebon Blade", "Northrend",11,91,0,0,0,0,3200,0,0,0),
    1106 => array("Argent Crusade",            "Northrend",11,94,32767,0,0,0,0,0,0,0),
    1119 => array("The Sons of Hodir",         "Northrend",11,97,1791,0,0,0,-42000,0,0,0),

      87 => array("Bloodsail Buccaneers",           "Other",12,0,1791,0,0,0,-6500,0,0,0),
      92 => array("Gelkis Clan Centaur",            "Other",12,2,1791,0,0,0,2000,0,0,0),
      93 => array("Magram Clan Centaur",            "Other",12,3,1791,0,0,0,2000,0,0,0),
      59 => array("Thorium Brotherhood",            "Other",12,4,1791,0,0,0,0,0,0,0),
     349 => array("Ravenholdt",                     "Other",12,5,1791,0,0,0,0,0,0,0),
      70 => array("Syndicate",                      "Other",12,6,1791,0,0,0,-10000,0,0,0),
     471 => array("Wildhammer Clan",                "Other",12,8,1097,690,4,0,150,-42000,500,0),
     169 => array("Steamwheedle Cartel",            "Other",12,10,1791,0,0,0,500,0,0,0),
     469 => array("Alliance",                       "Other",12,11,1101,690,0,0,3300,-42000,0,0),
      67 => array("Horde",                          "Other",12,12,690,1101,0,0,3500,-42000,0,0),
     529 => array("Argent Dawn",                    "Other",12,13,1791,0,0,0,200,0,0,0),
      86 => array("Leatherworking - Dragonscale",   "Other",12,22,1791,0,0,0,2999,0,0,0),
      83 => array("Leatherworking - Elemental",     "Other",12,23,1791,0,0,0,2999,0,0,0),
     549 => array("Leatherworking - Tribal",        "Other",12,24,1791,0,0,0,2999,0,0,0),
     551 => array("Engineering - Gnome",            "Other",12,25,1791,0,0,0,2999,0,0,0),
     550 => array("Engineering - Goblin",           "Other",12,26,1791,0,0,0,2999,0,0,0),
     589 => array("Wintersaber Trainers",           "Other",12,27,690,1101,0,0,-42000,0,0,0),
      46 => array("Blacksmithing - Armorsmithing",  "Other",12,29,1791,0,0,0,0,0,0,0),
     289 => array("Blacksmithing - Weaponsmithing", "Other",12,30,1791,0,0,0,0,0,0,0),
     570 => array("Blacksmithing - Axesmithing",    "Other",12,31,1791,0,0,0,0,0,0,0),
     571 => array("Blacksmithing - Swordsmithing",  "Other",12,32,1791,0,0,0,0,0,0,0),
     569 => array("Blacksmithing - Hammersmithing", "Other",12,33,1791,0,0,0,0,0,0,0),
     574 => array("Caer Darrow",                    "Other",12,34,1791,0,0,0,0,0,0,0),
     576 => array("Timbermaw Hold",                 "Other",12,35,1791,0,0,0,-3500,0,0,0),
     609 => array("Cenarion Circle",                "Other",12,36,1791,40,0,0,0,2000,0,0),
     749 => array("Hydraxian Waterlords",           "Other",12,42,1791,0,0,0,0,0,0,0),
     980 => array("Outland",                        "Other",12,43,0,0,0,0,0,0,0,0),
     809 => array("Shen'dralar",                    "Other",12,44,1791,0,0,0,0,0,0,0),
     891 => array("Alliance Forces",                "Other",12,47,1101,178,0,0,0,-42000,0,0),
     892 => array("Horde Forces",                   "Other",12,48,690,77,0,0,0,-42000,0,0),
     909 => array("Darkmoon Faire",                 "Other",12,50,1791,0,0,0,0,0,0,0),
     270 => array("Zandalar Tribe",                 "Other",12,51,1791,0,0,0,0,0,0,0),
     910 => array("Brood of Nozdormu",              "Other",12,54,1791,0,0,0,-42000,0,0,0),
     922 => array("Tranquillien",                   "Other",12,56,690,1101,0,0,0,-42000,0,0),
     990 => array("The Scale of the Sands",         "Other",12,57,1791,0,0,0,0,0,0,0),
     936 => array("Shattrath City",                 "Other",12,59,2047,0,0,0,0,0,0,0),
     967 => array("The Violet Eye",                 "Other",12,63,4095,0,0,0,0,0,0,0),
     989 => array("Keepers of Time",                "Other",12,67,1791,0,0,0,0,0,0,0),
    1005 => array("Friendly, Hidden",               "Other",12,68,32767,0,0,0,3000,0,0,0),
    1037 => array("Alliance Vanguard",              "Other",12,88,1101,690,0,0,0,0,0,0),
    1052 => array("Horde Expedition",               "Other",12,75,690,1101,0,0,0,-42000,0,0),
    1097 => array("Northrend",                      "Other",12,89,0,0,0,0,0,0,0,0),
    1117 => array("Sholazar Basin",                 "Other",12,95,1791,0,0,0,0,0,0,0)
  );
  return $fact_id;
}


//#############################################################################
//get reputation ranks lengths - http://www.wowwiki.com/Reputation

function fact_get_reputation_rank_length()
{
  $reputation_rank_length = array
    (36000, 3000, 3000, 3000, 6000, 12000, 21000, 999);

  return $reputation_rank_length;
}


//#############################################################################
//get reputation ranks by its id - http://www.wowwiki.com/Reputation

function fact_get_reputation_rank_arr()
{
  $reputation_rank =
    array
    (
      0 => "Hated",
      1 => "Hostile",
      2 => "Unfriendly",
      3 => "Neutral",
      4 => "Friendly",
      5 => "Honored",
      6 => "Revered",
      7 => "Exalted"
    );
  return $reputation_rank;
}


//#############################################################################
//get faction name by its id

function fact_get_faction_name($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $faction_name = $sqlm->fetch_row($sqlm->query("SELECT `field_19` FROM `dbc_faction` WHERE `id` = $id LIMIT 1"));
  return $faction_name[0];

}


//#############################################################################
//get faction tree by its id - needs to be redone

function fact_get_faction_tree($id)
{
  $fact_id = fact_get_fact_id();

  if( isset($fact_id[$id]))
    return $fact_id[$id][2];
  else
    return 0;
}


//#############################################################################
//get faction name by its id

function fact_get_base_reputation($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $faction_base_reputation = $sqlm->fetch_row($sqlm->query("SELECT `field_1`, `field_2`, `field_3`, `field_4`, `field_5`, `field_10`, `field_11`, `field_12`, `field_13` FROM `dbc_faction` WHERE `id` = $id LIMIT 1"));
  if(!isset($faction_base_reputation[$id]))
    return 0;
  for ($i = 0; $i <= 4; $i++)
  {
    if ($faction_base_reputation[$id][0 + $i] & (1 << ($race-1)))
      return $faction_base_reputation[$id][5 + $i];
  }
  return 0;
}


//#############################################################################
//get reputation by its id

function fact_get_reputation($id, $standing, $race)
{
  return fact_get_base_reputation($id, $race) + $standing;
}


//#############################################################################
//get reputation rank by its id

function fact_get_reputation_rank($id,  $standing, $race)
{
  $reputation = fact_get_reputation($id, $standing, $race);
    return fact_reputation_to_rank($reputation);
}


//#############################################################################
//get reputation at rank by its id

function fact_get_reputation_at_rank($id,  $standing, $race)
{
  $reputation = fact_get_reputation($id, $standing, $race);
    return fact_reputation_at_rank($reputation);
}


//#############################################################################
//get base reputation rank by its id

function fact_get_base_reputation_rank($id, $race)
{
  $reputation = fact_get_base_reputation($id, $race);
    return fact_reputation_to_rank($reputation);
}


//#############################################################################
//get reputation at to rank by its id
//- http://github.com/mangos/mangos/blob/fcc2bfc52bab344de0a60c95dcbbdc55d2d226ba/src/game/ReputationMgr.h

function fact_reputation_at_to_rank($standing, $type)
{
  $reputation_rank = fact_get_reputation_rank_arr();
  $reputation_rank_length = fact_get_reputation_rank_length();
  $reputation_cap         =  42999;
  $reputation_bottom      = -42000;
  $MIN_REPUTATION_RANK = 0;
  $MAX_REPUTATION_RANK = 8;
  $reputation_rank_length = fact_get_reputation_rank_length();

  $limit = $reputation_cap;
  for ($i = $MAX_REPUTATION_RANK-1; $i >= $MIN_REPUTATION_RANK; --$i)
  {
    $limit -= $reputation_rank_length[$i];
    if ($standing >= $limit )
      return (($type) ? $standing - $limit : $i);
  }
  return (($type) ? 0 : $MIN_REPUTATION_RANK);
}

//#############################################################################
//get reputation to rank by its id

function fact_reputation_to_rank($standing)
{
  return fact_reputation_at_to_rank($standing, 0);
}

//#############################################################################
//get reputation at rank by its id

function fact_reputation_at_rank($standing)
{
  return fact_reputation_at_to_rank($standing, 1);
}


?>
