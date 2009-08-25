<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/archieve_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//#############################################################################
// SHOW CHARACTERS ACHIEVEMENTS
//#############################################################################
function char_achievements(&$sqlr, &$sqlc)
{
  global $output, $lang_global, $lang_char,
    $realm_id, $characters_db, $mmfpm_db,
    $action_permission, $user_lvl, $user_name,
    $achievement_datasite, $itemperpage,
    $developer_test_mode, $new_achieve_page;

  // this page uses wowhead tooltops
  wowhead_tt();

  // we need at least an id or we would have nothing to show
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

  //-------------------SQL Injection Prevention--------------------------------
  // no point going further if we don have a valid ID
  $id = $sqlc->quote_smart($_GET['id']);
  if (is_numeric($id));
  else error($lang_global['empty_fields']);

  // this page has multipage support and field ordering, so we need these
  $start = (isset($_GET['start'])) ? $sqlc->quote_smart($_GET['start']) : 0;
  if (is_numeric($start)); else $start = 0;

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 'date';
  if (preg_match('/^[_[:lower:]]{1,4}$/', $order_by)); else $order_by = 'date';

  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 1;
  if (preg_match('/^[01]{1}$/', $dir)); else $dir = 1;

  $order_dir = ($dir) ? 'ASC' : 'DESC';
  $dir = ($dir) ? 0 : 1;

  // getting character data from database
  $result = $sqlc->query('SELECT account, name, race, class, level, gender
    FROM characters WHERE guid = '.$id.' LIMIT 1');

  // no point going further if character does not exist
  if ($sqlc->num_rows($result))
  {
    $char = $sqlc->fetch_assoc($result);

    // we get user permissions first
    $owner_acc_id = $sqlc->result($result, 0, 'account');
    $result = $sqlr->query('SELECT gmlevel, username FROM account WHERE id = '.$char['account'].'');
    $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
    $owner_name = $sqlr->result($result, 0, 'username');

    // check user permission
    if ( ($user_lvl > $owner_gmlvl) || ($owner_name === $user_name) )
    {
      //------------------------Character Tabs---------------------------------
      // we start with a lead of 10 spaces,
      //  because last line of header is an opening tag with 8 spaces
      //  keep html indent in sync, so debuging from browser source would be easy to read
      $output .= '
          <!-- start of char_achieve.php -->
          <center>
            <div id="tab">
              <ul>
                <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['char_sheet'].'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['inventory'].'</a></li>
                <li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['talents'].'</a></li>
                <li id="selected"><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['achievements'].'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['quests'].'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['friends'].'</a></li>
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
              <br /><br />';
      //---------------Page Specific Data Starts Here--------------------------

      $sqlm = new SQL;
      $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

      if ($developer_test_mode && $new_achieve_page)
      {
        $output .= '
              <table class="lined" style="width: 500px;">';

        $result = $sqlc->query('SELECT achievement, date FROM character_achievement WHERE guid = '.$id.'');
        $char_achieve = array();

        while ($temp = $sqlc->fetch_assoc($result))
          $char_achieve[$temp['achievement']] = $temp['date'];

        unset($result);
        $total = 0;
        $points = 0;
        $main_cats = achieve_get_main_category($sqlm);
        $sub_cats = achieve_get_sub_category($sqlm);
        foreach($main_cats as $cat_id => $cat)
        {
          $total_cat= 0;
          if (isset($cat['name01']))
          {
            $i=0;
            $temp_output = '';
            $achieve_sub_cat = achieve_get_id_category($cat['id'], $sqlm);
            foreach($achieve_sub_cat as $achieve_id => $id)
            {
              if (isset($char_achieve[$id['id']]))
              {
                $temp_output .= '
                      <tr>
                        <td>
                          <a href="'.$achievement_datasite.$id['id'].'" target="_blank">
                            <img src="'.achieve_get_icon($id['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                          </a>
                        </td>
                        <td colspan="2" align="left">
                          <a href="'.$achievement_datasite.$id['id'].'" target="_blank">'.$id['name01'].'</a><br />
                          '.$id['description01'].'<br />
                          '.$id['rewarddesc01'].'
                        </td>
                        <td width="5%" align="right">'.$id['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                        <td width="10%" align="right">'.date('o-m-d', $char_achieve[$id['id']]).'</td>
                      </tr>';
                $points += $id['rewpoints'];
                ++$i;
              }
            }
            $total = $total + $i;
            $total_cat = $total_cat + $i;
            $temp_cat_output = '';
            if (isset($sub_cats[$cat['id']]))
            {
              $main_sub_cats = $sub_cats[$cat['id']];
              foreach($main_sub_cats as $sub_cat_id => $sub_cat)
              {
                $total_sub_cat = 0;
                if (isset($sub_cat))
                {
                  $j=0;
                  $temp_sub_output = '';
                  $achieve_cat = achieve_get_id_category($sub_cat_id, $sqlm);
                  foreach($achieve_cat as $achieve_id => $id)
                  {
                    if (isset($char_achieve[$id['id']]))
                    {
                      $temp_sub_output .= '
                            <tr>
                              <td>
                                <a href="'.$achievement_datasite.$id['id'].'" target="_blank">
                                  <img src="'.achieve_get_icon($id['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                                </a>
                              </td>
                              <td colspan="2" align="left">
                                <a href="'.$achievement_datasite.$id['id'].'" target="_blank">'.$id['name01'].'</a><br />
                                '.$id['description01'].'<br />
                                '.$id['rewarddesc01'].'
                              </td>
                              <td width="5%" align="right">'.$id['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                              <td width="10%" align="right">'.date('o-m-d', $char_achieve[$id['id']]).'</td>
                            </tr>';
                       $points += $id['rewpoints'];
                       ++$j;
                    }
                  }
                  $total = $total + $j;
                  $total_cat = $total_cat + $j;
                  $total_sub_cat = $total_sub_cat + $j;
                  $temp_sub_cat_output = '';

                  // this is working code for 3rd level sub category, reserved for future
                  /*
                  if (isset($sub_cats[$sub_cat_id]))
                  {
                    $main_sub_sub_cats = $sub_cats[$sub_cat_id];
                    foreach($main_sub_sub_cats as $sub_sub_cat_id => $sub_sub_cat)
                    {
                      if (isset($sub_sub_cat))
                      {
                        $k=0;
                        $temp_sub_sub_ = '';
                        $achieve_cat = achieve_get_id_category($sub_sub_cat_id, $sqlm);
                        foreach($achieve_cat as $achieve_id => $id)
                        {
                          if (isset($char_achieve[$id['id']]))
                          {
                            $temp_sub_sub_output .= '
                                  <tr>
                                    <td>
                                      <a href="'.$achievement_datasite.$id['id'].'" target="_blank">'.$id['name01'].'</a><br />
                                        <img src="'.achieve_get_icon($id['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                                      </a>
                                    </td>
                                    <td colspan="2" align="left">
                                      <a href="'.$achievement_datasite.$id['id'].'" target="_blank">'.$id['name01'].'</a><br />
                                      '.$id['description01'].'<br />
                                      '.$id['rewarddesc01'].'
                                    </td>
                                    <td width="5%" align="right">'.$id['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                                    <td width="10%" align="right">'.date('o-m-d', $char_achieve[$id['id']]).'</td>
                                  </tr>';
                            ++$k;
                            $points += $id['rewpoints'];
                          }
                        }
                        $total = $total + $k;
                        $total_cat = $total_cat + $k;
                        if ($k)
                        {
                          $temp_sub_cat_output .= '
                            <tr>
                              <td colspan="5" align="left">'.$sub_sub_cat.'</td>
                            </tr>
                            <tr>
                              <td colspan="5">
                                <table id="'.$sub_cat_id_cat.'" style="width: 500px; display: table;">';
                          $temp_sub_cat_output .= '
                                  <tr>
                                    <th colspan="3" align="left">'.$lang_char['achievement_title'].'</th>
                                    <th width="5%">'.$lang_char['achievement_points'].'</th>
                                    <th width="10%">'.$lang_char['achievement_date'].'</th>
                                  </tr>'.$temp_sub_sub_output;
                          $temp_sub_cat_output .= '
                                </table>
                              </td>
                            </tr>';
                        }
                      }
                    }
                  }
                  */
                  // end of working 3rd level category code

                  if ($total_sub_cat)
                  {
                    $sub_cat = str_replace('&', '&amp;', $sub_cat);
                    $sub_cat = $sqlm->quote_smart($sub_cat);
                    $temp_cat_output .= '
                      <tr>
                        <th colspan="5" align="left">
                          <div id="div'.$sub_cat_id.'" onclick="expand(\'t'.$sub_cat_id.'\', this, \''.$sub_cat.' ('.$total_sub_cat.')\');">[+] '.$sub_cat.' ('.$total_sub_cat.')</div>
                        </th>
                      </tr>
                      <tr>
                        <td colspan="5">
                          <table id="t'.$sub_cat_id.'" style="width: 500px; display: none;">'.$temp_sub_cat_output;
                    if ($j)
                      $temp_cat_output .= '
                            <tr>
                              <th colspan="3" align="left">'.$lang_char['achievement_title'].'</th>
                              <th width="5%">'.$lang_char['achievement_points'].'</th>
                              <th width="10%">'.$lang_char['achievement_date'].'</th>
                            </tr>'.$temp_sub_output;
                    $temp_cat_output .= '
                          </table>
                        </td>
                      </tr>';
                  }
                }
              }
            }
            if ($total_cat)
            {
              $cat = str_replace('&', '&amp;', $cat);
              $cat = $sqlm->quote_smart($cat);
              $output .='
                <tr>
                  <th align="left">
                    <div id="div'.$cat_id.'" onclick="expand(\'t'.$cat_id.'\', this, \''.$cat['name01'].' ('.$total_cat.')\');">[+] '.$cat['name01'].' ('.$total_cat.')</div>
                  </th>
                </tr>
                <tr>
                  <td>
                    <table id="t'.$cat_id.'" class="lined" style="width: 500px; display: none;">'.$temp_cat_output;
              if ($i)
                $output .='
                      <tr>
                        <th colspan="3" align="left">'.$lang_char['achievement_title'].'</th>
                        <th width="5%">'.$lang_char['achievement_points'].'</th>
                        <th width="10%">'.$lang_char['achievement_date'].'</th>
                      </tr>'.$temp_output;
              $output .='
                    </table>
                  </td>
                </tr>';
            }
          }
        }
        $output .='
                <tr>
                  <td>
                    Achievement Points: '.$points.'
                  </td>';
      }
      else
      {
        $output .= '
              <table class="lined" style="width: 550px;">
                <tr>
                  <td width="100%" align="right" colspan="4">';

        // for multipage support
        $result = $sqlc->query('SELECT count(*) FROM character_achievement WHERE guid = '.$id.'');
        $all_record = $sqlc->result($result,0);

        // main data that we need for this page, character achievements
        $result = $sqlc->query('SELECT achievement, date FROM character_achievement
          WHERE guid = '.$id.' ORDER BY '.$order_by.' '.$order_dir.' LIMIT '.$start.', '.$itemperpage.'');

        // multi page links
        $output .= generate_pagination('char_achieve.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).'', $all_record, $itemperpage, $start);
        $output .= '
                    </td>
                  </tr>
                  <tr>';

        //---------------Page Specific Data Starts Here--------------------------
        // developer note: for now we are only able to list achievements,
        //   their categories, rewards and date, only date is sortable
        // todo: group by categories, and if possible sort by other fields.

        // column headers, with links for sorting
        $output .= '
                    <th width="30%">'.$lang_char['achievement_category'].'</th>
                    <th width="50%">'.$lang_char['achievement_title'].'</th>
                    <th width="1%">'.$lang_char['achievement_points'].'</th>
                    <th width="1%"><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=date&amp;start='.$start.'&amp;dir='.$dir.'"'.($order_by==='date' ? ' class="'.$order_dir.'"' : '').'>'.$lang_char['achievement_date'].'</a></th>
                  </tr>';

        // we match character data with info from MiniManager database using achievement library

        while ($data = $sqlc->fetch_assoc($result))
        {
          $output .= '
                  <tr>
                    <td>'.achieve_get_category($data['achievement'], $sqlm).'</td>
                    <td align="left"><a href="'.$achievement_datasite.$data['achievement'].'" target="_blank">'.achieve_get_name($data['achievement'], $sqlm).'</a><br />'.achieve_get_reward($data['achievement'], $sqlm).'</td>
                    <td>'.achieve_get_points($data['achievement'], $sqlm).' <img src="img/money_achievement.gif" alt="" /></td>
                    <td>'.date('o-m-d', $data['date']).'</td>
                  </tr>';
        }
        unset($data);
        unset($result);
        $output .= '
                  <tr>
                    <td class="hidden" width="100%" align="right" colspan="4">';
        // multi page links
        $output .= generate_pagination('char_achieve.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).'', $all_record, $itemperpage, $start);
        unset($all_record);
        $output .= '
                    </td>';
      }
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


//#############################################################################
// MAIN
//#############################################################################

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// load language
$lang_char = lang_char();

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
char_achievements($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
