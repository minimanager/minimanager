<?php


//#############################################################################
//get map name by its id

function get_map_name($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $map_name = $sqlm->fetch_row($sqlm->query("SELECT `name01` FROM `dbc_map` WHERE `id`={$id} LIMIT 1"));
  return $map_name[0];
}


//#############################################################################
//get zone name by its id

function get_zone_name($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $zone_name = $sqlm->fetch_row($sqlm->query("SELECT `name` FROM `dbc_zones` WHERE `id` = {$id} LIMIT 1")); //This table does not exist on dbc files, it was taken from CSWOWD
  return $zone_name[0];
}


?>
