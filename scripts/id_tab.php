<?php


//#############################################################################
//get WOW Expansion by id

function id_get_exp_lvl()
{
  $exp_lvl_arr =
    array
    (
      0 => array(0, "Classic",                ""     ),
      1 => array(1, "The Burning Crusade",    "TBC"  ),
      2 => array(2, "Wrath of the Lich King", "WotLK")
    );
  return $exp_lvl_arr;
}


//#############################################################################
//get GM level by ID

function id_get_gm_level($id)
{
  global $lang_id_tab, $gm_level_arr;
  if(isset($gm_level_arr[$id]))
    return $gm_level_arr[$id][1];
  else
    return($lang_id_tab['unknown']);
}


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


//#############################################################################
//get spell name by its id

function get_spell_name($id, &$sqlm=0)
{
  global $mmfpm_db;
  // not all functions that call this function will pass reference to existing SQL links
  // so we need to check and overload when needed
  if(empty($sqlm))
  {
    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  }
  $spell_name = $sqlm->fetch_row($sqlm->query("SELECT `spellname_loc0` FROM `dbc_spell` WHERE `spellID`={$id} LIMIT 1"));
  return $spell_name[0];
}


//#############################################################################
//get spell rank by its id

function get_spell_rank($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $spell_rank = $sqlm->fetch_row($sqlm->query("SELECT `rank_loc0` FROM `dbc_spell` WHERE `spellID`={$id} LIMIT 1"));
  return $spell_rank[0];
}


//#############################################################################
//get item set name by its id

 function get_itemset_name($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $itemset = $sqlm->fetch_row($sqlm->query("SELECT `name_loc0` FROM `dbc_itemset` WHERE `itemsetID`={$id} LIMIT 1"));
  return $itemset[0];
}


?>
