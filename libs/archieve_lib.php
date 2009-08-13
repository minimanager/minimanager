<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * License: GNU General Public License v2(GPL)
 */


//#############################################################################
//get achievement name by its id

function achieve_get_name($id, &$sqlm)
{
  $achievement_name = $sqlm->fetch_assoc($sqlm->query('SELECT name01 FROM dbc_achievement WHERE id= '.$id.' LIMIT 1'));
  return $achievement_name['name01'];
}


//#############################################################################
//get achievement category name by its id

function achieve_get_category($id, &$sqlm)
{
  $category_id= $sqlm->fetch_assoc($sqlm->query('SELECT categoryid FROM dbc_achievement WHERE id = '.$id.' LIMIT 1'));
  $category_name = $sqlm->fetch_assoc($sqlm->query('SELECT name01 FROM dbc_achievement_category WHERE id = '.$category_id['categoryid'].' LIMIT 1'));
  return $category_name['name01'];
}


//#############################################################################
//get achievement reward name by its id

function achieve_get_reward($id, &$sqlm)
{
  $achievement_reward = $sqlm->fetch_assoc($sqlm->query('SELECT rewarddesc01 FROM dbc_achievement WHERE id ='.$id.' LIMIT 1'));
  return $achievement_reward['rewarddesc01'];
}


//#############################################################################
//get achievement points name by its id

function achieve_get_points($id, &$sqlm)
{
  $achievement_points = $sqlm->fetch_assoc($sqlm->query('SELECT rewpoints FROM dbc_achievement WHERE id = '.$id.' LIMIT 1'));
  return $achievement_points['rewpoints'];
}

?>
