<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */
 
require_once("header.php");

//#################################################################################################
// Login
//#################################################################################################
function dologin(){
global $lang_global, $realm_db;

 if ( empty($_POST['user']) || empty($_POST['pass']) ) redirect("login.php?error=2");

 $sql = new SQL;
 $link = $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $user_name  = $sql->quote_smart($_POST['user']);
 $user_pass  = $sql->quote_smart($_POST['pass']);

 if (strlen($user_name) > 255 || strlen($user_pass) > 255) redirect("login.php?error=1");
 
 $result = $sql->query("SELECT id,gmlevel,username FROM account WHERE username='$user_name' AND sha_pass_hash='$user_pass' ");

 if ($sql->num_rows($result) == 1) {
	$id = $sql->result($result, 0, 'id');
	$result1 = $sql->query("SELECT count(*) FROM account_banned WHERE id ='$id' AND active = '1'");
	if ($sql->result($result1, 0)) {
		$sql->close();
		redirect("login.php?error=3");
	  } else {
			$_SESSION['user_id'] = $id;
			$_SESSION['uname'] = $sql->result($result, 0, 'username');
			$_SESSION['user_lvl'] = $sql->result($result, 0, 'gmlevel');
			$_SESSION['realm_id'] = $sql->quote_smart($_POST['realm']);
			$_SESSION['client_ip'] = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');

			 if (isset($_POST['remember'])&&$_POST['remember'] != ''){
					setcookie("uname", $_SESSION['uname'], time()+60*60*24*7);
					setcookie("realm_id", $_SESSION['realm_id'], time()+60*60*24*7);
					setcookie("p_hash", $user_pass, time()+60*60*24*7);
				}
				$sql->close();
				redirect("index.php");
			}
   } else {
		$sql->close();
		redirect("login.php?error=1");
	}
}

//#################################################################################################
// Print login form
//#################################################################################################
function login(){
global $lang_global, $lang_login, $output, $realm_db;

 $output .= "<center>
 <script type=\"text/javascript\" src=\"js/sha1.js\"></script>
 <script type=\"text/javascript\">
		function dologin () {
			document.form.pass.value = hex_sha1(document.form.user.value.toUpperCase()+':'+document.form.login_pass.value.toUpperCase());
			document.form.login_pass.value = '0';
			do_submit();
		}
 </script>
 <fieldset class=\"half_frame\">
	<legend>{$lang_login['login']}</legend>
   <form method=\"post\" action=\"login.php?action=dologin\" name=\"form\" onsubmit=\"return dologin()\">
   <input type=\"hidden\" name=\"pass\" value=\"\" maxlength=\"256\" />
   <table class=\"hidden\">
    <tr align=\"left\">
		<td colspan=\"2\">{$lang_login['username']}:</td>
	</tr>
	<tr align=\"left\">
		<td colspan=\"2\"><input type=\"text\" name=\"user\" size=\"40\" maxlength=\"16\" /></td>
	</tr>
	<tr align=\"left\">
		<td colspan=\"2\">{$lang_login['password']}:</td>
	</tr>
	<tr align=\"left\">
		<td><input type=\"password\" name=\"login_pass\" size=\"24\" maxlength=\"40\" /></td>
		<td><input type=\"submit\" value=\"\" style=\"display:none\" />";
	makebutton($lang_login['login'], "javascript:dologin()",100);
	 $sql = new SQL;
 $link = $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
 $result = $sql->query("SELECT id,name FROM `realmlist` LIMIT 10");

 if ($sql->num_rows($result)>1){
	$output .= "<tr align=\"left\">
			<td colspan=\"2\">{$lang_login['select_realm']} :
			 <select name=\"realm\">";
			 
	while ($realm = $sql->fetch_row($result))
		$output .= "<option value=\"$realm[0]\">$realm[1]</option>";

	$output .= "</select></td></tr>";
	} else {
		$output .= "<input type=\"hidden\" name=\"realm\" value=\"".$sql->result($result, 0, 'id')."\" />";
		}
 $sql->close();
 
 $output .= "</td>
	</tr>";

 $output .= "<tr align=\"left\">
		<td colspan=\"2\"><input type=\"checkbox\" name=\"remember\" value=\"1\" checked=\"checked\" /> - {$lang_login['remember_me']}</td>
	</tr>
	<tr><td colspan=\"2\"><hr /></td></tr>
	<tr align=\"left\">
		<td><a href=\"form.php\">{$lang_login['not_registrated']}</a></td>
		<td><a href=\"register.php?action=pass_recovery\">{$lang_login['pass_recovery']}</a></td>
	</tr>
   </table>
   <script type=\"text/javascript\">
	<!--
		document.form.user.focus();
	//-->
	</script>
   </form><br /></fieldset><br /><br />
</center>";
}


//#################################################################################################
// Login via set cookie
//#################################################################################################
function do_cookie_login(){
 global $lang_global, $realm_db;

 if ( empty($_COOKIE['uname']) || empty($_COOKIE['p_hash']) || empty($_COOKIE['realm_id'])) redirect("login.php?error=2");

 $sql = new SQL;
 $link = $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
 $user_name = $sql->quote_smart($_COOKIE['uname']);
 $user_pass  = $sql->quote_smart($_COOKIE['p_hash']);

 $result = $sql->query("SELECT username,gmlevel,id FROM account WHERE username='$user_name' AND sha_pass_hash='$user_pass'");

 if ($sql->num_rows($result)) {
 	$id = $sql->result($result, 0, 'id');
	$result1 = $sql->query("SELECT count(*) FROM account_banned WHERE id ='$id'");
	if ($sql->result($result1, 0)) {
		$sql->close();
		redirect("login.php?error=3");
	  } else {
			$_SESSION['user_id'] = $id;
			$_SESSION['uname'] = $sql->result($result, 0, 'username');
			$_SESSION['user_lvl'] = $sql->result($result, 0, 'gmlevel');
			$_SESSION['realm_id'] = $sql->quote_smart($_COOKIE['realm_id']);
			$_SESSION['client_ip'] = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
					
			$sql->close();
   			redirect("index.php");
		 }
 } else {
	$sql->close();
	setcookie ("uname", "", time() - 3600);
	setcookie ("realm_id", "", time() - 3600);
	setcookie ("p_hash", "", time() - 3600);
	redirect("login.php?error=1");
	}
}


//#################################################################################################
// MAIN
//#################################################################################################
if (isset($_COOKIE["uname"]) && isset($_COOKIE["p_hash"]) && isset($_COOKIE["realm_id"]) && !isset($_GET['error'])) {
	do_cookie_login();
 }

$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "<div class=\"top\">";
switch ($err) {
case 1:
   $output .=  "<h1><font class=\"error\">{$lang_login['bad_pass_user']}</font></h1>";
   break;
case 2:
   $output .=  "<h1><font class=\"error\">{$lang_login['missing_pass_user']}</font></h1>";
   break;
case 3:
   $output .=  "<h1><font class=\"error\">{$lang_login['banned_acc']}</font></h1>";
   break;
case 5:
   $output .=  "<h1><font class=\"error\">{$lang_login['no_permision']}</font></h1>";
   break;
default: //no error
    $output .=  "<h1>{$lang_login['enter_valid_logon']}</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action){
case "dologin":
   dologin();
   break;
default:
   login();
}

require_once("footer.php");
?>
