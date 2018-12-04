<?
	if (defined('CMS_SERVER_RENDER') && !filter_var(CMS_SERVER_RENDER, FILTER_VALIDATE_BOOLEAN)) {
		include 'Application/View/ajax.php';
		header('Content-Type: application/json');
	} else {
		include 'Application/View/render.php';
	}
	// $View = new View();