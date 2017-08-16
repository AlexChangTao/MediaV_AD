<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('prc');

class M_advertiser extends CI_Model
{

    /**
     * 获取广告主列表
     * return array
     */
    public function get_advertiser_list($where = '1 = 1', $offset, $page_size)
    {
        $data = $this->db->select()
                            ->from('advertisers')
                            ->where($where)
                            ->limit($page_size, $offset)
                            ->get()
                            ->result_array();
        return $data;
    }

    public function get_count($where = '1 = 1')
    {
        $count = $this->db->from('advertisers')
                        ->where($where)
                        ->count_all_results();
        return $count;
    }

    /**
     * 获取要编辑广告主的信息
     */
    public function get_advertiser_info($id)
    {
        if($id)
        {
            $data = $this->db->where(array('id' => $id))->get('advertisers')->row_array();
            return $data;
        }
    }

    /**
     * 新增或更新广告主
     */
    public function add_or_update($data)
    {
        if($data['id'])
        {
            $bool = $this->db->where(array('id' => $data['id']))->update('advertisers', $data);
            if($bool)
            {
                redirect('admin/c_advertiser/index');
            }
            else
            {
                showmessage('参数错误', 'back', 1);
            }           
        }
        else
        {
            unset($data['id']);
            $bool = $this->db->insert('advertisers', $data);

            if($bool)
            {
                redirect('admin/c_advertiser/index');
            }
            else
            {
                showmessage('参数错误', 'back', 1);
            }
        }
    }
}
