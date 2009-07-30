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
require_once("scripts/id_tab.php");

//########################################################################################################################
// BROWSE TELEPORT LOCATIONS
//########################################################################################################################
function browse_tele() {
 global $lang_tele, $lang_global, $output, $world_db, $realm_id, $itemperpage;

 $sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
 
 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "id";

 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;
 
//get total number of items
 $query_1 = $sql->query("SELECT count(*) FROM game_tele");
 $all_record = $sql->result($query_1,0);
 $query = $sql->query("SELECT id, name, map, position_x, position_y, position_z, orientation 
		 FROM game_tele ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
 $this_page = $sql->num_rows($query);

//==========================top tage navigaion starts here========================
 $output .="<center><table class=\"top_hidden\">
          <tr><td width=\"80%\">
			<table class=\"hidden\"> 
				<tr><td>";
		makebutton($lang_tele['add_new'], "tele.php?action=add_tele",80);
 $output .="<form action=\"tele.php\" method=\"get\" name=\"form\">
	   <input type=\"hidden\" name=\"action\" value=\"search\" />
	   <input type=\"hidden\" name=\"error\" value=\"4\" />
	   <input type=\"text\" size=\"30\" name=\"search_value\" />
	   <select name=\"search_by\">
		<option value=\"name\">{$lang_tele['loc_name']}</option>
		<option value=\"id\">{$lang_tele['loc_id']}</option>
		<option value=\"map\">{$lang_tele['on_map']}</option>
	   </select></form></td><td>";
	   makebutton($lang_global['search'], "javascript:do_submit()",80);
 $output .= "</td></tr></table>
			<td width=\"20%\" align=\"right\">";
 $output .= generate_pagination("tele.php?action=browse_tele&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
 $output .= "</td></tr></table>";
//==========================top tage navigaion ENDS here ========================

 $output .= "<script type=\"text/javascript\">
	answerbox.btn_ok='{$lang_global['yes_low']}';
	answerbox.btn_cancel='{$lang_global['no']}';
	var question = '{$lang_global['are_you_sure']}';
	var del_tele = 'tele.php?action=del_tele&amp;order_by=$order_by&amp;start=$start&amp;dir=$dir&amp;id=';
 </script>
 <table class=\"lined\">
   <tr>
	<th width=\"5%\">{$lang_global['delete_short']}</th>
	<th width=\"5%\"><a href=\"tele.php?order_by=id&amp;start=$start&amp;dir=$dir\"".($order_by=='id' ? " class=\"$order_dir\"" : "").">{$lang_tele['id']}</a></th>
	<th width=\"28%\"><a href=\"tele.php?order_by=name&amp;start=$start&amp;dir=$dir\"".($order_by=='name' ? " class=\"$order_dir\"" : "").">{$lang_tele['name']}</a></th>
	<th width=\"22%\><a href=\"tele.php?order_by=map&amp;start=$start&amp;dir=$dir\"".($order_by=='map' ? " class=\"$order_dir\"" : "").">{$lang_tele['map']}</a></th>
	<th width=\"9%\"><a href=\"tele.php?order_by=position_x&amp;start=$start&amp;dir=$dir\"".($order_by=='position_x' ? " class=\"$order_dir\"" : "").">{$lang_tele['x']}</a></th>
	<th width=\"9%\"><a href=\"tele.php?order_by=position_y&amp;start=$start&amp;dir=$dir\"".($order_by=='position_y' ? " class=\"$order_dir\"" : "").">{$lang_tele['y']}</a></th>
	<th width=\"9%\"><a href=\"tele.php?order_by=position_z&amp;start=$start&amp;dir=$dir\"".($order_by=='position_z' ? " class=\"$order_dir\"" : "").">{$lang_tele['z']}</a></th>
	<th width=\"10%\"><a href=\"tele.php?order_by=orientation&amp;start=$start&amp;dir=$dir\"".($order_by=='orientation' ? " class=\"$order_dir\"" : "").">{$lang_tele['orientation']}</a></th>
   </tr>";

while ($data = $sql->fetch_row($query)){
   	$output .= "<tr>
			<td><img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('{$lang_global['delete']}: <font color=white>{$data[1]}</font> <br /> ' + question, del_tele + $data[0]);\" style=\"cursor:pointer;\" /></td>
			<td>$data[0]</td>
			<td><a href=\"tele.php?action=edit_tele&amp;id=$data[0]\">$data[1]</a></td>
			<td>".get_map_name($data[2])." ($data[2])</td>
			<td>$data[3]</td>
			<td>$data[4]</td>
			<td>$data[5]</td>
			<td>$data[6]</td>
            </tr>";
}

 $output .= "<tr><td colspan=\"8\" class=\"hidden\" align=\"right\">{$lang_tele['tot_locations']} : $all_record</td></tr>
   </table></center>";

 $sql->close();
}


//########################################################################################################################
//  SEARCH
//########################################################################################################################
function search() {
 global $lang_tele, $lang_global, $output, $world_db, $realm_id, $sql_search_limit;

 if(empty($_GET['search_value']) || empty($_GET['search_by'])) redirect("guild.php?error=2");

 $sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
 
 $search_value = $sql->quote_smart($_GET['search_value']);
 $search_by = $sql->quote_smart($_GET['search_by']);

 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "id";
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;
 
 $query = $sql->query("SELECT id, name, map, position_x, position_y, position_z, orientation 
		FROM game_tele WHERE $search_by LIKE '%$search_value%' ORDER BY $order_by $order_dir LIMIT $sql_search_limit");
 $total_found = $sql->num_rows($query);

//==========================top tage navigaion starts here========================
 $output .="<script type=\"text/javascript\">
			answerbox.btn_ok='{$lang_global['yes_low']}';
			answerbox.btn_cancel='{$lang_global['no']}';
			var question = '{$lang_global['are_you_sure']}';
			var del_tele = 'tele.php?action=del_tele&amp;order_by=$order_by&amp;dir=$dir&amp;id=';
		 </script>
		 <center><table class=\"top_hidden\">
			<tr><td>";
			makebutton($lang_tele['add_new'], "tele.php?action=add_tele",90);
 $output .="<form action=\"tele.php\" method=\"get\" name=\"form\">
	   <input type=\"hidden\" name=\"action\" value=\"search\" />
	   <input type=\"hidden\" name=\"error\" value=\"4\" />
	   <input type=\"text\" size=\"45\" name=\"search_value\" />
	   <select name=\"search_by\">
		<option value=\"name\">{$lang_tele['loc_name']}</option>
		<option value=\"id\">{$lang_tele['loc_id']}</option>
		<option value=\"map\">{$lang_tele['on_map']}</option>
	   </select></form></td><td>";
	   makebutton($lang_global['search'], "javascript:do_submit()",80);
	   makebutton($lang_global['back'], "javascript:window.history.back()", 80);
$output .= "</td></tr></table>";
//==========================top tage navigaion ENDS here ========================

 $output .= "<table class=\"lined\">
   <tr>
	<th width=\"5%\">{$lang_global['delete_short']}</th>
	<th width=\"5%\"><a href=\"tele.php?action=search&amp;error=4&amp;order_by=id&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\"".($order_by=='id' ? " class=\"$order_dir\"" : "").">{$lang_tele['id']}</a></th>
	<th width=\"28%\"><a href=\"tele.php?action=search&amp;error=4&amp;order_by=name&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\"".($order_by=='name' ? " class=\"$order_dir\"" : "").">{$lang_tele['name']}</a></th>
	<th width=\"22%\"><a href=\"tele.php?action=search&amp;error=4&amp;order_by=map&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\"".($order_by=='map' ? " class=\"$order_dir\"" : "").">{$lang_tele['map']}</a></th>
	<th width=\"9%\"><a href=\"tele.php?action=search&amp;error=4&amp;order_by=position_x&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\"".($order_by=='position_x' ? " class=\"$order_dir\"" : "").">{$lang_tele['x']}</a></th>
	<th width=\"9%\"><a href=\"tele.php?action=search&amp;error=4&amp;order_by=position_y&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\"".($order_by=='position_y' ? " class=\"$order_dir\"" : "").">{$lang_tele['y']}</a></th>
	<th width=\"9%\"><a href=\"tele.php?action=search&amp;error=4&amp;order_by=position_z&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\"".($order_by=='position_z' ? " class=\"$order_dir\"" : "").">{$lang_tele['z']}</a></th>
	<th width=\"10%\"><a href=\"tele.php?action=search&amp;error=4&amp;order_by=orientation&amp;search_by=$search_by&amp;search_value=$search_value&amp;dir=$dir\"".($order_by=='orientation' ? " class=\"$order_dir\"" : "").">{$lang_tele['orientation']}</a></th>
   </tr>";

while ($data = $sql->fetch_row($query)){
   	$output .= "<tr>
		<td><img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('{$lang_global['delete']}: <font color=white>{$data[1]}</font> <br /> ' + question, del_tele + $data[0]);\" style=\"cursor:pointer;\" /></td>
		<td>$data[0]</td>
		<td><a href=\"tele.php?action=edit_tele&amp;id=$data[0]\">$data[1]</a></td>
		<td>".get_map_name($data[2])." ($data[2])</td>
		<td>$data[3]</td>
		<td>$data[4]</td>
		<td>$data[5]</td>
		<td>$data[6]</td>
        </tr>";
}

 $output .= "<tr>
      <td colspan=\"8\" class=\"hidden\" align=\"right\">{$lang_tele['total_found']} : $total_found {$lang_global['limit']} : $sql_search_limit</td>
    </tr>
   </table></center>";

 $sql->close();
}


//########################################################################################################################
// DO DELETE TELE FROM LIST
//########################################################################################################################
function del_tele() {
 global $world_db, $realm_id;

 $sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
 
 if(isset($_GET['id'])) $id = $sql->quote_smart($_GET['id']);
	else redirect("Location: tele.php?error=1");

 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "id";
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $dir = ($dir) ? 0 : 1;
 
 $sql->query("DELETE FROM game_tele WHERE id = '$id'");

 if ($sql->affected_rows() != 0) {
	$sql->close();
	redirect("tele.php?error=3&order_by=$order_by&start=$start&dir=$dir");
    } else {
 	 $sql->close();
	 redirect("tele.php?error=5");
	}
}


//########################################################################################################################
//  EDIT   TELE
//########################################################################################################################
function edit_tele() {
 global  $lang_tele, $lang_global, $output, $world_db, $realm_id, $map_id;

 $sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
 
 if(isset($_GET['id'])) $id = $sql->quote_smart($_GET['id']);
	else redirect("tele.php?error=1");

 $query = $sql->query("SELECT id, name, map, position_x, position_y, position_z, orientation FROM game_tele WHERE id = '$id'");

 if ($sql->num_rows($query) == 1) {
	$tele = $sql->fetch_row($query);

	$output .= "<script type=\"text/javascript\">
		answerbox.btn_ok='{$lang_global['yes_low']}';
		answerbox.btn_cancel='{$lang_global['no']}';
	</script>
	<center>
	<fieldset class=\"half_frame\">
	<legend>{$lang_tele['edit_tele']}</legend>
    <form method=\"get\" action=\"tele.php\" name=\"form\">
	<input type=\"hidden\" name=\"action\" value=\"do_edit_tele\" />
	<input type=\"hidden\" name=\"id\" value=\"$id\" />
	<table class=\"flat\">
      <tr>
        <td>{$lang_tele['loc_id']}</td>
        <td>$tele[0]</td>
      </tr>
     <tr>
        <td>{$lang_tele['loc_name']}</td>
        <td><input type=\"text\" name=\"new_name\" size=\"42\" maxlength=\"98\" value=\"$tele[1]\" /></td>
      </tr>
	 <tr>
        <td>{$lang_tele['on_map']}</td>
        <td><select name=\"new_map\">";
			foreach ($map_id as $map){
					$output .= "<option value=\"{$map[0]}\" ";
					if ($tele[2] == $map[0]) $output .= "selected=\"selected\" ";
					$output .= ">{$map[0]} : {$map[1]}</option>";
					}
			$output .= "</select></td>
      </tr>
	 <tr>
        <td>{$lang_tele['position_x']}</td>
        <td><input type=\"text\" name=\"new_x\" size=\"42\" maxlength=\"36\" value=\"$tele[3]\" /></td>
      </tr>
	  <tr>
        <td>{$lang_tele['position_y']}</td>
        <td><input type=\"text\" name=\"new_y\" size=\"42\" maxlength=\"36\" value=\"$tele[4]\" /></td>
      </tr>
	  <tr>
        <td>{$lang_tele['position_z']}</td>
        <td><input type=\"text\" name=\"new_z\" size=\"42\" maxlength=\"36\" value=\"$tele[5]\" /></td>
      </tr>
	 <tr>
        <td>{$lang_tele['orientation']}</td>
        <td><input type=\"text\" name=\"new_orientation\" size=\"42\" maxlength=\"36\" value=\"$tele[6]\" /></td>
      </tr>
      <tr><td>";
			makebutton($lang_tele['delete_tele'], "#\" onclick=\"answerBox('{$lang_global['delete']}: <font color=white>{$tele[1]}</font> <br /> {$lang_global['are_you_sure']}', 'tele.php?action=del_tele&amp;id=$id');\" type=\"wrn",148);
$output .= "</td><td>
		<table class=\"hidden\">
          <tr><td>";
			makebutton($lang_tele['update_tele'], "javascript:do_submit()",130);
			makebutton($lang_global['back'], "tele.php\" type=\"def",148);
$output .= "</td></tr>
        </table>";
	$output .= "</td></tr>
     </table>
     </form></fieldset><br /><br /></center>";
  } else error($lang_global['err_no_records_found']);
  
 $sql->close();
}


//########################################################################################################################
//  DO EDIT TELE LOCATION
//########################################################################################################################
function do_edit_tele() {
 global $world_db, $realm_id;

 if( empty($_GET['id']) || !isset($_GET['new_name']) || !isset($_GET['new_map']) || !isset($_GET['new_x'])
 || !isset($_GET['new_y'])|| !isset($_GET['new_z'])|| !isset($_GET['new_orientation']))
  redirect("tele.php?error=1");

 $sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
 
 $id = $sql->quote_smart($_GET['id']);
 $new_name = $sql->quote_smart($_GET['new_name']);
 $new_map = $sql->quote_smart($_GET['new_map']);
 $new_x = $sql->quote_smart($_GET['new_x']);
 $new_y = $sql->quote_smart($_GET['new_y']);
 $new_z = $sql->quote_smart($_GET['new_z']);
 $new_orientation = $sql->quote_smart($_GET['new_orientation']);

 $sql->query("UPDATE game_tele SET position_x='$new_x', position_y ='$new_y', position_z ='$new_z', orientation ='$new_orientation', map ='$new_map', name ='$new_name' WHERE id = '$id'");

 if ($sql->affected_rows()) {
	$sql->close();
	redirect("tele.php?error=3");
    } else {
		$sql->close();
		redirect("tele.php?error=5");
	}
}


//########################################################################################################################
//  ADD NEW TELE
//########################################################################################################################
function add_tele() {
 global  $output, $lang_tele, $lang_global, $map_id;

	$output .= "<center>
	<fieldset class=\"half_frame\">
	<legend>{$lang_tele['add_new_tele']}</legend>
    <form method=\"get\" action=\"tele.php\" name=\"form\">
	<input type=\"hidden\" name=\"action\" value=\"do_add_tele\" />
	<table class=\"flat\">
     <tr>
        <td>{$lang_tele['loc_name']}</td>
        <td><input type=\"text\" name=\"name\" size=\"42\" maxlength=\"98\" value=\"{$lang_tele['name']}\" /></td>
      </tr>
	 <tr>
        <td>{$lang_tele['on_map']}</td>
        <td><select name=\"map\">";
			foreach ($map_id as $map) $output .= "<option value=\"{$map[0]}\">{$map[0]} : {$map[1]}</option>";
			$output .= "</select></td>
      </tr>
	 <tr>
        <td>{$lang_tele['position_x']}</td>
        <td><input type=\"text\" name=\"x\" size=\"42\" maxlength=\"36\" value=\"0.0000\" /></td>
      </tr>
	  <tr>
        <td>{$lang_tele['position_y']}</td>
        <td><input type=\"text\" name=\"y\" size=\"42\" maxlength=\"36\" value=\"0.0000\" /></td>
      </tr>
	  <tr>
        <td>{$lang_tele['position_z']}</td>
        <td><input type=\"text\" name=\"z\" size=\"42\" maxlength=\"36\" value=\"0.0000\" /></td>
      </tr>
	 <tr>
        <td>{$lang_tele['orientation']}</td>
        <td><input type=\"text\" name=\"orientation\" size=\"42\" maxlength=\"36\" value=\"0\" /></td>
      </tr>
      <tr><td>";
			makebutton($lang_tele['add_new'], "javascript:do_submit()",130);
$output .= "</td><td>
		<table class=\"hidden\">
          <tr><td>";
			makebutton($lang_global['back'], "tele.php",310);
$output .= "</td></tr>
        </table>";
	$output .= "</td></tr>
     </table>
     </form></fieldset><br /><br /></center>";
}


//########################################################################################################################
//  DO ADD  TELE LOCATION
//########################################################################################################################
function do_add_tele() {
 global $world_db, $realm_id;

 if( !isset($_GET['name']) || !isset($_GET['map']) || !isset($_GET['x'])
 || !isset($_GET['y'])|| !isset($_GET['z'])|| !isset($_GET['orientation']))
  redirect("tele.php?error=1");

 $sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

 $name = $sql->quote_smart($_GET['name']);
 $map = $sql->quote_smart($_GET['map']);
 $x = $sql->quote_smart($_GET['x']);
 $y = $sql->quote_smart($_GET['y']);
 $z = $sql->quote_smart($_GET['z']);
 $orientation = $sql->quote_smart($_GET['orientation']);

 $sql->query("INSERT INTO game_tele VALUES (NULL,'$x','$y', '$z' ,'$orientation' ,'$map' ,'$name')");

 if ($sql->affected_rows()) {
	$sql->close();
	redirect("tele.php?error=3");
    } else {
		$sql->close();
		redirect("tele.php?error=5");
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
   $output .= "<h1><font class=\"error\">{$lang_global['err_no_search_passed']}</font></h1>";
   break;
case 3:
	$output .= "<h1><font class=\"error\">{$lang_tele['tele_updated']}</font></h1>";
   break;
case 4:
   $output .= "<h1><font class=\"error\">{$lang_tele['search_results']}</font></h1>";
   break;
case 5:
	$output .= "<h1><font class=\"error\">{$lang_tele['error_updating']}</font></h1>";
   break;
default: //no error
    $output .= "<h1>{$lang_tele['tele_locations']}</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "browse_tele": 
   browse_tele();
   break;
case "search": 
   search();
   break;
case "edit_tele": 
   edit_tele();
   break;
case "do_edit_tele": 
   do_edit_tele();
   break;
case "add_tele": 
   add_tele();
   break;
case "do_add_tele": 
   do_add_tele();
   break;
case "del_tele": 
   del_tele();
   break;
default:
    browse_tele();
}

require_once("footer.php");
?>
