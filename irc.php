<?php


// page header, and any additional required libraries
require_once 'header.php';
// minimum permission to view page
valid_login($action_permission['read']);

if (file_exists('lang/irc/'.$lang.'.lang') && file_exists('lang/irc/pixx-'.$lang.'.lang'))
  $irclang = $lang;
else
  $irclang = 'english';

// we start with a lead of 10 spaces,
//  because last line of header is an opening tag with 8 spaces
//  keep html indent in sync, so debuging from browser source would be easy to read
$output .= '
          <!-- start of irc.php -->
          <center>
            <br />
            <applet code="IRCApplet.class" archive="libs/js/irc/irc.jar, libs/js/irc/pixx.jar" width="780" height="400">
              <param name="nick" value="'.$user_name.'" />
              <param name="alternatenick" value="'.$user_name.'_tmp" />
              <param name="name" value="'.$user_name.'" />
              <param name="host" value="'.$irc_cfg['server'].'" />
              <param name="port" value="'.$irc_cfg['port'].'" />
              <param name="gui"  value="pixx" />
              <param name="asl"  value="false" />
              <param name="language"      value="lang/irc/'.$lang.'" />
              <param name="pixx:language" value="lang/irc/pixx-'.$lang.'" />
              <param name="style:bitmapsmileys"  value="false" />
              <param name="style:floatingasl"    value="true" />
              <param name="style:highlightlinks" value="true" />
              <param name="pixx:highlight"     value="true" />
              <param name="pixx:highlightnick" value="true" />
              <param name="pixx:nickfield" value="true">
              <param name="pixx:showabout" value="false" />
              <param name="pixx:helppage" value="http://mangos.osh.nu/forums/index.php?showforum=19">
              <param name="pixx:timestamp" value="true" />
              <param name="pixx:color5"  value="2a2a2a" />
              <param name="pixx:color6"  value="383838" />
              <param name="pixx:color7"  value="565656" />
              <param name="pixx:color9"  value="d4d4d4" />
              <param name="pixx:color10" value="d4d4d4" />
              <param name="pixx:color11" value="d4d4d4" />
              <param name="pixx:color12" value="d4d4d4" />
              <param name="command1" value="/join #'.$irc_cfg['channel'].'" />
            </applet>
            <br /><br />
          </center>
          <!-- end of irc.php -->';

require_once 'footer.php';


?>
