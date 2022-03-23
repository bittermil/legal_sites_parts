<?

require_once(__DIR__.'/../inc/config.inc');

require_once(__DIR__.'/../inc/session.inc');
$SESSION_NAME = 'SESSION_PANEL';
$Sess = new sessions($SESSION_NAME);

include(__DIR__.'/TV.php');
include(__DIR__.'/common.php');

include(__DIR__.'/core.php');

Core::$core = new Core();
core()->include_core_modules();

$login = '';
$pass = '';
if(isset($_SESSION['login'])) $login = $_SESSION['login'];
if(isset($_SESSION['pass'])) $pass = $_SESSION['pass'];

$passHash = md5($pass);

$admin = NULL;
$administrator = NULL;
if($login != '' and $login != ''){
	$sql = "SELECT * FROM `users` WHERE `name` LIKE '$login' AND `password` = '$passHash'";
	$admin = dbh()->query($sql)->fetch();

	$admin['prava'] = ($admin['users_id'] == 0)?0:(($admin['usergroups'] == 12)?1:2);
	$admin['isAdmin'] = ($admin['prava'] == 0 or $admin['prava'] == 1);

	if($admin['landings']) $admin['landings'] = explode("\n", $admin['landings']);
	if(!$admin['landings']) $admin['landings'] = [];
	foreach($admin['landings'] as &$_landing) $_landing = trim($_landing);

	// prava == 0: Суперадминистратор
	// prava == 1: Администратор
	// prava == 2: Менеджер
	if(!$admin['name']) $admin = NULL;

	Core::$i_admin = $admin['isAdmin'];
	Core::$admin = $admin;

	$administrator = $admin['users_id'];
}