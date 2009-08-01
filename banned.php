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

//########################################################################################################################
// SHOW BANNED LIST
//########################################################################################################################
function show_list() {
 global  $lang_global, $lang_banned, $output, $realm_db, $itemperpage, $action_permission, $user_lvl;

valid_login($action_permission['read']);

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $ban_type = (isset($_GET['ban_type'])) ? $sql->quote_smart($_GET['ban_type']) : "account_banned";
 $key_field = ($ban_type == "account_banned") ? "id" :"ip";
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : $key_field;

 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

 $query_1 = $sql->query("SELECT count(*) FROM $ban_type");
 $all_record = $sql->result($query_1,0);

 $result = $sql->query("SELECT $key_field, bandate, unbandate, bannedby, SUBSTRING_INDEX(banreason,' ',3) FROM $ban_type ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
 $this_page = $sql->num_rows($result);

  $output .= "<center>
	<table class=\"top_hidden\">
       <tr><td>";
       if($user_lvl >= $action_permission['insert'])
		makebutton($lang_banned['add_to_banned'], "banned.php?action=add_entry",180);
		 if ($ban_type === "account_banned") makebutton($lang_banned['banned_ips'], "banned.php?ban_type=ip_banned",180);
			else makebutton($lang_banned['banned_accounts'], "banned.php?ban_type=account_banned",180);

		makebutton($lang_global['back'], "javascript:window.history.back()",140);
  $output .= "</td>
     <td align=\"right\">".generate_pagination("banned.php?action=show_list&amp;order_by=$order_by&amp;ban_type=$ban_type&amp;dir=".!$dir, $all_record, $itemperpage, $start)."</td>
	 </tr></table>
  <script type=\"text/javascript\">
	answerbox.btn_ok='{$lang_global['yes_low']}';
	answerbox.btn_cancel='{$lang_global['no']}';
	var del_banned = 'banned.php?action=do_delete_entry&amp;ban_type=$ban_type&amp;$key_field=';
 </script>
 <table class=\"lined\">
   <tr>
	<th width=\"5%\">{$lang_global['delete_short']}</td>
	<th width=\"19%\"><a href=\"banned.php?order_by=$key_field&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by==$key_field ? " class=\"$order_dir\"" : "").">{$lang_banned['ip_acc']}</a></th>
	<th width=\"18%\"><a href=\"banned.php?order_by=bandate&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='bandate' ? " class=\"$order_dir\"" : "").">{$lang_banned['bandate']}</a></th>
	<th width=\"18%\"><a href=\"banned.php?order_by=unbandate&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='unbandate' ? " class=\"$order_dir\"" : "").">{$lang_banned['unbandate']}</a></th>
	<th width=\"15%\"><a href=\"banned.php?order_by=bannedby&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='bannedby' ? " class=\"$order_dir\"" : "").">{$lang_banned['bannedby']}</a></th>
	<th width=\"25%\"><a href=\"banned.php?order_by=banreason&amp;ban_type=$ban_type&amp;dir=$dir\"".($order_by=='banreason' ? " class=\"$order_dir\"" : "").">{$lang_banned['banreason']}</a></th>
  </tr>";

 while ($ban = $sql->fetch_row($result)){

  if ($ban_type === "account_banned"){
	$result1 = $sql->query("SELECT username FROM account WHERE id ='$ban[0]'");
	$owner_acc_name = $sql->result($result1, 0, 'username');
	$name_out = "<a href=\"user.php?action=edit_user&amp;error=11&amp;id=$ban[0]\">$owner_acc_name</a>";
  } else {
			$name_out = $ban[0];
			$owner_acc_name = $ban[0];
		}

  $output .= "<tr>
			<td>";
			if($user_lvl >= $action_permission['delete'])
			  $output .= "<img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('{$lang_global['delete']}: <font color=white>$owner_acc_name</font><br />{$lang_global['are_you_sure']}', del_banned + '$ban[0]');\" style=\"cursor:pointer;\" />";
			$output .= "</td>
			<td>$name_out</td>
			<td>".date('d-m-Y G:i', $ban[1])."</td>
			<td>".date('d-m-Y G:i', $ban[2])."</td>
			<td>$ban[3]</td>
			<td>$ban[4]</td>
			</tr>";
  }
  $output .= "<tr>
      <td colspan=\"6\" align=\"right\" class=\"hidden\">{$lang_banned['tot_banned']} : $all_record</td>
	 </tr>
 </table></center><br/>";
 $sql->close();
}


