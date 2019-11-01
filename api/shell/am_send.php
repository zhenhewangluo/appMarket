<?php
	/************************************************************
	 *   定时发送邮件召回：	超过一周以上未登录用户邮件召回
     *   服务器接口文件： am_send.php 
     *   发送时间：每周五上午8点
     *
     *   Author: liu jingqi
	 *   Create Time: 2010-10-21
	 *************************************************************/
	//header("Content-Type:text/html;charset=utf-8");
	require("../inc/config.inc.php");	
	require_once(AM_SITE_ROOT ."inc/error.eng.php");
	include(AM_SITE_ROOT ."inc/functions.php");
	include(AM_SITE_ROOT ."inc/channel_config.php");
	include(AM_SITE_ROOT ."inc/interface_config.php");
	include(AM_SITE_ROOT ."inc/Log.php");
	require(AM_SITE_ROOT ."class/smtp.class.php");	
	
	$today = getdate();
	if($today['wday'] != 5) exit;
	
	//连接数据库
	$conn = connect_comm_db();  
	if($conn === FALSE){
		////记录连接数据库失败
		file_put_contents("am_send_log.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}


	//// 取得所有注册用户UID和EMAIL
	$arrTest = $arrList = array();
	$sql = "select id,email from am_registered_user where email !=''";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
		$arrTest[] = $row;
	    
	}	
	@mysql_close($conn);
	$conn = connect_db();  
	for($i=0; $i<count($arrTest);$i++){
		$row = $arrTest[$i];
		$sql2 = "select id from am_send_log where uid='".$row['id']."' and type=3";
	    $rs2 = mysql_query($sql2, $conn);
		if(mysql_num_rows($rs2) <3 ){ ////发送该类邮件3次之内
			$t1 = strtotime(date("Y-m-d 00:00:00" , time()-3600*24*7));
			$sql3 = "select id from am_view_history where user_id='".$row['id']."' and viewtime>".$t1;
	    	$rs3 = mysql_query($sql3, $conn);
	    	if(mysql_num_rows($rs3) == 0){
	    		$arrList[] = $row['email'];
	    		
	    		$sql4 = "insert into am_send_log set uid='".$row['id']."',mid='',type=3,dateline='".time()."'";
				mysql_query($sql4, $conn);
	    	}
		}
	}
	@mysql_close($conn);
	
	//print_r($arrList);die;
	$subject = 'xxxx8-带给您最新最快的Android体验';
	$content = file_get_contents("am_send.html");
	$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件
	$smtp = new smtp($MailSet['host'],$MailSet['port'],true,$MailSet['user'],$MailSet['pass']);//这里面的一个true是表示使用身份验证,否则不使用身份验证.
	$smtp->debug = false;//是否显示发送的调试信息	
	//$arrList = array("41426@qq.com","liu.jingqi@smartermo.com");
	for($i=0; $i<count($arrList); $i++){
		$smtpemailto = trim($arrList[$i]);
		if(ereg("^([a-zA-Z0-9_-])+@([a-zA-Z0-9_-])+(.[a-zA-Z0-9_-])+",$smtpemailto)) {
			$smtp->sendmail($smtpemailto, $MailSet['user'], $subject, $content, $mailtype);
			file_put_contents("am_send_email".date("Ymd").".php" , $smtpemailto."\n", FILE_APPEND);
		}
	}
?>
