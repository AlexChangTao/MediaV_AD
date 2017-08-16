<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 奖项配置
 */

class Lottery_rule extends USER_Controller {

	// 活动ID
	protected $_activity_id = '';
	// 筛选的reward_name
	protected $_reward_name = '';
	/**
	 * 显示页面
	 */
	public function index() {
		$this->_activity_id = (int)$_GET['activity_id'];
		if(empty($this->_activity_id)) {
			$this->_error('请选择正确的活动！');
		}

		$order = '';
		if(!empty($order = $_GET['order_name'])) {
			$order = $order . ' ' . $_GET['order'];
		}
		// 筛选条件
		$this->_reward_name = $_GET['reward_name'];

		$this->load->model('Lottery_rule_model', 'rule');
		$data['rule_list'] = $this->_optimizing_data($this->rule->get_rule('', $order));

		$this->_view(array('public_lottery/change_card', 'lottery_rule/index'), array(array('activity_id' => $this->_activity_id), $data));
	}

	/**
	 * 编辑页面
	 */
	public function edit() {
		$id  = (int)$_GET['id'];
		$this->_activity_id = (int)$_GET['activity_id'];

		if(empty($this->_activity_id)) {
			$this->_error('请选择正确的活动！');
		}

		$this->load->model('Lottery_rule_model', 'rule');
		if(IS_POST) {
			$data = array_filter($_POST);
			if(empty($data['reward_id'])) {
				$this->_error('请选择正确的奖品！');
			}

			$data['lottery_start'] = strtotime($data['lottery_start']);
			$data['lottery_end']   = strtotime($data['lottery_end']);
			
			// 检测概率是否一致
			if(empty($this->rule->check_probability($data['reward_id'], $data['probability'], $id))) {
				$this->_error('同种奖品概率需要保持一致！');
			}

			if(empty($id)) {
				$result = $this->rule->add_rule($data);
			}else {
				$result = $this->rule->update_rule($data, array('id' => (int)$id));
			}
			
			if($result) {
				$this->_success('保存成功！');
			}else {
				$this->_error('保存失败！');
			}
		}
		
		if(!empty($id)) {
			$data = $this->rule->get_rule($id);
		}
		$this->load->model('reward_model');
		$data['reward_list'] = $this->reward_model->get_activity_reward($this->_activity_id, 'id, name');
		$this->_view('lottery_rule/edit', $data, false, false);
	}


	/**
	 * 数据的格式化
	 * @param [array] $data [需要格式化的数据]
	 * @return mixd
	 */
	protected function _optimizing_data($data) {
		if(empty($data)) {
			return false;
		}
		$this->load->model('reward_model');
		$reward_info = $this->reward_model->get_activity_reward($this->_activity_id, 'id, name');

		$reward = array();
		foreach ($reward_info as $val) {
			// 过滤查询条件
			if(empty($val) || (!empty($this->_reward_name && $this->_reward_name != $val['name']))) {
				continue;
			}
			$reward[$val['id']] = $val['name'];
		}

		foreach ($data as $key => $val) {
			if(empty($val) || empty($reward[$val['reward_id']])) {
				unset($data[$key]);
				continue;
			}

			$data[$key]['reward_name'] = $reward[$val['reward_id']];
			$data[$key]['period_text'] = date('Y-m-d H:i', $val['lottery_start']) . ' ~ ' . date('Y-m-d H:i', $val['lottery_end']) . ' | ' . $val['period_award_amount'];
		}

		return $data;
	}
}