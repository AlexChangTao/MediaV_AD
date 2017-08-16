<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 活动站抽奖日志模型
 */

class Lottery_log_model extends CI_Model {
	// 规定表名称
	protected $_table_name   = 'lottery_log';
	// 数据库字段
	protected $_fields   = array(
								'id',
								'ip',
								'activity_id',
								'user_id',
								'award',
								'reward_id',
								'created_at'
							);

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
	 * 获取日志
	 * @param [array] $map [筛选条件数组] [string] $field [eg:'id,ip']
	 * @return mixd
	 */
	public function get_log($map, $field='') {
		if(empty($map['activity_id'])) {
			return false;
		}
		return $this->_select($map, '', $field);
	}

	/**
	 * 添加日志
	 * @param [array] $data [添加的日志数组]
	 * @param [int] $count [批量添加的数目]
	 * @return bool
	 */
	public function add_log($data, $count = 100) {
		if(empty($data)) {
			return false;
		}

		return $this->_add_batch($data);
	}

	/**
	 * 批量新增一条记录
	 * @param [array] $data [需要新增的数据]
	 * @param [int] $count [批量添加的数目]
	 * @return bool
	 */
	private function _add_batch($data, $count = 100) {
		return $this->db->insert_batch($this->_table_name, $data, NULL, $count);
	}

	/**
	 * 根据条件查询记录
	 * @param [array] $map [筛选条件] [int] $limit [限制条数]
	 */
	private function _select($map, $limit = '', $field='') {
		return $this->db->select($field)->get_where($this->_table_name, $map, $limit)->result_array();
	}
}