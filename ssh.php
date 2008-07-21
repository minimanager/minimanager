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

if (test_port($server[$realm_id]['addr'],$server[$realm_id]['term_port'])){
  $output .= "<center><br /><applet codebase=\".\" archive=\"js/ssh.jar\"
            code=\"de.mud.jta.Applet\" width=\"780\" height=\"350\">
        	<param name=\"plugins\" value=\"Status,Socket,{$server[$realm_id]['term_type']},Terminal\" />
        	<param name=\"Socket.host\" value=\"{$server[$realm_id]['addr']}\" />
        	<param name=\"Socket.port\" value=\"{$server[$realm_id]['term_port']}\" />
		</applet><br /><br /></center>";
 	} else {
		$output .= "<div class=\"top\">";
		$output .= "<h1><font class=\"error\">{$lang_ssh['server_offline']}</font></h1>";
		$output .= "</div><center>{$lang_ssh['config_server_properly']}</center>";
		}

require_once("footer.php");
?>