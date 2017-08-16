<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

/**
 * @ClassName  媒体模型
 * @author AlexChangTao  1692576541@qq.com
 * @Date 2017-06-10 10:00:00
 */
class m_media extends CI_model {
	
	private $_table = 'ad_media';
	
	public function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	
	public function getMediaListCount($keyword,$type){
		$sql = "SELECT count(*) as count FROM ad_media WHERE 1=1";
		$parames = array();
		if($keyword){
			$sql.=" and (media_name = ? or identifier = ?) ";
			array_push($parames, $keyword);
			array_push($parames, $keyword);
		}
		if($type && !$keyword){
			$sql.=" and status = ?";
			array_push($parames, $type);
		}
		$query = $this->db->query($sql,$parames);
		return $query->row()->count;
	}
	
	public function getMediaList($num = 10, $offset = 0, $keyword,$type, $latest){
		$sql = "SELECT id, identifier, media_name, status, add_time, remark from ad_media WHERE 1=1 ";
		$parames = array();
		if($keyword != ""){
			$sql.=" and (media_name like ? or identifier like ?) "; //模糊查找
			array_push($parames, "%".$keyword."%");
			array_push($parames, "%".$keyword."%");
		}
	
		if($offset == ""){
			$offset = 0;
		}
		$offset = $offset+0;
		
		if ($latest == true){
			$sql .= " order by id desc ";
		}
		if($num == false){
			$sql .= " ";
		}else {
			$sql .= " LIMIT ?,?";
			array_push($parames, $offset);
			array_push($parames, $num);
		}
	
		$query = $this->db->query($sql, $parames);
		return $query->result();
	}
	
	public function getOneMedia($id){
		if(empty($id)){
			return false;
		}else{
			$parames = array();
			$sql = "select * from ad_media where id=?";
			array_push($parames, $id);
			$query = $this->db->query($sql, $parames);
			return $query->row();
		}
	}
	
	public function del_media($id){
		if(empty($id)){
			return false;
		}else{
			$parames = array();
			$sql = "delete from ad_media where id=? ";
		    array_push($parames, $id);
		    $query = $this->db->query($sql, $parames);
		    return $query;
		}
		return false;
	}
	
	/**
	 * 添加媒体
	 */
	public function add_media($media_name, $identifier, $status, $remark){
		$this->db->query("insert into ad_media(media_name, identifier, status, add_time, remark) values(?,?,?,?,?)",			array($media_name, $identifier, $status, date('Y-m-d H:m:s'), $remark));
		return $this->db->affected_rows()>0;
	}
	
	/**
	 * 修改媒体
	 */
	public function alter_media($media_name, $identifier, $status, $id, $remark){
		$this->db->query("update ad_media set media_name=?, identifier=?, status=?, remark=? where id=?",array($media_name, $identifier, $status, $remark, $id));
		return $this->db->affected_rows()>0;
	}
}