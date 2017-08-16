<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 对外文档接口查看
 */

class Api_doc {
    // 查询文档的路径
	private $_path     =  '';
    // 需要读取的模块名称
    private $_module   = '';
    // 需要读取的action名称
    private $_action   = '';
    // 类注释
    private $_r_class  = '';
    // 方法注释
    private $_r_method = '';
	/**
	 * 配置参数
	 * @param  [array] $config [参数]
	 */
	public function __construct() {
        // 获取需要读取的模块文件名称
        $this->_module = ucfirst($_GET['module']);
        $this->_action = $_GET['action'];
        // 路径
        $this->_path   = APPPATH . 'controllers/';

        // 引入该文件
        if(file_exists($this->_path . $this->_module . '.php')) {
            if(!class_exists($this->_module)) {
                require_once($this->_path . $this->_module . '.php');
            }
        }

        // 类有注释
        $this->_r_class  = new ReflectionClass($this->_module);
        // 方法注释
        $this->_r_method = new Reflectionmethod($this->_module, $this->_action);
	}

    /**
     * [获取标题注释]
     */
    protected function _get_comment($comment = '') {
        $arr = array();
        if(!empty($comment)) {
            array_walk(explode('*', trim($comment, '/')), function($value, $key) use (&$arr){
                if(!empty($val = trim($value))) {
                    $arr[] = $val;
                }
            });
        }
        
        return $arr;
    }

    /**
     * [获取参数注释]
     */
    public function get_param() {

    }

    /**
     * [获取返回参数的注释]
     */
    public function get_return() {

    }

    /**
     * 获取当前URL
     * @param   string  $uri
     * @param   string  $protocol
     * @return  string
     */
    protected function _base_url($uri = '', $protocol = NULL) {
        return get_instance()->config->base_url($uri, $protocol);
    }

    /**
     * [获取文档内容并返回]
     */
    public function get_doc() {
        // 类的描述
        $class_comment  = $this->_r_class->getDocComment();
        $description    = current($this->_get_comment($class_comment));

        // 方法的描述
        $method_comment = $this->_r_method->getDocComment();
        $method_desc    = $this->_get_comment($method_comment);
        $rules = array();
        foreach ($method_desc as $key => $val) {
            // 方法描述的注释的判断
            if(stripos($val, '@') ===  FALSE) {
                $descComment = $val;
                continue;
            }
            // 获取接口请求的URL
            $request = stripos($val, '@request');
            $arr     = array();
            if($request !== FALSE) {
                $val = substr($val, $request + 8);
                $arr = array_values(array_filter(explode(' ', $val)));
                // 请求的方式
                $request_type = $arr[0];
                // 请求的URL
                $url          = $this->_base_url($arr[1]);
                    continue;
            }

            // 请求的参数
            $param = stripos($val, '@param');
            $arr   = array();
            if($param !== FALSE) {
                $val = substr($val, $param + 6);
                array_walk(explode(' ', $val), function($vo, $k) use (&$arr){
                    if(!empty($trim = trim($vo))) {
                        $arr[] = $trim;
                    }
                });
                // type
                $rules[$key]['type']    = $arr[0];
                // name
                $rules[$key]['name']    = ltrim($arr[1], '$');
                // 描述desc
                $rules[$key]['desc']    = $arr[2];
                // require
                $rules[$key]['require'] = $arr[3];
                // 默认值
                $rules[$key]['default'] = $arr[4];
                continue;
            }

            // 返回参数
            $return = stripos($val, '@return');
            $arr    = array();
            if($return !== FALSE) {
                $val = substr($val, $return + 7);
                array_walk(explode(' ', $val), function($vo, $k) use (&$arr){
                    if(!empty($trim = trim($vo))) {
                        $arr[] = $trim;
                    }
                });

                // 返回数据 type
                $returns[$key]['type']    = $arr[0];
                // name
                $returns[$key]['name']    = ltrim($arr[1], '$');
                // 描述desc
                $returns[$key]['desc']    = $arr[2];
            }
        }
        $module = $this->_module;
        $action = $this->_action;
        // 引入模版
        include(APPPATH . "views/doc/index.html");
    }
}