<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */


require_once("header.php");
valid_login($action_permission['read']);

$output .= "
        <center>
          <br />
            <applet code=\"IRCApplet.class\" archive=\"libs/js/irc/irc.jar,libs/js/irc/pixx.jar\" width=\"780\" height=\"400\">
              <param name=\"nick\" value=\"$user_name\" />
              <param name=\"alternatenick\" value=\"".$user_name."_tmp\" />
              <param name=\"name\" value=\"$user_name\" />
              <param name=\"host\" value=\"{$irc_cfg['server']}\" />
              <param name=\"port\" value=\"{$irc_cfg['port']}\" />
              <param name=\"gui\" value=\"pixx\" />
              <param name=\"asl\" value=\"false\" />
              <param name=\"language\" value=\"js/irc/english\" />
              <param name=\"pixx:language\" value=\"js/irc/pixx-english\" />
              <param name=\"style:bitmapsmileys\" value=\"false\" />
              <param name=\"style:floatingasl\" value=\"true\" />
              <param name=\"style:highlightlinks\" value=\"true\" />
              <param name=\"pixx:highlight\" value=\"true\" />
              <param name=\"pixx:highlightnick\" value=\"true\" />
              <param name=\"pixx:showabout\" value=\"false\" />
              <param name=\"pixx:showhelp\" value=\"false\" />
              <param name=\"pixx:timestamp\" value=\"true\" />
              <param name=\"pixx:color5\" value=\"2a2a2a\" />
              <param name=\"pixx:color6\" value=\"383838\" />
              <param name=\"pixx:color7\" value=\"565656\" />
              <param name=\"pixx:color9\" value=\"d4d4d4\" />
              <param name=\"pixx:color10\" value=\"d4d4d4\" />
              <param name=\"pixx:color11\" value=\"d4d4d4\" />
              <param name=\"pixx:color12\" value=\"d4d4d4\" />
              <param name=\"command1\" value=\"/join #{$irc_cfg['channel']}\" />
            </applet>
            <br />
            <br />
          </center>";

require_once("footer.php");

?>
