<?php

/*
 * @Description 易宝支付非银行卡支付专业版接口范例 
 * @V3.0
 * @Author yang.xu
 */
  	
	/* 商户编号p1_MerId,以及密钥merchantKey 需要从易宝支付平台获得*/
	$p1_MerId	= "10001167060";
	$merchantKey	= "0Lg1214E4139g483Q3u9v32ZadMI0K35E968t8eVF6x2932J6G12nr8z208B";
	$logName	= "YeePay_CARD.log";
	# 非银行卡支付专业版请求地址,无需更改.
	$reqURL_SNDApro		= "https://www.yeepay.com/app-merchant-proxy/command.action";
	# 非银行卡支付专业版测试地址,无需更改.
	#$reqURL_SNDApro		= "http://tech.yeepay.com:8080/robot/debug.action";
?> 
