<?php
defined('BASEPATH') OR exit('No direct script access allowed');

//后台管理员
class Admin extends User_Controller {

    public function __construct(){
        parent :: __construct();
    }

    public function index(){
        $data['title'] = '管理首页';
        $data['menu'] = $this->_get_menu();
        $this->_view('index/index.html',$data);
    }

    public function main(){
        $this->_view('index/main.html');
    }

    public function sysinfo(){
        $this->load->library('sysinfo');
        $sysinfo = $this->sysinfo->getinfo();
        $os = explode(' ', php_uname());
        //网络使用状况
        $net_state = null;
        if ($sysinfo['sysReShow'] == 'show' && false !== ($strs = @file("/proc/net/dev"))){
            for ($i = 2; $i < count($strs); $i++ ){
                preg_match_all( "/([^\s]+):[\s]{0,}(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)\s+(\d+)/", $strs[$i], $info );
                $net_state.="{$info[1][0]} : 已接收 : <font color=\"#CC0000\"><span id=\"NetInput{$i}\">".$sysinfo['NetInput'.$i]."</span></font> GB &nbsp;&nbsp;&nbsp;&nbsp;已发送 : <font color=\"#CC0000\"><span id=\"NetOut{$i}\">".$sysinfo['NetOut'.$i]."</span></font> GB <br />";
            }
        }
        $data['sysinfo'] = $sysinfo;
        $data['os'] = $os;
        $data['net_state'] = $net_state;
        $this->_view('index/sysinfo.html',$data);
    }

    //用户列表
    public function admin_list(){
        $data['data'] = $this->db->select('id,account,advertisers,login_time,login_ip,create_time,status')
        ->from('admin')
        ->order_by('id DESC')
        ->limit(PAGE_SIZE,@$_GET['p']*PAGE_SIZE)
        ->get()
        ->result_array();
        $data['advertisers'] = $this->db->get('advertisers')->result_array();
        $this->load->library('auth');
        foreach ($data['data'] as $key => $value) {
            $group = $this->auth->getGroups($value['id']);
            $data['data'][$key]['group'] = $group[0]['title'];
        }
        $this->load->library('pager');
        $page_config['total_row'] = count($data['data']);
        $page_config['cur_page'] = 1;
        $page_config['base_link_url'] = base_url('admin/admin_list?p=');
        $page_config['page_size'] = PAGE_SIZE;
        $data['page'] = $this->pager->create($page_config);
        $data['title'] = '管理员管理';
        $this->_view('admin/admin_list.html',$data);
    }

    //检查账号是否已注册
    public function check_account(){
        $where['account'] = $this->input->get_post('account');//账号
        $data = $this->db->from('admin')->select('id')->where($where)->get()->row_array();
        if(empty($data)){
            exit(json_encode(0));//不存在
        }else{
            exit(json_encode(1));//存在
        }
    }
        
    //添加用户
    public function admin_add(){
        if(!empty($_POST)){
            $data['account']     = $this->input->get_post('account');
            $data['password']    = md5($this->input->get_post('password'));
            $data['create_time'] = time();//创建时间
            $where['account']    = $this->input->get_post('account');
            $result              = $this->db->where($where)->get('admin')->row_array();
            
            if(!empty($result)){
                exit(json_encode(0));//用户名重复
            }
            //拥有的广告主
            $data['advertisers'] = implode(',', $_POST['advertisers']);
            //添加用户
            $r = $this->db->insert('admin',$data);

            //分配用户组
            $result['uid']  = $this->db->insert_id();
            $result['group_id'] = $this->input->get_post('group_id');   //用户组ID
            if($result['uid']){
                $r = $this->db->insert('auth_group_access',$result);
                if($r){
                    exit(json_encode(1));//分配用户组成功
                }else{
                    exit(json_encode(2));//分配用户组失败
                }
            }else{
                exit(json_encode(0));//添加用户失败
            }
        }else{
            $data['data'] = $this->db->where('status',1)->order_by('id','desc')->get('auth_group')->result_array();
            $data['advertisers'] = $this->db->from('advertisers')->get()->result_array();
            $data['title'] = '管理员管理';
            $this->_view('admin/admin_add.html',$data);
        }
    }

