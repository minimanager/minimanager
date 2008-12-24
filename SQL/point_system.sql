--
-- Table structure for table `point_system_invites`
--

DROP TABLE IF EXISTS `point_system_invites`;
CREATE TABLE `point_system_invites` (
  `entry` int(11) NOT NULL auto_increment,
  `PlayersAccount` char(50) default NULL,
  `InvitedBy` char(50) default NULL,
  `InviterAccount` char(50) default NULL,
  `Treated` int(1) NOT NULL default '0',
  `Rewarded` int(1) NOT NULL default '0',
  UNIQUE KEY `entry` (`entry`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `point_system_invites`
--

LOCK TABLES `point_system_invites` WRITE;
/*!40000 ALTER TABLE `point_system_invites` DISABLE KEYS */;
/*!40000 ALTER TABLE `point_system_invites` ENABLE KEYS */;
UNLOCK TABLES;