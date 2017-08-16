<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 接口调用控制器
 */

class Open_api extends Basic_Controller {
	
	/**
	 * 获取奖品信息
	 * @request POST open_api/get_reward_info
	 * @param int    $activity_id 活动id  required 11
	 * @return array
	 */
	public function get_reward_info() {
		$activity_id = (int)$_POST['activity_id'];
		if(empty($activity_id)) {
			$this->_error(1005);
		}

		$this->load->model(array('lottery_rule_model', 'reward_model', 'award_model'));
		// 获取奖项数据
		$post_arr   = $this->lottery_rule_model->get_probability_rule($activity_id);
		$award_info = $this->award_model->get_award_cache();
		$return     = array();
		$reward     = '';
		foreach ($post_arr as $key => $val) {
			if(empty($val)) {
				continue;
			}

		    $reward = $this->reward_model->get_reward_cache($activity_id, $key);
		    if(empty($reward) || (int)$reward['status'] !== reward_model::$on) {
		    	continue;
		    }

		    $return[$award_info[$reward['award_id']]['name']] = $reward['name'];
		}

		if(!empty($return)) {
			$this->_success($return);
		}
		$this->_error(1005);
	}

	/**
	 * 前台获取token的接口
	 * @request GET open_api/get_token
	 * @param   string $ip 用户ip required  1.1.1.1
	 */
	public function get_token() {
		if(empty($ip = $_GET['ip'])) {
			$this->_error(1002);
		}
		// 校验ip
		if(empty($this->_check_ip($ip))) {
			$this->_error(1002);
		}
		$token  = $this->_create_token($ip);
		$this->_success($token);
	}

	/**
	 * 获取验证码
	 * @request POST open_api/get_verify
	 * @param string $ip 用户ip required 1.1.1.1
	 * @param int $activity_id 活动id required 11
	 */
	public function get_verify() {
		
		if(!empty($activity_id = (int)$_POST['activity_id'])) {
			// 获取该活动的详细信息
			$this->load->model('Activity_restriction_model', 'activity');
			$info  = $this->activity->get_activity_info($activity_id);
			// 不合法的活动
			if(empty($info)) {
				$this->_error(1005);
			}

			if(isset($info['verify_code']) && !empty($info['verify_code'])) {
				// 获取ip
				$ip = $_POST['ip'];
				$this->load->library('Verify');
				// 输出验证码
				$this->verify->output($activity_id . '-' . $ip);
			}
		}
		$this->_error(1003);
	}

	/**
	 * 提交用户表单信息接口
	 * @request POST open_api/get_user_info?token=
	 * @param string $ip 		  用户ip  required 1.1.1.1
	 * @param int    $activity_id 活动id  required 11
	 * @param string $verify_code 验证码  required  dfre
	 * @param string $user_id     用户名称 required user_id
	 */
	public function get_user_info() {

		if(empty($ip = $_POST['ip']) ) {
			$this->_error(1002);
		} 
		// 验证token
		if($_GET['token'] !== $this->_create_token($ip)) {
			$this->_error(1001);
		}

		if(empty($activity_id = (int)$_POST['activity_id'])) {
			$this->_error(1005);
		}

		// 获取该活动的详细信息
		$this->load->model('Activity_restriction_model', 'activity');
		$info  = $this->activity->get_activity_info($activity_id);
		// 不合法的活动
		if(empty($info)) {
			$this->_error(1005);
		}

		if(isset($info['verify_code']) && !empty($info['verify_code'])) {
			// 验证验证码
			$this->load->library('Verify');
			$checkCode  = $this->verify->check($_POST['verify_code'], $activity_id . '-' . $ip);

			if(!$checkCode) {
				$this->_error(1003);
			}
		}

		// 核实用户id
		if(empty($name = $_POST['user_id'])) {
			$this->_error(1002);
		}
		
		// 更新用户信息
		$this->load->model('user_info_model');
		if ($this->user_info_model->check_user_info($activity_id, $name, $ip)) {
			$this->_success();
		}

		$this->_error(1002);
	}

	/**
	 * 抽奖接口
	 * @request POST open_api/get_lottery
	 * @param int    $activity_id  活动id required 11
	 * @param string $user_id 	   用户名称 required user_id
	 */
	public function get_lottery() {
		// 调用抽奖接口
		if(file_exists(dirname(__FILE__) . '/Lottery.php')) {
			if(!class_exists('Lottery')) {
				require_once(dirname(__FILE__) . '/Lottery.php');
			}

			$class = new lottery();
			return $class->get_reward();
		}
		$this->error(1000);
	}
}