    //编辑
    public function admin_edit(){
        if(!empty($_POST)){
            if(empty($_POST['group_id'])){
                $this->_error('请选择用户组');
            }
            $result = $this->db->from('auth_group_access')->where(array('uid'=>$_POST['id']))->get()->row_array();
            if(empty($result)){
                $map['uid']      = $_POST['id'];
                $map['group_id'] = $_POST['group_id'];
                $this->db->insert('auth_group_access',$map);
            } else {
                $map['group_id'] = $_POST['group_id'];
                $this->db->where(array('uid'=>$_POST['id']))->update('auth_group_access',$map);
            }
            $data['id']     = $_POST['id'];
            $data['mobile'] = $_POST['mobile'];
            $data['email']  = $_POST['email'];
            if($this->session->userdata('aid') == _MANAGER_ID_){
                $data['password'] = md5($_POST['password']);
            }
            if($_POST['status'] >= 0){
                $data['status'] = $_POST['status'];
            }
            //拥有的广告主
            $data['advertisers'] = implode(',', $_POST['advertisers']);
            $result = $this->db->where(array('id'=>$data['id']))->update('admin',$data);
            if($this->db->affected_rows()) {
                $this->_success('修改成功');
            }else{
                $this->_error('修改失败');
            }
        }else{
            $result = $this->db->where(array('id'=>$this->input->get_post('id')))->get('admin')->row_array();
            $this->load->library('auth');
            $group = $this->auth->getGroups($result['id']);
            $result['title'] = $group[0]['title'];
            $result['group_id'] = $group[0]['group_id'];
            $data['data'] = $result;
            $data['group'] = $this->db->where('status',1)->order_by('id','desc')->get('auth_group')->result_array();
            $data['advertisers'] = $this->db->from('advertisers')->get()->result_array();
            $advertisers_arr = explode(',',$result['advertisers']);
            foreach ($advertisers_arr as $key => $value) {
                foreach ($data['advertisers'] as $k => $v) {
                    if($value == $v['id']){
                        $data['advertisers'][$k]['on'] = 1;
                        break;
                    }
                }
            }
            $data['title'] = '管理员管理';
            $this->_view('admin/admin_edit.html',$data);
        }
    }
    
    //删除用户
    public function admin_del(){
        $id = $_POST['id'];//用户ID
        if($id == 1){
            exit(json_encode(0));//不允许删除超级管理员
        }
        $this->db->where('uid='.$id)->delete('auth_group_access');
        $this->db->where('id='.$id)->delete('admin');
        if ($this->db->affected_rows()){
            exit(json_encode(1));//成功
        } else {
            exit(json_encode(0));//删除失败
        }
    }

    //角色-组
    public function auth_group(){
        $rs = $this->db->from('auth_group')->order_by('id DESC')->get()->result_array();
        $data['data'] = $rs;
        $this->load->library('pager');
        $page_config['total_row'] = count($data['data']);
        $page_config['cur_page'] = 1;
        $page_config['base_link_url'] = base_url('admin/auth_group?p=');
        $page_config['page_size'] = PAGE_SIZE;
        $data['page'] = $this->pager->create($page_config);
        $data['title'] = '用户组管理';
        $this->_view('admin/auth_group.html',$data);
    }
    
    //添加组
    public function group_add(){
        if(!empty($_POST)){
            $data['rules'] = $this->input->post('rules');
            if(empty($data['rules'])){
                $this->error('权限不能为空');
            }
            $data['title'] = $this->input->post('title');
            $data['status'] = $this->input->post('status');
            $data['rules'] = implode(',', $data['rules']);
            $result = $this->db->insert('auth_group',$data);
            if($result){
                $this->_success('添加成功',base_url('admin/auth_group'));
            }else{
                $this->_error('添加失败');
            }
        }else{
            $rs = $this->db->from('auth_rule')->select('id,name,title,pid')->order_by('sort desc,id asc')->get()->result_array();
            $this->load->library('tree');
            $rs = $this->tree->toList($rs);
            $data['data'] = $rs;
            $data['title'] = '用户组管理';
            $this->_view('admin/group_add.html',$data);
        }   
    }

