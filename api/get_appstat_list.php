<?php
	/************************************************************
	 *   Interface 28 for Bajiev2
     *   取应用易变信息列表 get_appstat_list.php
	 *
     *   Author: bluesie
	 *   Create Time: 2010-06-11 Fit to xxxx_ServiceAPI_v2.14 
	 *	 Update Records:
	 *	 paras  proto  uid  sid  mid   applist(appid1,appid2,appid3)	  
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 28){
		echo error2json("E002");
		die;
	}

	if(!isset($_POST['applist'])){
		echo error2json("E197");	
		die;
	}else if(empty($_POST['applist'])){
		echo error2json("E197");
		die;
	}

	$arr = explode(',', __getPost('applist'));
	$app_id_arr = array();
	if($arr === FALSE){
		echo error2json("E197");
		die;
	}
	foreach($arr as $ele){
		$app_id_arr[] = intval($ele);
	}
	$id_list = join(',', $app_id_arr);
	
	////如果是TCL接口，判断正版
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)) $sqlTcl = " and copyright=1 ";
	
	$sql = "select * from am_appinfo where app_id in (". $id_list .")".$sqlTcl;

	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");
		die;
	}

	$applist = array();	
	while ($row = mysql_fetch_assoc($rs)) {
		////评论总分不能大于总次数*5 by xxxxxxxxx 2011-03-31
		checkScoreAndNum($row["total_score"] , $row["total_num"]);
	
		$applist[] = array(
			"appid"      => $row["app_id"], 
			"rating_up"	 => $row["app_rate_up"], 
			"rating_down"=> $row["app_rate_down"], 
			"download_cnt"=>$row["app_downloads"]*AM_DOWNLOAD_CNT, 
			"infover"    => $row["infover"],
			"total_score"    => $row["total_score"],
			"total_num"    => $row["total_num"],
			"is_english"    => $row["is_english"],
			"total_rate_num"    => $row["total_comments"],
		);		
	}
	@mysql_free_result($rs);
	
	$response = array(
		"proto" => 28,
		"reqsuccess" => AM_REQUEST_SUCCESS,
		"list"       => $applist,
			);

	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
	die;		
?>
