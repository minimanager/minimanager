<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

$time_start = microtime(true);

if ( !ini_get('session.auto_start') ) session_start();

if (file_exists("./scripts/config.php"))
{ if (file_exists("./scripts/config.dist.php"))
    require_once("./scripts/config.dist.php");
  else
    exit("<center><br><code>'./scripts/config.dist.php'</code> not found,<br> please restore <code>'./scripts/config.dist.php'</code></center>");
  require_once("./scripts/config.php");
}
else
  exit("<center><br><code>'./scripts/config.php'</code> not found,<br> please copy <code>'./scripts/config.dist.php'</code> to <code>'./scripts/config.php'</code> and make appropriate changes.");

//override PHP error reporting
if ($debug > 1)
  error_reporting (E_ALL);
else
  error_reporting (E_COMPILE_ERROR);

if (isset($_COOKIE["theme"]))
{
  if (is_dir("themes/".$_COOKIE["theme"]))
    if (is_file("themes/".$_COOKIE["theme"]."/".$_COOKIE["theme"]."_1024.css"))
      $theme = $_COOKIE["theme"];
}

if (isset($_COOKIE["lang"]))
{
  $lang = $_COOKIE["lang"];
  if (!file_exists("lang/$lang.php"))
    $lang = $language;
}
else
  $lang = $language;

require_once("./lang/$lang.php");

if($debug) $tot_queries = 0;
require_once("./libs/db_lib.php");

require_once("./scripts/global_lib.php");
require_once("./scripts/id_tab.php");

header("Content-Type: text/html; charset=".$site_encoding);
//application/xhtml+xml
$output .= "
<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head>
  <title>$title</title>
  <meta http-equiv=\"Content-Type\" content=\"text/html; charset=$site_encoding\" />
  <meta http-equiv=\"Content-Type\" content=\"text/javascript; charset=$site_encoding\" />
  <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/".$theme."/".$theme."_1024.css\" title=\"1024\" />
  <link rel=\"stylesheet\" type=\"text/css\" href=\"themes/".$theme."/".$theme."_1280.css\" title=\"1280\" />
  <link rel=\"SHORTCUT ICON\" href=\"img/favicon.ico\" />
  <script type=\"text/javascript\" charset=\"utf-8\"></script>
  <script type=\"text/javascript\" src=\"js/general.js\"></script>
  <script type=\"text/javascript\" src=\"js/layout.js\"></script>
  <script type=\"text/javascript\" src=\"$tt_script\"></script>

  <!--[if lte IE 7]>
    <style>
      #menuwrapper, #menubar ul a {height: 1%;}
      a:active {width: auto;}
      legend{margin:5px 0px 20px 0px;}
      span.button{margin:15px 0px 0px 0px;}
      #tab a { display: inline-block;}
    </style>
   <![endif]-->
</head>

<body onload=\"dynamicLayout();\">
  <center>
    <table class=\"table_top\">
      <tr>
        <td class=\"table_top_left\">";
unset($tt_script);

if($developer_test_mode && $allow_anony && (!isset($_SESSION['logged_in'])))
{
  $_SESSION['user_lvl'] = -1;
  $_SESSION['uname'] = $anony_uname;
  $_SESSION['user_id'] = -1;
  $_SESSION['realm_id'] = 1;
  $_SESSION['client_ip'] = ( !empty($_SERVER['REMOTE_ADDR']) ) ? $_SERVER['REMOTE_ADDR'] : getenv('REMOTE_ADDR');
}

