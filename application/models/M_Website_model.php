<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_Website_model extends CI_Model{
    /**
     * [get_ad_total_row 获取广告主站点数]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $ad_id [description]
     * @return   [type]            [description]
     */
    public function get_ad_total_row($ad_id){
        $query=$this->db->select('id')
            ->where(array('ad_id'=>$ad_id))
            ->get('website');
        return $query->num_rows();
    }
    /**
     * [get_webcode_by_id 获取站点code]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-21
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_webcode_by_id($id){
        $query=$this->db->select('web_code')
                ->where(array('id'=>$id))
                ->get('website');
        return $query->row_array();
    }
    /**
     * [get_web_model_conf_by_code 找到站点试驾功能的开始结束时间]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-21
     * @param    [type]     $code [description]
     * @return   [type]           [description]
     */
    public function get_web_drive_conf_by_code($code){
        $query=$this->db->select('wm.start_date,wm.end_date')
            ->from($this->db->dbprefix('web_model'). ' as wm')
            ->join($this->db->dbprefix('models').' as mod','mod.id=wm.m_id')
            ->join($this->db->dbprefix('website').' as web','web.id=wm.web_id')
            ->where(array('mod.name'=>'预约试驾','web.web_code'=>$code))
            ->get();
        return $query->row_array();

    }
    public function get_advcode_by_webcode($web_code){
        $query=$this->db->select('adv.code')
                ->from($this->db->dbprefix('website').' as web')
                ->join($this->db->dbprefix('advertisers').' as adv','web.ad_id=adv.id')
                ->where(array('web.web_code'=>$web_code))
                ->get();
        return $query->row_array();
    }
    /**
     * [get_adv_id_by_webcode 获取广告主id]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-27
     * @param    [type]     $code [description]
     * @return   [type]           [description]
     */
    public function get_adv_id_by_webcode($code){
        $query=$this->db->select('ad_id,id')
                ->where(array('web_code'=>$code))
                ->get('website');
        return $query->row_array();
    }
    /**
     * [get_advcode_by_webid 根据站点id获取广告主的code]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-21
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_advcode_by_webid($id){
        $query=$this->db->select('adv.code')
                ->from($this->db->dbprefix('website').' as web')
                ->join($this->db->dbprefix('advertisers').' as adv','web.ad_id=adv.id')
                ->where(array('web.id'=>$id))
                ->get();
        return $query->row_array();
    }

    public function count_admin_all($adv_arr){
        $query=$this->db->where_in('id',$adv_arr)
            ->from('website')->count_all_results();
        return $query;
    }
    /**
     * [get_web_page 获取所有的站点分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-12
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_web_page($num,$offset){
        $query=$this->db->select('adv.id as ad_id, adv.name,web.*')
            ->from($this->db->dbprefix('website').' as web')
            ->join($this->db->dbprefix('advertisers').' as adv','web.ad_id=adv.id')
            ->order_by('web.id','desc')
            ->limit($num,$num*$offset)

            ->get();
        return $query->result_array();
    }
    /**
     * [get_admin_web_page 获取管理员权限内站点]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-25
     * @param    [type]     $num     [description]
     * @param    [type]     $offset  [description]
     * @param    [type]     $adv_arr [description]
     * @return   [type]              [description]
     */
    public function get_admin_web_page($num,$offset,$adv_arr){
        $query=$this->db->select('adv.id as ad_id, adv.name,web.*')
            ->from($this->db->dbprefix('website').' as web')
            ->join($this->db->dbprefix('advertisers').' as adv','web.ad_id=adv.id')
            ->where_in('adv.id',$adv_arr)
            ->order_by('web.id','desc')
            ->limit($num,$num*$offset)
            ->get();
        return $query->result_array();
    }
    /**
     * [get_media 获取启用媒体]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-21
     * @return   [type]     [description]
     */
    public function get_media(){
        $query=$this->db->select('*')
            ->where(array('status'=>1))
            ->get('media');
        return $query->result_array();
    }
    /**
     * [get_webinfo_by_webcode 根据站点code获取站点信息]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $web_code [description]
     * @return   [type]               [description]
     */
    public function get_webinfo_by_webcode($web_code){
        $query=$this->db->select('*')
                ->where(array('web_code'=>$web_code))
                ->get('website');
        return $query->row_array();
    }
    /**
     * [write_drive_conf 试驾配置编辑]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @param    [type]     $param [description]
     * @return   [type]            [description]
     */
    public function write_drive_conf($param){
        $this->db->insert('web_drive',$param);
        return $this->db->insert_id();
    }
    /**
     * [update_crm_conf description]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-28
     * @param    [type]     $web_id [description]
     * @param    [type]     $param  [description]
     * @return   [type]             [description]
     */
    public function update_crm_conf($web_id,$param){
        return $this->db->where(array('web_id'=>$web_id))
            ->update('web_drive',$param);
    }
    /**
     * [get_advid_by_code 根据站点code获取广告主id]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-19
     * @param    [type]     $code [description]
     * @return   [type]           [description]
     */
    public function get_advid_by_code($code){
        $query= $this->db->select('ad_id')
            ->where(array('web_code'=>$code))
            ->get('website');
        return $query->row_array(); 
    }
    /**
     * [get_ad_web_page 获取广告主的站点分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-12
     * @param    [type]     $ad_id  [description]
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_ad_web_page($ad_id,$num,$offset){
        $query=$this->db->select('adv.id as ad_id, adv.name,web.*')
            ->from($this->db->dbprefix('website').' as web')
            ->join($this->db->dbprefix('advertisers').' as adv','web.ad_id=adv.id')
            ->limit($num,$num*$offset)
            ->order_by('web.id','desc')
            ->where(array('web.ad_id'=>$ad_id))
            ->get();
        return $query->result_array();
    }
    /**
     * [get_webadv_info_by_webid 获取一个站点与所有者的信息]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_webadv_info_by_webid($id){
        $query=$this->db->select('adv.name,web.web_name,adv.id,adv.code')
            ->from($this->db->dbprefix('website').' as web')
            ->join($this->db->dbprefix('advertisers').' as adv','web.ad_id=adv.id')
            ->where(array('web.id'=>$id))
            ->get();
        return $query->row_array();
    }
    /**
     * [get_web_model_status 获取功能模块状态]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @param    [int]     $web_id [网站id]
     * @param    [int]     $m_id   [功能id]
     * @return   [arr]             
     */
    public function get_web_model_status($web_id,$m_id){
        $query=$this->db->select('is_use,start_date,end_date')
                ->where(array('web_id'=>$web_id,'m_id'=>$m_id))
                ->get('web_model');
        return $query->row_array();
    }
    /**
     * [up_website 站点修改]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $id    [description]
     * @param    [type]     $param [description]
     * @return   [type]            [description]
     */
    public function up_website($id,$param){
        return $this->db->where(array('id'=>$id))
            ->update('website',$param);
    }
    /**
     * [get_web_modelid_list 获取站点功能id]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $web_id [description]
     * @return   [type]             [description]
     */
    public function get_web_modelid_list($web_id){
        return $this->db->select('m_id')
                ->where(array('web_id'=>$web_id))
                ->get('web_model')
                ->result_array();
    }
    /**
     * [get_web_info_by_id 根据id获取站点信息]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_web_info_by_id($id){
        $query=$this->db->select('*')
            ->where(array('id'=>$id))
            ->get('website');
        return $query->row_array();
    }
    /**
     * [get_web_drive_info 获取网站试驾配置]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @param    [type]     $web_id [description]
     * @param    [type]     $m_id   [description]
     * @return   [type]             [description]
     */
    public function get_web_model_info($web_id,$m_id){
        $query=$this->db->select('is_use,start_date,end_date')
                ->where(array('web_id'=>$web_id,'m_id'=>$m_id))
                ->get('web_model');
        return $query->row_array();
    }
    /**
     * [get_webcrm_by_webid 获取站点的crm]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-28
     * @param    [type]     $web_id [description]
     * @return   [type]             [description]
     */
    public function get_webcrm_by_webid($web_id){
        $query=$this->db->select('*')
                ->where(array('web_id'=>$web_id))
                ->get('web_drive');
        return $query->row_array();
    }
    /**
     * [up_web_model_status 修改功能模块状态]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @param    [int]     $web_id [网站id]
     * @param    [int]     $m_id   [功能id]
     * @param    [int]     $status [状态]
     * @return   [type]             [description]
     */
    public function up_web_model($web_id,$m_id,$param){
        return $this->db->where(array('web_id'=>$web_id,'m_id'=>$m_id))
                ->update('web_model',$param);
    }
    /**
     * [count_webmodel 获取站点功能数]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-06
     * @param    [type]     $web_id [description]
     * @return   [type]             [description]
     */
    public function count_webmodel($web_id){
        $query=$this->db->select('id')
            ->where(array('web_id'=>$web_id))
            ->get('web_model');
        return $query->num_rows();
    }
    /**
     * [count_all_by_name 统计总条数]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-06
     * @param    [type]     $name [description]
     * @return   [type]           [description]
     */
    public function count_all_by_name($name){
        $query=$this->db->select('id')
                ->like('web_name',$name,'both')
                ->get('website');
        return $query->num_rows();
    }

    /**
     * [get_web_page 获取所有的站点分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-12
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_web_page_by_name($name,$num,$offset){
        $query=$this->db->select('adv.name,web.*')
            ->from($this->db->dbprefix('website').' as web')
            ->join($this->db->dbprefix('advertisers').' as adv','web.ad_id=adv.id')
            ->like('web.web_name',$name,'both')
            ->order_by('web.id','desc')
            ->limit($num,$num*$offset)
            ->get();
        return $query->result_array();
    }
    /**
     * [get_web_model_page 站点功能分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_web_model_page_of_one($webid,$num,$offset){
        $query=$this->db->select('adv.name as adv_name, m.name as mname,m.id as m_id,web.*,wm.is_use,wm.start_date as m_start_date,wm.end_date as m_end_date')
                ->from($this->db->dbprefix('web_model').' as wm')
                ->join($this->db->dbprefix('website').' as web','wm.web_id=web.id')
                ->join($this->db->dbprefix('models').' as m','wm.m_id=m.id')
                ->join($this->db->dbprefix('advertisers').' as adv','adv.id=web.ad_id')
                ->where(array('wm.web_id'=>$webid))
                ->limit($num,$num*$offset)
                ->get();
        return $query->result_array();
    }
    /**
     * [count_admin_all_wm 获取管理员权限内站点功能总数]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-25
     * @param    [type]     $adv_arr [description]
     * @return   [type]              [description]
     */
    public function count_admin_all_wm($adv_arr){
        return $this->db->select('web.ad_id')
            ->from($this->db->dbprefix('web_model').' as wm')
            ->join($this->db->dbprefix('website').' as web','wm.web_id=web.id')
            ->where_in('web.ad_id',$adv_arr)
            ->count_all_results();
    }
    /**
     * [get_admin_web_model_page 获取管理员权限内站点功能列表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-25
     * @param    [type]     $num     [description]
     * @param    [type]     $offset  [description]
     * @param    [type]     $adv_arr [description]
     * @return   [type]              [description]
     */
    public function get_admin_web_model_page($num,$offset,$adv_arr){
        $query=$this->db->select('adv.name as adv_name, m.name as mname,m.id as m_id,web.*,wm.is_use,wm.start_date as m_start_date,wm.end_date as m_end_date')
                ->from($this->db->dbprefix('web_model').' as wm')
                ->join($this->db->dbprefix('website').' as web','wm.web_id=web.id')
                ->join($this->db->dbprefix('models').' as m','wm.m_id=m.id')
                ->join($this->db->dbprefix('advertisers').' as adv','adv.id=web.ad_id')
                ->where_in('adv.id',$adv_arr)
                ->limit($num,$num*$offset)
                ->get();
        return $query->result_array();
    }
    /**
     * [get_web_model_page 站点功能分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_web_model_page($num,$offset){
        $query=$this->db->select('adv.name as adv_name, m.name as mname,m.id as m_id,web.*,wm.is_use,wm.start_date as m_start_date,wm.end_date as m_end_date')
                ->from($this->db->dbprefix('web_model').' as wm')
                ->join($this->db->dbprefix('website').' as web','wm.web_id=web.id')
                ->join($this->db->dbprefix('models').' as m','wm.m_id=m.id')
                ->join($this->db->dbprefix('advertisers').' as adv','adv.id=web.ad_id')
                ->limit($num,$num*$offset)
                ->get();
        return $query->result_array();
    }
    /**
     * [get_id_by_code description]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-13
     * @param    [str]     $code [网站编码]
     * @return   [type]           [description]
     */
    public function get_id_by_code($code){
        $query=$this->db->select('id')
            ->where(array('web_code'=>$code))
            ->get('website');
        return $query->row_array();
    }
    /**
     * [write_data 写入数据]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-13
     * @param    [arr]     $param [网站配置数据]
     * @return   [type]            [description]
     */
    public function write_data($param){
        $this->db->insert('website', $param);
        return $this->db->insert_id();
    }
    /**
     * [get_webname_by_webid 获取站点名称]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-06
     * @param    [type]     $web_id [description]
     * @return   [type]             [description]
     */
    public function get_webname_by_webid($web_id){
        $query=$this->db->select('web_name')
                ->where(array('id'=>$web_id))
                ->get('website');
        return $query->row_array();
    }
    /**
     * [get_web_model_by_webid 根据站点id获取站点功能列表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @param    [int]     $webid [网站id]
     * @return   [type]            [description]
     */
    public function get_web_model_by_webid($webid){
        $query=$this->db->select('m.name,wm.m_id as id,wm.start_date,wm.end_date,wm.is_use')
            ->from($this->db->dbprefix('web_model').' as wm')
            ->join($this->db->dbprefix('models').' as m','wm.m_id=m.id')
            ->where(array('wm.web_id'=>$webid))
            ->get();
        return $query->result_array();
    }
    /**
     * [write_web_model 关联表写入]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-14
     * @param    [array]     $param [表单功能数据]
     * @return   [type]            [description]
     */
    public function write_web_model($param){
        return $this->db->insert_batch('web_model',$param);
    }
}
