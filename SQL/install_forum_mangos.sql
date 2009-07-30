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
(1,0,'Jiboo',1,1,1,'Hello admin!','[b]Hi[/b] !!:D<br /><br />If you read this, that mean that you [i]correctly[/i] installed this forum, [u]gj[/u]XD<br /><br /><br /><br />So what\' s next?<br /><br />Edit [color=red]forum.conf.php[/color]!<br /><br /><br /><br />And enjoy!<br /><br /><br /><br />Report bug at .Jiboo on [url=Mangos forums]http://www.mangosproject.org/forum[/url]!<br /><br />Do not reply anything related to this forum in the minimanager topics, thx.<br /><br /><br /><br />Bye!<br /><br />Jiboo','06/12/07 14:56:08',1,0);
