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
require_once("scripts/bbcode_lib.php");
valid_login($action_permission['insert']);

//#############################################################################
// ADD MOTD
//#############################################################################
function add_motd()
{
  global $lang_motd, $lang_global, $output, $action_permission;
  valid_login($action_permission['insert']);

  $output .= "
        <center>
          <form action=\"motd.php?action=do_add_motd\" method=\"post\" name=\"form\">
            <table class=\"top_hidden\">
              <tr>
                <td colspan=\"3\">";
                  add_bbcode_editor();
  $output .= "
                </td>
              </tr>
              <tr>
                <td colspan=\"3\">
                  <textarea id=\"msg\" name=\"msg\" rows=\"10\" cols=\"80\"></textarea>
                </td>
              </tr>
              <tr>
                <td>{$lang_motd['post_rules']}</td>
                <td>";
                  makebutton($lang_motd['post_motd'], "javascript:do_submit()\" type=\"wrn",220);
  $output .= "
                </td>
                <td>";
                  makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);
  $output .= "
                </td>
              </tr>
            </table>
          </form>
          <br />
        </center>
";
}


//#############################################################################
// EDIT MOTD
//#############################################################################
function edit_motd()
{
  global $lang_motd, $lang_global, $output, $characters_db, $realm_id, $action_permission;
  valid_login($action_permission['update']);

  if(!isset($_GET['id'])) redirect("motd.php?error=1");

  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $id = $sql->quote_smart($_GET['id']);
  if(!preg_match("/^[[:digit:]]{1,10}$/", $id)) redirect("tele.php?error=1");

  $result = $sql->query("SELECT content FROM bugreport WHERE id = '$id'");
  $msg = $sql->result($result, 0);
  unset($result);
  $sql->close();
  unset($sql);

  $output .= "
        <center>
          <form action=\"motd.php?action=do_edit_motd\" method=\"post\" name=\"form\">
            <input type=\"hidden\" name=\"id\" value=\"$id\" />
            <table class=\"top_hidden\">
              <tr>
                <td colspan=\"3\">";
                  add_bbcode_editor();
  $output .= "
                </td>
              </tr>
              <tr>
                <td colspan=\"3\">
                  <textarea id=\"msg\" name=\"msg\" rows=\"10\" cols=\"80\">$msg</textarea>
                </td>
              </tr>
              <tr>
                <td>{$lang_motd['post_rules']}</td>
                <td>";
                  makebutton($lang_motd['post_motd'], "javascript:do_submit()\" type=\"wrn",220);
  $output .= "
                </td>
                <td>";
                  makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);
  $output .= "
                </td>
              </tr>
            </table>
          </form>
          <br />
        </center>
";
}


//#####################################################################################################
// DO ADD MOTD
//#####################################################################################################
function do_add_motd()
{
  global $characters_db, $realm_id, $user_name, $action_permission;
  valid_login($action_permission['insert']);

  if (empty($_POST['msg']))
    redirect("motd.php?error=1");

  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $msg = $sql->quote_smart($_POST['msg']);

  if (strlen($msg) > 4096)
  {
    $sql->close();
    unset($sql);
    redirect("motd.php?error=2");
  }

  $by = date("m/d/y H:i:s")." Posted by: $user_name";

  $sql->query("INSERT INTO bugreport (type, content) VALUES ('$by','$msg')");
  $sql->close();
  unset($sql);

  redirect("index.php");
}


//#####################################################################################################
// DO EDIT MOTD
//#####################################################################################################
function do_edit_motd()
{
  global $characters_db, $realm_id, $user_name, $action_permission;
  valid_login($action_permission['update']);

  if (empty($_POST['msg']) || empty($_POST['id']))
   redirect("motd.php?error=1");

  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $msg = $sql->quote_smart($_POST['msg']);
  $id = $sql->quote_smart($_POST['id']);
  if(!preg_match("/^[[:digit:]]{1,10}$/", $id)) redirect("motd.php?error=1");

  $by = $sql->result($sql->query("SELECT type FROM bugreport WHERE id = '$id'"), 0, 'type');

  if (strlen($msg) > 4096)
  {
    $sql->close();
    unset($sql);
      redirect("motd.php?error=2");
  }

  $by = split("<br />", $by, 2);
  $by = "{$by[0]}<br />".date("m/d/y H:i:s")." Edited by: $user_name";

  $sql->query("UPDATE bugreport SET type = '$by', content = '$msg' WHERE id = '$id'");
  $sql->close();
  unset($sql);

  redirect("index.php");
}


//#####################################################################################################
// DELETE MOTD
//#####################################################################################################
function delete_motd()
{
  global $characters_db, $realm_id, $action_permission;
  valid_login($action_permission['delete']);

  if (empty($_GET['id'])) redirect("index.php");

  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $id = $sql->quote_smart($_GET['id']);
  if(!preg_match("/^[[:digit:]]{1,10}$/", $id)) redirect("motd.php?error=1");

  $query = $sql->query("DELETE FROM bugreport WHERE id ='$id'");

  $sql->close();
  unset($sql);
  redirect("index.php");
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$lang_motd = lang_motd();

$output .= "
        <div class=\"top\">";

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">{$lang_motd['err_max_len']}</font></h1>";
    break;
  default: //no error
    $output .= "
          <h1>{$lang_motd['add_motd']}</h1>";
}

unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "delete_motd":
    delete_motd();
    break;
  case "add_motd":
    add_motd();
    break;
  case "do_add_motd":
    do_add_motd();
    break;
  case "edit_motd":
    edit_motd();
    break;
  case "do_edit_motd":
    do_edit_motd();
    break;
  default:
    add_motd();
}

unset($action);
unset($action_permission);
unset($lang_motd);

require_once("footer.php");

?>
