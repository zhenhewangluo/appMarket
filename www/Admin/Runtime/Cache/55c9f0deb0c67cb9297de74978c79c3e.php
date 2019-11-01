<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title></title>
	<link rel="stylesheet" type="text/css" href="../Public/css/admin.css" />
	<link rel="stylesheet" type="text/css" href="__PUBLIC__/js/autovalidate/style.css" />
	<script type='text/javascript' src="__PUBLIC__/js/jquery-1.7.1.min.js"></script>
	<script type='text/javascript' src="__PUBLIC__/js/autovalidate/validate.js"></script>
	<script type='text/javascript' src="__PUBLIC__/js/artdialog/jquery.artDialog.js?skin=aero"></script>
	<script type='text/javascript' src="__PUBLIC__/js/form.js"></script>
	<script type='text/javascript' src="__PUBLIC__/js/common.js"></script>
	<script type='text/javascript' src="../Public/js/admin.js"></script>
	<script type='text/javascript' src="../Public/js/common.js"></script>
	<script type='text/javascript' src="../Public/js/menu.js"></script>
</head>
<body>
<div class="container">
	<div id="header">
		<div class="logo">
			<a href="{url:/system/default}"><img src="__PUBLIC__/images/logo.png" height="43" /></a>
		</div>
		<div id="menu">
			<ul name="menu"></ul>
		</div>
		<p><a href="<?php echo U('Admin://Sysuser/logout');?>">退出管理</a> <a href="<?php echo U('Admin://Apps/AppList');?>">后台首页</a> <a href="__ROOT__/index.php" target='_blank'>AppStore首页</a> <span>您好 <label class='bold'><?php echo ($webUname); ?></label>，当前身份 <label class='bold'><?php echo ($webUname_role); ?></label></span></p>
	</div>
	<div id="info_bar"><label class="navindex"><a href="{url:/system/navigation}">快速导航管理</a></label><span class="nav_sec">
	<a href="<?php echo ($item['url']); ?>" class="selected"><?php echo ($item['naviga_name']); ?></a>
	</span></div>
	<div id="admin_left">
		<ul class="submenu">
		</ul>
		<div id="copyright">
			
		</div>
	</div>
	<?php import('@.ORG.Menu');$menu = new Menu();;?>
	<script type='text/javascript'>
		var data = <?php echo $menu->submenu();?>;
		var current = '<?php echo $menu->current;?>';
		var url='<?php echo U("Admin://");?>';
		initMenu(data,current,url);
	</script>
	<div id="admin_right">
	<!-- layout::$viewcontent::0 -->
	</div>
	<div id="separator"></div>
</div>
<script type='text/javascript'>
	//隔行换色
	$(".list_table tr::nth-child(even)").addClass('even');
	$(".list_table tr").hover(
		function () {
			$(this).addClass("sel");
		},
		function () {
			$(this).removeClass("sel");
		}
	);
</script>
</body>
</html>