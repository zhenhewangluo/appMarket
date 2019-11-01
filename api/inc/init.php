<?php

global $___bajiephp_loaded_time;
$___bajiephp_loaded_time = microtime();

//include common config and tools functions.
//$url =  dirname(__FILE__);
require("config.inc.php");
// echo AM_SITE_ROOT . "inc/error.eng.php";die;
require_once(AM_SITE_ROOT . "inc/error.eng.php");

include(AM_SITE_ROOT . "inc/functions.php");
include(AM_SITE_ROOT . "inc/channel_config.php");
include(AM_SITE_ROOT . "inc/interface_config.php");
include(AM_SITE_ROOT . "inc/Log.php");

//every interface needs this parameter
if (!isset($_POST["proto"]))
{
	echo error2json("E100");
	die;
}


if (!is_numeric(__getPost("proto")))
{
	echo error2json("E101");
	die;
}
//store it in global array AM_CURRENT_REQUEST
$AM_CURRENT_REQUEST["PROTO"] = intval(__getPost("proto"));

//get the config infor for current interface
//including authorize & log level.
$interface_config = isset($INTERFACE_CONFIG_ARR[$AM_CURRENT_REQUEST["PROTO"]]) ? $INTERFACE_CONFIG_ARR[$AM_CURRENT_REQUEST["PROTO"]] : $INTERFACE_CONFIG_ARR[0];

//set the log level for current interface.
$AM_LOGGING_INFO["LOG_ALLOW_MIN_LEVEL"] = $interface_config["LOG_ALLOW_MIN_LEVEL"];
if (!in_array($_POST['screen_size'], $AM_SCREEN_EXISTS))
{
	$_POST['screen_size'] = "480x800";
	$_POST['sdk'] = "8";
}
// =============  Auth Checking BOF ==================
if ($interface_config["AUTH_LEVEL"] != AM_AUTH_FOR_ALL)
{

	//need sid/mid uploaded by POST
	if (!isset($_POST["mid"]) || !isset($_POST["sid"]))
	{
		echo error2json("E009");
		die;
	}
	if (empty($_POST["mid"]) || empty($_POST["sid"]))
	{
		echo error2json("E010");
		die;
	}
	$mid = intval(__getPost("mid"));
	$uid = isset($_POST["uid"]) ? intval(__getPost("uid")) : 0;
	$sid = stopSql(__getPost("sid"));
	if (!is_valid_session_id($sid))
	{
		log_message("Invalid Session ID:" . $sid, 'S');
		echo error2json("E006");
		die;
	}
	session_id($sid);
	session_start();

	if (isset($_SESSION["mid"]))
	{
		if ($mid != intval($_SESSION["mid"]))
		{
			echo error2json("E011");
			die;
		}
	}
	else
	{
		$conn = connect_comm_db();
		if ($conn === FALSE)
		{
			echo error2json("S100");
			die;
		}
		$sql = "select * from am_terminal where mid=" . $mid;
		$rs_ter = mysql_query($sql, $conn);
		if ($rs_ter === FALSE)
		{
			echo error2json("S401");
			die;
		}
		if (mysql_num_rows($rs_ter) == 0)
		{
			log_message("No specified terminal:" . $mid, 'S');
			echo error2json("E402");
			die;
		}
		else
		{
			$row = mysql_fetch_assoc($rs_ter);
			$_SESSION["mid"] = $mid;
			$_SESSION["channel"] = $row["channel_id"];
			$_SESSION["model"] = $row["device_id"];
		}
		@mysql_free_result($rs_ter);

		if ($uid != 0)
		{
			$sql = sprintf("select * from am_registered_user where id=%d ", $uid);
			$rs_user = mysql_query($sql);
			if ($rs_user === FALSE)
			{
				log_message($sql, 'S');
				echo $sql . "\n";
				echo error2json("S003");
				die;
			}
			if (mysql_num_rows($rs_user) > 0)
			{
				$row = mysql_fetch_assoc($rs_user);
				$_SESSION["username"] = $row["email"];
				$_SESSION["uid"] = $row["id"];
			}
		}
		log_message("[RELOGON] " . array2str($_SESSION) . ',sid=' . $sid, 'I');
	}

	$AM_CURRENT_REQUEST["MID"] = $mid;
	$AM_CURRENT_REQUEST["SID"] = $sid;
	$AM_CURRENT_REQUEST["CHANNEL"] = $_SESSION["channel"];
	$AM_CURRENT_REQUEST["MODEL"] = $_SESSION["model"];

	//If the interface needs user to logon, check if the username is in session
	if ($interface_config["AUTH_LEVEL"] == AM_AUTH_NEED_LOGON)
	{

		if (!isset($_SESSION["username"]) || !isset($_SESSION["uid"]))
		{
			echo error2json("E012");
			die;
		}
		else if ($uid == 0 || $uid != $_SESSION["uid"])
		{
			log_message("Unmatched uid between post & session");
			echo error2json("E013");
			die;
		}
		else
		{
			//important, interface can get the logon user's username from
			//global array.
			$AM_CURRENT_REQUEST["USERNAME"] = $_SESSION["username"];
			$AM_CURRENT_REQUEST["UID"] = $_SESSION["uid"];
		}
	}
	else if ($AM_CURRENT_REQUEST["PROTO"] == 11)
	{
		// FIXME: this is for the download interface to get the uid/username
		// 	should use smarter way
		$AM_CURRENT_REQUEST["USERNAME"] = isset($_SESSION["username"]) ? $_SESSION["username"] : "";
		$AM_CURRENT_REQUEST["UID"] = isset($_SESSION["uid"]) ? $_SESSION["uid"] : 0;
	}
}
log_message("[INIT DONE]" . array2str($AM_CURRENT_REQUEST), 'I');
?>


