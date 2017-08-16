<div class="row wrapper border-bottom white-bg page-heading">
    <div class="col-lg-10">
        <h2>经销商列表</h2>
        <div class="breadcrumb">
            <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
            <li>推广站点管理</li>
            <li><a href="<?=base_url('C_Advertisers/Adv_list')?>" class="page-action" title="广告主管理">广告主管理</a></li>
            <li>经销商列表</li>
        </div>
    </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
        <?php if ($ad_id): ?>
		<div>
			<a title="添加经销商" href="<?php echo site_url('c_dealer/add/').$ad_id;?>" type="button" class="page-action btn btn-sm btn-primary" rel='0'>
				<i class="fa fa-plus"></i>&nbsp;添加经销商
			</a>
            <a target="_blank" href="<?php echo site_url('c_dealer/dealer_xml/').$ad_id;?>" type="button" class="btn btn-sm btn-primary" rel='0'>
                <i class="fa fa-plus"></i>&nbsp;更新经销商缓存
            </a>
		</div>
        <br/>
        <?php endif ?>
        <?php if ($list): ?>
        <table class="table table-striped table-bordered table-hover">
            <thead>
                <tr>
                    <th><input class="check-all" type="checkbox" value=""></th>
                    <th>ID</th>
                    <th>所属广告主</th>
                    <th>省份</th>
                    <th>城市</th>
                    <th>经销商名称</th>
                    <th>经销商代码</th>
                    <th>电话</th>
                    <th>类型</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($list as $key => $val): ?>
                    <tr>
                        <td class="center"><input class="ids" type="checkbox" name="ids[]" value="<{$val['id']}>"></td>
                        <td><?= $val['id'] ?></td>
                        <td><a title="广告主经销商" href="<?php echo site_url('C_dealer/index/').$val['ad_id']?> " class="page-action"><?= $val['advertiser_name'] ?></a> </td>
                        <td><?= $val['province'] ?></td>
                        <td><?= $val['city'] ?></td>
                        <td><?= $val['name'] ?></td>
                        <td><?= $val['dealer_code'] ?></td>
                        <td><?= $val['tel'] ?></td>
                        <td><?= $val['dealer_type'] ?></td>
                        <td>
                           <?php echo ($val['status'] == 0)?'启用':'禁用'; ?>
                        </td>
                        <td class="center">
                            <div class="btn-group">
                            <a title="经销商修改" href="<?php echo site_url('c_dealer/edit/').$val['ad_id'].'/'.$val['id'];?>" type="button" class="page-action btn btn-xs btn-default" rel='0'>
                                <i class="fa fa-pencil-square-o"></i>&nbsp;修改
                            </a>
                            <a title="经销商查看" href="<?php echo site_url('c_dealer/show/').$val['ad_id'].'/'.$val['id'];?>" type="button" class="page-action btn btn-xs btn-default" rel='0'>
                                <i class="fa fa-eye"></i>&nbsp;查看
                            </a>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?= $page ?>
        <?php endif ?>
	</div>
</div>