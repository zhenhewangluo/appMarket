<?php if (!defined('THINK_PATH')) exit();?><div class="headbar">
	<div class="position"><span>用户</span><span>></span><span>用户管理</span><span>></span><span><?php if(isset($user['id'])): ?>编辑用户<?php else: ?>添加用户<?php endif; ?></span></div>
</div>
<div class="content_box">
	<div class="content form_content">
		<form action="<?php echo U('Admin://User/userSave');?>" method="post" name="memberForm">
			<table class="form_table">
				<col width="150px" />
				<col />
				<tr>
					<th>用户名：</th>
					<td>
						<?php if(isset($user['id'])): ?><?php echo ($user['name']); ?>
							<input name="name" value="<?php echo ($user['name']); ?>" type="hidden" />
							<input name="id" value="<?php echo ($user['id']); ?>" type="hidden" />
						<?php else: ?>
							<input class="normal" name="name" type="text" value="<?php echo ($user['name']); ?>" pattern="required" alt="用户名不能为空" /><label>* 用户名称（必填）</label><?php endif; ?>
					</td>
				</tr>
				<?php if(isset($user['registered_time'])): ?><tr>
					<th>注册时间：</th>
					<td><?php echo ($user['registered_time']); ?></td>
				</tr><?php endif; ?>
				<?php if(isset($user['RegisteredUserHead']['path'])): ?><tr>
					<th>用户头像：</th>
					<td>
						<?php $userFaceArr = explode(',', $user['RegisteredUserHead']['path']);?>
						<img src="__ROOT__/<?php echo ($userFaceArr[1]); ?>" />
					</td>
				</tr><?php endif; ?>
				<tr>
					<th>是否验证通过：</th>
					<td>
						<label><input name="status" type="radio" value="1" <?php if($user['status'] == 1): ?>checked="checked"<?php endif; ?> />是</label>
						<label><input name="status" type="radio" value="0" <?php if($user['status'] == 0): ?>checked="checked"<?php endif; ?> />否</label>
					</td>
				</tr>
				<tr>
					<th>Email：</th>
					<td>
						<?php if(isset($user['email'])): ?><?php echo ($user['email']); ?>
						<input type="hidden" name="email" value="<?php echo ($user['email']); ?>"/>
						<?php else: ?>
						<input type="text" class="normal" name="email" pattern="email" alt="邮箱错误"/><label>* 邮箱不能为空</label><?php endif; ?>
					</td>
				</tr>
				<tr>
					<th>Email是否公开：</th>
					<td>
						<label><input name="email_ispublic" type="radio" value="1" <?php if($user['email_ispublic'] == 1): ?>checked="checked"<?php endif; ?> />是</label>
						<label><input name="email_ispublic" type="radio" value="0" <?php if($user['email_ispublic'] == 0): ?>checked="checked"<?php endif; ?> />否</label>
					</td>
				</tr>
				<tr>
					<th>密码：</th>
					<td>
						<input class="normal" pattern="^\S{6,32}$" name="password" type="password" bind="repassword" empty  /><label><?php if(isset($user['id'])): ?>不修改密码，请保持为空<?php else: ?>* 登录密码（必填）<?php endif; ?></label>
					</td>
				</tr>
				<tr>
					<th>确认密码：</th>
					<td><input class="normal" pattern="^\S{6,32}$" name="repassword" type="password" bind="password" empty /><label>确认密码</label></td>
				</tr>
				<tr>
					<th>手机：</th>
					<td><input class="normal" name="phone" type="text" value="<?php echo ($user['phone']); ?>" empty pattern="mobi" alt="格式不正确" /><label>手机号码</label></td>
				</tr>
				<tr>
					<th>手机是否公开：</th>
					<td>
						<label><input name="mobile_ispublic" type="radio" value="1" <?php if($user['mobile_ispublic'] == 1): ?>checked="checked"<?php endif; ?> />是</label>
						<label><input name="mobile_ispublic" type="radio" value="0" <?php if($user['mobile_ispublic'] == 0): ?>checked="checked"<?php endif; ?> />否</label>
					</td>
				</tr>
				<tr>
					<th>QQ：</th><td><input class="normal" name="qq" type="text" value="<?php echo ($user['qq']); ?>" empty pattern="qq" alt="格式不正确" /><label>QQ号码</label></td>
				</tr>
				<tr>
					<th>QQ是否公开：</th>
					<td>
						<label><input name="qq_ispublic" type="radio" value="1" <?php if($user['qq_ispublic'] == 1): ?>checked="checked"<?php endif; ?> />是</label>
						<label><input name="qq_ispublic" type="radio" value="0" <?php if($user['qq_ispublic'] == 0): ?>checked="checked"<?php endif; ?> />否</label>
					</td>
				</tr>
				<tr>
					<th>微博地址：</th><td><input class="normal" name="weburl" type="text" value="<?php echo ($user['weburl']); ?>" empty pattern="url" alt="格式不正确" /><label>QQ号码</label></td>
				</tr>
				<tr>
					<th>微博是否公开：</th>
					<td>
						<label><input name="weburl_ispublic" type="radio" value="1" <?php if($user['weburl_ispublic'] == 1): ?>checked="checked"<?php endif; ?> />是</label>
						<label><input name="weburl_ispublic" type="radio" value="0" <?php if($user['weburl_ispublic'] == 0): ?>checked="checked"<?php endif; ?> />否</label>
					</td>
				</tr>
				<tr>
					<th>地址：</th><td><input class="normal" name="address" type="text" value="<?php echo ($user['address']); ?>" /><label>联系地址</label></td>
				</tr>
				<tr>
					<th>选择快递方式：<br />（用于发送奖品）</th>
					<td>
						<label><input name="expressdelivery" type="radio" value="1" <?php if($user['expressdelivery'] == 1): ?>checked="checked"<?php endif; ?> />是</label>
						<label><input name="expressdelivery" type="radio" value="0" <?php if($user['expressdelivery'] == 0): ?>checked="checked"<?php endif; ?> />否</label>
					</td>
				</tr>
				<tr>
					<td></td><td><button class="submit" type="submit" onclick="return check()"><span>确 定</span></button></td>
				</tr>
			</table>
		</form>
	</div>
</div>