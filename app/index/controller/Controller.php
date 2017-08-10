<?php 
namespace index\controller;
use framework\Tmp;
class Controller extends Tmp{
	public function __construct() {
		parent::__construct(1);
		// 检测登录信息
	}
	// 获取上一页路径
	function getPrev() {
		return $_SERVER['HTTP_REMOTE']. '?' .http_build_query($_GET);
	}
	// 获取本页路径
	function getPath($key, $value) {
		$url = 'http://'. $_SERVER['SERVER_NAME']. ':'. $_SERVER['SERVER_PORT'];
		$info = parse_url($_SERVER['REQUEST_URI']);
		$url .= $info['path'];
		if ( !empty($info['query']) ) {
			parse_str($info['query'], $query);
			unset($query[$key]);
			$query[$key] = $value;
			$url .= '?'.http_build_query($query);
		} else {
			$url .= "?$key=$value";
		}
		return $url;
	}
	
	public function tmp($tmpFile = '') {
		if (!$tmpFile) {
			$controller = isset($_GET['c']) ? $_GET['c'] : 'index' ;
			$tmpFile = $controller . '/'. 'index.html';
		} else {
			$controller = isset($_GET['c']) ? $_GET['c'] : 'index' ;
			$tmpFile = $controller . '/'. $tmpFile. '.html';
		}
		parent::tmp($tmpFile);
	}
	public function cache($tmpFile = '') {
		var_dump($tmpFile);
		if (!$tmpFile) {
			$controller = isset($_GET['c']) ? $_GET['c'] : 'index' ;
			$tmpFile = $controller . '/'. 'index.html';
		} else {
			$controller = isset($_GET['c']) ? $_GET['c'] : 'index' ;
			$tmpFile = $controller . '/'. $tmpFile. '.html';
		}
		var_dump($tmpFile);
		parent::cache($tmpFile);
	}
}