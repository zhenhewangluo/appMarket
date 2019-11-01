<?php
	/************************************************************
	 *   Interface 13
     *   获取应用评论信息  get_comments.php  
	 *
     *   Author:  bluesie
	 *   Time:    2010-6-29
	 *	 paras    proto uid mid sid  appid  start_no  length	  
	 *************************************************************/
	require("../inc/init_interface.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 13){
		echo error2json("E002");
		die;
	}
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	//paras
	$appid = intval(__getPost('appid'));
	//page
	$page_size = intval(__getPost('length'));	
	$start_no = intval(__getPost('start_no'));
	if($start_no < 0) $start_no = 0;
	
	////memcache缓存
	$isflagexists = 0; $aResult = array();
	$keyName = $AM_MEMCACHE["thunder_am"][0]."_comments_".$start_no."_".$page_size."_".$appid;
	if($AM_MEMCACHE["thunder_am"][2]){
		if(!($aResult = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sqlComm = "select id,user_name,update_time,rate,content from am_comment where app_id=".$appid." and stat='normal'";
		$sqlComm .= ' limit '. $start_no . ',' . $page_size;
		//echo $sqlComm;
		$rs = mysql_query($sqlComm, $conn);
		$i = 0;
		while ($row = mysql_fetch_assoc($rs))
		{
			$aResult[$i]['id'] = $row['id'];
			$aResult[$i]['name'] = $row['user_name'];
			$aResult[$i]['time'] = $row['update_time'];
			$aResult[$i]['rate'] = $row['rate'];
			$aResult[$i]['comment'] = $row['content'];
			$i++;
		}
		if($isflagexists == 2) $memobj->set($keyName ,$aResult , 0 ,$AM_MEMCACHE["thunder_am"][1]);
	}
	//print_r($aResult);
	//exit();
	if(count($aResult)){

		$json_arr = array(
			"proto" => 13,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => $aResult,		
		);
		echo array2json($json_arr); 

	}else{

		echo error2json("E123");
		die;

	}
	
	@mysql_free_result($rs);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
