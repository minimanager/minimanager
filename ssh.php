<?php


require_once("header.php");
valid_login($action_permission['delete']);

$lang_ssh = lang_ssh();

if (test_port($server[$realm_id]['addr_wan'],$server[$realm_id]['term_port']))
{
  $output .= "
        <center>
          <br />
          <applet codebase=\".\" archive=\"libs/js/ssh.jar\"
            code=\"de.mud.jta.Applet\" width=\"780\" height=\"350\">
            <param name=\"plugins\" value=\"Status,Socket,{$server[$realm_id]['term_type']},Terminal\" />
            <param name=\"Socket.host\" value=\"{$server[$realm_id]['addr_wan']}\" />
            <param name=\"Socket.port\" value=\"{$server[$realm_id]['term_port']}\" />
          </applet>
          <br />
          <br />
        </center>";
}
else
{
  $output .= "
        <div class=\"top\">
          <h1><font class=\"error\">{$lang_ssh['server_offline']}</font></h1>
        </div>
        <center>
          {$lang_ssh['config_server_properly']}
        </center>";
}

unset($action_permission);
unset($lang_ssh);

require_once("footer.php");

?>
