<?php 
namespace framework;
/**
 * 生成验证码的类
 */
class Verify {
	public $width;
	public $height;
	public $canvas;
	public $code;
	public $suffix;

	public function __construct($width, $height, $len=4, $suffix='png') {
		$this->height = $height;
		$this->width  = $width;
		$this->len    = $len; 
		$this->suffix  = $suffix;
	}
	public function __destruct() {
		imagedestroy($this->canvas);
	}

    
	public static function yzm($width = 200, $height = 100) {
		$yzm = new self($width, $height);
		$yzm->init();
		return $yzm->code;
	}

	// 初始化
	public function init() {
		// 生成画布
		$this->createCanvas();
		// 生成干扰线
		$this->code = $this->createCode();
		// 生成随机字符串
		$this->disturb();
		// 字符串画在画布上
		$this->paintCode();
		// 呈现在页面中
		$this->display();
	}

	// 生成画布
	protected function createCanvas() {
		$this->canvas = imagecreatetruecolor($this->width, $this->height);
		imagefill($this->canvas, 0, 0, $this->randColor(100, 150));
	}
	// 生成随机字符串
	protected function createCode() {
		$str = '1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM';
		return substr(str_shuffle($str), 0, $this->len);
	}
	// 将字符串画在画布上
	protected function paintCode() {
		$codeArr = str_split($this->code);
		$length  = count($codeArr);
		$offset  = $this->width / $length;
		for ($i=0; $i < $length; $i++) {
			$x = rand($i * $offset, ($i+1) * $offset - 12);
			$y = rand(0, $this->height - 20);
			imagechar($this->canvas, 5, $x, $y, $codeArr[$i], $this->randColor(0, 80));
		}
	}
	// 生成干扰点
	protected function disturb() {
		for ($i = 0; $i < 200; $i++) {
			$x = rand(0, $this->width);
			$y = rand(0, $this->height);
			imagesetpixel($this->canvas, $x, $y, $this->randColor(150, 255));
		}
	}
	// 显示
	protected function display() {
		header('content-Type:image/'. $this->suffix);
		$func = 'image'. $this->suffix;
		if (!function_exists($func)) exit($this->suffix. '不符合要求');
		imagepng($this->canvas);
	}


	// 生成随机颜色
	protected function randColor($start, $end) {
		$temp1 = rand($start, $end);
		$temp2 = rand($start, $end);
		$temp3 = rand($start, $end);
		return imagecolorallocate($this->canvas ,$temp1, $temp2, $temp3);
	}
}
// VerifyCode::yzm(100, 20);