<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript">
	var gl_localPrefix = '/en/android';
	var gl_fatalMsg = 'An unexpected error has occured.';
</script>
<script type="text/javascript" src="../Public/js/common.js?v=50"></script>
<script type="text/javascript" src="../Public/js/apps.js?v=28"></script>
<script type="text/javascript" src="../Public/js/jquery.mousewheel-3.0.4.pack.js"></script>
<script type="text/javascript" src="__PUBLIC__/js/fancybox/jquery.fancybox-1.3.4.pack.js"></script>
<link rel="stylesheet" type="text/css" href="__PUBLIC__/js/fancybox/jquery.fancybox-1.3.4.css" media="screen" />
<script type="text/javascript">
$(function() {
	initAppPage();
	$("a[rel=screenshots]").fancybox({
		'transitionIn'  : 'none',
		'transitionOut' : 'none',
		'titlePosition' : 'over',
		'overlayColor'  : '#000',
		'titleFormat'   : function(title, currentArray, currentIndex, currentOpts) {
			return '<' + 'span id="fancybox-title-over">Screenshot ' + (currentIndex + 1) + ' / ' + currentArray.length + (title.length ? ' &nbsp; ' + title : '') + '<' + '/span>';
		}
	});

	$("a.similarAppsPopup").fancybox({
		'transitionIn'  : 'none',
		'transitionOut' : 'none',
		'titlePosition' : 'over',
		'overlayColor'  : '#000'
	});

	$("a#installButtonLoginPopup").fancybox({
		'transitionIn'  : 'none',
		'transitionOut' : 'none',
		'titlePosition' : 'over',
		'overlayColor'  : '#000'
	});
  //提交评论
	$('#submit').live('click', function(){
		var comment_content = $('#comment').val();
		var score = parseInt($('#rating').val());
		if(isNaN(score)){
			alert('请填写评分');
			return false;
		}
		if($.trim(comment_content) == ''){
			alert('请填写评论内容');
			return false;
		}
		if(score <=0 || score > 5){
			alert('评分只能是1-5分');
			return false;
		}
		$.getJSON(
			"<?php echo U('App-Ajax/appComment');?>", 
			{'score' : score,'comment_content':comment_content, 'app_id':$('#app_id').val()},
			function(data){
				alert(data.msg);
				if(data.status == 1)
				{
					$('#existingUserComments').prepend(data.returnhtml);
					$('.slide' + data.dlflag).slideDown('slow');
					$('#comment_form').remove();
					$('.score_count').html(data.score_count);
					$('.review').html(data.html);
					$('#topStarCountshow').attr('class', 'RatingStarsDark rating-' + (score * 2));
					$('#comment').val('');
					$('#rating').val(0);
					setAppCommentRating(0);
				}
			}
		);
		return false;
	});
});
function ajax_page(p)
{
	$.getJSON("<?php echo U('App-Ajax/appCommentList?do=ajax_page');?>", {'p':p, 'aid' : $('#app_id').val()} ,function(data){
		$('#commentsContainer_all').html(data.html);
	});
}
</script>
<link rel="stylesheet" href="../Public/css/pagination.css" type="text/css" media="screen" />
<script type="text/javascript" src="../Public/js/jquery.pagination.js"></script>
<div class="breadcrumbBar">
	<div class="breadcrumb">
		您的位置： <a href="/">***</a> &raquo; <a href ="<?php echo U('App-Apps/index');?>">应用中心</a> &raquo; 
		<?php if(is_array($breadcrumbs)): $i = 0; $__LIST__ = $breadcrumbs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><a href ="<?php echo U('AppStore://App-Apps/AppCenterList?cid=' . $vo['id']);?>"><?php echo ($vo['name']); ?></a> &raquo;&nbsp;<?php endforeach; endif; else: echo "" ;endif; ?>
	</div>
	<div class="clear"></div>
