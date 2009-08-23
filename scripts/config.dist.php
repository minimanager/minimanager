<?php


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


//#############################################################################
//---- Version Information ----

$show_version = array
(
  'show'        =>  '1',    // 0 - Don't Show, 1 - Show Version, 2 - Show Version and SVN Revision
  'version'     =>  '0.14',
  'version_lvl' => '-1',    // Minimum account level to show Version to, -1 is guest account
  'svnrev'      =>  '0',    // SVN Revision will be read from .svn folder, values here hold no meaning or effect
  'svnrev_lvl'  =>  '5',    // Minimum account level to show SVN Revision to.
);


//#############################################################################
//---- SQL Configuration ----
//
//  SQL server type  :
//  'MySQL'   - Mysql
//  'PgSQL'   - PostgreSQL
//  'MySQLi'  - MySQLi
//  'SQLLite' - SQLLite

$db_type          = 'MySQL';

$sql_search_limit =  100;           // limit number of maximum search results

$mmfpm_db = array
(
  'addr'       => '127.0.0.1:3306', // SQL server IP:port this DB located on
  'user'       => 'root',           // SQL server login this DB located on
  'pass'       => '1',              // SQL server pass this DB located on
  'name'       => 'mmfpm',          // MiniManager DB name
  'encoding'   => 'utf8'            // SQL connection encoding
);

$realm_db = array
(
  'addr'       => '127.0.0.1:3306', // SQL server IP:port this realmd located on
  'user'       => 'root',           // SQL server login this realmd located on
  'pass'       => '1',              // SQL server pass this realmd located on
  'name'       => 'realmd',         // realmd DB name
  'encoding'   => 'utf8'            // SQL connection encoding
);

$world_db = array
(
  1 => array
  (                                 // position in array must represent realmd ID
    'id'       =>  1,               // Realm ID
    'addr'     => '127.0.0.1:3306', // SQL server IP:port this DB located on
    'user'     => 'root',           // SQL server login this DB located on
    'pass'     => '1',              // SQL server pass this DB located on
    'name'     => 'mangos',         // World Database name, by default "mangos" for MaNGOS, "world" for Trinity
    'encoding' => 'utf8'            // SQL connection encoding
  ),
);

$characters_db = Array
(
  1 => array
  (                                 // position in array must represent realmd ID
    'id'       =>  1,               // Realm ID
    'addr'     => '127.0.0.1:3306', // SQL server IP:port this DB located on
    'user'     => 'root',           // SQL server login this DB located on
    'pass'     => '1',              // SQL server pass this DB located on
    'name'     => 'characters',     // Character Database name
    'encoding' => 'utf8',           // SQL connection encoding
  ),                                // NOTE: THIS USER MUST HAVE AT LEAST READ ACCESS ON THE WORLD DATABASE
);


//#############################################################################
//---- Game Server Configuration ----

$server_type        =  0;           // 0=MaNGOS, 1=Trinity

$server = array
(                                   // if more than one realm used, even if they are on same system new subarray MUST be added.
  1 => array
  (                                 // position in array must represent realmd ID, same as in $world_db
    'addr'          => '127.0.0.1', // Game Server IP, as seen by MiniManager, from your webhost
    'addr_wan'      => '127.0.0.1', // Game Server IP, as seen by clients - Must be external address
    'game_port'     =>  8085,       // Game Server port
    'term_type'     => 'SSH',       // Terminal type - ("SSH"/"Telnet")
    'term_port'     =>  22,         // Terminal port
    'telnet_port'   =>  3443,       // Telnet port - Telnet settins are needed for sending InGame Mail.
    'telnet_user'   => 'USER',      // Telnet username, must be all CAPS
    'telnet_pass'   => 'pass',      // Telnet password
    'rev'           => 'rev. ',     // MaNGOS rev. used (Trinity does not need this)
    'both_factions' =>  true,       // Allow to see opponent faction characters. Affects only players.
    'talent_rate'   =>  1,          // Talent rate set for this server, needed for talent point calculation
  ),
);


