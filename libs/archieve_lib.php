<?php


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
//get achievements by category id

function achieve_get_id_category($id, &$sqlm)
{
  $achieve_cat = array();
  $result = ($sqlm->query('SELECT id, name01, description01, rewarddesc01, rewpoints FROM dbc_achievement WHERE categoryid = \''.$id.'\' ORDER BY `order` DESC'));
  while ($achieve_cat[] = $sqlm->fetch_assoc($result));
  return $achieve_cat;
}


//#############################################################################
//get achievement main category

function achieve_get_main_category(&$sqlm)
{
  $main_cat = array();
  $result = $sqlm->query('SELECT id, name01 FROM dbc_achievement_category WHERE parentid = -1 ORDER BY `order` ASC');
  while ($main_cat[] = $sqlm->fetch_assoc($result));
  return $main_cat;
}


//#############################################################################
//get achievement sub category

function achieve_get_sub_category(&$sqlm)
{
  $sub_cat = array();
  $result = $sqlm->query('SELECT id, parentid, name01 FROM dbc_achievement_category WHERE parentid != -1 ORDER BY `order` ASC');
  $temp = $sqlm->fetch_assoc($result);
  while ($sub_cat[$temp['parentid']][$temp['id']] = $temp['name01'])
  {
    $temp = $sqlm->fetch_assoc($result);
  }
  return $sub_cat;
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


//#############################################################################
//get achievement icon - if icon not exists in item_icons folder D/L it from web.

function achieve_get_icon($achieveid, &$sqlm)
{
  global $proxy_cfg, $get_icons_from_web, $item_icons;

  $result = $sqlm->query('SELECT field_42 FROM dbc_achievement WHERE id = '.$achieveid.' LIMIT 1');

  if ($result)
    $displayid = $sqlm->result($result, 0);
  else
    $displayid = 0;

  if ($displayid)
  {
    $result = $sqlm->query('SELECT name FROM dbc_spellicon WHERE id = '.$displayid.' LIMIT 1');

    if($result)
    {
      $achieve = $sqlm->result($result, 0);

      if ($achieve)
      {
        if(file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
        {
          return ''.$item_icons.'/'.$achieve.'.jpg';
        }
        else
          $achieve = '';
      }
      else
        $achieve = '';
    }
    else
      $achieve = '';
  }

  if($get_icons_from_web)
  {
    $xmlfilepath='http://www.wowhead.com/?achievement=';
    $proxy = $proxy_cfg['addr'];
    $port = $proxy_cfg['port'];

    if (empty($proxy_cfg['addr']))
    {
      $proxy = 'www.wowhead.com';
      $xmlfilepath = '?achievement=';
      $port = 80;
    }

    if ($achieve == '')
    {
      //get the icon name
      $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.4);
      if ($fp);
      else
        return 'img/INV/INV_blank_32.gif';
      $out = "GET /$xmlfilepath$achieveid HTTP/1.0\r\nHost: www.wowhead.com\r\n";
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
      $achieve_icon_name = $temp_string4;

      $achieve = $achieve_icon_name;
    }

    if (file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
    {
      $sqlm->query('REPLACE INTO dbc_spellicon (id, name) VALUES (\''.$displayid.'\', \''.$achieve.'\')');
      return ''.$item_icons.'/'.$achieve.'.jpg';
    }

    //get the icon itself
    if (empty($proxy_cfg['addr']))
    {
      $proxy = 'static.wowhead.com';
      $port = 80;
    }
    $fp = @fsockopen($proxy, $port, $errno, $errstr, 0.4);
    if ($fp);
    else
      return 'img/INV/INV_blank_32.gif';
    $iconfilename = strtolower($achieve);
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

    if (file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
    {
      $sqlm->query('REPLACE INTO dbc_spellicon (id, name) VALUES (\''.$displayid.'\', \''.$achieve.'\')');
      return ''.$item_icons.'/'.$achieve.'.jpg';
    }

    $img_file = fopen(''.$item_icons.'/'.$achieve.'.jpg', 'wb');
    while (!feof($fp))
      fwrite($img_file,fgets($fp, 4096));
    fclose($fp);
    fclose($img_file);

    if (file_exists(''.$item_icons.'/'.$achieve.'.jpg'))
    {
      $sqlm->query('REPLACE INTO dbc_spellicon (id, name) VALUES (\''.$displayid.'\', \''.$achieve.'\')');
      return ''.$item_icons.'/'.$achieve.'.jpg';
    }
    else
      return 'img/INV/INV_blank_32.gif';
  }
  else
    return 'img/INV/INV_blank_32.gif';
}


?>
