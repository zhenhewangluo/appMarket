<?php
	/*
	proto:55
	中间件下载成功
	author:xxxxxxxxx
	date:2011-03-17	
	*/
	require("inc/config.inc.php");	
	require_once("inc/error.eng.php");
	require_once("inc/functions.php");
	
	$AM_CURRENT_REQUEST["PROTO"] = __getPost('proto');
    if(__getPost('proto') != 55){
		echo error2json("E002");die;
	}
	////中间件版本号，暂时未用
	$midware_version = __getPost('midware_version');
	$cp_id = __getPost('cp_id');
	$download_id = (__getPost('download_id'))?__getPost('download_id'):0;
	
	$conn = connect_db();
	
	$sql = "select id from statlog.`middleware_download`  where `id`=".$download_id;
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");die;
	}
	if(mysql_num_rows($rs) == 0){
		echo error2json("E124");die;
	}
	
	$sql = "update statlog.middleware_download set status=1,end_time='".time()."' where `id`=".$download_id;
	mysql_query($sql, $conn);
	
	echo array2json(array(
		"proto" => 55,
  		"reqsuccess"  => AM_REQUEST_SUCCESS,
  		"download_id" => $download_id
	));
	
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
