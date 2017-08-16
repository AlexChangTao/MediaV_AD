<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * basic class 活动站抽奖接口总控制器
 */

class Basic_Controller extends CI_Controller {

	const TOKEN_PREFIX = 'mediav.lottory';
	// 最大日志存储量
	const LOG_MAX      = 500;
	// 错误码
	protected $_error_code = array();
	// 抽奖活动信息
	protected $_act_info_key  = 'lottery:%s:info';
	// 日志缓存key
	protected $_log_cache_key = 'lottery:log:info';

	/**
	 * [加载错误码,访问来源控制]
	 */
	public function __construct() {
		parent::__construct();

		if(file_exists(APPPATH.'config/error_code.php')) {
			require_once(APPPATH.'config/error_code.php');
		}
		if(file_exists(APPPATH.'config/'.ENVIRONMENT.'/error_code.php')) {
			require_once(APPPATH.'config/'.ENVIRONMENT.'/error_code.php');
		}
		$this->_error_code = $error_code;

		// 只开放唯一入口api
		$controller = $this->uri->segment(1);
		if(strtolower($controller) != 'open_api') {
			$this->_error(1000);
		}
	}

	/**
     * [访问来源控制]
     * @return [type] [description]
     */
    protected function _accessValidate() {}

	/**
	 * [日志记录]
	 */
	protected function _accessLog() {

	}

	/**
	 * 抽奖请求日志
	 * @param [arary] $data [数据]
	 */
	protected function _lottory_log($data) {
		$redis = get_redis_instance();
		$result= $redis->rpush($this->_log_cache_key, $data);
		if($redis->llen($this->_log_cache_key) >= self::LOG_MAX) {
			$arr = $redis->lrange($this->_log_cache_key);
			array_walk($arr, function($value, $key) use (&$add_batch){
    			$add_batch[] = unserialize($value);
			});
			$this->load->model('lottery_log_model');
			if($this->lottery_log_model->add_log($add_batch, self::LOG_MAX)) {
				$redis->ltrim($this->_log_cache_key, self::LOG_MAX);
			}
		}
	}

	/**
	 * 校验ip
	 * @param [string] $ip [参数ip]
	 * @return bool
	 */
	protected function _check_ip($ip) {
		$this->load->model('disable_ip_model');
		return $this->disable_ip_model->validate_ip($ip);
	}

	/**
	 * [生成token]
	 * @param [string] $ip [参数ip]
	 * @return string
	 */
	protected function _create_token($ip) {
		return md5(sha1(TOKEN_PREFIX) . ip2long($ip));
	}

	protected function _check_token($string) {

	}

	/**
	 * 抽奖算法
	 */
	protected function _get_rand($post_arr) {
		if(empty($post_arr)) {
			return false;
		}

		$sum = array_sum($post_arr);
		$num = '';
		$result = '';
		foreach ($post_arr as $key => $val) {
			$num = mt_rand(1, $sum);
			if($num <= $val) {
				$result  = $key;   
	            break;
			} else{
				$sum    -= $val;
			}
		}
		unset($post_arr);
		return $result;
	}

	/**
	 * [成功返回信息]
     * @param  [string] $data [返回信息]
	 */
	protected function _success($data) {
		$errMsg = $this->_errMsg('200');
        $return = array(
            'errCode' => '200',
            'errMsg'  => $errMsg,
            'status'  => true,
            'data'    => $data
        );
        $this->_return($return);
	}

	/**
	 * [失败返回信息]
     * @param  [string] $errorCode [错误码]
	 */
	protected function _error($errorCode) {
		$errMsg = $this->_errMsg($errorCode);
        $return = array(
            'errCode' => $errorCode,
            'errMsg'  => $errMsg,
            'status'  => false,
            'data'    => array()
        );
        $this->_return($return);
	}

	/**
     * [获取错误码配置]
     * @param  [string] $errorCode [错误码]
     */
    private function _errMsg($errorCode) {
		return $this->_error_code[(string)$errorCode];
    }

    /**
	 * [返回数据包装]
	 */
	private function _return($data) {
        $return = json_encode($data);
        echo $return;
        exit();
	}


	/**
	 * [获取锁机制]
	 * @param [string] $key [锁的key]
	 */
	protected function _get_lock($key) {
		$this->load->library('lock');
        return $this->lock->set_key($key)->get_lock();
	}

	/**
	 * [释放锁机制]
	 * @param [string] $key [锁的key]
	 */
	protected function _del_lock($key) {
		$this->load->library('lock');
        return $this->lock->set_key($key)->delete();
	}

	/**
	 * 获取op_reids的实例
	 */
	protected function _get_op_redis() {
		$this->load->library('op_redis');
		return $this->op_redis;
	}
}