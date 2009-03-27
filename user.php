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
valid_login($action_permission['read']);
require_once("scripts/id_tab.php");
require_once("scripts/defines.php");

//########################################################################################################################
// BROWSE USERS
//########################################################################################################################
function browse_users() {
 global $lang_global, $lang_user, $output, $realm_db, $mmfpm_db, $itemperpage, $user_lvl, $user_name, $gm_level_arr, $exp_lvl_arr, $action_permission;

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "id";

 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

//get total number of items
 $query_1 = $sql->query("SELECT count(*) FROM account");
 $all_record = $sql->result($query_1,0);

 $query = $sql->query("SELECT id,username,gmlevel,email,joindate,last_ip,failed_logins,locked,last_login,online,expansion
		FROM account ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
 $this_page = $sql->num_rows($query);


//==========================top tage navigaion starts here========================
 $output .="<script type=\"text/javascript\" src=\"js/check.js\"></script>
			<center><table class=\"top_hidden\">
			<tr><td>";
if($user_lvl >= $action_permission['update'])
{
 makebutton($lang_user['add_acc'], "user.php?action=add_new", 124);
 makebutton($lang_user['cleanup'], "cleanup.php", 122);
 makebutton($lang_user['backup'], "backup.php", 122);
}
 makebutton($lang_global['back'], "javascript:window.history.back()", 122);
 $output .= " </td><td align=\"right\" width=\"25%\" rowspan=\"2\">";
 $output .= generate_pagination("user.php?action=brows_user&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
 $output .= "</td></tr>
	<tr align=\"left\"><td>
	  <table class=\"hidden\">
       <tr><td>
	   <form action=\"user.php\" method=\"get\" name=\"form\">
	   <input type=\"hidden\" name=\"action\" value=\"search\" />
	   <input type=\"hidden\" name=\"error\" value=\"3\" />
	   <input type=\"text\" size=\"42\" maxlength=\"50\" name=\"search_value\" />
	   <select name=\"search_by\">
	    <option value=\"username\">{$lang_user['by_name']}</option>
		<option value=\"id\">{$lang_user['by_id']}</option>
		<option value=\"gmlevel\">{$lang_user['by_gm_level']}</option>
		<option value=\"greater_gmlevel\">{$lang_user['greater_gm_level']}</option>
		<option value=\"email\">{$lang_user['by_email']}</option>
		<option value=\"joindate\">{$lang_user['by_join_date']}</option>
		<option value=\"last_ip\">{$lang_user['by_ip']}</option>
		<option value=\"failed_logins\">{$lang_user['by_failed_loggins']}</option>
		<option value=\"last_login\">{$lang_user['by_last_login']}</option>
		<option value=\"online\">{$lang_user['by_online']}</option>
		<option value=\"banned\">{$lang_user['by_banned']}</option>
		<option value=\"locked\">{$lang_user['by_locked']}</option>
		<option value=\"expansion\">{$lang_user['by_expansion']}</option>
	   </select></form></td>
	   <td>";
		makebutton($lang_global['search'], "javascript:do_submit()",80);
	  $output .= "</td></tr></table>
		</td></tr></table>";
//==========================top tage navigaion ENDS here ========================

 $output .= "<form method=\"get\" action=\"user.php\" name=\"form1\">
	 <input type=\"hidden\" name=\"action\" value=\"del_user\" />
	 <input type=\"hidden\" name=\"start\" value=\"$start\" />
	 <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
 <table class=\"lined\">
   <tr>";
   if($user_lvl >= $action_permission['update']) $output.= "<th width=\"1%\"><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll(document.form1);\" /></th>";
    else $output .= "<th width=\"1%\"></th>";
   $output .="<th width=\"5%\"><a href=\"user.php?order_by=id&amp;start=$start&amp;dir=$dir\">".($order_by=='id' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['id']}</a></th>
	<th width=\"21%\"><a href=\"user.php?order_by=username&amp;start=$start&amp;dir=$dir\">".($order_by=='username' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['username']}</a></th>
	<th width=\"5%\"><a href=\"user.php?order_by=gmlevel&amp;start=$start&amp;dir=$dir\">".($order_by=='gmlevel' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['gm_level']}</a></th>
	<th width=\"5%\"><a href=\"user.php?order_by=expansion&amp;start=$start&amp;dir=$dir\">".($order_by=='expansion' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."EXP</a></th>
	<th width=\"16%\"><a href=\"user.php?order_by=email&amp;start=$start&amp;dir=$dir\">".($order_by=='email' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['email']}</a></th>
	<th width=\"14%\"><a href=\"user.php?order_by=joindate&amp;start=$start&amp;dir=$dir\">".($order_by=='joindate' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['join_date']}</a></th>
	<th width=\"10%\"><a href=\"user.php?order_by=last_ip&amp;start=$start&amp;dir=$dir\">".($order_by=='last_ip' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['ip']}</a></th>
	<th width=\"5%\"><a href=\"user.php?order_by=failed_logins&amp;start=$start&amp;dir=$dir\">".($order_by=='failed_logins' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['failed_logins']}</a></th>
	<th width=\"3%\"><a href=\"user.php?order_by=locked&amp;start=$start&amp;dir=$dir\">".($order_by=='locked' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['locked']}</a></th>
	<th width=\"14%\"><a href=\"user.php?order_by=last_login&amp;start=$start&amp;dir=$dir\">".($order_by=='last_login' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['last_login']}</a></th>
	<th width=\"3%\"><a href=\"user.php?order_by=online&amp;start=$start&amp;dir=$dir\">".($order_by=='online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['online']}</a></th>
	<th width=\"3%\">{$lang_global['country']}</th>
   </tr>";

 while ($data = $sql->fetch_row($query)){


		$ip = $data[5];

        $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
	   	$nation = $sql->query("SELECT c.code, c.country FROM ip2nationCountries c, ip2nation i WHERE i.ip < INET_ATON('".$ip."') AND c.code = i.country ORDER BY i.ip DESC LIMIT 0,1;");
		$country = $sql->fetch_row($nation);

	if (($user_lvl >= $data[2])||($user_name == $data[1])){
   		$output .= "<tr>";
		if ($user_lvl >= $action_permission['update']) $output .= "<td><input type=\"checkbox\" name=\"check[]\" value=\"$data[0]\" onclick=\"CheckCheckAll(document.form1);\" /></td>";
                 else $output .= "<td></td>";
   		$output .= "<td>$data[0]</td>
           	<td><a href=\"user.php?action=edit_user&amp;error=11&amp;id=$data[0]\">$data[1]</a></td>
			<td>".$gm_level_arr[$data[2]][2]."</td>
			<td>".$exp_lvl_arr[$data[10]][2]."</td>";
                if ($user_lvl >= $action_permission['update']) $output .= "
			<td><a href=\"mailto:$data[3]\">".substr($data[3],0,15)."</a></td>";
                else $output .= "<td>***@***</td>";
		$output .="<td class=\"small\">$data[4]</td>";
		if (($user_lvl >= $action_permission['update'])||($user_name == $data[1])) $output .= "<td>$data[5]</td>";
			else $output .= "<td>******</td>";
		$output .= "<td>".(($data[6]) ? $data[6] : "-")."</td>
			<td>".(($data[7]) ? $lang_global['yes_low'] : "-")."</td>
			<td class=\"small\">$data[8]</td>
			<td>".(($data[9]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
 			<td>".(($country[0]) ? "<img src='img/flags/".$country[0].".png' onmousemove='toolTip(\"".($country[1])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />" : "-")."</td>
            </tr>";
	} else {
		$output .= "<tr><td>*</td><td>***</td><td>You</td><td>Have</td><td>No</td>
			<td class=\"small\">Permission</td><td>to</td><td>View</td><td>this</td><td>Data</td><td>*</td><td>*</td></tr>";
	}
}
 $output .= "<tr><td colspan=\"12\" class=\"hidden\"><br /></td></tr>
	<tr>
		<td colspan=\"8\" align=\"left\" class=\"hidden\">";
		if($user_lvl >= $action_permission['update']) {
			makebutton($lang_user['del_selected_users'], "javascript:do_submit('form1',0)",220);
			makebutton($lang_user['backup_selected_users'], "javascript:do_submit('form1',1)",220); }
 $output .= "</td>
      <td colspan=\"4\" align=\"right\" class=\"hidden\">{$lang_user['tot_acc']} : $all_record</td>
	 </tr>
 </table></form><br /></center>";

 $sql->close();
}


//#######################################################################################################
//  SEARCH
//#######################################################################################################
function search() {
 global $lang_global, $lang_user, $output, $realm_db, $mmfpm_db, $user_lvl, $user_name, $sql_search_limit, $gm_level_arr, $action_permission;
valid_login($action_permission['read']);
 if(!isset($_GET['search_value']) || !isset($_GET['search_by'])) redirect("user.php?error=2");

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $search_value = $sql->quote_smart($_GET['search_value']);
 $search_by = $sql->quote_smart($_GET['search_by']);
 $search_menu = array('username', 'id', 'gmlevel', 'greater_gmlevel', 'email', 'joindate', 'last_ip', 'failed_logins', 'last_login', 'online', 'banned', 'locked', 'expansion');
 if (!in_array($search_by, $search_menu)) $search_by = 'username';

 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "id";
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

 switch ($search_by){

 case "greater_gmlevel":
	 $sql_query = "SELECT id,username,gmlevel,email,joindate,last_ip,failed_logins,locked,last_login,online
		 FROM account WHERE gmlevel > $search_value ORDER BY $order_by $order_dir LIMIT $sql_search_limit";
 break;

 case "banned":
	$sql_query = "SELECT id,username,gmlevel,email,joindate,last_ip,failed_logins,locked,last_login,online
		 FROM account WHERE id = 0 ";
	$que = $sql->query("SELECT id FROM account_banned");
	while ($banned = $sql->fetch_row($que)) $sql_query .= "OR id =$banned[0] ";
	 $sql_query .= " ORDER BY $order_by $order_dir LIMIT $sql_search_limit";
 break;

 case "failed_logins":
	 $sql_query = "SELECT id,username,gmlevel,email,joindate,last_ip,failed_logins,locked,last_login,online
		 FROM account WHERE failed_logins > $search_value ORDER BY $order_by $order_dir LIMIT $sql_search_limit";
 break;

 default:
    $sql_query = "SELECT id,username,gmlevel,email,joindate,last_ip,failed_logins,locked,last_login,online
		 FROM account WHERE $search_by LIKE '%$search_value%' ORDER BY $order_by $order_dir LIMIT $sql_search_limit";
 }

 $query = $sql->query($sql_query);
 $total_found = $sql->num_rows($query);

//==========================top tage navigaion starts here========================
 $output .= "<script type=\"text/javascript\" src=\"js/check.js\"></script>
			<center><table class=\"top_hidden\">
			<td align=\"left\">";
		makebutton($lang_user['user_list'], "user.php", 120);
		makebutton($lang_global['back'], "javascript:window.history.back()", 120);

 $output .= "</td><td><form action=\"user.php\" method=\"get\" name=\"form\">
	   <input type=\"hidden\" name=\"action\" value=\"search\" />
	   <input type=\"text\" size=\"32\" maxlength=\"50\" name=\"search_value\" />
	   <select name=\"search_by\">
	    <option value=\"username\">{$lang_user['by_name']}</option>
	    <option value=\"id\">{$lang_user['by_id']}</option>
		<option value=\"gmlevel\">{$lang_user['by_gm_level']}</option>
		<option value=\"greater_gmlevel\">{$lang_user['greater_gm_level']}</option>
		<option value=\"email\">{$lang_user['by_email']}</option>
		<option value=\"joindate\">{$lang_user['by_join_date']}</option>
		<option value=\"last_ip\">{$lang_user['by_ip']}</option>
		<option value=\"failed_logins\">{$lang_user['by_failed_loggins']}</option>
		<option value=\"last_login\">{$lang_user['by_last_login']}</option>
		<option value=\"online\">{$lang_user['by_online']}</option>
		<option value=\"banned\">{$lang_user['by_banned']}</option>
		<option value=\"locked\">{$lang_user['by_locked']}</option>
		<option value=\"expansion\">{$lang_user['by_expansion']}</option>
	   </select></form></td><td>";
	   makebutton($lang_global['search'], "javascript:do_submit()",80);
 $output .= "</td></tr>
	</table>";
//==========================top tage navigaion ENDS here ========================

 $output .= "<form method=\"get\" action=\"user.php\" name=\"form1\">
 <input type=\"hidden\" name=\"action\" value=\"del_user\" />
 <input type=\"hidden\" name=\"backup_op\" value=\"0\"/>
 <table class=\"lined\">
   <tr>
	<th width=\"1%\"><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll(document.form1);\" /></th>
	<th width=\"5%\"><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=id&amp;dir=$dir\">".($order_by=='id' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['id']}</a></th>
	<th width=\"21%\"><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=username&amp;dir=$dir\">".($order_by=='username' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['username']}</a></th>
	<th width=\"5%\"><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=gmlevel&amp;dir=$dir\">".($order_by=='gmlevel' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['gm_level']}</a></th>
    <th width=\"16%\><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=email&amp;dir=$dir\">".($order_by=='email' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['email']}</a></th>
	<th width=\"14%\"><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=joindate&amp;dir=$dir\">".($order_by=='joindate' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['join_date']}</a></th>
	<th width=\"10%\"><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=last_ip&amp;dir=$dir\">".($order_by=='last_ip' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['ip']}</a></th>
	<th width=\"5%\"><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=failed_logins&amp;dir=$dir\">".($order_by=='failed_logins' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['failed_logins']}</a></th>
	<th width=\"3%\"><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=locked&amp;dir=$dir\">".($order_by=='locked' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['locked']}</a></th>
	<th width=\"14%\"><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=last_login&amp;dir=$dir\">".($order_by=='last_login' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['last_login']}</a></th>
	<th width=\"3%\"><a href=\"user.php?action=search&amp;error=3&amp;search_value=$search_value&amp;search_by=$search_by&amp;order_by=online&amp;dir=$dir\">".($order_by=='online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_user['online']}</a></th>
	<th width=\"3%\">{$lang_global['country']}</th>
   </tr>";

 while ($data = $sql->fetch_row($query)){

		$ip = $data[5];

        $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
	   	$nation = $sql->query("SELECT c.code, c.country FROM ip2nationCountries c, ip2nation i WHERE i.ip < INET_ATON('".$ip."') AND c.code = i.country ORDER BY i.ip DESC LIMIT 0,1;");
		$country = $sql->fetch_row($nation);

	if (($user_lvl >= $data[2])||($user_name == $data[1])){
   		$output .= "<tr>";
		if ($user_lvl >= $action_permission['update']) $output .= "<td><input type=\"checkbox\" name=\"check[]\" value=\"$data[0]\" onclick=\"CheckCheckAll(document.form1);\" /></td>";
                 else $output .= "<td></td>";
   		$output .= "<td>$data[0]</td>
           	<td><a href=\"user.php?action=edit_user&amp;error=11&amp;id=$data[0]\">$data[1]</a></td>
			<td>".$gm_level_arr[$data[2]][2]."</td>";
                if ($user_lvl >= $action_permission['update']) $output .= "
			<td><a href=\"mailto:$data[3]\">".substr($data[3],0,15)."</a></td>";
                else $output .= "<td>***@***</td>";
		$output .="<td class=\"small\">$data[4]</td>";
		if (($user_lvl >= $action_permission['update'])||($user_name == $data[1])) $output .= "<td>$data[5]</td>";
			else $output .= "<td>******</td>";
		$output .= "<td>".(($data[6]) ? $data[6] : "-")."</td>
			<td>".(($data[7]) ? $lang_global['yes_low'] : "-")."</td>
			<td class=\"small\">$data[8]</td>
			<td>".(($data[9]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
 			<td>".(($country[0]) ? "<img src='img/flags/".$country[0].".png' onmousemove='toolTip(\"".($country[1])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />" : "-")."</td>
            </tr>";
	} else {
		$output .= "<tr><td>*</td><td>***</td><td>You</td><td>Have</td><td>No</td>
			<td class=\"small\">Permission</td><td>to</td><td>View</td><td>this</td><td>Data</td><td>*</td><td>*</td></tr>";
	}
}
$output .= "<tr><td colspan=\"12\" class=\"hidden\"><br /></td></tr>
	<tr>
		<td colspan=\"8\" align=\"left\" class=\"hidden\">";
		if($user_lvl >= $action_permission['update']) {
			makebutton($lang_user['del_selected_users'], "javascript:do_submit('form1',0)",220);
			makebutton($lang_user['backup_selected_users'], "javascript:do_submit('form1',1)",220); }
$output .= "</td>
      <td colspan=\"4\" align=\"right\" class=\"hidden\">{$lang_user['tot_found']} : $total_found : {$lang_global['limit']} $sql_search_limit</td>
	 </tr>
 </table>
 </form><br /></center>";

 $sql->close();
}


//#######################################################################################################
//  DELETE USER
//#######################################################################################################
function del_user() {
global $lang_global, $lang_user, $output, $realm_db, $action_permission;
valid_login($action_permission['delete']);
 if(isset($_GET['check'])) $check = $_GET['check'];
	else redirect("user.php?error=1");

 $pass_array = "";

 //skip to backup
 if (isset($_GET['backup_op'])&&($_GET['backup_op'] == 1)){
	for ($i=0; $i<count($check); $i++){
		$pass_array .= "&check%5B%5D=$check[$i]";
		}
	redirect("user.php?action=backup_user$pass_array");
	}

 $output .= "<center><img src=\"img/warn_red.gif\" width=\"48\" height=\"48\" alt=\"\" />
			<h1><font class=\"error\">{$lang_global['are_you_sure']}</font></h1><br />
			<font class=\"bold\">{$lang_user['acc_ids']}: ";

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 for ($i=0; $i<count($check); $i++){
	$username = $sql->result($sql->query("SELECT username FROM `account` WHERE id = {$check[$i]}"),0);
	$output .= "<a href=\"user.php?action=edit_user&amp;id=$check[$i]\" target=\"_blank\">$username, </a>";
	$pass_array .= "&amp;check%5B%5D=$check[$i]";
	}
 $sql->close();

 $output .= "<br />{$lang_global['will_be_erased']}</font><br /><br />
		<table class=\"hidden\">
          <tr><td>";
			makebutton($lang_global['yes'], "user.php?action=dodel_user$pass_array",120);
			makebutton($lang_global['no'], "user.php",120);
 $output .= "</td></tr>
        </table></center><br />";

}


//#####################################################################################################
//  DO DELETE USER
//#####################################################################################################
function dodel_user() {
 global $lang_global, $lang_user, $output, $realm_db, $characters_db, $realm_id, $user_lvl,
		$tab_del_user_characters, $tab_del_user_realmd, $action_permission;
valid_login($action_permission['delete']);
 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 if(isset($_GET['check'])) $check = $sql->quote_smart($_GET['check']);
	else redirect("user.php?error=1");

 $deleted_acc = 0;
 $deleted_chars = 0;
 require_once("scripts/del_lib.php");

 for ($i=0; $i<count($check); $i++) {
    if ($check[$i] != "" ) {
		list($flag,$del_char) = del_acc($check[$i]);
		if ($flag) {
			$deleted_acc++;
			$deleted_chars += $del_char;
		}
  }
 }
 $sql->close();
 $output .= "<center>";
 if ($deleted_acc == 0) $output .= "<h1><font class=\"error\">{$lang_user['no_acc_deleted']}</font></h1>";
   else {
	$output .= "<h1><font class=\"error\">{$lang_user['total']} <font color=blue>$deleted_acc</font> {$lang_user['acc_deleted']}</font><br /></h1>";
	$output .= "<h1><font class=\"error\">{$lang_user['total']} <font color=blue>$deleted_chars</font> {$lang_user['char_deleted']}</font></h1>";
	}
 $output .= "<br /><br />";
 $output .= "<table class=\"hidden\">
          <tr><td>";
			makebutton($lang_user['back_browsing'], "user.php", 200);
 $output .= "</td></tr>
        </table><br /></center>";
}


//#####################################################################################################
//  DO BACKUP USER
//#####################################################################################################
function backup_user() {
 global $lang_global, $lang_user, $output, $realm_db, $characters_db, $realm_id, $user_lvl,$backup_dir,$action_permission;
valid_login($action_permission['insert']);
 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 if(isset($_GET['check'])) $check = $sql->quote_smart($_GET['check']);
	else redirect("user.php?error=1");

 require_once("scripts/backup_tab.php");
 $subdir = "$backup_dir/accounts/".date("m_d_y_H_i_s")."_partial";
 mkdir($subdir, 0750);

 for ($t=0; $t<count($check); $t++) {
  if ($check[$t] != "" ) {
	$sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

	$query = $sql->query("SELECT id FROM account WHERE id = $check[$t]");
	$acc = $sql->fetch_array($query);

	$file_name_new = $acc[0]."_{$realm_db['name']}.sql";
	$fp = fopen("$subdir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));

	fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$realm_db['name']};\n")or die (error($lang_backup['file_write_err']));
	fwrite($fp, "USE {$realm_db['name']};\n\n")or die (error($lang_backup['file_write_err']));

	foreach ($tab_backup_user_realmd as $value) {
			$acc_query = $sql->query("SELECT * FROM $value[0] WHERE $value[1] = $acc[0]");
			$num_fields = $sql->num_fields($acc_query);
			$numrow = $sql->num_rows($acc_query);

			$result = "-- Dumping data for $value[0] ".date("m.d.y_H.i.s")."\n";
			$result .= "LOCK TABLES $value[0] WRITE;\n";
			$result .= "DELETE FROM $value[0] WHERE $value[1] = $acc[0];\n";

			if ($numrow){
				$result .= "INSERT INTO $value[0] (";

				for($count = 0; $count < $num_fields; $count++) {
					$result .= "`".$sql->field_name($acc_query,$count)."`";
					if ($count < ($num_fields-1)) $result .= ",";
					}
				$result .= ") VALUES \n";

				for ($i =0; $i<$numrow; $i++) {
					$result .= "\t(";
					$row = $sql->fetch_row($acc_query);
					for($j=0; $j<$num_fields; $j++) {
						$row[$j] = addslashes($row[$j]);
						$row[$j] = ereg_replace("\n","\\n",$row[$j]);
						if (isset($row[$j])) {
							if ($sql->field_type($acc_query,$j) == "int") $result .= "$row[$j]";
								else $result .= "'$row[$j]'" ;
						}else $result .= "''";
						if ($j<($num_fields-1)) $result .= ",";
						}
				if ($i < ($numrow-1)) $result .= "),\n";
				}
				$result .= ");\n";
				}
			$result .= "UNLOCK TABLES;\n";
			$result .= "\n";
			fwrite($fp, $result)or die (error($lang_backup['file_write_err']));
			}
	fclose($fp);

	foreach ($characters_db as $db){
		$file_name_new = $acc[0]."_{$db['name']}.sql";
		$fp = fopen("$subdir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));
		fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$db['name']};\n")or die (error($lang_backup['file_write_err']));
		fwrite($fp, "USE {$db['name']};\n\n")or die (error($lang_backup['file_write_err']));

		$sql->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
		$all_char_query = $sql->query("SELECT guid,name FROM `characters` WHERE account = $acc[0]");

		while ($char = $sql->fetch_array($all_char_query)){
				fwrite($fp, "-- Dumping data for character $char[1]\n")or die (error($lang_backup['file_write_err']));
				foreach ($tab_backup_user_characters as $value) {
					$char_query = $sql->query("SELECT * FROM $value[0] WHERE $value[1] = $char[0]");
					$num_fields = $sql->num_fields($char_query);
					$numrow = $sql->num_rows($char_query);

					$result = "LOCK TABLES $value[0] WRITE;\n";
					$result .= "DELETE FROM $value[0] WHERE $value[1] = $char[0];\n";

					if ($numrow){
						$result .= "INSERT INTO $value[0] (";

						for($count = 0; $count < $num_fields; $count++) {
							$result .= "`".$sql->field_name($char_query,$count)."`";
							if ($count < ($num_fields-1)) $result .= ",";
							}
						$result .= ") VALUES \n";

						for ($i =0; $i<$numrow; $i++) {
							$result .= "\t(";
							$row = $sql->fetch_row($char_query);
							for($j=0; $j<$num_fields; $j++) {
								$row[$j] = addslashes($row[$j]);
								$row[$j] = ereg_replace("\n","\\n",$row[$j]);
								if (isset($row[$j])) {
									if ($sql->field_type($char_query,$j) == "int") $result .= "$row[$j]";
										else $result .= "'$row[$j]'" ;
								}else $result .= "''";
								if ($j<($num_fields-1)) $result .= ",";
								}
						if ($i < ($numrow-1)) $result .= "),\n";
						}
						$result .= ");\n";

						}
					$result .= "UNLOCK TABLES;\n";
					$result .= "\n";
					fwrite($fp, $result)or die (error($lang_backup['file_write_err']));
				}
			}
		fclose($fp);
		}
  }
 }
 $sql->close();

redirect("user.php?error=15");
}


//#######################################################################################################
//  ADD NEW USER
//#######################################################################################################
function add_new() {
 global $lang_global, $lang_user, $output, $action_permission;
  valid_login($action_permission['insert']);
 $output .= "<center>
  <script type=\"text/javascript\" src=\"js/sha1.js\"></script>
  <script type=\"text/javascript\">
		function do_submit_data () {
			if (document.form.new_pass1.value != document.form.new_pass2.value){
				alert('{$lang_user['nonidentical_passes']}');
				return;
			} else {
				document.form.pass.value = hex_sha1(document.form.new_user.value.toUpperCase()+':'+document.form.new_pass1.value.toUpperCase());
				document.form.new_pass1.value = '0';
				document.form.new_pass2.value = '0';
				do_submit();
			}
		}
	</script>

	<fieldset style=\"width: 550px;\">
	<legend>{$lang_user['create_new_acc']}</legend>
     <form method=\"get\" action=\"user.php\" name=\"form\">
	 <input type=\"hidden\" name=\"pass\" value=\"\" maxlength=\"256\" />
     <input type=\"hidden\" name=\"action\" value=\"doadd_new\" />
     <table class=\"flat\">
     <tr>
        <td>{$lang_user['username']}</td>
        <td><input type=\"text\" name=\"new_user\" size=\"42\" maxlength=\"15\" value=\"New_Account\" /></td>
      </tr>
     <tr>
        <td>{$lang_user['password']}</td>
        <td><input type=\"text\" name=\"new_pass1\" size=\"42\" maxlength=\"25\" value=\"123456\" /></td>
     </tr>
     <tr>
        <td>{$lang_user['confirm']}</td>
        <td><input type=\"text\" name=\"new_pass2\" size=\"42\" maxlength=\"25\" value=\"123456\" /></td>
     </tr>
     <tr>
        <td>{$lang_user['email']}</td>
        <td><input type=\"text\" name=\"new_mail\" size=\"42\" maxlength=\"225\" value=\"none@mail.com\" /></td>
     </tr>
     <tr>
        <td>{$lang_user['locked']}</td>
        <td><input type=\"checkbox\" name=\"new_locked\" value=\"1\" /></td>
     </tr>
	 <tr>
        <td>{$lang_user['expansion_account']}</td>
  	 <td>
	   <select name=\"new_expansion\">
	    <option value=\"2\">{$lang_user['wotlk']}</option>
	    <option value=\"1\">{$lang_user['tbc']}</option>
	    <option value=\"0\">{$lang_user['classic']}</option>
	   </select>
	 </td>
     </tr>
     <tr><td>";
			makebutton($lang_user['create_acc'], "javascript:do_submit_data()",120);
 $output .= "</td><td>";
			makebutton($lang_global['back'], "javascript:window.history.back()",306);
 $output .= "</td></tr>
	</table>
    </form>
	</fieldset><br /><br /></center>";
}


//#########################################################################################################
// DO ADD NEW USER
//#########################################################################################################
function doadd_new() {
 global $lang_global, $realm_db, $action_permission;
 valid_login($action_permission['insert']);

 if ( empty($_GET['new_user']) || empty($_GET['pass']) )
   redirect("user.php?action=add_new&error=4");

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $new_user = $sql->quote_smart(trim($_GET['new_user']));
 $pass = $sql->quote_smart($_GET['pass']);

 //make sure username/pass at least 4 chars long and less than max
 if ((strlen($new_user) < 4) || (strlen($new_user) > 15)){
		$sql->close();
     	redirect("user.php?action=add_new&error=8");
   	}

 require_once("scripts/valid_lib.php");
 //make sure it doesnt contain non english chars.
 if (!alphabetic($new_user)) {
		$sql->close();
     	redirect("user.php?action=add_new&error=9");
   	}

 $result = $sql->query("SELECT username FROM account WHERE username = '$new_user'");

 //there is already someone with same username
 if ($sql->num_rows($result)){
		$sql->close();
    	redirect("user.php?action=add_new&error=7");
 } else {
    $last_ip = "0.0.0.0";
	$new_mail = (isset($_GET['new_mail'])) ? $sql->quote_smart(trim($_GET['new_mail'])) : NULL;

	$locked = (isset($_GET['new_locked'])) ? $sql->quote_smart($_GET['new_locked']) : 0;
	$expansion = (isset($_GET['new_expansion'])) ? $sql->quote_smart($_GET['new_expansion']) : 0;

	$result = $sql->query("INSERT INTO account (username,sha_pass_hash,gmlevel,email, joindate,last_ip,failed_logins,locked,last_login,online,expansion)
								VALUES ('$new_user','$pass',0 ,'$new_mail',now() ,'$last_ip',0, $locked ,NULL, 0, $expansion)");
	$sql->close();

	if ($result) redirect("user.php?error=5");
 	}
}


//###########################################################################################################
//  EDIT USER
//###########################################################################################################
function edit_user() {
 global $lang_global, $lang_user, $output, $realm_db, $characters_db, $realm_id, $user_lvl, $user_name, $gm_level_arr, $action_permission;
 valid_login($action_permission['view']);

 if (empty($_GET['id'])) redirect("user.php?error=10");

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $id = $sql->quote_smart($_GET['id']);

 $result = $sql->query("SELECT id,username,gmlevel,email,joindate,last_ip,failed_logins,locked,last_login,online,expansion FROM account WHERE id = '$id'");
 $data = $sql->fetch_row($result);

 if ($sql->num_rows($result)){
	//restricting accsess to lower gmlvl
	if (($user_lvl <= $data[2])&&($user_name != $data[1])){
		$sql->close();
		redirect("user.php?error=14");
		}

 $output .= "<center>
  <script type=\"text/javascript\" src=\"js/sha1.js\"></script>
  <script type=\"text/javascript\">
		function do_submit_data () {
			if ((document.form.username.value != '$data[1]')&&(document.form.new_pass.value == '******')){
				alert('If you are changing Username, The password must be changed too.');
				return;
			} else {
				document.form.pass.value = hex_sha1(document.form.username.value.toUpperCase()+':'+document.form.new_pass.value.toUpperCase());
				document.form.new_pass.value = '0';
				do_submit();
				}
		}
	</script>

 <fieldset style=\"width: 550px;\">
	<legend>{$lang_user['edit_acc']}</legend>
   <form method=\"post\" action=\"user.php?action=doedit_user\" name=\"form\">
   <input type=\"hidden\" name=\"pass\" value=\"\" maxlength=\"256\" />
   <input type=\"hidden\" name=\"id\" value=\"$id\" />
   <table class=\"flat\">
      <tr>
        <td>{$lang_user['id']}</td>
        <td>$data[0]</td>
      </tr>
      <tr>
        <td>{$lang_user['username']}</td>";
      if($user_lvl >= $action_permission['update']) { $output .="
	<td><input type=\"text\" name=\"username\" size=\"43\" maxlength=\"15\" value=\"$data[1]\" /></td>"; }
      else $output.="<td>$data[1]</td>";
   $output .= "
      </tr>
      <tr>
        <td>{$lang_user['password']}</td>";
      if($user_lvl >= $action_permission['update']) { $output .="
        <td><input type=\"text\" name=\"new_pass\" size=\"43\" maxlength=\"40\" value=\"******\" /></td>"; }
      else $output.="<td>********</td>";
   $output .= "
      </tr>
      <tr>
        <td>{$lang_user['email']}</td>";
      if($user_lvl >= $action_permission['update']) { $output .="
        <td><input type=\"text\" name=\"mail\" size=\"43\" maxlength=\"225\"value=\"$data[3]\" /></td>"; }
      else $output.="<td>$data[3]</td>";
   $output .= "
      </tr>
      <tr>
        <td>{$lang_user['gm_level_long']}</td>";
      if($user_lvl >= $action_permission['update']) { $output .="
		<td><select name=\"gmlevel\">";
		foreach ($gm_level_arr as $level){
				if (($level[0] < $user_lvl)||($data[1] == $user_name)){
					$output .= "<option value=\"{$level[0]}\" ";
					if ($data[2] == $level[0]) $output .= "selected=\"selected\" ";
					$output .= ">{$level[1]}</option>";
					}
				}
		$output .= "</select>
		</td>";         }
		else 
                
                foreach ($gm_level_arr as $level){
				if ($data[2] == $level[0])
					$output .= "<td>{$level[0]}</td>";
				}

                //$output .= "<td></td>";
		$output .="
      </tr>
      <tr>
        <td>{$lang_user['join_date']}</td>
        <td>$data[4]</td>
      </tr>
      <tr>
        <td>{$lang_user['banned']}</td>";

	$que = $sql->query("SELECT bandate, unbandate, bannedby, banreason FROM account_banned WHERE id = $id");
	if ($sql->num_rows($que)){
		$banned = $sql->fetch_row($que);
		$ban_info = " From:".date('d-m-Y G:i', $banned[0])." till:".date('d-m-Y G:i', $banned[1])."<br />by $banned[2]";
		$ban_checked = " checked=\"checked\"";
	} else {
			$ban_checked = "";
			$ban_info = "";
			}
      if($user_lvl >= $action_permission['update']) {
      $output .= "<td><input type=\"checkbox\" name=\"banned\" value=\"1\" $ban_checked/>$ban_info</td>"; }
        else
      $output .= "<td>$ban_info</td>";
      $output .="
      </tr>
      <tr>
        <td>{$lang_user['last_ip']}</td>";
      if($user_lvl >= $action_permission['update']) { $output .="
        <td>$data[5]<a href=\"banned.php?action=do_add_entry&amp;entry=$data[5]&amp;bantime=3600&amp;ban_type=ip_banned\"> <- {$lang_user['ban_this_ip']}</a></td>"; }
      else $output .= "<td>***.***.***.***</td>";
      $output .= "
      </tr>
      <tr>
        <td>{$lang_user['banned_reason']}</td>";
      if($user_lvl >= $action_permission['update']) { $output .="
	    <td><input type=\"text\" name=\"banreason\" size=\"43\" maxlength=\"255\" value=\"$banned[3]\" /></td>";}
      else $output .= "<td>$banned[3]</td>";
 $output .="</tr><tr>
	   <td>{$lang_user['client_type']}</td>";
      if($user_lvl >= $action_permission['update']) { $output .="
		<td><select name=\"expansion\">";
      $output .= "<option value=\"0\">{$lang_user['classic']}</option>
			 <option value=\"1\" ";
			 if ($data[10] == 1) $output .= "selected=\"selected\" ";
			$output .= ">{$lang_user['tbc']}</option>
			 <option value=\"2\" ";
			 if ($data[10] ==2) $output .= "selected=\"selected\" ";
			$output .= ">{$lang_user['wotlk']}</option>
			</select>
		</td>"; }
      else $output .= "<td>{$lang_user['classic']}</td>";
      $output .="</tr>
      <tr>
        <td>{$lang_user['failed_logins_long']}</td>";
      if($user_lvl >= $action_permission['update']) { $output .="
	    <td><input type=\"text\" name=\"failed\" size=\"43\" maxlength=\"3\" value=\"$data[6]\" /></td>";}
      else $output .= "<td>$data[6]</td>";
 $output .="</tr>
      <tr>
        <td>{$lang_user['locked']}</td>";
		$lock_checked = ($data[7]) ? " checked=\"checked\"" : "";
     if($user_lvl >= $action_permission['update'])
     $output .= "<td><input type=\"checkbox\" name=\"locked\" value=\"1\" $lock_checked/></td>";
     else
     $output .="<td></td>";
 $output.="
      </tr>
      <tr>
        <td>{$lang_user['last_login']}</td>
        <td>$data[8]</td>
      </tr>
      <tr>
        <td>{$lang_user['online']}</td>";
		$ol = ( $data[9] ) ? $lang_global['yes'] : $lang_global['no'];
 $output .= "<td>$ol</td>
      </tr>";

	$query = $sql->query("SELECT SUM(numchars) FROM realmcharacters WHERE acctid = '$id'");
    $tot_chars = $sql->result($query, 0);

	$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
	$query = $sql->query("SELECT count(*) FROM `characters` WHERE account = $id");
	$chars_on_realm = $sql->result($query, 0);

	$output .= "<tr>
        <td>{$lang_user['tot_chars']}</td>
        <td>$tot_chars</td>
      </tr>
	  <tr>
        <td>{$lang_user['chars_on_realm']}</td>
        <td>$chars_on_realm</td>
      </tr>";

	//if there is any chars to display
	if ($chars_on_realm){
		$char_array = $sql->query("SELECT guid,name,race,class,SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) FROM `characters` WHERE account = $id");
		while ($char = $sql->fetch_array($char_array)){
			$output .= "<tr>
			<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'---></td>
			<td><a href=\"char.php?id=$char[0]\">$char[1]  - ".get_player_race($char[2])." ".get_player_class($char[3])." | lvl $char[4]</a></td>
			</tr>";
		}
	}

 
 if($user_lvl >= $action_permission['update'])
 {
 $output .= "<tr><td>";
		makebutton($lang_user['update_data'], "javascript:do_submit_data()",140);
		makebutton($lang_user['del_acc'], "user.php?action=del_user&amp;check%5B%5D=$id",150);
 }
 else
 $output .= "<tr><td>";
 $output .= "</td><td>";
		makebutton($lang_global['back'], "javascript:window.history.back()",150);
 $output .= "</td></tr>
		</table>
    </form></fieldset><br /><br /></center>";

  } else error($lang_global['err_no_user']);
 $sql->close();
}


//############################################################################################################
//  DO   EDIT   USER
//############################################################################################################
function doedit_user() {
 global $lang_global, $realm_db, $user_lvl, $user_name;

 if( (!isset($_POST['id']) || $_POST['id'] === '') || (!isset($_POST['username']) || $_POST['username'] === '') || (!isset($_POST['pass']) || $_POST['pass'] === '') )
   redirect("user.php?action=edit_user&&id={$_POST['id']}&error=1");

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $id = $sql->quote_smart($_POST['id']);
 $username = $sql->quote_smart($_POST['username']);
 $banreason = $sql->quote_smart($_POST['banreason']);
 $pass = $sql->quote_smart($_POST['pass']);
 $user_pass_change = ($pass != sha1(strtoupper($username).":******")) ? "username='$username',sha_pass_hash='$pass'," : "";

 $mail = (isset($_POST['mail']) && $_POST['mail'] != '') ? $sql->quote_smart($_POST['mail']) : "";
 $failed = (isset($_POST['failed'])) ? $sql->quote_smart($_POST['failed']) : 0;
 $gmlevel = (isset($_POST['gmlevel'])) ? $sql->quote_smart($_POST['gmlevel']) : 0;
 $expansion = (isset($_POST['expansion'])) ? $sql->quote_smart($_POST['expansion']) : 1;
 $banned = (isset($_POST['banned'])) ? $sql->quote_smart($_POST['banned']) : 0;
 $locked = (isset($_POST['locked'])) ? $sql->quote_smart($_POST['locked']) : 0;

 //make sure username/pass at least 4 chars long and less than max
 if ((strlen($username) < 4) || (strlen($username) > 15)){
	$sql->close();
    redirect("user.php?action=edit_user&id=$id&error=8");
   }

 if ($gmlevel >= $user_lvl) {
	$sql->close();
    redirect("user.php?action=edit_user&&id={$_POST['id']}&error=16");
   }

 require_once("scripts/valid_lib.php");
 //make sure it doesnt contain non english chars.
 if (!alphabetic($username)) {
	$sql->close();
    redirect("user.php?action=edit_user&error=9&id=$id");
   }

 //restricting accsess to lower gmlvl
 $result = $sql->query("SELECT gmlevel,username FROM account WHERE id = '$id'");
 if (($user_lvl <= $sql->result($result, 0, 'gmlevel'))&&($user_name != $sql->result($result, 0, 'username'))){
	$sql->close();
	redirect("user.php?error=14");
	}

 if (!$banned) $sql->query("DELETE FROM account_banned WHERE id='$id'");
	else {
			$result = $sql->query("SELECT count(*) FROM account_banned WHERE id = '$id'");
			if(!$sql->result($result, 0))
				$sql->query("INSERT INTO account_banned (id, bandate, unbandate, bannedby, banreason, active)
							   VALUES ($id, ".time().",".(time()+(365*24*3600)).",'$user_name','$banreason', 1)");
		 }

 $sql->query("UPDATE account SET email='$mail', $user_pass_change failed_logins='$failed',locked='$locked',gmlevel='$gmlevel',expansion='$expansion' WHERE id=$id");

 $sql->close();
 redirect("user.php?action=edit_user&error=13&id=$id");
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
   $output .= "<h1><font class=\"error\">{$lang_global['err_no_search_passed']}</font></h1>";
   break;
case 3:
   $output .= "<h1>{$lang_user['search_results']}</h1>";
   break;
case 4:
   $output .= "<h1><font class=\"error\">{$lang_user['acc_creation_failed']}</font></h1>";
   break;
case 5:
   $output .= "<h1>{$lang_user['acc_created']}</h1>";
   break;
case 6:
   $output .= "<h1><font class=\"error\">{$lang_user['nonidentical_passes']}</font></h1>";
   break;
case 7:
   $output .= "<h1><font class=\"error\">{$lang_user['user_already_exist']}</font></h1>";
   break;
case 8:
   $output .= "<h1><font class=\"error\">{$lang_user['username_pass_too_long']}</font></h1>";
   break;
case 9:
   $output .= "<h1><font class=\"error\">{$lang_user['use_only_eng_charset']}</font></h1>";
   break;
case 10:
   $output .= "<h1><font class=\"error\">{$lang_user['no_value_passed']}</font></h1>";
   break;
case 11:
   $output .= "<h1>{$lang_user['edit_acc']}</h1>";
   break;
case 12:
   $output .= "<h1><font class=\"error\">{$lang_user['update_failed']}</font></h1>";
   break;
case 13:
   $output .= "<h1>{$lang_user['data_updated']}</h1>";
   break;
case 14:
   $output .= "<h1><font class=\"error\">{$lang_user['you_have_no_permission']}</font></h1>";
   break;
case 15:
   $output .= "<h1><font class=\"error\">{$lang_user['acc_backedup']}</font></h1>";
   break;
case 16:
   $output .= "<h1><font class=\"error\">{$lang_user['you_have_no_permission_to_set_gmlvl']}</font></h1>";
   break;
default: //no error
    $output .= "<h1>{$lang_user['browse_acc']}</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "browse_users":
   browse_users();
   break;
case "search":
   search();
   break;
case "add_new":
   add_new();
   break;
case "doadd_new":
   doadd_new();
   break;
case "edit_user":
   edit_user();
   break;
case "doedit_user":
   doedit_user();
   break;
case "del_user":
   del_user();
   break;
case "dodel_user":
   dodel_user();
   break;
case "backup_user":
   backup_user();
   break;
default:
    browse_users();
}

require_once("footer.php");
?>
