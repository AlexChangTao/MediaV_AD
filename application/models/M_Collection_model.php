<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_Collection_model extends CI_Model{
    /**
     * [write_data 采集数据写入]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-27
     * @param    [type]     $table [description]
     * @param    [type]     $param [description]
     * @return   [type]            [description]
     */
    public function write_collection_data($table,$param){
        return $this->db->insert($table, $param);
    }
}
