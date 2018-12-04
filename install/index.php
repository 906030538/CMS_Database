<?php
	if(file_exists('install.lock')){
		die('Application installed');
	}
	if(file_exists('../config.inc.php')){
		define('INDEX', '');
		include '../config.inc.php';
		
		global $db;
	}
	if (isset($_POST['install'])){
		$dbtype = $_POST['dbtype'];
		$dbhost = $_POST['dbhost'];
		$dbport = $_POST['dbport'];
		$dbuser = $_POST['dbuser'];
		$dbpass = $_POST['dbpass'];
		$dbname = $_POST['dbname'];
		$dbperfix = $_POST['dbperfix'];
		$db = [
			'type' => $dbtype,
			'host' => $dbhost,
			'port' => $dbport,
			'user' => $dbuser,
			'pass' => $dbpass,
			'name' => $dbname,
			'pfix' => $dbperfix
		];
	}
	if (isset($db)) {
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
			die('UNKNOW DB TYPE');
		}

		$sql = str_ireplace("__app__", $db['pfix'], file_get_contents('init_mssql.sql'));

		$conn->beginTransaction();
		foreach (explode("GO", $sql) as $l) if ($l) {
			$conn->exec($l);
			echo $conn->errorInfo()[2];
		}
		$conn->commit();

		$f = fopen('install.lock', 'w');
		fprintf($f, 'install');
		fclose($f);

		$f = fopen('../config.inc.php', 'w');
		fprintf($f, "<?php\n\tif(!defined('INDEX')) {\n\t\tthrow new Exception('Illegal request.', ILLEGAL_ACCESS);\n\t}");
		ob_start();
			var_export($db);
			$a = ob_get_contents();
		ob_end_clean();
		fprintf($f, "\tglobal \$db;\n\t\$db = %s;\n?>", $a);
		fclose($f);
		header('Location: /');
	} else {
		?>
		<html>
			<body></body>
		</html>
		<?
	}
?>