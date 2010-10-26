<?php

// page header, and any additional required libraries
require_once 'header.php';
require_once 'libs/char_lib.php';
// minimum permission to view page
valid_login($action_permission['read']);

//########################################################################################################################
// BROWSE USERS
//########################################################################################################################
function browse_users(&$sqlr)
{
	global	$output, $lang_global, $lang_user,
			$mmfpm_db,
			$action_permission, $user_lvl, $user_name,
			$itemperpage, $showcountryflag, $expansion_select,
			$gm_level_arr;

	$active_realm_id_pq = "active_realm_id";

$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

//==========================$_GET and SECURE========================
	$start = (isset($_GET['start'])) ? $sqlr->quote_smart($_GET['start']) : 0;
	if (is_numeric($start)); else $start=0;

	$order_by = (isset($_GET['order_by'])) ? $sqlr->quote_smart($_GET['order_by']) : 'id';
	if (preg_match('/^[_[:lower:]]{1,15}$/', $order_by)); else $order_by='id';

	$dir = (isset($_GET['dir'])) ? $sqlr->quote_smart($_GET['dir']) : 1;
	if (preg_match('/^[01]{1}$/', $dir)); else $dir=1;

	$order_dir = ($dir) ? 'ASC' : 'DESC';
	$dir = ($dir) ? 0 : 1;
//==========================$_GET and SECURE end========================

	$search_by = '';
	$search_value = '';

	// if we have a search request, if not we just return everything
	if(isset($_GET['search_value']) && isset($_GET['search_by']))
	{
		// injection prevention
		$search_value = $sqlr->quote_smart($_GET['search_value']);
		$search_by = (isset($_GET['search_by'])) ? $sqlr->quote_smart($_GET['search_by']) : 'username';
		$search_menu = array('username', 'id', 'gmlevel', 'greater_gmlevel', 'email', 'joindate', 'last_ip', 'failed_logins', 'last_login', 'active_realm_id', 'banned', 'locked', 'expansion');
		if (in_array($search_by, $search_menu));
		else $search_by = 'username';
unset($search_menu);

		// special search cases
		// developer note: 'if else' is always faster then 'switch case'
		if ($search_by === 'greater_gmlevel')
		{
			$sql_query = '
				SELECT id, username, gmlevel, email, joindate, last_ip, failed_logins, locked,last_login, '.$active_realm_id_pq.', expansion
				FROM account
				WHERE gmlevel > '.$search_value.'
				ORDER BY '.$order_by.' '.$order_dir.'
				LIMIT '.$start.', '.$itemperpage.'';

			$query_1 = $sqlr->query('
				SELECT count(*)
				FROM account
				WHERE gmlevel > '.$search_value.'');
		}
		elseif ($search_by === 'banned')
		{
		$sql_query = '
			SELECT id, username, gmlevel, email, joindate, last_ip, failed_logins, locked, last_login, '.$active_realm_id_pq.', expansion
			FROM account
			WHERE id = 0 ';

		$count_query = '
			SELECT count(*)
			FROM account
			WHERE id = 0 ';

		$que = $sqlr->query('
			SELECT id
			FROM account_banned');

			while ($banned = $sqlr->fetch_assoc($que))
			{
				$sql_query .= ' OR id = '.$banned['id'].' ';
				$count_query .= ' OR id = '.$banned['id'].' ';
			}
			$sql_query .= '
				ORDER BY '.$order_by.' '.$order_dir.'
				LIMIT '.$start.', '.$itemperpage.'';

			$query_1 = $sqlr->query($count_query);
unset($count_query);
		}
		elseif ($search_by === 'failed_logins')
		{
		$sql_query = '
			SELECT *
			FROM account
			WHERE failed_logins > '.$search_value.'
			ORDER BY '.$order_by.' '.$order_dir.'
			LIMIT '.$start.', '.$itemperpage.'';

			$query_1 = $sqlr->query('
				SELECT count(*)
				FROM account
				WHERE failed_logins > '.$search_value.'');
		}
		else
		{
			// default search case
		$sql_query = '
			SELECT id, username, gmlevel, email, joindate, last_ip, failed_logins, locked, last_login, '.$active_realm_id_pq.', expansion
			FROM account
			WHERE '.$search_by.' LIKE "%'.$search_value.'%"
			ORDER BY '.$order_by.' '.$order_dir.'
			LIMIT '.$start.', '.$itemperpage.'';

			$query_1 = $sqlr->query('
				SELECT count(*)
				FROM account
				WHERE '.$search_by.' LIKE "%'.$search_value.'%"');
		}
	$query = $sqlr->query($sql_query);
	}
	else
	{
		// get total number of items
		$query_1 = $sqlr->query('
			SELECT count(*)
			FROM account');

		$query = $sqlr->query('
			SELECT *
			FROM account
			ORDER BY '.$order_by.' '.$order_dir.'
			LIMIT '.$start.', '.$itemperpage.'');
	}
	// this is for multipage support
	$all_record = $sqlr->result($query_1,0);
unset($query_1);	

//==========================top tage navigaion starts here========================
$output .='
<script type="text/javascript" src="libs/js/check.js"></script>
<center>
<table class="top_hidden">
	<tr>
		<td>';
	if ($user_lvl >= $action_permission['insert'])
	{
			makebutton($lang_user['add_acc'], 'accounts.php?action=add_new', 130);
$output .= '
			';
	}
	if($user_lvl >= $action_permission['insert'])
	{
			makebutton($lang_user['backup'], 'backup.php" type="def', 130);
$output .= '
			';
	}
	if($user_lvl >= $action_permission['delete'])
	{
			makebutton($lang_user['cleanup'], 'cleanup.php" type="wrn', 130);
$output .= '
			';
	}
			makebutton($lang_global['back'], 'javascript:window.history.back()', 130);
	if ($search_by && $search_value)
	{
			makebutton($lang_user['user_list'], 'accounts.php', 130);
	}
$output .= '
		</td>
	</tr>
	<tr>
		<td>
			<table class="lined">
				<tr>
					<td align="left" width="40%">
						<form action="accounts.php" method="get" name="form">
							<input type="hidden" name="error" value="3" />
							<input type="text" size="24" maxlength="50" name="search_value" value="'.$search_value.'" />
							<select name="search_by">
								<option value="username"'.($search_by === 'username' ? ' selected="selected"' : '').'>'.$lang_user['by_name'].'</option>
								<option value="id"'.($search_by === 'id' ? ' selected="selected"' : '').'>'.$lang_user['by_id'].'</option>
								<option value="gmlevel"'.($search_by === 'gmlevel' ? ' selected="selected"' : '').'>'.$lang_user['by_gm_level'].'</option>
								<option value="greater_gmlevel"'.($search_by === 'greater_gmlevel' ? ' selected="selected"' : '').'>'.$lang_user['greater_gm_level'].'</option>
								<option value="expansion"'.($search_by === 'expansion' ? ' selected="selected"' : '').'>'.$lang_user['by_expansion'].'</option>
								<option value="email"'.($search_by === 'email' ? ' selected="selected"' : '').'>'.$lang_user['by_email'].'</option>
								<option value="joindate"'.($search_by === 'joindate' ? ' selected="selected"' : '').'>'.$lang_user['by_join_date'].'</option>
								<option value="last_ip"'.($search_by === 'last_ip' ? ' selected="selected"' : '').'>'.$lang_user['by_ip'].'</option>
								<option value="failed_logins"'.($search_by === 'failed_logins' ? ' selected="selected"' : '').'>'.$lang_user['by_failed_loggins'].'</option>
								<option value="last_login"'.($search_by === 'last_login' ? ' selected="selected"' : '').'>'.$lang_user['by_last_login'].'</option>
								<option value="active_realm_id"'.($search_by === 'active_realm_id' ? ' selected="selected"' : '').'>'.$lang_user['by_online'].'</option>
								<option value="locked"'.($search_by === 'locked' ? ' selected="selected"' : '').'>'.$lang_user['by_locked'].'</option>
								<option value="banned"'.($search_by === 'banned' ? ' selected="selected"' : '').'>'.$lang_user['by_banned'].'</option>
							</select>
						</form>
					</td>
					<td align="left">';
						makebutton($lang_global['search'], 'javascript:do_submit()',80);
$output .= '
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>';

//==========================top tage navigaion ENDS here ========================

$output .= '
<form method="get" action="accounts.php" name="form1">
	<input type="hidden" name="action" value="del_user" />
	<input type="hidden" name="start" value="'.$start.'" />
	<input type="hidden" name="backup_op" value="0"/>
	<table class="lined">
		<tr>
			<td "class="hidden" align="left" width="25%">';
$output .= '
				'.$lang_user['tot_acc'].' : '.$all_record.'
			</td>
			<td "class="hidden" align="right" width="25%">';
$output .=
				generate_pagination('accounts.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'', $all_record, $itemperpage, $start);
$output .= '
			</td>
		</tr>
	</table>
	<table class="lined">
		<tr>';
	// column headers, with links for sorting
	// first column is the selection check box
	if($user_lvl >= $action_permission['insert'])
$output.= '
			<th width="1%">
				<input name="allbox" type="checkbox" value="Check All" onclick="CheckAll(document.form1);" />
			</th>';
	else
$output .= '
			<th width="1%"></th>';
$output .='
			<th width="1%"><a href="accounts.php?order_by=id&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='id' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['id'].'</a></th>
			<th width="20%"><a href="accounts.php?order_by=username&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='username' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['username'].'</a></th>
			<th width="1%"><a href="accounts.php?order_by=gmlevel&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='gmlevel' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['gm_level'].'</a></th>
			<th width="25%"><a href="accounts.php?order_by=email&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='email' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['email'].'</a></th>
			<th width="1%"><a href="accounts.php?order_by=joindate&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='joindate' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['join_date'].'</a></th>
			<th width="1%"><a href="accounts.php?order_by=last_ip&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='last_ip' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['ip'].'</a></th>
			<th width="1%"><a href="accounts.php?order_by=failed_logins&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='failed_logins' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['failed_logins'].'</a></th>
			<th width="1%"><a href="accounts.php?order_by=locked&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='locked' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['locked'].'</a></th>
			<th width="1%"><a href="accounts.php?order_by=last_login&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='last_login' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['last_login'].'</a></th>
			<th width="1%"><a href="accounts.php?order_by=active_realm_id&amp;start='.$start.( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'&amp;dir='.$dir.'"'.($order_by==='active_realm_id' ? ' class="'.$order_dir.'"' : '').'>'.$lang_user['online'].'</a></th>';
	if ($showcountryflag)
	{
require_once 'libs/misc_lib.php';
$output .= '
			<th width="1%">'.$lang_global['country'].'</th>';
	}
$output .= '
		</tr>';

//---------------Page Specific Data Starts Here--------------------------

	while ($data = $sqlr->fetch_assoc($query))
	{
		if (($user_lvl >= $data['gmlevel'])||($user_name === $data['username']))
		{
$output .= '
		<tr>';
			if ($user_lvl >= $action_permission['insert'])
$output .= '
			<td><input type="checkbox" name="check[]" value="'.$data['id'].'" onclick="CheckCheckAll(document.form1);" /></td>';
			else
$output .= '
			<td></td>';
				$exp_lvl_arr = id_get_exp_lvl();
$output .= '
			<td>'.$data['id'].'</td>
			<td>
				<a href="accounts.php?action=edit_user&amp;error=11&amp;id='.$data['id'].'">
					<span onmousemove="toolTip(\''.$exp_lvl_arr[$data['expansion']][2].'\', \'item_tooltip\')" onmouseout="toolTip()">'.htmlentities($data['username']).'</span>
				</a>
			</td>';
unset($exp_lvl_arr);
$output .= '
			<td>'.$gm_level_arr[$data['gmlevel']][2].'</td>';
			if ($user_lvl >= $action_permission['update']||($user_name === $data['username']))
$output .= '
			<td class="small"  align="left"><a href="mailto:'.$data['email'].'">'.substr($data['email'],0,30).'</a></td>';
			else
$output .= '
			<td>***@***.***</td>';
$output .= '
			<td class="small">'.$data['joindate'].'</td>';
			if (($user_lvl >= $action_permission['update'])||($user_name === $data['username']))
$output .= '
			<td class="small"  align="right">'.$data['last_ip'].'</td>';
			else
$output .= '
			<td>*******</td>';
$output .= '
			<td>'.(($data['failed_logins']) ? $data['failed_logins'] : '-').'</td>
			<td>'.(($data['locked']) ? $lang_global['yes_low'] : '-').'</td>
			<td class="small">'.$data['last_login'].'</td>
			<td>'.(($data['active_realm_id']) ? '<img src="img/up.gif" alt="" />' : '-').'</td>';

			if ($showcountryflag)
			{
			$country = misc_get_country_by_ip($data['last_ip'], $sqlm);
$output .= '
			<td>'.(($country['code']) ? '<img src="img/flags/'.$country['code'].'.png" onmousemove="toolTip(\''.($country['country']).'\', \'item_tooltip\')" onmouseout="toolTip()" alt="" />' : '-').'</td>';
			}
$output .= '
		</tr>';
		}
		else
		{
$output .= '
		<tr>
			<td>*</td><td>***</td><td>You</td><td>Have</td><td>No</td>
			<td class=\"small\">Permission</td><td>to</td><td>View</td><td>this</td><td>Data</td><td>***</td>';
			if ($expansion_select)
$output .= '
			<td>*</td>';
			if ($showcountryflag)
$output .= '
			<td>*</td>';
$output .= '
		</tr>';
		}
	}
$output .= '
	</table>
	<table class="lined">
		<tr>
			<th>
			</th>
		</tr>
	</table>
	<table class="lined">
		<tr>
			<td "class="hidden" align="left" width="25%">';
$output .= '
				'.$lang_user['tot_acc'].' : '.$all_record.'
			</td>
			<td "class="hidden" align="right" width="25%">';
$output .=
				generate_pagination('accounts.php?order_by='.$order_by.'&amp;dir='.(($dir) ? 0 : 1).( $search_value && $search_by ? '&amp;search_by='.$search_by.'&amp;search_value='.$search_value.'' : '' ).'', $all_record, $itemperpage, $start);
$output .= '
			</td>
		</tr>
	</table>
	<table class="top_hidden">
		<tr>
			<td>';
	if($user_lvl >= $action_permission['delete'])
				makebutton($lang_user['del_selected_users'], 'javascript:do_submit(\'form1\',0)" type="wrn',230);
	if($user_lvl >= $action_permission['insert'])
				makebutton($lang_user['backup_selected_users'], 'javascript:do_submit(\'form1\',1)',230);
$output .= '
			</td>
		</tr>
	</table>
</form>
<br />
</center>';

}

//#######################################################################################################
//  DELETE USER
//#######################################################################################################
function del_user(&$sqlr)
{
	global 	$lang_global, $lang_user,
			$output,
			$action_permission;

	valid_login($action_permission['delete']);

	if(isset($_GET['check']))
		$check = $_GET['check'];
	else
		redirect("accounts.php?error=1");

  $pass_array = '';

	//skip to backup
	if (isset($_GET['backup_op'])&&($_GET['backup_op'] == 1))
	{
		$n_check = count($check);
		for ($i=0; $i<$n_check; ++$i)
		{
		$pass_array .= '&amp;check%5B%5D='.$check[$i].'';
		}
		redirect('accounts.php?action=backup_user'.$pass_array.'');
	}

$output .= '
<center>
<table class="lined">
	<tr>
		<td>
			<img src="img/warn_red.gif" width="48" height="48" alt="" />
			<h1>
				<font class="error">
					'.$lang_global['are_you_sure'].'
				</font>
			</h1>
			<br />
			<font class="bold">'.$lang_user['acc_ids'].': ';

	$n_check = count($check);
	for ($i=0; $i<$n_check; ++$i)
	{
		$username = $sqlr->result($sqlr->query('
			SELECT username
			FROM account
			WHERE id = '.$check[$i].''), 0);

$output .= '
			<a href="accounts.php?action=edit_user&amp;id='.$check[$i].'" target="_blank">'.$username.', </a>';
		$pass_array .= '&amp;check%5B%5D='.$check[$i].'';
	}
unset($name);
unset($n_check);
unset($check);

$output .= '
				<br />'.$lang_global['will_be_erased'].'
			</font>
			<br /><br />
		</td>
	</tr>
</table>
<br /><br />
<table width="300" class="hidden" align="center">
	<tr>
		<td>';
			makebutton($lang_global['yes'], 'accounts.php?action=dodel_user'.$pass_array.'" type="wrn', 130);
			makebutton($lang_global['no'], 'accounts.php" type="def' ,130);
unset($pass_array);
$output .= '
		</td>
	</tr>
</table>
<br />
</center>';

}

//#####################################################################################################
//  DO DELETE USER
//#####################################################################################################
function dodel_user(&$sqlr, &$sqlc)
{
	global 	$lang_global, $lang_user,
			$output,
			$realm_id,
			$user_lvl,
			$tab_del_user_characters, $tab_del_user_realmd, $action_permission;

	valid_login($action_permission['delete']);

	if(isset($_GET['check']))
		$check = $sqlr->quote_smart($_GET['check']);
	else
		redirect('accounts.php?error=1');

	$deleted_acc = 0;
	$deleted_chars = 0;

require_once 'libs/del_lib.php';

	$n_check = count($check);
	for ($i=0; $i<$n_check; ++$i)
	{
		if ($check[$i] != "" )
		{
			list($flag,$del_char) = del_acc($check[$i]);
			if ($flag)
			{
				$deleted_acc++;
				$deleted_chars += $del_char;
			}
		}
	}
unset($n_check);
unset($check);

$output .= '
<center>
<table class="lined">
	<tr>
		<td>';
	if ($deleted_acc == 0)
$output .= '
			<h1>
				<font class="error">'.$lang_user['no_acc_deleted'].'</font>
			</h1>';
	else
	{
$output .= '
			<h1>
				<font class="error">
					'.$lang_user['total'].'
					<font color=blue>'.$deleted_acc.'</font>
					'.$lang_user['acc_deleted'].'
				</font><br />
			</h1>';
$output .= '
			<h1>
				<font class="error">
					'.$lang_user['total'].'
					<font color=blue>'.$deleted_chars.'</font>
					'.$lang_user['char_deleted'].'</font>
				</h1>';
	}
unset($deleted_acc);
unset($deleted_chars);
$output .= '
			<br /><br />
		</td>
	</tr>
</table>';
$output .= '
<table width="300" class="hidden" align="center">
	<tr>
		<td>';
			makebutton($lang_user['back_browsing'], 'accounts.php', 230);
$output .= '
		</td>
	</tr>
</table>
<br />
</center>';

}

//#####################################################################################################
//  DO BACKUP USER
//#####################################################################################################
function backup_user(&$sqlr, &$sqlc)
{
	global 	$lang_global, $lang_user,
			$output,
			$realm_db, $characters_db,
			$realm_id,
			$user_lvl,
			$backup_dir,
			$action_permission;

	valid_login($action_permission['insert']);

  $sqlr = new SQL;
  $sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);

  if(isset($_GET['check'])) $check = $sqlr->quote_smart($_GET['check']);
    else redirect("accounts.php?error=1");

  require_once("libs/tab_lib.php");

    $subdir = "$backup_dir/accounts/".date("m_d_y_H_i_s")."_partial";
    mkdir($subdir, 0777);

    for ($t=0; $t<count($check); $t++)
    {
    if ($check[$t] != "" )
    {
      $query = $sqlr->query("SELECT id FROM account WHERE id = $check[$t]");
      $acc = $sqlr->fetch_array($query);
      $file_name_new = $acc[0]."_{$realm_db['name']}.sql";
      $fp = fopen("$subdir/$file_name_new", 'w') or die ($lang_backup['file_write_err']);
      fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$realm_db['name']};\n")or die (error($lang_backup['file_write_err']));
      fwrite($fp, "USE {$realm_db['name']};\n\n")or die ($lang_backup['file_write_err']);
      foreach ($tab_backup_user_realmd as $value) {
      $acc_query = $sqlr->query("SELECT * FROM $value[0] WHERE $value[1] = $acc[0]");
      $num_fields = $sqlr->num_fields($acc_query);
      $numrow = $sqlr->num_rows($acc_query);
      $result = "-- Dumping data for $value[0] ".date("m.d.y_H.i.s")."\n";
      $result .= "LOCK TABLES $value[0] WRITE;\n";
      $result .= "DELETE FROM $value[0] WHERE $value[1] = $acc[0];\n";

      if ($numrow)
      {
        $result .= "INSERT INTO $value[0] (";
        for($count = 0; $count < $num_fields; $count++)
        {
          $result .= "`".$sqlr->field_name($acc_query,$count)."`";
          if ($count < ($num_fields-1)) $result .= ",";
        }
        $result .= ") VALUES \n";
        for ($i =0; $i<$numrow; $i++)
        {
          $result .= "\t(";
          $row = $sqlr->fetch_row($acc_query);
          for($j=0; $j<$num_fields; $j++)
          {
            $row[$j] = addslashes($row[$j]);
            $row[$j] = ereg_replace("\n","\\n",$row[$j]);
            if (isset($row[$j]))
            {
              if ($sqlr->field_type($acc_query,$j) == "int")
                $result .= "$row[$j]";
              else
                $result .= "'$row[$j]'" ;
            }
            else
              $result .= "''";
            if ($j<($num_fields-1))
              $result .= ",";
            }
            if ($i < ($numrow-1))
              $result .= "),\n";
          }
          $result .= ");\n";
        }
        $result .= "UNLOCK TABLES;\n";
        $result .= "\n";
        fwrite($fp, $result)or die (error($lang_backup['file_write_err']));
      }
      fclose($fp);

      foreach ($characters_db as $db)
      {
        $file_name_new = $acc[0]."_{$db[$realm_id]['name']}.sql";
        $fp = fopen("$subdir/$file_name_new", 'w') or die (error($lang_backup['file_write_err']));
        fwrite($fp, "CREATE DATABASE /*!32312 IF NOT EXISTS*/ {$db[$realm_id]['name']};\n")or die (error($lang_backup['file_write_err']));
        fwrite($fp, "USE {$db[$realm_id]['name']};\n\n")or die (error($lang_backup['file_write_err']));

        $all_char_query = $sqlc->query("SELECT guid,name FROM `characters` WHERE account = $acc[0]");

        while ($char = $sqlc->fetch_array($all_char_query))
        {
          fwrite($fp, "-- Dumping data for character $char[1]\n")or die (error($lang_backup['file_write_err']));
          foreach ($tab_backup_user_characters as $value)
          {
            $char_query = $sqlc->query("SELECT * FROM $value[0] WHERE $value[1] = $char[0]");
            $num_fields = $sqlc->num_fields($char_query);
            $numrow = $sqlc->num_rows($char_query);
            $result = "LOCK TABLES $value[0] WRITE;\n";
            $result .= "DELETE FROM $value[0] WHERE $value[1] = $char[0];\n";
            if ($numrow)
            {
              $result .= "INSERT INTO $value[0] (";
              for($count = 0; $count < $num_fields; $count++)
              {
                $result .= "`".$sqlc->field_name($char_query,$count)."`";
                if ($count < ($num_fields-1)) $result .= ",";
              }
              $result .= ") VALUES \n";
              for ($i =0; $i<$numrow; $i++)
              {
                $result .= "\t(";
                $row = $sqlc->fetch_row($char_query);
                for($j=0; $j<$num_fields; $j++)
                {
                  $row[$j] = addslashes($row[$j]);
                  $row[$j] = ereg_replace("\n","\\n",$row[$j]);
                  if (isset($row[$j]))
                  {
                    if ($sqlc->field_type($char_query,$j) == "int")
                      $result .= "$row[$j]";
                    else
                      $result .= "'$row[$j]'" ;
                  }
                  else
                    $result .= "''";
                  if ($j<($num_fields-1))
                    $result .= ",";
                }
                if ($i < ($numrow-1))
                  $result .= "),\n";
              }
              $result .= ");\n";
            }
            $result .= "UNLOCK TABLES;\n";
            $result .= "\n";
            fwrite($fp, $result)or die (error($lang_backup['file_write_err']));
          }
        }
        fclose($fp);
      }
    }
  }
  redirect("accounts.php?error=15");
}


//#######################################################################################################
//  ADD NEW USER
//#######################################################################################################
function add_new()
{
	global 	$lang_global, $lang_user,
			$output,
			$action_permission,
			$expansion_select;

valid_login($action_permission['insert']);

  $output .= "
        <center>
          <script type=\"text/javascript\" src=\"libs/js/sha1.js\"></script>
          <script type=\"text/javascript\">
            // <![CDATA[
              function do_submit_data ()
              {
                if (document.form.new_pass1.value != document.form.new_pass2.value)
                {
                  alert('{$lang_user['nonidentical_passes']}');
                  return;
                }
                else
                {
                  document.form.pass.value = hex_sha1(document.form.new_user.value.toUpperCase()+':'+document.form.new_pass1.value.toUpperCase());
                  document.form.new_pass1.value = '0';
                  document.form.new_pass2.value = '0';
                  do_submit();
                }
              }
            // ]]>
          </script>
          <fieldset style=\"width: 550px;\">
            <legend>{$lang_user['create_new_acc']}</legend>
            <form method=\"get\" action=\"accounts.php\" name=\"form\">
              <input type=\"hidden\" name=\"pass\" value=\"\" maxlength=\"256\" />
              <input type=\"hidden\" name=\"action\" value=\"doadd_new\" />
              <table class=\"flat\">
                <tr>
                  <td>{$lang_user['username']}</td>
                  <td><input type=\"text\" name=\"new_user\" size=\"24\" maxlength=\"15\" value=\"New_Account\" /></td>
                </tr>
                <tr>
                  <td>{$lang_user['password']}</td>
                  <td><input type=\"text\" name=\"new_pass1\" size=\"24\" maxlength=\"25\" value=\"123456\" /></td>
                </tr>
                <tr>
                  <td>{$lang_user['confirm']}</td>
                  <td><input type=\"text\" name=\"new_pass2\" size=\"24\" maxlength=\"25\" value=\"123456\" /></td>
                </tr>
                <tr>
                  <td>{$lang_user['email']}</td>
                  <td><input type=\"text\" name=\"new_mail\" size=\"24\" maxlength=\"225\" value=\"none@mail.com\" /></td>
                </tr>
                <tr>
                  <td>{$lang_user['locked']}</td>
                  <td><input type=\"checkbox\" name=\"new_locked\" value=\"1\" /></td>
                </tr>";
  if ( $expansion_select )
    $output .= "
                <tr>
                  <td>{$lang_user['expansion_account']}</td>
                  <td>
                    <select name=\"new_expansion\">
                      <option value=\"2\">{$lang_user['wotlk']}</option>
                      <option value=\"1\">{$lang_user['tbc']}</option>
                      <option value=\"0\">{$lang_user['classic']}</option>
                    </select>
                  </td>
                </tr>";
  $output .="
                <tr>
                  <td>";
                    makebutton($lang_user['create_acc'], "javascript:do_submit_data()\" type=\"wrn",130);
  $output .= "
                  </td>
                  <td>";
                    makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);
  $output .= "
                  </td>
                </tr>
              </table>
            </form>
          </fieldset>
          <br /><br />
        </center>
";
}


//#########################################################################################################
// DO ADD NEW USER
//#########################################################################################################
function doadd_new(&$sqlr)
{
	global 	$lang_global,
			$action_permission;

	valid_login($action_permission['insert']);

	if ( empty($_GET['new_user']) || empty($_GET['pass']) )
		redirect("accounts.php?action=add_new&error=4");

  $new_user = $sqlr->quote_smart(trim($_GET['new_user']));
  $pass = $sqlr->quote_smart($_GET['pass']);

	//make sure username/pass at least 4 chars long and less than max
	if ((strlen($new_user) < 4) || (strlen($new_user) > 15))
		redirect("accounts.php?action=add_new&error=8");

require_once 'libs/valid_lib.php';

	//make sure it doesnt contain non english chars.
	if (!valid_alphabetic($new_user))
		redirect("accounts.php?action=add_new&error=9");

	$result = $sqlr->query("
		SELECT username
		FROM account
		WHERE username = '$new_user'");

	//there is already someone with same username
	if ($sqlr->num_rows($result))
		redirect("accounts.php?action=add_new&error=7");
	else
		$last_ip = "0.0.0.0";
		$new_mail = (isset($_GET['new_mail'])) ? $sqlr->quote_smart(trim($_GET['new_mail'])) : NULL;
		$locked = (isset($_GET['new_locked'])) ? $sqlr->quote_smart($_GET['new_locked']) : 0;
		$expansion = (isset($_GET['new_expansion'])) ? $sqlr->quote_smart($_GET['new_expansion']) : 0;
		$result = $sqlr->query("
			INSERT INTO account
				(username,sha_pass_hash,gmlevel,email, joindate,last_ip,failed_logins,locked,last_login,active_realm_id,expansion)
			VALUES
				('$new_user','$pass',0 ,'$new_mail',now() ,'$last_ip',0, $locked ,NULL, 0, $expansion)");
  if ($result)
    redirect("accounts.php?error=5");

}

//###########################################################################################################
//  EDIT USER
//###########################################################################################################
function edit_user(&$sqlr, &$sqlc)
{
	global	$lang_global, $lang_user, $output,
			$realm_id,
			$mmfpm_db, $characters_db,
			$user_lvl, $user_name,
			$gm_level_arr, $action_permission, $expansion_select, $developer_test_mode, $multi_realm_mode, $server;

	valid_login($action_permission['update']);
	  
	$active_realm_id_pq = "active_realm_id";


	if (empty($_GET['id'])) redirect("accounts.php?error=10");


$sqlm = new SQL;
$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	$id = $sqlr->quote_smart($_GET['id']);

	$result = $sqlr->query("
		SELECT id, username, gmlevel, email, joindate, last_ip, failed_logins, locked, last_login, {$active_realm_id_pq}, expansion
		FROM account
		WHERE id = '$id'");

	$data = $sqlr->fetch_assoc($result);

	$refguid = $sqlm->fetch_assoc($sqlm->query('
		SELECT InviterAccount
		FROM mm_point_system_invites
		WHERE PlayersAccount = '.$data['id'].''));

	$refguid = $refguid['InviterAccount'];
	$referred_by = $sqlr->fetch_assoc($sqlr->query("
		SELECT username
		FROM account
		WHERE id = '$refguid'"));

unset($refguid);
	$referred_by = $referred_by['username'];

	if ($sqlr->num_rows($result))
	{
$output .= '
<center>
<script type="text/javascript" src="libs/js/sha1.js"></script>
<script type="text/javascript">
	// <![CDATA[
		function do_submit_data ()
		{
			if ((document.form.username.value != "'.$data['username'].'") && (document.form.new_pass.value == "******"))
			{
				alert("If you are changing Username, The password must be changed too.");
				return;
			}
			else
			{
				document.form.pass.value = hex_sha1(document.form.username.value.toUpperCase()+":"+document.form.new_pass.value.toUpperCase());
				document.form.new_pass.value = "0";
				do_submit();
			}
		}
	// ]]>
</script>
<fieldset style="width: 550px;">
	<legend>'.$lang_user['edit_acc'].'</legend>
	<form method="post" action="accounts.php?action=doedit_user" name="form">
	<input type="hidden" name="pass" value="" maxlength="256" />
	<input type="hidden" name="id" value="'.$id.'" />
	<table class="flat">
		<tr>
			<td>'.$lang_user['id'].'</td>
			<td>'.$data['id'].'</td>
		</tr>
		<tr>
			<td>'.$lang_user['username'].'</td>';
		if($user_lvl >= $action_permission['update'])
$output .='
			<td><input type="text" name="username" size="42" maxlength="15" value="'.$data['username'].'" /></td>';
		else
$output.='
			<td>'.$data['username'].'</td>';
$output .= '
		</tr>
		<tr>
			<td>'.$lang_user['password'].'</td>';
		if($user_lvl >= $action_permission['update'])
$output .="
			<td><input type=\"text\" name=\"new_pass\" size=\"42\" maxlength=\"40\" value=\"******\" /></td>";
		else
$output.="
			<td>********</td>";
$output .= "
		</tr>
		<tr>
			<td>{$lang_user['email']}</td>";
		if($user_lvl >= $action_permission['update'])
$output .= '
			<td><input type="text" name="mail" size="42" maxlength="225" value="'.$data['email'].'" /></td>';
		else
$output.="
			<td>***@***.***</td>";
$output .= "
		</tr>
		<tr>
			<td>{$lang_user['invited_by']}:</td>
			<td>";
	if($user_lvl >= $action_permission['update'] && !$referred_by !=NULL)
$output .="
				<input type=\"text\" name=\"referredby\" size=\"42\" maxlength=\"12\" value=\"$referred_by\" />";
	else
$output .="
				$referred_by";
$output .="
			</td>
		</tr>
		<tr>
			<td>{$lang_user['gm_level_long']}</td>";
	if($user_lvl >= $action_permission['update'])
	{
$output .="
			<td>
				<select name=\"gmlevel\">";
		foreach ($gm_level_arr as $level)
		{
			if (($level[0] > -1) && ($level[0] < $user_lvl))
			{
$output .= "
					<option value=\"{$level[0]}\" ";
				if ($data['gmlevel'] == $level[0])
$output .=
						"selected=\"selected\" ";
$output .=
					">{$level[1]}</option>";
			}
		}
$output .= "
				</select>
			</td>";
	}
	else
$output .= '
			<td>'.id_get_gm_level($data['gmlevel']).' ( '.$data['gmlevel'].' )</td>';
  $output .= '
              </tr>
              <tr>
                <td>'.$lang_user['join_date'].'</td>
                <td>'.$data['joindate'].'</td>
              </tr>
              <tr>
                <td>'.$lang_user['last_ip'].'</td>';
  if($user_lvl >= $action_permission['update'])
    $output .= '
                <td>'.$data['last_ip'].'<a href="banned.php?action=do_add_entry&amp;entry='.$data['last_ip'].'&amp;bantime=3600&amp;ban_type=ip_banned"> &lt;- '.$lang_user['ban_this_ip'].'</a></td>';
  else
    $output .= "
                <td>***.***.***.***</td>";
  $output .= "
              </tr>
              <tr>
                <td>{$lang_user['banned']}</td>";
  $que = $sqlr->query("SELECT bandate, unbandate, bannedby, banreason FROM account_banned WHERE id = $id");
  if ($sqlr->num_rows($que))
  {
    $banned = $sqlr->fetch_row($que);
    $ban_info = " From:".date('d-m-Y G:i', $banned[0])." till:".date('d-m-Y G:i', $banned[1])."<br />by $banned[2]";
    $ban_checked = " checked=\"checked\"";
  }
  else
  {
    $ban_checked = "";
    $ban_info    = "";
    $banned[3]   = "";
  }
  if($user_lvl >= $action_permission['update'])
    $output .= "
                <td><input type=\"checkbox\" name=\"banned\" value=\"1\" $ban_checked/>$ban_info</td>";
  else
    $output .= "
                <td>$ban_info</td>";
  $output .="
              </tr>
              <tr>
                <td>{$lang_user['banned_reason']}</td>";
  if($user_lvl >= $action_permission['update'])
    $output .="
                <td><input type=\"text\" name=\"banreason\" size=\"42\" maxlength=\"255\" value=\"$banned[3]\" /></td>";
  else
    $output .= "
                <td>$banned[3]</td>";
  if ($expansion_select)
  {
    $output .="
              </tr>
              <tr>";
    if($user_lvl >= $action_permission['update'])
    {
      $output .="
                <td>{$lang_user['client_type']}</td>";
      $output .="
                <td>
                  <select name=\"expansion\">";
      $output .= "
                    <option value=\"0\">{$lang_user['classic']}</option>
                    <option value=\"1\" ";
      if ($data['expansion'] == 1)
        $output .= "selected=\"selected\" ";
      $output .= ">{$lang_user['tbc']}</option>
                   <option value=\"2\" ";
      if ($data['expansion'] ==2)
        $output .= "selected=\"selected\" ";
      $output .= ">{$lang_user['wotlk']}</option>
                  </select>
                </td>";
    }
    else
      $output .= "
                <td>{$lang_user['classic']}</td>";
  }
  $output .="
              </tr>
              <tr>
                <td>{$lang_user['failed_logins_long']}</td>";
  if($user_lvl >= $action_permission['update'])
    $output .='
                <td><input type="text" name="failed" size="42" maxlength="3" value="'.$data['failed_logins'].'" /></td>';
  else
    $output .= '
                <td>'.$data['failed_logins'].'</td>';
  $output .="
              </tr>
              <tr>
                <td>{$lang_user['locked']}</td>";
  $lock_checked = ($data['locked']) ? " checked=\"checked\"" : "";
  if($user_lvl >= $action_permission['update'])
    $output .= "
                <td><input type=\"checkbox\" name=\"locked\" value=\"1\" $lock_checked/></td>";
  else
    $output .="
                <td></td>";
  $output.= '
              </tr>
              <tr>
                <td>'.$lang_user['last_login'].'</td>
                <td>'.$data['last_login'].'</td>
              </tr>
              <tr>
                <td>'.$lang_user['online'].'</td>';
  $output .= "
                <td>".(( $data['active_realm_id'] ) ? $lang_global['yes'] : $lang_global['no'])."</td>
              </tr>";
  $query = $sqlr->query("SELECT SUM(numchars) FROM realmcharacters WHERE acctid = '$id'");
  $tot_chars = $sqlr->result($query, 0);
  $query = $sqlc->query("SELECT count(*) FROM `characters` WHERE account = $id");
  $chars_on_realm = $sqlc->result($query, 0);
  $output .= "
              <tr>
                <td>{$lang_user['tot_chars']}</td>
                <td>$tot_chars</td>
              </tr>";
  $realms = $sqlr->query("SELECT id, name FROM realmlist");
 
$sqlc = new SQL;
$sqlc->connect($characters_db[$realm_id]['addr'], $characters_db[$realm_id]['user'], $characters_db[$realm_id]['pass'], $characters_db[$realm_id]['name']);

  if ($developer_test_mode && $multi_realm_mode && ($sqlr->num_rows($realms) > 1 && (count($server) > 1) && (count($characters_db) > 1)))
  {
    require_once("libs/get_lib.php");
    while ($realm = $sqlr->fetch_array($realms))
    {
      $sqlc->connect($characters_db[$realm[0]]['addr'], $characters_db[$realm[0]]['user'], $characters_db[$realm[0]]['pass'], $characters_db[$realm[0]]['name']);
      $query = $sqlc->query("SELECT count(*) FROM `characters` WHERE account = $id");
      $chars_on_realm = $sqlc->result($query, 0);
      $output .= "
              <tr>
                <td>{$lang_user['chars_on_realm']} ".get_realm_name($realm[0])."</td>
                <td>$chars_on_realm</td>
              </tr>";
      if ($chars_on_realm)
      {
        $char_array = $sqlc->query("SELECT guid, name, race, class, level, gender
          FROM `characters` WHERE account = $id");
        while ($char = $sqlc->fetch_array($char_array))
        {
          $output .= "
              <tr>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'---></td>
                <td>
                      <a href=\"char.php?id=$char[0]&amp;realm=$realm[0]\">$char[1]  - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />
                      <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\"/> - lvl ".char_get_level_color($char[4])."</a>
                </td>
              </tr>";
        }
      }
    }
  }
  else
  {
    $query = $sqlc->query("SELECT count(*) FROM `characters` WHERE account = $id");
    $chars_on_realm = $sqlc->result($query, 0);
    $output .= "
              <tr>
                <td>{$lang_user['chars_on_realm']}</td>
                <td>$chars_on_realm</td>
              </tr>";
    if ($chars_on_realm)
    {
      $char_array = $sqlc->query("SELECT guid,name,race,class, level, gender FROM `characters` WHERE account = $id");
      while ($char = $sqlc->fetch_array($char_array))
      {
        $output .= "
                <tr>
                  <td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'---></td>
                  <td>
                    <a href=\"char.php?id=$char[0]\">$char[1]  - <img src='img/c_icons/{$char[2]}-{$char[5]}.gif' onmousemove='toolTip(\"".char_get_race_name($char[2])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\" />
                    <img src='img/c_icons/{$char[3]}.gif' onmousemove='toolTip(\"".char_get_class_name($char[3])."\",\"item_tooltip\")' onmouseout='toolTip()' alt=\"\"/> - lvl ".char_get_level_color($char[4])."</a>
                  </td>
                </tr>";
      }
    }
  }
  $output .= "
              <tr>
                <td>";
  if($user_lvl >= $action_permission['delete'])
                  makebutton($lang_user['del_acc'], "accounts.php?action=del_user&amp;check%5B%5D=$id\" type=\"wrn",130);
  $output .= "
                </td>
                <td>";
  if($user_lvl >= $action_permission['update'])
                  makebutton($lang_user['update_data'], "javascript:do_submit_data()",130);
                  makebutton($lang_global['back'], "javascript:window.history.back()\" type=\"def",130);
  $output .= "
                </td>
                </tr>
              </table>
            </form>
          </fieldset>
          <br /><br />
        </center>
";

  }
  else error($lang_global['err_no_user']);

}

//############################################################################################################
//  DO   EDIT   USER
//############################################################################################################
function doedit_user(&$sqlr)
{
	global 	$lang_global,
			$realm_db, $mmfpm_db,
			$user_lvl, $user_name,
			$action_permission;

	valid_login($action_permission['update']);

	if 
	( 
		(!isset($_POST['pass'])||($_POST['pass'] === ''))
		&& (!isset($_POST['mail'])||($_POST['mail'] === ''))
		&& (!isset($_POST['expansion'])||($_POST['expansion'] === ''))
		&& (!isset($_POST['referredby'])||($_POST['referredby'] === ''))
	)
		redirect("accounts.php?action=edit_user&&id={$_POST['id']}&error=1");

	$id = $sqlr->quote_smart($_POST['id']);
	$username = $sqlr->quote_smart($_POST['username']);
	$banreason = $sqlr->quote_smart($_POST['banreason']);
	$pass = $sqlr->quote_smart($_POST['pass']);
	$user_pass_change = ($pass != sha1(strtoupper($username).":******")) ? "username='$username',sha_pass_hash='$pass'," : "";

	$mail = (isset($_POST['mail']) && $_POST['mail'] != '') ? $sqlr->quote_smart($_POST['mail']) : "";
	$failed = (isset($_POST['failed'])) ? $sqlr->quote_smart($_POST['failed']) : 0;
	$gmlevel = (isset($_POST['gmlevel'])) ? $sqlr->quote_smart($_POST['gmlevel']) : 0;
	$expansion = (isset($_POST['expansion'])) ? $sqlr->quote_smart($_POST['expansion']) : 1;
	$banned = (isset($_POST['banned'])) ? $sqlr->quote_smart($_POST['banned']) : 0;
	$locked = (isset($_POST['locked'])) ? $sqlr->quote_smart($_POST['locked']) : 0;
	$referred = $sqlr->quote_smart(trim($_POST['referredby']));
	$referredby = strtoupper($referred);

	//make sure username/pass at least 4 chars long and less than max
	if ((strlen($username) < 4) || (strlen($username) > 15))
		redirect("accounts.php?action=edit_user&id=$id&error=8");

	if ($gmlevel >= $user_lvl)
		redirect("accounts.php?action=edit_user&&id={$_POST['id']}&error=16");

require_once 'libs/valid_lib.php';

	if (!valid_alphabetic($username))
		redirect("accounts.php?action=edit_user&error=9&id=$id");
	//restricting accsess to lower gmlvl
	$result = $sqlr->query("
		SELECT gmlevel,username
		FROM account
		WHERE id = '$id'");
	if (($user_lvl <= $sqlr->result($result, 0, 'gmlevel')) && ($user_name != $sqlr->result($result, 0, 'username')))
		redirect("accounts.php?error=14");
	if (!$banned)
		$sqlr->query("
			DELETE FROM account_banned
			WHERE id='$id'");
	else
	{
		$result = $sqlr->query("
			SELECT count(*)
			FROM account_banned
			WHERE id = '$id'");
		if(!$sqlr->result($result, 0))
			$sqlr->query("
				INSERT INTO account_banned
					(id, bandate, unbandate, bannedby, banreason, active)
				VALUES
					($id, ".time().",".(time()+(365*24*3600)).",'$user_name','$banreason', 1)");
	}
	$sqlr->query("
		UPDATE account
		SET email='$mail', $user_pass_change gmlevel='$gmlevel', v=0,s=0,failed_logins='$failed',locked='$locked',expansion='$expansion'
		WHERE id='$id'");
	if (doupdate_referral($referredby, $id) || $sqlr->affected_rows())
		redirect("accounts.php?action=edit_user&error=13&id=$id");
	else
		redirect("accounts.php?action=edit_user&error=12&id=$id");
}

function doupdate_referral($referredby, $user_id)
{
	global 	$realm_db, $mmfpm_db;

	$sqlr = new SQL;
	$sqlr->connect($realm_db['addr'], $realm_db['user'], $realm_db['pass'], $realm_db['name']);
	$sqlm = new SQL;
	$sqlm->connect($mmfpm_db['addr'], $mmfpm_db['user'], $mmfpm_db['pass'], $mmfpm_db['name']);

	if (NULL == $sqlm->result($sqlm->query('
		SELECT InviterAccount 
		FROM mm_point_system_invites 
		WHERE PlayersAccount = \''.$user_id.'\''), 'InviterAccount'))
	{
		$referred_by = $sqlr->result($sqlr->query('
			SELECT id 
			FROM account 
			WHERE username = \''.$referredby.'\''), 'id');

		if ($referred_by == NULL);
		else
		{
			if ($referred_by == $user_id);
			else
			{
				$sqlm->query('
					INSERT INTO mm_point_system_invites
						(PlayersAccount, InviterAccount) 
					VALUES
						(\''.$user_id.'\', \''.$referred_by.'\')');
				return true;
			}
		}
	}
	return false;
}


//########################################################################################################################
// MAIN
//########################################################################################################################

// load language
$lang_user = lang_user();

// $_GET and SECURE
$err = (isset($_GET['error'])) ? $_GET['error'] : NULL;

$output .= '
<div class="top">';

// defines the title header in error cases
if(1 ==  $err)
$output .= '
	<h1><font class="error\">'.$lang_global['empty_fields'].'</font></h1>';
else if(2 == $err)
$output .= '
	<h1><font class="error\">'.$lang_global['err_no_search_passed'].'</font></h1>';
else if(3 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['search_results'].'</font></h1>';
else if(4 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['acc_creation_failed'].'</font></h1>';
else if(5 == $err)
$output .= '
	<h1>'.$lang_user['acc_created'].'</h1>';
else if(6 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['nonidentical_passes'].'</font></h1>';
else if(7 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['user_already_exist'].'</font></h1>';
else if(8 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['username_pass_too_long'].'</font></h1>';
else if(9 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['use_only_eng_charset'].'</font></h1>';
else if(10 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['no_value_passed'].'</font></h1>';
else if(11 == $err)
$output .= '
	<h1>'.$lang_user['edit_acc'].'</h1>';
else if(12 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['update_failed'].'</font></h1>';
else if(13 == $err)
$output .= '
	<h1>'.$lang_user['data_updated'].'</h1>';
else if(14 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['you_have_no_permission'].'</font></h1>';
else if(15 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['acc_backedup'].'</font></h1>';
else if(16 == $err)
$output .= '
	<h1><font class="error\">'.$lang_user['you_have_no_permission_to_set_gmlvl'].'</font></h1>';
else
$output .= '
	<h1>'.$lang_user['browse_acc'].'</h1>';

$output .= '
</div>';

// $_GET and SECURE
$action = (isset($_GET['action'])) ? $_GET['action'] : NULL;

// define functions to be called by actions
if ('add_new' == $action)
	add_new();
else if('doadd_new' == $action)
	doadd_new($sqlr);
else if('edit_user' == $action)
	edit_user($sqlr, $sqlc);
else if('doedit_user' == $action)
	doedit_user($sqlr);
else if('del_user' == $action)
	del_user($sqlr);
else if('dodel_user' == $action)
	dodel_user($sqlr, $sqlc);
else if('backup_user' == $action)
	backup_user($sqlr, $sqlc);
else
	browse_users($sqlr);

// close whats not needed anymore
unset($err);
unset($action);
unset($action_permission);
unset($lang_user);

// page footer
require_once("footer.php");

?>
