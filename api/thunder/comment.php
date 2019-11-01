<?php
	/************************************************************
	 *   Interface 18
     *   评论   comment.php  
	 *
     *   Author:  bluesie
	 *   Time:    2010-6-30
	 *	 paras    proto uid mid sid  appid  rate  comment
	 *************************************************************/
	require("../inc/init_interface.php");
	
    if($AM_CURRENT_REQUEST["PROTO"] != 18){
		echo error2json("E002");
		die;
	}
	//paras
	$appid = intval(__getPost('appid'));
	$uid = intval(__getPost('uid'));
	if(!isset($_POST['rate'])){
		echo error2json("E203");	
		die;
	}else if(empty($_POST['rate']) || !in_array(__getPost('rate'), array("up", "down"))){
		echo error2json("E203");
		die;
	}
	$rate = trim(__getPost('rate')) == "up" ? "up" : "down";

	
	if(__getPost("comment")){
		$comment = stopSql(__getPost("comment"));
	}	

	//comm
	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	$sql = "select name from am_registered_user where id=". $uid;
	//echo $sql;
	$rs = mysql_query($sql, $conn);
	if(mysql_num_rows($rs) > 0) 
	{
		$row = mysql_fetch_assoc($rs);
		$username = $row['name'];
	}
	else
	{
		echo error2json("E403");
		die;
	}
	//exit();
	//android
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	


	$sql = "select * from am_appinfo where app_id=". $appid;
	
	$rs1 = mysql_query($sql, $conn);
	if($rs1 === FALSE){
		echo error2json("S002");
		die;
	}
	//NOT FOUND
	if(mysql_num_rows($rs1) == 0){
		echo error2json("E107");
		die;
	}

	$sql = "SELECT id,user_id,rate FROM am_comment WHERE app_id = '".$appid."' AND user_id = '".$uid."'";
	//echo $sql;exit();
	$rs = mysql_query($sql);
	if(mysql_num_rows($rs) > 0) {
		$row = mysql_fetch_assoc($rs);
		if($row["rate"] != $rate) {
			// rate changed, need to update appinfo table
			$increase_field = ($rate == "up" ? "app_rate_up" : "app_rate_down");
			$decrease_field = ($rate == "up" ? "app_rate_down" : "app_rate_up");

			$sql = sprintf("update am_appinfo set %s=%s+1, %s=%s-1 where app_id=%s", $increase_field, $increase_field, $decrease_field, $decrease_field, $appid);

			if(mysql_query($sql) === FALSE) {	
				log_message($sql, 'S');
				echo error2json("S002");
				die;
			}
		}
		if($comment)
			$sql = sprintf("update am_comment set user_name='%s', content='%s', update_time=NOW(), rate='%s' where app_id=%s and user_id=%s", $username,$comment, $rate, $appid, $uid);
		else {
			$sql = sprintf("update am_comment set user_name='%s', update_time=NOW(), rate='%s' where app_id=%s and user_id=%s", $username,$rate, $appid, $uid);
		}

		if(mysql_query($sql) === FALSE) {	
		log_message($sql, 'S');
		echo error2json("S002");
		die;
	}

	}else{
		$create_time=time();
		$create_time=date("Y-m-d H:i:s", $create_time);

		$sql = "insert into am_comment(app_id, user_id, user_name,content, create_time, update_time, rate) values('".$appid."', '".$uid."','".$username."', '".$comment."', NOW(), NOW(), '". $rate ."')";
		if(mysql_query($sql) === FALSE)
		{	
			log_message($sql, 'S');
			echo error2json("S002");
			die;
		}
		//修改appinfo 中 rate 值  2010-7-7
		$increase_field = ($rate == "up" ? "app_rate_up" : "app_rate_down");
		//$decrease_field = ($rate == "up" ? "app_rate_down" : "app_rate_up");
		$sqlUpdate = sprintf("update am_appinfo set %s=%s+1 where app_id=%s", $increase_field, $increase_field, $appid);
			if(mysql_query($sqlUpdate) === FALSE) {	
				log_message($sqlUpdate, 'S');
				echo error2json("S002");
				die;
			}
	}

	$channel_config_info = isset($CHANNEL_CONFIG_ARR[$AM_CURRENT_REQUEST["CHANNEL"]]) ? $CHANNEL_CONFIG_ARR[$AM_CURRENT_REQUEST["CHANNEL"]] : $CHANNEL_CONFIG_ARR[0];
	/*
	if(!isset($channel_config_info["logging_actions_flag"])){
		echo error2json("S201");
		die;
	}
	if(!$channel_config_info["logging_actions_flag"]){
		echo array2json(array(
				"proto" => 18,
				"reqsuccess"  => AM_REQUEST_SUCCESS,
			));
		die;
	}
	*/
	echo array2json(array(
			"proto" => 18,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
		));
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
