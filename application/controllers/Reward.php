<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 奖项配置
 */

class Reward extends USER_Controller {
	/**
	 * 显示页
	 */
	public function index() {
		$activity_id = (int)$_GET['activity_id'];
		$id          = (int)$_GET['id'];

		if(empty($activity_id)) {
			$this->_error('请选择合适的活动！');
		}

		if(IS_POST) {
			$data = array_filter($_POST);
			if(empty($data['name']) || empty($data['status']) || empty($data['award_id'])) {
				$this->_error('请输入合适的数据!');
			}

			$data['activity_id'] = $activity_id;
			$data['id']			 = $id;
			$this->load->model('reward_model');
			$result = $this->reward_model->update_reward($data);
			if(!empty($result)) {
				$this->_success('保存成功！');
			}

		}
		$this->load->model('reward_model');
		// 获取该活动下所有奖品信息
		$data['reward_list'] = $this->reward_model->get_activity_reward($activity_id);

		$this->load->model('award_model');
		$data['award_list']  = $this->award_model->get_award_cache(false);

		$this->_view(array('public_lottery/change_card', 'reward/index'), array(array('activity_id' => $activity_id), $data));
	}


	/**
	 * 编辑获奖页面
	 */
	public function edit() {
		$activity_id = (int)$_GET['activity_id'];
		$id          = (int)$_GET['id'];
		// 编辑页面
		if(empty($id)) {
			$this->_error('请选择正确的奖品！');
		}
		
		if(IS_POST) {
			$data = array_filter($_POST);
			if(empty($activity_id)) {
				$this->_error('请选择合适的活动！');
			}

			if(empty($data['name']) || empty($data['status']) || empty($data['award_id'])) {
				$this->_error('请输入合适的数据!');
			}

			$data['activity_id'] = $activity_id;
			$data['id']			 = $id;
			$this->load->model('reward_model');
			$result = $this->reward_model->update_reward($data);
			if(!empty($result)) {
				$this->_success('保存成功！');
			}

		}

		$this->load->model('reward_model');
		$data = $this->reward_model->get_reward_by_id($id);
		$this->load->model('award_model');
		$data['award_list']  = $this->award_model->get_award_cache(false);

		$this->_view('reward/edit', $data, false, false);
	}
}