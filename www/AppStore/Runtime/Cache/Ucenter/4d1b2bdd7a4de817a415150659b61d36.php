<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="../Public/js/registration.js"></script>
<script type="text/javascript">
    var gl_localPrefix = '/en/android';
    var gl_fatalMsg = 'An unexpected error has occured.';
</script>
<style type="text/css">
	div.slideTabContainer{margin:20px 0;}
	h5.slideTab{margin:0 20px;display:inline-block;*float:left;}
	h5.slideTab a{color:#43A8DA;text-decoration: none;margin:20px 0;}
	h5.slideTab a:hover{text-decoration: underline;}
</style>
<div class="content">
	<div class="left">
		<h1 id="member_h">密码找回</h1>
		<p id="member_intro"></p>
		<div class="box">
			<div class="top"></div>
			<div class="body">
				<div class="slideTabContainer">
					<h5 class="slideTab"><a href="<?php echo U('Ucenter-Index/forgotPass', array('acttype' => 'market'));?>">使用E-mail找回</a></h5>
					<h5 class="slideTab"><a href="<?php echo U('Ucenter-Index/forgotPass', array('acttype' => C('ACTIVITY_NAME')));?>" >使用手机号重置密码</a></h5>
				</div>
				<?php $realType = C('ACTIVITY_NAME');;?>
				<form id="formRegister" action="<?php echo U('Ucenter-Index/forgotPassAct');?>" method="post" style="*clear:both;">
					<input type="hidden" name='callbackUrl' value="<?php echo ($callbackUrl); ?>" />
					<input type="hidden" name="acttype" value="<?php echo ($acttype); ?>" />
					<?php if(($acttype)  !=  $realType): ?><p>
							<label for="regEmailAddress">填写注册E-mail：</label>
							<input type="text" class="inputbox" id="regEmailAddress" name="email" maxlength="200" pattern="email" required value="" />
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
							<span class="error" style="display:none">test</span>
						</p>
					<?php else: ?>
						<p>
							<label for="regMobileAddress">手机号：</label>
							<input type="text" class="inputbox" id="regMobileAddress" name="mobile" maxlength="200" pattern="mobi" required value="" />
							<span class="error" style="display:none"></span>
						</p>
						<p>
							<label></label>
							<input type="button" id="sendVerify" name="sendVerify" value="发送手机验证码" />
						</p>
						<p>
							<label for="regMobilePassword">新密码：</label>
							<input type="password" class="inputbox" id="regMobilePassword" name="password" pattern="pass" required bind="repassword" maxlength="200" value="" />
							<span>至少6位字符</span>
							<span class="error" style="display:none"></span>
						</p>
						<p>
							<label for="regMobilePassword2">确认密码：</label>
							<input type="password" class="inputbox" id="regMobilePassword2" name="repassword" pattern="pass" required bind="password" maxlength="200" value="" />
							<span class="error" style="display:none"></span>
						</p>
						<div class="hr"></div>
						<p>
							<label for="mobileVerify">手机验证码：</label>
							<input type="text" class="inputboxSmall" id="mobileVerify" maxlength="6" name="mobileVefiry" value="" pattern="verify" required />
							<span class="error" style="display:none"></span>
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
<?php
	if(!$isIe6){
?>
<script type="text/javascript">
	$(function(){
		$('#newCaptcha').click(function(){
			var timenow = new Date().getTime();
			$('#captchaImg').attr('src', "<?php echo U('Base/verify?random=');?>"+timenow);
			return false;
		});
		$('#sendVerify').click(function(){
			$.getJSON('<?php echo U("Ucenter-Ajax/sendVerify");?>', {mobile : $('input:[name="mobile"]').val(), type : 'forgot'}, function(data){
				if(data.status == 1)
				{
					//do something...
				}
				alert(data.msg)
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
			if(data.isExisit < 0)
			{
				obj.removeClass('valid-text').addClass('invalid-text');
				obj.next().removeClass('valid-msg').addClass('invalid-msg').html(data.msg).show();
			}
		}, 'JSON');
	}
</script>
<?php
	}
	else
	{
?>
<script type="text/javascript">
	$(function(){
		$('#newCaptcha').click(function(){
			var timenow = new Date().getTime();
			$('#captchaImg').attr('src', "<?php echo U('Base/verify?random=');?>"+timenow);
			return false;
		});
		$('#sendVerify').click(function(){
			$.getJSON('<?php echo U("Ucenter-Ajax/sendVerify");?>', {mobile : $('input:[name="mobile"]').val(), type : 'forgot'}, function(data){
				if(data.status == 1)
				{
					//do something...
				}
				alert(data.msg)
			});
		});
		$('input:[name="email"]').blur(function(){
			
			if($(this).attr('class').indexOf('invalid-ie6-text') == -1 && $(this).next().attr('class').indexOf('invalid-msg') == -1 && $.trim($(this).val().length) > 0)
				checkUserExisit('email', $(this).val(), $(this));
		});
		$('input:[name="mobile"]').blur(function(){
			if($(this).attr('class').indexOf('invalid-ie6-text') == -1 && $(this).next().attr('class').indexOf('invalid-msg') == -1 && $.trim($(this).val().length) > 0)
				checkUserExisit('phone', $(this).val(), $(this));
		});
		$('input:[name="username"]').blur(function(){
			if($(this).attr('class').indexOf('invalid-ie6-text') == -1 && $(this).next().attr('class').indexOf('invalid-msg') == -1 && $.trim($(this).val().length) > 0)
				checkUserExisit('username', $(this).val(), $(this));
		});

	});

	function checkUserExisit(checkType, checkVal, obj)
	{
		$.post("<?php echo U('Ucenter-Ajax/checkUserExisit');?>", {checkType: checkType, checkVal: checkVal},function(data){
			if(data.isExisit < 0)
			{
				obj.removeClass('valid-text').addClass('invalid-ie6-text');
				obj.next().removeClass('valid-msg').addClass('invalid-msg').html(data.msg).show();
			}
		}, 'JSON');
	}
</script>
<?php
	}
?>