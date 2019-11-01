<?php
	/************************************************************
	 *   要给T-PARK渠道加水印
     *   Author: liu jingqi
	 *   Create Time: 2011-05-13
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
	/*$sql = "select * from am_rank order by screen";
	$result = mysql_query($sql, $conn);
	$str = "";
	while ($row = mysql_fetch_assoc($result)){
		$str .= ",".$row['week'];
		$str .= ",".$row['month'];
		$str .= ",".$row['all'];
	}	*/
	
	$sql = "select app_id from am_appinfo where app_visible=1 order by app_id ";
	$result = mysql_query($sql, $conn);
	$str = "";
	while ($row = mysql_fetch_assoc($result)){
		$str .= ",".$row['app_id'];
	}	
	$arrList = explode("," , $str);
	$arrList = array_unique($arrList);
	
	$sql = "select screenshots from am_app_device_type where app_id in(".trim(@implode("," ,$arrList) , ",").")";
	$result = mysql_query($sql, $conn);
	$str_screenshot = "";
	while ($row = mysql_fetch_assoc($result)){
		$str_screenshot .= "|".$row['screenshots'];
	}	
	
	$arr_screenshot = explode("|" , $str_screenshot);
	$arr_screenshot = array_unique($arr_screenshot);
	if(!file_exists("/home/jinhou/screenshot")) mkdir("/home/jinhou/screenshot" , 0777);
	$AM_SCREEN_EXISTS = array("240x320","240x400","320x480","360x640","480x800","480x854","1280x720");

	foreach($arr_screenshot as $key => $val){
		if($val){
			if(file_exists("../res/".$val)){
				$arrS = explode("/" , $val);
				if(in_array($arrS[2] ,$AM_SCREEN_EXISTS)){
					if(!file_exists("/home/jinhou/screenshot/".$arrS[2])) mkdir("/home/jinhou/screenshot/".$arrS[2] , 0777);
					$filename  = str_replace("/" , "_" , $val);
					$filepath = "/home/jinhou/screenshot/".$arrS[2]."/".$filename;
					if(!file_exists($filepath) || filesize($filepath)<10) {
						copy("../res/".$val ,$filepath);
					}
				}else{
					$filename  = str_replace("/" , "_" , $val);
					copy("../res/".$val ,"/home/jinhou/screenshot/".$filename);
				}
			}
		}
	}
	@mysql_close($conn);
	echo "success!";
?>
