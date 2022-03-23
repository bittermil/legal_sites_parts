<?

include_once('submods_engine.php');

// базовый класс для работы с модулями системы
class mod_base{

	use submods_engine;

	var $vars = []; // дополнительные переменные модуля

	// Системные свойства
	protected $index = 0;
	protected $read = false;
	protected $write = false;
	protected $only_owner_w = false;
	protected $only_owner_r = false;
	protected $isAdmin = false;

	var $curl;
	var $t_verif = 'users_verif';

	function init($index = 0, $prava = 0){
		if($index){
			$this->index = $index;
		}else{
			$this->index = $this->getIndex();
		}

		if(!$prava) $prava = m('user')->prava[$this->index];

		if($prava >= 1) $this->read = true; // Только чтение
		if($prava >= 2) $this->write = true; // Чтение и запись
		if($prava >= 3) $this->only_owner_w = true; // Ограничение на запись только своих элементов
		if($prava == 4) $this->only_owner_r = true; // Ограничение на чтение только своих элементов

		$this->isAdmin = ($this->write and !$this->only_owner_w); // полные права на этот модуль
	}

	// инициализация прав модуля (без регистрации модуля в БД)
	function free_init($prava = 4){
		if($prava >= 1) $this->read = true; // Только чтение
		if($prava >= 2) $this->write = true; // Чтение и запись
		if($prava >= 3) $this->only_owner_w = true; // Ограничение на запись только своих элементов
		if($prava == 4) $this->only_owner_r = true; // Ограничение на чтение только своих элементов
	}

	// получить идентификатор модуля в системе
	function getIndex(){
		$class_name = get_class($this);
		$sql = "SELECT `id` FROM `modules` WHERE `file` = '$class_name' LIMIT 1";

		return (int)dbh()->query($sql)->fetchColumn();
	}

	// Получить права в ассоциативном массиве или значение конкретных прав
	function getLaw($lawName = ''){
		if($lawName and isset($this->$lawName)) return $this->$lawName;

		$law_arr = [];
		$law_arr['read'] = $this->read;
		$law_arr['write'] = $this->write;
		$law_arr['only_owner_w'] = $this->only_owner_w;
		$law_arr['only_owner_r'] = $this->only_owner_r;

		return $law_arr;
	}

	function isAdmin(){
		return $this->isAdmin;
	}

	// ========================= Функции, применительные к модулям =========================

	// Вызов функций для cron
	function cron($func){
		try{
			return $this->{'cron_'.$func}();
		}catch(Exception $e){
			core()->error($e->getMessage());
		}
	}

	// вызов функции в контексте GET/POST запроса
	function call($oper, $func, $requestData = [], $transparentTransferData = false){
		if($transparentTransferData) $requestData = array_merge($_REQUEST, $requestData);

		// перехватчик ошибок находится в ajax контроллере
		if(defined('IS_AJAX')) return $this->ajax($oper, $func, $requestData);

		try{
			return $this->ajax($oper, $func, $requestData);
		}catch(Exception $Exception){
			if($Exception->getMessage()) core()->error($Exception->getMessage(), '', $Exception->getCode());
		}
	}

	function ajax($oper, $func, $requestData = []){
		setRequest($requestData);

		if(!isset($requestData['id'])) $requestData['id'] = false;

		$w = false;
		$l = false;
		$o = false;
		$id = $requestData['id'];

		switch($oper){
			case 'get':
				$result = $this->get($w, $l, $o, $func);
				break;
			case 'add':
				$result = $this->add($requestData, $func);
				break;
			case 'edit':
				$result = $this->edit($requestData, $id, $func);
				break;
			case 'del':
				$result = $this->del($id, $func);
				break;
			default:
				core()->error('unknown oper');
				$result = 0;
		}

		backRequest();

		return $result;
	}

	// Получение данных
	function get($where = '', $limit = '', $order = '', $func = ''){
		if($func) $func = '_'.$func;

		if($func and method_exists($this, 'api'.$func)){
			$res = ['result' => $this->{'api'.$func}()]; // это вызов API (любые операции, не только получение данных)
			if($res['result'] === NULL) $res['result'] = 0;
			return $res;
		}

		if(!$this->read){
			if(method_exists($this, 'get_minimal'.$func))
				return $this->{'get_minimal'.$func}($where, $limit, $order);
			else
				return [];
		}

//		if(!method_exists($this, 'protected_get'.$func)) return core()->error('Method not exists');
		return $this->{'protected_get'.$func}($where, $limit, $order);
	}

