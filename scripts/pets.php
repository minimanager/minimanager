<?php

require_once("config.dist.php");
require_once("config.php");
$pet_ability = Array(
//	Bite
	17253 => array(17253,$lang_id_tab['PET_ABILITY_BITE_RANK_1'],'1','bite.jpg'),
	17255 => array(17255,$lang_id_tab['PET_ABILITY_BITE_RANK_2'],'4','bite.jpg'),
	17256 => array(17256,$lang_id_tab['PET_ABILITY_BITE_RANK_3'],'7','bite.jpg'),
	17257 => array(17257,$lang_id_tab['PET_ABILITY_BITE_RANK_4'],'10','bite.jpg'),
	17258 => array(17258,$lang_id_tab['PET_ABILITY_BITE_RANK_5'],'13','bite.jpg'),
	17259 => array(17259,$lang_id_tab['PET_ABILITY_BITE_RANK_6'],'17','bite.jpg'),
	17260 => array(17260,$lang_id_tab['PET_ABILITY_BITE_RANK_7'],'21','bite.jpg'),
	17261 => array(17261,$lang_id_tab['PET_ABILITY_BITE_RANK_8'],'25','bite.jpg'),
	27050 => array(27050,$lang_id_tab['PET_ABILITY_BITE_RANK_9'],'29','bite.jpg'),
//	Claw
	16827 => array(16827,$lang_id_tab['PET_ABILITY_CLAW_RANK_1'],'1','claw.jpg'),
	16828 => array(16828,$lang_id_tab['PET_ABILITY_CLAW_RANK_2'],'4','claw.jpg'),
	16829 => array(16829,$lang_id_tab['PET_ABILITY_CLAW_RANK_3'],'7','claw.jpg'),
	16830 => array(16830,$lang_id_tab['PET_ABILITY_CLAW_RANK_4'],'10','claw.jpg'),
	16831 => array(16831,$lang_id_tab['PET_ABILITY_CLAW_RANK_5'],'13','claw.jpg'),
	16832 => array(16832,$lang_id_tab['PET_ABILITY_CLAW_RANK_6'],'17','claw.jpg'),
	3010 => array(3010,$lang_id_tab['PET_ABILITY_CLAW_RANK_7'],'21','claw.jpg'),
	3009 => array(3009,$lang_id_tab['PET_ABILITY_CLAW_RANK_8'],'25','claw.jpg'),
	27049 => array(27049,$lang_id_tab['PET_ABILITY_CLAW_RANK_9'],'29','claw.jpg'),
//	Cobra Reflexes
	25076 => array(25076,$lang_id_tab['PET_ABILITY_COBRA_REFLEXES'],'15','cobrareflexes.jpg'),
//	Cower
	1742 => array(1742,$lang_id_tab['PET_ABILITY_COWER_RANK_1'],'8','cower.jpg'),
	1753 => array(1753,$lang_id_tab['PET_ABILITY_COWER_RANK_2'],'10','cower.jpg'),
	1754 => array(1754,$lang_id_tab['PET_ABILITY_COWER_RANK_3'],'12','cower.jpg'),
	1755 => array(1755,$lang_id_tab['PET_ABILITY_COWER_RANK_4'],'14','cower.jpg'),
	1756 => array(1756,$lang_id_tab['PET_ABILITY_COWER_RANK_5'],'16','cower.jpg'),
	16697 => array(16697,$lang_id_tab['PET_ABILITY_COWER_RANK_6'],'18','cower.jpg'),
	27048 => array(27048,$lang_id_tab['PET_ABILITY_COWER_RANK_7'],'21','cower.jpg'),
//	Dash
	23099 => array(23099,$lang_id_tab['PET_ABILITY_DASH_RANK_1'],'15','dash.jpg'),
	23109 => array(23109,$lang_id_tab['PET_ABILITY_DASH_RANK_2'],'20','dash.jpg'),
	23110 => array(23110,$lang_id_tab['PET_ABILITY_DASH_RANK_3'],'25','dash.jpg'),
//	Dive
	23146 => array(23146,$lang_id_tab['PET_ABILITY_DIVE_RANK_1'],'15','dive.jpg'),
	23149 => array(23149,$lang_id_tab['PET_ABILITY_DIVE_RANK_2'],'20','dive.jpg'),
	23150 => array(23150,$lang_id_tab['PET_ABILITY_DIVE_RANK_3'],'25','dive.jpg'),
//	Firebreath
	34889 => array(34889,$lang_id_tab['PET_ABILITY_FIRE_BREATH_RANK_1'],'5','firebreath.jpg'),
	35323 => array(35323,$lang_id_tab['PET_ABILITY_FIRE_BREATH_RANK_2'],'25','firebreath.jpg'),
//	Furious howl
	24609 => array(24609,$lang_id_tab['PET_ABILITY_FURIOUS_HOWL_RANK_1'],'10','furioushowl.jpg'),
	24608 => array(24608,$lang_id_tab['PET_ABILITY_FURIOUS_HOWL_RANK_2'],'15','furioushowl.jpg'),
	24607 => array(24607,$lang_id_tab['PET_ABILITY_FURIOUS_HOWL_RANK_3'],'20','furioushowl.jpg'),
	24599 => array(24599,$lang_id_tab['PET_ABILITY_FURIOUS_HOWL_RANK_4'],'25','furioushowl.jpg'),
//	Gore
	35290 => array(35290,$lang_id_tab['PET_ABILITY_GORE_RANK_1'],'1','gore.jpg'),
	35291 => array(35291,$lang_id_tab['PET_ABILITY_GORE_RANK_2'],'4','gore.jpg'),
	35292 => array(35292,$lang_id_tab['PET_ABILITY_GORE_RANK_3'],'7','gore.jpg'),
	35293 => array(35293,$lang_id_tab['PET_ABILITY_GORE_RANK_4'],'10','gore.jpg'),
	35294 => array(35294,$lang_id_tab['PET_ABILITY_GORE_RANK_5'],'13','gore.jpg'),
	35295 => array(35295,$lang_id_tab['PET_ABILITY_GORE_RANK_6'],'17','gore.jpg'),
	35296 => array(35296,$lang_id_tab['PET_ABILITY_GORE_RANK_7'],'21','gore.jpg'),
	35297 => array(35297,$lang_id_tab['PET_ABILITY_GORE_RANK_8'],'25','gore.jpg'),
	35298 => array(35298,$lang_id_tab['PET_ABILITY_GORE_RANK_9'],'29','gore.jpg'),
//	Growl
	2649 => array(2649,$lang_id_tab['PET_ABILITY_GROWL_RANK_1'],'0','growl.jpg'),
	14916 => array(14916,$lang_id_tab['PET_ABILITY_GROWL_RANK_2'],'0','growl.jpg'),
	14917 => array(14917,$lang_id_tab['PET_ABILITY_GROWL_RANK_3'],'0','growl.jpg'),
	14918 => array(14918,$lang_id_tab['PET_ABILITY_GROWL_RANK_4'],'0','growl.jpg'),
	14919 => array(14919,$lang_id_tab['PET_ABILITY_GROWL_RANK_5'],'0','growl.jpg'),
	14920 => array(14920,$lang_id_tab['PET_ABILITY_GROWL_RANK_6'],'0','growl.jpg'),
	14921 => array(14921,$lang_id_tab['PET_ABILITY_GROWL_RANK_7'],'0','growl.jpg'),
	27047 => array(27047,$lang_id_tab['PET_ABILITY_GROWL_RANK_8'],'0','growl.jpg'),
//	Lightning breath
	24845 => array(24845,$lang_id_tab['PET_ABILITY_LIGHTNING_BREATH_RANK_1'],'1','lightningbreath.jpg'),
	25013 => array(25013,$lang_id_tab['PET_ABILITY_LIGHTNING_BREATH_RANK_2'],'5','lightningbreath.jpg'),
	25014 => array(25014,$lang_id_tab['PET_ABILITY_LIGHTNING_BREATH_RANK_3'],'10','lightningbreath.jpg'),
	25015 => array(25015,$lang_id_tab['PET_ABILITY_LIGHTNING_BREATH_RANK_4'],'15','lightningbreath.jpg'),
	25016 => array(25016,$lang_id_tab['PET_ABILITY_LIGHTNING_BREATH_RANK_5'],'20','lightningbreath.jpg'),
	25017 => array(25017,$lang_id_tab['PET_ABILITY_LIGHTNING_BREATH_RANK_5'],'25','lightningbreath.jpg'),
//	Poison Spit
	35388 => array(35388,$lang_id_tab['PET_ABILITY_POISON_SPIT_RANK_1'],'5','poisonspit.jpg'),
	35390 => array(35390,$lang_id_tab['PET_ABILITY_POISON_SPIT_RANK_2'],'20','poisonspit.jpg'),
	35391 => array(35391,$lang_id_tab['PET_ABILITY_POISON_SPIT_RANK_3'],'25','poisonspit.jpg'),
//	Prowl
	24451 => array(24451,$lang_id_tab['PET_ABILITY_PROWL_RANK_1'],'15','prowl.jpg'),
	24454 => array(24454,$lang_id_tab['PET_ABILITY_PROWL_RANK_2'],'20','prowl.jpg'),
	24455 => array(24455,$lang_id_tab['PET_ABILITY_PROWL_RANK_3'],'25','prowl.jpg'),
//	Scorpid Poison
	24641 => array(24455,$lang_id_tab['PET_ABILITY_SCORPID_POISON_RANK_1'],'10','scorpidpoison.jpg'),
	24584 => array(24584,$lang_id_tab['PET_ABILITY_SCORPID_POISON_RANK_2'],'15','scorpidpoison.jpg'),
	24588 => array(24588,$lang_id_tab['PET_ABILITY_SCORPID_POISON_RANK_3'],'20','scorpidpoison.jpg'),
	24589 => array(24589,$lang_id_tab['PET_ABILITY_SCORPID_POISON_RANK_4'],'25','scorpidpoison.jpg'),
	27361 => array(27361,$lang_id_tab['PET_ABILITY_SCORPID_POISON_RANK_5'],'29','scorpidpoison.jpg'),
//	Screech
	24424 => array(24424,$lang_id_tab['PET_ABILITY_SCREECH_RANK_1'],'10','screech.jpg'),
	24580 => array(24580,$lang_id_tab['PET_ABILITY_SCREECH_RANK_2'],'15','screech.jpg'),
	24581 => array(24581,$lang_id_tab['PET_ABILITY_SCREECH_RANK_3'],'20','screech.jpg'),
	24582 => array(24582,$lang_id_tab['PET_ABILITY_SCREECH_RANK_4'],'25','screech.jpg'),
	27349 => array(27349,$lang_id_tab['PET_ABILITY_SCREECH_RANK_5'],'29','screech.jpg'),
//	Shell Shield
	26064 => array(26064,$lang_id_tab['PET_ABILITY_SHELL_SHIELD'],'15','shellshield.jpg'),
//	Thunderstomp
	26094 => array(26094,$lang_id_tab['PET_ABILITY_THUNDERSTOMP_RANK_1'],'15','thunderstomp.jpg'),
	26189 => array(26189,$lang_id_tab['PET_ABILITY_THUNDERSTOMP_RANK_2'],'20','thunderstomp.jpg'),
	26190 => array(26190,$lang_id_tab['PET_ABILITY_THUNDERSTOMP_RANK_3'],'25','thunderstomp.jpg'),
	27366 => array(27366,$lang_id_tab['PET_ABILITY_THUNDERSTOMP_RANK_4'],'29','thunderstomp.jpg'),
//	Warp  
	35348 => array(35348,$lang_id_tab['PET_ABILITY_WARP'],'1','warp.jpg'),
//	Great Stamina
	4195 => array(4195,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_1'],'5','greatstamina.jpg'),
	4196 => array(4196,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_2'],'10','greatstamina.jpg'),
	4197 => array(4197,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_3'],'15','greatstamina.jpg'),
	4198 => array(4198,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_4'],'25','greatstamina.jpg'),
	4199 => array(4199,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_5'],'50','greatstamina.jpg'),
	4200 => array(4200,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_6'],'75','greatstamina.jpg'),
	4201 => array(4201,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_7'],'100','greatstamina.jpg'),
	4202 => array(4202,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_8'],'125','greatstamina.jpg'),
	5048 => array(5048,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_9'],'150','greatstamina.jpg'),
	5049 => array(5049,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_10'],'185','greatstamina.jpg'),
	27364 => array(27364,$lang_id_tab['PET_ABILITY_GREAT_STAMINA_RANK_11'],'215','greatstamina.jpg'),
//	Natural Armor
	24574 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_1'],'1','naturalarmor.jpg'),
	24556 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_2'],'5','naturalarmor.jpg'),
	24557 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_3'],'10','naturalarmor.jpg'),
	24558 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_4'],'15','naturalarmor.jpg'),
	24559 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_5'],'25','naturalarmor.jpg'),
	24560 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_6'],'50','naturalarmor.jpg'),
	24561 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_7'],'75','naturalarmor.jpg'),
	24562 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_8'],'100','naturalarmor.jpg'),
	24631 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_9'],'125','naturalarmor.jpg'),
	24632 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_10'],'150','naturalarmor.jpg'),
	27362 => array(24574,$lang_id_tab['PET_ABILITY_NATURAL_ARMOR_RANK_11'],'175','naturalarmor.jpg'),
//	Arcane Resist
	24495 => array(24495,$lang_id_tab['PET_ABILITY_ARCANE_RESIST_RANK_1'],'5','arcaneresist.jpg'),
	24508 => array(24508,$lang_id_tab['PET_ABILITY_ARCANE_RESIST_RANK_2'],'15','arcaneresist.jpg'),
	24509 => array(24509,$lang_id_tab['PET_ABILITY_ARCANE_RESIST_RANK_3'],'45','arcaneresist.jpg'),
	24510 => array(24510,$lang_id_tab['PET_ABILITY_ARCANE_RESIST_RANK_4'],'90','arcaneresist.jpg'),
	27350 => array(27350,$lang_id_tab['PET_ABILITY_ARCANE_RESIST_RANK_5'],'105','arcaneresist.jpg'),
//	Fire Resist
	24440 => array(24440,$lang_id_tab['PET_ABILITY_FIRE_RESIST_RANK_1'],'5','fireresist.jpg'),
	24441 => array(24441,$lang_id_tab['PET_ABILITY_FIRE_RESIST_RANK_2'],'15','fireresist.jpg'),
	24463 => array(24463,$lang_id_tab['PET_ABILITY_FIRE_RESIST_RANK_3'],'45','fireresist.jpg'),
	24464 => array(24464,$lang_id_tab['PET_ABILITY_FIRE_RESIST_RANK_4'],'90','fireresist.jpg'),
	27351 => array(27351,$lang_id_tab['PET_ABILITY_FIRE_RESIST_RANK_5'],'105','fireresist.jpg'),
//	Frost Resist
	24475 => array(24475,$lang_id_tab['PET_ABILITY_FROST_RESIST_RANK_1'],'5','frostresist.jpg'),
	24476 => array(24476,$lang_id_tab['PET_ABILITY_FROST_RESIST_RANK_2'],'15','frostresist.jpg'),
	24477 => array(24477,$lang_id_tab['PET_ABILITY_FROST_RESIST_RANK_3'],'45','frostresist.jpg'),
	24478 => array(24478,$lang_id_tab['PET_ABILITY_FROST_RESIST_RANK_4'],'90','frostresist.jpg'),
	27352 => array(27352,$lang_id_tab['PET_ABILITY_FROST_RESIST_RANK_5'],'105','frostresist.jpg'),
//	Nature Resist
	24494 => array(24494,$lang_id_tab['PET_ABILITY_NATURE_RESIST_RANK_1'],'5','natureresist.jpg'),
	24511 => array(24511,$lang_id_tab['PET_ABILITY_NATURE_RESIST_RANK_2'],'15','natureresist.jpg'),
	24512 => array(24512,$lang_id_tab['PET_ABILITY_NATURE_RESIST_RANK_3'],'45','natureresist.jpg'),
	24513 => array(24513,$lang_id_tab['PET_ABILITY_NATURE_RESIST_RANK_4'],'90','natureresist.jpg'),
	27354 => array(27354,$lang_id_tab['PET_ABILITY_NATURE_RESIST_RANK_5'],'105','natureresist.jpg'),
//	Shadow Resist
	24490 => array(24490,$lang_id_tab['PET_ABILITY_SHADOW_RESIST_RANK_1'],'5','shadowresist.jpg'),
	24514 => array(24514,$lang_id_tab['PET_ABILITY_SHADOW_RESIST_RANK_2'],'15','shadowresist.jpg'),
	24515 => array(24515,$lang_id_tab['PET_ABILITY_SHADOW_RESIST_RANK_3'],'45','shadowresist.jpg'),
	24516 => array(24516,$lang_id_tab['PET_ABILITY_SHADOW_RESIST_RANK_4'],'90','shadowresist.jpg'),
	27353 => array(27353,$lang_id_tab['PET_ABILITY_SHADOW_RESIST_RANK_5'],'105','shadowresist.jpg'),
//	Avoidance
	35699 => array(35699,$lang_id_tab['PET_ABILITY_AVOIDANCE_RANK_1'],'15','avoidance.jpg'),
	35700 => array(35700,$lang_id_tab['PET_ABILITY_AVOIDANCE_RANK_2'],'25','avoidance.jpg')
);

function get_pet_ability_name($id){
global $lang_pet_ability, $pet_ability;
	if( isset($pet_ability[$id]) ) return $pet_ability[$id][1];
	else return "Unknown";
}
function get_pet_ability_trainvalue($id){
global $lang_pet_ability, $pet_ability;
	if( isset($pet_ability[$id][2]) ) return $pet_ability[$id][2];
	else return 0;
}

function get_pet_ability_image($id){
global $lang_pet_ability, $pet_ability;
	if( isset($pet_ability[$id][3]) ) return $pet_ability[$id][3];
	else return 'missing.jpg';
}


?>
