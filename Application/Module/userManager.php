<?php
class userManager {
	private $userRepo;
	function __construct() {
		if(!defined('INDEX')) {
			throw new Exception('Illegal request.', ILLEGAL_ACCESS);
		}
		
		$this->userRepo = new userRepository();
	}

	public function getUser(int $id) {
		$where = new b_query(new q_eq('Id', $id));
		$result = $this->userRepo->select(['Id', 'Name', 'Data', 'Commit', 'CreationTime', 'LastModificationTime'], $where);
		if ($result) return $result[0];
		return null;
	}

	public function updateUser(int $id, $data) {
	}
}
?>