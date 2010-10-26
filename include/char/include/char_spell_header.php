<?php

$output .= '
	<div id="tab">
		<ul>
			<li><a href="char_spell.php?action=char_spell&amp;id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['spells'].'</a></li>
			<li><a href="char_spell.php?action=char_companion&amp;id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['companions'].'</a></li>
			<li><a href="char_spell.php?action=char_mounts&amp;id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['mounts'].'</a></li>
		</ul>
	</div>
	<br />';

?>