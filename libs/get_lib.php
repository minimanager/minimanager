<?php


//#############################################################################
//get name from realmlist.name

function get_realm_name($realm_id)
{
	global 	$realm_db;

$sqlr = new SQL;
$sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

	$result = $sqlr->query("
		SELECT name 
		FROM `realmlist` 
		WHERE id = '$realm_id'");
	$realm_name = $sqlr->result($result, 0);

	return $realm_name;
}


//#############################################################################
//get WOW Expansion by id

function id_get_exp_lvl()
{
	$exp_lvl_arr =
		array
		(
			0 => array(0, "Classic",                "WOW"  ),
			1 => array(1, "The Burning Crusade",    "TBC"  ),
			2 => array(2, "Wrath of the Lich King", "WotLK")
		);
	return $exp_lvl_arr;
}


//#############################################################################
//get WOW version by client build

function id_get_build()
{
	$client_build =
		array
		(
			5875 =>  array(5875,  "1.12.1"),
			6005 =>  array(6005,  "1.12.2"),
			8606 =>  array(8606,  "2.4.3"),   // has parsed UpdateFields.h
			9183 =>  array(9183,  "3.0.3"),   // has parsed UpdateFields.h
			9947 =>  array(9947,  "3.1.3"),   // has parsed UpdateFields.h
			10505 => array(10505, "3.2.2a"),  // has parsed UpdateFields.h
			11159 => array(11159, "3.3.0a"),
			11403 => array(11403, "3.3.2"),
			11723 => array(11723, "3.3.3a"),
			12340 => array(12340, "3.3.5a")
		);
	return $client_build;
}


//#############################################################################
//get GM level by ID

function id_get_gm_level($id)
{
	global 	$lang_id_tab, 
			$gm_level_arr;

	if(isset($gm_level_arr[$id]))
		return $gm_level_arr[$id][1];
	else
		return($lang_id_tab['unknown']);
}


//#############################################################################
//set color per Level range

function get_days_with_color($how_long)
{
	$days = count_days($how_long, time());

	if($days < 1)
		$lastlogin = '<font color="#009900">'.$days.'</font>';
	else if($days < 8)
		$lastlogin = '<font color="#0000CC">'.$days.'</font>';
	else if($days < 15)
		$lastlogin = '<font color="#FFFF00">'.$days.'</font>';
	else if($days < 22)
		$lastlogin = '<font color="#FF8000">'.$days.'</font>';
	else if($days < 29)
		$lastlogin = '<font color="#FF0000">'.$days.'</font>';
	else if($days < 61)
		$lastlogin = '<font color="#FF00FF">'.$days.'</font>';
	else
		$lastlogin = '<font color="#FF0000">'.$days.'</font>';

	return $lastlogin;
}


//#############################################################################
//get DBC Language from config

function get_lang_id()
{
	# DBC Language Settings
	#  0 = English
	#  1 = Korean
	#  2 = French
	#  3 = German
	#  4 = Chinese
	#  5 = Taiwanese
	#  6 = Spanish
	#  7 = Spanish Mexico
	#  8 = Russian
	#  9 = Unknown
	# 10 = Unknown
	# 11 = Unknown
	# 12 = Unknown
	# 13 = Unknown
	# 14 = Unknown
	# 15 = Unknown

	global 	$language;
	if (isset($_COOKIE["lang"]))
		$language=$_COOKIE["lang"];

// 0 = English/Default; 1 = Korean; 2 = French; 4 = German; 8 = Chinese; 16 = Taiwanese; 32 = Spanish; 64 = Russian
	switch ($language)
	{
		case 'korean':
			return 1;
			break;
		case 'french':
			return 2;
			break;
		case 'german':
			return 3;
			break;
		case 'chinese':
			return 4;
			break;
		case 'taiwanese':
			return 5;
			break;
		case 'spanish':
			return 6;
			break;
		case 'mexican':
			return 7;
		break;
		case 'russian':
			return 8;
			break;
		default:
			return 0;
			break;
	}
}


?>
