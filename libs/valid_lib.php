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
//making sure the input string contains only [A-Z][a-z][0-9]-_ chars.
function valid_alphabetic($srting)
{
  if(ereg('[^a-zA-Z0-9_-]{1,}', $srting))
    return false;
  else
    return true;
}


//#############################################################################
//testing given mail
function valid_email($email='')
{
  global $validate_mail_host;
  // checks proper syntax
  if( !preg_match( "/^([a-zA-Z0-9])+([a-zA-Z0-9._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9._-]+)+$/", $email))
    return false;
  elseif ($validate_mail_host)
  {
    // gets domain name
    list($username,$domain)=split('@',$email);
    // checks for if MX records in the DNS
    $mxhosts = array();
    if(!getmxrr($domain, $mxhosts))
    {
      // no mx records, ok to check domain
      if (!fsockopen($domain,25,$errno,$errstr,7))
        return false;
      else
        return true;
    }
    else
    {
      // mx records found
      foreach ($mxhosts as $host)
      {
        if (fsockopen($host,25,$errno,$errstr,7)) return true;
      }
      return false;
    }
  }
  else
    return true;
}


//php under win does not support getmxrr()  function - so heres workaround
if (!function_exists ('getmxrr') )
{
  function getmxrr($hostname, &$mxhosts)
  {
    $mxhosts = array();
    exec('%SYSTEMDIRECTORY%\\nslookup.exe -q=mx '.escapeshellarg($hostname), $result_arr);
    foreach($result_arr as $line)
    {
      if (preg_match("/.*mail exchanger = (.*)/", $line, $matches))
        $mxhosts[] = $matches[1];
    }
    return( count($mxhosts) > 0 );
  }
}


?>
