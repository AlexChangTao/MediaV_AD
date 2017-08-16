<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class M_Api_model extends CI_Model{
    /**
     * [get_api_info_by_apiid 获取Api详情]
     * @author Greedywolf 1154505909@qq.com
     * @DateTime 2017-06-29
     * @param    [type]     $id [description]
     * @return   [type]         [description]
     */
    public function get_api_info_by_apiid($id){
        $query=$this->db->select('*')
            ->where(array('id'=>$id))
            ->get('api');
        return $query->row_array();
    }
}
