<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License (GPL)
 *
 * Forum modification, by Jiboo.
 */

$maxqueries = 20; // Max topic / post by pages
$minfloodtime = 15; // Minimum time beetween two post
$enablesidecheck = false; // if you dont use side specific forum, desactive it, because it will do one less query.

$forum_skeleton = Array(
	1 => Array(
		"name" => "Server Category",
		"forums" => Array(
			1 => Array(
				"name" => "News",
				"desc" => "News and infos about the server",
				"level_post_topic" => 3
			),
			2 => Array(
				"name" => "General Talks",
				"desc" => "Talk about everything related to the server"
			)
		)
	),
	2 => Array(
		"name" => "Game Category",
		"forums" => Array(
			3 => Array(
				"name" => "Bugs and problems",
				"desc" => "Ask here help from GM or Admin, not to beg money item or xp, thx.",
			),
			4 => Array(
				"name" => "Horde and alliance forums",
				"desc" => "Talk about everything related to the game"
			),
			5 => Array(
				"name" => "Horde forum only",
				"desc" => "Only horde players can see this",
				"side_access" => "H"
			),
			6 => Array(
				"name" => "Alliance forum only",
				"desc" => "Only alliance players can see this",
				"side_access" => "A"
			),
			7 => Array(
				"name" => "Admins forums only",
				"desc" => "Only admins can see this",
				"level_read" => "3",
				"level_post" => "3"
			)
		)
	)
);
?>