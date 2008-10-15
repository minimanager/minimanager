<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

require_once("header.php");
valid_login($action_permission['read']);

//##############################################################################################################
// EDIT USER
//##############################################################################################################
function edit_user() {
 global $lang_edit, $lang_global, $output, $realm_db, $characters_db, $realm_id, $user_name, $user_id,
		$lang_id_tab, $gm_level_arr;

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $result = $sql->query("SELECT email,gmlevel,joindate,expansion FROM account WHERE username ='$user_name'");

 if ($acc = $sql->fetch_row($result)) {
  require_once("scripts/id_tab.php");

  $output .= "<center>
  <script type=\"text/javascript\" src=\"js/sha1.js\"></script>
  <script type=\"text/javascript\">
		function do_submit_data () {
			document.form.pass.value = hex_sha1('".strtoupper($user_name).":'+document.form.user_pass.value.toUpperCase());
			document.form.user_pass.value = '0';
			do_submit();
		}
 </script>
  <fieldset style=\"width: 550px;\">
	<legend>{$lang_edit['edit_acc']}</legend>
    <form method=\"post\" action=\"edit.php?action=doedit_user\" name=\"form\">
	<input type=\"hidden\" name=\"pass\" value=\"\" maxlength=\"256\" />
    <table class=\"flat\">
      <tr>
        <td>{$lang_edit['id']}</td>
        <td>$user_id</td>
      </tr>
      <tr>
        <td>{$lang_edit['username']}</td>
        <td>$user_name</td>
      </tr>
	  <tr>
        <td>{$lang_edit['password']}</td>
        <td><input type=\"text\" name=\"user_pass\" size=\"43\" maxlength=\"40\" value=\"******\" /></td>
      </tr>
      <tr>
        <td>{$lang_edit['mail']}</td>
        <td><input type=\"text\" name=\"mail\" size=\"43\" maxlength=\"225\" value=\"$acc[0]\" /></td>
      </tr>
	  <tr>
        <td>{$lang_edit['gm_level']}</td>
        <td>".get_gm_level($acc[1])." ( $acc[1] )</td>
      </tr>
	  <tr>
	  <td >{$lang_edit['client_type']}:</td>
  	 <td>
	   <select name=\"expansion\">
	    <option value=\"1\" ";
		if($acc[3]) $output .= "selected=\"selected\"";
		$output .= ">{$lang_edit['expansion']}</option>
	    <option value=\"0\" ";
		if(!$acc[3]) $output .= "selected=\"selected\"";
		$output .= ">{$lang_edit['classic']}</option>
	   </select>
	</td>
	</tr>
      <tr>
        <td>{$lang_edit['join_date']}</td>
        <td>$acc[2]</td>
      </tr>";

	$result = $sql->query("SELECT SUM(numchars) FROM realmcharacters WHERE acctid = '$user_id'");
	$output .= "<tr>
        <td>{$lang_edit['tot_chars']}</td>
        <td>".$sql->result($result, 0)."</td>
      </tr>";

	$sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
	$result = $sql->query("SELECT guid,name,race,class,SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', 35), ' ', -1) FROM `characters` WHERE account = $user_id");

	$output .= "<tr>
        <td>{$lang_edit['characters']}</td>
        <td>".$sql->num_rows($result)."</td>
      </tr>";

	while ($char = $sql->fetch_array($result)){
		$output .= "<tr>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'---></td>
		<td><a href=\"char.php?id=$char[0]\">$char[1]  - ".get_player_race($char[2])." ".get_player_class($char[3])." | lvl $char[4]</a></td>
		</tr>";
	}

 $output .= "<tr><td>";
		makebutton($lang_edit['update'], "javascript:do_submit_data()",140);
 $output .= "</td><td>";
		makebutton($lang_edit['del_acc'], "edit.php?action=delete_user",150);
		makebutton($lang_global['back'], "javascript:window.history.back()",150);
 $output .= "</td></tr>
    	</table>
    </form></fieldset>
	<br />
	<fieldset style=\"width: 550px;\">
	<legend>{$lang_edit['cms_options']}</legend>
	<table class=\"hidden\" style=\"width: 450px;\">
     <tr>
	  <td align=\"left\">{$lang_edit['select_cms_layout_lang']} :</td>
	  <td align=\"right\">
	  <form action=\"edit.php\" method=\"get\" name=\"form1\">
	  <input type=\"hidden\" name=\"action\" value=\"lang_set\" />
		<select name=\"lang\">
		<optgroup label=\"{$lang_edit['language']}\">";
		if (is_dir("./lang")){
			if ($dh = opendir("./lang")){
				while (($file = readdir($dh)) != false){
					$lang = explode('.', $file);
					if(isset($lang[1]) && $lang[1] == 'php'){
					   if ((substr($file,0,6) != 'forum_') ){
					     $output .= "<option value=\"{$lang[0]}\"";
					     if (isset($_COOKIE["lang"]) && ($_COOKIE["lang"] == $lang[0])) $output .= " selected=\"selected\" ";
					     $output .= ">{$lang[0]}</option>";
					     }
					 }
				}
			closedir($dh);
			}
		}
 $output .= "</optgroup>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;
			</form>
			</td><td>";
			makebutton($lang_edit['save'], "javascript:do_submit('form1',0)",100);
 $output .= "</td>
		</tr>
	<tr>
	  <td align=\"left\">{$lang_edit['select_cms_template']} :</td>
	  <td align=\"right\">
	  <form action=\"edit.php\" method=\"get\" name=\"form2\">
	  <input type=\"hidden\" name=\"action\" value=\"template_set\" />
		<select name=\"template\">
		<optgroup label=\"{$lang_edit['template']}\">";
		if (is_dir("./templates")){
			if ($dh = opendir("./templates")){
				while (($file = readdir($dh)) != false){
					if (($file != '.')&&($file != '..')&&($file != '.htaccess')&&($file != 'index.html')&&($file != '.svn')&&($file != 'pomm.css')){
						$output .= "<option value=\"$file\"";
						if (isset($_COOKIE["css_template"]) && ($_COOKIE["css_template"] == $file)) $output .= " selected=\"selected\" ";
						$output .= ">$file</option>";
					}
				}
			closedir($dh);
			}
		}
 $output .= "</optgroup>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;
			</form>
			</td>
			<td>";
			makebutton($lang_edit['save'], "javascript:do_submit('form2',0)",100);
 $output .= "</td></tr>
		</table>
	 </fieldset>
	<br /></center>";
 } else error($lang_global['err_no_records_found']);

 $sql->close();
}


