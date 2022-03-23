<?

// в C++: переменные из TVDebug доступны в пределах файла
class TVDebug{

	static $timestamps = [];

}

function tStart(){
	TVDebug::$timestamps[] = microtime(true);
}

//function tCheck(bool $reset = false, bool $inMessage = false): float{
function tCheck($reset = false){
	$startTimestamp = array_pop(TVDebug::$timestamps);
	$sec = (microtime(true) - $startTimestamp);

	if($reset){
		tStart();
	}else{
		TVDebug::$timestamps[] = $startTimestamp;
	}

//	if($inMessage) core()->message($sec);

	return $sec;
}

function tStop($stop = false){
	if(!isset(TVDebug::$timestamps)) TVDebug::$timestamps = [];

	$sec = tStopValue();
	echo '<!--Время выполнения: '.($sec * 1000).' ms-->';

	if($stop) exit();
}

function tStopValue(){
	if(!isset(TVDebug::$timestamps)) TVDebug::$timestamps = [];

	$startTimestamp = array_pop(TVDebug::$timestamps);
	return (microtime(true) - $startTimestamp);
}

function tTest($count, $callback){
	tStart();

	for($i = 0; $i < $count; $i++) $callback();

	return tStopValue();
}
