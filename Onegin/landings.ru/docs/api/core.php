<?

include 'core.email.php';

class Core extends core_email{

    static $core = NULL;
    static $SYSTEM = [];
    static $i_admin = false;
    static $admin = NULL;
    var $m = []; // список доступных для подключения модулей
    var $messages = [];
    var $errors = [];
    var $resultTotal = NULL;
    var $resultMeta = NULL;
    var $root = NULL;
    var $folderLibraries = '';
    var $folderTmp = '';
    var $vars = [];
    var $lang = NULL;

    function __construct(){
	   $this->root = dirname(__DIR__);
	   $this->folderLibraries = $this->root.'/landings_new/libraries';
	   $this->folderTmp = sys_get_temp_dir();

	   include_once(__DIR__.'/common.php');

	   include($this->folderLibraries.'/vendor/autoload.php');
	   include_once(__DIR__.'/pdo.php');

	   $this->setSession();
    }

    // установка сессии
    function setSession(){
	   $url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	   $host = $url;
	   $host = preg_replace('/(^https?:\/\/)/', '', $host);

	   session_start();
	   session_name('langSession');

	   if(isset($_SESSION['lang'])){
		  $this->lang = $_SESSION['lang'];
	   }else{
		  switch($host){
			 case 'www.onegin-consulting.ru/about_company/':
				$this->lang = 'en';
				break;
			 default:
				$this->lang = 'ru';
		  }
	   }
	   
	   return $this->lang;
    }

    // установить необходимый путь к шаблону (сначала поиск в языковой версии, затем в общей)
    function get_tpl_path($tpl, $searchInParentLevel = 0, $lang = 'ru'){
	   if(file_exists("$this->root/landings_new/tpl/$lang/$tpl")) return "$this->root/landings_new/tpl/$lang/$tpl";

	   if(!$searchInParentLevel or file_exists("$this->root/tpl/$tpl")) return "$this->root/tpl/$tpl";

	   // искать в папке родителя
	   $tpl = preg_replace('/\/[^\/]+(\/[^\/]+)$/', '$1', $tpl);
	   return $this->get_tpl_path($tpl, $searchInParentLevel - 1);
    }

    // вернуть текст шаблона
    function get_tpl($tpl, $data = [], $searchInParentLevel = 0){
	   ob_start();
	   include($this->get_tpl_path($tpl, $searchInParentLevel));
	   $html = ob_get_contents();
	   ob_end_clean();

	   if($data) $html = $this->render_tpl($html, $data);

	   return $html;
    }

    // вставка значений переменных в шаблон
    function render_tpl($html, $data){
	   foreach($data as $name => $val){
		  if(is_array($val)){
			 foreach($val as $subName => $subVal){
				if(is_array($subVal)){
				    foreach($subVal as $subName2 => $subVal2){
					   if(is_array($subVal2)) continue;

					   $html = str_replace('%'.$name.'['.$subName.']'.'['.$subName2.']%', $subVal2, $html);
				    }
				}else{
				    $html = str_replace('%'.$name.'['.$subName.']%', $subVal, $html);
				}
			 }
		  }else{
			 $html = str_replace("%$name%", $val, $html);
		  }
	   }

	   return $html;
    }

