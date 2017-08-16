<?php
class Pager {
	//链接
	private $base_link_url = '/';
	//总行数
	private $total_row = 0;
	//当前页码
	private $cur_page = 0;
	//每一页显示的条目数
	private $page_size = 2;
	//总页数
	private $page_row = 0;
	//CI
	private $CI;
	//记录
	private $max_num = 0;
	private $item_num = 5;
	private $flag = 0;

	function __construct()
	{
		$this->CI =& get_instance();
		$this->CI->load->helper('array');
	}

	public function init(array $config = array())
	{
		foreach ($config as $key => $val)
		{
			if (property_exists($this, $key))
			{
				$this->$key = $val;
			}
		}
		$this->set_page_row();
		return $this;
	}

	private function set_page_row()
	{
		$this->page_row = $this->total_row%$this->page_size ? intval($this->total_row/$this->page_size) + 1 : intval($this->total_row/$this->page_size);
		$this->max_num = $this->page_row > $this->item_num ? $this->item_num : $this->page_row;
		$this->flag = $this->page_row > $this->item_num ? 1 : 0;
	}

	public function create(array $config = array())
	{
		$this->init($config);
		return '<ul class="pagination">'.$this->set_style().'</ul>';
	}

	private function set_style()
	{
		$pre = '<li><a href="'.$this->base_link_url.'1">首页</a></li><li class=""><a href="'.$this->base_link_url.($this->cur_page-1).'">«上一页</a></li>';
		$dis_pre = '<li class="disabled"><a href="javascript:void(0);">首页</a></li><li class="disabled"><a href="javascript:void(0);">«上一页</a></li>';
		$next = '<li><a href="'.$this->base_link_url.($this->cur_page + 1).'">下一页»</a></li><li><a href="'.$this->base_link_url.$this->page_row.'">末页</a></li>';
		$dis_next = '<li class="disabled"><a href="javascript:void(0);">下一页»</a></li><li class="disabled"><a href="javascript:void(0);">末页</a></li>';
		$sl = '<li class="disabled"><a href="javascript:void(0);">...</a></li>';

		$li = $pre;

		if ($this->cur_page == 1) {
			$li = $dis_pre;
		}
		
		if ($this->cur_page >= $this->max_num - 1 && $this->flag > 0) {
			$li .= $sl;
		}

		$imin = 1;
		$imax = $this->max_num;
		if ($this->page_row > $this->max_num) {
			$imax = ($this->cur_page + 2) > $this->page_row ? $this->page_row : $this->cur_page + 2;
			$imin = ($this->cur_page - 2) <= 0 ? 1 : $this->cur_page - 2;
		}
		for ($i=$imin; $i <= $imax; $i++) {
			if($i == $this->cur_page) {
				$li .= '<li class="active"><a href="javascript:void(0);" class="disabled">'.$this->cur_page.'<span class="sr-only">(current)</span></a></li>';
			} else {
				$li .= '<li><a href="'.$this->base_link_url.$i.'">'.$i.'</a></li>';
			}
		}
		if ($this->cur_page + 2 < $this->page_row && $this->flag > 0) {
			$li .= $sl;
		}
		if ($this->cur_page == $this->page_row) {
			$li .= $dis_next;
		} else {
			$li .= $next;
		}
		$li.= '<li class="disabled"><span>共'.$this->total_row.'条记录</span></li>';
		//没有记录或页数为1 不显示
		if($this->total_row = 0 || $this->page_row == 1) {
			// $li = '';
		}
		return $li;
	}
}