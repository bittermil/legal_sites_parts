<?

$GLOBALS['dbh_main'] = false;
$GLOBALS['dbh_remote'] = false;

function dbh(){
//	echo '{"message":"'.L('Message_dbh_remote_error').'.","error":true}';
//	exit();

	return new dbh($GLOBALS['dbh_main'], TV::$config['db_main']);
}

function dbhR(){
//	echo '{"message":"'.L('Message_dbh_remote_error').'.","error":true}';
//	exit();

	return new dbh($GLOBALS['db_robots_1'], TV::$config['db_robots'][1]);
}

function dbhStop(){
	unset($GLOBALS['dbh_main']);
}

function dbhRStop(){
	unset($GLOBALS['db_robots_1']);
}

class dbh{

	protected $db_config;
	protected $availableUTF8mb4 = false;
	protected $autoEncodeJson = false;
	var $dbh;
	protected $PDOStatement;
	var $query = '';
	var $error = false;
	var $errorMessage = '';
	var $count_error = 0;

	function __construct(&$dbh, &$db_config){
		$this->db_config = &$db_config;
		if(!$dbh) $dbh = $this->dbh_init($this->db_config);

		$this->dbh = &$dbh;
	}

	function dbh_init($db_config){
		$host = $db_config['dbhost'];
		if(!TV::$isReserve and !TV::$isRobot) $host = $db_config['dblocalhost'];

		$pdo_options = [PDO::MYSQL_ATTR_LOCAL_INFILE => true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION];

		try{
			$dbh = new PDO("$db_config[dbtype]:host=$host;dbname=$db_config[dbname]", $db_config['dbuser'], $db_config['dbpass'], $pdo_options);
		}catch(PDOException $e){
			$error = "Mysql, error connect to: $host";

			throw new Exception($error, $e->getCode());
		}

		$dbh->query('SET NAMES utf8mb4');
		$dbh->query("SET lc_time_names = 'ru_RU'");
		if(defined('IS_CRON') or TV::$isRobot) $dbh->query('SET wait_timeout = 900');

		if(!TV::$isRobot) $dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

		return $dbh;
	}

	function setAvailableUTF8mb4(bool $availableUTF8mb4){
		$this->availableUTF8mb4 = $availableUTF8mb4;

		return $this;
	}

	function setAutoEncodeJson(bool $autoEncodeJson){
		$this->autoEncodeJson = $autoEncodeJson;

		return $this;
	}

	function prepare_table_name($table_name){
		return str_replace(['`', '.'], ['', '`.`'], $table_name);
	}

	function prepare_column_name($column_name){
		return str_replace(['`', '.'], ['', '`.`'], $column_name);
	}

	function prepare_sql($sql){
		if(!$this->availableUTF8mb4) $sql = $this->stripUTF8mb4($sql);

		$sql = preg_replace("/(^|[^\\\])'/", "$1\'", $sql); // экранировать кавычку
		$sql = preg_replace("/(^|[^\\\])((\\\{2})+)'/", "$1$2\'", $sql); // добавить нечетное экранирование кавычки
		$sql = str_replace("''", "'\'", $sql);
		$sql = preg_replace('/(?:^|[^\\\])(?:[\\\][\\\])*\\\$/', '$0\\', $sql); // экранировать нечетный обратный слеш в конце строки

		return $sql;
	}

	function prepare_val($val, $sanitize = false){
		if($this->autoEncodeJson and is_array($val)) $val = json_encode($val, JSON_UNESCAPED_UNICODE);

		if($val === 'NULL' or $val === NULL) return 'NULL';
		if($sanitize) return "'".sanitize($val)."'";
		return "'".$this->prepare_sql($val)."'";
	}

