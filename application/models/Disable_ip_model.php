<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 活动站限制ip模型
 */

class Disable_ip_model extends CI_Model {

	// 规定表名称
	protected $_table_name   = 'disable_ip';
	// 数据库字段
	protected $_fields   = array(
								'id',
								'ip',
								'activity_id',
								'created_at',
								'updated_at'
							);

	// eg: lottery:activity_id:disable_ip
	protected $_cache_key = 'lottery:%s:disable_ip';
	/**
	 * 实例化
	 */
	public function __construct() {
		$this->_get_storage();
	}

	/**
	 * [获取活动信息] 
	 * @param [int] $activity_id [活动id] [bool] $cache [是否使用缓存数据]
	 * @return [mixd]
	 */
	public function get_disable_ip($activity_id, $cache = true) {
		if(empty($activity_id)) {
			return false;
		}
		$result = array();
		// 根据缓存获取ip
		if($cache) {
			$result = $this->get_cache($activity_id);
			if(!empty($result)) {
				return $result;
			}
		}

		$arr = $this->_select(array('activity_id' => (int)$activity_id));
		if(!empty($arr)) {
			$result = array_column($arr, 'ip');
			// 设置缓存
			$this->set_cache($activity_id, $result);
		}

		return $result;
	}

	/**
	 * [更新禁用ip的数据]
	 * @param [array] $data [array('ip' => '1.1.1.1')]
	 * @return [bool]
	 */
	public function update_by_ip($data) {
		if(empty($data['ip']) || empty($data['activity_id'])) {
			return false;
		}

		$map['ip'] = $data['ip'];
		$map['activity_id'] = (int)$data['activity_id'];

		$return = '';
		if(!empty($map) && $result = $this->_select($map, 1)) {
			$return = $this->_save($data, $map);
		}else {
			$return = $this->_add($data);
		}

		$this->set_cache($map['activity_id']);
		return $return;
	}

	/**
	 * [删除禁用ip]
	 */
	public function delete_disable_ip($map) {
		if(empty($map['ip']) || empty($map['activity_id'])) {
			return false;
		}

		$result = $this->_delete($map);
		$this->set_cache($map['activity_id']);
		return $result;
	}

	/**
	 * 获取实例
	 */
	private function _get_storage() {
		return $this->load->database();
	}

	/**
	 * 根据条件查询记录
	 */
	private function _select($map, $limit = '') {
		if($limit == 1) {
			return $this->db->get_where($this->_table_name, $map)->row_array();
		}
		return $this->db->get_where($this->_table_name, $map, $limit)->result_array();
	}

	/**
	 * 根据条件删除记录
	 */
	private function _delete($map) {
		return $this->db->delete($this->_table_name, $map);
	}

	/**
	 * 新增一条记录
	 * @return bool
	 */
	private function _add($data) {

		$data['activity_id'] = (int)$data['activity_id'];

		if(!isset($data['created_at'])) {
			$data['created_at'] = time();
		}

		if(!isset($data['updated_at'])) {
			$data['updated_at'] = time();
		}

		return $this->db->insert($this->_table_name, $data);
	}

	/**
	 * 更新一条记录
	 * @return bool
	 */
	private function _save($data, $map) {
		if(!isset($data['updated_at'])) {
			$data['updated_at'] = time();
		}
		return $this->db->update($this->_table_name, $data, $map);
	}

	/**
	 * 更新缓存
	 * @param [int] $activity_id [活动Id]
	 * @param [array] $value [需要缓存的值]
	 */
	public function set_cache($activity_id, $value = FALSE) {
		if(empty($activity_id)) {
			return false;
		}
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		// 无传入缓存值获取数据库的值
		if($value === FALSE) {
			$value = $this->get_disable_ip($activity_id, false);
		}
		
		return $redis->save($key, $value);
	}

	/**
	 * 根据活动获取缓存
	 * @param [int] $activity_id [活动Id]
	 */
	public function get_cache($activity_id) {
		if(empty($activity_id)) {
			return false;
		}
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		return $redis->get($key);
	}


	/**
	 * 校验ip
	 * @param [string] $ip [待检测的ip]
	 */
	public function validate_ip($ip) {
		preg_match('/\b(((\d{1,2})|(1\d{2})|(2[0-4]\d)|(25[0-5]))\.){3}((\d{1,2})|(1\d{2})|(2[0-4]\d)|(25[0-5]))\b/', $ip, $matches);
		return $matches;
	}
}