<?php 
namespace framework;
/**
 * 图片处理类
 * 1. 图片放大缩小  setSize
 * 2. 图片加水印    watermark
 * 3. 获得图片大小  getImgSize
 */ 
class ImgDeal {
	public $filePath;
	public $width;
	public $height;
	public $fileSuffix;

	public function __construct($filePath, $width=null, $height=null) {
		$this->filePath = $filePath;
		// 检查类型, 后缀, 路径
		if ( !file_exists($this->filePath) ) {
			exit('该图片路径错误'. __file__. ' on line '. __line__);
		}
		// 检查后缀
		$suffix = ['png', 'jpg', 'jpe', 'jpeg', 'gif', 'bmp'];
		$fileSuffix = image_type_to_extension(getimagesize($filePath)[2], false);
		if ( !in_array($fileSuffix, $suffix) ) {
			exit('文件后缀不匹配');
		}
		// 检查宽高, 至少传一个
		if (!$width && !$height) {
			exit('宽高至少传一个'. __file__. ' on line '. __line__);
		}
		$this->fileSuffix = $fileSuffix;
		$this->width    = $width;
		$this->height   = $height;
		return $this->setSize();
	}

	/**
	 * 图片放大缩小
	 * @param $filePath  图片路径
	 * @param $path 	 新路径
	 */ 
	public function setSize($path=null) {
		if ($this->width === null && $this->height === null) {
			return $this;
		}
		// 获得图片大小
		list($ditWidth, $ditHeight) = getimagesize($this->filePath);
		$oldSize = ['width' => $ditWidth, 'height' => $ditHeight];
		$this->getNewSize($oldSize);
		// 获取原图资源
		$func = 'imagecreatefrom'. $this->fileSuffix;
		$oldLink = $func($this->filePath);
		// 创建一个画布
		$canvas = imagecreatetruecolor($this->width, $this->height);
		// 把图片按比例画在画布中
		imagecopyresampled($canvas, $oldLink, 0, 0, 0, 0, $this->width, 
			$this->height, $oldSize['width'], $oldSize['height']);
		// 写入路径
		$putFn = 'image'. $this->fileSuffix;
		$path = $path ? $path : $this->filePath;
		$result = $putFn($canvas, $path);
		// 关闭资源
		imagedestroy($canvas);
		imagedestroy($oldLink);

		return $this;
	}

	// 图片加水印
	public function watermark($srcPath, $place=1, $alpha=100) {
		// 得到图片, 水印图片的宽高,类型
		list($ditWidth, $ditHeight) = getimagesize($this->filePath);
		var_dump($this->filePath);
		list($srcWidth, $srcHeight, $srcType) = getimagesize($srcPath);
		// 获得源图片资源
		$func = 'imagecreatefrom'. $this->fileSuffix;
		$ditImg = $func($this->filePath);
		$func2 = 'imagecreatefrom'. image_type_to_extension($srcType, false);
		$srcImg = $func2($srcPath);
		// 得到位置
		list($x, $y) = $this->getSite($ditWidth, $ditHeight, $srcWidth, $srcHeight, $place);
		// 把水印画在源图片上
		imagecopymerge($ditImg, $srcImg, $x, $y, 0, 0, $srcWidth, $srcHeight, $alpha);
		// 覆盖原图片
		$func3 = 'image'. $this->fileSuffix;
		$func3($ditImg, $this->filePath);

		return $this;
	}

	// 获取新尺寸
	protected function getNewSize($oldSize) {
		// 假如只有宽
		if ( !$this->height ) {
			$temp = $oldSize['width'] / $this->width;
			$this->height = $oldSize['height'] / $temp;
		}
		// 假如只有高
		if ( !$this->width ) {
			$temp = $oldSize['height'] / $this->height;
			$this->width = $oldSize['width'] / $temp;
		}
	}

	// 得到水印位置
	protected function getSite($ditWidth, $ditHeight, $srcWidth, $srcHeight, $place) {
		$x = (($place-1) % 3) * ($ditWidth - $srcWidth) / 2;
		$y = (ceil($place / 3) - 1) * ($ditHeight - $srcHeight) / 2;
		return [$x, $y];
	}

	// 获取图片尺寸
	public function getImgSize($type='array') {
		list($ditWidth, $ditHeight) = getimagesize($this->filePath);
		$result = ['width' => $ditWidth, 'height' => $ditHeight];
		if ($type === 'string') {
			return "width: $ditWidth, height: $ditHeight";
		}
		return $result;
	}
}













