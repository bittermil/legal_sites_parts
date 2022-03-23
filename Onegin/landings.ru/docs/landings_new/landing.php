<?php

class Landing{

	var $name;
	var $root;
	var $folderTpl;
	var $folderLanding;
	var $config;
	var $authOk;
	var $userEmail;
	var $vars = [];

	// инициализация объекта
	function __construct($landingName){
		$this->name = $landingName;

		// определяем необходимые пути
		$this->root = dirname(__FILE__);
		$this->folderTpl = $this->root.'/tpl';
		$this->folderLanding = $this->root.'/pages/'.$this->name;

		// подгружаем настройки
		include($this->folderLanding.'/config.php');
		$this->config = $config;
		if(isset($vars)) $this->vars = $vars;

		$lifetime = 1800;
		session_set_cookie_params($lifetime);
		//session_start();

		isset($_SESSION['authOk']) ? $this->authOk = $_SESSION['authOk']:false;
		isset($_SESSION['authOk']) ? $this->userEmail = $_SESSION['email']:false;

		// выполнить необходимое действие, определяется параметром action
		if(req('action')) $this->execAction(req('action'));
	}

	// выполнить действие
	function execAction($action){
		switch($action){
			// оправка формы обратной связи
			case 'feedback':
				$type = req('type');

				$name = req('name');
				$phone = req('phone');
				$city = req('city');
				$lang = req('lang');
				$message = req('message');
				$email = req('email');
				$program= req('program');

				$urlsite = req('urlsite');
				$keyword = req('keyword');
				$files = $_FILES;

				$tplData = array(
					'name' => $name,
					'phone' => $phone,
					'city' => $city,
					'lang' => $lang,
					'email' => $email,
					'message' => $message,
					'program' => $program,
					'site' => 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'],
					'landingName' => $this->config['landingName'],
				);

				switch($type){
					// форма обратной связи вверху
					case 'top':
						$title = "Заявка на консультацию ($tplData[landingName])";
						$tpl = '/email/feedbackTop.php';


						break;
					// форма обратной связи внизу
					case 'bottom':
						$title = "Заявка на консультацию ($tplData[landingName])";
						$tpl = '/email/feedbackBottom.php';

						break;
					// всплывающая форма обратной связи
					case 'popup':
						$title = "Заявка на консультацию ($tplData[landingName])";
						$tpl = '/email/feedbackWithCity.php';

						break;
					// заказ звонка
					case 'call':
						$title = "Заказ звонка ($tplData[landingName])";
						$tpl = '/email/feedbackCall.php';

						break;
					// форма обратной связи с сообщением
					case 'textmessage':
						$title = "Сообщение ($tplData[landingName])";
						$tpl = '/email/feedbackMessage.php';

						break;
					// форма обратной связи с городом
					case 'feedbackwithcity':
						$title = "Сообщение ($tplData[landingName])";
						$tpl = '/email/feedbackWithCity.php';

						break;
					// форма обратной связи с городом для франшизы
					case 'feedbackwithcityfranshiza':
						$title = "Сообщение ($tplData[landingName])";
						$tpl = '/email/feedbackWithCityFranshiza.php';

						break;
					// форма обратной связи с названием программы
					case 'feedbackapply':
						$title = "Сообщение ($tplData[landingName])";
						$tpl = '/email/feedbackApply.php';

						break;
					// форма обратной связи с городом и языком
					case 'feedbackwithcitylang':
						$title = "Сообщение ($tplData[landingName])";
						$tpl = '/email/feedbackWithCityLang.php';

						break;
					default: return;
				}


				$tplHTML = file_get_contents($this->root.'/tpl/'.$tpl);
				$this->tplRender($tplHTML, $tplData);

				// добавляем в письмо информацию об иточнике перехода на лэндинг
				if($urlsite) $tplHTML .= "Площадка: $urlsite<br />";
				if($keyword) $tplHTML .= "Поисковый запрос: $keyword<br />";

				core()->sendMail($this->config['managerEmail'], $title, $tplHTML, $files);

				$result = new stdClass();
				$result->message = L('FEEDBACK_Form_submit_reply');

				echo json_encode($result);
				exit();

			case 'getPhoneByURL':
				$url = req('url');
				$host = $url;
				$host = preg_replace('/(^https?:\/\/)|(\/.*$)/', '', $host);

				switch($host){
//					case 'галичевский.рф':
//					case 'xn--80aebjochgf7d3c.xn--p1ai':
//						$phone = '+79052833753';
//						break;
					case 'ok-fizbankrot.ru':
					case 'ok-urbankrot.ru':
						$phone = '+74993909235';
						break;
					default:
						$phone = '+78126423368';
				}

				$fileLog = dirname(__FILE__).'/ringostat.log';
				file_put_contents($fileLog, date('Y-m-d H:i:s').' - '.$url."\n", FILE_APPEND);
				file_put_contents($fileLog, date('Y-m-d H:i:s').' - '.$host."\n", FILE_APPEND);
				file_put_contents($fileLog, date('Y-m-d H:i:s').' => '.$phone."\n\n", FILE_APPEND);

				echo $phone;
				exit();

			case 'bankrotstvoAuth':
				$email = req('email');
				$code = req('code');

				$currentDate = date("Y-m-d");
				$expiryDate = mysql::get_expiry_date($email, $code);
				$startDate = mysql::get_start_date($email, $code);

				$this->authOk = mysql::landings_code($email, $code);
				if($this->authOk) $this->authOk = true;


				if(!$this->authOk){
					$result = new stdClass();
					$result->message = 'Email или код введены неверно. Повторите, пожалуйста, еще раз.';

					echo json_encode($result);
					exit();
				}

				if($currentDate >= $startDate && $expiryDate != '0000-00-00' && $currentDate > $expiryDate){
					$this->authOk = false;

					$result = new stdClass();
					$result->message = 'Подписка истекла. Пожалуйста, продлите подписку!';

					echo json_encode($result);
					exit();
				}

				if($currentDate < $startDate){
					$this->authOk = false;

					$result = new stdClass();
					$result->message = 'Срок начала действия кода еще не наступил!';

					echo json_encode($result);
					exit();
				}

				$_SESSION['email'] = $email;
				$_SESSION['authOk'] = $this->authOk;

				echo $this->authOk;
				exit();

			case 'bankrotstvoLogout':
				$_SESSION['authOk'] = false;

				echo 1;
				exit();
			}
	}


