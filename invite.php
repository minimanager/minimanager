<?php 
// MySQL settings 
$WoWHostname = "localhost"; // server hostname or ip 
$WoWUsername = "*"; // MySQL username 
$WoWPassword = "*"; // MySQL password 
$CharacterDatabase = 'characters'; // TC characters DB 
$RealmDatabase = 'realmd'; // TC realmd DB 
$InviteDatabase = 'mmfpm'; // MiniManager DB 
$CharacterDatabaseEncoding = 'utf8';  

// MySQL connect 
$WoWconn = mysql_connect($WoWHostname, $WoWUsername, $WoWPassword) or die('Connection failed: ' . mysql_error()); 

mysql_select_db($CharacterDatabase, $WoWconn) or die('Select DB failed: ' . mysql_error()); 
// Select characters by name order 
$sql = "SELECT `guid`, `account`, `name` FROM `characters` ORDER BY `name`"; 
$result = mysql_query($sql, $WoWconn) or die('Query failed: ' . mysql_error()); 
?> 
<table border=0 cellspacing=0 cellpadding=3> 
<tr> 
  <td align=\"left\" valign=\"middle\" width=\"120\"><strong>Inviter</strong></td> 
  <td align=\"left\" valign=\"middle\" width=\"50\"><strong>#</strong></td> 
  <td align=\"left\" valign=\"middle\" width=\"90\"><strong>Treated</strong></td>     
  <td align=\"left\" valign=\"middle\" width=\"90\"><strong>Rewarded</strong></td>     
</tr> 
<?php 
// Connect & select statements 
$realm_db = mysql_connect($WoWHostname, $WoWUsername, $WoWPassword); 
mysql_select_db($RealmDatabase, $realm_db); 
$db_result = mysql_query("SET NAMES $CharacterDatabaseEncoding", $realm_db); 

$world_db = mysql_connect($WoWHostname, $WoWUsername, $WoWPassword, TRUE); 
mysql_select_db($CharacterDatabase, $world_db); 
$db_result = mysql_query("SET NAMES $CharacterDatabaseEncoding", $world_db); 

$invite_db = mysql_connect($WoWHostname, $WoWUsername, $WoWPassword); 
mysql_select_db($InviteDatabase, $invite_db); 
$db_result = mysql_query("SET NAMES $CharacterDatabaseEncoding", $invite_db); 

$invites_query = mysql_query("SELECT * FROM $InviteDatabase.`mm_point_system_invites` GROUP BY `InvitedBy", $invite_db)or die(mysql_error()); 

// Fetch data for each row in mm_point_system_invites, grouped by inviter 
while($invites_results = mysql_fetch_array($invites_query)) { 
$inviter_character = $invites_results['InvitedBy']; 
$inviter_treated_result = $invites_results['Treated']; 
$inviter_rewarded_result = $invites_results['Rewarded']; 

// Get inviter name 
$inviter_name_query = mysql_query("SELECT `name` FROM $CharacterDatabase.`characters` WHERE `guid` = '$inviter_character'", $world_db)or die(mysql_error()); 
$inviter_name_results = mysql_fetch_array($inviter_name_query); 
$inviter_name = $inviter_name_results['name']; 

// Count invites 
$count_invites_query = mysql_query("SELECT (SELECT COUNT(`entry`) FROM $InviteDatabase.`mm_point_system_invites` WHERE `InvitedBy` = '$inviter_character') AS count_invites", $invite_db)or die(mysql_error()); 
$count_invites_results = mysql_fetch_array($count_invites_query); 
$count_invites = $count_invites_results['count_invites']; 

// Is Treated/Rewarded or not? 
if ($inviter_treated_result < 1) { 
    $inviter_treated = "Not treated"; 
} 
else { 
    $inviter_treated = "Treated: ".$inviter_treated_result; 
} 
if ($inviter_rewarded_result < 1) { 
    $inviter_rewarded = "Not rewarded"; 
} 
else { 
    $inviter_rewarded = "Rewarded: ".$inviter_rewarded_result; 
} 
// Display invitation status 
echo "<tr> 
    <td align=\"left\" valign=\"middle\" width=\"120\">" . $inviter_name . "</td> 
    <td align=\"left\" valign=\"middle\" width=\"50\">" . $count_invites . "</td> 
    <td align=\"left\" valign=\"middle\" width=\"90\">" . $inviter_treated . "</td>     
    <td align=\"left\" valign=\"middle\" width=\"90\">" . $inviter_rewarded . "</td>     
  </tr>"; 
} 
 ?> 
</table>