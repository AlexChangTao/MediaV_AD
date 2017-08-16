<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ip display class 活动站抽奖ip限制控制器
 */

class Disable_ip extends USER_Controller {
	// 活动ID
	protected $_activity_id = '';
	// 筛选的IP
	protected $_ip 			= '';
	// 默认分页大小
	const PAGE_SIZE 		= 10;

	/**
	 * 限制ip展示控制器
	 */
	public function index() {
		$this->_activity_id = (int)$_GET['activity_id'];

		if(empty($this->_activity_id)) {
			$this->_error('请选择正确的活动！');
		}
		
		if(IS_POST) {
			if(empty($ip = $_POST['ip'])) {
				$this->_error('输入IP格式错误！');
			}

			$data['ip'] = $_POST['ip'];
			$data['activity_id'] = $this->_activity_id;

			$this->load->model('disable_ip_model');
			// 校验ip
			if(empty($this->disable_ip_model->validate_ip($data['ip']))) {
				$this->_error('输入IP格式错误！');
			}

			$result = $this->disable_ip_model->update_by_ip($data);
			if(!empty($result)) {
				$this->_success('保存成功！');
			}else {
				$this->_error('保存失败！');
			}

		}
			
		// 获取筛选条件
		$map['activity_id'] = $this->_activity_id;
		// 筛选IP
		if(!empty($this->_ip = $_GET['search_ip'])) {
			$map['ip'] = $this->_ip;
		}

		$field  = 'id, activity_id, ip, award';
		$this->load->model('lottery_log_model');
		$result = $this->lottery_log_model->get_log($map, $field);
		$data['logs'] = $this->_optimizing_data($result);

		// 当前页面
		$page = isset($_GET['p']) ? (int)$_GET['p'] : 1;
		// 分页
		$this->load->library('pager');
        $page_config['total_row']     = count($data['logs']);
        $page_config['cur_page']      = $page;
        $page_config['base_link_url'] = base_url('disable_ip/index?activity_id=' . $this->_activity_id . '&p=');
        $page_config['page_size']     = self::PAGE_SIZE;
        $data['page'] = $this->pager->create($page_config);

        // 展示的数据
        $data['logs'] = array_slice($data['logs'], $page - 1, self::PAGE_SIZE);
		$this->_view(array('public_lottery/change_card', 'ip/index'), array(array('activity_id' => $this->_activity_id), $data));
	}

	/**
	* 设置ip的状态（开启，禁用）
	*/
	public function change_status() {
		$data 	= array();
		$method = $_GET['method'];

		$data['activity_id'] 	= (int)$_GET['activity_id'];
		$data['ip'] 			= $_GET['ip'];
		
		if($data['ip'] && $data['activity_id']) {
			$this->load->model('disable_ip_model');
			switch ($method) {
				// 开启
				case 'resume' :
					if($this->disable_ip_model->update_by_ip($data)) {
						$this->_success('保存成功！');
					}
					break;
				// 关闭
				case 'close'  :
					if($this->disable_ip_model->delete_disable_ip($data)) {
						$this->_success('保存成功！');
					}
					break;
			}
		}

		$this->_error('保存失败！');
	}

	/**
	 * 数据的格式化
	 */
	protected function _optimizing_data($data) {
		// 获取禁用ip
		$this->load->model('disable_ip_model');
		$disable_ip = $this->disable_ip_model->get_disable_ip($this->_activity_id);

		$result = array();

		foreach ($disable_ip as $ip) {
			if(empty($ip) || (!empty($this->_ip) && $ip != $this->_ip)) {
				continue;
			}
			$result[$ip]['ip']     = $ip;
			$result[$ip]['status'] = 2;
		}

		foreach ($data as $val) {
			if(empty($val)) {
				continue;
			}
			if(!isset($result[$val['ip']]['id'])) {
				$result[$val['ip']]['id'] 			= $val['id'];
				$result[$val['ip']]['ip'] 			= $val['ip'];
				$result[$val['ip']]['activity_id'] 	= $val['activity_id'];
				$result[$val['ip']]['award_times']  = (int)$val['award'];
				$result[$val['ip']]['lottery_times']= 1;
				// 判断该ip的状态
				$result[$val['ip']]['status']  		= !in_array($val['ip'], $disable_ip) ? 1 : 2;
				continue;
			}

			$result[$val['ip']]['award_times']  += (int)$val['award'];
			$result[$val['ip']]['lottery_times']+= 1;
		}

		return $result;
	}
}