//########################################################################################################################
// DO DELETE ENTRY FROM LIST
//########################################################################################################################
function do_delete_entry() {
 global $lang_global, $realm_db, $action_permission, $user_lvl;
valid_login($action_permission['delete']);
 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 if(isset($_GET['ban_type'])) $ban_type = $sql->quote_smart($_GET['ban_type']);
	else redirect("banned.php?error=1");

 $key_field = ($ban_type == "account_banned") ? "id" : "ip";

 if(isset($_GET[$key_field])) $entry = $sql->quote_smart($_GET[$key_field]);
	else redirect("banned.php?error=1");

 $sql->query("DELETE FROM $ban_type WHERE $key_field = '$entry'");

 if ($sql->affected_rows()) {
	$sql->close();
	redirect("banned.php?error=3&ban_type=$ban_type");
    } else {
 	 $sql->close();
	 redirect("banned.php?error=2&ban_type=$ban_type");
	}
}


//########################################################################################################################
//  BAN NEW IP
//########################################################################################################################
function add_entry() {
 global  $lang_global, $lang_banned, $output, $action_permission, $user_lvl;
 valid_login($action_permission['insert']);
  $output .= "<center>
  <fieldset class=\"half_frame\">
	<legend>{$lang_banned['ban_entry']}</legend>
 <form method=\"get\" action=\"banned.php\" name=\"form\">
 <input type=\"hidden\" name=\"action\" value=\"do_add_entry\" />
 	<table class=\"flat\">
	    <tr>
          <td>{$lang_banned['ban_type']}</td>
          <td><select name=\"ban_type\">
				<option value=\"ip_banned\" >{$lang_banned['ip']}</option>
				<option value=\"account_banned\" >{$lang_banned['account']}</option>
		      </select>
		  </td>
      </tr>
	  <tr>
        <td>{$lang_banned['entry']}</td>
        <td><input type=\"text\" name=\"entry\" size=\"40\" maxlength=\"20\" value=\"\" /></td>
      </tr>
	  <tr>
        <td>{$lang_banned['ban_time']}</td>
        <td><input type=\"text\" name=\"bantime\" size=\"40\" maxlength=\"16\" value=\"1\" /></td>
      </tr>
	  <tr>
        <td>{$lang_banned['banreason']}</td>
        <td><input type=\"text\" name=\"banreason\" size=\"40\" maxlength=\"255\" value=\"\" /></td>
      </tr>
      <tr>
        <td>";
			makebutton($lang_banned['ban_entry'], "javascript:do_submit()",150);
$output .= "</td><td>";
			makebutton($lang_global['back'], "banned.php",295);
	$output .= "</td></tr>
     </table>

    </form></fieldset><br/><br/></center>";
}


//########################################################################################################################
//DO  BAN NEW IP/ACC
//########################################################################################################################
function do_add_entry() {
 global $lang_global, $realm_db, $user_name, $lang_banned, $output, $action_permission, $user_lvl;
valid_login($action_permission['insert']);
 if((empty($_GET['ban_type']))||(empty($_GET['entry'])) ||(empty($_GET['bantime'])))
	redirect("banned.php?error=1&action=add_entry");

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $ban_type = $sql->quote_smart($_GET['ban_type']);

 $entry = $sql->quote_smart($_GET['entry']);
 if ($ban_type == "account_banned") {
	$result1 = $sql->query("SELECT id FROM account WHERE username ='$entry'");
	if (!$sql->num_rows($result1)) redirect("banned.php?error=4&action=add_entry");
		else $entry = $sql->result($result1, 0, 'id');
 }

 $bantime = time() + (3600 * $sql->quote_smart($_GET['bantime']));

 $banreason = (isset($_GET['banreason']) && ($_GET['banreason'] != '')) ? $sql->quote_smart($_GET['banreason']) : "none";

 if ($ban_type === "account_banned"){
	$result = $sql->query("SELECT count(*) FROM account_banned WHERE id = '$entry'");
	if(!$sql->result($result, 0))
		$sql->query("INSERT INTO account_banned (id, bandate, unbandate, bannedby, banreason, active)
					   VALUES ('$entry',".time().",$bantime,'$user_name','$banreason', 1)");

 } else {
		$sql->query("INSERT INTO ip_banned (ip, bandate, unbandate, bannedby, banreason)
						VALUES ('$entry',".time().",$bantime,'$user_name','$banreason')");
		}

 if ($sql->affected_rows()) {
	$sql->close();
	redirect("banned.php?error=3&ban_type=$ban_type");
	} else {
		$sql->close();
		redirect("banned.php?error=2&ban_type=$ban_type");
	 }
 $sql->close();
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
    $output .= "<h1><font class=\"error\">{$lang_banned['err_del_entry']}</font></h1>";
   break;
case 3:
    $output .= "<h1><font class=\"error\">{$lang_banned['updated']}</font></h1>";
   break;
case 4:
    $output .= "<h1><font class=\"error\">{$lang_banned['acc_not_found']}</font></h1>";
   break;
default: //no error
     $output .= "<h1>{$lang_banned['banned_list']}</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {

case "do_delete_entry":
 	do_delete_entry();
    break;
case "add_entry":
 	add_entry();
 	break;
case "do_add_entry":
	do_add_entry();
	break;
default:
    show_list();
}

require_once("footer.php");
?>
