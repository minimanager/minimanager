<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * License: GNU General Public License v2(GPL)
 */


//#############################################################################
//get achievement name by its id

function achieve_get_name($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $achievement_name = $sqlm->fetch_row($sqlm->query("SELECT `name01` FROM `dbc_achievement` WHERE `id`={$id} LIMIT 1"));
  return $achievement_name[0];
}


//#############################################################################
//get achievement category name by its id

function achieve_get_category($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $category_id= $sqlm->fetch_row($sqlm->query("SELECT `categoryid` FROM `dbc_achievement` WHERE `id` = {$id} LIMIT 1"));
  $category_name = $sqlm->fetch_row($sqlm->query("SELECT `name01` FROM `dbc_achievement_category` WHERE `id` = $category_id[0] LIMIT 1"));
  return $category_name[0];

}


//#############################################################################
//get achievement reward name by its id

function achieve_get_reward($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $achievement_reward = $sqlm->fetch_row($sqlm->query("SELECT `rewarddesc01` FROM `dbc_achievement` WHERE `id`={$id} LIMIT 1"));
  return $achievement_reward[0];

}


//#############################################################################
//get achievement points name by its id

function achieve_get_points($id)
{
  global $mmfpm_db;
  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $achievement_points = $sqlm->fetch_row($sqlm->query("SELECT `rewpoints` FROM `dbc_achievement` WHERE `id`={$id} LIMIT 1"));
  return $achievement_points[0];

}

?>
