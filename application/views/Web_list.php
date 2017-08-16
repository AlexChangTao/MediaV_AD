<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>站点列表 <?php if ($adv_id){ echo "<a title='添加站点' class='page-action' href=".site_url('C_Website/Web_add_page/').$adv_id.">添加站点</a>";}?> </h2>
        <div class="breadcrumb">
            <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
            <li>推广站点管理</li>
            <li>站点列表</li>
        </div>
    </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
    <?php if (!$adv_id) :?>
        <div class="form-group">
            <form method="get" action="<?php echo site_url('C_Website/web_search') ?>" role="form" class="form-horizontal form-inline">
                <div class="input-group">
                    <input type="text" name="name" class="form-control input-sm" placeholder="根据站点名称搜索" >
                    <span class="input-group-btn">
                        <button class="btn btn-sm btn-primary" type="submit">搜索</button>
                    </span>
                </div>
            </form>
        </div>
    <?php endif; ?>
    <table class="footable table table-stripped toggle-arrow-tiny" data-page-size="15">
        <thead>
        <tr>
            <th>站点名称</th>
            <th>站点编码</th>
            <th>所属广告主</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>状态</th>
            <th>功能模块</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if ($result): ?>
            <?php foreach($result as $web): ?>
            <tr>
                <td><a title="<?php echo $web['web_name'] ?>站点修改" class="page-action" href="<?php echo site_url('C_Website/web_edit_page/').$web['id'].'/'.$web['ad_id'] ?>"><?php echo $web['web_name'] ?></a></td>
                <td><?php echo $web['web_code'] ?></td>
                <td><a title="<?php echo $web['name'].'站点'; ?>" class="page-action" href="<?php echo site_url('C_Website/Web_advlist/').$web['ad_id']?>"><?php echo $web['name'] ?></a></td>
                <td><?php echo $web['start_date'] ?></td>
                <td><?php echo $web['end_date'] ?></td>
                <td><?php echo $web['is_use']?'启用':'停用'; ?></td>
                <td>
                <?php foreach ($web['wm'] as $value):?>
                    <a title="<?php echo $web['web_name'].$value['name'] ?>" class="page-action btn-default btn btn-xs" href="<?php echo site_url('C_Website/Webmodel_write').'/'.$web['id'].'/'.$value['id'] ?>"><?php echo $value['name'] ?></a>
                <?php endforeach; ?>
                </td>
                <td>
                    <a title="<?=$web['web_name']?>&nbsp;站点功能" class="page-action btn-default btn btn-xs" href="<?php echo site_url('C_Website/web_model/').$web['id'] ?>">查看</a>
                </td>
            </tr>
            <?php endforeach; ?>
        <?php endif ?>
    </table>
    <?php echo $page; ?>
</div>
