<?php
/*
	mysql db operation
	@author tiger <ji.xiaod@gmail.com>
*/
class db
{
	static $table = ''; // default table name
	static $last_sql = ''; // last query sql
	static $querys = array(); // query history
	static $auto_free = false; // switch of auto free result 
	static $ignore_insert = false; // 设置是否为忽略插入
	static $last_query_id = false; // query id
	static $prefix = ''; // 表名前缀
	static $params = array(); // db params
	static $debug = false;
	//var $last_query_connect = null;
	static $state = array();

	public function __construct(){}
	public static function vendor_init($params)
	{
        self::$params = $params;
		self::$prefix = $params['table_pre'];
	}

	public static function get_prefix()
	{
		return self::$params['table_pre'];
	}

	public static function get_table($table_name='', $tb_prefix='') 
	{
		if (empty($tb_prefix)) {
			$tb_prefix = self::get_prefix();
		}
		$table_name = trim($table_name);
		$table_name = $table_name == ''? self::$table : $table_name;
		return $tb_prefix . $table_name;
	}

	public static function get($id, $table='', $id_name = 'id') 
    {
        $table = self::get_table($table);
		if (!$table) {
			return false;
		}

		$sql = 'SELECT * FROM ' . $table . ' WHERE `' . self::escape($id_name) . '` =\'' . self::escape($id) . '\'';

		return self::fetch_one($sql);
		
	}
	
	public static function delete($id, $table = '', $id_name = 'id') 
	{
		if (!$table = self::get_table($table)) {
			return false;
		}
		if (!is_array($id)) {
			$id = explode(',', $id);
		}
		
		$ids_string = '';
		
		foreach($id as $k => $v){
			$ids_string .= ("'". self::escape(trim($v)). "',");
		}
		$ids_string = substr($ids_string, 0, -1);
		if(empty($ids_string)){
			return false;
		}
		
		$sql = 'DELETE FROM ' . $table . ' WHERE `' . self::escape($id_name) . '` IN(' . $ids_string. ')';
		if (!self::query($sql)) {
			return false;
		}
		return self::affected_rows();
	}
	
	public static function save($data, $id = '', $table = '', $id_name = 'id') {

		if ($id == '') {
			$type = 'insert';
		} else {
			$type = 'update';
		}
		$table = self::get_table($table);
		if ($type == 'insert') {
			$keys = array();
			$values = array();
			foreach ($data as $key => $value) {
				$keys[] = '`' . self::escape($key). '`';
				$values[] = '\'' . self::escape($value) . '\'';
			}
			if (sizeof($keys) != sizeof($values)) {
				return false;
			}
			$ignore = '';
			if (self::$ignore_insert) {
				$ignore = ' IGNORE ';
			}

			$sql = 'INSERT ' . $ignore . ' INTO ' . $table . '(' .implode(',', $keys). ') VALUES('. implode(',', $values) .')';
			if (!self::query($sql)) {
				return false;
			}

			return self::get_insert_id();
		}
		$values = array();
		foreach ($data as $key=>$value) {
			$values[] = '`' . self::escape(trim($key)) . '`=\'' . self::escape($value) . '\'';
		}
		if (!sizeof($values)) {
			return false;
		}
		$sql = 'UPDATE ' . $table . ' SET ' . implode(',', $values) . ' WHERE `' . self::escape($id_name) . '` =\'' . self::escape($id) . '\'';
		
		if (!self::query($sql)) {
			return false;
		}
		return self::affected_rows();
	}
	static function get_connect($mode = 'write', $index = null, $reconnect = false) 
	{
		static $connect 		= null;
		static $count_reconnect = 0;// 重连次数
		static $error_connect 	= 0;// 连接错误的服务器数
		$mode = in_array($mode, array('write', 'read')) ? strtolower($mode) : 'write';
		
		// 如果第一次连接
		if (!isset($connect[$mode]) || $reconnect !== false) 
		{
			$count_reconnect = 0; // 重新计算重连次数
			// 如果设置时使用读服务器，但没有相关配置项，则尝试使用写服务器
			if ($mode == 'read' && isset(self::$params['slaves']) && !empty(self::$params['slaves'])) {
				// 配置的服务器数量
				$count = count(self::$params['slaves']);
				if ($index === null || !is_int($index) || $index < 0 || $index>=$count) {
					$index = rand(0, $count - 1);
				} 
				$p = self::$params['slaves'][$index];
			} else {
				$p = self::$params;
			}
			// 检查配置项完整
			if (!(isset($p['db_host']) && isset($p['db_user']) && isset($p['db_pass']) )) 
			{
			    trigger_error("[getConnect]Mysql config error. "  , E_USER_ERROR);
				exit;
			}
			if (!isset($p['db_port'])) {
				$p['db_port'] = 3306;
			}

			$connect[$mode] = @mysql_connect($p['db_host'] . ':' . $p['db_port'], $p['db_user'], $p['db_pass']);

			// 如果连接失败，则尝试连接下一台读服务器
			if (!$connect[$mode]) 
			{
                
				if ($mode == 'read') 
				{
					++ $error_connect;
					//如查所有读服务器都连接失败,尝试连接写服务器
					if ($error_connect >= $count) 
					{
						return $this->get_connect();
					}
					
					if (++$index > $count) {
						$index = 0;
					}
					
					return self::get_connect('read', $index);
				} else {
                    trigger_error('[getConnect]Connect Mysql server error', E_USER_ERROR);
                    exit;
				}
			}
			
			
			// 默认使用UTF8编码
			$p['db_charset'] = isset($p['db_charset']) ? $p['db_charset'] : 'utf-8';
			self::_set_charset($p['db_charset'], $connect[$mode]);
			//mysql_query('SET NAMES ' . $p['charset'], $connect[$mode]);
			
			// 默认使用和写数据库相同的库名
			if (!isset($p['db_name'])) {
				$p['db_name'] = self::$params['db_name'];
			}
			
			mysql_select_db($p['db_name'], $connect[$mode]);
		}
		
		// 如果出现长时间没mysql动作而引起超时，则尝试重连，重连次数为3
		if (!mysql_ping($connect[$mode])) 
		{
			if ($count_reconnect < 3) 
			{
				$count_reconnect ++;
				mysql_close($connect[$mode]);
				
				return $this->getConnect('read', $index, true);
			} else {
				return false;
			}
		}
		return $connect[$mode];
	}

