<?php
	if(!defined('INDEX')) {
		throw new Exception('Illegal request.', ILLEGAL_ACCESS);
	}
	$configManager = new configManager();
	
	global $config;
	$config = $configManager->gerConfig();
	
	foreach ($config as $k => $i) {
		if (!defined($k)) define($k, $i);
	}
?>