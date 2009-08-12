<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Shnappie
 * Copyright: Q.SA, Shnappie
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */


require_once("header.php");
require_once("scripts/defines.php");
require_once("scripts/get_lib.php");
valid_login($action_permission['read']);

//########################################################################################################################
// BROWSE ARENA TEAMS
//########################################################################################################################
function browse_teams()
{
  global $lang_arenateam, $lang_global, $output, $realm_db, $characters_db, $realm_id, $itemperpage,
    $action_permission, $user_lvl, $user_id;

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  //==========================$_GET and SECURE=================================
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $start)) $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : "atid";
  if (!preg_match("/^[_[:lower:]]{1,17}$/", $order_by)) $order_by="atid";

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end=============================
  //==========================Browse/Search CHECK==============================
  $search_by ='';
  $search_value = '';
  if(isset($_GET['search_value']) && isset($_GET['search_by']))
  {
    $search_value = $sqlc->quote_smart($_GET['search_value']);
    $search_by = $sqlc->quote_smart($_GET['search_by']);
    $search_menu = array('atname', 'lguid', 'atid');
    if (!array_key_exists($search_by, $search_menu)) $search_by = 'atname';

    if ($search_by == "leadername")
    {
      $temp = $sqlc->query("SELECT guid FROM `characters` WHERE name LIKE '$search_value'");
      $search_value = $sqlc->result($temp, 0, 'guid');
    }
    $query_1 = $sqlc->query("SELECT count(*) FROM arena_team, arena_team_stats
      WHERE arena_team.arenateamid = arena_team_stats.arenateamid
      AND $search_by LIKE '%$search_value%'");
    $query = $sqlc->query("SELECT arena_team.arenateamid AS atid, arena_team.name AS atname, arena_team.captainguid AS lguid,
      arena_team.type AS attype,
      (SELECT name FROM `characters` WHERE guid = lguid) AS lname,(SELECT COUNT(*) FROM  arena_team_member WHERE arenateamid = atid) AS tot_chars,
      rating AS atrating, games as atgames, wins as atwins
      FROM arena_team, arena_team_stats
      WHERE arena_team.arenateamid = arena_team_stats.arenateamid
      AND $search_by LIKE '%$search_value%'
      ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
  }
  else
  {
    $query = $sqlc->query("SELECT arena_team.arenateamid AS atid, arena_team.name AS atname, arena_team.captainguid AS lguid,
      arena_team.type AS attype,
      (SELECT name FROM `characters` WHERE guid = lguid) AS lname,(SELECT COUNT(*) FROM  arena_team_member WHERE arenateamid = atid) AS tot_chars,
      rating AS atrating, games as atgames, wins as atwins,
      (SELECT count(*) AS GCNT  FROM `arena_team_member`, `characters`, `arena_team` WHERE arena_team.arenateamid = atid AND arena_team_member.arenateamid = arena_team.arenateamid AND arena_team_member.guid = characters.guid AND characters.online = 1) as arenateam_online
      FROM arena_team, arena_team_stats
      WHERE arena_team.arenateamid = arena_team_stats.arenateamid
      ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
  }
  $query_1 = $sqlc->query("SELECT count(*) FROM arena_team");
  $all_record = $sqlc->result($query_1,0);
  unset($query_1);
  $this_page = $sqlc->num_rows($query);

//==========================top page navigation starts here====================
  $output .="
        <center>
          <table class=\"top_hidden\">
            <tr>
              <td>";
                makebutton($lang_global['back'], "javascript:window.history.back()", 130);
  ($search_by &&  $search_value) ? makebutton($lang_arenateam['arenateams'], "arenateam.php", 130) : $output .= "";
  $output .= "
              </td>
            </tr>
            <tr>
              <td>
                <table class=\"hidden\">
                  <tr>
                    <td>
                      <form action=\"arenateam.php\" method=\"get\" name=\"form\">
                        <input type=\"hidden\" name=\"error\" value=\"4\" />
                        <input type=\"text\" size=\"24\" name=\"search_value\" />
                        <select name=\"search_by\">
                          <option value=\"atname\"".($search_by == 'atname' ? " selected=\"selected\"" : "").">{$lang_arenateam['by_name']}</option>
                          <option value=\"lguid\"".($search_by == 'lguid' ? " selected=\"selected\"" : "").">{$lang_arenateam['by_team_leader']}</option>
                          <option value=\"atid\"".($search_by == 'atid' ? " selected=\"selected\"" : "").">{$lang_arenateam['by_id']}</option>
                        </select>
                      </form>
                    </td>
                    <td>";
                      makebutton($lang_global['search'], "javascript:do_submit()",80);
  $output .= "
                    </td>
                  </tr>
                </table>
              </td>
              <td align=\"right\">";
  $output .= generate_pagination("arenateam.php?order_by=$order_by".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=".!$dir, $all_record, $itemperpage, $start);
  $output .= "
              </td>
            </tr>
          </table>";
//==========================top page navigation ENDS here =====================

  $output .= "
          <table class=\"lined\">
            <tr>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=atid&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='atid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_arenateam['id']}</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=atname&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='atname' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_arenateam['arenateam_name']}</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=l_name&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='l_name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_arenateam['captain']}</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=attype&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='attype' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_arenateam['type']}</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=tot_chars&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='tot_chars' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_arenateam['members']}</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=arenateam_online&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='arenateam_online' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_arenateam['arenateam_online']}</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=rating&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='rating' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_arenateam['rating']}</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=games&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='games' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_arenateam['games']}</a></th>
              <th width=\"1%\"><a href=\"arenateam.php?order_by=wins&amp;start=$start".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."&amp;dir=$dir\">".($order_by=='wins' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_arenateam['wins']}</a></th>
            </tr>";
  while ($data = $sqlc->fetch_row($query))
  {
    $gonline = $sqlc->query("SELECT count(*) AS GCNT  FROM `arena_team_member`, `characters`, `arena_team` WHERE arena_team.arenateamid = ".$data[0]." AND arena_team_member.arenateamid = arena_team.arenateamid AND arena_team_member.guid = characters.guid AND characters.online = 1;");
    $arenateam_online = $sqlc->result($gonline,"GCNT");
    $output .= "
            <tr>
              <td>$data[0]</td>
              <td><a href=\"arenateam.php?action=view_team&amp;error=3&amp;id=$data[0]\">".htmlentities($data[1])."</a></td>
              <td><a href=\"char.php?id=$data[2]\">".htmlentities($data[4])."</a></td>
              <td>{$lang_arenateam[$data[3]]}</td>
              <td>$data[5]</td>
              <td>$arenateam_online</td>
              <td>$data[6]</td>
              <td>$data[7]</td>
              <td>$data[8]</td>
            </tr>";
  }
  $output .= "
            <tr><td colspan=\"6\" class=\"hidden\" align=\"right\">{$lang_arenateam['tot_teams']} : $all_record</td></tr>
          </table>
        </center>";

}


