<?php


require_once("header.php");

//#############################################################################
// Login
//#############################################################################
function dologin()
{
  global $realm_db;

  if ( empty($_POST['user']) || empty($_POST['pass']) )
    redirect("login.php?error=2");

  $sqlr = new SQL;
  $link = $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

  $user_name  = $sqlr->quote_smart($_POST['user']);
  $user_pass  = $sqlr->quote_smart($_POST['pass']);

  if (strlen($user_name) > 255 || strlen($user_pass) > 255)
    redirect("login.php?error=1");

  $result = $sqlr->query("SELECT id,gmlevel,username FROM account WHERE username='$user_name' AND sha_pass_hash='$user_pass' ");

  if ($sqlr->num_rows($result) == 1)
  {
    $id = $sqlr->result($result, 0, 'id');
    $result1 = $sqlr->query("SELECT count(*) FROM account_banned WHERE id ='$id' AND active = '1'");
    if ($sqlr->result($result1, 0))
    {
      redirect("login.php?error=3");
    }
    else
    {
      $_SESSION['user_id'] = $id;
      $_SESSION['uname'] = $sqlr->result($result, 0, 'username');
      $_SESSION['user_lvl'] = $sqlr->result($result, 0, 'gmlevel');
      $_SESSION['realm_id'] = $sqlr->quote_smart($_POST['realm']);
      $_SESSION['client_ip'] = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
      $_SESSION['logged_in'] = true;

      if (isset($_POST['remember'])&&$_POST['remember'] != '')
      {
        setcookie("uname", $_SESSION['uname'], time()+60*60*24*7);
        setcookie("realm_id", $_SESSION['realm_id'], time()+60*60*24*7);
        setcookie("p_hash", $user_pass, time()+60*60*24*7);
      }
      redirect("index.php");
    }
  }
  else
  {
    redirect("login.php?error=1");
  }
}


