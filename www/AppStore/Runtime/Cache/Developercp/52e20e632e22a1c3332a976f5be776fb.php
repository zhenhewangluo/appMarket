<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="../Public/css/developer.css">
<script type="text/javascript">
/*
EASY TABS 1.2 Produced and Copyright by Koller Juergen
www.kollermedia.at | www.austria-media.at
Need Help? http:/www.kollermedia.at/archive/2007/07/10/easy-tabs-12-now-with-autochange
You can use this Script for private and commercial Projects, but just leave the two credit lines, thank you.
*/

//EASY TABS 1.2 - MENU SETTINGS
//Set the id names of your tablinks (without a number at the end)
var tablink_idname = new Array("tablink")
//Set the id names of your tabcontentareas (without a number at the end)
var tabcontent_idname = new Array("appList") 
//Set the number of your tabs in each menu
var tabcount = new Array("3")
//Set the Tabs wich should load at start (In this Example:Menu 1 -> Tab 2 visible on load, Menu 2 -> Tab 5 visible on load)
var loadtabs = new Array("1")  
//Set the Number of the Menu which should autochange (if you dont't want to have a change menu set it to 0)
var autochangemenu = 0;
//the speed in seconds when the tabs should change
var changespeed = 3;
//should the autochange stop if the user hover over a tab from the autochangemenu? 0=no 1=yes
var stoponhover = 0;
//END MENU SETTINGS


/*Swich EasyTabs Functions - no need to edit something here*/
function easytabs(menunr, active) {if (menunr == autochangemenu){currenttab=active;}if ((menunr == autochangemenu)&&(stoponhover==1)) {stop_autochange()} else if ((menunr == autochangemenu)&&(stoponhover==0))  {counter=0;} menunr = menunr-1;for (i=1; i <= tabcount[menunr]; i++){document.getElementById(tablink_idname[menunr]+i).className='tab'+i;document.getElementById(tabcontent_idname[menunr]+i).style.display = 'none';}document.getElementById(tablink_idname[menunr]+active).className='tab'+active+' tabactive';document.getElementById(tabcontent_idname[menunr]+active).style.display = 'block';}var timer; counter=0; var totaltabs=tabcount[autochangemenu-1];var currenttab=loadtabs[autochangemenu-1];function start_autochange(){counter=counter+1;timer=setTimeout("start_autochange()",1000);if (counter == changespeed+1) {currenttab++;if (currenttab>totaltabs) {currenttab=1}easytabs(autochangemenu,currenttab);restart_autochange();}}function restart_autochange(){clearTimeout(timer);counter=0;start_autochange();}function stop_autochange(){clearTimeout(timer);counter=0;}

window.onload=function(){
var menucount=loadtabs.length; var a = 0; var b = 1; do {easytabs(b, loadtabs[a]);  a++; b++;}while (b<=menucount);
if (autochangemenu!=0){start_autochange();}
}
</script>
<script type="text/javascript">
$(document).ready(function(){
  $("#tablink2").click(function(){
  $('#appList2').load('<?php echo U("Developercp-Apps/appauditList");?>');
  });

$("#tablink3").click(function(){
  $('#appList3').load('<?php echo U("Developercp-Apps/appauditfailList");?>');
  });
});
</script>

<div class="content_box">
<div class="position"><span>开发者用户中心</span><span>></span><span>APK管理</span><span>></span><span>审核中应用列表</span></div>
<!-- layout::Layout:developerbar::0 -->	
<div class="menu">
<h1 style="margin:20px 0 20px 20px; display:block; color:#53A6D1;">APK管理 </h1>
<ul>
<li><a href="#" onmouseover="return false;" onfocus="easytabs('1', '1');" onclick="easytabs('1', '1');"  title="" id="tablink1">待审核</a></li>
<li><a href="#" onmouseover="return false;" onfocus="easytabs('1', '2');" onclick="easytabs('1', '2');"  title="" id="tablink2">审核通过 </a></li>
<li><a href="#" onmouseover="return false;" onfocus="easytabs('1', '3');" onclick="easytabs('1', '3');"  title="" id="tablink3">审核未通过 </a></li>
</ul>
</div>

<div class="form_content" id="appList1">
<form action="<?php echo U('Admin://User/userDel');?>" method="post" name="orderForm">
<div class="content" style="width:100%; padding-top:0;">
	<table id="list_table" class="list_table">
		<col width="40px" />
		<col width="130px" />
		<col width="130px" />
		<col width="60px" />
		<col width="130px" />
		<col width="50px" />
		<col width="50px" />
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
			<th class="t_c">编辑</th>
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
<div class="page" style="clear:both; margin-top:10px;">
<?php echo ($page); ?>
</div>
	</div>
    
    <div class="form_content" id="appList2">

	</div>
    
    <div class="form_content" id="appList3">

	</div>
    
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