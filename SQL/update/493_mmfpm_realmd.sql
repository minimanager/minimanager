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

LOCK TABLES `mm_forum_posts` WRITE;
/*!40000 ALTER TABLE `mm_realmd` DISABLE KEYS */;
INSERT INTO `mm_realmd` VALUES ('MaNGOS', '127.0.0.1', '3724');
/*!40000 ALTER TABLE `mm_realmd` ENABLE KEYS */;
UNLOCK TABLES;