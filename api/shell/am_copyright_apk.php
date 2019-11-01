<?php
	/************************************************************
	 *   拷贝没有版权APK到一个目录下
     *   Author: liu jingqi
	 *   Create Time: 2011-08-17
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
		file_put_contents("am_copyright_apk.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	
	
	$sql = "select app_id from am_appinfo where copyright!=1 and app_visible=1";
	echo $sql."<br/>";
	$result = mysql_query($sql, $conn);
	$arrR = array();$j=0;
	while ($row = mysql_fetch_assoc($result)){
		$arrR[] = $row['app_id'];
	}
	
	$sql = "select apk_path from am_app_device_type where app_id in(".@implode(",",$arrR).")";
	echo $sql."<br/>";
	$result = mysql_query($sql, $conn);
	$path = "./res/"; 
	if(!file_exists($path)) mkdir($path , 0777);
	while ($row = mysql_fetch_assoc($result)){
		if(file_exists($path.$row['apk_path'])) continue;
 		$list = @explode("/" , $row['apk_path']);
		$cpath = $path;
		for($i=0; $i<count($list); $i++){
			if($i+1 == count($list)){
				echo "cp -r ../res/".$row['apk_path']." ".$cpath."<br/>";
				system("cp -r ../res/".$row['apk_path']." ".$cpath);
			}else{
				$cpath .= $list[$i]."/";
				if(!file_exists($cpath)) mkdir($cpath , 0777);
			}
		}
	}
	
	echo "success!";
?>
