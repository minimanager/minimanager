<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 6.8.2009 inital version (0.0.1a)
 * Author: playon2007
 * Copyright: playon2007
 * Special thanks to : xiongguoy
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */
require_once("header.php");
valid_login($action_permission['read']);

//#############################################################################
// BROWSE SPELLS
//#############################################################################
function browse_spells()
{
  global $lang_spelld, $lang_global, $output, $world_db, $realm_id, $action_permission;
  valid_login($action_permission['read']);
  $sql = new SQL;
  $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

  $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;

  $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) :"entry";
  $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;

		//get total number of items
 		$query_1 = $sql->query("SELECT count(*) FROM spell_disabled");
 		$all_record = $sql->result($query_1,0);

    $result = $sql->query("SELECT `entry`, `disable_mask`, `comment` FROM `spell_disabled` ORDER BY $order_by $order_dir;");
    $total_found = $sql->num_rows($result);

//==========================top tage navigaion starts here========================
 $output .="<script type=\"text/javascript\" src=\"js/check.js\"></script>
			<center><table class=\"top_hidden\">
			<tr><td>";

	if ($user_lvl >= $action_permission['insert']) 
	makebutton($lang_spelld['add_spell'], "spelld.php?action=add_new", 125);
	makebutton($lang_global['back'], "javascript:window.history.back()", 122);
	
  $output .= " </td><td align=\"right\" width=\"25%\" rowspan=\"5\">";
  $output .= "</td></tr>
	<tr align=\"left\"><td>
	  <table class=\"hidden\">
       <tr><td>
	   <form action=\"spelld.php\" method=\"get\" name=\"form\">
	   <input type=\"hidden\" name=\"action\" value=\"search\" />
	   <input type=\"hidden\" name=\"error\" value=\"3\" />
	   <input type=\"text\" size=\"34\" maxlength=\"64\" name=\"search_value\" />
	   <select name=\"search_by\">
	    <option value=\"entry\">{$lang_spelld['by_id']}</option>
		<option value=\"disable_mask\">{$lang_spelld['by_disable']}</option>
		<option value=\"comment\">{$lang_spelld['by_comment']}</option>
	   </select></form></td>
	   <td>";
		makebutton($lang_global['search'], "javascript:do_submit()",80);
	  $output .= "</td></tr></table>
		</td></tr></table>";
//==========================top tage navigaion ENDS here ========================

 $output .= "<form method=\"get\" action=\"spelld.php\" name=\"form1\">
	 <input type=\"hidden\" name=\"action\" value=\"del_spell\" />
	 <input type=\"hidden\" name=\"start\" value=\"$start\" />
          <table class=\"lined\">
            <tr>";
	       if($user_lvl >= $action_permission['delete']) $output .="<th width=\"1%\"><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll(document.form1);\" /></th>";
              else $output .= "<th width=\"1%\"></th>";
	$output .="<th width=\"24%\"><a href=\"spelld.php?order_by=entry&amp;start=$start&amp;dir=$dir\">".($order_by=='entry' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_spelld['entry']}</a></th>
              <th width=\"25%\"><a href=\"spelld.php?order_by=disable_mask&amp;start=$start&amp;dir=$dir\">".($order_by=='disable_mask' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_spelld['disable_mask']}</a></th>
              <th width=\"50%\"><a href=\"spelld.php?order_by=comment&amp;start=$start&amp;dir=$dir\">".($order_by=='comment' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_spelld['comment']}</a></th>";
       $output .="     
	     </tr>";

  for ($i=1; $i<=$total_found; $i++)
  {
    $spelld = $sql->fetch_array($result);

 $output .= "<tr>";
 if($user_lvl >= $action_permission['delete']) $output .= "<td><input type=\"checkbox\" name=\"check[]\" value=\"$spelld[0]\" onclick=\"CheckCheckAll(document.form1);\" /></td>";
 else $output .= "<td></td>";
        $output .= "
              <td>$spelld[0]</td>
              <td>$spelld[1]</td>
              <td>$spelld[2]</td>
		     ";
  }
 $output .= "<tr><td colspan=\"10\" class=\"hidden\"><br /></td></tr>
		<tr>
		<td colspan=\"5\" align=\"left\" class=\"hidden\">";
		if($user_lvl >= $action_permission['delete']){
		makebutton($lang_spelld['del_selected_spells'], "javascript:do_submit('form1',0)",220);
}
 $output .= "</td>
	<tr> <td colspan=\"5\" align=\"right\" class=\"hidden\">{$lang_spelld['tot_spell']} : $all_record</td></tr>
 	 </tr>
 </table></form><br /></center>";

  $sql->close();
}

