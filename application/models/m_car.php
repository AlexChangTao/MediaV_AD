<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('prc');

class M_car extends CI_Model
{	
	/**
	 * [get_all_count 获取所有车系总数]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-07
	 * @return   [type]     [description]
	 */
	public function get_all_count(){
		return $this->db->count_all('car');
	}
	/**
	 * [get_admin_car_count 获取管理员权限内车系数量]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-25
	 * @param    [type]     $adv_arr [description]
	 * @return   [type]              [description]
	 */
	public function get_admin_car_count($adv_arr){
		return $this->db->where_in('ad_id',$adv_arr)
			->from('car')
			->count_all_results();
	}
	/**
	 * [get_carid_by_code 根据车系代码获取车系id]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-19
	 * @param    [type]     $code [description]
	 * @return   [type]           [description]
	 */
	public function get_carid_by_code($code){
		$query=$this->db->select('id')
			->where(array('car_code'=>$code))
			->get('car');
		return $query->row_array();
	}
	/**
	 * [get_adv_car 获取经销商所有车]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-14
	 * @param    [type]     $ad_id [description]
	 * @return   [type]            [description]
	 */
	public function get_adv_car($ad_id){
		$query=$this->db->where(array('ad_id'=>$ad_id))->get('car');
		return $query->result_array();
	}
	/**
	 * [get_all_car_page 获取所有车系分页]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-07
	 * @param    [type]     $num    [description]
	 * @param    [type]     $offset [description]
	 * @return   [type]             [description]
	 */
	public function get_all_car_page($num,$offset){
		$data = $this->db->select('c.id, c.name, c.car_code, c.car_img_thumb, c.is_use,a.id as ad_id, a.name as adver_name')
							->from('car as c')
							->join('advertisers as a', 'a.id = c.ad_id', 'left')
							->limit($num, $num*$offset)
							->order_by('c.id','desc')
							->get()
							->result_array();
		return $data;
	}
	/**
	 * [get_admin_car_page 获取管理员权限内车系]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-25
	 * @param    [type]     $num     [description]
	 * @param    [type]     $offset  [description]
	 * @param    [type]     $adv_arr [description]
	 * @return   [type]              [description]
	 */
	public function get_admin_car_page($num,$offset,$adv_arr){
		$data = $this->db->select('c.id, c.name, c.car_code, c.car_img_thumb, c.is_use,a.id as ad_id, a.name as adver_name')
							->from('car as c')
							->join('advertisers as a', 'a.id = c.ad_id', 'left')
							->where_in('c.ad_id',$adv_arr)
							->order_by('c.id','desc')
							->limit($num, $num*$offset)
							->get()
							->result_array();
		return $data;
	}
	/**
	 * 获取广告主列表
	 * return array
	 */
	public function get_car_list($where = '1 = 1', $offset, $page_size)
	{
		$data = $this->db->select('c.id, c.name, c.car_code, c.car_img_thumb, c.is_use,a.id as ad_id, a.name as adver_name')
							->from('car as c')
							->join('advertisers as a', 'a.id = c.ad_id', 'left')
							->where($where)
							->order_by('c.id','desc')
							->limit($page_size, $page_size*$offset)
							->get()
							->result_array();

		return $data;
	}

	/**
	 * 获取该广告主下的车系名称
	 */ 
	public function get_car_name($id)
	{
		$data = $this->db->select('id, name')->where(array('ad_id' => $id, 'is_use' => 0))->get('car')->result_array();
		return $data;
	}

	public function get_count($where = '1 = 1')
	{
		$count = $this->db->from('car')
						->where($where)
						->count_all_results();
		return $count;
	}

	/**
	 * 获取要编辑广告主的信息
	 */
	public function get_car_info($id)
	{
		if($id)
		{
			$data = $this->db->where(array('id' => $id))->get('car')->row_array();
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
			$car = $this->get_car_info($data['id']);

			$bool = $this->db->where(array('id' => $data['id']))->update('car', $data);
			if($bool)
			{
				if(!empty($car['car_img']) && $data['car_img'] != $car['car_img'])  // 删除之前在服务器上的图片
				{
					@unlink('./uploads/car_img/' . $car['car_img']);
					@unlink('./uploads/car_img/' . $car['car_img_thumb']);
				}		
			}

			return $bool;
		}
		else
		{
			unset($data['id']);
			$bool = $this->db->insert('car', $data);
			return $bool;
		}
	}
}
