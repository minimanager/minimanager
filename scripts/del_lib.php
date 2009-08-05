<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

require_once("backup_tab.php");

//##########################################################################################
//Delete character
function del_char($guid,$realm){
	global $characters_db, $realm_db, $user_lvl, $user_id, $server_type, $tab_del_user_characters, $tab_del_user_characters_trinity;
 if ($server_type)
   $tab_del_user_characters = $tab_del_user_characters_trinity;

	$sql_01 = new SQL;
	$sql_01->connect($characters_db[$realm]['addr'], $characters_db[$realm]['user'], $characters_db[$realm]['pass'], $characters_db[$realm]['name']);
	$query = $sql_01->query("SELECT account,online FROM `characters` WHERE guid ='$guid' LIMIT 1");
	$owner_acc_id = $sql_01->result($query, 0, 'account');
	$sql_01->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
	$query1 = $sql_01->query("SELECT gmlevel FROM account WHERE id ='$owner_acc_id'");
	$owner_gmlvl = $sql_01->result($query1, 0, 'gmlevel');
	unset($query1);

	if (($user_lvl > $owner_gmlvl)||($owner_acc_id == $user_id)) {
		$sql_01->connect($characters_db[$realm]['addr'], $characters_db[$realm]['user'], $characters_db[$realm]['pass'], $characters_db[$realm]['name']);

		if (!$sql_01->result($query, 0, 'online')){
			foreach ($tab_del_user_characters as $value){
				$query = $sql_01->query("DELETE FROM {$value[0]} WHERE {$value[1]} = '$guid'");
				}

			$sql_01->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
			$query_2 = $sql_01->query("SELECT numchars FROM realmcharacters WHERE acctid ='$owner_acc_id' AND realmid = '$realm'");
			$chars_in_acc = $sql_01->result($query_2, 0, 'numchars');
			if ($chars_in_acc) $chars_in_acc--;
				else $chars_in_acc = 0;
			$query_2 = $sql_01->query("UPDATE realmcharacters SET numchars='$chars_in_acc' WHERE acctid ='$owner_acc_id' AND realmid = '$realm'");

			$sql_01->close();
			return true;
			}
		}
	$sql_01->close();
	return false;
}

//##########################################################################################
//Delete Account - return array(deletion_flag , number_of_chars_deleted)
function del_acc($acc_id){
	global $characters_db, $realm_db, $user_lvl, $user_id, $tab_del_user_characters, $tab_del_user_characters_trinity, $tab_del_user_realmd;
 if ($server_type)
   $tab_del_user_characters = $tab_del_user_characters_trinity;
	$del_char = 0;

	$sql_01 = new SQL;
	$sql_01->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
	$query = $sql_01->query("SELECT gmlevel,online FROM account WHERE id ='$acc_id'");
	$gmlevel = $sql_01->result($query, 0, 'gmlevel');

	if (($user_lvl > $gmlevel)||($acc_id == $user_id)) {
		if (!$sql_01->result($query, 0, 'online')){
		foreach ($characters_db as $db){
			$sql_01->connect($db['addr'], $db['user'], $db['pass'], $db['name']);
			$result = $sql_01->query("SELECT guid FROM `characters` WHERE account='$acc_id'");
			while ($row = $sql_01->fetch_array($result)) {
					foreach ($tab_del_user_characters as $value)
					$query = $sql_01->query("DELETE FROM $value[0] WHERE $value[1] = '$row[0]'");
				$del_char++;
			}
		}

		$sql_01->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

		foreach ($tab_del_user_realmd as $value){
				$query = $sql_01->query("DELETE FROM $value[0] WHERE $value[1] = '$acc_id'");
				}
		if ($sql_01->affected_rows()) {
			$sql_01->close();
			return array(true,$del_char);
			}
		}
	}
	$sql_01->close();
	return array(false,$del_char);
}


