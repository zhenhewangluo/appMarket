<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<title>升级渠道添加</title>
<link href="style.css" rel="stylesheet" type="text/css">
</HEAD>
	
<body>
<br/>
<table align=center width="50%">
	<tr><td align=left><input type="button" onclick="window.location.href='?do=index'" value="返回列表">&nbsp;&nbsp;
	<input type="button" onclick="window.location.href='?do=create'" value="生成配置文件">
	</td></tr>
</table><br/><br/>
		
<div align="center">
<form action="?do=addSub" method="POST" name="addappinfo" enctype="multipart/form-data" onsubmit="return addinfo();">
	<table border="1">
	<tr><th align=left width="130">渠道号: </th><th align=left><select name="channel_id"><?php echo $options;?></select></th></tr>
	<tr><th align=left width="130">渠道名(英文): </th><th align=left><input type="text" id="channel_name" name="channel_name" value="" size="20"> <font color=red>*例如：public_1</font></th></tr>
	<tr><th align=left width="130">内部版本: </th><th align=left><input type="text" id="current_version" name="current_version" value="" size="20"> <font color=red>*例如：2011011401</font></th></tr>
	<tr><th align=left width="130">版本号: </th><th align=left><input type="text" id="app_version" name="app_version" value="" size="20">  <font color=red>*例如：2.1.5</font></th></tr>
	<tr><th align=left width="130">强制升级: </th><th align=left><select name='need_upgrade'><option value='false'>false</option><option value='true'>true</option></select></th></tr>
	<tr><th align=left width="130">need_english: </th><th align=left><select name='need_english'><option value='true'>true</option><option value='false'>false</option></select></th></tr>
	<tr><th align=left width="130">device_list: </th><th align=left><input type="text" id="device_list" name="device_list" value="10001,10002,10004,71,2,25" size="40">  <font color=red>*例如：10001,10002,10004,71,2,25</font></th></tr>
	<tr><th align=left width="130">上传升级包: </th><th align=left><input type="file" id="apk_path" name="apk_path" size='40'>  </th></tr>
	<tr><th align=center colspan=2><input type="submit" value=" 添加 ">&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" onclick="window.location.href='?do=index'" value="返回列表"></th></tr>
	</table>
</form>

<script>
function addinfo(){
	if(document.getElementById("channel_name").value ==""){
		alert("请输入渠道名(英文)！");document.getElementById("channel_name").focus();return false;
	}
	if(document.getElementById("current_version").value ==""){
		alert("请输入内部版本！");document.getElementById("current_version").focus();return false;
	}
	if(document.getElementById("app_version").value ==""){
		alert("请输入版本号！");document.getElementById("app_version").focus();return false;
	}
	
	if(document.getElementById("device_list").value ==""){
		alert("请输入device_list！");document.getElementById("device_list").focus();return false;
	}
	if(document.getElementById("apk_path").value ==""){
		alert("请上传升级包！");document.getElementById("apk_path").focus();return false;
	}
}
</script>	
</div>
</body>