//#############################################################################################################
//  DO EDIT USER
//#############################################################################################################
function doedit_user() {
 global $realm_db, $user_name;

 if ( (!isset($_POST['pass'])||$_POST['pass'] === '') || (!isset($_POST['mail'])||$_POST['mail'] === '') ||(!isset($_POST['expansion'])||$_POST['expansion'] === '') )
	redirect("edit.php?error=1");

 $sql = new SQL;
 $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

 $new_pass = ($sql->quote_smart($_POST['pass']) != sha1(strtoupper($user_name).":******")) ? "sha_pass_hash='".$sql->quote_smart($_POST['pass'])."', " : "";
 $new_mail = $sql->quote_smart(trim($_POST['mail']));
 $new_expansion = $sql->quote_smart(trim($_POST['expansion']));

 //make sure the mail is valid mail format
 require_once("scripts/valid_lib.php");
 if ((!is_email($new_mail))||(strlen($new_mail)  > 224)) redirect("edit.php?error=2");

 $sql->query("UPDATE account SET email='$new_mail', $new_pass expansion='$new_expansion' WHERE username = '$user_name'");

 if ($sql->affected_rows()) {
	$sql->close();
	redirect("edit.php?error=3");
    } else {
		$sql->close();
		redirect("edit.php?error=4");
	}
}


//###############################################################################################################
// DELETE USER
//###############################################################################################################
function delete_user() {
 global $lang_edit, $lang_global, $output, $user_name;

 $output .= "<center><h1><font class=\"error\">{$lang_global['are_you_sure']}</font></h1><br />
			<font class=\"bold\">{$lang_edit['username']} : '$user_name' {$lang_edit['will_be_erased']}</font><br /><br />
			<table class=\"hidden\">
			<tr><td>";
				makebutton($lang_global['yes'], "edit.php?action=dodelete_user",120);
				makebutton($lang_global['no'], "edit.php",120);
 $output .= "</td></tr>
        </table></center><br />";
}


//###############################################################################################################
// DO DELETE  USER
//###############################################################################################################
function dodelete_user() {
 global $realm_db, $characters_db, $realm_id, $user_id, $tab_del_user_characters, $tab_del_user_realmd;

 require_once("./scripts/del_lib.php");
 list($flag,$del_char) = del_acc($user_id);

 if ($flag) include("logout.php");
	else redirect("edit.php?error=5");
}


//###############################################################################################################
// SET DEFAULT INTERFACE LANGUAGE
//###############################################################################################################
function lang_set() {
 if (empty($_GET['lang'])) redirect("edit.php?error=1");
	else $lang = addslashes($_GET['lang']);

 if ($lang) {
		setcookie("lang", $lang, time()+60*60*24*30*6); //six month
		redirect("edit.php");
		} else redirect("edit.php?error=1");
}


//###############################################################################################################
// SET DEFAULT INTERFACE TEMPLATE
//###############################################################################################################
function template_set() {
 if (empty($_GET['template'])) redirect("edit.php?error=1");
	else $tmpl = addslashes($_GET['template']);

 if ($tmpl) {
		setcookie("css_template", $tmpl, time()+3600*24*30*6); //six month
		redirect("edit.php");
		} else redirect("edit.php?error=1");
}

//###############################################################################################################
// MAIN
//###############################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "<div class=\"top\">";
switch ($err) {
case 1:
   $output .= "<h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
   break;
case 2:
   $output .= "<h1><font class=\"error\">{$lang_edit['use_valid_email']}</font></h1>";
   break;
case 3:
   $output .= "<h1><font class=\"error\">{$lang_edit['data_updated']}</font></h1>";
   break;
case 4:
   $output .= "<h1><font class=\"error\">{$lang_edit['error_updating']}</font></h1>";
   break;
case 5:
   $output .= "<h1><font class=\"error\">{$lang_edit['del_error']}</font></h1>";
   break;
default: //no error
   $output .= "<h1>{$lang_edit['edit_your_acc']}</h1>";
}
$output .= "</div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action) {
case "doedit_user":
	doedit_user();
	break;
case "delete_user":
	delete_user();
	break;
case "dodelete_user":
 	dodelete_user();
	break;
case "lang_set":
 	lang_set();
	break;
case "template_set":
 	template_set();
	break;
default:
    edit_user();
}

require_once("footer.php");
?>
