<?php
/*
 * @Description 易宝支付非银行卡支付专业版接口范例 
 * @V3.0
 * @Author yang.xu
 */

	include 'YeePayCommon.php';	

	#商家设置用户购买商品的支付信息.
	#商户订单号.提交的订单号必须在自身账户交易中唯一.
	$p2_Order			= $_POST['p2_Order'];

	#支付卡面额
	$p3_Amt				= $_POST['p3_Amt'];

	#是否较验订单金额
	$p4_verifyAmt		= $_POST['p4_verifyAmt'];

	#产品名称
	$p5_Pid				= $_POST['p5_Pid'];

	#iconv("UTF-8","GBK//TRANSLIT",$_POST['p5_Pid']);
	#产品类型
	$p6_Pcat			= $_POST['p6_Pcat'];

	#iconv("UTF-8","GBK//TRANSLIT",$_POST['p6_Pcat']);
	#产品描述
	$p7_Pdesc			= $_POST['p7_Pdesc'];

	#iconv("UTF-8","GBK//TRANSLIT",$_POST['p7_Pdesc']);
	#商户接收交易结果通知的地址,易宝支付主动发送支付结果(服务器点对点通讯).通知会通过HTTP协议以GET方式到该地址上.	
	$p8_Url				= $_POST['p8_Url'];

	#临时信息
	$pa_MP				= $_POST['pa_MP'];

	#iconv("UTF-8","GB2312//TRANSLIT",$_POST['pa_MP']);
	#卡面额
	$pa7_cardAmt			= arrToStringDefault($_POST['pa7_cardAmt']);

	#支付卡序列号.
	$pa8_cardNo			= arrToStringDefault($_POST['pa8_cardNo']);

	#支付卡密码.
	$pa9_cardPwd			= arrToStringDefault($_POST['pa9_cardPwd']);

	#支付通道编码
	$pd_FrpId			= $_POST['pd_FrpId'];

	#应答机制
	$pr_NeedResponse		= $_POST['pr_NeedResponse'];

	#用户唯一标识
	$pz_userId			= $_POST['pz_userId'];

	#用户的注册时间
	$pz1_userRegTime		= $_POST['pz1_userRegTime'];

	
	#非银行卡支付专业版测试时调用的方法，在测试环境下调试通过后，请调用正式方法annulCard
	#两个方法所需参数一样，所以只需要将方法名改为annulCard即可
	#测试通过，正式上线时请调用该方法
	annulCard($p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pz_userId,$pz1_userRegTime);
?> 
