<?php
	/************************************************************
	 *   Interface 33
     *   获取某个分类下 的应用详细信息 get_newest_applist.php
	 *   
     *   Author: bluesie
	 *   Create Time: 2010-09-07
	 *	 Update Records:
	 *	 param  cateid		  
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 40){
		echo error2json("E002");
		die;
	}
	
	
	echo array2json(
		array(
			"proto" => 40,
			"reqsuccess" => AM_REQUEST_SUCCESS,	
		)
	);
	if($memobj)$memobj->close();
?>
