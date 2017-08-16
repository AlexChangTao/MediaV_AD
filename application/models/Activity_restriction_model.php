<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 活动站抽奖全局限制模型
 */

class Activity_restriction_model extends CI_Model {

	// 规定表名称
	protected $_table_name   = 'activity_restriction';
		// 数据库字段
	protected $_fields   = array(
								'id',
								'activity_id',
								'ip_award_times',
								'',
								'created_at',
								'updated_at'
							);
	// 抽奖活动信息
	protected $_cache_key = 'lottery:%s:info';

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
	 * [根据活动ID获取活动信息]
	 * @param [int] $activity_id [活动ID]
	 * @return mixd
	 */
	public function get_activity_info($activity_id, $cache = true) {
		if(empty($activity_id)) {
			return false;
		}

		// 是否根据缓存获取数据
		$result = array();
		if($cache) {
			$result = $this->get_cache($activity_id);
			if(!empty($result)) {
				return $result;
			}
		}

		$result = $this->_select(array('activity_id' => (int)$activity_id), 1);

		// 仅在需要缓存的时候去缓存数据
		$this->set_cache($activity_id, $result);

		return $result;
	}

	/**
	 * 更新活动信息
	 * @param 
	 */
	public function update_activity_info($data) {
		if(empty($data)) {
			return false;
		}
		// 根据活动id或者activity_id去更新数据
		$map = array();
		if(!empty($data['id'])) {
			$map['id'] = (int)$data['id'];
		}else if(!empty($data['activity_id'])) {
			$map['activity_id'] = (int)$data['activity_id'];
		}

		if(empty($map)) {
			return false;
		}

		$result = false;
		if(!empty($result = $this->_select($map, 1))) {
			// 保存更新活动信息
			$result = $this->_save($data, $map);
		}else {
			if(!empty($data['start_at']) && !empty($data['start_at'])) {
				// 新增活动信息
				$result = $this->_add($data);
			}
		}

		// 设置缓存
		$this->set_cache($map['activity_id']);
		return $result;	
	} 
	
	/**
	 * 根据条件查询记录
	 * @param [array] $map [筛选条件] [int] $limit [限制条数]
	 */
	private function _select($map, $limit = '') {
		if($limit == 1) {
			return $this->db->get_where($this->_table_name, $map)->row_array();
		}
		return $this->db->get_where($this->_table_name, $map, $limit)->result_array();
	}

	/**
	 * 新增一条记录
	 * @param [array] $data [需要新增的数据]
	 * @return bool
	 */
	private function _add($data) {

		$data = array_map('intval', $data);

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

		$data = array_map('intval', $data);

		if(!isset($data['updated_at'])) {
			$data['updated_at'] = time();
		}
		return $this->db->update($this->_table_name, $data, $map);
	}

	/**
	 * 更新缓存
	 * @param [int] $activity_id [活动Id]
	 * @param [array] $value [需要缓存的值]
	 * @return mixd
	 */
	public function set_cache($activity_id, $value = FALSE) {
		if(empty($activity_id)) {
			return false;
		}
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		// 无传入缓存值获取数据库的值
		if($value === FALSE) {
			$value = $this->get_activity_info($activity_id, false);
		}
		
		return $redis->save($key, $value);
	}

	/**
	 * 根据活动获取缓存
	 * @param [int] $activity_id [活动Id]
	 * @return mixd
	 */
	public function get_cache($activity_id) {
		if(empty($activity_id)) {
			return false;
		}
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		return $redis->get($key);
	}
}