//#######################################################################################################
//  SEARCH
//#######################################################################################################
function search() 
{
 global $lang_spelld, $lang_global, $output, $world_db, $realm_id, $server_type, $itemperpage, $sql_search_limit, $action_permission;
valid_login($action_permission['read']);

 if(!isset($_GET['search_value']) || !isset($_GET['search_by'])) redirect("spelld.php?error=2");

  $sql = new SQL;
  $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

$search_value = $sql->quote_smart($_GET['search_value']);
 $search_by = $sql->quote_smart($_GET['search_by']);
 $search_menu = array('entry', 'disable_mask', 'comment');
 if (!in_array($search_by, $search_menu)) $search_by = 'entry';

 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "entry";
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

  $sql_query = "SELECT entry,disable_mask,comment
		  FROM spell_disabled WHERE $search_by LIKE '%$search_value%' ORDER BY $order_by $order_dir LIMIT $sql_search_limit";

$query = $sql->query($sql_query);
$total_found = $sql->num_rows($query);

//==========================top tage navigaion starts here========================
 $output .="<script type=\"text/javascript\" src=\"js/check.js\"></script>
			<center><table class=\"top_hidden\">
			<td align=\"left\">";

		makebutton($lang_spelld['spell_list'], "spelld.php", 120);
		makebutton($lang_global['back'], "javascript:window.history.back()", 120);

 $output .= "</td><td><form action=\"spelld.php\" method=\"get\" name=\"form\">
	   <input type=\"hidden\" name=\"action\" value=\"search\" />
	   <input type=\"text\" size=\"30\" maxlength=\"64\" name=\"search_value\" />
	   <select name=\"search_by\">
	    <option value=\"entry\">{$lang_spelld['by_id']}</option>
		<option value=\"disable_mask\">{$lang_spelld['by_disable']}</option>
		<option value=\"comment\">{$lang_spelld['by_comment']}</option>
	   </select></form></td><td>";
		makebutton($lang_global['search'], "javascript:do_submit()",80);
$output .= "</td></tr>
	</table>";
//==========================top tage navigaion ENDS here ========================

 $output .= "<form method=\"get\" action=\"spelld.php\" name=\"form1\">
	 <input type=\"hidden\" name=\"action\" value=\"del_spell\" />
          <table class=\"lined\">
            <tr>";
		if ($user_lvl >= $action_permission['delete']) $output.= "<th width=\"1%\"><input name=\"allbox\" type=\"checkbox\" value=\"Check All\" onclick=\"CheckAll(document.form1);\" /></th>";
              else $output .= "<th width=\"1%\"></th>";
	$output .="
		<th width=\"24%\"><a href=\"spelld.php?order_by=entry&amp;start=$start&amp;dir=$dir\">".($order_by=='entry' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_spelld['entry']}</a></th>
              <th width=\"25%\"><a href=\"spelld.php?order_by=disable_mask&amp;start=$start&amp;dir=$dir\">".($order_by=='disable_mask' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_spelld['disable_mask']}</a></th>
              <th width=\"50%\"><a href=\"spelld.php?order_by=comment&amp;start=$start&amp;dir=$dir\">".($order_by=='comment' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_spelld['comment']}</a></th>";
       $output .="     
	     </tr>";

 while ($spelld = $sql->fetch_row($query))
  {
 $output .= "<tr>";
 if($user_lvl >= $action_permission['delete']) $output .= "<td><input type=\"checkbox\" name=\"check[]\" value=\"$spelld[0]\" onclick=\"CheckCheckAll(document.form1);\" /></td>";
	else $output .= "<td></td>";
        $output .= "
              <td>$spelld[0]</td>
              <td>$spelld[1]</td>
              <td>$spelld[2]</td>
		     ";
  }

 $output .= "<tr><td colspan=\"12\" class=\"hidden\"><br /></td></tr>
<tr>
		<td colspan=\"8\" align=\"left\" class=\"hidden\">";
		if($user_lvl >= $action_permission['delete'])
			makebutton($lang_spelld['del_selected_spells'], "javascript:do_submit('form1',0)",220);

 $output .= "</td>
    <tr>  <td colspan=\"4\" align=\"right\" class=\"hidden\">{$lang_spelld['tot_found']} : $total_found - {$lang_global['limit']} $sql_search_limit</td></tr>
 	 </tr>
 </table></form><br /></center>";

 $sql->close();
}

