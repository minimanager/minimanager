<?php


require_once("header.php");

$err = (isset($_GET['err'])) ? ($_GET['err']) : "Oopsy...";

$output .= "
        <center>
          <br />
          <table width=\"400\" class=\"flat\">
            <tr>
              <td align=\"center\">
                <h1>
                  <font class=\"error\">
                    <img src=\"img/warn_red.gif\" width=\"48\" height=\"48\" alt=\"\" />
                    <br />ERR!
                  </font>
                </h1>
                <br />$err<br />";
unset($err);
$output .="
                <br />
              </td>
            </tr>
          </table>
          <br />
            <table width=\"300\" class=\"hidden\">
              <tr>
                <td align=\"center\">";
                  makebutton($lang_global['home'], "index.php", 130);
                  makebutton($lang_global['back'], "javascript:window.history.back()", 130);
$output .= "
                </td>
              </tr>
            </table>
          <br />
        </center>
";

require_once("footer.php");

?>
