<?php
	/*
	 *  修改 添加 source  
	 *  bluesie  2010-8-16
	*/
	require("./inc/init.php");
	
	//check proto 
	if($AM_CURRENT_REQUEST["PROTO"] != 52){
		echo(error2json("E002"));
		die;
	}

	//check appid
	if(!isset($_POST["appid_list"])){
		echo(error2json("E106"));
		die;
	}

	if(empty($_POST["appid_list"])){
		echo(error2json("E107"));
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

$current_uid = isset($AM_CURRENT_REQUEST["UID"])? $AM_CURRENT_REQUEST["UID"] : 0;
	
$applist = explode("," , $_POST["appid_list"]);
 foreach ($applist as $key=>$appid){
 	 $AM_CURRENT_REQUEST["APPID"] = $appid;
 	$sql = "select app_id,app_version,app_price from am_appinfo where app_id=". $appid;
 	$rs = mysql_query($sql, $conn);
 	$app_row       = mysql_fetch_assoc($rs);
	$app_version   = $app_row["app_version"];
	$app_price     = $app_row["app_price"];
	$app_location = "";
	////TCL特殊判断,如果判断条件成功，则下载TCL提供下载包
	if(($_SESSION["channel"] == $xxxxcopyrightchekChannelid[0]) && $TCLArrAppinfo_A890['tcl_'.$appid]){
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
		$sql = "select apk_path from am_app_device_type where app_id=". $appid ." group by app_id ";
		$rs = mysql_query($sql, $conn);
		$app_row       = mysql_fetch_assoc($rs);
		$app_location  = $AM_APP_DOWNLOAD_LOC_PREFIX . $app_row["apk_path"];
	}
	
	//get downid start 
	$sql = "select id from am_download_history where mid=". $AM_CURRENT_REQUEST["MID"]." and app_id = ".$appid;
			
	$app_check = mysql_query($sql);

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
		$sql = "insert into am_download_history (mid, user_id,app_id,source,session,create_time,end_time,status,type,channel_id) values(". $AM_CURRENT_REQUEST["MID"] .", ". $current_uid .", ". $appid .", '".$source."','". $AM_CURRENT_REQUEST["SID"] ."', NOW(), NOW(), '". $AM_DOWNLOAD_STATUS["START"] ."','mobile','".$_SESSION["channel"]."')";
		
		if(mysql_query($sql, $conn) === FALSE){
				echo error2json_withlog("S004", true, "Sql: ". $sql);
				die;
		}

		$dl_id = mysql_insert_id();
	}
	//get downid end
	if($appid && $app_location && $dl_id){
		$outappid[$key]["appid"] = $appid;
		$outappid[$key]["location"] = $app_location;	
		$outappid[$key]["download_id"] = $dl_id;
	}
	
 }


	$response = array(
		"proto"	     =>	 52,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
		"list" => $outappid,
	);
	
	
	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

