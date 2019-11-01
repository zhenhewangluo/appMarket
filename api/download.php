<?php
	/*
	 *  修改 添加 source  
	 *  bluesie  2010-8-16
	*/
	require("./inc/init.php");
	
	//check proto 
	if($AM_CURRENT_REQUEST["PROTO"] != 11){
		echo(error2json("E002"));
		die;
	}

	//check appid
	if(!isset($_POST['appid'])){
		echo(error2json("E106"));
		die;
	}

	if(empty($_POST['appid']) || !is_numeric($_POST['appid'])){
		echo(error2json("E107"));
		die;
	}
	$AM_CURRENT_REQUEST["APPID"] = intval(__getPost('appid'));
	
	//check payid 
	if(!isset($_POST['payid'])){
		echo(error2json("E152"));
		die;
	}
	
	//判断是否传值班screen,sdk---2010.11.11
	if(!($screen=__getPost('screen_size')) || !($sdk=__getPost('sdk'))){
			$conn = connect_comm_db();
			$res = mysql_query("select screen_size,sdk from am_terminal_info where mid=". $mid, $conn);		
			if(mysql_num_rows($res) == 0){
				$screen = "480x800";
				$sdk = 4;
			}else{
				$result = mysql_fetch_assoc($res);
				$screen	= $result['screen_size'];
				$sdk	= $result['sdk'];
			}
			mysql_close($conn);
	}
	
	$AM_CURRENT_REQUEST["PAYID"] = intval(__getPost('payid'));
	$source = __getPost("source");
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	//查询2010.11.11 xxxxxxxxx
	$arrTestDeviceId = array(); $isflagexists = 0;
	////memcache缓存
	$keyName = $AM_MEMCACHE["am_device_type"][0].$screen.$sdk;
	if($AM_MEMCACHE["am_device_type"][2]){
		if(!($arrTestDeviceId = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sqlD = "select app_device_type_id from am_device_type where screen='$screen' and sdk_version<='$sdk' and app_device_type_id>0";
		$rs = mysql_query($sqlD, $conn);
		while ($row = mysql_fetch_assoc($rs)) {
			$arrTestDeviceId[] = $row['app_device_type_id'];
		}
		if($isflagexists == 2) $memobj->set($keyName ,$arrTestDeviceId , 0 ,$AM_MEMCACHE["am_device_type"][1]);
	}
	if(count($arrTestDeviceId)<1) $arrTestDeviceId[] = 0;
	
	//$AM_CURRENT_REQUEST["APPID"] = 6000037;//  	
	//$AM_CURRENT_REQUEST["MID"] = 295;
	$sql = "select app_id,app_version,app_price from am_appinfo where app_id=". $AM_CURRENT_REQUEST["APPID"];
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){		
		echo error2json("S002");
		die;
	}
	//NOT FOUND
	if(mysql_num_rows($rs) == 0){
		echo error2json("E124");
		die;
	}	
	$app_row       = mysql_fetch_assoc($rs);
	$app_version   = $app_row["app_version"];
	$app_price     = $app_row["app_price"];
	$app_location = "";
	////TCL特殊判断,如果判断条件成功，则下载TCL提供下载包
	if(($_SESSION["channel"] == $xxxxcopyrightchekChannelid[0]) && $TCLArrAppinfo_A890['tcl_'.$AM_CURRENT_REQUEST["APPID"]]){
		mysql_close($conn);
		$conn = connect_comm_db();
		$sql = "select mid from am_terminal_info where mid=". $mid." and product like '%A890%'";
		$rsAppTime = mysql_query($sql, $conn);
		$rowTcl = mysql_fetch_assoc($rsAppTime);
		if($rowTcl['mid'] && $TCLArrAppinfo_A890['tcl_'.$AM_CURRENT_REQUEST["APPID"]]){
			$app_location = $AM_APP_DOWNLOAD_LOC_PREFIX . $TCLArrAppinfo_A890['tcl_'.$AM_CURRENT_REQUEST["APPID"]];
		}else{
			$sql = "select mid from am_terminal_info where mid=". $mid." and product like '%A906%'";
			$rsAppTime = mysql_query($sql, $conn);
			$rowTcl = mysql_fetch_assoc($rsAppTime);
			if($rowTcl['mid'] && $TCLArrAppinfo_A906['tcl_'.$AM_CURRENT_REQUEST["APPID"]])	$app_location = $AM_APP_DOWNLOAD_LOC_PREFIX . $TCLArrAppinfo_A906['tcl_'.$AM_CURRENT_REQUEST["APPID"]];
			else $app_location = "";
		}
		
		mysql_close($conn);
		$conn = connect_db();
	}
	if($app_location == ""){
		$sql = "select apk_path from am_app_device_type where app_id=". $AM_CURRENT_REQUEST["APPID"] ." group by app_id ";
		$rs = mysql_query($sql, $conn);
		if($rs === FALSE){
			echo error2json("S002");
			die;
		}
		//NOT FOUND
		if(mysql_num_rows($rs) == 0){
			echo error2json("E124");
			die;
		}
		$app_row       = mysql_fetch_assoc($rs);
		$app_location  = $AM_APP_DOWNLOAD_LOC_PREFIX . $app_row["apk_path"];
	}
	$is_logon = isset($_SESSION["username"]) && isset($_SESSION["uid"]);
	$notify_login = false;
	if(!$is_logon){
		// user hasn't logged in
		if($app_price > 0){
			echo error2json('E008');
			die;
		}else{
			$sql = "select count(*) from am_download_history where mid=". $AM_CURRENT_REQUEST["MID"]." and type <> 'import'";
			$rs_check = mysql_query($sql);
			if($rs_check === FALSE){
				echo error2json("S002");
				die;
			}
			$nums = mysql_result($rs_check, 0, 0);
			if($nums >= 7){ //2010.11.01修改没注册并下载次数超过七次，提示notify_login = true
				//log_message("[DOWNLOAD]Download times of anoymouse user overflow, MID=". $AM_CURRENT_REQUEST["MID"], "S");
				//echo error2json('E008');die;
				$notify_login = true;
			}
		}
	}
	//$AM_CURRENT_REQUEST1["UID"] = 10120;
	if($app_price > 0){
		$sql = "select id from am_consume where  result=1 and product=".$AM_CURRENT_REQUEST["APPID"]." and user_id=". $AM_CURRENT_REQUEST["UID"];
		$rs1 = mysql_query($sql, $conn);
		if($rs1 === FALSE){
			echo error2json("S002");
			die;
		}
		//NOT FOUND
		if(mysql_num_rows($rs1) == 0){
			echo error2json("E154");
			die;
		}		
	}

	$current_uid = isset($AM_CURRENT_REQUEST["UID"])? $AM_CURRENT_REQUEST["UID"] : 0;

	//检查 download mid 对应 appid 条数
	$sql = "select id from am_download_history where mid=". $AM_CURRENT_REQUEST["MID"]." and app_id = ".$AM_CURRENT_REQUEST["APPID"];
			
	$app_check = mysql_query($sql);
	if($rs_check === FALSE){
		echo error2json("S002");
		die;
	}
	$result = mysql_fetch_assoc($app_check);
	$downloadid = $result['id'];
	if($downloadid)
	{
		$sqlUpdate = "update am_download_history set create_time =  NOW(),end_time=NOW() where id = ".$downloadid;
		
		if(mysql_query($sqlUpdate, $conn) === FALSE){
				echo error2json_withlog("S004", true, "Sql: ". $sql);
				die;
		}
		$dl_id = $downloadid;
	}
	else
	{
		$sql = "insert into am_download_history (mid, user_id,app_id,source,session,create_time,end_time,status,type,channel_id) values(". $AM_CURRENT_REQUEST["MID"] .", ". $current_uid .", ". $AM_CURRENT_REQUEST["APPID"] .", '".$source."','". $AM_CURRENT_REQUEST["SID"] ."', NOW(), NOW(), '". $AM_DOWNLOAD_STATUS["START"] ."','mobile','".$_SESSION["channel"]."')";
		
		if(mysql_query($sql, $conn) === FALSE){
				echo error2json_withlog("S004", true, "Sql: ". $sql);
				die;
		}

		$dl_id = mysql_insert_id();
	}
	//积分
	if(isset($_SESSION["uid"]))
	{
		$data = array("userid"=>$_SESSION["uid"],
			"password"=>$_SESSION["password"],
			"mid"=>$_POST["mid"],
			"type"=>"download",
			"appid"=>$_POST['appid']
			);
		$re = postCurl("http://www.hjapk.com/UserCenter/index.php?m=AppScore&a=addScore", $data);
	}
	$response = array(
		"proto"	     =>	 11,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
		"location"   =>  $app_location,
		"download_id" =>  $dl_id,
		"notify_login"=>$notify_login,
	);
	
	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

