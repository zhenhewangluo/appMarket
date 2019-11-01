<?php

/*
 * @Description 易宝支付非银行卡支付专业版接口范例 
 * @V3.0
 * @Author yang.xu
 */
	include 'YeePayCommon.php';	
			@file_put_contents("a.txt","1-----"."\n",FILE_APPEND );		 	
	#	解析返回参数.
	$return = getCallBackValue($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,
$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac);
	#	判断返回签名是否正确（True/False）
	$bRet = CheckHmac($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,
$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac);
	#	以上代码和变量不需要修改.
$value = $deposit_id."|".$deposit_balance_after."|".$p8_cardStatus."|".$r1_Code."|".$p2_Order."|".date("Y-m-d H:i:s");
		$name = $uid."_callback";
		$sFile = $name.'.txt';
		@file_put_contents($sFile,$value."\n",FILE_APPEND );		 	
	#	校验码正确.
	if($bRet){
		echo "success";
		  if($r1_Code=="1"){
		      echo "<br>支付成功!";
		      echo "<br>商户订单号:".$rb_Order;
		      echo "<br>支付金额:".$rc_Amt;
		      exit; 
		  } else if($r1_Code=="2"){
		      echo "<br>支付失败!";
		      echo "<br>商户订单号:".$p2_Order;
		      exit; 
		  }
		} else{
		
	$sNewString = getCallbackHmacString($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,
	$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct);
			echo "<br>localhost:".$sNewString;	
			echo "<br>YeePay:".$hmac;
			echo "<br>交易签名无效!";
			exit; 
	}
  
?> 
<html>
<head>
<title>Return from YeePay Page</title>
</head>
<body>
</body>
</html>