<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<HTML>
<HEAD>
<title>升级渠道列表</title>
<link href="style.css" rel="stylesheet" type="text/css">
</HEAD>
	
<body>
<br/>
<table align=center width="70%">
	<tr><td align=left><input type="button" onclick="window.location.href='?do=add'" value="添加新升级渠道">&nbsp;&nbsp;
	<input type="button" onclick="window.location.href='?do=create'" value="生成配置文件">
	</td></tr>
</table><br/><br/>
	
<table align=center width="70%">
	<tr><td align=left><font color=black><strong>升级渠道列表<?php echo $strChannel;?></strong></font></td></tr>
	<tr><td align=left>版本号:&nbsp;&nbsp;<?php echo $strVersion;?></td></tr>
</table>
<div align="center">

<table border="1">
<tr>
	<th>渠道号</th>
	<th>渠道名(英文)</th>
	<th>内部版本</th>
	<th>版本号</th>
	<th>强制升级</th>
	<th>need_english</th>
	<th>device_list</th>
	<th>升级包名</th>
	<th>创建时间</th>
	<th>操作</th>
</tr>
<?php
for($i=0; $i<count($arrR); $i++){
?>
<tr>
	<td><?php echo $arrR[$i]['name']."_".$arrR[$i]['channel_id'];?></td>
	<td><?php echo $arrR[$i]['channel_name'];?></td>
	<td><?php echo $arrR[$i]['current_version'];?></td>
	<td><?php echo $arrR[$i]['app_version'];?></td>
	<td><?php echo $arrR[$i]['need_upgrade'];?></td>
	<td><?php echo $arrR[$i]['need_english'];?></td>
	<td><?php echo $arrR[$i]['device_list'];?></td>
	<td><?php echo "<a href='".$REPO_ROOT.$arrR[$i]['apk_path']."'>".$arrR[$i]['apk_path']."</a>";?></td>
	<td><?php echo $arrR[$i]['createtime'];?></td>
	<td><a href="?do=del&id=<?php echo $arrR[$i]['id'];?>" onclick="if(confirm('您确定要删除吗?')) return ture; else return false;">删除</a>&nbsp;&nbsp;</td>
</tr>
<?php }?>
<tr><td colspan=10><font color=red>备注：
	<br/>1.升级包名生成规则： xxxx_版本号_渠道名(英文)_commonpkg.apk
	<br/>2.每次进行操作后必须生成配置文件，否则修改不生效
	</font></td></tr>
</table>
</div>
	</body>