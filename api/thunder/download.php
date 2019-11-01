<?php
	require("../inc/init.php");
	
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
	$AM_CURRENT_REQUEST["PAYID"] = intval(__getPost('payid'));
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	
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

	//THE END
	$sql = "select apk_path from am_app_device_type where app_id=". $AM_CURRENT_REQUEST["APPID"];
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

	$is_logon = isset($_SESSION["username"]) && isset($_SESSION["uid"]);
	if(!$is_logon){
		// user hasn't logged in
		if($app_price > 0){
			echo error2json('E008');
			die;
		}else{
			$sql = "select count(*) from am_download_history where mid=". $AM_CURRENT_REQUEST["MID"];
			
			$rs_check = mysql_query($sql);
			if($rs_check === FALSE){
				echo error2json("S002");
				die;
			}
			$nums = mysql_result($rs_check, 0, 0);
			if($nums > AM_MAX_ANONY_DOWNLOAD_TIME){
				log_message("[DOWNLOAD]Download times of anoymouse user overflow, MID=". $AM_CURRENT_REQUEST["MID"], "S");
				echo error2json('E008');
				die;
			}
		}
	}
	//$AM_CURRENT_REQUEST1["UID"] = 10120;
	if($app_price > 0){
		$sql = "select id from am_consume where id=". $AM_CURRENT_REQUEST["PAYID"] ." and result=1 and product=".$AM_CURRENT_REQUEST["APPID"]." and user_id=". $AM_CURRENT_REQUEST["UID"];
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
		$sqlUpdate = "update am_download_history set create_time =  NOW() where id = ".$downloadid;
		
		if(mysql_query($sqlUpdate, $conn) === FALSE){
				echo error2json_withlog("S004", true, "Sql: ". $sql);
				die;
		}
		$dl_id = $downloadid;
	}
	else
	{
		$sql = "insert into am_download_history (mid, user_id,app_id,session,create_time,status) values(". $AM_CURRENT_REQUEST["MID"] .", ". $current_uid .", ". $AM_CURRENT_REQUEST["APPID"] .", '". $AM_CURRENT_REQUEST["SID"] ."', NOW(), '". $AM_DOWNLOAD_STATUS["START"] ."')";
		
		if(mysql_query($sql, $conn) === FALSE){
				echo error2json_withlog("S004", true, "Sql: ". $sql);
				die;
		}

		$dl_id = mysql_insert_id();
	}
	$response = array(
		"proto"	     =>	 11,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
		"location"   =>  $app_location,
		"download_id" =>  $dl_id,
	);

	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
