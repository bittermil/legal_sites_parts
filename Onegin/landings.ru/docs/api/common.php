<?

CONST DAY_SECS = 86400;
CONST MONTH_SECS = 2592000;

include('common.vars.php');

class common{
	static $mCheckLast = [
		'usage' => 0,
		'realUsage' => 0,
		'peakUsage' => 0,
		'realPeakUsage' => 0
	];
	static $t_bann_domain = 'bann_domain';
	static $request_stack = []; // стэк $_REQUESTS, используется для изменения $_REQUESTS с последующим откатом

	static function translate($text, $trnslit = false){
		return self::google_translate($text, $trnslit);
	}

	static function percent($part, $full = 100, $precision = 2){
		$percent = $full?$part / $full:0;
		$percent *= 100;

		// если десятичные знаки не выводятся и число при округлении может дать ноль, то округляем в большую сторону
		if(!$precision and $percent < 1)
			$percent = ceil($percent);
		else
			$percent = round($percent, $precision);

		return $percent;
	}

	static function dateFormat($date, $time = 0, $short = false){
		if(!$date) return '';
		if(substr($date, 0, 10) == '0000-00-00') return '--';

		$format = 'd.m.Y';
		if($short) $format = strtolower($format);

		$string = date($format, strtotime($date));

		switch($time){
			case 1:
				// часы
				$string .= ' '.substr($date, 11, 2);

				break;
			case 2:
				// часы и минуты
				$string .= ' '.substr($date, 11, 5);

				break;
			case 3:
				// часы, минуты и секунды
				$string .= ' '.substr($date, 11, 8);

				break;
		}

		return $string;
	}

	static function dateToRus($date, $delimiter = '.'){
		return preg_replace('/(\d{4})-(\d{2})-(\d{2})/', '$3'.$delimiter.'$2'.$delimiter.'$1', $date);
	}

	static function getWeekDay($date){
		$w = date('w', strtotime($date));

		$weekDays = ['Воскресенье', 'Понедельник', 'Вторник', 'Среда', 'Четверг', 'Пятница', 'Суббота'];
		$weekDay = $weekDays[$w];

		return $weekDay;
	}

	static function getMonth($time = '', $type = 1, $all = false){
		if(!$time) $time = time();
		if($time > 12)
			$n = date('n', $time);
		else
			$n = (int)$time;

        if($type == 1) $months = explode("|", L('CALENDAR_months'));
		else $months = explode("|", L('CALENDAR_of_months'));

		if(!$all){
			$month = $months[$n];
			return $month;
		}

		return $months;
	}

	static function check_email($email){
		//if(!preg_match("/^[-0-9a-zа-яё_\.]+@[-0-9a-zа-яё_^\.]+\.[a-zа-яё]{2,7}$/iu", $email)) return false;
		if(!preg_match("/^[-0-9a-z_\.\+]+@[-0-9a-z_^\.]+\.[a-z]{2,20}$/iu", $email)) return false;

		$domain = preg_replace('/^[^@]+@/', '', $email);
		$domain_first = preg_replace('/(?:[^.]+\.)+([^.]+\.[^.]+)/', '$1', $domain);
		$is_ban = dbh()->sel(1)->from(self::$t_bann_domain)->where("`domain` = '$domain' OR `domain` = '$domain_first'")->fetchColumn();
		if($is_ban) return false;

		return true;
	}

