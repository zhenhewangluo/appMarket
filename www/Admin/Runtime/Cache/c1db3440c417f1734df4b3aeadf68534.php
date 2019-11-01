<?php if (!defined('THINK_PATH')) exit();?><div class="headbar">
	<div class="position"><span>应用</span><span>></span><span>应用分类</span><span>></span><span>分类列表</span></div>
	<div class="operating">
		<div class="search f_r">
			<form name="serachuser" action="{url:/}" method="get">
			<input type='hidden' name='controller' value='goods' />
			<input type='hidden' name='action' value='goods_list' />
			<select class="auto" name="search">
				<option value="goods.name" {if:$search=='goods.name'}selected{/if}>分类名</option>
				<option value="c.name" {if:$search=='c.name'}selected{/if}>分类</option>
			</select>
			<input class="small" name="keywords" type="text" value="<?php echo ($keywords); ?>" /><button class="btn" type="submit"><span class="sch">搜 索</span></button>
			</form>
		</div>
		<a href="javascript:void(0);">
			<button class="operating_btn" type="button" onclick="window.location='<?php echo U("Admin://Apps/appCateAdd");?>'">
					<span class="addition">添加分类</span>
			</button>
		</a>
	</div>
<!--	<div class="searchbar">
		<form action="{url:/}" method="get" name="goods_list">
			<input type='hidden' name='controller' value='goods' />
			<input type='hidden' name='action' value='goods_list' />
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
				<col width="30px" />
				<col width="100px"/>
				<col />
				<col width="100px" />
				<col width="100px" />
				<col width="100px" />
				<col width="60px" />
				<col width="70px" />
				<thead>
					<tr role="head">
						<th class="t_c">选择</th>
						<th class="t_c">ID</th>
						<th >名称</th>
						<th class="t_c">描述</th>
						<th class="t_c">父类</th>
						<th class="t_c">order</th>
						<th class="t_c">可见</th>
						<th>排序</th>
						<th>编辑</th>
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
		<col width="30px" />
		<col width="100px"/>
		<col />
		<col width="100px" />
		<col width="100px" />
		<col width="100px" />
		<col width="60px" />
		<col width="70px" />		
		<tbody>
			<?php if(is_array($applist)): $i = 0; $__LIST__ = $applist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
					<td class="t_c"><input name="id[]" type="checkbox" value="<?php echo ($vo['id']); ?>" /></td>
					<td><a href="" target="_blank" title=""></a><?php echo ($vo['id']); ?></td>
					<td><a href="" target="_blank" title=""></a><?php echo ($vo['name']); ?></td>
					<td><?php echo ($vo['description']); ?></td>
					<td class="t_c"><?php echo ($vo['parent_id']); ?></td>
					<td class="t_c"><?php echo ($vo['order']); ?></td>
					<td class="t_c"><?php echo ($vo['visible']); ?></td>
					<td><input type="text" class="tiny" id="s" value="" onblur="toSort();" size="5"/></td>
					<td><a href="<?php echo U("Admin://Apps/appCateEdit?id=".$vo['id']);?>"><img class="operator" src="../Public/images/icon_edit.gif" alt="编辑" /></a>
					<a href="javascript:void(0)" onclick="apps_cate_del_one(<?php echo ($vo['id']); ?>)" ><img class="operator" src="../Public/images/icon_del.gif" alt="删除" /></a></td>
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
function apps_cate_del_one(id)
{
	//var url = '<?php echo U("Admin://Apps/appsCateDel?id=");?>' + id;
	//confirm('确定要删除所选中的信息吗？','window.location.href="'+url+'"');
}
</script>