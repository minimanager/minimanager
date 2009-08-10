<?php
  echo $output;
  unset($output);

  print "
      </div>
      <div id=\"body_buttom\">
        <center>
";
  if($developer_test_mode && $allow_anony && !isset($_SESSION['logged_in']))
  {
    $lang_login = lang_login();
    $output ="
          <table>
            <tr>
              <td>
                <a class=\"button\" style=\"width:130px;\" href=\"register.php\">Register</a>
                <a class=\"button\" style=\"width:130px;\" href=\"login.php\">Login</a>
              </td>
            </tr>
          </table>";
    echo $output;
    unset($output);
  }
  print "
          <table class=\"table_buttom\">
            <tr>
              <td class=\"table_buttom_left\"></td>
              <td class=\"table_buttom_middle\">";
  $lang_footer = lang_footer();
  print "
                {$lang_footer['bugs_to_admin']} <a href=\"mailto:$admin_mail\">{$lang_footer['site_admin']}</a><br />";
  unset($lang_footer);
  printf("
                Execute time: %.5f", (microtime(true) - $time_start));
  unset($time_start);
  if($debug > 0)
  {
    print "
                Queries: $tot_queries on ".$_SERVER['SERVER_SOFTWARE'];
    unset($tot_queries);
    if (function_exists('memory_get_usage'))
      printf("
                <br />Mem. Usage: %.0f/%.0fK Peek: %.0f/%.0fK Global: %.0fK Limit: %s",memory_get_usage()/1024, memory_get_usage(true)/1024,memory_get_peak_usage()/1024,memory_get_peak_usage(true)/1024,sizeof($GLOBALS),ini_get('memory_limit'));
  }
  print "
                <p>";
  if ($server_type)
    print "
                  <a href=\"http://www.trinitycore.org/\" target=\"_blank\"><img src=\"img/logo-trinity.png\" class=\"logo_border\" alt=\"trinity\" /></a>";
  else
    print "
                  <a href=\"http://getmangos.com/\" target=\"_blank\"><img src=\"img/logo-mangos.png\" class=\"logo_border\" alt=\"mangos\" /></a>";
  unset($server_type);
  print "
                  <a href=\"http://www.php.net/\" target=\"_blank\"><img src=\"img/logo-php.png\" class=\"logo_border\" alt=\"php\" /></a>
                  <a href=\"http://www.mysql.com/\" target=\"_blank\"><img src=\"img/logo-mysql.png\" class=\"logo_border\" alt=\"mysql\" /></a>
                  <a href=\"http://validator.w3.org/check?uri=referer\" target=\"_blank\"><img src=\"img/logo-css.png\" class=\"logo_border\" alt=\"w3\" /></a>
                  <a href=\"http://www.spreadfirefox.com/\" target=\"_blank\"><img src=\"img/logo-firefox.png\" class=\"logo_border\" alt=\"firefox\" /></a>
                  <a href=\"http://www.opera.com/\" target=\"_blank\"><img src=\"img/logo-opera.png\" class=\"logo_border\" alt=\"opera\" /></a>
                </p>
              </td>
              <td class=\"table_buttom_right\"></td>
            </tr>
          </table>
        </center>
        <br />
";
  if($debug > 2)
  {
    echo "
        <table>
          <tr>
            <td align='left'>";
    $arrayObj = new ArrayObject(get_defined_vars());
    for($iterator = $arrayObj->getIterator(); $iterator->valid(); $iterator->next())
    {
      echo "
              <br />".$iterator->key() . ' => ' . $iterator->current();
    }
    unset($iterator);
    unset($arrayObj);
    if($debug > 3)
    {
      echo "
              <pre>";
      print_r ($GLOBALS);
      echo "
              </pre>";
    }
    echo "
            </td>
          </tr>
        <table>";
  }
  unset($debug);
?>
      </div>
    </div>
  </center>
</body>
</html>
