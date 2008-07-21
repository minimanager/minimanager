<?php

require_once("config.php");
$char_aura = Array(
	2383 => array(2383,$lang_id_tab['CHAR_AURA_FIND_HERBS'],'FindHerbs.jpg'),
	2580 => array(2580,$lang_id_tab['CHAR_AURA_FIND_MINERALS'],'FindMinerals.jpg'),
	5784 => array(5784,$lang_id_tab['CHAR_AURA_SUMMON_FELSTEED'],'SummonFelsteed.jpg'),
	13159 => array(13159,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_PACK'],'AspectOfThePack.jpg'),
	13163 => array(13163,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_MONKEY'],'AspectOfTheMonkey.jpg'),
	19878 => array(19878,$lang_id_tab['CHAR_AURA_TRACK_DEMONS'],'TrackDemons.jpg'),
	1494 => array(1494,$lang_id_tab['CHAR_AURA_TRACK_BEASTS'],'TrackBeasts.jpg'),
	19879 => array(19879,$lang_id_tab['CHAR_AURA_TRACK_DRAGONKIN'],'TrackDragonkin.jpg'),
	19880 => array(19880,$lang_id_tab['CHAR_AURA_TRACK_ELEMENTALS'],'TrackElementals.jpg'),
	19883 => array(19883,$lang_id_tab['CHAR_AURA_TRACK_HUMANOIDS'],'TrackHumanoids.jpg'),
	19882 => array(19882,$lang_id_tab['CHAR_AURA_TRACK_GIANTS'],'TrackGiants.jpg'),
	19884 => array(19884,$lang_id_tab['CHAR_AURA_TRACK_UNDEAD'],'TrackUndead.jpg'),
	19885 => array(19885,$lang_id_tab['CHAR_AURA_TRACK_HIDDEN'],'TrackHidden.jpg'),
	1126 => array(1126,$lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_1'],'MarkOfTheWild.jpg'),
	5232 => array(5232,$lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_2'],'MarkOfTheWild.jpg'),
	6756 => array(6756,$lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_3'],'MarkOfTheWild.jpg'),
	5234 => array(5234,$lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_4'],'MarkOfTheWild.jpg'),
	8907 => array(8907,$lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_5'],'MarkOfTheWild.jpg'),
	9884 => array(9884,$lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_6'],'MarkOfTheWild.jpg'),
	9885 => array(9885,$lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_7'],'MarkOfTheWild.jpg'),
	26990 => array(26990,$lang_id_tab['CHAR_AURA_MARK_OF_THE_WILD_RANK_8'],'MarkOfTheWild.jpg'),
	13165 => array(13165,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_1'],'AspectOfTheHawk.jpg'),
	14318 => array(14318,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_2'],'AspectOfTheHawk.jpg'),
	14319 => array(14319,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_3'],'AspectOfTheHawk.jpg'),
	14320 => array(14320,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_4'],'AspectOfTheHawk.jpg'),
	14321 => array(14321,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_5'],'AspectOfTheHawk.jpg'),
	14322 => array(14322,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_6'],'AspectOfTheHawk.jpg'),
	25296 => array(25296,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_7'],'AspectOfTheHawk.jpg'),
	27044 => array(27044,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_HAWK_RANK_8'],'AspectOfTheHawk.jpg'),
	34074 => array(34074,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_VIPER'],'AspectOfTheViper.jpg'),
	20043 => array(20043,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_WILD_RANK_1'],'AspectOfTheWild.jpg'),
	20190 => array(20190,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_WILD_RANK_2'],'AspectOfTheWild.jpg'),
	27045 => array(27045,$lang_id_tab['CHAR_AURA_ASPECT_OF_THE_WILD_RANK_3'],'AspectOfTheWild.jpg'),
	19506 => array(19506,$lang_id_tab['CHAR_AURA_TRUESHOT_AURA_RANK_1'],'Trueshot.jpg'),
	20905 => array(20905,$lang_id_tab['CHAR_AURA_TRUESHOT_AURA_RANK_2'],'Trueshot.jpg'),
	20906 => array(20906,$lang_id_tab['CHAR_AURA_TRUESHOT_AURA_RANK_3'],'Trueshot.jpg'),
	27066 => array(27066,$lang_id_tab['CHAR_AURA_TRUESHOT_AURA_RANK_4'],'Trueshot.jpg'),
	1459 => array(1459,$lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_1'],'ArcaneIntellect.jpg'),
	1460 => array(1460,$lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_2'],'ArcaneIntellect.jpg'),
	1461 => array(1461,$lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_3'],'ArcaneIntellect.jpg'),
	10156 => array(10156,$lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_4'],'ArcaneIntellect.jpg'),
	10157 => array(10157,$lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_5'],'ArcaneIntellect.jpg'),
	27126 => array(27126,$lang_id_tab['CHAR_AURA_ARCANE_INTELLECT_RANK_6'],'ArcaneIntellect.jpg'),
	168 => array(168,$lang_id_tab['CHAR_AURA_FROST_ARMOR_RANK_1'],'FrostArmor.jpg'),
	7300 => array(7300,$lang_id_tab['CHAR_AURA_FROST_ARMOR_RANK_2'],'FrostArmor.jpg'),
	7301 => array(7301,$lang_id_tab['CHAR_AURA_FROST_ARMOR_RANK_3'],'FrostArmor.jpg'),
	6117 => array(6117,$lang_id_tab['CHAR_AURA_MAGE_ARMOR_RANK_1'],'MageArmor.jpg'),
	22782 => array(22782,$lang_id_tab['CHAR_AURA_MAGE_ARMOR_RANK_2'],'MageArmor.jpg'),
	22783 => array(22783,$lang_id_tab['CHAR_AURA_MAGE_ARMOR_RANK_3'],'MageArmor.jpg'),
	27125 => array(27125,$lang_id_tab['CHAR_AURA_MAGE_ARMOR_RANK_4'],'MageArmor.jpg'),
	30482 => array(30482,$lang_id_tab['CHAR_AURA_MOLTEN_ARMOR'],'MoltenArmor.jpg')
);

function get_char_aura_name($id){
global $lang_char_aura, $char_aura;
	if( isset($char_aura[$id]) ) return $char_aura[$id][1];
	else return "Unknown";
}

function get_char_aura_image($id){
global $char_aura;
	if( isset($char_aura[$id][2]) ) return $char_aura[$id][2];
	else return 'missing.jpg';
}


