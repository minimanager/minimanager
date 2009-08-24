<?php


require_once 'header.php';
require_once 'libs/bbcode_lib.php';
require_once 'libs/char_lib.php';
require_once 'libs/map_zone_lib.php';
valid_login($action_permission['read']);

//#############################################################################
// MINIMANAGER FRONT PAGE
//#############################################################################
function front(&$sqlr, &$sqlc)
{
  global $output, $lang_global, $lang_index,
    $realm_id, $realm_db, $world_db, $characters_db, $mmfpm_db, $server,
    $action_permission, $user_lvl, $user_id,
    $server_type, $showcountryflag, $motd_display_poster, $gm_online_count, $gm_online ;

  $output .= '
          <div class="top">';

  if (test_port($server[$realm_id]['addr'],$server[$realm_id]['game_port']))
  {
    $stats = $sqlr->fetch_assoc($sqlr->query('SELECT starttime, maxplayers FROM uptime WHERE realmid = '.$realm_id.' ORDER BY starttime DESC LIMIT 1'), 0);
    $uptimetime = time() - $stats['starttime'];

    function format_uptime($seconds)
    {
      $secs  = intval($seconds % 60);
      $mins  = intval($seconds / 60 % 60);
      $hours = intval($seconds / 3600 % 24);
      $days  = intval($seconds / 86400);

      $uptimeString='';

      if ($days)
      {
        $uptimeString .= $days;
        $uptimeString .= ((1 === $days) ? ' day' : ' days');
      }
      if ($hours)
      {
        $uptimeString .= ((0 < $days) ? ', ' : '').$hours;
        $uptimeString .= ((1 === $hours) ? ' hour' : ' hours');
      }
      if ($mins)
      {
        $uptimeString .= ((0 < $days || 0 < $hours) ? ', ' : '').$mins;
        $uptimeString .= ((1 === $mins) ? ' minute' : ' minutes');
      }
      if ($secs)
      {
        $uptimeString .= ((0 < $days || 0 < $hours || 0 < $mins) ? ', ' : '').$secs;
        $uptimeString .= ((1 === $secs) ? ' second' : ' seconds');
      }
      return $uptimeString;
    }

    $staticUptime = $lang_index['realm'].' <em>'.htmlentities(get_realm_name($realm_id)).'</em> '.$lang_index['online'].' for '.format_uptime($uptimetime);
    unset($uptimetime);
    $output .= '
            <div id="uptime">
              <h1><font color="#55aa55">'.$staticUptime.'<br />'.$lang_index['maxplayers'].': '.$stats['maxplayers'].'</font></h1>
            </div>';
    unset($staticUptime);
    unset($stats);
    $online = true;
  }
  else
  {
    $output .= '
            <h1><font class="error">'.$lang_index['realm'].' <em>'.htmlentities(get_realm_name($realm_id)).'</em> '.$lang_index['offline_or_let_high'].'</font></h1>';
    $online = false;
  }

  $sqlw = new SQL;
  $sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

  //  This retrieves the actual database version from the database itself, instead of hardcoding it into a string
  if ($server_type)
  {
    $version = $sqlw->fetch_assoc($sqlw->query('SELECT core_revision, db_version FROM version'), 0);
    $output .= $lang_index['trinity_rev'].' '.$version['core_revision'].' '.$lang_index['using_db'].' '.$version['db_version'].'</div>';
  }
  else
  {
    $version = $sqlw->fetch_assoc($sqlw->query('SELECT version FROM db_version'));
    $output .= 'Mangos: '.$server[$realm_id]['rev'].' '.$lang_index['using_db'].' '.$version['version'].'</div>';
  }
  unset($version);

  //MOTD part
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $all_record = $sqlc->result($sqlc->query('SELECT count(*) FROM bugreport'), 0);

  if ($user_lvl >= $action_permission['delete'])
    $output .= '
            <script type="text/javascript">
              answerbox.btn_ok="'.$lang_global['yes_low'].'";
              answerbox.btn_cancel="'.$lang_global['no'].'";
              var del_motd = "motd.php?action=delete_motd&amp;id=";
            </script>';
  $output .= '
            <center>
              <table class="lined">
                <tr>
                  <th align="right">';
  if ($user_lvl >= $action_permission['insert'])
    $output .= '
                    <a href="motd.php?action=add_motd">'.$lang_index['add_motd'].'</a>';
  $output .= '
                  </th>
                </tr>';

  if($all_record)
  {
    $result = $sqlc->query('SELECT id, type, content FROM bugreport ORDER BY id DESC LIMIT '.$start.', 3');
    while($post = $sqlc->fetch_assoc($result))
    {
      $output .= '
                <tr>
                  <td align="left" class="large">
                    <blockquote>'.bbcode_bbc2html($post['content']).'</blockquote>
                   </td>
                </tr>
                <tr>
                  <td align="right">';
      ($motd_display_poster) ? $output .= $post['type'] : '';

      if ($user_lvl >= $action_permission['delete'])
        $output .= '
                    <img src="img/cross.png" width="12" height="12" onclick="answerBox(\''.$lang_global['delete'].': &lt;font color=white&gt;'.$post['id'].'&lt;/font&gt;&lt;br /&gt;'.$lang_global['are_you_sure'].'\', del_motd + '.$post['id'].');" style="cursor:pointer;" alt="" />';
      if ($user_lvl >= $action_permission['update'])
        $output .= '
                    <a href="motd.php?action=edit_motd&amp;error=3&amp;id='.$post['id'].'">
                      <img src="img/edit.png" width="14" height="14" alt="" />
                    </a>';
      $output .= '
                  </td>
                </tr>
                <tr>
                  <td class="hidden"></td>
                </tr>';
    }
    $output .= '
                <tr>
                  <td align="right" class="hidden">'.generate_pagination('index.php?', $all_record, 3, $start).'</td>
                </tr>';
  }
  unset($all_record);
  $output .= '
              </table>
              <br />';

  //print online chars
  if ($online)
  {
    //==========================$_GET and SECURE=================================
    $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'name';
    if (preg_match('/^[_[:lower:]]{1,12}$/', $order_by)); else $order_by='name';

    $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
    if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

    $order_dir = ($dir) ? 'ASC' : 'DESC';
    $dir = ($dir) ? 0 : 1;
    //==========================$_GET and SECURE end=============================

    $order_side = '';
    if( $user_lvl || $server[$realm_id]['both_factions']);
    else
    {
      $result = $sqlc->query('SELECT race FROM characters WHERE account = '.$user_id.' AND totaltime = (SELECT MAX(totaltime) FROM characters WHERE account = '.$user_id.') LIMIT 1');
      if ($sqlc->num_rows($result))
        $order_side = (in_array($sqlc->result($result, 0),array(2,5,6,8,10))) ? ' AND race IN (2,5,6,8,10) ' : ' AND race IN (1,3,4,7,11) ';
    }
    $query = '
      SELECT guid, name, race, class, zone, map, level, account, gender,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_HONOR_POINTS+1).'), " ", -1) AS UNSIGNED) AS highest_rank,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_GUILD_ID+1).'), " ", -1) AS UNSIGNED) as gname
        '.($server_type ? ', latency ' : ' ').'
        FROM characters WHERE online= 1 '.($gm_online == '0' ? 'AND extra_flags &1 = 0 ' : '').$order_side.' ORDER BY '.$order_by.' '.$order_dir.'';
    $result = $sqlc->query($query);
    $total_online = $sqlc->result($sqlc->query('SELECT count(*) FROM characters WHERE online= 1'.(($gm_online_count == '0') ? ' AND extra_flags &1 = 0' : '')), 0);
    $output .= '
            <font class="bold">'.$lang_index['tot_users_online'].': '.$total_online.'</font>
            <br /><br />
            <table class="lined">
              <tr>
                <th width="15%"><a href="index.php?order_by=name&amp;dir='.$dir.'"'.($order_by=='name' ? ' class="'.$order_dir.'"' : '').'>'.$lang_index['name'].'</a></th>
                <th width="1%"><a href="index.php?order_by=race&amp;dir='.$dir.'"'.($order_by=='race' ? ' class="'.$order_dir.'"' : '').'>'.$lang_index['race'].'</a></th>
                <th width="1%"><a href="index.php?order_by=class&amp;dir='.$dir.'"'.($order_by=='class' ? ' class="'.$order_dir.'"' : '').'>'.$lang_index['class'].'</a></th>
                <th width="5%"><a href="index.php?order_by=level&amp;dir='.$dir.'"'.($order_by=='level' ? ' class="'.$order_dir.'"' : '').'>'.$lang_index['level'].'</a></th>
                <th width="1%"><a href="index.php?order_by=highest_rank&amp;dir='.$dir.'"'.($order_by=='highest_rank' ? ' class="'.$order_dir.'"' : '').'>'.$lang_index['rank'].'</a></th>
                <th width="15%"><a href="index.php?order_by=gname&amp;dir='.$dir.'"'.($order_by=='gname' ? ' class="'.$order_dir.'"' : '').'>'.$lang_index['guild'].'</a></th>
                <th width="20%"><a href="index.php?order_by=map&amp;dir='.$dir.'"'.($order_by=='map' ? ' class="'.$order_dir.'"' : '').'>'.$lang_index['map'].'</a></th>
                <th width="25%"><a href="index.php?order_by=zone&amp;dir='.$dir.'"'.($order_by=='zone' ? ' class="'.$order_dir.'"' : '').'>'.$lang_index['zone'].'</a></th>';
    if ($server_type)
      $output .='
                <th width="25%"><a href="index.php?order_by=latency&amp;dir='.$dir.'"'.($order_by=='latency' ? ' class="'.$order_dir.'"' : '').'>'.$lang_index['latency'].'</th>';
    if ($showcountryflag)
    {
      require_once 'libs/misc_lib.php';
      $output .= '
                <th width="1%">'.$lang_global['country'].'</th>';
    }
    $output .= '
              </tr>';
    unset($total_online);

    if ($server_type)
    {
      $tlatency = 0;
      $latencycount = 0;
    }

    $sqlm = new SQL;
    $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

    while ($char = $sqlc->fetch_assoc($result))
    {
      $gm = $sqlr->result($sqlr->query('SELECT gmlevel FROM account WHERE id='.$char['account'].''), 0);
      $guild_name = $sqlc->result($sqlc->query('SELECT name FROM guild WHERE guildid='.$char['gname'].''));

      if ($server_type)
      {
        $lat = $char['latency'];
        if ('120' > $lat)
          $cc = '<font color="#00FF00">';
        else if ('120' < $lat AND '350' > $lat)
          $cc = '<font color="#FFFF00">';
        else
          $cc = '<font color="#FF0000">';

        if ($lat)
          $cc .= $lat;
        else
          $cc = '<i>Pending..</i>';
        $cc .= '</font> ms';

        $tlatency = ($tlatency+$lat);
        $latencycount = ($latencycount+1);
      }
      $output .= '
              <tr>
                <td>';
      if (($user_lvl >= $gm))
        $output .= '
                  <a href="char.php?id='.$char['guid'].'">
                    <span onmousemove="toolTip(\''.id_get_gm_level($gm).'\', \'item_tooltip\')" onmouseout="toolTip()">'.htmlentities($char['name']).'</span>
                  </a>';
      else
        $output .='
                  <span onmousemove="toolTip(\''.id_get_gm_level($gm).'\', \'item_tooltip\')" onmouseout="toolTip()">'.htmlentities($char['name']).'</span>';
      $output .= '
                </td>
                <td>
                  <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                </td>
                <td>
                  <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                </td>
                <td>'.char_get_level_color($char['level']).'</td>
                <td>
                  <span onmouseover="toolTip(\''.char_get_pvp_rank_name($char['highest_rank'], char_get_side_id($char['race'])).'\', \'item_tooltip\')" onmouseout="toolTip()" style="color: white;"><img src="img/ranks/rank'.char_get_pvp_rank_id($char['highest_rank'], char_get_side_id($char['race'])).'.gif" alt="" /></span>
                </td>
                <td>
                  <a href="guild.php?action=view_guild&amp;error=3&amp;id='.$char['gname'].'">'.$guild_name.'</a>
                </td>
                <td>'.get_map_name($char['map'], $sqlm).'</td>
                <td>'.get_zone_name($char['zone'], $sqlm).'</td>';
      if ($server_type)
        $output .='
                <td>'.$cc.'</td>';
      if ($showcountryflag)
      {
        $country = misc_get_country_by_account($char['account'], $sqlr, $sqlm);
        $output .='
                <td>'.(($country['code']) ? '<img src="img/flags/'.$country['code'].'.png" onmousemove="toolTip(\''.($country['country']).'\',\'item_tooltip\')" onmouseout="toolTip()" alt="" />' : '-').'</td>';
      }
      $output .='
              </tr>';
    }
    if ($server_type)
      $output .= '
              <tr>
                <td colspan="'.(($showcountryflag) ? '10' : '9').'" class="hidden" align="right">'.$lang_index['a_latency'].' : '.round(( ($latencycount) ? $tlatency/$latencycount : 0 ), 2).' ms</td>
              <tr>';
    $output .= '
            </table>
            <br />
          </center>';
  }

}


//#############################################################################
// MAIN
//#############################################################################

//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$lang_index = lang_index();

front($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_index);

require_once 'footer.php';

?>
