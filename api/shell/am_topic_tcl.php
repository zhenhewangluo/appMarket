<?php
	/************************************************************
	 *   导入TCL正版应用
     *   Author: liu jingqi
	 *   Create Time: 2010-11-26
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
		file_put_contents("am_topic_tcl_log.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}

	$sql = "select app_id from am_appinfo where copyright=1 and app_visible=1 order by app_id";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
	    $sql2 = "insert into am_app_topic set topic_id='95',`order`='99',app_id=".$row['app_id'];
	    echo $sql2."<br/>";
	    mysql_query($sql2, $conn);
	}
		
	echo "success!!!!";
	
	@mysql_close($conn);
?>
