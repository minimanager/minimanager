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

$err = (isset($_GET['err'])) ? htmlentities($_GET['err'], ENT_QUOTES) : "Oopsy...";

$output .= "<center><br /><table width=\"300\" class=\"flat\">
          <tr>
            <td align=\"center\"><h1><font class=\"error\"><img src=\"img/warn_red.gif\" width=\"48\" height=\"48\" alt=\"\" /><br />ERR!</font></h1>
            <br />$err<br /><br />
            </td>
		  </tr>
        </table><br />";
$output .= "<table class=\"hidden\">
          <tr><td>";
				makebutton($lang_global['back'], "javascript:window.history.back()", 120);
				makebutton($lang_global['home'], "index.php", 120);
$output .= "</td></tr>
        </table><br /></center>";

require_once("footer.php");
?>