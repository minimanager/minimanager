<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Thorazi (ahstats.php)
 * Copyright: Thorazi(ahstats.php)
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

require_once("header.php");
valid_login($action_permission['read']);
require_once("scripts/get_lib.php");

//########################################################################################################################
// BROWSE AUCTIONS
//########################################################################################################################
function browse_auctions() {
 global $lang_auctionhouse, $lang_global, $lang_item, $output, $characters_db, $realm_id, $world_db,
		$itemperpage, $item_datasite, $server, $user_lvl, $user_id;

 $red = "#DD5047";
 $blue = "#0097CD";
 $sidecolor = array(1 => $blue,2 => $red,3 => $blue,4 => $blue,5 => $red,6 => $red,7 => $blue,8 => $red,10 => $red);
 $hiddencols = array(1,8,9,10);

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "time";
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

 $query_1 = $sql->query("SELECT count(*) FROM auctionhouse");
 $all_record = $sql->result($query_1,0);

 if( !$user_lvl && !$server[$realm_id]['both_factions']){
	$result = $sql->query("SELECT race FROM `characters` WHERE account = $user_id AND totaltime = (SELECT MAX(totaltime) FROM `characters` WHERE account = $user_id) LIMIT 1");
	if ($sql->num_rows($result)){
		$order_side = (in_array($sql->result($result, 0, 'race'),array(2,5,6,8,10))) ?
		" AND `characters`.`race` IN (2,5,6,8,10) " : " AND `characters`.`race` IN (1,3,4,7,11) ";
	} else $order_side = "";
 } else $order_side = "";
 
 $result = $sql->query("SELECT `characters`.`name` AS `seller`, `auctionhouse`.`item_template` AS `itemid`, `item_template`.`name` AS `itemname`, `auctionhouse`.`buyoutprice` AS `buyout`,
 `auctionhouse`.`time`-unix_timestamp(), `c2`.`name` AS `encherisseur`, `auctionhouse`.`lastbid`, `auctionhouse`.`startbid`, SUBSTRING_INDEX(SUBSTRING_INDEX(`item_instance`.`data`, ' ',15), ' ',-1) AS qty, `characters`.`race` AS seller_race, `c2`.`race` AS buyer_race
 FROM `".$characters_db[$realm_id]['name']."`.`characters` , `".$characters_db[$realm_id]['name']."`.`item_instance` , `".$world_db[$realm_id]['name']."`.`item_template` , `".$characters_db[$realm_id]['name']."`.`auctionhouse`
LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` c2 ON `c2`.`guid`=`auctionhouse`.`buyguid`
 WHERE `auctionhouse`.`itemowner`=`characters`.`guid` AND `auctionhouse`.`item_template`=`item_template`.`entry` AND `auctionhouse`.`itemguid`=`item_instance`.`guid`
 $order_side ORDER BY `auctionhouse`.`$order_by` $order_dir LIMIT $start, $itemperpage");
 $this_page = $sql->num_rows($result);

 $output .="<center><table class=\"top_hidden\">
      <tr><td width=\"80%\">
	  <form action=\"ahstats.php\" method=\"get\" name=\"form\">
	   <input type=\"hidden\" name=\"action\" value=\"search_auctions\" />
	   <input type=\"hidden\" name=\"error\" value=\"2\" />
		<table class=\"hidden\">
		<tr><td>
	  <td><input type=\"text\" size=\"25\" name=\"search_value\" /></td>

	   <td><select name=\"search_by\">
	    <option value=\"item_name\">{$lang_auctionhouse['item_name']}</option>
		<option value=\"item_id\">{$lang_auctionhouse['item_id']}</option>
		<option value=\"seller_name\">{$lang_auctionhouse['seller_name']}</option>
		<option value=\"buyer_name\">{$lang_auctionhouse['buyer_name']}</option>
	   </select></td>

	   <td><select name=\"search_class\">
	    <option value=\"-1\">{$lang_auctionhouse['all']}</option>
		<option value=\"0\">{$lang_item['consumable']}</option>
		<option value=\"1\">{$lang_item['bag']}</option>
		<option value=\"2\">{$lang_item['weapon']}</option>
		<option value=\"4\">{$lang_item['armor']}</option>
		<option value=\"5\">{$lang_item['reagent']}</option>
		<option value=\"7\">{$lang_item['trade_goods']}</option>
		<option value=\"9\">{$lang_item['recipe']}</option>
		<option value=\"11\">{$lang_item['quiver']}</option>
		<option value=\"14\">{$lang_item['permanent']}</option>
		<option value=\"15\">{$lang_item['misc_short']}</option>
	   </select></td>

	   <td><select name=\"search_quality\">
		<option value=\"-1\">{$lang_auctionhouse['all']}</option>
		<option value=\"0\">{$lang_item['poor']}</option>
		<option value=\"1\">{$lang_item['common']}</option>
		<option value=\"2\">{$lang_item['uncommon']}</option>
		<option value=\"3\">{$lang_item['rare']}</option>
		<option value=\"4\">{$lang_item['epic']}</option>
		<option value=\"5\">{$lang_item['legendary']}</option>
		<option value=\"6\">{$lang_item['artifact']}</option>
	   </select></td>
	   <td>";
	   makebutton($lang_global['search'], "javascript:do_submit()",80);
 $output .= "</td></tr></table></form>
		<td width=\"20%\" align=\"right\">";
 $output .= generate_pagination("ahstats.php?action=browse_auctions&amp;order_by=$order_by&amp;dir=".!$dir, $all_record, $itemperpage, $start);
 $output .= "</td></tr></table>

 <table class=\"lined\">
   <tr>
	<th width=\"10%\"><a href=\"ahstats.php?order_by=itemowner&amp;start=$start&amp;dir=$dir\">".($order_by=='itemowner' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['seller']}</a></th>
	<th width=\"20%\"><a href=\"ahstats.php?order_by=item_template&amp;start=$start&amp;dir=$dir\">".($order_by=='item_template' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['item']}</a></th>
	<th width=\"15%\"><a href=\"ahstats.php?order_by=buyoutprice&amp;start=$start&amp;dir=$dir\">".($order_by=='buyoutprice' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['buyoutprice']}</a></th>
	<th width=\"15%\"><a href=\"ahstats.php?order_by=time&amp;start=$start&amp;dir=$dir\">".($order_by=='time' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['timeleft']}</a></th>
	<th width=\"10%\"><a href=\"ahstats.php?order_by=buyguid&amp;start=$start&amp;dir=$dir\">".($order_by=='buyguid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['buyer']}</a></th>
	<th width=\"15%\"><a href=\"ahstats.php?order_by=lastbid&amp;start=$start&amp;dir=$dir\">".($order_by=='lastbid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['lastbid']}</a></th>
	<th width=\"15%\"><a href=\"ahstats.php?order_by=startbid&amp;start=$start&amp;dir=$dir\">".($order_by=='startbid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['firstbid']}</a></th>
   </tr>";

 while ($rows = $sql->fetch_row($result)) {
	$output .= "<tr>";
	foreach($rows as $row => $value) {
		switch ($row) {
			case 4:
			$value = ($value >= 0)? (floor($value / 86400).$lang_auctionhouse['dayshortcut']." ". floor(($value % 86400)/3600).$lang_auctionhouse['hourshortcut']." ".floor((($value % 86400)%3600)/60).$lang_auctionhouse['mnshortcut']) : $lang_auctionhouse['auction_over'];
			break;
			case 5:
			$value = "<b>".((!empty($rows[10])) ? "<font color=".$sidecolor[$rows[10]].">".htmlentities($value)."</font>" : "N/A")."</b>";
			break;
			case 7:
			case 6:
			case 3:
			$g = floor($value/10000);
			$value -= $g*10000;
			$s = floor($value/100);
			$value -= $s*100;
			$c = $value;
			$value = $g."<img src=\"./img/gold.gif\" /> ".$s."<img src=\"./img/silver.gif\" /> ".$c."<img src=\"./img/copper.gif\" /> ";
			break;
			case 2:
			$value = "<a href=\"$item_datasite$rows[1]\" target=\"_blank\" onmouseover=\"toolTip,'item_tooltip')\"><img src=\"".get_icon($rows[1])."\" class=\"icon_border_0\" alt=\"$value\"><br/>$value".(($rows[8]>1) ? " (x$rows[8])" : "")."</img></a>";
			break;
			case 0:
			$value = "<b>".((!empty($rows[9])) ? "<font color=".$sidecolor[$rows[9]].">".htmlentities($value)."</font>" : "N/A")."</b>";
			break;
		}
		if (!in_array($row,$hiddencols)) $output .= "<td><center>".$value."</center></td>";
	}
	$output .= "</tr>";
 }
 $sql->close();

 $output .= "<tr><td colspan=\"7\" class=\"hidden\" align=\"right\">{$lang_auctionhouse['total_auctions']} : $all_record</td></tr>
   </table></center>";
}


//########################################################################################################################
// SEARCH AUCTIONS
//########################################################################################################################
function search_auctions() {
 global $lang_auctionhouse, $lang_global, $lang_item, $output, $characters_db, $realm_id, $world_db,

		$itemperpage, $item_datasite, $server, $user_lvl, $user_id, $sql_search_limit;

 $red = "#DD5047";
 $blue = "#0097CD";
 $sidecolor = array(1 => $blue,2 => $red,3 => $blue,4 => $blue,5 => $red,6 => $red,7 => $blue,8 => $red,10 => $red);
 $hiddencols = array(1,8,9,10);

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "time";
 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

  if( ($_GET['search_class'] == "-1")&&($_GET['search_quality'] == "-1")
	&&(!isset($_GET['search_value']))&&(!isset($_GET['search_by'])) )
	redirect("ahstats.php?error=1");

  $search_class = $sql->quote_smart($_GET['search_class']);
  $search_quality = $sql->quote_smart($_GET['search_quality']);
  $search_value = $sql->quote_smart($_GET['search_value']);
  $search_by = $sql->quote_smart($_GET['search_by']);

 if( !$user_lvl && !$server[$realm_id]['both_factions']){
	$result = $sql->query("SELECT race FROM `characters` WHERE account = '$user_id' AND totaltime = (SELECT MAX(totaltime) FROM `characters` WHERE account = '$user_id') LIMIT 1");
	if ($sql->num_rows($result)){
		$order_side = (in_array($sql->result($result, 0, 'race'),array(2,5,6,8,10))) ?
		" AND `characters`.`race` IN (2,5,6,8,10) " : " AND `characters`.`race` IN (1,3,4,7,11) ";
	} else $order_side = "";
 } else $order_side = "";

 switch ($search_by) {
	case "item_name":

	 if(( ($search_class >= 0) || ($search_quality >= 0))&&(!isset($search_value))){
		if ($search_class >= 0) $search_filter = " AND item_template.class = '$search_class'";
		if ($search_quality >= 0) $search_filter = " AND item_template.Quality = '$search_quality'";
	}
	else
	{
		$item_prefix = "";
		if ($search_class >= 0) $item_prefix .= "AND class = '$search_class' ";
		if ($search_quality >= 0) $item_prefix .= "AND Quality = '$search_quality' ";

		$result = $sql->query("SELECT entry FROM `".$world_db[$realm_id]['name']."`.`item_template` WHERE name LIKE '%$search_value%' $item_prefix");
		$search_filter = "AND auctionhouse.item_template IN(0";
		while ($item = $sql->fetch_row($result)) $search_filter .= ", $item[0]";
		$search_filter .= ")";
	}
	break;

	case "item_id":
		$search_filter = "AND auctionhouse.item_template = '$search_value'";
	break;

	case "seller_name":
		$result = $sql->query("SELECT guid FROM `characters` WHERE name LIKE '%$search_value%'");
		$search_filter = "AND auctionhouse.itemowner IN(0";
		while ($char = $sql->fetch_row($result)) $search_filter .= ", $char[0]";
		$search_filter .= ")";
	break;
	case "buyer_name":
		$result = $sql->query("SELECT guid FROM `characters` WHERE name LIKE '%$search_value%'");
		$search_filter = "AND auctionhouse.buyguid IN(-1";
		while ($char = $sql->fetch_row($result)) $search_filter .= ", $char[0]";
		$search_filter .= ")";
	break;
	default:
		redirect("ahstats.php?error=1");
 }

$result = $sql->query("SELECT `characters`.`name` AS `seller`, `auctionhouse`.`item_template` AS `itemid`, `item_template`.`name` AS `itemname`, `auctionhouse`.`buyoutprice` AS `buyout`,
 `auctionhouse`.`time`-unix_timestamp(), `c2`.`name` AS `encherisseur`, `auctionhouse`.`lastbid`, `auctionhouse`.`startbid`, SUBSTRING_INDEX(SUBSTRING_INDEX(`item_instance`.`data`, ' ',15), ' ',-1) AS qty, `characters`.`race` AS seller_race, `c2`.`race` AS buyer_race
 FROM `".$characters_db[$realm_id]['name']."`.`characters` , `".$characters_db[$realm_id]['name']."`.`item_instance` , `".$world_db[$realm_id]['name']."`.`item_template` , `".$characters_db[$realm_id]['name']."`.`auctionhouse` LEFT JOIN `".$characters_db[$realm_id]['name']."`.`characters` c2 ON `c2`.`guid`=`auctionhouse`.`buyguid`
 WHERE `auctionhouse`.`itemowner`=`characters`.`guid` AND `auctionhouse`.`item_template`=`item_template`.`entry` AND `auctionhouse`.`itemguid`=`item_instance`.`guid` $search_filter
 $order_side ORDER BY `auctionhouse`.`$order_by` $order_dir LIMIT $sql_search_limit");
 $tot_found = $sql->num_rows($result);

 $output .="<center>
		<form action=\"ahstats.php\" method=\"get\" name=\"form\">
			<input type=\"hidden\" name=\"action\" value=\"search_auctions\" />
			<input type=\"hidden\" name=\"error\" value=\"2\" />
		<table class=\"top_hidden\">
          <tr><td>
		<input type=\"text\" size=\"30\" name=\"search_value\" />

	  <select name=\"search_by\">
	    <option value=\"item_name\">{$lang_auctionhouse['item_name']}</option>
		<option value=\"item_id\">{$lang_auctionhouse['item_id']}</option>
		<option value=\"seller_name\">{$lang_auctionhouse['seller_name']}</option>
		<option value=\"buyer_name\">{$lang_auctionhouse['buyer_name']}</option>
	   </select></form>

	   <select name=\"search_class\">
	    <option value=\"-1\">{$lang_auctionhouse['all']}</option>
		<option value=\"0\">{$lang_item['consumable']}</option>
		<option value=\"1\">{$lang_item['bag']}</option>
		<option value=\"2\">{$lang_item['weapon']}</option>
		<option value=\"4\">{$lang_item['armor']}</option>
		<option value=\"5\">{$lang_item['reagent']}</option>
		<option value=\"7\">{$lang_item['trade_goods']}</option>
		<option value=\"9\">{$lang_item['recipe']}</option>
		<option value=\"11\">{$lang_item['quiver']}</option>
		<option value=\"14\">{$lang_item['permanent']}</option>
		<option value=\"15\">{$lang_item['misc_short']}</option>
	   </select>

	  <select name=\"search_quality\">
		<option value=\"-1\">{$lang_auctionhouse['all']}</option>
		<option value=\"0\">{$lang_item['poor']}</option>
		<option value=\"1\">{$lang_item['common']}</option>
		<option value=\"2\">{$lang_item['uncommon']}</option>
		<option value=\"3\">{$lang_item['rare']}</option>
		<option value=\"4\">{$lang_item['epic']}</option>
		<option value=\"5\">{$lang_item['legendary']}</option>
		<option value=\"6\">{$lang_item['artifact']}</option>
	   </select>
	 </td><td>";
	   makebutton($lang_global['search'], "javascript:do_submit()",80);
	   makebutton($lang_global['back'], "javascript:window.history.back()",80);
 $output .= "</td>
  </tr></table></form>

 <table class=\"lined\">
   <tr>
	<th width=\"10%\"><a href=\"ahstats.php?action=search_auctions&amp;error=2&amp;order_by=itemowner&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;dir=$dir\">".($order_by=='itemowner' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['seller']}</a></th>
	<th width=\"20%\"><a href=\"ahstats.php?action=search_auctions&amp;error=2&amp;order_by=item_template&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;dir=$dir\">".($order_by=='item_template' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['item']}</a></th>
	<th width=\"15%\"><a href=\"ahstats.php?action=search_auctions&amp;error=2&amp;order_by=buyoutprice&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;dir=$dir\">".($order_by=='buyoutprice' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['buyoutprice']}</a></th>
	<th width=\"15%\"><a href=\"ahstats.php?action=search_auctions&amp;error=2&amp;order_by=time&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;dir=$dir\">".($order_by=='time' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['timeleft']}</a></th>
	<th width=\"10%\"><a href=\"ahstats.php?action=search_auctions&amp;error=2&amp;order_by=buyguid&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;dir=$dir\">".($order_by=='buyguid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['buyer']}</a></th>
	<th width=\"15%\"><a href=\"ahstats.php?action=search_auctions&amp;error=2&amp;order_by=lastbid&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;dir=$dir\">".($order_by=='lastbid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['lastbid']}</a></th>
	<th width=\"15%\"><a href=\"ahstats.php?action=search_auctions&amp;error=2&amp;order_by=startbid&amp;search_by=$search_by&amp;search_value=$search_value&amp;search_quality=$search_quality&amp;search_class=$search_class&amp;dir=$dir\">".($order_by=='startbid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_auctionhouse['firstbid']}</a></th>
   </tr>";

 while ($rows = $sql->fetch_row($result)) {
	$output .= "<tr>";
	foreach($rows as $row => $value) {
		switch ($row) {
			case 4:
			$value = ($value >= 0)? (floor($value / 86400).$lang_auctionhouse['dayshortcut']." ". floor(($value % 86400)/3600).$lang_auctionhouse['hourshortcut']." ".floor((($value % 86400)%3600)/60).$lang_auctionhouse['mnshortcut']) : $lang_auctionhouse['auction_over'];
			break;
			case 5:
			$value = "<b>".((!empty($rows[10])) ? "<font color=".$sidecolor[$rows[10]].">".htmlentities($value)."</font>" : "N/A")."</b>";
			break;
			case 7:
			case 6:
			case 3:
			$g = floor($value/10000);
			$value -= $g*10000;
			$s = floor($value/100);
			$value -= $s*100;
			$c = $value;
			$value = $g."<img src=\"./img/gold.gif\" /> ".$s."<img src=\"./img/silver.gif\" /> ".$c."<img src=\"./img/copper.gif\" /> ";
			break;
			case 2:
			$value = "<a href=\"$item_datasite$rows[1]\" target=\"_blank\" onmouseover=\"toolTip,'item_tooltip')\"><img src=\"".get_icon($rows[1])."\" class=\"icon_border_0\" alt=\"$value\"><br/>$value".(($rows[8]>1) ? " (x$rows[8])" : "")."</img></a>";
			break;
			case 0:
			$value = "<b>".((!empty($rows[9])) ? "<font color=".$sidecolor[$rows[9]].">".htmlentities($value)."</font>" : "N/A")."</b>";
			break;
		}
		if (!in_array($row,$hiddencols)) $output .= "<td><center>".$value."</center></td>";
	}
	$output .= "</tr>";
 }
 $sql->close();

 $output .= "<tr><td colspan=\"7\" class=\"hidden\" align=\"right\">{$lang_auctionhouse['tot_found']} : $tot_found {$lang_global['limit']} : $sql_search_limit</td></tr>
   </table></center>";
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
   $output .= "<h1><font class=\"error\">{$lang_auctionhouse['search_results']}</font></h1>";
   break;
default:
   $output .= "<h1>{$lang_auctionhouse['auctionhouse']}</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "browse_auctions":
   browse_auctions();
   break;
case "search_auctions":
   search_auctions();
   break;
default:
   browse_auctions();
}

require_once("footer.php");
?>
