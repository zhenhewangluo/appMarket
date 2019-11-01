<?php
	/************************************************************
	 *   最近七天原始数据

     *   Author: liu jingqi
	 *   Create Time: 2011-08-19
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("../../inc/config.inc.php");	
	 require_once(AM_SITE_ROOT ."inc/error.eng.php");	 
	 include(AM_SITE_ROOT ."inc/functions.php");

	//连接数据库
	$conn = connect_db();
	if($conn === FALSE){
		////记录连接数据库失败
		file_put_contents("statics_3.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	$s_date = date("Y-m-d 00:00:00" , time()-8*3600*24);

	$m = $k = 0;
	
	//$channel_id = 1;
	$strFile = "mid  时间  版本  设备号  渠道号  状态（1表示成功）  设备IMEI号\n";
	$sql = "SELECT `mid` , `imei` , `create_time` , `app_version` , `device_id` , `channel_id` , `status` FROM xxxx_v2_common.`am_terminal` WHERE `create_time` > '$s_date' ORDER BY create_time DESC ";
	$result = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($result)){
		$strFile .= $row['mid']."  ".$row['create_time']."  ".$row['app_version']."  ".$row['device_id']."  ".$row['channel_id']."  ".$row['status']."  ".$row['imei']."\n";
	}
	file_put_contents("static_3.txt" , $strFile);
	
	
	
	///////开启量
	$strFile = "mid  时间  \n";
	$sql = "SELECT mid, from_unixtime( `createtime` , '%Y-%m-%d %H:%i:%s' ) AS create_time FROM xxxx_v2_android.`am_client_open_history` WHERE from_unixtime( `createtime` , '%Y-%m-%d %H:%i:%s' ) > '$s_date' ORDER BY createtime DESC ";
	$result = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($result)){
		$strFile .= $row['mid']."  ".$row['create_time']."\n";
	}
	file_put_contents("static_4.txt" , $strFile);
	
	///////下载量
	$strFile = "mid  应用ID  下载开始时间  下载结束时间  状态（1表示成功）  类型  来源  \n";
	$sql = "SELECT mid,app_id,create_time,end_time,status,type,source FROM `am_download_history_month` WHERE create_time>'$s_date'";
	$result = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($result)){
		$strFile .= $row['mid']."  ".$row['app_id']."  ".$row['create_time']."  ".$row['end_time']."  ".$row['status']."  ".$row['type']."  ".$row['source']."\n";
	}
	file_put_contents("static_5.txt" , $strFile);
	
	echo "success";
?>
