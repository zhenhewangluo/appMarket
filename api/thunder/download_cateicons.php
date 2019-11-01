<?php
	/************************************************************
	 *   Interface 16
     *   下载分类cate icon  download_cateicons.php  
	 *
     *   Author:  bluesie
	 *   Time:    2010-6-30
	 *	 paras    proto uid mid sid  catelist  
	 *************************************************************/
	require("../inc/init_interface.php");
	
    if($AM_CURRENT_REQUEST["PROTO"] != 16){
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
	$catelist = __getPost('catelist');
	$aCate = explode(",",$catelist);
	$aId = array();
	foreach($aCate as $val)
	{
		$aId[] = intval($val);
	}
	$catelist = @join(",",$aId);
	//apk_info
	//test test test test test
	//$current_device_id = 1;


	$sql = "select category_id,icon from am_category_device_type where category_id in (".$catelist.") and device_type_id=".$current_device_id;
	//echo $sql;
	$rs = mysql_query($sql, $conn);
	$i = 0;
	while ($row = mysql_fetch_assoc($rs))
	{
		$icon = $row['icon'];
		$aResult[$i]['cateid'] = $row['category_id'];
		if($icon)
		{
			$icon_file = AM_SITE_ROOT."res/".$icon;
			$icon_fileurl = AM_SITE_URL.$icon;
			//echo $icon_fileurl."<br />";
			if(!file_exists($icon_file))
			{
				$aResult[$i]['icon_url'] = '';
			}
			else
			{
				$aResult[$i]['icon_url'] = $icon_fileurl;
			}
		}
		else
		{
			$aResult[$i]['icon_url'] = '';
		}
		$i++;
	}
	//print_r($aResult);
	//exit();
	if($aResult)
	{
		$json_arr = array(
			"proto" => 16,
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
