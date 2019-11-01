<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<title>添加-排行榜</title>
<link href="style.css" rel="stylesheet" type="text/css">
</HEAD>
	
<body>
<br/>
<table align=center width="40%">
	<tr><td align=left><input type="button" onclick="window.location.href='?do=index'" value="返回列表">&nbsp;&nbsp;
	<input type="button" onclick="window.location.href='?do=add'" value="添加新应用">
	</td></tr>
</table><br/><br/>


<div align="center">

<form action="?do=addSub" method="POST" name="addappinfo" >
	<table border="1"><input type="hidden" id="app_id" name="app_id" value="<?php echo $row['app_id'];?>" >
	<tr><th align=left width="130">app_id: </th><th align=left><input type="text" id="app_id" name="app_id" value="" >*例如：6000001</th></tr>
	<tr><th align=left width="130">必须版权: </th><th align=left>
		<input type='checkbox' name='copyright' value='1' />必须版权，例如TCL排行里应用 &nbsp;&nbsp;
	</th></tr>
		
	<tr height=20><th colspan=2>&nbsp;</th></tr>
	<tr><th align=left width="130">应用属性: </th><th align=left><font color=red>应用</font></th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='s_w' value='1' />周排行 &nbsp;&nbsp;位置: <select name='s_w_p'><?php echo $options;?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='s_m' value='1' />月排行 &nbsp;&nbsp;位置: <select name='s_m_p'><?php echo $options;?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='s_a' value='1' />所有排行 &nbsp;&nbsp;位置: <select name='s_a_p'><?php echo $options;?></select>
	</th></tr>
	
		
	<tr height=20><th colspan=2>&nbsp;</th></tr>
		
		
	<tr height=20><th colspan=2>&nbsp;</th></tr>
	<tr><th align=left width="130">应用属性: </th><th align=left><font color=red>游戏</font></th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='g_w' value='1' />周排行 &nbsp;&nbsp;位置: <select name='g_w_p'><?php echo $options;?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='g_m' value='1' />月排行 &nbsp;&nbsp;位置: <select name='g_m_p'><?php echo $options;?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='g_a' value='1' />所有排行 &nbsp;&nbsp;位置: <select name='g_a_p'><?php echo $options;?></select>
	</th></tr>
		
	
	<tr height=20><th colspan=2>&nbsp;</th></tr>
		


	<tr height=20><th colspan=2>&nbsp;</th></tr>
	<tr><th align=left width="130">应用属性: </th><th align=left><font color=red>全部</font></th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='a_w' value='1' />周排行 &nbsp;&nbsp;位置: <select name='a_w_p'><?php echo $options;?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='a_m' value='1' />月排行 &nbsp;&nbsp;位置: <select name='a_m_p'><?php echo $options;?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='a_a' value='1' />所有排行 &nbsp;&nbsp;位置: <select name='a_a_p'><?php echo $options;?></select>
	</th></tr>
		
	
	<tr><th align=center colspan=2><input type="submit" value=" 添加 ">&nbsp;&nbsp;<input type="button" onclick="window.location.href='?do=index'" value="返回列表"></th></tr>
	</table>
</form>
<br/><br/><br/><br/>
</div>
</body>