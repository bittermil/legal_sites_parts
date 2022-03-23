<?

$root = dirname(dirname(__FILE__));

include_once($root."/inc/dbinit.inc");
$dbh->query("set names 'windows-1251'");

include_once($root."/admin2/mod/mysql.php");
mysql::$dbh = $dbh;

