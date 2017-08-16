<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>接口配置</h2>
        <div class="breadcrumb">
          <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
          <li>配置查看</li>
        </div>
    </div>
</div>
<br/>
<div class="ibox-content main_content_margin float-e-margins">
    <form method="post" class="form-horizontal">
        <div class="form-group">
          <label class="col-lg-2 control-label">广告主</label>
            <div class="col-lg-10">
              <input type="text"  value="<?php echo $adv_info['name']; ?>" readonly  class="form-control">
            </div>
            <input type="hidden" name="web_id" value="<?php echo $web_id; ?>">
            <input type="hidden" name="m_id" value="<?php echo $m_id; ?>">
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">活动站</label>
            <div class="col-lg-10">
              <input type="text" value="<?php echo $adv_info['web_name']; ?>" readonly class="form-control">
            </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">模块名称</label>
            <div class="col-lg-10">
              <input type="text" readonly value="<?php echo $model_name['name']; ?>" readonly class="form-control">
            </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">开始时间</label>
            <div class="col-lg-10">
              <input type="text" name="start_date" readonly value="<?php echo $model_status['start_date'] ?>" class="form-control ">
            </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">结束时间</label>
            <div class="col-lg-10">
              <input type="text" name="end_date" readonly value="<?php echo $model_status['end_date'] ?>" class="form-control ">
            </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">是否启用</label>
          <div class="col-lg-10">
            <input type="text" name="end_date" readonly value="<?php echo $model_status['is_use']==1?'启用':'禁用'; ?>" class="form-control ">
            
          </div>
        </div>
        <div class="form-group">
            <div class="btn-group col-lg-offset-2 col-lg-10">
                <button class="btn btn-default btn-sm" onclick="if(self=='top'){history.go(-1);}else{window.parent.close_actvie_tab();};return false;">关闭</button>
            </div>
        </div>
    </form>
</div>
<link href="<?=base_url()?>web/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
<link href="<?=base_url()?>web/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=base_url()?>web/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>web/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
<script type="text/javascript">
$(function(){
    $('.form_datetime').datetimepicker({
        formatTime: 'HH:ii',
        formatDate: 'yyyy-mm-dd',
        autoclose: true,
        todayBtn: true,
        todayHighlight: true,
        language: 'zh-CN',
        pickerPosition: "bottom-left",
    });
    showTab();
});
</script>
