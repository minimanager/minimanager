<?php


require_once 'header.php';

//#############################################################################
// Login
//#############################################################################
function dologin(&$sqlr)
{
  if (empty($_POST['user']) || empty($_POST['pass']))
    redirect('login.php?error=2');

  $user_name  = $sqlr->quote_smart($_POST['user']);
  $user_pass  = $sqlr->quote_smart($_POST['pass']);

  if (255 < strlen($user_name) || 255 < strlen($user_pass))
    redirect('login.php?error=1');

  $result = $sqlr->query('SELECT id, gmlevel, username FROM account WHERE username = \''.$user_name.'\' AND sha_pass_hash = \''.$user_pass.'\'');

  unset($user_name);

  if (1 == $sqlr->num_rows($result))
  {
    $id = $sqlr->result($result, 0, 'id');
    if ($sqlr->result($sqlr->query('SELECT count(*) FROM account_banned WHERE id = '.$id.' AND active = \'1\''), 0))
    {
      redirect('login.php?error=3');
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
      redirect('index.php');
    }
  }
  else
  {
    redirect('login.php?error=1');
  }
}


//#################################################################################################
// Print login form
//#################################################################################################
function login(&$sqlr)
{
  global $output, $lang_login,
    $characters_db, $server, $remember_me_checked;

  $output .= '
          <center>
		  

		  
		  <script type="text/javascript" src="libs/js/jquery.js"></script>
		  <script type="text/javascript" src="libs/js/login.js"></script>
            <script type="text/javascript" src="libs/js/sha1.js"></script>
            <script type="text/javascript">
              // <![CDATA[
                function dologin1 ()
                {
                  document.form.pass.value = hex_sha1(document.form.user.value.toUpperCase()+":"+document.form.login_pass.value.toUpperCase());
                  document.form.login_pass.value = "0";
                  do_submit();
                }
              // ]]>
            </script>
            <fieldset class="half_frame">
			 <table id="message" style="display:none;" width="300" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><strong>An error occured</strong> </td>
  </tr>
</table>
              <legend>'.$lang_login['login'].'</legend>
              <form method="post" action="login.php?action=dologin" name="form" onsubmit="return dologin()">
                <input type="hidden" name="pass" value="" maxlength="256" />
                <table class="hidden">
                  <tr>
                    <td>
                      <hr />
                    </td>
                  </tr>
                  <tr align="right">
                    <td>'.$lang_login['username'].' : <input type="text" id="login_user" name="user" size="24" maxlength="16" /></td>
                  </tr>
                  <tr align="right">
                    <td>'.$lang_login['password'].' : <input type="password" id="login_pass" name="login_pass" size="24" maxlength="40" /></td>
                  </tr>';

  $result = $sqlr->query('SELECT id, name FROM realmlist LIMIT 10');

  if ($sqlr->num_rows($result) > 1 && (count($server) > 1) && (count($characters_db) > 1))
  {
    $output .= '
                  <tr align="right">
                    <td>'.$lang_login['select_realm'].' :
                      <select name="realm" id="realm">';
    while ($realm = $sqlr->fetch_assoc($result))
      if(isset($server[$realm['id']]))
        $output .= '
                        <option value="'.$realm['id'].'">'.htmlentities($realm['name']).'</option>';
    $output .= '
                      </select>
                    </td>
                  </tr>';
  }
  else
    $output .= '
                  <input type="hidden" name="realm" value="'.$sqlr->result($result, 0, 'id').'" />';
  $output .= '
                  <tr>
                    <td>
                    </td>
                  </tr>
                  <tr align="right">
                    <td>'.$lang_login['remember_me'].' : <input type="checkbox" id="remember"  name="remember" value="1"';
  if ($remember_me_checked)
    $output .= ' checked="checked"';
  $output .= ' /></td>
                  </tr>
                  <tr>
                    <td>
                    </td>
                  </tr>
                  <tr align="right">
                    <td width="290">
                      <input type="submit" value="" style="display:none" />';
                        makebutton($lang_login['not_registrated'], 'register.php" type="wrn', 130, "btnRegister");
                        makebutton($lang_login['login'], 'javascript:doLogin()" type="def', 130, "btnLogin");
  $output .= '
                    </td>
                  </tr>
                  <tr align="center">
                    <td><a href="register.php?action=pass_recovery">'.$lang_login['pass_recovery'].'</a></td>
                  </tr>
                  <tr>
                    <td>
                      <hr />
                    </td>
                  </tr>
                </table>
                <script type="text/javascript">
                  // <![CDATA[
                    document.form.user.focus();
                  // ]]>
                </script>
              </form>
              <br />
            </fieldset>
            <br /><br />
          </center>';
}


//#################################################################################################
// Login via set cookie
//#################################################################################################
function do_cookie_login(&$sqlr)
{
  if (empty($_COOKIE['uname']) || empty($_COOKIE['p_hash']) || empty($_COOKIE['realm_id']))
    redirect('login.php?error=2');

  $user_name = $sqlr->quote_smart($_COOKIE['uname']);
  $user_pass = $sqlr->quote_smart($_COOKIE['p_hash']);

  $result = $sqlr->query('SELECT username, gmlevel, id FROM account WHERE username = \''.$user_name.'\' AND sha_pass_hash = \''.$user_pass.'\'');

  unset($user_name);
  unset($user_pass);

  if ($sqlr->num_rows($result))
  {
    $id = $sqlr->result($result, 0, 'id');
    if ($sqlr->result($sqlr->query('SELECT count(*) FROM account_banned WHERE id ='.$id.' AND active = \'1\''), 0))
    {
      redirect('login.php?error=3');
    }
    else
    {
      $_SESSION['user_id']   = $id;
      $_SESSION['uname']     = $sqlr->result($result, 0, 'username');
      $_SESSION['user_lvl']  = $sqlr->result($result, 0, 'gmlevel');
      $_SESSION['realm_id']  = $sqlr->quote_smart($_COOKIE['realm_id']);
      $_SESSION['client_ip'] = (isset($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
      $_SESSION['logged_in'] = true;
      redirect('index.php');
    }
  }
  else
  {
    setcookie (   'uname', '', time() - 3600);
    setcookie ('realm_id', '', time() - 3600);
    setcookie (  'p_hash', '', time() - 3600);
    redirect('login.php?error=1');
  }
}


//#################################################################################################
// MAIN
//#################################################################################################
if (isset($_COOKIE["uname"]) && isset($_COOKIE["p_hash"]) && isset($_COOKIE["realm_id"]) && empty($_GET['error']))
  do_cookie_login($sqlr);

$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$lang_login = lang_login();

$output .= '
          <div class="top">';

if (1 == $err)
  $output .=  '
            <h1><font class="error">'.$lang_login['bad_pass_user'].'</font></h1>';
elseif (2 == $err)
  $output .=  '
            <h1><font class="error">'.$lang_login['missing_pass_user'].'</font></h1>';
elseif (3 == $err)
  $output .=  '
            <h1><font class="error">'.$lang_login['banned_acc'].'</font></h1>';
elseif (5 == $err)
  $output .=  '
            <h1><font class="error">'.$lang_login['no_permision'].'</font></h1>';
elseif (6 == $err)
  $output .=  '
            <h1><font class="error">'.$lang_login['after_registration'].'</font></h1>';
else
  $output .=  '
            <h1>'.$lang_login['enter_valid_logon'].'</h1>';

unset($err);

$output .= '
          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('dologin' === $action)
  dologin($sqlr);
else
  login($sqlr);

unset($action);
unset($action_permission);
unset($lang_login);

require_once 'footer.php';


?>
