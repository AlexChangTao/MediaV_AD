<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_Features_model extends CI_Model{
    public function get_modle_code_by_id($id){
        $query=$this->db->select('code')->where(array('id'=>$id))->get('models');
        return $query->row_array();
    }
    /**
     * [get_id_by_code 根据code查找id]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     * @param    [str]     $code [description]
     * @return   [type]           [description]
     */
    public function get_id_by_code($code){
        $query=$this->db->select('id')
                ->where(array('code'=>$code))
                ->get('models');
        return $query->row_array();
    }
    /**
     * [get_modelcode_by_id 获取模块code]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-27
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_modelcode_by_id($id){
        $query=$this->db->select('code')
                ->where(array('id'=>$id))
                ->get('models');
        return $query->row_array();
    }
    /**
     * [up_model 数据修改]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $id    [description]
     * @param    [type]     $param [description]
     * @return   [type]            [description]
     */
    public function up_model($id,$param){
        return $this->db->where(array('id'=>$id))
            ->update('models',$param);
    }
    /**
     * [write_data 数据插入]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     * @param    [type]     $param [description]
     * @return   [type]            [description]
     */
    public function write_data($param){
        return $this->db->insert('models', $param);
    }
    /**
     * [get_model_of_trade 获取行业所有模块]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-13
     * @param    [type]     $trade [description]
     * @return   [type]            [description]
     */
    public function get_model_of_trade($trade){
        $query=$this->db->select('name,id')
            ->where(array('trade'=>$trade))
            ->get('models');
        return $query->result_array();
    }
    /**
     * [get_page 分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-02
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_model_page($num,$offset){
        $query=$this->db->get('models',$num,$num*$offset);
        return $query->result_array();
    }
    /**
     * [get_model_list 获取模块列表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-05
     * @return   [type]     [description]
     */
    public function get_model_list(){
        $query=$this->db->select('id,name')->get('models');
        return $query->result_array();
    }
    /**
     * [get_modelname_by_id 根据模块id 获取模块名字]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-06
     * @param    [type]     $mid [description]
     * @return   [type]          [description]
     */
    public function get_modelname_by_id($mid){
        $query=$this->db->select('name')->where(array('id'=>$mid))->get('models');
        return $query->row_array();
    }
    /**
     * [get_info_by_id 获取模块详细信息]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_info_by_id($id){
        return $this->db->select('*')
                ->where(array('id'=>$id))
                ->get('models')
                ->row_array();
    }
    /**
     * [get_mod_private 模块是否私有]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-30
     * @param    [type]     $code [description]
     * @return   [type]           [description]
     */
    public function get_mod_private($code){
        $query=$this->db->select('is_private')
            ->where(array('code'=>$code))
            ->get('models');
        return $query->row_array();
    }
    /**
     * [get_private_table_of_trade 获取建立广告主时需要初始化的表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-08
     * @param    [type]     $trade [description]
     * @return   [type]            [description]
     */
    public function get_private_table_of_trade($trade){
        $query=$this->db->select('tab.id,tab.table_type,tab.table_name')
            ->from($this->db->dbprefix('models').' as mod')
            ->join($this->db->dbprefix('model_tables').' as tab','mod.id=tab.id')
            ->where(array('mod.trade'=>$trade,'mod.is_private'=>1))
            ->get();
        return $query->result_array();
    }
    /**
     * [get_private_table_of_trade 获取建立广告主时需要初始化的表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-08
     * @param    [type]     $trade [description]
     * @return   [type]            [description]
     */
    public function get_model_table_by_modelid($id){
        $query=$this->db->select('tab.id,tab.table_type,tab.table_name,mod.is_private')
            ->from($this->db->dbprefix('models').' as mod')
            ->join($this->db->dbprefix('model_tables').' as tab','mod.id=tab.id')
            ->where(array('mod.id'=>$id))
            ->get();
        return $query->result_array();
    }
}
