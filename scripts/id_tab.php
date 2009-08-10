<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA (edted by thorazi to support multi-language)
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */


function id_get_exp_lvl()
{
  $exp_lvl_arr =
    Array
    (
      0 => array(0, "Classic",                ""     ),
      1 => array(1, "The Burning Crusade",    "TBC"  ),
      2 => array(2, "Wrath of the Lich King", "WotLK")
    );
  return $exp_lvl_arr;
}


function id_get_char_faction()
{
  global $lang_id_tab;
  $CHAR_FACTION =
    array
    (
      0 => $lang_id_tab['Alliance'],
      1 => $lang_id_tab['Horde']
    );
  return $CHAR_FACTION;
}


function id_get_char_race()
{
  global $lang_id_tab;
  $CHAR_RACE =
    array
    (
       1 => array($lang_id_tab['human'], 0),
       2 => array($lang_id_tab['orc'], 1),
       3 => array($lang_id_tab['dwarf'], 0),
       4 => array($lang_id_tab['nightelf'], 0),
       5 => array($lang_id_tab['undead'], 1),
       6 => array($lang_id_tab['tauren'], 1),
       7 => array($lang_id_tab['gnome'], 0),
       8 => array($lang_id_tab['troll'], 1),
      10 => array($lang_id_tab['bloodelf'], 1),
      11 => array($lang_id_tab['draenei'], 0)
    );
   return $CHAR_RACE;
}


function id_get_char_rank()
{
  global $lang_id_tab;
  $CHAR_RANK =
    array
    (
      0 => array
      (
        '00' => $lang_id_tab['None'],
        '01' => $lang_id_tab['None'],
         0   => $lang_id_tab['None'],
         1   => $lang_id_tab['Private'],
         2   => $lang_id_tab['Corporal'],
         3   => $lang_id_tab['Sergeant'],
         4   => $lang_id_tab['Master_Sergeant'],
         5   => $lang_id_tab['Sergeant_Major'],
         6   => $lang_id_tab['Knight'],
         7   => $lang_id_tab['Knight-Lieutenant'],
         8   => $lang_id_tab['Knight-Captain'],
         9   => $lang_id_tab['Knight-Champion'],
        10   => $lang_id_tab['Lieutenant_Commander'],
        11   => $lang_id_tab['Commander'],
        12   => $lang_id_tab['Marshal'],
        13   => $lang_id_tab['Field_Marshal'],
        14   => $lang_id_tab['Grand_Marshal']
      ),
      1 => array
      (
        '00' => $lang_id_tab['None'],
        '01' => $lang_id_tab['None'],
         0   => $lang_id_tab['None'],
         1   => $lang_id_tab['Scout'],
         2   => $lang_id_tab['Grunt'],
         3   => $lang_id_tab['Sergeant'],
         4   => $lang_id_tab['Senior_Sergeant'],
         5   => $lang_id_tab['First_Sergeant'],
         6   => $lang_id_tab['Stone_Guard'],
         7   => $lang_id_tab['Blood_Guard'],
         8   => $lang_id_tab['Legionnare'],
         9   => $lang_id_tab['Centurion'],
        10   => $lang_id_tab['Champion'],
        11   => $lang_id_tab['Lieutenant_General'],
        12   => $lang_id_tab['General'],
        13   => $lang_id_tab['Warlord'],
        14   => $lang_id_tab['High_Warlord']
      )
    );
  return $CHAR_RANK;
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
  $sql = new SQL;
  $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $map_name = $sql->fetch_row($sql->query("SELECT `name01` FROM `dbc_map` WHERE `id`={$id} LIMIT 1"));
  $sql->close();
  unset($sql);
  return $map_name[0];
}


//#############################################################################
//get zone name by its id
function get_zone_name($id)
{
  global $mmfpm_db;
  $sql = new SQL;
  $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $zone_name = $sql->fetch_row($sql->query("SELECT `name` FROM `dbc_zones` WHERE `id` = {$id} LIMIT 1")); //This table does not exist on dbc files, it was taken from CSWOWD
  $sql->close();
  unset($sql);
  return $zone_name[0];
}