	static function get_domain_regexp(){
		$regexp_en =  '(?:ru|com|abbott|ac|ad|ae|aero|af|ag|ai|al|am|an|ao|aq|ar|as|asia|at|au|aw|ax|az|ba|bb|bd|be|bf|bg|bh|bi|biz|bj|bm|bn|bo|br|bs|bt|bv|bw|by|bz|ca|cat|cc|cd|cf|cg|ch|ci|ck|cl|cm|cn|co|coop|cr|cs|gb|cu|cv|cx|cy|cz|dd|de|dj|dk|dm|do|dz|ec|eco|edu|ee|eg|es|et|eu|fi|fj|fk|fm|fo|fr|ga|gd|ge|gf|gg|gh|gi|gl|gm|gn|gov|gp|gq|gr|gs|gt|gu|gw|gy|hk|hm|hn|hr|ht|hu|icu|id|ie|il|im|in|int|info|io|iq|ir|is|it|je|jm|jo|jobs|jp|ke|kg|kh|ki|km|kn|kp|kr|kw|ky|kz|la|lb|lc|li|live|lk|lr|ls|lt|lu|lv|ly|ma|mc|md|me|mh|mil|mg|mk|ml|mm|mn|mo|mobi|mp|mq|mr|ms|mt|mu|museum|mv|mw|mx|my|mz|na|name|nc|ne|net|ng|ni|nl|no|np|nr|nu|nz|org|pa|pe|pf|pg|ph|pk|pl|pm|pn|pr|pro|ps|pt|pw|py|qa|re|ro|rs|rw|sa|sb|sc|sd|se|sg|sh|si|sj|sk|sl|sm|sn|so|sr|st|su|sv|sx|sy|sz|tc|td|tech|tel|tf|tg|th|tj|tk|tl|tm|tn|to|tp|tr|tt|tv|tw|tz|ua|ug|uk|us|uy|uz|va|vc|ve|vg|vn|vu|wf|ws|ye|yt|za|zm|zw|academy|agency|art|arte|audible|audio|band|bargains|bike|blog|boutique|builders|buzz|bargains|bike|boutique|builders|cab|camera|camp|cam|careers|center|cheap|clothing|club|codes|coffee|company|computer|condos|construction|contractors|cool|cruises|dance|dating|democrat|diamonds|directory|domains|education|email|enterprises|equipment|estate|events|expert|exposed|farm|flights|florist|foundation|futbol|gallery|gift|glass|graphics|guitars|guru|holdings|holiday|house|immobilien|kitchen|land|lighting|limo|link|maison|management|marketing|men|menu|moe|ninja|partners|photo|photography|photos|pics|plumbing|productions|properties|recipes|rent|rentals|repair|review|reviews|sandvik|sexy|shoes|singles|social|solar|solutions|support|systems|tattoo|technology|tienda|tips|today|training|travel|vacations|ventures|viajes|villas|voyage|watch|works|zone|xxx|ads|associates|booking|business|ceo|ecom|forum|gives|global|gmbh|inc|institute|insure|lifeinsurance|llc|llp|ltd|ltda|mba|new|news|ngo|press|sarl|services|srl|studio|trade|trading|wiki|xin|auction|bid|blackfriday|buy|capital|charity|claims|compare|coupon|coupons|deal|dealer|deals|delivery|discount|exchange|flowers|free|furniture|gifts|gripe|grocery|jewelry|kaufen|lotto|parts|plus|promo|qpon|racing|rsvp|sale|salon|save|seat|shop|show|shopping|silk|spa|store|supplies|supply|taxi|tickets|tires|tools|toys|watches|college|courses|degree|ged|phd|prof|scholarships|school|schule|science|shiksha|study|translations|university|beer|cafe|catering|cityeats|cooking|diet|food|organic|pet|pizza|pub|rest|restaurant|soy|wine|blue|circle|dot|duck|fast|final|finish|fire|fun|fyi|goo|got|green|here|horse|how|ieee|jot|joy|like|limited|makeup|meme|mint|moi|moto|monster|now|nowruz|ong|onl|ooo|page|pars|pid|pink|play|plus|read|red|reise|reisen|rocks|safe|safety|seek|select|sky|smile|spot|sucks|talk|top|trust|uno|vin|vodka|web|wed|win|winners|wow|wtf|xyz|yamaxun|you|zero|abudhabi|africa|alsace|amsterdam|aquitaine|arab|barcelona|bayern|berlin|boston|broadway|brussels|budapest|bzh|capetown|cologne|corsica|country|cymru|desi|doha|dubai|durban|earth|eus|gent|hamburg|helsinki|international|irish|istanbul|joburg|kiwi|koeln|kyoto|london|madrid|market|melbourne|miami|nagoya|nrw|nyc|okinawa|osaka|paris|persiangulf|place|quebec|rio|roma|ryukyu|saarland|scot|shia|stockholm|stream|swiss|sydney|taipei|tatar|thai|tirol|tokyo|vegas|vlaanderen|wales|wanggou|wien|world|yokohama|zuerich|clinic|dental|dentist|docs|doctor|health|healthcare|hiv|hospital|med|medical|pharmacy|physio|rehab|surgery|auto|autos|bio|boats|cars|cleaning|consulting|design|energy|industries|motorcycles|adult|baby|beauty|beknown|best|bet|bingo|bom|cards|community|contact|dad|diy|dog|express|family|fan|fans|fashion|garden|gay|giving|group|guide|hair|halal|hiphop|imamat|jetzt|kid|kids|kim|kinder|latino|lgbt|lifestyle|style|living|love|luxe|luxury|moda|mom|navy|pets|poker|porn|republican|vip|vision|vote|voting|voto|wedding|feedback|film|media|mov|movie|movistar|music|pictures|radio|show|song|theater|theatre|tunes|video|accountant|accountants|analytics|bank|banque|broker|cash|cashbackbonus|cfd|cpa|credit|creditcard|finance|financial|financialaid|fund|gold|gratis|investments|ira|loan|loans|markets|money|mortgage|mutual|mutualfunds|pay|reit|prime|security|yun|abogado|airforce|archi|architect|army|attorney|author|dds|engineer|engineering|esq|law|lawyer|legal|retirement|vet|apartments|casa|case|forsale|haus|homes|lease|property|realestate|realtor|realty|room|baseball|basketball|coach|cricket|fish|fishing|fit|fitness|football|game|games|golf|hockey|juegos|mls|rodeo|rugby|run|ski|soccer|sport|sports|spreadbetting|surf|team|tennis|yoga|app|box|chat|click|cloud|comsec|bot|data|date|dev|digital|download|drive|call|fail|help|host|hosting|map|mobile|network|online|phone|report|search|secure|site|software|space|storage|tube|webcam|webs|website|weibo|zip|active|casino|christmas|hangout|hoteis|hotel|hoteles|hotels|meet|party|tour|tours|bible|church|catholic|faith|indians|islam|ismaili|memorial|moscow|actor|bar|black|build|care|city|direct|immo|ink|life|tax|town|work|one|insure|saxo|sex|weber|world|yandex|ovh)';
		$regexp_ru = '(?:рф|дети|онлайн|сайт|укр|католик|ком|москва|орг|рус|бел)';
		$_w = '[^\b\s.\/\\!-,:-@\[-`{-¿]';

		return "(?:$_w{1})(?:(?:\.?$_w))*\.(?:$regexp_en|$regexp_ru)\/?";
	}

