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


  $show_type = (isset($_POST['show_type'])) ? $sqlc->quote_smart($_POST['show_type']) : 0;
  if (is_numeric($show_type)); else $show_type = 0;

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
          <script>
              function expand(thistag)
              {
                var i = 0;
                %%REPLACE%%

                if (thistag == \'tsummary\')
                {
                  document.getElementById(\'tsummary\').style.display="table";
                  document.getElementById(\'divsummary\').innerHTML = \'[-] '.$lang_char['summary'].'\' ;
                  for(x in main_cats)
                  {
                    if(document.getElementById(main_cats[x]).style.display=="table")
                    {
                      document.getElementById(main_cats[x]).style.display="none";
                      document.getElementById(main_cats_achieve[x]).style.display="none";
                      document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                    }
                  }
                  for(x in main_sub_cats)
                  {
                    if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                    {
                      document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                      document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                    }
                  }
                }
                else
                {
                  if (document.getElementById(\'tsummary\').style.display="table")
                  {
                    document.getElementById(\'tsummary\').style.display="none";
                    document.getElementById(\'divsummary\').innerHTML = \'[+] '.$lang_char['summary'].'\' ;
                  }
                  for(x in main_cats)
                  {
                    if (main_cats[x] == thistag)
                    {
                      i = 1;
                    }
                  }

                  if (i == 1)
                  {
                    for(x in main_cats)
                    {
                      if (main_cats[x] == thistag)
                      {
                        if(document.getElementById(main_cats[x]).style.display=="table")
                        {
                          document.getElementById(main_cats[x]).style.display="none";
                          document.getElementById(main_cats_achieve[x]).style.display="none";
                          document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                          document.getElementById(\'tsummary\').style.display="table";
                          document.getElementById(\'divsummary\').innerHTML = \'[-] '.$lang_char['summary'].'\' ;
                        }
                        else
                        {
                          document.getElementById(main_cats[x]).style.display="table";
                          document.getElementById(main_cats_achieve[x]).style.display="table";
                          document.getElementById(main_cats_div[x]).innerHTML = \'[-] \' + main_cats_name[x];
                        }
                      }
                      else
                      {
                        if(document.getElementById(main_cats[x]).style.display=="table")
                        {
                          document.getElementById(main_cats[x]).style.display="none";
                          document.getElementById(main_cats_achieve[x]).style.display="none";
                          document.getElementById(main_cats_div[x]).innerHTML = \'[+] \' + main_cats_name[x];
                        }
                      }
                    }
                    for(x in main_sub_cats)
                    {
                      if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                      {
                        document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                        document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                      }
                    }
                  }
                  else if (i == 0)
                  {
                    for(x in main_sub_cats)
                    {
                      if (main_sub_cats[x] == thistag)
                      {
                        if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                        {
                          document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                          document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                        }
                        else
                        {
                          document.getElementById(main_sub_cats_achieve[x]).style.display="table";
                          document.getElementById(main_sub_cats_div[x]).innerHTML = \'[-] \' + main_sub_cats_name[x];
                        }
                      }
                      else
                      {
                        if(document.getElementById(main_sub_cats_achieve[x]).style.display=="table")
                        {
                          document.getElementById(main_sub_cats_achieve[x]).style.display="none";
                          document.getElementById(main_sub_cats_div[x]).innerHTML = \'[+] \' + main_sub_cats_name[x];
                        }
                      }
                    }
                    for(x in main_cats)
                    {
                      if(document.getElementById(main_cats_achieve[x]).style.display=="table")
                      {
                        document.getElementById(main_cats_achieve[x]).style.display="none";
                      }
                    }
                  }
                }
              }
            </script>
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
              <table class="top_hidden" style="width: 90%;">
                <tr>
                  <td witdh="80%">
                  </td>
                  %%REPLACE_POINTS%%
                  <td align="right">
                    <form action="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'" method="post" name="form">
                      '.$lang_char['show'].' :
                      <select name="show_type">
                        <option value="1"';
        if (1 == $show_type)
          $output .= ' selected="selected"';
        $output .= '>'.$lang_char['all'].'</option>
                        <option value="0"';
        if (0 == $show_type)
          $output .= ' selected="selected"';
        $output .= '>'.$lang_char['earned'].'</option>
                        <option value="2"';
        if (2 == $show_type)
          $output .= ' selected="selected"';
        $output .= '>'.$lang_char['incomplete'].'</option>
                      </select>
                    </form>
                  </td>
                  <td align="right">';
                    makebutton('View', 'javascript:do_submit()', 130);
        $output .= '
                  </td>
                </tr>
              </table>
              <table class="lined" style="width: 90%;">
                <tr valign="top">
                  <td width="30%">
                    <table class="hidden" style="width: 100%">
                      <tr>
                         <th align="left">
                           <div id="divsummary" onclick="expand(\'tsummary\')">[-] '.$lang_char['summary'].'</div>
                         </th>
                      </tr>
                      <tr>
                        <td>
                        </td>
                      </tr>';
        $result = $sqlc->query('SELECT achievement, date FROM character_achievement WHERE guid = '.$id.'');
        $char_achieve = array();
        while ($temp = $sqlc->fetch_assoc($result))
          $char_achieve[$temp['achievement']] = $temp['date'];
        $result = $sqlc->query('SELECT achievement, date FROM character_achievement WHERE guid = \''.$id.'\' order by date DESC limit 4');

        $points = 0;

        $main_cats = achieve_get_main_category($sqlm);
        $sub_cats  = achieve_get_sub_category($sqlm);

        $output_achieve_main_cat = array();
        $output_u_achieve_main_cat = array();
        $output_achieve_sub_cat = array();
        $output_u_achieve_sub_cat = array();

        $js_main_cats = '
                var main_cats = new Array();
                var main_cats_div = new Array();
                var main_cats_name = new Array();
                var main_cats_achieve = new Array();
                var main_sub_cats = new Array();
                var main_sub_cats_div = new Array();
                var main_sub_cats_name = new Array();
                var main_sub_cats_achieve = new Array();';

        foreach($main_cats as $cat_id => $cat)
        {
          if (isset($cat['name01']))
          {
            $i=0;
            $output_achieve_main_cat[$cat_id] = '';
            $output_u_achieve_main_cat[$cat_id] = '';
            $achieve_main_cat = achieve_get_id_category($cat['id'], $sqlm);
            foreach($achieve_main_cat as $achieve_id => $id)
            {
              if (isset($achieve_id) && isset($id['id']))
              {
                if (isset($char_achieve[$id['id']]))
                {
                  if (2 > $show_type)
                  {
                    $id['rewarddesc01'] = str_replace('&', '&amp;', $id['rewarddesc01']);
                    $output_achieve_main_cat[$cat_id] .= '
                      <tr>
                        <td width="1%" align="left">
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
                        <td width="15%" align="right">'.date('o-m-d', $char_achieve[$id['id']]).'</td>
                      </tr>';
                    ++$i;
                  }
                  $points += $id['rewpoints'];
                }
                elseif ($show_type && isset($achieve_id))
                {
                  $id['rewarddesc01'] = str_replace('&', '&amp;', $id['rewarddesc01']);
                  $output_u_achieve_main_cat[$cat_id] .= '
                      <tr>
                        <td width="1%" align="left">
                          <a href="'.$achievement_datasite.$id['id'].'" target="_blank">
                            <span style="opacity:0.2;">
                              <img src="'.achieve_get_icon($id['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                            </span>
                          </a>
                        </td>
                        <td colspan="2" align="left">
                          <a href="'.$achievement_datasite.$id['id'].'" target="_blank">'.$id['name01'].'</a><br />
                          '.$id['description01'].'<br />
                          '.$id['rewarddesc01'].'
                        </td>
                        <td width="5%" align="right">'.$id['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                        <td width="15%" align="right">'.$lang_char['incomplete'].'</td>
                      </tr>';
                  ++$i;
                }
              }
            }
            $output_achieve_main_cat[$cat_id] = '
                    <table class="hidden" id="ta'.$cat_id.'" style="width: 100%; display: none;">
                      <tr>
                        <th colspan="3" align="left">'.$lang_char['achievement_title'].'</th>
                        <th width="5%">'.$lang_char['achievement_points'].'</th>
                        <th width="15%">'.$lang_char['achievement_date'].'</th>
                      </tr>'.$output_achieve_main_cat[$cat_id].$output_u_achieve_main_cat[$cat_id].'
                    </table>';
            $js_main_cats .='
                  main_cats_achieve['.$cat_id.'] = "ta'.$cat_id.'";';

            $output_sub_cat = '';
            $total_sub_cat = 0;
            if (isset($sub_cats[$cat['id']]))
            {
              $main_sub_cats = $sub_cats[$cat['id']];
              foreach($main_sub_cats as $sub_cat_id => $sub_cat)
              {
                if (isset($sub_cat))
                {
                  $j=0;
                  $output_achieve_sub_cat[$sub_cat_id] = '';
                  $output_u_achieve_sub_cat[$sub_cat_id] = '';
                  $achieve_sub_cat = achieve_get_id_category($sub_cat_id, $sqlm);
                  foreach($achieve_sub_cat as $achieve_id => $id)
                  {
                    if (isset($achieve_id) && isset($id['id']))
                    {
                      if (isset($char_achieve[$id['id']]))
                      {
                        if (2 > $show_type)
                        {
                          $id['rewarddesc01'] = str_replace('&', '&amp;', $id['rewarddesc01']);
                          $output_achieve_sub_cat[$sub_cat_id] .= '
                            <tr>
                              <td width="1%" align="left">
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
                              <td width="15%" align="right">'.date('o-m-d', $char_achieve[$id['id']]).'</td>
                            </tr>';
                          ++$j;
                        }
                        $points += $id['rewpoints'];
                      }
                      elseif ($show_type && isset($achieve_id))
                      {
                        $id['rewarddesc01'] = str_replace('&', '&amp;', $id['rewarddesc01']);
                        $output_u_achieve_sub_cat[$sub_cat_id] .= '
                            <tr>
                              <td width="1%" align="left">
                                <a href="'.$achievement_datasite.$id['id'].'" target="_blank">
                                  <span style="opacity:0.2;">
                                    <img src="'.achieve_get_icon($id['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                                  </span>
                                </a>
                              </td>
                              <td colspan="2" align="left">
                                <a href="'.$achievement_datasite.$id['id'].'" target="_blank">'.$id['name01'].'</a><br />
                                '.$id['description01'].'<br />
                                '.$id['rewarddesc01'].'
                              </td>
                              <td width="5%" align="right">'.$id['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                              <td width="15%" align="right">'.$lang_char['incomplete'].'/td>
                            </tr>';
                        ++$j;
                      }
                    }
                  }
                  $total_sub_cat = $total_sub_cat + $j;
                  if($j)
                  {
                    $output_sub_cat .='
                              <tr>
                                <th align="left">
                                  <div id="divs'.$sub_cat_id.'" onclick="expand(\'tsa'.$sub_cat_id.'\');">[+] '.$sub_cat.' ('.$j.')</div>
                                </th>
                              </tr>';
                    $js_main_cats .='
                      main_sub_cats['.$sub_cat_id.']      = "tsa'.$sub_cat_id.'";
                      main_sub_cats_div['.$sub_cat_id.']  = "divs'.$sub_cat_id.'";
                      main_sub_cats_name['.$sub_cat_id.'] = "'.$sub_cat.' ('.$j.')";';
                    $output_achieve_sub_cat[$sub_cat_id] = '
                      <table class="hidden" id="tsa'.$sub_cat_id.'" style="width: 100%; display: none;">
                        <tr>
                          <th colspan="3" align="left">'.$lang_char['achievement_title'].'</th>
                          <th width="5%">'.$lang_char['achievement_points'].'</th>
                          <th width="15%">'.$lang_char['achievement_date'].'</th>
                        </tr>'.$output_achieve_sub_cat[$sub_cat_id].$output_u_achieve_sub_cat[$sub_cat_id].'
                      </table>';
                    $js_main_cats .='
                      main_sub_cats_achieve['.$sub_cat_id.'] = "tsa'.$sub_cat_id.'";';
                  }
                }
              }
            }
            if($total_sub_cat || $i)
            {
              $output .='
                        <tr>
                          <th align="left">
                            <div id="div'.$cat_id.'" onclick="expand(\'t'.$cat_id.'\');">[+] '.$cat['name01'].' ('.($i+$total_sub_cat).')</div>
                          </th>
                        </tr>
                        <tr>
                          <td>
                            <table class="hidden" id="t'.$cat_id.'" style="width: 100%; display: none;">'.$output_sub_cat.'
                            </table>
                          </td>
                        </tr>';
              unset($output_sub_cat);
              $js_main_cats .='
                    main_cats['.$cat_id.']      = "t'.$cat_id.'";
                    main_cats_div['.$cat_id.']  = "div'.$cat_id.'";
                    main_cats_name['.$cat_id.'] = "'.$cat['name01'].' ('.($i+$total_sub_cat).')";';
            }
          }
        }

        $output = str_replace('%%REPLACE%%', $js_main_cats, $output);
        unset($js_main_cats);
        $output = str_replace('%%REPLACE_POINTS%%', '
                  <td align="right">
                    '.$lang_char['achievements'].' '.$lang_char['achievement_points'].': '.$points.'
                  </td>', $output);

        $output .= '
                    </table>
                  </td>
                  <td>';

        foreach($output_achieve_main_cat as $temp)
          $output .= $temp;
        unset($temp);
        unset($output_achieve_main_cat);

        foreach($output_achieve_sub_cat as $temp)
          $output .= $temp;
        unset($temp);
        unset($output_achieve_sub_cat);

        $output .= '
                    <table class="hidden" id="tsummary" style="width: 100%; display: table;">
                      <tr>
                        <th colspan="5">
                          '.$lang_char['recent'].' '.$lang_char['achievements'].'
                        </th>
                      </tr>
                      <tr>
                        <th colspan="3" align="left">'.$lang_char['achievement_title'].'</th>
                        <th width="5%">'.$lang_char['achievement_points'].'</th>
                        <th width="15%">'.$lang_char['achievement_date'].'</th>
                      </tr>';
        while ($temp = $sqlc->fetch_assoc($result))
        {
          $char_achieve = achieve_get_details($temp['achievement'], $sqlm);
          $output .= '
                      <tr>
                        <td width="1%" align="left">
                          <a href="'.$achievement_datasite.$char_achieve['id'].'" target="_blank">
                            <img src="'.achieve_get_icon($char_achieve['id'], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                          </a>
                        </td>
                        <td colspan="2" align="left">
                          <a href="'.$achievement_datasite.$char_achieve['id'].'" target="_blank">'.$char_achieve['name01'].'</a><br />
                          '.$char_achieve['description01'].'<br />
                          '.$char_achieve['rewarddesc01'].'
                        </td>
                        <td width="5%" align="right">'.$char_achieve['rewpoints'].' <img src="img/money_achievement.gif" alt="" /></td>
                        <td width="15%" align="right">'.date('o-m-d', $temp['date']).'</td>
                      </tr>';
        }
        $output .= '
                    </table>';
        unset($result);
        $output .= '
                  </td>
                </tr>';
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
