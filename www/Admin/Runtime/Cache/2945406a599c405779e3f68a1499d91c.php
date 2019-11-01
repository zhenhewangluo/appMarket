<?php if (!defined('THINK_PATH')) exit();?><div class="headbar">
	<div class="position"><span>应用</span><span>></span><span>应用管理</span><span>></span><span>应用列表</span></div>
	<div class="operating">
		<div class="search f_r">
			<form name="serachuser" action="<?php echo U('Admin://Common/searchApp');?>" method="get">
			{__NOTOKEN__}
			<input type="hidden" name="is_visible" value="1" />
			<input type="hidden" name="<?php echo C('VAR_MODULE');?>" value="Common" />
			<input type="hidden" name="<?php echo C('VAR_ACTION');?>" value="searchApp" />
			<select class="auto" name="type">
				<option value="appName" <?php if(($searchType)  ==  "appName"): ?>selected="selected"<?php endif; ?>>应用名称</option>
				<option value="appId" <?php if(($searchType)  ==  "appId"): ?>selected="selected"<?php endif; ?>>应用ID</option>
				<option value="appCate" <?php if(($searchType)  ==  "appCate"): ?>selected="selected"<?php endif; ?>>所属分类</option>
			</select>
			<input class="small" name="sval" type="text" value="<?php echo ($searchVal); ?>" /><button class="btn" type="submit"><span class="sch">搜 索</span></button>
			</form>
		</div>
		<a href="javascript:void(0);">
			<button class="operating_btn" type="button" onclick="window.location='<?php echo U("Admin://Apps/appAdd");?>'">
					<span class="addition">添加应用</span>
			</button>
		</a>
		<a href="javascript:void(0)" onclick="selectAll('id[]')">
			<button class="operating_btn" type="button">
				<span class="sel_all">全选</span>
			</button>
		</a>
		<a href="javascript:void(0)" onclick="apps_del()">
			<button class="operating_btn" type="button">
				<span class="delete">批量删除</span>
			</button>
		</a>
		<a href="javascript:void(0)" onclick="apps_status('up')">
			<button class="operating_btn" type="button">
				<span class="import">批量推荐</span>
			</button>
		</a>
		<a href="javascript:void(0)" onclick="apps_status('down')">
			<button class="operating_btn" type="button">
				<span class="export">批量取消推荐</span>
			</button>
		</a>
		<a href="javascript:void(0)">
			<button class="operating_btn" type="button" onclick="location.href='<?php echo U("Admin://Apps/appRecycleList");?>'">
				<span class="recycle">回收站</span>
			</button>
		</a>
	</div>
<!--	<div class="searchbar">
		<form action="<?php echo U('Admin://Apps/appList');?>" method="get" name="goods_list">
			{__NOTOKEN__}
			<input type='hidden' name="<?php echo C('VAR_MODULE');?>" value='goods' />
			<input type='hidden' name="<?php echo C('VAR_ACTION');?>" value='goods_list' />
			<select class="auto" name="category_id">
				<option value="">选择分类</option>
				{query:name=category}
				<option value="<?php echo ($item['id']); ?>" {if:$category_id==$item['id']}selected{/if}><?php echo ($item['name']); ?></option>
				{/query}
			</select>
			<select class="auto" name="added" style="width:95px">
				<option value="">选择上下架</option>
				<option value="0" {if:$added=='0'}selected{/if}>上架</option>
				<option value="1" {if:$added=='1'}selected{/if}>下架</option>
			</select>
			<select class="auto" name="store_nums">
				<option value="">选择库存</option>
				<option value="1" {if:$store_nums=='1'}selected{/if}>缺货</option>
				<option value="10" {if:$store_nums=='10'}selected{/if}>低于10</option>
				<option value="100" {if:$store_nums=='100'}selected{/if}>10-100</option>
				<option value="101" {if:$store_nums=='101'}selected{/if}>100以上</option>
			</select>
			<select class="auto" name="commend">
				<option value="">选择商品标签</option>
				<option value="1" {if:$commend=='1'}selected{/if}>最新商品</option>
				<option value="2" {if:$commend=='2'}selected{/if}>特价商品</option>
				<option value="3" {if:$commend=='3'}selected{/if}>热卖商品</option>
				<option value="4" {if:$commend=='4'}selected{/if}>推荐商品</option>
			</select>
			<button class="btn" type="submit"><span class="sel">筛 选</span></button>
		</form>
	</div>-->
	<div class="field">
		<div class="table_box">
			<table class="list_table">
				<col width="30px" />
				<col />
				<col width="100px" />
				<col width="200px" />
				<col width="60px" />
				<col width="130px" />
				<col width="100px" />
				<col width="50px" />
				<col width="60px" />
				<col width="60px" />
				<col width="90px" />
				<thead>
					<tr role="head">
						<th class="t_c">选择</th>
						<th class="t_c">APP名称</th>
						<th class="t_c">分类</th>
						<th class="t_c">作者</th>
						<th class="t_c">版本</th>
						<th class="t_c">更新时间</th>
						<th class="t_c">下载次数</th>
						<th class="t_c">评分</th>
						<th class="t_c">推荐</th>
						<th class="t_c">排序</th>
						<th class="t_c">编辑</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
