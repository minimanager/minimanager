<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 *
 * Updated by Shnappie to work with 3 databases
 * instead of 2 supported by version of Q.SA
 */

//#############################################################################
//
// configuration note.
//
// Do not edit, move or delete this file.
//
// Option 1 (recommended)
//  create blank config.php file,
//  copy only the settings you want to change into config.php
//  make changes there.
//
// Option 2
//  copy this file as config.php,
//  make changes there.

$version = "0.14";

//#############################################################################
//---- SQL Configuration ----

//  SQL server type  :
//  "MySQL"   - Mysql
//  "PgSQL"   - PostgreSQL
//  "MySQLi"  - MySQLi
//  "SQLLite" - SQLLite

$db_type = "MySQL";

$sql_search_limit = 100;            // limit number of maximum search results

$mmfpm_db = Array
(
  'addr'     => "127.0.0.1:3306",   // SQL server IP:port this DB located on
  'user'     => "root",             // SQL server login this DB located on
  'pass'     => "1",                // SQL server pass this DB located on
  'name'     => "mmfpm",            // MiniManager DB name
  'encoding' => "utf8"              // SQL connection encoding
);

$realm_db = Array
(
  'addr'     => "127.0.0.1:3306",   // SQL server IP:port this realmd located on
  'user'     => "root",             // SQL server login this realmd located on
  'pass'     => "1",                // SQL server pass this realmd located on
  'name'     => "realmd",           // realmd DB name
  'encoding' => "utf8"              // SQL connection encoding
);

$world_db = Array
(
  1 => array
  (                                 // position in array must represent realmd ID
    'id'       => 1,                // Realm ID
    'addr'     => "127.0.0.1:3306", // SQL server IP:port this DB located on
    'user'     => "root",           // SQL server login this DB located on
    'pass'     => "1",              // SQL server pass this DB located on
    'name'     => "mangos",         // World Database name, by default "mangos" for MaNGOS, "world" for Trinity
    'encoding' => "utf8"            // SQL connection encoding
  ),
);

$characters_db = Array
(
  1 => array
  (                                 // position in array must represent realmd ID
    'id' => 1,                      // Realm ID
    'addr' => "127.0.0.1:3306",     // SQL server IP:port this DB located on
    'user' => "root",               // SQL server login this DB located on
    'pass' => "1",                  // SQL server pass this DB located on
    'name' => "characters",         // Character Database name
    'encoding' => "utf8",           // SQL connection encoding
  ),                                // NOTE: THIS USER MUST HAVE AT LEAST READ ACCESS ON THE WORLD DATABASE
);

//#############################################################################
//---- Game Server Configuration ----
$server_type = 0;                   // 0=MaNGOS, 1=Trinity

$server = Array
(                                   // if more than one realm used, even if they are on same system new subarray MUST be added.
  1 => array
  (                                 // position in array must represent realmd ID, same as in $world_db
    'addr'          => "127.0.0.1", // Game Server IP - Must be external address
    'game_port'     => 8085,        // Game Server port
    'term_type'     => "SSH",       // Terminal type - ("SSH"/"Telnet")
    'term_port'     => 22,          // Terminal port
    'rev'           => "rev. ",     // MaNGOS/Trinity rev. used
    'both_factions' => true         // Allow to see opponent faction characters. Affects only players.
  ),
);

//#############################################################################
//---- Mail configuration ----
$admin_mail = "mail@mail.com";      // mail used for bug reports and other user contact

$mailer_type = "smtp";              // type of mailer to be used("mail", "sendmail", "smtp")
$from_mail = "mail@mail.com";       // all emails will be sent from this email

//smtp server config
$smtp_cfg = array
(
  'host' => "smtp.mail.com",        // smtp server
  'port' => 25,                     // port
  'user' => "",                     // username - use only if auth. required
  'pass' => ""                      // pass
);

//#############################################################################
//---- IRC Options ------
$irc_cfg = array
(
  'server'  => "mangos.cjb.net",    // irc server
  'port'    => 6667,                // port
  'channel' => "minimanager"        // channel
);

