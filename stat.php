<?php


require_once 'header.php';
valid_login($action_permission['read']);

function stats($action, &$sqlr, &$sqlc)
{
  global $output, $lang_global, $lang_stat, $lang_index,
    $realm_id, $realm_db, $characters_db,
    $theme;

  $race = Array
  (
    1  => array(1, 'Human','',''),
    2  => array(2, 'Orc','',''),
    3  => array(3, 'Dwarf','',''),
    4  => array(4, 'Nightelf','',''),
    5  => array(5, 'Undead','',''),
    6  => array(6, 'Tauren','',''),
    7  => array(7, 'Gnome','',''),
    8  => array(8, 'Troll','',''),
    10 => array(10,'Bloodelf','',''),
    11 => array(11,'Draenei','','')
  );

  $class = Array
  (
    1  => array(1, 'Warrior','',''),
    2  => array(2, 'Paladin','',''),
    3  => array(3, 'Hunter','',''),
    4  => array(4, 'Rogue','',''),
    5  => array(5, 'Priest','',''),
    6  => array(6, 'Death Knight','',''),
    7  => array(7, 'Shaman','',''),
    8  => array(8, 'Mage','',''),
    9  => array(9, 'Warlock','',''),
    11 => array(11,'Druid','','')
  );

  $level = Array
  (
    1 => array(1,1,9,'',''),
    2 => array(2,10,19,'',''),
    3 => array(3,20,29,'',''),
    4 => array(4,30,39,'',''),
    5 => array(5,40,49,'',''),
    6 => array(6,50,59,'',''),
    7 => array(7,60,69,'',''),
    8 => array(8,70,79,'',''),
    9 => array(9,80,80,'','')
  );

  if($action)
    $query = $sqlc->query('SELECT count(*) FROM characters WHERE online= 1');
  else
  {
    $query = $sqlr->query('SELECT count(*) FROM account UNION SELECT count(*) FROM account WHERE gmlevel > 0');
    $total_acc = $sqlr->result($query,0);
    $total_gms = $sqlr->result($query,1);

    $data = date('Y-m-d H:i:s');
    $data_1 = mktime(date('H'), date('i'), date('s'), date('m'), date('d')-1, date('Y'));
    $data_1 = date('Y-m-d H:i:s', $data_1);

    $max_ever = $sqlr->result($sqlr->query('SELECT maxplayers FROM uptime WHERE realmid = '.$realm_id.' ORDER BY maxplayers DESC LIMIT 1'), 0);
    $max_restart = $sqlr->result($sqlr->query('SELECT maxplayers FROM uptime WHERE realmid = '.$realm_id.' ORDER BY starttime DESC LIMIT 1'), 0);

    $uniqueIPs = $sqlr->result($sqlr->query('select distinct count(last_ip) from account where last_login > \''.$data_1.'\' and last_login < \''.$data.'\''),0);

    $query = $sqlr->query('SELECT AVG(uptime)/60,MAX(uptime)/60,(100*SUM(uptime)/(UNIX_TIMESTAMP()-MIN(starttime))) FROM uptime WHERE realmid = '.$realm_id.'');
    $uptime = $sqlr->fetch_row($query);

    $query = $sqlc->query('SELECT count(*) FROM characters');
  }

  $total_chars = $sqlc->result($query,0);

  if ($total_chars)
  {
    $order_race = (isset($_GET['race'])) ? 'AND race ='.$sqlc->quote_smart($_GET['race']) : '';
    $order_class = (isset($_GET['class'])) ? 'AND class ='.$sqlc->quote_smart($_GET['class']) : '';

    if(isset($_GET['level']))
    {
      $lvl_min = $sqlc->quote_smart($_GET['level']);
      $lvl_max = $lvl_min + 4;
      $order_level = 'AND level >= '.$lvl_min.' AND level <= '.$lvl_max.'';
    }
    else
      $order_level = '';

    if(isset($_GET['side']))
    {
      if ($sqlc->quote_smart($_GET['side']) == 'h')
        $order_side = 'AND race IN(2,5,6,8,10)';
      elseif ($sqlc->quote_smart($_GET['side']) == 'a')
        $order_side = 'AND race IN (1,3,4,7,11)';
    }
    else
      $order_side = '';
    $output .= '
          <center>
            <div id="tab">
              <ul>
                <li'.(($action) ? '' : ' id="selected"').'>
                  <a href="stat.php">
                    '.$lang_stat['srv_statistics'].'
                  </a>
                </li>
                <li'.(($action) ? ' id="selected"' : '').'>
                  <a href="stat.php?action=true">
                    '.$lang_stat['on_statistics'].'
                  </a>
                </li>
              </ul>
            </div>
            <div id="tab_content">';
    $output .= '
              <div class="top"><h1>'.(($action) ? $lang_stat['on_statistics'] : $lang_stat['srv_statistics']).'</h1></div>';

    //there is always less hordies
    $query = $sqlc->query('SELECT count(guid) FROM characters WHERE race IN(2,5,6,8,10)'.(($action) ? ' AND online= 1' : ''));
    $horde_chars = $sqlc->result($query,0);
    $horde_pros = round(($horde_chars*100)/$total_chars ,1);
    $allies_chars = $total_chars - $horde_chars;
    $allies_pros = 100 - $horde_pros;

    $output .= '
              <center>
                <table class="hidden">
                  <tr>
                    <td align="left">
                      <h1>'.$lang_stat['general_info'].'</h1>
                    </td>
                  </tr>
                  <tr align="left">
                    <td class="large">';
    if($action)
      $output .= '
                      <font class="bold">'.$lang_index['tot_users_online'].' : '.$total_chars.'</font><br /><br />';
    else
    {
       $output .= '
                      <table>
                        <tr valign="top">
                          <td align="left">
                            '.$lang_stat['uptime_prec'].':<br />
                            '.$lang_stat['avg_uptime'].':<br />
                            '.$lang_stat['max_uptime'].':<br />
                            <br />
                            '.$lang_stat['tot_accounts'].':<br />
                            '.$lang_stat['tot_chars_on_realm'].':<br />
                          </td>
                          <td align="right">
                            '.round($uptime[2],1).'%<br />
                            '.(int)($uptime[0]/60).':'.(int)(($uptime[0]%60)).'h<br />
                            '.(int)($uptime[1]/60).':'.(int)(($uptime[1]%60)).'h<br />
                            <br />
                            '.$total_acc.'<br />
                             '.$total_chars.'<br />
                          </td>
                          <td>&nbsp;&nbsp;
                          </td>
                          <td align="left">
                            '.$lang_stat['unique_ip'].':<br />
                            <br />
                            '.$lang_stat['max_players'].' :<br />
                            '.$lang_stat['max_ever'].' :<br />
                            '.$lang_stat['max_restart'].' :<br />
                          </td>
                          <td align="right">
                            '.$uniqueIPs.'<br />
                            <br />
                            <br />
                            '.$max_ever.'<br />
                            '.$max_restart.'<br />
                          </td>
                        </tr>
                        <tr align="left">
                          <td colspan="2">
                            '.$lang_stat['average_of'].' '.round($total_chars/$total_acc,1).' '.$lang_stat['chars_per_acc'].'<br />
                            '.$lang_stat['total_of'].' '.$total_gms.' '.$lang_stat['gms_one_for'].' '.round($total_acc/$total_gms,1).' '.$lang_stat['players'].'
                          </td>
                          <td colspan="2">
                          </td>
                        </tr>
                      </table>
                      <br />';
    }
    $output .= '
                      <table class="tot_bar">
                        <tr>
                          <td width="'.$horde_pros.'%" background="img/bar_horde.gif" height="40"><a href="stat.php?action='.$action.'&amp;side=h">'.$lang_stat['horde'].': '.$horde_chars.' ('.$horde_pros.'%)</a></td>
                          <td width="'.$allies_pros.'%" background="img/bar_allie.gif" height="40"><a href="stat.php?action='.$action.'&amp;side=a">'.$lang_stat['alliance'].': '.$allies_chars.' ('.$allies_pros.'%)</a></td>
                        </tr>
                      </table>
                      <hr/>
                    </td>
                  </tr>';
    // RACE
    foreach ($race as $id)
    {
      $race[$id[0]][2] = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters
        WHERE race = '.$id[0].' '.$order_class.' '.$order_level.' '.$order_side.(($action) ? ' AND online= 1' : '')),0);
      $race[$id[0]][3] = round((($race[$id[0]][2])*100)/$total_chars,1);
    }
    $output .= '
                  <tr align="left">
                    <td>
                      <h1>'.$lang_stat['chars_by_race'].'</h1>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table class="bargraph">
                        <tr>';
    foreach ($race as $id)
    {
      $height = ($race[$id[0]][3])*4;
      $output .= '
                          <td>
                            <a href="stat.php?action='.$action.'&amp;race='.$id[0].'" class="graph_link">'.$race[$id[0]][3].'%<img src="themes/'.$theme.'/column.gif" width="69" height="'.$height.'" alt="'.$race[$id[0]][2].'" /></a>
                          </td>';
    }
    $output .= '
                        </tr>
                        <tr>';
    foreach ($race as $id)
    {
      $output .= '
                          <th>'.$race[$id[0]][1].'<br />'.$race[$id[0]][2].'</th>';
    }
    $output .= '
                        </tr>
                      </table>
                      <br />
                    </td>
                  </tr>';
    // RACE END
    // CLASS
    foreach ($class as $id)
    {
      $class[$id[0]][2] = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters
        WHERE class = '.$id[0].' '.$order_race.' '.$order_level.' '.$order_side.(($action) ? ' AND online= 1' : '')), 0);
      $class[$id[0]][3] = round((($class[$id[0]][2])*100)/$total_chars,1);
    }
    $output .= '
                  <tr align="left">
                    <td>
                      <h1>'.$lang_stat['chars_by_class'].'</h1>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table class="bargraph">
                        <tr>';
    foreach ($class as $id)
    {
      $height = ($class[$id[0]][3])*4;
      $output .= '
                          <td>
                            <a href="stat.php?action='.$action.'&amp;class='.$id[0].'" class="graph_link">'.$class[$id[0]][3].'%<img src="themes/'.$theme.'/column.gif" width="69" height="'.$height.'" alt="'.$class[$id[0]][2].'" /></a>
                          </td>';
    }
    $output .= '
                        </tr>
                        <tr>';
    foreach ($class as $id)
    {
      $output .= '
                          <th>'.$class[$id[0]][1].'<br />'.$class[$id[0]][2].'</th>';
    }
    $output .= '
                        </tr>
                      </table>
                      <br />
                    </td>
                  </tr>';
    // CLASS END
    // LEVEL
    foreach ($level as $id)
    {
      $level[$id[0]][3] = $sqlc->result($sqlc->query('SELECT count(guid) FROM characters
        WHERE level >= '.$id[1].'
          AND level <= '.$id[2].'
            '.$order_race.' '.$order_class.' '.$order_side.(($action) ? ' AND online= 1' : '').''),0);
      $level[$id[0]][4] = round((($level[$id[0]][3])*100)/$total_chars,1);
    }

    $output .= '
                  <tr align="left">
                    <td>
                      <h1>'.$lang_stat['chars_by_level'].'</h1>
                    </td>
                  </tr>
                  <tr>
                    <td>
                      <table class="bargraph">
                        <tr>';
    foreach ($level as $id)
    {
      $height = ($level[$id[0]][4])*4;
      $output .= '
                          <td><a href="stat.php?action='.$action.'&amp;level='.$id[1].'" class="graph_link">'.$level[$id[0]][4].'%<img src="themes/'.$theme.'/column.gif" width="77" height="'.$height.'" alt="'.$level[$id[0]][3].'" /></a></td>';
    }
    $output .= '
                        </tr>
                        <tr>';
    foreach ($level as $id)
      $output .= '
                          <th>'.$level[$id[0]][1].'-'.$level[$id[0]][2].'<br />'.$level[$id[0]][3].'</th>';
    $output .= '
                        </tr>
                      </table>
                      <br />
                      <hr/>
                    </td>
                  </tr>
                  <tr>
                    <td>';
    // LEVEL END
                      makebutton($lang_stat['reset'], 'stat.php', 720);
    $output .= '
                    </td>
                  </tr>
                </table>
              </center>
            </div>
            <br />
          </center>';
  }
  else
    error($lang_global['err_no_result']);

}


//#############################################################################
// MAIN
//#############################################################################
//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//unset($err);

$lang_index = lang_index();
$lang_stat = lang_stat();

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

stats($action, $sqlr, $sqlc);

unset($action);
unset($action_permission);
unset($lang_index);
unset($lang_stat);

require_once 'footer.php';


?>
