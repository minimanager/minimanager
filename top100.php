<?php

require_once("header.php");
require_once("scripts/id_tab.php");
require_once("scripts/defines.php");
valid_login(0);

$sql = new SQL;
$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

$start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
$order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) :"honor";
$dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
$order_dir = ($dir) ? "DESC" : "DESC";
$dir = ($dir) ? 0 : 1;

$result = $sql->query("SELECT guid, name, race, class, account, totaltime, online,
		CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_POINTS+1)."), ' ', -1) AS UNSIGNED) AS honor,
		CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_HONOR_KILL+1)."), ' ', -1) AS UNSIGNED) AS kills,
		CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
		CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_ARENA_POINTS+1)."), ' ', -1) AS UNSIGNED) AS arena,
		CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_GUILD_ID+1)."), ' ', -1) AS UNSIGNED) as GNAME,
		mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender,
		CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_GOLD+1)."), ' ', -1) AS UNSIGNED) as money
		FROM `characters` $order_side ORDER BY $order_by $order_dir LIMIT 100");

 $total_found = $sql->num_rows($result);

 $output .= "<center><table class=\"top_hidden\">
       <tr><td>";
  $output .= "</td>
     <td align=\"right\">Total: $total_found</td>
 </tr></table>";

  $output .= "<table class=\"lined\">
	<tr>
		<th width=\"10%\">{$lang_top['name']}</th>
		<th width=\"5%\">{$lang_top['race']}</th>
		<th width=\"5%\">{$lang_top['class']}</th>
		<th width=\"5%\"><a href=\"top100.php?order_by=level&amp;dir=$dir\"".($order_by=='level' ? " class=\"$order_dir\"" : "").">{$lang_top['level']}</a></th>
		<th width=\"19%\">{$lang_top['guild']}</th>
		<th width=\"16%\"><a href=\"top100.php?order_by=money&amp;dir=$dir\"".($order_by=='money' ? " class=\"$order_dir\"" : "").">{$lang_top['money']}</a></th>
		<th width=\"5%\"><a href=\"top100.php?order_by=honor&amp;dir=$dir\"".($order_by=='honor' ? " class=\"$order_dir\"" : "").">{$lang_top['rank']}</a></th>
		<th width=\"5%\">{$lang_top['honor_points']}</th>
		<th width=\"5%\"><a href=\"top100.php?order_by=kills&amp;dir=$dir\"".($order_by=='kills' ? " class=\"$order_dir\"" : "").">{$lang_top['kills']}</a></th>
		<th width=\"5%\"><a href=\"top100.php?order_by=arena&amp;dir=$dir\"".($order_by=='arena' ? " class=\"$order_dir\"" : "").">{$lang_top['arena_points']}</a></th>
		<th width=\"15%\"><a href=\"top100.php?order_by=totaltime&amp;dir=$dir\"".($order_by=='totaltime' ? " class=\"$order_dir\"" : "").">{$lang_top['time_played']}</a></th>
		<th width=\"5%\">{$lang_top['online']}</th>
	</tr>";

for ($i=1; $i<=$total_found; $i++){
		$char = $sql->fetch_array($result);

	$guild_name = $sql->fetch_row($sql->query("SELECT `name` FROM `guild` WHERE `guildid`=".$char[11].";"));

	$g = floor($char[13]/10000);
	$char[13] -= $g*10000;
	$s = floor($char[13]/100);
	$char[13] -= $s*100;
	$c = $char[13];
	$money = "";
		if ($char[13] > 0){
	$money = $g."<img src=\"./img/gold.gif\" /> ".$s."<img src=\"./img/silver.gif\" /> ".$c."<img src=\"./img/copper.gif\" /> ";
		}

	$days = floor(round($char[5] / 3600)/24);
	$hours = round($char[5] / 3600) - ($days * 24);
	$time = "";
		if ($days > 0) {
			$time .= $days;
			$time .= " days ";
		}
		if ($hours > 0){
			$time .= $hours;
			$time .= " hours";
		}

    $level = $char[9];

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

	$output .= "<tr valign=top>
		<td><a href=\"char.php?id=$char[0]\"><span onmousemove='toolTip(\"".get_player_user_level($gm)."\",\"item_tooltip\")' onmouseout='toolTip()'>".htmlentities($char[1])."</span></a></td>
        <td><img src='img/c_icons/{$char[2]}-{$char[12]}.gif' onmousemove='toolTip(\"".get_player_race($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
		<td><img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".get_player_class($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
		<td>$lev</td>
		<td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$char[11]\">".htmlentities($guild_name[0])."</a></td>
		<td>$money</td>
		<td><span onmouseover='toolTip(\"".$CHAR_RANK[$CHAR_RACE[$char[2]][1]][pvp_ranks($char[7])]."\",\"item_tooltip\")' onmouseout='toolTip()' style='color: white;'><img src='img/ranks/rank".pvp_ranks($char[7],$CHAR_RACE[$char[2]][1]).".gif'></span></td>
   	    <td>$char[7]</td>
		<td>$char[8]</td>
   	    <td>$char[10]</td>
		<td>$time</td>
		<td>".(($char[6]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
	</tr>";

 }

  $output .= "</table></center><br />";

$sql->close();


require_once("footer.php");
?>
