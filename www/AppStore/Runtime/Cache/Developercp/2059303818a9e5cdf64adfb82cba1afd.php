<?php if (!defined('THINK_PATH')) exit();?><link rel="stylesheet" type="text/css" href="../Public/css/developer.css">
<link rel="stylesheet" type="text/css" href="__PUBLIC/js/autovalidate/style.css">
<link rel="stylesheet" type="text/css" href="/hjapp/Public/js/autovalidate/style.css">
<script type="text/javascript">
	$(function(){
		$('#btRegister').click(function(){
			var phoneVal = $('input:[name="phone"]').val();
			var qqVal = $('input:[name="qq"]').val();
			var obj = {
				'emailIsPublic' : parseInt($('[name="email_ispublic"]:checked').val()),				
				//'phone' : (phoneVal == '') ? '' : parseInt(phoneVal),
				//'mobile_ispublic' : parseInt($('[name="mobile_ispublic"]:checked').val()),
				'qq' : (qqVal == '') ? '' : parseInt(qqVal),
				'qqIsPublic' : parseInt($('[name="qq_ispublic"]:checked').val()),
				'webUrl' : $('input:[name="weburl"]').val(),
				'webUrlIsPublic' : parseInt($('[name="weburl_ispublic"]:checked').val()),
				'address' : $('input:[name="address"]').val(),
				'expressDelivery' : parseInt($('[name="expressdelivery"]:checked').val()),
				'__hash__' : $('#userinfo input:[name="__hash__"]').val() 
			};
			if($('#userinfo .invalid-msg').length > 0)
			{
				return false;
			}
			$.post("<?php echo U('Ucenter-Ajax/updateUserInfo');?>", obj, function(data){
				alert(data.msg);
			},'json');
			return false;
		});
	});
   //清空radio
    function clearRadio(radio_oj){
		for(var i=0;i<radio_oj.length;i++) //循环
		{
			if(radio_oj[i].value=='1') //比较值
			{ 
				radio_oj[i].checked=true; //修改选中状态
				break; //停止循环
			}
		}   
    }  //清空内容
    function clearInput(){
        $("input:text").attr('value','');
		$("input:password").attr('value','');
	//	clearRadio(document.userinfo.mobile_ispublic);
		clearRadio(document.userinfo.qq_ispublic);
		clearRadio(document.userinfo.weburl_ispublic);
		clearRadio(document.userinfo.expressdelivery);
		clearRadio(document.userinfo.email_ispublic);
		$('input').removeClass('invalid-text valid-text invalid-ie6-text');
		$('input + label').html('&nbsp;').removeClass('invalid-msg valid-msg');
		$('input + span').html('&nbsp;').removeClass('invalid-msg valid-msg');
    }
	function updateavatar() {
		window.location.reload();
	}
</script>
<div class="content_box">
	<div class="position"><span>开发者管理中心</span><span>></span><span>用户资料</span><span>></span><span>上传头像</span></div>
	<!-- layout::Layout:developerbar::0 -->	
	<div class="form_content"  id="step3" style="margin:0 auto; border:1px solid #DDD; min-height:413px; background:none; min-height:456px;">
		<h1 style="margin:20px 0 20px 20px; display:block; color:#53A6D1;">上传头像 </h1>
		<div style="width:200px; float:left; overflow:hidden; display:inline; margin-left:20px;"><span>我现在的头像：</span><img src="__ROOT__/<?php echo ($webUhead[1]); ?>" /></div>
        <div style="margin:20px; width:460px; float:left; overflow:hidden; display:inline;"><span style="margin-bottom:20px; display:block;"><?php echo ($flash); ?> </span>      
        <img src="__ROOT__/<?php echo ($webUhead[0]); ?>" />
		<img src="__ROOT__/<?php echo ($webUhead[1]); ?>" />
		<img src="__ROOT__/<?php echo ($webUhead[2]); ?>" />
        </div>    
	</div> 
</div>