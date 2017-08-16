<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class C_Interface extends MY_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->helper('url');
        $this->load->model('m_Features_model','Feat');
    }
    /**
     * [word 文档页面]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-20
     * @return   [type]     [description]
     */
    public function word(){
        $web_id=intval($this->uri->segment(3,0));
        $m_id=intval($this->uri->segment(4,0));
        if ($web_id>0&&$m_id>0) {
            
            $modelname=$this->Feat->get_modelname_by_id($m_id);
            switch ($modelname['name']) {
                case '预约试驾':
                    self::drive_api_word($web_id);
                    break;
                case '抽奖':
                    self::drive_api_word($web_id);
                    break;
                default:#目前数据采集类全部采用默认接口
                    self::collection_api_word($web_id,$m_id);
                    break;
            }
            
        }
    }
    public function collection_api_word($web_id,$m_id){
        $this->load->model('m_Website_model','Web');
        $web_code=$this->Web->get_webcode_by_id($web_id);
        #固定参数webcode
        $data['web_code']=$web_code['web_code'];
        $mod_code=$this->Feat->get_modelcode_by_id($m_id);
        $data['mod_code']=$mod_code['code'];
        #提交地址
        $data['url']=site_url('C_Collection/Form_data');
        #参数列表
        $this->load->model('m_Table_model','Tab');
        $tab=$this->Tab->get_model_tabcol_info($m_id);
        $param[]=array('param_name'=>'web_code','param_description'=>'站点编码','param_type'=>'string','need'=>'是');
        $param[]=array('param_name'=>'mod_code','param_description'=>'功能编码','param_type'=>'string','need'=>'是');
        #根据模块表设计字段名以及注释来
        foreach ($tab as $key => $value) {
            $param[]=array('param_name'=>$value['table_name'].'__'.$value['colum_name'],'param_description'=>$value['remark'],'param_type'=>$value['colum_type'],'need'=>$value['emp']?'是':'否');
        }
        $data['param']=$param;
        $data['method']='post';
        $this->load->view('interface.php',$data);
        
    }
    /**
     * [drive_api_word 试驾文档]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-27
     * @param    [type]     $web_id [description]
     * @return   [type]             [description]
     */
    public function drive_api_word($web_id){
        $this->load->model('m_Website_model','Web');
        $web_code=$this->Web->get_webcode_by_id($web_id);
        #固定参数webcode
        $data['web_code']=$web_code['web_code'];
        #经销商xml
        $adv_code=$this->Web->get_advcode_by_webid($web_id);
        $dealerfile='uploads/xml/'.$adv_code['code'].'/all_dealer.xml';
        $data['dealer_xml']=base_url($dealerfile);
        $carfile='uploads/xml/'.$adv_code['code'].'/all_car.xml';
        $data['car_xml']=base_url($carfile);
        $data['param']=array(
            array('param_name'=>'code','param_description'=>'站点编码','param_type'=>'string','need'=>'是'),
            array('param_name'=>'name','param_description'=>'姓名','param_type'=>'string','need'=>'是'),
            array('param_name'=>'mobile','param_description'=>'电话','param_type'=>'string','need'=>'是'),
            array('param_name'=>'car','param_description'=>'车系','param_type'=>'string','need'=>'否'),
            array('param_name'=>'car_code','param_description'=>'车系code','param_type'=>'string','need'=>'否'),
            array('param_name'=>'province_name','param_description'=>'省份名','param_type'=>'string','need'=>'否'),
            array('param_name'=>'province_id','param_description'=>'省份id','param_type'=>'int','need'=>'否'),
            array('param_name'=>'city_name','param_description'=>'城市名','param_type'=>'string','need'=>'否'),
            array('param_name'=>'city_id','param_description'=>'城市id','param_type'=>'int','need'=>'否'),
            array('param_name'=>'dealer_code','param_description'=>'经销商code','param_type'=>'string','need'=>'否'),
            array('param_name'=>'dealer_name','param_description'=>'经销商名','param_type'=>'string','need'=>'否'),
            );
        $data['url']=site_url('C_Trydrive/try_drive');
        $data['method']='post';
        $this->load->view('interface.php',$data);
    }
}
