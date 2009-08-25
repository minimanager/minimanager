<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once 'libs/spell_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTER TALENTS
//########################################################################################################################
function char_talent(&$sqlr, &$sqlc)
{
  global $output, $lang_global, $lang_char,
    $realm_id, $realm_db, $characters_db, $mmfpm_db, $server,
    $action_permission, $user_lvl, $user_name, $spell_datasite;
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

  $result = $sqlc->query('SELECT account, name, race, class, level, gender,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_POINTS1+1).'), " ", -1) AS UNSIGNED) AS talent_points
    FROM characters WHERE guid = '.$id.' LIMIT 1');

  if ($sqlc->num_rows($result))
  {
    $char = $sqlc->fetch_assoc($result);

    $owner_acc_id = $sqlc->result($result, 0, 'account');
    $result = $sqlr->query('SELECT gmlevel,username FROM account WHERE id = '.$char['account'].'');
    $owner_gmlvl = $sqlr->result($result, 0, 'gmlevel');
    $owner_name = $sqlr->result($result, 0, 'username');

    if (($user_lvl > $owner_gmlvl)||($owner_name === $user_name))
    {
      $result = $sqlc->query('SELECT spell FROM character_spell WHERE guid = '.$id.' ORDER BY spell DESC');
      $output .= '
          <center>
              <div id="tab">
              <ul>
                <li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['char_sheet'].'</a></li>
                <li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['inventory'].'</a></li>
                <li id="selected"><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['talents'].'</a></li>
                <li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['achievements'].'</a></li>
                <li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['quests'].'</a></li>
                <li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['friends'].'</a></li>
              </ul>
            </div>
            <div id="tab_content">
              <font class="bold">'.htmlentities($char['name']).' -
              <img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif" onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
              <img src="img/c_icons/'.$char['class'].'.gif" onmousemove="toolTip(\''.char_get_class_name($char['class']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" /> - lvl '.char_get_level_color($char['level']).'</font>
              <br /><br />
              <table class="lined" style="width: 550px;">
                <tr valign="top" align="center">';
      if ($sqlc->num_rows($result))
      {
        $talent_rate = (isset($server[$realmid]['talent_rate']) ? $server[$realmid]['talent_rate'] : 1);
        $talent_points = ($char['level'] - 9) * $talent_rate;
        $talent_points_left = $char['talent_points'];
        $talent_points_used = $talent_points - $talent_points_left;

        $sqlm = new SQL;
        $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

        $tabs = array();
        $l = 0;

        while (($talent = $sqlc->fetch_assoc($result)) && ($l < $talent_points_used))
        {
          if ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank5 = '.$talent['spell'].' LIMIT 1')))
          {
            if (empty($tabs[$tab['tab']][$tab['row']][$tab['col']]))
            {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'5');
              $l += 5;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $l, $sqlm);
            }
          }
          elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank4 = '.$talent['spell'].' LIMIT 1')))
          {
            if (empty($tabs[$tab['tab']][$tab['row']][$tab['col']]))
            {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'4');
              $l += 4;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $l, $sqlm);
            }
          }
          elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank3 = '.$talent['spell'].' LIMIT 1')))
          {
            if (empty($tabs[$tab['tab']][$tab['row']][$tab['col']]))
              {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'3');
              $l += 3;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $l, $sqlm);
            }
          }
          elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank2 = '.$talent['spell'].' LIMIT 1')))
          {
            if (empty($tabs[$tab['tab']][$tab['row']][$tab['col']]))
            {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'2');
              $l += 2;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $l, $sqlm);
            }
          }
          elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank1 = '.$talent['spell'].' LIMIT 1')))
          {
            if (empty($tabs[$tab['tab']][$tab['row']][$tab['col']]))
            {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'1');
              $l += 1;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $l, $sqlm);
            }
          }
        }
        unset($tab);
        unset($talent);
        foreach ($tabs as $k=>$data)
        {
          $points = 0;
          $output .= '
                  <td>
                    <table class="hidden" style="width: 0px;">
                     <tr>
                       <td colspan="6" style="border-bottom-width: 0px;">
                       </td>
                     </tr>
                     <tr>';
          for($i=0;$i<11;++$i)
          {
            for($j=0;$j<4;++$j)
            {
              if(isset($data[$i][$j]))
              {
                $output .= '
                        <td valign="bottom" align="center" style="border-top-width: 0px;border-bottom-width: 0px;">
                          <a href="'.$spell_datasite.$data[$i][$j][0].'" target="_blank">
                            <img src="'.spell_get_icon($data[$i][$j][0], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                          </a>
                          <div style="width:0px;margin:-14px 0px 0px 30px;font-size:14px;color:black">'.$data[$i][$j][1].'</div>
                          <div style="width:0px;margin:-14px 0px 0px 29px;font-size:14px;color:white">'.$data[$i][$j][1].'</div>
                        </td>';
                $points += $data[$i][$j][1];
              }
              else
                $output .= '
                        <td valign="bottom" align="center" style="border-top-width: 0px;border-bottom-width: 0px;">
                          <img src="img/blank.gif" width="44" height="44" alt="" />
                        </td>';
            }
            $output .= '
                      </tr>
                      <tr>';
          }
          $output .= '
                       <td colspan="6" style="border-top-width: 0px;border-bottom-width: 0px;">
                       </td>
                     </tr>
                      <tr>
                        <td colspan="6" valign="bottom" align="left">
                         '.$sqlm->result($sqlm->query('SELECT name_loc0 FROM dbc_talenttab WHERE id = '.$k.''), 0, 'name_loc0').': '.$points.'
                        </td>
                      </tr>
                    </table>
                  </td>';
        }
        unset($data);
        unset($k);
        unset($tabs);
        $output .='
                </tr>
              </table>
              <br />
              <table>
                <tr>
                  <td align="left">
                    '.$lang_char['talent_rate'].': <br />
                    '.$lang_char['talent_points'].': <br />
                    '.$lang_char['talent_points_used'].': <br />
                    '.$lang_char['talent_points_shown'].': <br />
                    '.$lang_char['talent_points_left'].':
                  </td>
                  <td align="left">
                    '.$talent_rate.'<br />
                    '.$talent_points.'<br />
                    '.$talent_points_used.'<br />
                    '.$l.'<br />
                    '.$talent_points_left.'
                  </td>
                  <td width="64">
                  </td>
                  <td align="right">';
        unset($l);
        unset($talent_rate);
        unset($talent_points);
        unset($talent_points_used);
        unset($talent_points_left);
        $glyphs = explode(' ', $sqlc->result($sqlc->query('SELECT data FROM characters WHERE guid = '.$id.''), 0));
        for($i=0;$i<6;++$i)
        {
          if ($glyphs[(CHAR_DATA_OFFSET_GLYPHS+($i))])
          {
            $glyph = $sqlm->result($sqlm->query('select spellid from dbc_glyphproperties where id = '.$glyphs[(CHAR_DATA_OFFSET_GLYPHS+($i))].''), 0);
            $output .='
                    <a href="'.$spell_datasite.$glyph.'" target="_blank">
                      <img src="'.spell_get_icon($glyph, $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
                    </a>';
          }
        }
        unset($glyphs);
        $output .='
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
          <!-- end of char_talent.php -->';
    }
    else
      error($lang_char['no_permission']);
  }
  else
    error($lang_char['no_char_found']);

}


