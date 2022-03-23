<?

class File{

	// безопасный путь (путь без возможности обращения к родительской директории)
	static function safe_path($path){
		$path = preg_replace('/\.+\//', '_/', $path);
		$path = preg_replace('/\s+/', ' ', $path);

		return $path;
	}

	// временный файл после завершения скрипта будет удален
	// временная папка после завершения скрипта не будет удалена
	static function tmpfile(string $name = NULL, $dir = NULL){
		if($dir){
			$dir = self::safe_path($dir);
			$dir = trim($dir, '/');

			$dir = sys_get_temp_dir().'/'.$dir;
			if(!is_dir($dir)) mkdir($dir, 0755, true);
		}else{
			$dir = sys_get_temp_dir();
		}

		if($name){
			$name = str_replace('/', '_', $name);
			$filename = "$dir/$name";
		}else{
			$filename = tempnam($dir, 'tmpfile_');
		}

		register_shutdown_function(function() use($filename){
			if(file_exists($filename)) unlink($filename);
		});

		return fopen($filename, 'w');
	}

	// рекурсивное удаление папки
	static function rrmdir($dirname, $noDeleteTargetDir = false){
		$dh = opendir($dirname);
		while(($filename = readdir($dh)) !== false){
			if($filename == '.' or $filename == '..') continue;

			$fullFilename = $dirname.'/'.$filename;
			if(is_dir($fullFilename)){
				self::rrmdir($fullFilename);
			}else{
				unlink($fullFilename);
			}
		}
		closedir($dh);

		if(!$noDeleteTargetDir) rmdir($dirname);
	}

	static function download($file, $name = '', $contentType = 'application/octet-stream'){
		if(!file_exists($file)) exit();

		$handle = fopen($file, 'r');
		if(!$name) $name = basename($file);
		$length = filesize($file);

		File::downloadFromHandle($handle, $name, $contentType, $length);
	}

	static function downloadFromHandle($handle, $name, $contentType = 'application/octet-stream', $length = NULL){
		self::setDownloadHeaders($handle, $name, $contentType, $length);

		rewind($handle);
		fpassthru($handle);

		exit();
	}

	static function setDownloadHeaders($handle, $name, $contentType = 'application/octet-stream', int $length = NULL){
		$name = self::translit($name);
		if(is_null($length) and $handle){
			$fstat = fstat($handle);
			if(isset($fstat['size'])) $length = $fstat['size'];
		}

		$charset = File::charset();
		$isPlainText = preg_match('/(\.txt|\.csv)$/', $name);

//		// сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
//		// если этого не сделать файл будет читаться в память полностью
		if(ob_get_level()) ob_end_clean();

		// заставляем браузер показать окно сохранения файла
		header('Content-Description: File Transfer');
		header('Content-Type: '.$contentType.'; charset:'.$charset);
		header('Content-Disposition: attachment; filename="'.$name.'"');
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		if(!is_null($length)){
			if($charset == 'utf-8' and $isPlainText) $length += 3; // BOM
			header('Content-Length: '.$length);
		}

		if($charset == 'utf-8' and $isPlainText) echo "\xEF\xBB\xBF"; // BOM
	}

	// подготовить имя файла для скачивания
	static function prepareDownloadFileName(string $fileName){
		$fileName = preg_replace('/[^\w()-]/u', '_', $fileName);
		$fileName = preg_replace('/_+/', '_', $fileName);

		return $fileName;
	}

	static function translit($str){
		$tr = [
			"А" => "A", "Б" => "B", "В" => "V", "Г" => "G",
			"Д" => "D", "Е" => "E", "Ж" => "J", "З" => "Z", "И" => "I",
			"Й" => "Y", "К" => "K", "Л" => "L", "М" => "M", "Н" => "N",
			"О" => "O", "П" => "P", "Р" => "R", "С" => "S", "Т" => "T",
			"У" => "U", "Ф" => "F", "Х" => "H", "Ц" => "TS", "Ч" => "CH",
			"Ш" => "SH", "Щ" => "SCH", "Ъ" => "", "Ы" => "YI", "Ь" => "",
			"Э" => "E", "Ю" => "YU", "Я" => "YA", "а" => "a", "б" => "b",
			"в" => "v", "г" => "g", "д" => "d", "е" => "e", "ж" => "j",
			"з" => "z", "и" => "i", "й" => "y", "к" => "k", "л" => "l",
			"м" => "m", "н" => "n", "о" => "o", "п" => "p", "р" => "r",
			"с" => "s", "т" => "t", "у" => "u", "ф" => "f", "х" => "h",
			"ц" => "ts", "ч" => "ch", "ш" => "sh", "щ" => "sch", "ъ" => "y",
			"ы" => "yi", "ь" => "", "э" => "e", "ю" => "yu", "я" => "ya",
			" " => "_", ":" => "_", "/" => "_", "\\" => "_"
		];
		return strtr($str, $tr);
	}

