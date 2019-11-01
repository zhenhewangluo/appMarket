<?php if (!defined('THINK_PATH')) exit();?><div class="logincon">
	<div class="home_body_t1">
		<h6 class="hico_friend">用户登录</h6>
	</div>
	<div class="home_body_c1">
		<form method="post" action="<?php echo U('Admin://Simple/loginAction');?>" id="loginForm">
			<table>
				<tbody>
				<tr>
					<th class="t_r">账户：</th>
					<td><input class="long" type="text" name="name" value=""  pattern="required" alt="请输入账户名" /><label></label></td>
				</tr>
				<tr>
					<th class="t_r">密码：</th>
					<td><input class="long" type="password" name="password" value=""  pattern="required" alt="请输入密码" /><label></label></td>
				</tr>
				<tr>
					<th class="t_r">验证码：</th>
					<td><label><input class="long" type="text" name="verifyCard" value=""  pattern="required" alt="请输入验证码" /><img style='cursor:pointer' title='刷新验证码' src="<?php echo U('Admin://Simple/verify');?>" id='verifyImg' onClick="fleshVerify()" /></label></td>
				</tr>
				<tr>
					<th class="t_r"></th><td><label><input type="submit" class="user_submit"  value="登 录"/> </label></td>
				</tr>
				</tbody>
			</table>
		</form>
	</div>
	<div class="home_body_b1"></div>
	</div>
	<script type="text/javascript">
	function fleshVerify(){
	//重载验证码
	var timenow = new Date().getTime();
	$('#verifyImg').src= "<?php echo U('Admin://Simple/verify?random=');?>"+timenow;
	}
</script>