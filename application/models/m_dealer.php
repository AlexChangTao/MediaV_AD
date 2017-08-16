<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
date_default_timezone_set('prc');

class M_dealer extends CI_Model
{	
	/**
	 * [get_all_count 获取所有经销商总数]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-07
	 * @return   [type]     [description]
	 */
	public function get_all_count(){
		return $this->db->count_all('dealer');
	}
	/**
	 * [get_admin_count 获取管理员全选以内的经销商]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-25
	 * @param    [type]     $adv_arr [description]
	 * @return   [type]              [description]
	 */
	public function get_admin_count($adv_arr){
		return $this->db->where_in('ad_id',$adv_arr)
			->from('dealer')
			->count_all_results();
	}
	/**
	 * [get_all_dealer_list 获取所有经销商]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-07
	 * @param    [type]     $num    [description]
	 * @param    [type]     $offset [description]
	 * @return   [type]             [description]
	 */
	public function get_all_dealer_list($num,$offset){
		$data = $this->db->select('d.*,a.id as ad_id, a.name as advertiser_name, dis1.name as province, dis2.name as city')
							->from('dealer as d')
							->join('advertisers as a', 'a.id = d.ad_id', 'left')
							->join('area as dis1', 'dis1.id = province_id', 'left')
							->join('area as dis2', 'dis2.id = city_id', 'left')
							->limit($num, $num*$offset)
							->order_by('d.id', 'desc')
							->get()
							->result_array();							
		return $data;
	}
	/**
	 * [get_admin_dealer_list 获取管理员权限内车系]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-25
	 * @param    [type]     $num     [description]
	 * @param    [type]     $offset  [description]
	 * @param    [type]     $adv_arr [description]
	 * @return   [type]              [description]
	 */
	public function get_admin_dealer_list($num,$offset,$adv_arr){
		$data = $this->db->select('d.*,a.id as ad_id, a.name as advertiser_name, dis1.name as province, dis2.name as city')
							->from('dealer as d')
							->join('advertisers as a', 'a.id = d.ad_id', 'left')
							->join('area as dis1', 'dis1.id = province_id', 'left')
							->join('area as dis2', 'dis2.id = city_id', 'left')
							->where_in('d.ad_id',$adv_arr)
							->limit($num, $num*$offset)
							->order_by('d.id', 'desc')
							->get()
							->result_array();							
		return $data;
	}
	/**
	 * 获取广告主列表
	 * return array
	 */
	public function get_dealer_list($where = '1 = 1', $offset, $page_size)
	{
		$data = $this->db->select('d.*,a.id as ad_id, a.name as advertiser_name, dis1.name as province, dis2.name as city')
							->from('dealer as d')
							->join('advertisers as a', 'a.id = d.ad_id', 'left')
							->join('area as dis1', 'dis1.id = province_id', 'left')
							->join('area as dis2', 'dis2.id = city_id', 'left')
							->where($where)
							->limit($page_size, $offset)
							->order_by('d.id', 'desc')
							->get()
							->result_array();							
		return $data;
	}

	public function get_count($where = '1 = 1')
	{
		$count = $this->db->from('dealer')
						->where($where)
						->count_all_results();
		return $count;
	}

	/**
	 * 获取要编辑广告主的信息
	 */
	public function get_dealer_info($id)
	{
		if($id)
		{
			$data = $this->db->where(array('id' => $id))->get('dealer')->row_array();
			return $data;
		}
	}

	public function get_dealer_car_info($id)
	{
		if($id)
		{
			$data = $this->db->select('car_id')->where(array('dealer_id' => $id, 'status' => 1))->get('dealer_car')->result_array();
			return $data;
		}
	}
	/**
	 * [get_adv_dealer 获取广告主所有经销商]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-06-20
	 * @param    [type]     $ad_id [description]
	 * @return   [type]            [description]
	 */
	public function get_adv_dealer($ad_id){
		$query=$this->db->select('area.name as province_name,are.name as city_name,dea.*')
				->from($this->db->dbprefix('dealer').' as dea')
				->join($this->db->dbprefix('area').' as area','dea.province_id=area.id')
				->join($this->db->dbprefix('area').' as are','dea.city_id=are.id')
				->where(array('ad_id'=>$ad_id,'status'=>0))
				->get();
		return $query->result_array();
	}

	/**
	 * 获取省份
	 */
	public function get_province()
	{
		return $this->db->select('id, name, extra, suffix')->where(array('parent_id' => 0))->get('area')->result_array();
	}

	/**
	 * 获取城市地区
	 */
	public function get_city_county($where)
	{
		return $this->db->where($where)->get('area')->result_array();
	}

	/**
	 * 新增或更新经销商
	 */
	public function add_or_update($data)
	{
		if($data['dealer']['id'])  // 更新
		{
			$this->db->trans_begin(); // 开启事物

			$this->db->where(array('id' => $data['dealer']['id']))->update('dealer', $data['dealer']);

			$this->db->where(array('dealer_id' => $data['dealer']['id']))->update('dealer_car', array('status' => 0));

			if(is_array($data['dealer_car']['car_id']))
			{
				$arr = array();
				foreach ($data['dealer_car']['car_id'] as $val)
				{
					$result = $this->db->where(array('dealer_id' => $data['dealer']['id'], 'car_id' => $val))->get('dealer_car')->row_array();

					if($result)
					{	
						$this->db->where(array('dealer_id' => $data['dealer']['id'], 'car_id' => $val))->update('dealer_car', array('status' => 1));
					}
					else
					{
						$arr['car_id'] = $val;
						$arr['dealer_id'] = $data['dealer']['id'];
						$arr['status'] = 1;

						$this->db->insert('dealer_car', $arr);
					}
				}
			}

			if($this->db->trans_status() !== false)
			{
				$this->db->trans_commit();  // 事物提交
				return true;
				
			}
			else
			{
				$this->db->trans_rollback();  // 事物回滚
				return false;
			}						
		}
		else  // 新增
		{
			unset($data['dealer']['id']);

			$this->db->trans_begin(); // 开启事物

			$this->db->insert('dealer', $data['dealer']);
			$dealer_id = $this->db->insert_id();

			if(is_array($data['dealer_car']['car_id']))
			{
				$arr = array();
				foreach ($data['dealer_car']['car_id'] as $val)
				{
					$arr['car_id'] = $val;
					$arr['dealer_id'] = $dealer_id;
					$arr['status'] = 1;
					$this->db->insert('dealer_car', $arr);
				}
			}

			if($this->db->trans_status() === false)
			{
				$this->db->trans_rollback();  // 事物回滚
				return false;
			}
			else
			{
				$this->db->trans_commit();  // 事物提交
				return true;
			}
		}
	}
}
