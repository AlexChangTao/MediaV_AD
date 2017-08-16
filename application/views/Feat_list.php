<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>模块列表</h2>
        <div class="breadcrumb">
            <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
            <li>功能模块管理</li>
            <li><a href="<?=base_url('C_Features/Feat_list')?>" class="page-action" title="模块管理">模块管理</a></li>
            <li>模块列表</li>
        </div>
    </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
        <thead>
        <tr>
            <th>ID</th>
            <th>名称</th>
            <th>表</th>
            <th>应用数</th>
            <th>行业</th>
            <th>说明</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($result as $value): ?>
            <tr>
                <td><?php echo $value['id']?></td>
                <td><?php echo $value['name']?></td>
                <td><?php foreach ($value['table'] as $item):?>
                    <a title="<?php echo $item['table_name'].'管理'; ?>" class="page-action btn-default btn btn-xs" href="<?php echo site_url('C_Colum/Colum_add/').$item['id']?>"><?php echo $item['table_name']?></a>
                    <?php endforeach; ?>
                </td>
                <td><?php echo $value['use_count']?></td>
                <td><?php echo $value['trade']?></td>
                <td><?php echo $value['remark']?></td>
                <td>
                <?php if (!in_array($value['name'], array('预约试驾','抽奖')) ): ?>
                    <div class="btn-group">
                    <a title="模块修改" class="page-action btn-default btn btn-xs" href="<?php echo site_url('C_Features/Feat_edit/').$value['id'] ?>">
                        <i class="fa fa-pencil-square-o"></i>&nbsp;修改
                    </a>
                    <?php if ($value['use_count']<1): ?>
                    <a title="模块添加表" class="page-action btn-default btn btn-xs" href="<?php echo site_url('C_Table/tab_add'); ?>">
                        <i class="fa fa-plus-circle"></i>&nbsp;添加表
                    </a>
                    <?php endif; ?>
                    </div>
                </td>
                <?php endif; ?>
            </tr>
        <?php endforeach;?> 
        </tbody>
    </table>
    <?php echo $page; ?>
</div>