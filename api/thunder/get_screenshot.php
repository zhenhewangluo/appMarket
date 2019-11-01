<?php
	require("../inc/init.php");	
	
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

	// get device id 
	$current_device_id = $AM_CURRENT_REQUEST["MODEL"];
	if($current_device_id == 0){
		echo error2json("E195");
		die;
	}

	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}

	// get device type
	$sql = "select * from am_device where id=". $current_device_id;
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		log_message($sql, 'S');
		echo error2json("S002");
		die;
	}elseif(mysql_num_rows($rs) == 0){
		echo error2json("E195");
		die;
	}	
	$current_device_arr = mysql_fetch_assoc($rs);

	$screenshots = array();
	$sql = "select screenshots from am_app_device_type where app_id=". $AM_CURRENT_REQUEST["APPID"] ." and device_type_id=". $current_device_arr["device_type_id"];
	$rs = mysql_query($sql, $conn);
	if($rs !== FALSE){
		$row = mysql_fetch_assoc($rs);
		$screenshot_string = $row["screenshots"];
		$arr = explode('|', $screenshot_string);
		foreach($arr as $ele) {
			$screenshots[] = array(
				"name" => "screenshot",
				"url"  => $AM_APP_SCREENSHOT_LOC_PREFIX . $ele,
			);
		}
	}
	
	if(empty($screenshots)){
		echo error2json("E107");
		die;
	}else{
		echo array2json(array(
			"proto" => 7,
      		"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => $screenshots,		
		));
	}
	if($memobj)$memobj->close();
?>
