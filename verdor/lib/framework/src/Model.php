<?php 
namespace framework;

class Model {

	protected $host;
	protected $username;
	protected $password;
	protected $charset;
	protected $dbName;
	protected $link;
	public $sql;

	public $option;
	public $table;
	protected static $instance;

	public function __construct() {
		$config = $GLOBALS['config']['db'];
		$this->host     = $config['host'];
		$this->username = $config['username'];
		$this->password = $config['password'];
		$this->charset  = $config['charset'];
		$this->dbName   = $config['dbName'];
		$this->getTable();
		$this->getLink();
		$this->init();
	}
	protected function getTable() {
		$class = get_class($this);
		$index = strrpos($class, '\\');
		$class = substr($class, $index+1);
		$user = strtolower(substr($class, 0, -5));

		$this->table = $user;
		
	}
	// public function static model() {
	// 	if (!empty(self::$instance)) {
	// 		return self::$instance;
	// 	}

	// 	self::$instance = new self($GLOBALS['db']);
	// 	return self::$instance;
	// }

	// private function __clone() {}

	protected function init() {
		$this->option = [
			'where' => '',
			'group' => '',
			'having'=> '',
			'order' => '',
			'limit' => ''
		];
	}

	protected function getLink() {
		$link = mysqli_connect($this->host, $this->username, $this->password, $this->dbName);
		if (!$link) {
			exit('链接数据库错误:'. __file__. ' on line '. __line);
		}
		if (!mysqli_set_charset($link, $this->charset)) {
			exit('设置字符集错误:'. __file__. ' on line '. __line);
		}
		$this->link = $link;
		return $this;
	}

	public function query() {
		$query = mysqli_query($this->link,$this->sql);
		return $query;
	}

	// username 或 ['username', 'password']
	public function select($info) {
		is_array($info) && $info = join(',', $info);
		extract($this->option);
		$sql = 'select '. $info.' from '. $this->table;
		$sql .= $where. $group. $having. $order. $limit;
		$this->init();
		$this->sql = $sql;
		$query = $this->query($sql);
		if (!$query) {
			exit('select错误:'. __FILE__. ' on line '. __LINE__. ' sql: '. $sql);
		}
		if (!$query->num_rows) {
			return null;
		}
		while($row = mysqli_fetch_assoc($query)) {
			$result[] = $row;
		}
		return $result;
	}

	// insert into user(id,ni) values();
	public function insert($info, $getId=true) {
		$this->init();
		if (!is_array($info)) {
			exit('只能传入关联数组:'. __FILE__. ' on line '. __LINE__);
		}
		$key = join(',' ,array_keys($info));
		foreach($info as $v) {
			if (is_string($v)) {
				$v = '\''. htmlentities($v). '\'';
			}
			$value[] = $v;
		}
		$value = join(',', $value);
		$sql = "insert into $this->table($key) values($value)";
		$result = mysqli_query($this->link, $sql);
		if ($result && mysqli_affected_rows($this->link) > -1) {
			return $getId ? mysqli_insert_id($this->link) : true;
		}
		echo 'error: '. $sql;
		return false;
	}

	// delete from bbs_user where
	public function delete() {
		$sql = "delete from $this->table". $this->option['where'];
		$this->init();
		$result = mysqli_query($this->link,$sql);
		if (!$result) {
			exit('delete错误:'. __FILE__. ' on line '. __LINE__. ' sql: '. $sql);
		}
		return $result;
	}

	// update id=1,username='nihao' from username where id=1;
	public function update($info) {
		if (is_array($info)) {
			foreach ($info as $k => $v) {
				if (is_string($v)) {
					$v = '\''. htmlentities($v). '\'';
				}
				$result[] = "$k=$v";
			}
			$info = join(',', $result);
		} 
		$sql = "update $this->table set $info". $this->option['where'];
		$result = mysqli_query($this->link, $sql);
		$this->init();
		if (!$result) {
			exit('update错误:'. __FILE__. ' on line '. __LINE__. ' sql: '. $sql);
			// return $
		}
		return $result;
	}

	//'id=5' ; [id=>5, username=>'nihao']
	public function where($where) {
		if (is_array($where)) {
			foreach ($where as $k => $v) {
				if (is_string($v)) {
					$v = '\''. $v. '\'';
				}
				$result[] = "$k=$v";
			}
			$where = ' where '. join(' and ', $result);
		} else {
			$where = is_string($where) ? ' where '. $where : '';
		}
		$this->option['where'] = $where;
		return $this;
	}
	// 数组形式: [order]
	public function order($info, $sort = true) {
		$sort = $sort ? 'asc' : 'desc';
		if (is_array($info)) {
			$order = join(',', $info);
			$order = " order by $order";
		} else {
			$order = " order by $info $sort";
		}
		$this->option['order'] = $order;
		return $this;
	}
	public function group($info) {
		$group = " group by $info";
		$this->option['group'] = $group;
		return $this;
	}
	public function limit($info) {
		if (!is_string($info)) {
			exit('limit接收字符串:'. __file__. ' on line '. __line__);
		}
		$this->option['limit'] = " limit $info";
		return $this;
	}
	public function having($info) {
		$having = " having $having";
		$this->option['having'] = $having;
		return $this;
	}
}
