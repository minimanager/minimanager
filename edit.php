<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

require_once("header.php");
valid_login($action_permission['read']);
require_once("scripts/defines.php");
require_once("scripts/get_lib.php");

//##############################################################################################################
// EDIT USER
//##############################################################################################################
function edit_user()
{
  global $lang_edit, $lang_global, $output, $realm_db, $mmfpm_db, $characters_db, $realm_id, $user_name, $user_id,
    $lang_id_tab, $expansion_select, $server, $developer_test_mode;

  $sqlr = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $result = $sqlr->query("SELECT email, gmlevel, joindate, expansion, last_ip FROM account WHERE username = '$user_name'");
  $refguid = $sqlm->fetch_row($sqlm->query("SELECT InvitedBy FROM point_system_invites WHERE PlayersAccount = '$user_id'"));
  $refguid = $refguid[0];
  $referred_by = $sqlc->fetch_row($sqlc->query("SELECT name FROM characters WHERE guid = '$refguid'"));
  unset($refguid);
  $referred_by = $referred_by[0];

  if ($acc = $sqlc->fetch_row($result))
  {
    $output .= "
        <center>
          <script type=\"text/javascript\" src=\"js/sha1.js\"></script>
          <script type=\"text/javascript\">
           function do_submit_data ()
           {
             document.form.pass.value = hex_sha1('".strtoupper($user_name).":'+document.form.user_pass.value.toUpperCase());
             document.form.user_pass.value = '0';
             do_submit();
           }
          </script>
          <fieldset style=\"width: 550px;\">
            <legend>{$lang_edit['edit_acc']}</legend>
            <form method=\"post\" action=\"edit.php?action=doedit_user\" name=\"form\">
              <input type=\"hidden\" name=\"pass\" value=\"\" maxlength=\"256\" />
              <table class=\"flat\">
                <tr>
                  <td>{$lang_edit['id']}</td>
                  <td>$user_id</td>
                </tr>
                <tr>
                  <td>{$lang_edit['username']}</td>
                  <td>$user_name</td>
                </tr>
                <tr>
                  <td>{$lang_edit['password']}</td>
                  <td><input type=\"text\" name=\"user_pass\" size=\"42\" maxlength=\"40\" value=\"******\" /></td>
                </tr>
                <tr>
                  <td>{$lang_edit['mail']}</td>
                  <td><input type=\"text\" name=\"mail\" size=\"42\" maxlength=\"225\" value=\"$acc[0]\" /></td>
                </tr>
                <tr>
                  <td>{$lang_edit['invited_by']}:</td>
                  <td>";
    if($referred_by !=NULL)
      $output .= "
                    $referred_by";
    else
      $output .= "
                    <input type=\"text\" name=\"referredby\" size=\"42\" maxlength=\"12\" value=\"$referred_by\" />";
    unset($referred_by);
    $output .= "
                  </td>
                </tr>
                <tr>
                  <td>{$lang_edit['gm_level']}</td>
                  <td>".id_get_gm_level($acc[1])." ( $acc[1] )</td>
                </tr>
                <tr>
                  <td>{$lang_edit['join_date']}</td>
                  <td>$acc[2]</td>
                </tr>
                <tr>
                  <td>{$lang_edit['last_ip']}</td>
                  <td>$acc[4]</td>
                </tr>";
    if ($expansion_select)
    {
      $output .="
                 <tr>
                  <td >{$lang_edit['client_type']}:</td>
                  <td>
                    <select name=\"expansion\">
                      <option value=\"2\" ";
      if(!$acc[3])
        $output .= "selected=\"selected\"";
      $output .= ">{$lang_edit['wotlk']}</option>
                      <option value=\"1\" ";
    if(!$acc[3]) $output .= "selected=\"selected\"";
      $output .= ">{$lang_edit['tbc']}</option>
                      <option value=\"0\" ";
    if(!$acc[3]) $output .= "selected=\"selected\"";
      $output .= ">{$lang_edit['classic']}</option>
                    </select>
                  </td>
                </tr>";
    }
    $result = $sqlr->query("SELECT SUM(numchars) FROM realmcharacters WHERE acctid = $user_id");
    $output .= "
                <tr>
                  <td>{$lang_edit['tot_chars']}</td>
                  <td>".$sqlr->result($result, 0)."</td>
                </tr>";
    $realms = $sqlr->query("SELECT id, name FROM realmlist");
    if ($developer_test_mode && ($sqlr->num_rows($realms) > 1 && (count($server) > 1) && (count($characters_db) > 1)))
    {
      while ($realm = $sqlr->fetch_array($realms))
      {
        $sqlc->connect($characters_db[$realm[0]]['addr'], $characters_db[$realm[0]]['user'], $characters_db[$realm[0]]['pass'], $characters_db[$realm[0]]['name']);
        $result = $sqlc->query("SELECT guid, name, race, class,
          SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1),
          mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender
          FROM characters WHERE account = '$user_id'");

        $output .= "
                  <tr>
                    <td>{$lang_edit['characters']} ".get_realm_name($realm[0])."</td>
                    <td>".$sqlc->num_rows($result)."</td>
                  </tr>";

        while ($char = $sqlc->fetch_array($result))
        {
          $output .= "
                  <tr>
                    <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'---></td>
                    <td>
                      <a href=\"char.php?id=$char[0]&amp;realm=$realm[0]\">$char[1]  - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />
                      <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\"/> - lvl ".get_level_with_color($char[4])."</a>
                    </td>
                  </tr>";
        }
      }
      unset($realm);
    }
    else
    {
      $result = $sqlc->query("SELECT guid, name, race, class,
        SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1),
        mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender
        FROM characters WHERE account = '$user_id'");

      $output .= "
                <tr>
                  <td>{$lang_edit['characters']}</td>
                  <td>".$sqlc->num_rows($result)."</td>
            </tr>";
      while ($char = $sqlc->fetch_array($result))
      {
        $output .= "
                <tr>
                  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'---></td>
                  <td>
                    <a href=\"char.php?id=$char[0]\">$char[1]  - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />
                    <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\"/> - lvl ".get_level_with_color($char[4])."</a>
                  </td>
                </tr>";
      }
    }
    unset($result);
    unset($realms);
    $output .= "
                <tr>
                  <td>";
                    makebutton($lang_edit['update'], "javascript:do_submit_data()\" type=\"wrn", 130);
    $output .= "
                  </td>
                  <td>";
                    makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def", 130);
    $output .= "
                  </td>
                </tr>
              </table>
            </form>
          </fieldset>
          <br />
          <fieldset style=\"width: 550px;\">
            <legend>{$lang_edit['theme_options']}</legend>
            <table class=\"hidden\" style=\"width: 450px;\">
              <tr>
                <td align=\"left\">{$lang_edit['select_layout_lang']} :</td>
                <td align=\"right\">
                  <form action=\"edit.php\" method=\"get\" name=\"form1\">
                    <input type=\"hidden\" name=\"action\" value=\"lang_set\" />
                    <select name=\"lang\">
                      <optgroup label=\"{$lang_edit['language']}\">";
    if (is_dir("./lang"))
    {
      if ($dh = opendir("./lang"))
      {
        while (($file = readdir($dh)) != false)
        {
          $lang = explode('.', $file);
          if(isset($lang[1]) && $lang[1] == 'php')
          {
            if ((substr($file,0,6) != 'forum_') )
            {
              $output .= "
                        <option value=\"{$lang[0]}\"";
              if (isset($_COOKIE["lang"]) && ($_COOKIE["lang"] == $lang[0]))
                $output .= " selected=\"selected\" ";
              $output .= ">{$lang[0]}</option>";
            }
          }
        }
        closedir($dh);
      }
    }
    $output .= "
                      </optgroup>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;
                  </form>
                </td>
                <td>";
                  makebutton($lang_edit['save'], "javascript:do_submit('form1',0)",130);
    $output .= "
                </td>
              </tr>
              <tr>
                <td align=\"left\">{$lang_edit['select_theme']} :</td>
                <td align=\"right\">
                  <form action=\"edit.php\" method=\"get\" name=\"form2\">
                    <input type=\"hidden\" name=\"action\" value=\"theme_set\" />
                    <select name=\"theme\">
                      <optgroup label=\"{$lang_edit['theme']}\">";
    if (is_dir("./themes"))
    {
      if ($dh = opendir("./themes"))
      {
        while (($file = readdir($dh)) != false)
        {
          if (($file != '.')&&($file != '..')&&($file != '.htaccess')&&($file != 'index.html')&&($file != '.svn'))
          {
            $output .= "
                        <option value=\"$file\"";
            if (isset($_COOKIE["theme"]) && ($_COOKIE["theme"] == $file))
              $output .= " selected=\"selected\" ";
            $output .= ">$file</option>";
          }
        }
        closedir($dh);
      }
    }
    $output .= "
                      </optgroup>
                    </select>&nbsp;&nbsp;&nbsp;&nbsp;
                  </form>
                </td>
                <td>";
                  makebutton($lang_edit['save'], "javascript:do_submit('form2',0)",130);
    $output .= "
                </td>
              </tr>
            </table>
          </fieldset>
          <br />
        </center>
";
  }
  else
    error($lang_global['err_no_records_found']);

}


