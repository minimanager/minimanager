<?php


//#############################################################################
/**
 * calculate creature health, mana and armor
 * 
 * kinda crappy way, but works
 * 
 * if $type is used:
 * 1 -> returns health
 * 2 -> returns mana
 * 3 -> returns armor
 * 0 -> returns array(health,mana,armor)      
 */  
function get_additional_data($entryid, $type = 0)
{
	global 	$world_db, 
			$realm_id;
    
	if (!is_numeric($entryid))
		return array(0,0,0);

$sqlw = new SQL;
$sqlw->connect($world_db[$realm_id]['addr'], $world_db[$realm_id]['user'], $world_db[$realm_id]['pass'], $world_db[$realm_id]['name']);

	$q = $sqlw->query("
		SELECT 
			(SELECT unit_class 
			FROM creature_template 
			WHERE entry = ".$entryid.") AS class, 
				(SELECT FLOOR(minlevel + (RAND() * (maxlevel - minlevel + 1))) 
				FROM creature_template 
				WHERE entry = ".$entryid.") AS level, 
				(SELECT exp 
				FROM creature_template 
				WHERE entry = ".$entryid.") AS exp;");
	$data = $sqlw->fetch_assoc($q);
    
	if ($sqlw->num_rows($q) == 0)
		return array(0,0,0);
      
		$q = "
			SELECT 
				((SELECT Health_Mod 
				FROM creature_template 
				WHERE entry = ".$entryid.")
					*(SELECT basehp".$data['exp']." 
					FROM creature_classlevelstats 
					WHERE level = ".$data['level']." AND class = ".$data['class'].")+0.5), 
				((SELECT Mana_Mod 
				FROM creature_template 
				WHERE entry = ".$entryid.")
					*(SELECT basemana 
					FROM creature_classlevelstats 
					WHERE level = ".$data['level']." AND class = ".$data['class'].")+0.5),
				((SELECT Armor_Mod 
				FROM creature_template 
				WHERE entry = ".$entryid.")
				*(SELECT basearmor 
				FROM creature_classlevelstats 
				WHERE level = ".$data['level']." AND class = ".$data['class'].")+0.5);";          
	if ($type == 1)
		$q = "
			SELECT 
				((SELECT Health_Mod 
				FROM creature_template 
				WHERE entry = ".$entryid.")
					*(SELECT basehp".$data['exp']." 
					FROM creature_classlevelstats 
					WHERE level = ".$data['level']." AND class = ".$data['class'].")+0.5);";
    if ($type == 2)
		$q = "
			SELECT 
				((SELECT Mana_Mod 
				FROM creature_template 
				WHERE entry = ".$entryid.")
					*(SELECT basemana 
					FROM creature_classlevelstats 
					WHERE level = ".$data['level']." AND class = ".$data['class'].")+0.5);";
    if ($type == 3)
		$q = "
			SELECT 
				((SELECT Armor_Mod 
				FROM creature_template 
				WHERE entry = ".$entryid.")
					*(SELECT basearmor 
					FROM creature_classlevelstats 
					WHERE level = ".$data['level']." AND class = ".$data['class'].")+0.5);";
    
	$query = $sqlw->query($q);         
	$result = $sqlw->fetch_row($query);
	$sqlw->close();
unset($sql);
    
	if ($type == 2 && $result[0] == 0.5)
		return 0;
    
	if ($type == 0 && $result[1] == 0.5)
		return array($result[0],0,$result[2]);
    
	return (($type > 0) ? $result[0] : $result);
}


?>
