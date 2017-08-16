<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 功能模块控制器
 */
class C_Features extends USER_Controller{

    public function __construct(){
        parent::__construct();
        $this->load->model('m_Features_model','Features');
        $this->load->model('m_Table_model','Mtable');
        $this->load->helper('url');
    }
    /**
     * [Feat_list 功能模块列表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     */
    public function Feat_list(){
        //分页类
        $this->load->library('pagination');
        //配置分页参数
        $this->load->library('pager');
        $page_config['total_row'] =$this->db->count_all('models');
        $page_config['cur_page'] = $this->uri->segment(3,1);
        $page_config['base_link_url'] = site_url('C_Features/Feat_list/');
        $page_config['page_size'] = 10;
        $data['page'] = $this->pager->create($page_config);
        $list=$this->Features->get_model_page(10,intval($this->uri->segment(3,1)-1));
        foreach ($list as $key => $value) {
            #模块表
            $table=$this->Mtable->get_table_by_mid($value['id']);
            $list[$key]['table']=$table;
            #应用数
            $count=$this->Mtable->count_modeluse($value['id']);
            $list[$key]['use_count']=$count;
        }
        $data['result']=$list;
        $this->_view('Feat_list.php',$data);

    }
    /**
     * Feat_add 添加功能模块
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     */
    public function Feat_add(){
        //加载表单类
        $this->load->helper('form');
        //加载表单验证类
        $this->load->library('form_validation');
        //设置验证规则
        $this->form_validation->set_rules('name','Name','required');
        $this->form_validation->set_rules('code','Code','required|min_length[4]|max_length[11]|callback_check_code_exit');
        if($this->form_validation->run()===FALSE){
            $this->config->load('trade',true);
            $trade=$this->config->item('trade');
            $data['trade']=$trade['trade'];
            //渲染页面

            $this->_view('Feat_add.php',$data);
        }else{
            //写入数据
            $param=self::get_param();
            $res=$this->Features->write_data($param);
            if ($res) {
                $this->_log_message('Add','Features',json_encode($param));
                $this->_success('添加成功',site_url('C_Features/Feat_list'));
            }else{
                $this->_error('添加失败');
            }
        }
    }
    /**
     * [Feat_edit description]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     */
    public function Feat_edit(){
        $feat_id=intval($this->uri->segment(3,0));
        if ($feat_id) {
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name','Name','required');
            if ($this->form_validation->run()===false) {
                $feat_info=$this->Features->get_info_by_id($feat_id);
                $data['feat']=$feat_info;
                $this->config->load('trade',true);
                $trade=$this->config->item('trade');
                $data['trade']=$trade['trade'];
                #$this->load->view('public/header');
                $this->_view('Feat_edit.php',$data);
                #$this->load->view('public/footer');
            }else{
                $feat_id=$this->input->post('feat_id');
                $param=self::get_param();
                $res=$this->Features->up_model($feat_id,$param);
                if ($res) {
                    $this->_log_message('Update','Features '.$feat_id,json_encode($param));
                    $this->_success('添加成功',site_url('C_Features/Feat_edit/').$feat_id);
                }else{
                    $this->_error('添加失败');
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
        if ($this->input->post('name')) {
            $param['name']=$this->input->post('name');
        }
        if ($this->input->post('code')) {
            $param['code']=$this->input->post('code');
        }
        if (!empty($this->input->post('is_private'))) {
            $param['is_private']=$this->input->post('is_private');
        }
        if ($this->input->post('trade')) {
            $param['trade']=$this->input->post('trade');
        }
        if ($this->input->post('remark')) {
            $param['remark']=$this->input->post('remark');
        }
        return $param;

    }
    //逻辑功能部分--结束
    /*-----验证部分-----*/
    /**
     * [check_code 验证唯一码]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     * @return   [type]     [description]
     */
    public function check_code_exit($code){
        $result=$this->Features->get_id_by_code($code);
        if (empty($result)) {
            return True;
        }else{
            $this->form_validation->set_message('check_code_exit', '唯一码重复');
            return False;
        }
    }
}
