<?php

// +----------------------------------------------------------------------
// | Author: Cindy <nanxin@mediav.cn>
// +----------------------------------------------------------------------

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 对外文档接口
 */

class Doc extends CI_Controller {

	/**
	 * 显示页面
	 **/
	public function index() {
		// 展示文档
		$this->load->library('Api_doc');
		$this->api_doc->get_doc();
	}
}