	static function valid_domain($domain){
		$domain = urlFromPuny($domain);

		$regexp = self::get_domain_regexp();
		return preg_match("/^$regexp$/ui", $domain);
	}

	static function ellipsis($string, $max_length = 20, $pos = 1){
		// pos = 0 - начало, 1 - середина, 2 - конец
		if(mb_strlen($string, 'utf-8') <= $max_length) return $string;

		if($pos == 0){
			$string = mb_substr($string, mb_strlen($string, 'utf-8') - $max_length, $max_length, 'utf-8');
			$string = '...'.$string;
		}else if($pos == 1){
			$string1 = mb_substr($string, 0, $max_length / 2, 'utf-8');
			$string2 = mb_substr($string, mb_strlen($string, 'utf-8') - $max_length / 2, ceil($max_length / 2), 'utf-8');
			$string = $string1.'...'.$string2;
		}else if($pos == 2){
			$string = mb_substr($string, 0, $max_length, 'utf-8');
			$string = $string.'...';
		}

		return $string;
	}

	static function highlightHtml(string $text, string $word){
		$word = str_replace(['[', ']', '(', ')', '{', '}', '.', '+', '*', '\\', '/'], ['\[', '\]', '\(', '\)', '\{', '\}', '\.', '\+', '\*', '\\', '\/'], $word);
		$text = preg_replace("/($word)/iu", '<span class="found">$1</span>', $text);

		return $text;
	}

	static function numberEnding($number, $ending0, $ending1, $ending2){
		// $ending0 - родительный падеж, множ. число (10 минут)
		// $ending1 - именительный падеж, ед. число (1 минута)
		// $ending2 - винительный падеж, множ. число (3 минуты)
		$num100 = $number % 100;
		$num10 = $number % 10;

		if($num100 >= 5 && $num100 <= 20){
			return $ending0;
		}else if($num10 == 0){
			return $ending0;
		}else if($num10 == 1){
			return $ending1;
		}else if($num10 >= 2 && $num10 <= 4){
			return $ending2;
		}else if($num10 >= 5 && $num10 <= 9){
			return $ending0;
		}else{
			return $ending2;
		}
	}

