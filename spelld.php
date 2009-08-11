<?php
function lang_spelld()
{
  $lang_spelld = array
  (
    'add_new_spell' => 'Add New Spell ID',
    'entry' => 'Spell ID (numbers only, max 11)',
    'disable_mask' => 'Spell Disable Mask (numbers only, max 8)',
    'comment' => 'Comment (max 64 chars)',
    'add_spell' => 'Add Spell',
    'wrong_fields' => 'Some Fields Wrong',
    'updated' => 'New Spell Added',
    'err_add_entry' => 'Adding New Spell Fail',
  );
  return $lang_spelld;
}

require_once("header.php");
valid_login($action_permission['insert']);

//#####################################################################################################
//  ADD NEW SPELL
//#######################################################################################################
function add_new()
{
  global $lang_global, $lang_spelld, $output, $action_permission;

  $output .= "
        <center>
          <fieldset style=\"width: 550px;\">
            <legend>{$lang_spelld['add_new_spell']}</legend>
            <form method=\"get\" action=\"spelld.php\" name=\"form\">
              <input type=\"hidden\" name=\"action\" value=\"doadd_new\" />
              <table class=\"flat\">
                <tr>
                  <td>{$lang_spelld['entry']}</td>
                  <td><input type=\"text\" name=\"entry\" size=\"24\" maxlength=\"11\" value=\"\" /></td>
                </tr>
                <tr>
                  <td>{$lang_spelld['disable_mask']}</td>
                  <td><input type=\"text\" name=\"disable_mask\" size=\"24\" maxlength=\"8\" value=\"\" /></td>
                </tr>
                <tr>
                  <td>{$lang_spelld['comment']}</td>
                  <td><input type=\"text\" name=\"comment\" size=\"24\" maxlength=\"64\" value=\"\" /></td>
                </tr>
                <tr>
                  <td>";
                    makebutton($lang_spelld['add_spell'], "javascript:do_submit()\" type=\"wrn",130);
  $output .= "
                  </td>
                  <td>";
                   makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);
  $output .= "
                  </td>
                </tr>
              </table>
            </form>
          </fieldset>
          <br />
        </center>";
}


//#########################################################################################################
// DO ADD NEW SPELL
//#########################################################################################################
function doadd_new()
{
  global $world_db, $realm_id, $action_permission;

  if ( empty($_GET['entry']) && empty($_GET['disable_mask']) && empty($_GET['comment']) )
    redirect("spelld.php?error=1");

  $sqlw = new SQL;
  $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

  $entry = $sqlw->quote_smart($_GET['entry']);
  if (!preg_match("/^[[:digit:]]{1,8}$/", $entry))
    redirect("spelld.php?error=1");
  $disable_mask = $sqlw->quote_smart($_GET['disable_mask']);
  if (!preg_match("/^[[:digit:]]{1,11}$/", $disable_mask))
    redirect("spelld.php?error=1");
  $comment = $sqlw->quote_smart($_GET['comment']);

  $sqlw->query("INSERT INTO spell_disabled (entry, disable_mask, comment) VALUES ('$entry','$disable_mask','$comment')");
  if ($sqlw->affected_rows())
    redirect("spelld.php?error=3");
  else
   redirect("spelld.php?error=2");
}

//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
          <div class=\"top\">";

$lang_spelld = lang_spelld();

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">{$lang_spelld['wrong_fields']}</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">{$lang_spelld['err_add_entry']}</font></h1>";
    break;
  case 3:
    $output .= "
          <h1><font class=\"error\">{$lang_spelld['updated']}</font></h1>";
    break;
  default:
    $output .= "
          <h1>{$lang_spelld['add_new_spell']}</h1>";
}
unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "doadd_new":
    doadd_new();
    break;
  default:
    add_new();
}

unset($action);
unset($action_permission);
unset($lang_spelld);

require_once("footer.php");

?>