	// данные загруженного файла с проверками размера и расширения
	static function getLoadedFileInfo($name, array $availableExtensionsNames, $maxSizeMB = 16, $important_required = true){
		if(!isset($_FILES[$name])){
			if($important_required) throw new Exception(L('Request_error_required').": File '$name'", 4003);
		}

		if($_FILES[$name]['size'] > $maxSizeMB * 1024 * 1024) return core()->error(L('FILES_Max_size', ['maxSize' => "$maxSizeMB MB"]));

		$path_info = pathinfo($_FILES[$name]['name']);
		$extension = $path_info['extension'];

		if(!in_array($extension, $availableExtensionsNames)){
			return core()->error(L('FILES_Incorrect_file_format', ['availableExtensionsNames' => implode('/', $availableExtensionsNames)]));
		}

		return ['path' => $_FILES[$name]['tmp_name'], 'extension' => $extension, 'basename' => $_FILES[$name]['name']];
	}


	// разделение данных из $dataURL
	static function get_dataURL($dataURL){
		$dataURL = explode(',', $dataURL);
		if(sizeof($dataURL) != 2) return;

		$format = $dataURL[0];
		$data = $dataURL[1]; // закодированная картинка
		unset($dataURL);

		$format = explode(';', $format);
		if(sizeof($format) != 2) return;
		$encoding = $format[1]; // base64 (тип кодирования)
		$mime_type = substr($format[0], strpos($format[0], ':') + 1);

		if($encoding == 'base64') $data = base64_decode($data);

		return [$mime_type, $data];
	}

	static function im_get($file, $extension = ''){
		if(!$extension){
			$path_info = pathinfo($file);
			$extension = $path_info['extension'];
		}

		$extension = strtolower($extension);
		switch($extension){
			case 'jpg':
			case 'jpeg': $im = @imagecreatefromjpeg($file); break;
			case 'png': $im = @imagecreatefrompng($file); break;
			default: return false;
		}

		if(!$im) return core()->error('Wrong file format: ".'.$extension.'"');

		return $im;
	}

	static function im_save($im, $outfile, $extension = ''){
		if(!$extension){
			$path_info = pathinfo($outfile);
			$extension = $path_info['extension'];
		}

		$extension = strtolower($extension);
		switch($extension){
			case 'jpg':
			case 'jpeg': return imagejpeg($im, $outfile, 100);
			case 'png': return imagepng($im, $outfile, 100);
			default: return imagejpeg($im, $outfile, 100);
		}
	}

	// изменение размеров фотографии
	static function im_resize($infile, $outfile, $w_new, $h_new, $of_max = true, $extension = ''){
		$im = self::im_get($infile, $extension);
		if(!$im) return;

		$w = imagesx($im);
		$h = imagesy($im);

		if($w_new > $w) $w_new = $w;
		if($h_new > $h) $h_new = $h;

		$k1 = $w_new / $w;
		$k2 = $h_new / $h;
		if($of_max) $k = $k1 > $k2?$k2:$k1;
		else $k = $k1 < $k2?$k2:$k1;

		$w_new = intval(imagesx($im) * $k);
		$h_new = intval(imagesy($im) * $k);

		$im_new = imagecreatetruecolor($w_new, $h_new);
		imagecopyresampled($im_new, $im, 0, 0, 0, 0, $w_new, $h_new, imagesx($im), imagesy($im));

		return self::im_save($im_new, $outfile);
	}

	static function im_squeezeGD($gd, $W, $H){
		$X = ImageSX($gd);
		$Y = ImageSY($gd);

		$H_NEW = $Y;
		$W_NEW = $X;

		if($X > $W){
			$W_NEW = $W;
			$H_NEW = $W * $Y / $X;
		}

		if($H_NEW > $H){
			$H_NEW = $H;
			$W_NEW = $H * $X / $Y;
		}

		$H = (int)$H_NEW;
		$W = (int)$W_NEW;

		$gd_new = imagecreatetruecolor($W, $H);

		$transparent = imagecolorallocatealpha($gd_new, 0, 0, 0, 127);
		imagefill($gd_new, 0, 0, $transparent);
		imagesavealpha($gd_new, true);

		imagecopyresampled($gd_new, $gd, 0, 0, 0, 0, $W, $H, $X, $Y);

		return $gd_new;
	}

