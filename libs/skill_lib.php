<?php


//#############################################################################
//get skill type by its id

function skill_get_type($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $skill_type = $sqlm->fetch_row($sqlm->query("SELECT `Category` FROM `dbc_skillline` WHERE `id`={$id} LIMIT 1")); //This table came from CSWOWD as its fields are named
  return $skill_type[0];
}


//#############################################################################
//get skill name by its id

function skill_get_name($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $skill_name = $sqlm->fetch_row($sqlm->query("SELECT `Name` FROM `dbc_skillline` WHERE `id`={$id} LIMIT 1")); //This table came from CSWOWD as its fields are named
  return $skill_name[0];
}

?>