//#############################################################################
//---- New account creation Options ----
$disable_acc_creation  = false;     // true = Do not allow new accounts to be created
$expansion_select      = true;      // true = Shows option to select expansion or classic False = no option, WOTLK enabled by default
$defaultoption         = 2;         // if the above is false then set what the default option will be (2 = WOTLK, 1 = TBC, 0 = Classic)
$enable_captcha        = false;     // false = no security image check (enable for protection against 'bot' registrations)
$send_mail_on_creation = false;     // true = send mail at account creation.
$create_acc_locked     = 0;         // if set to '1' newly created accounts will be made locked to 0.0.0.0 IP disallowing user to login.
$validate_mail_host    = false;     // actualy make sure the mail host provided in email is valid/accessible host.
$limit_acc_per_ip      = false;     // true = limit to one account per IP
$simple_register       = false;     // Sets the registration to a simple form. Name, Password, Expansion and Email.

// this option will limit account creation to users from selected net range(s).
// allow all => empty array
// e.g: "120-122.55.255-0.255-0"
$valid_ip_mask = array
(
  // "255-0.255-0.255-0.255-0",
);

//#############################################################################
//---- Layout configuration ----

$title               = "MiniManager for Mangos/Trinity Server";
$itemperpage         = 25;
$showcountryflag     = true;

$css_template        = "Sulfur";    // file/folder name of css tamplate to use from templates directory by default
$language            = "english";   // default site language
$timezone            = "UTC";       // default timezone (use your local timezone code)
$gm_online           = "1";         // display GM Characters in the Online Character List (1 = enable, 0 = disable)
$gm_online_count     = "1";         // include GM Characters in the Online User Count (1 = enable, 0 = disable)
$motd_display_poster = "1";         // display the poserter info in the MOTD (1 = enable, 0 = disable)


//#############################################################################
//---- External Links ----

$tt_lang                    = "www";  // wowhead tooltip language. choices are 'fr', 'de', 'es' (for 'en' use www)
$item_datasite              = "http://$tt_lang.wowhead.com/?item=";
$quest_datasite             = "http://$tt_lang.wowhead.com/?quest=";
$creature_datasite          = "http://$tt_lang.wowhead.com/?npc=";
$spell_datasite             = "http://$tt_lang.wowhead.com/?spell=";
$skill_datasite             = "http://$tt_lang.wowhead.com/?spells=";
$talent_datasite            = "http://$tt_lang.wowhead.com/?spell=";
$go_datasite                = "http://$tt_lang.wowhead.com/?object=";
$talent_calculator_datasite = "http://www.worldofwarcraft.com/info/classes";
$get_icons_from_web         = false; //wherever to get icons from the web in case they are missing in /img/INV dir.


//#############################################################################
//---- Active Translations
// 0 = English/Default; 1 = Korean; 2 = French; 4 = German; 8 = Chinese; 16 = Taiwanese; 32 = Spanish; 64 = Mexican; 128 = Russian
// Prototype for search options
// Show only on language search option active translations entries (locales_XXX)
// Example (use flag values by adding the values) : Korean (1) + German (4) + Russian (64) = 69
// NOTE : Righ now only for Creature.php

$locales_search_option = 0;  // No search option, don't use locales_XXX for search
$site_encoding = "utf-8";    // used charset

//#############################################################################
//---- Backup configuration ----

$backup_dir = "./backup";    // make sure webserver have the permission to write/read it!

//#############################################################################
//---- HTTP Proxy Configuration ----

$proxy_cfg = Array
(
  'addr' => "",              // configure only if requierd
  'port' => 80,
  'user' => "",
  'pass' => ""
);


//#############################################################################
// ---- Module and Security settings ----
// --   Meaning of the columns : TARGET, LANG_TEXT, ( READ/VIEW , INSERT , UPDATE , DELETE ) min Permission GM LEVEL
// --   Files excluded for this : Login.php, Pomm.php 
// --   - Both files don't use header.php, so we can't include this method.. but its not a big deal
//
// --   - Updates will follow
// -- 
// --   Example: array("item.php", 'items',0,1,2,3),
// --    level 0 can only view and search,
// --    level 1 can add new items but cannot edit,
// --    level 2 can add and edit but cannot delete,
// --    level 3 has full access

