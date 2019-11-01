<?php
	/************************************************************
	 *   测试评论数据删除
     *   测试帐号：10156 dang@ovi.com	10157 dara@smartermob.com
     *   Author: liu jingqi
	 *   Create Time: 2010-11-08
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("../inc/config.inc.php");	
	 require_once(AM_SITE_ROOT ."inc/error.eng.php");
	 
	 include(AM_SITE_ROOT ."inc/functions.php");
	 include(AM_SITE_ROOT ."inc/channel_config.php");
	 include(AM_SITE_ROOT ."inc/interface_config.php");
	 include(AM_SITE_ROOT ."inc/Log.php");	
	//连接数据库
	$conn = connect_db();  
	if($conn === FALSE){
		////记录连接数据库失败
		file_put_contents("am_comment_delete_log.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	
	$sql = "select id,app_id,rate,user_id from am_comment where user_id in(10156,10157)";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
		$sql2 = "select app_rate_up,app_rate_down from am_appinfo where app_id=".$row['app_id'];
		$rs2 = mysql_query($sql2, $conn);
		$row2 = mysql_fetch_assoc($rs2);
		if($row['rate'] == "up"){
			if($row2['app_rate_up']>=1){	
				$sql = "update am_appinfo set app_rate_up = app_rate_up-1 where app_id=".$row['app_id'];
				mysql_query($sql, $conn);
				file_put_contents("am_appinfo_delete_log.txt" , $sql."\n" , FILE_APPEND);
			}
		}else{
			if($row2['app_rate_down']>=1){	
				$sql = "update am_appinfo set app_rate_down = app_rate_down-1 where app_id=".$row['app_id'];
				mysql_query($sql, $conn);
				file_put_contents("am_appinfo_delete_log.txt" , $sql."\n" , FILE_APPEND);
			}
		}
	}
	
	$sql = "delete from am_comment where user_id in(10156,10157)";
	mysql_query($sql, $conn);
	
	echo "success!!!!";

	@mysql_close($conn);
?>
