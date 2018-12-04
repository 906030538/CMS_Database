<?php
class articleManager {
	private $articleRepo;
	private $articleViewRepo;
	private $articlePublicViewRepo;
	private $articleCommitRepo;
	private $articleCommitViewRepo;
	private $articleFileRepo;
	private $binaryObjectRepo;
	private $userManager;

	function __construct() {
		if(!defined('INDEX')) {
			throw new Exception('Illegal request.', ILLEGAL_ACCESS);
		}
		$this->articleRepo = new articleRepository();
		$this->articleViewRepo = new articleViewRepository();

		$this->articleCommitRepo = new articleCommitRepository();
		$this->articleCommitViewRepo = new articleCommitViewRepository();

		$this->userManager = new userManager();
		
		// $this->articleFileRepo = new articleFileRepository();
		// $this->binaryObjectRepo = new binaryObjectRepository();
	}
	private function userIdToName(array $result) :array {
		foreach ($result as $k => $i) {
			if ($i['CreatorUserId'] != null) {
				$user = $this->userManager->getUser($i['CreatorUserId']);
				$result[$k]['CreatorUserName'] = $user['Name'];
			}
			if ($i['LastModifierUserId'] != null) {
				$user = $this->userManager->getUser($i['LastModifierUserId']);
				$result[$k]['LastModifierUserName'] = $user['Name'];
			}
		}
		return $result;
	}
	// Articles
	public function getAllArticles() {
		$result =  $this->articleViewRepo->select();
		return $result;
	}
	public function getArticleList() {
		$result =  $this->articleViewRepo->select();
		return $result;
	}
	public function getArticle(int $Id) {
		$where = new b_query(new q_eq('Id', $Id));
		$result = $this->articleRepo->select(null, $where);
		$result = $this->userIdToName($result);
		return $result[0];
	}
	public function findArticlesTitle(string $word) :array {
		$where = new b_query(new q_like('Title', '%'.$word.'%'));
		$result =  $this->articleRepo->select(null, $where);
		$result = $this->userIdToName($result);
		return $result;
	}
	public function findArticlesContent(string $word) :array {
		$where = new b_query(new q_like('Content', '%'.$word.'%'));
		$where->OR(new q_like('Title', '%'.$word.'%'));
		$result =  $this->articleRepo->select(null, $where);
		$result = $this->userIdToName($result);
		return $result;
	}
	public function createArticle(string $title, string $content) :int {
		$this->articleRepo->insert(['Title', 'Public', 'Content'], [$title, 0, $content]);
		return $lastId;
	}
	public function updateArticle(int $Id, string $title, string $content) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->articleRepo->update([new v_eq('Title', $title), new v_eq('Content', $content)], $where);
	}
	public function publishArticle(int $Id) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->articleRepo->update([new v_eq('Public', 1)], $where);
	}
	public function deleteArticle(int $Id) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->articleRepo->delete($where);
	}
	// Commits
	private function buildtree(array $list, array $tree) {
		foreach ($tree as $n) {
			$n['Children'] = [];
			foreach ($list as $i) if ($n['Id'] == $i['Pid']) array_push($n['Children'], $i);
			buildtree($list, $n['Children']);
		}
	}
	public function getCommit(int $Id) :array {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->articleCommitViewRepo->select(['Id', 'Pid', 'Title', 'Content', 'CreatorUserId', 'CreationTime', 'LastModifierUserId', 'LastModificationTime'], $where)[0];
	}
	public function getArticleCommits(int $Id) :array {
		$where = new b_query(new q_eq('Aid', $Id));
		return $this->articleCommitRepo->select(['Id', 'Pid', 'Title', 'Content', 'CreatorUserId', 'CreationTime', 'LastModifierUserId', 'LastModificationTime'], $where);
	}
	public function getArticleCommitTree(int $Id) :array {
		$where = new b_query(new q_eq('Aid', $Id));
		$tree = $this->articleCommitViewRepo->select(['Id', 'Pid', 'Title', 'Content', 'CreatorUserId', 'CreationTime', 'LastModifierUserId', 'LastModificationTime'], $where);
		foreach ($list as $i) if (is_null($i['Pid'])) array_push($tree, $i);
		buildtree($list, $tree);
		return $tree;
	}
	public function createArticleCommit(int $Aid, int $Pid = null, string $Commit) :int {
		$this->articleCommitRepo->insert(['Aid', 'Pid', 'Commit'], [$Aid, $Pid, $Commit]);
		return $lastId;
	}
	public function updateArticleCommit(int $Id, string $Commit) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->articleCommitRepo->update([new v_eq('Commit', $Commit)], $where);
	}
	public function deleteCommit(int $Id) :array {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->articleCommitRepo->delete($where);
	}
}
?>