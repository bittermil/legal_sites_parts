<?

require_once(__DIR__.'/../api/api.php');

include(dirname(__FILE__).'/landing.php');
include(dirname(__FILE__).'/db.php');

$landingName = req('landing');
$landing = new Landing($landingName);
include($landing->root.'/tpl/index.php');

$current_url = 'http://www.onegin-consulting.ru'.$_SERVER['REQUEST_URI'];
$current_url = urlencode($current_url);