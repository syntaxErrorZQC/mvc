<?php 
namespace admin\controller;
use framework\Tmp;
class Controller extends Tmp{
	public $userInfo;
	public function __construct($checkUser = true) {
		parent::__construct(0);
		// 检测登录信息, 如果没有登录, 跳回到登录页面
		if ($checkUser) {
			$this->checkLogin();
		}

	}
	//  将用户信息存入属性中, 方便使用
	function getUser() {
		$this->userInfo = $_SESSION['userInfo'];
	}
	function checkLogin() {
		if (!isset($_SESSION['login']) ) {
			header('location:' .WEB_ADDR. '?l=admin&c=login&a=login');
		} else {
			$this->getUser();
		}
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