</div>
<div class="content" style="margin-top:-20px;">
	<div class="appColWrap detailPage visualBox">
		<div class="popup button">
			<div class="popupInner">
			</div>
		</div>
		<div class="detailHead">
			<div class="leftBox">
				<?php $grade_point = ($approw['app_starcount'] > 0 ) ? round($approw['app_score_www']/$approw['app_starcount'], 1) : 0;?>
				<img src="<?php echo C('PICTURE_HOST');?><?php echo ($approw['AppDeviceType'][0]['icon']); ?>" style="width:124px; height:124px;" alt="Icon" />
				<div class="stars darkStars">
					<div id="topStarCountshow" class="RatingStarsDark rating-<?php echo round($grade_point) * 2;?>" style="margin:0 auto;"></div>
					<div class="number">评分次数：(<span class="score_count"><?php echo (number_format($approw['app_starcount'])); ?></span>)</div>
				</div>
				<div class="titleAndOwner">
					<h1><?php echo ($approw['app_name']); ?></h1>
					<p><a href="javascript:void(0);">作者：<?php echo ($approw['author_name']); ?></a></p>
				</div>
				<div class="qrc">
					<a class="infoBubble infoBubbleQRCode" style="line-height:0px;" href="#" onclick="return false;" target="_blank">
						<span class="outer"><span class="inner" style="padding-top: 6px !important">
								<img src="http://hjapp.hjapp.com/global/qr/?url=<?php echo urlencode(C('PICTURE_HOST') . $approw['AppDeviceType'][0]['apk_path']);?>" style="width:164px; height:164px; line-height:0" alt="QR Code" />
							</span></span>
						<img src="http://hjapp.hjapp.com/global/qr/?url=<?php echo urlencode(C('PICTURE_HOST') . $approw['AppDeviceType'][0]['apk_path']);?>" style="width:82px; height:82px;" alt="QR Code" />
					</a>
				</div>
			</div>

			<div class="bigButton">
				<a class="buttonLink" href="<?php echo C('PICTURE_HOST');?><?php echo ($approw['AppDeviceType'][0]['apk_path']); ?>" onclick="getApp('<?php echo ($approw['app_id']); ?>');">应用下载</a>
				<div class="selectbutton"></div>
			</div>
			<div class="visual">
				<a href="http://d.hjapp.com/" title="欢聚夺宝"><img src="__PUBLIC__/ad/index/duobao.png" alt="欢聚夺宝" /></a>
			</div>
		</div>

		<div class="leftAppCol">

			<div class="dataTitle">应用概况:<a href="#" id="btnShowMoreData" style="background-position: 0 -19px" onclick="showMoreData(); return false;">显示更多</a></div>
			<div class="showMoreData" id="showMoreData1">
				<div class="subData">
					<div class="label">文件大小：</div>
					<div class="data"><?php echo (round($approw['AppDeviceType'][0]['app_size']/1048576,1)); ?> MB</div>
				</div>
				<div class="subData">
					<div class="label">最后更新：</div>
					<div class="data"><?php echo (date('Y-m-d',strtotime($approw['app_update_date']))); ?></div>
				</div>
				<div class="subData">
					<div class="label">下载次数：</div>
					<div class="data" id="down_hits"><?php echo (number_format($approw['app_downloads'])); ?></div>
				</div>
			</div>
			<div class="subData">
				<div class="label">作者：</div>
				<div class="data"><?php echo ($approw['author_name']); ?></div>
			</div>
			<div class="showMoreData">
				<div class="subData">
					<div class="label">应用版本：</div>
					<div class="data"><?php echo ($approw['app_version']); ?></div>
				</div>
				<div class="subData">
					<div class="label">评分次数：</div>
					<div class="data"><?php echo (number_format($approw['app_starcount'])); ?></div>
				</div>
			</div>
			<div class="leftseparator"></div>
			<h2>喜欢<?php echo ($approw['app_name']); ?>的网友还喜欢</h2>
			<?php if(is_array($recommenlist)): $i = 0; $__LIST__ = $recommenlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="item">
				<div class="icon">
					<a href="<?php echo U('AppStore://App-Apps/detail', array('aid' => $vo['app_id']));?>">
						<img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['icon']); ?>" style="width:72px; height:72px;" alt="<?php echo showCutStr($vo['app_name'], 10);?>" />
					</a>
				</div>
				<div class="datas">
					<div class="title"><a href="<?php echo U('AppStore://App-Apps/detail', array('aid' => $vo['app_id']));?>"><?php echo showCutStr($vo['app_name'], 10);?></a></div>
					<div class="sub"><a href="<?php echo U('AppStore://App-Apps/detail', array('aid' => $vo['app_id']));?>"><?php echo showCutStr($vo['author_name'], 10);?></a></div>
					<div class="stars smallStars"><div class="RatingStarsSmall rating-<?php echo round($vo['app_score_www']*2/($vo['app_starcount']+1e-5));?>"> </div></div> (<?php echo ($vo['app_downloads']); ?>)
					<!--div class="price">
						free            </div-->
				</div>
				<div class="clear"></div>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>
			<div class="leftseparator"></div>
			<h2>下载排行</h2>
			<?php if(is_array($downloadlist)): $i = 0; $__LIST__ = $downloadlist;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="item">
				<div class="icon">
					<a href="<?php echo U('AppStore://App-Apps/detail', array('aid' => $vo['app_id']));?>">
						<img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['icon']); ?>" style="width:72px; height:72px;" alt="<?php echo showCutStr($vo['app_name'], 10);?>" />
					</a>
				</div>
				<div class="datas">
					<div class="title"><a href="<?php echo U('AppStore://App-Apps/detail', array('aid' => $vo['app_id']));?>"><?php echo showCutStr($vo['app_name'], 10);?></a></div>
					<div class="sub"><a href="<?php echo U('AppStore://App-Apps/detail', array('aid' => $vo['app_id']));?>"><?php echo showCutStr($vo['author_name'], 10);?></a></div>
					<div class="stars smallStars"><div class="RatingStarsSmall rating-<?php echo round($vo['app_score_www']*2/($vo['app_starcount']+1e-5));?>"> </div></div> (<?php echo ($vo['app_downloads']); ?>)
					<!--div class="price">
						free            </div-->
				</div>
				<div class="clear"></div>
			</div><?php endforeach; endif; else: echo "" ;endif; ?>			
		</div>
		<div class="leftAppColBGR"></div>

		<div class="rightAppCol">
			<div class="rightColBody">
				<div id="contentTab">
					<div class="top">
						<ul>
							<li id="contentTabbtn1" class="btn1A"><a href="#" onclick="setTab('contentTab',1,4); return false;">概览</a></li>
							<li id="contentTabbtn2" class="btn2"><a href="#" onclick="setTab('contentTab',2,4); return false;">用户评论 (<i class="score_count"><?php echo (number_format($approw['app_starcount'])); ?></i>)</a></li>
						</ul>
					</div>
					<div id="contentTabBody1" class="body clearfix">
						<div class="block description">
							<div class="moreContainer" id="description">
								<div class="text">
									<h2>应用描述</h2>
									<?php echo ($approw['app_desc']); ?>
								</div>
								<div class="morebar"><span><!-- otherwise HTML error --></span></div>
								<div class="lessbar"><span><!-- otherwise HTML error --></span></div>
							</div>
							<div class="clear"></div>
						</div>
						<div class="block screenshot">
							<h2>应用截图</h2>
							<div class="itemHolder">
								<div id="resultTab1">
									<?php
										if(!empty($approw['AppDeviceType'][0]['screenshots']))
										{
									?>
									<?php $screenshots = explode(';', $approw['AppDeviceType'][0]['screenshots']);?>
									<?php if(is_array($screenshots)): $i = 0; $__LIST__ = $screenshots;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="screenImage"><a href="<?php echo C('PICTURE_HOST');?><?php echo ($vo); ?>" rel="screenshots"><img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo); ?>" alt="" style="width:140px; height:224px"/></a></div><?php endforeach; endif; else: echo "" ;endif; ?>
									<?php
									}
									?>
								</div>
							</div>
							<div class="clear"></div>
							<div id="paginationTab1"></div>
							<div class="clear"></div>
						</div>
						<div class="block review">
							<h2>用户评论</h2>
							<div class="bars">
								<?php if(is_array($appScore)): $i = 0; $__LIST__ = $appScore;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php if(($key)  !=  "0"): ?><div class="row">
										<div class="stars reviewStars"><div class="ReviewInactiveStars rating-<?php echo ($key); ?>"></div> </div>
										<div class="colorBar">
											<?php if(($approw['app_starcount'])  >  "0"): ?><div class="bar" style="width:<?php echo round($appScore[$key]/$approw['app_starcount'], 2) *146;?>px; background:#8eb528;"></div>
											<?php else: ?>
											<div class="bar" style="width:0px;"></div><?php endif; ?>
										</div>
									</div><?php endif; ?><?php endforeach; endif; else: echo "" ;endif; ?>
							</div>
							<div class="average">
								<div>平均评分</div>
								<div class="avgNumber"><?php echo ($grade_point); ?></div>
								<div class="stars darkStars">
									<div class="RatingStarsDark rating-<?php echo round($grade_point * 2);?>"> </div> 
									<div class="number">(<?php echo (number_format($approw['app_starcount'])); ?>)</div>
								</div>
							</div>
							<div class="rate">
								<div class="label">您的评分</div>

								<div class="stars darkStars"><div class="RatingStarsDark rating-<?php if(isset($userScore))echo $userScore*2; else echo 0;;?>"></div></div>  
							</div>
							<div class="clear"></div>
						</div>
						<div class="block comments" style="_background:#fff;_padding-left:20px;">
							<div class="boxLightBlue">
								<div class="top"></div>
								<div class="body" id="commentsBody1" style="padding-top: 10px">
									<h2>最新评论</h2>
									<div id="existingUserComments">
										<?php if(is_array($appComment['appList'])): $i = 0; $__LIST__ = $appComment['appList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php
											if($key > 29)break;
										?>
										<div class="comments" id="aucContainer40085">
											<a id="auc40085"></a>
											<div class="user">
												<a href="javascript:void(0);" title="">
													<?php $headArr = explode(',', $vo['headPath']);?>
													<img src="__ROOT__/<?php echo ($headArr[2]); ?>" style="width:65px; height:70px" alt="" />
												</a><br />
												<a href="javascript:void(0);" title=""><?php echo ($vo['user_name']); ?></a><br />
											</div>
											<div class="bubble">
												<div class="text">
													<div class="stars whiteStars"><div class="RatingStarsWhite rating-<?php echo $vo['score_www']*2;?>"></div></div>
													<span class="date"><?php echo (date('Y年m月d日',strtotime($vo['create_time']))); ?>
													</span>
													<p id="aucText40085"><?php echo keyWordFilter($vo['content']);?></p>
												</div>
											</div>
										</div><?php endforeach; endif; else: echo "" ;endif; ?>
									</div>
									<input type="hidden" name="app_id" id="app_id" value="<?php echo ($approw['app_id']); ?>" />
									<div id="commentForm"></div>
								</div>
								<div class="bottom"></div>
							</div>
						</div>
					</div>
					<div id="contentTabBody2" class="body"  style="display:none">

						<div class="block review">
							<h2>用户评论</h2>
							<div class="bars">
								<?php if(is_array($appScore)): $i = 0; $__LIST__ = $appScore;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php if(($key)  !=  "0"): ?><div class="row">
										<div class="stars reviewStars"><div class="ReviewInactiveStars rating-<?php echo ($key); ?>"></div> </div>
										<div class="colorBar">
											<?php if(($approw['app_starcount'])  >  "0"): ?><div class="bar" style="width:<?php echo round($appScore[$key]/$approw['app_starcount'], 2) *146;?>px; background:#8eb528;"></div>
											<?php else: ?>
											<div class="bar" style="width:0px;"></div><?php endif; ?>
										</div>
									</div><?php endif; ?><?php endforeach; endif; else: echo "" ;endif; ?>
							</div>
							<div class="average">
								<div>平均评分</div>
								<div class="avgNumber"><?php echo ($grade_point); ?></div>
								<div class="stars darkStars">
									<div class="RatingStarsDark rating-<?php echo round($grade_point * 2);?>"> </div> 
									<div class="number">(<?php echo (number_format($approw['app_starcount'])); ?>)</div>
								</div>
							</div>
							<div class="rate">
								<div class="label">您的评分</div>

								<div class="stars darkStars"><div class="RatingStarsDark rating-<?php if(isset($userScore))echo $userScore*2; else echo 0;;?>"></div></div>  
							</div>
							<div class="clear"></div>
						</div>
						<div id="commentTab" style="margin-top:25px">
							<div id="commentTabBody1" class="body">
								<div id="commentsContainer_all">
									<div class="commentBody">
										<?php if(is_array($appComment['appList'])): $i = 0; $__LIST__ = $appComment['appList'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><div class="comments">
											<div class="user">
												<?php $headPathArr = explode(',', $vo['headPath']);?>
												<img class="market" src="__ROOT__/<?php echo ($headPathArr[2]); ?>" alt="" /><br />
												<?php echo ($vo['user_name']); ?>
											</div>
											<div class="bubble">
												<div class="text">
													<div class="stars whiteStars"><div class="RatingStarsWhite rating-<?php echo round($grade_point * 2);?>"></div></div>
													<br/>
													<span class="date"><?php echo (date('Y年m月d日',strtotime($vo['create_time']))); ?></span>
													<p><?php echo keyWordFilter($vo['content']);?></p>
												</div>
											</div>
										</div>
										<div class="clear"></div><?php endforeach; endif; else: echo "" ;endif; ?>
									</div>
									<div class="commentBottom"></div>
									<?php if((trim($appComment['pageShow']))  !=  ""): ?><table class="pager" style="width:300px;" cellspacing="0">
										<tr>
											<td class="cl"></td>
											<td>跳转至：</td>
											<td>
												<?php echo ($appComment['pageShow']); ?>
											</td>
											<td class="cr"></td>
										</tr>
									</table><?php endif; ?>
								</div>
							</div>
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