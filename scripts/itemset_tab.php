<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

 function get_itemset_name($id)
{
  global $mmfpm_db;
  $sql = new SQL;
  $sql->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
  $itemset = $sql->fetch_row($sql->query("SELECT `name_loc0` FROM `dbc_itemset` WHERE `itemsetID`={$id} LIMIT 1"));
  $sql->close();
  unset($sql);
  return $itemset[0];
}
 
?>
