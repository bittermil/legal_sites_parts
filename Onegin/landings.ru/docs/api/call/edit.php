<?

include('ajax.php');

try{
	include_once('../api.php');

	tStart();

	$module = request('module', '', true);
	if(!m($module)->getLaw('write')){
		if(m('user')->id == -1) throw new Exception('Access error', ERROR_CODE_AUTH);
		throw new Exception('Access error', ERROR_CODE_RIGHTS);
	}

	$oper = request('oper', '', true);
	if(!is_string($oper)) $oper = '--';
}catch(Exception $Exception){
	core()->error($Exception->getMessage(), NULL, $Exception->getCode());
	core()->exception_json();
}

$func = request('func'); // Функция модуля, отвечающая за получения данных
if(!$func) $func = request('fedit'); // deprecated

if($oper != 'add'){
	$id = request('id', '');

	// разрешенные значения для $id
	if(is_array($id)){
		foreach($id as $index => $id_i) $id[$index] = (int)$id_i;
		//$id = implode(',', $id);
	}elseif(!is_numeric($id)){
		$id = explode(',', $id);
		foreach($id as $index => $id_i) $id[$index] = (int)$id_i;
		$id = implode(',', $id);

		if(!$id) $id = (int)$id;
	}else{
		$id = (int)$id;
	}
}

$memoryLimit = ini_get('memory_limit');
ini_set('memory_limit', '64M');
$data = [];
foreach($_POST as $index => $val){
	if($index == 'ssi' or $index == 'id' or $index == 'module' or $index == 'oper' or $index == 'func' or $index == 'type_result' or $index == 'api_key' or $index == 'app_auth') continue;
	$data[$index] = sanitize($val);
}
ini_set('memory_limit', $memoryLimit);

try{
	switch($oper){
		case 'add':
			$result = m($module)->add($data, $func);
			break;
		case 'edit':
			$result = m($module)->edit($data, $id, $func);
			break;
		case 'del':
			$result = m($module)->del($id, $func);
			break;
		default:
			core()->error('unknown oper');
			$result = 0;
	}
}catch(Exception $Exception){
	include('catch_errors.php');
}

if($result === false or $result === NULL) $result = 0;

core()->exception_json($result);