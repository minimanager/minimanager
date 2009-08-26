<?php


require_once("header.php");
valid_login($action_permission['read']);

//#############################################################################
//  BROWSE  TICKETS
//#############################################################################
function browse_tickets()
{
  global  $lang_global, $lang_ticket, $output, $characters_db, $realm_id, $itemperpage, $server_type, $action_permission, $user_lvl;

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  if ($server_type)
  {
    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : "guid";
    if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by)) $order_by="guid";
  }
  else
  {
    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : "ticket_id";
    if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by)) $order_by="ticket_id";
  }

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================

  //get total number of items
  if($server_type)
    $query_1 = $sqlc->query("SELECT count(*) FROM gm_tickets");
  else
    $query_1 = $sqlc->query("SELECT count(*) FROM character_ticket");
  $all_record = $sqlc->result($query_1,0);
  unset($query_1);

  if($server_type)
    $query = $sqlc->query("SELECT gm_tickets.guid, gm_tickets.playerGuid, SUBSTRING_INDEX(gm_tickets.message,' ',6),`characters`.name
      FROM gm_tickets,`characters`
          WHERE gm_tickets.playerGuid = `characters`.`guid`
            ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
 else
   $query = $sqlc->query("SELECT character_ticket.ticket_id, character_ticket.guid, SUBSTRING_INDEX(character_ticket.ticket_text,' ',6), `characters`.name
     FROM character_ticket,`characters`
         WHERE character_ticket.guid = `characters`.`guid`
           ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");

  $output .="
        <script type=\"text/javascript\" src=\"libs/js/check.js\"></script>
        <center>
          <table class=\"top_hidden\">
            <tr>
              <td width=\"25%\" align=\"right\">";
  $output .= generate_pagination("ticket.php?action=browse_tickets&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
  $output .= "
              </td>
            </tr>
          </table>";
  $output .= "
          <form method=\"get\" action=\"ticket.php\" name=\"form\">
            <input type=\"hidden\" name=\"action\" value=\"delete_tickets\" />
            <input type=\"hidden\" name=\"start\" value=\"$start\" />
            <table class=\"lined\">
              <tr>";
  if($user_lvl >= $action_permission['delete'])
    $output .="
                <th width=\"7%\"><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll(document.form);\" /></th>";
  if($user_lvl >= $action_permission['update'])
    $output .="
                <th width=\"7%\">{$lang_global['edit']}</th>";
  if ($server_type)
    $output .="
                <th width=\"10%\"><a href=\"ticket.php?order_by=guid&amp;start=$start&amp;dir=$dir\">".($order_by=='guid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_ticket['id']}</a></th>
                <th width=\"16%\"><a href=\"ticket.php?order_by=playerGuid&amp;start=$start&amp;dir=$dir\">".($order_by=='playerGuid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_ticket['sender']}</a></th>";
  else
    $output .="
                <th width=\"10%\"><a href=\"ticket.php?order_by=ticket_id&amp;start=$start&amp;dir=$dir\">".($order_by=='ticket_id' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_ticket['id']}</a></th>
                <th width=\"16%\"><a href=\"ticket.php?order_by=guid&amp;start=$start&amp;dir=$dir\">".($order_by=='guid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" alt=\"\" /> " : "")."{$lang_ticket['sender']}</a></th>";
  $output .="
                <th width=\"60%\">{$lang_ticket['ticket_text']}</th>
              </tr>";
  while ($ticket = $sqlc->fetch_row($query))
  {
    $output .= "
              <tr>";
    if($user_lvl >= $action_permission['delete'])
      $output .="
                <td><input type=\"checkbox\" name=\"check[]\" value=\"$ticket[0]\" onclick=\"CheckCheckAll(document.form);\" /></td>";
    if($user_lvl >= $action_permission['update'])
      $output .="
                <td><a href=\"ticket.php?action=edit_ticket&amp;error=4&amp;id=$ticket[0]\">{$lang_global['edit']}</a></td>";
    $output .="
                <td>$ticket[0]</td>
                <td><a href=\"char.php?id=$ticket[1]\">".htmlentities($ticket[3])."</a></td>
                <td>".htmlentities($ticket[2])." ...</td>
              </tr>";
  }
  unset($query);
  unset($ticket);
  $output .= "
              <tr>
                <td colspan=\"5\" align=\"right\" class=\"hidden\" width=\"25%\">";
  $output .= generate_pagination("ticket.php?action=browse_tickets&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
  $output .= "
                </td>
              </tr>
              <tr>
                <td colspan=\"3\" align=\"left\" class=\"hidden\">";
  if($user_lvl >= $action_permission['delete'])
                  makebutton($lang_ticket['del_selected_tickets'], "javascript:do_submit()\" type=\"wrn",230);
  $output .= "
                </td>
                <td colspan=\"2\" align=\"right\" class=\"hidden\">{$lang_ticket['tot_tickets']}: $all_record</td>
              </tr>
            </table>
          </form>
          <br />
        </center>
";

}


//########################################################################################################################
//  DELETE TICKETS
//########################################################################################################################
function delete_tickets()
{
  global $lang_global, $characters_db, $realm_id, $server_type, $action_permission;
  valid_login($action_permission['delete']);

  if(!isset($_GET['check'])) redirect("ticket.php?error=1");

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $check = $sqlc->quote_smart($_GET['check']);

  $deleted_tickets = 0;
  for ($i=0; $i<count($check); $i++)
  {
    if ($check[$i] != "" )
    {
      if ($server_type)
        $query = $sqlc->query("DELETE FROM gm_tickets WHERE guid = '$check[$i]'");
      else
        $query = $sqlc->query("DELETE FROM character_ticket WHERE ticket_id = '$check[$i]'");
      $deleted_tickets++;
    }
  }

  if ($deleted_tickets == 0)
    redirect("ticket.php?error=3");
  else
    redirect("ticket.php?error=2");
}


//########################################################################################################################
//  EDIT   TICKET
//########################################################################################################################
function edit_ticket()
{
  global  $lang_global, $lang_ticket, $output, $characters_db, $realm_id, $server_type, $action_permission;
  valid_login($action_permission['update']);

  if(!isset($_GET['id'])) redirect("Location: ticket.php?error=1");

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $id = $sqlc->quote_smart($_GET['id']);
  if(is_numeric($id)); else redirect("ticket.php?error=1");

  if ($server_type)
    $query = $sqlc->query("SELECT gm_tickets.playerGuid, gm_tickets.message text, `characters`.name
      FROM gm_tickets,`characters`
        LEFT JOIN gm_tickets k1 ON k1.`playerGuid`=`characters`.`guid`
          WHERE gm_tickets.playerGuid = `characters`.`guid` AND gm_tickets.guid = '$id'");
  else
    $query = $sqlc->query("SELECT character_ticket.guid, character_ticket.ticket_text, `characters`.name
      FROM character_ticket,`characters`
        LEFT JOIN character_ticket k1 ON k1.`guid`=`characters`.`guid`
          WHERE character_ticket.guid = `characters`.`guid` AND character_ticket.ticket_id = '$id'");

  if ($ticket = $sqlc->fetch_row($query))
  {
    $output .= "
        <center>
          <fieldset style=\"width: 550px;\">
            <legend>{$lang_ticket['edit_reply']}</legend>
            <form method=\"post\" action=\"ticket.php?action=do_edit_ticket\" name=\"form\">
              <input type=\"hidden\" name=\"id\" value=\"$id\" />
                <table class=\"flat\">
                  <tr>
                    <td>{$lang_ticket['ticket_id']}</td>
                    <td>$id</td>
                  </tr>
                  <tr>
                    <td>{$lang_ticket['submitted_by']}:</td>
                    <td><a href=\"char.php?id=$ticket[0]\">".htmlentities($ticket[2])."</a></td>
                  </tr>
                  <tr>
                    <td valign=\"top\">{$lang_ticket['ticket_text']}</td>
                    <td><textarea name=\"new_text\" rows=\"5\" cols=\"40\">".htmlentities($ticket[1])."</textarea></td>
                  </tr>
                  <tr>
                    <td>";
                      makebutton($lang_ticket['update'], "javascript:do_submit()\" type=\"wrn",130);
    $output .= "
                    </td>
                    <td>
                      <table class=\"hidden\">
                        <tr>
                          <td>";
                            makebutton($lang_ticket['send_ingame_mail'], "mail.php?type=ingame_mail&amp;to=$ticket[2]",130);
    $output .= "
                          </td>
                          <td>";
                            makebutton($lang_global['back'], "ticket.php\" type=\"def",130);
    $output .= "
                          </td>
                        </tr>
                      </table>";
    $output .= "
                    </td>
                  </tr>
                </table>
              </form>
            </fieldset>
            <br /><br />
          </center>";
  }
  else
    error($lang_global['err_no_records_found']);

}


//########################################################################################################################
//  DO EDIT  TICKET
//########################################################################################################################
function do_edit_ticket()
{
  global $characters_db, $realm_id, $server_type, $action_permission;
  valid_login($action_permission['update']);

  if(empty($_POST['new_text']) || empty($_POST['id']) )
    redirect("ticket.php?error=1");

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $new_text = $sqlc->quote_smart($_POST['new_text']);
  $id = $sqlc->quote_smart($_POST['id']);
  if(is_numeric($id)); else redirect("ticket.php?error=1");

  if ($server_type)
    $query = $sqlc->query("UPDATE gm_tickets SET message='$new_text' WHERE guid = '$id'");
  else
    $query = $sqlc->query("UPDATE character_ticket SET ticket_text='$new_text' WHERE ticket_id = '$id'");

  if ($sqlc->affected_rows())
  {
    redirect("ticket.php?error=5");
  }
  else
  {
    redirect("ticket.php?error=6");
  }
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";

$lang_ticket = lang_ticket();

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">{$lang_ticket['ticked_deleted']}</font></h1>";
    break;
  case 3:
    $output .= "
          <h1><font class=\"error\">{$lang_ticket['ticket_not_deleted']}</font></h1>";
    break;
  case 4:
    $output .= "
          <h1>{$lang_ticket['edit_ticked']}</h1>";
    break;
  case 5:
    $output .= "
          <h1><font class=\"error\">{$lang_ticket['ticket_updated']}</font></h1>";
    break;
  case 6:
    $output .= "
          <h1><font class=\"error\">{$lang_ticket['ticket_update_err']}</font></h1>";
    break;
  default: //no error
    $output .= "
          <h1>{$lang_ticket['browse_tickets']}</h1>";
}

unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "browse_tickets":
    browse_tickets();
    break;
  case "delete_tickets":
    delete_tickets();
    break;
  case "edit_ticket":
    edit_ticket();
    break;
  case "do_edit_ticket":
    do_edit_ticket();
    break;
  default:
    browse_tickets();
}

unset($action);
unset($action_permission);
unset($lang_tikcet);

require_once("footer.php");

?>
