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
valid_login(1);

$ticket_type = Array(
	0 => array(0,$lang_ticket['other']),
	1 => array(1,$lang_ticket['stuck']),
	2 => array(2,$lang_ticket['behavior']),
	3 => array(3,$lang_ticket['guild']),
	4 => array(4,$lang_ticket['item']),
	5 => array(5,$lang_ticket['environmental']),
	6 => array(6,$lang_ticket['creep']),
	7 => array(7,$lang_ticket['quest']),
	8 => array(8,$lang_ticket['technical']),
	9 => array(9,$lang_ticket['account']),
	10 => array(10,$lang_ticket['character'])
);

//########################################################################################################################
//  BROWSE  TICKETS
//########################################################################################################################
function browse_tickets() {
 global  $lang_global, $lang_ticket, $output, $characters_db, $realm_id, $itemperpage, $ticket_type;

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
 
 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;

 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "ticket_id";
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;
 
 //get total number of items
 $query_1 = $sql->query("SELECT count(*) FROM character_ticket");
 $all_record = $sql->result($query_1,0);

 $query = $sql->query("SELECT character_ticket.ticket_id, character_ticket.guid,SUBSTRING_INDEX(character_ticket.ticket_text,' ',6),
						character_ticket.ticket_category,`characters`.name
						FROM character_ticket,`characters`
						LEFT JOIN character_ticket k1 ON k1.`guid`=`characters`.`guid`
						WHERE character_ticket.guid = `characters`.`guid`
						ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
 $this_page = $sql->num_rows($query);

 $output .="<script type=\"text/javascript\" src=\"js/check.js\"></script>
			<center><table class=\"top_hidden\">
			<tr><td>";
 $output .= generate_pagination("ticket.php?action=browse_tickets&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
 $output .= "</td></tr></table>";

 $output .= "<form method=\"get\" action=\"ticket.php\" name=\"form\">
	<input type=\"hidden\" name=\"action\" value=\"delete_tickets\">
	<input type=\"hidden\" name=\"start\" value=\"$start\">
 <table class=\"lined\">
   <tr>
	<th width=\"5%\"><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll(document.form);\" /></th>
	<th width=\"5%\">{$lang_global['edit']}</th>
	<th width=\"8%\"><a href=\"ticket.php?order_by=ticket_id&amp;start=$start&amp;dir=$dir\">".($order_by=='ticket_id' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_ticket['id']}</a></th>
	<th width=\"15%\"><a href=\"ticket.php?order_by=guid&amp;start=$start&amp;dir=$dir\">".($order_by=='guid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_ticket['sender']}</a></th>
	<th width=\"55%\">{$lang_ticket['ticket_text']}</th>
    <th width=\"13%\"><a href=\"ticket.php?order_by=ticket_category&amp;start=$start&amp;dir=$dir\">".($order_by=='ticket_category' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_ticket['category']}</a></tthd>
  </tr>";

 while ($ticket = $sql->fetch_row($query)){
	$output .= "<tr>
   		    <td><input type=\"checkbox\" name=\"check[]\" value=\"$ticket[0]\" onclick=\"CheckCheckAll(document.form);\" /></td>
   		    <td><a href=\"ticket.php?action=edit_ticket&amp;error=4&amp;id=$ticket[0]\">{$lang_global['edit']}</a></td>
   		    <td>$ticket[0]</td>
   		    <td><a href=\"char.php?id=$ticket[1]\">$ticket[4]</a></td>
			<td>$ticket[2] ...</td>
			<td>{$ticket_type[$ticket[3]][1]}</td>
            </tr>";
}

$output .= "<tr><td colspan=\"12\" class=\"hidden\"><br /></td></tr>
	<tr>
		<td colspan=\"4\" align=\"left\" class=\"hidden\">";
			makebutton($lang_ticket['del_selected_tickets'], "javascript:do_submit()",200);
$output .= "</td>
      <td colspan=\"2\" align=\"right\" class=\"hidden\">{$lang_ticket['tot_tickets']}: $all_record</td>
	 </tr>
 </table>
 </form><br /></center>";
 
$sql->close();
}


//########################################################################################################################
//  DELETE TICKETS
//########################################################################################################################
function delete_tickets() {
global $lang_global, $characters_db, $realm_id;

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 if(isset($_GET['check'])) $check = $sql->quote_smart($_GET['check']);
	else redirect("ticket.php?error=1");

 $deleted_tickets = 0;

 for ($i=0; $i<count($check); $i++) {
    if ($check[$i] != "" ) {
		$query = $sql->query("DELETE FROM character_ticket WHERE ticket_id = '$check[$i]'");
		$deleted_tickets++;
		}
	}

 $sql->close();

 if ($deleted_tickets == 0) redirect("ticket.php?error=3");
	else redirect("ticket.php?error=2");
}


//########################################################################################################################
//  EDIT   TICKET
//########################################################################################################################
function edit_ticket() {
 global  $lang_global, $lang_ticket, $output, $characters_db, $realm_id, $ticket_type;

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
 
 if(isset($_GET['id'])) $id = $sql->quote_smart($_GET['id']);
	else redirect("ticket.php?error=1");

 $query = $sql->query("SELECT character_ticket.guid, character_ticket.ticket_text,
						character_ticket.ticket_category,`characters`.name
						FROM character_ticket,`characters`
						LEFT JOIN character_ticket k1 ON k1.`guid`=`characters`.`guid`
						WHERE character_ticket.guid = `characters`.`guid` AND character_ticket.ticket_id = '$id'");

 if ($ticket = $sql->fetch_row($query)) {
	$output .= "<center>
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
        <td><a href=\"char.php?id=$ticket[0]\">$ticket[3]</a></td>
      </tr>
	  <tr>
        <td valign=\"top\">{$lang_ticket['ticket_text']}</td>
        <td><textarea name=\"new_text\" rows=\"5\" cols=\"40\">$ticket[1]</textarea></td>
      </tr>
      <tr>
        <td>{$lang_ticket['category']}</td>
        <td><select name=\"new_category\">";
			foreach ($ticket_type as $type){ 
					$output .= "<option value=\"{$type[0]}\" ";
					if ($ticket[2]==$type[0]) $output .= "selected=\"selected\" ";
					$output .= ">{$type[1]}</option>";
					}
			$output .= "</select></td>
      </tr>
      <tr>
        <td>";
			makebutton($lang_ticket['update'], "javascript:do_submit()",130);
$output .= "</td>
        <td>
		<table class=\"hidden\">
          <tr><td>";
			makebutton($lang_ticket['send_ingame_mail'], "mail.php?type=ingame_mail&amp;to=$ticket[3]",205);
			makebutton($lang_global['back'], "ticket.php",120);
$output .= "</td></tr>
        </table>";
	$output .= "</td></tr>
     </table>
     </form></fieldset><br /><br /></center>";
  } else error($lang_global['err_no_records_found']);
  
 $sql->close();
}


//########################################################################################################################
//  DO EDIT  TICKET
//########################################################################################################################
function do_edit_ticket() {
 global $characters_db, $realm_id;

 if((!isset($_POST['new_category'])) ||($_POST['new_category'] === '')|| empty($_POST['new_text']) || empty($_POST['id']) ) {
   redirect("ticket.php?error=1");
 } 

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
 
 $new_category = $sql->quote_smart($_POST['new_category']);
 $new_text = $sql->quote_smart($_POST['new_text']);
 $id = $sql->quote_smart($_POST['id']);

 $query = $sql->query("UPDATE character_ticket SET ticket_text='$new_text', ticket_category ='$new_category' WHERE ticket_id = '$id'");

 if ($sql->affected_rows()) {
	$sql->close();
	redirect("ticket.php?error=5");
    } else {
		$sql->close();
		redirect("ticket.php?error=6");
	}
}


//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "<div class=\"top\">";
switch ($err) {
case 1:
   $output .= "<h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
   break;
case 2:
   $output .= "<h1><font class=\"error\">{$lang_ticket['ticked_deleted']}</font></h1>";
   break;
case 3:
   $output .= "<h1><font class=\"error\">{$lang_ticket['ticket_not_deleted']}</font></h1>";
   break;
case 4:
   $output .= "<h1>{$lang_ticket['edit_ticked']}</h1>";
   break;
case 5:
   $output .= "<h1><font class=\"error\">{$lang_ticket['ticket_updated']}</font></h1>";
   break;
case 6:
   $output .= "<h1><font class=\"error\">{$lang_ticket['ticket_update_err']}</font></h1>";
   break;
default: //no error
    $output .= "<h1>{$lang_ticket['browse_tickets']}</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
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

require_once("footer.php");
?>
