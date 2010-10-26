DROP TABLE IF EXISTS `point_system_invites`;

CREATE TABLE `point_system_invites` (
  `entry` int(11) NOT NULL auto_increment,
  `PlayersAccount` char(50) default NULL,
  `InvitedBy` char(50) default NULL,
  `InviterAccount` char(50) default NULL,
  `Treated` int(1) NOT NULL default '0',
  `Rewarded` int(1) NOT NULL default '0',
  UNIQUE KEY `entry` (`entry`)
) ENGINE=MyISAM AUTO_INCREMENT=196 DEFAULT CHARSET=latin1;