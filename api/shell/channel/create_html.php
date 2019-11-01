<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<title>升级渠道配置文件生成</title>
<link href="style.css" rel="stylesheet" type="text/css">
</HEAD>
	
<body>
<br/>
<table align=center width="60%">
	<tr><td align=left><input type="button" onclick="window.location.href='?do=index'" value="返回列表">&nbsp;&nbsp;
	<input type="button" onclick="window.location.href='?do=add'" value="添加新升级渠道">
	</td></tr>
</table><br/><br/>

<table align=center width="60%">
	<tr><td align=left><font color=black><strong>升级渠道列表<?php echo $strChannel;?></strong></font></td></tr>
	<tr><td align=left>版本号:&nbsp;&nbsp;<?php echo $strVersion;?></td></tr>
</table>
	
<div align="center">

<form action="?do=createSub" method="POST" name="addappinfo" onsubmit="return addinfo();">
	<table border="1"><input type="hidden" id="app_version" name="app_version" value="<?php echo $app_version;?>" >
	<tr><th align=left width="130">版本号: </th><th align=left><input type="text" id="app_version2" name="app_version2" value="<?php echo $app_version;?>" readonly disabled size="20">  <font color=red>*例如：2.1.5</font></th></tr>
	<tr><th align=left width="130">升级简介: </th><th align=left><textarea id="content" name="content" id="content" cols="80" rows="10"><?php echo $strv_content;?></textarea></th></tr>
	<tr><th align=left width="130">密钥: </th><th align=left><input type="password" id="password" name="password" size="10" maxlength="6"></th></tr>
	
	<tr><th align=center colspan=2><input type="submit" value=" 生成配置文件 ">&nbsp;&nbsp;<input type="button" onclick="window.location.href='?do=index'" value="返回列表"></th></tr>
	</table>
</form>

<script>
function addinfo(){
	if(document.getElementById("app_version").value ==""){
		alert("请输入版本号！");document.getElementById("app_version").focus();return false;
	}
	
	if(document.getElementById("content").value ==""){
		alert("请输入升级简介！");document.getElementById("content").focus();return false;
	}
	
	if(document.getElementById("password").value ==""){
		alert("请输入密钥！");document.getElementById("password").focus();return false;
	}
}
</script>	
</div>
</body>