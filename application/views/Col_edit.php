<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>字段修改</h2>
        <div class="breadcrumb">
            <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
            <li>功能模块管理</li>
            <li><a href="<?=base_url('C_Colum/Colum_list')?>" class="page-action" title="字段管理">字段管理</a></li>
            <li>字段修改</li>
        </div>
    </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
        <thead>
        <tr>
            <td>字段名</td>
            <td>字段类型</td>
            <td>长度</td>
            <td>允许空值</td>
            <td>注释</td>
            <td>操作</td>   
        </tr>
        </thead>
        <tbody>
        <?php foreach ($col as  $value): ?>
            <?php if ($value['id']==$info['id']): ?>
               <?php continue; ?> 
            <?php endif ?>
            <tr>
                <td><?php echo $value['colum_name'] ?></td>
                <td><?php echo $value['colum_type'] ?></td>
                <td><?php echo $value['colum_len'] ?></td>
                <td><?php echo $value['emp']?'是':'否'; ?></td>
                <td><?php echo $value['remark'] ?></td>
                <td><a href="<?php echo site_url('C_Colum/Col_edit/').$value['t_id'].'/'.$value['id']; ?> ">修改</a></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
        <tfoot>
        <tr>
        
        </tr>
        </tfoot>
    </table>
</div>
<?php if ($model_use<1): ?>
<div class="col-lg-5">
<div style="color:red"><?php echo validation_errors(); ?></div> 
    <div class="ibox float-e-margins">
         <form method="post" action="<?php echo site_url('C_Colum/Col_edit/').$info['id'].'/'.$info['t_id']; ?>" class="form-horizontal">
            <div class="form-group"><label class="col-lg-2 control-label">字段名</label>
                <div class="col-lg-10">
                    <input type="text" name="name" readonly value="<?php echo $info['colum_name'] ?>" placeholder="字段名" class="form-control">
                    <input type="hidden" name="id" value="<?php echo $info['id']; ?>">
                    <input type="hidden" name="tid" value="<?php echo $info['t_id']; ?>">
                </div>
            </div>
            <div class="form-group"><label class="col-lg-2 control-label">字段长度</label>
                <div class="col-lg-10"><input type="text" name="length" placeholder="字段长度" value="<?php echo $info['colum_len'] ?>" class="form-control"></div>
            </div>
            <div class="form-group"><label class="col-sm-2 control-label">字段类型</label>
                <div class="col-sm-10">
                    <label class="checkbox-inline i-checks"> <input type="radio" name="type" <?php if ($info['colum_type']=='varchar'): ?>
                        checked 
                    <?php endif ?> disabled  value="varchar">varchar </label>
                    <label class="checkbox-inline i-checks"> <input type="radio" name="type"  <?php if ($info['colum_type']=='int'): ?>
                        checked 
                    <?php endif ?> disabled value="int">int</label>
                    <label class="checkbox-inline i-checks"> <input type="radio" name="type" <?php if ($info['colum_type']=='int'): ?>
                        checked 
                    <?php endif ?> disabled value="datetime">datetime</label></div>
            </div>
            <div class="form-group"><label class="col-sm-2 control-label">允许空值</label>
                <div class="col-sm-10">
                        <label class="checkbox-inline i-checks"> <input type="radio" name="emp" <?php if ($info['emp']==1): ?>
                            checked
                        <?php endif ?> value="1">是</label>
                        <label class="checkbox-inline i-checks"> <input type="radio" name="emp" <?php if ($info['emp']==0): ?>
                            checked
                        <?php endif ?> value="0">否</label>
                </div>
            </div>
             <div class="form-group"><label class="col-lg-2 control-label">注释</label>
                <div class="col-lg-10"><input type="text" name="remark" placeholder="注释" value="<?php echo $info['remark'] ?>" class="form-control"></div>
            </div>
            <div class="form-group">
                <div class="col-lg-offset-2 col-lg-10">
                    <button class="btn btn-sm btn-white" type="submit">提交</button>
                    <button class="btn btn-default btn-sm" onclick="if(self=='top'){history.go(-1);}else{window.parent.close_actvie_tab();};return false;">取 消</button>
                </div>
            </div>
        </form> 
</div>
</div>
<?php endif ?>