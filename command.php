<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 21.02.2007 version (0.0.9a)
 * Author: t0chiro (command.php)
 * Copyright: t0chiro(command.php)
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

include("header.php");
valid_login($action_permission['read']);

//#############################################################################
// PRINT COMMAND FORM
//#############################################################################
function print_commands_form()
{
  global $lang_command, $output, $world_db, $action_permission, $user_lvl, $realm_id, $gm_level_arr;
  valid_login($action_permission['read']);

  $levels = $gm_level_arr;

  $sql = new SQL;
  $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

  $query = $sql->query("SELECT name,help,`security` FROM command WHERE `security` <= $user_lvl");

  while ($data = $sql->fetch_row($query))
  {
    $tmp_output = "
        <tr>";
    $tmp_output .=
          ($user_lvl >= $action_permission['update']) ? "<td><input type=\"checkbox\" name=\"check[$data[0]]\" value=\"$data[2]\" /></td>" : "<td></td>";
    $tmp_output .= "
          <td align=\"left\">.$data[0]</td>";
    $comm = explode("\r\n",$data[1],2);
    $syntax = ereg_replace("[a-zA-Z ]+:* *\.".$data[0]." *", "", str_replace("/", "<br />",$comm[0]));
    if (isset($comm[1]))
      $description = str_replace("\r\n\r\n", "<br />", $comm[1]);
    else
    {
      $comm = explode("<!>",ereg_replace(" ([a-zA-Z]+ .*)", "<!>\\0", $syntax),2);
      $syntax = $comm[0];
      $description = isset($comm[1]) ? $comm[1] : " ";
    }
    $tmp_output .="
          <td>".htmlentities($syntax)."</td>
          <td>".htmlentities($description)."</td>
        </tr>";
    $levels[$data[2]][3] .= $tmp_output;
  }
  unset($data);
  unset($query);

  $output .= "
         <center>
           <form method=\"get\" action=\"command.php\" name=\"form\">
             <input type=\"hidden\" name=\"action\" value=\"update\" />";
  for ($i=0; $i<=$user_lvl; $i++)
  {
    $output .= "
             <fieldset class=\"full_frame\">
               <legend>".(($user_lvl) ? "<a href=\"#\" onclick=\"showHide('{$levels[$i][1]}')\">{$levels[$i][1]}</a>" : "{$levels[$i][1]}")."</legend>
                 <div id=\"{$levels[$i][1]}\">";
    $output .="
               <br />
                 <table class=\"lined\" style=\"width: 720px;text-align: left;\">
                   <tr style=\"text-align: center;\">
                     <th width=\"2%\"></th>
                     <th width=\"13%\">{$lang_command['command']}</th>
                     <th width=\"20%\">{$lang_command['syntax']}</th>
                     <th width=\"65%\">{$lang_command['description']}</th>
                   </tr>" . $levels[$i][3];
    if($user_lvl >= $action_permission['update'])
    {
      $output .= "
                 </table>
                 <br />
                 <table class=\"hidden\" style=\"width: 720px;\">
                   <tr>
                     <td>";
                       makebutton($lang_command['change_level'], "javascript:do_submit()",280);
      $output .="
                     </td>
                   </tr>";
    }
    $output .= "
                 </table>
               </div>
             </fieldset>";
  }
  $output .= "
           </form>
         <br />
       </center>";

$sql->close();
unset($sql);

}


//#############################################################################
//  UPDATE COMMAND LEVEL
//#############################################################################
function update_commands()
{
  global  $lang_global, $lang_command, $output, $action_permission, $user_lvl, $gm_level_arr;
  valid_login($action_permission['update']);

  if(isset($_GET['check'])) $check = $_GET['check'];
    else redirect("command.php?error=1");

  $output .= "
        <center>
          <form method=\"get\" action=\"command.php\" name=\"form\">
            <input type=\"hidden\" name=\"action\" value=\"doupdate\" />
              <table class=\"lined\" style=\"width: 720px;\">
                <tr>
                  <th width=\"1%\"></th>";
  for ($i=0; $i<=$user_lvl; $i++)
  {
    $output .= "
                  <th width=\"1%\">{$gm_level_arr[$i][1]}</th>";
  }

  $output .= "
                </tr>";

  $commands = array_keys($check);
  for ($i=0; $i<count($check); $i++)
  {
    $output .= "
                <tr>
                  <td>.$commands[$i]</td>";
    for ($j=0; $j<=$user_lvl; $j++)
    {
      $output .= "
                  <td><input type=\"radio\" name=\"change[".$commands[$i]."]\" value=\"".$j."\"";
      if ($check[$commands[$i]]==$j)
        $output .= " checked=\"checked\"";
      $output .= " /></td>";
    }
    $output .="</tr>";
  }

  $output .= "
              </table>
            </form>
            <table width=\"300\" class=\"hidden\">
              <tr>
                <td>";
                  makebutton($lang_command['save'], "javascript:do_submit()\" type=\"wrn",130);
                  makebutton($lang_global['back'], "command.php\" type=\"def",130);
  $output .= "
                </td>
              </tr>
            </table>
          </center>";
}


//#############################################################################
//  DO UPDATE COMMAND LEVEL
//#############################################################################
function doupdate_commands()
{
  global $lang_global, $output, $world_db, $realm_id, $action_permission;
  valid_login($action_permission['update']);

  $sql = new SQL;
  $sql->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);
  if(isset($_GET['change']))
    $change = $sql->quote_smart($_GET['change']);
  else
    redirect("command.php?error=1");

  $commands = array_keys($change);

  for ($i=0; $i<count($change); $i++)
  {
    $query = $sql->query("UPDATE command SET `security` = '".$change[$commands[$i]]."' WHERE name= '$commands[$i]'");
  }

  $sql->close();
  unset($sql);
  redirect("command.php");
}

//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";

$lang_command = lang_command();

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
    break;
  default: //no error
    $output .= "
          <h1>{$lang_command['command_list']}</h1>";
}

unset($err);

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "update":
    update_commands();
    break;
  case "doupdate":
    doupdate_commands();
    break;
  default:
    print_commands_form();
}

unset($action);
unset($action_permission);
unset($lang_command);

include("footer.php");
?>