    static function auto_connect($sql = null) {
		$write_command = array('insert', 'update', 'delete', 'replace', 'alter', 'create', 'drop', 'rename', 'truncate');
		if ($sql !== null) {
			$sql = explode(' ', trim((string)$sql));
		}
		if ($sql === null || !in_array(strtolower($sql[0]), $write_command)) {
			return self::get_connect('read');
		}
		return self::get_connect();
	}

	static function _set_charset($charset, $link_identifier){
		$version = mysql_get_server_info($link_identifier);
		$charset = strtolower($charset);
		if($version > '4.1'){
			$sql = $charset ? "character_set_connection={$charset}, character_set_results={$charset}, character_set_client=binary" : '';
			$sql .= $version > '5.0.1' ? ((empty($sql) ? '' : ', ')."sql_mode=''") : '';
			$sql && mysql_query("SET {$sql}", $link_identifier);
		}
        //Solve utf-8 messcode bug.
        mysql_query("set names 'utf8'");
	}

    static function set_field($field, $value, $id, $table, $id_name)
    {
        if(is_array($field)) {                                                                                                                                 
            $data           =   $field;
        }else{
            $data[$field]   =   $value;
        }    
        return self::save($data, $id, $table, $id_name);

    }
    
    static function set_inc($field, $step=1, $id, $table='', $id_name='id')
    {
        return self::set_field($field,array('exp',$field.'+'.$step), $id, $table, $id_name); 
    }

    static function set_dec($field,$step=1)
    {
        return self::set_field($field,array('exp',$field.'-'.$step), $id, $table, $id_name); 
    }

	/*
		Escapes a string for use in a mysql_query
	*/
	public static function escape($str) {
		return mysql_escape_string($str);
	}

	public static function fetch_array($query, $resultType = MYSQL_ASSOC) 
	{
		return mysql_fetch_array($query, $resultType);
	}

	public static function result_one($sql) 
	{
		$query = self::query($sql);
		return self::result($query, 0);
	}

	public static function fetch_one($sql) 
	{
        $query = self::query($sql);
		return self::fetch_array($query);
	}

	public static function fetch_all($sql, $id = '') 
	{
        $arr = array();
		$query = self::query($sql);
		while($data = self::fetch_array($query)) {
			$id ? $arr[$data[$id]] = $data : $arr[] = $data;
		}
		return $arr;

	}

    static function execute($sql) 
	{	
		$sql = self::push_sql($sql);
		$conn = self::auto_connect($sql);
		if (!$conn) return false;
		$rs = mysql_query($sql, $conn);
		if (!$rs) {
            trigger_error('[execute]query error:'. mysql_error($conn) . '. SQL: '.$sql , E_USER_ERROR);
		}
		// 查询后的状态
		self::$state = array(
					'sql' => $sql,
					'result' => $rs,
					'last_insert_id' => mysql_insert_id($conn),
					'affected_rows' => mysql_affected_rows($conn),
					'error' => mysql_error($conn),
					'errno' => mysql_errno($conn)
					);

		return $rs ? $rs : false;
	}

    /**
    *   查询返回结果集
    */
    static function query($sql, $fetch_mode = MYSQL_ASSOC, $id='') 
	{
		if (!$rs = self::execute($sql)) {
			return false;
		}
        return $rs;
	}

    static function push_sql($sql) {
		array_push(self::$querys, $sql);
		return self::$last_sql = $sql;
	}

    /**
	 * 得到执行一SQL写操作后的影响记录行数
	 * @return int 返回受影响的记录行数
	 */
	static function affected_rows() {
    
		return self::$state['affected_rows'];
	}

	public static function error() 
	{
		return ((self::link) ? mysql_error(self::$link) : mysql_error());
	}

	public static function errno() 
	{
		return intval((self::link) ? mysql_errno(self::$link) : mysql_errno());
	}

	public static function result($query, $row) 
	{
		$query = @mysql_result($query, $row);
		return $query;
	}

	public static function num_rows($query) 
	{
		$query = mysql_num_rows($query);
		return $query;
	}

	public static function num_fields($query) 
	{
		return mysql_num_fields($query);
	}

    
    /**
	 * 释放mysql查询结果
	 * @param int query handle
	 */
    static function free($query_id="")
    {
		if ($query_id == '') {
			return;
		}
		mysql_free_result($query_id);
		self::$last_query_id = '';
    }

    /**
	 * 得到最后插入记录的自增ID
	 * @return int
	 */
	static function get_insert_id() {
		return self::$state['last_insert_id'] ? self::$state['last_insert_id']: false;
	}

	public static function fetch_row($query) 
	{
		$query = mysql_fetch_row($query);
		return $query;
	}

	public static function fetch_fields($query) 
	{
		return mysql_fetch_field($query);
	}

	public static function version() 
	{
		return mysql_get_server_info(self::$link);
	}

	public static function close() 
	{
		return mysql_close(self::$link);
	}

}
/**
* $Id db.vdr.php tiger <ji.xiaod@gmail.com>
*/

