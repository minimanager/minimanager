<?php


if (!function_exists('sqlite_open'))
	die('This PHP environment doesn\'t have SQLite support built in.');

class SQL //MySQL Lite
{
	var $link_id;
	var $query_result;
	var $in_transaction = 0;
	var $num_queries = 0;
	var $sqlite_error = 0;
	
	function connect($db_host, $db_username, $db_password, $db_name, $use_names = '', $pconnect = true, $newlink = false) {
		global $lang_global;

		if (!file_exists($db_name)){
			@touch($db_name);
			@chmod($db_name, 0666);
			if (!file_exists($db_name)) error($lang_global['err_sql_conn_db']);
		}

		if ((!is_readable($db_name))||(!is_writable($db_name))) error($lang_global['err_sql_conn_db']);

		if ($pconnect) $this->link_id = @sqlite_popen($db_name, 0666, $this->sqlite_error);
		else $this->link_id = @sqlite_open($db_name, 0666, $this->sqlite_error);
	
		if ($this->link_id) return $this->link_id;
			else die(error($lang_global['err_sql_conn_db']));
	}

	function db($db_name) {
		$this->connect(NULL,NULL,NULL, $db_name, NULL);
	}

	function query($sql){
		$this->query_result = @sqlite_query($this->link_id, $sql);

		if ($this->query_result){
			++$this->num_queries;
			return $this->query_result;
		} else {
			if ($this->in_transaction) @sqlite_query($this->link_id, 'ROLLBACK');
			--$this->in_transaction;
			
			die(error(@sqlite_error_string(sqlite_last_error($this->link_id))));
			return false;
		}
	}

	function result($query_id = 0, $row = 0, $field = NULL){
		if ($query_id){
			if ($row != 0) @sqlite_seek($query_id, $row);
			return @current(@sqlite_current($query_id));
		} else return false;
	}

	function fetch_row($query_id = 0){
		return ($query_id) ? @sqlite_fetch_array($query_id, SQLITE_NUM) : false;
	}
	
	function fetch_array($query_id = 0){
		return ($query_id) ? @sqlite_fetch_array($query_id, SQLITE_ASSOC) : false;
	}
	
	function fetch_assoc($query_id = 0){
		if ($query_id){
			$cur_row = @sqlite_fetch_array($query_id, SQLITE_ASSOC);
			if ($cur_row){
				while (list($key, $value) = @each($cur_row)){
				    $dot_spot = strpos($key, '.');
				    if ($dot_spot !== false){
				        unset($cur_row[$key]);
				        $key = substr($key, $dot_spot+1);
				        $cur_row[$key] = $value;
				    }
				}
			}
			return $cur_row;
		}
		else return false;
	}

	function num_rows($query_id = 0){
		return ($query_id) ? @sqlite_num_rows($query_id) : false;
	}

	function num_fields($query_id = 0){
		return ($query_id) ? @sqlite_num_fields($query_id) : false;
	}
	
	function affected_rows(){
		return ($this->query_result) ? @sqlite_changes($this->query_result) : false;
	}

	function insert_id(){
		return ($this->link_id) ? @sqlite_last_insert_rowid($this->link_id) : false;
	}

	function get_num_queries(){
		return $this->num_queries;
	}

	function free_result($query_id = false){
		return true;
	}

	function field_type($query_id = 0,$field_offset){
		return false;
	}

	function field_name($query_id = 0,$field_offset){
		return ($query_id) ? @sqlite_field_name($query_id,$field_offset) : false;
	}
	
	function quote_smart($value){
	if( is_array($value) ) {
		return array_map( array('SQL','quote_smart') , $value);
	} else {
		if( get_magic_quotes_gpc() ) $value = stripslashes($value);
		if( $value === '' ) $value = NULL;
		return sqlite_escape_string($value);
		}
	}

	function error(){
		return @sqlite_error_string(sqlite_last_error($this->link_id));
	}

	function close(){
		if ($this->link_id){
			if ($this->in_transaction){ @sqlite_query($this->link_id, 'COMMIT');
			}
			return @sqlite_close($this->link_id);
		}
		else return false;
	}
	
	function start_transaction(){
		++$this->in_transaction;
		return (@sqlite_query($this->link_id, 'BEGIN')) ? true : false;
	}

	function end_transaction(){
		--$this->in_transaction;

		if (@sqlite_query($this->link_id, 'COMMIT')) return true;
		else {
			@sqlite_query($this->link_id, 'ROLLBACK');
			return false;
		}
	}

}
?>