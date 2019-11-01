<?php
/*
 *  功能：  测试 mail 函数
 *  作者：  bluesie  2010-06-24
 *  
 */
header("Content-Type:text/html;charset=utf-8");
/*
include_once("../class/MailSmtp.class.php");
//发送邮件
//smtp
$MailSet['host'] = "smtp.gmail.com";    //SMTP
$MailSet['user'] = "hjapp@gmail.com";      //SMTP    bluesie@smartermob.com
$MailSet['pass'] = "wyslmt";          //SMTP   smartermob

$subject = iconv("UTF-8","gb2312","密码重置确认（系统自动生成-请勿回复此邮件）");
$content = "密码重置确认";
$to = "hjapp@gmail.com";
$from = "管理员";
$from = iconv("UTF-8","gb2312",$from);
$smtp = new MailSmtp($MailSet['host'],25,true,$MailSet['user'],$MailSet['pass']);
$smtp->send_mail($to, $from, $subject, $content, "TXT");
print_r($smtp);
*/
/*
include_once("../class/mail.class.php");
$mail = new Email();
$mail->setTo("hjapp@gmail.com"); //收件人 
//$mail-> setCC（"b@b.com,c@c.com"）; //抄送 
//$mail-> setCC（"d@b.com,e@c.com"）; //秘密抄送 
$mail->setFrom("bluesie1126@163.com");//发件人 
$mail->setSubject("主题 16:44") ; //主题 
$mail->setText("内容 内容") ;//发送文本格式也可以是变量 
//$mail->setHTML(“html格式”) ;//发送html格式也可以是变量 
//$mail->setAttachments(“c:a.jpg”) ;//添加附件,需表明路径 
$mail->send(); //发送邮件 
*/
$action = $_POST['action'];
if($action)
{
	include_once("smtp.class.php");

	$smtpserver = "smtp.163.com";//SMTP服务器


	$smtpserverport =25;//SMTP服务器端口


	$smtpusermail = "bluesie1126@163.com";//SMTP服务器的用户邮箱


	//$smtpemailto = "bluesie1126@163.com";//发送给谁
	$smtpemailto = trim($_POST['email']);


	$smtpuser = "bluesie1126@163.com";//SMTP服务器的用户帐号


	$smtppass = "wyslmt";//SMTP服务器的用户密码


	$mailsubject = "Test Subject ".date("Y-m-d H:i:s");//邮件主题


	$mailbody = "<h1>This is a test mail</h1>";//邮件内容


	$mailtype = "HTML";//邮件格式（HTML/TXT）,TXT为文本邮件


	$smtp = new smtp($smtpserver,$smtpserverport,true,$smtpuser,$smtppass);//这里面的一个true是表示使用身份验证,否则不使用身份验证.


	$smtp->debug = TRUE;//是否显示发送的调试信息


	$smtp->sendmail($smtpemailto, $smtpusermail, $mailsubject, $mailbody, $mailtype);


	echo "ok";
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> New Document </TITLE>
  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8" /> 
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
 </HEAD>

 <BODY>


 <b>test send email </b><br/>
 <hr length=600/><br/>
 <form  method="POST" name="test_24" action="?">
 email : <input type=text name="email" value="" /> 
 <input name="action" type="hidden" value=1 /> <input type="submit">
 </form>
 <br/>
 </BODY>
</HTML>