if ( (isset($_SESSION['user_lvl'])) && (isset($_SESSION['uname'])) && (isset($_SESSION['realm_id']))&& (!isset($_GET['err'])) )
{
  if(ini_get('max_execution_time') < 1800)
  {
    if(!ini_set('max_execution_time',0))
      error("Error - max_execution_time not set.<br /> Please set it manually to 0, in php.ini for full functionality.");
  }

  //temp workaround
  if (ini_get('memory_limit') < 16)
    @ini_set('memory_limit', '16M');

  //set user variables
  session_regenerate_id();
  $user_lvl = $_SESSION['user_lvl'];
  $user_name = $_SESSION['uname'];
  $user_id = $_SESSION['user_id'];
  $realm_id = (isset($_GET['r_id'])) ? (int)$_GET['r_id'] : $_SESSION['realm_id'];

  $user_lvl_name = id_get_gm_level($user_lvl);

  // get file we are executing
  $array = explode ( '/', $_SERVER['PHP_SELF']);
  $lookup_file = $array[sizeof($array)-1];
  unset($array);

  $output .= "
          <div id=\"menuwrapper\">
            <ul id=\"menubar\">";

  $lang_header = lang_header();

  $action_permission=array();
  foreach ($menu_array as $trunk)
  {
    if ($trunk[1] != "invisible") // ignore "invisible array" this is for setting security read/write values for not accessible elements not in the navbar!
    {
      $output .= "
              <li><a href=\"{$trunk[0]}\">{$lang_header[$trunk[1]]}</a>";
      if(isset($trunk[2][0]))
        $output .= "
                <ul>";
      foreach ($trunk[2] as $branch)
      {
        if($branch[0] == $lookup_file)
        {
          $action_permission['read']   = $branch[2];
          $action_permission['insert'] = $branch[3];
          $action_permission['update'] = $branch[4];
          $action_permission['delete'] = $branch[5];
        }
        if ( $user_lvl >= $branch[2] )
          $output .= "
                  <li><a href=\"{$branch[0]}\">{$lang_header[$branch[1]]}</a></li>";
      }
      if(isset($trunk[2][0]))
        $output .= "
                </ul>";
      $output .= "
              </li>";
    }
    else
    {
      foreach ($trunk[2] as $branch)
      {
        if($branch[0] == $lookup_file)
        {
          $action_permission['read']   = $branch[2];
          $action_permission['insert'] = $branch[3];
          $action_permission['update'] = $branch[4];
          $action_permission['delete'] = $branch[5];
        }
      }
    }
  }
  unset($branch);
  unset($trunk);
  unset($lookup_file);

  $output .= "
              <li><a class=\"trigger\" href=\"edit.php\">{$lang_header['my_acc']}</a>
                <ul>";

  $sqlr = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  $result = $sqlr->query("SELECT id, name FROM `realmlist` LIMIT 10");

  if ( $sqlr->num_rows($result) > 1 && (count($server) > 1) )
  {
    while ($realm = $sqlr->fetch_row($result))
    {
      if(isset($server[$realm[0]]))
      {
        $set = ($realm[0] == $realm_id) ? ">" : "";
        $output .= "
                  <li><a href=\"realm.php?action=set_def_realm&amp;id=$realm[0]&amp;url={$_SERVER['PHP_SELF']}\">".htmlentities($set." ".$realm[1])."</a></li>";
      }
    }
    unset($realm);
    if (isset($set)) unset($set);
    $output .= "
                  <li><a href=\"#\">-------------------</a></li>";
  }
  unset($result);

  if($developer_test_mode && $allow_anony && !isset($_SESSION['logged_in']))
    $output .= "
                  <li><a href=\"login.php\">Login</a></li>";
  else
    $output .= "
                  <li><a href=\"edit.php\">{$lang_header['edit_my_acc']}</a></li>
                  <li><a href=\"logout.php\">{$lang_header['logout']}</a></li>";
  $output .= "
                </ul>
              </li>
            </ul>
            <br class=\"clearit\" />
          </div>
        </td>
        <td class=\"table_top_middle\">
          <div id=\"username\">$user_name .:{$user_lvl_name}'s {$lang_header['menu']}:.</div>
        </td>
        <td class=\"table_top_right\"></td>
      </tr>
    </table>";
}
else
{
  $output .= "
        </td>
        <td class=\"table_top_middle\"></td>
        <td class=\"table_top_right\"></td>
      </tr>
    </table>";
}
unset($menu_array);
unset($lang_header);

$output .= "
    <div id=\"version\">$version</div>
    <div id=\"body_main\">
      <div class=\"bubble\">";

?>
