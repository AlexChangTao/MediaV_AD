<div class="row wrapper border-bottom white-bg page-heading">
        <div class="col-lg-10">
            <h2>表列表</h2>
            <div class="breadcrumb">
                <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
                <li>功能模块管理</li>
                <li>表列表</li>
            </div>
        </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
        <thead>
        <tr>
            <th>表名</th>
            <th>记录数</th>
            <th>数据大小</th>
            <th>注释</th>
            <th>所属模块</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach($result as  $item):?>
        <tr>
            <td><?php echo $item['table_name'] ?></td>
            <td><?php echo $item['row_count'] ?></td>
            <td><?php echo $item['data_space'] ?>K</td>
            <td><?php echo $item['remark'] ?></td>
            <td><?php echo $item['name'] ?></td>
            <td><a title="<?php echo $item['table_name'].'字段管理'; ?>" class="page-action btn-white btn btn-xs" href="<?php echo site_url("C_Colum/Colum_add/").$item['id'] ?>">字段管理</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo $page; ?>
</div>
