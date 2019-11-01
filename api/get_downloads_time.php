<?php
	/************************************************************
	 *   Interface 50 
     *   客户端传递一个时间，从这个时间到最新的有多少个下载（返回整型数字）
     *
     *   Author: xxxxxxxxx
	 *   Create Time: 2011-01-20
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	


    if($AM_CURRENT_REQUEST["PROTO"] != 50){
		echo error2json("E002");die;
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
	
	
	$updatetime = (__getPost('updatetime'))?__getPost('updatetime'):0;	
	$sql = "select count(*) as c from am_download_history a  where a.end_time >= '".date("Y-m-d H:i:s",$updatetime)."' and a.mid <>".$mid."  and a.status = 1 and a.type <> 'import'";
	$rs = mysql_query($sql);
	if($rs === FALSE){
		echo error2json("S002");die;
	}
	$nums = mysql_result($rs, 0, 0);
	
	echo array2json(array(
		"proto" => 50,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
		"count"  => $nums,
		"update_interval"  => (time()- $updatetime)
	));
		
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>