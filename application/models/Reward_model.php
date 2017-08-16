<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 奖品模型
 */

class Reward_model extends CI_Model {
	// 开启
	static $on  = 1;
	// 关闭
	static $off = 2;

	// 规定表名称
	protected $_table_name   = 'reward';
	// 数据库字段
	protected $_fields  	 = array();
	// 奖品缓存的key(eg: reward:activity_id:info)
	protected $_cache_key    = 'reward:%s:info';
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
	 * [获取缓存中奖品详细信息, 前台接口调用缓存]
	 * @param [int] $activity_id [活动ID]
	 * @param [int] $id [奖品ID]
	 * @return mixd
	 */
	public function get_reward_cache($activity_id, $id, $cache = true) {
		if(empty($activity_id) || empty($id)) {
			return false;
		}

		// 是否根据缓存获取数据
		$result = array();
		if($cache) {
			$result = $this->get_cache($activity_id, $id);
			if(!empty($result)) {
				return $result;
			}
		}

		$result = $this->get_reward_by_id($id);
		if(!empty($result)) {
			// 获取奖品设置规则
			$this->load->model('lottery_rule_model', 'rule');
			$reward_rule = $this->rule->get_reward_lottery_rule($id);
			foreach ($reward_rule as $rule) {
				if(empty($rule)) {
					continue;
				}
	
				$result['award_time'][$rule['lottery_start']][$rule['lottery_end']] = $rule;
			}
 
			$this->set_cache($activity_id, $id, $result);
		}

		return $result;
	}

	/**
	 * [通过ID获取奖品信息]
	 * @param [int] $id [奖品ID]
	 * @return mixd
	 */
	public function get_reward_by_id($id) {
		if(empty($id)) {
			return false;
		}

		return $this->_select(array('id' => (int)$id), 1);
	}

	/**
	 * [通过活动ID获取该活动下所有奖品信息]
	 * @param [int] $activity_id [活动ID]
	 * @param [sting] $field [字段]
	 * @return mixd
	 */
	public function get_activity_reward($activity_id, $field = '') {
		if(empty($activity_id)) {
			return false;
		}

		return $this->_select(array('activity_id' => (int)$activity_id), '', $field);
	}

	/**
	 * 通过条件$map保存数据$data
	 * [array] $map [查询条件] [array] $data [保存的数据]
	 * return mixd
	 */
	public function save_data($map, $data) {
		if(empty($data) || empty($data)) {
			return false;
		}
		$this->db->trans_start();

		try {
			$result = $this->_save($data, $map);
			if(empty($result)) {
				throw new Exception('Update mysql reward ' . $map['id'] . ' failed.');
			}
		} catch (Exception $e) {
			log_message('error', $e->getMessage());
			$this->db->trans_rollback();
			return false;
		}

		$this->db->trans_complete();

		return $result;
	}
	
	/**
	 * 更新奖品信息
	 * @param [array] $data [需要更新的数据]
	 */
	public function update_reward($data) {
		if(empty($data)) {
			return false;
		}

		$data      = array_filter($data);
		$map       = array();
		if(!empty($data['id'])) {
			$map['id'] = (int)$data['id'];
		}

		$result = false;
		$field  = 'activity_id, name, award_id, remain, total';
		// 避免重复重新构造数据,更新之前是否重复
		if(!empty($map) && !empty($result = $this->_select($map, 1, $field))) {
			$data['activity_id'] = $result['activity_id'];
			$data = $data + $result;
			if(isset($data['total']) && (int)$result['total'] == (int)$result['remain']) {
				// 同步数量
				$data['remain'] = (int)$data['total'];
			}
		}else {
			if(empty($data['name']) || empty($data['activity_id']) || empty($data['award_id'])) {
				return false;
			}
			// 同步数量
			$data['remain'] = (int)$data['total'];
		}

		// 判断更新后的数据是否重复
		$where['name'] = $data['name'];
		$where['award_id']    = $data['award_id'];
		$where['activity_id'] = (int)$data['activity_id'];

		// 无重复数据新增
		if(empty($result = $this->_select($where, 1))) {
			if(!empty($map)) {
				// 保存更新活动信息
				$result = $this->_save($data, $map);
			}else {
				// 新增活动信息
				$result = $this->_add($data);
				$map['id'] = $this->db->insert_id();
			}
		}else if($map['id'] == $result['id']) {
			// 保存更新活动信息
			$result = $this->_save($data, $map);
		}

		// 设置缓存
		$this->load->model('lottery_rule_model');
		$this->lottery_rule_model->set_cache($map['id'], $data['activity_id']);
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
	 * @param [int] $id [奖品id]
	 * @param [array] $value [需要缓存的值]
	 * @return mixd
	 */
	public function set_cache($activity_id, $id, $value = FALSE) {
		if(empty($activity_id) || empty($id)) {
			return false;
		}
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		// 无传入缓存值获取数据库的值
		if($value === FALSE) {
			$value = $this->get_reward_cache($activity_id, $id, false);
		}
		
		return $redis->hset($key, (int)$id, $value);
	}

	/**
	 * 获取缓存
	 * @param [int] $activity_id [活动Id]
	 * @param [int] $id [奖品id]
	 * @return mixd
	 */
	public function get_cache($activity_id, $id) {
		if(empty($activity_id) || empty($id)) {
			return false;
		}
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		return $redis->hget($key, (int)$id);
	}
}