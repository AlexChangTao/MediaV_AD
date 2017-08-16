<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * @ClassName 媒体
 * @author AlexChangTao  1692576541@qq.com
 * @Date 2017-06-10 10:00:00
 */
class C_media extends USER_Controller {
	public static $isTest = false;
	public $user_id;
	public function __construct() {
		parent :: __construct();
		$this->load->model('M_media');
	}
	
	public function index(){
		//分页类
		$this->load->library('pagination');//用它才能使用下面的this->_view()方法能加载header.html
		$this->load->library('pager');//做一些分页面的准备工作
		$keyword = $this->input->get('keyword');
		$type = $this->input->get('type');
		//$page = $this->input->get('page');
		$order = $this->input->get('order');
		$id = $this->input->get('id');
		$data['id'] = $id;
		$latest = 1; //设置为真以默认实现查询最新作品列表
		if(!$page || $page < 1){
			$page=1;
		}
		if(!$type){
			$type=1;
		}
		$page_config['total_row'] =$this->M_media->getMediaListCount($keyword,$type);
		$page_config['cur_page'] = $this->uri->segment(3,1);//生成分布选择按钮,page为当前页要与下面的page和地址栏中的当前页一致
		$page_config['base_link_url'] = site_url('C_media/index/');
		$page_config['page_size'] = 10;
		$pageSize = 10;////要跟上面的pageSize保持一致
		$data['page'] = $this->pager->create($page_config);
		$data['media'] = $this->M_media->getMediaList($pageSize, $pageSize * intval($this->uri->segment(3,1)-1), $keyword,$type,$latest);
		$this->_view('media/media.php', @$data);
		
	}
	
	/*
	 * 删除一个媒体
	 */
    public function del_media(){
    	$id = $this->input->get('id');
    	if ($this->M_media->del_media($id)) {
    		$this->returnJson(0, '删除成功！');
    	} else {
    		$this->returnJson(1001, '删除失败！');
    	}
    }
    
    /**
     * 添加媒体
     */
    public function add_media(){
    	if(IS_POST){
			$media_name = $this->input->post('media_name');
			$status     = $this->input->post('status');
			$identifier = $this->input->post('identifier');
			$remark     = $this->input->post('remark');
			if(!$media_name || !$identifier ){
				$this->returnJson(1002,'缺少参数！');
			}
			$r = $this->M_media->add_media($media_name, $identifier, $status, $remark);
			if($r>0){
                 $this->_log_message('Add','Media',json_encode(array('media'=>$media_name,'status'=>$status,'identifier'=>$identifier,'remark'=>$remark)));
				$this->returnJson(0,'成功');
			}else {
				$this->returnJson(1005,'添加失败！');
			}
    	} else {
    		$this->_view('media/add_media.php');
    	}
    }
    
    /**
     * 修改媒体
     */
    public function alter_media(){
    	if(IS_POST){
	    	$media_name = $this->input->post('media_name');
	    	$status = $this->input->post('status');
	    	$identifier = $this->input->post('identifier');
	    	$id = $this->input->post('id');
	    	$remark = $this->input->post('remark');
	    	 if(!$media_name || !$identifier  || !$id ){ //当status为为0时会提示缺少参数且不是0就会是1所以不要判断status,另外remark可以为空
	    		$this->returnJson(1002,'缺少参数！');
	    	} 
	    	$r = $this->M_media->alter_media($media_name, $identifier, $status, $id, $remark);
	    	if($r>0){
                $this->_log_message('Update','Media '.$id,json_encode(array('media'=>$media_name,'status'=>$status,'identifier'=>$identifier,'remark'=>$remark)));
	    		$this->returnJson(0,'成功');
	    	}else {
	    		$this->returnJson(1005,'没有任何更改，修改失败！');
	    	}
    	} else {
    		$data['media'] = $this->db->where('id',$this->input->get('id'))->get('media')->row();
    		$this->_view('media/alter_media.php',$data);
    	}
    }

	/**
	 * 返回指定格式的json数据
	 * @param unknown $code
	 * @param unknown $msg
	 * @param array $data
	 */	
	function returnJson($code,$msg,$data=array()){
		exit(json_encode(array('code'=>$code, 'msg'=>$msg, 'data'=>$data)));
	}
	
	public function user_add()
	{
		$this->load->library('form_validation');
		$this->form_validation->set_rules('account','帐号','trim|required|min_length[2]');//汉字和英文输入xss_clean不能通过
		$this->form_validation->set_rules('password','密码','trim|required|min_length[2]');//汉字和英文输入xss_clean不能通过
		$this->form_validation->set_rules('create_time','日期','trim|required|min_length[2]');
		if ($this->form_validation->run() == false)
		{
			$data['title'] = '管理员管理';
			$data['advertisers'] = $this->M_admin->get_advertisers();
			$data['data'] = $this->M_admin->get_auth_group($where);
			$this->_view('admin/admin_add.html',$data);
		}
		else
		{
			$form = self::get_data();
			$this->M_admin->add_or_update();
		}
	}
	
	public function get_data()
	{	
		if (!empty($_POST))
		{
			 $form = array(
		 		 	'account' => $this->input->post('account'),
		 			'password' => md5($this->input->post('password')),
		 			'create_time' =>time(),
			 );
			 return $form;
		}
		else 
		{
			return false;
		}
	}
	
	
}
