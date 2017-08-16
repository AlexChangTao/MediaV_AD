<link href="<?=base_url()?>web/select2/select2.css" rel="stylesheet">
<script src="<?=base_url()?>web/select2/select2.full.min.js"></script>
<script type="text/javascript">
$(function(){
  $('select').select2();
});
</script>
<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
      <h2>广告主:<?php echo $ad_title ?></h2>
      <div class="breadcrumb">
          <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
          <li>推广站点管理</li>
          <li><a href="<?=base_url('C_Advertisers/Adv_list')?>" class="page-action" title="广告主管理">广告主管理</a></li>
          <li>经销商添加</li>
      </div>
      <div style="color:red"><?php echo validation_errors(); ?></div>
  </div>
</div>
<br/>
<div class="ibox-content main_content_margin float-e-margins">
   <form id="role-form" class="form-horizontal form-data" action="<?php echo site_url('c_dealer/add/').$ad_id; ?>" method="post">
      <div class="modal-body">
          <input type="hidden" id="ad_id" name="ad_id" value="<?php echo $ad_id; ?>">
          <input type="hidden" id="d_id" name="d_id" value="<?php if(isset($d_id)) echo $d_id; ?>">
          <div class="form-group">
              <label class="col-sm-2 control-label"> 经销商名称 </label>
              <div class="col-sm-3">
                  <input type="text" id="name" name="name" class="form-control" placeholder="请输入经销商名称" value='<?php if(isset($dealer['name'])) echo $dealer['name']; ?>' />
              </div>
              <label class="col-sm-2 control-label"> 经销商代码 </label>
              <div class="col-sm-3">
                  <input type="text" id="dealer_code" name="dealer_code" class="form-control" placeholder="请输入经销商代码" value='<?php if(isset($dealer['dealer_code'])) echo $dealer['dealer_code']; ?>' />
              </div>
          </div>
          <div class="form-group">
              <label class="col-sm-2 control-label"> 电话 </label>
              <div class="col-sm-3">
                  <input type="text" id="tel" name="tel" class="form-control" placeholder="请输入电话" value='<?php if(isset($dealer['tel'])) echo $dealer['tel']; ?>' />
              </div>
          </div>
          <div class="form-group">
              <label class="col-sm-2 control-label"> 省市地区 </label>
              <div class="col-sm-2">
                  <?php $url = site_url('c_dealer/get_region') ?>
                  <select id="province_id" name="province_id" class="form-control" onchange="load_region('province_id', 2, 'city_id', '<?php echo $url ?>')">
                      <option value="">请选择</option>
                      <?php foreach ($province as $key => $val): ?>
                          <option value="<?php echo $val['id'] ?>" ><?php echo $val['name'] ?></option>
                      <?php endforeach; ?>
                  </select>
              </div>
              <div class="col-sm-2">
                  <select id="city_id" name="city_id" class="form-control" onchange="load_region('city_id', 3, 'county_id', '<?php echo $url; ?>')">
                      <option value="0">市</option>
                  </select>
              </div>
              <div class="col-sm-2">
                  <select id="county_id" name="county_id" class="form-control">
                      <option value="0">县/区</option>
                  </select>
              </div>
          </div>
          <div class="form-group">
              <label class="col-sm-2 control-label"> 地址 </label>
              <div class="col-sm-3">
                  <input type="text" id="address" name="address" class="form-control" placeholder="请输入地址" value='<?php if(isset($dealer['address'])) echo $dealer['address']; ?>' />
              </div>

              <label class="col-sm-2 control-label"> 邮箱 </label>
              <div class="col-sm-3">
                  <input type="text" id="email" name="email" class="form-control" placeholder="请输入邮箱" value='<?php if(isset($dealer['email'])) echo $dealer['email']; ?>' />
              </div>
          </div>
          <div class="form-group">
              <label class="col-sm-2 control-label"> 销售车型 </label>
              <div class="col-sm-8">
                  <?php if(isset($car)): ?>
                      <?php foreach ($car as $v): ?>
                          <label class="col-xs-2 checkbox-inline" style="width:120px;">
                              <input name="car_id[]" class="ace ace-checkbox-2 father" type="checkbox" value="<?php echo $v['id'] ?>"
                              <?php
                                  if(isset($dealer_car))
                                  {
                                      foreach($dealer_car as $vv)
                                      {
                                          if($vv['car_id'] == $v['id'])
                                          {
                                              echo "checked='checked'";
                                          }
                                      }
                                  }
                              ?>/>
                              <span class="lbl"> <?php echo $v['name'] ?> </span>
                          </label>
                      <?php endforeach; ?>
                  <?php endif; ?>
              </div>
          </div>
          <div class="form-group">
              <label class="col-sm-2 control-label"> 经度 </label>
              <div class="col-sm-3">
                  <input type="text" id="longitude" name="longitude" class="form-control" placeholder="请输入经度" value='<?php if(isset($dealer['longitude'])) echo $dealer['longitude']; ?>' />
              </div>
              <label class="col-sm-2 control-label"> 纬度 </label>
              <div class="col-sm-3">
                  <input type="text" id="latitude" name="latitude" class="form-control" placeholder="请输入纬度" value='<?php if(isset($dealer['latitude'])) echo $dealer['latitude']; ?>' />
              </div>
          </div>
          <div class="form-group">
              <label class="col-sm-2 control-label"> 备注 </label>
              <div class="col-sm-3">
                  <input type="text" id="remark" name="remark" class="form-control" placeholder="请输入备注" value='<?php if(isset($dealer['remark'])) echo $dealer['remark']; ?>' />
              </div>
          </div>
          <div class="form-group">
              <label class="col-sm-2 control-label"> 状态 </label>
              <div class="col-sm-3">
                  <select id="status" name="status" class="form-control">
                      <option value="0" > 启用 </option>
                      <option value="1" > 禁用 </option>
                  </select>
              </div>
              <label class="col-sm-2 control-label"> 经销商类别 </label>
              <div class="col-sm-3">
                  <select id="dealer_type" name="dealer_type" class="form-control">
                      <option value="销售店" > 销售店 </option>
                      <option value="服务店" > 服务店 </option>
                  </select>
              </div>
          </div>
          <div class="btn-group col-md-offset-5 col-md-9">
              <input class="btn btn-primary btn-sm" type="submit" value="提交">
              <button class="btn btn-default btn-sm" onclick="if(self=='top'){history.go(-1);}else{window.parent.close_actvie_tab();};return false;">取 消</button>
          </div>
      </div>
  </form>
</div>
<script>
    function load_region(sel, type_id, selName, url)
    {
        $("#"+selName+" option").each(function(){
            $(this).remove();
        })

        $("<option value=0>请选择</option>").appendTo($("#"+selName));
        if($("#"+sel).val()==0)
        {
            return;
        }

        $.getJSON(url, {parent_id:$("#"+sel).val(), type:type_id},

            function(data)
            {
                if(data)
                {
                    $.each(data, function(idx, item){
                        $("<option value="+item.id+">" + item.name + item.suffix + "</option>").appendTo($("#"+selName))
                    })
                }
                else
                {
                    $("<option value='0'>请选择</option>").appendTo($("#"+selName))
                }
            }
        )
    }
</script>
