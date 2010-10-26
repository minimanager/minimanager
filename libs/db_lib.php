<?php


if ($db_type === 'MySQL')
  require_once 'db_lib/mysql.php';
elseif ($db_type === 'PgSQL')
  require_once 'db_lib/pgsql.php';
elseif ($db_type === 'MySQLi')
  require_once 'db_lib/mysqli.php';
elseif ($db_type === 'SQLLite')
  require_once 'db_lib/sqlite.php';
else
  exit('<center /><br /><code />'.$db_type.'</code> is not a valid database type.<br>
    Please check settings in <code>\'scripts/config.php\'</code>.</center>');


?>
