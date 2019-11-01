<?php if (!defined('THINK_PATH')) exit();?><link href="../Public/css/jcarousel.css" type="text/css" rel="stylesheet" />
<link href="../Public/css/pagination.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../Public/js/jquery.pagination.js"></script>
<script type="text/javascript" src="../Public/js/jquery.jcarousel.min.js"></script>
<script type="text/javascript" src="../Public/js/shop.js"></script>
<script type="text/javascript">
    $(document).ready(initShop);
</script>
<div class="breadcrumbBar">
    <div class="breadcrumb">
		您的位置： <a href="/">***</a> &raquo; <a href ="<?php echo U('App-Apps/index');?>">应用中心</a>
    </div>
    <div class="clear"></div>
</div>
<div class="content">
    <div class="clear"></div>
    <div class="appColWrap">
		<!-- layout::$appCenterBar::0 -->
		<div class="rightAppCol">
			<div class="rightColTop"></div>
			<div class="rightColBodyindex">
				<div class="appList">
					<h2>推荐应用</h2>
					<div class="itemHolder">
						<div class="items" id="resultsPools"></div>
					</div>
					<div id="hiddenresultsPools" style="display:none;">
						<?php
						foreach($recommenlist as $k => $v)
						{
						?>
						<?php if(is_array($v)): $i = 0; $__LIST__ = $v;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="item result">
							<div class="icon">
								<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>">
									<img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['icon']); ?>" style="width:78px; height:78px;" alt="<?php echo ($vo['app_name']); ?>" />
								</a>
							</div>
							<div class="title">
								<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" title="<?php echo ($vo['app_name']); ?>"><?php echo showCutStr($vo['app_name'], 9);?></a>
							</div>
							<div class="sub">
								<a href="/<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" title="<?php echo ($vo['author_name']); ?>"><?php echo showCutStr($vo['author_name'], 9);?></a>
							</div>
							<div class="stars smallStars ">
								<div class="RatingStarsSmall rating-<?php echo round($vo['app_score_www']*2/($vo['app_starcount']+1e-5));?>"></div> (<?php echo number_format($vo['app_downloads']);;?>)
							</div>
							<a class="button_normal" href="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['apk_path']); ?>" onclick="getApp('<?php echo ($vo['app_id']); ?>');<?php echo ($returnFlase); ?>">下载</a>
						</div><?php endforeach; endif; else: echo "" ;endif; ?>
						<?php		
						}
						?>
					</div>
					<div class="clear"></div>
					<div id="paginationPools"></div>
					<div class="clear"></div>
				</div>          
				<div class="appList bottomMarg0">
					<h2>测试应用</h2>
					<div class="itemHolder">
						<div class="items" id="resultsTests"></div>
					</div>
					<div id="hiddenresultsTests" style="display:none;">
						<?php
						foreach($testlist as $k => $v)
						{
						?>
						<?php if(is_array($v)): $i = 0; $__LIST__ = $v;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="item result">
							<div class="icon">
								<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>">
									<img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo['icon']); ?>" style="width:78px; height:78px;" alt="<?php echo ($vo['app_name']); ?>" />
								</a>
							</div>
							<div class="title">
								<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" title="<?php echo ($vo['app_name']); ?>"><?php echo showCutStr($vo['app_name'], 9);?></a>
							</div>
							<div class="sub">
								<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" title="<?php echo ($vo['author_name']); ?>"><?php echo showCutStr($vo['author_name'], 9);?></a>
							</div>
							<div class="stars smallStars">
								<div class="RatingStarsSmall rating-<?php echo round($vo['app_score_www']*2/($vo['app_starcount']+1e-5));?>"></div> (<?php echo number_format($vo['app_downloads']);;?>)
							</div>
							<a class="button_normal" href="<?php echo C('PICTURE_HOST');?><?php echo ($vo['apk_path']); ?>" onclick="getApp('<?php echo ($vo['app_id']); ?>');<?php echo ($returnFlase); ?>">下载</a>
						</div><?php endforeach; endif; else: echo "" ;endif; ?>
						<?php		
						}
						?>
					</div>
					<div class="clear"></div>
					<div id="paginationTests"></div>
					<div class="clear"></div>
				</div>
			</div>
		</div>
		<div class="rightAppColBGR"></div>
		<div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>