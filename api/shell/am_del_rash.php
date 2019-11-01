<?php
	/************************************************************
	 *   删除垃圾数据
     *   Author: liu jingqi
	 *   Create Time: 2010-12-07
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
	
	$rash = $_GET['rash']?$_GET['rash']:"186 6609 5925";
	$rash = @iconv("gb2312","UTF-8",$rash);
	$sql = "select app_id,count(*) as c from am_comment where content like '%$rash%' group by app_id order by c desc";
	echo $sql."<br/><br/><br/>";
	$result = mysql_query($sql, $conn);
	$arrR = array();$j=0;
	while ($row = mysql_fetch_assoc($result)){
		$sql = "update `am_appinfo` set  total_comments = total_comments - ".$row['c']." where app_id=".$row['app_id'];
		echo $sql."<br/>";
		mysql_query($sql, $conn);
		
	}
	
	$sql = "delete from am_comment where content like '%$rash%'";
	mysql_query($sql, $conn);
	
	@mysql_close($conn);
	echo "success!";
?>