	// поворот фотографии
	// $direction:
	// 1 - по часовой (90 градусов)
	// -1 - против часовой (90 градусов)
	static function im_rotate($file, $direction){
		$im = self::im_get($file);
		if(!$im) return;

		$im_new = imagerotate($im, -90 * $direction, 0);
		return self::im_save($im_new, $file);
	}

	// обрезать фотографию
	static function im_crop($infile, $outfile, $x1, $y1, $x2, $y2){
		$im = self::im_get($infile);
		if(!$im) return;

		$w = $x2 - $x1;
		$h = $y2 - $y1;

		$im_new = imagecreatetruecolor($w, $h);
		imagecopyresampled($im_new, $im, 0, 0, $x1, $y1, $w, $h, $w, $h);

		return self::im_save($im_new, $outfile);
	}

	static function charset($handle = NULL){
		if($handle){
			rewind($handle);
			$mayBeBOM = fread($handle, 3);
			rewind($handle);

			if($mayBeBOM == "\xEF\xBB\xBF") return 'utf-8';
		}

		$charset = 'Windows-1251';

		return $charset;
	}

	static function CSV_delimiter(){
		$delimiter = ';';

		return $delimiter;
	}

	// разделитель дробных чисел
	static function CSV_decPoint(){
		$delimiter = '.';

		return $delimiter;
	}

	static function arrayToCSV($rows, $handle, $delimiter = NULL, $convertCharset = true, $decPoint = NULL){
		if(!$delimiter) $delimiter = File::CSV_delimiter();
		if(!$decPoint) $decPoint = File::CSV_decPoint();

		$convertToCharset = false;
		if($convertCharset) $convertToCharset = File::charset();

		foreach($rows as $row) self::rowToCSV($row, $handle, $delimiter, $convertToCharset, $decPoint);
	}

	static function rowToCSV(array $row, $handle, $delimiter, $convertToCharset, $decPoint){
		if($convertToCharset and $convertToCharset != 'utf-8'){
			foreach($row as $index => $cell){
				if($cell = iconv('utf-8', $convertToCharset.'//TRANSLIT//IGNORE', $cell)) $row[$index] = $cell;
			}
		}

		if($decPoint != '.'){
			$row = array_map(function($cell) use($decPoint){
				if(!is_float($cell)) return $cell;

				return str_replace('.', $decPoint, $cell);
			}, $row);
		}

		fputcsv($handle, $row, $delimiter);
	}

	static function genHandlePDFFromHTML(string $html, $brand_name = ''){
		ini_set('memory_limit', '2048M');

		$mpdfOptions = [
			'mode' => 'utf-8',
			'format' => 'A4',
			'default_font_size' => '8',
			'margin_left' => 10,
			'margin_right' => 10,
			'margin_top' => 7,
			'margin_bottom' => 20,
			'margin_header' => 10,
			'margin_footer ' => 10,
			'tempDir' => core()->folderTmp
		];

		$mpdf = new \Mpdf\Mpdf($mpdfOptions);
		$mpdf->list_indent_first_level = 0;
		$mpdf->setHTMLFooter('<div class="footer"><div class="footer_org_name">'.$brand_name.'</div><div class="paging">'.L('Page').' {PAGENO}/{nb}</div></div>');

		preg_match('/<style[^>]*>([^<]*)<\/style>(.*)/s', $html, $matches);
		$stylesheet = $matches[1];
		$html = $matches[2];

		$mpdf->WriteHTML($stylesheet, 1);
		$mpdf->WriteHTML($html, 2); //формируем pdf

		$handle = fopen('php://temp', 'r+');
		fwrite($handle, $mpdf->Output('', 'S'));

		return $handle;

	}

//	// Обратная функция для str_getcsv()
//	static function str_putcsv($fields, $delimiter = ',', $enclosure = '"'){
//		$handle = fopen('php://temp', 'r+');
//		fputcsv($handle, $fields, $delimiter, $enclosure);
//		rewind($handle);
//		$data = fread($handle, 1048576);
//		fclose($handle);
//
//		return rtrim($data, "\n");
//	}

	static function CSV_string($string){
		return '"'.str_replace('"', '""', $string).'"';
	}

}
