<?php

$output .= '
<fieldset>
	<legend>Char Info</legend>
	<div id="tab">
		<font class="bold">
			'.htmlentities($char['name']).' -
			<img src="img/c_icons/'.$char['race'].'-'.$char['gender'].'.gif"
				onmousemove="toolTip(\''.char_get_race_name($char['race']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />
			<img src="img/c_icons/'.$char['class'].'.gif"
				onmousemove="toolTip(\''.char_get_class_name($char['class']).'\',\'item_tooltip\')" onmouseout="toolTip()" alt="" /> - lvl '.char_get_level_color($char['level']).'
		</font>
	</div>
</fieldset>';

?>