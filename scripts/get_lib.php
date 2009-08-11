<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */


//#############################################################################
//get name from realmlist.name

function get_realm_name($realm_id)
{
  global $realm_db;

  $sqlr = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

  $result = $sqlr->query("SELECT name FROM `realmlist` WHERE id = '$realm_id'");
  $realm_name = $sqlr->result($result, 0);

  return $realm_name;
}


//#############################################################################
//generate item border from item_template.entry

function get_item_border($item_id)
{
  global $world_db, $realm_id;

  if($item_id)
  {
    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $result_2 = $sqlw->query("SELECT Quality FROM item_template WHERE entry = '$item_id'");
    $iborder = ($sqlw->num_rows($result_2) == 1) ? $sqlw->result($result_2, 0,"Quality"): "Quality: $iborder Not Found" ;

    return "icon_border_$iborder";
  }
  else
    return "icon_border_0";
}


//#############################################################################
//get item name from item_template.entry

function get_item_name($item_id)
{
  global $world_db, $realm_id;

  if($item_id)
  {
    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $deplang = get_lang_id();
    $result = $sqlw->query("SELECT IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",`name`) as name FROM item_template LEFT JOIN locales_item ON item_template.entry = locales_item.entry WHERE item_template.entry = '$item_id'");
    $item_name = ($sqlw->num_rows($result) == 1) ? $sqlw->result($result, 0,"name") : "ItemID: $item_id Not Found" ;

    return $item_name;
  }
  else
    return NULL;
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

  global $language;
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


//#############################################################################
//get item icon - if icon not exists in item_icons folder D/L it from web.

function get_item_icon($itemid)
{
  global $mmfpm_db, $proxy_cfg, $get_icons_from_web, $item_icons;

  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $result = $sqlm->query("SELECT `displayid` FROM `dbc_item` WHERE `entry` = $itemid LIMIT 1");

  if ($result)
    $displayid = $sqlm->result($result, 0);
  else
    $displayid = 0;

  if ($displayid)
  {
    $result = $sqlm->query("SELECT `name` FROM `dbc_itemdisplayinfo` WHERE `id`=$displayid LIMIT 1");

    if($result)
    {
      $item = $sqlm->result($result, 0);

      if ($item)
      {
        if(file_exists("$item_icons/$item.jpg"))
        {
          return "$item_icons/$item.jpg";
        }
      }
      else
        $item = '';
    }
    else
      $item = '';
  }
  else
    $item = '';

  if($get_icons_from_web)
  {
    $xmlfilepath="http://www.wowhead.com/?item=";
    $proxy = $proxy_cfg['addr'];
    $port = $proxy_cfg['port'];

    if (empty($proxy_cfg['addr']))
    {
      $proxy = "www.wowhead.com";
      $xmlfilepath = "?item=";
      $port = 80;
    }

    if ($item == '')
    {
      //get the icon name
      $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.4);
      if (!$fp)
        return "img/INV/INV_blank_32.gif";
      $out = "GET /$xmlfilepath$itemid HTTP/1.0\r\nHost: www.wowhead.com\r\n";
      if (!empty($proxy_cfg['user']))
        $out .= "Proxy-Authorization: Basic ". base64_encode ("{$proxy_cfg['user']}:{$proxy_cfg['pass']}")."\r\n";
      $out .="Connection: Close\r\n\r\n";

      $temp = "";
      fwrite($fp, $out);
      while ($fp && !feof($fp))
        $temp .= fgets($fp, 4096);
      fclose($fp);

      $wowhead_string = $temp;
      $temp_string1 = strstr($wowhead_string, "Icon.create(");
      $temp_string2 = substr($temp_string1, 12, 50);
      $temp_string3 = strtok($temp_string2, ',');
      $temp_string4 = substr($temp_string3, 1, strlen($temp_string3) - 2);
      $icon_name = $temp_string4;

      $item = $icon_name;
    }

    if (file_exists("$item_icons/$item.jpg"))
    {
      $sqlm->query("REPLACE INTO dbc_itemdisplayinfo (id, name) VALUES ('$displayid','$item')");
      return "$item_icons/$item.jpg";
    }

    //get the icon itself
    if (empty($proxy_cfg['addr']))
    {
      $proxy = "static.wowhead.com";
      $port = 80;
    }
    $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.4);
    if (!$fp)
      return "img/INV/INV_blank_32.gif";
    $iconfilename = strtolower($item);
    $file = "http://static.wowhead.com/images/icons/medium/$iconfilename.jpg";
    $out = "GET $file HTTP/1.0\r\nHost: static.wowhead.com\r\n";
    if (!empty($proxy_cfg['user']))
      $out .= "Proxy-Authorization: Basic ". base64_encode ("{$proxy_cfg['user']}:{$proxy_cfg['pass']}")."\r\n";
    $out .="Connection: Close\r\n\r\n";
    fwrite($fp, $out);

    //remove header
    while ($fp && !feof($fp))
    {
      $headerbuffer = fgets($fp, 4096);
      if (urlencode($headerbuffer) == "%0D%0A")
        break;
    }

    if (file_exists("$item_icons/$item.jpg"))
    {
      $sqlm->query("REPLACE INTO dbc_itemdisplayinfo (id, name) VALUES ('$displayid','$item')");
      return "$item_icons/$item.jpg";
    }

    $img_file = fopen("$item_icons/$item.jpg", 'wb');
    while (!feof($fp))
      fwrite($img_file,fgets($fp, 4096));
    fclose($fp);
    fclose($img_file);

    if (file_exists("$item_icons/$item.jpg"))
    {
      $sqlm->query("REPLACE INTO dbc_itemdisplayinfo (id, name) VALUES ('$displayid','$item')");
      return "$item_icons/$item.jpg";
    }
    else
      return "img/INV/INV_blank_32.gif";
  }
  else
    return "img/INV/INV_blank_32.gif";
}


