<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Shnappie
 * Copyright: Q.SA, Shnappie
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

 require_once("header.php");
 valid_login($action_permission['read']);
 require_once("scripts/get_lib.php");
 require_once("scripts/defines.php");
 require_once("scripts/id_tab.php");

 global $lang_honor, $lang_global, $output, $characters_db, $realm_id, $itemperpage, $realm_db;

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "atid";

 $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
 $order_dir = ($dir) ? "ASC" : "DESC";
 $dir = ($dir) ? 0 : 1;

 $query = $sql->query("SELECT
				guid,name,race,class,
				CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_POINTS+1)."), ' ', -1) AS UNSIGNED) AS honor ,
				CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
				CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_GUILD_ID+1)."), ' ', -1) AS UNSIGNED) as GNAME,
				mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender
				FROM `characters`
				where race in (1,3,4,7,11)
				order by CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_POINTS+1)."), ' ', -1) AS UNSIGNED) desc
				LIMIT 25;");

 $this_page = $sql->num_rows($query);

$output .= "<script type=\"text/javascript\">
	answerbox.btn_ok='{$lang_global['yes_low']}';
	answerbox.btn_cancel='{$lang_global['no']}';
 </script>
 <center>
 <fieldset style=\"width: 776px;\">
	<legend><img src='img/alliance.gif' /></legend>

 <table class=\"lined\" style=\"width: 705px;\">

  <tr class=\"bold\">
    <td colspan=\"11\">{$lang_honor['allied']} {$lang_honor ['browse_honor']}</td>
  </tr>

  <tr>
    <th width=\"30%\">{$lang_honor['guid']}</th>
    <th width=\"7%\">{$lang_honor['race']}</th>
    <th width=\"7%\">{$lang_honor['class']}</th>
    <th width=\"7%\">{$lang_honor['level']}</th>
    <th width=\"10%\">{$lang_honor['honor points']}</th>
    <th width=\"9%\">{$lang_honor['honor']}</th>
    <th width=\"30%\">{$lang_honor['guild']}</th>
  </tr>";


while ($char = $sql->fetch_row($query))	{

$guild_name = $sql->fetch_row($sql->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char[6].";"));

    $level = $char[5];

    if($level < 10)
      $lev = '<font color="#FFFFFF">'.$level.'</font>';
    else if($level < 20)
      $lev = '<font color="#858585">'.$level.'</font>';
    else if($level < 30)
      $lev = '<font color="#339900">'.$level.'</font>';
    else if($level < 40)
      $lev = '<font color="#3300CC">'.$level.'</font>';
    else if($level < 50)
      $lev = '<font color="#C552FF">'.$level.'</font>';
    else if($level < 60)
      $lev = '<font color="#FF8000">'.$level.'</font>';
    else if($level < 70)
      $lev = '<font color="#FFF280">'.$level.'</font>';  
    else if($level < 80)
      $lev = '<font color="#FF0000">'.$level.'</font>';  
    else
      $lev = '<font color="#000000">'.$level.'</font>';

  	$output .= " <tr>
			 <td><a href=\"char.php?id=$char[0]\">$char[1]</a></td>
		 	 <td><img src='img/c_icons/{$char[2]}-{$char[7]}.gif'></td>
		  	 <td><img src='img/c_icons/{$char[3]}.gif'></td>
			 <td>$lev</td>
			 <td>$char[4]</td>
		     <td><span onmouseover='toolTip(\"".$CHAR_RANK[$CHAR_RACE[$char[2]][1]][pvp_ranks($char[4])]."\",\"item_tooltip\")' onmouseout='toolTip()' style='color: white;'><img src='img/ranks/rank".pvp_ranks($char[4],$CHAR_RACE[$char[2]][1]).".gif'></span></td>
			 <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[6]\">$guild_name[0]</a></td>
 			 </tr>";
}

$output .= "</table><br /></fieldset>";

$query = $sql->query("SELECT
 				guid,name,race,class,
 				CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_POINTS+1)."), ' ', -1) AS UNSIGNED) AS highest_rank ,
 				CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
				CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_GUILD_ID+1)."), ' ', -1) AS UNSIGNED) as GNAME,
				mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender
				FROM `characters`
				where race not in (1,3,4,7,11)
				order by CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_POINTS+1)."), ' ', -1) AS UNSIGNED) desc
				LIMIT 25;");


 $this_page = $sql->num_rows($query);

$output .= "<script type=\"text/javascript\">
	answerbox.btn_ok='{$lang_global['yes_low']}';
	answerbox.btn_cancel='{$lang_global['no']}';
 </script>
 <center>
 <fieldset style=\"width: 776px;\">
	<legend><img src='img/horde.gif' /></legend>
 <table class=\"lined\" style=\"width: 705px;\">
  <tr class=\"bold\">
    <td colspan=\"11\">{$lang_honor['horde']} {$lang_honor ['browse_honor']}</td>
  </tr>

  <tr>
    <th width=\"30%\">{$lang_honor['guid']}</th>
    <th width=\"7%\">{$lang_honor['race']}</th>
    <th width=\"7%\">{$lang_honor['class']}</th>
    <th width=\"7%\">{$lang_honor['level']}</th>
    <th width=\"10%\">{$lang_honor['honor points']}</th>
    <th width=\"9%\">{$lang_honor['honor']}</th>
    <th width=\"30%\">{$lang_honor['guild']}</th>
  </tr>";


while ($char = $sql->fetch_row($query))	{

$guild_name = $sql->fetch_row($sql->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char[6].";"));

    $level = $char[5];

    if($level < 10)
      $lev = '<font color="#FFFFFF">'.$level.'</font>';
    else if($level < 20)
      $lev = '<font color="#858585">'.$level.'</font>';
    else if($level < 30)
      $lev = '<font color="#339900">'.$level.'</font>';
    else if($level < 40)
      $lev = '<font color="#3300CC">'.$level.'</font>';
    else if($level < 50)
      $lev = '<font color="#C552FF">'.$level.'</font>';
    else if($level < 60)
      $lev = '<font color="#FF8000">'.$level.'</font>';
    else if($level < 70)
      $lev = '<font color="#FFF280">'.$level.'</font>';  
    else if($level < 80)
      $lev = '<font color="#FF0000">'.$level.'</font>';  
    else
      $lev = '<font color="#000000">'.$level.'</font>';

  	$output .= " <tr>
			 <td><a href=\"char.php?id=$char[0]\">$char[1]</a></td>
		 	 <td><img src='img/c_icons/{$char[2]}-{$char[7]}.gif'></td>
		  	 <td><img src='img/c_icons/{$char[3]}.gif'></td>
			 <td>$lev</td>
			 <td>$char[4]</td>
		     <td><span onmouseover='toolTip(\"".$CHAR_RANK[$CHAR_RACE[$char[2]][1]][pvp_ranks($char[4])]."\",\"item_tooltip\")' onmouseout='toolTip()' style='color: white;'><img src='img/ranks/rank".pvp_ranks($char[4],$CHAR_RACE[$char[2]][1]).".gif'></span></td>
			 <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[6]\">$guild_name[0]</a></td>
			 </tr>";
}

$output .= "</table><br /></fieldset>";
  $sql->close();


require_once("footer.php");
?>
