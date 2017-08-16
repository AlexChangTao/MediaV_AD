<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class C_Collection extends MY_Controller{
    public function __construct(){
        parent::__construct();

        $this->load->model('m_Features_model','Feat');
        $this->load->model('m_Website_model','Web');
    }
    public function Form_data(){
        $web_code=$this->input->post('web_code');
        $mod_code=$this->input->post('mod_code');
        if (self::check_web_code($web_code)&&self::check_mod_code($mod_code)) {
            $adv_code=$this->Web->get_advcode_by_webcode($web_code);
            $param=$_POST;
            unset($param['web_code']);
            unset($param['mod_code']);
            $param_key=array_keys($param);
            #判断数据是否私有
            $modtype=self::get_model_type($mod_code);
            if ($modtype==true) {
                foreach ($param_key as $value) {
                    $tab_col=explode('__',$value);
                    //arr[表名][字段]=值
                    $split_param[$tab_col[0].'_'.$adv_code['code']][$tab_col[1]]=$param[$value];
                }
            }else{
                 foreach ($param_key as $value) {
                    $tab_col=explode('__',$value);
                    $split_param[$tab_col[0]][$tab_col[1]]=$param[$value];
                    $split_param[$tab_col[0]]['web_code']=$web_code;
                }
            }
            
            #echo json_encode($split_param);die;
            $this->load->model('M_Collection_model','Collection');
            
           
            //各种sql执行
            foreach ($split_param as $key => $value) {
                $this->Collection->write_collection_data($key,$split_param[$key]);
            }
            
    
           
            echo json_encode(array('status'=>1,'data'=>array(),'message'=>'成功'));exit;
        }else{
            echo json_encode(array('status'=>1,'data'=>array(),'message'=>'数据不合法'));exit;
        }
    }
    /**
     * [get_model_type 获取模块是否私有,影响数据表命名]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-30
     * @param    [type]     $code [description]
     * @return   [type]           [description]
     */
    public function get_model_type($code){
        $res=$this->Feat->get_mod_private($tablename);
        if($res['is_private']==1){
            return true;
        }else{
            return false;
        }

    }
    /**
     * [check_web_code 校验站点code]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-27
     * @param    [type]     $web_code [description]
     * @return   [type]               [description]
     */
    public function check_web_code($web_code){
        if (empty($web_code)) {
            return false;
        }
        $res=$this->Web->get_id_by_code($web_code);
        if ($res) {
            return true;
        }else{
            return false;
        }
    }
    /**
     * [check_mod_code 校验功能code]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-27
     * @param    [type]     $mod_code [description]
     * @return   [type]               [description]
     */
    public function check_mod_code($mod_code){
        if (empty($mod_code)) {
            return false;
        }   
        $res=$this->Feat->get_id_by_code($mod_code);
        if ($res) {
            return true;
        }else{
            return false;
        }
    }
}