//#############################################################################
//---- Mail configuration ----

$admin_mail  = 'mail@mail.com';     // mail used for bug reports and other user contact
$mailer_type = 'smtp';              // type of mailer to be used("mail", "sendmail", "smtp")
$from_mail   = 'mail@mail.com';     // all emails will be sent from this email

//smtp server config
$smtp_cfg = array
(
  'host'     => 'smtp.mail.com',    // smtp server
  'port'     =>  25,                // port
  'user'     => '',                 // username - use only if auth. required
  'pass'     => ''                  // pass
);


//#############################################################################
//---- IRC Options ------

$irc_cfg = array
(
  'server'  => 'mangos.cjb.net',    // irc server
  'port'    =>  6667,               // port
  'channel' => 'minimanager'        // channel
);


//#############################################################################
//---- HTTP Proxy Configuration ----

$proxy_cfg = array
(
  'addr' => '',              // configure only if requierd
  'port' => 80,
  'user' => '',
  'pass' => ''
);


//#############################################################################
//---- External Links ----

$tt_lang                    = 'www'; // wowhead tooltip language. choices are 'fr', 'de', 'es', 'ru' (for 'en' use www)
$item_datasite              = 'http://'.$tt_lang.'.wowhead.com/?item=';
$quest_datasite             = 'http://'.$tt_lang.'.wowhead.com/?quest=';
$creature_datasite          = 'http://'.$tt_lang.'.wowhead.com/?npc=';
$spell_datasite             = 'http://'.$tt_lang.'.wowhead.com/?spell=';
$skill_datasite             = 'http://'.$tt_lang.'.wowhead.com/?spells=';
$go_datasite                = 'http://'.$tt_lang.'.wowhead.com/?object=';
$achievement_datasite       = 'http://'.$tt_lang.'.wowhead.com/?achievement=';
$talent_calculator_datasite = 'http://www.wowarmory.com/talent-calc.xml?cid=';
$get_icons_from_web         =  false;           // wherever to get icons from the web.
$item_icons                 = 'img/item_icons'; // and this is where it will save to and get from.


//#############################################################################
//---- New account creation Options ----

$disable_acc_creation  = false;     // true = Do not allow new accounts to be created
$expansion_select      = true;      // true = Shows option to select expansion or classic. (false = no option, WOTLK enabled by default)
$defaultoption         = 2;         // if the above is false then set what the default option will be (2 = WOTLK, 1 = TBC, 0 = Classic)
$enable_captcha        = false;     // false = no security image check (enable for protection against 'bot' registrations)
                                    // captcha needs php GD & FreeType Library support
$send_mail_on_creation = false;     // true = send mail at account creation.
$create_acc_locked     = 0;         // if set to '1' newly created accounts will be made locked to registered IP, disallowing user to login from other IPs.
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
//---- Login Options ----

$remember_me_checked  = false;     // "Remember Me" cookie check box default, false = unchecked

$allow_anony         =  true;      // allow anonymouse login, aka guest account
$anony_uname         = 'Guest';    // guest account name, this is purely cosmetic
$anony_realm_id      =  1;         // guest account default realm

// permission level for guest access is -1
// set it like how you set any page permission level in menu config below, using the value -1
// the "Guest" account exists only in MiniManager, not in your realms or server or database

//#############################################################################
//---- Layout configuration ----

$title               = 'MiniManager for Mangos/Trinity Server';
$itemperpage         =  25;
$showcountryflag     =  true;

$theme               = 'Sulfur';    // file/folder name of theme to use from themes directory by default
$language            = 'english';   // default site language
$timezone            = 'UTC';       // default timezone (use your local timezone code)
$gm_online           = '1';         // display GM Characters in the Online Character List and Player Map (1 = enable, 0 = disable)
$gm_online_count     = '1';         // include GM Characters in the Online User Count and Player Map (1 = enable, 0 = disable)
$motd_display_poster = '1';         // display the poserter info in the MOTD (1 = enable, 0 = disable)


