<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="__PUBLIC__/js/autovalidate/style.css">
<script type="text/javascript" src="__PUBLIC__/js/autovalidate/validate.js"></script>
<script language="JavaScript">
function changeVerify(){
	var timenow = new Date().getTime();
	document.getElementById('verifyImg').src='__URL__/verify/'+timenow;  
}
</script>
<script type="text/javascript" src="../Public/js/jquery.js"></script>
<script type="text/javascript" src="../Public/js/jquery.form.js"></script>
<script language="JavaScript">
<!--
function checkName(){
    $.post('__URL__/checkName',{'username':$('#username').val()},function(data){
        $('#result').html(data.info).show();
        $("#result").fadeOut(4000);
    },'json');
}

$(function(){
    $('#form1').ajaxForm({
        beforeSubmit:  checkForm,  // 表单提交执行前检测
        success:       complete,  // 表单提交后执行函数
        dataType: 'json'
    });
    function checkForm(){
        if( '' == $.trim($('#username').val())){
            $('#result').html('用户名不能为空！').show();
            $("#result").fadeOut(4000);
            $('#username').focus();
            return false;
        }
        // 可以在此添加其它判断
    }
    function complete(data){
        if(data.status==1){
            $('#result').html(data.info).show();
            // 更新列表
            username = data.data;
            $('#list').html('<span style="color:blue">'+username+'你好!</span>');
        }else{
            $('#result').html(data.info).show();
            // 隐藏上次遗留的信息
            $('#list').hide();
        }
    }
});
//-->
</script>
<div class="position" style="width:930px; margin:0 auto;">
	<a href ="__APP__">首页</a>&nbsp;>&nbsp;开发者个人/团队注册&nbsp;>&nbsp;
</div>
<div class="simple_box" style="width:930px; margin:0 auto;">
	<div class="simple_box_t"></div>
	<div class="simple_box_c content">
		<div class="pencil"></div>
		<h1>开发者注册流程</h1>
		<div class="steps">
			
		</div>
		<div class="reg_form" id="step1" style="display:<?php echo ($step[1]); ?>">
			<form action="<?php echo U('AppStore://Developercp-Index/registerAction');?>" method="post" name="team">             
				<table>
					<col width="150px">
					<col />
					<tr>
						<th class="t_r">
							用户名：
						</th>
						<td>
							<input class="long" type="text" name="teamname"  alt="请输入要注册的用户名" /><label>　</label>
						</td>
					</tr>
                    <tr>
						<th class="t_r">
							密码：
						</th>
						<td>
							<input  type="password" name="password"  alt="" /><label>　</label>
						</td>
					</tr>
                    <tr>
						<th class="t_r">
							确认密码：
						</th>
						<td>
							<input class="long" type="password" name="repassword"  alt="" /><label>　</label>
						</td>
					</tr>
                    <tr>
						<th></th>
						<td>
							<input type="text" name="verify" size="4"><img id="verifyImg" src="__APP__/Public/verify/" onClick="changeVerify()" title="点击刷新验证码"/> 
						</td>
					</tr>
					<tr>
						<th></th>
						<td>
						<input class="user_submit" type="submit" value="提交" /> 
						</td>
					</tr>
				</table> 
                           
			</form>
            
		</div>
		<!--<div class="reg_form" id="step2" style="display:<?php echo ($step[2]); ?>">
			邮件已经发送至您填写的邮箱，请查收，并根据提示完成注册………<br /><br /><br /><br />
		</div>-->
		<div id="step4" style="display:<?php echo ($step[4]); ?>">
			<div class="complete">
				您的资料已经成功提交，工作人员会在1-2个工作日内完成审核，审核结果会以邮件形式通知您，请注意查收！<br />
				如有任何问题或需要帮助，请发邮件至：<a>kefu@hjapp.com</a>
			</div>
		</div>

		
	</div>
	<div class="simple_box_b"></div>
</div>