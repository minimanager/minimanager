<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 *
 * Updated by Shnappie to work with 3 databases
 * instead of 2 supported by version of Q.SA
 */

$version = "0.12";

//---- SQL Configuration ----

/* SQL server type  :
*  "MySQL" - Mysql
*  "PgSQL" - PostgreSQL
*  "MySQLi" - MySQLi
*  "SQLLite" - SQLLite
*/
$db_type = "MySQL";

$mmfpm_db = Array(
	'addr' => "127.0.0.1:3306",	//SQL server IP:port this DB located on
	'user' => "root",			//SQL server login this DB located on
	'pass' => "",			//SQL server pass this DB located on
	'name' => "mmfpm",			//realmd DB name
	'encoding' => "utf8" 		//SQL connection encoding
	);

$realm_db = Array(
	'addr' => "127.0.0.1:3306",	//SQL server IP:port this realmd located on
	'user' => "root",			//SQL server login this realmd located on
	'pass' => "",			//SQL server pass this realmd located on
	'name' => "realmd",			//realmd DB name
	'encoding' => "utf8" 		//SQL connection encoding
	);

$mangos_db = Array(
	1 => array(		//position in array must represent realmd ID
			'id' => 1,					//Realm ID
			'addr' => "127.0.0.1:3306",	//SQL server IP:port this DB located on
			'user' => "root",			//SQL server login this DB located on
			'pass' => "",			//SQL server pass this DB located on
			'name' => "mangos",			//World Database name
			'encoding' => "utf8" 		//SQL connection encoding
			),
);

$characters_db = Array(
	1 => array(		//position in array must represent realmd ID
			'id' => 1,					//Realm ID
			'addr' => "127.0.0.1:3306",	//SQL server IP:port this DB located on
			'user' => "root",			//SQL server login this DB located on
			'pass' => "",			//SQL server pass this DB located on
			'name' => "characters",			//Character Database name
			'encoding' => "utf8", 		//SQL connection encoding
			),          //NOTE: THIS USER MUST HAVE AT LEAST READ ACCESS ON THE WORLD DATABASE
);

//---- Game Server Configuration ----
$server = Array(	//if more than one realm used, even if they are on same system new subarray MUST be added.
	1 => array(		//position in array must represent realmd ID, same as in $mangos_db
			'addr' => "127.0.0.1",		//Game Server IP - Must be external address
			'game_port' => 8085,		//Game Server port
			'term_type' => "SSH",		//Terminal type - ("SSH"/"Telnet")
			'term_port' => 22,			//Terminal port
			'rev' => "rev. 6360",		//Mangos rev. used
			'both_factions' => true	//Allow to see opponent faction characters. Affects only players.
			),
);
$sql_search_limit = 100; //limit number of maximum search results

//---- Mail configuration ----
$admin_mail = "mail@mail.com";	//mail used for bug reports and other user contact

$mailer_type = "smtp"; 			// type of mailer to be used("mail", "sendmail", "smtp")
$from_mail = "mail@mail.com"; 	//all emails will be sent from this email
//smtp server config
$smtp_cfg = array(
			'host' => "smtp.mail.com",	//smtp server
			'port' => 25,				//port
			'user' => "",				//username - use only if auth. required
			'pass' => ""				//pass
			);

//---- New account creation Options ----
$disable_acc_creation = false; 	//true = Do not allow new accounts to be created
$expansion_select = false; //true = Shows option to select expansion or classic False = no option(TBC) enabled by default
$defaultoption = 1; //if the above is false then set what the default option will be (1 = TBC, 0 = Classic)
$enable_captcha = true; //false = no security image check (enable for protection against 'bot' registrations)
$send_mail_on_creation = false; //true = send mail at account creation.
$create_acc_locked = 0; 		//if set to '1' newly created accounts will be made locked to 0.0.0.0 IP disallowing user to login.
$validate_mail_host = false;  	//actualy make sure the mail host provided in email is valid/accessible host.
$limit_acc_per_ip = false; 		//true = limit to one account per IP
/* this option will limit account creation to users from selected net range(s).
*  allow all => empty array
* e.g: "120-122.55.255-0.255-0", */
$valid_ip_mask = array(
				//"255-0.255-0.255-0.255-0",
				);

//---- Layout configuration ----
$title = "MiniManager for MaNgOs srv.";
$itemperpage = 25;

$css_template = "Sulfur"; 		//file/folder name of css tamplate to use from templates directory by default
$language = "english"; 			//default site language
$tt_lang = "www";          //wowhead tooltip language.....choices are 'fr', 'de', 'es' (for 'en' use www)
$timezone = "UTC";         //default timezone (use your local timezone code)

