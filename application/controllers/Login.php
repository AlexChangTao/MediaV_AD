<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends Public_Controller {

	public function __construct(){
		parent :: __construct();
	}

	public function index()
	{
        if($this->session->userdata('aid') && $this->session->userdata('account')){
            redirect(base_url('admin/index'));
            die;
        }
		$this->load->view('public/login.html');
	}

    //登录验证
    public function to_login()
    {
    	if(!empty($_POST)){
            $code = $this->input->get_post('code');
            $this->load->library('verifycode');
            if(!$this->verifycode->check($code)){
                $this->_error('验证码错误');
            } else {
                $this->session->set_userdata('auth_code',null);
            }
    		$map['account'] = $this->input->get_post('account');             //用户名
    		$map['password'] = md5($this->input->get_post('password'));	     //密码
    		$result = $this->db->select('id,account,login_count,status')->where($map)->limit(1)->get('admin')->row_array();
    		if($result){
    			if($result['status'] == 0){
    				$this->_error('登录失败，账号被禁用');
    			}
                //检查所在用户组
                $this->load->library('auth');
                $group = $this->auth->getGroups($result['id']);
                if(!count($group)){
                    $this->_error('登录失败，账号被禁用');
                }
                //保存登录信息
                $data['login_ip']    = $this->input->ip_address();    //最后登录IP
                $data['login_time']  = time();                      //最后登录时间        
                $data['login_count'] = $result['login_count'] + 1;
                $this->db->where(array('id'=>$result['id']))->update('admin',$data);
                $this->session->set_userdata('aid',$result['id']);             //管理员ID
                $this->session->set_userdata('account',$result['account']);    //用户名
                $this->session->set_userdata('login_ip',$data['login_ip']);
                $this->session->set_userdata('login_time',$data['login_time']);
                redirect(base_url('admin/index'));
    		}else{
                redirect(base_url('login/index'));
    		}
    	}else{
    		if($this->session->userdata('aid') && $this->session->userdata('account')){
    			redirect(base_url('admin/index'));
    		}
    		redirect(base_url('login/index'));
    	}
    }

    //退出登录
    public function logout(){
        $this->session->set_userdata('aid',null);
        $this->session->set_userdata('account',null);
        $this->_success('退出登录成功',base_url('login'));
    }

    //显示验证码
    public function get_code(){
        $this->load->library('verifycode');
        $this->verifycode->show();
    }

    //ajxa检查验证码
    public function check_code()
    {
        $code = $this->input->get_post('code');
        $this->load->library('verifycode');
        if($this->verifycode->check($code)){
            exit(json_encode(1));
        } else {
            exit(json_encode(0));
        }
    }
}