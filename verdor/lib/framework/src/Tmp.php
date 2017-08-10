<?php
namespace framework;
// 用法: $tmp = new Tmp; $tmp->setVars(['data'=>'nihao'])->tmp('admin.html');
class Tmp {
	public $cacheDir;
	public $tmpDir;
	public $vars = [];
	public $update;

	public function __construct($type=0, $update=30000) {
		$this->tmpDir   = $type ? $GLOBALS['config']['TMP_INDEX_DIR'] : $GLOBALS['config']['TMP_ADMIN_DIR'];
		$this->cacheDir = $type ? $GLOBALS['config']['CACHE_INDEX_DIR'] : $GLOBALS['config']['CACHE_ADMIN_DIR'];
		$this->update   = $update;
	}

	/**
	 * 模板引擎
	 * @param string $tmpFile 文件名
	 * @return mixed 返回成功写入的字节数, 或false
	 */
	public function tmp($tmpFile = '') {
		$tmpUrl   = $this->tmpDir. $tmpFile;
		$cacheUrl = $this->cacheDir. $tmpFile;
		// 1.检查模板文件是否存在
		if (!file_exists($tmpUrl)) {
			exit($tmpUrl. ' 模板文件不存在') ;
		}

		// 1.1 检查缓存文件是否存在, 是否需要更新, 如果不需要, 直接引入模板文件
		if ($this->checkCache($tmpFile)) {
			extract($this->vars);
			include $cacheUrl;
			$this->vars = [];
			return $this;
		}

		// 2.按规则替换模板中的内容
		$content = $this->compile($tmpUrl);
		// 4. 导入模板
		$dir = dirname($cacheUrl);
		if (!file_exists($dir) ) {
			mkdir($dir);
		}
		$result = file_put_contents($cacheUrl, $content);
		if(!$result) return false;
		extract($this->vars);
		include $cacheUrl;
		$this->vars = [];
		return $this;
	}

	public function setVars($info) {
		$this->vars = $info;
		return $this;
	}


	// 缓存
	public function cache($tmpFile = '') {
		$tmpUrl   = $this->tmpDir. $tmpFile;
		$cacheUrl = $this->cacheDir. $tmpFile;
		// 1.检查模板文件是否存在
		if (!file_exists($tmpUrl)) {
			exit($tmpUrl. ' 模板文件不存在') ;
		}

		// 1.1 检查缓存文件是否存在, 是否需要更新, 如果不需要, 直接引入模板文件
		if ($this->checkCache($tmpFile)) {
			return true;
		}

		// 2.按规则替换模板中的内容
		$content = $this->cacheCompile($tmpUrl);
		// 4. 导入模板
		$dir = dirname($cacheUrl);
		if (!file_exists($dir) ) {
			mkdir($dir);
		}
		$result = file_put_contents($cacheUrl, $content);
		if(!$result) return false;
		return true;
	}
	/**
	  * 检查缓存文件
	  */ 
	public function checkCache($tmpFile) {
		$tmpUrl   = $this->tmpDir. $tmpFile;
		$cacheUrl = $this->cacheDir. $tmpFile;
		// 判断缓存文件是否存在
		if (!file_exists($cacheUrl)) return false;
		// 判断是否过期
		if ( (filectime($cacheUrl) + $this->update) > time()) return false;
		// 判断模板文件是否已经修改
		if ( filemtime($tmpDir) < filemtime($cacheUrl) ) return false;
		return true;
	}

	// 缓存规则替换
	public function cacheCompile($tmpFile) {
		// var_dump($info);
		//读取文件内容
		$content = file_get_contents($tmpFile);
		//定义编译规则
		$rule = [
			//表达式赋值
			'/\{%=(.+)%\}/U' => 'var_export(',
			// //表达式
			// '/\{%(\s*\$.+=.+)%\}/U' => '<?',
			// //输出
			// '/\{%(\s*\$.+)%\}/U' => '<?=',
			// //其他
			// '/\{%(.+)%\}/U' => '<?'
		];
		foreach ($rule as $k => $v) {
			//替换
			$content = preg_replace_callback($k, function($matches) use($v){
				extract($this->vars);
				$var = ltrim($matches[1], '$');
				$result = var_export($$var, true);
				$result = trim($result,'\'');
				return $result;
			}, $content);
		}
		return $content;
	}
	/**
	 * 编译模板
	 * @param  string $tmpFile 文件地址
	 * @return string 编译后的内容
	 */
	public function compile($tmpFile) {
		//读取文件内容
		$content = file_get_contents($tmpFile);
		//定义编译规则
		$rule = [
			//表达式赋值
			'/\{%=(.+)%\}/U' => '<?=',
			//表达式
			'/\{%(\s*\$.+=.+)%\}/U' => '<?',
			//输出
			'/\{%(\s*\$.+)%\}/U' => '<?=',
			//其他
			'/\{%(.+)%\}/U' => '<?'
		];
		foreach ($rule as $k => $v) {
			//替换
			$content = preg_replace_callback($k, function($matches) use($v){
				return $v.$matches[1].'?>';
			}, $content);
		}
		return $content;
	}
}







