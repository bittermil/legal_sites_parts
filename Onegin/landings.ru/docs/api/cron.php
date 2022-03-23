<?

error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT);
ini_set('display_errors', 1);

include(__DIR__.'/api.php');

m('landings_2')->taro_tv_cronNoticeNeedAddEvent();
m('landings_2')->taro_breakfast_cronNoticeNeedAddEvent();

echo core()->getErrorsString();