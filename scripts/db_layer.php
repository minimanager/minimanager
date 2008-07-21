<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */
switch ($db_type){
	case 'MySQL':
		require_once("db_layer/mysql.php");
		break;
	case 'PgSQL':
		require_once("db_layer/pgsql.php");
		break;
	case 'MySQLi':
		require_once("db_layer/mysqli.php");
		break;
	case 'SQLLite':
		require_once("db_layer/sqlite.php");
		break;
	default:
		error("'$db_type' is not a valid database type. Please check settings in config.php.");
		break;
}
?>