<?php
	define('FILTER', 3);
	function filter($pram) {
		if (is_array($pram))
			foreach($pram as $k => $i) $pram[$k] = filter($i);
		else
			return addslashes(trim($pram));
	}
	// $_REQUEST = filter($_REQUEST);
	$_GET = filter($_GET);
	$_POST = filter($_POST);
	$_COOKIE = filter($_COOKIE);
	// mysql_real_escape_string()
?>