<?php
	if(!defined('INDEX')) {
		throw new Exception('Illegal request.', ILLEGAL_ACCESS);
	}
	define('DB', 1);

	if(file_exists('config.inc.php')){
		include 'config.inc.php';
	} else {
		header('Location: /install', TRUE, 302);
		exit();
	}
	global $conn;
	global $db;
	try {
		switch ($db['type']) {
			case 'ODBC':
				$conn = new PDO("odbc:DSN=".$db['host'].";", $db['user'], $db['pass']);
				break;
			case 'MSSQL':
				$conn = new PDO("sqlsrv:server=tcp:".$db['host'].",".$db['port']."; Database=".$db['name'], $db['user'], $db['pass']);
				break;
			case 'MYSQL':
				// $conn = new mysqli($db['host'], $db['user'], $db['pass']);
				$conn = new PDO("mysql:host=".$db['host']."; port=".$db['port']."; dbname=".$db['name']."; charset=utf8", $db['user'], $db['pass']);
				break;
			case 'SQLITE':
				$conn = new PDO("sqlite:".$db['host']);
				break;
			default:
		}
	} catch (PDOException $e) {
		throw new Exception('Connection failed: ' . $e->getMessage(), DATABASE_ERROR);
	}
	include 'Lib/query.php';
	include 'Lib/entity.php';
	include 'Lib/dbContext.php';
?>