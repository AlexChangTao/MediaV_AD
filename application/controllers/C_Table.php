<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * 模块表
 */
class C_Table extends USER_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('m_Features_model','Features');
        $this->load->model('m_Table_model','Tab');
        $this->load->helper('url');
    }
    /**
     * Tab_list表列表
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     */
    public function Tab_list(){
        //分页类
        $this->load->library('pagination');
        //配置分页参数
        $this->load->library('pager');
        $page_config['total_row'] =$this->db->count_all('model_tables');
        $offset=$this->uri->segment(3,1);
        $page_config['cur_page'] = $offset;
        $page_config['base_link_url'] = site_url('C_Table/Tab_list/');
        $page_config['page_size'] = 10;
        $data['page'] = $this->pager->create($page_config);
        $list=$this->Tab->get_tab_page(10,intval($offset-1));
        foreach ($list as $key => $value) {
            #统计表内数据
            $tab_list=$this->Tab->get_like_table($value['table_name']);
            #获取所有表
            $row_count=0;
            $data_space=0;
            foreach ($tab_list as $ke => $valu) {
                $row_count=$row_count+$this->Tab->count_table_row($valu['table_name']);
                $space=$this->Tab->get_table_space($valu['table_name']);
                $data_space=$data_space+$space['space'];
            }
            $list[$key]['data_space']=$data_space/1024;
            $list[$key]['row_count']=$row_count;
        }
        $data['result']=$list;
        $this->_view('Tab_list.php',$data);
    }
    /**
     * [Tab_add 添加表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     */
    public function Tab_add(){
        //加载表单类
        $this->load->helper('form');
        //加载表单验证类
        $this->load->library('form_validation');
        //设置验证规则
        $this->form_validation->set_rules('name','
            name','required|callback_check_tablename_exit');
        $this->form_validation->set_rules('mid','Mid','required|callback_check_model_exit');
        $this->form_validation->set_rules('type','type','required|callback_check_type_licit');
        if($this->form_validation->run()===FALSE){
            $data['model']=$this->Features->get_model_list();
            $this->_view('Tab_add.php',$data);
        }else{
            $param['m_id']=$this->input->post('mid');
            $param['table_name']=$this->input->post('name');
            $param['remark']=$this->input->post('remark');
            $param['table_type']=$this->input->post('type');
            $res=$this->Tab->write_database($param);
            if ($res) {
                 $this->_log_message('Add','Table',json_encode($param));
                $this->_success('添加成功',site_url('C_Colum/Colum_add/').$res);
            }else{
                $this->_error('添加失败');
            }
           
        }
    }


    /*-------数据验证部分--------*/
    /**
     * [check_tablename_exit 检测表名是否已存在]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-06
     * @param    [type]     $table_name [description]
     * @return   [type]                 [description]
     */
    public function check_tablename_exit($table_name){
        $result=$this->Tab->get_tableid_by_name($table_name);
        if (!$result) {
            return TRUE;
        }else{
            $this->form_validation->set_message('check_tablename_exit', '表名已存在');
            return FALSE;
        }
    }
    /**
     * [check_model_exit 检测模块id是否合法]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-06
     * @param    [type]     $m_id [description]
     * @return   [type]           [description]
     */
    public function check_model_exit($m_id){
        $result=$this->Features->get_modelname_by_id($m_id);
        if (!empty($result)) {
            return TRUE;
        }else{
            $this->form_validation->set_message('check_model_exit', '模块不存在');
            return FALSE;
        }
    }
    /**
     * [check_type_licit 检测表类型是否合法]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-06
     * @param    [type]     $table_type [description]
     * @return   [type]                 [description]
     */
    public function check_type_licit($table_type){
        $type=array('myisam','innodb');
        if (in_array($table_type,$type)) {
            return TRUE;
        }else{
            $this->form_validation->set_message('check_type_licit', '表类型错误');
            return FALSE;
        }
    }   
}
