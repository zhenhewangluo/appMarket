<?php
	/************************************************************
	 *   Interface 43
     *   客户端开启时间记录
	 *   Author: liu jingqi
	 *   Create Time: 2010-12-22 
	 *			  
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 43){
		echo error2json("E002");
		die;
	}

	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	
	$sql = "insert into am_client_open_history set mid='$mid',uid='".__getPost('uid')."',createtime='".time()."',net_type='".__getPost('net_type')."',net_extra='".__getPost('net_extra')."'";
	mysql_query($sql, $conn);
	
	$response = array(
		"proto" => 43,
		"reqsuccess" => AM_REQUEST_SUCCESS,
	);

	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
	die;		
?>

