MiniManager for Project MaNGOS/Trinity - mmfpm

ABOUT:
 MiniManager for Project MaNGOS/Trinity Server is a PHP web front end to provide easy access
to MaNGOS/Trinity server management side. Manage/add/remove/edit/lock/ban/ect. User accounts/characters,
manage DB and server itself. It is small, easy to use, flexible tool.
 MiniManager entirely based on database provided by MaNGOS/Trinity and does not required any external add-ons.
The user access level is based on server MaNGOS/Trinity account's gmlevel, in other words if you have 
an gamemaster account (gmlvl 2) on the server you will have permission to access
level 2 content on MiniManager and so on. Every operation have its minimal level to be preformed,
from viewing online users (lvl0 -player) to account cleanup (lvl3-admin).

It is not an eye-candy - strictly functional utility, almost no graphics what so ever.
I will not list all its functionalities here, just setup it and see for yourself, logging at different user levels.

 Since MiniManager is under development (far away from perfect) and it is one man project ATM
you WILL find bugs and ways to make it better both efficiency and design wise. Please let
me know or if you can and have time do it yourself, Thank you in advance.


TECHNICAL NOTES:
 If you obtained this package I assume you already have or able to setup working MaNGOS/Trinity server,
if not, QUIT right now - this content is USLESS to you.

 This is NOT a "How to setup working web Server with PHP support" - if you got so far
you either already got one running or can set it up by yourself.

 MiniManager was written and tested under Linux_FC5 Apache2 PHPv5.1 MySQL5 FireFox2
Therefore it MAY have issues running under other environment. I can assume it works equally
good under most of modern webservers/browsers, other MySQL server able to run MaNGOS/Trinity DB.
Tested under:
Linux_FC5/FC6/FC7 / WinXP_32_sp2
MySQL 5.2/5.1/5.0
Apache 1.3/2.0.59/2.2.4
PHP 5.2.1 
IEv7 / FireFox 1.5/2

 In order to use build in SSH/Telnet clent make sure you have ssh/telnet server up and running 
and you have jave enabled web browser. Supports Telnet 2.5 / SSH 1.5 protocols.

 Internet Explorer or any other browser users MAY experience minor problems with CSS design,
it is fixable from editing .css file.
 Windows Developers: All files excluding readme.txt ARE in Unix EOL format - Sorry - use
anything supports it under win (basically it is anything but notepad)

 ".htaccess" files where used to avoid subdir browsing, if your webserver does not support
them - it's up to you restricting access to protected content.


INSTALLATION:
 Just extract and place inside one of your website folders.
Naturally you will need webserver supports PHP 4.3 and up.
Just make sure you have sessions setup correctly in your php.ini file.
Under "scripts" directory you will find "config.php" file - Edit it - its content is
self-explainable. DO NOT forget to configure your realm in addition to Trinityd.
 If you have issues with proper item icon display edit /scripts/get_lib.php line 619 - set correct
proxy address if used, and make sure the INV directory is set to R/W permission for webserver.
Alternatively you can download and extract pre-cached icons, extracting them into /img/INV directory.


COPYRIGHTING RELATED:
 I did my best following the MaNGOS/Trinity Project spirit avoiding copyrighted material, almost.
As for MiniManager itself - it is Under GNU GENERAL PUBLIC LICENSE version 2, please take a look at GPL file.
By downloading my scripts you acknowledged the usage rules and any legal issues under which this package
distributed under (GPL).

THANKS:
- To entire MaNGOS/Trinity team and all related for providing our "community" this great
 educational tool.
- Guys from www.javassh.org for providing basic source for the terminal.
- PHPMailer Team http://phpmailer.sourceforge.net/ for providing mail class.
- To mirage666 (mailto:mirage666@pisem.net) for basic POMM code.
- To Paul Johnston for JavaScript implementation of the Secure Hash Algorithm, SHA-1.
- To guys from http://particletree.com for Dynamic Resolution Dependent Layout javascript code.
- To people from http://www.pjirc.com/ for irc java applet.
- Everybody who helped me with ideas, bugreports, actual code.
- To QSA for the initial release of the MaNGOS/Trinity minimanager.

Thank you for attention.