//#############################################################################
//get player class by its id
function get_player_class($id)
{
  global $lang_id_tab;
  //global $mmfpm_db;
  //$sql = new SQL;
  //$sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  //$class_name = $sql->fetch_row($sql->query("SELECT `field_4` FROM `dbc_chrclasses` WHERE `id`={$id} LIMIT 1")); //needs to be improved with new tables
  //$sql->close();
  //unset($sql);
  //return $class_name[0];

  $class = Array
  (
    1  => array(1,$lang_id_tab['warrior'],"",""),
	2  => array(2,$lang_id_tab['paladin'],"",""),
	3  => array(3,$lang_id_tab['hunter'],"",""),
	4  => array(4,$lang_id_tab['rogue'],"",""),
	5  => array(5,$lang_id_tab['priest'],"",""),
	6  => array(6,$lang_id_tab['death_knight'],"",""),
	7  => array(7,$lang_id_tab['shaman'],"",""),
	8  => array(8,$lang_id_tab['mage'],"",""),
	9  => array(9,$lang_id_tab['warlock'],"",""),
	11 => array(11,$lang_id_tab['druid'],"","")
  );
  return $class[$id][1];
}


//#############################################################################
//get player race by its id
function get_player_race($id)
{
  //global $mmfpm_db;
  //$sql = new SQL;
  //$sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  //$race_name = $sql->fetch_row($sql->query("SELECT `field_12` FROM `dbc_chrraces` WHERE `id`={$id} LIMIT 1")); //we need to use either db or function id_get_char_race, if from db we need to determine side (alliance or horde) as that info isnt present in chrraces.dbc
  //$sql->close();
  //unset($sql);
  //return $race_name[0];
  $CHAR_RACE = id_get_char_race();
  return $CHAR_RACE[$id][0];
}


//#############################################################################
//get pvp rank ID by honor point
function pvp_ranks($honor=0, $faction=0)
{
  $rank = '0'.$faction;
  if($honor > 0)
  {
     if($honor < 2000)
       $rank = 1;
     else
       $rank = ceil($honor / 5000) + 1;
  }
  if ($rank>14)
    $rank = 14;
  return $rank;
};


//#############################################################################
//get skill type by its id
function get_skill_type($id)
{
  global $mmfpm_db;
  $sql = new SQL;
  $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $skill_type = $sql->fetch_row($sql->query("SELECT `Category` FROM `dbc_skillline` WHERE `id`={$id} LIMIT 1")); //This table came from CSWOWD as its fields are named
  $sql->close();
  unset($sql);
  return $skill_type[0];
}


//#############################################################################
//get skill name by its id
function get_skill_name($id)
{
  global $mmfpm_db;
  $sql = new SQL;
  $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $skill_name = $sql->fetch_row($sql->query("SELECT `Name` FROM `dbc_skillline` WHERE `id`={$id} LIMIT 1")); //This table came from CSWOWD as its fields are named
  $sql->close();
  unset($sql);
  return $skill_name[0];
}


//#############################################################################
//get spell name by its id
function get_spell_name($id)
{
  global $mmfpm_db;
  $sql = new SQL;
  $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $spell_name = $sql->fetch_row($sql->query("SELECT `spellname_loc0` FROM `dbc_spell` WHERE `spellID`={$id} LIMIT 1"));
  return $spell_name[0];
}


//#############################################################################
//get spell name by its id
function get_spell_rank($id)
{
  global $mmfpm_db;
  $sql = new SQL;
  $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $spell_rank = $sql->fetch_row($sql->query("SELECT `rank_loc0` FROM `dbc_spell` WHERE `spellID`={$id} LIMIT 1"));
  return $spell_rank[0];
}


//#############################################################################
//get achievement name by its id
function get_achievement_name($id)
{
  global $mmfpm_db;
  $sql = new SQL;
  $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $achievement_name = $sql->fetch_row($sql->query("SELECT `name01` FROM `dbc_achievement` WHERE `id`={$id} LIMIT 1"));
  $sql->close();
  unset($sql);
  return $achievement_name[0];
}


//#############################################################################
//get avatar image dir by char level, gender. race and class
function get_image_dir($level,$sex,$race,$class,$gm=0)
{
  $return = "";
  if ($gm>0 && file_exists("img/avatars/bliz/$gm.gif"))
    $return .= "img/avatars/bliz/$gm.gif";
  else
  {
    if ($gm>0 && file_exists("img/avatars/bliz/$gm.gif"))
      $return .= "img/avatars/bliz/$gm.gif";
    else
    {
      if ($gm>0 && file_exists("img/avatars/bliz/$gm.jpg"))
        $return .= "img/avatars/bliz/$gm.jpg";
      else
      {
        if($level >= 60)
        {
          if($level >= 70)
            $return .= "img/avatars/70/$sex-$race-$class.gif";
          else
            $return .= "img/avatars/60/$sex-$race-$class.gif";
        }
        else
          $return .= "img/avatars/np/$sex-$race-$class.gif";
      }
    }
  }
  return $return;
};

?>
