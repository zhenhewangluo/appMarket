<?php
	header("Cache-Control: no-store, no-cache, must-revalidate");
	header("Cache-Control: post-check=0, pre-check=0", false);
	header("Pragma: no-cache");
	session_start();

	
	
	if(empty($_SESSION["uid"])){
		//未登陆状态
	   ?>
		<html>
<head>
<title>后台管理系统 - 登录界面</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link href="login.css" rel="stylesheet" type="text/css" />
<script  type="text/javascript">
function $(id) 
{
	return document.getElementById(id);
}
function check(obj)
{
	if(document.fLogin.username.value=='') 
	{ 
		alert('用户名不能为空');
		document.fLogin.username.focus();
		return false;
	}
	else if(document.fLogin.password.value=='')
	 { 
		alert('密码不能为空');
		document.fLogin.password.focus();
		return false;
	 } 
 return true;
}
</script> 
</head>
<body onload="document.getElementById('username').focus();">
	<div id="loginbody">
		<div class="lbody">
			<p align="right">
			
<br/><br/>
			<font style="font-size:14px;color:#2B5A74;text-decoration:none;">
			<b>后台管理</b></font>
			</p>
		</div>
		<div class="rbody">
			<form name="fLogin" method="post" action="pass.php" onsubmit="return check(this)">
			<p>登录名：<input type="text" name="username" class="inputs" id="username"></p>
			<p>密　码：<input type="password" name="password" class="inputs"></p>
			<p style="margin-left:160px"><input type="submit" name="key_login" value="登 录" class="btn" ></p>
			</form>
		</div>
	</div>
</body>
</html>
	   
	   
	   <?
	}else{
		//登陆状态
		
		echo "<script>window.location.href='main.php'</script>";
		exit;
	}
	
?>