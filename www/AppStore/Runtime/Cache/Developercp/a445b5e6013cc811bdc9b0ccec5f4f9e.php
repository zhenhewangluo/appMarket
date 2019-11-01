<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="../Public/css/developer.css">
<div class="content_box">
	<div class="position"><span>开发者用户中心</span><span>></span><span>用户资料管理</span><span>></span><span>密码修改</span></div>
<!-- layout::Layout:developerbar::0 -->

	<div class="form_content"  id="step3" style="margin:0 auto; border:1px solid #DDD; min-height:413px;">
<h1 style="margin:20px 0 20px 20px; display:block; color:#53A6D1;">密码修改 </h1>
			<form action='<?php echo U("AppStore://Developercp-Index/modifypassAction");?>' method="post" class="form_content" style="margin-left:20px;">
				<div id="dev_person">
					
					<table  class="table" cellpadding="12" cellspacing="0" border=0>                   	 
                       <tr>
							<th class="left"><span>*</span>名称/团队名称：</th>
							<td> <input type="text" name="teamname" value="<?php echo ($teampass["teamname"]); ?>"/>
                            </td>
						</tr>
                        <tr>
							<td class="left"><span>*</span>原密码：</td>
							<td><input class="long" name="passwordold" type="password" value=""/></td>
						</tr>
                        <tr>
							<td class="left"><span>*</span>新密码：</td>
							<td><input class="long" name="password" type="password" value=""/></td>
						</tr>
                        <tr>
							<td class="left"><span>*</span>确认：</td>
							<td><input class="long" name="passwordcheck" type="password" value=""/></td>
						</tr>
						<tr>
							<td class="left"></td>
							<td><input type="submit" value="提交" /></td>
						</tr>
					</table>
                   
				</div>
               
			</form>

	</div>
</div>