//#############################################################################
//---- Player Map configuration ----

// GM online options
$map_gm_show_online_only_gmoff     = 0; // show GM point only if in '.gm off' [1/0]
$map_gm_show_online_only_gmvisible = 0; // show GM point only if in '.gm visible on' [1/0]
$map_gm_add_suffix                 = 1; // add '{GM}' to name [1/0]
$map_status_gm_include_all         = 1; // include 'all GMs in game'/'who on map' [1/0]

// status window options:
$map_show_status =  1;                  // show server status window [1/0]
$map_show_time   =  1;                  // Show autoupdate timer 1 - on, 0 - off
$map_time        = 24;                  // Map autoupdate time (seconds), 0 - not update.

// all times set in msec (do not set time < 1500 for show), 0 to disable.
$map_time_to_show_uptime    = 3000;     // time to show uptime string
$map_time_to_show_maxonline = 3000;     // time to show max online
$map_time_to_show_gmonline  = 3000;     // time to show GM online


//#############################################################################
//---- Active Translations
// 0 = English/Default; 1 = Korean; 2 = French; 4 = German; 8 = Chinese; 16 = Taiwanese; 32 = Spanish; 64 = Mexican; 128 = Russian
// Prototype for search options
// Show only on language search option active translations entries (locales_XXX)
// Example (use flag values by adding the values) : Korean (1) + German (4) + Russian (64) = 69
// NOTE : Righ now only for Creature.php

$locales_search_option =  0;         // No search option, don't use locales_XXX for search
$site_encoding         = 'utf-8';    // used charset


//#############################################################################
//---- Backup configuration ----

$backup_dir = 'backup';    // make sure webserver have the permission to write/read it!


//#############################################################################
//---- Account Levels ----

