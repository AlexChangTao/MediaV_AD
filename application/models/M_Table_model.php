<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_Table_model extends CI_Model{
    /**
     * [get_tableid_by_name 根据表名获取表id]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-05
     * @param    [type]     $table_name [description]
     * @return   [type]                 [description]
     */
    public function get_tableid_by_name($table_name){
        $query=$this->db->select('id')
            ->where(array('table_name'=>$table_name))
            ->get('model_tables');
        return $query->row_array();
    }
    /**
     * [get_like_table 查询所有表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-10
     * @param    [type]     $table_name [description]
     * @return   [type]                 [description]
     */
    public function get_like_table($table_name){
        $query=$this->db->query("select table_name from information_schema.tables where  table_name like '".$this->db->dbprefix($table_name)."%';");
        return $query->result_array();
    }
    /**
     * [count_table_row 统计条数]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-10
     * @param    [type]     $table_name [description]
     * @return   [type]                 [description]
     */
    public function count_table_row($table_name){
        return $this->db->count_all($table_name);
    }
    public function get_table_space($table_name){
        $query=$this->db->query("SELECT sum(DATA_LENGTH)+sum(INDEX_LENGTH) as space FROM information_schema.TABLES where TABLE_NAME='$table_name';");
        return $query->row_array();
    }
    /**
     * [get_model_tabcol_info 获取模块下表详情]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-27
     * @param    [type]     $m_id [description]
     * @return   [type]           [description]
     */
    public function get_model_tabcol_info($m_id){
        $query=$this->db->select('mb.table_name,col.colum_name,col.colum_type,col.remark,col.emp')
            ->from($this->db->dbprefix('model_tables').' as mb')
            ->join($this->db->dbprefix('table_colums').' as col','mb.id=col.t_id')
            ->where(array('mb.m_id'=>$m_id))
            ->get();
            return $query->result_array();
    }
    /**
     * [get_table_by_mid 根据模块id获取包含表]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-06
     * @param    [type]     $m_id [description]
     * @return   [type]           [description]
     */
    public function get_table_by_mid($m_id){
        $query=$this->db->select('id,table_name')
            ->where(array('m_id'=>$m_id))
            ->get('model_tables');
        return $query->result_array();
    }
    /**
     * [get_modelid_by_table 根据表获取]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-17
     * @param    [type]     $table_id [description]
     * @return   [type]               [description]
     */
    public function get_modelid_by_table($table_id){
        $query=$this->db->select('m_id')
                ->where(array('id'=>$table_id))
                ->get('model_tables');
        return $query->row_array();
    }
    /**
     * [count_modeluse 功能应用数]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-07-17
     * @param    [type]     $m_id [description]
     * @return   [type]           [description]
     */
    public function count_modeluse($m_id){
        return $this->db->from('web_model')
                ->where(array('m_id'=>$m_id))
                ->count_all_results();
    }
    /**
     * [write_database 数据写入]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-06
     * @param    [type]     $param [description]
     * @return   [type]            [description]
     */
    public function write_database($param){
        $this->db->insert('model_tables', $param);
        return $this->db->insert_id();
    }
    /**
     * [get_table_name_by_id description]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-07
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_table_name_by_id($id){
        return $this->db->select('table_name')
            ->where(array('id'=>$id))
            ->get('model_tables')
            ->row_array();
    }
    /**
     * [get_page 表--分页]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-07
     * @param    [type]     $num    [description]
     * @param    [type]     $offset [description]
     * @return   [type]             [description]
     */
    public function get_tab_page($num,$offset){
        $query=$this->db->select('mod.name,tab.*')
            ->from($this->db->dbprefix('model_tables').' as tab')
            ->join($this->db->dbprefix('models').' as mod','tab.m_id=mod.id')
            ->limit($num,$num*$offset)
            ->get();
        return $query->result_array();
    }
}
