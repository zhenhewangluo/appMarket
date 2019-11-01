<?php if (!defined('THINK_PATH')) exit();?><link href="../Public/css/jcarousel.css" type="text/css" rel="stylesheet" />
<link href="../Public/css/pagination.css" type="text/css" rel="stylesheet" />
<script type="text/javascript" src="../Public/js/jquery.pagination.js"></script>
<script type="text/javascript" src="../Public/js/jquery.jcarousel.min.js"></script>
<script type="text/javascript" src="../Public/js/shop.js"></script>
<script type="text/javascript">
	$(function(){
		initShop();
	})
	function ajax_page(p)
	{
		var order = $('input:[name="order_type"]').val();
		var listRow = $('input:[name="list_row"]').val();
		var cid = '<?php echo ($cid); ?>';
		$.getJSON("<?php echo U('AppStore://App-Ajax/appCenterList?do=ajax_page');?>", {'p':p, 'order':order, 'listRow':listRow, 'cid' : cid} ,function(data){
			$('#appList').html(data.content);
//			$('.page').html(data.pageInfo);
		});
	}
</script>
<div class="breadcrumbBar">
    <div class="breadcrumb">
		您的位置： <a href="/">***</a> &raquo; <a href ="<?php echo U('App-Apps/index');?>">应用中心</a> &raquo; 
		<?php if(is_array($breadcrumbs)): $i = 0; $__LIST__ = $breadcrumbs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><a href ="<?php echo U('AppStore://App-Apps/appCenterList?cid=' . $vo['id']);?>"><?php echo ($vo['name']); ?></a> &raquo;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    <div class="clear"></div>
</div>
<div class="content">
    <div class="clear"></div>
    <div class="appColWrap categoriesPage">
		<!-- layout::$appCenterBar::0 -->
		<div class="rightAppCol">
			<div class="rightColTop"></div>
			<div class="rightColBody">
				<div id="contentTab">
					<div class="top">
						<h2><?php echo ($cateName); ?></h2>
					</div>
					<div class="body" style="_background:#fff;">
						<div id="appList" class="appList topMarg20">
							<?php $listCount = count($list);;?>
							<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php if($key%2 == 0): ?><div class="row"><?php endif; ?>
							<div class="listitem">
								<div class="icon">
									<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>">
										<img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['icon']); ?>" style="width:78px; height:78px;" alt="<?php echo ($vo['app_name']); ?>" />
									</a>
								</div>
								<div class="datas">
									<div class="title"><a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" title="<?php echo ($vo['app_name']); ?>"><?php echo ($vo['app_name']); ?></a></div>
									<div class="sub"><a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" title="<?php echo ($vo['author_name']); ?>"><?php echo ($vo['author_name']); ?></a></div>
									<div class="intro"><?php echo showCutStr($vo['app_desc'], 65);?></div>
								</div>
								<div class="clear"></div>
								<div class="stars"><div class="RatingStars5 rating-9"></div></div>
								<a class="button_normal" href="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['apk_path']); ?>" onclick="getApp('<?php echo ($vo['app_id']); ?>');<?php echo ($returnFlase); ?>">安装</a>
								<div class="clear"></div>
							</div>
							<?php if((($key+1)%2 == 0) OR (($key+1) == $listCount)): ?></div><?php endif; ?><?php endforeach; endif; else: echo "" ;endif; ?>
							<?php if((trim($page))  !=  ""): ?><table class="pager" style="width:460px" cellspacing="0">
								<tr>
									<td class="cl"></td>
									<td style="text-align:left;">跳转至：</td>
									<td style="text-align:left;">
										<?php echo ($page); ?>
									</td>
									<td class="cr"></td>
								</tr>
							</table><?php endif; ?>
							<div class="clear"></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="rightAppColBGR"></div>
		<div class="clear"></div>
    </div>
    <div class="clear"></div>
</div>