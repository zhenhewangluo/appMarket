<?php
	require("./inc/init.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 7){
		echo error2json("E002");
		die;
	}
	if(!isset($_POST['appid'])){
		echo error2json("E106");
		die;
	}
	if(empty($_POST['appid']) || !is_numeric($_POST['appid'])){
		echo(error2json("E107"));
		die;
	}
	$AM_CURRENT_REQUEST["APPID"] = intval(__getPost('appid'));

	//ÅÐ¶ÏÊÇ·ñ´«Öµ°àscreen,sdk---2010.11.11
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

	//²éÑ¯2010.11.11 xxxxxxxxx
	$arrTestDeviceId = array(); $isflagexists = 0;
	////memcache»º´æ
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

	$screenshots = array();
	$sql = "select screenshots from am_app_device_type where app_id=". $AM_CURRENT_REQUEST["APPID"] ." group by app_id ";
	$rs = mysql_query($sql, $conn);
	if($rs !== FALSE){
		$row = mysql_fetch_assoc($rs);
		$screenshot_string = $row["screenshots"];
		$arr = explode('|', $screenshot_string);
		foreach($arr as $ele) {
			if($_SESSION["channel"] == 10028){ ////T-PARKÇþµÀ
				$ele = str_replace("/" , "_" , $ele);
				$stri = ($ele)?$AM_APP_LOGO_LOC_PREFIX."nosign/".$ele:"";
			}else{
				$stri = $AM_APP_SCREENSHOT_LOC_PREFIX . $ele;
			}
			$screenshots[] = array(
				"name" => "screenshot",
				"url"  => $stri,
			);
		}
	}
	
	if(empty($screenshots)){
		echo error2json("E107");
	}else{
		echo array2json(array(
			"proto" => 7,
      		"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => $screenshots,		
		));
	}
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
