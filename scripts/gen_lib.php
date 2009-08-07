<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */


//##########################################################################################
//SEND INGAME MAIL BY TELNET
//
// Xiong Guoy
// 2009-08-08
function send_ingame_group_mail($realm_id, $massmails)
{
  require_once("./libs/telnet_lib.php");
  global $server;
  $telnet = new telnet_lib();
  $telnet->show_connect_error=0;

  //$massmails array format
  //($to, $subject, $body, $gold = 0, $item = 0, $stack = 1)

  $result = $telnet->Connect($server[$realm_id]['addr'],$server[$realm_id]['telnet_user'],$server[$realm_id]['telnet_pass']);

  switch ($result)
  {
    case 0:
      foreach($massmails as $mails)
      {
        $mess_str = '';
        $result = '';

        if ($mails[3] && $mails[4])
        {
          $mess_str1 = "send money ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\" ".$mails[3]."";
          $telnet->DoCommand($mess_str1, $result1);

          $mess_str .= $mess_str1."<br >";
          $result .= $result1."";

          $mess_str1 = "send item ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\" ".$mails[4].(($mails[5] > 1) ? "[:count".$mails[5]."]" : " ");
          $telnet->DoCommand($mess_str1, $result1);

          $mess_str .= $mess_str1."<br >";
          $result .= $result1."";
        }
        elseif ($mails[3])
        {
          $mess_str1 = "send money ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\" ".$mails[3]."";
          $telnet->DoCommand($mess_str1, $result1);

          $mess_str .= $mess_str1."<br >";
          $result .= $result1."";
        }
        elseif ($mails[4])
        {
          $mess_str1 = "send item ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\" ".$mails[4].(($mails[5] > 1) ? "[:count".$mails[5]."]" : " ");
          $telnet->DoCommand($mess_str1, $result1);

          $mess_str .= $mess_str1."<br >";
          $result .= $result1."";
        }
        else
        {
          $mess_str1 = "send mail ".$mails[0]." \"".$mails[1]."\" \"".$mails[2]."\"";
          $telnet->DoCommand($mess_str1, $result1);

          $mess_str .= $mess_str1."<br >";
          $result .= $result1."";
        }
      }
      $result = str_replace("mangos>","",$result);
      $result = str_replace(array("\r\n", "\n", "\r"), '<br />', $result);
      $mess_str .= "<br /><br />".$result;
      $telnet->Disconnect();
      redirect("mail.php?action=result&error=6&mess=$mess_str");
      break;
    case 1:
      $mess_str = "Connect failed: Unable to open network connection";
      redirect("mail.php?action=result&error=6&mess=$mess_str");
      break;
    case 2:
      $mess_str = "Connect failed: Unknown host";
      redirect("mail.php?action=result&error=6&mess=$mess_str");
      break;
    case 3:
      $mess_str = "Connect failed: Login failed";
      redirect("mail.php?action=result&error=6&mess=$mess_str");
      break;
    case 4:
      $mess_str = "Connect failed: Your PHP version does not support PHP Telnet";
      redirect("mail.php?action=result&error=6&mess=$mess_str");
      break;
  }

}


