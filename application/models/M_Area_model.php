<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_Area_model extends CI_Model{
    /**
     * [get_province_city 获取所有省市组合]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-13
     * @return   [type]     [description]
     */
    public function get_province_city(){
        $query=$this->db->select('a.name as province_name,a.id as province_id,ar.name as city_name,ar.id as city_id')
            ->from($this->db->dbprefix('area').' as a')
            ->join($this->db->dbprefix('area').' as ar','ar.parent_id=a.id')
            ->where(array('a.parent_id'=>0))
            ->get();
        return $query->result_array();
    }
    /**
     * [get_parent_id 获取父级id]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-19
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_parent_id($id){
        return $this->db->select('parent_id')->where(array('id'=>$id))->get('area')->row_array();
    }
    /**
     * [get_name_by_id 获取名字]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-19
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_name_by_id($id){
        return $this->db->select('name')->where(array('id'=>$id))->get('area')->row_array();
    }
}
