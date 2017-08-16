<div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>字段列表</h2>
            <div class="breadcrumb">
                <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
                <li>功能模块管理</li>
                <li>字段列表</li>
            </div>
        </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
        <thead>
        <tr>
            <th>ID</th>
            <th>字段名</th>
            <th>长度</th>
            <th>类型</th>
            <th>注释</th>
            <th>所属表</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($result as  $col):?>
        <tr>
            <td><?php echo $col['id'] ?></td>
            <td><?php echo $col['colum_name'] ?></td>
            <td><?php echo $col['colum_len'] ?></td>
            <td><?php echo $col['colum_type'] ?></td>
            <td><?php echo $col['remark'] ?></td>
            <td><?php echo $col['table_name'] ?></td>
            <td>
                <a title="字段修改" class="btn btn-xs btn-default page-action" href="<?php echo site_url('C_Colum/Col_edit/').$col['t_id'].'/'.$col['id']?>">
                <i class="fa fa-pencil-square-o"></i>&nbsp;修改
                </a>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo $page ?>
</div>
