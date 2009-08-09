<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */


if ( !ini_get('session.auto_start') )
  session_start();

session_destroy();

unset($_SESSION['user_id']);
unset($_SESSION['uname']);
unset($_SESSION['user_lvl']);
unset($_SESSION['realm_id']);
unset($_SESSION['client_ip']);
unset($_SESSION['logged_in']);

setcookie ("uname", "", time() - 3600);
setcookie ("realm_id", "", time() - 3600);
setcookie ("p_hash", "", time() - 3600);

if ( strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') === false )
{
  header("Location: login.php");
  exit();
}
else
  die('<meta http-equiv="refresh" content="0;URL=login.php" />');

?>
