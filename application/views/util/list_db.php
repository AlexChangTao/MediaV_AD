<div class="row wrapper border-bottom white-bg page-heading">
  <div class="col-lg-10">
	  <h2>数据库管理</h2>
	  <div class="breadcrumb">
	    <li><a href="<?=base_url('admin/main')?>" class="page-action">首页</a></li>
	    <li>数据库管理</li>
	  </div>
  </div>
</div>
<br/>
<div class="ibox-content main_content_margin">
	<ul class="nav nav-tabs" role="tablist">
	    <li role="presentation" class="active"><a href="#table" aria-controls="table" role="tab" data-toggle="tab">备份数据库</a></li>
	    <li role="presentation"><a href="#database" aria-controls="database" role="tab" data-toggle="tab">还原数据库</a></li>
	    <li role="presentation"><a href="#import" aria-controls="import" role="tab" data-toggle="tab">导入数据库</a></li>
	</ul>
	<div class="tab-content">
		<div role="tabpanel" class="tab-pane active" id="table">
			<br/>
			<p>
				<a href="backup_db" class="btn btn-primary btn-sm btn_backup">立即备份</a>
				<a href="backup_db?save_latest=1" class="btn btn-primary btn-sm btn_backup">立即备份 (并保存为最后一次备份)</a>
			</p>
			<table class="table table-striped table-hover table-bordered">
				<tbody>
					<tr>
						<th>#</th>
						<th>表名</th>
						<th>行数</th>
						<th>大小</th>
						<th>冗余</th>
						<th>引擎</th>
						<th>自增值</th>
						<th>排列规则</th>
						<th>备注</th>
						<th>操作</th>
					</tr>
					<?foreach ($tables as $key=>$value):?>
					<tr>
						<td><?=$key+1?></td>
						<td><?=$value['Name']?></td>
						<td><?=$value['Rows']?></td>
						<td><?=formatBytes($value['Data_length'])?></td>
						<td><?=$value['Data_free']?></td>
						<td><?=$value['Engine']?></td>
						<td><?=$value['Auto_increment']?></td>
						<td><?=$value['Collation']?></td>
						<td><?=$value['Comment']?></td>
						<td>
							<div class="btn-group">
								<a href="optimize/<?php echo $value['Name']; ?>" class="btn btn-xs btn-default btn_optimize">优化表</a>
								<a href="repair/<?php echo $value['Name']; ?>" class="btn btn-xs btn-default btn_repair">修复表</a>
							</div>
						</td>
					</tr>
					<?endforeach;?>
				</tbody>
			</table>
		</div>
		<div role="tabpanel" class="tab-pane" id="database">
			<br/>
			<div class="alert alert-warning alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <span><strong>安全警告！</strong>还原数据库可能会造成数据丢失！确保在维护时间内操作，务必提前备份数据库！</span>
			</div>
			<table class="table table-striped table-hover table-bordered">
				<tbody>
					<tr>
						<th>版本</th>
						<th>文件路径</th>
						<th>文件大小</th>
						<th>创建时间</th>
						<th>操作</th>
					</tr>
					<?php foreach ($backup_sql_files as $file=>$value): ?>
					<tr>
						<td>
							<?php
								$datetime = explode('_', str_replace('.sql', '', $file));
								echo '<b>'.$datetime[0].'</b> '.str_replace('-', ':', $datetime[1]);
							?>
						</td>
						<td><?=$value['server_path'];?></td>
						<td><?=formatBytes($value['size']);?></td>
						<td><?=date('Y-m-d H:i:s',$value['date']);?></td>
						<td>
							<div class="btn-group">
								<a href="restore_db/<?php echo $file; ?>" class="btn btn-xs btn-primary btn_restore">还原</a>
								<?if($datetime[0]!='latest'):?><a href="remove_db/<?php echo $file; ?>" class="btn btn-xs btn-default btn_remove_db">删除</a><?endif;?>
							</div>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div role="tabpanel" class="tab-pane" id="import">
			<br/>
			<div class="alert alert-warning alert-dismissible" role="alert">
			  <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			  <span><strong>安全警告！</strong>DROP TABLE ... 语句会导致数据丢失，请谨慎操作！</span>
			</div>
			<form id="parse_form" action="<?=base_url('util/parse')?>">
				<div class="form-group">
					<textarea id="textarea1" name="sql" class="form-control" rows="6"></textarea>
				</div>
				<div class="form-group">
					<span id="pasre_btn" class="btn btn-sm btn-primary">分析SQl语句</span>
					<span id="import_btn" class="btn btn-sm btn-default">执行SQl语句</span>
				</div>
			</form>
			<form id="import_form" action="<?=base_url('util/import')?>">
				<table id="result" class="table"></table>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
