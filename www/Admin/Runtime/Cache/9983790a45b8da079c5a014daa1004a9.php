<?php if (!defined('THINK_PATH')) exit();?><div class="headbar">
	<div class="position"><span>用户</span><span>></span><span>用户管理</span><span>></span><span>用户列表</span><span>></span><span>用户回收站</span></div>
	<div class="operating">
		<div class="search f_r">
		<form name="serachuser" action="{url:/}" method="get">
		<input type='hidden' name='controller' value='member' />
		<input type='hidden' name='action' value='member_list' />
			<select class="normal" name="search">
				<option value="u.username" {if:$search=='u.username'}selected{/if}>用户名</option>
				<option value="m.true_name" {if:$search=='m.true_name'}selected{/if}>姓名</option>
				<option value="m.telephone" {if:$search=='m.telephone'}selected{/if}>电话</option>
				<option value="m.mobile" {if:$search=='m.mobile'}selected{/if}>手机</option>
				<option value="u.email" {if:$search=='u.email'}selected{/if}>Email</option>
			</select><input class="small" name="keywords" type="text" value="<?php echo ($keywords); ?>" /><button class="btn" type="submit"><span class="sch">搜 索</span></button>
		</form>
		</div>
		<a href="javascript:;"><button class="operating_btn" onclick="window.location='<?php echo U("Admin://User/userList");?>'"><span class="return">返回列表</span></button></a>
		<a href="javascript:void(0)" onclick="selectAll('id[]')"><button class="operating_btn" type="button"><span class="sel_all">全选</span></button></a>
		<a href="javascript:void(0)" onclick="user_recycle_del()"><button class="operating_btn" type="button"><span class="delete">彻底删除</span></button></a>
		<a href="javascript:void(0)" onclick="user_recycle_restore()"><button class="operating_btn"><span class="recover">还原</span></button></a>
	</div>

	<div class="field">
		<table class="list_table">
			<col width="30px" />
			<col width="90px" />
			<col width="60px" />
			<col width="80px" />
			<col width="40px" />
			<col width="130px"/>
			<col width="60px" />
			<col width="60px" />
			<col width="65px" />
			<thead>
				<tr role="head">
					<th class="t_c">选择</th>
					<th>用户名</th>
					<th>Email</th>
					<th>手机</th>
					<th>QQ</th>
					<th>微博</th>
					<th>地址</th>
					<th>注册日期</th>
					<th>操作</th>
				</tr>
			</thead>
		</table>
	</div>
</div>
<form action="<?php echo U('Admin://User/');?>" method="post" name="user_list" onsubmit="return checkboxCheck('id[]','尚未选中任何记录！')">
<div class="content">
	<input type="hidden" name="move_group" value="" />
	<input type="hidden" name="move_point" value="" />
	<table id="list_table" class="list_table">
			<col width="30px" />
			<col width="90px" />
			<col width="60px" />
			<col width="80px" />
			<col width="40px" />
			<col width="130px"/>
			<col width="60px" />
			<col width="60px" />
			<col width="65px" />
		<tbody>
			<?php if(is_array($userlist)): $i = 0; $__LIST__ = $userlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
					<td class="t_c"><input name="id[]" type="checkbox" value="<?php echo ($vo['id']); ?>" /></td>
					<td><a href="" target="_blank" title=""></a><?php echo ($vo['name']); ?></td>
					<td class="t_c"><?php echo ($vo['email']); ?></td>
					<td class="t_c"><?php echo ($vo['phone']); ?></td>
					<td class="t_c"><?php echo ($vo['qq']); ?></td>
					<td class="t_c"><?php echo ($vo['weburl']); ?></td>
					<td class="t_c"><?php echo ($vo['address']); ?></td>
					<td class="t_c"><?php echo ($vo['registered_time']); ?></td>
					<td class="t_c">
						<a href="<?php echo U("Admin://User/userEdit?id=".$vo['id']);?>"">
						   <img class="operator" src="../Public/images/icon_edit.gif" alt="编辑" />
						</a>
						<a href="javascript:void(0)" onclick="user_recycle_del_one(<?php echo ($vo['id']); ?>)" >
							<img class="operator" src="../Public/images/icon_del.gif" alt="删除" />
						</a>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>
</div>
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
function user_recycle_del_one(id)
{
	var url = '<?php echo U("Admin://User/userRecycleDel?id=");?>' + id;
	confirm('确定要删除所选中的信息吗？','window.location.href="'+url+'"');
}
function user_recycle_del()
{
	$("form[name='user_list']").attr('action','<?php echo U("Admin://User/userRecycleDel");?>');
	confirm('确定要彻底删除所选中的信息吗？','formSubmit(\'user_list\')');
}
function user_recycle_restore()
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
	$("form[name='user_list']").attr('action','<?php echo U("Admin://User/userRecycleRestore");?>');
	confirm('确定要还原所选中的信息吗？','formSubmit(\'user_list\')');
}
</script>