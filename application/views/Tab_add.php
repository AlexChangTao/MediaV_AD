<link href="<?=base_url()?>web/select2/select2.css" rel="stylesheet">
<script src="<?=base_url()?>web/select2/select2.full.min.js"></script>
<script type="text/javascript">
$(function(){
  $('select').select2();
});
</script>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>表添加</h2>
        <div class="breadcrumb">
            <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
            <li>功能模块管理</li>
            <li><a href="<?=base_url('C_Table/Tab_list')?>" class="page-action" title="表管理">表管理</a></li>
            <li>表添加</li>
        </div>
        <div style="color:red"><?php echo validation_errors(); ?></div>
    </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
    <form method="post" action="<?php echo site_url('C_Table/Tab_add') ?>" class="form-horizontal">
        <div class="form-group">
          <label class="col-lg-2 control-label">所属模块</label>
          <div class="col-lg-10">
              <select name='mid' class="form-control">
                  <?php foreach ($model as $key => $value):?>
                      <option value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?></option>
                  <?php endforeach; ?>
              </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">表名</label>
          <div class="col-lg-10"><input type="text" name="name" placeholder="表名" class="form-control"></div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">表属性</label>
          <div class="col-lg-10">
            <select name="type" class="form-control">
                <option value="innodb">innodb</option>
                <option value="myisam">myisam</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">说明</label>
          <div class="col-lg-10"><input type="text" name="remark" placeholder="表说明" class="form-control"></div>
        </div>
        <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10 btn-group">
                <button class="btn btn-sm btn-primary" type="submit">提交</button>
                <button class="btn btn-default btn-sm" onclick="if(self=='top'){history.go(-1);}else{window.parent.close_actvie_tab();};return false;">取 消</button>
            </div>
        </div>
    </form>
</div>
