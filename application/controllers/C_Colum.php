<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class C_Colum extends USER_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('m_Colum_model','Col');
        $this->load->model('m_Table_model','Tab');
        $this->load->helper('url');
    }
    public function Colum_list(){
        //分页类
        $this->load->library('pagination');
        $this->load->library('pager');
        $page_config['total_row'] =$this->db->count_all('table_colums');
        $page_config['cur_page'] = $this->uri->segment(3,1);
        $page_config['base_link_url'] = site_url('C_Colum/Colum_list');
        $page_config['page_size'] = 10;
        $data['page'] = $this->pager->create($page_config);
        $list=$this->Col->get_page($page_config['page_size'],intval($this->uri->segment(3,1))-1);
        $data['result']=$list;
        //配置分页参数
        $this->_view('Col_list.php',$data);

    }
    /**
     * [Col_edit 字段编辑]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     */
    public function Col_edit(){
        $colid=intval($this->uri->segment(4));
        $table_id=intval($this->uri->segment(3));
        if ($colid) {
            $this->load->helper('form');
            //加载表单验证类
            $this->load->library('form_validation');
            $this->form_validation->set_rules('length','length','required|integer');
            if ($this->form_validation->run()===false) {
                $data['t_name']=$this->Tab->get_table_name_by_id($table_id);
                $data['info']=$this->Col->get_colinfo_by_id($colid);
                $data['col']=$this->Col->get_table_col_by_tid($table_id);
                $m_id=$this->Tab->get_modelid_by_table($table_id);
                $data['model_use']=$this->Tab->count_modeluse($m_id['m_id']);
                var_dump($data['model_use']);die;
                $this->_view('Col_edit.php',$data);
            }else{
                $param=self::get_param();
                $id=$this->input->post('id');
                $res= $this->Col->up_col($id,$param);
                $tid=$this->input->post('tid');
                if ($res) {
                    $this->_log_message('Update','Colum '.$id,json_encode($param));
                    $this->_success('修改成功',site_url('C_Colum/Colum_add/').$tid);
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
        if ($this->input->post('name')) {
            $param['colum_name']=$this->input->post('name');
        }
        if ($this->input->post('length')) {
            $param['colum_len']=$this->input->post('length');
        }
        if ($this->input->post('type')) {
            $param['colum_type']=$this->input->post('type');
        }
        if (!empty($this->input->post('emp'))) {
            $param['emp']=$this->input->post('emp');
        }
        if ($this->input->post('remark')) {
             $param['remark']=$this->input->post('remark');
        }
        if ($this->input->post('t_id')) {
             $param['t_id']=$this->input->post('t_id');
        }
        return $param;
    }
    /**
     * [Colum_add 字段添加]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-07
     */
    public function Colum_add(){
        #表id
        $table_id=intval($this->uri->segment(3));
        if ($table_id) {
             //加载表单类
            $this->load->helper('form');
            //加载表单验证类
            $this->load->library('form_validation');
            //设置验证规则
            $this->form_validation->set_rules('name','name','required');
            $this->form_validation->set_rules('length','length','required|integer');
            $this->form_validation->set_rules('type','type','required');
            $data['t_id']=$table_id;
            if ($this->form_validation->run()===FALSE) {
                $data['t_name']=$this->Tab->get_table_name_by_id($table_id);
                $data['col']=$this->Col->get_table_col_by_tid($table_id);
                $m_id=$this->Tab->get_modelid_by_table($table_id);
                $data['model_use']=$this->Tab->count_modeluse($m_id['m_id']);

                $this->_view('Col_add.php',$data);
                #$this->load->view('public/footer');
            }else{
                #数据写入
                $param=self::get_param();
                $check=$this->Col->get_columid_of_table_by_name($param['colum_name'],$param['t_id']);
                if (!$check) {
                    $res=$this->Col->write_database($param);
                    if ($res) {
                        $this->_log_message('add','Colum',json_encode($param));
                        $this->_success('添加成功',site_url('C_Colum/Colum_add/').$param['t_id']);
                    }else{
                        $this->_error('添加失败');
                    }
                }else{
                    $this->_error('字段名重复');
                    #redirect('/C_Colum/Colum_add/'.$param['t_id'], 'refresh','');
                }
            }
        }
    }

    /*-----------校验部分-------------*/
    /**
     * [check_colname_exit 同表列名检测]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-07
     * @param    [type]     $colname  [description]
     * @param    [type]     $table_id [description]
     * @return   [type]               [description]
     */
    public function check_colname_exit($colname,$table_id){
        log_message('info', $table_id);
        $res=$this->Col->get_columid_of_table_by_name($colname,$table_id);
        if ($res) {
            $this->form_validation->set_message('check_colname_exit', '同表列名重复');
            return false;
        }else{
            return true;
        }
    }
}
