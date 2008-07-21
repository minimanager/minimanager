<?php
/*
 * Project Name: MiniManager for Mangos Server
 * Date: 17.10.2006 inital version (0.0.1a)
 * Author: Q.SA
 * Copyright: Q.SA
 * Email: *****
 * License: GNU General Public License v2(GPL)
 */

if (!function_exists('pg_connect'))
	die('This PHP environment doesn\'t have PostgreSQL support built in.');

class SQL //PgSQL
{
	var $link_id;
	var $query_result;
	var $num_queries = 0;
	//var $last_query_text = array();
	var $in_transaction = 0;

	function connect($db_host, $db_username, $db_password, $db_name, $use_names = '', $pconnect = true, $newlink = false) {
		global $lang_global;
		if ($db_host != ''){
			if (strpos($db_host, ':') !== false){
				list($db_host, $dbport) = explode(':', $db_host);
				$connect_str = 'host='.$db_host.' port='.$dbport;
			} else {
				if ($db_host != 'localhost') $connect_str = 'host='.$db_host;
			}
		}
		if ($db_name) $connect_str .= 'dbname='.$db_name;
		if ($db_username != '') $connect_str .= 'user='.$db_username;
		if ($db_password != '') $connect_str .= 'password='.$db_password;
		
		if ($pconnect) $this->link_id = @pg_pconnect($connect_str,($newlink?PGSQL_CONNECT_FORCE_NEW:NULL));
		else $this->link_id = @pg_connect($connect_str,($newlink?PGSQL_CONNECT_FORCE_NEW:NULL));

		if (!$this->link_id) error($lang_global['err_sql_conn_db']);
			else {
				if (!empty($use_names)) pg_set_client_encoding($this->link_id, $use_names);
				return $this->link_id;
				}
	}

	function db($db_name) {
		global $lang_global;
		if ($this->link_id){
			if ($this->query("USE $db_name;")) return $this->link_id;
			else die(error($lang_global['err_sql_open_db']." ('$db_name')"));
		} else die(error($lang_global['err_sql_conn_db']));
	}

	function query($sql){
		@pg_send_query($this->link_id, $sql);
		$this->query_result = @pg_get_result($this->link_id);

		if (pg_result_status($this->query_result) != PGSQL_FATAL_ERROR){
			++$this->num_queries;
			//$this->last_query_text[$this->query_result] = $sql;
			return $this->query_result;
		} else {
			if ($this->in_transaction) @pg_query($this->link_id, 'ROLLBACK');
			--$this->in_transaction;
			
			die(error(pg_result_error($this->query_result)));
			return false;
		}
	}

	function result($query_id = 0, $row = 0, $field = NULL){
		return ($query_id) ? @pg_fetch_result($query_id, $row, $field) : false;
	}
	
	function fetch_row($query_id = 0){
		return ($query_id) ? @pg_fetch_row($query_id) : false;
	}

	function fetch_assoc($query_id = 0){
		return ($query_id) ? @pg_fetch_assoc($query_id) : false;
	}

	function num_rows($query_id = 0){
		return ($query_id) ? @pg_num_rows($query_id) : false;
	}
	/* right now its not in use. 
	function insert_id(){
		$query_id = $this->query_result;
		if ($query_id && $this->last_query_text[$query_id] != ''){
			if (preg_match('/^INSERT INTO ([a-z0-9\_\-]+)/is', $this->last_query_text[$query_id], $table_name)){
				if (substr($table_name[1], -6) == 'groups')
					$table_name[1] .= '_g';
				$temp_q_id = @pg_query($this->link_id, 'SELECT currval(\''.$table_name[1].'_id_seq\')');
				return ($temp_q_id) ? intval(@pg_fetch_result($temp_q_id, 0)) : false;
			}
		}
		return false;
	}
	*/
	function fetch_array($query_id = 0){
		return ($query_id) ? @pg_fetch_array($query_id) : false;
	}

	function get_num_queries(){
		return $this->num_queries;
	}
	
	function affected_rows(){
		return ($this->query_result) ? @pg_affected_rows($this->query_result) : false;
	}
	
	function free_result($query_id = false){
		if (!$query_id) $query_id = $this->query_result;
		return ($query_id) ? @pg_free_result($query_id) : false;
	}

	function num_fields($query_id = 0){
		return ($query_id) ? @pg_num_fields($query_id) : false;
	}

	function field_type($query_id = 0,$field_offset){
		return ($query_id) ? @pg_field_type($query_id,$field_offset) : false;
	}

	function field_name($query_id = 0,$field_offset){
		return ($query_id) ? @pg_field_name($query_id,$field_offset) : false;
	}
	
	function quote_smart($value){
		if( is_array($value) ) {
			return array_map( array('SQL','quote_smart') , $value);
			} else {
				if( get_magic_quotes_gpc() ) $value = stripslashes($value);
				if( $value === '' ) $value = NULL;
				return pg_escape_string($value);
			}
	}
	
	function error(){
		return pg_last_error($this->link_id);
	}
	
	function close(){
		global $tot_queries;
		$tot_queries += $this->num_queries;
		if ($this->link_id){
			if ($this->in_transaction){
				@pg_query($this->link_id, 'COMMIT');
			}

			if ($this->query_result) @pg_free_result($this->query_result);
			return @pg_close($this->link_id);
		}
		else return false;
	}

	function start_transaction(){
		++$this->in_transaction;
		return (@pg_query($this->link_id, 'BEGIN')) ? true : false;
	}

	function end_transaction(){
		--$this->in_transaction;

		if (@pg_query($this->link_id, 'COMMIT'))
			return true;
		else{
			@pg_query($this->link_id, 'ROLLBACK');
			return false;
		}
	}

}
?>