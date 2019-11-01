<?php
/********************************************************************
 * @Description 19pay接口
 * @cxj
 * @Updtes: 2010-04-19 lixiaan am_user、am_deposit表均移至comm数据库
 *


 ********************************************************************/
	 //载入配置文件
	require("inc/config.inc.php");	
	require_once("inc/error.eng.php");
	require_once("inc/functions.php");
	require_once '19pay/19payCommon.php';	
	
	#	解析返回参数.
	//?version_id=3.00&merchant_id=4071&order_date=20100525&order_id=smartermob_1104&result=Y&amount=1.00&currency=RMB&pay_sq=GWA10052613475753320&pay_date=20100526134800&user_name=&user_phone=&user_mobile=&user_email=&order_pname=&order_pdesc=&count=1&card_num1=7cf69992b0b317808ce88cad11f6bea8&card_pwd1=3bffb74f81f0f53b1370b52d570d8d42ca02e92ed4a6b4d7&pm_id1=LTJFK&pc_id1=LTJFK00020000&card_status1=0&card_code1=00000&card_date1=20100526134800&r1=&verifystring=b392a9fea9930d920626fe75fbf6dba3
	$return = getCallBackValue($version_id, $merchant_id, $order_date, $order_id, $result, $amount, $currency, $pay_sq, $pay_date,$count,$card_num1,$card_pwd1,$pm_id1, $pc_id1, $card_status1,$card_code1,$card_date1,$r1, $verifystring);

	$deposit_interf_response = array(
				#版本号
				'version_id' => $version_id,

				#商户ID
				'merchant_id' => $merchant_id,

				#订单日期
				'order_date' =>	$order_date,

				#订单号
				'order_id' => $order_id,

				#支付结果  Y成功  F失败 
				'result' => $result,
				
				#支付金额
				'amount' => $amount,

				#货币类型
				'currency' => $currency,
				
				#支付流水号
				'pay_sq' => $pay_sq,
		
				#支付日期
				'pay_date' => $pay_date,

				#卡支付次数
				'count' => $count,
				
				#卡号
				'card_num' => $card_num1,
				
				#卡密码
				'card_pwd' => $card_pwd1,

				#支付方式
				'pm_id'	=> $pm_id1,
				
				#支付通道编号
				'pc_id'	=>	$pc_id11,
				
				#卡支付状态  0 成功 1失败
				'card_status'	=> $card_status1,
				
				#卡支付错误码
				'card_code'	=> $card_code1,

				#卡支付完成时间
				'card_date' => $card_date1,

				#扩展字段
				'r1'        => $rl,
				#校验串
				'verifystring' => $verifystring,
			);
			$deposit_interf_response_str = array2json($deposit_interf_response);
	#	判断返回签名是否正确（True/False）
	$bRet =  CheckSignString($version_id, $merchant_id, $order_id, $result, $order_date, $amount, $currency, $pay_sq, $pay_date, $count,$card_num1,$card_pwd1,$pc_id1,$card_status1,$card_code1,$card_date1,$r1,$verifystring);
	#	以上代码和变量不需要修改.

/*
	{
		$value = $bRet."|".$result."|".$card_status1."|".$card_code1."|".$order_id;
		$name = $uid."_19pay_callback";
		$sFile = $name.'.txt';
		@file_put_contents($sFile,$value);
	}
	
	echo "Y";
	exit();
*/		
	#	校验码正确.

	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	$temparr = @explode("_", $order_id);
	$uid = $temparr[1];
	$sql = "select * from am_registered_user where id=".$uid;	
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		die;
	}
	//无此用户
	if(mysql_num_rows($rs) == 0){
		die;
	}
		
	$row = mysql_fetch_assoc($rs);
	//充值前的余额
	$balance =	is_numeric($row["balance"])?$row["balance"]:0;
	if($bRet){
		echo "Y";
		
		if($result=="Y"){
			
			//充值成功

			//充值前余额
			$deposit_balance_before = $balance;

			//充值后余额
			$deposit_balance_after = $balance + $p3_Amt;
			
		} else {
			//充值前余额
			$deposit_balance_before = $balance;

			//充值后余额
			$deposit_balance_after = $balance;
		}
	} else{
		//充值前余额
		$deposit_balance_before = $balance;

		//充值后余额
		$deposit_balance_after = $balance;
	}
	//修改记录
	$sqlS = "select id from am_deposit where order_id='".$order_id."'";	
	$rs = mysql_query($sqlS, $conn);
	if($rs === FALSE){
		die;
	}
	$row = mysql_fetch_assoc($rs);
	//print_r($row);exit();
	$deposit_id = $row['id'];
	//["ChargeCardDirect","2","10001167060","smartermob_1_1274611559","0.0","UNICOM","111111","0.0","0.0","7","","","","57c3a27aa81771bf7ebe0ea9b4965a6f"]
	$deposit_interf_response = array2json(array($version_id, $merchant_id, $order_date, $order_id, $result, $amount, $currency, $pay_sq, $pay_date,$count,$card_num1,$card_pwd1,$pm_id1, $pc_id1, $card_status1,$card_code1,$card_date1,$r1, $verifystring));
	$sqlUpdate = "update am_deposit set channel_response='".$deposit_interf_response."',balance_after='".$deposit_balance_after."',status='".$result."' where id = '{$deposit_id}'  order by id desc limit 1";
	//echo $sqlUpdate;exit();
	mysql_query($sqlUpdate);
	
	if($deposit_balance_after - $deposit_balance_before > 0)
	{
		$sqlU = "update am_registered_user set balance = ".$deposit_balance_after." where id=".$uid;
		mysql_query($sqlU);
	}
	@mysql_close($conn);


	/************************************************************/
	{
		$value = $deposit_id."|".$deposit_balance_after."|".$result."|".$card_status1."|".$card_code1;
		$name = $uid."_19pay_callback";
		$sFile = $name.'.txt';
		@file_put_contents($sFile,$value);
	}
	
	/************************************************************/

?> 