function talent_dependencies(&$tabs, &$tab, &$i, &$sqlm)
{
  if ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, rank5, dependsOn from dbc_talent where id = '.$tab['dependsOn'].' and rank5 != 0 LIMIT 1')))
  {
    if(empty($tabs[$dep['tab']][$dep['row']][$dep['col']]))
    {
      $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank5'],'5');
      $i += 5;
      if ($dep['dependsOn'])
        talent_dependencies($tabs, $dep, $i, $sqlm);
    }
  }
  elseif ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, rank4, dependsOn from dbc_talent where id = '.$tab['dependsOn'].' and rank4 != 0 LIMIT 1')))
  {
    if(empty($tabs[$dep['tab']][$dep['row']][$dep['col']]))
    {
      $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank4'],'4');
      $i += 4;
      if ($dep['dependsOn'])
        talent_dependencies($tabs, $dep, $i, $sqlm);
    }
  }
  elseif ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, rank3, dependsOn from dbc_talent where id = '.$tab['dependsOn'].' and rank3 != 0 LIMIT 1')))
  {
    if(empty($tabs[$dep['tab']][$dep['row']][$dep['col']]))
    {
      $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank3'],'3');
      $i += 3;
      if ($dep['dependsOn'])
        talent_dependencies($tabs, $dep, $i, $sqlm);
    }
  }
  elseif ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, rank2, dependsOn from dbc_talent where id = '.$tab['dependsOn'].' and rank2 != 0 LIMIT 1')))
  {
    if(empty($tabs[$dep['tab']][$dep['row']][$dep['col']]))
    {
      $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank2'],'2');
      $i += 2;
      if ($dep['dependsOn'])
        talent_dependencies($tabs, $dep, $i, $sqlm);
    }
  }
  elseif ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, rank1, dependsOn from dbc_talent where id = '.$tab['dependsOn'].' and rank1 != 0 LIMIT 1')))
  {
    if(empty($tabs[$dep['tab']][$dep['row']][$dep['col']]))
    {
      $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank1'],'1');
      $i += 1;
      if ($dep['dependsOn'])
        talent_dependencies($tabs, $dep, $i, $sqlm);
    }
  }
}


//########################################################################################################################
// MAIN
//########################################################################################################################

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

$lang_char = lang_char();

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
char_talent($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once 'footer.php';


?>
