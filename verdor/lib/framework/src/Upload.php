<?php 
namespace framework;
/**
 * 文件上传类
 * 1. 处理单个上传
 * 2. 处理批量上传
 */
class Upload {
	protected $fileInfo;   // 上传的信息
	protected $type;       // 上传文件的类型限定
	protected $dir;        // 保存的目录

	public function __construct($type='img', $dir='/upload/') {
		$this->type = $type;
		$this->dir  = $dir;
	}
	// 处理单个上传
	public function singleUpload($fileName) {
		$this->fileInfo = $this->getFileInfo($fileName);
		// 检测上传的信息是否出错
		if ( $err = $this->checkErr($this->fileInfo['error']) ) {
			return $err;
		}
		// 检测类型和后缀
		if (!$this->checkType()) {
			return ['errno' => 400, 'msg' => '类型或后缀名不匹配'];
		}
		// 检测文件地址是否存在, 不存在创建
		if (!file_exists(IMG_DIR. $this->dir)) {
			mkdir($this->dir, 0755, true);
		}
		// 保存到地址
		$suffix = pathinfo($this->fileInfo['name'])['extension'];
		$subPath = rtrim($this->dir, '/'). '/'. uniqid(). '.'. $suffix;
		$path = IMG_DIR. $subPath;
		if(!move_uploaded_file($this->fileInfo['tmp_name'], $path)) {
			return ['errno' => 500, 'msg' => '文件没有储存成功'];
		}
		return ['errno' => 0, 'msg' => $subPath];
	}

	// 处理多个上传, 
	public function moreUpload() {
		// 得到$_FILES信息, 处理成非数组形式$_FILES信息形式的索引数组
		$temp = $this->dealFileInfo($this->fileInfo);
		$fileInfoArr = $temp['content'];
		$inx = 0;
		foreach ($fileInfoArr as $k => $v) {
			$result[$k] = $this->singleUpload($fileInfoArr[$k]);
			$result[$k]['index'] = $temp['index'][$k];
		}
		return $result;
	}
	
	/**
	 * 整理上传数组信息, 组成单个file信息形式的索引数组
	 */
	protected function dealFileInfo($temp) {
		$arr1 = array_keys($temp);
		$inx = 0;
		foreach ($temp['name'] as $k => $v) {
			// 去除空的项
			if (!empty($temp['name'][$k])) {
				// 得到有值的项的索引数组
				$arr2[$inx] = array_column($temp, $k, 'name');
				// 索引数组替换成关联数组
				$result['content'][$inx] = array_combine($arr1, $arr2[$inx]);
				$result['index'][$inx] = $k;
				$inx++;
			}
		}
		return $result;
	}

	// 得到上传信息
	protected function getFileInfo($fileName) {
		if ( !isset($_FILES[$fileName]) ) {
			return ['errno'=>10, 'error'=>'没有上传文件, 或文件名错误'];
			// exit('没有找到文件上传信息: '. __FILE__. ' in line '. __line__);
		}
		return $_FILES[$fileName];
	}

	// 检测上传类型, 和要求的类型是否匹配
	protected function checkType() {
		switch ($this->type) {
			case 'img':
				$types  = ['image/png', 'image/jpeg', 'image/gif', 'image/bmp'];
 	        	$suffix = ['png', 'jpg', 'jpe', 'jpeg', 'gif', 'bmp'];
 	        	break;
 	        default: 
 	        	return false;
		}
		if (!in_array($this->fileInfo['type'], $types)) {
			return false;
		}
		if (!in_array(pathinfo($this->fileInfo['name'])['extension'], $suffix)) {
			return false;
		}
		return ture;
	}

	// 处理$_FILES错误号
	protected function checkErr($errInfo) {
		switch ($errInfo) {
			case 0:
				return false;
			case 1:  
				return ['errno' => 1, 'error' => '上传文件大小超过服务器允许的最大值'];
			case 2:  
				return ['errno' => 2, 'error' => '上传文件大小超过表单允许的最大值'];
			case 3:  
				return ['errno' => 3, 'error' => '上传文件不完整'];
			case 4:  
				return ['errno' => 4, 'error' => '文件上传失败'];
			case 6:  
				return ['errno' => 6, 'error' => '找不到临时文件夹'];
			case 7:  
				return ['errno' => 7, 'error' => '文件写入失败'];
			default:
				return ['errno' => 666, 'error' => '出现未知错误'];
		}
	}

}