<?

if(!defined('ERROR_CODE_AUTH')){
	define('ERROR_CODE_SERVER_OFF', 503);
	define('ERROR_CODE_SERVER_TOO_MANY_REQUESTS', 429);
	define('ERROR_CODE_AUTH', 53);
	define('ERROR_CODE_RIGHTS', 54);
	define('ERROR_CODE_REQUEST_NAME', 1000);
	define('ERROR_CODE_SERVICE', 1001);
	define('ERROR_CODE_OPERATOR', 1002);
	define('ERROR_CODE_METHOD', 1003);
	define('ERROR_CODE_API_VERSION', 1004);
	define('ERROR_CODE_REQUEST_DATA', 2000);
	define('ERROR_CODE_REQUEST_REQUIRED', 2001);
	define('ERROR_CODE_REQUEST_TYPE', 2002);
	define('ERROR_CODE_REQUEST_VALUE', 2003);
	define('ERROR_CODE_REQUEST_FILTER', 2004);
	define('ERROR_CODE_REQUEST_PAGING', 2005);
	define('ERROR_CODE_NOT_ENOUGH_MONEY', 3000);
	define('ERROR_CODE_REQUIRED_PAYMENT', 3001);
	define('ERROR_CODE_INTERNAL', 10001);
	define('ERROR_CODE_ARG', 12003);
}

function setRequest($array = []){
	// прокидываем формат (может быть необходимо для определения формата исключений)
	if(req('getFormat') and !isset($array['getFormat'])) $array['getFormat'] = req('getFormat');

	array_push(common::$request_stack, $_REQUEST);
	$_REQUEST = $array;
}

function backRequest(){
	if(common::$request_stack) $_REQUEST = array_pop(common::$request_stack);
}

function sanitize($value, $quotes2entity = true){
	if(is_array($value)){
		foreach($value as $i => $value_i) $value[$i] = sanitize($value_i, $quotes2entity);
		return $value;
	}

	if($quotes2entity){
		$value = preg_replace('/(<|&lt;)(\w|[!?\/])/i', '$1 $2', $value); // убрать открывание тэгов
		$value = str_replace("'", '&#39;', $value);
		$value = str_replace('"', '&quot;', $value);
	}

	return addslashes($value);
}

// получить данные из запроса (GET, POST)
function request($name, $default = '', $important_required = false, $sanitize = true, $sanitize_quotes2entity = true){
	if(isset($_REQUEST[$name])){
		$value = $_REQUEST[$name];
		if($sanitize) $value = sanitize($value, $sanitize_quotes2entity);
		return $value;
	}elseif($important_required){
		throw new Exception(L('Request_error_required').": '$name'", ERROR_CODE_REQUEST_REQUIRED);
	}

	return $default;
}

// получить данные из запроса (краткая запись)
function req($name, $default = '', $important_required = false, $sanitize = true, $sanitize_quotes2entity = true){
	return request($name, $default, $important_required, $sanitize, $sanitize_quotes2entity);
}

