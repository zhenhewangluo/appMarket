<?php if (!defined('THINK_PATH')) exit();?><div class="content">
	<div class="left">
		<h1 id="member_h">修改密码</h1>
		<p id="member_intro"></p>
		<div class="box">
			<div class="topError" id="registrationError" style="display:none">
			</div>
			<div class="top"></div>
			<div class="body">
				<form id="formRegister" action="<?php echo U('Ucenter-Panel/updatePassAct');?>" method="post">
					<input type="hidden" name='callbackUrl' value="<?php echo ($callbackUrl); ?>" />
					<input type="hidden" name="acttype" value="<?php echo ($acttype); ?>" />
					<p>
						<label for="pwd">原　密　码：</label>
						<input type="password" class="inputbox" id="pwd" name="pwd" pattern="pass" maxlength="200" required value="" />
						<span class="error" style="display:none"></span>
					</p>
					<div class="hr"></div>
					<p>
						<label for="newpwd">新　密　码：</label>
						<input type="password" class="inputbox" id="newpwd" name="newpwd" pattern="pass" required maxlength="200" value="" />
						<span class="error" style="display:none"></span>
					</p>
					<p>
						<label for="newrepwd">重复新密码：</label>
						<input type="password" class="inputbox" id="regPassword" name="newrepwd" pattern="pass" required maxlength="200" value="" />
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