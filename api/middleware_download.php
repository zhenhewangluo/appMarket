<?php
	/*
	proto:54
	中间件下载
	author:xxxxxxxxx
	date:2011-03-17	
	*/
	require("inc/config.inc.php");	
	require_once("inc/error.eng.php");
	require_once("inc/functions.php");
	
	$AM_CURRENT_REQUEST["PROTO"] = __getPost('proto');
    if(__getPost('proto') != 54){
		echo error2json("E002");		die;
	}
	////中间件版本号，暂时未用
	$midware_version = __getPost('midware_version');
	$cp_id = __getPost('cp_id');
	
	if(!isset($_POST['appid'])){
		echo(error2json("E106"));die;
	}	
	$app_id = __getPost('appid');
	
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
	
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");die;
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

//------------------------------获取下载地址----------------------------------------------
	$sql = "select apk_path from am_app_device_type where app_id=". $app_id ." group by app_id ";
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");die;
	}
	//NOT FOUND
	if(mysql_num_rows($rs) == 0){
		echo error2json("E124");die;
	}
	$app_row       = mysql_fetch_assoc($rs);
	$app_location  = $AM_APP_DOWNLOAD_LOC_PREFIX . $app_row["apk_path"];
		
	////入库
	$sql = "insert into statlog.middleware_download set cp_id='$cp_id',app_id='".$app_id."',create_time='".time()."',end_time='".time()."',`status`=0";
	mysql_query($sql, $conn);
	$dl_id = mysql_insert_id();
	
	echo array2json(array(
		"proto" => 54,
  		"reqsuccess"  => AM_REQUEST_SUCCESS,
  		"location"   =>  $app_location,
		"download_id" =>  $dl_id
	));
	
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
