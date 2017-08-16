<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class C_Website extends USER_Controller{
    public function __construct(){
        parent::__construct();
        $this->load->model('m_Website_model','Web');
        $this->load->helper('url');
        $this->load->model('m_Advertisers_model','Adv');
    }
    /**
     * [Web_add_page 添加页面]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     */
    public function Web_add_page(){
        $adv_id=intval($this->uri->segment(3,0));
        #表单
        if ($adv_id>0) {
            $this->load->helper('form');
            $this->load->library('form_validation');
            //设置验证规则
            $this->form_validation->set_rules('name','Name','required');
            $this->form_validation->set_rules('code','Code','required|min_length[4]|max_length[20]|callback_check_code_exit');
            if ($this->form_validation->run()===FALSE) {
                
                $this->load->model('m_Features_model','Features');
                $adv_info=$this->Adv->get_adv_info_by_id($adv_id);
                #地区
                $data['area']=self::get_assembly_area();
                $ad_name=$this->Adv->get_advname_by_id($adv_id);
                $ad_code=$this->Adv->get_advcode_by_id($adv_id);
                $data['ad_title']=$ad_name['name'].'_'.$ad_code['code'];
                $data['adv_id']=$adv_id;
                #媒体
                $data['media']=$this->Web->get_media();
                #功能列表
                $data['models']=$this->Features->get_model_of_trade($adv_info['trade']);
                $this->_view('Web_add.php',$data);
            }else{
                $ad_id=intval($this->input->post('ad_id'));
                $write_res=self::web_add();
                if ($write_res) {

                    $this->_success('添加成功',site_url('C_Website/Web_add_page/'.$ad_id));
                }else{
                    $this->_error('相关表创建失败');
                }
            }
        } 
    }
     /**
     * [web_add 数据写入]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @return   [type]     [description]
     */
    public function web_add(){
        $param=self::get_web_param();
        $webres= $this->Web->write_data($param);
        #站点功能
        #本表数据结束
        if ($webres) {
            $this->_log_message('Add','Website',json_encode($param));
            $models=$this->input->post('feat');
            if ($models){
                $adv_code=$this->Adv->get_code_by_id($param['ad_id']);
                foreach ($models as $key => $value) {
                    $web_model[$key]=array('web_id'=>$webres,'m_id'=>intval($value));
                    #创建功能需要的表
                    $res=self::Create_adv_tab($value,$adv_code['code']);
                    if ($res['flag']==false) {
                        return false;
                    }
                }
                
            }else{
                return true;
            }
            return $this->Web->write_web_model($web_model);
        }else{
            #写入失败
            return false;
        }
    }
    /**
     * [web_model 站点功能列表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-17
     * @return   [type]     [description]
     */
    public function web_model(){
        $web_id=intval($this->uri->segment(3,0));
        if ($web_id>0) {
             //分页类
            $this->load->library('pagination');
            //配置分页参数
            $this->load->library('pager');
            $page_config['total_row'] =$this->Web->count_webmodel($web_id);
            $page_config['cur_page'] = $this->uri->segment(4,1);
            $page_config['base_link_url'] =site_url('C_Website/web_model/'.$web_id.'/');
            $page_config['page_size'] = 10;
            $data['page'] = $this->pager->create($page_config);
            $list=$this->Web->get_web_model_page_of_one($web_id,10,intval($this->uri->segment(4,1)-1));
            $data['result']=$list;
            $this->_view('Webmodel_list.php',$data);
        }
    }
    /**
     * [web_edit_page 站点编辑]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @return   [type]     [description]
     */
    public function web_edit_page(){
        $web_id=intval($this->uri->segment(3,0));
        $adv_id=intval($this->uri->segment(4,0));
        if ($web_id>0&&$adv_id>0) {

            $this->load->model('m_Features_model','Features');
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('name','Name','required');
            if ($this->form_validation->run()===false) {
                $data['adv_id']=$adv_id;
                #站点信息
                $web_info=$this->Web->get_web_info_by_id($web_id);
                #站点信息
                $data['web']=$web_info;
                #地区
                $data['web_area']=self::area_des($web_info['province'],$web_info['city']);
                $data['area']=self::get_assembly_area();
                #媒体
                $data['media']=$this->Web->get_media();
                $data['web_media']=explode(';', $web_info['media']);
                #功能列表
                $adv_info=$this->Adv->get_adv_info_by_id($adv_id);
                $data['models']=$this->Features->get_model_of_trade($adv_info['trade']);
                $web_model=$this->Web->get_web_modelid_list($web_id);
                $wm=array();
                foreach ($web_model as  $value) {
                    $wm[]=$value['m_id'];
                }
                $data['wm']=$wm;
                $this->_view('Web_edit.php',$data);
            }else{
                $web_id=intval($this->input->post('web_id'));
                $adv_id=intval($this->input->post('adv_id'));
                $param=self::get_web_param();
                if ($this->Web->up_website($web_id,$param)) {
                     $this->_log_message('Update','Website '.$web_id,json_encode($param));
                    $model=$this->input->post('feat');
                    #功能编辑
                    if ($model) {
                        $adv_code=$this->Adv->get_code_by_id($adv_id);
                        foreach ($model as $key => $value) {
                            $web_model[$key]=array('web_id'=>$web_id,'m_id'=>$value);
                            #创建表
                            $res=self::Create_adv_tab($value,$adv_code['code']);
                            if ($res['flag']===false) {
                                $this->_error('相关表创建失败');exit;
                            }
                        }
                        #记录站点功能
                        if ($this->Web->write_web_model($web_model)){
                            $this->_success('修改成功',site_url('C_Website/web_edit_page/').$web_id.'/'.$adv_id);
                        }else{
                            $this->_error('修改失败');
                        }
                    }else{
                        $this->_success('修改成功',site_url('C_Website/web_edit_page/').$web_id.'/'.$adv_id);
                    }
                }else{
                    $this->_error('修改失败');
                }
                
            }
            
        }
    }
    
    /**
     * [Web_advlist 广告主站点]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-27
     */
    public function Web_advlist(){
         $adv_id=intval($this->uri->segment(3,0));
         if ($adv_id>0) {
            $this->load->library('pagination');
            $data['adv_id']=$adv_id;
            #有广告主id进入私有列表
            $this->load->library('pager');
            $page_config['total_row'] =$this->Web->get_ad_total_row($adv_id);
            $page_config['cur_page'] = $this->uri->segment(4,1);
            $page_config['base_link_url'] = site_url("C_Website/Web_list/$adv_id/");
            $page_config['page_size'] = 10;
            $data['page'] = $this->pager->create($page_config);
            $list=$this->Web->get_ad_web_page($adv_id,10,intval($this->uri->segment(4,1)-1));
            foreach ($list as $key => $value) {
                $list[$key]['wm']=$this->Web->get_web_model_by_webid($value['id']);
            }
            $data['result']=$list;
            $this->_view('Web_list.php',$data);

        }
    }

    /**
     * [Web_list 站点分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-13
     */
    public function Web_list(){
        //分页类
        $data['adv_id']='';
        $this->load->library('pagination');
        $this->load->library('pager');
        $offset=$this->uri->segment(3,1);
        if (self::is_supper()) {
            $page_config['total_row'] =$this->db->count_all('website');
            $list=$this->Web->get_web_page(10,intval($offset-1));
        }else{
            $id = $this->session->userdata('aid');
            $advstr=$this->Adv->get_ower_adv($id);
            $adv_arr=explode(',', $advstr['advertisers']);
            $page_config['total_row'] =$this->Web->count_admin_all($adv_arr);
            $list=$this->Web->get_admin_web_page(10,intval($offset-1),$adv_arr);
        }
        $page_config['cur_page'] = $offset;
        $page_config['base_link_url'] = site_url("C_Website/Web_list/");
        $page_config['page_size'] = 10;
        $data['page'] = $this->pager->create($page_config);
        foreach ($list as $key => $value) {
            $list[$key]['wm']=$this->Web->get_web_model_by_webid($value['id']);
        }
        $data['result']=$list;
        $this->_view('Web_list.php',$data);
    }
   
   
    /**
     * [Create_adv_tab 创建模块表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-08
     * @param    [type]     $trade [description]
     * @param    [type]     $id    [description]
     */
    public function Create_adv_tab($m_id,$suffix){
        $this->load->model('m_Features_model','Fea');
        $this->load->model('m_Colum_model','Col');
        #获取模块下的表
        $tableinfo=$this->Fea->get_model_table_by_modelid($m_id);
        $result=array();
        $result['flag']=True;
        $result['tab']='';
        $result['tab_err']='';
        if ($tableinfo!=null) {
            #根据数据的私有性创建表名
            foreach ($tableinfo as $key => $value) {
                if ($value['is_private']==1) {
                    $tablename=$value['table_name'].'_'.$suffix;
                }else{
                    $tablename=$value['table_name'];
                }
                #检测表是否存在
                if (!$this->db->table_exists($tablename)) {
                    $result['tab'].=$tablename;
                    #获取表字段
                    $colum=$this->Col->get_colums_by_tid($value['id']);
                    if (!empty($colum)) {
                        #创建sql
                        $sql=self::create_sql($tablename,$value['table_type'],$colum,$value['is_private']);
                        if ($sql!==false) {
                            #创建表
                            if(!$this->Adv->create_table_by_sql($sql)){
                                $this->_log_message('Create Table',$tablename,$sql);
                                $result['tab_err'].=$tablename;
                                $result['flag']=false;
                            }
                        }else{
                            #sql生成失败
                            $result['tab_err'].=$tablename;
                            $result['flag']=false;
                        }
                    }
                }
            }
            #创建结束,返回创建相关信息
            return $result;
        }else{
            #不需要创建表直接返回
            return $result;
        }
        
       
    }
    /**
     * [create_sql 生成sql]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-09
     * @param    [type]     $tabname [description]
     * @param    [type]     $tabtype [description]
     * @param    [type]     $col     [description]
     * @return   [type]              [description]
     */
    public function create_sql($tabname,$tabtype,$col,$isprivate){
        if (!empty($col)) {
            $sql="CREATE TABLE ".$this->db->dbprefix($tabname)." (
                    id INT(11) NOT NULL AUTO_INCREMENT ,\n";
            #非私有需要将站点code字段加入
            if ($isprivate!=1) {
                $sql.="web_code varchar(20) NULL DEFAULT '' ,\n";
            }
            foreach ($col as  $value) {
                    $sql.=$value['colum_name'] ;
                    #是否为空以及默认字符
                    if ($value['emp']==0) {
                        $is_null='NOT NULL';
                    }else{
                        if ($value['colum_type']=='int') {
                            $is_null="NULL DEFAULT 0";
                        }else{
                            $is_null="NULL DEFAULT ''";
                        }
                       
                    }
                    #字段类型
                    switch  ($value['colum_type']) {
                        case 'int':
                             $sql.=" ".$value['colum_type']."(".$value['colum_len'].") ".$is_null.", \n";
                             break;
                        case 'varchar':
                             $sql.=" ".$value['colum_type']."(".$value['colum_len'].") ".$is_null.", \n";
                             break;
                        case 'datetime':
                             $sql.=" ".$value['colum_type']." , \n";
                            break;
                         default:
                             return false;
                             break;
                    }
                }   
            $sql.="PRIMARY KEY (id)\n ) ENGINE=".$tabtype." DEFAULT CHARSET=utf8";
            return $sql;
        }else{
            return false;
        }
        
    }
     /**
     * [area_des 地区反序列化组合]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $province [description]
     * @param    [type]     $city    [description]
     * @return   [type]              [description]
     */
    public function area_des($province,$city){
        $res=array();
        if ($province) {
            $res=array_merge($res,explode(';',$province));
        }
        if ($city) {
            $res=array_merge($res,explode(';',$city));
        }
        return implode(',', $res);
    }
    
    /**
     * [get_web_param 参数获取]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @return   [type]     [description]
     */
    public function get_web_param(){
        if ($this->input->post('ad_id')!==null) {
            $param['ad_id']=intval($this->input->post('ad_id'));
        }
        if ($this->input->post('code')!==null) {
            $param['web_code']=$this->input->post('code');
        }
        if ($this->input->post('name')!==null) {
            $param['web_name']=$this->input->post('name');
        }
        if ($this->input->post('url')!==null) {
            $param['web_url']=$this->input->post('url');
        }
        if ($this->input->post('remark')!==null) {
            $param['remark']=$this->input->post('remark');
        }
        if ($this->input->post('start')!==null) {
            $param['start_date']=$this->input->post('start');
        }
        if ($this->input->post('end')!==null) {
            $param['end_date']=$this->input->post('end');
        }
        if ($this->input->post('area')!==null) {
            #地区数据特殊处理
            $area=self::fittle_area($this->input->post('area'));
            $param['province']=$area['province'];
            $param['city']=$area['city'];

        }
        if ($this->input->post('media')) {
            $param['media']=implode(';',$this->input->post('media'));
        }
        return $param;
    }
    /**
     * [fittle_area 地区数据省份城市筛选方法]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-20
     * @param    [type]     $strarea [description]
     * @return   [type]              [description]
     */
    public function fittle_area($strarea){
        $arr_area=explode(',', $strarea);
        $province=array();
        $city=array();
        foreach ($arr_area as $key => $value) {
            if ($value<35) {
                $province[]=$value;
            }else{
                $city[]=$value;
            }
        }
        return array('province'=>implode(';', $province),'city'=>implode(';', $city));
    }
    /**
     * [get_assembly_area 获取组装的省市]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-13
     * @return   [type]     [description]
     */
    public function get_assembly_area(){
        $this->load->model('m_Area_model','Area');
        $citylist=$this->Area->get_province_city();
        $list=[];
        foreach ($citylist as $key => $value) {
            $tmp=array('city_name'=>$value['city_name'],'city_id'=>$value['city_id']);
            $list[$value['province_id']]['province_id']=$value['province_id'];
            $list[$value['province_id']]['province_name']=$value['province_name'];
            $list[$value['province_id']]['child'][]=$tmp;
        }
        return $list;
    }
    /**
     * [web_search 站点搜索]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-19
     * @return   [type]     [description]
     */
    public function web_search(){
        $name=$this->input->get('name');
        if (!empty($name)) {
            $this->load->library('pagination');
            #有广告主id进入私有列表
            $this->load->library('pager');
            $page=$this->input->get('p')?$this->input->get('p'):1;
            $page_config['total_row'] =$this->Web->count_all_by_name($name);
            $page_config['cur_page'] =$page;
            $page_config['base_link_url'] = site_url("C_Website/web_search?name=".$name."&p=");
            $page_config['page_size'] = 10;
            $data['page'] = $this->pager->create($page_config);
            $list=$this->Web->get_web_page_by_name($name,10,$page-1);
            foreach ($list as $key => $value) {
                $list[$key]['wm']=$this->Web->get_web_model_by_webid($value['id']);
            }
            $data['result']=$list;
            $this->_view('Web_list.php',$data);
        }
    }
    /**
     * [Webmodel_write 站点功能]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     */
    public function Webmodel_write(){
        $web_id=intval($this->uri->segment(3));
        $m_id=intval($this->uri->segment(4));
        $this->load->model('m_Features_model','Features');
        $m_name=$this->Features->get_modelname_by_id($m_id);
        switch ($m_name['name']) {
            case '预约试驾':
                redirect('C_Website/drive_write/'.$web_id.'/'.$m_id);
                break;
            case '抽奖':
                redirect('activity_restriction/index?activity_id='.$web_id);
                break;
            default:
                redirect('C_Website/deploy/'.$web_id.'/'.$m_id);
                break;
        }
    }
    /**
     * [Webmodel_write 站点功能]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     */
    public function Webmodel_show(){
        $web_id=intval($this->uri->segment(3));
        $m_id=intval($this->uri->segment(4));
        $this->load->model('m_Features_model','Features');
        $m_name=$this->Features->get_modelname_by_id($m_id);
        switch ($m_name['name']) {
            case '预约试驾':
                redirect('C_Website/drive_write_show/'.$web_id.'/'.$m_id);
                break;
            case '抽奖':
                redirect('activity_restriction/index?activity_id='.$web_id);
                break;
            default:
                redirect('C_Website/deploy_show/'.$web_id.'/'.$m_id);
                break;
        }
    }
    public function deploy_show(){
        $web_id=intval($this->uri->segment(3));
        $m_id=intval($this->uri->segment(4,0));
        if($m_id>0){
            $data['web_id']=$web_id;
            $data['m_id']=$m_id;
            $data['adv_info']=$this->Web->get_webadv_info_by_webid($web_id);
            $this->load->model('m_Features_model','Features');
            $data['model_name']=$this->Features->get_modelname_by_id($m_id);
            $data['model_status']=$this->Web->get_web_model_status($web_id,$m_id);
            $this->_view('deploy_show.php',$data);
        }
    }
    /**
     * [deploy 公共功能配置页面]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-21
     * @return   [type]     [description]
     */
    public function deploy(){
        $web_id=intval($this->uri->segment(3));
        $m_id=intval($this->uri->segment(4,0));
        if($m_id>0){
            $this->load->library('form_validation');
            $this->form_validation->set_rules('start_date','start_date','required');
            $this->form_validation->set_rules('end_date','end_date','required');
            if ($this->form_validation->run()===false) {
                $data['web_id']=$web_id;
                $data['m_id']=$m_id;
                $data['adv_info']=$this->Web->get_webadv_info_by_webid($web_id);
                $this->load->model('m_Features_model','Features');
                $data['model_name']=$this->Features->get_modelname_by_id($m_id);
                $data['model_status']=$this->Web->get_web_model_status($web_id,$m_id);
                $this->_view('deploy.php',$data);
            }else{
                #修改统一配置
                $param['web_id']=$this->input->post('web_id');
                $m_id=$this->input->post('m_id');
                $wm['start_date']=$this->input->post('start_date');
                $wm['end_date']=$this->input->post('end_date');
                $wm['is_use']=$this->input->post('is_use');
                if ($this->Web->up_web_model($param['web_id'],$m_id,$wm)) {
                    $this->_log_message('Update','Web_model'.$param['web_id'],json_encode($wm));
                    $this->_success('修改成功');
                }else{
                    $this->_error('修改失败');
                }
            }
            
        }
    }
    public function drive_write_show(){
        $web_id=intval($this->uri->segment(3));
        $m_id=intval($this->uri->segment(4,0));
        if($m_id>0){
            $data['web_id']=$web_id;
            $data['m_id']=$m_id;
            $data['adv_info']=$this->Web->get_webadv_info_by_webid($web_id);
            $this->load->model('m_Features_model','Features');
            $data['model_name']=$this->Features->get_modelname_by_id($m_id);
            $data['model_status']=$this->Web->get_web_model_status($web_id,$m_id);
            #单独配置读取
            $data['drive_conf']=$this->Web->get_web_model_info($web_id,$m_id);
            #查询是否有crm
            $data['crm']=$this->Adv->get_adv_api_by_id($data['adv_info']['id']);
            $data['web_crm']=$this->Web->get_webcrm_by_webid($web_id);
            $this->_view('Drive_conf_show.php',$data); 
        } 
    }
    /**
     * [drive_write 试驾配置]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @return   [type]     [description]
     */
    public function drive_write(){
        $web_id=intval($this->uri->segment(3));
        $m_id=intval($this->uri->segment(4,0));
        if($m_id>0){
            $this->load->helper('form');
            $this->load->library('form_validation');
            $this->form_validation->set_rules('start_date','start_date','required');
            $this->form_validation->set_rules('end_date','end_date','required');
            if ($this->form_validation->run()===false) {
                $data['web_id']=$web_id;
                $data['m_id']=$m_id;
                $data['adv_info']=$this->Web->get_webadv_info_by_webid($web_id);
                $this->load->model('m_Features_model','Features');
                $data['model_name']=$this->Features->get_modelname_by_id($m_id);
                $data['model_status']=$this->Web->get_web_model_status($web_id,$m_id);
                #单独配置读取
                $data['drive_conf']=$this->Web->get_web_model_info($web_id,$m_id);
                #查询是否有crm
                $data['crm']=$this->Adv->get_adv_api_by_id($data['adv_info']['id']);
                $data['web_crm']=$this->Web->get_webcrm_by_webid($web_id);
                $this->_view('Drive_conf.php',$data);

            }else{
                #提交数据
                $param['web_id']=$this->input->post('web_id');
                $crm=$this->input->post('crm');
                if ($crm!==null) {
                    $param['is_crm']=$crm;
                    $param['api_id']=$this->input->post('api');
                }
                $web_crm=$this->Web->get_webcrm_by_webid($param['web_id']);
                if ($web_crm) {        
                    $res=$this->Web->update_crm_conf($param['web_id'],$param);
                    $this->_log_message('Update','Trydrive'.$param['web_id'],json_encode($param));
                }else{
                    $this->Web->write_drive_conf($param); 
                    $this->_log_message('Add','Trydrive',json_encode($param));
                }

                #修改统一配置
                $m_id=$this->input->post('m_id');
                $wm['start_date']=$this->input->post('start_date');
                $wm['end_date']=$this->input->post('end_date');
                $wm['is_use']=$this->input->post('is_use');
                if ($this->Web->up_web_model($param['web_id'],$m_id,$wm)) {
                    $this->_log_message('Update','Web_model'.$param['web_id'],json_encode($wm));
                    $this->_success('修改成功');
                }else{
                    $this->_error('修改失败');
                }
                
               
            }
        }
    }
       
    /**
     * [web_model_list 站点功能列表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @return   [type]     [description]
     */
    public function web_model_list(){
        //分页类
        $this->load->library('pagination');
        //配置分页参数
        $this->load->library('pager');
        $offset=$this->uri->segment(3,1);
        if (self::is_supper()) {
            $page_config['total_row'] =$this->db->count_all('web_model');
            $list=$this->Web->get_web_model_page(10,intval($offset-1));
        }else{
            $id = $this->session->userdata('aid');
            $advstr=$this->Adv->get_ower_adv($id);
            $adv_arr=explode(',', $advstr['advertisers']);
            $page_config['total_row'] =$this->Web->count_admin_all_wm($adv_arr);
            $list=$this->Web->get_admin_web_model_page(10,intval($offset-1),$adv_arr);
        }
        
        $page_config['cur_page'] = $this->uri->segment(3,1);
        $page_config['base_link_url'] =site_url('C_Website/web_model_list/');
        $page_config['page_size'] = 10;
        $data['page'] = $this->pager->create($page_config);
        
        $data['result']=$list;
        $this->_view('Webmodel_list.php',$data);
    }
    /*--------------校验部分代码--------------*/
    /**
     * [check_code_exit ]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-13
     * @param    [type]     $code [description]
     * @return   [type]           [description]
     */
    public function check_code_exit($code){
        $res=$this->Web->get_id_by_code($code);
        if ($res) {
            $this->form_validation->set_message('check_code_exit', '唯一码重复');
            return false;
        }else{
            return True;
        }
    }
}
