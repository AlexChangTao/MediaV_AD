<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 抽奖活动接口控制器
 */

class Lottery extends Basic_Controller {
	
	// 用户标识ID
	private	$_user_id     		= '';
	// 用户标识名称
	private	$_user_name     	= '';
	// 活动id
	private	$_activity_id 		= '';
	// 抽奖活动信息
	private $_activity_info 	= '';
	// 客户信息
	private $_user_info        	= '';
	// 奖品信息
	private $_reward_info       = '';
	// 奖项信息
	private $_award_info        = '';
	// 当前抽奖时间点
	private $_time              = 0;
	// 获得的奖品ID
	private $_reward_id         = '';
	// 剩余库存
	private $_remain_stock      = 0;
	// 回滚的redis数据
	private $_trans_redis       = array();

	// ip获奖次数的缓存key(eg: reward:activity_id:times)
	private $_ip_reward			= 'reward:%d:ip_times';
	// 奖品天中奖次数的缓存key(eg: reward:activity_id:Ymd:times)
	private $_reward_day	    = 'reward:%d:%d:times';
	// 客户每天抽奖次数的缓存key(eg: lottery:activity_id:Ymd:times)
	private $_user_lottery_day 	= 'lottery:%d:%d:user_times';
	// 客户中不同奖次数的缓存key(eg: reward:activity_id:times)
	private $_user_reward       = 'reward:%d:user_times';
	// 客户每天中奖次数的缓存key(eg: reward:activity_id:Ymd:user_times)
	private $_user_reward_day 	= 'reward:%d:%d:user_times';
	// 某个时间段发放奖品的数量(eg: reward:activity_id:reward_id:times)
	private $_reward_period_num = 'reward:%d:%d:times';
	// 产品库存缓存key(eg: reward:activity_id:reward_id:stock)
	private $_reward_stock      = 'reward:%d:%d:stock';

	/**
	 * 限制未注册
	 */
	public function __construct() {
		parent::__construct();

		$this->_user_name   = $_POST['user_id'];
		$this->_activity_id = (int)$_POST['activity_id'];
		// 获取用户信息
		$this->load->model('user_info_model');
		$this->_user_info = $this->user_info_model->get_user_info($this->_activity_id, $this->_user_name);
		// 检测用户
		if(empty($this->_user_info)) {
			$this->_error(1004);
		}
		$this->_user_id = (int)$this->_user_info['id'];
	}

	/**
	 * 检测获奖
	 */
	public function get_reward() {
		// 用户锁
		$this->_initialize();
		// 抽奖前的验证
		$this->_validate_before();

		// 抽奖
		$this->_get_rand_lottery();

		// 抽奖后的验证
		$this->_validate_after();

		// 获奖，更新数据库
		$this->_update_stock();
	}

	/**
	 * [用户锁]
	 */
	private function _initialize() {
		// 用户锁的key
		$key = $this->_activity_id . ':' . $this->_user_id;
		// 获得用户锁
		if(!$this->_get_lock($key)) {
			$this->_error(1000);
		}
	}

	/**
	 * 抽奖前的验证过滤
	 */
	private function _validate_before() {
		
		// 验证活动
		$this->_validate_activity();
		// 非法请求的过滤

		// ip的限制
		$this->_validate_ip();
		// 用户的限制
		$this->_validate_user();
	}

	/**
	 * 抽奖后的验证筛选
	 */
	private function _validate_after() {
		// 验证是否中奖
		$this->_validate_award();
		// 中奖时间段数量的限制
		$this->_validate_reward_period();
		// 用户日中奖，活动中奖上限的限制
		$this->_validate_reward_user();
		// 活动中奖限制
		$this->_validate_restriction();
		// 奖品限制
		$this->_validate_stock();
	}


