<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="../Public/css/developer.css">
<link rel="stylesheet" type="text/css" href="__PUBLIC/js/autovalidate/style.css">
<link rel="stylesheet" type="text/css" href="/hjapp/Public/js/autovalidate/style.css">
<div class="content_box">
<div class="position"><span>开发者管理中心</span><span>></span><span>用户资料管理</span></div>
<!-- layout::Layout:developerbar::0 -->	
<div class="form_content"  id="step3" style="margin:0 auto; border:1px solid #DDD; min-height:413px;">
<h1 style="margin:20px 0 20px 20px; display:block; color:#53A6D1;">开发者资料编辑 </h1>				
			<form action='<?php echo U("AppStore://Developercp-Index/updateprofile/id/$teamprofile[id]");?>' method="post" style="margin-left:20px;">
				<div id="dev_person">
					<?php if(isset($teamprofile)): ?><table  class="table" cellpadding="12" cellspacing="0" border=0>
                    <tr>
							<th class="left"><span>*</span>我的头像：</th>
							<td> <img src="__ROOT__/<?php echo ($webUhead[1]); ?>" />
                            </td>
						</tr>                	 
                       <tr>
							<th class="left"><span>*</span>名称/团队名称：</th>
							<td> <input type="text" name="teamname" value="<?php echo ($teamprofile["teamname"]); ?>"/>
                            </td>
						</tr>				
						<tr>
							<td class="left"><span>*</span>身份证号：</td>
							<td><input class="long" name="idcard" type="text" value="<?php echo ($teamprofile["idcard"]); ?>"/></td>
						</tr>
                        <tr>
							<td class="left"><span>*</span>联系地址：</td>
							<td><input class="long" name="address" type="text" value="<?php echo ($teamprofile["address"]); ?>"/> </td>
						</tr>
						<tr>
							<td class="left"><span>*</span>联系电话：</td>
							<td><input class="long" name="phone" type="text" value="<?php echo ($teamprofile["phone"]); ?>"/> </td>
						</tr>
                        <tr>
							<td class="left"><span>&nbsp;</span>备用联系人：</td>
							<td><input class="long" name="sparecontact" type="text" value="<?php echo ($teamprofile["sparecontact"]); ?>"/> </td>
						</tr>
                        <tr>
							<td class="left"><span>&nbsp;</span>备用电话：</td>
							<td><input class="long" name="sparephone" type="text" value="<?php echo ($teamprofile["sparephone"]); ?>"/> </td>
						</tr>
						<tr>
							<td class="left">个人网站：</td>
							<td><input class="long" name="site" type="text"/> </td>
						</tr>
						<tr>
							<td class="left"></td>
							<td><input type="submit" value="提交" /></td>
						</tr>
					</table><?php endif; ?>
				</div>
               
			</form>
   
           
	</div> 
</div>