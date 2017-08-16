<link type="text/css" rel="stylesheet" href="<?=base_url()?>web/tree/font-awesome.min.css" />
<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
      <h2>广告主:<?php echo $ad_title; ?>:站点添加</h2>
      <div class="breadcrumb">
          <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
          <li>推广站点管理</li>
          <li><a href="<?=base_url('C_Website/web_list')?>" class="page-action" title="站点管理">站点管理</a></li>
          <li>站点添加</li>
      </div>
      <div style="color:red"><?php echo validation_errors(); ?></div>
  </div>
</div>
<br/>
<div class="ibox-content main_content_margin float-e-margins">
    <form method="post" action="<?php echo site_url('C_Website/web_add_page/').$adv_id; ?>" class="form-horizontal">
        <div class="form-group">
          <label class="col-lg-2 control-label">开始时间</label>
          <div class="col-lg-10"><input value="<?php echo set_value('start'); ?>" type="text" name="start" placeholder="选择时间" class="form-control form_datetime">
          </div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">结束时间</label>
          <div class="col-lg-10"><input value="<?php echo set_value('end'); ?>" type="text" name="end" placeholder="选择时间" class="form-control form_datetime"></div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">站点名称</label>
          <div class="col-lg-10"><input type="text" value="<?php echo set_value('name'); ?>" name="name" placeholder="站点名称" class="form-control"></div>
          <input type="hidden" name="ad_id" value="<?php echo $adv_id; ?>">
        </div>
         <div class="form-group">
           <label class="col-lg-2 control-label">站点编码</label>
          <div class="col-lg-10"><input type="text" value="<?php echo set_value('code'); ?>" name="code" placeholder="站点编码" class="form-control"></div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">站点链接</label>
          <div class="col-lg-10"><input type="text" value="<?php echo set_value('url'); ?>" name="url" placeholder="站点链接" class="form-control"></div>
        </div>
        <div class="form-group">
          <label class="col-lg-2 control-label">站点说明</label>
          <div class="col-lg-10"><input type="text" value="<?php echo set_value('remark'); ?>" name="remark" placeholder="站点说明" class="form-control"></div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">选择地区 </label>
          <div class="col-sm-10">
            <div id="treeview-checkbox-demo" style="border:1px solid #f5f5f5;height:250px;width:350px;overflow:scroll;">
              <ul>
               <!-- 省份 -->
                 <?php foreach ($area as $key => $value):?>
                    <li data-value="<?php echo $value['province_id'] ?>"><?php echo $value['province_name'] ?>
                        <ul>
                        <!-- 市 -->
                            <?php foreach ($value['child'] as $key => $city):?>
                            <li data-value="<?php echo $city['city_id']; ?>"><?php echo $city['city_name'] ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
           <!--  <button type="button" class="btn btn-success" id="show-values">确定选择</button> -->
            <input type="hidden" name="area" id="values">
            <!-- <pre name="area" id="values"></pre>  -->
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">选择媒体</label>
          <div class="col-sm-10">
           <?php foreach ($media as $key => $value):?>
              <label class="checkbox-inline">
                  <input type="checkbox" name="media[]" value="<?php echo $value['id'] ?>"><?php echo $value['media_name'] ?>
              </label>
          <?php endforeach; ?>
          </div>
        </div>
        <div class="form-group">
          <label class="col-sm-2 control-label">选择功能 </label>
          <div class="col-sm-10">
          <?php foreach ($models as $key => $value):?>
              <div class="i-checks">
                  <label class="checkbox-inline">
                      <input type="checkbox" name="feat[]" value="<?php echo $value['id'] ?>"><?php echo $value['name'] ?>
                  </label>
              </div>
          <?php endforeach; ?>
          </div>
        </div>
        <div class="form-group">
            <div class="btn-group col-lg-offset-2 col-lg-10">
                <button class="btn btn-sm btn-primary" type="submit">提交</button>
                <button class="btn btn-default btn-sm" onclick="if(self=='top'){history.go(-1);}else{window.parent.close_actvie_tab();};return false;">取 消</button>
            </div>
        </div>
    </form>
</div>
<script src="<?=base_url()?>web/tree/logger.js"></script>
<script src="<?=base_url()?>web/tree/treeview.js"></script>
<link href="<?=base_url()?>web/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
<link href="<?=base_url()?>web/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?=base_url()?>web/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="<?=base_url()?>web/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
<!-- 用于树形选择 -->
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
<script>
    $('#treeview-checkbox-demo').treeview({
        debug : true,
        data : []
    });
    $('#show-values').on('click', function(){
        $('#values').val(
            $('#treeview-checkbox-demo').treeview('selectedValues')
        );
    });
</script>
