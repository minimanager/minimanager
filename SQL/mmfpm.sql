-- Database: mmfpm

--
-- Table structure for table `mm_realmd`
--

DROP TABLE IF EXISTS `mm_realmd`;
CREATE TABLE `mm_realmd` (
`name` varchar(32) NOT NULL DEFAULT 'MaNGOS',
`address` varchar(32) NOT NULL DEFAULT '127.0.0.1',
`port` int(11) NOT NULL DEFAULT '3724'
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Realm System';

--
-- Dumping data for table `mm_realmd`
--

-- table not found error
-- LOCK TABLES `mm_forum_posts` WRITE
-- table not found error

LOCK TABLES `mm_realmd` WRITE;
/*!40000 ALTER TABLE `mm_realmd` DISABLE KEYS */;
INSERT INTO `mm_realmd` VALUES ('MaNGOS', '127.0.0.1', '3724');
/*!40000 ALTER TABLE `mm_realmd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mm_account_verification`
--

DROP TABLE IF EXISTS `mm_account_verification`;
CREATE TABLE `mm_account_verification` (
  `id` int(11) unsigned NOT NULL auto_increment COMMENT 'Identifier',
  `username`      varchar(32) NOT NULL default '',
  `sha_pass_hash` varchar(40) NOT NULL default '',
  `gmlevel` tinyint(3) unsigned NOT NULL default '0',
  `sessionkey` longtext,
  `v` longtext,
  `s` longtext,
  `email` text,
  `joindate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `last_ip` varchar(30) NOT NULL default '0.0.0.0',
  `failed_logins` int(11) unsigned NOT NULL default '0',
  `locked` tinyint(3) unsigned NOT NULL default '0',
  `last_login` timestamp NOT NULL default '0000-00-00 00:00:00',
  `active_realm_id` int(11) unsigned NOT NULL default '0',
  `expansion` tinyint(3) unsigned NOT NULL default '0',
  `mutetime` bigint(40) unsigned NOT NULL default '0',
  `locale` tinyint(3) unsigned NOT NULL default '0',
  `authkey` varchar(40) DEFAULT '',
  PRIMARY KEY  (`id`),
  UNIQUE KEY `idx_username` (`username`),
  KEY `idx_gmlevel` (`gmlevel`)
) ENGINE=MyISAM AUTO_INCREMENT=0 DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci ROW_FORMAT=DYNAMIC COMMENT='Account System';

--
-- Table structure for table `mm_forum_posts`
--

DROP TABLE IF EXISTS `mm_forum_posts`;

CREATE TABLE `mm_forum_posts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `authorid` bigint(20) unsigned NOT NULL DEFAULT '0',
  `authorname` varchar(16) NOT NULL DEFAULT '',
  `forum` bigint(20) unsigned NOT NULL DEFAULT '0',
  `topic` bigint(20) unsigned NOT NULL DEFAULT '0',
  `lastpost` bigint(20) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `text` longtext,
  `time` varchar(255) NOT NULL,
  `annouced` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `sticked` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `closed` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

--
-- Dumping data for table `mm_forum_posts`
--

LOCK TABLES `mm_forum_posts` WRITE;
/*!40000 ALTER TABLE `mm_forum_posts` DISABLE KEYS */;
INSERT INTO `mm_forum_posts`(`id`,`authorid`,`authorname`,`forum`,`topic`,`lastpost`,`name`,`text`,`time`,`annouced`,`sticked`) values
(1,0,'miniManagerTeam',1,1,1,'Hello Admin!','[b]Hi[/b] !!:D<br /><br />If you are reading this, that means that you have [i]correctly[/i] installed this forum, XD<br /><br /><br /><br />So what\' s next?<br /><br />Edit [color=red]forum.conf.php[/color]<br /><br /><br /><br />And enjoy!<br /><br /><br /><br />Report bugs at [url=http://mangos.osh.nu/forums/index.php?showforum=19]miniManager forums[/url]<br /><br /><br /><br />Bye!<br /><br />miniManagerTeam','00/00/00 00:00:00',1,0);
/*!40000 ALTER TABLE `mm_forum_posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mm_motd`
--

DROP TABLE IF EXISTS `mm_motd`;

CREATE TABLE `mm_motd` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifier',
  `realmid` int(11) NOT NULL,
  `type` longtext NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='MOTD System';

--
-- Dumping data for table `mm_motd`
--

LOCK TABLES `mm_motd` WRITE;
/*!40000 ALTER TABLE `mm_motd` DISABLE KEYS */;
INSERT INTO `mm_motd` VALUES (1, 1, '02/05/10 14:29:07 Posted by: MiniManager Team', 'Hello Admin\r\n\r\nhelp supporting Minimanager\r\n\r\nhttp://mangos.osh.nu/forums/index.php?app=uportal\r\n\r\nif you found a bug or improved it, please contribute\r\n\r\nor it will eventually stop development from lack of interrest from community ');
/*!40000 ALTER TABLE `mm_motd` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `mm_point_system_invites`
--

DROP TABLE IF EXISTS `mm_point_system_invites`;
CREATE TABLE `mm_point_system_invites` (
  `entry` int(11) NOT NULL auto_increment,
  `PlayersAccount` char(50) default NULL,
  `InvitedBy` char(50) default NULL,
  `InviterAccount` char(50) default NULL,
  `Treated` int(1) NOT NULL default '0',
  `Rewarded` int(1) NOT NULL default '0',
  UNIQUE KEY `entry` (`entry`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