	// сумма прописью
	static function num2str($num, $lang = 'ru'){
		switch($lang){
			case 'ua':
				$nul = 'ноль';
				$ten = [
					['', 'один', 'два', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять'],
					['', 'одна', 'дві', 'три', 'чотири', 'п\'ять', 'шість', 'сім', 'вісім', 'дев\'ять'],
                ];
				$a20 = ['десять', 'одинадцять', 'дванадцять', 'тринадцять', 'чотирнадцять', 'п\'ятнадцять', 'шістнадцять', 'сімнадцять', 'вісімнадцять', 'дев\'ятнадцять'];
				$tens = [2 => 'двадцять', 'тридцять', 'сорок', 'п\'ятдесят', 'шістдесят', 'сімдесят', 'вісімдесят', 'дев\'яносто'];
				$hundred = ['', 'сто', 'двісті', 'триста', 'чотириста', 'п\'ятсот', 'шістсот', 'сімсот', 'вісімсот', 'дев\'ятсот'];

				// имена разрядов
				$unit = [
					['копійок', 'копійка', 'копійки', 1],
					['гривень', 'гривня', 'гривні', 0],
					['тисяч', 'тисяча', 'тисячі', 1],
					['мільйонів', 'мільйон', 'мільйона', 0],
					['мільярдів', 'мільярд', 'мільярда', 0],
                ];
			break;
			case 'ru':
			default:
				$nul = 'нуль';
				$ten = [
					['', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
					['', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'],
                ];
				$a20 = ['десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать'];
				$tens = [2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто'];
				$hundred = ['', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот'];

				// имена разрядов
				$unit = [
					['копеек', 'копейка', 'копейки', 1],
					['рублей', 'рубль', 'рубля', 0],
					['тысяч', 'тысяча', 'тысячи', 1],
					['миллионов', 'миллион', 'миллиона', 0],
					['миллиардов', 'миллиард', 'миллиарда', 0],
                ];
			break;
		}

		list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
		$out = [];
		if(intval($rub) > 0){
			foreach(str_split($rub, 3) as $uk => $v){ // по 3 символа
				if(!intval($v)) continue;

				$uk = sizeof($unit) - $uk - 1; // номер разряда
				$gender = $unit[$uk][3];

				list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));

				$out[] = $hundred[$i1]; # 100-999

				if($i2 > 1) $out[] = $tens[$i2].' '.$ten[$gender][$i3];# 20-99
				else $out[] = ($i2 > 0)?$a20[$i3]:$ten[$gender][$i3];# 10-19 | 1-9
				// разрадяы без подписи (без "рублей" и "копеек")
				if($uk > 1) $out[] = common::numberEnding($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
			} // foreach
		}else{
			$out[] = $nul;
		}

		$out[] = common::numberEnding(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // рублей
		$out[] = $kop.' '.common::numberEnding($kop, $unit[0][0], $unit[0][1], $unit[0][2]); // копеек

		$out = implode(' ', $out);
		#$out = preg_replace('/{2,}/', ' ', $out);
		return trim($out);
	}
	// /сумма прописью

	static function getWeekdayName($n, $full = false){
		if(!$n) $n = 7;
		if($n > 7) return;
		$n--;

		if(!$full){
			$names = [L('CALENDAR_Dw_1'), L('CALENDAR_Dw_2'), L('CALENDAR_Dw_3'), L('CALENDAR_Dw_4'), L('CALENDAR_Dw_5'), L('CALENDAR_Dw_6'), L('CALENDAR_Dw_7')];
		}else{
			$names = [L('CALENDAR_Day_of_week_1'), L('CALENDAR_Day_of_week_2'), L('CALENDAR_Day_of_week_3'), L('CALENDAR_Day_of_week_4'), L('CALENDAR_Day_of_week_5'), L('CALENDAR_Day_of_week_6'), L('CALENDAR_Day_of_week_7'),];
		}

		return $names[$n];
	}

	static function invert_keyboard_layout($string){
		$a1 = 'qwertyuiop[]asdfghjkl; zxcvbnm,./QWERTYUIOP{}ASDFGHJKL;"ZXCVBNM<>?'; // символ "'" - запрещен
		$a2 = 'йцукенгшщзхъфывапролджэячсмитьбю.ЙЦУКЕНГШЩЗХЪФЫВАПРОЛДжЭЯЧСМИТЬБЮ,';

		$a1 = preg_split("//u", $a1, -1, PREG_SPLIT_NO_EMPTY);
		$a2 = preg_split("//u", $a2, -1, PREG_SPLIT_NO_EMPTY);

		$new_str = str_replace($a1, $a2, $string);
		if($new_str != $string) return $new_str;

		$new_str = str_replace($a2, $a1, $string);
		return $new_str;
	}

	static function translit($str){
		$tr = [
			"А"=>"A","Б"=>"B","В"=>"V","Г"=>"G",
			"Д"=>"D","Е"=>"E","Ж"=>"J","З"=>"Z","И"=>"I",
			"Й"=>"Y","К"=>"K","Л"=>"L","М"=>"M","Н"=>"N",
			"О"=>"O","П"=>"P","Р"=>"R","С"=>"S","Т"=>"T",
			"У"=>"U","Ф"=>"F","Х"=>"H","Ц"=>"TS","Ч"=>"CH",
			"Ш"=>"SH","Щ"=>"SCH","Ъ"=>"","Ы"=>"YI","Ь"=>"",
			"Э"=>"E","Ю"=>"YU","Я"=>"YA","а"=>"a","б"=>"b",
			"в"=>"v","г"=>"g","д"=>"d","е"=>"e","ж"=>"j",
			"з"=>"z","и"=>"i","й"=>"y","к"=>"k","л"=>"l",
			"м"=>"m","н"=>"n","о"=>"o","п"=>"p","р"=>"r",
			"с"=>"s","т"=>"t","у"=>"u","ф"=>"f","х"=>"h",
			"ц"=>"ts","ч"=>"ch","ш"=>"sh","щ"=>"sch","ъ"=>"y",
			"ы"=>"yi","ь"=>"","э"=>"e","ю"=>"yu","я"=>"ya",
			" "=>"_",":"=>"_"
        ];
		return strtr($str,$tr);
	}

	// возвращает строку в нижнем регистре
	static function retranslit($str){
		$str = strtolower($str);

		$combinations4 = [
			"aire" => "эр", "augh" => "о", "eigh" => "эй", "ewer" => "оуэр",
			"oore" => "ор", "ough" => "ау", "ower" => "ауэр"
		];
		$str = strtr($str, $combinations4);

		$combinations3 = [
			"air" => "эр", "ayr" => "эр", "are" => "эр", "cia" => "шия",
			"ear" => "эр", "eer" => "ир", "eir" => "ир", "ere" => "ир",
			"ewe" => "ю", "iar" => "айар", "ier" => "айер", "igh" => "ай",
			"ire" => "айр", "irr" => "ирр", "oar" => "ор", "oor" => "ор",
			"ore" => "ор", "our" => "ор", "tch" => "тч", "wor" => "уэр"
		];
		$str = strtr($str, $combinations3);

		$combinations2 = [
			"ae" => "э", "ai" => "эй", "ay" => "и", "al" => "ол",
			"au" => "о", "aw" => "о", "ch" => "ч", "ck" => "к",
			"ea" => "и", "ee" => "и", "ei" => "и", "eo" => "е",
			"er" => "эр", "eu" => "ю", "ew" => "ю", "ey" => "и",
			"gg" => "дж", "gh" => "ф", "ia" => "ия", "ie" => "айе",
			"io" => "е", "ir" => "эр", "oa" => "оу", "oe" => "и",
			"oi" => "ой", "oy" => "ой", "oo" => "у", "ou" => "оу",
			"ow" => "оу", "ph" => "ф", "qu" => "к", "sh" => "ш", "ss" => "з",
			"th" => "т", "ts" => "тс", "tz" => "ц", "ue" => "e", "ui" => "и",
			"ur" => "эр", "ya" => "я", "yr" => "эр"
		];
		$str = strtr($str, $combinations2);

		$oneSymbols = [
			"a" => "а", "b" => "б", "c" => "к", "d" => "д", "e" => "и",
			"f" => "ф", "g" => "г", "h" => "х", "i" => "и", "j" => "дж",
			"k" => "к", "l" => "л", "m" => "м", "n" => "н", "o" => "о", "p" => "п",
			"q" => "к", "r" => "р", "s" => "с", "t" => "т", "u" => "у",
			"v" => "в", "w" => "в", "x" => "кс", "y" => "и", "z" => "з"
		];
		$str = strtr($str, $oneSymbols);

		return $str;
	}

	// убрать 4 байтные символы
	static function stripUTF8mb4($string){
		return preg_replace('/[\x{10000}-\x{10FFFF}]/u', '', $string);
	}

	static function insertSelect($name, $options_html = '', $value_label = '', $selected_value = '', $disabled = false, $add_changer = false){
		if(is_array($options_html)){
			$options_array = $options_html;
			$options_html = '';
			foreach($options_array as $value => $label) $options_html .= '<option value="'.$value.'">'.$label.'</option>';
		}

		if($selected_value !== ''){
			$selected_value = str_replace(['.', '/'], ['\.', '\/'], $selected_value);
			$options_html = preg_replace('/<option[^>]+value="'.$selected_value.'"/', '$0 selected', $options_html);
		}

		$changer_html = '';
		if($add_changer) $changer_html = '<span class="icon-loop changer"></span>';

		$html = '
			<label class="select'.($value_label?' exists_default':'').($disabled?' disabled':'').' '.$name.'">
				<span class="text">'.$value_label.'</span>
				'.$changer_html.'
				<span class="arrow icon-caret-down"></span>
				<select name="'.$name.'" '.($disabled?' disabled':'').'>'.$options_html.'</select>
			</label>
		';

		return $html;
	}

	static function insertSelectSearch($name, $selected_value = false, $prefix_data = false, $ajax_module = false, $ajax_func = false, $style = '', $min_length = 0, $limit = 30){
		$input = '<input type="text" name="'.$name.'" placeholder="'.L('Search').'" data-selector="true" data-selector_one="true"
			data-min_length="'.$min_length.'"
			data-limit="'.$limit.'"
			data-module="'.$ajax_module.'" data-func="'.$ajax_func.'"';
		if($selected_value) $input .= ' data-selected_value=\''.str_replace("'", "\\'", $selected_value).'\'';
		if($prefix_data) $input .= ' data-prefix_data=\''.str_replace("'", "\\'", $prefix_data).'\'';
		if($style) $input .= ' style="'.$style.'"';
		$input .= '>';

		return $input;
	}

	static function insertCheckbox($name, $value = '', $label = '', $checked = false, $disabled = false){
		$name_html = ($name !== '')?'name="'.$name.'" ':'';
		$checked_html = ($checked)?'checked ':'';
		$disabled_html = ($disabled)?'disabled ':'';

		$html = '
			<label class="checkbox '.$checked_html.$disabled_html.' '.$name.'">
				<span class="icons"></span>
				<input type="checkbox" value="'.$value.'" '.$name_html.$checked_html.$disabled_html.'/>
				'.$label.'
			</label>
		';

		return $html;
	}

	static function insertRadio($name, $value = '', $label = '', $checked = false, $disabled = false){
		$name_html = ($name !== '')?'name="'.$name.'" ':'';
		$checked_html = ($checked)?'checked ':'';
		$disabled_html = ($disabled)?'disabled ':'';

		$html = '
			<label class="radio '.$checked_html.$disabled_html.' '.$name.'">
				<i class="icons"></i>
				<input type="radio" value="'.$value.'" '.$name_html.$checked_html.$disabled_html.'/>
				'.$label.'
			</label>
		';

		return $html;
	}

	static function insertRadioGroup(string $name, array $values, $selectedValue = false){
		$html = '<i class="radio_group">';
		foreach($values as $value => $label){
			$checked = ($value == $selectedValue);
			$html .= '<label><input name="'.$name.'" type="radio" value="'.$value.'" '.($checked?'checked':'').'><i>'.$label.'</i></label>';
		}
		$html .= '</i>';

		return $html;
	}

	static function insertRulerSelector($name, $from, $to, $selected_value = ''){
		$name_html = ($name !== '')?'name="'.$name.'" ':'';

		$html = '<div class="ruler_selector ruler_selector-'.$name.'">';
		$html .= '<input type="hidden" '.$name_html.' value="'.$selected_value.'">';
		for($i = $from; $i <= $to; $i++){
			$active = ($i == $selected_value)?'active':'';
			$line = ($i != $to)?'<span></span>':'';

			$html .= '<div class="el '.$active.'"><a href=".">'.$i.'</a>'.$line.'</div>';
		}
		$html .= '</div>';
		$html .= '<br><br>';

		return $html;
	}

	// вставка значений переменных в шаблон
	static function template_render($template, array $data){
		foreach($data as $name => $val){
			if(is_array($val)){
				foreach($val as $subName => $subVal){
					if(is_array($subVal)){
						foreach($subVal as $subName2 => $subVal2){
							if(is_array($subVal2)) continue;

							$template = str_replace('%'.$name.'['.$subName.']'.'['.$subName2.']%', $subVal2, $template);
						}
					}else{
						$template = str_replace('%'.$name.'['.$subName.']%', $subVal, $template);
					}
				}
			}else{
				$template = str_replace("%$name%", $val, $template);
			}
		}

		return $template;
	}

	static function tpl_render($template, array $data){
		return self::template_render($template, $data);
	}

	// получить интервал дат (даты в формате объектов)
	static function get_array_of_dates($date1, $date2, $type = 'P1D'){
		if(!$date1 and ! $date2) return;
		if(!$date1) $date1 = $date2;
		if(!$date2) $date2 = $date1;

		$interval = new DateInterval($type);
		$period = new DatePeriod($date1, $interval, $date2);
		$arrayOfDates = array_map(function($item){
			return $item->format('Y-m-d');
		}, iterator_to_array($period));
		return $arrayOfDates;
	}

	// получить список дат
	static function gen_dates($date = '', $count = 31, $step = -86400){
		$for_month = (abs($step) >= 86400 * 28);

		if(!$date) $date = $for_month?date('Y-m-01'):date('Y-m-d');
		$date_format = $for_month?'Y-m-01':'Y-m-d';

		$dates = [$date];

		$count = abs((int)$count);
		while(--$count){
			$date = date($date_format, strtotime($date) + $step);
			$dates[] = $date;
		}

		return $dates;
	}

	static function getOS($userAgent = ''){
		if(!$userAgent) $userAgent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';
		$oses = [
			'iOS' => '(iPhone|iPad)',
			'Android' => '(Android)',
			'WP' => '(IEMobile)',
			'Windows 3.11' => 'Win16',
			'Windows 95' => '(Windows 95)|(Win95)|(Windows_95)',
			'Windows 98' => '(Windows 98)|(Win98)',
			'Windows 2000' => '(Windows NT 5.0)|(Windows 2000)',
			'Windows XP' => '(Windows NT 5.1)|(Windows XP)',
			'Windows 2003' => '(Windows NT 5.2)',
			'Windows Vista' => '(Windows NT 6.0)|(Windows Vista)',
			'Windows 7' => '(Windows NT 6.1)|(Windows 7)',
			'Windows 8' => '(Windows NT 6.[23])|(Windows 8)',
			'Windows 10' => '(Windows NT 10)',
			'Windows NT 4.0' => '(Windows NT 4.0)|(WinNT4.0)|(WinNT)|(Windows NT)',
			'Windows ME' => 'Windows ME',
			'Open BSD'=>'OpenBSD',
			'Sun OS'=>'SunOS',
			'Linux'=>'(Linux)|(X11)',
			'Macintosh'=>'(Mac_PowerPC)|(Macintosh)|(Mac OS X 10.4)|(Mac OS X 10.5)|(Mac OS X 10.6)',
			'QNX'=>'QNX',
			'BeOS'=>'BeOS',
			'OS/2'=>'OS\/2',
			'Search Bot'=>'(nuhk)|(Googlebot)|(Yammybot)|(Openbot)|(Slurp\/cat)|(msnbot)|(ia_archiver)|(AdsBot-Google)',
			'Topvisor App iOS'=>'CFNetwork'
        ];

		foreach($oses as $os => $pattern){
			if(preg_match("/$pattern/i", $userAgent)){
				return $os;
			}
		}

		return 'Unknown';
	}

	static function getBrowser($userAgent = ''){
		if(!$userAgent) $userAgent = isset($_SERVER['HTTP_USER_AGENT'])?$_SERVER['HTTP_USER_AGENT']:'';

		switch(true){
			case (strpos($userAgent, 'Topvisor_API') !== false): $browser = 'topvisor-api'; break;
			case (strpos($userAgent, 'Topvisor_Android') !== false): $browser = 'android-app'; break;
			case (strpos($userAgent, 'Topvisor_iOS') !== false): $browser = 'ios-app'; break;
			case (strpos($userAgent, 'Topvisor') !== false): $browser = 'ios-app'; break;
			case (strpos($userAgent, 'Firefox') !== false): $browser = 'Firefox'; break;
			case (strpos($userAgent, 'OPR') !== false): $browser = 'Opera'; break;
			case (strpos($userAgent, 'YaBrowser') !== false): $browser = 'Ya'; break;
			case (strpos($userAgent, 'NMTE') !== false): $browser = 'IE'; break;
			case (strpos($userAgent, 'Chrome') !== false): $browser = 'Chrome'; break;
			case (strpos($userAgent, 'Safari') !== false): $browser = 'Safari'; break;
			default: $browser = '?';
		}

		return $browser;
	}

}

function redirect($url = '/', $notice_popup = false){
	if($notice_popup){
		$hash = preg_replace('/^[^#]+/', '', $url);
		$url = preg_replace('/#.*/', '', $url);

		$url .= ((strpos($url, '?') === false)?'?':'&').'notice_popup='.base64_encode($notice_popup);
		$url .= '&notice_popup_hash='.md5(base64_encode($notice_popup).TV::$config['sault']);

		if($hash) $url .= $hash;
	}

	$url = preg_replace('/\s/', '%20', $url);

    if(is_array($url)) $url = '/';

	header('Location: '.$url);
	exit();
}

function vd($var, $stop = 0){
	echo '<pre>';
	var_dump($var);
	echo '</pre>';

	if($stop) exit();
}

function vd_to_file($var = '', $include_requests = false){
	if($var === '' and ! $include_requests) return;

	ob_start();

	$filename = core()->folderLogs.'/vd_to_file.log';

	echo "============================================== ".date('Y.m.d H:i:s')." ==============================================\n";

	if(isset($_SERVER['SERVER_NAME'])) echo $_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];

	$data = [];
	if($var !== '') $data['$var'] = $var;
	if($include_requests){
		$data['$_GET'] = $_GET;
		$data['$_POST'] = $_POST;

		$phpInput = file_get_contents('php://input');
		if($phpInput) $data['php://input'] = $phpInput;
	}

	echo "\n\n==== json =====\n";
	echo json_encode($data, JSON_UNESCAPED_UNICODE);

	echo "\n\n==== var_dump =====\n";
	var_dump($data);

	$data = ob_get_contents();
	$data = trim($data);
	$data .= "\n";

	file_put_contents($filename, $data, FILE_APPEND);

	ob_end_clean();
}

function mCheck($inMessage = false){
	$mCheck = [
		'usage' => memory_get_usage(),
		'realUsage' => memory_get_usage(true),
		'peakUsage' => memory_get_peak_usage(),
		'realPeakUsage' => memory_get_peak_usage(true)
	];

	$mCheckDelta = [];
	foreach(common::$mCheckLast as $index => $val) $mCheckDelta[$index] = numberWhithWord($mCheck[$index] - $val, 4, 1024);

	common::$mCheckLast = $mCheck;

	if($inMessage) core()->message($mCheckDelta);

	return $mCheckDelta;
}

function mInfo($inMessage = false){
	$info = [
		'usage' => numberWhithWord(memory_get_usage(), 4, 1024),
		'realUsage' => numberWhithWord(memory_get_usage(true), 4, 1024),
		'peakUsage' => numberWhithWord(memory_get_peak_usage(), 4, 1024),
		'realPeakUsage' => numberWhithWord(memory_get_peak_usage(true), 4, 1024)
	];

	if($inMessage) core()->message($info);

	return $info;
}
