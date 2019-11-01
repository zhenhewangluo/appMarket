<?php if (!defined('THINK_PATH')) exit();?><?php $params = array('listRow' => 10);?>
<?php if(isset($_GET['order']))$params['order'] = $_GET['order'];?>
<?php if(isset($_GET['listRow']))$params['listRow'] = $_GET['listRow'];?>
<?php $key_words = urldecode(implode(' ', $keyWords));;?>
<link href="../Public/css/<?php echo ($css); ?>recommendations.css" type="text/css" rel="stylesheet" />
<script type='text/javascript' src="../Public/js/all.js"></script>
<script type='text/javascript' src="../Public/js/in.js"></script>
<script type='text/javascript' src="../Public/js/plusone.js"></script>
<script type='text/javascript' src="../Public/js/social.js"></script>
<script type='text/javascript' src="../Public/js/recommendations.js"></script>
<link href="../Public/css/socialshareprivacy.css" type="text/css" rel="stylesheet" />
<script type="text/javascript">
	$(function(){
		initRecommendations();
		$('.headerNav .menu').lavaLamp({fx:'easeOutBack',speed:700});
	});
</script>
<script type="text/javascript">
	var gl_localPrefix_saveAct = '<?php echo U("App-Ajax/saveActOrder");?>';
	var gl_localPrefix_getApp = '<?php echo U("App-Ajax/ajaxGetAppsList");?>';
	var gl_localPrefix_get3App = '<?php echo U("App-Ajax/ajaxGet3AppsList");?>';
	var gl_localPrefix_layer = '<?php echo U("App-Ajax/getPopupAppinfo");?>';
	var text_recommendations_showAll = '显示全部';    
	var gl_localPrefix_recomm = '<?php echo U("App-Ajax/ajaxGetMoreApps");?>';

    var currentRowId = '<?php echo ($rid); ?>';
    var maxResults = '<?php echo ($maxPerPage); ?>';

	function ajax_page(p){
		var rid = '<?php echo ($rid); ?>';
		var order = $('input:[name="order_type"]').val();
		var obj = $.extend(ar_filter.toAjax(), {'do' : 'ajax_page', 'rid':rid,'si':p, 'order':order, 'max' : maxResults});
		$.post("<?php echo U('AppStore://App-Ajax/ajaxGetMoreApps');?>", obj, function(data){
			$('.appsList').html(data.content);
			$('.ajaxpage').html(data.pageInfo);
		}, 'json');
	}
</script>
<div class="breadcrumbBar">
	<div class="breadcrumb">
		您的位置： <a href="/">***</a> &raquo; 搜索结果
	</div>
	<div class="clear">&nbsp;</div>
