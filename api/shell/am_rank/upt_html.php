<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<title>修改-排行榜</title>
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

<form action="?do=uptSub" method="POST" name="addappinfo" >
	<table border="1"><input type="hidden" id="id" name="id" value="<?php echo $row['id'];?>" ><input type="hidden" id="old_app_id" name="old_app_id" value="<?php echo $row['app_id'];?>" >
	<tr><th align=left width="130">app_id: </th><th align=left><input type="text" id="app_id" name="app_id" value="<?php echo $row['app_id'];?>" ></th></tr>
	<tr><th align=left width="130">必须版权: </th><th align=left>
		<input type='checkbox' name='copyright' value='1' <?php if($row['copyright']) echo 'checked';?>/>必须版权，例如TCL排行里应用 &nbsp;&nbsp;
	</th></tr>
		
	<tr height=20><th colspan=2>&nbsp;</th></tr>
	<tr><th align=left width="130">应用属性: </th><th align=left><font color=red>应用</font></th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='s_w' value='1' <?php if($row['s_w']) echo 'checked';?>/>周排行 &nbsp;&nbsp;位置: <select name='s_w_p'><?php $arr = $arrOptions;if($row['s_w']){$arr[$row['s_w_p']-1]="<option value='".$row['s_w_p']."' selected>"."第 ".$row['s_w_p']." 位"."</option>";echo @implode(" ",$arr);}else{echo "<option value='0'>"."  "."</option>".@implode(" ",$arr);}?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='s_m' value='1' <?php if($row['s_m']) echo 'checked';?>/>月排行 &nbsp;&nbsp;位置: <select name='s_m_p'><?php $arr = $arrOptions;if($row['s_m']){$arr[$row['s_m_p']-1]="<option value='".$row['s_m_p']."' selected>"."第 ".$row['s_m_p']." 位"."</option>";echo @implode(" ",$arr);}else{echo "<option value='0'>"."  "."</option>".@implode(" ",$arr);}?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='s_a' value='1' <?php if($row['s_a']) echo 'checked';?>/>所有排行 &nbsp;&nbsp;位置: <select name='s_a_p'><?php $arr = $arrOptions;if($row['s_a']){$arr[$row['s_a_p']-1]="<option value='".$row['s_a_p']."' selected>"."第 ".$row['s_a_p']." 位"."</option>";echo @implode(" ",$arr);}else{echo "<option value='0'>"."  "."</option>".@implode(" ",$arr);}?></select>
	</th></tr>
	
		
	<tr height=20><th colspan=2>&nbsp;</th></tr>
		
		
	<tr height=20><th colspan=2>&nbsp;</th></tr>
	<tr><th align=left width="130">应用属性: </th><th align=left><font color=red>游戏</font></th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='g_w' value='1' <?php if($row['g_w']) echo 'checked';?>/>周排行 &nbsp;&nbsp;位置: <select name='g_w_p'><?php $arr = $arrOptions;if($row['g_w']){$arr[$row['g_w_p']-1]="<option value='".$row['g_w_p']."' selected>"."第 ".$row['g_w_p']." 位"."</option>";echo @implode(" ",$arr);}else{echo "<option value='0'>"."  "."</option>".@implode(" ",$arr);}?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='g_m' value='1' <?php if($row['g_m']) echo 'checked';?>/>月排行 &nbsp;&nbsp;位置: <select name='g_m_p'><?php $arr = $arrOptions;if($row['g_m']){$arr[$row['g_m_p']-1]="<option value='".$row['g_m_p']."' selected>"."第 ".$row['g_m_p']." 位"."</option>";echo @implode(" ",$arr);}else{echo "<option value='0'>"."  "."</option>".@implode(" ",$arr);}?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='g_a' value='1' <?php if($row['g_a']) echo 'checked';?>/>所有排行 &nbsp;&nbsp;位置: <select name='g_a_p'><?php $arr = $arrOptions;if($row['g_a']){$arr[$row['g_a_p']-1]="<option value='".$row['g_a_p']."' selected>"."第 ".$row['g_a_p']." 位"."</option>";echo @implode(" ",$arr);}else{echo "<option value='0'>"."  "."</option>".@implode(" ",$arr);}?></select>
	</th></tr>
		
	
	<tr height=20><th colspan=2>&nbsp;</th></tr>
		


	<tr height=20><th colspan=2>&nbsp;</th></tr>
	<tr><th align=left width="130">应用属性: </th><th align=left><font color=red>全部</font></th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='a_w' value='1' <?php if($row['a_w']) echo 'checked';?>/>周排行 &nbsp;&nbsp;位置: <select name='a_w_p'><?php $arr = $arrOptions;if($row['a_w']){$arr[$row['a_w_p']-1]="<option value='".$row['a_w_p']."' selected>"."第 ".$row['a_w_p']." 位"."</option>";echo @implode(" ",$arr);}else{echo "<option value='0'>"."  "."</option>".@implode(" ",$arr);}?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='a_m' value='1' <?php if($row['a_m']) echo 'checked';?>/>月排行 &nbsp;&nbsp;位置: <select name='a_m_p'><?php $arr = $arrOptions;if($row['a_m']){$arr[$row['a_m_p']-1]="<option value='".$row['a_m_p']."' selected>"."第 ".$row['a_m_p']." 位"."</option>";echo @implode(" ",$arr);}else{echo "<option value='0'>"."  "."</option>".@implode(" ",$arr);}?></select>
	</th></tr>
	<tr><th align=left width="130"></th><th align=left>
		<input type='checkbox' name='a_a' value='1' <?php if($row['a_a']) echo 'checked';?>/>所有排行 &nbsp;&nbsp;位置: <select name='a_a_p'><?php $arr = $arrOptions;if($row['a_a']){$arr[$row['a_a_p']-1]="<option value='".$row['a_a_p']."' selected>"."第 ".$row['a_a_p']." 位"."</option>";echo @implode(" ",$arr);}else{echo "<option value='0'>"."  "."</option>".@implode(" ",$arr);}?></select>
	</th></tr>
		
	
	<tr><th align=center colspan=2><input type="submit" value=" 修改 ">&nbsp;&nbsp;<input type="button" onclick="window.location.href='?do=index'" value="返回列表"></th></tr>
	</table>
</form>
<br/><br/><br/><br/>
</div>
</body>