// получить данные из запроса (с проверкой типа)
function r_int($name, $default = 0, $important_required = false){
	$val = request($name, $default, $important_required);
	if(!is_numeric($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);

	return (int)$val;
}

function r_float($name, $default = 0, $important_required = false){
	$val = request($name, $default, $important_required);
	if(!is_numeric($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);

	return (float)$val;
}


function r_bool($name, $default = false, $important_required = false){
	$val = request($name, $default, $important_required);

	if($val == 0) $val = false;
	if($val == 1) $val = true;

	if(!is_bool($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);

	return (bool)$val;
}

function r_date($name, $default = '2010-01-01', $important_required = false){
	$val = request($name, $default, $important_required);

	if(!is_string($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);
	if(!preg_match('/^\d\d\d\d-\d\d-\d\d$/', $val)) throw new Exception(L('Request_error_value').": '$name'", ERROR_CODE_REQUEST_VALUE);

	return $val;
}

function r_datetime($name, $default = '2010-01-01 00:00:00', $important_required = false){
	$val = request($name, $default, $important_required);

	if(!is_string($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);
	if(!preg_match('/^\d\d\d\d-\d\d-\d\d \d\d:\d\d:\d\d$/', $val)) throw new Exception(L('Request_error_value').": '$name'", ERROR_CODE_REQUEST_VALUE);

	return $val;
}

function r_url($name, $default = '', $important_required = false){
	$val = request($name, $default, $important_required, false, false);
	if(!is_string($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);

	if($val === '') return '';

	$val = trim($val);
	$val = preg_replace('~(^(?:https?://)?(?:www\.)?)|/+$~i', '', $val);
	$val = str_replace(['<', '>'], '', $val);

	$val = urlToPuny($val);
	$val = urlHostToLowerCase($val);

	$domain = preg_replace('~^https?://|/.*~i', '', $val);
	if(!common::valid_domain($domain)) throw new Exception(L('Request_error_value').": '$name'".' ('.L('PROJECTS_Incorrect_domain').')', ERROR_CODE_REQUEST_VALUE);

	return $val;
}

function r_url_full($name, $default = '', $important_required = false){
	$val = request($name, $default, $important_required, false, false);
	if(!is_string($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);

	if($val === '') return $val;

	$val = urlToPuny($val);
	$val = urlHostToLowerCase($val);

	$domain = preg_replace('~^https?://|/.*~i', '', $val);
	if(!common::valid_domain($domain)) throw new Exception(L('Request_error_value').": '$name'".' ('.L('PROJECTS_Incorrect_domain').')', ERROR_CODE_REQUEST_VALUE);

	return $val;
}

function r_email($name, $default = '', $important_required = false){
	$val = request($name, $default, $important_required);
	if(!is_string($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);

	$val = trim($val);
	if(!common::check_email($val)) throw new Exception(L('USER_Invalid_email_error'), ERROR_CODE_REQUEST_VALUE);

	return $val;
}

// получить данные из запроса (с проверкой значения)
function r_exp($name, $default = '', $important_required = false, $expected = []){
	$val = request($name, $default, $important_required);

	if(is_array($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);
	if(!in_array($val, $expected)) throw new Exception(L('Request_error_value').": '$name'", ERROR_CODE_REQUEST_VALUE);

	return $val;
}

// получить данные из запроса (с проверкой типа)
function r_arr($name, $default = [], $important_required = false, $sanitize = true, $sanitize_quotes2entity = true){
	$val = request($name, $default, $important_required, $sanitize, $sanitize_quotes2entity);
	if(!is_array($val)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);

	return $val;
}

// получить данные из запроса в виде массива (с проверкой типа - число)
// ключи массива - только числа
function r_arr_int($name, $default = [], $important_required = false){
	$val = r_arr($name, $default, $important_required);

	foreach($val as $index => $val_i){
		if(!is_numeric($index)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);
		if(!is_numeric($val_i)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);
	}

	return $val;
}

// получить данные из запроса в виде массива (с проверкой типа - дата)
function r_arr_date($name, $default = [], $important_required = false){
	$val = r_arr($name, $default, $important_required);

	foreach($val as $val_i){
		if(!preg_match('/^\d\d\d\d-\d\d-\d\d$/', $val_i)) throw new Exception(L('Request_error_value').": '$name'", ERROR_CODE_REQUEST_VALUE);
	}

	return $val;
}

// получить данные из запроса в виде массива (с проверкой типа - url)
function r_arr_url($name, $default = [], $important_required = false){
	$val = r_arr($name, $default, $important_required, false, false);

	foreach($val as &$_val_i){
		$_val_i = r_url('', $_val_i);
	}

	return $val;
}

// получить данные из запроса в виде массива (с проверкой типа - полного url)
function r_arr_url_full($name, $default = [], $important_required = false){
	$val = r_arr($name, $default, $important_required, false, false);

	foreach($val as &$_val_i){
		$_val_i = r_url_full('', $_val_i);
	}

	return $val;
}

// получить данные из запроса в виде массива (с проверкой типа - url)
function r_arr_email($name, $default = [], $important_required = false){
	$val = r_arr($name, $default, $important_required);

	foreach($val as &$_val_i){
		$_val_i = r_email('', $_val_i);
	}

	return $val;
}

// получить данные из запроса в виде массива (с проверкой значения)
function r_arr_exp($name, $default = [], $important_required = false, $expected = []){
	$val = r_arr($name, $default, $important_required);

	foreach($val as $val_i){
		if(is_array($val_i)) throw new Exception(L('Request_error_type').": '$name'", ERROR_CODE_REQUEST_TYPE);
		if(!in_array($val_i, $expected)) throw new Exception(L('Request_error_value').": '$name'", ERROR_CODE_REQUEST_VALUE);
	}

	return $val;
}

// сгенерировать строку с лимитами (для последующего использования в SQL)
function r_limit(){
	$limit = req('limit', 0);
	if(!$limit) return false;

	$offset = req('offset', 0);
	$page = r_int('page', 1);
//
	if(!is_numeric($limit)) throw new Exception("'limit': Must be greater than 0 and less than 10000", ERROR_CODE_REQUEST_PAGING);
	if(!is_numeric($offset)) throw new Exception("'offset': Must be greater than or equal to zero", ERROR_CODE_REQUEST_PAGING);

	$offset = $offset + ($page - 1) * $limit;
	if($offset < 0) $offset = 0;

	$limit = "$offset, $limit";

	return $limit;
}

function r_selectorData(){
	$selectror_data = [];

	$selectror_data['fields'] = r_arr('fields', [], false, false);
	$selectror_data['filters'] = r_arr('filters', [], false, false);
	$selectror_data['orders'] = r_arr('orders', [], false, false);

	$selectror_data['limit'] = r_limit();
	$selectror_data['offset'] = 0;
	if($selectror_data['limit']){
		$selectror_data['limit'] = explode(',', $selectror_data['limit']);

		$selectror_data['offset'] = $selectror_data['limit'][0];
		$selectror_data['limit'] = $selectror_data['limit'][1];
	}

	$selectror_data['fetchStyle'] = req('fetchStyle');

	return $selectror_data;
}

function implodeQuotes($arr, bool $isString = NULL){
	if(!$arr) return "''";

	foreach($arr as &$_item){
		if(is_numeric($_item) and ! $isString) continue;

		$_item = "'$_item'";
	}

	return implode(',', $arr);
}