</div>
<div class="containerAllApps" style="background:none;">
<!--	<div class="sidebarAllApps">
		<div class="top">
			<h2>应用筛选</h2>
		</div>
		<div class="sidebarInner" id="filterSidebar">
			<ul class="favSortList" id="favSortList">

				<?php $appTypeTextCount = count($appTypeText);$firstClass='';$lastClass='';;?>

				<?php $unassignArrCount = count($unassignArr);$addWidget = '';;?>
				<?php if(($unassignArrCount)  <=  "0"): ?><?php $addWidget = 'style="display:none;"';;?><?php endif; ?>
				<li class="addWidget" <?php echo ($addWidget); ?>>
					<div class="addFavListClosed">
						<a href="javascript:void(0);" onclick="openAddWidget(); return false">Add widget</a>
					</div>
					<div class="addFavListOpen" id="addFavList">
						<a href="javascript:void(0);" class="close" onclick="closeAddWidget(); return false">x</a>
						<ul class="unassignedFavs" id="unassignedFavs">
							<?php if(is_array($unassignArr)): $i = 0; $__LIST__ = $unassignArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><li id="fav_<?php echo ($key); ?>">
									<a href="javascript:void(0);" onclick="addToFavs('<?php echo ($key); ?>');  return false"><?php echo ($vo['desc']); ?></a>
								</li><?php endforeach; endif; else: echo "" ;endif; ?>
						</ul>
					</div>
				</li>
			</ul>
			<form action="" method="post" class="appFilter" id="appFilter">
				<fieldset class="section">
					<fieldset class="appCategoriesClosed" id="appCategoriesClosed">
						<h3>应用分类<a href="#" onclick="toggleCategories(); return false" class="appCategoriesClosed">toggle</a></h3>
						<fieldset>
							<ul>
								<li>
									<h4><a href="#" onclick="toggleCategories(); return false">软件分类</a></h4>
									<?php $tmpShowAppCateInArray = false;;?>
									<ul id="appsCategoryList">
										<?php if(is_array($appCateTree[1]['_child'])): $i = 0; $__LIST__ = $appCateTree[1]['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php
											if(isset($checkedCate) && in_array($vo['id'], $checkedCate))
											{
											$tmpShowAppCateInArray = ture;
											break;
											}
											?><?php endforeach; endif; else: echo "" ;endif; ?>
										<?php
										if(!$tmpShowAppCateInArray)
										{
										?>
										<li>所有软件</li>
										<?php
										}
										?>
									</ul>
								</li>
								<li>
									<h4><a href="#" onclick="toggleCategories(); return false">游戏分类</a></h4>
									<?php $tmpShowGameCateInArray = false;;?>
									<ul id="gamesCategoryList">
										<?php if(is_array($appCateTree[2]['_child'])): $i = 0; $__LIST__ = $appCateTree[2]['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php
											if(isset($checkedCate) && in_array($vo['id'], $checkedCate))
											{
											$tmpShowGameCateInArray = ture;
											break;
											}
											?><?php endforeach; endif; else: echo "" ;endif; ?>
										<?php

										if(!$tmpShowGameCateInArray)
										{
										?>
										<li>所有游戏</li>
										<?php
										}
										?>
									</ul>
								</li>
							</ul>
						</fieldset>
					</fieldset>
					<fieldset class="appCategoriesOpen" id="appCategoriesOpen">
						<h3>应用分类<a href="#" onclick="toggleCategories(); return false" class="appCategoriesOpen">toggle</a></h3>
						<fieldset class="innerSection" id="appsCategoryListOpen">
							<fieldset>
								<input type="checkbox" value="*" id="appCategory_0" <?php if(!$tmpShowAppCateInArray)echo 'checked="checked"';?> /><label for="appCategory_0">所有软件</label>
							</fieldset>
							<?php if(is_array($appCateTree[1]['_child'])): $i = 0; $__LIST__ = $appCateTree[1]['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><fieldset>
									<?php $tmpInArray = 0;;?>
									<?php if(isset($checkedCate) && in_array($vo['id'], $checkedCate))$tmpInArray =1;;?>
									<input type="checkbox" value="<?php echo ($vo['id']); ?>" id="appCategory_<?php echo ($i); ?>" <?php if(($tmpInArray)  ==  "1"): ?>checked="checked"<?php endif; ?> /><label for="appCategory_<?php echo ($i); ?>"><?php echo ($vo['name']); ?></label>
								</fieldset><?php endforeach; endif; else: echo "" ;endif; ?>
						</fieldset>
						<hr />
						<fieldset class="innerSection" id="gamesCategoryListOpen">
							<fieldset>
								<input type="checkbox" value="*" id="gameCategory_0" <?php if(!$tmpShowGameCateInArray)echo 'checked="checked"';?> /><label for="gameCategory_0">所有游戏</label>
							</fieldset>
							<?php if(is_array($appCateTree[2]['_child'])): $i = 0; $__LIST__ = $appCateTree[2]['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><?php $tmpInArray = 0;;?>
								<?php if(isset($checkedCate) && in_array($vo['id'], $checkedCate))$tmpInArray =1;;?>
								<fieldset>
									<input type="checkbox" value="<?php echo ($vo['id']); ?>" id="gameCategory_<?php echo ($i); ?>" <?php if(($tmpInArray)  ==  "1"): ?>checked="checked"<?php endif; ?> /><label for="gameCategory_<?php echo ($i); ?>"><?php echo ($vo['name']); ?></label>
								</fieldset><?php endforeach; endif; else: echo "" ;endif; ?>
						</fieldset>
					</fieldset>
				</fieldset>
			</form>
			<form action="" method="post" class="appRating" id="appRating">
				<fieldset class="section">
					<h3>评分筛选</h3>
					<p>minimum:</p>
					<?php
					for($i = 5; $i > 0; $i--)
					{
					?>
					<fieldset class="innerSection">
						<input type="radio" name="minRating" id="minRating_<?php echo ($i); ?>stars" value="<?php echo ($i); ?>" <?php if(isset($checkedRate) && $i == $checkedRate)echo 'checked="checked"';?> />
							   <label for="minRating_<?php echo ($i); ?>stars"><div class="smallStars"><div class="RatingStarsSmall rating-<?php echo ($i * 2);?>"></div></div></label>
					</fieldset>
					<?php
					}
					?>
					<fieldset class="innerSection">
						<input type="radio" name="minRating" id="minRating_0stars" value="0" <?php if(!isset($checkedRate) || $checkedRate == 0 || $checkedRate == '')echo 'checked="checked"';?> />
							   <label for="minRating_0stars"><div class="smallStars"><div class="RatingStarsSmall rating-0"></div></div></label>
					</fieldset>
				</fieldset>
			</form>
			<form action="" method="post" class="appPeriod" id="appPeriod">
				<fieldset class="section">
					<h3>时间筛选</h3>
													<fieldset class="innerSection">
														<input type="radio" name="timeFilter" value="SINCE_LAST_LOGIN" id="timeFilter_SINCE_LAST_LOGIN" disabled="disabled" title="Not logged in - please log in or register."/>
														<label for="timeFilter_SINCE_LAST_LOGIN" style="color:#aaa" title="Not logged in - please log in or register.">since last login</label>
													</fieldset>
					<?php if(is_array($dateArr)): $i = 0; $__LIST__ = $dateArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><fieldset class="innerSection">
							<input type="radio" name="timeFilter" value="<?php echo ($key); ?>" id="timeFilter_<?php echo ($key); ?>" <?php if(isset($checkedDate) && $checkedDate == $key)echo 'checked="checked"';?> />
								   <label for="timeFilter_<?php echo ($key); ?>"><?php echo ($vo['desc']); ?></label>
						</fieldset><?php endforeach; endif; else: echo "" ;endif; ?>
					<fieldset class="innerSection">
						<input type="radio" name="timeFilter" value="LAST_365_DAYS" id="timeFilter_LAST_365_DAYS" <?php if(!isset($checkedDate) || $checkedDate == '' || $checkedDate == 'LAST_365_DAYS')echo 'checked="checked"';?> />
							   <label for="timeFilter_LAST_365_DAYS">过去365天</label>
					</fieldset>
				</fieldset>
			</form>
			<form action="" method="post" class="appInstallations" id="appInstalls">
				<fieldset class="section">
					<h3>下载量筛选</h3>
					<fieldset class="innerSection">
						<input type="radio" name="appInstalls" value="any" id="appInstalls_any" <?php if(!isset($checkedDownload) || ($checkedDownload['from'] == -1 && $checkedDownload['to'] == -1) || empty($checkedDownload))echo 'checked="checked"';?> />
							   <label for="appInstalls_any">any</label>
					</fieldset>
					<fieldset class="innerSection" style="margin-top:2px">
						<input type="radio" name="appInstalls" value="range" id="appInstalls_range"  <?php if(isset($checkedDownload) && !empty($checkedDownload) && $checkedDownload['from'] != -1)echo 'checked="checked"';?> />
							   <table style="border:0" cellspacing="0" cellpadding="0">
							<tr>
								<td><label for="appInstalls_range">from:&nbsp;</label></td>
								<td style="padding-bottom:1px">
									<select id="appInstalls_min" name="appInstalls_min">
										<option value="0" <?php if(isset($checkedDownload) && !empty($checkedDownload) && $checkedDownload['from'] == 0)echo 'selected="selected"';?>>0</option>
										<?php if(is_array($downloadArr)): $i = 0; $__LIST__ = $downloadArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><option value="<?php echo ($vo); ?>" <?php if(isset($checkedDownload) && !empty($checkedDownload) && $checkedDownload['from'] == $vo)echo 'selected="selected"';?>><?php echo number_format($vo);?></option><?php endforeach; endif; else: echo "" ;endif; ?>
									</select>
								</td>
							</tr>
							<tr>
								<td><label for="appInstalls_max">to:&nbsp;</label></td>
								<td>
									<select id="appInstalls_max" name="appInstalls_max">
										<?php if(is_array($downloadArr)): $i = 0; $__LIST__ = $downloadArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><option value="<?php echo ($vo); ?>" <?php if(isset($checkedDownload) && !empty($checkedDownload) && $checkedDownload['to'] == $vo)echo 'selected="selected"';?>><?php echo number_format($vo);?></option><?php endforeach; endif; else: echo "" ;endif; ?>
										<option value="-1" <?php if(isset($checkedDownload) && !empty($checkedDownload) && $checkedDownload['to'] == -1)echo 'selected="selected"';?>>&gt; 100,000,000</option>
									</select>
								</td>
							</tr>
						</table>
					</fieldset>
				</fieldset>
			</form>
		</div>
	</div>-->
	<div class="contentContainerAllApps categoriesPage" id="contentTab">
		<div class="rightColTop"></div>
		<div class="top">
			<h2>搜索<span class="green"><?php echo ($key_words); ?></span>的结果</h2>
		</div>
		<div id="contentTabBody1" class="body">
			<div class="categorySelectBar">
				<fieldset style="float:right;">
					<span>每页显示:</span>
					<select id="searchMax" name="max">
						<option <?php if(($params['listRow'])  ==  "5"): ?>selected="selected"<?php endif; ?> value="5">5</option>
						<option <?php if(($params['listRow'])  ==  "10"): ?>selected="selected"<?php endif; ?> <?php if(!isset($params['listRow'])) echo 'selected="selected"';?> value="10">10</option>
						<option <?php if(($params['listRow'])  ==  "25"): ?>selected="selected"<?php endif; ?> value="25">25</option>
						<option <?php if(($params['listRow'])  ==  "50"): ?>selected="selected"<?php endif; ?> value="50">50</option>
					</select>
				</fieldset>
			</div>
			<div id="listviewApps" class="main" style="position:relative;">          
				<ul class="appsList">
					<?php if(is_array($list)): $i = 0; $__LIST__ = $list;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): ++$i;$mod = ($i % 2 )?><li>
							<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" class="appIcon" id="<?php echo ($rid); ?>-<?php echo ($vo['app_id']); ?>">
								<img src="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['icon']); ?>" style="width: 78px; height: 78px;" alt="Icon">
							</a>
							<div class="appText">
								<a href="<?php echo U('App-Apps/detail', array('aid' => $vo['app_id']));?>" class="title">
								<h3><?php echo replaceKeyWords($keyWords, $vo['app_name']);;?></h3>
								</a>
								<p><?php echo showCutStr($vo['app_desc'],100);;?></p>
							</div>
							<div class="appInfo">
								<div class="stars darkStars"><div class="RatingStarsDark rating-<?php echo round($vo['app_score_www']*2/($vo['app_starcount']+1e-5));?>"></div></div>
								<a class="button_normal" href="<?php echo C('PICTURE_HOST');?><?php echo ($vo['AppDeviceType'][0]['apk_path']); ?>" onclick="getApp('<?php echo ($vo['app_id']); ?>');<?php echo ($returnFlase); ?>">下载</a>
							</div>
						</li><?php endforeach; endif; else: echo "" ;endif; ?>
				</ul>
				<?php if((trim($page))  !=  ""): ?><table class="pager" style="width: 460px;" cellspacing="0">
					<tbody>
						<tr>
							<td class="cl"></td>
							<td>跳转至：</td>
							<td>
								<div class="ajaxpage"> <?php echo ($page); ?></div>
							</td>
							<td class="cr"></td>
						</tr>
					</tbody>
				</table><?php endif; ?>
			</div>
		</div>
	</div>
	<div style="clear:both;"></div>
</div>
<script type="text/javascript">
	$(function(){
		$('#searchMax').change(function(){
			var searchUrl = "<?php echo U('App-Search/index', array_merge($params, array('key_word' => $key_words)));?>";
			searchUrl = searchUrl.replace(/\/listRow\/\w{0,2}/, '/listRow/' + encodeURIComponent($(this).val()));
			document.location.href=searchUrl;
		});
	})
</script>