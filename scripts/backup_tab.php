<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

//list of tables in realmd db will be saved on Global backup
$tables_backup_realmd = Array(
    "account",
    "ip_banned",
    "realmcharacters",
    "account_banned",
    "realmlist"
);

//list of tables in characters db will be saved on Global backup
$tables_backup_characters = Array(
    "arena_team",
    "arena_team_member",
    "arena_team_stats",
    "auctionhouse",
    "characters",
    "character_action",
    "character_aura",
    "character_gifts",
    "character_homebind",
    "character_instance",
    "character_inventory",
    "character_pet",
    "character_queststatus",
    "character_reputation",
    "character_ticket",
    "character_social",
    "character_spell",
    "character_spell_cooldown",
    "character_tutorial",
    "corpse",
    "groups",
    "group_member",
    "guild",
    "guild_bank_eventlog",
    "guild_bank_item",
    "guild_bank_right",
    "guild_bank_tab",
    "guild_member",
    "guild_rank",
    "instance",
    "item_instance",
    "item_text",
    "mail",
    "mail_items",
    "petition",
    "petition_sign",
    "pet_aura",
    "pet_spell",
    "pet_spell_cooldown",
);

$tables_backup_characters_trinity = Array(
    "arena_team",
    "arena_team_member",
    "arena_team_stats",
    "auctionhouse",
    "characters",
    "character_action",
    "character_aura",
    "character_gifts",
    "character_homebind",
    "character_instance",
    "character_inventory",
    "character_pet",
    "character_queststatus",
    "character_reputation",
    "gm_tickets",
    "character_social",
    "character_spell",
    "character_spell_cooldown",
    "character_tutorial",
    "corpse",
    "groups",
    "group_member",
    "guild",
    "guild_bank_eventlog",
    "guild_bank_item",
    "guild_bank_right",
    "guild_bank_tab",
    "guild_member",
    "guild_rank",
    "instance",
    "item_instance",
    "item_text",
    "mail",
    "mail_items",
    "petition",
    "petition_sign",
    "pet_aura",
    "pet_spell",
    "pet_spell_cooldown",
);

//list of tables in realmd db you need to delete data on user deletion
$tab_del_user_realmd = Array(
    Array("realmcharacters","acctid"),
    Array("account_banned","id"),
    Array("account","id")
);

//list of tables in characters db you need to delete data from on user deletion
$tab_del_user_characters = Array(
    Array("arena_team_member","guid"),
    Array("auctionhouse","itemowner"),
    Array("character_action","guid"),
    Array("character_aura","guid"),
    Array("character_gifts","guid"),
    Array("character_homebind","guid"),
    Array("character_instance","guid"),
    Array("character_inventory","guid"),
    Array("character_pet","owner"),
    Array("character_queststatus","guid"),
    Array("character_reputation","guid"),
    Array("character_social","guid"),
    Array("character_social","friend"),
    Array("character_spell","guid"),
    Array("character_spell_cooldown","guid"),
    Array("character_ticket","guid"),
    Array("corpse","player"),
    Array("groups","leaderGuid"),
    Array("group_member","memberGuid"),
    Array("group_member","leaderGuid"),
    Array("guild_member","guid"),
    Array("item_instance","owner_guid"),
    Array("mail","receiver"),
    Array("mail_items","receiver"),
    Array("petition","ownerguid"),
    Array("petition_sign","ownerguid"),
    Array("petition_sign","playerguid"),
    Array("characters","guid")
);

$tab_del_user_characters_trinity = Array(
    Array("arena_team_member","guid"),
    Array("auctionhouse","itemowner"),
    Array("character_action","guid"),
    Array("character_aura","guid"),
    Array("character_gifts","guid"),
    Array("character_homebind","guid"),
    Array("character_instance","guid"),
    Array("character_inventory","guid"),
    Array("character_pet","owner"),
    Array("character_queststatus","guid"),
    Array("character_reputation","guid"),
    Array("character_social","guid"),
    Array("character_social","friend"),
    Array("character_spell","guid"),
    Array("character_spell_cooldown","guid"),
    Array("gm_tickets","playerGuid"),
    Array("corpse","player"),
    Array("groups","leaderGuid"),
    Array("group_member","memberGuid"),
    Array("group_member","leaderGuid"),
    Array("guild_member","guid"),
    Array("item_instance","owner_guid"),
    Array("mail","receiver"),
    Array("mail_items","receiver"),
    Array("petition","ownerguid"),
    Array("petition_sign","ownerguid"),
    Array("petition_sign","playerguid"),
    Array("characters","guid")
);


//list of tables in realmd db you need to backup data on single user backup
$tab_backup_user_realmd = Array(
    Array("realmcharacters","acctid"),
    Array("account_banned","id"),
    Array("account","id")
);

//list of tables in characters db you need to backup data from on single user backup
$tab_backup_user_characters = Array(
    Array("arena_team_member","guid"),
    Array("auctionhouse","itemowner"),
    Array("character_action","guid"),
    Array("character_aura","guid"),
    Array("character_gifts","guid"),
    Array("character_homebind","guid"),
    Array("character_instance","guid"),
    Array("character_inventory","guid"),
    Array("character_pet","owner"),
    Array("character_queststatus","guid"),
    Array("character_reputation","guid"),
    Array("character_social","guid"),
    Array("character_social","friend"),
    Array("character_spell","guid"),
    Array("character_spell_cooldown","guid"),
    Array("character_ticket","guid"),
    Array("corpse","player"),
    Array("groups","leaderGuid"),
    Array("group_member","memberGuid"),
    Array("group_member","leaderGuid"),
    Array("guild_member","guid"),
    Array("item_instance","owner_guid"),
    Array("mail","receiver"),
    Array("mail_items","receiver"),
    Array("petition","ownerguid"),
    Array("petition_sign","ownerguid"),
    Array("petition_sign","playerguid"),
    Array("characters","guid")
);

$tab_backup_user_characters_trinity = Array(
    Array("arena_team_member","guid"),
    Array("auctionhouse","itemowner"),
    Array("character_action","guid"),
    Array("character_aura","guid"),
    Array("character_gifts","guid"),
    Array("character_homebind","guid"),
    Array("character_instance","guid"),
    Array("character_inventory","guid"),
    Array("character_pet","owner"),
    Array("character_queststatus","guid"),
    Array("character_reputation","guid"),
    Array("character_social","guid"),
    Array("character_social","friend"),
    Array("character_spell","guid"),
    Array("character_spell_cooldown","guid"),
    Array("gm_tickets","playerGuid"),
    Array("corpse","player"),
    Array("groups","leaderGuid"),
    Array("group_member","memberGuid"),
    Array("group_member","leaderGuid"),
    Array("guild_member","guid"),
    Array("item_instance","owner_guid"),
    Array("mail","receiver"),
    Array("mail_items","receiver"),
    Array("petition","ownerguid"),
    Array("petition_sign","ownerguid"),
    Array("petition_sign","playerguid"),
    Array("characters","guid")
);

?>