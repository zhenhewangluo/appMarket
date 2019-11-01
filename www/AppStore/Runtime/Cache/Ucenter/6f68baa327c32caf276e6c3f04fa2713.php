<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="../Public/js/registration.js"></script>
<script type="text/javascript">
    var gl_localPrefix = '/en/android';
    var gl_fatalMsg = 'An unexpected error has occured.';
</script>
<div class="content">
	<div class="left">
		<h1 id="member_h">密码重置</h1>
		<p id="member_intro"></p>
		<div class="box">
			<div class="topError" id="registrationError" style="display:none">
			</div>
			<div class="top"></div>
			<div class="body">
				<?php $realType = C('ACTIVITY_NAME');;?>
				<form id="formRegister" action="<?php echo U('Ucenter-Index/resetPassAct');?>" method="post">
					<input type="hidden" name="resetcode" value="<?php echo ($resetcode); ?>" />
					<input type="hidden" name='callbackUrl' value="<?php echo ($callbackUrl); ?>">
					<p>
						<label for="regPassword">新密码：</label>
						<input type="password" class="inputbox" id="regPassword" name="password" pattern="pass" required bind="repassword" maxlength="200" value="" />
						<span>至少6位字符</span>
						<span class="error" style="display:none"></span>
					</p>
					<p>
						<label for="regPassword2">确认密码：</label>
						<input type="password" class="inputbox" id="regPassword2" name="repassword" pattern="pass" required bind="password" maxlength="200" value="" />
						<span class="error" style="display:none"></span>
					</p>
					<div class="hr"></div>
					<p class="captchaLeft">
						<label>验证码：</label>
						<img width="100" height="35" src="<?php echo U('Base/verify');?>" style="width:150px; height:50px" id="captchaImg" />
						<span class="newCaptcha"><a id="newCaptcha" href="#">换一张</a></span>
					</p>
					<p class="captchaRight">
						<input type="text" class="inputboxSmall" maxlength="6" name="verifyCard" value="" pattern="verify" required />
						<span class="error" style="display:none"></span>
					</p>
					<div class="hr"></div>
					
					<p class="submit">
						<input type="submit" class="btnSubmit" name="submit" value="确 定" />
					</p>
				</form>
			</div>
			<div class="bottom"></div>
		</div>
	</div>
	<div class="right">
	</div>
</div>
<script type="text/javascript">
	$(function(){
		$('#newCaptcha').click(function(){
			var timenow = new Date().getTime();
			$('#captchaImg').attr('src', "<?php echo U('Base/verify?random=');?>"+timenow);
			return false;
		});
	});
</script>