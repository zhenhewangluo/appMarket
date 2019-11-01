<?php
	/**************************************************************
	 *   Э��ţ�14
     *   �������ӿ��ļ��� buyapp.php for BajieV2
	 *   ʹ���˻���RMB��λ��������Ӧ�ó���    
     *
     *   Author: Li Xiaan
	 *   Create Time: 2010-01-09
	 *   Updates:     2010-04-19  am_user������comm��
	 *   �޸�      bluesie  2010-5-19
	 *  
	 *************************************************************/
	require("../inc/init.php");
	include_once("../inc/funcRsa.inc.php");	
	include_once("../inc/libvar.inc.php");	
	include_once("../class/BigInteger.class.php");
	include_once("../class/des.class.php");


	function dumpDecryptLog($str){
		log_message($str, 'D');
	}
	///////////////////////////////////////////////////
	//
	//      �ϴ����ݻ������
	//
	///////////////////////////////////////////////////	
	//������
	$keyEncode = __getRequest('keyEncode'); //key����
	$stringEncode = __getRequest('stringEncode'); //��������
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

	// �������͵�����
	$key_private = new Math_BigInteger(RSA_PRIVATE_KEY);
	$modulo = new  Math_BigInteger(RSA_MODULO);

	$keyEncodeBI = new Math_BigInteger($keyEncode);
	$keyEncodeByte = $keyEncodeBI->toBytes();

	$keyDecode = rsa_decrypt($keyEncodeByte, $key_private->toString(), $modulo->toString(),1024);//�����key

	$aKeyDecode = explode("|",$keyDecode);
	$deskey = $aKeyDecode[1];
	$crypt = new CookieCrypt($deskey);
	$paras = $crypt->decrypt($stringEncode);

	//�������
	$aPara = explode("|",$paras);

	$count = count($aPara);
	//paras ��ʽ��proto|uid|sid|parent_id|appid|passwd|other_price
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
	$sid = "9pov21k1vl9bssecb09i1uc2s7";//����
	//$sid = "ec9195ef512e17ad2fe835daba808fa4";//����
	$parentid = 'xxxx';
	$appid = '6000035';
	$passwd = '123456';
	*/
	///////////////////////////////////////////////
	//
	//      �������ݿ������
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
	//      ��am_user�������룬��ȡ�û��˻����
	//
	///////////////////////////////////////////////
	$sql = "select * from am_registered_user where id=". $uid;	
	$rs = mysql_query($sql, $conn_comm);
	if($rs === FALSE){
		dumpBuyAppError("S002", 0, NULL,$deskey);	
		die;
	}
	//�޴��û�
	if(mysql_num_rows($rs) == 0){
		dumpBuyAppError("E130", 0, NULL,$deskey);	
		die;
	}
	$row = mysql_fetch_assoc($rs);

	//δ�������룬����
	if(empty($row["password"])){
		dumpBuyAppError("E132", 0, NULL,$deskey);	
		die;
	}		
	$pwd = md5($passwd);
	//�������
	if(strcmp($row["password"], $pwd) != 0){
		dumpBuyAppError("E131", 0, NULL,$deskey);	
		die;
	}

	dumpDecryptLog("Pwd in Database:". $pwd);

	//������� = �˻���� - ֹ�����
	$balance =	is_numeric($row["balance"])?$row["balance"]:0;
	$stop_amount = is_numeric($row["frozen_amount"])?$row["frozen_amount"]:0;
	$available_balance   = $balance - $stop_amount;
	//��������С��0
	if($available_balance < 0){
		$available_balance = 0;
		//dumpBuyAppError("E131", 0, NULL);
	}
	mysql_free_result($rs);
	

		///////////////////////////////////////////////
		//
		//      ��am_appinfo��õ���Ӧ�õļ۸�
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
		//�޴�Ӧ��
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
		//��ʱ�����Դ
		mysql_free_result($rs1);
	

		///////////////////////////////////////////////
		//
		//   �������Ӧ��, ��дconsume��ֱ�ӷ���
		//   ���Ӧ�õķ���payidΪ0
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
		//  ��ѯ���û��Ƿ������Ӧ�ã���Ϊ��ѵ�Ӧ�ò�д
		//  consume�������м�����¼�ģ���Ϊ�շѵ�Ӧ��
		//  ��ǰ������ģ�Ҳ��дconsume��
		//
		////////////////////////////////////////////////////
		$sql = "select * from am_consume where mid='". $AM_CURRENT_REQUEST["MID"] ."' and product='". $appid ."' and result=1";
		//echo $sql;
		$rs2 = mysql_query($sql, $conn);
		if($rs2 === FALSE){
			dumpBuyAppError("S002", 0, NULL,$deskey);	
			die;
		}
		if(mysql_num_rows($rs2) != 0){
			$row = mysql_fetch_assoc($rs2);
			$old_consume_id = $row["id"];
			/*
			echo array2json(
				array(
					"proto" => 14,
					"reqsuccess" => AM_REQUEST_SUCCESS,
					"payid" => $old_consume_id,
				)	
			);
			*/
			$response =  array2json(
				array(
					"proto" => 14,
					"reqsuccess" => AM_REQUEST_SUCCESS,
					"payid" => $old_consume_id,
				)	
			);
			$encodeRes = $crypt->encrypt($response);
			echo $encodeRes;
			die;	
		}
		//��ʱ�����Դ
		mysql_free_result($rs2);
	}

	///////////////////////////////////////////////
	//
	//      ��am_consume������һ����¼,��ȡconsume_id
	//
	///////////////////////////////////////////////
	//��ʽ��POST
	$request = array();
	foreach($_POST as $key => $val){
		$request[] = $key . "=" . stopSql($val);	
	}
	if(!$parentid)
		$shop_id = 0;
	else
		$shop_id = $parentid;
	$sql= "insert into am_consume (user_id, mid,shop_id,product, app_request, create_time,price,balance_before) values('". $uid ."',".$AM_CURRENT_REQUEST["MID"].", '{$shop_id}','". $appid ."','". join("&", $request) ."', NOW(),". $app_price .",". $available_balance .")";
	//echo $sql."-----";
	//exit();
	//����ʧ��
	if(mysql_query($sql, $conn) === FALSE){
			dumpBuyAppError("S102", 0, NULL,$deskey);	
			die;
	}
	//���id
	$consume_id = mysql_insert_id();	
	///////////////////////////////////////////////
	//
	//      �������
	//
	///////////////////////////////////////////////
	if($app_price > $available_balance){
		dumpBuyAppError("E133", $consume_id, $conn,$deskey);	
		die;		
	}
	///////////////////////////////////////////////
	//
	//      �����û���������
	//
	///////////////////////////////////////////////
	//��COMM��CONN�������ݿⶼ��һ̨�����ϣ���Ҫ�л����ݿ⡣
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
	//      ����log�е��º������, ���ѽ��Ϊ�ɹ�(1)
	//
	///////////////////////////////////////////////
	$response = array2json(array(
				"proto" => 14,
				"reqsuccess" => AM_REQUEST_SUCCESS,
				"payid" => $consume_id,
				"balance"=>$new_balance,
				));	

	//��COMM��CONN�������ݿⶼ��һ̨�����ϣ���Ҫ�л����ݿ⡣
	if($conn_comm == $conn){
		if (!mysql_select_db($AM_DATABASE_INFOR["DB_NAME"], $conn_comm)){
			dumpBuyAppError("S104", $consume_id, $conn,$deskey);	
			die;	
		}
	}
	$sql = "update am_consume set price='". $app_price ."', balance_after='". $new_balance ."', result=1, error_code='0000',app_response='". $response ."' where id=". $consume_id;

	if(mysql_query($sql, $conn) === FALSE){
		//�ر�ע����ǣ�����ѿۣ�����δ��������״̬��Ӧ�ó����״̬�ı���
		dumpBuyAppError("S103", $consume_id, $conn,$deskey);	
		die;
	}
	$encodeRes = $crypt->encrypt($response);
	echo $encodeRes;
	@mysql_close($conn);
	if($memobj)$memobj->close();

		/**
	 *	��¼������Ϊ���õط��϶࣬��Ϊ������
	 */
	function dumpBuyAppError($error_code, $consume_id, $conn,$key){
		
		global $AM_ERROR_LOG_FILE;
		$error_response = error2json($error_code);

		//����ͼ���µ�am_consume���ݱ���
		if($consume_id > 0 && $conn != NULL){
		
			$sql = "update am_consume set app_response='". $error_response ."', result=0, error_code='". $error_code ."' where id=". $consume_id;
		
			mysql_query($sql, $conn);
		}

		//��д������ļ���, �Զ�DUMP��
		//���ͻ��˷�װ�˴����jsonӦ��
		$response = error2json($error_code);
		$crypt = new CookieCrypt($key);
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}
?>