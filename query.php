<?php
// security check only accept querys when reffer is our site
// in any other case someone probaly tries to inject
if(!isset($_SERVER['HTTP_REFERER'])) die("0~wtf?");
else
{
	$domain = parse_url($_SERVER['HTTP_REFERER']);
	if($domain["host"] != $_SERVER['HTTP_HOST'])
		die("0~wtf?");
}

$time_start = microtime(true);
// resuming login session if available, or start new one
if (ini_get('session.auto_start'));
else session_start();

if (file_exists('scripts/config.php'))
{
  if (file_exists('scripts/config.dist.php'))
    require_once 'scripts/config.dist.php';
  else
    exit('<center><br><code>\'scripts/config.dist.php\'</code> not found,<br>
          please restore <code>\'scripts/config.dist.php\'</code></center>');
  require_once 'scripts/config.php';
}
else
  die('Error');

require_once 'libs/db_lib.php';

$sqlr = new SQL;
$sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

//---------------------Error reports for Debugging-----------------------------
if ($debug) $tot_queries = 0;
if (1 < $debug)
  error_reporting (E_ALL);
else
  error_reporting (E_COMPILE_ERROR);



//#############################################################################
// Login
//#############################################################################
function dologin(&$sqlr)
{
	$err = 0;
	
  if (empty($_POST['user']) || empty($_POST['pass']))
  {
	echo('200~Please enter your username and password');
	return;
  }

  $user_name  = $sqlr->quote_smart($_POST['user']);
  $user_pass  = $sqlr->quote_smart($_POST['pass']);

  if (255 < strlen($user_name) || 255 < strlen($user_pass))
  {
	echo('210~Too many characters used.');
	return;
  }
	
  $result = $sqlr->query('SELECT id, gmlevel, username FROM account WHERE username = \''.$user_name.'\' AND sha_pass_hash = \''.$user_pass.'\'');

  unset($user_name);

  if (1 == $sqlr->num_rows($result))
  {
    $id = $sqlr->result($result, 0, 'id');
    if ($sqlr->result($sqlr->query('SELECT count(*) FROM account_banned WHERE id = '.$id.' AND active = \'1\''), 0))
  {
	echo('220~Authentication failed.');
  }
    else
    {
      $_SESSION['user_id']   = $id;
      $_SESSION['uname']     = $sqlr->result($result, 0, 'username');
      $_SESSION['user_lvl']  = $sqlr->result($result, 0, 'gmlevel');
      $_SESSION['realm_id']  = $sqlr->quote_smart($_POST['realm']);
      $_SESSION['client_ip'] = (isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
      $_SESSION['logged_in'] = true;

      if (isset($_POST['remember']) && $_POST['remember'] != '')
      {
        setcookie(   'uname', $_SESSION['uname'], time()+60*60*24*7);
        setcookie('realm_id', $_SESSION['realm_id'], time()+60*60*24*7);
        setcookie(  'p_hash', $user_pass, time()+60*60*24*7);
      }
      	echo '100~index.php';
    }
  }
  else
  {
      echo '300~Authentication failed.';
  }
}

dologin($sqlr);

$sqlr->close();

?>