//#############################################################################################################
//  DO EDIT USER
//#############################################################################################################
function doedit_user()
{
  global $lang_edit, $lang_global, $output, $realm_db, $mmfpm_db, $characters_db, $realm_id, $user_name, $user_id,
  $lang_id_tab;

  if ( (!isset($_POST['pass'])||$_POST['pass'] === '') || (!isset($_POST['mail'])||$_POST['mail'] === '') ||(!isset($_POST['expansion'])||$_POST['expansion'] === '') )
    redirect("edit.php?error=1");

  $sqlr = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

  $new_pass = ($sqlr->quote_smart($_POST['pass']) != sha1(strtoupper($user_name).":******")) ? "sha_pass_hash='".$sqlr->quote_smart($_POST['pass'])."', " : "";
  $new_mail = $sqlr->quote_smart(trim($_POST['mail']));
  $new_expansion = $sqlr->quote_smart(trim($_POST['expansion']));

  //make sure the mail is valid mail format
  require_once("libs/valid_lib.php");
  if ((!valid_email($new_mail))||(strlen($new_mail)  > 224))
    redirect("edit.php?error=2");

  $sqlr->query("UPDATE account SET email = '$new_mail', $new_pass expansion = '$new_expansion' WHERE username = '$user_name'");
  if ($sqlr->affected_rows())
  {
    doupdate_referral($mmfpm_db, $user_id);
    redirect("edit.php?error=3");
  }
  else
  {
    doupdate_referral($mmfpm_db, $user_id);
    redirect("edit.php?error=4");
  }
}
function doupdate_referral($mmfpm_db, $user_id)
{
  global $realm_db, $mmfpm_db, $characters_db, $realm_id, $user_name, $user_id;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $sqlr = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

  $result = $sqlm->fetch_row($sqlm->query("SELECT InvitedBy FROM point_system_invites WHERE PlayersAccount = '$user_id'"));
  $result = $result[0];

  if ($result == NULL)
  {
    $referredby = $_POST['referredby'];
    $referred_by = $sqlc->fetch_row($sqlc->query("SELECT guid FROM characters WHERE name = '$referredby'"));
    $referred_by = $referred_by[0];

    if ($referred_by != NULL)
    {
      $char = $sqlc->fetch_row($sqlc->query("SELECT account FROM characters WHERE guid = '$referred_by'"));
      $result = $sqlr->fetch_row($sqlr->query("SELECT id FROM account WHERE id = '$char'"));
      $result = $result[0];
      if ($result != $user_id)
      {
        $sqlm->query("INSERT INTO point_system_invites (PlayersAccount, InvitedBy, InviterAccount) VALUES ('$user_id', '$referred_by', '$result')");
        redirect("edit.php?error=3");
      }
      else
        redirect("edit.php?error=4");
    }
  }
}


