<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_Colum_model extends CI_Model{
    /**
     * [get_table_col_by_tid 根据表id获取表字段]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-06
     * @param    [type]     $t_id [description]
     * @return   [type]           [description]
     */
    public function get_table_col_by_tid($t_id){
        $query=$this->db->select('*')->where(array('t_id'=>$t_id))->get('table_colums');
        return $query->result_array();
    }
    public function up_col($id,$param){
        return $this->db->where(array('id'=>$id))->update('table_colums',$param);
    }
    /**
     * [get_colinfo_by_id 获取字段详细信息]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-16
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_colinfo_by_id($id){
        return $this->db->select('*')->where(array('id'=>$id))->get('table_colums')->row_array();
    }
    /**
     * [write_database 数据写入]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-07
     * @param    [type]     $param [description]
     * @return   [type]            [description]
     */
    public function write_database($param){
        return $this->db->insert('table_colums', $param);
    }
    /**
     * [get_columid_of_table_by_name 获取表字段id]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-07
     * @param    [type]     $colname [description]
     * @param    [type]     $t_id    [description]
     * @return   [type]              [description]
     */
    public function get_columid_of_table_by_name($colname,$t_id){
        $query=$this->db->select('id')->where(array('colum_name'=>$colname,'t_id'=>$t_id))->get('table_colums');
        return $query->row_array();
    }
    /**
     * [get_page 字段分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-07
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_page($num,$offset){
        $query=$this->db->select('tab.table_name,col.*')
            ->from($this->db->dbprefix('table_colums').' as col')
            ->join($this->db->dbprefix('model_tables').' as tab','col.t_id=tab.id')
            ->limit($num,$num*$offset)
            ->get();
        return $query->result_array();
    }
    /**
     * [get_colums_by_tid 获取表的所有字段]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-08
     * @param    [type]     $t_id [description]
     * @return   [type]           [description]
     */
    public function get_colums_by_tid($t_id){
        $query=$this->db->select('*')
            ->where(array('t_id'=>$t_id))
            ->get('table_colums');
        return $query->result_array();
    }
}
