<?php


require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/skill_lib.php';
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTERS SKILLS
//########################################################################################################################
function char_skill(&$sqlr, &$sqlc)
{
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $characters_db, $mmfpm_db,
    $action_permission, $user_lvl, $user_name, $skill_datasite;
  wowhead_tt();

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

  $id = $sqlc->quote_smart($_GET['id']);
  if (is_numeric($id)); else $id = 0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 1;

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir = 1;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;

  $result = $sqlc->query('SELECT account, name, race, class, level, gender FROM characters WHERE guid = '.$id.' LIMIT 1');

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
      $result = $sqlc->query('SELECT data, name, race, class, level, gender FROM characters WHERE guid = '.$id.'');
      $char = $sqlc->fetch_assoc($result);
      $char_data = explode(' ',$char['data']);

      $output .= '
          <center>
            <div id="tab">
              <ul>
                <li id="selected"><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['char_sheet'].'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['inventory'].'</a></li>
                <li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['talents'].'</a></li>
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['achievements'].'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['quests'].'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['friends'].'</a></li>
               </ul>
            </div>
            <div id="tab_content">
              <div id="tab">
                <ul>
                  <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['char_sheet'].'</a></li>';
      if( char_get_class_name($char['class']) == 'Hunter' )
        $output .= '
                  <li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['pets'].'</a></li>';
      $output .= '
                  <li><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['reputation'].'</a></li>
                  <li id="selected"><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['skills'].'</a></li>
                </ul>
              </div>
              <div id="tab_content2">
                <font class="bold">
                  '.htmlentities($char['name']).' -
                  <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif"
                    onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
                  <img src="img/c_icons/'.$char['class'].'.gif"
                    onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /> - lvl '.char_get_level_color($char['level']).'
                </font>
                <br /><br />
                <table class="lined" style="width: 550px;">
                  <tr>
                    <th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['skills'].'</th>
                  </tr>
                  <tr>
                    '.($user_lvl ? '<th><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=0&amp;dir='.$dir.'"'.($order_by==0 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['skill_id'].'</a></th>' : '').'
                    <th align="right"><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=1&amp;dir='.$dir.'"'.($order_by==1 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['skill_name'].'</a></th>
                    <th><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=2&amp;dir='.$dir.'"'.($order_by==2 ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['skill_value'].'</a></th>
                  </tr>';

      $skill_array = array();
      $class_array = array();
      $prof_1_array = array();
      $prof_2_array = array();
      $weapon_array = array();
      $armor_array = array();
      $language_array = array();

      $skill_rank_array = array(
         75 => $lang_char['apprentice'],
        150 => $lang_char['journeyman'],
        225 => $lang_char['expert'],
        300 => $lang_char['artisan'],
        375 => $lang_char['master'],
        450 => $lang_char['inherent'],
        385 => $lang_char['wise']
      );

      $sqlm = new SQL;
      $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

      for ($i = CHAR_DATA_OFFSET_SKILL_DATA; $i <= CHAR_DATA_OFFSET_SKILL_DATA+384 ; $i+=3)
      {
        if (($char_data[$i])&&(skill_get_name($char_data[$i] & 0x0000FFFF, $sqlm)))
        {
          $temp = unpack("S", pack("L", $char_data[$i+1]));
          $skill = ($char_data[$i] & 0x0000FFFF);

          if (skill_get_type($skill, $sqlm) == 6)
          {
            array_push($weapon_array , array(($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp[1]));
          }
          elseif (skill_get_type($skill, $sqlm) == 7)
          {
            array_push($class_array , array(($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp[1]));
          }
          elseif (skill_get_type($skill, $sqlm) == 8)
          {
            array_push($armor_array , array(($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp[1]));
          }
          elseif (skill_get_type($skill, $sqlm) == 9)
          {
            array_push($prof_2_array , array(($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp[1]));
          }
          elseif (skill_get_type($skill, $sqlm) == 10)
          {
            array_push($language_array , array(($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp[1]));
          }
          elseif (skill_get_type($skill, $sqlm) == 11)
          {
            array_push($prof_1_array , array(($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp[1]));
          }
          else
          {
            array_push($skill_array , array(($user_lvl ? $skill : ''), skill_get_name($skill, $sqlm), $temp[1]));
          }
        }
      }
      unset($char_data);

      aasort($skill_array, $order_by, $dir);
      aasort($class_array, $order_by, $dir);
      aasort($prof_1_array, $order_by, $dir);
      aasort($prof_2_array, $order_by, $dir);
      aasort($weapon_array, $order_by, $dir);
      aasort($armor_array, $order_by, $dir);
      aasort($language_array, $order_by, $dir);

      foreach ($skill_array as $data)
      {
        $max = ($data[2] < $char['level']*5) ? $char['level']*5 : $data[2];
        $output .= '
                  <tr>
                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                    <td align="right">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.'</span>
                    </td>
                  </tr>';
      }

      if(count($class_array))
        $output .= '
                  <tr><th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['classskills'].'</th></tr>';
      foreach ($class_array as $data)
      {
        $max = ($data[2] < $char['level']*5) ? $char['level']*5 : $data[2];
        $output .= '
                  <tr>
                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                    <td align="right"><a href="'.$skill_datasite.'7.'.$char['class'].'.'.$data[0].'" target="_blank">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" style="background-position: 0px;">
                    </td>
                  </tr>';
      }

      if(count($prof_1_array))
        $output .= '
                  <tr><th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['professions'].'</th></tr>';
      foreach ($prof_1_array as $data)
      {
        $max = ($data[2]<76 ? 75 : ($data[2]<151 ? 150 : ($data[2]<226 ? 225 : ($data[2]<301 ? 300 : ($data[2]<376 ? 375 : ($data[2]<376 ? 375 : 450))))));
        $output .= '
                  <tr>
                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                    <td align="right"><a href="'.$skill_datasite.'11.'.$data[0].'" target="_blank">'.$data[1].'</a></td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.' ('.$skill_rank_array[$max].')</span>
                    </td>
                  </tr>';
      }

      if(count($prof_2_array))
        $output .= '
                  <tr><th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['secondaryskills'].'</th></tr>';
      foreach ($prof_2_array as $data)
      {
        $max = ($data[2]<76 ? 75 : ($data[2]<151 ? 150 : ($data[2]<226 ? 225 : ($data[2]<301 ? 300 : ($data[2]<376 ? 375 : ($data[2]<376 ? 375 : 450))))));
        $output .= '
                  <tr>
                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                    <td align="right"><a href="'.$skill_datasite.'9.'.$data[0].'" target="_blank">'.$data[1].'</a></td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.' ('.$skill_rank_array[$max].')</span>
                    </td>
                  </tr>';
      }

      if(count($weapon_array))
        $output .= '
                  <tr><th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['weaponskills'].'</th></tr>';
      foreach ($weapon_array as $data)
      {
        $max = ($data[2] < $char['level']*5) ? $char['level']*5 : $data[2];
        $output .= '
                  <tr>
                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                    <td align="right">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.'</span>
                    </td>
                  </tr>';
      }

      if(count($armor_array))
        $output .= '
                  <tr><th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['armorproficiencies'].'</th></tr>';
      foreach ($armor_array as $data)
      {
        $max = ($data[2] < $char['level']*5) ? $char['level']*5 : $data[2];
        $output .= '
                  <tr>
                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                    <td align="right">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" style="background-position: 0px;">
                    </td>
                  </tr>';
      }

      if(count($language_array))
        $output .= '
                  <tr><th class="title" colspan="'.($user_lvl ? '3' : '2').'" align="left">'.$lang_char['languages'].'</th></tr>';
      foreach ($language_array as $data)
      {
        $max = ($data[2] < $char['level']*5) ? $char['level']*5 : $data[2];
        $output .= '
                  <tr>
                    '.($user_lvl ? '<td>'.$data[0].'</td>' : '').'
                    <td align="right">'.$data[1].'</td>
                    <td valign="center" class="bar skill_bar" style="background-position: '.(round(450*$data[2]/$max)-450).'px;">
                      <span>'.$data[2].'/'.$max.'</span>
                    </td>
                  </tr>';
      }

      $output .= '
                </table>
                <br />
              </div>
              <br />
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
                  makebutton($lang_char['send_mail'], 'mail.php?type=ingame_mail&amp;to='.$char['name'].'', 130);
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
          <!-- end of char_achieve.php -->';
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

char_skill($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
