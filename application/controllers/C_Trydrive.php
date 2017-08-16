<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class C_Trydrive extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('m_Website_model','Web');
        $this->load->model('m_Advertisers_model','Adv');
        $this->load->model('m_Area_model','Area');
        $this->load->model('M_Trydrive_model','Trydrive');
    }

 
   
    public function try_drive(){
 
        #code
        $code=$this->input->post('code');
        if ($code) {
            #找到站点功能配置
            $conf=$this->Web->get_web_drive_conf_by_code($code);
            if (!$conf) {
                echo json_encode(array('status'=>0,'data'=>array(),'message'=>'站点信息错误'));exit;
            }
            $now=time();
            $start=strtotime($conf['start_date']);
            $end=strtotime($conf['end_date']);
            if ($now<$start) {
                echo json_encode(array('status'=>0,'data'=>array(),'message'=>'活动尚未开始'));exit;
            }
            if ($now>$end) {
                echo json_encode(array('status'=>0,'data'=>array(),'message'=>'活动已结束'));exit;
            }
            #找到广告主
            $web=$this->Web->get_webinfo_by_webcode($code);
            $adv_code=$this->Adv->get_code_by_id($web['ad_id']);
            if ($web) {
                $table='try_drive_'.$adv_code['code'];
                $param=self::get_drive_param();
                if (is_array($param)) {
                    $insert_id=$this->Trydrive->write_drive_data($table,$param);
                    if ($insert_id!==null) {
                        #推送到CRM
                        $this->_log_message('Add','Trydrive',json_encode($param));
                        $res=self::send_to_api($web,$param,$insert_id);
                        if ($res) {
                            echo json_encode(array('status'=>1,'data'=>array(),'message'=>'成功'));exit;
                        }else{
                            echo json_encode(array('status'=>0,'data'=>array(),'message'=>'失败'));exit;
                        }
                    }else{
                         echo json_encode(array('status'=>0,'data'=>array(),'message'=>'失败'));exit;
                    }
                }else{
                    #缺少参数 
                    echo json_encode(array('status'=>0,'data'=>array(),'message'=>'缺少参数'));exit;
                }
            }
        }else{
            echo json_encode(array('status'=>0,'data'=>array(),'message'=>'缺少参数'));exit;
        }
       

        #写入对应的数据表
        #找到对应crm接口
        #将数据推送
        #写入返回结果
    }
    /**
     * [send_to_api 推送]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-14
     * @param    [type]     $web       [description]
     * @param    [type]     $param     [description]
     * @param    [type]     $insert_id [description]
     * @return   [type]                [description]
     */
    public function send_to_api($web,$param,$insert_id){
        $crm=$this->Trydrive->get_drive_crm_conf($web['id']);
        if ($crm['is_crm']==1) {
            $this->load->model('m_Api_model','Api');
            #获取api详情
            $api=$this->Api->get_api_info_by_apiid($crm['api_id']);

            #请求推送
            $send_res='';
            #将推送返回结果写入数据库
            $this->Trydrive->write_api_respone($send_res,$insert_id);
            #推送
            return true;
        }else{
            return true;
        }
    }
     

    /**
     * [fittle_param 参数判断]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-21
     * @param    [type]     $name [description]
     * @return   [type]           [description]
     */
    public function fittle_param($name){
        if ($this->input->post($name)!==null){
            return true;
        }else{
            return false;
        }
    }
    /**
     * [get_drive_param 获取过滤参数]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-14
     * @return   [type]     [description]
     */
    public function get_drive_param(){
        if (self::fittle_param('name')) {
            $param['name']=$this->input->post('name');
        }else{
            return false;
        }
        if (self::fittle_param('mobile')) {
            $mobile=$this->input->post('mobile');
            $res=self::isMobile($mobile);
            if ($res) {
                $param['mobile']=$mobile;
            }else{
                return false;
            }
        }else{
            return false;
        }
        if (self::fittle_param('car')) {
            $param['car']=$this->input->post('car');
        }
        if (self::fittle_param('province_name')) {
            $param['province_name']=$this->input->post('province_name');
        }
        if (self::fittle_param('province_id')) {
            $param['province_id']=intval($this->input->post('province_id'));
        }
        if (self::fittle_param('city_id')) {
            $param['city_id']=$this->input->post('city_id');
        }
        if (self::fittle_param('city_name')) {
            $param['city_name']=$this->input->post('city_name');
        }
        if (self::fittle_param('dealer_name')) {
            $param['dealer_name']=$this->input->post('dealer_name');
        }
        if (self::fittle_param('dealer_code')) {
            $param['dealer_code']=$this->input->post('dealer_code');
        }
        if (self::fittle_param('drive_date')) {
             $param['drive_date']=$this->input->post('drive_date');
        }
        $param['edit_date']=date('Y-m-d H:i:s');
        return $param;
    }
    function isMobile($mobile) {  
        if (!is_numeric($mobile)) {  
            return false;  
        }  
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;  
    }
}
