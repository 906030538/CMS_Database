<?php
	define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));
	define('INDEX', 0);

	include 'Lib/Exception.php';
	include 'Lib/db.inc.php';
	include 'Application/Module/include.php';
	include 'Lib/dataConfig.php';
	include 'Application/View/include.php';
	// include 'Lib/filter.inc.php';
	try {
		// with php7.2
		// $module = isset($_GET['module']) ?? '';
		// $action = isset($_GET['action']) ?? '';
		$module = isset($_GET['module']) ? $_GET['module'] : '';
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		switch ($module) {
			case 'login':
			case 'view':
			case 'edit':
				require 'Application/Controller/'.$module.'.php';
				break;
			case 'home':
			case '':
				require 'Application/Controller/home.php';
				break;
			default:
				throw new Exception('Unknow Module: '.$module, UNKNOW_MODULE);
		}
	} catch (Exception $e) {
		View::Error($e);
		var_dump($e);
	}
?>