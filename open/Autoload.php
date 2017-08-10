<?php 
// 自动加载类
class Autoload {
	public $map = [];
	public function __construct() {
		spl_autoload_register([$this, 'autoload']);

	}

	// 定义自动加载的对应关系, 自动加载include
	public function autoload($className) {
		$index = strrpos($className, '\\');
		$class = substr($className, $index+1). '.php';
		$space = substr($className, 0, $index);
		$filePath = 'app/'. str_replace('\\', '/', ltrim($space, '/') ). '/'. $class;
		$filePath = file_exists($filePath) ? $filePath : 
		(isset($this->map[$space]) ? $this->map[$space]. $class : $filePath);
		//var_dump($filePath);
		if (!file_exists($filePath)) {
			exit($filePath. ' 该文件不存在');
		}
		include $filePath;
	}
	public function setMap($map) {
		$this->map = $map;
	}

}