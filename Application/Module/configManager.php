<?php
class configManager {
	private $configRepo;

	function __construct() {
		if(!defined('INDEX')) {
			throw new Exception('Illegal request.', ILLEGAL_ACCESS);
		}

		$this->configRepo = new configRepository();
	}

	public function gerConfig() {
		$res = $this->configRepo->select();
		$config = [];
		foreach ($res as $item) {
			$config[$item['Key']] = $item['Value'];
		}
		return $config;
	}
}
?>