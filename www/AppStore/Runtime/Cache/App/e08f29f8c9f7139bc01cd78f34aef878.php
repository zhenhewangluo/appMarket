<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html lang="zh-cn">
	<head>
		<title>***</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta http-equiv="content-language" content="zh-cn" />
		<meta http-equiv="cache-control" content="no-cache" />
		<meta content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0;" name="viewport" />

		<meta name="viewport" content="width=320;"/>
		<link type="image/x-icon" href="__PUBLIC__/images/favicon.ico" rel="icon" />
		<link type="image/x-icon" href="__PUBLIC__/images/favicon.ico" rel="bookmark" />
		<style type="text/css">
			body { margin:0; padding:0; background-color:#000; color:#c0c0c0; font: normal normal 15px arial; }
			div.logo { width:320px; margin:10px auto 0 auto; }
			div.text { width:300px; margin:15px auto 0 auto; padding: 0 10px; }
			a.button { width:300px; height:36px; background:url('../Public/images/doorway_btn.png') #91b82b no-repeat top left; color:#000; display:block; text-decoration:none; margin: 8px auto; font: normal bold 18px/38px arial; text-align:center; }
			a.button:hover { color:#fff; }
			a.txt { width:300px; display:block; font: normal bold 16px arial; color:#769c11; text-align:center; }
		</style>
	</head>
	<body>
		<div class="logo" align="center">
			<img src="../Public/images/logo_top.jpg"  alt="***" />
		</div>
		<div class="text" style="margin-top:10px;">欢迎来到专注安卓，服务用户的***客栈，点击下载安装客户端便可浏览测试报告，博客。在应用中心可下载中意的应用并进行打分评论。</div>
		<div class="text">
			  
			<a class="button" href="<?php echo C('PICTURE_HOST');?><?php echo ($apk_path); ?>">安装客户端</a>
			
		</div>
		<div class="text">
			点击下方，也可选择用浏览器进行网页浏览  
			<a class="button" href="<?php echo U('App-Index/index', array('nomobile' => 1));?>">进入网站</a>
		</div>


	</body>
</html>