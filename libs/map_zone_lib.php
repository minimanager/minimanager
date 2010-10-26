<?php


//#############################################################################
//get map name by its id

function get_map_name($id, &$sqlm)
{
	$map_name = $sqlm->fetch_assoc($sqlm->query('
		SELECT name01
		FROM dbc_map
		WHERE id= '.$id.' LIMIT 1'));
	return $map_name['name01'];
}


//#############################################################################
//get zone name by its id

function get_zone_name($id, &$sqlm)
{
    $zone_name = $sqlm->fetch_assoc($sqlm->query('
		SELECT name01
		FROM dbc_areatable
		WHERE id= '.$id.' LIMIT 1'));
	return $zone_name['name01'];
}

//#############################################################################
//get zone for map name by its id

function get_map_zone($id, &$sqlm)
{
	$map_zone = $sqlm->fetch_assoc($sqlm->query('
		SELECT area_id
		FROM dbc_map
		WHERE id='.$id.' LIMIT 1'));
	return get_zone_name($map_zone['area_id'], $sqlm);
}

//#############################################################################
//get map type type by its id

function get_map_type_name($id)
{
	$map_type_name = array
    (
       0 => '',
       1 => '<font color="#3300CC">Party</font>',
       2 => '<font color="#FF8000">Raid</font>',
       3 => '<font color="#FF0000">PVP</font>',
       4 => '<font color="#339900">Arena</font>',
       5 => ''
    );
	return $map_type_name[$id];
}

//#############################################################################
//get map type by its id

function get_map_type($id, &$sqlm)
{
	global $char_aura;
	$map_type = $sqlm->fetch_assoc($sqlm->query('
		SELECT type
		FROM dbc_map
		WHERE id='.$id.' LIMIT 1'));
	return get_map_type_name($map_type['type'], $sqlm);
}

//#############################################################################
//get map expansion by its id

function get_map_exp($id, &$sqlm)
{
require_once 'get_lib.php';

	$exp_lvl_arr = id_get_exp_lvl();

	$map_exp = $sqlm->fetch_assoc($sqlm->query('
		SELECT expansion
		FROM dbc_map
		WHERE id='.$id.' LIMIT 1'));
	return $exp_lvl_arr[$map_exp['expansion']][2];
}

//#############################################################################
//get map number of people by its id

function get_map_ppl($id, &$sqlm)
{
require_once 'char_lib.php';

	$map_ppl = $sqlm->fetch_assoc($sqlm->query('
		SELECT number_pl
		FROM dbc_map
		WHERE id='.$id.' LIMIT 1'));
	return char_get_level_color($map_ppl['number_pl']);
}

?>
