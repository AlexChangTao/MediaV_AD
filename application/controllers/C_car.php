<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * 车系控制器
 */

class C_car extends USER_Controller 
{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));
		$this->load->model('m_advertiser');
		$this->load->model('m_car');
		$this->load->model('m_Advertisers_model','Adv');
	}
	/**
	 * [car_xml 更新车系xml]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-14
	 * @return   [type]     [description]
	 */
	public function car_xml(){
		$ad_id=$this->input->get('ad_id'); // 广告主id
		$carlist=$this->m_car->get_adv_car($ad_id);
		$xml_str=self::cararr_to_xml($carlist);
		#生成路径
		$this->load->model('m_Advertisers_model','Adv');
		$code=$this->Adv->get_code_by_id($ad_id);
		#创建目录
		$path='uploads/xml/'.$code['code'];
		if (!is_dir(FCPATH.$path)) {
			mkdir($path,0777,true);
		}
		#文件写入
		$file=$path.'/all_car.xml';
		$f=fopen(FCPATH.$file, 'w');
		fwrite($f,$xml_str);
		fclose($f);
		redirect(base_url($file));

	}
	/**
	 * [cararr_to_xml 车系数组构造xml]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-07-19
	 * @param    [type]     $carlist [description]
	 * @return   [type]              [description]
	 */
	public function cararr_to_xml($carlist){
		$str='<?xml version="'.'1.0'.'" encoding="'.'utf-8'.'"?>'."\n".'<list>'."\n";
		foreach ($carlist as $key => $value) {
			$str.='<Series name="'.$value['name'].'" Code="'.$value['car_code'].'" Pic="">'."\n";
			$str.="<Colors></Colors>\n<Models>\n<Model>\n<Name>默认</Name>\n<Code>NONE</Code>\n</Model>\n</Models>\n</Series>";

		}
		$str.='</list>';
		return $str;
	}
	public function index()
	{
		$ad_id = $this->input->get('ad_id'); // 广告主id
		$this->load->library('pagination');
		$this->load->library('pager');
		if ($ad_id) {
			$data['ad_id']=$ad_id;
			$where = array('ad_id' => $ad_id);
			$page_config['total_row'] = $this->m_car->get_count($where);
	        $offset= $this->input->get('p')?$this->input->get('p'):1;
	        $page_config['cur_page'] = $offset;
	        $page_config['base_link_url'] = site_url('C_car/index?ad_id='.$ad_id.'&p=');
	        $page_config['page_size'] = 10;
	        $data['page'] = $this->pager->create($page_config);
	       	$data['list'] = $this->m_car->get_car_list($where, $offset-1, $page_config['page_size']); 
		}else{
			$offset=$this->uri->segment(3,1);
			$page_config['page_size'] = 10;
			$page_config['cur_page'] = $offset;
			if (self::is_supper()) {
				$page_config['total_row'] = $this->m_car->get_all_count();
				$data['list'] = $this->m_car->get_all_car_page($page_config['page_size'],$offset-1);  
			}else{
				$id = $this->session->userdata('aid');
	            $advstr=$this->Adv->get_ower_adv($id);
	            $adv_arr=explode(',', $advstr['advertisers']);
	            $page_config['total_row'] = $this->m_car->get_admin_car_count($adv_arr);
	            $data['list'] = $this->m_car->get_admin_car_page($page_config['page_size'],$offset-1,$adv_arr);  
			}
			$data['ad_id'] = '';
	        
	        
	        
	        $page_config['base_link_url'] = site_url('C_car/index/');
	        
	        $data['page'] = $this->pager->create($page_config);
	       
		}
		$this->_view('car/index.html', $data);

	}

	private function get_where($gid)
	{
		$where = array('ad_id' => $gid);
		return $where;
	}
	/**
	 * [show 查看页面]
	 * @author Greedywolf 1154505909@qq.com
	 * @DateTime 2017-08-02
	 * @return   [type]     [description]
	 */
	public function show(){
		$ad_id = $this->input->get('ad_id'); // 广告主id
		$c_id = $this->input->get('c_id'); // 车系id
		$data['advertiser'] = $this->m_advertiser->get_advertiser_info($ad_id); // 获取该广告主名称
		$data['car'] = $this->m_car->get_car_info($c_id);// 获取要编辑的车系信息
		$data['car_id']=$c_id;
		$data['ad_id']=$ad_id;
		$this->_view('car/show.html', $data);
	}
	/**
	 * 显示新增表单
	 */
	public function add()
	{
		$ad_id = $this->input->get('ad_id'); // 广告主id
		//加载验证类
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', '车系名称', 'required');
		$this->form_validation->set_rules('car_code', '车系代码', 'required|min_length[4]|max_length[11]');
		if ($this->form_validation->run()===false) {
			$data['advertiser'] = $this->m_advertiser->get_advertiser_info($ad_id); // 获取该广告主名称
			$data['ad_id']=$ad_id;
			$this->_view('car/form.html', $data);
		}else{
			setcookie('pic_url', '', time()-100, '/');
			$data = $this->get_form_data();  // 获取表单数据
			$car_check=self::check_carcode($data['car_code']);
			if ($car_check) {
				$bool = $this->m_car->add_or_update($data);
				if($bool)
					{
						$this->_log_message('Add','Car',json_encode($data));
						redirect("c_car/index?ad_id=" . $data['ad_id']);
					}
					else
					{
						$this->_error('参数错误');
					}
			}else{
				$this->_error('车系代码重复');
			}
		}
		
	}

	/**
	 * 显示编辑表单
	 */
	public function edit()
	{
		$ad_id = $this->input->get('ad_id'); // 广告主id
		$c_id = $this->input->get('c_id'); // 车系id
		$this->load->library('form_validation');
		$this->form_validation->set_rules('name', '车系名称', 'required');
		$this->form_validation->set_rules('car_code', '车系代码', 'required|min_length[4]|max_length[11]');
		if ($this->form_validation->run()===false) {
			$data['advertiser'] = $this->m_advertiser->get_advertiser_info($ad_id); // 获取该广告主名称
			$data['car'] = $this->m_car->get_car_info($c_id);// 获取要编辑的车系信息
			$data['car_id']=$c_id;
			$data['ad_id']=$ad_id;
			$this->_view('car/edit.html', $data);
		}else{
			setcookie('pic_url', '', time()-100, '/');
			$data = $this->get_form_data();  // 获取表单数据
			$code_check=self::check_carcode($data['car_code'],$data['id']);

			if ($code_check==true) {
				#写入修改
				$bool = $this->m_car->add_or_update($data);

				if($bool){
					$this->_log_message('Update','Car',json_encode($data));
					$this->_success('修改成功');
				}else{
					$this->_error('参数错误');
				}
			}else{
				#报错
				$this->_error('车系代码重复');
			}

		}
		
	}
	public function check_carcode($code,$car_id=0){
		$id=$this->m_car->get_carid_by_code($code);
		if ($car_id>0) {
			#修改校验
			if ($id['id']==$car_id||empty($id['id'])) {
				return true;
			}else{
				return false;
			}
		}else{
			#增加校验
			if ($id['id']!=false) {
				return false;
			}else{
				return true;
			}
		}
	}
	/**
	 * 新增或编辑车系
	 */
	public function add_or_update()
	{
		

        //加载验证类
		$this->load->library('form_validation');

		$this->form_validation->set_rules('name', '车系名称', 'required');
		$this->form_validation->set_rules('car_code', '车系代码', 'required|min_length[4]|max_length[11]');

		if($this->form_validation->run() == FALSE)
		{
			 
		}
		else
		{
			$data = $this->get_form_data();  // 获取表单数据
			$car_check=self::check_carcode($data['car_code']);
			if ($car_check) {
				$bool = $this->m_car->add_or_update($data);
				if($bool)
					{
						redirect("c_car/index?ad_id=" . $data['ad_id']);
					}
					else
					{
						$this->_error('参数错误');
					}
			}else{
				$this->_error('车系代码重复');
			}
			
			
		}		
	}

	/**
	 * 获取表单数据
	 */
	private function get_form_data()
	{
		$data = array(
			'id' => intval($this->input->post('c_id')),
			'ad_id' => intval($this->input->post('ad_id')),
			'name' => $this->input->post('name'),
			'car_code' => $this->input->post('car_code'),
			'is_use' => $this->input->post('is_use'),
			'car_img' => $this->input->post('car_img'),
			'car_img_thumb' => $this->input->post('car_img_thumb')
		);

		return $data;
	}

	/**
	 * 上传车系图片
	 */
	public function upload_car_img()
	{
		$data = $this->_upload('car_img', 100);
		echo json_encode($data);
	}

	/**
	 * 生成缩略图
	 * @param $path 原图的本地路径
	 * @return null 创建一个原图_thumb.扩展名的文件
	 */
	private function _deal_thumb($path, $mini = 100)
	{
		$config['image_library'] = 'gd2';
        $config['source_image'] = $path;
        $config['create_thumb'] = TRUE;
        //生成的缩略图将在保持纵横比例 在宽度和高度上接近所设定的width和height
        $config['maintain_ratio'] = TRUE;
        $config['width'] = $mini;
        $config['height'] = $mini;

        $this->load->library('image_lib', $config);
        $this->image_lib->resize();
        $this->image_lib->clear();
	}

	/**
	 * 图片上传处理
	 * $folder 文件夹名称，$mini 缩略图的宽高
	 */
	private function  _upload($folder, $mini)
	{
		$config['upload_path'] = './uploads/'. $folder .'/'.date("Y-m-d");   //注意：此路劲是相对于CI框架中的根目录下的目录

		if(!file_exists('./uploads/'. $folder .'/'.date("Y-m-d")))
		{
			mkdir('./uploads/'. $folder .'/'.date("Y-m-d"));//原图路径
			chmod('./uploads/'. $folder .'/'.date("Y-m-d"),0777);
		}

		$config['allowed_types'] = 'gif|jpg|png';    //设置上传的图片格式
		$config['max_size'] = '200';              //设置上传图片的文件最大值
		$config['max_width']  = '1200';            //设置图片的最大宽度
		$config['max_height']  = '1200';
		$config['encrypt_name'] = TRUE;
		$this->load->library('upload', $config);   //加载CI中的图片上传类，并递交设置的各参数值

		if ($this->upload->do_upload('file'))  // 文件对象名称:file webloader:文本域的name默认是file
		{  
		    $info = $this->upload->data();     //此函数是返回图片上传成功后的信息

		    $this->_deal_thumb($info['full_path'], $mini);

		    $data = explode('.', $info['file_name']);
		    $new_name = $data[0] . '_thumb.' . $data[1];

		    $thumb_name = '';
		    if(file_exists($info['file_path'] . $new_name))
		    {
		    	$thumb_name = $new_name;
		    }

		    // 更换图片 删除之前在服务器上的图片
		    $timeout = time() + 3600 * 24 * 1;
		    setcookie('pic_url', './uploads/'. $folder .'/'.date("Y-m-d")."/" . ($thumb_name == '' ? $info['file_name'] : $thumb_name), $timeout, '/');
		    if(isset($_COOKIE['pic_url']) && $_COOKIE['pic_url'] != null)
		    {
		    	@unlink($_COOKIE['pic_url']);
		    }

		    return array(
		    	'code' => 1,
		    	'path' => array(
		    		'car_img' => date("Y-m-d") . "/" . $info['file_name'],
			    	'car_img_thumb' => date("Y-m-d") . "/" . ($thumb_name == '' ? $info['file_name'] : $thumb_name)
		    	)
		    );
		}
		else
		{
			return array('code'=>'0','msg'=>$this->upload->display_errors());
		}
	}
}
