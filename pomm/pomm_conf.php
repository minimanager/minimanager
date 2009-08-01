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

// use Minimanager configuration
require_once "func.php";
require_once("../scripts/config.dist.php");
require_once("../scripts/config.php");
$realmid = (int)$_GET["realmid"];
$server_arr = $server;

// points located on these maps(do not modify it)
$maps_for_points = "0,1,530,571,609";

// Names of the maps(screens)
$maps_names = "'Azeroth','Outland','Northrend'";

// count of maps
$maps_count = count(explode(',',$maps_names));

// database coding(do not modify it)
$database_encoding = 'utf8';

// language (en/ru)
$lang = 'en';

// show GM online (1/0)
$show_gm_online = 1;

// add '{GM}' to name if player is 'gm on' (1/0)
$add_gm_suffix = 1;

// show server status window (1/0)
// time to show uptime string (msec)
// time to show max online (msec)
// (do not set time < 1500)
$show_status = 1;
$time_to_show_uptime = 10000;
$time_to_show_maxonline = 10000;

// Image dir
$img_base = "img/";

// Server adress (for realm status)
$server = $server_arr[$realmid]["addr"];

// Server port (for realm statust) 8085 or 3724
$port = $server_arr[$realmid]["game_port"];

// Update time (seconds), 0 - not update.
$time= "12";

// Show update timer 1 - on, 0 - off
$show_time="1";

// see UpdateFields.h
// 2.4.3 :
// UNIT_FIELD_LEVEL   = 34
// UNIT_FIELD_BYTES_0 = 36
// PLAYER_FLAGS       = 236
// 3.0.3 :
// UNIT_FIELD_BYTES_0 = 22
// UNIT_FIELD_LEVEL   = 53
// PLAYER_FLAGS       = 150
$UNIT_FIELD_BYTES_0 = 22;
$UNIT_FIELD_LEVEL   = 53;
$PLAYER_FLAGS   = 150;

//DB connect options		
$host = $characters_db[$realmid]["addr"];				// HOST for characters database
$user = $characters_db[$realmid]["user"];					// USER for characters database
$password = $characters_db[$realmid]["pass"];				// PASS for characters database
$db = $characters_db[$realmid]["name"];				// NAME of characters database

$hostw = $world_db[$realmid]["addr"];				// HOST for world database
$userw = $world_db[$realmid]["user"];					// USER for world database
$passwordw = $world_db[$realmid]["pass"];				// PASS for world database
$dbw = $world_db[$realmid]["name"];					// NAME of world database

$hostr = $realm_db["addr"];				// HOST for realm database
$userr = $realm_db["user"];					// USER for realm database
$passwordr = $realm_db["pass"];				// PASS for realm database
$dbr = $realm_db["name"];					// NAME of realm database

// realm name
$realm_db = new DBLayer($hostr, $userr, $passwordr, $dbr);
$query = $realm_db->query("SELECT name FROM realmlist WHERE id = ".$realmid);
$realm_name = $realm_db->fetch_assoc($query);
$realm_name = htmlentities($realm_name["name"]);
?>
