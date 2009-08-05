<?php
/*
 * Project Name: MiniManager for Mangos/Trinity Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA (thanks to mirage666 for the original idea) 
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */
 
require_once("config.dist.php");
require_once("config.php");
require_once("../scripts/global_lib.php");
require_once("../scripts/db_lib.php");
require_once("../scripts/get_lib.php");

if (isset($_COOKIE["lang"])){
	$lang = substr($_COOKIE["lang"],0,4);
	if (!file_exists("../lang/$lang.php")) $lang = $language;
	} else $lang = $language;
require_once("../lang/$lang.php");

require_once("../scripts/id_tab.php");

if ( !ini_get('session.auto_start') ) session_start();
$realm_id = $_SESSION['realm_id'];
$user_lvl = $_SESSION['user_lvl'];
$user_id = $_SESSION['user_id'];

function get_player_position($x,$y,$map,$zone) {
 $xpos = round(($x / 1000)*17.7,0);
 $ypos = round(($y / 1000)*17.7,0);
 switch ($map){
   case 1:
    $pos['x'] = 152 - $ypos;
    $pos['y'] = 259 - $xpos;
    break;
   case 0:
    $pos['x'] = 569 - $ypos;
    $pos['y'] = 175 - $xpos;
	break;
	
	case 530:
	if (($zone == 3525) || ($zone == 3557) || ($zone == 3524)){
		$pos['x'] = -162 - $ypos;
		$pos['y'] = 75 - $xpos;
	} else if (($zone == 3487) || ($zone == 3433) || ($zone == 3430)){
				$pos['x'] = 528 - $ypos;
				$pos['y'] = 218 - $xpos;
				} else {
						$pos['x'] = 484 - $ypos;
						$pos['y'] = 272 - $xpos;
				}
	break;

case 70:
    $pos['x'] = 610;
	$pos['y'] = 305;
break;
case 43:
    $pos['x'] = 190;
	$pos['y'] = 275;
break;
case 229:
	$pos['x'] = 582;
	$pos['y'] = 300;
break;
case 230:
	$pos['x'] = 582;
	$pos['y'] = 300;
break;
case 409:
	$pos['x'] = 582;
	$pos['y'] = 302;
break;
case 469:
	$pos['x'] = 582;
	$pos['y'] = 301;
break;
case 489:
    $pos['x'] = 185;
	$pos['y'] = 237;
break;
case 369:
	$pos['x'] = 582;
	$pos['y'] = 265;
break;
case 451:
	$pos['x'] = 435;
	$pos['y'] = 75;
break;
case 34:
	$pos['x'] = 560;
	$pos['y'] = 335;
break;
case 209:
   	$pos['x'] = 200;
	$pos['y'] = 370;
break;
case 35:
	$pos['x'] = 561;
	$pos['y'] = 336;
break;
case 449:
	$pos['x'] = 560;
	$pos['y'] = 335;
break;
case 47:
    $pos['x'] = 190;
	$pos['y'] = 340;
break;
case 531:
    $pos['x'] = 120;
	$pos['y'] = 410;
break;
case 509:
    $pos['x'] = 125;
	$pos['y'] = 410;
break;
case 90:
	$pos['x'] = 560;
	$pos['y'] = 270;
break;
case 389:
	$pos['x'] = 227;
	$pos['y'] = 230;
break;
case 450:
	$pos['x'] = 227;
	$pos['y'] = 228;
break;
case 533:
   	$pos['x'] = 640;
	$pos['y'] = 130;
break;
case 532:
   $pos['x'] = 605;
   $pos['y'] = 365;
break;
case 550:
   $pos['x'] = 455;
   $pos['y'] = 216;
break;
case 552:
   $pos['x'] = 455;
   $pos['y'] = 216;
break;
case 553:
   $pos['x'] = 455;
   $pos['y'] = 216;
break;
case 554:
   $pos['x'] = 455;
   $pos['y'] = 216;
break;
case 540:
   $pos['x'] = 425;
   $pos['y'] = 275;
break;
case 542:
   $pos['x'] = 425;
   $pos['y'] = 275;
break;
case 543:
   $pos['x'] = 425;
   $pos['y'] = 275;
break;
case 544:
   $pos['x'] = 425;
   $pos['y'] = 275;
break;
case 555:
   $pos['x'] = 380;
   $pos['y'] = 330;
break;
case 556:
   $pos['x'] = 380;
   $pos['y'] = 330;
break;
case 557:
   $pos['x'] = 380;
   $pos['y'] = 330;
break;
case 558:
   $pos['x'] = 380;
   $pos['y'] = 330;
break;
case 545:
   $pos['x'] = 338;
   $pos['y'] = 290;
break;
case 546:
   $pos['x'] = 338;
   $pos['y'] = 290;
break;
case 547:
   $pos['x'] = 338;
   $pos['y'] = 290;
break;
case 548:
   $pos['x'] = 338;
   $pos['y'] = 290;
break;
case 249:
   $pos['x'] = 215;
   $pos['y'] = 340;
break;
case 329:
   $pos['x'] = 630;
   $pos['y'] = 115;
break;
case 289:
   $pos['x'] = 612;
   $pos['y'] = 150;
break;
case 565:
   $pos['x'] = 375;
   $pos['y'] = 210;
break;
case 269:
   $pos['x'] = 225;
   $pos['y'] = 410;
break;
case 189:
   $pos['x'] = 580;
   $pos['y'] = 120;
break;
case 33:
   $pos['x'] = 540;
   $pos['y'] = 175;
break;
case 109:
   $pos['x'] = 640;
   $pos['y'] = 352;
break;
case 36:
   $pos['x'] = 545;
   $pos['y'] = 310;
break;
case 48:
   $pos['x'] = 135;
   $pos['y'] = 185;
break;
case 129:
    $pos['x'] = 195;
	$pos['y'] = 340;
break;
case 309:
    $pos['x'] = 605;
	$pos['y'] = 385;
break;
case 429:
    $pos['x'] = 135;
	$pos['y'] = 325;
break;
case 349:
    $pos['x'] = 100;
	$pos['y'] = 275;
break;
case 560:
   $pos['x'] = 225;
   $pos['y'] = 410;
break;
case 534:
   $pos['x'] = 225;
   $pos['y'] = 410;
break;
/* TODO: ADD PROPER COORDS FOR INSTANCES
case 30:
   return($lang_id_tab['alterac_valley']);
   break;
case 44:
   return($lang_id_tab['monastery_interior']);
   break;
case 169:
   return($lang_id_tab['emerald_forest']);
   break;
case 529:
   return($lang_id_tab['arathi_basin']);
   break;
case 559:
   return($lang_id_tab['nagrand_arena']);
   break;
case 562:
   return($lang_id_tab['blades_edge_arena']);
   break;
case 564:
   return($lang_id_tab['black_temple']);
   break;
case 566:
   return($lang_id_tab['netherstorm_arena']);
   break;
case 568:
   return($lang_id_tab['zulaman']);
   break;
*/	
   default:
    $pos['x'] = -1;
    $pos['y'] = -1;
 }
 return $pos;
}

?>