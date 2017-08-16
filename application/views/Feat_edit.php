<link href="<?=base_url()?>web/select2/select2.css" rel="stylesheet">
<script src="<?=base_url()?>web/select2/select2.full.min.js"></script>
<script type="text/javascript">
$(function(){
  $('select').select2();
});
</script>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>模块修改</h2>
        <div class="breadcrumb">
            <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
            <li>功能模块管理</li>
            <li><a href="<?=base_url('C_Features/Feat_list')?>" class="page-action" title="模块管理">模块管理</a></li>
            <li>模块修改</li>
        </div>
        <div style="color:red"><?php echo validation_errors(); ?></div>
    </div>
</div>
<br/>
<div class="ibox-content main_content_margin float-e-margins">
    <form method="post" action="<?php echo site_url('C_Features/Feat_edit/').$feat['id']; ?>" class="form-horizontal">
        <input type="hidden" name="feat_id" value="<?php echo $feat['id']; ?>">
        <div class="form-group">
          <label class="col-lg-2 control-label">模块名称</label>
          <div class="col-lg-10"><input type="text" name="name" value="<?php echo $feat['name'] ?>" placeholder="模块名称" class="form-control"></div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">唯一码</label>
          <div class="col-lg-10"><input type="text" name="code" readonly value="<?php echo $feat['code']; ?>" placeholder="唯一码" class="form-control"></div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">应用行业</label>
          <div class="col-lg-10">
            <select disabled="disabled"  name="trade" class="form-control">
              <?php foreach ($trade as  $value): ?>
                  <option value="<?php echo $value; ?>" <?php if ($feat['trade']==$value): ?>
                      selected
                  <?php endif ?>><?php echo $value ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">模块说明</label>
          <div class="col-lg-10"><input type="text" value="<?php echo $feat['remark']; ?>"  name="remark" placeholder="模块说明" class="form-control"></div>
        </div>
        <div class="form-group">
            <div class="btn-group col-lg-offset-2 col-lg-10">
                <button class="btn btn-sm btn-primary" type="submit">提交</button>
                <button class="btn btn-default btn-sm" onclick="if(self=='top'){history.go(-1);}else{window.parent.close_actvie_tab();};return false;">取 消</button>
            </div>
        </div>
    </form>
</div>