//#############################################################################
//get aura icon - if icon not exists in item_icons folder D/L it from web.

function get_spell_icon($auraid)
{
  global $proxy_cfg, $get_icons_from_web, $mmfpm_db, $item_icons;

  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $result = $sqlm->query("SELECT `spellicon` FROM `dbc_spell` WHERE `spellID`=$auraid LIMIT 1");

  if ($result)
    $displayid = $sqlm->result($result, 0);
  else
    $displayid = 0;

  if ($displayid)
  {
    $result = $sqlm->query("SELECT `name` FROM `dbc_spellicon` WHERE `id` = $displayid LIMIT 1");

    if($result)
    {
      $aura = $sqlm->result($result, 0);

      if ($aura)
      {
        if(file_exists("$item_icons/$aura.jpg"))
        {
          return "$item_icons/$aura.jpg";
        }
      }
      else
        $aura = '';
    }
    else
      $aura = '';
  }
  else
    $aura = '';

  if($get_icons_from_web)
  {
    $xmlfilepath="http://www.wowhead.com/?spell=";
    $proxy = $proxy_cfg['addr'];
    $port = $proxy_cfg['port'];

    if (empty($proxy_cfg['addr']))
    {
      $proxy = "www.wowhead.com";
      $xmlfilepath = "?spell=";
      $port = 80;
    }

    if ($aura == '')
    {
      //get the icon name
      $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.4);
      if (!$fp)
        return "img/INV/INV_blank_32.gif";
      $out = "GET /$xmlfilepath$auraid HTTP/1.0\r\nHost: www.wowhead.com\r\n";
      if (!empty($proxy_cfg['user']))
        $out .= "Proxy-Authorization: Basic ". base64_encode ("{$proxy_cfg['user']}:{$proxy_cfg['pass']}")."\r\n";
      $out .="Connection: Close\r\n\r\n";

      $temp = "";
      fwrite($fp, $out);
      while ($fp && !feof($fp))
        $temp .= fgets($fp, 4096);
      fclose($fp);

      $wowhead_string = $temp;
      $temp_string1 = strstr($wowhead_string, "Icon.create(");
      $temp_string2 = substr($temp_string1, 12, 50);
      $temp_string3 = strtok($temp_string2, ',');
      $temp_string4 = substr($temp_string3, 1, strlen($temp_string3) - 2);
      $aura_icon_name = $temp_string4;

      $aura = $aura_icon_name;
    }

    if (file_exists("$item_icons/$aura.jpg"))
    {
      $sqlm->query("REPLACE INTO dbc_spellicon (id, name) VALUES ('$displayid','$aura')");
      return "$item_icons/$aura.jpg";
    }

    //get the icon itself
    if (empty($proxy_cfg['addr']))
    {
      $proxy = "static.wowhead.com";
      $port = 80;
    }
    $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.4);
    if (!$fp)
      return "img/INV/INV_blank_32.gif";
    $iconfilename = strtolower($aura);
    $file = "http://static.wowhead.com/images/icons/medium/$iconfilename.jpg";
    $out = "GET $file HTTP/1.0\r\nHost: static.wowhead.com\r\n";
    if (!empty($proxy_cfg['user']))
      $out .= "Proxy-Authorization: Basic ". base64_encode ("{$proxy_cfg['user']}:{$proxy_cfg['pass']}")."\r\n";
    $out .="Connection: Close\r\n\r\n";
    fwrite($fp, $out);

    //remove header
    while ($fp && !feof($fp))
    {
      $headerbuffer = fgets($fp, 4096);
      if (urlencode($headerbuffer) == "%0D%0A")
        break;
    }

    if (file_exists("$item_icons/$aura.jpg"))
    {
      $sqlm->query("REPLACE INTO dbc_spellicon (id, name) VALUES ('displayid','$aura')");
      return "$item_icons/$aura.jpg";
    }

    $img_file = fopen("$item_icons/$aura.jpg", 'wb');
    while (!feof($fp))
      fwrite($img_file,fgets($fp, 4096));
    fclose($fp);
    fclose($img_file);

    if (file_exists("$item_icons/$aura.jpg"))
    {
      $sqlm->query("REPLACE INTO dbc_spellicon (id, name) VALUES ('displayid','$aura')");
      return "$item_icons/$aura.jpg";
    }
    else
      return "img/INV/INV_blank_32.gif";
  }
  else
    return "img/INV/INV_blank_32.gif";
}