	// Добавление элемента
	function add($data, $func = ''){
		if($func) $func = '_'.$func;

		$this->vars['id'] = false;###

		if(!$this->write){
			if(method_exists($this, 'public_add'.$func))
				$res = $this->{'public_add'.$func}($data);
			else
				return 0;
		}else{
//			if(!method_exists($this, 'protected_add'.$func)) return core()->error('Method not exists');
			$res = $this->{'protected_add'.$func}($data);
		}

		if($res){
			if($this->vars['id']){###
				$this->onAdd($this->vars['id'], $func);###
				return $this->vars['id'];###
			}else###
			if($id = dbh()->id()){
				$this->onAdd($id, $func);

				if(req('getFormat') == 'apiV2') return $res;

				return $id; ### API v2
			}else{
				$this->onAdd(0, $func);
				return $res;
			}
		}else {
			return 0;
		}
	}

	// Редактирование элемента
	function edit($data, $id = NULL, $func = ''){
		if($func) $func = '_'.$func;

		if(!$this->write){
			if(method_exists($this, 'public_edit'.$func))
				return $res = $this->{'public_edit'.$func}($data, $id);
			else
				return 0;
		}else{
//			if(!method_exists($this, 'protected_edit'.$func)) return core()->error('Method not exists');
			$res = $this->{'protected_edit'.$func}($data, $id);
		}

		if($res) $this->onEdit($id, $func);

		return $res;
	}

	// Удаление элементов
	function del($ids, $func = ''){
		if($func) $func = '_'.$func;

		if(!$this->write) return 0;

		$ids = sanitize($ids);

//		if(!method_exists($this, 'protected_del'.$func)) return core()->error('Method not exists');

		$res = $this->{'protected_del'.$func}($ids);
		if($res) $this->onDel($ids, $func);

		return $res;
	}

	// Вызов curl
	function curlCall(string $url, $useCookies = false, $post = [], $options = [], array $headers = []){
		if(!isset($this->curl)) $this->curl = new \Topvisor\Curl\Curl();
		return $this->curl->call($url, $useCookies, $post, $options, $headers);
	}

// ========================= События =========================
	// получить дамп нужных строк
	protected function getForDump($ids, $func = ''){
		if(!$ids) return;

		if(method_exists($this, 'protected_get'.$func))
			$rows = $this->{'protected_get'.$func}("`id` IN (".$ids.")");
		else
			$rows = $this->{'protected_get'}("`id` IN (".$ids.")");

		return $rows;
	}

	// событие - добавление строки
	protected function onAdd($id, $func = ''){
		$this->onUpdate($id, $func);
	}

	// событие - редактирование строки
	protected function onEdit($id, $func = ''){
		$this->onUpdate($id, $func);
	}

	// событие - удаление из таблицы
	protected function onDel($ids, $func = ''){
		$this->onUpdate($ids, $func);
	}

	// событие - изменение таблицы
	protected function onUpdate($ids, $func = ''){

	}
// ========================= /События =========================

	// загрузить настройку
	function get_properties($table, $id, $name, $col_type = 'varchar', $fromBase64 = false){
		$value = dbh()->sel("`$col_type`")->from('properties')->w("`table` = '$table' AND `id` = '$id' AND `name` = '$name'")->fetchColumn();

		if($fromBase64) $value = base64_decode($value);
		return $value;
	}

	// сохранить настройку
	function save_properties($table, $id, $name, $value, $col_type = 'varchar', $toBase64 = false, $insertIgnore = false){
		$duplicateUpdate = true;
		if($insertIgnore) $duplicateUpdate = false;
		if($toBase64) $value = base64_encode($value);
		return dbh()->insert('properties', $insertIgnore)->set(['table' => $table, 'id' => $id, 'name' => $name, $col_type => $value], $duplicateUpdate)->exec();
	}

	// удалить настройку
	function del_properties($table, $id, $name){
		return dbh()->del('properties')->w("`table` = '$table' AND `id` = '$id' AND `name` = '$name'")->exec();
	}

