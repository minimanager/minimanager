CREATE TABLE `mm_motd` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Identifier',
  `realmid` int(11) NOT NULL,
  `type` longtext NOT NULL,
  `content` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='MOTD System';

LOCK TABLES `mm_motd` WRITE;
/*!40000 ALTER TABLE `mm_motd` DISABLE KEYS */;
INSERT INTO `mm_motd` VALUES (1, 1, '02/05/10 14:29:07 Posted by: MiniManager Team', 'Hello Admin\r\n\r\nhelp supporting Minimanager\r\n\r\nhttp://mangos.osh.nu/forums/index.php?app=uportal\r\n\r\nif you found a bug or improved it, please contribute\r\n\r\nor it will eventually stop development from lack of interrest from community ');
/*!40000 ALTER TABLE `mm_motd` ENABLE KEYS */;
UNLOCK TABLES;
