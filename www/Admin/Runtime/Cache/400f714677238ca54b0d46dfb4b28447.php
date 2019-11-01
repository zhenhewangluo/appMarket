<?php if (!defined('THINK_PATH')) exit();?><div class="headbar">
	<div class="position"><span>用户</span><span>></span><span>用户管理</span><span>></span><span>用户列表</span></div>
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
		<a href="javascript:;"><button class="operating_btn" type="button" onclick="window.location='<?php echo U("Admin://User/userEdit");?>'"><span class="addition">添加会员</span></button></a>
		<a href="javascript:void(0)" onclick="selectAll('id[]')"><button class="operating_btn" type="button"><span class="sel_all">全选</span></button></a>
		<a href="javascript:void(0)" onclick="delModel({form:'user_list',msg:'确定要删除所选中的会员吗？<br />删除的会员可以从回收站找回。'})"><button class="operating_btn" type="button"><span class="delete">批量删除</span></button></a>
		<a href="javascript:;"><button class="operating_btn" type="button" onclick="window.location='<?php echo U("Admin://User/userRecycleList");?>'"><span class="recycle">回收站</span></button></a>
		<a href="javascript:void(0)" onclick="filter()"><button class="operating_btn" type="button"><span class="remove">筛选</span></button></a>
<script type="text/javascript">

</script>
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
<form action="<?php echo U('Admin://User/userDel');?>" method="post" name="user_list" onsubmit="return checkboxCheck('id[]','尚未选中任何记录！')">
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
						<a href="<?php echo U("Admin://User/userEdit?uid=".$vo['id']);?>"">
						   <img class="operator" src="../Public/images/icon_edit.gif" alt="编辑" />
						</a>
						<a href="javascript:void(0)" onclick="user_del_one(<?php echo ($vo['id']); ?>)" >
							<img class="operator" src="../Public/images/icon_del.gif" alt="删除" />
						</a>
					</td>
				</tr><?php endforeach; endif; else: echo "" ;endif; ?>
		</tbody>
	</table>
</div>
<?php echo ($page); ?>
</form>
<script language="javascript">
<!--
var js_group = {};
var tpl_group = '<table style="width:250px" class="form_table"><tr><th>会员等级：</th><td><select class="auto" id="removeto">{foreach:items=$group key=$key item=$value}<option value=<?php echo ($key); ?>><?php echo ($value); ?></option>{/foreach}</select></td></tr>'+
				'	<tr><th>积分：</th><td><input class="tiny" type="text" name="point" value="" /></td></tr>'+
				'</table>';
var content_filter = {};
var tpl_filter =	'<div class="pop_win clearfix" style="width:600px;padding:5px"><form name="form_filter" action="{url:/member/member_filter}" method="post"></form>'+
					'<table class="form_table"><col width="100px" /><col /><col width="150px" /><tfoot name="filter">'+
					'		<tr name="menu"><td>添加筛选条件：</td>'+
					'		<td><select class="auto" name="requirement" onchange="addoption()">'+
					'				<option value="c">请选择</option>'+
					'				<option value="group">会员等级</option>'+
					'				<option value="username">用户名</option>'+
					'				<option value="truename">姓名</option>'+
					'				<option value="mobile">手机</option>'+
					'				<option value="telephone">固定电话</option>'+
					'				<option value="email">Email</option>'+
					'				<option value="zip">邮编</option>'+
					'				<option value="sex">性别</option>'+
					'				<option value="point">经验值</option>'+
					'				<option value="regtime">注册日期</option>'+
					'			</select></td>'+
					'		<td><a class="blue" href="javascript:void(0)" onclick="del_all_option()" >删除所有筛选条件</a></td>'+
					'	</tr></tfoot>'+
					'</table>';
var tpl_option = new Array();
tpl_option['group'] =	'	<tr name="group">'+
						'		<td>会员等级</td>'+
						'		<td><select class="auto" name="group_key"><option value="eq">等于</option><option value="neq">不等于</option></select><select class="auto" name="group_value">{foreach:items=$group key=$key item=$value}<option value=<?php echo ($key); ?>><?php echo ($value); ?></option>{/foreach}</select></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
tpl_option['username'] ='	<tr name="username">'+
						'		<td>用户名</td>'+
						'		<td><select class="auto" name="username_key"><option value="eq">等于</option><option value="contain">包含</option></select><input class="middle" type="text" name="username_value" /></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
tpl_option['truename'] ='	<tr name="truename">'+
						'		<td>姓名</td>'+
						'		<td><select class="auto" name="truename_key"><option value="eq">等于</option><option value="contain">包含</option></select><input class="middle" type="text" name="truename_value" /></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
tpl_option['mobile'] =	'	<tr name="mobile">'+
						'		<td>手机</td>'+
						'		<td><select class="auto" name="mobile_key"><option value="eq">等于</option><option value="contain">包含</option></select><input class="middle" type="text" name="mobile_value" /></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
tpl_option['telephone'] ='	<tr name="telephone">'+
						'		<td>固定电话</td>'+
						'		<td><select class="auto" name="telephone_key"><option value="eq">等于</option><option value="contain">包含</option></select><input class="middle" type="text" name="telephone_value" /></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
tpl_option['email']	=	'	<tr name="email">'+
						'		<td>Email</td>'+
						'		<td><select class="auto" name="email_key"><option value="eq">等于</option><option value="contain">包含</option></select<input class="middle" type="text" name="email_value" />></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
