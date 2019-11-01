<?php
	/************************************************************
	 *   Interface 17
     *   下载应用截图   download_screeshots.php  
	 *
     *   Author:  bluesie
	 *   Time:    2010-6-30
	 *	 paras    proto uid mid sid  appid  
	 *************************************************************/
	require("../inc/init_interface.php");
	
    if($AM_CURRENT_REQUEST["PROTO"] != 17){
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

	//test test test test test
	//$current_device_id = 1;


	$sql = "select app_id,screenshots from am_app_device_type where app_id = ".$appid." and device_type_id=".$current_device_id;
	//echo $sql;
	$rs = mysql_query($sql, $conn);
	$row = mysql_fetch_assoc($rs);
	$screenshots = $row['screenshots'];
	if($screenshots)
	{
		$aScree = explode("|",$screenshots);
		foreach($aScree as $key=>$val)
		{
			$screenshots_file = AM_SITE_ROOT."res/".$val;
			$screenshots_fileurl = AM_SITE_URL.$val;
			if(!file_get_contents($screenshots_file))
			{
				$aResult[$key]['url'] = '';
			}
			else
			{
				$aResult[$key]['url'] = $screenshots_fileurl;
			}
		}
		$json_arr = array(
			"proto" => 17,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => $aResult,		
		);
	}
	else
	{
		echo error2json("E107");
		die;
	}
	//print_r($aResult);
	//exit();

	echo array2json($json_arr); 
	@mysql_free_result($rs);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
