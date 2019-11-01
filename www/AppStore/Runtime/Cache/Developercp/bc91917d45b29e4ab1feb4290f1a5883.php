<?php if (!defined('THINK_PATH')) exit();?>
<form action="<?php echo U('Admin://User/userDel');?>" method="post" name="orderForm" >
<div class="content" style="width:100%; padding-top:0;">
	<table id="list_table" class="list_table">
		<col width="60px" />
		<col width="200px" />
		<col width="150px" />
		<col width="60px" />
		<col width="160px" />
		<col width="80px" />
		<col width="70px" />
        <thead>
			<tr role="head">
				<th class="t_c">选择</th>
				<th class="t_c">APP名称</th>
				<th class="t_c">分类</th>						
				<th class="t_c">版本</th>
				<th class="t_c">更新时间</th>
				<th class="t_c">下载次数</th>
				<th class="t_c">评分</th>
				</tr>
				</thead>
		<tbody>
			<?php if(is_array($applist)): $i = 0; $__LIST__ = $applist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr id="app_<?php echo ($vo['app_id']); ?>">
					<td class="t_c"><input name="id[]" type="checkbox" value="<?php echo ($vo['app_id']); ?>" /></td>
					<td><a href="<?php echo U('AppStore://App-Apps/detail', array('aid' => $vo['app_id']));?>" target="_blank" title="<?php echo ($vo['app_name']); ?>"><?php echo ($vo['app_name']); ?></a></td>
					<td class="t_c"><?php echo ($vo['app_cate_name']); ?></td>					
					<td class="t_c"><?php echo ($vo['app_version']); ?></td>
					<td class="t_c"><?php echo ($vo['app_update_date']); ?></td>
					<td class="t_c"><?php echo ($vo['app_downloads']); ?></td>
					<td class="t_c"><?php echo ($vo['app_score_average']); ?></td>			
					
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>
</div>

</form>
<div class="page" style="clear:both; margin-left:168px; margin-top:10px;">
<?php echo ($page); ?>
</div>

<script type="text/javascript">
//排序
function toSort(id)
{
	if(id!='')
	{
		var va = $('#s'+id).val();
		if(va!='' && va!=undefined)
		{
			$.get("{url:/goods/goods_sort}",{'id':id,'sort':va}, function(data)
			{
				if(data=='0')
				{
					alert('修改商品排序错误!');
				}
			});
		}
	}
}

function apps_del_one(id)
{
	$.dialog.confirm(
			'确定要删除所选中的信息吗？',
			function(){
				$.getJSON("<?php echo U('Admin://Apps/appsDel');?>", { id : id }, function(data){
					var tips = '';
					if(data.status == 1)
					{
						tips = 'succeed';
						$('#app_'+id).remove();
					}
					getDialog(data.msg, tips);
				})
			},
			function(){}
	);
//	var url = '{:U("Admin://Apps/appsDel?id=")}' + id;
//	
//	confirm('确定要删除所选中的信息吗？','window.location.href="'+url+'"');
}
//function apps_del_one(id)
//{
////	var url = '<?php echo U("Admin://Apps/appsDel?id=");?>' + id;
//	$.dialog.confirm(
//			'确定要删除所选中的信息吗？',
//			function(){
//				$.getJSON("<?php echo U('Admin://Apps/appsDel');?>", {id:id}, function(data){
//		//			if(data.status == 1)getDialog(data.msg, 'succeed');
//					getDialog(data.msg, (data.status == 1) ? 'succeed' : '');
//				})
//			},
//			function(){}
//	);
////	confirm('确定要删除所选中的信息吗？','window.location.href="'+url+'"');
//}
function apps_del()
{
	var flag = 0;
	var id = [];
	var idNodes = $('input:checkbox[name="id[]"]:checked');
	idNodes.each(
		function(i)
		{
			flag = 1;
			id.push($(this).val());
		}
	);
	if(flag == 0 )
	{
		alert('请选择要删除的数据');
		return false;
	}
	$.dialog.confirm(
			'确定要删除所选中的信息吗？',
			function(){
				$.getJSON("<?php echo U('Admin://Apps/appsDel');?>", {id:id}, function(data){
		//			if(data.status == 1)getDialog(data.msg, 'succeed');
					var tips = '';
					if(data.status == 1)
					{
						tips = 'succeed';
						idNodes.each(
							function(i)
							{
								flag = 1;
								$('#app_'+$(this).val()).remove();
							}
						);
					}
					getDialog(data.msg, tips);
				})
			},
			function(){}
	);
	return false;
//	$("form[name='orderForm']").attr('action',"{:U('Admin://Apps/appsDel')}");
//	confirm('确定要删除所选中的信息吗？','formSubmit(\'orderForm\')');
}
function apps_status(type)
{
	var flag = 0;
	$('input:checkbox[name="id[]"]:checked').each(
		function(i)
		{
			flag = 1;
		}
	);
	if(flag == 0 )
	{
		if(type=='up')
		{
			alert('请选择要加入推荐的应用!');
		}
		else
		{
			alert('请选择要取消推荐的应用!');
		}
		return false;
	}
	$("form[name='orderForm']").attr('action',"<?php echo U('Admin://Apps/appsRecommend');?>"+type);
	if(type=='up')
	{
		confirm('确定将选中的应用加入推荐吗？','formSubmit(\'orderForm\')');
	}
	else
	{
		confirm('确定将选中的应用取消推荐吗？','formSubmit(\'orderForm\')');
	}
}
</script>