<?php 
namespace framework;
/**
 * 分类类:
 * 1. 得到首页url
 * 2. 上一页url
 * 3. 下一页url
 * 4. 尾页url
 * 5. 跳页
 * 6. 得到偏移量
 */
class Page {
	private $infoTotal;        // 总信息数
	private $infoSize;         // 每页显示信息数
	private $pageTotal;        // 总页数
	private $page;	           // 所在页面
	private $limit;     	   // 偏移量

	public function __construct($infoTotal, $infoSize) {
		$this->infoTotal = $infoTotal;
		$this->infoSize  = $infoSize;
		$this->pageTotal = ceil($infoTotal / $infoSize);
		$this->page      = $this->getPage();
		$this->limit     = (($this->page - 1) * $infoSize). ','. $infoSize;
	}
 
	public static function pageData($infoTotal, $infoSize) {
		$page = new self($infoTotal,$infoSize);
		return [
			'first' => $page->first(),
			'prev'  => $page->prev(),
			'next'  => $page->next(),
			'end'   => $page->end(),
			'all'   => $page->all(),
			'page' => $page->page,
			'pageTotal' => $page->pageTotal,
			'limit' => $page->limit
		];
	}
	// 首页
	public function first() {
		return $this->setUrl(1);
	}

	// 上一页
	public function prev() {
		return $this->setUrl($this->page - 1);
	}

	// 下一页
	public function next() {
		return $this->setUrl($this->page + 1);
	}

	// 尾页
	public function end() {
		return $this->setUrl($this->pageTotal);
	}

	//每一页
	public function all() {
		for($i = 1; $i <= $this->pageTotal; $i++) {
			$all[] = $this->setUrl($i);
		}
		return $all;
	}

	// 修正page
	protected function amend($page) {
		return min(max($page,1), $this->pageTotal);
	}


	// 得到从数据库取得时候的偏移量
	public function limit() {
		$this->limit = (($this->page - 1) * 3). ','. $this->infoSize;
		$limit = " limit $this->limit";
		return $limit;
	}

	// 设置url
	public function setUrl($page) {
		$page = $this->amend($page);
		$url = 'http://'. $_SERVER['SERVER_NAME']. ':'. $_SERVER['SERVER_PORT'];
		$info = parse_url($_SERVER['REQUEST_URI']);
		$url .= $info['path'];
		if ( !empty($info['query']) ) {
			parse_str($info['query'], $query);
			unset($query['page']);
			$query['page'] = $page;
			$url .= '?'.http_build_query($query);
		} else {
			$url .= "?page=$page";
		}
		return $url;
	}

	public function echoInfo() {
		$result  = "infoTotal(信息总数): $this->infoTotal<br>";
		$result .= "infoSize(每页长度): $this->infoSize<br>";
		$result .= "pageTotal(总页数): $this->pageTotal<br>";
		$result .= "page(第几页): $this->page<br>";
		$result .= "limit(偏移量): $this->limit<br>";
		echo $result;
	}

	// 得到现在页数
	public function getPage() {
		// var_dump($_GET['page']);
		$result = isset($_GET['page']) ? $this->amend((int)$_GET['page']) : 1;
		return $result;
	}
	// 得到信息总数
	public function getInfoTotal($dbTool ,$info, $table) {
		return $dbTool->select("count($info) count", $table)[0]['count'];
	}
}


