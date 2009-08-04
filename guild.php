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
require_once("scripts/get_lib.php");

//########################################################################################################################
// BROWSE GUILDS
//########################################################################################################################
function browse_guilds()
{
  global $lang_guild, $lang_global, $output, $realm_db, $characters_db, $realm_id, $itemperpage, $search_by, $search_value,
    $action_permission, $user_lvl, $user_id;
  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  //==========================$_GET and SECURE========================
  $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $start)) $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "gid";
  if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by)) $order_by="gid";

  $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================
  //==========================MyGuild========================

  $query_myGuild = $sql->query("SELECT g.guildid as gid, g.name, g.leaderguid AS lguid, 
  (SELECT name from characters where guid = lguid), (SELECT race in (2,5,6,8,10) from characters where guid = lguid) as faction, 
  (select count(*) from characters where guid in (select guid from guild_member where guildid = lguid) and online = 1) as gonline, 
  (select count(*) from guild_member where guildid = gid), SUBSTRING_INDEX(g.MOTD,' ',6), g.createdate, 
  (select account from characters where guid = lguid) FROM guild as g
  left outer join guild_member as gm on gm.guildid = g.guildid left outer join characters as c on c.guid = gm.guid
  where c.account = $user_id group by g.guildid order by gid");

  if ($query_myGuild)
  {
    $output .= "
        <center>
          <fieldset>
            <legend>{$lang_guild['my_guilds']}</legend>
            <table class=\"lined\" align=\"center\">
              <tr>
                <th width=\"5%\">{$lang_guild['id']}</th>
                <th width=\"25%\">{$lang_guild['guild_name']}</th>
                <th width=\"15%\">{$lang_guild['guild_leader']}</th>
                <th width=\"10%\">{$lang_guild['guild_faction']}</th>
                <th width=\"10%\">{$lang_guild['tot_m_online']}</th>
                <th width=\"20%\">{$lang_guild['guild_motd']}</th>
                <th width=\"15%\">{$lang_guild['create_date']}</th>
              </tr>";
    $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
    while ($data = $sql->fetch_row($query_myGuild))
    {
      $result = $sql->query("SELECT gmlevel FROM account WHERE id ='$data[9]'");
      $owner_gmlvl = $sql->result($result, 0, 'gmlevel');
      $output .= "
              <tr>
                <td>$data[0]</td>
                <td><a href=\"guild.php?action=view_guild&id=$data[0]\">$data[1]</a></td>";
      $output .= ($user_lvl < $owner_gmlvl ) ? "<td>".htmlentities($data[3])."</td>" : "<td><a href=\"char.php?id=$data[2]\">".htmlentities($data[3])."</a></td>";
      $output .= "
                <td><img src=\"img/".($data[4]==0 ? "alliance" : "horde")."_small.gif\" /></td>
                <td>$data[5]/$data[6]</td>
                <td>".htmlentities($data[7])." ...</td>
                <td class=\"small\">$data[8]</td>
              </tr>";
    }
    $output .= "
            </table>
          </fieldset>
        </center>
        <br />";
    $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
  }
  //==========================MyGuild end========================
  //==========================Browse/Search Guilds CHECK========================
  if(isset($_GET['search_value']) && isset($_GET['search_by']))
  {
    $search_by = $sql->quote_smart($_GET['search_by']);
    $search_value = $sql->quote_smart($_GET['search_value']);

    $search_menu = array("name", "leadername", "guildid");
    if (!in_array($search_by, $search_menu)) $search_by = 'name';

    switch($search_by)
    {
      case "name":
        if (preg_match('/^[\t\v\b\f\a\n\r\\\"\'\? <>[](){}_=+-|!@#$%^&*~`.,0123456789\0]{1,30}$/', $search_value)) redirect("guild.php?error=5");
        $query = $sql->query("SELECT g.guildid as gid, g.name,g.leaderguid as lguid, (SELECT name from characters where guid = lguid) as lname, c.race in (2,5,6,8,10) as lfaction, (select count(*) from guild_member where guildid = gid) as tot_chars, createdate, c.account as laccount FROM guild as g left outer join characters as c on c.guid = g.leaderguid where g.name like '%$search_value%' ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
        $query_count = $sql->query("SELECT 1 from guild where name like '%$search_value%'");
        break;
      case "leadername" :
        if (preg_match('/^[\t\v\b\f\a\n\r\\\"\'\? <>[](){}_=+-|!@#$%^&*~`.,0123456789\0]{1,30}$/', $search_value)) redirect("guild.php?error=5");
        $query = $sql->query("SELECT g.guildid as gid, g.name,g.leaderguid as lguid, (SELECT name from characters where guid = lguid) as lname, c.race in (2,5,6,8,10) as lfaction, (select count(*) from guild_member where guildid = gid) as tot_chars, createdate, c.account as laccount FROM guild as g left outer join characters as c on c.guid = g.leaderguid where g.leaderguid in (SELECT guid from characters where name like '%$search_value%') ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
        $query_count = $sql->query("SELECT 1 from guild where leaderguid in (select guid from characters where name like '%$search_value%')");
        break;
      case "guildid" :
        if (!preg_match('/^[[:digit:]]{1,12}$/', $search_value)) redirect("guild.php?error=5");
        $query = $sql->query("SELECT g.guildid as gid, g.name,g.leaderguid as lguid, (SELECT name from characters where guid = lguid) as lname, c.race in (2,5,6,8,10) as lfaction, (select count(*) from guild_member where guildid = gid) as tot_chars, createdate, c.account as laccount FROM guild as g left outer join characters as c on c.guid = g.leaderguid where g.guildid = '$search_value' ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
        $query_count = $sql->query("SELECT 1 from guild where guildid = '$search_value'");
        break;
      default : 
        redirect("guild.php?error=2");
    }
  }
  else
  {
    $query = $sql->query("SELECT g.guildid as gid, g.name,g.leaderguid as lguid, (SELECT name from characters where guid = lguid) as lname, c.race in (2,5,6,8,10) as lfaction, (select count(*) from guild_member where guildid = gid) as tot_chars, createdate, c.account as laccount FROM guild as g left outer join characters as c on c.guid = g.leaderguid ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");
    $query_count = $sql->query("SELECT 1 from guild");
  }
  $all_record = $sql->num_rows($query_count);
  //==========================Browse/Search Guilds CHECK end========================
  //==========================Browse/Search Guilds========================

  $output .="
        <table class=\"top_hidden\" align=\"center\">
          <tr>
            <td width =\"200\">";
  ($search_by &&  $search_value) ? makebutton($lang_guild['show_guilds'], "guild.php\" type=\"def", 130) : $output .= "";
  $output .= "
            </td>
            <td align=\"right\">
              <form action=\"guild.php\" method=\"get\" name=\"form\">
                <input type=\"hidden\" name=\"action\" value=\"browse_guilds\" />
                <input type=\"hidden\" name=\"error\" value=\"4\" />
                <input type=\"text\" size=\"42\" name=\"search_value\" value=\"{$search_value}\" />
                <select name=\"search_by\">
                  <option value=\"name\"".($search_by == 'name' ? " selected=\"selected\"" : "").">{$lang_guild['by_name']}</option>
                  <option value=\"leadername\"".($search_by == 'leadername' ? " selected=\"selected\"" : "").">{$lang_guild['by_guild_leader']}</option>
                  <option value=\"guildid\"".($search_by == 'guildid' ? " selected=\"selected\"" : "").">{$lang_guild['by_id']}</option>
                </select>
              </form>
            </td>
            <td>";
              makebutton($lang_global['search'], "javascript:do_submit()",130);
  $output .= "
            </td>
          </tr>
          <tr>
            <td colspan=\"3\" align=\"right\">";
  $output .= generate_pagination("guild.php?action=brows_guilds&amp;order_by=$order_by&amp;".($search_value && $search_by ? "search_by=$search_by&amp;search_value=$search_value&amp" : "")."dir=".!$dir, $all_record, $itemperpage, $start);
  $output .= "
            </td>
          </tr>
        </table>";
  //==========================top tage navigaion ENDS here ========================
  $output .= "
        <center>
          <fieldset>
            <legend>{$lang_guild['browse_guilds']}</legend>
              <table class=\"lined\" align=\"center\">
                <tr>
                  <th width=\"5%\"><a href=\"guild.php?order_by=gid&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='gid' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['id']}</a></th>
                  <th width=\"30%\"><a href=\"guild.php?order_by=name&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='name' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['guild_name']}</a></th>
                  <th width=\"20%\"><a href=\"guild.php?order_by=lname&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='lname' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['guild_leader']}</a></th>
                  <th width=\"10%\"><a href=\"guild.php?order_by=lfaction&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='lfaction' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['guild_faction']}</a></th>
                  <th width=\"15%\"><a href=\"guild.php?order_by=tot_chars&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='tot_chars' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['tot_members']}</a></th>
                  <th width=\"20%\"><a href=\"guild.php?order_by=createdate&amp;start=$start&amp;dir=$dir".( $search_value && $search_by ? "&amp;search_by=$search_by&amp;search_value=$search_value" : "" )."\">".($order_by=='createdate' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['create_date']}</a></th>
                </tr>";
  $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  while ($data = $sql->fetch_row($query))
  {
    $result = $sql->query("SELECT gmlevel FROM account WHERE id ='$data[7]'");
    $owner_gmlvl = $sql->result($result, 0, 'gmlevel');
    $output .= "
                <tr>
                  <td>$data[0]</td>";
    $output .= ($user_lvl >= $action_permission['update']) ? "<td><a href=\"guild.php?action=view_guild&amp;error=3&amp;id=$data[0]\">".htmlentities($data[1])."</td>" : "<td>".htmlentities($data[1])."</td>";
    $output .= ($user_lvl < $owner_gmlvl ) ? "<td>".htmlentities($data[3])."</td>" : "<td><a href=\"char.php?id=$data[2]\">".htmlentities($data[3])."</a></td>";
    $output .= "
                  <td><img src=\"img/".($data[4]==0 ? "alliance" : "horde")."_small.gif\" /></td>
                  <td>$data[5]</td>
                  <td class=\"small\">".htmlentities($data[6])."</td>
                </tr>";
  }
  $output .= "
                <tr>
                  <td colspan=\"6\" class=\"hidden\" align=\"right\">".generate_pagination("guild.php?action=brows_guilds&amp;order_by=$order_by&amp;".($search_value && $search_by ? "search_by=$search_by&amp;search_value=$search_value&amp" : "")."dir=".!$dir, $all_record, $itemperpage, $start)."</td>
                </tr>
                <tr>
                  <td colspan=\"6\" class=\"hidden\" align=\"right\">{$lang_guild['tot_guilds']} : $all_record</td>
                </tr>
              </table>
            </fieldset>
          </center>
          <br />";
  //==========================Browse/Search Guilds end========================

  $sql->close();
}


function count_days( $a, $b )
{
  $gd_a = getdate( $a );
  $gd_b = getdate( $b );
  $a_new = mktime( 12, 0, 0, $gd_a['mon'], $gd_a['mday'], $gd_a['year'] );
  $b_new = mktime( 12, 0, 0, $gd_b['mon'], $gd_b['mday'], $gd_b['year'] );
  return round( abs( $a_new - $b_new ) / 86400 );
}


//########################################################################################################################
// VIEW GUILD
//########################################################################################################################
function view_guild()
{
  global $lang_guild, $lang_global, $output, $realm_db, $characters_db, $realm_id, $itemperpage,
    $action_permission, $user_lvl, $user_id;
  if(!isset($_GET['id'])) redirect("guild.php?error=1");

  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  $guild_id = $sql->quote_smart($_GET['id']);
  if(!preg_match("/^[[:digit:]]{1,10}$/", $guild_id)) redirect("guild.php?error=6");

  //==========================SQL INGUILD and GUILDLEADER========================
  $q_inguild = $sql->query("select 1 from guild_member where guildid = '$guild_id' and guid in (select guid from characters where account = '$user_id')");
  $inguild = $sql->result($q_inguild, 0, '1');
  if ( $user_lvl < $action_permission['update'] && !$inguild )
    redirect("guild.php?error=6");

  $q_amIguildleader = $sql->query("select 1 from guild where guildid = '$guild_id' and leaderguid in (select guid from characters where account = '$user_id')");
  $amIguildleader = $sql->result($q_amIguildleader, 0, '1');

  $q_guildmemberCount = $sql->query("SELECT 1 from guild_member where guildid = '$guild_id'");
  $guildmemberCount = $sql->num_rows($q_guildmemberCount);
  //==========================SQL INGUILD and GUILDLEADER end========================

  //==========================$_GET and SECURE========================
  $start = (isset($_GET['start'])) ? $sql->quote_smart($_GET['start']) : 0;
  if (!preg_match("/^[[:digit:]]{1,5}$/", $start)) $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sql->quote_smart($_GET['order_by']) : "mrank";
  if (!preg_match("/^[_[:lower:]]{1,10}$/", $order_by)) $order_by="mrank";

  $dir = (isset($_GET['dir'])) ? $sql->quote_smart($_GET['dir']) : 1;
  if (!preg_match("/^[01]{1}$/", $dir)) $dir=1;

  $order_dir = ($dir) ? "ASC" : "DESC";
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================

  require_once("scripts/defines.php");

  $query = $sql->query("SELECT guildid, name, info, MOTD, createdate, (select count(*) from guild_member where guildid = '$guild_id') as mtotal, (select count(*) from guild_member where guildid = '$guild_id' and guid in (select guid from characters where online = 1)) as monline FROM guild WHERE guildid = '$guild_id'");
  $guild_data = $sql->fetch_row($query);

  $output .= "
        <script type=\"text/javascript\">
          answerbox.btn_ok='{$lang_global['yes']}';
          answerbox.btn_cancel='{$lang_global['no']}';
        </script>
        <center>
          <fieldset>
            <legend>{$lang_guild['guild']}</legend>
            <table class=\"hidden\" style=\"width: 100%;\">
              <tr>
                <td>
                  <table class=\"lined\">
                    <tr>
                      <td width=\"25%\"><b>{$lang_guild['create_date']}:</b><br>$guild_data[4]</td>
                      <td width=\"50%\" class=\"bold\">$guild_data[1]</td>
                      <td width=\"25%\"><b>{$lang_guild['tot_m_online']}:</b><br>$guild_data[6] / $guild_data[5]</td>
                    </tr>";
  if ($guild_data[2] != '')
    $output .= "
                    <tr>
                      <td colspan=\"3\"><b>{$lang_guild['info']}:</b><br>$guild_data[2]</td>
                    </tr>";
  if ($guild_data[3] != '')
    $output .= "
                    <tr>
                      <td colspan=\"3\"><b>{$lang_guild['motd']}:</b><br>$guild_data[3]</td>
                    </tr>";
  $output .="
                  </table>
                </td>
              </tr>
              <div align=\"right\">".generate_pagination("guild.php?action=view_guild&amp;id=$guild_id&amp;order_by=$order_by&amp;dir=".!$dir, $guildmemberCount, $itemperpage, $start)."</div>
              <tr>
                <td>
                  <table class=\"lined\">
                    <tr>
                      <th width=\"3%\">{$lang_guild['remove']}</th>
                      <th width=\"21%\"><a href=\"guild.php?action=view_guild&amp;id=$guild_id&amp;order_by=cname&amp;start=$start&amp;dir=$dir\">".($order_by=='cname' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['name']}</a></th>
                      <th width=\"3%\"><a href=\"guild.php?action=view_guild&amp;id=$guild_id&amp;order_by=crace&amp;start=$start&amp;dir=$dir\">".($order_by=='crace' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['race']}</a></th>
                      <th width=\"3%\"><a href=\"guild.php?action=view_guild&amp;id=$guild_id&amp;order_by=class&amp;start=$start&amp;dir=$dir\">".($order_by=='cclass' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['class']}</a></th>
                      <th width=\"3%\"><a href=\"guild.php?action=view_guild&amp;id=$guild_id&amp;order_by=clevel&amp;start=$start&amp;dir=$dir\">".($order_by=='clevel' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['level']}</a></th>
                      <th width=\"21%\"><a href=\"guild.php?action=view_guild&amp;id=$guild_id&amp;order_by=mrank&amp;start=$start&amp;dir=$dir\">".($order_by=='mrank' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['rank']}</a></th>
                      <th width=\"14%\">{$lang_guild['pnote']}</th>
                      <th width=\"14%\">{$lang_guild['offnote']}</th>
                      <th width=\"15%\"><a href=\"guild.php?action=view_guild&amp;id=$guild_id&amp;order_by=clogout&amp;start=$start&amp;dir=$dir\">".($order_by=='clogout' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['llogin']}</a></th>
                      <th width=\"3%\"><a href=\"guild.php?action=view_guild&amp;id=$guild_id&amp;order_by=conline&amp;start=$start&amp;dir=$dir\">".($order_by=='conline' ? "<img src=\"img/arr_".($dir ? "up" : "dw").".gif\" /> " : "")."{$lang_guild['online']}</a></th>
                    </tr>";
  $members = $sql->query("SELECT gm.guid as cguid, c.name as cname, c.`race` as crace ,c.`class` as cclass,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(c.`data`, ' ', ".(CHAR_DATA_OFFSET_LEVEL+1)."), ' ', -1) AS UNSIGNED) AS clevel,
    gm.rank AS mrank, (SELECT rname FROM guild_rank WHERE guildid ='$guild_id' AND rid = mrank+1) AS rname,
    gm.Pnote, gm.OFFnote,
    mid(lpad( hex( CAST(substring_index(substring_index(data,' ',".(CHAR_DATA_OFFSET_GENDER+1)."),' ',-1) as unsigned) ),8,'0'),4,1) as gender,
    c.`online` as conline, c.`account`, c.`logout_time` as clogout
    FROM guild_member as gm left outer join characters as c on c.guid = gm.guid
    WHERE gm.guildid = '$guild_id' ORDER BY $order_by $order_dir LIMIT $start, $itemperpage");

  $sql->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
  while ($member = $sql->fetch_row($members))
  {
    $result = $sql->query("SELECT gmlevel FROM account WHERE id ='$member[11]'");
    $owner_gmlvl = $sql->result($result, 0, 'gmlevel');
    $output .= "
                    <tr>";
    // gm, gildleader or own account! are allowed to remove from guild
    $output .= ($user_lvl >= $action_permission['delete'] || $amIguildleader || $member[11] == $user_id) ? "
                      <td>
                        <img src=\"img/aff_cross.png\" alt=\"\" onclick=\"answerBox('{$lang_global['delete']}: <font color=white>{$member[1]}</font><br />{$lang_global['are_you_sure']}', 'guild.php?action=rem_char_from_guild&amp;id=$member[0]&amp;guld_id=$guild_id');\" style=\"cursor:pointer;\" />
                      </td>" : "
                      <td>
                      </td>";
    $output .= ($user_lvl < $owner_gmlvl ) ? "
                      <td>".htmlentities($member[1])."</td>" : "
                      <td><a href=\"char.php?id=$member[0]\">".htmlentities($member[1])."</a></td>";
    $output .= "
                      <td><img src='img/c_icons/{$member[2]}-{$member[9]}.gif' onmousemove='toolTip(\"".get_player_race($member[2])."\",\"item_tooltip\")' onmouseout='toolTip()'/></td>
                      <td><img src='img/c_icons/{$member[3]}.gif' onmousemove='toolTip(\"".get_player_class($member[3])."\",\"item_tooltip\")' onmouseout='toolTip()'/></td>
                      <td>".get_level_with_color($member[4])."</td>
                      <td>".htmlentities($member[6])." (".$member[5].")</td>
                      <td>".htmlentities($member[7])."</td>
                      <td>".htmlentities($member[8])."</td>
                      <td>".get_days_with_color($member[12])."</td>
                      <td>".(($member[10]) ? "<img src=\"img/up.gif\" alt=\"\" />" : "-")."</td>
                    </tr>";
  }
  $output .= "
                  </table>
                </td>
              </tr>
            </table>
            <br />";
  $sql->close();
  $output .= "
            <table class=\"hidden\">
              <tr>
                <td>";
                  makebutton($lang_guild['show_guilds'], "guild.php\" type=\"def", 130);
  if ($user_lvl >= $action_permission['delete'] || $amIguildleader)
  {
    $output .= "
                </td>
                <td>";
                  makebutton($lang_guild['del_guild'], "guild.php?action=del_guild&amp;id=$guild_id\" type=\"wrn", 130);
  }
  $output .= "
                </td>
              </tr>
            </table>
          </fieldset>
        </center>
";
}

//########################################################################################################################
// ARE YOU SURE  YOU WOULD LIKE TO OPEN YOUR AIRBAG?
//########################################################################################################################
function del_guild()
{
  global $lang_guild, $lang_global, $output, $characters_db, $realm_id,
    $action_permission, $user_lvl, $user_id;
  if(isset($_GET['id']))
    $id = $_GET['id'];
  else
    redirect("guild.php?error=1");
  if (!preg_match('/^[[:digit:]]{1,12}$/', $id))
    redirect("guild.php?error=5");
  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
  $q_amIguildleader = $sql->query("select 1 from guild where guildid = '$id' and leaderguid in (select guid from characters where account = '$user_id')");
  $amIguildleader = $sql->result($q_amIguildleader, 0, '1');
  if ($user_lvl < $action_permission['delete'] && !$amIguildleader)
    redirect("guild.php?error=6");
  $output .= "
        <center>
          <h1><font class=\"error\">{$lang_global['are_you_sure']}</font></h1>
          <br />
          <font class=\"bold\">{$lang_guild['guild_id']}: $id {$lang_global['will_be_erased']}</font>
          <br /><br />
          <form action=\"cleanup.php?action=docleanup\" method=\"post\" name=\"form\">
            <input type=\"hidden\" name=\"type\" value=\"guild\" />
            <input type=\"hidden\" name=\"check\" value=\"-$id\" />
            <input type=\"hidden\" name=\"override\" value=\"1\" />
            <table class=\"hidden\">
              <tr>
                <td>";
                  makebutton($lang_global['yes'], "javascript:do_submit()\" type=\"wrn",130);
  $output .= "
                </td>
                <td>";
                  makebutton($lang_global['no'], "guild.php?action=view_guild&amp;id=$id\" type=\"def",130);
  $output .= "
                </td>
              </tr>
            </table>
          </form>
        </center>
        <br />
";

  $sql->close();
}


//##########################################################################################
//REMOVE CHAR FROM GUILD
function rem_char_from_guild()
{
  global $characters_db, $realm_id, $user_lvl, $user_id;

  require_once("scripts/defines.php");

  if(isset($_GET['id']))
    $guid = $_GET['id'];
  else
    redirect("guild.php?error=1");
  if (!preg_match('/^[[:digit:]]{1,12}$/', $guid))
    redirect("guild.php?error=5");
  if(isset($_GET['guld_id']))
    $guld_id = $_GET['guld_id'];
  else
    redirect("guild.php?error=1");
  if (!preg_match('/^[[:digit:]]{1,12}$/', $guld_id))
    redirect("guild.php?error=5");
  $sql = new SQL;
  $sql->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);
  $q_amIguildleaderOrSelfRemoval = $sql->query("select 1 from guild as g left outer join guild_member as gm on gm.guildid = g.guildid
                                   where g.guildid = '$guld_id' and
                                   (g.leaderguid in (select guid from characters where account = '$user_id')
                                   or gm.guid in (select guid from characters where account = '$user_id' and guid = '$guid'))");
  $amIguildleaderOrSelfRemoval = $sql->result($q_amIguildleaderOrSelfRemoval, 0, '1');
  if ($user_lvl < $action_permission['delete'] && !$amIguildleaderOrSelfRemoval )
    redirect("guild.php?error=6");
  $char_data = $sql->query("SELECT data FROM `characters` WHERE guid = '$guid'");
  $data = $sql->result($char_data, 0, 'data');
  $data = explode(' ',$data);
  $data[CHAR_DATA_OFFSET_GUILD_ID] = 0;
  $data[CHAR_DATA_OFFSET_GUILD_RANK] = 0;
  $data = implode(' ',$data);
  $sql->query("UPDATE `characters` SET data = '$data' WHERE guid = '$guid'");
  $sql->query("DELETE FROM guild_member WHERE guid = '$guid'");
  $sql->close();
  redirect("guild.php?action=view_guild&id=$guld_id");
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
          <h1><font class=\"error\">{$lang_global['err_empty_fields']}</font></h1>";
    break;
  case 2:
    $output .= "
          <h1><font class=\"error\">{$lang_global['err_no_search_passed']}</font></h1>";
    break;
  case 3: //keep blank
    break;
  case 4:
    $output .= "
          <h1>{$lang_guild['guild_search_result']}:</h1>";
    break;
  case 5:
    $output .= "
          <h1>{$lang_global['err_invalid_input']}:</h1>";
    break;
  case 6:
    $output .= "
          <h1>{$lang_global['err_no_permission']}:</h1>";
    break;
  default: //no error
    $output .= "
          <h1>{$lang_guild['browse_guilds']}</h1>";
}
$output .= "
        </div>";

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "browse_guilds":
    browse_guilds();
    break;
  case "view_guild":
    view_guild();
    break;
  case "del_guild":
    del_guild();
    break;
  case "rem_char_from_guild":
    rem_char_from_guild();
    break;
  default:
    browse_guilds();
}

require_once("footer.php");

?>
