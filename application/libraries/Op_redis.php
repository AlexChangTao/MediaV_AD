<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * [redis 的数据操作存储]
 */
class Op_redis {

    protected $_key         = '';
    // 哈希值的field
    protected $_field       = '';
    // 过期时间
    protected $_expire      = null;
    // 过期时间存储类型(默认为s)
    protected $_expire_type = 'seconds';
    // 原子操作的偏移值
    protected $_offset      = 0;
    // 是否使用管道模式
    protected $_multi       = false;

	/**
     * 取得缓存类实例
     * @static
     * @param [bool] $reconect [是否重连]
     * @return mixed
     */
    static function get_instance($reconect = false) {
		static $_instance =	array();
        if(!isset($_instance['redis']) || $reconect){
			$CI = & get_instance();
			$CI->load->driver('cache');
			$_instance['redis']	=	$CI->cache->redis;
		}
		return $_instance['redis'];
    }

    /**
     * 设置key
     */
    public function set_key($key) {
        $this->_key = $key;
        return $this;
    }

    /**
     * 设置field
     */
    public function set_field($field) {
        $this->_field = $field;
        return $this;
    }

     /**
     * 设置过期时间
     */
    public function set_expire($time) {
        $this->_expire = $time;
        return $this;
    }

    /**
     * 设置过期时间类型[unix|seconds]
     */
    public function set_expire_type($type) {
        $this->_expire_type = $type;
        return $this;
    }

    /**
     * 原子操作field
     */
    public function set_decrement($offset='') {
        $this->_offset = $offset;
        return $this;
    }

    /**
     * 获取redis实例
     * @param [bool] $multi [是否使用管道实例]
     */
    public function set_redis_type($multi = false) {
        $this->_multi = $multi;
        return $this;
    }

    /**
     * 存储
     */
    public function hset($value) {
        if(empty($this->_key)) {
            return false;
        }

        $redis = get_redis_instance();

        if(empty($this->_field)) {// set存储
            $result = $redis->save($this->_key, $value);
        }else {// 哈希存储
            $result = $redis->hset($this->_key, $this->_field, $value);
        }
        
        // 设置过期时间
        if(!is_null($this->_expire)) {
            switch ($this->_expire_type) {
                case 'seconds':
                    $redis->expire($this->_key, $this->_expire);
                    break;
                case 'unix':
                    $redis->expireat($this->_key, $this->_expire);
                    break;
            }
        }
        return $result;
    }


    /**
     * [初始化哈希field的值或者set的值]
     * @param [mixd] $value [需要被存储的哈希值]
     * @return [mixd]
     */
    public function initialize_hash($value) {
        if(empty($this->_key)) {
            return false;
        }
        $redis = get_redis_instance();
        $result= true;
        if(empty($this->_field)) {// set存储
            if($redis->exists($this->_key) === false) {
                $result = $this->hset($value);
            }
        }else {// hash存储
            if($redis->hexists($this->_key, $this->_field) === false) {
                $result = $this->hset($value);
            }
        }
        
        // 原子偏移量,减为加上负数
        if($result && !empty($this->_offset)) {
            if(empty($this->_field)) {
                $result = $redis->increment($this->_key, $this->_offset);
            }else {
                $result = $redis->hincrby($this->_key, $this->_field, $this->_offset);
            }
        }

        return $result;
    }

    /**
     * [redis 的批量操作, 管道操作]
     * @param [array] $trans_data [需要批量操作的数据]
     * @return bool
     */
    public function trans_redis($trans_data) {
        if(empty($trans_data)) {
            return false;
        }

        $result = true;
        // 设置管道批量操作redis数据
        $redis  = get_redis_instance();
        $pipe   = $redis->multi(Redis::PIPELINE);
        foreach ($trans_data as $val) {
            if(empty($val)) {
                continue;
            }

            if(!empty($val['key'])) {
                // 数据设置
                if(!isset($val['field']) || empty($val['field'])) {
                    $pipe->incr($val['key'], $val['offset']);
                }else {
                    $pipe->hincrby($val['key'], $val['field'], $val['offset']);
                }
            }
        }
        $result = $pipe->exec();

        return $result;
    }
}