//#################################################################################################
// Print login form
//#################################################################################################
function login()
{
  global $lang_login, $output, $realm_db, $characters_db, $server, $remember_me_checked;

  $output .= "
        <center>
          <script type=\"text/javascript\" src=\"js/sha1.js\"></script>
          <script type=\"text/javascript\">
            function dologin ()
            {
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
                <tr>
                  <td>
                    <hr />
                  </td>
                </tr>
                <tr align=\"right\">
                  <td>{$lang_login['username']} : <input type=\"text\" name=\"user\" size=\"24\" maxlength=\"16\" /></td>
                </tr>
                <tr align=\"right\">
                  <td>{$lang_login['password']} : <input type=\"password\" name=\"login_pass\" size=\"24\" maxlength=\"40\" /></td>
                </tr>";

  $sqlr = new SQL;
  $link = $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $result = $sqlr->query("SELECT id,name FROM `realmlist` LIMIT 10");

  if ($sqlr->num_rows($result) > 1 && (count($server) > 1) && (count($characters_db) > 1))
  {
    $output .= "
                <tr align=\"right\">
                  <td>{$lang_login['select_realm']} :
                    <select name=\"realm\">";
    while ($realm = $sqlr->fetch_row($result))
      if(isset($server[$realm[0]]))
        $output .= "
                      <option value=\"$realm[0]\">".htmlentities($realm[1])."</option>";
    $output .= "
                    </select>
                  </td>
                </tr>";
  }
  else
    $output .= "
                <input type=\"hidden\" name=\"realm\" value=\"".$sqlr->result($result, 0, 'id')."\" />";
  $output .= "
                <tr>
                  <td>
                  </td>
                </tr>
                <tr align=\"right\">";
  if ($remember_me_checked)
    $output .= "
                  <td>{$lang_login['remember_me']} : <input type=\"checkbox\" name=\"remember\" value=\"1\" checked=\"checked\" /></td>";
  else
    $output .= "
                  <td>{$lang_login['remember_me']} : <input type=\"checkbox\" name=\"remember\" value=\"1\" /></td>";
  $output .= "
                </tr>
                <tr>
                  <td>
                  </td>
                </tr>
                <tr align=\"right\">
                  <td width=\"290\">
                    <input type=\"submit\" value=\"\" style=\"display:none\" />";
                    makebutton($lang_login['not_registrated'], "register.php\" type=\"wrn",130);
                    makebutton($lang_login['login'], "javascript:dologin()\" type=\"def",130);
  $output .= "
                  </td>
                </tr>
                <tr align=\"center\">
                  <td><a href=\"register.php?action=pass_recovery\">{$lang_login['pass_recovery']}</a></td>
                </tr>
                <tr>
                  <td>
                    <hr />
                  </td>
                </tr>
              </table>
              <script type=\"text/javascript\">
                <!--
                  document.form.user.focus();
                //-->
              </script>
            </form>
            <br />
          </fieldset>
          <br /><br />
        </center>
";
}


//#################################################################################################
// Login via set cookie
//#################################################################################################
function do_cookie_login()
{
  global $realm_db;

  if ( empty($_COOKIE['uname']) || empty($_COOKIE['p_hash']) || empty($_COOKIE['realm_id']))
    redirect("login.php?error=2");

  $sqlr = new SQL;
  $link = $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $user_name = $sqlr->quote_smart($_COOKIE['uname']);
  $user_pass  = $sqlr->quote_smart($_COOKIE['p_hash']);
  $result = $sqlr->query("SELECT username,gmlevel,id FROM account WHERE username='$user_name' AND sha_pass_hash='$user_pass'");
  if ($sqlr->num_rows($result))
  {
    $id = $sqlr->result($result, 0, 'id');
    $result1 = $sqlr->query("SELECT count(*) FROM account_banned WHERE id ='$id'");
    if ($sqlr->result($result1, 0))
    {
      redirect("login.php?error=3");
    }
    else
    {
      $_SESSION['user_id'] = $id;
      $_SESSION['uname'] = $sqlr->result($result, 0, 'username');
      $_SESSION['user_lvl'] = $sqlr->result($result, 0, 'gmlevel');
      $_SESSION['realm_id'] = $sqlr->quote_smart($_COOKIE['realm_id']);
      $_SESSION['client_ip'] = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
      redirect("index.php");
    }
  }
  else
  {
    setcookie ("uname", "", time() - 3600);
    setcookie ("realm_id", "", time() - 3600);
    setcookie ("p_hash", "", time() - 3600);
    redirect("login.php?error=1");
  }
}


//#################################################################################################
// MAIN
//#################################################################################################
if (isset($_COOKIE["uname"]) && isset($_COOKIE["p_hash"]) && isset($_COOKIE["realm_id"]) && !isset($_GET['error']))
  do_cookie_login();

$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$lang_login = lang_login();

$output .= "
        <div class=\"top\">";

switch ($err)
{
  case 1:
    $output .=  "
          <h1><font class=\"error\">{$lang_login['bad_pass_user']}</font></h1>";
    break;
  case 2:
    $output .=  "
          <h1><font class=\"error\">{$lang_login['missing_pass_user']}</font></h1>";
    break;
  case 3:
    $output .=  "
          <h1><font class=\"error\">{$lang_login['banned_acc']}</font></h1>";
    break;
  case 5:
    $output .=  "
          <h1><font class=\"error\">{$lang_login['no_permision']}</font></h1>";
    break;
  case 6:
    $output .=  "
          <h1><font class=\"error\">{$lang_login['after_registration']}</font></h1>";
    break;
  default: //no error
    $output .=  "
          <h1>{$lang_login['enter_valid_logon']}</h1>";
}
unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "dologin":
    dologin();
    break;
  default:
    login();
}

unset($action);
unset($action_permission);
unset($lang_login);

require_once("footer.php");

?>
