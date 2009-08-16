<?php


// level 4 debug prints all global arrays, but can't print content of classes
//  so we would have to close these, or we can't have debug output
  if( 3 < $debug)
  {
    unset($sql);
    unset($sqlr);
    unset($sqlc);
    unset($sqlm);
    unset($sqlw);
  }

  // we start with a lead of 10 spaces,
  //  because last line of header is an opening tag with 8 spaces
  //  so if the file before this follows the indent, we will be at the same place it starts
  //  keep html indent in sync, so debuging from browser source would be easy to read
  $output .= '
          <!-- start of footer.php -->
        </div>
        <div id="body_buttom">
          <center>';
  // show login and register button at bottom of every page if guest mode is activated
  if($developer_test_mode && $allow_anony && empty($_SESSION['logged_in']))
  {
    $lang_login = lang_login();
    $output .= '
            <table>
              <tr>
                <td>
                  <a class="button" style="width:130px;" href="register.php">Register</a>
                  <a class="button" style="width:130px;" href="login.php">Login</a>
                </td>
              </tr>
            </table>';
  }
  $output .= '
            <table class="table_buttom">
              <tr>
                <td class="table_buttom_left"></td>
                <td class="table_buttom_middle">';
  $lang_footer = lang_footer();
  $output .=
                  $lang_footer['bugs_to_admin'].'<a href="mailto:$admin_mail"> '.$lang_footer['site_admin'].'</a><br />';
  unset($lang_footer);
  $output .= sprintf('
                  Execute time: %.5f', (microtime(true) - $time_start));

  // if any debug mode is activated, show memory usage
  if($debug)
  {
    $output .= '
                  Queries: '.$tot_queries.' on '.$_SERVER['SERVER_SOFTWARE'];
    if (function_exists('memory_get_usage'))
      $output .= sprintf('
                  <br />Mem. Usage: %.0f/%.0fK Peek: %.0f/%.0fK Global: %.0fK Limit: %s',memory_get_usage()/1024, memory_get_usage(true)/1024,memory_get_peak_usage()/1024,memory_get_peak_usage(true)/1024,sizeof($GLOBALS),ini_get('memory_limit'));
  }

  // links at footer
  $output .= '
                  <p>';
  if ($server_type)
    $output .= '
                    <a href="http://www.trinitycore.org/" target="_blank"><img src="img/logo-trinity.png" class="logo_border" alt="trinity" /></a>';
  else
    $output .= '
                    <a href="http://getmangos.com/" target="_blank"><img src="img/logo-mangos.png" class="logo_border" alt="mangos" /></a>';
  $output .= '
                    <a href="http://www.php.net/" target="_blank"><img src="img/logo-php.png" class="logo_border" alt="php" /></a>
                    <a href="http://www.mysql.com/" target="_blank"><img src="img/logo-mysql.png" class="logo_border" alt="mysql" /></a>
                    <a href="http://validator.w3.org/check?uri=referer" target="_blank"><img src="img/logo-css.png" class="logo_border" alt="w3" /></a>
                    <a href="http://www.spreadfirefox.com" target="_blank"><img src="img/logo-firefox.png" class="logo_border" alt="firefox" /></a>
                    <a href="http://www.opera.com/" target="_blank"><img src="img/logo-opera.png" class="logo_border" alt="opera" /></a>
                  </p>
                </td>
                <td class="table_buttom_right"></td>
              </tr>
            </table
            <br />';
  echo $output;
  unset($output);
  // we need to close $output before we start debug mode 3 or higher
  //  we will get double output if we don't
  if(2 < $debug)
  {
    echo '
            <table>
              <tr>
                <td align="left">';
    $arrayObj = new ArrayObject(get_defined_vars());
    for($iterator = $arrayObj->getIterator(); $iterator->valid(); $iterator->next())
    {
      echo '
                  <br />'.$iterator->key() . ' => ' . $iterator->current();
    }
    // debug mode 3 lists all global vars and their values, but not for arrays
    // debug mode 4 branches all arrays and their content,
    if(3 < $debug)
    {
      echo '
                  <pre>';
                    print_r ($GLOBALS);
      echo '
                  </pre>';
    }
    echo '
                </td>
              </tr>
            <table>';
  }

?>
          </center>
        </div>
      </div>
    </center>
  </body>
</html>
