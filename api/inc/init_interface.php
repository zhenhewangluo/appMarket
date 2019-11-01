<?php
	global $___bajiephp_loaded_time;
	$___bajiephp_loaded_time = microtime();

	 //include common config and tools functions.
	 require("config.inc.php");	
	 require_once(AM_SITE_ROOT ."inc/error.eng.php");
	 require_once(AM_SITE_ROOT ."inc/functions.php");
	 require_once(AM_SITE_ROOT ."inc/channel_config.php");
	 require_once(AM_SITE_ROOT ."inc/thunder_interface_config.php");
	 require_once(AM_SITE_ROOT ."inc/Log.php");
	
	 //every interface needs this parameter
	 if(!isset($_POST["proto"])){
		echo error2json("E100");
		die;
	 }	
	 if(!is_numeric($_POST["proto"])){
		echo error2json("E101");
		die;
	 }

	$AM_CURRENT_REQUEST["PROTO"] = intval(__getRequest("proto"));
	$AM_LOGGING_INFO["LOG_ALLOW_MIN_LEVEL"] = $interface_config["LOG_ALLOW_MIN_LEVEL"];
	//need sid/mid uploaded by POST
	if(!isset($_POST["mid"]) || !isset($_POST["sid"])){
		echo error2json("E009");
		die;
	}		
	if(empty($_POST["mid"]) || empty($_POST["sid"])){
		echo error2json("E010");
		die;
	}	
	$mid =  intval(__getRequest("mid"));
	$uid =  isset($_POST["uid"]) ? intval(__getRequest("uid")) : 0;
	$sid =  stopSql(__getRequest("sid"));
	if(!is_valid_session_id($sid)){
		log_message("Invalid Session ID:". $sid, 'S');
		echo error2json("E006");
		die;			
	}
	session_id($sid);
	session_start();

	if(isset($_SESSION["mid"])){
		if( $mid != intval($_SESSION["mid"])) {
			echo error2json("E011");
			die;	
		}
	}else{
		$conn = connect_comm_db();
		if($conn === FALSE){
			echo error2json("S100");
			die;
		}
		$sql = "select * from am_terminal where mid=". $mid;
		$rs_ter = mysql_query($sql, $conn);
		if($rs_ter === FALSE){
			echo error2json("S401");
			die;
		}
		if(mysql_num_rows($rs_ter) == 0){
			log_message("No specified terminal:" . $mid, 'S');
			echo error2json("E402");
			die;
		}else{
			$row = mysql_fetch_assoc($rs_ter);
			$_SESSION["mid"] = $mid;
			$_SESSION["channel"] = $row["channel_id"];
			$_SESSION["model"]   = $row["device_id"];
		}	
		@mysql_free_result($rs_ter);
		
		if($uid != 0){
			$sql = sprintf("select * from am_registered_user where id=%d and last_login_sid='%s'", $uid, $sid);
			$rs_user = mysql_query($sql);
			if($rs_user === FALSE){
				log_message($sql, 'S');
				echo $sql ."\n";
				echo error2json("S003");
				die;
			}
			if(mysql_num_rows($rs_user) > 0){
				$row = mysql_fetch_assoc($rs_user);
				$_SESSION["username"] = $row["email"];
				$_SESSION["uid"]      = $row["id"];
			}	
		}
		log_message("[RELOGON] " . array2str($_SESSION) .',sid='.$sid, 'I');
	}

	$AM_CURRENT_REQUEST["MID"] = $mid;
		$AM_CURRENT_REQUEST["SID"] = $sid;
	$AM_CURRENT_REQUEST["CHANNEL"] = $_SESSION["channel"];
	$AM_CURRENT_REQUEST["MODEL"] = $_SESSION["model"];
	log_message("[INIT DONE]". array2str($AM_CURRENT_REQUEST), 'I');
?>
