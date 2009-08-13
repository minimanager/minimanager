<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Shnappie
 * Copyright: Q.SA, Shnappie
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

 require_once("header.php");
 require_once("scripts/defines.php");
 require_once("libs/char_lib.php");
  valid_login($action_permission['read']);

 //global $lang_honor, $lang_global, $output, $characters_db, $realm_id, $itemperpage, $realm_db;

 $sql = new SQL;
 $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
 $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) :"honor";

 $query = $sql->query("SELECT
        guid,name,race,class,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_POINTS+1)."), ' ', -1) AS UNSIGNED) AS honor ,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_KILL+1)."), ' ', -1) AS UNSIGNED) AS kills,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_ARENA_POINTS+1)."), ' ', -1) AS UNSIGNED) AS arena,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_GUILD_ID+1)."), ' ', -1) AS UNSIGNED) as GNAME,
        mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender
        FROM `characters`
        where race in (1,3,4,7,11)
        ORDER BY $order_by DESC
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
  <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">{$lang_honor['honor']}</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">{$lang_honor['honor points']}</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=kills\"".($order_by=='kills' ? " class=DESC" : "").">Kills</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=arena\"".($order_by=='arena' ? " class=DESC" : "").">AP</a></th>
    <th width=\"30%\">{$lang_honor['guild']}</th>
  </tr>";

while ($char = $sql->fetch_row($query)) {

$guild_name = $sql->fetch_row($sql->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char[8].";"));

    $output .= " <tr>
       <td><a href=\"char.php?id=$char[0]\">".htmlentities($char[1])."</a></td>
       <td><img src='img/c_icons/{$char[2]}-{$char[9]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
         <td><img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
       <td>".char_get_level_color($char[6])."</td>
       <td><span onmouseover='toolTip(\"".char_get_pvp_rank_name($char[4], char_get_side_id($char[2]))."\",\"item_tooltip\")' onmouseout='toolTip()' style='color: white;'><img src='img/ranks/rank".char_get_pvp_rank_id($char[4], char_get_side_id($char[2])).".gif'></span></td>
       <td>$char[4]</td>
       <td>$char[5]</td>
       <td>$char[7]</td>
       <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[8]\">".htmlentities($guild_name[0])."</a></td>
       </tr>";
}

$output .= "</table><br /></fieldset>";

$query = $sql->query("SELECT
        guid,name,race,class,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_POINTS+1)."), ' ', -1) AS UNSIGNED) AS honor ,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_KILL+1)."), ' ', -1) AS UNSIGNED) AS kills,
        CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_ARENA_POINTS+1)."), ' ', -1) AS UNSIGNED) AS arena,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', ".(CHAR_DATA_OFFSET_GUILD_ID+1)."), ' ', -1) AS UNSIGNED) as GNAME,
        mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender
        FROM `characters`
        where race not in (1,3,4,7,11)
        ORDER BY $order_by DESC
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
  <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">{$lang_honor['honor']}</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=honor\"".($order_by=='honor' ? " class=DESC" : "").">{$lang_honor['honor points']}</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=kills\"".($order_by=='kills' ? " class=DESC" : "").">Kills</a></th>
  <th width=\"5%\"><a href=\"honor.php?order_by=arena\"".($order_by=='arena' ? " class=DESC" : "").">AP</a></th>
    <th width=\"30%\">{$lang_honor['guild']}</th>
  </tr>";

while ($char = $sql->fetch_row($query)) {

$guild_name = $sql->fetch_row($sql->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char[8].";"));

    $output .= " <tr>
       <td><a href=\"char.php?id=$char[0]\">".htmlentities($char[1])."</a></td>
       <td><img src='img/c_icons/{$char[2]}-{$char[9]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
         <td><img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()'></td>
       <td>".char_get_level_color($char[6])."</td>
         <td><span onmouseover='toolTip(\"".char_get_pvp_rank_name($char[4], char_get_side_id($char[2]))."\",\"item_tooltip\")' onmouseout='toolTip()' style='color: white;'><img src='img/ranks/rank".char_get_pvp_rank_id($char[4], char_get_side_id($char[2])).".gif'></span></td>
       <td>$char[4]</td>
       <td>$char[5]</td>
       <td>$char[7]</td>
       <td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[8]\">".htmlentities($guild_name[0])."</a></td>
       </tr>";
}

$output .= "</table><br /></fieldset>";

require_once("footer.php");
?>
