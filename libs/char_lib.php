<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * License: GNU General Public License v2(GPL)
 */


//#############################################################################
//get character side by id

function get_char_side()
{
  global $lang_id_tab;
  $side =
    array
    (
      0 => $lang_id_tab['Alliance'],
      1 => $lang_id_tab['Horde']
    );
  return $side;
}


//#############################################################################
//get character race by id

function get_char_race()
{
  global $lang_id_tab;
  $race =
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
   return $race;
}


//#############################################################################
//get player class by it id

function get_char_class($id)
{
  global $lang_id_tab;
  $class =
    array
    (
       1  => array($lang_id_tab['warrior'],"",""),
       2  => array($lang_id_tab['paladin'],"",""),
       3  => array($lang_id_tab['hunter'],"",""),
       4  => array($lang_id_tab['rogue'],"",""),
       5  => array($lang_id_tab['priest'],"",""),
       6  => array($lang_id_tab['death_knight'],"",""),
       7  => array($lang_id_tab['shaman'],"",""),
       8  => array($lang_id_tab['mage'],"",""),
       9  => array($lang_id_tab['warlock'],"",""),
       11 => array($lang_id_tab['druid'],"","")
    );
    return $class;
}


//#############################################################################
//get player honor rank by it id

function get_char_pvp_rank()
{
  global $lang_id_tab;
  $pvp_rank =
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
  return $pvp_rank;
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


//#############################################################################
//set color per Level range

function get_level_with_color($lvl)
{
  if($lvl < 10)
    $level = '<font color="#FFFFFF">'.$lvl.'</font>';
  else if($lvl < 20)
    $level = '<font color="#858585">'.$lvl.'</font>';
  else if($lvl < 30)
    $level = '<font color="#339900">'.$lvl.'</font>';
  else if($lvl < 40)
    $level = '<font color="#3300CC">'.$lvl.'</font>';
  else if($lvl < 50)
    $level = '<font color="#C552FF">'.$lvl.'</font>';
  else if($lvl < 60)
    $level = '<font color="#FF8000">'.$lvl.'</font>';
  else if($lvl < 70)
    $level = '<font color="#FFF280">'.$lvl.'</font>';
  else if($lvl < 80)
    $level = '<font color="#FF0000">'.$lvl.'</font>';
  else
    $level = '<font color="#000000">'.$lvl.'</font>';

  return $level;
}


?>