// --   Example: array("item.php", 'items',1,0,3,2),
// --    this is tricky,
// --    level 0 would have no access
// --    level 1 can only search and browse
// --    level 2 can delete items, but cannot add or edit
// --    level 3 can add and edit, but cannot delete

$menu_array = Array
(
  array("index.php", 'main', array
  (
    array("ahstats.php", 'auctionhouse',0,5,5,5),    // new security system implemented
    array("arenateam.php", 'arena_teams',0,5,5,5),   // has own level security, but has yet to honor the new security system.
    array("guild.php", 'guilds',0,5,5,5),            // has own level security, but has yet to honor the new security system.
    array("honor.php", 'honor',0,5,5,5),             // this page is a read-only page, but links to other pages, please use with caution
    array("stat.php", 'statistics',0,5,5,5),         // new security system implemented
    array("events.php", 'events',0,5,5,5),           // new security system implemented
    array("instances.php", 'instances',0,5,5,5),     // new security system implemented
    array("top100.php", 'top100',0,5,5,5),           // this page is a read-only page, but links to other pages, please use with caution
    array("javascript:void(0);\" onclick=\"window.open('./pomm/pomm.php?realmid=".(empty($_SESSION)?"":$_SESSION['realm_id'])."', 'pomm', 'Toolbar=0, Location=0, Directories=0, Status=0, Menubar=0, Scrollbar=0, Resizable=0, Copyhistory=1, Width=966, Height=732')", 'player_map',0,5,5,5),
  ),
),
  array("#", 'tools', array
  (
    array("user.php", 'accounts',0,5,5,5),           // new security system implemented
    array("char_list.php", 'characters',0,5,5,5),    // new security system implemented
    array("command.php", 'command',0,5,5,5),         // has own level security, but has yet to honor the new security system.
    array("banned.php", 'banned_list',5,5,5,5),      // new security system implemented
    array("mail.php", 'mail',0,5,5,5),               // new security system implemented
    array("ticket.php", 'tickets',5,5,5,5),          // this page has yet to honor the new security system, please use with caution.
    array("tele.php", 'teleports',0,5,5,5),          // new security system implemented
    array("cleanup.php", 'cleanup',5,5,5,5),         // new security system implemented
    array("ssh.php", 'ssh_line',0,5,5,5),            // new security system implemented
    array("run_patch.php", 'run_sql_patch',0,5,5,5), // new security system implemented
    array("realm.php", 'realm',0,5,5,5),             // new security system implemented
    array("motd.php", 'add_motd',0,5,5,5),           // new security system implemented
    array("irc.php", 'irc',0,5,5,5),                 // new security system implemented
  ),
),
  array("#", 'db', array
  (
    array("item.php", 'items',0,5,5,5),              // new security system implemented
    array("creature.php", 'creatures',5,5,5,5),      // this page has yet to honor the new security system, please use with caution.
    array("game_object.php", 'game_object',5,5,5,5), // this page has yet to honor the new security system, please use with caution.
    array("backup.php", 'backup',5,5,5,5),           // this page has yet to honor the new security system, please use with caution.
    array("repair.php", 'repair',0,5,5,5),           // new security system implemented
  ),
),
  array("forum.php", 'forums', array
  (
  ),
),
  array("#", 'invisible', array
  (
    array("forum.php", 'forums',0,5,5,5),            // has own level security, but has yet to honor the new security system.
    array("javascript:void(0);\" onclick=\"window.open('./forum.html', 'forum')", 'forums',0,0,0,0),
    array("char.php", 'character',0,5,5,5),          // new security system implemented
    array("char_edit.php", 'char_edit',0,5,5,5),     // new security system implemented
    array("edit.php", 'MyAccount',0,5,5,5),          // new security system implemented
    array("index.php", 'Startpage',0,5,5,5),
    array("stat_on.php", 'statistics',0,5,5,5),      // new security system implemented
    array("realm.php", 'realm',0,5,5,5),             // this last one is special, if this is not here, users are unable to switch realms
  ),                                                 // if READ is set to level 3, only level 3 and above can switch realms.
),                                                   // INSERT, UPDATE and DELETE should have no effect, but best to keep it at 5.
);


$debug = false; //set to true if full php debugging requierd.

?>