	static function getSecretHash($secret){
		$hash = openssl_encrypt($secret, 'AES-256-ECB', TV::$config['sault'], OPENSSL_RAW_DATA);
		$hash = base64_encode($hash);
		return $hash;

//		$key = TV::$config['sault'];
//		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', 'ecb', '');
//		$key = substr($key, 0, mcrypt_enc_get_key_size($td));
//		$iv_size = mcrypt_enc_get_iv_size($td);
//		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//
//		mcrypt_generic_init($td, $key, $iv);
//		$hash = mcrypt_generic($td, $secret);
//		mcrypt_generic_deinit($td);
//		mcrypt_module_close($td);
//
//		return base64_encode($hash);
	}

	static function getSecret($hash = ''){
		if(!$hash) return;

		$hash = base64_decode($hash);
		if(!$hash) return false; // раскодировать не удалось

		$secret = openssl_decrypt($hash, 'AES-256-ECB', TV::$config['sault'], OPENSSL_RAW_DATA);
		return trim($secret);

//		$hash = base64_decode($hash);
//		if(!$hash) return false; // раскодировать не удалось
//
//		$key = TV::$config['sault'];
//		$td = mcrypt_module_open(MCRYPT_RIJNDAEL_256, '', 'ecb', '');
//		$key = substr($key, 0, mcrypt_enc_get_key_size($td));
//		$iv_size = mcrypt_enc_get_iv_size($td);
//		$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
//
//		mcrypt_generic_init($td, $key, $iv);
//		$secret = mdecrypt_generic($td, $hash);
//		mcrypt_generic_deinit($td);
//		mcrypt_module_close($td);
//
//		return trim($secret);
	}

// ========================== СТОРОННЯЯ АВТОРИЗАЦИЯ ==========================
	function auth_yandex($login = '', $password = '', $yandex_secret = ''){
		// авторизоваться через аккаунт сервиса
		if(!$login and !$password){
			$key_hash = $yandex_secret?$yandex_secret:TV::$config['yandex_secret'];
			$value = self::getSecret($key_hash);
			$value = explode(':', $value);

			if(sizeof($value) != 2) return;
			list($login, $password) = $value;
		}

		$result = $this->curlCall('https://passport.yandex.ru/passport', true, "login=$login&passwd=$password");
		$ok = strpos($result, 'name="passwd"') === false; // авторизоваться не предлагают - успешная авторизация
		//if($ok) $ok = strpos($result, 'name="answer"') === false; // могут предложить ввод телефона

//		if(m('user')->id == 10){
//			$answer = ''; // телефон Дэна
//
//			preg_match('/<form action="(.*?)"/uis', $result, $match);
//			$match = $match[1];
//
//			$track_id = preg_replace('/.*?=/uis', '', $match);
//			$challenge = 'phone';
//			$post = "track_id=$track_id&challenge=$challenge&answer=$answer";
//
//			$result = $this->get_content_new('https://passport.yandex.ru'.$match, '', true, $post);
//			echo $result;
//			exit;
//		}

		if(!$ok) return core()->error(L('Yandex_auth_error'));

		return $ok;
	}

	function authTwitter($lang = ''){
		$tokenIndexInConfig = $this->getTokenIndexInConfigByLang('twTokens', $lang);

		$consumerKey = TV::$config['twTokens'][$tokenIndexInConfig]['CONSUMER_KEY'];
		$consumerSecret = TV::$config['twTokens'][$tokenIndexInConfig]['CONSUMER_SECRET'];
		$oauthToken = TV::$config['twTokens'][$tokenIndexInConfig]['OAUTH_TOKEN'];
		$oauthSecret = TV::$config['twTokens'][$tokenIndexInConfig]['OAUTH_SECRET'];

		return new \Abraham\TwitterOAuth\TwitterOAuth($consumerKey, $consumerSecret, $oauthToken, $oauthSecret);
	}

	function getTokenIndexInConfigByLang(string $name, $lang = ''){
		if(!$lang) $lang = m('user')->lang;

		return $tokenIndexInConfig = array_search($lang, array_column(TV::$config[$name], 'lang'));
	}
// ========================== /СТОРОННЯЯ АВТОРИЗАЦИЯ ==========================

	// Ограничение для выборки из БД
	// $col - Колонка по которой будет вестись отбор (колонка владельца или колонка идентфикаторов ограничения)
	function restriction($sql, $oper, $col = 'user'){
		$current_userId = m('user')->id;
		if($this->only_owner_r and $oper == 'r' or $this->only_owner_w and $oper == 'w'){
			$sql .= " AND (`$this->t`.`$col` = '$current_userId')";
		}

		return $sql;
	}

}