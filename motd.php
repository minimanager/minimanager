<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/bbcode_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//#############################################################################
// SHOW MOTD
//#############################################################################
function motd(&$sqlm)
{
	global 	$output,
			$lang_motd, $lang_global, $lang_index,
			$action_permission, $user_lvl, $user_id,
			$motd_display_poster,
			$realm_id,
			$mmfpm_db;
// minimum permission to view page
valid_login($action_permission['read']);

  //MOTD part
	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);
	
	$start_m = (isset($_GET['start_m'])) ? $sqlm->quote_smart($_GET['start_m']) : 0;
	if (is_numeric($start_m)); else $start_m = 0;

	// get all entries, need to add total to page
	$all_record_m = $sqlm->result($sqlm->query('
		SELECT count(*) 
		FROM mm_motd'), 0);

	// to delete MOTD
	if ($user_lvl >= $action_permission['delete'])
$output .= '
<script type="text/javascript">
	// <![CDATA[
		answerbox.btn_ok="'.$lang_global['yes_low'].'";
		answerbox.btn_cancel="'.$lang_global['no'].'";
		var del_motd = "motd.php?action=delete_motd&amp;id=";
	// ]]>
</script>';
$output .= '
<center>
<table class="lined">
	<tr>
		<th align="center">';
	// to add new MOTD
	if ($user_lvl >= $action_permission['insert'])
$output .= '
			<font size="2"><a href="motd.php?action=add_motd&amp;error=4">'.$lang_index['add_motd'].'</a></font>';
	else
$output .= '
			<font size="2">'.$lang_index['motd'].'</a></font>
		</th>
	</tr>';
	// if is there any record
	if($all_record_m)
	{
		// here we get MOTD content
		$result = $sqlm->query('
			SELECT id, realmid, type, content 
			FROM mm_motd 
			WHERE realmid = '.$realm_id.' 
			ORDER BY id DESC 
			LIMIT '.$start_m.', 10');

		while($post = $sqlm->fetch_assoc($result))
		{
$output .= '
	<tr>
		<td align="left" class="large">
			<blockquote>'.bbcode_bbc2html($post['content']).'</blockquote>
		</td>
	</tr>
	<tr>
		<td align="left">';
	($motd_display_poster) ? $output .= $post['type'] : '';
		// to delete MOTD
		if ($user_lvl >= $action_permission['delete'])
$output .= '
			<img src="img/cross.png" width="12" height="12" onclick="answerBox(\''.$lang_global['delete'].': &lt;font color=white&gt;'.$post['id'].'&lt;/font&gt;&lt;br /&gt;'.$lang_global['are_you_sure'].'\', del_motd + '.$post['id'].');" style="cursor:pointer;" alt="" />';
		// to edit MOTD
		if ($user_lvl >= $action_permission['update'])
$output .= '
			<a href="motd.php?action=edit_motd&amp;error=3&amp;id='.$post['id'].'">
				<img src="img/edit.png" width="14" height="14" alt="" />
			</a>';
$output .= '
		</td>
	</tr>
	<tr>
		<td class="hidden"></td>
	</tr>';
		}
$output .= '
	<tr>
		<td align="right" class="hidden">'.generate_pagination('motd.php?start=0', $all_record_m, 10, $start_m, 'start_m').'</td>
	</tr>';
	}
$output .= '
</table>';
}

//#############################################################################
// ADD MOTD
//#############################################################################
function add_motd(&$sqlm)
{
	global 	$output,
			$lang_motd, $lang_global,
			$action_permission;
// minimum permission to view page
valid_login($action_permission['insert']);

$output .= '
<center>
	<form action="motd.php?action=do_add_motd" method="post" name="form">
		<table class="top_hidden">
			<tr>
				<td colspan="3">';
					bbcode_add_editor();
$output .= '
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<textarea id="msg" name="msg" rows="26" cols="97"></textarea>
				</td>
			</tr>
			<tr>
				<td>'.$lang_motd['post_rules'].'</td>
				<td>';
					makebutton($lang_motd['post_motd'], 'javascript:do_submit()" type="wrn', 230);
					$output .= '
				</td>
				<td>';
					makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
					$output .= '
				</td>
			</tr>
		</table>
	</form>
<br />
</center>';
}

//#####################################################################################################
// DO ADD MOTD
//#####################################################################################################
function do_add_motd(&$sqlm)
{
	global 	$action_permission,
			$user_name,
			$realm_id,
			$mmfpm_db;
// minimum permission to view page
valid_login($action_permission['insert']);

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if (empty($_POST['msg']))
		redirect('motd.php?error=1');
	$msg = $sqlm->quote_smart($_POST['msg']);
	if (4096 < strlen($msg))
		redirect('motd.php?error=2');

	$by = date('m/d/y H:i:s').' Posted by: '.$user_name;

	$sqlm->query('
		INSERT INTO mm_motd
			(realmid, type, content)
		VALUES
			(\''.$realm_id.'\', \''.$by.'\', \''.$msg.'\')');

unset($by);
unset($msg);

redirect('index.php');
}

//#############################################################################
// EDIT MOTD
//#############################################################################
function edit_motd(&$sqlm)
{
	global 	$output,
			$lang_motd, $lang_global,
			$realm_id, $mmfpm_db,
			$action_permission;
// minimum permission to view page
valid_login($action_permission['update']);

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if(empty($_GET['id']))
		redirect('motd.php?error=1');
	$id = $sqlm->quote_smart($_GET['id']);
	if(is_numeric($id));
	else
		redirect('motd.php?error=1');

	$msg = $sqlm->result($sqlm->query('
		SELECT content
		FROM mm_motd
		WHERE id = '.$id.''), 0);

$output .= '
<center>
	<form action="motd.php?action=do_edit_motd" method="post" name="form">
		<input type="hidden" name="id" value="'.$id.'" />
		<table class="top_hidden">
			<tr>
				<td colspan="3">';
unset($id);
					bbcode_add_editor();
					$output .= '
				</td>
			</tr>
			<tr>
				<td colspan="3">
					<textarea id="msg" name="msg" rows="26" cols="97">'.$msg.'</textarea>
				</td>
			</tr>
			<tr>
				<td>'.$lang_motd['post_rules'].'</td>
				<td>';
unset($msg);
					makebutton($lang_motd['post_motd'], 'javascript:do_submit()" type="wrn', 230);
					$output .= '
				</td>
				<td>';
					makebutton($lang_global['back'], 'javascript:window.history.back()" type="def', 130);
					$output .= '
				</td>
			</tr>
		</table>
	</form>
	<br />
</center>';
}

//#####################################################################################################
// DO EDIT MOTD
//#####################################################################################################
function do_edit_motd(&$sqlm)
{
	global 	$action_permission,
			$user_name,
			$realm_id,
			$mmfpm_db;
// minimum permission to view page
valid_login($action_permission['update']);

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if (empty($_POST['msg']) || empty($_POST['id']))
		redirect('motd.php?error=1');
	$id = $sqlm->quote_smart($_POST['id']);
	if(is_numeric($id));
	else
		redirect('motd.php?error=1');

	$msg = $sqlm->quote_smart($_POST['msg']);
	if (4096 < strlen($msg))
		redirect('motd.php?error=2');

	$by = $sqlm->result($sqlm->query('
		SELECT type
		FROM mm_motd
		WHERE id = '.$id.''), 0);
	$by = split('<br />', $by, 2);
	$by = $by[0].'<br />'.date('m/d/y H:i:s').' Edited by: '.$user_name;

	$sqlm->query('
		UPDATE mm_motd
		SET realmid = \''.$realm_id.'\', type = \''.$by.'\', content = \''.$msg.'\'
		WHERE id = '.$id.'');

unset($by);
unset($msg);
unset($id);
redirect('index.php');
}

//#####################################################################################################
// DELETE MOTD
//#####################################################################################################
function delete_motd(&$sqlm)
{
	global 	$action_permission,
			$realm_id,
			$mmfpm_db;
// minimum permission to view page
valid_login($action_permission['delete']);

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if (empty($_GET['id']))
		redirect('index.php');
	$id = $sqlm->quote_smart($_GET['id']);
	if(is_numeric($id));
	else
		redirect('motd.php?error=1');

	$sqlm->query('
		DELETE FROM mm_motd
		WHERE id ='.$id.'');

unset($id);
redirect('index.php');
}

//########################################################################################################################
// MAIN
//########################################################################################################################

// load language
$lang_motd = lang_motd();
$lang_index = lang_index();

// $_GET and SECURE
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
<div class="top">';

// defines the title header in error cases
if (1 == $err)
$output .= '
	<h1><font class="error">'.$lang_global['empty_fields'].'</font></h1>';
elseif (2 == $err)
$output .= '
	<h1><font class="error">'.$lang_motd['err_max_len'].'</font></h1>';
elseif (3 == $err)
$output .= '
	<h1>'.$lang_motd['edit_motd'].'</h1>';
elseif (4 == $err)
$output .= '
	<h1>'.$lang_motd['add_motd'].'</h1>';
else
$output .= '
	<h1>'.$lang_index['motd'].'</h1>';

$output .= '
</div>';

// $_GET and SECURE
$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// define functions to be called by actions
if ('delete_motd' == $action)
	delete_motd($sqlm);
elseif ('add_motd' == $action)
	add_motd($sqlm);
elseif ('do_add_motd' == $action)
	do_add_motd($sqlm);
elseif ('edit_motd' == $action)
	edit_motd($sqlm);
elseif ('do_edit_motd' == $action)
	do_edit_motd($sqlm);
else
	motd($sqlm);

// close whats not needed anymore
unset($err);
unset($action);
unset($action_permission);
unset($lang_motd);
unset($lang_index);

// page footer
require_once 'footer.php';

?>
