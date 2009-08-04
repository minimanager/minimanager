<?php
echo $output;
unset($output);
?>
        <i class="bl"></i>
        <i class="br"></i>
      </div>
      <br />
      <div id="body_buttom">
        <table class="table_buttom">
          <center>
            <tr>
              <td class="table_buttom_left"></td>
              <td class="table_buttom_middle">
<?php 
  print "
                {$lang_footer['bugs_to_admin']} <a href=\"mailto:$admin_mail\">{$lang_footer['site_admin']}</a><br />";
  printf("
                Execute time: %.5f", (microtime(true) - $time_start));
  if($debug)
  {
    print "
                Queries: $tot_queries on ".$_SERVER['SERVER_SOFTWARE']; 
    if (function_exists('memory_get_usage'))
      printf("
                <br />Mem. Usage: %.0f/%.0fK Peek: %.0f/%.0fK Limit: %s",memory_get_usage()/1024, memory_get_usage(true)/1024,memory_get_peak_usage()/1024,memory_get_peak_usage(true)/1024,ini_get('memory_limit'));
  }
  print "
                <p>";
  require_once("./scripts/config.dist.php");
  require_once("./scripts/config.php");
  if ($server_type)
    print "
                  <a href=\"http://www.trinitycore.org/\" target=\"_blank\"><img src=\"img/logo-trinity.png\" class=\"logo_border\" alt=\"\" /></a>";
  else
    print "
                  <a href=\"http://getmangos.com/\" target=\"_blank\"><img src=\"img/logo-mangos.png\" class=\"logo_border\" alt=\"\" /></a>";
?>
                  <a href="http://www.php.net/" target="_blank"><img src="img/logo-php.png" class="logo_border" alt="" /></a>
                  <a href="http://www.mysql.com/" target="_blank"><img src="img/logo-mysql.png" class="logo_border" alt="" /></a>
                  <a href="http://validator.w3.org/check?uri=referer" target="_blank"><img src="img/logo-css.png" class="logo_border" alt="" /></a>
                  <a href="http://www.spreadfirefox.com/" target="_blank"><img src="img/logo-firefox.png" class="logo_border" alt="" /></a>
                  <a href="http://www.opera.com/" target="_blank"><img src="img/logo-opera.png" class="logo_border" alt="" /></a>
                </p>
              </td>
              <td class="table_buttom_right"></td>
            </tr>
          </center>
        </table>
        <br />
      </div>
    </div>
  </center>
</body>
</html>