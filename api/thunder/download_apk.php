<?php
	/************************************************************
	 *   Interface 14
     *   下载应用  download_apk.php  
	 *
     *   Author:  bluesie
	 *   Time:    2010-6-29
	 *	 paras    proto uid mid sid  appid  
	 *************************************************************/
	require("../inc/init_interface.php");	
    if($AM_CURRENT_REQUEST["PROTO"] != 14){
		echo error2json("E002");
		die;
	}
	$current_channel_id = $AM_CURRENT_REQUEST["CHANNEL"];
	$current_device = $AM_CURRENT_REQUEST["MODEL"];
	
	if($current_device == 0){
		echo error2json("E195");
		die;
	}
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	//查询 device_type_id
	////memcache缓存
	$current_device_id = $isflagexists = 0;
	$keyName = $AM_MEMCACHE["thunder_am"][0]."_device_".$current_device;
	if($AM_MEMCACHE["thunder_am"][2]){
		if(!($current_device_id = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sql = "select device_type_id from am_device where id = ".$current_device;
		$rs = mysql_query($sql, $conn);
		$row = mysql_fetch_assoc($rs);
		$current_device_id = $row['device_type_id'];
		if($isflagexists == 2) $memobj->set($keyName ,$current_device_id , 0 ,$AM_MEMCACHE["thunder_am"][1]);
	}
	//paras
	$appid = intval(__getPost('appid'));
	$uid = intval(__getPost('uid'));
	$mid = intval(__getPost('mid'));
	//apk_info
	//test test test test test
	//$current_device_id = 1;
	
	$sql = "select apk_path from am_app_device_type where app_id=".$appid." and device_type_id=".$current_device_id;
	//echo $sql;
	$rs = mysql_query($sql, $conn);
	$row = mysql_fetch_assoc($rs);
	$apk = $row['apk_path'];
	if($apk)
	{
		$apk_file = AM_SITE_ROOT."res/".$apk;
		$apk_fileurl = AM_SITE_URL.$apk;
		if(!file_exists($apk_file))
		{
			$json_arr = array(
				"proto" => 14,
				"reqsuccess"  => AM_REQUEST_FAIL,
				'location'  => '',
				);
		}
		else
		{
				//检查 download mid 对应 appid 条数
				$sql = "select id from am_download_history where mid=". $mid." and app_id = ".$appid;
						
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
					$sql = "insert into am_download_history (mid, user_id,app_id,session,create_time,status) values(". $mid .", ". $uid .", ". $appid .", '". __getPost('sid') ."', NOW(), '0')";
					
					if(mysql_query($sql, $conn) === FALSE){
							echo error2json_withlog("S004", true, "Sql: ". $sql);
							die;
					}

					$dl_id = mysql_insert_id();
				}

				$json_arr = array(
				"proto" => 14,
				"reqsuccess"  => AM_REQUEST_SUCCESS,
				"location"  => $apk_fileurl,
				"download_id" =>  $dl_id,
				);
		}
		echo array2json($json_arr); 
	}
	else
	{
		echo error2json("E107");
		die;
	}

	@mysql_free_result($rs);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
