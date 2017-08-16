<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>广告主列表</h2>
        <div class="breadcrumb">
            <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
            <li>推广站点管理</li>
            <li><a href="<?=base_url('C_Advertisers/Adv_list')?>" class="page-action" title="广告主管理">广告主管理</a></li>
            <li>广告主列表</li>
        </div>
    </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
    <div class="form-inline">
        <?if(check_auth('C_Advertisers/Adv_add')):?>
        <div class="form-group">
            <a href="<?=base_url('C_Advertisers/Adv_add')?>" class="btn btn-sm btn-primary page-action" title="添加广告主"><i class="fa fa-plus-circle"></i>&nbsp;添加</a>
        </div>
        <?endif;?>
        <div class="form-group">
            <form method="get" action="<?php echo site_url('C_Advertisers/Adv_search') ?>" role="form" class="form-inline">
                <div class="input-group">
                        <input type="text" name="name" class="form-control input-sm" placeholder="根据广告主名称搜索" >
                        <span class="input-group-btn">
                            <button class="btn btn-sm btn-primary" type="submit">搜索</button>
                        </span>
                </div>
            </form>
        </div>
    </div>
    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
        <thead>
            <tr>
                <th>ID</th>
                <th>名称</th>
                <th>编码</th>
                <th>所有者</th>
                <th>行业</th>
                <th>说明</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($result as  $col):?>
            <tr>
                <td><?php echo $col['id'] ?></td>
                <td><?php echo $col['name'] ?></td>
                <td><?php echo $col['code'] ?></td>
                <td><?php echo $col['ower'] ?></td>
                <td><?php echo $col['trade'] ?></td>
                <td><?php echo $col['remark'] ?></td>
                <td>
                    <?if(check_auth('C_Advertisers/Adv_edit')):?>
                    <a title="<?php echo '修改'.$col['name']; ?>" class="page-action btn-default btn-sm" href="<?php echo site_url('C_Advertisers/Adv_edit/').$col['id']?>"><i class="fa fa-pencil-square-o"></i>&nbsp;修改</a>
                    <?endif;?>
                    <a title="<?php echo $col['name'].'&nbsp;站点列表'; ?>" class="page-action btn-info btn-sm" href="<?php echo site_url('C_Website/Web_advlist/').$col['id'];?>">推广站点</a>
                    <?php if ($col['trade']=='汽车'): ?>                                            
                    <a title="<?php echo $col['name'].'&nbsp;车系'; ?>" class="page-action btn-info btn-sm" href="<?php echo site_url('C_car/index') ?>?ad_id=<?= $col['id'] ?>">车系</a>
                    <a title="<?php echo $col['name'].'&nbsp;经销商'; ?>" class="page-action btn-info btn-sm" href="<?php echo site_url('C_dealer/index/').$col['id']?>">经销商</a>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php echo $page; ?>
</div>