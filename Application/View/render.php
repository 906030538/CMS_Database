<?php
class View {
	function __construct() {
		if(!defined('INDEX')) {
			throw new Exception('Illegal request.', ILLEGAL_ACCESS);
		}
	}
	static function Error ($msg) :void {
		echo $msg;
	}
	// Articles
	static function article (array $result) :void {
		$res = [
			'Id' => $result['Id'],
			'Title' => $result['Title'],
			'Content' => $result['Content'],
			'Creator' => $result['CreatorUserId'],
			'CreatorName' => $result['CreatorUserName'],
			'CreationTime' => $result['CreationTime'],
			'Modifier' => $result['LastModifierUserId'],
			'ModifierName' => $result['LastModifierUserName'],
			'ModifyTime' => $result['LastModificationTime']
		];
		echo '';
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
		echo '';
	}
	static function redirect(string $path) :void {
		header('Location: '.$path);
	}
	static function home() {
		echo '';
	}
}