	/**
	 * 更新库存
	 */
	private function _update_stock() {
		// 更新奖品库存
		$map['id']  		= $this->_reward_id;
		$map['activity_id']	= $this->_activity_id;
		$map['updated_at'] 	= $this->_reward_info['updated_at'];
		
		$data['remain']    = $this->_remain_stock;
		$this->load->model('reward_model');
		$result = $this->reward_model->save_data($map, $data);
		unset($map);
		unset($data);

		// 数据库更新失败，刷新库存缓存
		if(empty($result)) {
			$this->_error(1006);
		}
		// 更新用户中奖数据
		$this->load->model('user_info_model');
		$this->user_info_model->save_user_by_name($this->_activity_id, $this->_user_name, array('award_times' => $this->_user_info['award_times'] + 1));
		// 释放锁
		$this->_del_lock($this->_activity_id . ':' . $this->_user_id);
		$this->_success($this->_reward_id);
	}

	/**
	 * 验证活动
	 */
	private function _validate_activity() {
		$this->load->model('activity_restriction_model', 'activity'); 
		$this->_activity_info  = $this->activity->get_activity_info($this->_activity_id);

		// 活动是否启用
		if((int)$this->_activity_info['status'] != 1) {
			$this->_error(1005);
		}
		// 活动时间的判断
		$this->_get_time();
		if($this->_time < (int)$this->_activity_info['start_at'] || $this->_time > (int)$this->_activity_info['end_at']) {
			$this->_error(1005);
		}
	}

	/**
	 * ip的限制
	 */
	private function _validate_ip() {
		$this->load->model('disable_ip_model');
		// 获取被禁用的ip
		$disable_ip = $this->disable_ip_model->get_disable_ip($this->_activity_id);
		// 是否是禁用ip
		if(!empty($disable_ip) && in_array($this->_user_info['ip'], $disable_ip)) {
			$this->_error(1004);
		}
	}

	/**
	 * 用户的限制
	 */
	private function _validate_user() {
		// 限制用户天抽奖次数
		$times_day = (int)$this->_activity_info['user_lottery_times_day'];
		// -1为抽奖次数不受限
		if($times_day != -1) {
			$key = sprintf($this->_user_lottery_day, $this->_activity_id, date('Ymd', $this->_time));
			// 初始化
			$remain_times = $this->_get_op_redis()->set_key($key)->set_field($this->_user_id)->set_expire(12 * 3600)->set_decrement(-1)->initialize_hash($times_day);
			
			if(!is_int($remain_times) || $remain_times <= -1) {
				$this->_error(1004);
			}
		}
		
		// 限制用户活动期间抽奖次数
		$times = (int)$this->_activity_info['user_lottery_times'];
		// -1为抽奖次数不受限
		if($times != -1 && $this->_user_info['lottery_times'] > $times -1) {
			$this->_error(1004);
		}
	}

	/**
	 * [验证是否中奖]
	 */
	private function _validate_award() {
		// 判断是否中奖
		$this->load->model('award_model');
		$this->_award_info = $this->award_model->get_award_cache();

		// 获取奖品信息
		$this->load->model('reward_model');
		$this->_reward_info = $this->reward_model->get_reward_cache($this->_activity_id, $this->_reward_id);

		if(empty($this->_reward_info)) {
			$this->_error(1006);
		}

		$status = $this->_award_info[$this->_reward_info['award_id']]['status'];
		// 未中奖
		if((int)$status != award_model::$on) {
			$this->_error(1006);
		} 
	}

