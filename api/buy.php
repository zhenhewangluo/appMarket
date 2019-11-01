<?php
	/**************************************************************
	 *   协议号：14
     *   服务器接口文件： buyapp.php for BajieV2
	 *   使用账户余额（RMB单位），购买应用程序    
     *
     *   Author: Li Xiaan
	 *   Create Time: 2010-01-09
	 *   Updates:     2010-04-19  am_user表被移至comm库
	 *   修改      bluesie  2010-5-19
	 *
	 *************************************************************/
	require("./inc/init.php");
	include_once("./inc/funcRsa.inc.php");	
	include_once("./inc/libvar.inc.php");	
	include_once("./class/BigInteger.class.php");
	include_once("./class/des.class.php");


	function dumpDecryptLog($str){
		log_message($str, 'D');
	}

	$mid = __getPost('mid');
	///////////////////////////////////////////////////
	//
	//      上传数据基本检查
	//
	///////////////////////////////////////////////////	
	//输入检查
	$keyEncode = __getPost('keyEncode'); //key密文		
	$stringEncode = __getPost('stringEncode'); //参数密文
	//$keyEncode = "9987501780563344410284448816267600713393428318003002600126163546491438746026897583366006092065088049510742921899267909441147411291656753757258499170711882657408728559681608323870705543478723351585740585963447335909575069912552372449952814273117044101091552370039572924635409956320063501043830484333202103886";
	//$stringEncode = "n1cLdZrUxAII10dJ38234Eccbik++ou1tzkJrxjr7KmZSYsVjouJvjk/ogsHzI4PfQty6Zv11g2rHXQcDML94g==";
    if($keyEncode == '' || $stringEncode == '')
	{
		//echo error2json("E137");
		$response = error2json("E137");
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}

	// 解密上送的密码
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
	//paras 格式：proto|uid|sid|parent_id|appid|passwd|other_price
	if($count != 7 || $aPara[0] != 14)
	{
		//echo error2json("E137");
		$response = error2json("E137");
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}

	list($proto,$uid,$sid,$parentid,$appid,$passwd,$price) = $aPara;
	/*
	$proto = 14;
	$uid = "1";
	$sid = "9pov21k1vl9bssecb09i1uc2s7";//本地
	//$sid = "ec9195ef512e17ad2fe835daba808fa4";//线上
	$parentid = 'xxxx';
	$appid = '6000035';
	$passwd = '123456';
	*/
	///////////////////////////////////////////////
	//
	//      建立数据库的连接
	//
	///////////////////////////////////////////////
	//<------ Add this connection for bajiev2 -------->
	$conn_comm = connect_comm_db();
	if($conn_comm === FALSE){
		dumpBuyAppError("S100", 0, NULL,$deskey);	
		die;
	}	
	///////////////////////////////////////////////
	//
	//      读am_user表检查密码，读取用户账户余额
	//
	///////////////////////////////////////////////
	$sql = "select * from am_registered_user where id=". $uid;	
	$rs = mysql_query($sql, $conn_comm);
	if($rs === FALSE){
		dumpBuyAppError("S002", 0, NULL,$deskey);	
		die;
	}
	//无此用户
	if(mysql_num_rows($rs) == 0){
		dumpBuyAppError("E130", 0, NULL,$deskey);	
		die;
	}
	$row = mysql_fetch_assoc($rs);

	//未设置密码，报错
	if(empty($row["password"])){
		dumpBuyAppError("E132", 0, NULL,$deskey);	
		die;
	}		
	$pwd = md5($passwd);
	//密码错误
	if(strcmp($row["password"], $pwd) != 0){
		dumpBuyAppError("E131", 0, NULL,$deskey);	
		die;
	}

	dumpDecryptLog("Pwd in Database:". $pwd);

	//可用余额 = 账户余额 - 止付金额
	$balance =	is_numeric($row["balance"])?$row["balance"]:0;
	$stop_amount = is_numeric($row["frozen_amount"])?$row["frozen_amount"]:0;
	$available_balance   = $balance - $stop_amount;
	//可用余额不得小于0
	if($available_balance < 0){
		$available_balance = 0;
		//dumpBuyAppError("E131", 0, NULL);
	}
	mysql_free_result($rs);
	

		///////////////////////////////////////////////
		//
		//      读am_appinfo表得到该应用的价格
		//
		///////////////////////////////////////////////

		if(!$parentid)
			$selectAppId = intval($appid);
		else
			$selectAppId = intval($parentid);
		$conn = connect_db();
		if($conn === FALSE){
			dumpBuyAppError("S001", 0, NULL,$deskey);	
			die;
		}
		$sql = "select * from am_appinfo where app_id=".$selectAppId;
		$rs1 = mysql_query($sql, $conn);
		if($rs1 === FALSE){
			dumpBuyAppError("S002", 0, NULL,$deskey);	
			die;
		}
		//无此应用
		if(mysql_num_rows($rs1) == 0){
			dumpBuyAppError("E124", 0, NULL,$deskey);	
			die;
		}
		$row = mysql_fetch_assoc($rs1);
		if(!$parentid)
			$app_price = is_numeric($row["app_price"])?$row["app_price"]:0;
		else
			$app_price = $price;
		//$app_price = is_numeric($row["app_price"])?$row["app_price"]:0;
		//及时清空资源
		mysql_free_result($rs1);
	

		///////////////////////////////////////////////
		//
		//   处理免费应用, 不写consume表，直接返回
		//   免费应用的返回payid为0
		//
		///////////////////////////////////////////////
		if($app_price == 0){
			/*
			echo array2json(
				array(
					"proto" => 14,
					"reqsuccess" => AM_REQUEST_SUCCESS,
					"payid" => 0,
				)	
			);
			*/
			$response =  array2json(
				array(
					"proto" => 14,
					"reqsuccess" => AM_REQUEST_SUCCESS,
					"payid" => 0,
				)	
			);
			$encodeRes = $crypt->encrypt($response);
			echo $encodeRes;
			die;			
		}
	if(!$parentid)
	{
		////////////////////////////////////////////////////
		//
		//  查询该用户是否购买过此应用，因为免费的应用不写
		//  consume表，所以有既往记录的，必为收费的应用
		//  以前购买过的，也不写consume表
		//
		////////////////////////////////////////////////////
		$sql = "select * from am_consume where user_id='". $uid ."' and product='". $appid ."' and result=1 order by id desc";
		$rs2 = mysql_query($sql, $conn);
		if($rs2 === FALSE){
			dumpBuyAppError("S002", 0, NULL,$deskey);	
			die;
		}
		if(mysql_num_rows($rs2) != 0){
			$row = mysql_fetch_assoc($rs2);
			$old_consume_id = $row["id"];
			
			@mysql_close($conn);
			$conn = connect_comm_db();
			$sql = "select balance,frozen_amount from am_registered_user where id=". $uid;	
			$rs = mysql_query($sql, $conn);
			$row2 = mysql_fetch_assoc($rs);
			
			$balance = $row2['balance'] - $row2['frozen_amount'];
			$response =  array2json(
				array(
					"proto" => 14,
					"reqsuccess" => AM_REQUEST_SUCCESS,
					"payid" => $old_consume_id,
					"balance" => $balance
				)	
			);
			$encodeRes = $crypt->encrypt($response);
			echo $encodeRes;
			@mysql_close($conn);
			die;	
		}
		//及时清空资源
		mysql_free_result($rs2);
	}

	///////////////////////////////////////////////
	//
	//      在am_consume中申请一条记录,获取consume_id
	//
	///////////////////////////////////////////////
	//格式化POST
	$request = array();
	foreach($_POST as $key => $val){
		$request[] = $key . "=" . stopSql($val);	
	}
	if(!$parentid)
		$shop_id = 0;
	else
		$shop_id = $parentid;
	$sql= "insert into am_consume (user_id, mid,shop_id,product, app_request, create_time,price,balance_before) values('". $uid ."',".$mid.", '{$shop_id}','". $appid ."','". join("&", $request) ."', NOW(),". $app_price .",". $available_balance .")";
	//echo $sql."-----";
	//exit();
	//插入失败
	if(mysql_query($sql, $conn) === FALSE){
			dumpBuyAppError("S102", 0, NULL,$deskey);	
			die;
	}
	//获得id
	$consume_id = mysql_insert_id();	
	///////////////////////////////////////////////
	//
	//      余额不足错误
	//
	///////////////////////////////////////////////
	if($app_price > $available_balance){
		dumpBuyAppError("E133", $consume_id, $conn,$deskey);	
		die;		
	}
	///////////////////////////////////////////////
	//
	//      更新用户表的余额域
	//
	///////////////////////////////////////////////
	//若COMM和CONN两个数据库都在一台机器上，需要切换数据库。
	if($conn_comm == $conn){
		if (!mysql_select_db($AM_COMMON_INFO_DATABASE["DB_NAME"], $conn_comm)){
			dumpBuyAppError("S104", $consume_id, $conn,$deskey);	
			die;	
		}
	}

	$new_balance = $available_balance - $app_price;
	$sql = "update am_registered_user set balance='". $new_balance ."' where id=". $uid;
	
	if(mysql_query($sql, $conn_comm) === FALSE){
		dumpBuyAppError("S101", $consume_id, $conn,$deskey);	
		die;
	}
	///////////////////////////////////////////////
	//
	//      更新log中的事后余额域, 付费结果为成功(1)
	//
	///////////////////////////////////////////////
	$response = array2json(array(
				"proto" => 14,
				"reqsuccess" => AM_REQUEST_SUCCESS,
				"payid" => $consume_id,
				"balance"=>$new_balance,
				));	

	//若COMM和CONN两个数据库都在一台机器上，需要切换数据库。
	if($conn_comm == $conn){
		if (!mysql_select_db($AM_DATABASE_INFOR["DB_NAME"], $conn_comm)){
			dumpBuyAppError("S104", $consume_id, $conn,$deskey);	
			die;	
		}
	}
	$sql = "update am_consume set price='". $app_price ."', balance_after='". $new_balance ."', result=1, error_code='0000',app_response='". $response ."' where id=". $consume_id;

	if(mysql_query($sql, $conn) === FALSE){
		//特别注意的是，余额已扣，但是未更新下载状态，应该出这个状态的报表。
		dumpBuyAppError("S103", $consume_id, $conn,$deskey);	
		die;
	}
	$encodeRes = $crypt->encrypt($response);
	echo $encodeRes;
	@mysql_close($conn);
	if($memobj)$memobj->close();
		/**
	 *	记录错误，因为调用地方较多，设为函数。
	 */
	function dumpBuyAppError($error_code, $consume_id, $conn,$key){
		
		global $AM_ERROR_LOG_FILE;
		$error_response = error2json($error_code);

		//先试图更新到am_consume数据表中
		if($consume_id > 0 && $conn != NULL){
		
			$sql = "update am_consume set app_response='". $error_response ."', result=0, error_code='". $error_code ."' where id=". $consume_id;
		
			mysql_query($sql, $conn);
		}

		//再写入错误文件中, 自动DUMP。
		//给客户端封装了错误的json应答
		$response = error2json($error_code);
		$crypt = new CookieCrypt($key);
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}
?>
