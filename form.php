<?php 
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: gmaze
 * Copyright: gmaze
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */
 
require_once("header.php");
session_start();

//check to see if captcha check is enabled
 if (!$enable_captcha){
	header("Location: register.php");
 	exit();
 }

//print the form and image
function captcha(){
 global $lang_captcha ,$output;
 
 $output .= "<center>
	<fieldset style=\"width: 550px;\">
	<legend>{$lang_captcha['security_image']}</legend>
	<form method=\"POST\" action=\"form.php\">
    <table cellpadding=\"3\" cellspacing=\"0\" border=\"0\">
      <tr align=\"left\"><td>
				<img src=\"captcha/CaptchaSecurityImages.php?width=300&height=80&characters=6\" /><br /><br />
				<center><div class=\"large\">Security Code:
				<input id=\"security_code\" name=\"security_code\" type=\"text\" size=\"24\" /></div></center><br />
				<center><input type=\"submit\" name=\"submit\" value=\"Submit\" onmouseover=\"this.className='mouseover'\" onmouseout=\"this.className=''\" /></center>
      </td></table></form></fieldset><br /><br /></center>";
}

//compare input code with displayed code
  if( isset($_POST['submit'])) {
   if( $_SESSION['security_code'] == $_POST['security_code'] && !empty($_SESSION['security_code'] ) ) {
		//code for processing the form 
		header("Location: register.php");
		unset($_SESSION['security_code']);
   } else {
		//code for showing error
		header("Location: form.php?error=1");
   }
  } 

//error processing
if(isset($_GET['error'])) $err = $_GET['error'];
	else $err = NULL;

$output .= "<div class=\"top\">";
switch ($err) {
case 1:
   $output .=  "<h1><font class=\"error\">{$lang_captcha['invalid_code']}</font></h1>";
   break;
default: //no error
    $output .=  "<h1>{$lang_captcha['security_code']}</h1>";
}
$output .= "</div>";

switch ($action){
default:
    captcha();
}

require_once("footer.php");

?>
