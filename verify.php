<?php

// page header, and any additional required libraries
require_once("header.php");

//##############################################################################################
// MAIN
//##############################################################################################

$username = (isset($_GET['username'])) ? $_GET['username'] : NULL;
$authkey = (isset($_GET['authkey'])) ? $_GET['authkey'] : NULL;

$output .= "
<div class=\"top\">";

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

$query = $sqlm->query("SELECT * 
						FROM mm_account_verification 
						WHERE username = '$username' AND authkey = '$authkey'");

$lang_verify = lang_verify();

if ($sqlm->num_rows($query) < 1) 
{
$output .= "
	<h1><font class=\"error\">{$lang_verify['verify_failed']}</font></h1>";
}
else 
{
$output .= "<h1><font class=\"error\">{$lang_verify['verify_success']}</font></h1>";

$sqlr = new SQL;
$sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

	$data = $sqlm->fetch_array($query);
	list($id,$username,$pass,$gmlevel,$session,$v,$s,$email,$joindate,$last_ip,$failed_logins,$locked,$last_login,$active,$expansion) = $data;

	$sqlr->query("
		INSERT INTO account
			(id,
			username,
			sha_pass_hash,
			gmlevel,
			sessionkey,
			v,
			s,
			email,
			joindate,
			last_ip,
			failed_logins,
			locked,
			last_login,			
			active_realm_id,
			expansion)
		VALUES
			('',
			UPPER('$username'),
			'$pass',
			0,
			'',
			'',
			'',
			'$email'
			,now(),
			'$last_ip',
			0,
			$create_acc_locked,
			NULL,
			0,
			$expansion)");

	$result = $sqlr->query("
		SELECT * 
		FROM account 
		WHERE username='$username'");

}

	$sqlm->query("
		DELETE FROM mm_account_verification 
		WHERE username='$username'");

$output .= "
</div>";
$output .= "
<center>
<br />
<table class=\"hidden\">
	<tr>
		<td>".makebutton($lang_global['home'], 'index.php', 130)."</td>
	</tr>
</table>
</center>";

require_once("footer.php");

?>