<?php


//#############################################################################
//get skill type by its id

function skill_get_type($id, &$sqlm)
{
  //This table came from CSWOWD as its fields are named
  $skill_type = $sqlm->fetch_assoc($sqlm->query('SELECT Category FROM dbc_skillline WHERE id='.$id.' LIMIT 1'));
  return $skill_type['Category'];
}


//#############################################################################
//get skill name by its id

function skill_get_name($id, &$sqlm)
{
  //This table came from CSWOWD as its fields are named
  $skill_name = $sqlm->fetch_assoc($sqlm->query('SELECT Name FROM dbc_skillline WHERE id='.$id.' LIMIT 1'));
  return $skill_name['Name'];
}


?>
