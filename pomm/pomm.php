<?php
/*	POMM
	Player Online Map for MangOs
	16.09.2006
	Created by mirage666 (c) (mailto:mirage666@pisem.net icq# 152263154)
	
	Optimized and Edited In order to fit MMM framework by Q.SA.
*/

require_once ("pomm_lib.php");
valid_login(0);

$realm_name = get_realm_name($realm_id);
?>

<html>
<head><title><?php echo $realm_name ?></title>
<link rel="stylesheet" href="pomm.css" type="text/css">
</head>
<script language="JavaScript" src="../js/ajax/Js.js"></script>
<script language="JavaScript" src="../js/general.js"></script>
<script language="JavaScript" type="text/javascript">
var time = 30;
var race_name = {
		1: '<?php echo $lang_id_tab['human'] ?>',
		2: '<?php echo $lang_id_tab['orc'] ?>',
		3: '<?php echo $lang_id_tab['dwarf'] ?>',
		4: '<?php echo $lang_id_tab['nightelf'] ?>',
		5: '<?php echo $lang_id_tab['undead'] ?>',
		6: '<?php echo $lang_id_tab['tauren'] ?>',
		7: '<?php echo $lang_id_tab['gnome'] ?>',
		8: '<?php echo $lang_id_tab['troll'] ?>',
		9: '<?php echo $lang_id_tab['goblin'] ?>',
		10: '<?php echo $lang_id_tab['bloodelf'] ?>',
		11: '<?php echo $lang_id_tab['draenei'] ?>'
}

var class_name = {
		1: '<?php echo $lang_id_tab['warrior'] ?>',
		2: '<?php echo $lang_id_tab['paladin'] ?>',
		3: '<?php echo $lang_id_tab['hunter'] ?>',
		4: '<?php echo $lang_id_tab['rogue'] ?>',
		5: '<?php echo $lang_id_tab['priest'] ?>',
		7: '<?php echo $lang_id_tab['shaman'] ?>',
		8: '<?php echo $lang_id_tab['mage'] ?>',
		9: '<?php echo $lang_id_tab['warlock'] ?>',
		11: '<?php echo $lang_id_tab['druid'] ?>'
}

function show(data) {
 i=0;
 text='';
 if (data) {
	while (i<data.length) {
		if (data[i].race==2 || data[i].race==5 || data[i].race==6 || data[i].race==8 || data[i].race==10) 
			{point="../img/h_point.gif";} 
			else {point="../img/a_point.gif";}
		text=text+'<img src="'+point+'" style="position: absolute; left: '+data[i].x+'px; top: '+data[i].y+'px;" onmousemove="toolTip(\'<spawn>'+data[i].name+'</spawn><br />'+data[i].zone+'<br /><img src=\\\'../img/c_icons/'+data[i].race+'-'+data[i].gender+'.gif\\\' style=\\\'float:center\\\' /><img src=\\\'../img/c_icons/'+data[i].cl+'.gif\\\' style=\\\'float:center\\\' /><br />'+race_name[data[i].race]+'<br />'+class_name[data[i].cl]+'<br />'+data[i].level+'\',\'tip_text\');"onmouseout="toolTip();"/>';
		i++;
	}
 }
 document.getElementById("points").innerHTML=text;
 document.getElementById("server_info").innerHTML='<?php echo $lang_index['tot_users_online']?> : '+i+' on <?php echo $realm_name ?><br />';
}

function load_data() {
 var req = new JsHttpRequest();
 req.onreadystatechange = function() {
	if (req.readyState == 4) {show(req.responseJS);}
    }
    req.open('get', 'pomm_run.php', true);
    req.send({ });
}

function reset() {
 var ms = 0;
 then = new Date();
 then.setTime(then.getTime()-ms);
 load_data();
}

function display() {
 now = new Date();
 ms = now.getTime() - then.getTime();
 ms = time*1000-ms;
 if  (time!=0) {document.getElementById("timer").innerHTML=(Math.round(ms/1000));}
 if (ms<=0) {
	reset();
	}
 if (time!=0) {setTimeout("display();", 500);}
}

function start() {
 reset();
 display();
}
</script>
<body onload="start();">
<div ID="points"></div><div ID="world_map"></div><div ID="info">
<center>
<table border="0" cellspacing="0" cellpadding="0" height="20">
<tr><td valign="top" id="timer"></td></tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" height="470" width="1">
<tr><td></td></tr></table>
<table border="0" cellspacing="0" cellpadding="0" height="35" width="100%">
<tr><td align="center" valign="top" id="server_info"></td></tr></table></center>
</div></body></html>