function count_days( $a, $b ) {
  $gd_a = getdate( $a );
  $gd_b = getdate( $b );
  $a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
  $b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
  return round( abs( $a_new - $b_new ) / 86400 );
}

//########################################################################################################################
// VIEW ARENA TEAM
//########################################################################################################################
function view_team()
{
  global $lang_arenateam, $lang_global, $output, $characters_db, $realm_id, $user_lvl;

  if(!isset($_GET['id'])) redirect("arenateam.php?error=1");

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $arenateam_id = $sqlc->quote_smart($_GET['id']);

  $query = $sqlc->query("SELECT arenateamid, name, type FROM arena_team WHERE arenateamid = '$arenateam_id'");
  $arenateam_data = $sqlc->fetch_row($query);

  $query = $sqlc->query("SELECT arenateamid, rating, games, wins, played, wins2, rank FROM arena_team_stats WHERE arenateamid = '$arenateam_id'");
  $arenateamstats_data = $sqlc->fetch_row($query);

  $rating_offset = 1550;
  if ($arenateam_data[2] == 3)
  $rating_offset += 6;
  else if ($arenateam_data[2] == 5)
  $rating_offset += 12;

  $members = $sqlc->query("SELECT arena_team_member.guid,`characters`.name,
    SUBSTRING_INDEX(SUBSTRING_INDEX(`characters`.`data`, ' ', $rating_offset), ' ', -1) AS personal_rating,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
    arena_team_member.played_week, arena_team_member.wons_week, arena_team_member.played_season, arena_team_member.wons_season
    FROM arena_team_member,`characters`
    LEFT JOIN arena_team_member k1 ON k1.`guid`=`characters`.`guid` AND k1.`arenateamid`='$arenateam_id'
    WHERE arena_team_member.arenateamid = '$arenateam_id' AND arena_team_member.guid=`characters`.guid
    ORDER BY `characters`.`name`");

  $total_members = $sqlc->num_rows($members);
  $losses_week = $arenateamstats_data[2]-$arenateamstats_data[3];
  if($arenateamstats_data[2])
    $winperc_week = round((10000 * $arenateamstats_data[3]) / $arenateamstats_data[2]) / 100;
  else
    $winperc_week = $arenateamstats_data[2];
  $losses_season = $arenateamstats_data[4]-$arenateamstats_data[5];
  if($arenateamstats_data[4])
    $winperc_season = round((10000 * $arenateamstats_data[5]) / $arenateamstats_data[4]) / 100;
  else
    $winperc_season = $arenateamstats_data[4];
  $output .= "
        <script type=\"text/javascript\">
          answerbox.btn_ok='{$lang_global['yes_low']}';
          answerbox.btn_cancel='{$lang_global['no']}';
        </script>
        <center>
          <fieldset style=\"width: 720px;\">
            <legend>{$lang_arenateam['arenateam']} ({$arenateam_data[2]}v{$arenateam_data[2]})</legend>
            <table class=\"lined\" style=\"width: 720px;\">
              <tr class=\"bold\">
                <td colspan=\"13\">".htmlentities($arenateam_data[1])."</td>
              </tr>
              <tr>
                <td colspan=\"13\">{$lang_arenateam['tot_members']}: $total_members</td>
              </tr>
              <tr>
                <td colspan=\"4\">{$lang_arenateam['this_week']}</td>
                <td colspan=\"2\">{$lang_arenateam['games_played']} : $arenateamstats_data[2]</td>
                <td colspan=\"2\">{$lang_arenateam['games_won']} : $arenateamstats_data[3]</td>
                <td colspan=\"2\">{$lang_arenateam['games_lost']} : $losses_week</td>
                <td colspan=\"3\">{$lang_arenateam['ratio']} : $winperc_week %</td>
              </tr>
              <tr>
                <td colspan=\"4\">{$lang_arenateam['this_season']}</td>
                <td colspan=\"2\">{$lang_arenateam['games_played']} : $arenateamstats_data[4]</td>
                <td colspan=\"2\">{$lang_arenateam['games_won']} : $arenateamstats_data[5]</td>
                <td colspan=\"2\">{$lang_arenateam['games_lost']} : $losses_season</td>
                <td colspan=\"3\">{$lang_arenateam['ratio']} : $winperc_season %</td>
              </tr>
              <tr>
                <td colspan=\"13\">{$lang_arenateam['standings']} {$arenateamstats_data[6]} ({$arenateamstats_data[1]})</td>
              </tr>
              <tr>";
  if ($user_lvl > 2)
  {
    $output .= "
                <th width=\"1%\">{$lang_arenateam['remove']}</th>";
       }
    $output .= "
                <th width=\"1%\">{$lang_arenateam['name']}</th>
                <th width=\"1%\">Race</th>
                <th width=\"1%\">Class</th>
                <th width=\"1%\">Personal Rating</th>
                <th width=\"1%\">Last Login (Days)</th>
                <th width=\"1%\">Online</th>
                <th width=\"1%\">{$lang_arenateam['played_week']}</th>
                <th width=\"1%\">{$lang_arenateam['wons_week']}</th>
                <th width=\"1%\">Win %</th>
                <th width=\"1%\">{$lang_arenateam['played_season']}</th>
                <th width=\"1%\">{$lang_arenateam['wons_season']}</th>
                <th width=\"1%\">Win %</th>
              </tr>";

    while ($member = $sqlc->fetch_row($members))
    {
      $query = $sqlc->query("SELECT `race`,`class`,`online`, `account`, `logout_time`,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS level,
        mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender
        FROM `characters`
        WHERE `guid` = '$member[0]';");
      $online = $sqlc->fetch_row($query);
      $accid = $online[3];
      $output .= "
              <tr>";
      if ($user_lvl > 2)
      {
        $output .= "
                <td><img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('{$lang_global['delete']}: <font color=white>{$member[1]}</font><br />{$lang_global['are_you_sure']}', 'arenateam.php?action=rem_char_from_team&amp;id=$member[0]&amp;arenateam_id=$arenateam_id');\" style=\"cursor:pointer;\" /></td>";
      }
      if($member[4])
        $ww_pct = round((10000 * $member[5]) / $member[4]) / 100;
      else
        $ww_pct = $member[4];
      if($member[6])
        $ws_pct = round((10000 * $member[7]) / $member[6]) / 100;
      else
        $ws_pct = $member[6];
      $output .= "
                <td><a href=\"char.php?id=$member[0]\">".htmlentities($member[1])."</a></td>
                <td><img src='img/c_icons/{$online[0]}-{$online[6]}.gif' onmousemove='toolTip(\"".get_player_race($online[0])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
                <td><img src='img/c_icons/{$online[1]}.gif' onmousemove='toolTip(\"".get_player_class($online[1])."\",\"item_tooltip\")' onmouseout='toolTip()' /></td>
                <td>$member[2]</td>
                <td>".get_days_with_color($online[4])."</td>
                <td>".(($online[2]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
                <td>$member[4]</td>
                <td>$member[5]</td>
                <td>$ww_pct %</td>
                <td>$member[6]</td>
                <td>$member[7]</td>
                <td>$ws_pct %</td>
              </tr>";
    }
    $output .= "
            </table>
            <br />
            <table class=\"hidden\">
              <tr>
                <td>";
    if ($user_lvl > 2)
    {
      makebutton($lang_arenateam['del_team'], "arenateam.php?action=del_team&amp;id=$arenateam_id\" type=\"wrn", 180);
      $output .= "
                </td>
                <td>";
                  makebutton($lang_arenateam['arenateams'], "arenateam.php\" type=\"def", 130);
    $output .= "
                </td>
              </tr>
              <tr>
              </tr>";
    }
    else
    {
    makebutton($lang_arenateam['arenateams'], "arenateam.php", 130);
    $output .= "
                </td>
              </tr>";
    }
    $output .= "
            </table>
          </fieldset>
          <br />
        </center>
";

}


//########################################################################################################################
// ARE YOU SURE  YOU WOULD LIKE TO OPEN YOUR AIRBAG?
//########################################################################################################################
function del_team()
{
  global $lang_arenateam, $lang_global, $output;

  if(isset($_GET['id'])) $id = $_GET['id'];
  else redirect("arenateam.php?error=1");

  $output .= "
        <center>
          <h1><font class=\"error\">{$lang_global['are_you_sure']}</font></h1>
          <br />
          <font class=\"bold\">{$lang_arenateam['arenateam_id']}: $id {$lang_global['will_be_erased']}</font><br /><br />
          <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
            <input type=\"hidden\" name=\"type\" value=\"arenateam\" />
            <input type=\"hidden\" name=\"check\" value=\"-$id\" />
            <table class=\"hidden\">
              <tr>
                <td>";
                  makebutton($lang_global['yes'], "javascript:do_submit()",130);
                  makebutton($lang_global['no'], "arenateam.php?action=view_team&amp;id=$id",130);
  $output .= "
                </td>
              </tr>
            </table>
          </form>
          <br />
        </center>
";

}


//##########################################################################################
//REMOVE CHAR FROM TEAM
//##########################################################################################
function rem_char_from_team()
{
  global $characters_db, $realm_id, $user_lvl;

  if(isset($_GET['id'])) $guid = $_GET['id'];
    else redirect("arenateam.php?error=1");
  if(isset($_GET['arenateam_id'])) $arenateam_id = $_GET['arenateam_id'];
    else redirect("arenateam.php?error=1");

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    // must be checked that this user can delete it
  //$sql->query("DELETE FROM arena_team_member WHERE guid = '$guid'");

  redirect("arenateam.php?action=view_team&id=$arenateam_id");
}

//########################################################################################################################
// MAIN
//########################################################################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= "
        <div class=\"top\">";

switch ($err)
{
  case 1:
    $output .= "
          <h1><font class=\"error\">{$lang_global['empty_fields']}</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">{$lang_global['err_no_search_passed']}</font></h1>";
     break;
 case 3:
    $output .= "
          <h1><font class=\"error\">{$lang_arenateam['arenateam']}</font></h1>";
    break;
  case 4:
    $output .= "
          <h1>{$lang_arenateam ['team_search_result']}:</h1>";
    break;
  default: //no error
    $output .= "
          <h1>{$lang_arenateam ['browse_teams']}</h1>";
}

$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "browse_teams":
    browse_teams();
    break;
  case "search":
    search();
    break;
  case "view_team":
    view_team();
    break;
  case "del_team":
    del_team();
    break;
  case "rem_char_from_team":
    rem_char_from_team();
    break;
  default:
    browse_teams();
}

require_once("footer.php");

?>
