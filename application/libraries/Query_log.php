<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * [日志 操作存储]
 */
class Query_log {
	// dir_name所在目录名称
	private $_dir_path = '';
	// 日志文件名
	private $_name     = '';
	// 文件后缀
	private $_suffix   = '.txt';
	// 文件权限
	private $_mode     = 0775;
	// 需要被记录的sql类型
	private $_type     = 'update,delete';

	public function __construct() {
		$this->_dir_path = FCPATH . '/MysqlLog/';
		// 初始化日志文件名
		$this->_name = $this->_get_dir_name();
	}

	/**
	 * 设置存放日志的路径
	 */
	public function set_path($path) {
		$this->_dir_path = $path;
		return $this;
	}

	/**
	 * 设置文件名
	 */
	public function set_name($name) {
		$this->_name = $name;
		return $this;
	}

	/**
	 * 设置文件后缀
	 */
	public function set_suffix($suffix) {
		$this->_suffix = $suffix;
		return $this;
	}

	/**
	 * 设置文件权限
	 */
	public function set_mode($mode) {
		$this->_mode = $mode;
		return $this;
	}

	/**
	 * 设置哪些类型sql需要被记录到日志
	 */
	public function set_type($type) {
		$this->_type = $type;
		return $this;
	}

	/**
	 * 新增日志记录
	 * @param $data 
	 */
	public function add_log($sql, $result = true) {
		// 过滤掉不需要记录的日志
		$this->_filter_log($sql);
		if(empty($sql)) {
			return false;
		}

		$user = $this->_get_user();
		$record = array(
				'aid'    => $user['aid'],
				'account'=> $user['account'],
				'sql'    => $sql,
				'result' => $result,
				'created_at' => date('Y-m-d H:i:s'), // 创建时间
			);
		// 创建目录
		if(!is_dir($this->_dir_path)) {
			mkdir($this->_dir_path, $this->_mode, true);
 		}
		
		// 写入文件
		$string = $record['created_at'] . PHP_EOL . serialize($record);
		file_put_contents($this->_dir_path . $this->_name . $this->_suffix, $string . PHP_EOL . PHP_EOL, FILE_APPEND | LOCK_EX);
	}

	/**
	 * 通过sql过滤掉不需要记录的日志
	 * @param [string] $query [sql语句]
	 */
	private function _filter_log(&$query) {
		if(!empty($query)) {
			$return = false;
			$type   = explode(',', $this->_type);
			foreach ($type as $val) {
				if(empty($val)) {
					continue;
				}
				// 只将对数据库的修改和删除记录到日志
				if(stripos($query, trim($val)) !== FALSE) {
					$return = $query;
				}
			}
			$query = $return;
			unset($return);
		}
	}
	
	/**
	 * 根据当前时间生成对应日志目录名称
	 * @return [string] $return [返回的名称 eg: 2017-07-27]
	 */
	private function _get_dir_name() {
		return date('Y-m-d', time());
	}

	/**
	 * 获取当前用户数据
	 * @return mixd
	 */
	private function _get_user() {
		// 获取当前用户数据
		if(file_exists(BASEPATH . 'libraries/Session/Session.php')) {
			if(!class_exists('CI_Session')) {
				require_once(APPPATH . 'libraries/Session/Session.php');
			}

			$session = new CI_Session();
			return $session->userdata();
		}

		return false;
	}
}