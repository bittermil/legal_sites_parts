<?

include('ajax.php');

try{
	include_once('../api.php');

	tStart();

	$module = request('module', '', true);
	if(!m($module)->getLaw('read')){
		if(m('user')->id == -1) throw new Exception('Access error', ERROR_CODE_AUTH);
		throw new Exception('Access error', ERROR_CODE_RIGHTS);
	}

	if(isset($_POST['page']) and $_POST['page'] < 1 or isset($_REQUEST['page']) and $_REQUEST['page'] < 1) throw new Exception("'Page' must be greater than zero");
}catch(Exception $Exception){
	core()->error($Exception->getMessage(), NULL, $Exception->getCode());
	core()->exception_json();
}

//====================== ПОДГОТОВКА ЗАПРОСА ======================

$func = request('func'); // Функция модуля, отвечающая за получения данных
if(!$func) $func = request('fget'); // deprecated
if(!is_string($func)) $func = '--';
$where = ''; // Поисковой запрос

$limit = (int)request('rows', 0); // Количество элементов на странице
if(!$limit) $limit = (int)request('limit', 0); // Количество элементов на странице
$page = (int)request('page', 1); // Номер страницы запроса
$offset = (int)request('offset', 0); // Количество пропускаемых записей
$sidx = request('sidx'); // Имя поля по которому будет вестись сортировка
if($sidx){
	$sidx = str_replace('`', '', $sidx);
	$sidx = str_replace('.', '`.`', $sidx);
	$sidx = str_replace(',', '`,`', $sidx);
	$sidx = "`$sidx`";
}
$sord = strtoupper(request('sord')); // Направление сортировки
if($sord and $sord != 'ASC' and $sord != 'DESC') $sord = '';
$o = trim(str_replace(',', " $sord,", $sidx).' '.$sord);

$offset = $offset + ($page - 1) * $limit;
$limitSql = ($limit)?$offset.', '.$limit:'';

//====================== ВЫПОЛНЕНИЕ ЗАПРОСА ======================
try{
	$result = m($module)->get($where, $limitSql, $o, $func);
	if($getFormat != 'apiV2') $result = (array)$result;

	if(is_array($result) and isset($result[0]) and is_array($result[0]) and isset($result[0]['total'])){
		$total = (int)$result[0]['total'];
		array_shift($result);
	}else{
		if(!is_null(core()->resultTotal))
			$total = core()->resultTotal;
		else
			$total = (int)dbh()->query('SELECT FOUND_ROWS()')->fetchColumn();

		if(is_array($result)){
			if(count($result) > $total) $total = count($result);
		}
	}
}catch(Exception $Exception){
	include('catch_errors.php');
}

// вывести ошибку, без формирования результата
if(core()->errors) core()->exception_json();
//====================== ВЫВОД РЕЗУЛЬТАТОВ ======================

include_once('get.results.php');

//echo '<!--Время выполнения: '.round(microtime(true) - $start_time, 4).' секунд-->';