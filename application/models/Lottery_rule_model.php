<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 活动站限制ip模型
 */

class Lottery_rule_model extends CI_Model {

	// 规定表名称
	protected $_table_name   = 'lottery_rule';
	// 数据库字段
	protected $_fields  	 = array();
	// 中奖字段(eg:　lottery:activity_id:rule)
	protected $_cache_key    = 'lottery:%s:rule';

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
	 * 根据id查询获奖规则
	 * @param [int] $id [rule_id]
	 * @return mixd
	 */

	public function get_rule($id = '', $order = '') {
		// 无id查询所有结果
		if(empty($id)) {
			return $this->_select(NULL, '', '', $order);
		}
		// 查询单个id的获奖规则
		return $this->_select(array('id' => (int)$id), 1, '', $order);
	}


	/**
	 * 获取可能性
	 * @param [int] $activity_id [活动Id]
	 * @param [bool] $cache [是否使用缓存]
	 */
	public function get_probability_rule($activity_id, $cache = true) {
		if(empty($activity_id)) {
			return false;
		}

		$result = '';
		if($cache) {
			$result = $this->get_cache($activity_id);
			if(!empty($result)) {
				return $result;
			}
		}

		$this->load->model('reward_model');
		$rewards = $this->reward_model->get_activity_reward($activity_id, 'id, status');
		$rules 	 = $this->_select(NULL, '', 'reward_id, probability');
		array_walk($rewards, function($value, $key) use (&$reward){
    		$reward[$value['id']] = $value['status'];
		});
		
		foreach ($rules as $rule) {
			if(empty($rule) || isset($result[$rule['reward_id']])) {
				continue;
			}
			if(isset($reward[$rule['reward_id']]) && (int)$reward[$rule['reward_id']] !== reward_model::$off) {
				$result[$rule['reward_id']] = (int)$rule['probability'];
			}
		}
		$this->set_cache('', $activity_id, $result);

		return $result;
	}

	/**
	 * 更新获奖规则
	 * @param [array] $data [需要更新的数据]
	 * @param [array] $map [需要更新的条件]
	 * @return bool
	 */
	public function update_rule($data, $map) {
		if(empty($map) || empty($data)) {
			return false;
		}

		$result = false;
		// 检测概率
		if(empty($this->check_probability($data['reward_id'], $data['probability'], $map['id']))) {
			return $result;
		}

		if(isset($data['lottery_start']) || isset($data['lottery_end'])) {
			// 检验时间段的重复
			if($this->_check_period($data['reward_id'], $data['lottery_start'], $data['lottery_end'], $map['id'])) {
				$result = $this->_save($data, $map);
			}
		}else {
			$result = $this->_save($data, $map);
		}

		// 更新缓存
		$this->set_cache($data['reward_id']);
		return $result;
	}

	/**
	 * 新增获奖规则
	 * @param [array] $data [需要新增的数据]
	 * @return bool
	 */
	public function add_rule($data) {
		if(empty($data) || empty($data['reward_id']) || empty($data['lottery_start']) || empty($data['lottery_end'])) {
			return false;
		}
		// 检测概率
		$result = false;
		if(empty($this->check_probability($data['reward_id'], $data['probability']))) {
			return $result;
		}

		// 检验时间段是否重复
		if($this->_check_period($data['reward_id'], $data['lottery_start'], $data['lottery_end'])) {
			$result = $this->_add($data);
		}

		// 更新缓存
		$this->set_cache($data['reward_id']);
		return $result;
	}

	/**
	 * [从缓存中获取活动信息]
	 */
	public function get_reward_lottery_rule($reward_id = '') {
		if(empty($reward_id)) {
			return false;
		}

		$map['reward_id'] = (int)$reward_id;
		return $this->_select($map);
	}

