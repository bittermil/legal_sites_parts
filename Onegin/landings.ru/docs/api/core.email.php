<?

class core_email{

	// Отправка E-mail
	// files: [['filename' => 'Путь к файлу', 'name' => 'Имя файла'] [, ...]];
	function sendMail($to, $subject, $message, $from = NULL, $files = NULL){
		$mail = new PHPMailer\PHPMailer\PHPMailer(true);

		$mail->From = $to;
		$mail->FromName = $this->name;
		$mail->CharSet = 'UTF-8';
		$mail->IsSMTP(); // enable SMTP
		$mail->SMTPDebug = 0; // debugging: 1 = errors and messages, 2 = messages only
		$mail->SMTPAuth = true; // authentication enabled
		$mail->SMTPSecure = 'ssl'; // secure transfer enabled REQUIRED for Gmail
		$mail->Host = "smtp.gmail.com";
		$mail->Port = 465;
		$mail->Username = "onegin.consulting.messages@gmail.com";
		$mail->Password = "chvupU893vgYDGiWur7DFChh";

		try{
			$mail->MsgHTML($message);
			$mail->AddAddress($to);
			$mail->Subject = strip_tags($subject);

			// если был файл, то прикрепляем его к письму
			if(!is_null($files)){
				foreach($files as $file){
					if(!is_array($file)){
						$basename = preg_replace('/.*\//u', '', $file);
						$file = array('tmp_name' => $file, 'name' => $basename);
					}
					if(!preg_match('/\.(zip|rar)$/i', $file['name'])) continue;
					$mail->AddAttachment($file['tmp_name'], $file['name']);
				}
			}


			// отправляем письмо
			$sended = $mail->send();
		}catch(PHPMailer\PHPMailer\Exception $e){
			$this->error('Mailer Error: '.$mail->ErrorInfo);

			$sended = false;
		}

		return $sended;
	}

	// заполенние шаблона письма
	function send_mail_render_tpl(&$html){
		$html = preg_replace_callback('/%([\w.\/]+)%/', function($name){
			$name = $name[1];

			if(substr($name, -5) == '.html'){
				// подключение файла
				$name = str_replace('..', '', $name);
				return core()->get_tpl("templates/$name");
			}else{
				// значение переменной
				if(isset($this->vars["mail_$name"])) return $this->vars["mail_$name"];
				return '';
			}
		}, $html);
	}

	// затираем аргументы настроек письма
	function send_mail_clear_vars(){
		if(isset($this->vars['header_tpl'])) unset($this->vars['header_tpl']);
		if(isset($this->vars['footer_tpl'])) unset($this->vars['footer_tpl']);

		if(isset($this->vars['is_auto_generated'])) unset($this->vars['is_auto_generated']);
		if(isset($this->vars['Reply-To'])) unset($this->vars['Reply-To']);
		if(isset($this->vars['List-Unsubscribe'])) unset($this->vars['List-Unsubscribe']);

		foreach($this->vars as $name => $val){
			if(substr($name, 0, 5) == 'mail_') unset($this->vars[$name]);
		}
	}

}