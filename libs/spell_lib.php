<?php


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
//get spell icon - if icon not exists in item_icons folder D/L it from web.

function get_spell_icon($auraid, &$sqlm=0)
{
  global $proxy_cfg, $get_icons_from_web, $mmfpm_db, $item_icons;

  // not all functions that call this function will pass reference to existing SQL links
  // so we need to check and overload when needed
  if(empty($sqlm))
  {
    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  }

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


?>
