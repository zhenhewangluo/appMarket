<?php if (!defined('THINK_PATH')) exit();?><style type="text/css">
#devpbar ul li a { font-size:17px;}
#devpbar ul ul li a { font-size:13px;}
</style>
<div id="devpbar">
     <ul>
     <li><a href='<?php echo U("Developercp-Apps/appadd");?>'>应用发布</a></li>
     <li><a href='<?php echo U("Developercp-Apps/appList");?>'>APK管理</a>
     </li>
     <li><a href='<?php echo U("Developercp-Index/editprofile");?>'>用户资料</a></li>
    	 <ul style="display:block; margin-left:20px; padding-left:0;">
          	<li><a href='<?php echo U("Developercp-Index/devpAvatar");?>' >上传头像</a></li>
            <li><a href='<?php echo U("Developercp-Index/editprofile");?>'>修改资料</a></li>
            <li><a href='<?php echo U("Developercp-Index/modifypass");?>'>修改密码</a></li>            
        </ul>
     <li><a href='<?php echo U("Developercp-Index/agreement");?>'>授权条款</a></li>
     <li><a href='<?php echo U("AppStore://Developercp-Index/logoutAction");?>'>退出登陆</a></li>
     </ul>
     
     </div>