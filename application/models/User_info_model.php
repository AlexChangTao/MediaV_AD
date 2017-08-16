<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 活动站抽奖日志模型
 */

class User_info_model extends CI_Model {
	// 规定表名称
	protected $_table_name = 'user_info';

	// 规定表字段
	protected $_fields = array(
							'id',
							'activity_id',
							'name',
							'lottery_times', // 抽奖次数
							'award_times',   // 获奖次数
							'created_at',
							'updated_at'
		);
	// 用户信息缓存名称
	private $_cache_key = 'lottery:%s:user_info';


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
	 * 通过用户名称保存用户数据
	 * @param [int] $activity_id [活动Id]
	 * @param [string] $name [用户名称]
	 * @param [array] $data [用户数据]
	 */
	public function save_user_by_name($activity_id, $name, $data = '') {
		if(empty($activity_id) || empty($name) || empty($data)) {
			return false;
		}

		$map = array(
					'name'        => $name,
					'activity_id' => (int)$activity_id,
			);

		$result = $this->_save($data, $map);

		$this->set_cache($activity_id, $name);
		return $result;
	}

	/**
	 * 根据用户名检测并保存用户数据
	 * @param [int] $activity_id [活动Id]
	 * @param [string] $name [用户名称]
	 * @param [string] $ip [用户ip]
	 * @return mixd
	 */
	public function check_user_info($activity_id, $name, $ip = '') {

		if(empty($activity_id) || empty($name) || empty($ip)) {
			return false;
		}

		$map = array(
					'name'        => $name,
					'activity_id' => (int)$activity_id,
			);

		$result = '';
		if($info = $this->get_user_info($activity_id, $name, 1)) {
			$result = $this->_save(array('ip' => $ip), $map);
		}else{
			$map['ip'] = $ip;
			$result = $this->_add($map);
		}

		$this->set_cache($activity_id, $name);
		return $result;
	}

	/**
	 * [通过id和activity_id唯一获取用户信息]
	 * @param [int] $activity_id [活动Id]
	 * @param [string] $name [用户名称]
	 * @param [bool] $cache [是否使用缓存]
	 * @return mixd
	 */
	public function get_user_info($activity_id, $name, $cache = false) {
		if(empty($activity_id) || empty($name)) {
			return false;
		}

		// 是否根据缓存获取数据
		$result = array();
		if($cache) {
			$result = $this->get_cache($activity_id, $name);
			if(!empty($result)) {
				return $result;
			}
		}

		$map['name'] = $name;
		$map['activity_id'] = (int)$activity_id;
		$result = $this->_select($map, 1);

		// 设置缓存
		$this->set_cache($result['activity_id'], $result['name'], $result);

		return $result;
	}

	/**
	 * 根据条件查询记录
	 * @param [array] $map [筛选条件] [int] $limit [限制条数]
	 */
	private function _select($map, $limit = '', $field='') {
		if($limit == 1) {
			return $this->db->select($field)->get_where($this->_table_name, $map)->row_array();
		}
		return $this->db->select($field)->get_where($this->_table_name, $map, $limit)->result_array();
	}

	/**
	 * 新增一条记录
	 * @param [array] $data [需要新增的数据]
	 * @return bool
	 */
	private function _add($data) {

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
	 * @param [array] $data [需要更新的数据]
	 * @param [array] $map [更新的条件]
	 * @return bool
	 */
	private function _save($data, $map) {

		if(!isset($data['updated_at'])) {
			$data['updated_at'] = time();
		}
		return $this->db->update($this->_table_name, $data, $map);
	}

	/**
	 * 更新缓存,hset
	 * @param [int] $activity_id [活动Id]
	 * @param [string] $id [用户name]
	 * @param [array] $value [需要缓存的值]
	 * @return mixd
	 */
	public function set_cache($activity_id, $name, $value = FALSE) {
		if(empty($activity_id) || empty($name)) {
			return false;
		}
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		// 无传入缓存值获取数据库的值
		if($value === FALSE) {
			$value = $this->get_user_info($activity_id, $name, false);
		}
		
		return $redis->hset($key, $name, $value);
	}

	/**
	 * 获取缓存
	 * @param [int] $activity_id [活动Id]
	 * @param [string] $id [用户name]
	 * @return mixd
	 */
	public function get_cache($activity_id, $name) {
		if(empty($activity_id) || empty($name)) {
			return false;
		}
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		return $redis->hget($key, $name);
	}
}