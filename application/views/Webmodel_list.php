<div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>模块功能列表</h2>
            <div class="breadcrumb">
                <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
                <li>推广站点管理</li>
                <li><a href="<?=base_url('C_Website/web_list')?>" class="page-action" title="站点管理">站点管理</a></li>
                <li>站点功能列表</li>
            </div>
        </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
        <thead>
        <tr>
            <th>功能模块名称</th>
            <th>所属活动站</th>
            <th>所属广告主</th>
            <th>开始日期</th>
            <th>结束日期</th>
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($result as  $value):?>
        <tr>
            <td><?php echo $value['mname']; ?></td>
            <td><?php echo $value['web_name']; ?></td>
            <td><?php echo $value['adv_name']; ?></td>
            <td><?php echo $value['m_start_date']; ?></td>
            <td><?php echo $value['m_end_date']; ?></td>
            <td><?php echo $value['is_use']?'启用':'禁用'; ?></td>
            <td>
              <a title="功能设置" class="btn-default btn btn-xs page-action" href="<?php echo site_url('C_Website/Webmodel_write/').$value['id'].'/'.$value['m_id'] ?>">设置</a>
              <a title="查看配置" class="btn-default btn btn-xs page-action" href="<?php echo site_url('C_Website/Webmodel_show/').$value['id'].'/'.$value['m_id'] ?>">查看</a>
              <a title="接口文档" class="btn-default btn btn-xs page-action" target="_blank"  href="<?php echo site_url('C_Interface/word/').$value['id'].'/'.$value['m_id'] ?>">接口文档</a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo $page; ?>
</div>
