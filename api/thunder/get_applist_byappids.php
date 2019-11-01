<?php
	/************************************************************
	 *   Interface 7
     *   按 appids 获取应用列表 get_applist_byappids.php
	 *
     *   Author:  bluesie
	 *   Time:    2010-6-29
	 *	 paras  proto uid mid sid  applist (1,2,3) 	  
	 *************************************************************/
	require("../inc/init_interface.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 7){
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
	
	$error = 0;
	//paras
	$appids = __getPost('applist');
	$aPara = explode(",",$appids);
	$aId = array();
	foreach($aPara as $val)
	{
		$aId[] = intval($val);
	}
	$appids = @join(",",$aId);
	//info
	$sqlCate = "select a.app_id,a.app_name,a.app_desc,a.app_version,a.version,b.pkg_name,a.app_price,b.app_size,a.app_rate_up,a.app_rate_down,a.author_name from am_appinfo a left join am_app_device_type b on a.app_id =b.app_id  where b.device_type_id=".$current_device_id." and a.app_id in (".$appids.") ";
	//echo $sqlCate."<br />";
	$rs = mysql_query($sqlCate, $conn);
	if($rs === FALSE){
		log_message($sql, 'S');
		echo error2json("S002");
		die;
	}
	$i = 0;
	while ($row = mysql_fetch_assoc($rs))
	{
		$aCate[$i]['app_id'] = $row['app_id'];
		$aCate[$i]['app_name'] = $row['app_name'];
		$aCate[$i]['app_desc'] = deleteHtmlTags($row['app_desc']);
		$aCate[$i]['app_version'] = $row['app_version'];
		$aCate[$i]['version'] = $row['version'];
		$aCate[$i]['pkg_name'] = $row['pkg_name'];
		$aCate[$i]['app_price'] = $row['app_price'];
		$aCate[$i]['app_size'] = $row['app_size'];
		$aCate[$i]['app_rate_up'] = $row['app_rate_up'];
		$aCate[$i]['app_rate_down'] = $row['app_rate_down'];
		$aCate[$i]['author_name'] = $row['author_name'];
		$i++;
	}
	$aResult = $aCate;
	//print_r($aResult);
	//exit();
	if(count($aResult))
	{
		$json_arr = array(
			"proto" => 7,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => $aResult,		
		);
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
