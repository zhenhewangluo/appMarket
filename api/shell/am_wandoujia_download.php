<?php
	/************************************************************
	 *   宛豆夹下载数据导入到正式机
	 *   每天凌晨执行，导入昨天的数据，同时更新am_appinfo表的app_download字段
     *   Author: liu jingqi
	 *   Create Time: 2011-01-30
	 *   select * from am_download_history_month where type='wandoujia' and  DATE_FORMAT(end_time, '%Y-%m-%d')='2011-01-29'
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("../inc/config.inc.php");	
	include(AM_SITE_ROOT ."inc/functions.php");
	
	//连接数据库
	$conn = connect_db(); 
	if($conn === FALSE){
		////记录连接数据库失败
		file_put_contents("am_wandoujia_download.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	$arrComment = array();
	
	$adddate = date("Y-m-d" , time()-3600*24);	
	$sql = "select `from`,`type`,`appid`,`adddate` from statlog.webstatlog where action='wandoujia' and  DATE_FORMAT(adddate, '%Y-%m-%d')='$adddate' and appid>0 and appid is not null order by appid";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
		$sqlA = "select app_id from am_appinfo where app_id=".$row['appid'];
		$query = mysql_query($sqlA, $conn);
		if(mysql_num_rows($query)){
			$sql2 = "insert into am_download_history_month set app_id='$row[appid]',create_time='$row[adddate]',status=1,type='wandoujia',end_time='$row[adddate]'";
			mysql_query($sql2, $conn);
			
		    $sql4 = "update am_appinfo set app_downloads = app_downloads+1 where app_id=".$row['appid'];
		    mysql_query($sql4, $conn);
	    }
	}
	echo "success";
	@mysql_close($conn);
?>
