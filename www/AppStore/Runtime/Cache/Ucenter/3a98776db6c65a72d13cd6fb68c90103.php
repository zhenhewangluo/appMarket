<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="../Public/js/registration.js"></script>
<script type="text/javascript">
    var gl_localPrefix = '/en/android';
    var gl_fatalMsg = 'An unexpected error has occured.';
</script>
<div class="content">
	<div class="left">
		<h1 id="member_h">绑定<?php echo ($bindtype); ?></h1>
		<p id="member_intro"></p>
		<div class="box">
			<div class="topError" id="registrationError" style="display:none">
			</div>
			<div class="top"></div>
			<div class="body">
				<?php $activityName = C('ACTIVITY_NAME');;?>
				<?php $bbsName = C('BBS_NAME');;?>
				<form id="formRegister" action="<?php echo U('Ucenter-Index/bindAct');?>" method="post">
					<input type="hidden" name='callbackUrl' value="<?php echo ($callbackUrl); ?>" />
					<input type="hidden" name="acttype" value="<?php echo ($acttype); ?>" />
					<?php if(($acttype)  ==  $activityName): ?><p>
							<label for="regMobileAddress">手机号：</label>
							<input type="text" class="inputbox" id="regMobileAddress" name="mobile" maxlength="200" pattern="mobi" required value="" />
							<span class="error" style="display:none"></span>
						</p>
						<p>
							<label></label>
							<input type="button" id="sendVerify" name="sendVerify" value="发送手机验证码" />
						</p>
						<div class="hr"></div>
						<p>
							<label for="mobileVerify">手机验证码：</label>
							<input type="text" class="inputboxSmall" id="mobileVerify" maxlength="6" name="mobileVerify" value="" pattern="verify" required />
							<span class="error" style="display:none"></span>
						</p>
					<?php elseif($acttype == $bbsName): ?>
						<p>
							<label for="regEmailAddress">昵　称：</label>
							<input type="text" class="inputbox" id="regUsername" name="username" maxlength="200" pattern="username" required value="" />
							<span class="error" style="display:none"></span>
						</p>
						<p class="captchaLeft">
							<label>验证码：</label>
							<img width="100" height="35" src="<?php echo U('Base/verify');?>" style="width:150px; height:50px" id="captchaImg" />
							<span class="newCaptcha"><a id="newCaptcha" href="#">换一张</a></span>
						</p>
						<p class="captchaRight">
							<label>Code from image</label><br/>
							<input type="text" class="inputboxSmall" maxlength="6" name="verifyCard" value="" pattern="verify" required />
							<span class="error" style="display:none">test</span>
						</p>
					<?php else: ?>
						<p>
							<label for="regEmailAddress">E-mail：</label>
							<input type="text" class="inputbox" id="regEmailAddress" name="email" maxlength="200" pattern="email" required value="" />
							<span class="error" style="display:none"></span>
						</p>
						<p class="captchaLeft">
							<label>验证码：</label>
							<img width="100" height="35" src="<?php echo U('Base/verify');?>" style="width:150px; height:50px" id="captchaImg" />
							<span class="newCaptcha"><a id="newCaptcha" href="#">换一张</a></span>
						</p>
						<p class="captchaRight">
							<input type="text" class="inputboxSmall" maxlength="6" name="verifyCard" value="" pattern="verify" required />
							<span class="error" style="display:none">test</span>
						</p><?php endif; ?>
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
		$('#sendVerify').click(function(){
			$.getJSON('<?php echo U("Ucenter-Ajax/sendVerify");?>', {mobile : $('input:[name="mobile"]').val(), type : 'bind'}, function(data){
				if(data.status == 1)
				{
					//do something...
				}
				alert(data.msg);
			});
		});
		$('input:[name="email"]').blur(function(){
			if($(this).attr('class').indexOf('invalid-text') == -1 && $(this).next().attr('class').indexOf('invalid-msg') == -1 && $.trim($(this).val().length) > 0)
				checkUserExisit('email', $(this).val(), $(this));
		});
		$('input:[name="mobile"]').blur(function(){
			if($(this).attr('class').indexOf('invalid-text') == -1 && $(this).next().attr('class').indexOf('invalid-msg') == -1 && $.trim($(this).val().length) > 0)
				checkUserExisit('phone', $(this).val(), $(this));
		});
		$('input:[name="username"]').blur(function(){
			if($(this).attr('class').indexOf('invalid-text') == -1 && $(this).next().attr('class').indexOf('invalid-msg') == -1 && $.trim($(this).val().length) > 0)
				checkUserExisit('username', $(this).val(), $(this));
		});
	});

	function checkUserExisit(checkType, checkVal, obj)
	{
		$.post("<?php echo U('Ucenter-Ajax/checkUserExisit');?>", {checkType: checkType, checkVal: checkVal},function(data){
			if(data.isExisit > 0)
			{
				obj.removeClass('valid-text').addClass('invalid-text');
				obj.next().removeClass('valid-msg').addClass('invalid-msg').html(data.msg).show();
			}
		}, 'JSON');
	}
</script>