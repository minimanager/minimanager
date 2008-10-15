<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

//##########################################################################################
//SEND INGAME MAIL - SAFE TO USE ONLY WHILE SERVER IS OFFLINE
function send_ingame_mail($to, $from, $subject, $body, $gold = 0, $item = 0, $stack = 1){
	global $lang_global, $characters_db, $realm_id;

	$sql_0 = new SQL;
	$sql_0->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

	$result = $sql_0->query("SELECT MAX(`id`) FROM item_text");
	$item_page_id = ($sql_0->result($result, 0)) + 1;
	$result = $sql_0->query("INSERT INTO item_text (id, text) VALUES ($item_page_id,'$body')");

	$result = $sql_0->query("SELECT MAX(`id`) FROM mail");
	$mail_id = ($sql_0->result($result, 0)) + 1;

    $item_guid = ($item) ? gen_item_instance($to, $item, $stack) : 0;

    if ($item == 0) {
         $has_items = 0;
    } else {
         $has_items = 1;
    }

    $result = $sql_0->query("INSERT INTO mail (id,messageType,stationery,mailTemplateId,sender,receiver,subject,itemTextId,has_items,expire_time,deliver_time,money,cod,checked)
	    VALUES ($mail_id, 0, 61, 0, '$from', '$to', '$subject', '$item_page_id', '$has_items', '".(time() + (30*24*3600))."','".(time()+5)."', '$gold', 0, 0)");

 	if ($has_items) {
		$result = $sql_0->query("INSERT INTO mail_items (mail_id,item_guid,item_template,receiver)
		       VALUES ($mail_id, '$item_guid', '$item', '$to')");
	}

	if ($result) {
	      $sql_0->close();
          return $mail_id;
	} else {
			$sql_0->close();
			return 0;
			}
}


//##########################################################################################
//GENERATE ITEM_NSTANCE ENTRY
function gen_item_instance($owner, $item_id, $stack){
 global $lang_global, $characters_db, $realm_id, $mangos_db;

 $sql_1 = new SQL;
 $sql_1->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

 $result = $sql_1->query("SELECT MAX(guid) FROM item_instance");
 $guid = $sql_1->result($result, 0) + 1;

 $result = $sql_1->query("SELECT flags,stackable,MaxDurability,spellcharges_1,spellcharges_2,
							spellcharges_3,spellcharges_4,spellcharges_5 FROM `".$mangos_db[$realm_id]['name']."`.`item_template`
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
