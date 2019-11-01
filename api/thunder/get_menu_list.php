<?php
	/************************************************************
	 *   Interface 2
     *   获取分类列表 get_menu_list.php
	 *
     *   Author:  bluesie
	 *   Time:    2010-6-28
	 *	 paras  proto uid mid sid  menu_id 	  
	 *************************************************************/
	require("../inc/init_interface.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 2){
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

	$parent_id = intval(__getPost('menu_id'));
	//catagroy
	////memcache缓存
	$isflagexists = 0; $aCate = array();
	$keyName = $AM_MEMCACHE["thunder_am"][0]."_catep_".$parent_id."_".$current_device_id;
	if($AM_MEMCACHE["thunder_am"][2]){
		if(!($aCate = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sqlCate = "select id,name from am_category where parent_id=".$parent_id." and visible=1 and id not in (1,2,3)";
		$rs = mysql_query($sqlCate, $conn);
		if($rs === FALSE){
			log_message($sql, 'S');
			echo error2json("S002");
			die;
		}	
		$i = 0;
		while ($row = mysql_fetch_assoc($rs))
		{
			$aCate[$i]['cate_id'] = $row['id'];
			$aCate[$i]['name'] = $row['name'];
			//是否有子分类
			$sqlSub = "select id from am_category where parent_id=".$row['id'];
			$rsSub = mysql_query($sqlSub, $conn);
			$rowSub = mysql_fetch_assoc($rsSub);
			$subId = $rowSub['id'];
			if($subId) $aCate[$i]['has_subcate'] = true;
			else $aCate[$i]['has_subcate'] = false;
			//应用个数
			$sqlCount = "select count(distinct a.app_id) from am_app_category a left join am_appinfo b on a.app_id=b.app_id where b.app_visible=1 and b.app_id in 
			             ( SELECT app_id from am_app_device_type where device_type_id=".$current_device_id.") and a.cate_id=".$row['id']." order by `order`";
						 //echo $sqlCount;
			$rsCount = mysql_query($sqlCount, $conn);
			$count = mysql_result($rsCount,0,0); 
			$aCate[$i]['appcnt'] = intval($count);
			$i++;
		}
		if($isflagexists == 2) $memobj->set($keyName ,$aCate , 0 ,$AM_MEMCACHE["thunder_am"][1]);
	}

	//exit();
	$aResult = $aCate;
	if(count($aResult)){

		$json_arr = array(
			"proto" => 2,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => $aResult,		
		);

	}else{

		echo error2json("E118");
		die;

	}
	echo array2json($json_arr); 
	@mysql_free_result($rs);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
