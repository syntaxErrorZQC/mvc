<?php 
// 网址
define('WEB_ADDR', 'http://www.test.com/');
// 后台公共
define('WEB_ADMIN_PUBLIC', 'http://www.test.com/public/admin/');
// 前台公共
define('WEB_INDEX_PUBLIC', 'http://www.test.com/public/index/');
// 图片存储位置
define('IMG_DIR', 'public/images/');
$config = [
	'TEMPLATE_CACHE' => 'cache/',
	'TMP_INDEX_DIR' => 'app/index/view/',
	'TMP_ADMIN_DIR' => 'app/admin/view/',
	'CACHE_INDEX_DIR' => 'cache/index/',
	'CACHE_ADMIN_DIR' => 'cache/admin/',
	
];
$config['db'] = [
		'host' => '192.168.195.133', 
		'username' => 'root', 
		'password' => '123456',
		'charset' => 'utf8',
		'dbName' => 'blog'
];	
$config['map'] = ['framework' => 'verdor/lib/framework/src/'];
return $config;