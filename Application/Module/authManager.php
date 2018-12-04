<?php
class authManager {
	private $userRepo;
	private $tokenRepo;
	function __construct() {
		if(!defined('INDEX')) {
			throw new Exception('Illegal request.', ILLEGAL_ACCESS);
		}
		
		$this->userRepo = new userRepository();
		$this->tokenRepo = new tokenRepository();
	}

	private function passhash(string $name, string $pass, string $salt) :string {
		return sha1($name . md5($name . $salt . $pass));
	}

	public function login(string $user, string $pass) :boolean {
		if (filter_var($user, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => "/^[\u4e00-\u9fa5_a-zA-Z0-9]+$/"]])) {
			$Name = new q_eq('Name', $user);
		} else {
			throw new Exception('Illegal Name');
		}
		$where = new b_query($Name);
		if (filter_var($user, FILTER_VALIDATE_EMAIL)) {
			$Email = new q_eq('Email', $user);
			$where->OR($where, $Email);
		}
		$rs = ($this->userRepo->select(['Id', 'Name', 'Email', 'Passwd', 'Salt'], $where))[0];
		if ($rs['Passwd'] === passhash($user, $pass, $rs['Salt']))
			return true;
		else
			return false;
	}

	public function registe(string $name, string $email, string $pass) :boolean {
		if (!filter_var($name, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => "/^[\u4e00-\u9fa5_a-zA-Z0-9]+$/"]])) {
			throw new Exception('Illegal Name', ILLEGAL_PARAM);
		}
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			throw new Exception('Illegal Email', ILLEGAL_PARAM);
		}
		$salt = bin2hex(random_bytes(8));
		return $this->userRepo->insert(['Name', 'Email', 'Passwd', 'Salt'], [$name, $email, passhash($pass), $salt]);
	}

	public function genToken($user, $time = 3600, $authSource = null) :string {
		$Token = bin2hex(random_bytes(32));
		$now = time();
		$this->tokenRepo->insert(['Token', 'Uid', 'AuthSource', 'CreationTime', 'DeletionTime'], [$Token, $user->id, $authSource, $now, $now + $time]);
		return $Token;
	}
	// check is Token available
	//   -1 For not found
	//   userId For available
	public function checkToken(string $Token) :int {
		$where = new b_query(new q_eq('Token', $Token));
		// $to = new q_gt('DeletionTime', time());
		// $where->AND($to);
		$rs = $this->tokenRepo->select(['Uid', 'DeletionTime'], $where);
		if (count($rs) == 0) return LOGIN_ERROR;
		$rs = $rs[0];
		if ($rs['DeletionTime'] != null && time() > strtotime($rs['DeletionTime'])) return LOGIN_TIMEOUT;
		return $rs['Uid'];
	}

	public function logout(string $Token) {
		$where = new b_query(new q_eq('Token', $Token));
		$rs = $this->tokenRepo->delete($where);
	}
	public function authSource(string $Token) :string {
		$where = new b_query(new q_eq('Token', $Token));
		$rs = $this->tokenRepo->select(['AuthSource'], $where);
		if (count($rs) == 0) return LOGIN_ERROR;
		return $rs['AuthSource'];
	}
}
?>