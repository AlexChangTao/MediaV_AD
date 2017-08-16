<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 广告主控制器
 */

class C_dealer extends USER_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->model('m_dealer');
		$this->load->model('m_car');
		$this->load->model('m_Advertisers_model','Adv');
	}

	public function index()
	{
		$this->load->library('pagination');
		$this->load->library('pager');
		$ad_id = $this->uri->segment(3,0); // 广告主id
		if ($ad_id>0) {
			$where = array('ad_id' => $ad_id);
			$count = $this->m_dealer->get_count($where);
			$page_config['total_row'] =$count;
			$offset=intval($this->uri->segment(4,1));
	        $page_config['cur_page'] =  $offset;
	        $page_config['base_link_url'] =site_url('c_dealer/index/'.$ad_id.'/');
	        $page_config['page_size'] = 10;
	        $data['page'] = $this->pager->create($page_config);
	       	$data['list'] = $this->m_dealer->get_dealer_list($where,$offset-1,$page_config['page_size']);  
			$data['ad_id']=$ad_id;
			$this->_view('dealer/index.php', $data);
		}else{
			$offset=$this->uri->segment(3,1);
			$page_config['cur_page'] = $offset;
			$page_config['page_size'] = 10;
            if (self::is_supper()) {
            	$count=$this->m_dealer->get_all_count();
		       	$data['list'] = $this->m_dealer->get_all_dealer_list($page_config['page_size'],$offset-1); 
            }else{
            	$id = $this->session->userdata('aid');
	            $advstr=$this->Adv->get_ower_adv($id);
	            $adv_arr=explode(',', $advstr['advertisers']);
	            $count=$this->m_dealer->get_admin_count($adv_arr);
	            $data['list'] = $this->m_dealer->get_admin_dealer_list($page_config['page_size'],$offset-1,$adv_arr); 
            }
            $page_config['total_row'] =$count;
	        $page_config['base_link_url'] = site_url('c_dealer/index/');
	        $data['page'] = $this->pager->create($page_config);
            $data['ad_id']='';
			$this->_view('dealer/index.php', $data);
		}
		
	}
	public function show(){
		$ad_id = $this->uri->segment(3,0); // 广告主id
		$d_id = $this->uri->segment(4,0); //经销商id
		if ($d_id>0) {
			$data['province'] = $this->m_dealer->get_province(); // 获取省份
			$data['car'] = $this->m_car->get_car_name($ad_id); // 获取该广告主下的车系名称
			$data['dealer'] = $this->m_dealer->get_dealer_info($d_id);  // 获取经销商信息
			$data['dealer_car'] = $this->m_dealer->get_dealer_car_info($d_id);  // 获取经销商下的车系名称
			$data['ad_id']=$ad_id;
			$this->load->model('m_Area_model','Area');
			$province=$this->Area->get_name_by_id($data['dealer']['province_id']);
			$city=$this->Area->get_name_by_id($data['dealer']['city_id']);
			$county=$this->Area->get_name_by_id($data['dealer']['county_id']);
			$area=array('province'=>$province['name'],'city'=>$city['name'],'county'=>$county['name']);

			$data['area']=$area;
			#获取广告主的名字
			$ad_name=$this->Adv->get_advname_by_id($ad_id);
			$ad_code=$this->Adv->get_advcode_by_id($ad_id);
			$data['ad_title']=$ad_name['name'].'_'.$ad_code['code'];
			$data['d_id']=$d_id;
			$this->_view('dealer/show.html', $data);
		}
		
	}
	private function get_where()
	{
		$ad_id = $this->input->get('ad_id'); // 广告主id
		$where = array('ad_id' => $ad_id);
		return $where;
	}
	/**
	 * [fittle_city 将数组按照要求组合]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-06-20
	 * @param    [type]     $arr [description]
	 * @return   [type]          [description]
	 */
	public function fittle_city($arr){
		$res=array();
		foreach ($arr as $key => $value) {
			$res[$value['province_id'].'_'.$value['province_name']][$value['city_id'].'_'.$value['city_name']][]=$arr[$key];
		}
		return $res;
	}
	/**
	 * [arr_to_xml 数组转xml]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-06-20
	 * @param    [type]     $arr [description]
	 * @return   [type]          [description]
	 */
	public function arr_to_xml($arr){
		$str='<?xml version="'.'1.0'.'" encoding="'.'utf-8'.'"?>'."\n".'<list>'."\n";
		foreach ($arr as $provice_key => $provice_value) {
			$province=explode('_', $provice_key);
			$str.='<Province name="'.$province[1].'" id="'.$province[0].'">'."\n";
			foreach ($provice_value as $city_key => $city_val) {
				$city=explode('_', $city_key);
				$str.='<City name="'.$city[1].'" id="'.$city[0].'">';
				foreach ($city_val as $key => $value) {
					$str.="<Shop>";
					$str.="<Name>".$value['name']."</Name>\n";
					$str.="<Code>".$value['dealer_code']."</Code>\n";
					$str.="<Tel></Tel>\n<Address></Address>\n</Shop>\n";
				}
				$str.='</City>'."\n";
			}
			$str.='</Province>'."\n";
		}
		$str.='</list>';
		return $str;
	}
	/**
	 * [dealer_xml 更新经销商xml文件]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-06-20
	 * @return   [type]     [description]
	 */
	public function dealer_xml(){
		$ad_id = $this->uri->segment(3,0); // 广告主id
		if ($ad_id) {
			#获取所有经销商 
			$dealer=$this->m_dealer->get_adv_dealer($ad_id);
			#将经销商数据按照规则组合
			$arr=self::fittle_city($dealer);
			#数组组合为字符串
			$xml_str=self::arr_to_xml($arr);
			#生成路径

			$code=$this->Adv->get_code_by_id($ad_id);
			#创建目录
			$path='uploads/xml/'.$code['code'];
			if (!is_dir(FCPATH.$path)) {
				mkdir($path,0777,true);
			}
			#文件写入
			$file=$path.'/all_dealer.xml';
			$f=fopen(FCPATH.$file, 'w');
			fwrite($f,$xml_str);
			fclose($f);
			redirect(base_url($file));
		}
	}
	/**
	 * 新增或编辑经销商
	 */
	public function add_or_update()
	{

		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', '经销商名称', 'required');
		$this->form_validation->set_rules('dealer_code', '经销商代码', 'required');
		$this->form_validation->set_rules('tel', '电话', 'required');
		$this->form_validation->set_rules('longitude', '经度', 'is_numeric');
		$this->form_validation->set_rules('latitude', '纬度', 'is_numeric');
		if($this->form_validation->run() == FALSE)
		{
			showmessage('参数错误', 'back', 1);
		}
		else
		{
			$data = $this->get_dealer_form_data();  // 获取经销商表单数据
			$bool = $this->m_dealer->add_or_update($data);
			if($bool)
			{
				redirect("c_dealer/index/" . $data['dealer']['ad_id']);
			}
			else
			{
				redirect("c_dealer/add/" . $data['dealer']['ad_id']);
			}
		}		
	}

	/**
	 * [add 经销商添加]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-19
	 */
	public function add()
	{
		$ad_id = $this->uri->segment(3,0); // 广告主id
		if ($ad_id>0) {
			$this->load->helper('form');
			self::set_rule();
			if($this->form_validation->run() == FALSE){
				$data['province'] = $this->m_dealer->get_province(); // 获取省份
				$data['car'] = $this->m_car->get_car_name($ad_id); // 获取该广告主下的车系名称
				$data['ad_id']=$ad_id;
				$ad_name=$this->Adv->get_advname_by_id($ad_id);
				$ad_code=$this->Adv->get_advcode_by_id($ad_id);
				$data['ad_title']=$ad_name['name'].'_'.$ad_code['code'];
				$this->_view('dealer/form.php', $data);
			}else{
				$data = $this->get_dealer_form_data();  // 获取经销商表单数据
				$bool = $this->m_dealer->add_or_update($data);
				if($bool){
					$this->_log_message('Add','Dealer',json_encode($data));
					$this->_success('添加成功');
				}else{
					$this->_error('添加失败');
				}
			}
		}
		
	}
	public function set_rule(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', '经销商名称', 'required');
		$this->form_validation->set_rules('dealer_code', '经销商代码', 'required');
		$this->form_validation->set_rules('tel', '电话', 'required');
		$this->form_validation->set_rules('longitude', '经度', 'is_numeric');
		$this->form_validation->set_rules('latitude', '纬度', 'is_numeric');
	}
	/**
	 * 显示编辑表单的模态框
	 */
	public function edit()
	{
		$ad_id = $this->uri->segment(3,0); // 广告主id
		$d_id = $this->uri->segment(4,0); //经销商id
		if ($d_id>0) {
			$this->load->helper('form');
			/*$this->load->library('form_validation');
			$this->form_validation->set_rules('name', '经销商名称', 'required');*/
			self::set_rule();
			if($this->form_validation->run() === false){
				$data['province'] = $this->m_dealer->get_province(); // 获取省份
				$data['car'] = $this->m_car->get_car_name($ad_id); // 获取该广告主下的车系名称
				$data['dealer'] = $this->m_dealer->get_dealer_info($d_id);  // 获取经销商信息
				$data['dealer_car'] = $this->m_dealer->get_dealer_car_info($d_id);  // 获取经销商下的车系名称
				$data['ad_id']=$ad_id;
				$this->load->model('m_Area_model','Area');
				$province=$this->Area->get_name_by_id($data['dealer']['province_id']);
				$city=$this->Area->get_name_by_id($data['dealer']['city_id']);
				$county=$this->Area->get_name_by_id($data['dealer']['county_id']);
				$area=array('province'=>$province['name'],'city'=>$city['name'],'county'=>$county['name']);

				$data['area']=$area;
				#获取广告主的名字
				$ad_name=$this->Adv->get_advname_by_id($ad_id);
				$ad_code=$this->Adv->get_advcode_by_id($ad_id);
				$data['ad_title']=$ad_name['name'].'_'.$ad_code['code'];
				$data['d_id']=$d_id;
				$this->_view('dealer/edit.html', $data);
			}else{

				$data = $this->get_dealer_form_data();  // 获取经销商表单数据
				$bool = $this->m_dealer->add_or_update($data);
				if($bool){
					$this->_log_message('Update','Dealer',json_encode($data));
					$this->_success('修改成功',site_url('c_dealer/edit/').$ad_id.'/'.$d_id);
				}else{
					$this->_error('修改失败',site_url('c_dealer/edit/').$ad_id.'/'.$d_id);
				}
			}
			
		}else{
			$this->_error('请求错误',site_url(''));
		}
		
	}


	/**
	 * 获取城市地区 
	 */
	public function get_region()
	{
		$map['parent_id'] = $this->input->get('parent_id');
		#$map['type'] = $this->input->get('type');

		$list = $this->m_dealer->get_city_county($map);
		echo json_encode($list);
	}

	/**
	 * 获取经销商表单数据
	 */
	private function get_dealer_form_data()
	{
		$data = array(
			'dealer' => array(
				'id' => intval($this->input->post('d_id')),
				'ad_id' => intval($this->input->post('ad_id')),
				'name' => $this->input->post('name'),
				'dealer_code' => $this->input->post('dealer_code'),
				'tel' => $this->input->post('tel'),
				'province_id' => $this->input->post('province_id'),
				'city_id' => $this->input->post('city_id'),
				'county_id' => $this->input->post('county_id'),
				'address' => $this->input->post('address'),
				'email' => $this->input->post('email'),
				'longitude' => $this->input->post('longitude'),
				'latitude' => $this->input->post('latitude'),
				'dealer_type' => $this->input->post('dealer_type'),
				'status' => $this->input->post('status'),
				'remark' => $this->input->post('remark')
			),

			'dealer_car' => array(
				'car_id' => $this->input->post('car_id'),
			)
		);
		return $data;
	}
}
