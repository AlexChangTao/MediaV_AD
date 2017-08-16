<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_Trydrive_model extends CI_Model{
    public function write_drive_data($table,$param){
        $this->db->insert($table, $param);
        return $this->db->insert_id();
    }
    /**
     * [get_drive_crm_conf 获取试驾crm配置]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-21
     * @param    [type]     $web_id [description]
     * @return   [type]             [description]
     */
    public function get_drive_crm_conf($web_id){
        $query=$this->db->select('*')
                ->where(array('web_id'=>$web_id))
                ->get('web_drive');
        return $query->row_array();
    }
}
