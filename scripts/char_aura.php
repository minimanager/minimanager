<?php

require_once("config.php");
$char_aura = Array(
	2383 => array($lang_id_tab['CHAR_AURA_FIND_HERBS'],'INV_Misc_Flower_02'),
	2580 => array($lang_id_tab['CHAR_AURA_FIND_MINERALS'],'Spell_Nature_Earthquake'),
	5784 => array($lang_id_tab['CHAR_AURA_SUMMON_FELSTEED'],'Spell_Nature_Swiftness'),
	13159 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_PACK'],'Ability_Mount_WhiteTiger'),
	13163 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_MONKEY'],'Ability_Hunter_AspectOfTheMonkey'),
	19878 => array($lang_id_tab['CHAR_AURA_TRACK_DEMONS'],'Spell_Shadow_SummonFelHunter'),
	1494 => array($lang_id_tab['CHAR_AURA_TRACK_BEASTS'],'Ability_Tracking'),
	19879 => array($lang_id_tab['CHAR_AURA_TRACK_DRAGONKIN'],'INV_Misc_Head_Dragon_01'),
	19880 => array($lang_id_tab['CHAR_AURA_TRACK_ELEMENTALS'],'Spell_Frost_SummonWaterElemental'),
	19883 => array($lang_id_tab['CHAR_AURA_TRACK_HUMANOIDS'],'Spell_Holy_PrayerOfHealing'),
	19882 => array($lang_id_tab['CHAR_AURA_TRACK_GIANTS'],'Ability_Racial_Avatar'),
	19884 => array($lang_id_tab['CHAR_AURA_TRACK_UNDEAD'],'Spell_Shadow_DarkSummoning'),
	19885 => array($lang_id_tab['CHAR_AURA_TRACK_HIDDEN'],'Ability_Stealth'),
	1126 => array($lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_1'],'Spell_Nature_Regeneration'),
	5232 => array($lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_2'],'Spell_Nature_Regeneration'),
	6756 => array($lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_3'],'Spell_Nature_Regeneration'),
	5234 => array($lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_4'],'Spell_Nature_Regeneration'),
	8907 => array($lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_5'],'Spell_Nature_Regeneration'),
	9884 => array($lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_6'],'Spell_Nature_Regeneration'),
	9885 => array($lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_7'],'Spell_Nature_Regeneration'),
	26990 => array($lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_8'],'Spell_Nature_Regeneration'),
	13165 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_1'],'Spell_Nature_RavenForm'),
	14318 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_2'],'Spell_Nature_RavenForm'),
	14319 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_3'],'Spell_Nature_RavenForm'),
	14320 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_4'],'Spell_Nature_RavenForm'),
	14321 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_5'],'Spell_Nature_RavenForm'),
	14322 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_6'],'Spell_Nature_RavenForm'),
	25296 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_7'],'Spell_Nature_RavenForm'),
	27044 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_8'],'Spell_Nature_RavenForm'),
	34074 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_VIPER'],'Ability_Hunter_AspectoftheViper'),
	20043 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_WILD_RANK_1'],'Spell_Nature_ProtectionformNature'),
	20190 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_WILD_RANK_2'],'Spell_Nature_ProtectionformNature'),
	27045 => array($lang_id_tab['CHAR_AURA_ASPECT_OF_THE_WILD_RANK_3'],'Spell_Nature_ProtectionformNature'),
	19506 => array($lang_id_tab['CHAR_AURA_TRUESHOT_AURA_RANK_1'],'Ability_TrueShot'),
	20905 => array($lang_id_tab['CHAR_AURA_TRUESHOT_AURA_RANK_2'],'Ability_TrueShot'),
	20906 => array($lang_id_tab['CHAR_AURA_TRUESHOT_AURA_RANK_3'],'Ability_TrueShot'),
	27066 => array($lang_id_tab['CHAR_AURA_TRUESHOT_AURA_RANK_4'],'Ability_TrueShot'),
	1459 => array($lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_1'],'Spell_Holy_MagicalSentry'),
	1460 => array($lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_2'],'Spell_Holy_MagicalSentry'),
	1461 => array($lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_3'],'Spell_Holy_MagicalSentry'),
	10156 => array($lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_4'],'Spell_Holy_MagicalSentry'),
	10157 => array($lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_5'],'Spell_Holy_MagicalSentry'),
	27126 => array($lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_6'],'Spell_Holy_MagicalSentry'),
	168 => array($lang_id_tab['CHAR_AURA_FROST_ARMOR_RANK_1'],'Spell_Frost_FrostArmor02'),
	7300 => array($lang_id_tab['CHAR_AURA_FROST_ARMOR_RANK_2'],'Spell_Frost_FrostArmor02'),
	7301 => array($lang_id_tab['CHAR_AURA_FROST_ARMOR_RANK_3'],'Spell_Frost_FrostArmor02'),
	6117 => array($lang_id_tab['CHAR_AURA_MAGE_ARMOR_RANK_1'],'Spell_MageArmor'),
	22782 => array($lang_id_tab['CHAR_AURA_MAGE_ARMOR_RANK_2'],'Spell_MageArmor'),
	22783 => array($lang_id_tab['CHAR_AURA_MAGE_ARMOR_RANK_3'],'Spell_MageArmor'),
	27125 => array($lang_id_tab['CHAR_AURA_MAGE_ARMOR_RANK_4'],'Spell_MageArmor'),
	30482 => array($lang_id_tab['CHAR_AURA_MOLTEN_ARMOR'],'Ability_Mage_MoltenArmor')
);

function get_char_aura_name($id){
global $lang_char_aura, $char_aura;
	if( isset($char_aura[$id]) ) return $char_aura[$id][0];
	else return "Unknown";
}
?>

