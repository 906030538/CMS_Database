<?php
class pageManager {
	private $cmsPageRepo;
	private $cmsPageViewRepo;
	private $cmsTemplateRepo;
	private $cmsTemplateViewRepo;

	function __construct() {
		if(!defined('INDEX')) {
			throw new Exception('Illegal request.', ILLEGAL_ACCESS);
		}

		$this->cmsPageRepo = new cmsPageRepository();
		$this->cmsPageViewRepo = new cmsPageViewRepository();
		$this->cmsTemplateRepo = new cmsTemplateRepository();
		$this->cmsTemplateViewRepo = new cmsTemplateViewRepository();
	}
	// Pages
	public function getPageById(int $Id) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->cmsPageRepo->select(['Id', 'Name', 'Data', 'Commit', 'LastModificationTime'], $where)[0];
	}
	public function getPageByName(string $name) {
		$where = new b_query(new q_eq('Name', $name));
		return $this->cmsPageRepo->select(['Id', 'Name', 'Data', 'Commit', 'LastModificationTime'], $where)[0];
	}
	public function getPages() {
		return $this->cmsPageRepo->select(['Id', 'Name', 'Data', 'Commit', 'LastModificationTime']);
	}
	public function createPage (string $Name, string $Data, string $Commit) :int {
		$this->cmsPageRepo->insert(['Name', 'Data', 'Commit'], [$Name, $Data, $Commit]);
		return $lastId;
	}
	public function updatePage (int $Id, string $Data, string $Commit) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->cmsPageRepo->update([new v_eq('Data', $Data), new v_eq('Commit', $Commit)], $where);
	}
	public function deletePage(int $Id) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->cmsPageRepo->delete($where);
	}
	// Templates
	public function getTemplateById(int $Id) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->cmsTemplateRepo->select(['Id', 'Name', 'RootId', 'ParentId', 'Data', 'Template', 'Commit','CreatorUserId', 'LastModificationTime'], $where)[0];
	}
	public function getTemplateByName(string $name) {
		$where = new b_query(new q_eq('Name', $name));
		return $this->cmsTemplateRepo->select(['Id', 'Name', 'RootId', 'ParentId', 'Data', 'Template', 'Commit','CreatorUserId', 'LastModificationTime'], $where)[0];
	}
	public function getTemplateTrees(int $RootId) {
		$where = new b_query(new q_eq('RootId', $RootId));
		return $this->cmsTemplateRepo->select(['Id', 'Name', 'RootId', 'ParentId', 'Data', 'Template', 'Commit','CreatorUserId', 'LastModificationTime'], $where);
	}
	public function getTemplateRoots() {
		$where = new b_query(new q_ne('ParentId', null));
		return $this->cmsTemplateRepo->select(['Id', 'Name', 'RootId', 'ParentId', 'Data', 'Template', 'Commit','CreatorUserId', 'LastModificationTime'], $where);
	}
	public function getTemplates() {
		return $this->cmsTemplateRepo->select(['Id', 'Name', 'RootId', 'ParentId', 'Data', 'Template', 'Commit','CreatorUserId', 'LastModificationTime']);
	}
	public function createTemplate (string $Name, int $RootId, int $ParentId, string $Data, string $Template, string $Commit) :int {
		$this->cmsTemplateRepo->insert(['Name', 'RootId', 'ParentId', 'Data', 'Template', 'Commit'], [$Name, $RootId, $ParentId, $Data, $Template, $Commit]);
		return $lastId;
	}
	public function updateTemplate (int $Id, string $Name, string $Data, string $Template, string $Commit) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->cmsTemplateRepo->update([new v_eq('Name', $Name), new v_eq('Data', $Data), new v_eq('Template', $Template), new v_eq('Commit', $Commit)], $where);
	}
	public function deleteTemplate(int $Id) {
		$where = new b_query(new q_eq('Id', $Id));
		return $this->cmsTemplateRepo->delete($where);
	}
}
?>