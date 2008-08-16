<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

$time_start = microtime(true);

if ( !ini_get('session.auto_start') ) session_start();

require_once("scripts/config.php");
if($debug) $tot_queries = 0;
require_once("scripts/db_layer.php");

if (isset($_COOKIE["lang"])){
	$lang = $_COOKIE["lang"];
	if (!file_exists("lang/$lang.php")) $lang = $language;
	} else $lang = $language;

if (isset($_COOKIE["css_template"])){
	if (is_dir("templates/".$_COOKIE["css_template"]))
		if (is_file("templates/".$_COOKIE["css_template"]."/".$_COOKIE["css_template"]."_1024.css")) $css_template = $_COOKIE["css_template"];
	}

require_once("lang/$lang.php");
require_once("scripts/global_lib.php");
require_once("scripts/id_tab.php");

//application/xhtml+xml
$output .= "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Transitional//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd\">
<html xmlns=\"http://www.w3.org/1999/xhtml\" xml:lang=\"en\" lang=\"en\">
<head>
 <title>$title</title>
 <meta http-equiv=\"Content-Type\" content=\"text/html; charset=$site_encoding\" />
 <link rel=\"stylesheet\" type=\"text/css\" href=\"templates/".$css_template."/".$css_template."_1024.css\" title=\"1024\" />
 <link rel=\"stylesheet\" type=\"text/css\" href=\"templates/".$css_template."/".$css_template."_1280.css\" title=\"1280\" />

 <link rel=\"SHORTCUT ICON\" href=\"img/favicon.ico\" />
 <script type=\"text/javascript\" src=\"js/general.js\"></script>
 <script type=\"text/javascript\" src=\"js/layout.js\"></script>
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

if ( (isset($_SESSION['user_lvl'])) && (isset($_SESSION['uname'])) && (isset($_SESSION['realm_id']))&& (!isset($_GET['err'])) ){

	if(ini_get('max_execution_time') < 1800){
		if(!ini_set('max_execution_time',0)) 
			error("Error - max_execution_time not set.<br /> Please set it manually to 0, in php.ini for full functionality.");
		}

	//temp workaround
	@ini_set('memory_limit', '16M');

	//set user variables
	session_regenerate_id();
	$user_lvl = $_SESSION['user_lvl'];
	$user_name = $_SESSION['uname'];
	$user_id = $_SESSION['user_id'];
	$realm_id = (isset($_GET['r_id'])) ? addslashes($_GET['r_id']) : $_SESSION['realm_id'];
	
	$user_lvl_name = get_gm_level($user_lvl); 
	
	//override PHP error reporting
	if ($debug) error_reporting (E_ALL);
		else error_reporting (E_COMPILE_ERROR);

	$sql = new SQL;
	$sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
	$result = $sql->query("SELECT id,name FROM `realmlist` LIMIT 10");

	$output .= "<div id=\"menuwrapper\">
			<ul id=\"menubar\">";

    if(!isset($menu_array[$user_lvl])) error("Wrong menu_array configuration.<br />Level $user_lvl menu missing...");

  // get file we are executing
  $array = explode ( '/', $_SERVER['PHP_SELF']);
	$lookup_file = $array[sizeof($array)-1];
	
  foreach ($menu_array as $trunk){
    if ($trunk[1] != "invisible") // ignore "invisible array" this is for setting security read/write values for not accessible elements not in the navbar!
    {
      $output .= "<li><a href=\"{$trunk[0]}\">{$lang_header[$trunk[1]]}</a>";
      if(isset($trunk[2][0])) $output .= "<ul>";
      foreach ($trunk[2] as $branch){
        
        if($branch[0] == $lookup_file)
        {
          $action_permission['read']   = $branch[2];
          $action_permission['update'] = $branch[3];
          $action_permission['insert'] = $branch[4];
          $action_permission['delete'] = $branch[5];
        }
        
        if ( $user_lvl >= $branch[2] )
          $output .= "<li><a href=\"{$branch[0]}\">{$lang_header[$branch[1]]}</a></li>";
      }
      if(isset($trunk[2][0])) $output .= "</ul>";
      $output .= "</li>";
    }
    else
    {
      foreach ($trunk[2] as $branch){
        
        if($branch[0] == $lookup_file)
          $action_permission['read']=$branch[2];
      }
    }
    
  }
  
  // secure non secure settings
  // some pages don't have security yet, because they dont use header.php... like pomm.php and login.php
  if(!isset($action_permission['read']))                                                                   $action_permission['read']   = 3;
  if(!isset($action_permission['update']) || $action_permission['update'] < $action_permission['read']   ) $action_permission['update'] = $action_permission['read'];
  if(!isset($action_permission['insert']) || $action_permission['insert'] < $action_permission['update'] ) $action_permission['insert'] = $action_permission['update'];
  if(!isset($action_permission['delete']) || $action_permission['delete'] < $action_permission['insert'] ) $action_permission['delete'] = $action_permission['insert'];

	$output .= "<li><a class=\"trigger\" href=\"edit.php\">{$lang_header['my_acc']}</a>
			<ul>";
			if ($sql->num_rows($result) > 1){
				while ($realm = $sql->fetch_row($result)){
					$set = ($realm[0] == $realm_id) ? ">" : "";
					$output .= "<li><a href=\"realm.php?action=set_def_realm&amp;id=$realm[0]&amp;url={$_SERVER['PHP_SELF']}\">$set $realm[1]</a></li>";
					}
				$output .= "<li><a href=\"#\">-------------------</a></li>";
				}
	$output .= "<li><a href=\"edit.php\">{$lang_header['edit_my_acc']}</a></li>
				<li><a href=\"logout.php\">{$lang_header['logout']}</a></li>
			</ul>
			</li>
			</ul>
		<br class=\"clearit\" />
		</div></td>
			<td class=\"table_top_middle\">
			<div id=\"username\">$user_name .:{$user_lvl_name}'s {$lang_header['menu']}:.</div></td>
			<td class=\"table_top_right\"></td>
			</tr>
		</table>";
	
  $sql->close();
 
 } else {
 
	$output .= "</td>
			<td class=\"table_top_middle\"></td>
			<td class=\"table_top_right\"></td>
			</tr>
		</table>";
}

$output .= "<div id=\"version\">$version</div>
			<div id=\"body_main\">
				<div class=\"bubble\">
					<i class=\"tr\"></i><i class=\"tl\"></i>";
?>