<?php
class View {
	function __construct() {
		if(!defined('INDEX')) {
			throw new Exception('Illegal request.', ILLEGAL_ACCESS);
		}
	}
	static function Error ($msg) :void {
		$res = ['error' => $msg];
		echo json_encode($res, JSON_FORCE_OBJECT);
	}
	// Articles
	static function article (array $result) :void {
		$res = [
			'Id' => $result['Id'],
			'Title' => $result['Title'],
			'Content' => $result['Content'],
			'Creator' => $result['CreatorUserId'],
			'CreatorName' => isset($result['CreatorUserName']) ? $result['CreatorUserName'] : null,
			'CreationTime' => $result['CreationTime'],
			'Modifier' => $result['LastModifierUserId'],
			'ModifierName' => isset($result['LastModifierUserName']) ? $result['LastModifierUserName'] : null,
			'ModifyTime' => $result['LastModificationTime']
		];
		echo json_encode($res, JSON_FORCE_OBJECT);
	}
	static function articleList (array $result) :void {
		$res = [];
		foreach ($result as $item) {
			array_push($res, [
				'Id' => $item['Id'],
				'Title' => $item['Title'],
				'Creator' => $item['CreatorUserId'],
				'CreatorName' => $item['CreatorUserName'],
				'CreationTime' => $item['CreationTime'],
				'Modifier' => $item['LastModifierUserId'],
				'ModifierName' => $item['LastModifierUserName'],
				'ModifyTime' => $item['LastModificationTime']
			]);
		}
		echo json_encode($res);
	}
	static function redirect(string $path) :void {
		$res = ['redirect' => $path];
		echo json_encode($res, JSON_FORCE_OBJECT);
	}
	static function home() {
		header('Location: /index.html');
	}
}