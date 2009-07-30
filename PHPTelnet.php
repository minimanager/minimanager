<?php
require_once "scripts/PHPTelnet.php";

$telnet = new PHPTelnet();
$telnet->show_connect_error=0;

// if the first argument to Connect is blank,
// PHPTelnet will connect to the local host via 127.0.0.1
$result = $telnet->Connect('login.wowisrael.co.il','CREDITCONSOLE','a3d2**gs2!3');
$telename = $_GET['charname'];
$place = $_GET['tplace'];

switch ($result) {
case 0:
$telnet->DoCommand("tele name $telename $place", $result);
// NOTE: $result may contain newlines
echo $result;
// say Disconnect(0); to break the connection without explicitly logging out
$telnet->Disconnect();
break;
case 1:
echo '[PHP Telnet] Connect failed: Unable to open network connection';
break;
case 2:
echo '[PHP Telnet] Connect failed: Unknown host';
break;
case 3:
echo '[PHP Telnet] Connect failed: Login failed';
break;
case 4:
echo '[PHP Telnet] Connect failed: Your PHP version does not support PHP Telnet';
break;
}
?> 