	/**
	 * 中奖时间段数量的限制
	 */
	private function _validate_reward_period() {
		// 检测中奖时间段
		foreach ($this->_reward_info['award_time'] as $start => $val) {
			if($this->_time < (int)$start) {
				continue;
			}

			foreach ($val as $end => $vo) {
				if($this->_time > (int)$end) {
					continue;
				}

				$this->_reward_info['lottery_start'] = (int)$start;
				$this->_reward_info['lottery_end']   = (int)$end;
				$this->_reward_info['award_times_day']     = (int)$vo['award_times_day'];
				$this->_reward_info['user_award_times']    = (int)$vo['user_award_times'];
				$this->_reward_info['period_award_amount'] = (int)$vo['period_award_amount'];
				break;
			}
		}

		unset($this->_reward_info['award_time']);

		// 未在获奖时间段
		if(empty($this->_reward_info['lottery_start']) || empty($this->_reward_info['lottery_end'])) {
			$this->_error(1006);
		}

		// 超过该时间段内奖品发放的数量
		if((int)$this->_reward_info['period_award_amount'] != -1) {
			// 获取该时段的key
			$key = sprintf($this->_reward_period_num, $this->_activity_id, $this->_reward_id);
			$field = (int)$this->_reward_info['lottery_start'] . ':' . (int)$this->_reward_info['lottery_end'];
			// 初始化
			$remain_times = $this->_get_op_redis()->set_key($key)->set_field($field)->set_expire($this->_reward_info['lottery_end'])->set_expire_type('unix')->set_decrement(-1)->initialize_hash((int)$this->_reward_info['period_award_amount']);
			if(!is_int($remain_times) || $remain_times <= -1) {
				$this->_error(1006);
			}

			// 设置回滚的key,field,偏移量
			$this->_set_trans_redis($key, $field, 1);
		}
	}

	/**
	 * 活动中奖限制
	 */
	private function _validate_restriction() {
		// ip限制
		if((int)$this->_activity_info['ip_award_times'] != -1) {
			// 获取该时段的key
			$key   = sprintf($this->_ip_reward, $this->_activity_id);
			$field = $this->_user_info['ip'];
			// 初始化
			$remain_times = $this->_get_op_redis()->set_key($key)->set_field($field)->set_expire($this->_activity_info['end_at'])->set_expire_type('unix')->set_decrement(-1)->initialize_hash((int)$this->_activity_info['ip_award_times']);
			if(!is_int($remain_times) || $remain_times <= -1) {
				$this->_error(1006);
			}

			// 设置回滚的key,field,偏移量
			$this->_set_trans_redis($key, $field, 1);
		}

		// 奖品天中奖上限
		$times_day = (int)$this->_reward_info['award_times_day'];
		if($times_day != -1) {
			// 获取该时段的key
			$key   = sprintf($this->_reward_day, $this->_activity_id, date('Ymd', $this->_time));
			$field = $this->_reward_info['id'];
			// 初始化
			$remain_times = $this->_get_op_redis()->set_key($key)->set_field($field)->set_expire(12 * 3600)->set_decrement(-1)->initialize_hash($times_day);
			if(!is_int($remain_times) || $remain_times <= -1) {
				$this->_error(1006);
			}

			// 设置回滚的key,field,偏移量
			$this->_set_trans_redis($key, $field, 1);
		}
	}

	/**
	 * 用户日中奖，活动中奖上限的限制
	 */
	private function _validate_reward_user() {
		// 用户日中奖上限限制
		$times_day = (int)$this->_activity_info['user_award_times_day'];
		if($times_day != -1) {
			$key = sprintf($this->_user_reward_day, $this->_activity_id, date('Ymd', $this->_time));
			// 初始化
			$remain_times = $this->_get_op_redis()->set_key($key)->set_field($this->_user_id)->set_expire(12 * 3600)->set_decrement(-1)->initialize_hash($times_day);

			if(!is_int($remain_times) || $remain_times <= -1) {
				$this->_error(1006);
			}

			// 设置回滚的key,field,偏移量
			$this->_set_trans_redis($key, $this->_user_id, 1);
		}

		// 用户活动中中该奖的上限限制
		$reward_times = (int)$this->_reward_info['user_award_times'];
		if($reward_times != -1) {
			$key = sprintf($this->_user_reward, $this->_activity_id, $this->_reward_id);
			// 初始化
			$remain_times = $this->_get_op_redis()->set_key($key)->set_field($this->_user_id)->set_expire($this->_activity_info['end_at'])->set_expire_type('unix')->set_decrement(-1)->initialize_hash((int)$times_day);
			if(!is_int($remain_times) || $remain_times <= -1) {
				$this->_error(1006);
			}

			// 设置回滚的key,field,偏移量
			$this->_set_trans_redis($key, $this->_user_id, 1);
		}

		// 活动中奖上限限制
		$times = (int)$this->_activity_info['user_award_times'];
		if($times != -1 && $this->_user_info['award_times'] > $times -1) {
			$this->_error(1006);
		}
	}