	// вставка данных в шаблон
	function tplRender(&$tplHTML, array $tplData){
		foreach($tplData as $name => $val){
			$tplHTML = str_replace("%$name%", $val, $tplHTML);
		}
	}

	// это робот
	function iAmRobot(){
		return preg_match("/SputnikBot|Googlebot|Yahoo|Slurp|MSNBot|Bingbot|Teoma|Scooter|ia_archiver|Lycos|Yandex|StackRambler|Mail.Ru|Aport|WebAlta/i", $_SERVER['HTTP_USER_AGENT']);
	}

	// получить список изображений для блока - "Отзывы"
	function getImagesForReview(){
		$url = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$host = $url;
		$host = preg_replace('/(^https?:\/\/)/', '', $host);

		switch($host){
			case 'taro-katrin.ru/':
				$folder = $this->root.'/images/reviews/taro-katrin';
				$images = array();

				$handler = opendir($folder);
				while(($filename = readdir($handler)) !== false){
					if($filename[0] == '.') continue;

					$images[] = '/landings_new/images/reviews/taro-katrin/'.$filename;
				}
				break;
			case 'www.onegin-consulting.ru/about_company/':
				$folder = $this->root.'/images/reviews/about_company/en';
				$images = array();

				$handler = opendir($folder);
				while(($filename = readdir($handler)) !== false){
					if($filename[0] == '.') continue;

					$images[] = '/landings_new/images/reviews/about_company/'.core()->lang.'/'.$filename;
				}
				break;
			default:
				$folder = $this->root.'/images/reviews/common';

				$images = array();

				$handler = opendir($folder);
				while(($filename = readdir($handler)) !== false){
					if($filename[0] == '.') continue;

					$images[] = '/landings_new/images/reviews/common/'.$filename;
				}
		}

		return $images;
		exit();
	}

	// получить список изображений для блока - "Свидетельства"
	function getImagesForDocs(){
		//$folder = $this->root.'/images/docs/'.$this->name;
		$folder = $this->root.'/images/docs/common';

		$images = array();

		$handler = opendir($folder);
		while(($filename = readdir($handler)) !== false){
			if($filename[0] == '.') continue;

			//$images[] = '/landings_new/images/docs/'.$this->name.'/'.$filename;
			$images[] = '/landings_new/images/docs/common/'.$filename;
			asort($images);
		}

		return $images;
	}
}