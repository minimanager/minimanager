<?php

//##########################################################################################
//get player name
function get_char_name($id)
{
  global $characters_db, $realm_id;

  if($id)
  {
    $sql_0 = new SQL;
    $sql_0->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $result = $sql_0->query("SELECT `name` FROM `characters` WHERE `guid` = '$id'");
    $player_name = $sql_0->result($result, 0);

    $sql_0->close();
    unset($sql);
    return $player_name;
  }
  else
    return NULL;
}

//get mail text
function get_mail_text($id)
{
  global $characters_db, $realm_id;

  if($id)
  {
    $sql_0 = new SQL;
    $sql_0->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

    $result = $sql_0->query("SELECT `text` FROM `item_text` WHERE `id` = '$id'");
    $text_subject = $sql_0->result($result, 0);

    $sql_0->close();
    unset($sql);
    return $text_subject;
  }
  else
    return NULL;
}

// Mail Source
$mail_source = Array
(
  "0" => "Normal",
  "2" => "Auction",
  "3" => "Creature",
  "4" => "GameObject",
  "5" => "Item",
);

function get_mail_source($id)
{
  global $mail_source;
  return $mail_source[$id] ;
}

// Check State
$check_state = Array
(
  "0" => "Not Read",
  "1" => "Read",
  "4" => "Auction Checked",
  "8" => "COD Pay Checked",
  "16" => "Returned Checked",
);

function get_check_state($id)
{
  global $check_state;
  return $check_state[$id] ;
}

?>
