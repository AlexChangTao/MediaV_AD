<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Util extends User_Controller {

	private $mBackupSqlFiles;

	public function __construct(){
		parent :: __construct();
        $this->load->helper('common');
        $sql_path = FCPATH.'data/sql';
        $this->load->helper('file');
        $r = array_reverse(get_dir_file_info($sql_path.'/backup'));
        $this->mBackupSqlFiles = $r;
	}

	public function index()
	{
		redirect(base_url('util/list_db'));
	}

	public function optimize($table_name)
	{
		$this->load->dbutil();
		if ($this->dbutil->optimize_table($table_name)){
			if(IS_AJAX) exit(json_encode(array('success'=>true,'msg'=>"优化表成功")));
		    $this->_success('优化表成功');
		} else {
			if(IS_AJAX) exit(json_encode(array('success'=>false,'msg'=>"优化表失败")));
			$this->_success('优化表失败');
		}
	}

	public function repair($table_name)
	{
		$this->load->dbutil();
		if ($this->dbutil->repair_table($table_name)){
			if(IS_AJAX) exit(json_encode(array('success'=>true,'msg'=>"修复表成功")));
		    $this->_success('修复表成功');
		} else {
			if(IS_AJAX) exit(json_encode(array('success'=>false,'msg'=>"修复表失败")));
			$this->_success('修复表失败');
		}
	}

	public function list_db()
	{
		$data['title'] = '数据库管理';

		$sql = "SHOW TABLE STATUS";
		$r = $this->db->query($sql)->result_array();
		$data['tables'] = $r;
		$data['backup_sql_files'] = $this->mBackupSqlFiles;
		$this->_view('util/list_db.php',$data);
	}

	public function parse()
	{
		$sql = $this->input->post('sql');
		if($sql){
			$this->load->library('sql');
			$arr = $this->sql->parseSql($sql);
			$arr = array('code'=>0,'result'=>$arr);
			exit($this->_encode_json($arr));
		} else {
			$arr = array('code'=>0,'result'=>'');
			exit($this->_encode_json($arr));
		}
	}

	public function import()
	{
		$sql = $this->input->post('sql_str');
		if($sql){
			$result = array();
			$this->db->trans_start();
			foreach ($sql as $key => $value) {
				$result[] = $this->db->simple_query($value);
			}
			$this->db->trans_complete();
			$arr = array('code'=>0,'result'=>$result);
			exit($this->_encode_json($arr));
		} else {
			$arr = array('code'=>0,'result'=>'');
			exit($this->_encode_json($arr));
		}
	}

	// Backup current database version
	public function backup_db()
	{
		$this->load->dbutil();
		$this->load->helper('file');

		$prefs = array('format' => 'txt');
		$backup = $this->dbutil->backup($prefs);
		$file_path_1 = FCPATH.'data/sql/backup/'.date('Y-m-d_H-i-s').'.sql';
		$result_1 = write_file($file_path_1, $backup);

		// overwrite latest.sql
		$save_latest = $this->input->get('save_latest');
		if ( !empty($save_latest) )
		{
			$file_path_2 = FCPATH.'data/sql/backup/latest.sql';
			$result_2 = write_file($file_path_2, $backup);
		}

		if($result_1) {
			if(IS_AJAX){
				exit(json_encode(array('success'=>true,'msg'=>'备份成功')));
			} else {
				$this->_success('备份成功');
			}
		} else {
			if(IS_AJAX){
				exit(json_encode(array('success'=>false,'msg'=>'备份失败，请联系管理员检查目录权限。')));
			} else {
				$this->_success('备份失败，请联系管理员检查目录权限。');
			}
		}
	}

	// Restore specific version of database
	public function restore_db($file)
	{
		$pass = $this->input->post('pass').'';
		if($pass != '123'){
			if(IS_AJAX)	exit(json_encode(array('success'=>false,'msg'=>'口令错误')));
			$this->_success('口令错误');
		}

		$path = '';
		if ($file=='latest'){
			$path = FCPATH.'data/sql/backup/latest.sql';
		} else {
			foreach ($this->mBackupSqlFiles as $key => $value) {
				if ($file==$key){
					$path = FCPATH.'data/sql/backup/'.$file;
					break;
				}
			}
		}

		// proceed to execute SQL queries
		if ( !empty($path) && file_exists($path) )
		{
			$this->load->library('sql');
			$result = array();
			$arr = $this->sql->getSqlFromFile($path);
			$this->db->trans_start();
			foreach ($arr as $key => $value) {
				$result[$key]['sql'] = $value;
				$result[$key]['status'] = $this->db->simple_query($value);
			}
			$this->db->trans_complete();

			if(IS_AJAX)	exit(json_encode(array('success'=>true,'msg'=>'还原成功','data'=>$result)));
			$this->_success('还原成功');
		} else {

			if(IS_AJAX)	exit(json_encode(array('success'=>false,'msg'=>'还原失败','data'=>$result)));
			$this->_success('还原失败');
		}
	}

	// Remove specific database version
	public function remove_db($file)
	{
		foreach ($this->mBackupSqlFiles as $key => $value) {
			if ($file==$key){
				$path = FCPATH.'data/sql/backup/'.$file;

				$this->load->helper('file');
				unlink($path);
				$result = delete_files($path);

				if(IS_AJAX){
					exit(json_encode(array('success'=>true,'msg'=>'删除成功')));
				} else {
					$this->_error('删除成功');
				}
				break;
			}
		}
		if(IS_AJAX){
			exit(json_encode(array('success'=>false,'msg'=>'删除失败')));
		} else {
			$this->_error('删除失败');
		}
	}
}