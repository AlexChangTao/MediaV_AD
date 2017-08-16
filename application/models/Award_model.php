<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 奖项目模型
 */

class Award_model extends CI_Model {
	// 中奖
	static $on               = 1;
	// 未中奖
	static $off              = 2;
	// 规定表名称
	protected $_table_name   = 'award';
	// 数据库字段
	protected $_fields  	 = array();
	// 缓存的key
	protected $_cache_key    = 'lottery:award:info';
	/**
	 * 实例化
	 */
	public function __construct() {
		$this->_get_storage();
	}

	/**
	 * 获取实例
	 */
	private function _get_storage() {
		return $this->load->database();
	}
	
	/**
	 * [获取奖项信息]
	 * @return mixd
	 */
	public function get_award_cache($cache = true) {
		$result = false;
		if($cache) {
			$result = $this->get_cache();
			if(!empty($result)) {
				return $result;
			}
		}
		$arr = $this->_select(NULL);
		foreach($arr as $val) {
			if(empty($val)) {
				continue;
			}
			$result[$val['id']]['name']   = $val['name'];
			$result[$val['id']]['status'] = $val['status'];
		}
		$this->set_cache($result);
		return $result;
	}

	/**
	 * 根据条件查询记录
	 * @param [array] $map [筛选条件]
	 */
	private function _select($map) {
		return $this->db->get_where($this->_table_name, $map)->result_array();
	}

	/**
	 * 更新缓存
	 * @param [array] $value [需要缓存的值]
	 * @return mixd
	 */
	public function set_cache($value = FALSE) {
		$redis = get_redis_instance();
		// 无传入缓存值获取数据库的值
		if($value === FALSE) {
			$value = $this->get_award_cache(false);
		}
		
		return $redis->save($this->_cache_key, $value);
	}

	/**
	 * 获取缓存
	 * @return mixd
	 */
	public function get_cache() {
		$redis = get_redis_instance();
		return $redis->get($this->_cache_key);
	}
}