//---- IRC Options ------
$irc_cfg = array(
			'server' => "mangos.cjb.net",	//irc server
			'port' => 6667,					//port
			'channel' => "minimanager"				//channel
			);

//---- External Links ----
$item_datasite = "http://$tt_lang.wowhead.com/?item=";
$quest_datasite = "http://$tt_lang.wowhead.com/?quest=";
$creature_datasite = "http://$tt_lang.wowhead.com/?npc=";
$spell_datasite = "http://$tt_lang.wowhead.com/?spell=";
$skill_datasite = "http://$tt_lang.wowhead.com/?spells=";
$talent_datasite = "http://$tt_lang.wowhead.com/?spell=";
$talent_calculator_datasite = "http://www.worldofwarcraft.com/info/classes";
$go_datasite = "http://$tt_lang.wowhead.com/?object=";
$get_icons_from_web = true; //wherever to get icons from the web in case they are missing in /img/INV dir.

//---- Backup configuration ----
$backup_dir = "./backup";  //make sure webserver have the permission to write/read it!

//---- HTTP Proxy Configuration ----
$proxy_cfg = Array(
	'addr' => "", //configure only if requierd
	'port' => 80,
	'user' => "",
	'pass' => ""
	);

// ---- Module and Security settings ----
// --   Meaning of the columns : TARGET, LANG_TEXT, ( READ/VIEW , UPDATE , INSERT , DELETE ) min Permission GM LEVEL
// --   Files excluded for this : Login.php, Pomm.php 
// --   - Both files don't use header.php, so we can't include this method.. but its not a big deal
//
// --   Permmissions are set, but only READ Permission used right now... 
// --   - Updates will follow
// -- 
// --   If not set or lower, higher permission from lower rights overwrite higher permission  ( Example : VIEW = 5, UDDATE = 0 >> UPDATE = 5)

$menu_array = Array(
  array("index.php", 'main', array()),
	array("#", 'users', array(
	  array("user.php", 'accounts',1,0,0,0),
	  array("char_list.php", 'characters',1,0,0,0),
	  array("guild.php", 'guilds',0,0,0,0),
	  array("arenateam.php", 'arena_teams',0,0,0,0),
	  array("honor.php", 'honor',0,0,0,0),
	  array("banned.php", 'banned_list',1,0,0,0),
	  array("cleanup.php", 'cleanup',3,0,0,0),
	  array("stat.php", 'statistics',0,0,0,0),
	  array("javascript:void(0);\" onclick=\"window.open('./pomm/pomm.php', 'pomm', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbar=0, Resizable=0, Copyhistory=1, Width=784, Height=525')", 'player_map',0,0,0,0),
	  ),
	),
	array("#", 'tools', array(
	  array("ssh.php", 'ssh_line',3,0,0,0),
	  array("run_patch.php", 'run_sql_patch',3,0,0,0),
	  array("ticket.php", 'tickets',1,0,0,0),
	  array("ahstats.php", 'auctionhouse',0,0,0,0),
	  array("events.php", 'events',0,0,0,0),
	  array("realm.php", 'realm',0,0,0,0),
	  array("motd.php", 'add_motd',1,0,0,0),
	  array("mail.php", 'mail',1,0,0,0),
	  array("irc.php", 'irc',0,0,0,0),
	  array("instances.php", 'instances',0,0,0,0),
	  ),
	),
	array("#", 'db', array(
	  array("item.php", 'items',1,0,0,0),
	  array("creature.php", 'creatures',1,0,0,0),
	  array("game_object.php", 'game_object',1,0,0,0),
	  array("tele.php", 'teleports',1,0,0,0),
	  array("command.php", 'command',0,0,0,0),
	  array("backup.php", 'backup',3,0,0,0),
	  array("repair.php", 'repair',3,0,0,0),
	  ),
	),
	array("#", 'forums', array(
	  array("forum.php", 'forums',0,0,0,0),
    array("javascript:void(0);\" onclick=\"window.open('./forum.html', 'forum')", 'forums',0,0,0,0),
	  ),
	),
	array("#", 'invisible', array(
	  array("char.php", 'character',0,0,0,0),
	  array("char_edit.php", 'char_edit',1,0,0,0),
	  array("edit.php", 'MyAccount',0,0,0,0),
	  array("index.php, 'Startpage'",0,0,0,0),
	  ),
	),
);
				
$debug = false; //set to true if full php debugging requierd.
?>