	/**
	 * 中奖时间段重复判断
	 * @param [int] $reward_id [奖品Id]
	 * @param [int] $lottery_start [起始时间]
	 * @param [int] $lottery_end [结束时间]
	 * @param [int] $id [需要排除自己跟自己的时间段重复]
	 * @return bool
	 */
	protected function _check_period($reward_id = '', $lottery_start = '', $lottery_end = '', $id = '') {
		if(empty($reward_id) || empty($lottery_start) || empty($lottery_end)) {
			return false;
		}
		// 时间设置不合理
		if($lottery_start >= $lottery_end) {
			return false;
		}

		$map['reward_id'] = (int)$reward_id;
		$field= 'id, lottery_start, lottery_end';
		$rule = $this->_select($map, '', $field);
		
		foreach ($rule as $val) {
			if(empty($val) || (int)$id === (int)$val['id']) {
				continue;
			}
			
			if(!((int)$lottery_start >= (int)$val['lottery_end'] || (int)$lottery_end <= (int)$val['lottery_start'])) {
				return false;
			}
		}

		return true;
	}

	/**
	 * 检测设置的中奖概率的合法性
	 * @param [int] $reward_id [奖品Id]
	 * @param [int] $probability [时间段中奖概率]
	 * @param [int] $id [需要排除自己跟自己的时间段重复]
	 * @return bool
	 */
	public function check_probability($reward_id, $probability, $id) {
		if(empty($reward_id)) {
			return false;
		}

		$map['reward_id'] = (int)$reward_id;
		$field= 'id, probability';
		$rule = $this->_select($map, '', $field);

		foreach ($rule as $val) {
			if(empty($val) || (int)$id === (int)$val['id']) {
				continue;
			}

			if((int)$val['probability'] !== (int)$probability) {
				return false;
			}
		}

		return true;
	}

	/**
	 * 根据条件查询记录
	 * @param [array] $map [筛选条件] 
	 * @param [int] $limit [限制条数]
	 * @param [string] $field [查询的字段]
	 * @param [string] $order [排序]
	 */
	private function _select($map, $limit = '', $field = '', $order = '') {
		if($limit == 1) {
			return $this->db->select($field)->order_by($order)->get_where($this->_table_name, $map)->row_array();
		}
		return $this->db->select($field)->order_by($order)->get_where($this->_table_name, $map, $limit)->result_array();
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
	 * 更新缓存
	 * @param [int] $reward_id [奖品id]
	 * @param [int] $activity_id [活动Id]
	 * @param [array] $data [需要更新的数据]
	 * @return mixd
	 */
	public function set_cache($reward_id, $activity_id = '', $data = FALSE) {
		if(empty($reward_id) && empty($activity_id)) {
			return false;
		}

		if(empty($activity_id)) {
			// 获取当前活动id
			$this->load->model('reward_model');
			$reward 	 = $this->reward_model->get_reward_by_id($reward_id);
			$activity_id = $reward['activity_id'];
		}

		// 刷新概率缓存
		if($data === FALSE) {
			$data = $this->get_probability_rule($activity_id, false);
		}

		// 缓存的key
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		$result= $redis->save($key, $data);
		if(empty($reward_id)) {
			return $result;
		}

		// 刷新奖品的缓存
		return $this->reward_model->set_cache((int)$activity_id, $reward_id);
	}

	/**
	 * 获取获奖规则的缓存数据
	 * @param [int] $activity_id [活动Id]
	 * @param [int] $reward_id [奖品id]
	 * @return mixd
	 */
	public function get_cache($activity_id = '', $reward_id = '') {
		if(empty($activity_id) && empty($reward_id)) {
			return false;
		}

		if(empty($activity_id)) {
			// 获取当前活动id
			$this->load->model('reward_model');
			$reward 	 = $this->reward_model->get_reward_by_id($reward_id);
			$activity_id = $reward['activity_id'];
		}
		
		// 缓存的key
		$key   = sprintf($this->_cache_key, (int)$activity_id);
		$redis = get_redis_instance();
		return $redis->get($key);
	}
}