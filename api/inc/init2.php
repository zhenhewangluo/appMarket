<?php
	/******************************************
	 *	
	 *	Initialization Commom Page for BajieV2
	 *	@author lixiaan
	 *	@create_time 2010-03-28
	 *  @Updates: 
	 *          2010-5-13 接口权限受interface_config.php控制
	 *			2010-5-30 老的uid改为mid，新的uid为登陆后注册用户id
	 *
	 ******************************************/
	//error_reporting(0);
	/**
	 * 
	 */
	global $___bajiephp_loaded_time;
	$___bajiephp_loaded_time = microtime();
	

	 //include common config and tools functions.
	 require("config.inc.php");	
	 require_once(AM_SITE_ROOT ."inc/error.eng.php");
	 require_once(AM_SITE_ROOT ."inc/functions.php");
	 require_once(AM_SITE_ROOT ."inc/channel_config.php");
	 require_once(AM_SITE_ROOT ."inc/interface_config.php");
	 require_once(AM_SITE_ROOT ."inc/Log.php");
	
	 //every interface needs this parameter
	 if(!isset($_POST["proto"])){
		echo error2json("E100");
		die;
	 }	
	 if(!is_numeric(__getRequest("proto"))){
		echo error2json("E101");
		die;
	 }
	
	 //store it in global array AM_CURRENT_REQUEST
	 $AM_CURRENT_REQUEST["PROTO"] = intval(__getRequest("proto"));
 
	 //get the config infor for current interface
	 //including authorize & log level.
	 $interface_config = isset($INTERFACE_CONFIG_ARR[$AM_CURRENT_REQUEST["PROTO"]]) ? $INTERFACE_CONFIG_ARR[$AM_CURRENT_REQUEST["PROTO"]] : $INTERFACE_CONFIG_ARR[0];

	 //set the log level for current interface.
	 $AM_LOGGING_INFO["LOG_ALLOW_MIN_LEVEL"] = $interface_config["LOG_ALLOW_MIN_LEVEL"];

    // =============  Auth Checking BOF ==================
    if($interface_config["AUTH_LEVEL"] != AM_AUTH_FOR_ALL){
		
		//need sid/uid uploaded by POST
		if(!isset($_POST["mid"]) || !isset($_POST["sid"])){
			echo error2json("E001");
			die;
		}		
		if(empty($_POST["mid"]) || empty($_POST["sid"])){
			echo error2json("E001");
			die;
		}	
		$mid =  intval(__getRequest("mid"));
		$uid =  isset($_POST["uid"]) ? intval(__getRequest("uid")) : 0;
		$sid =  stopSql(__getRequest("sid"));
		if(!is_valid_session_id($sid)){
			log_message("Invalid Session ID:". $sid, 'S');
			echo error2json("E001");
			die;			
		}
		session_id($sid);
		session_start();

		//未过期，判上送MID是否与SESSION中存储的一致
		if(isset($_SESSION["mid"])){
			if( $mid != intval($_SESSION["mid"])) {
				echo error2json("E001");
				die;	
			}
		}else{
			//已过期, 使用上送的UID，从库中读到所需的信息,进行匿名登陆
			$conn = connect_comm_db();
			if($conn === FALSE){
				echo error2json("S100");
				die;
			}
			$sql = "select * from am_terminal where mid=". $mid;
			$rs_ter = mysql_query($sql, $conn);
			if($rs_ter === FALSE){
				echo error2json("S002");
				die;
			}
			if(mysql_num_rows($rs_ter) == 0){
				log_message("No specified terminal:" . $mid, 'S');
				echo error2json("E001");
				die;
			}else{
				$row = mysql_fetch_assoc($rs_ter);
				$_SESSION["mid"] = $mid;
				$_SESSION["channel"] = $row["channel_id"];
				$_SESSION["model"]   = $row["device_id"];
			}	
			@mysql_free_result($rs_ter);
			
			if($uid != 0){
				//若sid与该uid的上次登录session_id一致，则自动登陆此EMAIL。	
				$sql = sprintf("select * from am_registered_user where uid=%d and last_login_sid='%s'", $uid, $sid);
				$rs_user = mysql_query($sql);
				if($rs_user === FALSE){
					log_message($sql, 'S');
					echo error2json("S002");
					die;
				}
				if(mysql_num_rows($rs_user) > 0){
					$row = mysql_fetch_assoc($rs_user);
					$_SESSION["username"] = $row["email"];
					$_SESSION["uid"]      = $row["uid"];
				}	
			}
   		    log_message("[RELOGON] " . array2str($_SESSION) .',sid='.$sid, 'I');
		}

		$AM_CURRENT_REQUEST["MID"] = $mid;
	    $AM_CURRENT_REQUEST["SID"] = $sid;
		$AM_CURRENT_REQUEST["CHANNEL"] = $_SESSION["channel"];
		$AM_CURRENT_REQUEST["MODEL"] = $_SESSION["model"];
		
		//If the interface needs user to logon, check if the username is in session
		if($interface_config["AUTH_LEVEL"] == AM_AUTH_NEED_LOGON){

			if(!isset($_SESSION["username"]) || !isset($_SESSION["uid"])){
				//未登陆
				echo error2json("E008");
				die;
			}else if($uid == 0 || $uid != $_SESSION["uid"]){
				//未上送UID或与SESSION中不一致
				log_message("Unmatched uid between post & session");
				echo error2json("E008");
				die;
			}else{
				//important, interface can get the logon user's username from
				//global array.
				$AM_CURRENT_REQUEST["USERNAME"] = $_SESSION["username"];
				$AM_CURRENT_REQUEST["UID"]      = $_SESSION["uid"];
			}
		}
	}
	log_message("[INIT DONE]". array2str($AM_CURRENT_REQUEST), 'I');
?>
