<?php if (!defined('THINK_PATH')) exit();?><table class="table_app_list">
	<col />
	<col width="190px" />
	<thead>
		<tr>
			<th class="t_c">名称</th>
			<th class="t_c">下载时间</th>
			<th class="t_c">更新时间</th>
			<th class="t_c">最新版本</th>
		</tr>
	</thead>
	<tbody>
	<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><tr>
			<td class="t_l"><a href="<?php echo U('AppStore://App-Apps/detail?aid=' . $vo['app_id']);?>"  title="<?php echo ($vo['app_name']); ?>"><?php echo (mb_substr($vo['app_name'],0,20,'UTF-8')); ?></a></td>
			<td class="t_c"><?php echo ($vo['create_time']); ?></td>
			<td class="t_c"><?php echo ($vo['app_update_date']); ?></td>
			<td class="t_c"><?php echo ($vo['app_version']); ?></td>
		</tr><?php endforeach; endif; else: echo "" ;endif; ?>
</tbody>
</table>
<?php if((trim($page))  !=  ""): ?><div class="page" style="clear:both;">
		<div class="page_list">
			<?php echo ($page); ?>
		</div>
	</div><?php endif; ?>