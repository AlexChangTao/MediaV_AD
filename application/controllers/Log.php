<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Log extends User_Controller {

   public function __construct()
   {
      parent::__construct();
   }

   public function index(){
      if(IS_AJAX){
         $search = $this->input->get('search');
         $sort   = $this->input->get('sort')?$this->input->get('sort'):'id';
         $order  = $this->input->get('order');
         $offset = $this->input->get('offset');
         $limit  = $this->input->get('limit');
         $rs     = $this->db->select('a.*,b.account')->from('log a')->join('admin b','a.user_id=b.id','left')->order_by("$sort $order")->limit($limit,$offset)->get()->result_array();
         $total  = $this->db->count_all_results('log');
         $arr    = array('total'=>$total,'rows'=>$rs);
         exit(json_encode($arr));
      }
      $this->_view('admin/log_list.html');
   }

   public function log_del(){
      $id = $_POST['id'];
      if (!empty($id) && is_array($id)){
         $this->db->where_in('id',$id)->delete('log');
         if($this->db->affected_rows()){
            exit(json_encode(1));
         } else {
            exit(json_encode(0));
         }
      }
      exit(json_encode(0));
   }
}