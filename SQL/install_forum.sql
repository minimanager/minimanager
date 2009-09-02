/*Table structure for table `forum_posts` */
CREATE TABLE `forum_posts` (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `authorid` bigint(20) unsigned NOT NULL default '0',
  `authorname` varchar(16) NOT NULL default '',
  `forum` bigint(20) unsigned NOT NULL default '0',
  `topic` bigint(20) unsigned NOT NULL default '0',
  `lastpost` bigint(20) unsigned NOT NULL default '0',
  `name` varchar(50) NOT NULL default '',
  `text` longtext,
  `time` varchar(255) NOT NULL,
  `annouced` tinyint(3) unsigned NOT NULL default '0',
  `sticked` tinyint(3) unsigned NOT NULL default '0',
  `closed` tinyint(3) unsigned NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;

/*Data for the table `forum_posts` */
INSERT INTO `forum_posts`(`id`,`authorid`,`authorname`,`forum`,`topic`,`lastpost`,`name`,`text`,`time`,`annouced`,`sticked`) values
(1,0,'miniManagerTeam',1,1,1,'Hello Admin!','[b]Hi[/b] !!:D<br /><br />If you are reading this, that means that you have [i]correctly[/i] installed this forum, XD<br /><br /><br /><br />So what\' s next?<br /><br />Edit [color=red]forum.conf.php[/color]<br /><br /><br /><br />And enjoy!<br /><br /><br /><br />Report bugs at [url=http://mangos.osh.nu/forums/index.php?showforum=19]miniManager forums[/url]<br /><br /><br /><br />Bye!<br /><br />miniManagerTeam','00/00/00 00:00:00',1,0);
