<?

// Подмодули работают по принципу Декоратора с отложенной инициализацией и обратной связью
// Декорирумыей объект - модуль (основной объект модуля)
// Декоратор - модмодуль (подключемый объект модуля)
// Имена создаваемых объектов (подмодулей) ассоциируются с именами функций (submodName_funcName)
// При отстутствии необходимых методов у модуля, проихсдит подключение подмодуля (событие __call())
// При отстутствии необходимых методов у подмодуля происходит обращение к модулю (событие __call())
//
// Из любого подмодуля можно вызывать методы подмодуля и основного модуля
//  за исключением AJAX методов (например protected_get()), так как обращение к несуществующим AJAX методам подмодуля должно вызывать ошибку
// Для вызова из одного подмодуля метода другого подмодуля необходимо обращаться к нему через модуль:
//	- m('module_name')->submodName_funcName();
//	- $this->m->submodName_funcName();
//
// Из подмодуля можно обращаться к свойствам модуля через $this: $this->property_name;
// Запись свойств модуля необходимо осуществлять только через объект модуля:
//	- m('module_name')->property = 1;
//	- $this->m->property = 1;

trait submods_engine{

	var $cache_file_not_exists = [];
	var $m = false; // модуль (только у подмодулей)
	var $that = false; // родительский модуль
	var $subMods = []; // подмодули

	function m($module = false){
		if(!$module) $module = $this;

		if($module->that) return $this->m($module->that);
		return $module;
	}

	function __isset($property_name){
		if($this->m) return isset($this->m->$property_name);

		return false;
	}

	// поиск свойств в модуле
	function __get($property_name){
		if($this->m and isset($this->m->$property_name)) return $this->m->$property_name;

		$this->__get_error($property_name);
	}

	// поиск метода в модуле
	function __call($func_name, $param_arr){
		if(preg_match('/\W/', $func_name)) return $this->__call_error($func_name); // safety

		$res = false;

		// делегировпния ajax функция другому подмодулю невозможно
		if(preg_match('/^protected_(get|add|edit|del)$/', $func_name)) return $this->__call_error($func_name);

		// поиск функции в родительском модуле / подмодуле
		$called_ok = $this->__call_parent($func_name, $param_arr, $this, $res);

		// подключить подмодули относительно текущего модуля / подмодуля
		if(!$called_ok) $called_ok = $this->__call_child($func_name, $param_arr, $this, $res);

//		// подключить подмодули относитлеьно модуля
//		if(!$called_ok and $this->m){
//			if($this->m != $this) $called_ok = $this->__call_child($func_name, $param_arr, $this->m, $res);
//		}

		if(!$called_ok) $this->__call_error($func_name);

		return $res;
	}

	// получить имя функции и имя подмодуля
	function __call_get_funcName_subModuleName($func_name){
		$subModuleName = false;
		$func_name_parts = explode('_', $func_name);
		if(count($func_name_parts) == 1) return array($func_name, $subModuleName);

		if($func_name_parts[0] == 'protected'){
			// при обращении к специализированным функциям, имя модуля распологается в другой части строки
			switch($func_name_parts[1]){
				case 'get':
				case 'add':
				case 'edit':
				case 'del':
				case 'api':
					$subModuleName = isset($func_name_parts[2])?$func_name_parts[2]:'';
					if($subModuleName) unset($func_name_parts[2]);

					break;
				default: break;
			}
		}elseif($func_name_parts[0] == 'api'){
			$subModuleName = isset($func_name_parts[1])?$func_name_parts[1]:'';
			if($subModuleName) unset($func_name_parts[1]);
		}else{
			$subModuleName = array_shift($func_name_parts);
		}

		$func_name = implode('_', $func_name_parts);

		return array($func_name, $subModuleName);
	}

	// поиск метода в прородительском модуле
	function __call_parent($func_name, $param_arr, $module, &$res){
		$parent = $module->that;
		if(!$parent) return;

		if(method_exists($parent, $func_name)){
			$res = call_user_func_array(array($parent, $func_name), $param_arr);
			return true;
		}

		return $this->__call_parent($func_name, $param_arr, $parent, $res);
	}

	// поиск метода в подмодуле
	function __call_child($func_name, $param_arr, $module, &$res){
		$moduleName = get_class($module);
		$moduleName_parts = explode('_', preg_replace('/^mod_/', '', $moduleName));

		// в имени модуля указана версия
		$isAPIV2 = false;
		if(is_numeric(isset($moduleName_parts[1])?$moduleName_parts[1]:false)){
			$moduleName_parts[0] .= '_'.$moduleName_parts[1];
			unset($moduleName_parts[1]);

			$isAPIV2 = true;
		}

		list($func_name, $subModuleName) = $this->__call_get_funcName_subModuleName($func_name);
		if(!$subModuleName) return;

		if(isset($module->subMods[$subModuleName])){
			$res = call_user_func_array(array($module->subMods[$subModuleName], $func_name), $param_arr);
			return true;
		}

		if($isAPIV2){
			$subModuleFilename = core()->root."/api/services/$moduleName_parts[0]/".(implode('.', $moduleName_parts)).".$subModuleName.php";
		}else{
			$subModuleFilename = core()->root."/api/modules/$moduleName_parts[0]/".(implode('.', $moduleName_parts)).".$subModuleName.php";
		}

		$subModuleFilename_hash = md5($subModuleFilename);

		if(isset($this->cache_file_not_exists[$subModuleFilename_hash])) return;
		if(!file_exists($subModuleFilename)){
			// поиск подмодуля в папке
			$subModuleFilename = core()->root.'/api/services/'.(implode('/', $moduleName_parts))."/$subModuleName.php";

			// если подмодуль не разделен по папкам
			if(!file_exists($subModuleFilename) and count($moduleName_parts) >= 2){
				$moduleName_parts_folder = array_slice($moduleName_parts, 0, 2);
				$moduleName_parts = array_slice($moduleName_parts, 2);
				$moduleName_parts[] = $subModuleName;
				$subModuleFilename = core()->root.'/api/services/'.implode('/', $moduleName_parts_folder).'/'.(implode('.', $moduleName_parts)).'.php';
			}

			if(!file_exists($subModuleFilename)){
				$this->cache_file_not_exists[$subModuleFilename_hash] = 1;
				return;
			}
		}

		include_once($subModuleFilename);
		$class_name = $moduleName.'_'.$subModuleName;
		$module->subMods[$subModuleName] = new $class_name();
		$module->subMods[$subModuleName]->that = $module;
		$module->subMods[$subModuleName]->m = $this->m($module);
		$res = call_user_func_array(array($module->subMods[$subModuleName], $func_name), $param_arr);

		return true;
	}

	function __get_error($property_name){
		throw new Exception('Undefined property: '.get_class($this).'::'.$property_name);
	}

	function __call_error($func_name){
		throw new Exception('Call to undefined method '.get_class($this).'::'.$func_name, ERROR_CODE_METHOD);
	}

}
