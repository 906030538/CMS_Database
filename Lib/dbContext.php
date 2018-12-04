<?php
	if(!defined('INDEX')) {
		throw new Exception('Illegal request.', ILLEGAL_ACCESS);
	}
	global $db;
	define('TBALE_CONFIG', $db['pfix'].'Config');
	class configRepository extends Repository {
		const TABLE_NAME = TBALE_CONFIG;
	}
	define('TBALE_USER', $db['pfix'].'User');
	class userRepository extends Repository {
		const TABLE_NAME = TBALE_USER;
	}
	define('TBALE_TOKEN', $db['pfix'].'Token');
	class tokenRepository extends Repository {
		const TABLE_NAME = TBALE_TOKEN;
	}
	define('TBALE_TENANT', $db['pfix'].'Tenant');
	class tenantRepository extends Repository {
		const TABLE_NAME = TBALE_TENANT;
	}
	define('TBALE_ARTICLE', $db['pfix'].'Article');
	class articleRepository extends Repository {
		const TABLE_NAME = TBALE_ARTICLE;
	}
	define('TBALE_ARTICLECOMMIT', $db['pfix'].'ArticleCommit');
	class articleCommitRepository extends Repository {
		const TABLE_NAME = TBALE_ARTICLECOMMIT;
	}
	define('TBALE_BINARYOBJECT', $db['pfix'].'BinaryObject');
	class binaryObjectRepository extends Repository {
		const TABLE_NAME = TBALE_BINARYOBJECT;
	}
	define('TBALE_ARTICLEFILE', $db['pfix'].'ArticleFile');
	class articleFileRepository extends Repository {
		const TABLE_NAME = TBALE_ARTICLEFILE;
	}
	define('TBALE_CMSPAGE', $db['pfix'].'CMSPage');
	class cmsPageRepository extends Repository {
		const TABLE_NAME = TBALE_CMSPAGE;
	}
	define('TBALE_CMSTEMPLAT', $db['pfix'].'CMSTemplat');
	class cmsTemplatRepository extends Repository {
		const TABLE_NAME = TBALE_CMSTEMPLAT;
	}
	define('TBALE_ROLE', $db['pfix'].'Role');
	class RoleRepository extends Repository {
		const TABLE_NAME = TBALE_ROLE;
	}
	define('TBALE_USERROLE', $db['pfix'].'UserRole');
	class userRoleRepository extends Repository {
		const TABLE_NAME = TBALE_USERROLE;
	}
	// Views Repo
	define('VIEW_ARTICLE', $db['pfix'].'ArticleView');
	class articleViewRepository extends Repository {
		const TABLE_NAME = VIEW_ARTICLE;
	}
	define('VIEW_ARTICLECOMMI', $db['pfix'].'ArticleCommitView');
	class articleCommitViewRepository extends Repository {
		const TABLE_NAME = VIEW_ARTICLECOMMI;
	}
	define('VIEW_CMSPAGE', $db['pfix'].'CMSPageView');
	class cmsPageViewRepository extends Repository {
		const TABLE_NAME = VIEW_CMSPAGE;
	}
	define('VIEW_CMSTEMPLATE', $db['pfix'].'CMSTemplateView');
	class cmsTemplateViewRepository extends Repository {
		const TABLE_NAME = VIEW_CMSTEMPLATE;
	}
	
	class softDelete {
		private static function sqlexec(string $spname, $param) {
			$rs = $conn->prepare('EXEC :spname :param');
			$rs->bindValue(':spname', $spname, PDO::PARAM_STR);
			$rs->bindValue(':param', $param);
			$rs->execute();
		}
		static function enable() {
			self::sqlexec($db['pfix'].'Soft_Delete_Trigge', 1);
		}
		static function disable() {
			self::sqlexec($db['pfix'].'Soft_Delete_Trigge', 0);
		}
		static function clean() {
			self::sqlexec($db['pfix'].'Clean_Deleted');
		}
	}
?>