<?php


function lang_guildbank()
{
  $lang_guildbank = array
  (
    'guildbank' => 'Guild Bank',
    'tab' => 'Tab',
    'notfound' => 'Wrong ID, no Guild Bank Found.',

  );
  return $lang_guildbank;
}


require_once("header.php");
require_once("scripts/get_lib.php");
valid_login($action_permission['read']);

//########################################################################################################################
// GUILD BANK
//########################################################################################################################
function guild_bank()
{
  global $lang_global, $lang_guildbank, $output, $characters_db, $realm_id, $item_datasite, $item_icons,
    $developer_test_mode, $guild_bank;
  wowhead_tt();

  if (empty($_GET['id'])) error($lang_global['empty_fields']);

  $sqlc = new SQL;
  $sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  //==========================$_GET and SECURE=================================
  $guild_id = $sqlc->quote_smart($_GET['id']);
  if (!is_numeric($guild_id)) $guild_id = 0;

  if (empty($_GET['tab']))
    $current_tab = 0;
  else
    $current_tab = $sqlc->quote_smart($_GET['tab']);
  if (!is_numeric($current_tab) || ($current_tab > 6)) $current_tab = 0;
  //==========================$_GET and SECURE end=============================

  $result = $sqlc->query("SELECT name, BankMoney FROM `guild` WHERE guildid = $guild_id LIMIT 1");

  if($sqlc->num_rows($result) && $developer_test_mode && $guild_bank)
  {
    $guild_name  = $sqlc->result($result, 0, 'name');
    $bank_gold  = $sqlc->result($result, 0, 'BankMoney');

    $money_gold   = (int)($bank_gold/10000);
    $money_silver = (int)(($bank_gold-$money_gold*10000)/100);
    $money_cooper = (int)($bank_gold-$money_gold*10000-$money_silver*100);
    unset($bank_gold);
    $money = $money_gold."<img src=\"./img/gold.gif\" alt=\"gold\" /> ".$money_silver."<img src=\"./img/silver.gif\" alt=\"silver\" /> ".$money_cooper."<img src=\"./img/copper.gif\" alt=\"copper\" /> ";

    $result = $sqlc->query("SELECT TabId, TabName, TabIcon FROM `guild_bank_tab` WHERE guildid = $guild_id LIMIT 6");
    $tabs = array();
    while ($tab = $sqlc->fetch_assoc($result))
    {
      $tabs[$tab['TabId']] = $tab;
    }
    $output .= "
        <div class=\"top\">
          <h1>".$guild_name." {$lang_guildbank['guildbank']}</h1>
        </div>
        <center>
          <div id=\"tab\">
            <ul>";
    for($i=0;$i<6;$i++)
    {
      if (isset($tabs[$i]))
      {
        $output .="
              <li".(($current_tab == $i) ? " id=\"selected\"" : "").">
                <a href=\"guildbank.php?id=$guild_id&amp;tab=$i\">";
        if ($tabs[$i]['TabIcon'] != '')
          if (file_exists("$item_icons/".$tabs[$i]['TabIcon'].".jpg"))
            $output .="
                  <img src=\"$item_icons/".$tabs[$i]['TabIcon'].".jpg\" width=\"12\" height=\"12\" alt=\"\" />";
        if ($tabs[$i]['TabName'] != '')
          $output .="
                  <small>{$tabs[$i]['TabName']}</small>";
        else
          $output .="
                  <small>{$lang_guildbank['tab']}".($i+1)."</small>";
        $output .="
                </a>
              </li>";
      }
    }
    $output .="
            </ul>
          </div>
          <div id=\"tab_content\">";

    $result = $sqlc->query("SELECT SlotId, item_entry FROM `guild_bank_item` WHERE guildid = $guild_id AND TabID = $current_tab");
    $gb_slots = array();

    while ($tab = $sqlc->fetch_assoc($result))
      if ($tab['item_entry'] > 0)
        $gb_slots[$tab['SlotId']] = $tab;

    $output .= "
            <table style=\"width: 550px;\">";

    $item_position = 0;
    for ($i=0;$i<7;$i++)
    {
      $output .= "
              <tr>";
      for ($j=0;$j<14;$j++)
      {
        $item_position = $j*7+$i;
        if (isset($gb_slots[$item_position]))
        {
          $gb_item_id = $gb_slots[$item_position]['item_entry'];
          $output .= "
                <td>
                  <a href=\"".$item_datasite.$gb_item_id."\">
                    <img src=\"".get_item_icon($gb_item_id)."\" align=\"middle\" width=\"36\" height=\"36\" border=\"0\" alt=\"\" />
                  </a>
                </td>";
        }
        else
        {
          $output .= "
              <td>
                <img src=\"img/INV/Slot_Bag.gif\" align=\"middle\" alt=\"\" />
              </td>";
        }
      }
      $output .= "
              </tr>";
    }
    $output .= "
              <tr>
              </tr>
              <tr>
                <td colspan=\"14\" class=\"hidden\" align=\"right\">$money</td>
              </tr>
            </table>
          </div>
          <br>
        </center>";
  }
  else
    redirect("error.php?err={$lang_guildbank['notfound']}");
}
//#############################################################################
// MAIN
//#############################################################################
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$lang_guildbank = lang_guildbank();

switch ($err)
{
  case "unknown":
    break;
  default: //no error
}

unset($err);

$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

switch ($action)
{
  case "unknown":
    break;
  default:
    guild_bank();
}

unset($action);
unset($action_permission);
unset($lang_guildbank);

require_once("footer.php");

?>