    // 编辑组
    public function group_edit(){
        if(!empty($_POST)){
            $id = $this->input->post('id');
            $data['title'] = $this->input->post('title');
            $data['status'] = $this->input->post('status');
            $data['rules'] = !empty($this->input->post('rules')) ? implode(',', $this->input->post('rules')) : '';
            $result = $this->db->where(array('id'=>$id))->update('auth_group',$data);
            if($result){
                $this->_success('修改成功');
            }else{
                $this->_error('修改失败');
            }
        }else{
            $where['id'] = $this->input->get('id');
            $result = $this->db->from('auth_group')->select('id,title,status,rules')->where($where)->get()->row_array();
            $result['rules'] = ','.$result['rules'].',';
            $data['result'] = $result;

            $rs = $this->db->from('auth_rule')->select('id,name,title,pid')->order_by('sort desc,id asc')->get()->result_array();
            $this->load->library('tree');
            $rs = $this->tree->toList($rs);
            $data['data'] = $rs;
            $data['title'] = '用户组管理';
            $this->_view('admin/group_edit.html',$data);
        }
    }    

    //删除组
    public function group_del(){
        $where['id'] = $this->input->get_post('id');
        $r = $this->db->where($where)->delete('auth_group');
        if($r){
            exit(json_encode(1));
        }else{
            exit(json_encode(0));
        }
    }
         
    //权限列表
    public function auth_rule(){
        $rs = $this->db->from('auth_rule')->order_by('sort desc,id asc')->get()->result_array();
        $this->load->library('tree');
        $rs = $this->tree->toList($rs);
        $data['data'] = $rs;
        $data['page'] = '';
        $data['title'] = '权限管理';
        $this->_view('admin/auth_rule.html',$data);
    }
    
    //权限添加
    public function rule_add(){
        if(!empty($_POST)){
            $data['id'] = '';
            $data['icon'] = $this->input->get_post('icon');
            $data['name'] = trim($this->input->get_post('name'));
            $data['title'] = trim($this->input->get_post('title'));
            $data['pid'] = $this->input->get_post('pid');
            $data['sort'] = $this->input->get_post('sort');
            $data['status'] = $this->input->get_post('status');
            $data['show'] = $this->input->get_post('show');
            $data['create_time'] = time();

            if($this->db->where(array('name'=>$data['name'],'title'=>$data['title']))->count_all_results('auth_rule') >= 1){
                if(IS_AJAX){
                    exit(json_encode(array('code'=>1001,'data'=>0,'msg'=>'该权限已经存在')));
                }
                $this->_success('该权限已经存在');//成功
            }

            $r = $this->db->insert('auth_rule',$data);
            if($r){
                if(IS_AJAX){
                    exit(json_encode(array('code'=>0,'data'=>1,'msg'=>'添加成功')));
                }
                $this->_success('添加成功');//成功
            }else{
                if(IS_AJAX){
                    exit(json_encode(array('code'=>1001,'data'=>0,'msg'=>'添加失败')));
                }
                $this->_error('添加失败');//失败
            }
        }else{
            $r = $this->db->from('auth_rule')->select('id,name,title,pid,create_time,status')->order_by('sort desc,id asc')->get()->result_array();
            $this->load->library('tree');
            $r = $this->tree->toList($r);
            $data['data'] = $r;
            $data['title'] = '权限管理';
            $this->_view('admin/rule_add.html',$data);
        }
    }

