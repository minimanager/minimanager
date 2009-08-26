<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
valid_login($action_permission['read']);

function top100($realmid, &$sqlr, &$sqlc)
{
  global $output, $lang_top,
    $realm_db, $characters_db, $server,
    $itemperpage, $developer_test_mode, $multi_realm_mode;

  $realm_id = $realmid;

  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  //==========================$_GET and SECURE========================
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start=0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'honor';
  if (preg_match('/^[_[:lower:]]{1,10}$/', $order_by)); else $order_by = 'honor';

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

  $order_dir = ($dir) ? 'DESC' : 'DESC';
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================

  $result = $sqlc->query('SELECT count(*) FROM characters');
  $all_record = $sqlc->result($result, 0);
  $all_record = (($all_record < 100) ? $all_record : 100);

  $result = $sqlc->query('SELECT guid, name, race, class, totaltime, online, gender, level, money,
    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_HONOR_POINTS+1).'), " ", -1) AS UNSIGNED) AS honor,
    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_HONOR_KILL+1).'),   " ", -1) AS UNSIGNED) AS kills,
    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_ARENA_POINTS+1).'), " ", -1) AS UNSIGNED) AS arena,
    CAST(SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_GUILD_ID+1).'),     " ", -1) AS UNSIGNED) as gname
    FROM characters ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'');

  //==========================top tage navigaion starts here========================
  $output .= '
          <center>
            <table class="top_hidden">';
  if($developer_test_mode && $multi_realm_mode)
  {
    $realms = $sqlr->query('SELECT count(*) FROM realmlist');
    $tot_realms = $sqlr->result($realms, 0);
    if (1 < $tot_realms && 1 < count($server))
    {
      $output .= '
              <tr>
                <td colspan="2" align="left">';
                  makebutton('View', 'javascript:do_submit(\'form'.$realm_id.'\',0)', 130);
      $output .= '
                  <form action="top100.php" method="get" name="form'.$realm_id.'">
                    Number of Realms :
                    <input type="hidden" name="action" value="realms" />
                    <select name="n_realms">';
      for($i=1;$i<=$tot_realms;++$i)
        $output .= '
                      <option value="'.$i.'">'.htmlentities($i).'</option>';
      $output .= '
                    </select>
                  </form>
                </td>
              </tr>';
    }
  }
  $output .= '
              <tr>
                <td align="right">Total: '.$all_record.'</td>
                <td align="right" width="25%">';
  $output .= generate_pagination('top100.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).'', $all_record, $itemperpage, $start);
  $output .= '
                </td>
              </tr>
            </table>';
  //==========================top tage navigaion ENDS here ========================

  $output .= '
            <table class="lined">
              <tr>
                <th width="1%">'.$lang_top['name'].'</th>
                <th width="1%">'.$lang_top['race'].'</th>
                <th width="1%">'.$lang_top['class'].'</th>
                <th width="1%"><a href="top100.php?order_by=level&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='level' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['level'].'</a></th>
                <th width="10%">'.$lang_top['guild'].'</th>
                <th width="10%"><a href="top100.php?order_by=money&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='money' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['money'].'</a></th>
                <th width="1%"><a href="top100.php?order_by=honor&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='honor' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['rank'].'</a></th>
                <th width="1%">'.$lang_top['honor_points'].'</th>
                <th width="1%"><a href="top100.php?order_by=kills&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='kills' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['kills'].'</a></th>
                <th width="1%"><a href="top100.php?order_by=arena&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='arena' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['arena_points'].'</a></th>
                <th width="10%"><a href="top100.php?order_by=totaltime&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by=='totaltime' ? ' class="'.$order_dir.'"' : '').'>'.$lang_top['time_played'].'</a></th>
                <th width="1%">'.$lang_top['online'].'</th>
              </tr>';
  for ($i=0; $i<$itemperpage; ++$i)
  {
    $char = $sqlc->fetch_assoc($result);
    $guild_name = $sqlc->result($sqlc->query('SELECT name FROM guild WHERE guildid = '.$char['gname'].''), 0);

    $days  = floor(round($char['totaltime'] / 3600)/24);
    $hours = round($char['totaltime'] / 3600) - ($days * 24);
    $time = '';
    if ($days)
      $time .= $days.' days ';
    if ($hours)
      $time .= $hours.' hours';
    $output .= '
              <tr valign="top">
                <td><a href="char.php?id='.$char['guid'].'&amp;realm='.$realm_id.'">'.htmlentities($char['name']).'</a></td>
                <td><img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" alt="'.char_get_race_name($char['race']).'" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" /></td>
                <td><img src="img/c_icons/'.$char['class'].'.gif" alt="'.char_get_class_name($char['class']).'" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" /></td>
                <td>'.char_get_level_color($char['level']).'</td>
                <td><a href="guild.php?action=view_guild&amp;realm='.$realm_id.'&amp;error=3&amp;id='.$char['gname'].'">'.htmlentities($guild_name).'</a></td>
                <td>
                  '.substr($char['money'],  0, -4).'<img src="img/gold.gif" alt="" align="middle" />
                  '.substr($char['money'], -4,  2).'<img src="img/silver.gif" alt="" align="middle" />
                  '.substr($char['money'], -2).'<img src="img/copper.gif" alt="" align="middle" />
                </td>
                <td><span onmouseover="toolTip(\''.char_get_pvp_rank_name($char['honor'], char_get_side_id($char['race'])).'\', \'item_tooltip\')" onmouseout="toolTip()" style="color: white;"><img src="img/ranks/rank'.char_get_pvp_rank_id($char['honor'], char_get_side_id($char['race'])).'.gif" alt=""></img></span></td>
                <td>'.$char['honor'].'</td>
                <td>'.$char['kills'].'</td>
                <td>'.$char['arena'].'</td>
                <td>'.$time.'</td>
                <td>'.($char['online'] ? '<img src="img/up.gif" alt="" />' : '-').'</td>
              </tr>';
  }
  $output .= '
              <tr>
                <td colspan="12" class="hidden" align="right" width="25%">';
  $output .= generate_pagination('top100.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).'', $all_record, $itemperpage, $start);
  unset($all_record);
  $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>';


}

//#############################################################################
// MAIN
//#############################################################################

//$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

//$output .= '
//          <div class="top">';

$lang_top = lang_top();

//if(1 == $err);
//else
//  $output .= "
//            <h1>'.$lang_top['top100'].'</h1>;

//unset($err);

//$output .= '
//          </div>';

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

if ('realms' == $action)
{
  if (isset($_GET['n_realms']))
  {
    $n_realms = $_GET['n_realms'];

    $realms = $sqlr->query('SELECT id, name FROM realmlist LIMIT 10');

    if (1 < $sqlr->num_rows($realms) && 1 < (count($server)))
    {
      for($i=1;$i<=$n_realms;++$i)
      {
        $realm = $sqlr->fetch_assoc($realms);
        if(isset($server[$realm['id']]))
        {
          $output .= '
          <div class="top"><h1>Top 100 of '.$realm['name'].'</h1></div>';
          top100($realm['id'], $sqlr, $sqlc);
        }
      }
    }
    else
    {
      $output .= '
          <div class="top"><h1>'.$lang_top['top100'].'</h1></div>';
      top100($realm_id, $sqlr, $sqlc);
    }
  }
  else
  {
    $output .= '
          <div class="top"><h1>'.$lang_top['top100'].'</h1></div>';
    top100($realm_id, $sqlr, $sqlc);
  }
}
else
{
  $output .= '
          <div class="top"><h1>'.$lang_top['top100'].'</h1></div>';
  top100($realm_id, $sqlr, $sqlc);
}


unset($action);
unset($action_permission);
unset($lang_top);

require_once 'footer.php';


?>
