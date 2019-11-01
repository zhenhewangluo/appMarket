<?php if (!defined('THINK_PATH')) exit();?><div class="headbar">
	<div class="position"><span>应用</span><span>></span><span>应用管理</span><span>></span><span>应用列表</span><span>></span><span>回收站</span></div>
	<div class="operating">
		<div class="search f_r">
			<form name="serachuser" action="<?php echo U('Admin://Common/searchApp');?>" method="get">
			{__NOTOKEN__}
			<input type="hidden" name="is_visible" value="0" />
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
		<a href="javascript:;"><button class="operating_btn" onclick="window.location='<?php echo U("Admin://Apps/appList");?>'"><span class="return">返回列表</span></button></a>
		<a href="javascript:void(0)" onclick="selectAll('id[]')"><button class="operating_btn" type="button"><span class="sel_all">全选</span></button></a>
		<a href="javascript:void(0)" onclick="apps_recycle_del()"><button class="operating_btn" type="button"><span class="delete">彻底删除</span></button></a>
		<a href="javascript:void(0)" onclick="apps_recycle_restore()"><button class="operating_btn"><span class="recover">还原</span></button></a>
	</div>
<!--	<div class="searchbar">
		<form action="{url:/}" method="get" name="goods_list">
			<input type='hidden' name='controller' value='goods' />
			<input type='hidden' name='action' value='goods_recycle_list' />
			<select class="auto" name="category_id">
				<option value="">选择分类</option>
				{query:name=category}
				<option value="<?php echo ($item['id']); ?>" {if:$category_id==$item['id']}selected{/if}><?php echo ($item['name']); ?></option>
				{/query}
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
				<col width="100px" />
				<col width="100px" />
				<col width="100px" />
				<col width="100px" />
				<col width="100px" />
				<col width="100px" />
				<col width="60px" />
				<col width="70px" />
				<thead>
					<tr role="head">
						<th class="t_c">选择</th>
						<th class="t_c">APP名称</th>
						<th >分类</th>
						<th class="t_c">作者</th>
						<th class="t_c">版本</th>
						<th class="t_c">更新时间</th>
						<th class="t_c">下载次数</th>
						<th>评分</th>
						<th>推荐</th>
						<th>排序</th>
						<th>操作</th>
					</tr>
				</thead>
			</table>
		</div>
	</div>
</div>
<form action="" method="post" name="orderForm">
<div class="content">
	<table id="list_table" class="list_table">
		<col width="30px" />
		<col />
		<col width="100px" />
		<col width="100px" />
		<col width="100px" />
		<col width="100px" />
		<col width="100px" />
		<col width="100px" />
		<col width="100px" />
		<col width="60px" />
		<col width="70px" />
		<tbody>
			<?php if(is_array($applist)): $i = 0; $__LIST__ = $applist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
					<td class="t_c"><input name="id[]" type="checkbox" value="<?php echo ($vo['app_id']); ?>" /></td>
					<td><a href="" target="_blank" title=""></a><?php echo ($vo['app_name']); ?></td>
					<td><?php echo ($vo['app_cate_name']); ?></td>
					<td class="t_c"><?php echo ($vo['author_name']); ?></td>
					<td class="t_c"><?php echo ($vo['app_version']); ?></td>
					<td class="t_c"><?php echo ($vo['app_update_date']); ?></td>
					<td class="t_c"><?php echo ($vo['app_downloads']); ?></td>
					<td><?php echo ($vo['app_score_average']); ?></td>
					<td><?php echo ($vo['app_isrecommon']); ?></td>
					<td><input type="text" class="tiny" id="s" value="" onblur="toSort();" size="5"/></td>
					<td><a href="<?php echo U("Admin://Apps/appEdit?id=".$vo['app_id']);?>""><img class="operator" src="../Public/images/icon_edit.gif" alt="编辑" /></a>
					<a href="javascript:void(0)" onclick="apps_recycle_del_one(<?php echo ($vo['app_id']); ?>)" ><img class="operator" src="../Public/images/icon_del.gif" alt="删除" /></a></td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>

		</tbody>

	</table>

</div>
</form>
<div class="page" style="clear:both;">
<?php echo ($page); ?>
</div>
<script type="text/javascript">
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
function apps_recycle_del_one(id)
{
	var url = '<?php echo U("Admin://Apps/appsRecycleDel?id=");?>' + id;
	confirm('确定要删除所选中的信息吗？','window.location.href="'+url+'"');
}
function apps_recycle_del()
{
	$("form[name='orderForm']").attr('action','<?php echo U("Admin://Apps/appsRecycleDel");?>');
	confirm('确定要彻底删除所选中的信息吗？','formSubmit(\'orderForm\')');
}
function apps_recycle_restore()
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
		alert('请选择要还原的数据');
		return false;
	}
	$("form[name='orderForm']").attr('action','<?php echo U("Admin://Apps/appsRecycleRestore");?>');
	confirm('确定要还原所选中的信息吗？','formSubmit(\'orderForm\')');
}
</script>