    function m($moduleName, $moduleId = 0, $prava = 0){
	   // запрещенные имена модулей
	   if(!is_string($moduleName)) $moduleName = '--';
	   if($moduleName != File::safe_path($moduleName)) $moduleName = '--';
	   if(strpos($moduleName, '/') !== false) $moduleName = '--';

	   // Если модуль не загружен или прав на запись нет
	   // в момент инициализации $this->m[$moduleName] = true
	   if(!isset($this->m[$moduleName]) or is_object($this->m[$moduleName]) and method_exists($this->m[$moduleName], 'getLaw') and!$this->m[$moduleName]->getLaw('write')){
		  // переход на API v2
		  if(substr($moduleName, -2) == '_2'){
			 $serviceName = $moduleName;

			 $class_file = __DIR__."/services/$serviceName/$serviceName.php";
			 if(!file_exists($class_file)) throw new Exception("Service name error: '$serviceName'", ERROR_CODE_SERVICE);

			 include_once($class_file);
			 if(!class_exists($serviceName)) throw new Exception("Service name error: '$serviceName'", ERROR_CODE_SERVICE);

			 $this->m[$moduleName] = true; // сервис был инициирован (защита от цикличной инициализации внутри конструктора)
			 return $this->m[$moduleName] = new $serviceName($moduleId, $prava);
		  }


		  $class_file = "$this->root/class/modules/$moduleName.php";
		  if(!($fe = file_exists($class_file))){
			 $class_file = "$this->root/class/modules/".str_replace('mod_', '', $moduleName).'/'.str_replace('mod_', '', $moduleName).'.php';
			 $fe2 = file_exists($class_file);
		  }

		  if($fe or $fe2){
			 include_once($class_file);

			 if(!class_exists($moduleName)) throw new Exception("Service name error: '$moduleName'", ERROR_CODE_SERVICE);

			 $this->m[$moduleName] = true; // модуль был инициирован
			 return $this->m[$moduleName] = new $moduleName($moduleId, $prava);
		  }
	   }

	   if(!isset($this->m[$moduleName])) throw new Exception("Service name error: '$moduleName'", ERROR_CODE_SERVICE);

	   return $this->m[$moduleName];
    }

    // подключение необходимых модулей для работы системы
    function include_core_modules(){
	   include_once(__DIR__.'/file.php');
	   include_once(__DIR__.'/mod_base.php');
    }

    function setResultTotal(int $total = NULL){
	   $this->resultTotal = $total;
    }

    // генерация сообщения системы
    function message($message = ''){
	   if(is_array($message) or is_object($message))
			 $message = json_encode($message, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

	   if($message !== '') $this->messages[] = $message;

	   return 0;
    }

    function cleaerErros(){
	   $this->errors = [];
    }

    // генерация ошибки системы
    function error($errorString = '', $errorDetail = NULL, $errorCode = 0){
	   $this->errors[] = [
		  'code' => $errorCode,
		  'string' => $errorString,
		  'detail' => $errorDetail
	   ];

	   return 0;
    }

    function getErrorsString(){
	   $errorsString = [];
	   foreach($this->errors as $error){
		  $errorsString[] = $error['string'].($error['detail']?", $error[detail]":'').($error['code']?" ($error[code])":'');
	   }
	   $errorsString = implode("<br>", $errorsString);

	   return $errorsString;
    }

    function getMessagesString(){
	   return implode("<br>", $this->messages);
    }

    function exception_json($result = NULL){
	   $result_arr = [
		  'result' => $result
	   ];

	   if($this->messages) $result_arr['messages'] = $this->messages;
	   if($this->errors) $result_arr['errors'] = $this->errors;

	   echo json_encode($result_arr, JSON_UNESCAPED_UNICODE);
	   exit();
    }

}

function core(){
    return Core::$core;
}

function m($moduleName, $moduleId = 0, $prava = 0){
    return core()->m($moduleName, $moduleId, $prava);
}

function L($string_name, $data = false){
    $string = NULL;

    if(isset($L[$string_name])) $string = $L[$string_name];

    // искать в другом словаре
    if(is_null($string)){
	   if(preg_match('/^([A-Z]+)_(\w+)/', $string_name, $parts)){
		  $module_name = strtolower($parts[1]);
		  $string_name = $parts[2];

		  // файл словаря может быть не загружен
		  if(!isset($L[$module_name][$string_name])){
			 $file_dictionary = core()->root.'/landings_new/i18n/'.$module_name.'/'.core()->lang.'.php';
			 include($file_dictionary);
		  }

		  $string = '__'.$parts[1].'_'.$parts[2].'__';
		  if(isset($L[$module_name][$string_name])) $string = $L[$module_name][$string_name];
	   }else{
		  $string = '__'.$string_name.'__';
	   }
    }

    if($data) $string = common::tpl_render($string, $data);

    return $string;
}
