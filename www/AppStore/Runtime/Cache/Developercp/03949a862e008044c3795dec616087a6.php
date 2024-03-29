<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=7, IE=9" />
		<title><?php echo ($title); ?><?php echo ($sub_title); ?><?php echo ($page_title); ?><?php echo ($app_title); ?></title>
		<meta name="generator" content="<?php echo ($meta_generator); ?>" />
		<meta name="keywords" content="<?php echo ($meta_keywords); ?>" />
		<meta name="description" content="<?php echo ($meta_description); ?>" />
		<link type="image/x-icon" href="__PUBLIC__/images/favicon.ico?<?php echo rand();?>" rel="icon" />
		<link type="image/x-icon" href="__PUBLIC__/images/favicon.ico?<?php echo rand();?>" rel="bookmark" />

		<script type="text/javascript" src="__PUBLIC__/js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="__PUBLIC__/js/jquery.easing.1.3.js"></script>
		<script type="text/javascript" src="__PUBLIC__/js/jquery.lavalamp.min.js"></script>
		<script src="../Public/js/common.js" type="text/javascript"></script>
		<script type='text/javascript' src="__PUBLIC__/js/artdialog/jquery.artDialog.js?skin=idialog"></script>
		<script type='text/javascript' src="__PUBLIC__/js/common.js"></script>
		<link rel="stylesheet" type="text/css" href="__PUBLIC__/js/autovalidate<?php echo ($ie6js); ?>/style.css" />
		<link href="../Public/css/<?php echo ($css); ?>style.css?<?php echo rand();?>" type="text/css" rel="stylesheet" />
		<script type='text/javascript' src="__PUBLIC__/js/autovalidate<?php echo ($ie6js); ?>/validate.js"></script>
		<script type="text/javascript">
			function getApp(aid)
			{
				$.getJSON('<?php echo U("App-Ajax/setIncDownloadsByAid");?>',{ aid : aid } ,function(data){
					if(data.status == 1)
					{
						$.dialog.open(data.url, {show:false});
					}
				});
				return false;
			}
		</script>
	</head>
	<body>
		<div id="userBarBox"></div>
		<!--header start-->
		<div id="containerHeader">
			<div class="content">
				<a id="pageTop"></a>
				<a id="logo" href="/" title="***">/</a>
				<div id="boxSearch">
					<form id="search" action="<?php echo U('App-Search/index');?>" method="get" accept-charset="UTF-8">
						<p>
							<input type="text" class="inputbox" id="searchWidget" name="st" value="请输入搜索关键词" />
							<input type="submit" id="search_submit" class="btnSubmit" title="搜索" value=""/>
						</p>
					</form>
				</div>
				<div id="loginFormBox"></div>
				<div id="lineGreen">
					<ul id="menu">
						<?php if((GROUP_NAME == 'App') and (MODULE_NAME == 'Index')): ?><li class="active_gray">
								<div class="left"></div>
								<div class="body"><a href="<?php echo U('App-Index/index');?>">聚应用</a></div>
								<div class="right"></div>
							</li>
							<?php else: ?>
							<li>
								<a href="<?php echo U('App-Index/index');?>">聚应用</a>
							</li>
							<?php
							if(GROUP_NAME != 'App' || (GROUP_NAME == 'App' && (MODULE_NAME != 'Apps' && MODULE_NAME != 'Index'))){
							?>
							<li class="separator">&nbsp;</li>
							<?php
							}
							?><?php endif; ?>
						<?php if((GROUP_NAME == 'App') and (MODULE_NAME == 'Apps')): ?><li class="active_gray">
								<div class="left"></div>
								<div class="body"><a href="<?php echo U('App-Apps/index');?>">应用中心</a></div>
								<div class="right"></div>
							</li>
							<?php else: ?>
							<li><a href="<?php echo U('App-Apps/index');?>">应用中心</a></li>
							<?php
							if(GROUP_NAME != 'App' || (GROUP_NAME == 'App' && MODULE_NAME != 'Apps')){
							?>
							<li class="separator">&nbsp;</li>
							<?php
							}
							?><?php endif; ?>
						<li><a href="/Article/plus/list.php?tid=2">评测</a></li>
						<li class="separator">&nbsp;</li>
						<li><a href="/Article/plus/list.php?tid=1">新闻</a></li>
						<li class="separator">&nbsp;</li>
						<li><a href="http://pyd.cc/">朋友多</a></li>
						<li class="separator">&nbsp;</li>
						<li><a href="http://bbs.hjapp.com/">论坛</a></li>
					</ul>
				</div>
			</div>
		</div>
		<div id="fb-root"></div>
		<!--header end-->
		<div id="containerContent" <?php if((GROUP_NAME == 'App') and (MODULE_NAME == 'Index' or MODULE_NAME == 'Apps' or MODULE_NAME == 'Search')): ?>class="appShop"<?php endif; ?>>
			<!-- layout::$viewcontent::0 -->
		</div>
		<!--footer start-->
		<div id="containerFooter">
			<div class="content">
				<div class="box">
					<h2>支持***</h2>
					<p>
						喜欢***吗？有您的支持，我们会带给您更好的服务！
					</p>
					<ul>
<!--						<li><a href="/en/android/sponsoring" rel="nofollow">成为赞助商</a> »</li>
						<li><a href="/en/android/app-seller" rel="nofollow">合作推广</a> »</li>
						<li><a href="/en/android/paypal/test-request/form" rel="nofollow">申请应用评测</a> »</li>-->
					</ul>
				</div>
				<div class="box">
					<h2>分享给好友</h2>
					<p>享受***的服务吗？赶快向您的好友分享这一美秒体验吧！</p>
					<div id="recommendError" style="display:none">
						<p id="recommendErrorContents"></p>
					</div>