tpl_option['zip']	=	'	<tr name="zip">'+
						'		<td>邮编</td>'+
						'		<td><select class="auto" name="zip_key"><option value="eq">等于</option><option value="contain">包含</option></select><input class="middle" type="text" name="zip_value" /></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
tpl_option['sex']	=	'	<tr name="sex">'+
						'		<td>性别</td>'+
						'		<td><select class="auto" name="sex"><option value="-1">请选择</option><option value="1">男</option><option value="2">女</option><option value="9">保密</option></select></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
tpl_option['point']	=	'	<tr name="point">'+
						'		<td>经验值</td>'+
						'		<td><select class="auto" name="point_key"><option value="gt">大于</option><option value="lt">小于</option><option value="eq">等于</option></select><input class="middle" type="text" name="point_value" /></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
tpl_option['regtime'] =	'	<tr name="regtime">'+
						'		<td>注册日期</td>'+
						'		<td>开始 <input class="small" type="text" name="regtimeBegin" onfocus="WdatePicker()" /> - 截止 <input class="small" type="text" name="regtimeEnd" onfocus="WdatePicker()" /></td>'+
						'		<td><img class="operator" src="{skin:images/admin/icon_del.gif}" alt="删除" onclick="del_option(this)" /></td>'+
						'	</tr>';
function remove()
{
	art.dialog({
		id: 'remove',
		title: '修改等级',
		height: '80px',
		width: '250px',
		border: false,
		content: js_group,
		tmpl: tpl_group,
		yesFn:function(){
			$(":input[name='move_group']").attr("value",$("#removeto option:selected").attr("value"));
			$(":input[name='move_point']").attr("value",$(":input[name='point']").val());
			$("form[name='member_list']").attr("action","{url:/member/member_remove/}");
			$("form[name='member_list']").submit();
			return false;
		},
		noFn: true
	});
}
function filter()
{
	art.dialog({
		id: 'filter',
		title: '筛选',
		width: '480px',
		border: false,
		content: content_filter,
		tmpl: tpl_filter,
		yesFn:function(){
			var obj = $("select[name='requirement'] option");
			var queryurl = '';
			for (var i=1;i<obj.length ;i++)
			{
				if ($(obj[i]).attr('disabled')==true)
				{
					switch ($(obj[i]).val())
					{
						case 'group':
							queryurl += 'group_key='+$("select[name='group_key']").val()+'&group_value='+$("select[name='group_value']").val()+'&';
							break;
						case 'username':
							queryurl += 'username_key='+$("select[name='username_key']").val()+'&username_value='+$(":input[name='username_value']").val()+'&';
							break;
						case 'truename':
							queryurl += 'truename_key='+$("select[name='truename_key']").val()+'&truename_value='+$(":input[name='truename_value']").val()+'&';
							break;
						case 'mobile':
							queryurl += 'mobile_key='+$("select[name='mobile_key']").val()+'&mobile_value='+$(":input[name='mobile_value']").val()+'&';
							break;
						case 'telephone':
							queryurl += 'telephone_key='+$("select[name='telephone_key']").val()+'&telephone_value='+$(":input[name='telephone_value']").val()+'&';
							break;
						case 'email':
							queryurl += 'email_key='+$("select[name='email_key']").val()+'&email_value='+$(":input[name='email_value']").val()+'&';
							break;
						case 'zip':
							queryurl += 'zip_key='+$("select[name='zip_key']").val()+'&zip_value='+$(":input[name='zip_value']").val()+'&';
							break;
						case 'sex':
							queryurl += 'sex='+$("select[name='sex']").val()+'&';
							break;
						case 'point':
							queryurl += 'point_key='+$("select[name='point_key']").val()+'&point_value='+$(":input[name='point_value']").val()+'&';
							break;
						case 'regtime':
							queryurl += 'regtimeBegin='+$(":input[name='regtimeBegin']").val()+'&regtimeEnd='+$(":input[name='regtimeEnd']").val()+'&';
					}
				}
			}
			var tempUrl = '{url:/member/member_filter/@queryurl@}';
			tempUrl     = tempUrl.replace('@queryurl@',queryurl);
			$("form[name='form_filter']").attr('action',tempUrl);
			$("form[name='form_filter']").submit();
			return true;
		},
		noFn:true
	});
}
function del_all_option()
{
	$("div[name='filter']").children().not("div[name='menu']").each(function(i){
		$(this).remove();
	});
	$("select[name='requirement'] option").each(function(i){
		$(this).removeAttr('disabled');
	});
}
function del_option(obj)
{
	var name = $(obj).parent().parent().attr('name');
	$("select[name='requirement'] option[value='"+name+"']").removeAttr('disabled');
	$(obj).parent().parent().remove();
}
function addoption()
{
	var obj = $("select[name='requirement'] option:selected");
	if ($("tr[name='"+obj.val()+"']").length<1)
	{
		$("tfoot[name='filter']").append(tpl_option[obj.val()]);
	}
	obj.attr('disabled',true);
	$("select[name='requirement'] option:selected").removeAttr('selected');
}

function user_del_one(id)
{
	var url = '<?php echo U("Admin://User/userDel?id=");?>' + id;
	confirm('确定要删除所选中的信息吗？','window.location.href="'+url+'"');
}
//-->
</script>