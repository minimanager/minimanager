<?php


//#############################################################################
//get spell name by its id

function spell_get_name($id, &$sqlm)
{
  $spell_name = $sqlm->fetch_assoc($sqlm->query('SELECT sield_142 FROM dbc_spell WHERE id='.$id.' LIMIT 1'));
  return $spell_name['spellname_loc0'];
}


//#############################################################################
//get spell icon - if icon not exists in item_icons folder D/L it from web.

function spell_get_icon($auraid, &$sqlm)
{
  global $proxy_cfg, $get_icons_from_web, $item_icons;

  $result = $sqlm->query('SELECT field_139 FROM dbc_spell WHERE id = '.$auraid.' LIMIT 1');

  if ($result)
    $displayid = $sqlm->result($result, 0);
  else
    $displayid = 0;

  if ($displayid)
  {
    $result = $sqlm->query('SELECT field_1 FROM dbc_spellicon WHERE id = '.$displayid.' LIMIT 1');

    if($result)
    {
      $aura = $sqlm->result($result, 0);

      if ($aura)
      {
        if (file_exists(''.$item_icons.'/'.$aura.'.jpg'))
        {
          if (filesize(''.$item_icons.'/'.$aura.'.jpg') > 349)
          {
            return ''.$item_icons.'/'.$aura.'.jpg';
          }
          else
          {
            $sqlm->query('DELETE FROM dbc_spellicon WHERE id = '.$displayid.'');
            if (file_exists(''.$item_icons.'/'.$aura.'.jpg'))
              unlink(''.$item_icons.'/'.$aura.'.jpg');
            $aura = '';
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
  }
  else
    $aura = '';

  if($get_icons_from_web)
  {
    $xmlfilepath='http://www.wowhead.com/?spell=';
    $proxy = $proxy_cfg['addr'];
    $port = $proxy_cfg['port'];

    if (empty($proxy_cfg['addr']))
    {
      $proxy = 'www.wowhead.com';
      $xmlfilepath = '?spell=';
      $port = 80;
    }

    if ($aura == '')
    {
      //get the icon name
      $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.5);
      if ($fp);
      else
        return 'img/INV/INV_blank_32.gif';
      $out = "GET /$xmlfilepath$auraid HTTP/1.0\r\nHost: www.wowhead.com\r\n";
      if (isset($proxy_cfg['user']))
        $out .= "Proxy-Authorization: Basic ". base64_encode ("{$proxy_cfg['user']}:{$proxy_cfg['pass']}")."\r\n";
      $out .="Connection: Close\r\n\r\n";

      $temp = '';
      fwrite($fp, $out);
      while ($fp && !feof($fp))
        $temp .= fgets($fp, 4096);
      fclose($fp);

      $wowhead_string = $temp;
      $temp_string1 = strstr($wowhead_string, 'Icon.create(');
      $temp_string2 = substr($temp_string1, 12, 50);
      $temp_string3 = strtok($temp_string2, ',');
      $temp_string4 = substr($temp_string3, 1, strlen($temp_string3) - 2);
      $aura_icon_name = $temp_string4;

      $aura = $aura_icon_name;
    }

    if (file_exists(''.$item_icons.'/'.$aura.'.jpg'))
    {
      if (filesize(''.$item_icons.'/'.$aura.'.jpg') > 349)
      {
        $sqlm->query('REPLACE INTO dbc_spellicon (id, field_1) VALUES (\''.$displayid.'\', \''.$aura.'\')');
        return ''.$item_icons.'/'.$aura.'.jpg';
      }
      else
      {
        $sqlm->query('DELETE FROM dbc_spellicon WHERE id = '.$displayid.'');
        if (file_exists(''.$item_icons.'/'.$aura.'.jpg'))
          unlink(''.$item_icons.'/'.$aura.'.jpg');
      }
    }

    //get the icon itself
    if (empty($proxy_cfg['addr']))
    {
      $proxy = 'static.wowhead.com';
      $port = 80;
    }
    $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.5);
    if ($fp);
    else
      return 'img/INV/INV_blank_32.gif';
    $iconfilename = strtolower($aura);
    $file = 'http://static.wowhead.com/images/icons/medium/'.$iconfilename.'.jpg';
    $out = "GET $file HTTP/1.0\r\nHost: static.wowhead.com\r\n";
    if (isset($proxy_cfg['user']))
      $out .= "Proxy-Authorization: Basic ". base64_encode ("{$proxy_cfg['user']}:{$proxy_cfg['pass']}")."\r\n";
    $out .="Connection: Close\r\n\r\n";
    fwrite($fp, $out);

    //remove header
    while ($fp && !feof($fp))
    {
      $headerbuffer = fgets($fp, 4096);
      if (urlencode($headerbuffer) == '%0D%0A')
        break;
    }

    if (file_exists(''.$item_icons.'/'.$aura.'.jpg'))
    {
      if (filesize(''.$item_icons.'/'.$aura.'.jpg') > 349)
      {
        $sqlm->query('REPLACE INTO dbc_spellicon (id, field_1) VALUES (\''.$displayid.'\', \''.$aura.'\')');
        return ''.$item_icons.'/'.$aura.'.jpg';
      }
      else
      {
        $sqlm->query('DELETE FROM dbc_spellicon WHERE id = '.$displayid.'');
        if (file_exists(''.$item_icons.'/'.$aura.'.jpg'))
          unlink(''.$item_icons.'/'.$aura.'.jpg');
      }
    }

    $img_file = fopen(''.$item_icons.'/'.$aura.'.jpg', 'wb');
    while (!feof($fp))
      fwrite($img_file,fgets($fp, 4096));
    fclose($fp);
    fclose($img_file);

    if (file_exists(''.$item_icons.'/'.$aura.'.jpg'))
    {
      if (filesize(''.$item_icons.'/'.$aura.'.jpg') > 349)
      {
        $sqlm->query('REPLACE INTO dbc_spellicon (id, name) VALUES (\''.$displayid.'\', \''.$aura.'\')');
        return ''.$item_icons.'/'.$aura.'.jpg';
      }
      else
      {
        $sqlm->query('DELETE FROM dbc_spellicon WHERE id = '.$displayid.'');
        if (file_exists(''.$item_icons.'/'.$aura.'.jpg'))
          unlink(''.$item_icons.'/'.$aura.'.jpg');
      }
    }
    else
      return 'img/INV/INV_blank_32.gif';
  }
  else
    return 'img/INV/INV_blank_32.gif';
}


?>
