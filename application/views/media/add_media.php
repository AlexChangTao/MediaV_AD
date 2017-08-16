
<script type="text/javascript" src="<?= base_url('web/js/jquery.validator.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('web/js/zh_CN.js'); ?>"></script>
<script type="text/javascript" src="<?= base_url('web/js/jquery.form.js'); ?>"></script>
<script language="javascript" type="text/javascript">
	function alter_opus(){
		$('#form1').ajaxSubmit({
			dataType : "json",
			success : function(result){
				if(result.code == 0){		//提交成功
					alert('添加媒体成功');
				}else{
					alert(result.msg);	
				}
				$('#loadTips').hide();
			},
			error: function(xhr){
				$('#loadTips').hide();
			}
			
		});
	}
</script>
<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
      <h2>媒体管理</h2>
      <div class="breadcrumb">
        <li>媒体管理</li>
        <li>媒体添加</li>
      </div>
  </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
    <form id="form1" method="post" action="<?=base_url("C_media/add_media")?>" class="form">
        <input type="hidden" name="id" value="">
        <div class="form-group">
            <label>媒体名称</label>
            <input type="text" name="media_name" value="" placeholder="媒体名称" required="" class="form-control">
        </div>
        <div class="form-group">
            <label>唯一码</label>
            <input type="text" name="identifier" placeholder="唯一码" required="" class="form-control">
        </div>
        <div class="form-group">
            <label>备注</label>
            <input type="text" name="remark" placeholder="备注" class="form-control">
        </div>
        <div class="form-group">
            <label>状态</label>
            <div>
              <label class="radio-inline">
                <input type="radio" name="status" value="1"> 启用
              </label>
              <label class="radio-inline">
                <input type="radio" name="status" value="0"> 禁用
              </label>
            </div>
        </div>
        <div class="form-group">
            <div class="btn-group">
                <input class="btn btn-sm btn-primary" type="button" onclick="alter_opus()" value='保存'>
                <button class="btn btn-default btn-sm" onclick="if(self=='top'){history.go(-1);}else{window.parent.close_actvie_tab();};return false;">取 消</button>
            </div>
        </div>
    </form>
</div>