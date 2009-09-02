<?php


  require_once 'header.php';
  require_once 'libs/telnet_lib.php';
  valid_login($action_permission['read']);

  $telnet = new telnet_lib();
  $result = $telnet->Connect($server[$realm_id]['addr'], $server[$realm_id]['telnet_port'], $server[$realm_id]['telnet_user'], $server[$realm_id]['telnet_pass']);
  if (0 == $result)
  {
    $telnet->DoCommand('server info', $result);
    $result = str_replace("mangos>","",$result);
    $result = str_replace("\r\n", "\r\n  ", $result);
    $telnet->Disconnect();
  }
  unset($telnet);

  $doutput = '
';
  $show_version['svnrev'] = '';
  if ( is_readable('.svn/entries') )
  {
    $file_obj = new SplFileObject('.svn/entries');
    $file_obj->seek(3);
    $show_version['svnrev'] = $file_obj->current();
    unset($file_obj);
    $doutput .= '
  MiniManager : '.$show_version['version'].' r'.$show_version['svnrev'];
  }
  $doutput .= '
  Client      : '.$_SERVER['HTTP_USER_AGENT'].'

  OS          : '.php_uname('s').' '.php_uname('r').' '.php_uname('v').' '.php_uname('m').'
  http        : '.$_SERVER['SERVER_SOFTWARE'].'
  PHP         : '.phpversion().' '.php_sapi_name().'
  MySQL       : '.mysql_get_server_info();

  if ($result)
  {
    $doutput .='

  '.$result;
  }
  $l_rev = @file_get_contents('http://mmfpm.svn.sourceforge.net/svnroot/mmfpm/trunk/', NULL, NULL, 36, 3);
  $output .= '
          <center>';
  if ($l_rev)
  {
    if ( is_readable('.svn/entries') )
    {
      $output .='
            This revision of miniManager is r'.$show_version['svnrev'].'
            <br />
            Latest revision of miniManager is r'.$l_rev.'
            <br />';
      if ($l_rev > $show_version['svnrev'])
        $output .='
            Please update to latest revision before posting any bug reports.
            <br /><br />';
      else
        $output .='
            You are using the latest revision.
            <br /><br />';
    }
    else
    {
      $output .='
            Latest revision of miniManager is r'.$l_rev.'
            <br />
            Please update to latest revision before posting any bug reports.
            <br /><br />';
    }
  }
  unset($l_rev);
  $output .= '
            Copy the selected text below and paste it in your bug report.
            <br /><br />
            <textarea id="codearea" readonly="readonly" rows="'.($result ? '22' : '12').'" cols="80">'.$doutput.'</textarea>
            <br /><br />
            <a href="http://mangos.osh.nu/forums/index.php?showforum=38" target="_blank">miniManager Bug Report Forum: http://mangos.osh.nu/forums/index.php?showforum=38<br />
            (link opens in new tab/window)</a>
            <br /><br />
            <script type="text/javascript">
              document.getElementById(\'codearea\').focus();
              document.getElementById(\'codearea\').select();
            </script>
          </center>';

  require_once 'footer.php';


?>