	/**
	 * [奖品库存限制]
	 */
	private function _validate_stock() {
		$key = sprintf($this->_reward_stock, $this->_activity_id, $this->_reward_id);
		// 初始化
		$this->_remain_stock = $this->_get_op_redis()->set_key($key)->set_expire($this->_activity_info['end_at'])->set_expire_type('unix')->set_decrement(-1)->initialize_hash((int)$this->_reward_info['remain']);

		if(!is_int($this->_remain_stock) || $this->_remain_stock <= -1) {
			$this->_error(1006);
		}

		// 设置回滚的key,field,偏移量
		$this->_set_trans_redis($key, '', 1);
	}

	/**
	 * 客户抽奖
	 */
	private function _get_rand_lottery() {
		// 更新数据库用户抽奖次数
		$this->load->model('user_info_model');
		$result = $this->user_info_model->save_user_by_name($this->_activity_id, $this->_user_name, array('lottery_times' => $this->_user_info['lottery_times'] + 1));
		if(empty($result)) {
			$this->_error(1006);
		}

		// 获取抽奖规则
		$this->load->model('lottery_rule_model');
		$post_arr = $this->lottery_rule_model->get_probability_rule($this->_activity_id);
		// 获奖id
		$this->_reward_id   = $this->_get_rand($post_arr);
		unset($post_arr);

		if(empty($this->_reward_id)) {
			$this->_error(1006);
		}
	}

	/**
	 * 获取当前抽奖时间
	 */
	private function _get_time() {
		$this->_time = time();
		return $this->_time;
	}

	/**
	 * [重写父类成功返回信息，加入日志]
     * @param  [string] $data [返回信息]
	 */ 
	protected function _success($data) {
		$log['activity_id'] = $this->_activity_id;
		$log['ip']	        = $this->_user_info['ip'];
		$log['user_id']	    = $this->_user_info['id'];
		$log['reward_id']	= $this->_reward_id;
		$log['award']       = award_model::$on;
		$log['created_at']  = $this->_time;
		// 记录到日志
		$this->_lottory_log($log);
		parent::_success($data);
	}

	/**
	 * [重写父类失败返回信息，加入日志]
     * @param  [string] $errorCode [错误码]
	 */ 
	protected function _error($errorCode) {
		// 只有进入到抽奖模块才写入用户日志
		if(!empty($this->_reward_id)) {
			$status = $this->_award_info[$this->_reward_info['award_id']]['status'];
			if(!empty($status) && (int)$status == award_model::$on) {
				// 回滚redis里面的数据
				$this->_get_op_redis()->trans_redis($this->_trans_redis);
				unset($this->_trans_redis);
			}

			$log['activity_id'] = $this->_activity_id;
			$log['ip']	        = $this->_user_info['ip'];
			$log['user_id']	    = $this->_user_info['id'];
			$log['reward_id']	= $this->_reward_id;
			$log['award']       = award_model::$off;
			$log['created_at']  = $this->_time;
			// 记录到日志
			$this->_lottory_log($log);
		}
		
		parent::_error($errorCode);
	}

	/**
	 * 设置需要回滚的redis数据
	 * @param [string] $key [需要回滚的redis的key]
	 * @param [string] $field [需要回滚的redis的field]
	 * @param [int] $offset [需要回滚的redis的偏移量]
	 */
	private function _set_trans_redis($key = '', $field = '', $offset = 0) {
		// 设置数组的key
		$i = count($this->_trans_redis);
		if(!empty($key)) {
			// 设置redis的key
			$this->_trans_redis[$i]['key'] = $key;
			// 设置redis的field
			if(!empty($field)) {
				$this->_trans_redis[$i]['field'] = $field;
			}
			// 设置redis的offset
			if(!empty($offset)) {
				$this->_trans_redis[$i]['offset'] = $offset;
			}
		}
	}
}
