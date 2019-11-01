<?php
	require("./inc/init.php");	

	if($AM_CURRENT_REQUEST["PROTO"] != 19){
		echo error2json("E002");
		die;
	}
	$mid = $AM_CURRENT_REQUEST["MID"];
	$channel_id = $AM_CURRENT_REQUEST["CHANNEL"];
	$model  = $AM_CURRENT_REQUEST["MODEL"];
	$current_uid = isset($_SESSION["uid"])? $_SESSION["uid"] : 0;
	$current_email = isset($_SESSION["username"]) ? $_SESSION["username"] : '';
	
	//update logout information
	if($current_uid !=0 && $current_email != ''){
		
		//commented by lixiaan for bajiev2
		$conn = connect_comm_db();
		if($conn === FALSE){
			echo error2json("S100");
			die;
		}
		$sql = sprintf("update am_registered_user set last_logout_terminal=%d, last_logout_time=NOW() where id=%d and email='%s'",$AM_CURRENT_REQUEST["MID"], $current_uid, $current_email);
		
		if(mysql_query($sql) === FALSE){
			log_message($sql, 'S');
			echo error2json('S005');
			die;
		}	
	}

	//destroy the old session and generate a new one 
	session_destroy();
	session_start();
	$_SESSION["mid"] = $mid;
	$_SESSION["channel"] = $channel_id;
	$_SESSION["model"]   = $model;
	
	log_message(sprintf("[LOGOUT]MID=%s,UID=%s", $AM_CURRENT_REQUEST["MID"], $current_uid),'I');

	echo array2json(array(
		"proto" => 19,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
		"sid"        =>  session_id(),
	));
	
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
