<link href="<?=base_url()?>web/select2/select2.css" rel="stylesheet">
<script src="<?=base_url()?>web/select2/select2.full.min.js"></script>
<script type="text/javascript">
$(function(){
  $('select').select2();
});
</script>
<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>广告主添加</h2>
        <div class="breadcrumb">
            <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
            <li>推广站点管理</li>
            <li><a href="<?=base_url('C_Advertisers/Adv_list')?>" class="page-action" title="广告主管理">广告主管理</a></li>
            <li>广告主修改</li>
        </div>
         <div style="color:red"><?php echo validation_errors(); ?></div>
    </div>
</div>
<br/>
<div class="ibox-content main_content_margin float-e-margins">
    <form method="post" action="<?php echo site_url('C_Advertisers/Adv_edit/').$adv['id']; ?>" class="form-horizontal">
        <input type="hidden" name="adv_id" value="<?php echo $adv['id']; ?>">
        <div class="form-group">
          <label class="col-lg-2 control-label">广告主名称</label>
          <div class="col-lg-10">
            <input type="text" name="name" value="<?php echo $adv['name']; ?>"  placeholder="广告主名称" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">唯一码</label>
          <div class="col-lg-10">
            <input type="text" name="code" value="<?php echo $adv['code']; ?>" readonly placeholder="唯一码" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">所属行业</label>
          <div class="col-lg-10">
              <select name="trade" class="form-control">
              <option value="<?php echo $adv['trade'] ?>"><?php echo $adv['trade'] ?></option>
              </select>
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">所有者</label>
          <div class="col-lg-10">
            <input type="text" value="<?php echo $adv['ower']; ?>" name="ower" placeholder="所有者" class="form-control">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">备注</label>
          <div class="col-lg-10">
            <input type="text" name="remark" placeholder="备注" value="<?php echo $adv['remark']; ?>" class="form-control">
          </div>
        </div>
        <div class="form-group">
            <div class="col-lg-offset-2 col-lg-10 btn-group">
                <button class="btn btn-sm btn-primary" type="submit">提交</button>
                <button class="btn btn-default btn-sm" onclick="if(self=='top'){history.go(-1);}else{window.parent.close_actvie_tab();};return false;">取 消</button>
            </div>
        </div>
    </form>
</div>