$gm_level_arr = array
(
 -1 => array(-1,      'Guest',      '',''),
  0 => array( 0,     'Player',      '',''),
  1 => array( 1,  'Moderator',   'Mod',''),
  2 => array( 2, 'Gamemaster',    'GM',''), // change the name and alias as required
  3 => array( 3, 'BugTracker',    'BT',''),
  4 => array( 4,      'Admin', 'Admin',''),
  5 => array( 5,      'SysOp', 'SysOp',''),
  6 => array( 6,    'Unknown',   'UnK',''), // Add additional levels as required
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
//
// --   Example: array("item.php", 'items',1,0,3,2),
// --    this is tricky,
// --    level 0 would have no access
// --    level 1 can only search and browse
// --    level 2 can delete items, but cannot add or edit
// --    level 3 can add and edit, but cannot delete


$menu_array = array
(
  array
  (               'index.php',         'main', array
    (
      array(    'ahstats.php', 'auctionhouse', 0,5,5,5), // new security system implemented
      array(  'arenateam.php',  'arena_teams', 0,5,5,5), // has own level security, but has yet to honor the new security system.
      array(      'guild.php',       'guilds', 0,5,5,5), // new security system implemented
      array(      'honor.php',        'honor', 0,5,5,5), // new security system implemented
      array(       'stat.php',   'statistics', 0,5,5,5), // new security system implemented
      array(     'events.php',       'events', 0,5,5,5), // new security system implemented
      array(  'instances.php',    'instances', 0,5,5,5), // new security system implemented
      array(     'top100.php',       'top100', 0,5,5,5), // new security system implemented
      array('javascript:void(0);" onclick="window.open
        (\'map/\', \'map\', \'toolbar=0, location=0, directories=0, status=0, menubar=0, scrollbars=no, resizable=no, copyhistory=1, width=966, height=732\')',
                                 'player_map', 0,5,5,5), // this page has yet to honor the new security system, but it is a read only page
    ),
  ),
  array
  (                      '#',         'tools', array
    (
      array(      'user.php',      'accounts', 0,5,5,5), // new security system implemented
      array( 'char_list.php',    'characters', 0,5,5,5), // new security system implemented
      array(   'command.php',       'command', 0,5,5,5), // new security system implemented
      array(    'banned.php',   'banned_list', 0,5,5,5), // new security system implemented
      array(      'mail.php',          'mail', 0,5,5,5), // new security system implemented
      array(    'ticket.php',       'tickets', 0,5,5,5), // new security system implemented
      array(      'tele.php',     'teleports', 0,5,5,5), // new security system implemented
      array(   'cleanup.php',       'cleanup', 5,5,5,5), // new security system implemented
      array(       'ssh.php',      'ssh_line', 0,5,5,5), // new security system implemented
      array( 'run_patch.php', 'run_sql_patch', 0,5,5,5), // new security system implemented
      array(     'realm.php',         'realm', 0,5,5,5), // new security system implemented
      array(      'motd.php',      'add_motd', 0,5,5,5), // new security system implemented
      array(       'irc.php',           'irc', 0,5,5,5), // new security system implemented
      array(    'spelld.php',        'spelld', 0,5,5,5), // new security system implemented
    ),
  ),
  array
  (                        '#',          'db', array
    (
      array(        'item.php',       'items', 0,5,5,5), // new security system implemented
      array(    'creature.php',   'creatures', 5,5,5,5), // this page has yet to honor the new security system, please use with caution.
      array( 'game_object.php', 'game_object', 5,5,5,5), // this page has yet to honor the new security system, please use with caution.
      array(      'backup.php',      'backup', 5,5,5,5), // this page has yet to honor the new security system, please use with caution.
      array(      'repair.php',      'repair', 0,5,5,5), // new security system implemented
    ),
  ),
  array
  (                'forum.php',      'forums', array
    (
    ),
  ),
  array
  (                        '#',   'invisible', array
    (
      array('javascript:void(0);" onclick="window.open(\'./forum.html\', \'forum\')', 'forums',0,0,0,0),
      array(       'forum.php',      'forums', 0,5,5,5), // has own level security, but has yet to honor the new security system.
      array(        'char.php',   'character', 0,5,5,5), // new security system implemented
      array(    'char_inv.php',   'character', 0,5,5,5), // new security system implemented
      array(  'char_quest.php',   'character', 0,5,5,5), // new security system implemented
      array('char_achieve.php',   'character', 0,5,5,5), // new security system implemented
      array(  'char_skill.php',   'character', 0,5,5,5), // new security system implemented
      array( 'char_talent.php',   'character', 0,5,5,5), // new security system implemented
      array(    'char_rep.php',   'character', 0,5,5,5), // new security system implemented
      array(   'char_pets.php',   'character', 0,5,5,5), // new security system implemented
      array('char_friends.php',   'character', 0,5,5,5), // new security system implemented
      array(   'char_edit.php',   'char_edit', 0,5,5,5), // new security system implemented
      array(        'edit.php',   'MyAccount', 0,5,5,5), // new security system implemented
      array(       'index.php',   'Startpage',-1,5,5,5), // new security system implemented
      array(   'guildbank.php',   'guildbank', 0,5,5,5), // under development
      array(       'realm.php',       'realm', 0,5,5,5), // this last one is special, if this is not here, users are unable to switch realms
    ),                                                   // if READ is set to level 3, only level 3 and above can switch realms.
  ),                                                     // INSERT, UPDATE and DELETE should have no effect, but best to keep it at 5.
);


$debug = 0; // 0 - no debug, only fatal errors.
            // 1 - show total queries, mem usage, and only fatal errors.
            // 2 - show total queries, mem usage, and all errors.
            // 3 - show total queries, mem usage, all errors, and list of all global vars.
            // 4 - show total queries, mem usage, all errors, list of all global vars, and values of all global vars.


//#############################################################################
//---- Under Development ----
//
// These are either place holders for future stuff
// or stuff that are currently under development
// do not set or change any of these in here or in config.php
// unless you know what you are doing or were being told to do so
// no support are given to these 'features'

$developer_test_mode =  false;

$multi_realm_mode    =  true;


?>
