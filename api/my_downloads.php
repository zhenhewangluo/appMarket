<?php
	/************************************************************
	 *   Interface 26 for BajieV2
     *   得到我的下载记录 my_downloads.php  
     *   
     *
     *   Author: Li Xiaan
	 *   Create Time: 2010-05-10
	 *   Updates:
	 * 
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	


    if($AM_CURRENT_REQUEST["PROTO"] != 26){
		echo error2json("E002");
		die;
	}
	
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}

	$sql = "select DISTINCT(A.app_id), A.dl_time, A.dl_finish_time from am_download_history as A, am_appinfo as B where A.app_id=B.app_id and A.user_id=". $AM_CURRENT_REQUEST["UID"] ." and A.dl_status=1 and B.app_visible=1 order by dl_finish_time desc";

	$rs = mysql_query($sql);
	if($rs === FALSE){
		log_message($sql, 'S');
		echo error2json("S002");
		die;
	}
	$download_history = array();
	while($row = mysql_fetch_assoc($rs)){
		$download_history[] = array(
			"appid" => $row["app_id"],
			"start_time" => $row["dl_time"],
			"finish_time"=> $row["dl_finish_time"],
		);
	}

	echo array2json(array(
		"proto" => 26,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
		"list"  => $download_history,
	));
		
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>