//#############################################################################
// for calc next level xp
function xp_Diff($lvl)
{
  if( $lvl < 29 )
    return 0;
  if( $lvl == 29 )
    return 1;
  if( $lvl == 30 )
    return 3;
  if( $lvl == 31 )
    return 6;
  else
    return (5*($lvl-30));
}

function mxp($lvl)
{
  if ($lvl < 60)
  {
    return (45 + (5*$lvl));
  }
  else
  {
     return (235 + (5*$lvl));
  }
}

function get_xp_to_level($lvl)
{
  $RATE_XP_PAST_70 = 1;
  $xp = 0;
  if ($lvl < 60)
  {
    $xp = (8*$lvl + xp_Diff($lvl)) * mxp($lvl);
  }
  elseif ($lvl == 60)
  {
    $xp = (155 + mxp($lvl) * (1344 - 70 - ((69 - $lvl) * (7 + (69 - $lvl) * 8 - 1)/2)));
  }
  elseif ($lvl < 70)
  {
    $xp = (155 + mxp($lvl) * (1344 - ((69-$lvl) * (7 + (69 - $lvl) * 8 - 1)/2)));
  }
  else
  {
    // level higher than 70 is not supported
    $xp = (779700 * (pow($RATE_XP_PAST_70, $lvl - 69)));
    return (($xp < 0x7fffffff) ? $xp : 0x7fffffff);
  }
  // The $xp to Level is always rounded to the nearest 100 points (50 rounded to high).
  $xp = (($xp + 50) / 100) * 100;                  // use additional () for prevent free association operations in C++

  if (($lvl > 10) && ($lvl < 60))                  // compute discount added in 2.3.x
  {
    $discount = ($lvl < 28) ? ($lvl - 10) : 18;
    $xp = ($xp * (100 - $discount)) / 100;         // apply discount
    $xp = ($xp / 100) * 100;                       // floor to hundreds
  }

  return $xp;
}


//#############################################################################
//generate item tooltip from item_template.entry