<form action="<?php echo U('Admin://User/userDel');?>" method="post" name="orderForm">
<div class="content">
	<table id="list_table" class="list_table">
		<col width="30px" />
		<col />
		<col width="100px" />
		<col width="200px" />
		<col width="60px" />
		<col width="120px" />
		<col width="100px" />
		<col width="60px" />
		<col width="60px" />
		<col width="60px" />
		<col width="100px" />
		<tbody>
			<?php if(is_array($applist)): $i = 0; $__LIST__ = $applist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr id="app_<?php echo ($vo['app_id']); ?>">
					<td class="t_c"><input name="id[]" type="checkbox" value="<?php echo ($vo['app_id']); ?>" /></td>
					<td><a href="/index.php?g=App&m=Apps&a=detail&aid=<?php echo ($vo['app_id']); ?>" target="_blank" title="<?php echo ($vo['app_name']); ?>"><?php echo ($vo['app_name']); ?></a></td>
					<td class="t_c"><?php echo ($vo['app_cate_name']); ?></td>
					<td class="t_c"><?php echo ($vo['author_name']); ?></td>
					<td class="t_c"><?php echo ($vo['app_version']); ?></td>
					<td class="t_c"><?php echo ($vo['app_update_date']); ?></td>
					<td class="t_c"><?php echo ($vo['app_downloads']); ?></td>
					<td class="t_c"><?php echo ($vo['app_score_average']); ?></td>
					<td class="t_c"><?php echo ($vo['app_isrecommon']); ?></td>
					<td class="t_c"><input type="text" class="tiny" id="s" value="" onblur="toSort();" size="5" /></td>
					<td class="t_c">
						<a href="<?php echo U('Admin://Apps/appEdit?id='.$vo['app_id']);?>">
						   <img class="operator" src="../Public/images/icon_edit.gif" alt="编辑" />
						</a>
						<a href="javascript:void(0)" onclick="apps_del_one(<?php echo ($vo['app_id']); ?>)" >
							<img class="operator" src="../Public/images/icon_del.gif" alt="删除" />
						</a>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>

		</tbody>

	</table>

</div>

</form>
<div class="page" style="clear:both;">
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
//	var url = '<?php echo U("Admin://Apps/appsDel?id=");?>' + id;
//	confirm('确定要删除所选中的信息吗？','window.location.href="'+url+'"');
//}
//function apps_del()
//{
//	var flag = 0;
//	$('input:checkbox[name="id[]"]:checked').each(
//		function(i)
//		{
//			flag = 1;
//		}
//	);
//	if(flag == 0 )
//	{
//		alert('请选择要删除的数据');
//		return false;
//	}
//	$("form[name='orderForm']").attr('action',"<?php echo U('Admin://Apps/appsDel');?>");
//	confirm('确定要删除所选中的信息吗？','formSubmit(\'orderForm\')');
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