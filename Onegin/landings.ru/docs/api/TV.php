<?

// TV class
class TV{

	static $config = [
		'db_main' => [
			'dbtype' => 'mysql',
			'dbhost' => mysql_server,
			'dblocalhost' => mysql_server,
			'dbname' => mysql_db,
			'dbuser' => mysql_login,
			'dbpass' => mysql_password
		]
	];
	static $isReserve = false;
	static $isRobot = false;
	static $isDev = false;

}

include(__DIR__.'/TV.debug.php');