function get_item_tooltip($item_id)
{
  global $lang_item, $lang_id_tab, $world_db, $realm_id, $language;
  if($item_id)
  {
    $sqlw = new SQL;
    $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

    $deplang = get_lang_id();
    $result_1 = $sqlw->query("SELECT stat_type1,stat_value1,stat_type2,
      stat_value2,stat_type3,stat_value3,stat_type4,stat_value4,stat_type5,
      stat_value5,stat_type6,stat_value6,stat_type7,stat_value7,stat_type8,
      stat_value8,stat_type9,stat_value9,stat_type10,stat_value10,armor,
      holy_res,fire_res,nature_res,frost_res,arcane_res,shadow_res,spellid_1,
      spellid_2,spellid_3,spellid_4,spellid_5,
      IFNULL(".($deplang<>0?"name_loc$deplang":"NULL").",name),class,subclass,
      Quality,RequiredLevel,dmg_min1,dmg_max1,dmg_type1,dmg_min2,dmg_max2,
      dmg_type2,delay,bonding,description,itemset,item_template.entry,
      InventoryType,ItemLevel,displayid,maxcount,spelltrigger_1,spelltrigger_2,
      spelltrigger_3,spelltrigger_4,spelltrigger_5,ContainerSlots,
      spellcharges_1,spellcharges_2,spellcharges_3,spellcharges_4,
      spellcharges_5,AllowableClass,socketColor_1,socketColor_2,socketColor_3,
      RandomProperty,RandomSuffix
      FROM item_template LEFT JOIN locales_item ON item_template.entry = locales_item.entry
      WHERE item_template.entry = '$item_id' LIMIT 1");
    if ($item = $sqlw->fetch_row($result_1))
    {
      $tooltip = "";

      $itemname = htmlspecialchars($item[32]);
      switch ($item[35])
      {
        case 0: //Grey Poor
          $tooltip .= "<font color='#b2c2b9' class='large'>$itemname</font><br />";
          break;
        case 1: //White Common
          $tooltip .= "<font color='white' class='large'>$itemname</font><br />";
          break;
        case 2: //Green Uncommon
          $tooltip .= "<font color='#1eff00' class='large'>$itemname</font><br />";
          break;
        case 3: //Blue Rare
          $tooltip .= "<font color='#0070dd' class='large'>$itemname</font><br />";
          break;
        case 4: //Purple Epic
          $tooltip .= "<font color='#a335ee' class='large'>$itemname</font><br />";
          break;
        case 5: //Orange Legendary
          $tooltip .= "<font color='orange' class='large'>$itemname</font><br />";
          break;
        case 6: //Red Artifact
          $tooltip .= "<font color='red' class='large'>$itemname</font><br />";
          break;
        default:
      }

      $tooltip .= "<font color='white'>";

      switch ($item[53])
      {
        case 1: //Binds when Picked Up
          $tooltip .= "{$lang_item['bop']}<br />";
          break;
        case 2: //Binds when Equipped
          $tooltip .= "{$lang_item['boe']}<br />";
          break;
        case 3: //Binds when Used
          $tooltip .= "{$lang_item['bou']}<br />";
          break;
        case 4: //Quest Item
          $tooltip .= "{$lang_item['quest_item']}<br />";
          break;
        default:
      }

      if ($item[60]) $tooltip .= "{$lang_item['unique']}<br />";

      $tooltip .= "<br />";

      switch ($item[57])
      {
        case 1:
          $tooltip .= "{$lang_item['head']} - ";
          break;
        case 2:
          $tooltip .= "{$lang_item['neck']} - ";
          break;
        case 3:
          $tooltip .= "{$lang_item['shoulder']} - ";
          break;
        case 4:
          $tooltip .= "{$lang_item['shirt']} - ";
          break;
        case 5:
          $tooltip .= "{$lang_item['chest']} - ";
          break;
        case 6:
          $tooltip .= "{$lang_item['belt']} - ";
          break;
        case 7:
          $tooltip .= "{$lang_item['legs']} - ";
          break;
        case 8:
          $tooltip .= "{$lang_item['feet']} - ";
          break;
        case 9:
          $tooltip .= "{$lang_item['wrist']} - ";
          break;
        case 10:
          $tooltip .= "{$lang_item['gloves']} - ";
          break;
        case 11:
          $tooltip .= "{$lang_item['finger']} - ";
          break;
        case 12:
          $tooltip .= "{$lang_item['trinket']} - ";
          break;
        case 13:
          $tooltip .= "{$lang_item['one_hand']} - ";
          break;
        case 14:
          $tooltip .= "{$lang_item['off_hand']} - ";
          break;
        case 16:
          $tooltip .= "{$lang_item['back']} - ";
          break;
        case 18:
          $tooltip .= "{$lang_item['bag']}";
          break;
        case 19:
          $tooltip .= "{$lang_item['tabard']} - ";
          break;
        case 20:
          $tooltip .= "{$lang_item['robe']} - ";
          break;
        case 21:
          $tooltip .= "{$lang_item['main_hand']} - ";
          break;
        case 23:
          $tooltip .= "{$lang_item['tome']} - ";
          break;
        default:
      }
      switch ($item[33])
      {
        case 0: //Consumable
          $tooltip .= "{$lang_item['consumable']}<br />";
          break;
        case 2: //Weapon
          switch ($item[34])
          {
            case 0:
              $tooltip .= "{$lang_item['axe_1h']}<br />";
              break;
            case 1:
              $tooltip .= "{$lang_item['axe_2h']}<br />";
              break;
            case 2:
              $tooltip .= "{$lang_item['bow']}<br />";
              break;
            case 3:
              $tooltip .= "{$lang_item['rifle']}<br />";
              break;
            case 4:
              $tooltip .= "{$lang_item['mace_1h']}<br />";
              break;
            case 5:
              $tooltip .= "{$lang_item['mace_2h']}<br />";
              break;
            case 6:
              $tooltip .= "{$lang_item['polearm']}<br />";
              break;
            case 7:
              $tooltip .= "{$lang_item['sword_1h']}<br />";
              break;
            case 8:
              $tooltip .= "{$lang_item['sword_2h']}<br />";
              break;
            case 10:
              $tooltip .= "{$lang_item['staff']}<br />";
              break;
            case 11:
              $tooltip .= "{$lang_item['exotic_1h']}<br />";
              break;
            case 12:
              $tooltip .= "{$lang_item['exotic_2h']}<br />";
              break;
            case 13:
              $tooltip .= "{$lang_item['fist_weapon']}<br />";
              break;
            case 14:
              $tooltip .= "{$lang_item['misc_weapon']}<br />";
              break;
            case 15:
              $tooltip .= "{$lang_item['dagger']}<br />";
              break;
            case 16:
              $tooltip .= "{$lang_item['thrown']}<br />";
              break;
            case 17:
              $tooltip .= "{$lang_item['spear']}<br />";
              break;
            case 18:
              $tooltip .= "{$lang_item['crossbow']}<br />";
              break;
            case 19:
              $tooltip .= "{$lang_item['wand']}<br />";
              break;
            case 20:
              $tooltip .= "{$lang_item['fishing_pole']}<br />";
              break;
            default:
          }
          break;
        case 4: //Armor
          switch ($item[34])
          {
            case 0:
              $tooltip .= "{$lang_item['misc']}<br />";
              break;
            case 1:
              $tooltip .= "{$lang_item['cloth']}<br />";
              break;
            case 2:
              $tooltip .= "{$lang_item['leather']}<br />";
              break;
            case 3:
              $tooltip .= "{$lang_item['mail']}<br />";
              break;
            case 4:
              $tooltip .= "{$lang_item['plate']}<br />";
              break;
            case 6:
              $tooltip .= "{$lang_item['shield']}<br />";
              break;
            default:
          }
          break;
        case 6: //Projectile
          switch ($item[34])
          {
            case 2:
              $tooltip .= "{$lang_item['arrows']}<br />";
              break;
            case 3:
              $tooltip .= "{$lang_item['bullets']}<br />";
              break;
            default:
          }
          break;
        case 7: //Trade Goods
          switch ($item[34])
          {
            case 0:
              $tooltip .= "{$lang_item['trade_goods']}<br />";
              break;
            case 1:
              $tooltip .= "{$lang_item['parts']}<br />";
              break;
            case 2:
              $tooltip .= "{$lang_item['explosives']}<br />";
              break;
            case 3:
              $tooltip .= "{$lang_item['devices']}<br />";
              break;
            default:
          }
          break;
        case 9: //Recipe
          switch ($item[34])
          {
            case 0:
              $tooltip .= "{$lang_item['book']}<br />";
              break;
            case 1:
              $tooltip .= "{$lang_item['LW_pattern']}<br />";
              break;
            case 2:
              $tooltip .= "{$lang_item['tailoring_pattern']}<br />";
              break;
            case 3:
              $tooltip .= "{$lang_item['ENG_Schematic']}<br />";
              break;
            case 4:
              $tooltip .= "{$lang_item['BS_plans']}<br />";
              break;
            case 5:
              $tooltip .= "{$lang_item['cooking_recipe']}<br />";
              break;
            case 6:
              $tooltip .= "{$lang_item['alchemy_recipe']}<br />";
              break;
            case 7:
              $tooltip .= "{$lang_item['FA_manual']}<br />";
              break;
            case 8:
              $tooltip .= "{$lang_item['ench_formula']}<br />";
              break;
            case 9:
              $tooltip .= "{$lang_item['JC_formula']}<br />";
              break;
            default:
          }
          break;
        case 11: //Quiver
          switch ($item[34])
          {
            case 2:
              $tooltip .= " {$lang_item['quiver']}<br />";
              break;
            case 3:
              $tooltip .= " {$lang_item['ammo_pouch']}<br />";
              break;
            default:
          }
          break;
        case 12: //Quest
          if ($item[53] != 4)
            $tooltip .= "{$lang_item['quest_item']}<br />";
          break;
        case 13: //key
          switch ($item[34])
          {
            case 0:
              $tooltip .= "{$lang_item['key']}<br />";
              break;
            case 1:
              $tooltip .= "{$lang_item['lockpick']}<br />";
              break;
            default:
          }
          break;
        default:
      }
      $tooltip .= "$item[20] {$lang_item['armor']}<br />";

      for($f=37;$f<=51;$f+=3)
      {
        $dmg_type = $item[$f+2];
        $min_dmg_value = $item[$f];
        $max_dmg_value = $item[$f+1];

        if ($min_dmg_value && $max_dmg_value)
        {
          switch ($dmg_type)
          {
            case 0: // Physical
              $tooltip .= "$min_dmg_value - $max_dmg_value {$lang_item['damage']}<br />(".($item[52] ? round(((($min_dmg_value+$max_dmg_value)/2)/($item[52]/1000)),2): $min_dmg_value)." DPS)&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$lang_item['speed']} : ".(($item[52])/1000)."<br />";
              break;
            case 1: // Holy
              $tooltip .= "$min_dmg_value - $max_dmg_value {$lang_item['holy_dmg']}<br />";
              break;
            case 2: // Fire
              $tooltip .= "$min_dmg_value - $max_dmg_value {$lang_item['fire_dmg']}<br />";
              break;
            case 3: // Nature
              $tooltip .= "$min_dmg_value - $max_dmg_value {$lang_item['nature_dmg']}<br />";
              break;
            case 4: // Frost
              $tooltip .= "$min_dmg_value - $max_dmg_value {$lang_item['frost_dmg']}<br />";
              break;
            case 5: // Shadow
              $tooltip .= "$min_dmg_value - $max_dmg_value {$lang_item['shadow_dmg']}<br />";
              break;
            case 6: // Arcane
              $tooltip .= "$min_dmg_value - $max_dmg_value {$lang_item['arcane_dmg']}<br />";
              break;
            default:
          }
        }
      }

      //basic status
      for($s=0;$s<=18;$s+=2)
      {
        $stat_value = $item[$s+1];
        if ($item[$s] && $stat_value)
        {
          switch ($item[$s])
          {
            case 1:
              $tooltip .= "+$stat_value {$lang_item['health']}<br />";
              break;
            case 2:
              $tooltip .= "+$stat_value {$lang_item['mana']}<br />";
              break;
            case 3:
              $tooltip .= "+$stat_value {$lang_item['agility']}<br />";
              break;
            case 4:
              $tooltip .= "+$stat_value {$lang_item['strength']}<br />";
              break;
            case 5:
              $tooltip .= "+$stat_value {$lang_item['intellect']}<br />";
              break;
            case 6:
              $tooltip .= "+$stat_value {$lang_item['spirit']}<br />";
              break;
            case 7:
              $tooltip .= "+$stat_value {$lang_item['stamina']}<br />";
              break;
            default:
              $flag_rating = 1;
          }
        }
      }

      if ($item[21]) $tooltip .= "$item[21] {$lang_item['res_holy']}<br />";
      if ($item[25]) $tooltip .= "$item[25] {$lang_item['res_arcane']}<br />";
      if ($item[22]) $tooltip .= "$item[22] {$lang_item['res_fire']}<br />";
      if ($item[23]) $tooltip .= "$item[23] {$lang_item['res_nature']}<br />";
      if ($item[24]) $tooltip .= "$item[24] {$lang_item['res_frost']}<br />";
      if ($item[26]) $tooltip .= "$item[26] {$lang_item['res_shadow']}<br />";

      //sockets
      for($p=72;$p<=74;$p++)
      {
        if($item[$p])
        {
          switch ($item[$p])
          {
            case 1:
              $tooltip .= "<img src='img/socket_meta.gif' alt='' /><font color='gray'> {$lang_item['socket_meta']}</font><br />";
              break;
            case 2:
              $tooltip .= "<img src='img/socket_red.gif' alt='' /><font color='red'> {$lang_item['socket_red']}</font><br />";
              break;
            case 4:
              $tooltip .= "<img src='img/socket_yellow.gif' alt='' /><font color='yellow'> {$lang_item['socket_yellow']}</font><br />";
              break;
            case 8:
              $tooltip .= "<img src='img/socket_blue.gif' alt='' /><font color='blue'> {$lang_item['socket_blue']}</font><br />";
              break;
            default:
          }
        }
      }

      //level requierment
      if($item[36])
        $tooltip .= "{$lang_item['lvl_req']} $item[36]<br />";

      //allowable classes
      if (($item[71])&&($item[71] != -1)&&($item[71] != 1503))
      {
        $tooltip .= "{$lang_item['class']}:";
        if ($item[71] & 1) $tooltip .= " {$lang_id_tab['warrior']} ";
        if ($item[71] & 2) $tooltip .= " {$lang_id_tab['paladin']} ";
        if ($item[71] & 4) $tooltip .= " {$lang_id_tab['hunter']} ";
        if ($item[71] & 8) $tooltip .= " {$lang_id_tab['rogue']} ";
        if ($item[71] & 16) $tooltip .= " {$lang_id_tab['priest']} ";
        if ($item[71] & 64) $tooltip .= " {$lang_id_tab['shaman']} ";
        if ($item[71] & 128) $tooltip .= " {$lang_id_tab['mage']} ";
        if ($item[71] & 256) $tooltip .= " {$lang_id_tab['warlock']} ";
        if ($item[71] & 1024) $tooltip .= " {$lang_id_tab['druid']} ";
        $tooltip .= "<br />";
      }

      //number of bag slots
      if ($item[66])
        $tooltip .= " $item[66] {$lang_item['slots']}<br />";

      $tooltip .= "</font><br /><font color='#1eff00'>";
      //random enchantments
      if ($item[75] || $item[76])
        $tooltip .= "&lt; Random enchantment &gt;<br />";

      //Ratings additions.
      if (isset($flag_rating))
      {
        for($s=0;$s<=18;$s+=2)
        {
          $stat_type = $item[$s];
          $stat_value = $item[$s+1];
          if ($stat_type && $stat_value)
          {
            switch ($stat_type)
            {
              case 12:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['DEFENCE_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 13:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['DODGE_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 14:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['PARRY_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 15:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['SHIELD_BLOCK_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 16:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['MELEE_HIT_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 17:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['RANGED_HIT_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 18:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['SPELL_HIT_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 19:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['MELEE_CS_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 20:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['RANGED_CS_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 21:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['SPELL_CS_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 22:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['MELEE_HA_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 23:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['RANGED_HA_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 24:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['SPELL_HA_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 25:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['MELEE_CA_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 26:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['RANGED_CA_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 27:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['SPELL_CA_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 28:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['MELEE_HASTE_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 29:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['RANGED_HASTE_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 30:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['SPELL_HASTE_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 31:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['HIT_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 32:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['CS_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 33:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['HA_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 34:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['CA_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 35:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['RESILIENCE_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              case 36:
                $tooltip .= "{$lang_item['spell_equip']}: {$lang_item['improves']} {$lang_item['HASTE_RATING']} {$lang_item['rating_by']} $stat_value.<br />";
                break;
              default:
            }
          }
        }
      }
      //add equip spellid to status
      for($s1=27;$s1<=31;$s1++)
      {
        if ($item[$s1])
        {
          switch ($item[$s1+34])
          {
            case 0:
              $tooltip .= "{$lang_item['spell_use']}: ";
              break;
            case 1:
              $tooltip .= "{$lang_item['spell_equip']}: ";
              break;
            case 2:
              $tooltip .= "{$lang_item['spell_coh']}: ";
              break;
            default:
          }
          $tooltip .= " $item[$s1]<br />";
          if ($item[$s1])
          {
            if ($item[$s1+40])
              $tooltip.= abs($item[$s1+40])." {$lang_item['charges']}.<br />";
          }
        }
      }

      $tooltip .= "</font>";

      if ($item[55])
      {
        include_once("id_tab.php");
        $tooltip .= "<br /><font color='orange'>{$lang_item['item_set']} : ".get_itemset_name($item[55])." ($item[55])</font>";
      }
      if ($item[54])
        $tooltip .= "<br /><font color='orange'>''".str_replace("\"", " '", $item[54])."'</font>";

    }
    else
      $tooltip = "Item ID: $item_id Not Found" ;

    return $tooltip;
  }
  else
    return(NULL);
}


?>
