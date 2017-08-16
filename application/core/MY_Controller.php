 <?php
defined('BASEPATH') OR exit('No direct script access allowed');

//基础控制器
class MY_Controller extends CI_Controller {
	function __construct(){
		parent :: __construct();

		if (file_exists($file_path = APPPATH.'config/defined.php'))
		{
			require_once($file_path);
		}
		$this->init();
	}
	
	private function init(){
		//设置config
		$this->config->set_item('base_url', _URL_);
		$this->config->set_item('encryption_key', _ENCRYPTION_KEY_);
		$this->config->set_item('time_reference', date_default_timezone_set(_TIME_ZONE_));

		//加载数据库类
		$dbConfig = array(
			'dsn'	   => '',
			'hostname' => _DB_HOSTNAME_,
			'username' => _DB_USERNAME_,
			'password' => _DB_PASSWORD_,
			'database' => _DB_DATABASE_,
			'dbdriver' => _DB_DBDRIVER_,
			'dbprefix' => _DB_DBPREFIX_,
			'pconnect' => FALSE,
			'db_debug' => (ENVIRONMENT !== 'production'),
			'cache_on' => FALSE,
			'cachedir' => APPPATH.'/cache/',
			'char_set' => 'utf8',
			'dbcollat' => 'utf8_general_ci',
			'swap_pre' => '',
			'encrypt'  => FALSE,
			'compress' => FALSE,
			'stricton' => FALSE,
			'failover' => array(),
			'save_queries' => TRUE
		);
		$this->load->database($dbConfig);
	}

	protected function _encode_json($arr) {
		if (version_compare(PHP_VERSION,"5.4",">")) {
			$code = json_encode($arr,JSON_UNESCAPED_UNICODE);
		} else {
			$code = json_encode($arr);
			$code = preg_replace("#\\\u([0-9a-f]{4})#ie", "iconv('UCS-2', 'UTF-8', pack('H4', '\\1'))", $code);
			$code = stripslashes($code);
		}
		return $code;
	}
}

//通用控制器
class PUBLIC_Controller extends MY_Controller {
	function __construct() {
		parent :: __construct();
		$this->init();
	}

	private function init(){
		$this->load->library('form_validation');
		$this->load->helper(array('file','url'));
		// $this->config->set_item('sess_driver','database');
		// $this->config->set_item('sess_save_path','sessions');
		$this->load->library('session');
		if(ENVIRONMENT == 'development'){
			// $this->output->enable_profiler(TRUE);
		}
	}

	protected function _success($msg,$url=''){
		header("Content-type: text/html; charset=utf-8");
		if($url){
			echo '<script>alert("'.$msg.'");document.location.href="'.$url.'";</script>';
		}else{
			echo '<script>alert("'.$msg.'");history.back();</script>';
		}
		die;
	}

	public function success($msg){
		header('Content-type: application/json');
		exit(json_encode(array('code'=>1,'msg'=>$msg,'data'=>array(),'url'=>'')));
	}

	protected function _error($msg,$url=''){
		header("Content-type: text/html; charset=utf-8");
		if($url){
			echo '<script>alert("'.$msg.'");document.location.href="'.$url.'";</script>';
		}else{
			echo '<script>alert("'.$msg.'");history.back();</script>';
		}
		die;
	}

	public function error($msg){
		header('Content-type: application/json');
		exit(json_encode(array('code'=>0,'msg'=>$msg,'data'=>array(),'url'=>'')));
	}
}

class USER_Controller extends PUBLIC_Controller{
	/* function __construct() {
		parent :: __construct();
		$this->has_login();
		//加载自定义函数
		$this->load->helper('common');
		//session存在时，不需要验证的权限
		$not_check = array(
			'admin/index',
			'admin/main',
			'admin/clear_cache',
			'admin/edit_pwd',
			'login/logout',
		);
		$url = strtolower($this->uri->segment(1).'/'.$this->uri->segment(2));
		//当前操作的请求 模块名/方法名
		if(in_array($url, $not_check)){
			return true;
		}
		//下面代码动态判断权限
		$this->load->library('auth');
		if(!$this->auth->check($url,$this->session->userdata('aid')) && $this->session->userdata('aid') != 1){
			$this->_error('很抱歉，您没有该操作权限');
		} else {
			//日志记录
			$this->log($url);
		} */
		
	function __construct()
	{
		parent::__construct();
		$this->has_login();
		$this->load->helper('common');
		$not_check = array('admin/index','admin/main','admin/clear_cache','admin/edit_pwd','login/logout');
		$url = strtolower($this->uri->segment(1) . '/' . $this->uri->segment(2));
		if (in_array($url, $not_check))
		{
			return true;
		}
		$this->load->library('auth');
		if (!$this->auth->check($url, $this->session->userdata('aid')) && $this->session->userdata('aid') !=1)
		{
			$this->_error('sorry! You have not permission');
		}
		else
		{
			$this->log($url);
		}
	}

	private function has_login(){
		$id = $this->session->userdata('aid');
		if(!$id){
			redirect(base_url('login/index'));
		}
	}

	//写入日志
	private function log($url=''){
		if(!(strpos($url,'log/') === FALSE)) {
			return;
		}
		$method = $this->input->method();
		if($method == 'get') {
			$param = $this->input->get()?json_encode($this->input->get()):'';
		} elseif($method == 'post') {
			$param = json_encode($this->input->post());
		}
		$arr = array(
			'user_id'=>$this->session->userdata('aid'),
			'url'=>$url,
			'method'=>$method,
			'param'=>$param,
			'ip'=>$this->input->ip_address(),
			'time'=>time(),
			);
		$this->db->insert('log',$arr);
	}

	public function _view($content, $data=array()){
		// $n = 1; //分钟
		// $this->output->cache($n);
		//头部
		$this->load->view('public/header.html',$data);
		//内容
		if(is_array($content)) {
			foreach ($content as $key => $val) {
				$this->load->view($val,$data[$key]);
			}
		}else {
			$this->load->view($content,$data);
		}
		//底部
		$this->load->view('public/footer.html',$data);
	}

	//获取导航菜单
	protected function _get_menu(){
        $this->load->library('auth');
        $groups = $this->auth->getGroups($this->session->userdata('aid'));
        $arr = array();
        foreach ($groups as $key => $value) {
        	$arr = array_merge(explode(',', $value['rules']),$arr);
        }
        $rs = $this->db->from('auth_rule')->select('id,icon,name,title,pid,show')->order_by('sort desc,id asc')->where_in('id',$arr)->where(array('status'=>1,'show'=>1))->get()->result_array();
        //转换成树形结构
        $this->load->library('tree');
        $menu = $this->tree->toLayer($rs);
        //去除无效
        foreach ($menu as $k=>$v){
        	if($v['name']=='#' && !isset($v['child'])){
        		unset($menu[$k]);
        	}
        }
        return $menu;
	}
	public function is_supper(){
		$id = $this->session->userdata('aid');
        $group_id = $this->db->from('auth_group_access')->select('group_id')->where(array('uid'=>$id))->get()->row_array();
        if ($group_id['group_id']==1) {
        	return true;
        }else{
        	return false;
        }
	}
	public function _log_message($action,$object,$jsonparam){
		$id = $this->session->userdata('aid');
		log_message('info','Admin '.$id.' '.$action.' '.$object.' '.$jsonparam,'user');
	}
}
