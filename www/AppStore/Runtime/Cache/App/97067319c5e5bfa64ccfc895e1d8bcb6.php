<?php if (!defined('THINK_PATH')) exit();?><?php $listCount = count($list);;?>
<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php if($key%2 == 0): ?><div class="row"><?php endif; ?>
<div class="item">
	<div class="icon">
		<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>">
			<img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['icon']); ?>" style="width:78px; height:78px;" alt="Moon+ Reader Pro" />
		</a>
	</div>
	<div class="datas">
		<div class="title"><a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>"><?php echo ($vo['app_name']); ?></a></div>
		<div class="sub"><a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>"><?php echo ($vo['author_name']); ?></a></div>
		<div class="intro"><?php echo showCutStr($vo['app_desc'], 65);?></div>
	</div>
	<div class="clear"></div>
	<div class="stars"><div class="RatingStars5 rating-9"></div></div>
	<a class="button_normal" href="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['apk_path']); ?>" onclick="getApp('<?php echo ($vo['app_id']); ?>');<?php echo ($returnFlase); ?>">安装</a>
	<div class="clear"></div>
</div>
<?php if((($key+1)%2 == 0) OR (($key+1) == $listCount)): ?></div><?php endif; ?><?php endforeach; endif; else: echo "" ;endif; ?>
<table class="pager" style="width:460px" cellspacing="0">
	<tr>
		<td class="cl"></td>
		<td style="text-align:left;">跳转至:</td>
		<td style="text-align:left;">
			<?php echo ($page); ?>
		</td>
		<td class="cr"></td>
	</tr>
</table>
<div class="clear"></div>