<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="__PUBLIC__/js/autovalidate/style.css">
<div class="position" style="width:930px; margin:0 auto;">
	<a href ="__APP__">首页</a>&nbsp;>&nbsp;开发者个人/团队注册&nbsp;>&nbsp;
</div>
<div class="simple_box_c content" style="width:930px; margin:0 auto;">
		<div class="pencil"></div>
		<h1>开发者登录</h1>
		<form action="<?php echo U('AppStore://Developercp-Index/loginAction');?>" method="post">
			<table>
				<col width="80px">
				<col />
				<tr>
					<th class="t_r">
						用户名：
					</th>
					<td>
						<input class="long" type="text" name="teamname" alt="请输入用户名"/><label>　</label>
					</td>
				</tr>
				<tr>
					<th class="t_r">
						密码：
					</th>
					<td>
						<input class="long" type="password" pattern="^\S{6,32}$" alt="密码必填且至少6位" name="password" /><label>　</label>
					</td>
				</tr>
				<tr>
					<th></th>
					<td>
						<input class="user_submit" type="submit" value="登录" /> <a href="<?php echo U('AppStore://App-Developercp/resetPass');?>">忘记密码？</a>
					</td>
				</tr>
			</table>
		</form>
		<p>还未成为开发者？ <span><a href="<?php echo U('AppStore://Developercp-Index/register');?>">立即注册</a></span></p>
	</div>