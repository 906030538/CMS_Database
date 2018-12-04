<?php
	$articleManager = new articleManager();
	$resutl = null;
	switch ($action) {
		case 'l':
		case 'list':
			$resutl = $articleManager->getArticleList();
			View::articleList($resutl);
			break;
		case 's':
		case 'find':
		case 'search':
			$word = isset($_GET['word']) ?? '';
			$content = (boolean)(isset($_GET['content']) ?? false);
			if ($content)
				$resutl = $articleManager->findArticlesContent($word);
			else
				$resutl = $articleManager->findArticlesTitle($word);
			View::articleList($resutl);
			break;
		case 'v':
		case 'view':
			$aid = isset($_GET['id']) ? (int)$_GET['id'] : 0;
			$resutl = $articleManager->getArticle($aid);
			View::article($resutl);
			break;
		default:
			throw new Exception('Unknow Action: '.$action, UNKNOW_ACTION);
	}