//#####################################################################################################
//  ADD NEW SPELL
//#######################################################################################################
function add_new()
{
  global $lang_global, $lang_spelld, $output, $action_permission;
  valid_login($action_permission['insert']);
  $output .= "
        <center>
          <fieldset style=\"width: 550px;\">
            <legend>{$lang_spelld['add_new_spell']}</legend>
            <form method=\"get\" action=\"spelld.php\" name=\"form\">
              <input type=\"hidden\" name=\"action\" value=\"doadd_new\" />
              <table class=\"flat\">
                <tr>
                  <td>{$lang_spelld['entry2']}</td>
                  <td><input type=\"text\" name=\"entry\" size=\"24\" maxlength=\"11\" value=\"\" /></td>
                </tr>
                <tr>
                  <td>{$lang_spelld['disable_mask2']}</td>
                  <td><input type=\"text\" name=\"disable_mask\" size=\"24\" maxlength=\"8\" value=\"\" /></td>
                </tr>
                <tr>
                  <td>{$lang_spelld['comment2']}</td>
                  <td><input type=\"text\" name=\"comment\" size=\"24\" maxlength=\"64\" value=\"\" /></td>
                </tr>
                <tr>
                  <td>";
                    makebutton($lang_spelld['add_spell'], "javascript:do_submit()\" type=\"def",130);
  $output .= "
                  </td>
                  <td>";
                   makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"wrn",130);
  $output .= "
                  </td>
                </tr>
              </table>
            </form>
          </fieldset>
          <br />
        </center>";
$output .=  "
	<center>
	<fieldset style=\"width: 400px;\">
	<table class=\"flat\" border=\"2\" cellpadding=\"4\" cellspacing=\"2\">
	<p>{$lang_spelld['dm_exp']}</p><br />
  <tr>
  <th>{$lang_spelld['value']}</th><th> <center>{$lang_spelld['type']}</center>
  </th></tr>
  <tr>
  <td><center> 1 </center></td><td>{$lang_spelld['disabled_p']}
  </td></tr>
  <tr>
  <td><center> 2 </center></td><td>{$lang_spelld['disabled_crea_npc_pets']}
  </td></tr>
  <tr>
  <td><center> 3 </center></td><td>{$lang_spelld['disabled_p_crea_npc_pets']}
	</td></tr></table></center><br />";
}

//#########################################################################################################
// DO ADD NEW SPELL
//#########################################################################################################
function doadd_new()
{
  global $world_db, $realm_id, $action_permission;
  valid_login($action_permission['insert']);
  if ( empty($_GET['entry']) && empty($_GET['disable_mask']) && empty($_GET['comment']) )
    redirect("spelld.php?error=1");

  $sqlw = new SQL;
  $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

  $entry = $sqlw->quote_smart($_GET['entry']);
  if (!preg_match("/^[[:digit:]]{1,8}$/", $entry))
    redirect("spelld.php?error=6");
  $disable_mask = $sqlw->quote_smart($_GET['disable_mask']);
  if (!preg_match("/^[[:digit:]]{1,11}$/", $disable_mask))
    redirect("spelld.php?error=6");
  $comment = $sqlw->quote_smart($_GET['comment']);

   $sqlw->query("INSERT INTO spell_disabled (entry, disable_mask, comment) VALUES ('$entry','$disable_mask','$comment')");
  if ($sqlw->affected_rows())
    redirect("spelld.php?error=8");
  else
   redirect("spelld.php?error=7");

}

//#####################################################################################################
//  DELETE SPELL
//#####################################################################################################
function del_spell() 
{
 global $lang_global, $lang_spelld, $output, $world_db, $realm_id, $action_permission;
valid_login($action_permission['delete']);

 $sql = new SQL;
 $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

if(isset($_GET['check'])) $check = $sql->quote_smart($_GET['check']);
	else redirect("spelld.php?error=1");

 $deleted_spell = 0;
  for ($i=0; $i<count($check); $i++)
  {
    if ($check[$i] != "" )
    { $query = $sql->query("DELETE FROM spell_disabled WHERE entry = '$check[$i]'"); }
  }
 $sql->close();

  if ($deleted_spell == 0)
    redirect("spelld.php?error=4");
  else
    redirect("spelld.php?error=5");
}

//#############################################################################
// MAIN
//#############################################################################
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
   $output .= "<h1>{$lang_spelld['search_results']}</h1>";
   break;
case 4:
   $output .= "<h1><font class=\"error\">{$lang_spelld['spell_deleted']}</font></h1>";
   break;
case 5:
   $output .= "<h1><font class=\"error\">{$lang_spelld['spell_not_deleted']}</font></h1>";
   break;
case 6:
   $output .= "<h1><font class=\"error\">{$lang_spelld['wrong_fields']}</font></h1>";
   break;
case 7:
   $output .= "<h1><font class=\"error\">{$lang_spelld['err_add_entry']}</font></h1>";
   break;
case 8:
   $output .= "<h1><font class=\"error\">{$lang_spelld['spell_added']}</font></h1>";
   break;
default:
    $output .= "<h1>{$lang_spelld['spells']}</h1>";
}

$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;
switch ($action)
{
  case "browse_spells":
    browse_spells();
    break;
  case "search":
   search();
   break;
  case "del_spell":
   del_spell();
   break;
  case "add_new":
   add_new();
   break;
  case "doadd_new":
   doadd_new();
   break;
  default:
    browse_spells();
}

require_once("footer.php");

?>