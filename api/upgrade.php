<?php

require("./inc/init.php");
if ($memobj)
	$memobj->close();
if ($AM_CURRENT_REQUEST["PROTO"] != 2)
{
	echo error2json("E002");
	die;
}

//对ver进行检查	
if (!isset($_POST['ver']))
{
	echo error2json("E104");
	die;
}
else if (empty($_POST['ver']))
{
	echo error2json("E105");
	die;
}
else
{
	//更新会员使用版本 start
	$conn = connect_comm_db();
	if ($conn === FALSE)
	{
		echo error2json("S100");
		die;
	}
	$sqlInfo = "UPDATE `_kid_common`.`am_terminal` SET `app_version` = '" . __getPost('ver') . "' WHERE `am_terminal`.`mid` =" . __getPost('mid');
	mysql_query($sqlInfo, $conn);
	//end
	$AM_CURRENT_REQUEST["VER"] = stopSql(__getPost('ver'));
}

// check channel
/* 	if(!isset($CHANNEL_CONFIG_ARR[$AM_CURRENT_REQUEST["CHANNEL"]])){
  echo error2json("S201");
  die;
  }

  $CHANNEL_CONFIG = $CHANNEL_CONFIG_ARR[$AM_CURRENT_REQUEST["CHANNEL"]];

  $current_device_id = $AM_CURRENT_REQUEST["MODEL"];

  //$CHANNEL_CONFIG_INFO = $CHANNEL_CONFIG['release'];
  //update 2010-09-10  bluesie
  $device_config = $CHANNEL_CONFIG['device_list'];
  if($device_config)
  $aDeviceConfig = explode(",",$device_config);
  if(!in_array($current_device_id,$aDeviceConfig))
  {
  echo error2json("E195");
  die;
  } */
$conn = connect_db();
if ($conn === FALSE)
{
	echo error2json("S001");
	die;
}
$sqlInfo = "select * from `kid_pad`.`upgrade`";
$rs = mysql_query($sqlInfo, $conn);
if ($rs === FALSE||mysql_num_rows($rs) == 0)
{
	echo error2json_withlog("S002",false,$sqlInfo);
	die;
}
$row = mysql_fetch_assoc($rs);
$current_version = $AM_CURRENT_REQUEST["VER"]; 
$location = '';
$app_version = '';
$need_upgrade = false;
$changelog = '';
if ($row)
{
	$changelog = $row['changelog'];
	$sql = "select `app_version`,`version`,`apk_path` from `kid_pad`.`am_appinfo` a left join `kid_pad`.`am_app_device_type` b on a.app_id=b.app_id where a.`app_visible`=1 and a.`app_id`=".$row['appid'];
	$rs = mysql_query($sql, $conn);
	if ($rs === FALSE||mysql_num_rows($rs) == 0)
	{
		echo error2json_withlog("S002",false,$sql);
		die;
	}
	$row = mysql_fetch_assoc($rs);
	$current_version = $row['version'];
	$app_version = $row['app_version'];
	$location = $row['apk_path'];
	
	if($current_version > $AM_CURRENT_REQUEST["VER"])
	{
		$need_upgrade = true;
	}
}


//$current_version = $CHANNEL_CONFIG["current_version"]; //xxxx_<app_version>_<channel_name>_<device>.apk
//$app_version = $CHANNEL_CONFIG["app_version"];
//$device_name = $CHANNEL_MAP[$current_device_id];
//$location = $REPO_ROOT . $app_version . "/xxxx_" . $app_version . "_" . $CHANNEL_CONFIG['channel_name'] . "_" . $device_name . ".apk";


//zxg,AM_SITE_URL error ,temporary commite here,should open 
//if (!file_get_contents(AM_SITE_URL."res/".$location))
//{
//	echo error2json_withlog("E151",false,AM_SITE_URL."res/".$location);
//	die;
//}
$location = AM_DOWNLOAD_URL.$location;

//$need_upgrade = $CHANNEL_CONFIG['need_upgrade'];
//$changelog = CHANGELOG;
if (!isset($current_version) || !isset($location))
{
	echo error2json("S201");
	die;
}
else
{
	echo array2json(
			array(
				"proto" => 2,
				"reqsuccess" => AM_REQUEST_SUCCESS,
				"cur_ver" => $current_version,
				"loc" => $location,
				"app_version" => $app_version,
				"need_upgrade" => $need_upgrade,
				"changelog" => $changelog,
			)
	);
	die;
}
?>