<!--					<form action="#" method="post">
						<p>
							<label for="invSenderName">您的姓名:</label>
							<input type="text" class="inputbox" id="invSenderName" name="senderName" value="" />
						</p>
						<p>
							<label for="invSenderEmail">您的电子邮件:</label>
							<input type="text" class="inputbox" id="invSenderEmail" name="senderEmail" value="" />
						</p>
						<p>
							<label for="invReceiverEmail">朋友电子邮件:</label>
							<input type="text" class="inputbox" id="invReceiverEmail" name="receiverEmail" value="" />
						</p>
						<div class="hr"></div>
						<p>
							<input type="submit" class="btnSubmit" id="recommendSubmit" name="recommendSubmit" value="提　交" />
						</p>
					</form>-->
				</div>
				<div class="box">
					<h2>关键词搜索</h2>
					<object data="__PUBLIC__/flash/tagCloud/tagcloud.swf" type="application/x-shockwave-flash" width="228" height="220">
						<param name="movie" value="__PUBLIC__/flash/tagCloud/tagcloud.swf" />
						<param name="wmode" value="transparent" /> 
					</object>
				</div>
				<div class="box">
					<h2>问题 / 帮助</h2>
					<p>
						有任何问题，或想了解更多有关***的内容，请点击
					</p>
					<div class="hr"></div>
					<ul>
						<li><a href="http://bbs.hjapp.com/">论坛 »</a></li>
						<li><a href="<?php echo U('Block-Index/devNotes');?>">开发者须知 »</a></li>
						<li><a href="<?php echo U('Block-Index/agreement');?>">服务条款 »</a></li>
						<li><a href="/en/android/developers-info">常见问题 »</a></li>
					</ul>
					<div class="hr"></div>
					<h3>***微博</h3>
					<div id="bookmarkIcons">
						<ul id="bookmarks">
							<li id="bookmark_sina">
								<a href="http://weibo.com/huanjubao" title="***新浪微博" target="_blank" alt="***新浪微博"></a>
							</li>
							<li id="bookmark_tencent">
								<a href="http://t.qq.com/huanjubao" title="***腾讯微博" target="_blank" alt="***腾讯微博"></a>
							</li>
							<li id="bookmark_sohu">
								<a href="http://huanjubao.t.sohu.com" title="***搜狐微博" target="_blank" alt="***搜狐微博"></a>
							</li>
							<li id="bookmark_renren">
								<a href="http://page.renren.com/601403070" title="***人人主页" target="_blank" alt="***人人主页"></a>
							</li>
							<li id="bookmark_163">
								<a href="http://t.163.com/1689202311" title="***网易微博" target="_blank" alt="***网易微博"></a>
							</li>
							<li id="bookmark_douban">
								<a href="http://site.douban.com/146502/" title="***豆瓣小站" target="_blank" alt="***豆瓣小站"></a>
							</li>
						</ul>
					</div>
				</div>
				<div class="bottom">
					<a id="logoFooter" href="/" title="***">&nbsp;</a>
					<ul>
						<li><a href="/">网站首页</a></li>
						<li><a href="<?php echo U('Block-Index/aboutUs');?>">关于我们</a> </li>
						<li><a href="<?php echo U('Block-Index/agreement');?>">服务条款</a></li>
						<li><a href="<?php echo U('Block-Index/devNotes');?>">开发者须知</a></li>
						<li><a href="<?php echo U('Block-Index/disclaimer');?>">免责声明</a></li>
						<li class="last"><a href="<?php echo U('Block-Index/contactUs');?>">联系我们</a></li>
					</ul>
					<span>Copyright © 2010-2012 Hjapp Rights Reserved</span>
				</div>
			</div>
		</div>
		<!--footer end-->
		<script type="text/javascript">
			$(function(){
				var defaultText = $("#searchWidget").val();
				$("#searchWidget").bind({
					focus:function(){checkInput($(this),defaultText);},
					blur :function(){checkInput($(this),defaultText);}
				});
				$('#search_submit').click(function(){
					if($("#searchWidget").val() != defaultText){
						var searchUrl = '<?php echo U("AppStore://App-Search/index", array("key_word" => "@key_word@"));?>';
						searchUrl = searchUrl.replace('@key_word@',encodeURIComponent($('#searchWidget').val()));
						window.location=searchUrl;
					}
					return false;
				});
				$.getJSON('<?php echo U("App-Ajax/getUserInfo");?>', function(data){
					$('#commentForm').html(data.commentHtml);
					$('#userBarBox').html(data.userBarHtml);
					$('#loginFormBox').html(data.loginFormHtml);
				});
				$('#recommendSubmit').click(function(){
					var sendObj = {
						receiveEmail	: $('#invReceiverEmail').val(),
						sendEmail		: $('#invSenderEmail').val(),
						sendName		: $('#invSenderName').val()
					};
					var sendObjName = {
						sendName		: '您的姓名',
						sendEmail		: '您的邮箱',
						receiveEmail	: '朋友的邮箱'
					};
					for (objName in sendObj){
						if(sendObj[objName] == ''){
							alert(sendObjName[objName] + '不能为空！');
						}
					}
					$.getJSON('<?php echo U("Ucenter-Ajax/sendRecommendEmail");?>', sendObj, function(data){
						alert(data.msg);
					});
					return false;
				});
			});
		</script>
		<div style="display:none;">
			<script src="http://s84.cnzz.com/stat.php?id=4200679&web_id=4200679" type="text/javascript"></script>
		</div>
	</body>
</html>