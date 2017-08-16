<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_Advertisers_model extends CI_Model{
    /**
     * [get_id_by_code 根据code查询id]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-08
     * @param    [type]     $code [description]
     * @return   [type]           [description]
     */
    public function get_id_by_code($code){
        $query=$this->db->select('id')
            ->where(array('code'=>$code))
            ->get('advertisers');
        return $query->row_array();
    }
    /**
     * [get_advname_by_id 获取广告主名称]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-19
     * @param    [type]     $ad_id [description]
     * @return   [type]            [description]
     */
    public function get_advname_by_id($ad_id){
        $query=$this->db->select('name')
            ->where(array('id'=>$ad_id))
            ->get('advertisers');
        return $query->row_array();
    }
    public function get_advcode_by_id($ad_id){
        $query=$this->db->select('code')
            ->where(array('id'=>$ad_id))
            ->get('advertisers');
        return $query->row_array();
    }
    /**
     * [count_all_of_name 统计条数]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-06
     * @param    [type]     $name [description]
     * @return   [type]           [description]
     */
    public function count_all_of_name($name){
        $query=$this->db->select('id')
                ->like('name',$name,'both')
                ->get('advertisers');
        return $query->num_rows();
                
    }
    /**
     * [count_admin_of_name 搜索管理员权限内]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-27
     * @param    [type]     $name    [description]
     * @param    [type]     $adv_arr [description]
     * @return   [type]              [description]
     */
    public function count_admin_of_name($name,$adv_arr){
        $query=$this->db->select('id')
                ->where_in('id',$adv_arr)
                ->like('name',$name,'both')
                ->get('advertisers');
        return $query->num_rows();
    }
    /**
     * [get_adv_list_by_name_of_search 搜索分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-06
     * @param    [type]     $name   [description]
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_adv_list_by_name_of_search($name,$num,$offset){
        $query=$this->db->select("*")
            ->like('name',$name,'both')
            ->limit($num,$offset*$num)
            ->order_by('id','desc')
            ->get('advertisers');
        return $query->result_array();
    }
    /**
     * [get_adminadv_list_by_name_of_search 管理员权限内搜索]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-27
     * @param    [type]     $name    [名字]
     * @param    [type]     $num     [条数]
     * @param    [type]     $offset  [页码]
     * @param    [type]     $adv_arr [广告主id数组]
     * @return   [type]              [description]
     */
    public function get_adminadv_list_by_name_of_search($name,$num,$offset,$adv_arr){
        $query=$this->db->select("*")
            ->where_in('id',$adv_arr)
            ->like('name',$name,'both')
            ->limit($num,$offset*$num)
            ->order_by('id','desc')
            ->get('advertisers');
        return $query->result_array();
    }
    /**
     * [get_name_by_id description]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-13
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_name_by_id($id){
        $query=$this->db->select('name')
            ->where(array('id'=>$id))
            ->get('advertisers');
        return $query->row_array();
    }
    /**
     * [edit_adv 修改信息]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $id    [description]
     * @param    [type]     $param [description]
     * @return   [type]            [description]
     */
    public function edit_adv($id,$param){
        return $this->db->where(array('id'=>$id))
                ->update('advertisers',$param);
    }
    /**
     * [get_ad_trade_by_id 获取广告主行业]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-13
     * @return   [type]     [description]
     */
    public function get_adv_info_by_id($id){
        $query=$this->db->select('*')
                ->where(array('id'=>$id))
                ->get('advertisers');
        return $query->row_array();
    }
    /**
     * [get_crm_by_id 获取广告主的接口]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-21
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_adv_api_by_id($id){
        $query=$this->db->select('*')
            ->where(array('advertiser_id'=>$id))
            ->get('api');
            return $query->result_array();
    }
    /**
     * [get_page 分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-12
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_adv_page($num,$offset){
        $query=$this->db->select("*")
            ->limit($num,$offset*$num)
            ->order_by('id','asc')
            ->get('advertisers');
        return $query->result_array();
    }
    /**
     * [get_user_adv_page 获取管理员的广告主列表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-25
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @param    [type]     $user   [description]
     * @return   [type]             [description]
     */
    public function get_user_adv_page($num,$offset,$adv_arr){
        $query=$this->db->select("*")
            ->where_in('id',$adv_arr)
            ->limit($num,$offset*$num)
            ->order_by('id','asc')
            ->get('advertisers');
        return $query->result_array();
    }
    /**
     * [get_ower_adv 获取当前管理员所有广告主]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-25
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_ower_adv($id){
        $query=$this->db->select('advertisers')
                ->where(array('id'=>$id))
                ->get('admin');
        return $query->row_array();
    }
    /**
     * [write_database 数据写入]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-07
     * @param    [type]     $param [description]
     * @return   [type]            [description]
     */
    public function write_database($param){
        $this->db->insert('advertisers', $param);
        return $this->db->insert_id();
    }
    /**
     * [get_code_by_id 获取广告主code]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-20
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_code_by_id($id){
        $query=$this->db->select('code')
                ->where(array('id'=>$id))
                ->get('advertisers');
        return $query->row_array();
    }
    /**
     * [create_table 创建表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-09
     * @param    [type]     $sql       [description]
     * @param    [type]     $tablename [description]
     * @return   [type]                [description]
     */
    public function create_table_by_sql($sql){
        return $this->db->simple_query($sql);
    }
}
