<?php

/*
 * @Description �ױ�֧�������п�֧��רҵ��ӿڷ��� 
 * @V3.0
 * @Author yang.xu
 */
	include 'YeePayCommon.php';	
			@file_put_contents("a.txt","1-----"."\n",FILE_APPEND );		 	
	#	�������ز���.
	$return = getCallBackValue($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,
$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac);
	#	�жϷ���ǩ���Ƿ���ȷ��True/False��
	$bRet = CheckHmac($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,
$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac);
	#	���ϴ���ͱ�������Ҫ�޸�.
$value = $deposit_id."|".$deposit_balance_after."|".$p8_cardStatus."|".$r1_Code."|".$p2_Order."|".date("Y-m-d H:i:s");
		$name = $uid."_callback";
		$sFile = $name.'.txt';
		@file_put_contents($sFile,$value."\n",FILE_APPEND );		 	
	#	У������ȷ.
	if($bRet){
		echo "success";
		  if($r1_Code=="1"){
		      echo "<br>֧���ɹ�!";
		      echo "<br>�̻�������:".$rb_Order;
		      echo "<br>֧�����:".$rc_Amt;
		      exit; 
		  } else if($r1_Code=="2"){
		      echo "<br>֧��ʧ��!";
		      echo "<br>�̻�������:".$p2_Order;
		      exit; 
		  }
		} else{
		
	$sNewString = getCallbackHmacString($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,
	$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct);
			echo "<br>localhost:".$sNewString;	
			echo "<br>YeePay:".$hmac;
			echo "<br>����ǩ����Ч!";
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