//###############################################################################################################
// SET DEFAULT INTERFACE LANGUAGE
//###############################################################################################################
function lang_set()
{
  if (empty($_GET['lang']))
    redirect("edit.php?error=1");
  else
    $lang = addslashes($_GET['lang']);

  if ($lang)
  {
    setcookie("lang", $lang, time()+60*60*24*30*6); //six month
    redirect("edit.php");
  }
  else
    redirect("edit.php?error=1");
}


//###############################################################################################################
// SET DEFAULT INTERFACE THEME
//###############################################################################################################
function theme_set()
{
  if (empty($_GET['theme']))
    redirect("edit.php?error=1");
  else
    $tmpl = addslashes($_GET['theme']);

  if ($tmpl)
  {
    setcookie("theme", $tmpl, time()+3600*24*30*6); //six month
    redirect("edit.php");
  }
  else
    redirect("edit.php?error=1");
}


//###############################################################################################################
// MAIN
//###############################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";

$lang_edit = lang_edit();

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">{$lang_edit['use_valid_email']}</font></h1>";
    break;
  case 3:
    $output .= "
          <h1><font class=\"error\">{$lang_edit['data_updated']}</font></h1>";
    break;
  case 4:
    $output .= "
          <h1><font class=\"error\">{$lang_edit['error_updating']}</font></h1>";
    break;
  case 5:
    $output .= "
          <h1><font class=\"error\">{$lang_edit['del_error']}</font></h1>";
    break;
  default: //no error
    $output .= "
          <h1>{$lang_edit['edit_your_acc']}</h1>";
}

unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "doedit_user":
    doedit_user();
    break;
  case "lang_set":
    lang_set();
    break;
  case "theme_set":
    theme_set();
    break;
  default:
    edit_user();
}

unset($action);
unset($action_permission);
unset($lang_edit);

require_once("footer.php");

?>
