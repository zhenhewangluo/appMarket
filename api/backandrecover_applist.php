<?php
	require("./inc/init.php");	
    
    if($AM_CURRENT_REQUEST["PROTO"] != 51){
		echo error2json("E002");die;
	}
	
	$type = __getPost('type')?__getPost('type'):0;
	$uid = __getPost('uid');
	$applist = __getPost('applist');
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S100");die;
	}
	
	if($type == 1){
		$applist = trim($applist , ",");
		$sql = "select applist from am_backANDrecover_app where uid=". $uid;
		$rs = mysql_query($sql, $conn);
		$row = mysql_fetch_assoc($rs);
		if($row){
			$sql = "update am_backANDrecover_app set applist='$applist',dateline='".time()."' where uid=". $uid;
			mysql_query($sql, $conn);
		}else{
			$sql = "insert into am_backANDrecover_app set uid='$uid',applist='$applist',dateline='".time()."'";
			mysql_query($sql, $conn);
		}
	}else{
		$sql = "select applist from am_backANDrecover_app where uid=". $uid;	
		$rs = mysql_query($sql, $conn);
		$row = mysql_fetch_assoc($rs);
		$applist = $row['applist'];
	}
	
	
	echo array2json(array(
		"proto" => 51,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
		"applist" => ($applist)?$applist:""
	));
	
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
