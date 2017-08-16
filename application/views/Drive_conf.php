<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>接口配置</h2>
        
        <div style="color:red"><?php echo validation_errors(); ?></div> 
    </div>
</div>
<br/>
<div class="ibox-content float-e-margins">
    <form method="post" action="<?php echo site_url('C_Website/drive_write/').$web_id.'/'.$m_id; ?>" class="form-horizontal">
        <div class="form-group"><label class="col-lg-2 control-label">广告主</label>
            <div class="col-lg-10"><input type="text"  value="<?php echo $adv_info['name']; ?>" readonly  class="form-control"></div>
            <input type="hidden" name="web_id" value="<?php echo $web_id; ?>">
            <input type="hidden" name="m_id" value="<?php echo $m_id; ?>">
        </div>
        <div class="form-group"><label class="col-lg-2 control-label">活动站</label>
            <div class="col-lg-10"><input type="text" value="<?php echo $adv_info['web_name']; ?>" readonly class="form-control"></div>
        </div>
        <div class="form-group"><label class="col-lg-2 control-label">模块名称</label>
            <div class="col-lg-10"><input type="text"  value="<?php echo $model_name['name']; ?>" readonly class="form-control"></div>
        </div>
        <div class="form-group"><label class="col-lg-2 control-label">开始时间</label>
            <div class="col-lg-10"><input type="text" name="start_date" value="<?php echo $model_status['start_date'] ?>" class="form-control form_datetime"></div>
        </div>
        <div class="form-group"><label class="col-lg-2 control-label">结束时间</label>
            <div class="col-lg-10"><input type="text" name="end_date" value="<?php echo $model_status['end_date'] ?>" class="form-control form_datetime"></div>
        </div>
        <div class="form-group">
            <label class="col-lg-2 control-label">是否启用</label>
            <input type="radio" name="is_use" value="1"<?php if ($model_status['is_use']==1) {
                    echo "checked";
                } ?>>启用
                <input type="radio" value="0" name="is_use" <?php if ($model_status['is_use']==0) {
                    echo "checked";
                } ?>>禁用
        </div>
        <?php if ($crm):?>
            <div class="form-group">
                <label class="col-lg-2 control-label">对接crm</label>
            </div>
            <div class="form-group">
            <label class="col-lg-2 control-label">是否对接</label>
                <div>
                    <label class="radio-inline">
                    <input type="radio" name="crm" value="1" <?php if ($web_crm['is_crm']==1): ?>
                        checked
                    <?php endif ?>>是
                    </label>
                    <label class="radio-inline">
                    <input type="radio" name="crm" value="0" <?php if ($web_crm['is_crm']==0): ?>
                        checked
                    <?php endif ?> >否
                    </label>
                </div>
            </div>
             <div class="form-group"><label class="col-lg-2 control-label">接口名称</label>
             <div class="col-lg-10">
                <select name="api" class="form-control">
                 <option value="">选择接口</option>   
                <?php foreach ($crm as $value): ?>
                    <option value="<?php echo $value['id'] ?>" <?php if ($web_crm['api_id']==$value['id']): ?>
                        selected
                    <?php endif ?>><?php echo $value['name'] ?></option>
                <?php endforeach; ?>
                
            </select></div>
        </div>
        <?php endif; ?>
        <div class="form-group">
            <div class="btn-group col-lg-offset-2 col-lg-10">
                <button class="btn btn-sm btn-primary" type="submit">提交</button>
                <button class="btn btn-default btn-sm" onclick="if(self=='top'){history.go(-1);}else{window.parent.close_actvie_tab();};return false;">取 消</button>
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