function send_ingame_mail($realm_id, $to, $subject, $body, $gold = 0, $item = 0, $stack = 1, $group = 0)
{
  require_once("./libs/telnet_lib.php");
  global $server;
  $telnet = new telnet_lib();
  $telnet->show_connect_error=0;

  $result = $telnet->Connect($server[$realm_id]['addr'],$server[$realm_id]['telnet_user'],$server[$realm_id]['telnet_pass']);

  switch ($result)
  {
    case 0:
      if ($gold && $item)
      {
        $mess_str1 = "send money ".$to." \"".$subject."\" \"".$body."\" ".$gold."";
        $telnet->DoCommand($mess_str1, $result1);

        $mess_str = $mess_str1;
        $result = $result1;

        $mess_str2 = "send item ".$to." \"".$subject."\" \"".$body."\" ".$item.(($stack > 1) ? "[:count".$stack."]" : " ");
        $telnet->DoCommand($mess_str2, $result2);

        $mess_str .= "<br />".$mess_str2;
        $result .= "".$result2;
      }
      elseif ($gold)
      {
        $mess_str = "send money ".$to." \"".$subject."\" \"".$body."\" ".$gold."";
        $telnet->DoCommand($mess_str, $result);
      }
      elseif ($item)
      {
        $mess_str = "send item ".$to." \"".$subject."\" \"".$body."\" ".$item.(($stack > 1) ? "[:count".$stack."]" : " ");
        $telnet->DoCommand($mess_str, $result);
      }
      else
      {
        $mess_str = "send mail ".$to." \"".$subject."\" \"".$body."\"";
        $telnet->DoCommand($mess_str, $result);
      }

      $result = str_replace("mangos>","",$result);
      $result = str_replace(array("\r\n", "\n", "\r"), '<br />', $result);
      $mess_str .= "<br /><br />".$result;
      if($group)
      {
      }
      else
        redirect("mail.php?action=result&error=6&mess=$mess_str");
      $telnet->Disconnect();
      break;
    case 1:
      $mess_str = "Connect failed: Unable to open network connection";
      redirect("mail.php?action=result&error=6&mess=$mess_str");
      break;
    case 2:
      $mess_str = "Connect failed: Unknown host";
      redirect("mail.php?action=result&error=6&mess=$mess_str");
      break;
    case 3:
      $mess_str = "Connect failed: Login failed";
      redirect("mail.php?action=result&error=6&mess=$mess_str");
      break;
    case 4:
      $mess_str = "Connect failed: Your PHP version does not support PHP Telnet";
      redirect("mail.php?action=result&error=6&mess=$mess_str");
      break;
  }

}


//##########################################################################################
//GENERATE ITEM_NSTANCE ENTRY
function gen_item_instance($owner, $item_id, $stack){
 global $lang_global, $characters_db, $realm_id, $world_db;

 $sql_1 = new SQL;
 $sql_1->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $result = $sql_1->query("SELECT MAX(guid) FROM item_instance");
 $guid = $sql_1->result($result, 0) + 1;

 $result = $sql_1->query("SELECT flags,stackable,MaxDurability,spellcharges_1,spellcharges_2,
              spellcharges_3,spellcharges_4,spellcharges_5 FROM `".$world_db[$realm_id]['name']."`.`item_template`
              WHERE entry = '$item_id'");
 $item_template = $sql_1->fetch_row($result);

 if ($item_template[1] <= 1) $stack = 1;

 $item_data = array(
  'OBJECT_FIELD_GUID'               => $guid,
    'OBJECT_FIELD_TYPE'               => '1073741936 3',
    'OBJECT_FIELD_ENTRY'              => $item_id,
    'OBJECT_FIELD_SCALE_X'            => '1065353216',
    'OBJECT_FIELD_PADDING'            => 0,
    'ITEM_FIELD_OWNER'                => $owner.' 0',
    'ITEM_FIELD_CONTAINED'            => '0 0',
    'ITEM_FIELD_CREATOR'              => '0 0',
    'ITEM_FIELD_GIFTCREATOR'          => '0 0',
    'ITEM_FIELD_STACK_COUNT'          => $stack,
    'ITEM_FIELD_DURATION'             => 0,
    'ITEM_FIELD_SPELL_CHARGES'        => $item_template[3],
    'ITEM_FIELD_SPELL_CHARGES_01'     => $item_template[4],
    'ITEM_FIELD_SPELL_CHARGES_02'     => $item_template[5],
    'ITEM_FIELD_SPELL_CHARGES_03'     => $item_template[6],
    'ITEM_FIELD_SPELL_CHARGES_04'     => $item_template[7],
    'ITEM_FIELD_FLAGS'                => $item_template[0],
    'ITEM_FIELD_ENCHANTMENT'          => '0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0 0',
    'ITEM_FIELD_SUFFIX_FACTOR'        => 0,
    'ITEM_FIELD_RANDOM_PROPERTIES_ID' => 0,
    'ITEM_FIELD_ITEM_TEXT_ID'         => 0,
    'ITEM_FIELD_DURABILITY'           => $item_template[2],
    'ITEM_FIELD_MAXDURABILITY'        => $item_template[2].' '
 );

 $data = implode(" ",$item_data);

 $result = $sql_1->query("INSERT INTO item_instance (guid, owner_guid, data) VALUES ($guid, '$owner','$data')");

 if ($result) {
  $sql_1->close();
  return $guid;
 } else {
    $sql_1->close();
    return 0;
    }
}


?>
