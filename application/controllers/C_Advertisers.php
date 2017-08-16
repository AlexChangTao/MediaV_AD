<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 广告主
 */
class C_Advertisers extends USER_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('m_Advertisers_model','Adv');
    }

    /**
     * [Adv_list 广告主列表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     * @return   [type]     [description]
     */
    public function Adv_list(){
        $this->load->library('pagination');
        $this->load->library('pager');
        $offset=$this->uri->segment(3,1);
        $page_config['page_size'] = 10;
        $page_config['cur_page'] = $offset;
        if (self::is_supper()) {
            $page_config['total_row'] =$this->db->count_all('advertisers');
            $page_config['base_link_url'] = site_url('C_Advertisers/Adv_list/');
            $data['page'] = $this->pager->create($page_config);
            $list=$this->Adv->get_adv_page($page_config['page_size'],intval($offset-1));
            $data['result']=$list;
        }else{
            $id = $this->session->userdata('aid');
            $advstr=$this->Adv->get_ower_adv($id);
            $adv_arr=explode(',', $advstr['advertisers']);
            $page_config['total_row'] = count($adv_arr);
            $page_config['base_link_url'] = site_url('C_Advertisers/Adv_list/');
            $data['page'] = $this->pager->create($page_config);
            $list=$this->Adv->get_user_adv_page($page_config['page_size'],intval($offset-1),$adv_arr);
            $data['result']=$list;
        }
        $this->_view('Adv_list.php',$data);    
    }
    public function _user_adv_list(){

    }
    /**
     * [Adv_search 广告主搜索]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-06
     */
    public function Adv_search(){
        $name=$this->input->get('name');
        if (!empty($name)) {
            $this->load->library('pagination');
            $this->load->library('pager');
            $page=$this->input->get('p')?$this->input->get('p'):1;
            if (self::is_supper()) {
                $page_config['total_row'] =$this->Adv->count_all_of_name($name);
                $list=$this->Adv->get_adv_list_by_name_of_search($name,10,$page-1);
            }else{
                $id = $this->session->userdata('aid');
                $advstr=$this->Adv->get_ower_adv($id);
                $adv_arr=explode(',', $advstr['advertisers']);
                $page_config['total_row'] =$this->Adv->count_admin_of_name($name,$adv_arr);
                $list=$this->Adv->get_adminadv_list_by_name_of_search($name,10,$page-1);
            }
            $page_config['cur_page'] = $page;
            $page_config['base_link_url'] = site_url('C_Advertisers/Adv_search?name='.$name.'&p=');
            $page_config['page_size'] = 10;
            $data['page'] = $this->pager->create($page_config);
            $data['result']=$list;
            $this->_view('Adv_list.php',$data);    
        }
    }
    /**
     * [Adv_edit 修改]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     */
    public function Adv_edit(){
        $adv_id=intval($this->uri->segment(3));
        if ($adv_id) {
            $adv_info=$this->Adv->get_adv_info_by_id($adv_id);
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name','Name','required');
            $this->form_validation->set_rules('ower','Ower','required');
            if ($this->form_validation->run()===false) {
                $data['adv']=$adv_info;
                $this->_view('Adv_edit.php',$data);
            }else{
                $adv_id=intval($this->input->post('adv_id'));
                $param=self::get_param();
                $res=$this->Adv->edit_adv($adv_id,$param);
                if ($res) {
                    $this->_log_message('Update','Advertisers '.$adv_id,json_encode($param));
                    $this->_success('修改成功',site_url('C_Advertisers/Adv_edit/').$adv_id);
                }else{
                    $this->_error('修改失败');
                }
           
            }
        }
    }

    
    /**
     * [get_param 获取参数]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @return   [type]     [description]
     */
    public function get_param(){
        if ($this->input->post('name')!==null) {
            $param['name']=$this->input->post('name');
        }
        if ($this->input->post('code')!==null) {
            $param['code']=$this->input->post('code');
        }
        if ($this->input->post('ower')!==null) {
            $param['ower']=$this->input->post('ower');
        }
        if ($this->input->post('remark')!==null) {
            $param['remark']=$this->input->post('remark');
        }
        if ($this->input->post('trade')!==null) {
            $param['trade']=$this->input->post('trade');
        }
        /*if ($this->input->post('is_custom')!==null) {
            $param['is_custom']=$this->input->post('is_custom');
        }*/
        return $param;
    }
    /**
     * [Adv_add 添加广告主]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     */
    public function Adv_add(){
        //加载表单类
        $this->load->helper('form');
        //加载表单验证类
        $this->load->library('form_validation');
        //设置验证规则
        $this->form_validation->set_rules('name','Name','required');
        $this->form_validation->set_rules('code','Code','required|min_length[4]|max_length[11]|callback_check_code_exit');
        $this->form_validation->set_rules('ower','Ower','required');
        $this->form_validation->set_rules('trade','Trade','required');
        if ($this->form_validation->run()===FALSE) {
            #渲染加载页面
            $this->config->load('trade',true);
            $trade=$this->config->item('trade');
            $data['trade']=$trade['trade'];
            $this->_view('Adv_add.php',$data);
        }else{
            #写入数据
            #接收参数
            $param=self::get_param();
            #生成用户表后缀
            $write_res=$this->Adv->write_database($param);
            $suffix=$param['code'];
            if ($param['trade']=='汽车') {
                self::create_table($suffix);
            }
            if ($write_res) {
                $this->_log_message('Add','Advertisers',json_encode($param));
                $this->_success('添加成功',site_url('C_Advertisers/Adv_add'));
            }else{
                $this->_error('添加失败');
            }
        }
    }
    /**
     * [create_table 创建广告主私有表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-21
     * @param    [type]     $suffix [description]
     * @return   [type]             [description]
     */
    public function create_table($suffix){
        $sql='CREATE TABLE '.$this->db->dbprefix('try_drive_'.$suffix).' (';
        $sql.='`id`  int AUTO_INCREMENT ,';
        $sql.="`name`  varchar(50) NOT NULL COMMENT '姓名' ,";
        $sql.="`mobile`  varchar(15) NOT NULL COMMENT '手机号' ,";
        $sql.="`car`  varchar(30) NULL COMMENT '车型' ,";
        $sql.="`car_code`  varchar(30) NULL COMMENT '车型code' ,";
        $sql.="`province_id`  int NULL ,";
        $sql.="`province_name`  varchar(50) NULL ,";
        $sql.="`city_id`  int NULL ,";
        $sql.="`city_name`  varchar(80) NULL ,";
        $sql.="`dealer_code`  varchar(30) NULL COMMENT '经销商code' ,";
        $sql.="`dealer_name`  varchar(200) NULL COMMENT '经销商' ,";
        $sql.="`drive_date`  datetime NULL COMMENT '试驾时间' ,";
        $sql.="`edit_date`  datetime NULL COMMENT '写入时间' ,";
        $sql.="`crm_result`  varchar(500) NULL COMMENT 'crm结果' ,";
        $sql.="PRIMARY KEY (`id`)";
        $sql.=");";
        $this->_log_message('Create','Advertisers_table'.$this->db->dbprefix('try_drive_'.$suffix),$sql);
        return $this->Adv->create_table_by_sql($sql);
    }

    /*----------验证部分-----------*/
    /**
     * [check_code_exit 检测唯一码是否已存在]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-08
     * @param    [type]     $code [description]
     * @return   [type]           [description]
     */
    public function check_code_exit($code){
        $res=$this->Adv->get_id_by_code($code);
        if ($res) {
            $this->form_validation->set_message('check_code_exit','唯一码重复');
            return false;
        }else{
            return true;
        }
    }
}
