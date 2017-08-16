<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

class Api extends User_Controller {

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
         $rs     = $this->db->select('a.*,b.name as advertiser_name')->from('api a')->join('advertisers b','b.id=a.advertiser_id','left')->order_by("$sort $order")->limit($limit,$offset)->get()->result_array();
         $total  = $this->db->count_all_results('api');
         $arr    = array('total'=>$total,'rows'=>$rs);
         exit(json_encode($arr));
      }
      $this->_view('api/api_list.html');
   }

   public function api_add(){
      if(!$this->input->post()){
         $result['advertiser'] = $this->db->get('advertisers')->result_array();
         $this->_view('api/api_add.html',$result);
      }else{
         $name          = $this->input->get_post('name');
         $advertiser_id = $this->input->get_post('advertiser_id');
         $type_id       = $this->input->get_post('type_id');
         $address       = $this->input->get_post('address');
         $mark          = $this->input->get_post('mark');
         $control_name  = $this->input->get_post('control_name');
         $status        = $this->input->get_post('status');
         if(!$name || !$address || !$control_name) {
            $this->_error('添加失败，参数不全');
         }
         $time = time();
         $arr = array(
            'name'          =>$name,
            'advertiser_id' =>$advertiser_id,
            'type_id'       =>$type_id,
            'address'       =>$address,
            'mark'          =>$mark,
            'control_name'  =>$control_name,
            'status'        =>$status,
            'add_time'      =>$time,
            'update_time'   =>$time
            );
         $rs = $this->db->insert('api',$arr);
         if($rs){
            $this->_success('添加成功');
         }else{
            $this->_error('添加失败');
         }
      }
   }

   public function api_edit(){
      if(!$this->input->post()){
         $id                 = $this->input->get_post('id');
         $result             = $this->db->where(array('id'=>$id))->get('api')->row_array();
         $data['advertiser'] = $this->db->get('advertisers')->result_array();
         $data['result']     = $result;
         $data['title']      = '管理员管理';
         $this->_view('api/api_edit.html',$data);
      }else{
         $id            = $this->input->get_post('id');
         $name          = $this->input->get_post('name');
         $advertiser_id = $this->input->get_post('advertiser_id');
         $type_id       = $this->input->get_post('type_id');
         $address       = $this->input->get_post('address');
         $control_name  = $this->input->get_post('control_name');
         $mark          = $this->input->get_post('mark');
         $status        = $this->input->get_post('status');
         if(!$id || !$address || !$control_name) {
            $this->_error('添加失败，参数不全');
         }
         $arr = array(
            'name'          =>$name,
            'advertiser_id' =>$advertiser_id,
            'type_id'       =>$type_id,
            'address'       =>$address,
            'control_name'  =>$control_name,
            'mark'          =>$mark,
            'status'        =>$status,
            'update_time'   =>time()
            );
         $rs = $this->db->where(array('id'=>$id))->update('api',$arr);
         if($rs){
            $this->_success('修改成功');
         }else{
            $this->_error('修改失败');
         }
      }
   }

   public function api_del(){
      $id = $_POST['id'];
      if (!empty($id) && is_array($id)){
         $this->db->where_in('id',$id)->delete('api');
         if($this->db->affected_rows()){
            exit(json_encode(1));
         } else {
            exit(json_encode(0));
         }
      }
      exit(json_encode(0));
   }
}