$(function(){
	$('.btn_backup').click(function(){
		var obj = $(this);
		$.getJSON(obj.attr('href'),{},function(rs){
			if(rs.success){
				parent.layer.alert(rs.msg,function(){
					location.reload();
					parent.layer.closeAll();
				});
			}else{
				parent.layer.alert(rs.msg);
			}
		});
		return false;
	});
	//去除空格
	$("#textarea1").bind({
        'blur' : function(){
        	var str = trim($("#textarea1").val());
        	$("#textarea1").val(str);
        }
	});
	//分析
	$('#pasre_btn').click(function(){
		$.ajax({
			url : $('#parse_form').attr('action'),
			type : 'post',
			data : $('#parse_form').serialize(),
			dataType : 'json',
			success : function(rs){
				var str = '';
				$.each(rs.result,function(i,index){
					if(index.indexOf('DROP')){
						var input_str = '<input name="sql_str[]" type="checkbox" value="'+index+'" checked="true" />';
					} else {
						var input_str = '<input name="sql_str[]" type="checkbox" value="'+index+'" />';
					}
					str += '<tr><td width="10">'+input_str+'</td><td>'+index+'</td></tr>';
				});
				$('#result').html(str);
			}
		});
		return false;
	});
	//导入
	$('#import_btn').click(function(){
		$.ajax({
			url : $('#import_form').attr('action'),
			type : 'post',
			data : $('#import_form').serialize(),
			dataType : 'json',
			success : function(rs){
				var inputs = $('#result input:checked');
				$.each(rs.result,function(i,index){
					if(index){
						inputs.eq(i).parents('tr').addClass('input_bg_color_success');
					} else {
						inputs.eq(i).parents('tr').addClass('input_bg_color_error');
					}
				});
			}
		});
		return false;
	});
	$('.btn_remove_db').click(function(){
		var obj = $(this);
		parent.layer.confirm('确定要删除吗？',{
			btn: ['确定','取消']
		},function(){
			$.ajax({
				url : obj.attr('href'),
				dataType : 'json',
				success : function(rs){
					if(rs.success){
						parent.layer.closeAll();
						obj.parents('tr').fadeOut("slow", function() {
						    obj.parents('tr').remove();
						});
					} else {
						alert('删除失败');
					}
				}
			});
		},function(){
		});
		return false;
	});
	$('.btn_restore').click(function(){
		var obj = $(this);
		parent.layer.prompt({title: '输入口令', formType: 1}, function(pass, index){
			parent.layer.closeAll();
			var index = parent.layer.load(0, {shade: [0.2,'#000']}); //0代表加载的风格，支持0-2
			$.ajax({
				url : obj.attr('href'),
				type : 'post',
				data : {'pass':pass},
				dataType : 'json',
				success : function(rs){
					if(rs.success){
						parent.layer.closeAll();
						parent.layer.confirm(rs.msg, {
							btn: ['查看结果','关闭']
						},function(){
							var str = '<pre>'+formatJson(JSON.stringify(rs.data))+'</pre>';
							parent.layer.open({
							  type: 1 //Page层类型
							  ,area: ['700px', '480px']
							  ,title: '查看结果'
							  ,shade: 0.1 //遮罩透明度
							  ,maxmin: true //允许全屏最小化
							  ,anim: 0 //0-6的动画形式，-1不开启
							  ,content: '<div style="padding:10px;">'+str+'</div>'
							});
						},function(){
						});
					} else {
						parent.layer.closeAll();
						parent.layer.alert(rs.msg);
					}
				}
			});
		});
		return false;

		parent.layer.confirm('确定要还原数据库吗？', {
		  btn: ['确定','取消'] //按钮
		}, function(){
			parent.layer.closeAll();
			var index = parent.layer.load(0, {shade: [0.2,'#000']}); //0代表加载的风格，支持0-2
			$.ajax({
				url : obj.attr('href'),
				dataType : 'json',
				success : function(rs){
					if(rs.success){
						parent.layer.closeAll();
						parent.layer.confirm(rs.msg, {
							btn: ['查看结果','关闭']
						},function(){
							var str = '<pre>'+formatJson(JSON.stringify(rs.data))+'</pre>';
							parent.layer.open({
							  type: 1 //Page层类型
							  ,area: ['700px', '480px']
							  ,title: '查看结果'
							  ,shade: 0.1 //遮罩透明度
							  ,maxmin: true //允许全屏最小化
							  ,anim: 0 //0-6的动画形式，-1不开启
							  ,content: '<div style="padding:10px;">'+str+'</div>'
							});
						},function(){
						});
					} else {
						alert('还原失败');
					}
				}
			});
		}, function(){
		});
		return false;
	});
	$('.btn_optimize').click(function(){
		var obj = $(this);
		$.ajax({
			url:obj.attr('href'),
			type:'get',
			dataType:'json',
			success:function(rs){
				if(rs.success){
					parent.layer.alert(rs.msg);
				}
			}
		});
		return false;
	});
	$('.btn_repair').click(function(){
		var obj = $(this);
		$.ajax({
			url:obj.attr('href'),
			type:'get',
			dataType:'json',
			success:function(rs){
				if(rs.success){
					parent.layer.alert(rs.msg);
				}
			}
		});
		return false;
	});
});
//去除空格
function trim(str) {
	if(str == null){
		str = "";
	}
    return str.replace(/(^\s*)|(\s*$)/g, "");
};
function repeat(s, count) {
	return new Array(count + 1).join(s);
}

