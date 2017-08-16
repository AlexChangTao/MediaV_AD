<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 活动站的全站设置
 */

class Activity_restriction extends USER_Controller {
	
	/**
	 * 显示页
	 */
	public function index() {
		$activity_id = (int)$_GET['activity_id'];
		if(empty($activity_id)) {
			$this->_error('请选择正确的活动！');
		}

		$this->load->model('Activity_restriction_model', 'activity');
		if(IS_POST) {
			if(empty($activity_id) || empty($_POST['start_at']) || empty($_POST['end_at'])) {
				$this->_error('请输入正确的活动数据！');
			}
			$data = array_filter($_POST);
			$data['start_at']    = strtotime($data['start_at']);
			$data['end_at']      = strtotime($data['end_at']);
			$data['activity_id'] = $activity_id;
			
			// 保存数据
			$result = $this->activity->update_activity_info($data);
			if(!empty($result)) {
				$this->_success('保存成功！');
			}else {
				$this->_error('保存失败！');
			}
		}

		$data = $this->activity->get_activity_info($activity_id, false);

		$this->_view(array('public_lottery/change_card', 'activity_restriction/index'), array(array('activity_id' => $activity_id), $data));
	}
}