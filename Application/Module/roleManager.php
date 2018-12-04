<?php
class roleManager {
	private $roleRepo;
	private $userRoleRepo;

	function __construct() {
		if(!defined('INDEX')) {
			throw new Exception('Illegal request.', ILLEGAL_ACCESS);
		}

		$this->roleRepo = new roleRepository();
		$this->userRoleRepo = new userRoleRepository();
	}
}
?>