function formatJson($str) {
	var json    = $str;
	var i       = 0,
	len         = 0,
	tab         = "    ",
	targetJson  = "",
	indentLevel = 0,
	inString    = false,
	currentChar = null;
	for (i = 0, len = json.length; i < len; i += 1) {
		currentChar = json.charAt(i);

		switch (currentChar) {
			case '{':
			case '[':
				if (!inString) {
					targetJson += currentChar + "\n" + repeat(tab, indentLevel + 1);
					indentLevel += 1; 
				} else {
					targetJson += currentChar;
				}
			break;
			case '}':
			case ']':
				if (!inString) {
				indentLevel -= 1;
				targetJson += "\n" + repeat(tab, indentLevel) + currentChar;
				} else {
				targetJson += currentChar;
				}
				break;
			case ',':
				if (!inString) {
					targetJson += ",\n" + repeat(tab, indentLevel);
				} else {
					targetJson += currentChar;
				}
				break; 
			case ':':
				if (!inString) {
					targetJson += ": ";
				} else {
					targetJson += currentChar;
				} 
				break; 
			case ' ':
			case "\n":
			case "\t":
				if (inString) {
					targetJson += currentChar;
				}
				break;
			case '"':
				if (i > 0 && json.charAt(i - 1) !== '\\') {
					inString = !inString;
				}
				targetJson += currentChar;
				break;
			default:
				targetJson += currentChar;
				break;
		} 
	} 
	return targetJson;
}
</script>
<style type="text/css">
.input_bg_color_error {background-color: #ffcccc;}
.input_bg_color_success {background-color: #f5f5f5;}
</style>