<?php

	/************************************************************
	 *   Interface 1
     *   匿名登录 anonymous_login.php
	 *
     *   Author:  bluesie
	 *   Time:    2010-7-8  修改 修改
	 *	 paras    proto imei imsi ver  model  channel 增加 ：device_id screen sdk name  	  
	 *************************************************************/
	require("./inc/init.php");	
	require("./class/smtp.class.php");

    if($AM_CURRENT_REQUEST["PROTO"] != 1){
		echo error2json("E002");
		die;
	}

	if(!isset($_POST['imei'])){
		echo error2json("E003");	
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
	
	//判断是否传值班screen,sdk---2010.11.11
	$screen = (__getPost('screen_size') == "320x533")?"480x800":__getPost('screen_size');
	
	/* 修改 0708
	 * 增加传入参数 device_id
	 * 判断 device_id 是否存在
	 */
	$device_id = intval(__getPost('model'));
	//android 数据库连接
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}
	$model_name = __getPost('model_name');
	$sql = "select id from am_device  where id = ".$device_id;
	$rs = mysql_query($sql, $conn);
	$row = mysql_fetch_assoc($rs);
	if(!$row['id']){
		$sql2 = "select id from am_device where screen='$screen' and sdk_version<='".__getPost('sdk')."'";
		$rs2 = mysql_query($sql2, $conn);
		$row2 = mysql_fetch_assoc($rs2);
		if($row2['id']) $device_id = $row2['id'];
		else{
			$sql3 = "select id from am_device where model_name='".$model_name."'";
			$rs3 = mysql_query($sql3, $conn);
			$row3 = mysql_fetch_assoc($rs3);
			if($row3['id']) $device_id = $row3['id'];
			else{
				echo error2json("E205");
				die;
			}
		}
	}
	
	$deviceTypeIdResult = "";
	$deviceIdResult = $device_id;
	if($device_id > 10000)
	{
		//记录
		$email = "fisher@smartermob.com";
		feedbackQuestion($email);
	}
		
	
	$AM_CURRENT_REQUEST["IMEI"]     = stopSql(__getPost('imei'));
	$AM_CURRENT_REQUEST["MODEL"]    = $deviceIdResult;	
	$AM_CURRENT_REQUEST["DEVICE_TYPE_ID"]    = "";
	$AM_CURRENT_REQUEST["APP_VER"]  = stopSql(__getPost('ver'));
	$AM_CURRENT_REQUEST["CHANNEL"]  = intval(__getPost('channel'));

	$mid = 0;
	// 
	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}
	$sql = "insert into am_terminal (`imei`, `create_time`, `app_version`, `device_id`, `channel_id`, `status`) values ('". $AM_CURRENT_REQUEST["IMEI"]  ."',NOW(),'".$AM_CURRENT_REQUEST["APP_VER"] ."', ". $AM_CURRENT_REQUEST["MODEL"] .",'". $AM_CURRENT_REQUEST["CHANNEL"] ."',1)";
	//echo $sql."<br />";
	if(mysql_query($sql, $conn)){
		$mid = mysql_insert_id();
		//echo $mid;
		//新增一条am_terminal_info
		$sqlInfo = "insert into am_terminal_info (`mid`, `board`, `brand`, `cpu_abi`, `device`, `diplay`, `fingerprint`, `host`, `build_id`, `manufacturer`, `model_name`, `product`, `tags`, `time`, `type`, `user`, `codename`, `incremental`, `release`, `sdk`, `sdk_int`, `screen_size`) values ('". $mid  ."','".__getPost("board") ."', '". __getPost("brand") ."','". __getPost("cpu_abi") ."','". __getPost("device") ."','". __getPost("diplay") ."','". __getPost("fingerprint") ."','". __getPost("host") ."','". __getPost("build_id") ."','". __getPost("manufacturer") ."','". __getPost("model_name") ."','". __getPost("product") ."','". __getPost("tags") ."','". __getPost('time') ."','". __getPost('type') ."','". __getPost("user") ."','". __getPost("codename") ."','". __getPost("incremental") ."','". __getPost("release") ."','". __getPost('sdk') ."','". __getPost("sdk_int") ."','". $screen ."')";
		//echo $sqlInfo."<br />";die;
		mysql_query($sqlInfo, $conn);
	}else{
		log_message($sql, 'S');
		echo error2json("S004");
		die;
	}
	
	if(!in_array($screen , $AM_SCREEN_EXISTS)){
		$sql = "insert into am_screen_log set screen='". $screen ."', sdk= '". __getPost('sdk') ."', dateline= '".time()."'";
		if(mysql_query($sql, $conn) === FALSE){
			echo error2json_withlog("S007", true, "Sql: ". $sql);die;
		}
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
	$_SESSION["device_type_id"]   = "";
	if(isset($_SESSION["username"])){
		unset($_SESSION["username"]);
	}
	if(isset($_SESSION["uid"])){
		unset($_SESSION["uid"]);
	}
	//$guide_rand = rand(0,1);
	//$user_guide = ($guide_rand)?1:3;
	$user_guide = 1;
	echo array2json(
			array(
				"proto"      =>  1,
				"reqsuccess" =>  AM_REQUEST_SUCCESS,
				"mid"        =>  $mid,
				"sid"        =>  $sessionId,
				"device_type_id"=>$deviceTypeIdResult,
				"device_id"=>$deviceIdResult,
				"user_guide"=>$user_guide
			)
	);
	//if($memobj)$memobj->close();
function feedbackQuestion($email)
{	
	/*****************记录文本 *****************************/
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

