<?php
if (! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('prc');

/**
 * 用户模型
 */
class M_Admin extends CI_Model
{
	/**
	 * 查询广告主相关信息
	 * @param string $where
	 * @param int $offset
	 * @param int $page_size
	 */
	public function get_advertisers($where = '1 = 1', $offset = null, $page_size = null)
	{
		$form = $this->db->select()
		->where($where)
		->order_by('id', 'desc')
		->limit($page_size, $offset)
		->get('advertisers')
		->result_array();
		
		return $form;
	}
	
	public function get_auth_group($where = '1 = 1')
	{
		$form = $this->db->select()
		->where($where)
		->order_by('id','desc')
		->get('auth_group')
		->result_array();
		
		return $form;
	}
	
	/**
	 * 新增用户或更新用户
	 */
	public function add_or_update($form)
	{
		if ($form['id'])
		{
			$query = $this->db->where(array('id'=>$form['id']))
			->update('admin', $form);
			
			return $query;
		}
		else
		{
			unset($form['id']);
			$query2 = $this->db->insert('admin', $form);
			return $query2;
		}
	}
	
	/**
	 * 保存审批的状态
	 */
	public function approve1($form)
	{
		if($form['id'])
		{
			$query = $this->db->where(array('id'=>$form['id']))
			->update('deduction', $form);
			if($query)
			{
				addlog('更新保存审核成功！审核人: ' . $form['team_leader']);
				redirect('admin/C_finance/index1');
			}
			else
			{
				showmessage('更新审核失败', 'back', 1);
			}
		}
		else
		{
			unset($form['id']);
			$query2 = $this->db->insert('deduction', $form);
	
			if($query2)
			{
				addlog('新增审核保存成功！ 审核人:' . $form['team_leader']);
				redirect('admin/C_finance/index1');
			}
			else
			{
				showmessage('新增审核失败', 'back', 1);
			}
		}
	}
	
	/**
	 * 删除
	 */
	public function del($id)
	{
		if($id)
		{
			$bool = $this->db->where(array('id' => $id))->update('media', array('is_del' => 1));
	
			if($bool)
			{
				addlog('删除媒体名称, ID: ' . $id);
				return true;
			}
			else
			{
				showmessage('参数错误', 'back', 1);
				return false;
			}
		}
	}
	
}