	function stripUTF8mb4($string){
		return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $string);
	}

	function query($query){
		$this->query = $query;

		return $this;
	}

	function appendQuery($query){
		$this->query .= $query;

		return $this;
	}

	function sel($fieldsNames = '', $calc_found_rows = false){
		$this->query .= "SELECT ";
		if($calc_found_rows) $this->query .= 'SQL_CALC_FOUND_ROWS ';

		if($fieldsNames === ''){
			$field_str = '* ';
		}elseif(is_array($fieldsNames)){
			$field_str = implode('`, `', $fieldsNames);
			$field_str = '`'.str_replace('.', '`.`', $field_str).'` ';
		}else{
			$field_str = $fieldsNames.' ';
		}

		$this->query .= $field_str;

		return $this;
	}

	function from($table_name = '', $alias = ''){
		$table_name = $this->prepare_table_name($table_name);
		$this->query .= "FROM `$table_name` ";
		if($alias) $this->query .= "`$alias` ";

		return $this;
	}

	function join($table_name = '', $type_join = '', $alias = ''){
		$table_name = $this->prepare_table_name($table_name);

		switch($type_join){
			case 'l':
			case 'left':
				$this->query .= 'LEFT ';
				break;
			case 'r':
			case 'right':
				$this->query .= 'RIGHT ';
				break;
			default:
				$this->query .= 'INNER ';
		}

		$this->query .= "JOIN `$table_name` ";
		if($alias) $this->query .= "`$alias` ";

		return $this;
	}

	function on($where = ''){
		if($where) $this->query .= "ON ($where) ";

		return $this;
	}

	function use_index($index_name){
		if($index_name) $this->query .= " USE INDEX ($index_name) ";

		return $this;
	}

	function use_indexes(array $indexes_names){
		foreach($indexes_names as $index_name) $this->use_index($index_name);

		return $this;
	}

	function where($where = ''){
		if($where) $this->query .= "WHERE ($where) ";

		return $this;
	}

	function w($where = ''){
		return $this->where($where);
	}

	function group($group_by = ''){
		if($group_by) $this->query .= "GROUP BY $group_by ";

		return $this;
	}

	function g($group_by = ''){
		return $this->group($group_by);
	}

	function having($having = ''){
		if($having) $this->query .= "HAVING ($having) ";

		return $this;
	}

	function h($having = ''){
		return $this->having($having);
	}

	function order($order = ''){
		if($order) $this->query .= "ORDER BY $order ";

		return $this;
	}

	function o($order = ''){
		return $this->order($order);
	}

	function limit($limit = ''){
		if($limit) $this->query .= "LIMIT $limit ";

		return $this;
	}

	function l($limit = ''){
		return $this->limit($limit);
	}

	function handler(){
		try{
			return $this->dbh->query($this->query);
		}catch(PDOException $Exception){
			$this->error('', $Exception);
			return $this->try_again($Exception, __FUNCTION__, func_get_args());
		}
	}

	function fetchColumn($column_number = 0){
		try{
			$this->PDOStatement = $this->dbh->query($this->query);
			if($this->PDOStatement) return $this->PDOStatement->fetchColumn($column_number);
		}catch(PDOException $Exception){
			$this->error('', $Exception);
			return $this->try_again($Exception, __FUNCTION__, func_get_args());
		}
	}

	function fetch($fetch_style = false){
		if(!$fetch_style) $fetch_style = PDO::FETCH_ASSOC;
		try{
			$this->PDOStatement = $this->dbh->query($this->query);
			if($this->PDOStatement) return $this->PDOStatement->fetch($fetch_style);
		}catch(PDOException $Exception){
			$this->error('', $Exception);
			return $this->try_again($Exception, __FUNCTION__, func_get_args());
		}
	}

	function fetchAll($fetch_style = false){
		if(!$fetch_style) $fetch_style = PDO::FETCH_ASSOC;
		try{
			$this->PDOStatement = $this->dbh->query($this->query);
			if($this->PDOStatement) return $this->PDOStatement->fetchAll($fetch_style);
		}catch(PDOException $Exception){
			$this->error('', $Exception);
			return $this->try_again($Exception, __FUNCTION__, func_get_args());
		}
	}

	function insert($table_name, $ignore = false, $fieldsNames = ''){
		$table_name = $this->prepare_table_name($table_name);

		$this->query = "INSERT ";
		if($ignore) $this->query .= "IGNORE ";
		$this->query .= "INTO `$table_name` ";

		if($fieldsNames){
			if(is_array($fieldsNames)) $field_str = '`'.implode('`, `', $fieldsNames).'`';
			else $field_str = $fieldsNames;

			$this->query .= "($field_str) ";
		}

		return $this;
	}

	function update($table_name, $ignore = false, $alias = ''){
		$table_name = $this->prepare_table_name($table_name);

		$this->query = "UPDATE ";
		if($ignore) $this->query .= "IGNORE ";
		$this->query .= "`$table_name` ";
		if($alias) $this->query .= "`$alias` ";

		return $this;
	}

	function replace($table_name){
		$table_name = $this->prepare_table_name($table_name);

		$this->query = "REPLACE ";
		$this->query .= "`$table_name` ";

		return $this;
	}

	function set($data, $duplicate_update = false, $sanitize = false){
		$this->query .= "SET ";

		if(is_array($data)){
			$data_str = '';
			foreach($data as $name => $val) $data_str .= "`".$this->prepare_column_name($name)."` = ".$this->prepare_val($val, $sanitize).",";
			$data_str = substr($data_str, 0, strlen($data_str) - 1);
		}else{
			$data_str = $data;
		}

		$this->query .= "$data_str ";

		if($duplicate_update) $this->query .= "ON DUPLICATE KEY UPDATE $data_str ";

		return $this;
	}

	function setValues($data, $sanitize = false){
		foreach($data as &$_value) $_value = $this->prepare_val($_value, $sanitize);
		$this->query .= '('.implode(',', $data).')';

		return $this;
	}

	function del($table_name, $alias = ''){
		$table_name = $this->prepare_table_name($table_name);
		$this->query = "DELETE `".($alias?$alias:$table_name)."` FROM `$table_name` ";
		if($alias) $this->query .= "`$alias` ";

		return $this;
	}

	function exec($query = ''){
		if($query) $this->query = $query;

		try{
			return $this->dbh->exec($this->query);
		}catch(PDOException $Exception){
			$this->error('', $Exception);
			return $this->try_again($Exception, __FUNCTION__, func_get_args());
		}
	}

	function beginTransaction(){
		try{
			return $this->dbh->beginTransaction();
		}catch(PDOException $Exception){
			$this->error('', $Exception);
			return $this->try_again($Exception, __FUNCTION__, func_get_args());
		}
	}

	function commit(){
		try{
			return $this->dbh->commit();
		}catch(PDOException $Exception){
			$this->error('', $Exception);
			return $this->try_again($Exception, __FUNCTION__, func_get_args());
		}

	}

	function id(){
		return $this->dbh->lastInsertId();
	}

	function connection_id(){
		return $this->sel('CONNECTION_ID()')->fetchColumn();
	}

	// используйте функцию MySQL UNCOMPRESS(UNHEX(`fieldName`)) для разжатия на стороне сервера
	function compress(string $string){
		$data = gzcompress($string);
		$len = strlen($string);
		$head = pack('V', $len);

		return bin2hex($head.$data);
	}

	// повторная попытка отправки команды SQL
	function try_again($Exception, $function_name, $args, $reconnect = true){
		$try_again = $this->try_again_must($Exception);
		if(!$try_again) return;

		// номер попытки
		$this->count_error++;
		if($this->count_error > 1){
			$this->write_to_file("Number of restarts exceeded, try restarting is stopped\n", $try_again);
			return;
		}

		if($reconnect) $this->dbh = $this->dbh_init($this->db_config);

		// повторить запрос
		$this->error = false;
		$this->errorMessage = '';
		return call_user_func_array([$this, $function_name], $args);
	}

	// необходима ли повторная попытка
	function try_again_must($Exception){
		$try_again = false;

		if($Exception and isset($Exception->errorInfo[1])){
			if($Exception->errorInfo[1] == 2006) $try_again = true; // server timeout
			if($Exception->errorInfo[1] == 1205) $try_again = true; // lock
			if($Exception->errorInfo[1] == 1213) $try_again = true; // циличный lock
		}

		return $try_again;
	}

	function error($error = '', $Exception = false){
		$try_again = $this->try_again_must($Exception);

		if(!$error){
			$error = '';

			if($this->dbh){
				$bd_errorCode = $this->dbh->errorCode();
				$bd_errorInfo = $this->dbh->errorInfo();

				$error .= $bd_errorCode.' - '.$bd_errorInfo[2].'. ('.$bd_errorInfo[1].'/'.$bd_errorInfo[0].')';
				if($phpError = error_get_last()) $error .= "\nPHP: $phpError[message]";
			}

			if($Exception){
				if(!$this->dbh or !$this->dbh->errorInfo()[2]) $error .= "\n".$Exception->getMessage().' ('.$Exception->getCode().')';

				if(isset($Exception->getTrace()[1])){
					$file_trace = $Exception->getTrace()[1];
				}elseif(isset($Exception->getTrace()[0])){
					$file_trace = $Exception->getTrace()[0];
				}

				if(isset($file_trace)) $error .= "\n$file_trace[file] ($file_trace[line])";
			}

			$error .= "\n===== repeat: $this->count_error =====\n";
			$error .= $this->query?$this->query:'--';
			$error .= "\n=====";
		}

		$ms = round(explode(' ',microtime())[0] * 1000);
		$error_data = "\n[".date("Y-m-d H:i:s:$ms O")."]\n";

		if(isset($_SERVER['HTTP_USER_AGENT'])) $error_data .= 'User-Agent: '.$_SERVER['HTTP_USER_AGENT']."\n";

		if(isset($_SERVER['REQUEST_URI'])){
			if(!$_POST){
				$requestData = file_get_contents('php://input');
				if($requestData) $_POST = json_decode($requestData, true);
			}

			if(strpos($_SERVER['REQUEST_URI'], '/ajax/') !== false AND isset($_SERVER['HTTP_REFERER'])){
				$error_data .= $_SERVER['HTTP_REFERER']."\n";
			}

			if(isset($_SERVER['HTTP_HOST'])) $error_data .= $_SERVER['HTTP_HOST'];
			$error_data .= $_SERVER['REQUEST_URI'];
			if($_POST) $error_data .= "\nPOST: ".json_encode($_POST, JSON_UNESCAPED_UNICODE);
		}else{
			$error_data .= 'Console';
		}

		$error_data .= "\n=====";
		$error_data .= "\n$error\n";

		if(function_exists('core')){
			if(Core::$i_admin){
				if(!defined('IS_CRON')){
					throw new Exception($error);
				}else{
					core()->error($error);
				}
			}else{
				$ms = round(explode(' ',microtime())[0] * 1000);
				core()->error('Error DB: '.date("Y-m-d H:i:s:$ms O"));
			}
		}else{
			echo $error_data.'<br>';
			if($try_again) echo 'Query to be restarted<br>';
		}

		$this->write_to_file($error_data, $try_again);

		$this->error = true;
		$this->errorMessage = $error_data;
	}

	function write_to_file($text, $try_again = false){
		echo $text;

		error_log($text);
	}

	function getMeta(){
		if(!$this->PDOStatement) return [];

		$meta = [];
		for($i = 0; $i < $this->PDOStatement->columnCount(); $i++) $meta[] = $this->PDOStatement->getColumnMeta($i);

		return $meta;
	}

}