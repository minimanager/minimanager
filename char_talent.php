<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
require_once("libs/spell_lib.php");
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW CHARACTER TALENTS
//########################################################################################################################
function char_talent(&$sqlr, &$sqlc)
{
  // we shall load this one locally, no need to load in global, it contains a huge array
  require_once 'libs/talent_lib.php';
  global $lang_global, $lang_char, $output, $realm_id, $realm_db, $characters_db, $mmfpm_db,
    $action_permission, $user_lvl, $user_name, $spell_datasite, $talent_calculator_datasite, $developer_test_mode, $new_talent_tab;
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

  $sqlm = new SQL;
  $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

  // gmp reserved for talent tree calculation for use with 3rd party talent calculators
  //check for php gmp extension
  //if (extension_loaded('gmp'))
  //  $GMP=1;
  //else
  //  $GMP=0;
  //end of gmp check

  //-------------------SQL Injection Prevention--------------------------------
  // no point going further if we don have a valid ID
  $id = $sqlc->quote_smart($_GET['id']);
  if (is_numeric($id));
  else error($lang_global['empty_fields']);

  $order_by = (isset($_GET['order_by'])) ? $sqlc->quote_smart($_GET['order_by']) : 1;
  $dir = (isset($_GET['dir'])) ? $sqlc->quote_smart($_GET['dir']) : 0;
  $dir = ($dir) ? 0 : 1;

  $result = $sqlc->query('SELECT account, name, race, class,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_LEVEL+1).'), " ", -1) AS UNSIGNED) AS level,
    mid(lpad( hex( CAST(substring_index(substring_index(data, " ", '.(CHAR_DATA_OFFSET_GENDER+1).'), " ", -1) as unsigned) ), 8, 0), 4, 1) as gender
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
      $result = $sqlc->query('SELECT spell FROM character_spell WHERE guid = '.$id.' ORDER BY spell ASC');

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
              <br /><br />';
      if($developer_test_mode && $new_talent_tab)
      {
        $tabs = array();
        if ($sqlc->num_rows($result))
        {
          $i = 0;
          while (($talent = $sqlc->fetch_assoc($result)) && ($i < 120))
          {
            if ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank5 = '.$talent['spell'].' LIMIT 1')))
            {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'5');
              ++$i;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $i, $sqlm);
            }
            elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank4 = '.$talent['spell'].' LIMIT 1')))
            {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'4');
              ++$i;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $i, $sqlm);
            }
            elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank3 = '.$talent['spell'].' LIMIT 1')))
            {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'3');
              ++$i;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $i, $sqlm);
            }
            elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank2 = '.$talent['spell'].' LIMIT 1')))
            {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'2');
              ++$i;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $i, $sqlm);
            }
            elseif ($tab = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, dependsOn from dbc_talent where rank1 = '.$talent['spell'].' LIMIT 1')))
            {
              $tabs[$tab['tab']][$tab['row']][$tab['col']] = array($talent['spell'],'1');
              ++$i;
              if ($tab['dependsOn'])
                talent_dependencies($tabs, $tab, $i, $sqlm);
            }
          }
          $output .= '
              <table class="lined" style="width: 550px;">
                <tr valign="top" align="center">';
          foreach ($tabs as $k=>$data)
          {
            $points = 0;
            $output .= '
                  <td>
                    <table class="hidden" style="width: 0px;">
                     <tr>
                       <td colspan="6" style="border-bottom-width: 0px;">
                       </td>
                     </tr>';
            for($i=0;$i<11;++$i)
            {
              $output .= '
                      <tr>';
              for($j=0;$j<4;++$j)
              {
                if(isset($data[$i][$j]))
                {
                  $output .= '
                        <td valign="bottom" align="center" style="border-top-width: 0px;border-bottom-width: 0px;">
                          <a href="'.$spell_datasite.$data[$i][$j][0].'" target="_blank">
                            <img src="'.get_spell_icon($data[$i][$j][0], $sqlm).'" width="36" height="36" class="icon_border_0" alt="" />
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
                      </tr>';
            }
            $output .= '
                     <tr>
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
          $output .= '
                </tr>';
        }
      }
      else
      {
        $output .= '
              <table class="lined" style="width: 550px;">
                <tr>
                  <th><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=0&amp;dir='.$dir.'">'.($order_by==0 ? '<img src="img/arr_'.($dir ? 'up' : 'dw').'.gif" alt="" />' : '').$lang_char['talent_id'].'</a></th>
                  <th align="left"><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'&amp;order_by=1&amp;dir='.$dir.'">'.($order_by==1 ? '<img src="img/arr_'.($dir ? 'up' : 'dw').'.gif" alt="" />' : '').$lang_char['talent_name'].'</a></th>
                </tr>';
        $talents_1 = array();
        if ($sqlc->num_rows($result))
        {
          while ($talent = $sqlc->fetch_assoc($result))
          {
            if(talent_get_value($talent['spell']))
              array_push($talents_1, array($talent['spell'], get_spell_name($talent['spell'], $sqlm)));
          }
          aasort($talents_1, $order_by, $dir);
          //if ($GMP)
          //  $talent_sum = gmp_init(0);
          foreach ($talents_1 as $data)
          {
            $output .= '
                <tr>
                  <td>'.$data[0].'</td>
                  <td align="left">
                    <a style="padding:2px;" href="'.$spell_datasite.$data[0].'" target="_blank">
                      <img src="'.get_spell_icon($data[0], $sqlm).'" alt="" />
                    </a>
                    <a href="'.$spell_datasite.$data[0].'" target="_blank">'.$data[1].'</a>
                  </td>';
            //if ($GMP)
            //  $talent_sum = gmp_add($talent_sum,sprintf('%s',talent_get_value($data[0])));
            $output .= '
                </tr>';
          }
        }
        /*
        // reserved till we get to calculate talent points using the new data we have in db
        $playerclass = strtolower(char_get_class_name($char[3]));
        switch ($playerclass)
        {
          case "shaman":
            $padlength = 61;
            break;
          case "druid":
            $padlength = 62;
            break;
          case "warlock":
          case "paladin":
          case "hunter":
          case "priest":
            $padlength = 64;
            break;
          case "warrior":
            $padlength = 66;
            break;
          case "rogue":
          case "mage":
            $padlength = 67;
            break;
        }
        if ($GMP)
          $output .= "
                <tr>
                  <td>
                    <a href=\"".$talent_calculator_datasite.$char[3]."&tal=".str_pad(sprintf('%s',gmp_strval($talent_sum)), "0", "0", STR_PAD_LEFT)."\" target=\"_blank\">Talent Calculator</a>
                  </td>
                </tr>";
        */
      }
      //---------------Page Specific Data Ends here----------------------------
      //---------------Character Tabs Footer-----------------------------------
      $output .= '
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
    $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank5'],'5');
    ++$i;
    if ($dep['dependsOn'])
      talent_dependencies($tabs, $dep, $i, $sqlm);
  }
  elseif ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, rank4, dependsOn from dbc_talent where id = '.$tab['dependsOn'].' and rank4 != 0 LIMIT 1')))
  {
    $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank4'],'4');
    ++$i;
    if ($dep['dependsOn'])
      talent_dependencies($tabs, $dep, $i, $sqlm);
  }
  elseif ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, rank3, dependsOn from dbc_talent where id = '.$tab['dependsOn'].' and rank3 != 0 LIMIT 1')))
  {
    $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank3'],'3');
    ++$i;
    if ($dep['dependsOn'])
      talent_dependencies($tabs, $dep, $i, $sqlm);
  }
  elseif ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, rank2, dependsOn from dbc_talent where id = '.$tab['dependsOn'].' and rank2 != 0 LIMIT 1')))
  {
    $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank2'],'2');
    ++$i;
    if ($dep['dependsOn'])
      talent_dependencies($tabs, $dep, $i, $sqlm);
  }
  elseif ($dep = $sqlm->fetch_assoc($sqlm->query('SELECT tab, row, col, rank1, dependsOn from dbc_talent where id = '.$tab['dependsOn'].' and rank1 != 0 LIMIT 1')))
  {
    $tabs[$dep['tab']][$dep['row']][$dep['col']] = array($dep['rank1'],'1');
    ++$i;
    if ($dep['dependsOn'])
      talent_dependencies($tabs, $dep, $i, $sqlm);
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

require_once("footer.php");

?>
