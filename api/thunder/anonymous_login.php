<?php

	/************************************************************
	 *   Interface 1
     *   匿名登录 anonymous_login.php
	 *
     *   Author:  bluesie
	 *   Time:    2010-7-8  修改 修改
	 *	 paras    proto imei imsi ver  model  channel 增加 ：device_id screen sdk name  	  
	 *************************************************************/
	require("../inc/init.php");	
	require("../class/smtp.class.php");

    if($AM_CURRENT_REQUEST["PROTO"] != 1){
		echo error2json("E002");
		die;
	}


	if(!isset($_POST['model']) || !is_numeric($_POST['model'])){
		echo error2json("E004");
		die;	
	}

	if(!isset($_POST['ver']) || empty($_POST['ver'])){
		echo error2json("E005");
		die;
	}

	if(!isset($_POST['channel']) || !is_numeric($_POST['channel'])){
		echo error2json("E194");
		die;	
	}
	/* 修改 0708
	 * 增加传入参数 device_id
	 * 判断 device_id 是否存在
	 */
	$current_device = $device_id = intval(__getPost('model'));
	//android 数据库连接
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}
	//sql	 
	////memcache缓存
	$device_type_id = $isflagexists = 0;
	$keyName = $AM_MEMCACHE["thunder_am"][0]."_device_".$current_device;
	if($AM_MEMCACHE["thunder_am"][2]){
		if(!($device_type_id = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sql = "select device_type_id from am_device where id = ".$current_device;
		$rs = mysql_query($sql, $conn);
		$row = mysql_fetch_assoc($rs);
		$device_type_id = $row['device_type_id'];
		if($isflagexists == 2) $memobj->set($keyName ,$device_type_id , 0 ,$AM_MEMCACHE["thunder_am"][1]);
	}
	
	if($device_type_id)
	{
		//存在device_type_id
		$deviceTypeIdResult = $device_type_id;
		$deviceIdResult = $device_id;
		if($device_id > 10000)
		{
			//记录
			$email = "fisher@smartermob.com";
			feedbackQuestion($email);
		}
	}
	else
	{
		//不存在device_id
		$model_name = __getPost('model_name');
		$screen = __getPost('screen_size');
		if(__getPost('sdk_int'))
			$sdk = intval(__getPost('sdk_int'));
		else
			$sdk = intval(__getPost('sdk'));
		//根据 screen sdk 查询 device_type_id1
		$sqlSS = "select id from am_device_type where screen='".$screen."' and sdk_version<='".$sdk."'";
		//echo $sqlSS;//exit();
		$rsSS = mysql_query($sqlSS, $conn);
		$rowSS = mysql_fetch_assoc($rsSS);
		$device_type_id1 = $rowSS['id'];
		//根据name查询 device_type_id2
		$sqlN = "select b.id,a.id as device_id,a.device_type_id from am_device a left join am_device_type b on a.device_type_id=b.id where a.model_name = '".$model_name."'";
		$rsN = mysql_query($sqlN, $conn);
		$rowN = mysql_fetch_assoc($rsN);
		$device_type_id2 = $rowN['id'];
		$device_id2 = $rowN['device_id'];
		//比较 device_type_id1  device_type_id2 
		//echo $device_type_id1."---".$device_type_id2;exit();
		if($device_type_id1)
		{
			if($device_type_id2)
			{
				if($device_type_id1 == $device_type_id2)
				{
					$deviceTypeIdResult = $device_type_id1;
					$deviceIdResult = $device_id2;
				}
				else
				{
					$deviceTypeIdResult = $device_type_id1;
					$sqlDevice = "select id from am_device where device_type_id=".$device_type_id1." and id>10000 limit 1";//DEFAULT_ID;
					$rsD = mysql_query($sqlDevice, $conn);
					$rowD = mysql_fetch_assoc($rsD);
					$deviceIdResult = $rowD['id'];
					//记录
					$email = "fisher@smartermob.com";
					feedbackQuestion($email);
				}
			}
			else
			{
				$deviceTypeIdResult = $device_type_id1;
				$sqlDevice = "select id from am_device where device_type_id=".$device_type_id1." and id>10000 limit 1";//DEFAULT_ID;
				$rsD = mysql_query($sqlDevice, $conn);
				$rowD = mysql_fetch_assoc($rsD);
				$deviceIdResult = $rowD['id'];
				//记录
				$email = "fisher@smartermob.com";
				feedbackQuestion($email);
			}
		}
		else
		{
			if($device_type_id2)
			{
				$deviceTypeIdResult = $device_type_id2;
				$deviceIdResult = $device_id2;
				//记录
				$email = "fisher@smartermob.com";
				feedbackQuestion($email);
			}
			else
			{
				//记录
				$email = "fisher@smartermob.com";
				feedbackQuestion($email);
				echo error2json("E205");
				die;
			}
		}

	}

//echo $deviceTypeIdResult."----";exit();
	$AM_CURRENT_REQUEST["IMEI"]     = stopSql(__getPost('imei'));
	$AM_CURRENT_REQUEST["MODEL"]    = $deviceIdResult;	
	//记录 device_type_id
	$AM_CURRENT_REQUEST["DEVICE_TYPE_ID"]    = $deviceTypeIdResult;
	$AM_CURRENT_REQUEST["APP_VER"]  = stopSql(__getPost('ver'));
	$AM_CURRENT_REQUEST["CHANNEL"]  = intval(__getPost('channel'));

	$mid = 0;
	// 
	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}
	//$sql = "insert into am_terminal (imei,device_id,create_time,status,channel_id,app_version) values ('". $AM_CURRENT_REQUEST["IMEI"]  ."','".$AM_CURRENT_REQUEST["MODEL"] ."',NOW(), 1, ". $AM_CURRENT_REQUEST["CHANNEL"] .",'". $AM_CURRENT_REQUEST["APP_VER"] ."')";
	$sql = "insert into am_terminal (`imei`, `create_time`, `app_version`, `device_id`, `channel_id`, `status`, `device_type_id`) values ('". $AM_CURRENT_REQUEST["IMEI"]  ."',NOW(),'".$AM_CURRENT_REQUEST["APP_VER"] ."', ". $AM_CURRENT_REQUEST["MODEL"] .",'". $AM_CURRENT_REQUEST["CHANNEL"] ."',1,'".$deviceTypeIdResult."')";
	//echo $sql."<br />";
	if(mysql_query($sql, $conn)){
		$mid = mysql_insert_id();
		//echo $mid;
		//新增一条am_terminal_info
		$sqlInfo = "insert into am_terminal_info (`mid`, `board`, `brand`, `cpu_abi`, `device`, `diplay`, `fingerprint`, `host`, `build_id`, `manufacturer`, `model_name`, `product`, `tags`, `time`, `type`, `user`, `codename`, `incremental`, `release`, `sdk`, `sdk_int`, `screen_size`) values ('". $mid  ."','".__getPost("board") ."', '". __getPost("brand") ."','". __getPost("cpu_abi") ."','". __getPost("device") ."','". __getPost("diplay") ."','". __getPost("fingerprint") ."','". __getPost("host") ."','". __getPost("build_id") ."','". __getPost("manufacturer") ."','". __getPost("model_name") ."','". __getPost("product") ."','". __getPost("tags") ."','". __getPost('time') ."','". __getPost('type') ."','". __getPost("user") ."','". __getPost("codename") ."','". __getPost("incremental") ."','". __getPost("release") ."','". __getPost('sdk') ."','". __getPost("sdk_int") ."','". __getPost('screen_size') ."')";
		#echo $sqlInfo."<br />";die;
		mysql_query($sqlInfo, $conn);
	}else{
		log_message($sql, 'S');
		echo error2json("S004");
		die;
	}
	
	@mysql_close($conn);
	$conn1 = connect_db();
	if($conn1 === FALSE){
		echo error2json("S001");
		die;
	}
		
	$sql = "insert into am_channel_user (channel_id, mid, channel_onboard_time,channel_user_app_version) values(". $AM_CURRENT_REQUEST["CHANNEL"] .", ". $mid .", NOW(),'". $AM_CURRENT_REQUEST["APP_VER"] ."')";
			
	if(mysql_query($sql, $conn1) === FALSE){
		echo error2json_withlog("S007", true, "Sql: ". $sql);
		die;
	}

	//log the logon message.
	log_message(
	sprintf("[LOG ON]CHANNEL=%s,MID=%s,IMEI=%s,IMSI=%s,MODEL=%s,VER=%s", $AM_CURRENT_REQUEST["CHANNEL"], $mid, $AM_CURRENT_REQUEST["IMEI"],$AM_CURRENT_REQUEST["IMSI"],$AM_CURRENT_REQUEST["MODEL"],$AM_CURRENT_REQUEST["APP_VER"]),
	'S'
	);

	session_start();
    $sessionId = session_id();
	$_SESSION["mid"] = $mid;
	$_SESSION["channel"] = $AM_CURRENT_REQUEST["CHANNEL"];
	$_SESSION["model"]   = $AM_CURRENT_REQUEST["MODEL"];
	$_SESSION["device_type_id"]   = $AM_CURRENT_REQUEST["DEVICE_TYPE_ID"];
	if(isset($_SESSION["username"])){
		unset($_SESSION["username"]);
	}
	if(isset($_SESSION["uid"])){
		unset($_SESSION["uid"]);
	}

	echo array2json(
			array(
				"proto"      =>  1,
				"reqsuccess" =>  AM_REQUEST_SUCCESS,
				"mid"        =>  $mid,
				"sid"        =>  $sessionId,
				"device_type_id"=>$deviceTypeIdResult,
				"device_id"=>$deviceIdResult,
			)
	);
	if($memobj)$memobj->close();

function feedbackQuestion($email)
{	
	
	//save log 
	$filename = './log/device.txt';
	$handle = fopen($filename, 'a');
	$somecontent = "\r\n\r\n************************************\r\n".date("Y-m-d H:i:s",time())."记录device 信息\r\n";
	$somecontent .= $content;
	fwrite($handle, $somecontent);
	fclose($handle);
	//
	/*****************记录文本 THE END*****************************/
}
?>
