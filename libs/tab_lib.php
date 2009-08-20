<?php


//list of tables in realmd db will be saved on Global backup
$tables_backup_realmd = Array
(
  'account',
  'account_banned',
  'ip_banned',
  'realmcharacters',
  'realmlist',
);

//list of tables in characters db will be saved on Global backup
$tables_backup_characters = Array
(
  'arena_team',
  'arena_team_member',
  'arena_team_stats',
  'auctionhouse',
  'character_achievement',
  'character_achievement_progress',
  'character_action',
  'character_aura',
  'character_declinedname',
  'character_equipmentsets',
  'character_gifts',
  'character_homebind',
  'character_instance',
  'character_inventory',
  'character_pet',
  'character_pet_declinedname',
  'character_queststatus',
  'character_queststatus_daily',
  'character_reputation',
  'character_social',
  'character_spell',
  'character_spell_cooldown',
  'character_ticket',
  'character_tutorial',
  'characters',
  'corpse',
  'group_instance',
  'group_member',
  'groups',
  'guild',
  'guild_bank_eventlog',
  'guild_bank_item',
  'guild_bank_right',
  'guild_bank_tab',
  'guild_eventlog',
  'guild_member',
  'guild_rank',
  'instance',
  'item_instance',
  'item_text',
  'mail',
  'mail_items',
  'pet_aura',
  'pet_spell',
  'pet_spell_cooldown',
  'petition',
  'petition_sign',
);

$tables_backup_characters_trinity = Array
(
  'arena_team',
  'arena_team_member',
  'arena_team_stats',
  'auctionhouse',
  'character_achievement',
  'character_achievement_progress',
  'character_action',
  'character_aura',
  'character_declinedname',
  'character_equipmentsets',
  'character_gifts',
  'character_homebind',
  'character_instance',
  'character_inventory',
  'character_pet',
  'character_pet_declinedname',
  'character_queststatus',
  'character_queststatus_daily',
  'character_reputation',
  'character_social',
  'character_spell',
  'character_spell_cooldown',
  'character_tutorial',
  'characters',
  'corpse',
  'gm_tickets',
  'group_instance',
  'group_member',
  'groups',
  'guild',
  'guild_bank_eventlog',
  'guild_bank_item',
  'guild_bank_right',
  'guild_bank_tab',
  'guild_eventlog',
  'guild_member',
  'guild_rank',
  'instance',
  'item_instance',
  'item_text',
  'mail',
  'mail_items',
  'pet_aura',
  'pet_spell',
  'pet_spell_cooldown',
  'petition',
  'petition_sign',
);

//list of tables in realmd db you need to delete data on user deletion
$tab_del_user_realmd = Array
(
    Array('realmcharacters','acctid'),
    Array('account_banned','id'),
    Array('account','id'),
);

$tab_del_user_char = Array
(
    Array('account_data','account'),
);

//list of tables in realmd db you need to backup data on single user backup
$tab_backup_user_realmd = $tab_del_user_realmd;


//list of tables in characters db you need to delete data from on user deletion
$tab_del_user_characters = Array
(
  Array('arena_team_member','guid'),
  Array('auctionhouse','itemowner'),
  Array('character_achievement','guid'),
  Array('character_achievement_progress','guid'),
  Array('character_action','guid'),
  Array('character_aura','guid'),
  Array('character_declinedname','guid'),
  Array('character_equipmentsets','guid'),
  Array('character_gifts','guid'),
  Array('character_homebind','guid'),
  Array('character_instance','guid'),
  Array('character_inventory','guid'),
  Array('character_pet','owner'),
  Array('character_pet_declinedname','owner'),
  Array('character_queststatus','guid'),
  Array('character_queststatus_daily','guid'),
  Array('character_reputation','guid'),
  Array('character_social','guid'),
  Array('character_social','friend'),
  Array('character_spell','guid'),
  Array('character_spell_cooldown','guid'),
  Array('character_ticket','guid'),
  Array('corpse','player'),
  Array('groups','leaderGuid'),
  Array('group_member','memberGuid'),
  Array('group_member','leaderGuid'),
  Array('group_instance','leaderGuid'),
  Array('guild_bank_eventlog','PlayerGuid'),
  Array('guild_eventlog','PlayerGuid2'),
  Array('guild_eventlog','PlayerGuid1'),
  Array('guild_member','guid'),
  Array('item_instance','owner_guid'),
  Array('mail','receiver'),
  Array('mail_items','receiver'),
  Array('petition','ownerguid'),
  Array('petition_sign','ownerguid'),
  Array('petition_sign','playerguid'),
  Array('characters','guid'),
);


//list of tables in characters db you need to backup data from on single user backup
$tab_backup_user_characters = $tab_del_user_characters;


$tab_del_user_characters_trinity = Array
(
  Array('arena_team_member','guid'),
  Array('auctionhouse','itemowner'),
  Array('character_achievement','guid'),
  Array('character_achievement_progress','guid'),
  Array('character_action','guid'),
  Array('character_aura','guid'),
  Array('character_declinedname','guid'),
  Array('character_equipmentsets','guid'),
  Array('character_gifts','guid'),
  Array('character_homebind','guid'),
  Array('character_instance','guid'),
  Array('character_inventory','guid'),
  Array('character_pet','owner'),
  Array('character_pet_declinedname','owner'),
  Array('character_queststatus','guid'),
  Array('character_queststatus_daily','guid'),
  Array('character_reputation','guid'),
  Array('character_social','guid'),
  Array('character_social','friend'),
  Array('character_spell','guid'),
  Array('character_spell_cooldown','guid'),
  Array('gm_tickets','playerGuid'),
  Array('corpse','player'),
  Array('groups','leaderGuid'),
  Array('group_member','memberGuid'),
  Array('group_member','leaderGuid'),
  Array('group_instance','leaderGuid'),
  Array('guild_bank_eventlog','PlayerGuid'),
  Array('guild_eventlog','PlayerGuid2'),
  Array('guild_eventlog','PlayerGuid1'),
  Array('guild_member','guid'),
  Array('item_instance','owner_guid'),
  Array('mail','receiver'),
  Array('mail_items','receiver'),
  Array('petition','ownerguid'),
  Array('petition_sign','ownerguid'),
  Array('petition_sign','playerguid'),
  Array('characters','guid'),
);


$tab_backup_user_characters_trinity = $tab_del_user_characters_trinity;


?>
