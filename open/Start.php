<?php 
include __DIR__. '/Autoload.php';
// use \Autoload;
$config = include __DIR__. '/../config/config.php';
// 加载配置
class Start  {
	public static $auto;
	public static function autoload() {
		self::$auto = new Autoload();
		$config = $GLOBALS['config'];
		self::$auto->setMap($config['map']);
	}

	// 加载对应的控制器和控制器中对应的方法, 命名空间
	public static function rooter() {
		// www.text.com?l=admin&c=index&a=user    -> \indexControllor
		$local = isset($_GET['l']) ? $_GET['l'] : 'index' ;
		if (!isset($_GET['c'])) {
			$controller = $_GET['c'] = $local === 'index' ? 'index' : 'login';
		} else {
			$controller = $_GET['c'];
		}
		if (!isset($_GET['a'])) {
			$method = $_GET['a'] = $local === 'index' ? 'show' : 'login';

		} else {
			$method = $_GET['a'];
		}

		$class = "\\$local\\controller\\". ucfirst($controller). 'Controller';
		if ($class === '\\admin\\controller\\IndexController') {
			$class = '\\admin\\controller\\UserController';
			$method = 'login';
		}
		$instance = new $class;
		if ($method) {
			call_user_func([$instance, $method]);
		}
	}
}



