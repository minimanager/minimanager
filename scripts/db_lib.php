<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

switch ($db_type)
{
  case 'MySQL':
    require_once("db_lib/mysql.php");
    break;
  case 'PgSQL':
    require_once("db_lib/pgsql.php");
    break;
  case 'MySQLi':
    require_once("db_lib/mysqli.php");
    break;
  case 'SQLLite':
    require_once("db_lib/sqlite.php");
    break;
  default:
    exit("<center /><br /><code />'$db_type'</code> is not a valid database type.<br> Please check settings in <code>'./scripts/config.php'</code>.</center>");
    break;
}

?>