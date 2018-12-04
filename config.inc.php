<?php
	if(!defined('INDEX')) {
		throw new Exception('Illegal request.', ILLEGAL_ACCESS);
	}

	// Database connection type:
	//   ODBC: Open Database Connectivity
	//   MSSQL: Microsoft SQL Server
	//   MYSQL: MySQL/MariaDB
	//   SQLITE: Sqlite
	$dbtype = 'MSSQL';

	// Database connect address
	$dbhost = 'localhost';

	// Database port:
	//   for MSSQL, Default as 1433
	//   for MYSQL, Defautl as 3306
	$dbport = '1433';

	// Database user name
	$dbuser = 'cms';

	// Database user password, plain
	$dbpass = 'cms';

	// Database name
	$dbname = 'CMS_Database';

	// Sheets' perfix, to avoid mixup with other application when using ONE database
	//   Exmaple: When $perfix is 'App', the Sheet for User named 'AppUser'
	$dbperfix = 'App';

	global $db;
	$db = [
		'type' => $dbtype,
		'host' => $dbhost,
		'port' => $dbport,
		'user' => $dbuser,
		'pass' => $dbpass,
		'name' => $dbname,
		'pfix' => $dbperfix
	];
?>