    //权限编辑
    public function rule_edit(){
        if(!empty($_POST)){
            $id = $this->input->get_post('id');
            $data['icon'] = $this->input->get_post('icon');
            $data['name'] = $this->input->get_post('name');
            $data['title'] = $this->input->get_post('title');
            $data['pid'] = $this->input->get_post('pid');
            $data['status'] = $this->input->get_post('status');
            $data['show'] = $this->input->get_post('show');
            $data['sort'] = $this->input->get_post('sort');
            $data['create_time'] = time();
            if($id==$data['pid']){
                $this->error('ID与父级ID冲突');
            }
            $r = $this->db->where(array('id'=>$id))->update('auth_rule',$data);
            if($r){
                if(IS_AJAX){
                    exit(json_encode(array('code'=>0,'data'=>1,'msg'=>'编辑成功')));
                }
                $this->_success('编辑成功');//成功
            }else{
                if(IS_AJAX){
                    exit(json_encode(array('code'=>1001,'data'=>0,'msg'=>'编辑失败')));
                }
                $this->_error('编辑失败');//失败
            }
        }else{
            $r = $this->db->from('auth_rule')->select('id,name,title,pid,create_time,status,show')->order_by('sort desc,id asc')->get()->result_array();
            $this->load->library('tree');
            $r = $this->tree->toList($r);
            $data['data'] = $r;
            $item = $this->db->from('auth_rule')->where(array('id'=>$this->input->get_post('id')))->get()->row_array();
            $data['item'] = $item;
            $data['title'] = '权限管理';
            $this->_view('admin/rule_edit.html',$data);
        }
    }

    //权限删除
    public function rule_del(){
        $id = $this->input->get_post('id');//用户ID
        $r = $this->db->from('auth_rule')->where(array('pid'=>$id))->get()->result_array();
        if(count($r)){
            exit(json_encode(-1));//删除失败
        }
        $result = $this->db->where(array('id'=>$id))->delete('auth_rule');
        if ($result){
            exit(json_encode(1));//成功
        }else {
            exit(json_encode(0));//删除失败
        }
    }

    //修改密码
    public function edit_pwd(){
        if(!empty($_POST)){
            $where['id'] = $this->session->userdata('aid');
            $where['password'] = md5($this->input->get_post('old_pwd'));
            $new_pwd = md5($this->input->get_post('new_pwd'));
            $data = $this->db->from('admin')->where($where)->get()->row_array();
            if(empty($data)){
                exit(json_encode(0));
            }else{
                $result = $this->db->where(array('id'=>$where['id']))->update('admin',array('password'=>$new_pwd));
                if($result){
                    $this->session->set_userdata('aid',null);
                    $this->session->set_userdata('account',null);
                    exit(json_encode(1));
                }else{
                    exit(json_encode(2));
                }
            }
        }else{
            $this->_view('admin/edit_pwd.html',$data);
        }       
    }

    //清除缓存
    public function clear_cache(){
        $str = $this->input->get_post('clear'); //防止搜索到第一个位置为0的情况
        if($str){
            //strpos 参数必须加引号
            $arr = array();
            if(strpos("'".$str."'", '1')){
                $arr[] = $this->db->cache_delete_all();
            }
            exit(json_encode(1));
        }else{
            $this->_view('admin/clear_cache.html');
        }
    }
    

    public function user_add()
    {   
    	$this->load->Model('M_admin');
    	$this->load->library('form_validation');
    	//$this->form_validation->set_rules('account','帐号','trim|required|min_length[2]');//汉字和英文输入xss_clean不能通过
    	$this->form_validation->set_rules('password','密码','trim|required|min_length[2]');//汉字和英文输入xss_clean不能通过
    	//$this->form_validation->set_rules('group_id','用户组','trim|required|min_length[2]');
    	if ($this->form_validation->run() == false)
    	{
    		$data['title'] = '管理员管理';
    		$data['advertisers'] = $this->M_admin->get_advertisers();
    		$data['data'] = $this->M_admin->get_auth_group();
    		$this->_view('admin/admin_add.html',$data);
    	}
    	else
    	{	
    		$form = self::get_data();
    		$query = $this->M_admin->add_or_update($form);
    		if ($query)
    		{
    		 	$this->_success('修改成功');
    		}
    		else 
    		{
    			$this->_success('修改失败');
    		}
    
    	}
    }
    
    public function get_data()
    {
    	if (!empty($_POST))
    	{
    		$form = array(
    				'id' => $this->input->post('id'),
    				'account' => $this->input->post('account'),
    				'password' => md5($this->input->post('password')),
    				//'group_id' => $this->input->post('group_id'),//there is no this column in table admin
    				'advertisers' => implode(',', $this->input->post('advertisers')),
    				'mobile' => $this->input->post('mobile'),
    				'email' => $this->input->post('email'),
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