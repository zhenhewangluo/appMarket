<?php

/**************************************************************
 * @Description 易宝支付非银行卡支付专业版接口范例 
 * @V3.0
 * @cxj
 * @Updtes: 2010-04-19 lixiaan am_user、am_deposit表均移至comm数据库
 *
 ********************************************************************/
	 //载入配置文件
	require("inc/config.inc.php");	
	require_once("inc/error.eng.php");
	require_once("inc/functions.php");
	require_once 'yeepay/YeePayCommon.php';	
	//	解析返回参数.
	$return = getCallBackValue($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac);
	$deposit_interf_request = array(
				#加入业务类型
				'p0_Cmd' => $p0_Cmd,

				#支付结果
				'r1_Code' => $r1_Code,

				#加入商家ID
				'p1_MerId' => $p1_MerId,

				#加入商户订单号
				'p2_Order' => $p2_Order,

				# 成功金额
				'p3_Amt' =>	$p3_Amt,

				#支付方式
				'p4_FrpId' => $p4_FrpId,

				#卡序列号组
				'p5_CardNo' => $p5_CardNo,

				#确认金额组
				'p6_confirmAmount' => $p6_confirmAmount,

				#实际金额组
				'p7_realAmount' => $p7_realAmount,

				#卡状态组
				'p8_cardStatus' => $p8_cardStatus,

				#扩展信息
				'p9_MP' =>	$p9_MP,

				#支付余额
				'pb_BalanceAmt' => $pb_BalanceAmt,

				#余额卡号
				'pc_BalanceAct' => $pc_BalanceAct,

				#签名数据
				'hmac' => $hmac
			);
			$deposit_interf_request_str = array2json($deposit_interf_request);

	//	判断返回签名是否正确（True/False）
	$bRet = CheckHmac($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac);
	//$bRet = true;
	#	以上代码和变量不需要修改.
		 	
	#	校验码正确.
	//连接数据库
	//$conn = connect_db();

	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	$temparr = @explode("_", $p2_Order);
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
		echo "success";
		
		if($r1_Code=="1"){
			
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
	$sqlS = "select id from am_deposit where order_id='".$p2_Order."'";	
	$rs = mysql_query($sqlS, $conn);
	if($rs === FALSE){
		die;
	}
	$row = mysql_fetch_assoc($rs);
	//print_r($row);exit();
	$deposit_id = $row['id'];
	//["ChargeCardDirect","2","10001167060","smartermob_1_1274611559","0.0","UNICOM","111111","0.0","0.0","7","","","","57c3a27aa81771bf7ebe0ea9b4965a6f"]
	$deposit_interf_response = array2json(array($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct,$hmac));
	$sqlUpdate = "update am_deposit set channel_response='".$deposit_interf_response."',balance_after='".$deposit_balance_after."',status='".$r1_Code."' where id = '{$deposit_id}'  order by id desc limit 1";
	//echo $sqlUpdate;exit();
	mysql_query($sqlUpdate);
	
	if($deposit_balance_after - $deposit_balance_before > 0)
	{
		$sqlU = "update am_registered_user set balance = ".$deposit_balance_after." where id=".$uid;
		mysql_query($sqlU);
	}
	@mysql_close($conn);

	
	{
		$value = $deposit_id."|".$deposit_balance_after."|".$p8_cardStatus."|".$r1_Code;
		$name = $uid."_callback";
		$sFile = $name.'.txt';
		@file_put_contents($sFile,$value);
	}
?> 