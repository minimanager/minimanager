<?php

$output .= '
<fieldset>
	<legend>Menu</legend>
	<div id="tab">
		<ul>
			<li><a href="char.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['char_sheet'].'</a></li>
			<li><a href="char_inv.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['inventory'].'</a></li>
			<li><a href="char_extra.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['extra'].'</a></li>
				'.(($char['level'] < 10) ? '' : '<li><a href="char_talent.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['talents'].'</a></li>').'
			<li><a href="char_achieve.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['achievements'].'</a></li>
			<li><a href="char_rep.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['reputation'].'</a></li>
			<li><a href="char_skill.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['skills'].'</a></li>
			<li><a href="char_spell.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['spells'].'</a></li>
			<li><a href="char_quest.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['quests'].'</a></li>';
	if (char_get_class_name($char['class']) === 'Hunter' )
$output .= '
			<li><a href="char_pets.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['pets'].'</a></li>';
$output .= '
			<li><a href="char_friends.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['friends'].'</a></li>
			<li><a href="char_mail.php?id='.$id.'&amp;realm='.$realmid.'">'.$lang_char['mail'].'</a></li>
		</ul>
	</div>
</fieldset>';

?>