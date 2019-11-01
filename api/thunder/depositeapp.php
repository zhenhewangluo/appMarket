<?php
	/************************************************************
	 *   协议号：16
     *   服务器接口文件： depositeapp.php
     *   充值接口。
     *   Author: 
	 *   Create Time: 2009-01-07
	 *   Update: bluesie  2010-5-17
	 *   time:   此程序insert 一条记录，然后处理 1,2,4 个时间值  callback 修改第三个时间
	 *   deposit_interf_request_time  接口请求时间 
	 *   deposit_interf_request       发送请求的参数
	 *   正式卡： 账号：2015248023  密码：734381967809021  卡是完美的
	 *************************************************************/
	include_once("../inc/init.php");	
	include_once("../inc/funcRsa.inc.php");	
	include_once("../inc/libvar.inc.php");	
	include_once("../class/BigInteger.class.php");
	include_once("../class/des.class.php");

	
	//输入检查
	/*
    if($AM_CURRENT_REQUEST["PROTO"] != 16){
		echo error2json("E002");
		die;
	}
	*/
	//解码上行参数
	$keyEncode = __getRequest('keyEncode'); //key密文
	$stringEncode = __getRequest('stringEncode'); //参数密文
	//$keyEncode = "112623724536206452499091753099076232023515059394255832426022466247608374019519596070748529229662257054012178859830724396340698365119934609832833803430225111643067257205518811780267332874404262579960060699643101193405221689497107442835667262271181969047623212894997851564260421698126622368447398054103292209086";
	//$stringEncode = "DwgLSWZS1wpWztiTuS0lkZkFrWe36VGEzquOAPWWHnhizkdzZ9G1C5mgGQzSb7x7CdmrwF/4htD+dCX+exVFrMhkNVQtErklJXFwT0R2SnE=";
    if($keyEncode == '' || $stringEncode == '')
	{
		$response = error2json("E137");
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}
	/*
	$RSA = new RSA();
	$keyDecodeR = $RSA->decrypt ($keyEncode, RSA_PRIVATE_KEY, RSA_MODULO);//解码后key

	$keyDecode = strrev($keyDecodeR);      //从Java传来的密文解码后需要反转，尚不知原因
	echo $keyDecode."_____";
	$crypt = new CookieCrypt($keyDecode);
	$paras = $crypt->decrypt($stringEncode);
	*/
	//解密参数
	$key_private = new Math_BigInteger(RSA_PRIVATE_KEY);
	$modulo = new  Math_BigInteger(RSA_MODULO);

	$keyEncodeBI = new Math_BigInteger($keyEncode);
	$keyEncodeByte = $keyEncodeBI->toBytes();

	$keyDecode = rsa_decrypt($keyEncodeByte, $key_private->toString(), $modulo->toString(),1024);//解码后key

	$aKeyDecode = explode("|",$keyDecode);
	$deskey = $aKeyDecode[1];
	$crypt = new CookieCrypt($deskey);
	$paras = $crypt->decrypt($stringEncode);
	
	//处理参数
	$aPara = explode("|",$paras);

	$count = count($aPara);
	//paras 格式：proto|uid|sid|amount|card_amount|serial|passwd|type
	
	if($count != 8 || $aPara[0] != 16)
	{
		$response = error2json("E137");
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}

	list($proto,$uid,$sid,$amount,$card_amount,$serial,$passwd,$type) = $aPara;
	/*
	$proto = 16;
	$uid = "1";
	$sid = "9pov21k1vl9bssecb09i1uc2s7";//本地
	//$sid = "ec9195ef512e17ad2fe835daba808fa4";//线上
	$amount = 100;
	$card_amount = 100;
	$serial = "186013529876";
	$passwd = "54321";
	$type = 1;
	*/
	//生成订单编号
	$orderno = "smartermob_".$uid."_".time();
	/********************记录日志***********************/
	//添加日志记录到 am_deposit 表
	$conn = connect_comm_db();
	if($conn === FALSE){

		$response = error2json("S001");
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}
	$sql = "select * from am_registered_user where id=".$uid;	
	//echo $sql;
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

	//请求参数
	$deposit_app_request = array2json(array($proto,$uid,$sid,$amount,$card_amount,$serial,$passwd,$type));
	
	$deposit_app_request_time = date("Y-m-d H:i:s",time());
	$sqlS = "select id from am_deposit where order_id='".$orderno."'";	
	$rs = mysql_query($sqlS, $conn);
	if($rs === FALSE){
		die;
	}
	$row = mysql_fetch_assoc($rs);
	//print_r($row);exit();
	$deposit_id = $row['deposit_id'];
	//已有此订单，不处理
	/*
	if($deposit_id)
	{
		echo error2json("S001"); 
		die;
	}
	*/
	$sql = "insert into am_deposit (user_id, channel, amount, balance_after, balance_before, app_request, app_request_time, app_response, app_response_time, channel_request, channel_request_time, channel_response, channel_response_time, status, order_id) values('".$uid."', 'yeepay', '".$amount."', '0', '".$balance."', '".$deposit_app_request."', '{$deposit_app_request_time}','','{$deposit_app_request_time}', '', '{$deposit_app_request_time}', '', '{$deposit_app_request_time}', '0', '".$orderno."')";
	mysql_query($sql);
	$payid = mysql_insert_id();
	@mysql_close($conn);

	
	$conn = connect_comm_db();
	if($conn === FALSE){
		$response = error2json("S001");
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}
	//require("../inc/init.php");
	//$channel = 1;//支付渠道，现在只有易宝
	foreach($CHANNEL_CARD_CONFIG_ARR as $key=>$val)
	{
		if($CHANNEL_CARD_CONFIG_ARR[$key]['cardID'] == $type)
		{
			$channelID = $CHANNEL_CARD_CONFIG_ARR[$key]['channelID'];
			$cardNo = $CHANNEL_CARD_CONFIG_ARR[$key]['cardNo'];
			$cardChannel = $CHANNEL_CARD_CONFIG_ARR[$key]['cardChannel'];
		}
	}
	list($channel) = explode("|",$channelID);
	list($cardno) = explode("|",$cardNo);
	list($card_channel) = explode("|",$cardChannel);

	switch($channel) {
		case "1":
			include 'yeepay/YeePayCommon.php';
			$pd_FrpId = $cardno;
			$p2_Order = $orderno;
			$p3_Amt = $amount;
			$p4_verifyAmt = 'true';   //1  //是否加入校验
			$p5_Pid = "adroid market";  //加入产品名称
			$p6_Pcat = "deposit";    //产品类型
			$p7_Pdesc = "";      //加入产品描述
			$p8_Url = AM_SITE_URL."yeepay_callback.php";
			$pa_MP = "";  //临时信息
			$pa7_cardAmt = $card_amount;
			$pa8_cardNo = $serial;
			$pa9_cardPwd = $passwd;
			$pd_FrpId = $pd_FrpId;    //加入支付通道编码
			$pz_userId = $uid;

			$sql = "SELECT registered_time FROM am_registered_user WHERE id = '".$uid."'";
			$rs = mysql_query($sql,$conn);
			if(!$row = mysql_fetch_row($rs)) {
				$response = error2json("E112");
				$encodeRes = $crypt->encrypt($response);
				echo $encodeRes;
				die;
			} else {
				$pz1_userRegTime = $row[0];
			}
			@mysql_free_result($rs);
			//$pz1_userRegTime = date("Y-m-d H:i:s",time());
			$aa = annulCard($p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pz_userId,$pz1_userRegTime);
			//print_r($aa);
			//exit();
			
			//提交成功
			if($aa['r1_Code'] == 1)
			{
				$deposit_app_response = array2json($aa);
				$deposit_app_response_time = date("Y-m-d H:i:s",time());
				$deposit_interf_request = array2json(array($p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pz_userId,$pz1_userRegTime));
				$deposit_interf_request_time = date("Y-m-d H:i:s",time());
				//修改数据表 am_deposit 记录
				$sqlUpdate = "update am_deposit set app_response='".$deposit_app_response."', app_response_time='".$deposit_app_response_time."', channel_request='".$deposit_interf_request."', channel_request_time='".$deposit_interf_request_time."' where id=". $payid;
				mysql_query($sqlUpdate);
				//文件存储
				$name = $uid.'_callback';
				$sFile = $name.'.txt';
				$flag = 0;
				while($flag == 0)
				{
					if(file_exists($sFile))
					{
						$value = @file_get_contents($sFile);
						if($value)
						$aValue = explode("|",$value);
						$payid = $aValue[0];
						$balance = $aValue[1] * AM_EXCHANGE_RATE;
						$p8_cardStatus = $aValue[2];
						$r1_Code = $aValue[3];
						$flag = 1;
						@unlink($sFile);
					}
					else
					{
						sleep(0.5);
					}
				}
				//传值回client
				//卡错误
				if($r1_Code != 1)
				{
					if($p8_cardStatus == '1007')
					{
						$response = error2json("E163");
						$encodeRes = $crypt->encrypt($response);
						echo $encodeRes;
						die;
					}
					elseif($p8_cardStatus == '7')
					{
						$response = error2json("E164");
						$encodeRes = $crypt->encrypt($response);
						echo $encodeRes;
						die;
					}
					elseif($p8_cardStatus == '1003')
					{
						$response = error2json("E165");
						$encodeRes = $crypt->encrypt($response);
						echo $encodeRes;
						die;
					}
					else
					{
						$response = error2json("E166");
						$encodeRes = $crypt->encrypt($response);
						echo $encodeRes;
						die;
					}
				}
				else
				{
					if($payid)
					{
						$response = array2json(array(
						"proto" => 16,
						"reqsuccess" => AM_REQUEST_SUCCESS,
						"payid" => $payid,
						"balance" => $balance,
						));	
						//$encodeRes = $crypt->encrypt($response);
						//修改数据表 am_deposit 记录
						$deposit_interf_response_time = date("Y-m-d H:i:s",time());
						$sqlUpdate = "update am_deposit set channel_response='succss', channel_response_time='".$deposit_interf_response_time."' where id=". $payid;
						mysql_query($sqlUpdate);
						$encodeRes = $crypt->encrypt($response);
						//echo $encodeRes;
						echo $encodeRes;
					}
				}
			}
			else
			{
				//接口提交未成功
				$sqlUpdate = "update am_deposit set app_response='".join(",",$aa)." where order_id =". $orderno;
				mysql_query($sqlUpdate);
				$response = error2json("W200");
				$encodeRes = $crypt->encrypt($response);
				echo $encodeRes;
				die;
			}
			break;

		case "2":
			include '19pay/19payCommon.php'; 
			//$aa = annulCard($order_id,$amount,$cardnum1,$cardnum2,$pm_id,$pc_id,$notify_url,$retmode,$select_amount,'','','','','');
			$notify_url = AM_SITE_URL."19pay_callback.php";//
			$retmode = "1";  //临时信息
			$pm_id = $cardno;
			$pc_id = $card_channel;
			$aa = annulCard($orderno,$amount,$serial,$passwd,$pm_id,$pc_id,$notify_url,$retmode,$card_amount);
			//print_r($aa);
			//exit();
			$aResult = explode("|",$aa);
			list($version_id,$merchant_id,$verifystring,$order_date,$order_id,$amount,$currency,$pay_sq,$pay_date,$pc_id,$pm_id,$result,$resultstr)= $aResult;
			//提交成功
			if($result == 'P' and $resultstr == 1)
			{
				$deposit_app_response = array2json($aResult);
				$deposit_app_response_time = date("Y-m-d H:i:s",time());
				$deposit_interf_request = array2json(array($orderno,$amount,$serial,$passwd,$pm_id,$pc_id,$notify_url,$retmode,$card_amount));
				$deposit_interf_request_time = date("Y-m-d H:i:s",time());
				//修改数据表 am_deposit 记录
				$sqlUpdate = "update am_deposit set app_response='".$deposit_app_response."', app_response_time='".$deposit_app_response_time."', channel_request='".$deposit_interf_request."', channel_request_time='".$deposit_interf_request_time."' where id=". $payid;
				mysql_query($sqlUpdate);
				//文件存储
				$name = $uid.'_19pay_callback';
				$sFile = $name.'.txt';
				$flag = 0;
				while($flag == 0)
				{
					if(file_exists($sFile))
					{
						$value = @file_get_contents($sFile);
						if($value)
						$aValue = explode("|",$value);
						$payid = $aValue[0];
						$balance = $aValue[1];
						$result = $aValue[2];
						$card_status1 = $aValue[3];
						$card_code1 = $aValue[4];
						$flag = 1;
						@unlink($sFile);
					}
					else
					{
						sleep(0.5);
					}
				}
				//传值回client
				//卡错误
				if($result != 'Y')
				{
					if($card_code1 == '10119')
					{
						$response = error2json("E164");
						$encodeRes = $crypt->encrypt($response);
						echo $encodeRes;
						die;
					}
					elseif($card_code1 == '81007')
					{
						$response = error2json("E167");
						$encodeRes = $crypt->encrypt($response);
						echo $encodeRes;
						die;
					}
					elseif($card_code1 == '81006')
					{
						$response = error2json("E163");
						$encodeRes = $crypt->encrypt($response);
						echo $encodeRes;
						die;
					}
					else
					{
						$response = error2json("E166");
						$encodeRes = $crypt->encrypt($response);
						echo $encodeRes;
						die;
					}
				}
				else
				{
					if($payid)
					{
						$response = array2json(array(
						"proto" => 16,
						"reqsuccess" => AM_REQUEST_SUCCESS,
						"payid" => $payid,
						"balance" =>$balance,
						));	
						$encodeRes = $crypt->encrypt($response);
						//修改数据表 am_deposit 记录
						$deposit_interf_response_time = date("Y-m-d H:i:s",time());
						$sqlUpdate = "update am_deposit set channel_response='succss', channel_response_time='".$deposit_interf_response_time."' where id=". $payid;
						mysql_query($sqlUpdate);

						echo $encodeRes;
					}
				}
			}
			else
			{
				//接口提交未成功
				$sqlUpdate = "update am_deposit set app_response='".$aa." where order_id =". $orderno;
				mysql_query($sqlUpdate);
				$response = error2json("W200");
				$encodeRes = $crypt->encrypt($response);
				echo $encodeRes;
				die;
			}
			break;
	}
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