//##########################################################################################
//Delete Guild
function del_guild($guid,$realm){
	global $characters_db, $realm_db;

	require_once("scripts/defines.php");

	$sql_01 = new SQL;
	$sql_01->connect($characters_db[$realm]['addr'], $characters_db[$realm]['user'], $characters_db[$realm]['pass'], $characters_db[$realm]['name']);
	$query = $sql_01->query("DELETE FROM guild WHERE guildid = '$guid'");
	$query = $sql_01->query("DELETE FROM guild_rank WHERE guildid = '$guid'");

	//clean data inside characters.data field
	$temp = $sql_01->query("SELECT guid FROM guild_member WHERE guildid = '$guid'");
	while ($guild_member = $sql_01->fetch_row($temp)){
		$char_data = $sql_01->query("SELECT data FROM `characters` WHERE guid = '$guild_member[0]'");
		$data = $sql_01->result($char_data, 0, 'data');
		$data = explode(' ',$data);
		$data[CHAR_DATA_OFFSET_GUILD_ID] = 0;
		$data[CHAR_DATA_OFFSET_GUILD_RANK] = 0;
		$data = implode(" ",$data);
		$query = $sql_01->query("UPDATE `characters` SET data = '$data' WHERE guid = '$guild_member[0]'");
		}

	$query = $sql_01->query("DELETE FROM guild_member WHERE guildid = '$guid'");
	$query = $sql_01->query("DELETE FROM guild_bank_eventlog WHERE guildid = '$guid'");
	$query = $sql_01->query("DELETE FROM guild_bank_right WHERE guildid = '$guid'");
	$query = $sql_01->query("DELETE FROM guild_bank_tab WHERE guildid = '$guid'");
	$query = $sql_01->query("DELETE FROM guild_eventlog WHERE guildid = '$guid'");
	$query = $sql_01->query("DELETE FROM item_instance WHERE guid IN (SELECT item_guid FROM guild_bank_item WHERE guildid ='$guid')");
	$query = $sql_01->query("DELETE FROM guild_bank_item WHERE guildid = '$guid'");

	if ($sql_01->affected_rows()){
		$sql_01->close();
		return true;
		} else {
				$sql_01->close();
				return false;
				}
}

//##########################################################################################
//Delete Arena Team
function del_arenateam($guid,$realm){
	global $characters_db, $realm_db;

	require_once("scripts/defines.php");

	$sql_01 = new SQL;
	$sql_01->connect($characters_db[$realm]['addr'], $characters_db[$realm]['user'], $characters_db[$realm]['pass'], $characters_db[$realm]['name']);
	$query = $sql_01->query("DELETE FROM arena_team WHERE arenateamid = '$guid'");
	$query = $sql_01->query("DELETE FROM arena_team_stats WHERE arenateamid = '$guid'");

// Cant clean the character data field since the data is incorrect on these fields
// http://wiki.udbforums.org/index.php/Character_data
	//clean data inside characters.data field
	//$temp = $sql_01->query("SELECT guid FROM arena_team_member WHERE arenateamid = '$guid'");
	//while ($arenateam_member = $sql_01->fetch_row($temp)){
	//	$char_data = $sql_01->query("SELECT data FROM `characters` WHERE guid = '$arenateam_member[0]'");
	//	$data = $sql_01->result($char_data, 0, 'data');
	//	$data = explode(' ',$data);
	//	$data[CHAR_DATA_OFFSET_GUILD_ID] = 0;
	//	$data[CHAR_DATA_OFFSET_GUILD_RANK] = 0;
	//	$data = implode(" ",$data);
	//	$query = $sql_01->query("UPDATE `characters` SET data = '$data' WHERE guid = '$arenateam_member[0]'");
	//	}
    //
	$query = $sql_01->query("DELETE FROM arena_team_member WHERE arenateamid = '$guid'");

	if ($sql_01->affected_rows()){
		$sql_01->close();
		return true;
		} else {
				$sql_01->close();
				return false;
				}
}

?>
