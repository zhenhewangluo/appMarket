<?php if (!defined('THINK_PATH')) exit();?><?php if(($webUid)  <=  "0"): ?><a class="register" href="<?php echo U('Ucenter-Index/reg', array('acttype' => 'market'));?>" rel="nofollow">现在注册</a>
	<a href="#" id="openLogin" onclick="openLogin(); return false;">&raquo; 登录</a>
	<div id="boxLoginTop">
		<a href="#" id="closeLogin" onclick="closeLogin(); return false;">&laquo; 登录</a>
		<form id="loginForm" action="<?php echo U('Ucenter-Index/loginAct');?>" method="post">
			<input type="hidden" name="callbackUrl" value="<?php echo ($callbackUrl); ?>" />
			<p>
				<label for="emailAddress">邮箱 / 手机号</label>
				<label for="password">密　码</label>
			</p>
			<p>
				<input type="text" class="inputbox" id="emailAddress" name="username" value="" />
				<input type="password" class="inputbox" id="password" name="password" />
				<input type="submit" class="btnSubmit" id="loginSubmit" name="loginSubmit" value="登录" />
			</p>
			<p>
				<input type="checkbox" class="checkbox" id="rememberMe" name="rememberMe" value="1" /><label class="rememberMe" for="rememberMe">自动登录</label>
				<a id="remember" href="<?php echo U('Ucenter-Index/forgotPass', array('acttype' => 'market'));?>">忘记密码?</a>
			</p>
		</form>
	</div><?php endif; ?>