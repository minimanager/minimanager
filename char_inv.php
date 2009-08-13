<?php


// page header, and any additional required libraries
require_once 'header.php';
require_once 'scripts/defines.php';
require_once 'scripts/get_lib.php';
require_once 'libs/char_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// SHOW INV. AND BANK ITEMS
//########################################################################################################################
function char_inv(&$sqlr, &$sqlc)
{
  global $lang_global, $lang_char, $lang_item, $output, $realm_id, $realm_db, $world_db, $characters_db, $mmfpm_db,
    $action_permission, $user_lvl, $user_name, $item_datasite;
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

  // getting character data from database
  $result = $sqlc->query('SELECT account, name, race, class,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_LEVEL+1).'), " ", -1) AS UNSIGNED) AS level,
    mid(lpad( hex( CAST(substring_index(substring_index(data, " ", '.(CHAR_DATA_OFFSET_GENDER+1).'), " ",-1) as unsigned) ), 8, 0), 4, 1) as gender,
    CAST( SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", '.(CHAR_DATA_OFFSET_GOLD+1).'), " ", -1) AS UNSIGNED) AS gold
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
    if (($user_lvl > $owner_gmlvl)||($owner_name == $user_name))
    {
      // main data that we need for this page, character inventory
      $result = $sqlc->query('SELECT ci.bag, ci.slot, ci.item, ci.item_template,
        SUBSTRING_INDEX(SUBSTRING_INDEX(data, " ", 15), " ", -1) as stack_count
        FROM character_inventory ci INNER JOIN item_instance ii on ii.guid = ci.item
        WHERE ci.guid = '.$id.' ORDER BY ci.bag,ci.slot');

      //---------------Page Specific Data Starts Here--------------------------
      // lets start processing first before we display anything
      //  we have lots to do for inventory

      // character bags, 1 main + 4 additional
      $bag = array
      (
        0=>array(),
        1=>array(),
        2=>array(),
        3=>array(),
        4=>array()
      );

      // character bang, 1 main + 7 additional
      $bank = array
      (
        0=>array(),
        1=>array(),
        2=>array(),
        3=>array(),
        4=>array(),
        5=>array(),
        6=>array(),
        7=>array()
      );

      // this is where we will put items that are in main bank
      $bank_bag_id = array();
      // this is where we will put items that are in main bag
      $bag_id = array();
      // this is where we will put items that are in character bags, 4 arrays, 1 for each
      $equiped_bag_id = array(0,0,0,0,0);
      // this is where we will put items that are in bank bangs, 7 arrays, 1 for each
      $equip_bnk_bag_id = array(0,0,0,0,0,0,0,0);

// developer note: Xiong Guoy 2009-08-14
// note to self:
//  i left off here, just finish passing sql link by reference to get_item_icon
// todo: code optimization, documentation

      $sqlw = new SQL;
      $sqlw->connect($world_db[$realmid]['addr'], $world_db[$realmid]['user'], $world_db[$realmid]['pass'], $world_db[$realmid]['name']);

      while ($slot = $sqlc->fetch_row($result))
      {
        if ($slot[0] == 0 && $slot[1] > 18)
        {
          if($slot[1] < 23) // SLOT 19 TO 22 (Bags)
          {
            $bag_id[$slot[2]] = ($slot[1]-18);
            $equiped_bag_id[$slot[1]-18] = array($slot[3], $sqlw->result($sqlw->query("SELECT ContainerSlots FROM `".$world_db[$realmid]['name']."`.`item_template` WHERE entry ='{$slot[3]}'"), 0, 'ContainerSlots'), $slot[4]);
          }
          elseif($slot[1] < 39) // SLOT 23 TO 38 (BackPack)
          {
            if(isset($bag[0][$slot[1]-23]))
              $bag[0][$slot[1]-23][0]++;
            else $bag[0][$slot[1]-23] = array($slot[3],0,$slot[4]);
          }
          elseif($slot[1] < 67) // SLOT 39 TO 66 (Bank)
          {
            $bank[0][$slot[1]-39] = array($slot[3],0,$slot[4]);
          }
          elseif($slot[1] < 74) // SLOT 67 TO 73 (Bank Bags)
          {
            $bank_bag_id[$slot[2]] = ($slot[1]-66);
            $equip_bnk_bag_id[$slot[1]-66] = array($slot[3], $sqlw->result($sqlw->query("SELECT ContainerSlots FROM `".$world_db[$realmid]['name']."`.`item_template` WHERE entry ='{$slot[3]}'"), 0, 'ContainerSlots'), $slot[4]);
          }
        }
        else
        {
          // Bags
          if (isset($bag_id[$slot[0]]))
          {
            if(isset($bag[$bag_id[$slot[0]]][$slot[1]]))
            $bag[$bag_id[$slot[0]]][$slot[1]][1]++;
            else
              $bag[$bag_id[$slot[0]]][$slot[1]] = array($slot[3],0,$slot[4]);
          }
          // Bank Bags
          elseif (isset($bank_bag_id[$slot[0]]))
          {
            $bank[$bank_bag_id[$slot[0]]][$slot[1]] = array($slot[3],0,$slot[4]);
          }
        }
      }

      $output .= "
        <center>
          <div id=\"tab\">
            <ul>
            <li><a href=\"char.php?id=$id&amp;realm=$realmid\">{$lang_char['char_sheet']}</a></li>
            <li id=\"selected\"><a href=\"char_inv.php?id=$id&amp;realm=$realmid\">{$lang_char['inventory']}</a></li>
            <li><a href=\"char_talent.php?id=$id&amp;realm=$realmid\">{$lang_char['talents']}</a></li>
            <li><a href=\"char_achieve.php?id=$id&amp;realm=$realmid\">{$lang_char['achievements']}</a></li>
            <li><a href=\"char_quest.php?id=$id&amp;realm=$realmid\">{$lang_char['quests']}</a></li>
            <li><a href=\"char_friends.php?id=$id&amp;realm=$realmid\">{$lang_char['friends']}</a></li>
            </ul>
          </div>
          <div id=\"tab_content\">
            <font class=\"bold\">".htmlentities($char['name'])." - <img src='img/c_icons/{$char['race']}-{$char['gender']}.gif' onmousemove='toolTip(\"".char_get_race_name($char['race'])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> <img src='img/c_icons/{$char['class']}.gif' onmousemove='toolTip(\"".char_get_class_name($char['class'])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" /> - lvl ".char_get_level_color($char['level'])."</font>
            <br />
            <br />
            <table class=\"lined\" style=\"width: 700px;\">
              <tr>
                <th>";

      $sqlm = new SQL;
      $sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

      if($equiped_bag_id[1])
      {
        $output .="
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equiped_bag_id[1][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equiped_bag_id[1][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
        $output .= "
                  {$lang_item['bag']} I<br />
                  <font class=\"small\">({$equiped_bag_id[1][1]} {$lang_item['slots']})</font>";
      }
      $output .= "
                </th>
                <th>";
      if($equiped_bag_id[2])
      {
        $output .="
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equiped_bag_id[2][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equiped_bag_id[2][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
        $output .= "
                  {$lang_item['bag']} II<br />
                  <font class=\"small\">({$equiped_bag_id[2][1]} {$lang_item['slots']})</font>";
      }
      $output .= "
                </th>
                <th>";
      if($equiped_bag_id[3])
      {
        $output .="
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equiped_bag_id[3][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equiped_bag_id[3][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
        $output .= "
                  {$lang_item['bag']} III<br />
                  <font class=\"small\">({$equiped_bag_id[3][1]} {$lang_item['slots']})</font>";
      }
      $output .= "
                </th>
                <th>";
      if($equiped_bag_id[4])
      {
        $output .="
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equiped_bag_id[4][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equiped_bag_id[4][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
        $output .= "
                  {$lang_item['bag']} IV<br />
                  <font class=\"small\">({$equiped_bag_id[4][1]} {$lang_item['slots']})</font>";
      }
      $output .= "
                </th>
              </tr>
              <tr>";
      // adds equipped bag slots
      for($t = 1; $t < count($bag); $t++)
      {
        $output .= "
                <td class=\"bag\" valign=\"bottom\" align=\"center\">
                  <div style=\"width:".(4*43)."px;height:".(ceil($equiped_bag_id[$t][1]/4)*41)."px;\">";

        $dsp = $equiped_bag_id[$t][1]%4;
        if ($dsp)
          $output .= "
                    <div class=\"no_slot\" /></div>";
        foreach ($bag[$t] as $pos => $item)
        {
          $output .= "
                    <div style=\"left:".(($pos+$dsp)%4*42)."px;top:".(floor(($pos+$dsp)/4)*41)."px;\">";

          $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite{$item[0]}\" target=\"_blank\">
                    <img src=\"".get_item_icon($item[0], $sqlm, $sqlw)."\" alt=\"\" />".($item[1] ? ($item[1]+1) : "")."
                  </a>";
          $item[2] = $item[2] == 1 ? '' : $item[2];
          $output .= "
                      <div style=\"width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px\">$item[2]</div>
                      <div style=\"width:25px;margin:-21px 0px 0px 17px;font-size:14px\">$item[2]</div>
                    </div>";
        }
        $output .= "
                  </div>
                </td>";
      }
      $output .= "
              </tr>
              <tr>
                <th colspan=\"2\" align=\"left\">
                  <img class=\"bag_icon\" src=\"".get_item_icon(3960, $sqlm, $sqlw)."\" alt=\"\" align=\"middle\" style=\"margin-left:100px;\" />
                  <font style=\"margin-left:30px;\">{$lang_char['backpack']}</font>
                </th>
                <th colspan=\"2\">
                  {$lang_char['bank_items']}
                </th>
              </tr>
              <tr>
                <td colspan=\"2\" class=\"bag\" align=\"center\" height=\"220px\">
                  <div style=\"width:".(4*43)."px;height:".(ceil(16/4)*41)."px;\">";
      // inventory items
      foreach ($bag[0] as $pos => $item)
      {
        $output .= "
                    <div style=\"left:".($pos%4*42)."px;top:".(floor($pos/4)*41)."px;\">";
        $output .= "
                      <a style=\"padding:2px;\" href=\"$item_datasite{$item[0]}\" target=\"_blank\">
                        <img src=\"".get_item_icon($item[0], $sqlm, $sqlw)."\" alt=\"\" />".($item[1] ? ($item[1]+1) : "")."
                      </a>";
        $item[2] = $item[2] == 1 ? '' : $item[2];
        $output .= "
                      <div style=\"width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px\">$item[2]</div>
                      <div style=\"width:25px;margin:-21px 0px 0px 17px;font-size:14px\">$item[2]</div>
                    </div>";
      }
      $money_gold = (int)($char['gold']/10000);
      $money_silver = (int)(($char['gold']-$money_gold*10000)/100);
      $money_cooper = (int)($char['gold']-$money_gold*10000-$money_silver*100);
      $output .= "
                  </div>
                  <div style=\"text-align:right;width:168px;background-image:none;background-color:#393936;padding:2px;\">
                    <b>
                      $money_gold   <img src=\"img/gold.gif\" alt=\"\" align=\"middle\" />
                      $money_silver <img src=\"img/silver.gif\" alt=\"\" align=\"middle\" />
                      $money_cooper <img src=\"img/copper.gif\" alt=\"\" align=\"middle\" />
                    </b>";
      $output .= "
                  </div>
                </td>
                <td colspan=\"2\" class=\"bank\" align=\"center\">
                  <div style=\"width:".(7*43)."px;height:".(ceil(24/7)*41)."px;\">";
      // bank items
      foreach ($bank[0] as $pos => $item)
      {
        $output .= "
                    <div style=\"left:".($pos%7*43)."px;top:".(floor($pos/7)*41)."px;\">";
        $output .= "
                      <a style=\"padding:2px;\" href=\"$item_datasite{$item[0]}\" target=\"_blank\">
                        <img src=\"".get_item_icon($item[0], $sqlm, $sqlw)."\" class=\"inv_icon\" alt=\"\" />
                      </a>";
        $item[2] = $item[2] == 1 ? '' : $item[2];
        $output .= "
                      <div style=\"width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px\">$item[2]</div>
                      <div style=\"width:25px;margin:-21px 0px 0px 17px;font-size:14px\">$item[2]</div>
                    </div>";
      }
      $output .= "
                  </div>
                </td>
              </tr>
              <tr>
                <th>";
      if($equip_bnk_bag_id[1])
      {
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equip_bnk_bag_id[1][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equip_bnk_bag_id[1][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
        $output .= "
                  {$lang_item['bag']} I<br />
                  <font class=\"small\">({$equip_bnk_bag_id[1][1]} {$lang_item['slots']})</font>";
      }
      $output .= "
                </th>
                <th>";
      if($equip_bnk_bag_id[2])
      {
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equip_bnk_bag_id[2][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equip_bnk_bag_id[2][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
        $output .= "
                  {$lang_item['bag']} II<br />
                  <font class=\"small\">({$equip_bnk_bag_id[2][1]} {$lang_item['slots']})</font>";
      }
      $output .= "
                </th>
                <th>";
      if($equip_bnk_bag_id[3])
      {
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equip_bnk_bag_id[3][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equip_bnk_bag_id[3][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
                  $output .= "
                  {$lang_item['bag']} III<br />
                  <font class=\"small\">({$equip_bnk_bag_id[3][1]} {$lang_item['slots']})</font>";
      }
      $output .= "
                </th>
                <th>";
      if($equip_bnk_bag_id[4])
      {
        $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equip_bnk_bag_id[4][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equip_bnk_bag_id[4][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
        $output .= "
                  {$lang_item['bag']} IV<br />
                  <font class=\"small\">({$equip_bnk_bag_id[4][1]} {$lang_item['slots']})</font>";
      }
      $output .= "
                </th>
              </tr>
              <tr>";
      for($t=1; $t < count($bank); $t++)
      {
        if($t==5)
        {
          $output .= "
              </tr>
              <tr>
                <th>";
          if($equip_bnk_bag_id[5])
          {
            $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equip_bnk_bag_id[5][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equip_bnk_bag_id[5][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
            $output .= "
                  {$lang_item['bag']} V<br />
                  <font class=\"small\">({$equip_bnk_bag_id[5][1]} {$lang_item['slots']})</font>";
          }
          $output .= "
                </th>
                <th>";
          if($equip_bnk_bag_id[6])
          {
            $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equip_bnk_bag_id[6][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equip_bnk_bag_id[6][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
            $output .= "
                  {$lang_item['bag']} VI<br />
                  <font class=\"small\">({$equip_bnk_bag_id[6][1]} {$lang_item['slots']})</font>";
          }
          $output .= "
                </th>
                <th>";
          if($equip_bnk_bag_id[7])
          {
            $output .= "
                  <a style=\"padding:2px;\" href=\"$item_datasite{$equip_bnk_bag_id[7][0]}\" target=\"_blank\">
                    <img class=\"bag_icon\" src=\"".get_item_icon($equip_bnk_bag_id[7][0], $sqlm, $sqlw)."\" alt=\"\" />
                  </a>";
            $output .= "
                  {$lang_item['bag']} VII<br />
                  <font class=\"small\">({$equip_bnk_bag_id[7][1]} {$lang_item['slots']})</font>";
          }
          $output .= "
                </th>
                <th>
                </th>
              </tr>
              <tr>";
        }
        $output .= "
                <td class=\"bank\" align=\"center\">
                  <div style=\"width:".(4*43)."px;height:".(ceil($equip_bnk_bag_id[$t][1]/4)*41)."px;\">";
        $dsp=$equip_bnk_bag_id[$t][1]%4;
        if ($dsp)
          $output .= "
                    <div class=\"no_slot\" /></div>";
        foreach ($bank[$t] as $pos => $item)
        {
          $output .= "
                    <div style=\"left:".(($pos+$dsp)%4*43)."px;top:".(floor(($pos+$dsp)/4)*41)."px;\">";
          $output .= "
                      <a style=\"padding:2px;\" href=\"$item_datasite{$item[0]}\" target=\"_blank\">
                        <img src=\"".get_item_icon($item[0], $sqlm, $sqlw)."\" alt=\"\" />
                      </a>";
          $item[2] = $item[2] == 1 ? '' : $item[2];
          $output .= "
                      <div style=\"width:25px;margin:-20px 0px 0px 18px;color: black; font-size:14px\">$item[2]</div>
                      <div style=\"width:25px;margin:-21px 0px 0px 17px;font-size:14px\">$item[2]</div>
                    </div>";
        }
        $output .= "
                  </div>
                </td>";
      }
      $output .= "
                <td class=\"bank\"></td>
              </tr>
            </table>
          </div>
          <br />
          <table class=\"hidden\">
            <tr>
              <td>";
                makebutton($lang_char['chars_acc'], "user.php?action=edit_user&amp;id=$owner_acc_id",130);
      $output .= "
              </td>
              <td>";
      if (($user_lvl > $owner_gmlvl)&&($user_lvl >= $action_permission['delete']))
      {
                makebutton($lang_char['edit_button'], "char_edit.php?id=$id&amp;realm=$realmid",130);
        $output .= "
              </td>
              <td>";
      }
      if ((($user_lvl > $owner_gmlvl)&&($user_lvl >= $action_permission['delete']))||($owner_name == $user_name))
      {
                makebutton($lang_char['del_char'], "char_list.php?action=del_char_form&amp;check%5B%5D=$id\" type=\"wrn",130);
        $output .= "
              </td>
              <td>";
      }
      if ($user_lvl >= $action_permission['update'])
      {
                makebutton($lang_char['send_mail'], 'mail.php?type=ingame_mail&amp;to='.$char['name'].'',130);
        $output .= "
              </td>
              <td>";
      }
                makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);
      $output .= "
              </td>
            </tr>
          </table>
          <br />
        </center>
";
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

// action variable reserved for future use
//$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// load language
$lang_char = lang_char();

// we getting links to realm database and character database left behind by header
// header does not need them anymore, might as well reuse the link
char_inv($sqlr, $sqlc);

//unset($action);
unset($action_permission);
unset($lang_char);

require_once("footer.php");

?>
