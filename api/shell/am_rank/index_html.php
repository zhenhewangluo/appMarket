<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<title>排行榜</title>
<link href="style.css" rel="stylesheet" type="text/css">
</HEAD>
	
<body>
<br/>
<table align=center width="60%">
	<tr><td align=left><input type="button" onclick="window.location.href='?do=add'" value=" 添加新应用 ">&nbsp;&nbsp;
	</td></tr>
</table><br/><br/>
	

<div align="center">

<table border="1">
<tr>
	<th>app_id</th>
	<th>app_name</th>
	<th>应用 周 位置</th>
	<th>应用 月 位置</th>
	<th>应用 全部 位置</th>
	
	<td width=5 style="background:red">&nbsp;</td>
		
	<th>游戏 周 位置</th>
	<th>游戏 月 位置</th>
	<th>游戏 全部 位置</th>
	
	<td width=5 style="background:red">&nbsp;</td>
		
	<th>所有 周 位置</th>
	<th>所有 月 位置</th>
	<th>所有 全部 位置</th>
	
	<td width=5 style="background:red">&nbsp;</td>
		
	<th>必须版权</th>
	<th>操作</th>
</tr>
<?php
for($i=0; $i<count($arrR); $i++){
	$r = $arrR[$i];
?>
<tr>
	<td title="<?php echo $r['app_name'];?>" style="cursor:hand"> <?php echo $r['app_id'];?> </td>
	<td title="<?php echo $r['app_name'];?>" style="cursor:hand"> <?php echo $r['strname'];?> </td>
	<td><?php echo ($r['s_w'])?"<font color=red>是 &nbsp;&nbsp;".$r['s_w_p']."</font>":"否 &nbsp;&nbsp;0";?></td>
	<td><?php echo ($r['s_m'])?"<font color=red>是 &nbsp;&nbsp;".$r['s_m_p']."</font>":"否 &nbsp;&nbsp;0";?></td>
	<td><?php echo ($r['s_a'])?"<font color=red>是 &nbsp;&nbsp;".$r['s_a_p']."</font>":"否 &nbsp;&nbsp;0";?></td>
	
	<td width=5 style="background:red">&nbsp;</td>
		
	<td><?php echo ($r['g_w'])?"<font color=red>是 &nbsp;&nbsp;".$r['g_w_p']."</font>":"否 &nbsp;&nbsp;0";?></td>
	<td><?php echo ($r['g_m'])?"<font color=red>是 &nbsp;&nbsp;".$r['g_m_p']."</font>":"否 &nbsp;&nbsp;0";?></td>
	<td><?php echo ($r['g_a'])?"<font color=red>是 &nbsp;&nbsp;".$r['g_a_p']."</font>":"否 &nbsp;&nbsp;0";?></td>
	
	<td width=5 style="background:red">&nbsp;</td>
		
	<td><?php echo ($r['a_w'])?"<font color=red>是 &nbsp;&nbsp;".$r['a_w_p']."</font>":"否 &nbsp;&nbsp;0";?></td>
	<td><?php echo ($r['a_m'])?"<font color=red>是 &nbsp;&nbsp;".$r['a_m_p']."</font>":"否 &nbsp;&nbsp;0";?></td>
	<td><?php echo ($r['a_a'])?"<font color=red>是 &nbsp;&nbsp;".$r['a_a_p']."</font>":"否 &nbsp;&nbsp;0";?></td>
	
	<td width=5 style="background:red">&nbsp;</td>
		
	<td><?php echo $r['copyright']?"<font color=red>是</font>":"否";?></td>

	<td>
	<a href="?do=upt&app_id=<?php echo $r['app_id'];?>" >修改</a>&nbsp;&nbsp;
	<a href="?do=del&app_id=<?php echo $r['app_id'];?>" onclick="if(confirm('您确定要删除吗?')) return ture; else return false;">删除</a>&nbsp;&nbsp;
		</td>
</tr>
<?php }?>
</table>
</div>
	</body>