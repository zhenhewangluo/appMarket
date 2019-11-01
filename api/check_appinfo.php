<?php
	/************************************************************* 
	 *   Interface NULL
     *   get_feedback.php 接口：检查应用的版本信息 
	 *   Create Time: 2011-01-10
	 *************************************************************/
	 require("./inc/config.inc.php");	
	 require_once(AM_SITE_ROOT ."inc/error.eng.php");	 
	 include(AM_SITE_ROOT ."inc/functions.php");
	 include(AM_SITE_ROOT ."inc/channel_config.php");
	 include(AM_SITE_ROOT ."inc/interface_config.php");
	 include(AM_SITE_ROOT ."inc/Log.php");	
    ob_clean(); 
	$apk_name = __getGet('apk_name');
	if(empty($apk_name))
	exit("faild...");
	////数据库连接
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}	
	$sql = "SELECT `app_id` FROM `am_app_device_type` WHERE pkg_name='".$apk_name."'";	
	$rs1 = mysql_query($sql, $conn);
	//NOT FOUND
	if(mysql_num_rows($rs1) == 0){
		echo "0";
		die;
	}
	$result = mysql_fetch_assoc($rs1);
	$getversion = "SELECT app_version FROM `am_appinfo` WHERE `app_id`=".$result["app_id"];
	$verres = mysql_query($getversion, $conn);
	$verrow = mysql_fetch_array($verres);
	echo "1"."|".$result["app_id"]."|".$verrow["app_version"];
	die;
	
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
