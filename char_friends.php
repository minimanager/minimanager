<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/map_zone_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS ACHIEVEMENTS
//########################################################################################################################
function char_friends(&$sqlr, &$sqlc)
{
  global $output, $lang_global, $lang_char,
    $realm_id, $realm_db, $mmfpm_db, $characters_db,
    $action_permission, $user_lvl, $user_name;

  if (empty($_GET['id']))
    error($lang_global['empty_fields']);

  // this is multi realm support, as of writing still under development
  //  this page is already implementing it
  if (empty($_GET['realm']))
    $realmid = $realm_id;
  else
  {
    $realmid = $sqlr->quote_smart($_GET['realm']);
    if (is_numeric($realmid))
      $sqlc->connect($characters_db[$realmid]['addr'], $characters_db[$realmid]['user'], $characters_db[$realmid]['pass'], $characters_db[$realmid]['name']);
    else
      $realmid = $realm_id;
  }

  //==========================$_GET and SECURE========================
  $id = $sqlc->quote_smart($_GET['id']);
  if (is_numeric($id)); else $id = 0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'name';
  if (preg_match('/^[[:lower:]]{1,6}$/', $order_by)); else $order_by='name';

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;
  //==========================$_GET and SECURE end========================

  // getting character data from database
  $result = $sqlc->query('SELECT account, name, race, class,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_LEVEL+1).'), " ", -1) AS UNSIGNED) AS level,
    mid(lpad( hex( CAST(substring_index(substring_index(data, " ", '.(CHAR_DATA_OFFSET_GENDER+1).'), " ", -1) as unsigned) ), 8, 0), 4, 1) as gender
    FROM characters WHERE guid = '.$id.' LIMIT 1');

  if ($sqlc->num_rows($result))
  {
    $char = $sqlc->fetch_assoc($result);

    // we get user permissions first
    $owner_acc_id = $sqlc->result($result, 0, 'account');
    $result = $sqlr->query('SELECT gmlevel, username FROM account WHERE id = '.$char['account'].'');
    $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
    $owner_name = $sqlr->result($result, 0, 'username');

    if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
    {
      //------------------------Character Tabs---------------------------------
      // we start with a lead of 10 spaces,
      //  because last line of header is an opening tag with 8 spaces
      //  keep html indent in sync, so debuging from browser source would be easy to read
      $output .= '
          <center>
            <script type="text/javascript">
              // <![CDATA[
                function wrap()
                {
                  if (getBrowserWidth() > 1024)
                  document.write(\'</table></td><td><table class="lined" style="width: 1%;">\');
                }
              // ]]>
            </script>
            <div id="tab">
              <ul>
                <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['char_sheet'].'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['inventory'].'</a></li>
                <li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['talents'].'</a></li>
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['achievements'].'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['quests'].'</a></li>
                <li id="selected"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['friends'].'</a></li>
              </ul>
            </div>
            <div id="tab_content">
              <font class="bold">
                '.htmlentities($char['name']).' -
                <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif"
                  onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                <img src="img/c_icons/'.$char['class'].'.gif"
                  onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /> - lvl '.char_get_level_color($char['level']).'
              </font>
              <br /><br />
              <table class="hidden"  style="width: 1%;">
                <tr valign="top">
                  <td>
                    <table class="lined" style="width: 1%;">';

      $sqlm = new SQL;
      $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

      $result = $sqlc->query('SELECT name, race, class, map, zone,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_LEVEL+1).'), " ", -1) AS UNSIGNED) AS level,
        mid(lpad( hex( CAST(substring_index(substring_index(data, " ", '.(CHAR_DATA_OFFSET_GENDER+1).'), " ", -1) as unsigned) ), 8, 0), 4, 1) as gender, online, account, guid
        FROM characters WHERE guid in (SELECT friend FROM character_social WHERE guid = '.$id.' and flags <= 1) ORDER BY '.$order_by.' '.$order_dir.'');

      if ($sqlc->num_rows($result))
      {
        $output .= '
                      <tr>
                        <th colspan="7" align="left">'.$lang_char['friends'].'</th>
                      </tr>
                      <tr>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=name&amp;dir='.$dir.'"'.($order_by==='name' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['name'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=race&amp;dir='.$dir.'"'.($order_by==='race' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['race'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=class&amp;dir='.$dir.'"'.($order_by==='class' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['class'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=level&amp;dir='.$dir.'"'.($order_by==='level' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['level'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=map&amp;dir='.$dir.'"'.($order_by==='map' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['map'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=zone&amp;dir='.$dir.'"'.($order_by==='zone' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['zone'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=online&amp;dir='.$dir.'"'.($order_by==='online' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['online'].'</a></th>
                      </tr>';
        while ($data = $sqlc->fetch_assoc($result))
        {
          $char_gm_level=$sqlr->result($sqlr->query('SELECT gmlevel FROM account WHERE id = '.$data['account'].''), 0, 'gmlevel');
          $output .= '
                      <tr>
                        <td>';
          if ($user_lvl >= $char_gm_level)
            $output .= '<a href="char.php?id='.$data['guid'].'">'.$data['name'].'</a>';
          else
            $output .=$data['name'];
          $output .='</td>
                        <td><img src="img/c_icons/'.$data['race'].'-'.$data['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($data['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td><img src="img/c_icons/'.$data['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($data['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td>'.char_get_level_color($data['level']).'</td>
                        <td class="small"><span onmousemove="toolTip(\'MapID:'.$data['map'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_map_name($data['map'], $sqlm).'</span></td>
                        <td class="small"><span onmousemove="toolTip(\'ZoneID:'.$data['zone'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_zone_name($data['zone'], $sqlm).'</span></td>
                        <td>'.(($data['online']) ? '<img src="img/up.gif" alt="" />' : '-').'</td>
                      </tr>';
        }
      }

      $result = $sqlc->query('SELECT name, race, class, map, zone,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_LEVEL+1).'), " ", -1) AS UNSIGNED) AS level,
        mid(lpad( hex( CAST(substring_index(substring_index(data, " ", '.(CHAR_DATA_OFFSET_GENDER+1).'), " ", -1) as unsigned) ), 8, 0), 4, 1) as gender, online, account, guid
        FROM characters WHERE guid in (SELECT guid FROM character_social WHERE friend = '.$id.' and flags <= 1) ORDER BY '.$order_by.' '.$order_dir.'');

      if ($sqlc->num_rows($result))
      {
        $output .= '
                      <tr>
                        <th colspan="7" align="left">'.$lang_char['friendof'].'</th>
                      </tr>
                      <tr>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=name&amp;dir='.$dir.'"'.($order_by==='name' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['name'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=race&amp;dir='.$dir.'"'.($order_by==='race' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['race'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=class&amp;dir='.$dir.'"'.($order_by==='class' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['class'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=level&amp;dir='.$dir.'"'.($order_by==='level' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['level'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=map&amp;dir='.$dir.'"'.($order_by==='map' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['map'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=zone&amp;dir='.$dir.'"'.($order_by==='zone' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['zone'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=online&amp;dir='.$dir.'"'.($order_by==='online' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['online'].'</a></th>
                      </tr>';
        while ($data = $sqlc->fetch_assoc($result))
        {
          $char_gm_level=$sqlr->result($sqlr->query('SELECT gmlevel FROM account WHERE id = '.$data['account'].''), 0, 'gmlevel');
          $output .= '
                      <tr>
                        <td>';
          if ($user_lvl >= $char_gm_level)
            $output .= '<a href="char.php?id='.$data['guid'].'">'.$data['name'].'</a>';
          else
            $output .=$data['name'];
          $output .='</td>
                        <td><img src="img/c_icons/'.$data['race'].'-'.$data['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($data['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td><img src="img/c_icons/'.$data['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($data['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td>'.char_get_level_color($data['level']).'</td>
                        <td class="small"><span onmousemove="toolTip(\'MapID:'.$data['map'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_map_name($data['map'], $sqlm).'</span></td>
                        <td class="small"><span onmousemove="toolTip(\'ZoneID:'.$data['zone'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_zone_name($data['zone'], $sqlm).'</span></td>
                        <td>'.(($data['online']) ? '<img src="img/up.gif" alt="" />' : '-').'</td>
                      </tr>';
        }
      }

      $output .= '
                      <script type="text/javascript">
                        // <![CDATA[
                          wrap();
                        // ]]>
                      </script>';

      $result = $sqlc->query('SELECT name, race, class, map, zone,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_LEVEL+1).'), " ", -1) AS UNSIGNED) AS level,
        mid(lpad( hex( CAST(substring_index(substring_index(data, " ", '.(CHAR_DATA_OFFSET_GENDER+1).'), " ", -1) as unsigned) ), 8, 0), 4, 1) as gender, online, account, guid
        FROM characters WHERE guid in (SELECT friend FROM character_social WHERE guid = '.$id.' and flags > 1) ORDER BY '.$order_by.' '.$order_dir.'');

      if ($sqlc->num_rows($result))
      {
        $output .= '
                      <tr>
                        <th colspan="7" align="left">'.$lang_char['ignored'].'</th>
                      </tr>
                      <tr>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=name&amp;dir='.$dir.'"'.($order_by==='name' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['name'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=race&amp;dir='.$dir.'"'.($order_by==='race' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['race'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=class&amp;dir='.$dir.'"'.($order_by==='class' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['class'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=level&amp;dir='.$dir.'"'.($order_by==='level' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['level'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=map&amp;dir='.$dir.'"'.($order_by==='map' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['map'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=zone&amp;dir='.$dir.'"'.($order_by==='zone' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['zone'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=online&amp;dir='.$dir.'"'.($order_by==='online' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['online'].'</a></th>
                      </tr>';
        while ($data = $sqlc->fetch_assoc($result))
        {
          $char_gm_level=$sqlr->result($sqlr->query('SELECT gmlevel FROM account WHERE id = '.$data['account'].''), 0, 'gmlevel');
          $output .= '
                      <tr>
                        <td>';
          if ($user_lvl >= $char_gm_level)
            $output .= '<a href="char.php?id='.$data['guid'].'">'.$data['name'].'</a>';
          else
            $output .=$data['name'];
          $output .='</td>
                        <td><img src="img/c_icons/'.$data['race'].'-'.$data['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($data['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td><img src="img/c_icons/'.$data['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($data['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td>'.char_get_level_color($data['level']).'</td>
                        <td class="small"><span onmousemove="toolTip(\'MapID:'.$data['map'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_map_name($data['map'], $sqlm).'</span></td>
                        <td class="small"><span onmousemove="toolTip(\'ZoneID:'.$data['zone'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_zone_name($data['zone'], $sqlm).'</span></td>
                        <td>'.(($data['online']) ? '<img src="img/up.gif" alt="" />' : '-').'</td>
                      </tr>';
        }
      }

      $result = $sqlc->query('SELECT name, race, class, map, zone,
        CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_LEVEL+1).'), " ", -1) AS UNSIGNED) AS level,
        mid(lpad( hex( CAST(substring_index(substring_index(data, " ", '.(CHAR_DATA_OFFSET_GENDER+1).'), " ", -1) as unsigned) ), 8, 0), 4, 1) as gender, online, account, guid
        FROM characters WHERE guid in (SELECT guid FROM character_social WHERE friend = '.$id.' and flags > 1) ORDER BY '.$order_by.' '.$order_dir.'');

      if ($sqlc->num_rows($result))
      {
        $output .= '
                      <tr>
                        <th colspan="7" align="left">'.$lang_char['ignoredby'].'</th>
                      </tr>
                      <tr>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=name&amp;dir='.$dir.'"'.($order_by==='name' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['name'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=race&amp;dir='.$dir.'"'.($order_by==='race' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['race'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=class&amp;dir='.$dir.'"'.($order_by==='class' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['class'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=level&amp;dir='.$dir.'"'.($order_by==='level' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['level'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=map&amp;dir='.$dir.'"'.($order_by==='map' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['map'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=zone&amp;dir='.$dir.'"'.($order_by==='zone' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['zone'].'</a></th>
                        <th width="1%"><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=online&amp;dir='.$dir.'"'.($order_by==='online' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['online'].'</a></th>
                      </tr>';
        while ($data = $sqlc->fetch_assoc($result))
        {
          $char_gm_level=$sqlr->result($sqlr->query('SELECT gmlevel FROM account WHERE id = '.$data['account'].''), 0, 'gmlevel');
          $output .= '
                      <tr>
                        <td>';
          if ($user_lvl >= $char_gm_level)
            $output .= '<a href="char.php?id='.$data['guid'].'">'.$data['name'].'</a>';
          else
            $output .=$data['name'];
          $output .='</td>
                        <td><img src="img/c_icons/'.$data['race'].'-'.$data['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($data['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td><img src="img/c_icons/'.$data['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($data['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /></td>
                        <td>'.char_get_level_color($data['level']).'</td>
                        <td class="small"><span onmousemove="toolTip(\'MapID:'.$data['map'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_map_name($data['map'], $sqlm).'</span></td>
                        <td class="small"><span onmousemove="toolTip(\'ZoneID:'.$data['zone'].'\', \'item_tooltip\')" onmouseout="toolTip()">'.get_zone_name($data['zone'], $sqlm).'</span></td>
                        <td>'.(($data['online']) ? '<img src="img/up.gif" alt="" />' : '-').'</td>
                      </tr>';
        }
      }
      $output .= '
                    </table>
                  </td>';
      //---------------Page Specific Data Ends here----------------------------
      //---------------Character Tabs Footer-----------------------------------
      $output .= '
                </tr>
              </table>
            </div>
            <br />
            <table class="hidden">
              <tr>
                <td>';
                  // button to user account page, user account page has own security
                  makebutton($lang_char['chars_acc'], 'user.php?action=edit_user&amp;id='.$owner_acc_id.'', 130);
      $output .= '
                </td>
                <td>';

      // only higher level GM with delete access can edit character
      //  character edit allows removal of character items, so delete permission is needed
      if ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) )
      {
                  makebutton($lang_char['edit_button'], 'char_edit.php?id='.$id.'&amp;realm='.$realmid.'', 130);
        $output .= '
                </td>
                <td>';
      }
      // only higher level GM with delete access, or character owner can delete character
      if ( ( ($user_lvl > $owner_gmlvl) && ($user_lvl >= $action_permission['delete']) ) || ($owner_name === $user_name) )
      {
                  makebutton($lang_char['del_char'], 'char_list.php?action=del_char_form&amp;check%5B%5D='.$id.'" type="wrn', 130);
        $output .= '
                </td>
                <td>';
      }
      // only GM with update permission can send mail, mail can send items, so update permission is needed
      if ($user_lvl >= $action_permission['update'])
      {
                  makebutton($lang_char['send_mail'], 'mail.php?type=ingame_mail&amp;to='.$char['account'].'', 130);
        $output .= '
                </td>
                <td>';
      }
                  makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
      $output .= '
                </td>
              </tr>
            </table>
            <br />
          </center>
          <!-- end of char_friends.php -->';
    }
    else
      error($lang_char['no_permission']);
  }
  else
    error($lang_char['no_char_found']);

}


//########################################################################################################################
// MAIN
//########################################################################################################################

//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$lang_char = lang_char();

char_friends($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
