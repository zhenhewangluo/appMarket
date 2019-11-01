<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $cfg_soft_lang; ?>" />
<title><?php echo $GLOBALS['_vars']['spacename'];  echo $GLOBALS['_vars']['curtitle']; ?></title>
<link href="space/lxblog/images/style.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="showmenu" style="z-index:100;display:none;"></div>
<div class="wrap1"><div class="wrap2"><div class="wrap3">
<div dargarea='yes' id="container">
  <!--begin header-->
  <div id="header">
	<div id="title">
	  <div class="fr tar" id="toolbar">
		<a href="<?php echo $GLOBALS['cfg_basehost']; ?>" target="_blank"><?php echo $GLOBALS['cfg_basehost']; ?></a><br />	
		<?php if($cfg_ml->IsLogin()){ ?>
		<a href="index.php?uid=<?php echo $GLOBALS['cfg_ml']->M_LoginID; ?>">我的主页</a>
		| &nbsp;
		<a href="index.php">会员中心</a>
		| &nbsp;<a href="edit_space_info.php" target="_blank">空间管理</a> | <a href="index_do.php?fmdo=login&dopost=exit">注销</a>
 		<?php
}
else{
?>
		<a href="login.php">登录</a> | <a href="index_do.php?fmdo=user&dopost=regnew">注册</a>
 		<?php } ?>
	  </div>
	  <h1><a href="<?php echo $GLOBALS['_vars']['userurl']; ?>"><?php echo $GLOBALS['_vars']['spacename']; ?></a></h1>
	</div>
	<div class="c"></div>
	<ul id="guide">
	  <li><a href="index.php?uid=<?php echo $GLOBALS['_vars']['userid_e']; ?>">首页</a></li>
		<li><a href="index.php?uid=<?php echo $GLOBALS['_vars']['userid_e']; ?>&action=article">日志</a></li>
		<?php
		foreach($_vars['bloglinks'] as $k=>$v)
		{
			if($k==2) {
				echo "		<li><a href=\"index.php?uid={$_vars['userid_e']}&action=album\">相册</a></li>\r\n";
			}
			else {
				echo "		<li><a href=\"index.php?uid={$_vars['userid_e']}&action=archives&channelid={$k}\">{$v}</a></li>\r\n";
			}
		}
		?>
	  <li><a href="index.php?uid=<?php echo $GLOBALS['_vars']['userid_e']; ?>&action=guestbook">留言</a></li>
	  <li><a href="index.php?uid=<?php echo $GLOBALS['_vars']['userid_e']; ?>&action=infos">关于我</a></li>
	</ul>
  </div>
  <div class="c" ></div>