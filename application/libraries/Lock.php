<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * [redis 的锁操作存储]
 */
class Lock {
	protected $_redis       = '';
	// 锁的key
	protected $_key         = '';
	// 设置key的前缀
	const KEY_PREFIX        = 'lock:';
	// 默认60000ms超时（ms）
    protected $_timeout     = 60000;
    // 过期时间
    protected $_expire_time = '';
    // 等待时间（ms）
    protected $_usleep_time = 1;
    // 设置等待锁
    protected $_lock_type   = true;


    public function __construct() {
        // 实例化redis
        $this->_redis = get_redis_instance();
    }

    /**
     * 设置锁的key
     */
    public function set_key($key) {
    	$this->_key = self::KEY_PREFIX . $key;
    	return $this;
    }

    /**
     * 设置过期时间
     */
    public function set_timeout($timeout) {
    	$this->_timeout = (int)$timeout;
    	return $this;
    }

    /**
     * [获取redis的锁]
     */
    public function get_lock() {
        if(empty($this->_key)) {
            return false;
        }
        // 初始化
        $lock   = 0;
        $return = false;
        $time   = $this->_get_timestamp();
        while ($lock != 1) {
            $lock  = $this->_redis->setnx($this->_key, $this->_get_expire_timestamp());
            if ($lock == 1) {
                $return = true;
                break;
            }
            
            if($this->_get_timestamp() > (float)$this->_redis->get($this->_key) && $this->_get_timestamp() > $this->_redis->getset($this->_key, $this->_get_expire_timestamp())) {
                $return = true;
                break;
            }

            if($this->_lock_type) {
                break;
            }

            if (!is_null($this->_timeout) && ($this->_get_timestamp() - $time) > $this->_timeout) {
                break;
            }

            usleep($this->_usleep_time * 1000);
        }
        
        return $return;
    }


    /**
     * 获取当前时间戳,毫秒级别
     */
    private function _get_timestamp() {
        $timestamp = microtime();
        $arr       = explode(' ', $timestamp);
        return round(($arr[0] + $arr[1])*1000);
    }

    /**
     * [获取锁到期的时间戳]
     * @param [float] $time [当前时间戳] 
     * @param [int] $timeout [超时时间]
     */
    private function _get_expire_timestamp() {
    	$this->_expire_time = (float)($this->_get_timestamp() + $this->_timeout + 1);
        return $this->_expire_time;
    }

    /**
     * 释放锁机制
     */
    public function delete() {
    	if (empty($this->_key)) {
    		return false;
    	}

    	$return = false;
    	if ($this->_expire_time >= (float)$this->_redis->get($this->_key)) {
    		$return = $this->_redis->delete($this->_key);
    	} else {
    		$return = true;
    	}

    	return $return;
    }

    /**
     * 析构函数：释放锁
     */
    public function __destruct() {
        $this->delete();
    }
}