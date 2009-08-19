<?php


require_once 'header.php';
require_once 'libs/bbcode_lib.php';
valid_login($action_permission['insert']);

//#############################################################################
// ADD MOTD
//#############################################################################
function add_motd()
{
  global $output, $lang_motd, $lang_global,
    $action_permission;
  valid_login($action_permission['insert']);

  $output .= '
          <center>
            <form action="motd.php?action=do_add_motd" method="post" name="form">
              <table class="top_hidden">
                <tr>
                  <td colspan="3">';
                    bbcode_add_editor();
  $output .= '
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                    <textarea id="msg" name="msg" rows="26" cols="97"></textarea>
                  </td>
                </tr>
                <tr>
                  <td>'.$lang_motd['post_rules'].'</td>
                  <td>';
                    makebutton($lang_motd['post_motd'], 'javascript:do_submit()" type="wrn', 230);
  $output .= '
                  </td>
                  <td>';
                    makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
  $output .= '
                  </td>
                </tr>
              </table>
            </form>
            <br />
          </center>';

}


//#############################################################################
// EDIT MOTD
//#############################################################################
function edit_motd(&$sqlc)
{
  global $output, $lang_motd, $lang_global,
    $action_permission;
  valid_login($action_permission['update']);

  if(empty($_GET['id'])) redirect('motd.php?error=1');

  $id = $sqlc->quote_smart($_GET['id']);
  if(preg_match('/^[[:digit:]]{1,10}$/', $id));
  else redirect('motd.php?error=1');

  $msg = $sqlc->result($sqlc->query('SELECT content FROM bugreport WHERE id = '.$id.''), 0);

  $output .= '
          <center>
            <form action="motd.php?action=do_edit_motd" method="post" name="form">
              <input type="hidden" name="id" value="'.$id.'" />
              <table class="top_hidden">
                <tr>
                  <td colspan="3">';
                    bbcode_add_editor();
  $output .= '
                  </td>
                </tr>
                <tr>
                  <td colspan="3">
                    <textarea id="msg" name="msg" rows="26" cols="97">'.$msg.'</textarea>
                  </td>
                </tr>
                <tr>
                  <td>'.$lang_motd['post_rules'].'</td>
                  <td>';
                    makebutton($lang_motd['post_motd'], 'javascript:do_submit()" type="wrn', 230);
  $output .= '
                  </td>
                  <td>';
                    makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
  $output .= '
                  </td>
                </tr>
              </table>
            </form>
            <br />
          </center>';

}


//#####################################################################################################
// DO ADD MOTD
//#####################################################################################################
function do_add_motd(&$sqlc)
{
  global $action_permission, $user_name;
  valid_login($action_permission['insert']);

  if (empty($_POST['msg'])) redirect('motd.php?error=1');

  $msg = $sqlc->quote_smart($_POST['msg']);
  if (4096 < strlen($msg))
    redirect('motd.php?error=2');

  $by = date('m/d/y H:i:s').' Posted by: '.$user_name;

  $sqlc->query('INSERT INTO bugreport (type, content) VALUES (\''.$by.'\', \''.$msg.'\')');
  redirect('index.php');

}


//#####################################################################################################
// DO EDIT MOTD
//#####################################################################################################
function do_edit_motd(&$sqlc)
{
  global $action_permission, $user_name;
  valid_login($action_permission['update']);

  if (empty($_POST['msg']) || empty($_POST['id']))
    redirect('motd.php?error=1');

  $id = $sqlc->quote_smart($_POST['id']);
  if(preg_match('/^[[:digit:]]{1,10}$/', $id));
  else redirect('motd.php?error=1');

  $msg = $sqlc->quote_smart($_POST['msg']);
  if (4096 < strlen($msg))
    redirect('motd.php?error=2');

  $by = $sqlc->result($sqlc->query('SELECT type FROM bugreport WHERE id = '.$id.''), 0, 'type');
  $by = split('<br />', $by, 2);
  $by = $by[0].'<br />'.date('m/d/y H:i:s').' Edited by: '.$user_name;

  $sqlc->query('UPDATE bugreport SET type = \''.$by.'\', content = \''.$msg.'\' WHERE id = '.$id.'');
  redirect('index.php');

}


//#####################################################################################################
// DELETE MOTD
//#####################################################################################################
function delete_motd(&$sqlc)
{
  global $action_permission;
  valid_login($action_permission['delete']);

  if (empty($_GET['id'])) redirect('index.php');

  $id = $sqlc->quote_smart($_GET['id']);
  if(preg_match('/^[[:digit:]]{1,10}$/', $id));
  else redirect('motd.php?error=1');

  $query = $sqlc->query('DELETE FROM bugreport WHERE id ='.$id.'');
  redirect('index.php');

}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$lang_motd = lang_motd();

$output .= '
          <div class="top">';

if (1 == $err)
    $output .= '
            <h1><font class="error">'.$lang_global['empty_fields'].'</font></h1>';
elseif (2 == $err)
    $output .= '
            <h1><font class="error">'.$lang_motd['err_max_len'].'</font></h1>';
elseif (3 == $err)
    $output .= '
            <h1>'.$lang_motd['edit_motd'].'</h1>';
else
    $output .= '
            <h1>'.$lang_motd['add_motd'].'</h1>';

unset($err);

$output .= '
          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('delete_motd' == $action)
  delete_motd($sqlc);
elseif ('add_motd' == $action)
  add_motd($sqlc);
elseif ('do_add_motd' == $action)
  do_add_motd($sqlc);
elseif ('edit_motd' == $action)
  edit_motd($sqlc);
elseif ('do_edit_motd' == $action)
  do_edit_motd($sqlc);
else
  add_motd();

unset($action);
unset($action_permission);
unset($lang_motd);

require_once 'footer.php';


?>
