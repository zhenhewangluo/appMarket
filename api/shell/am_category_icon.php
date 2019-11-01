<?php
	/************************************************************
	 *   分类的图标取下载最多软件的图标,cate_id 属于1~6过滤（定时执行）
     *   服务器接口文件： am_category_icon.php 
     *   执行时间：每周
     *
     *   Author: liu jingqi
	 *   Create Time: 2010-11-01
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
		file_put_contents("am_category_icon_log.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
////分类ICON修改
	$sql = "select id,category_id from am_category_device_type where category_id in(select id from am_category where visible=1 and id not in(1,2,3,4,5,6))";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
			//查询2010.11.11 xxxxxxxxx
		$arrTestDeviceId = array();
		/*$screen = $row['screen'];
		$sqlD = "select app_device_type_id from am_device_type where screen='$screen' and app_device_type_id>0";
		$rs = mysql_query($sqlD, $conn);
		while ($row = mysql_fetch_assoc($rs)) {
			$arrTestDeviceId[] = $row['app_device_type_id'];
		}
		if(count($arrTestDeviceId)<1) $arrTestDeviceId[] = 0;
		*/
		$id = $row['id'];
	    $sql2 = "select distinct a.app_id from am_app_category a where a.cate_id = ".$row['category_id'];
	    $rs2 = mysql_query($sql2, $conn);
	    $ArrTemp = array();
		while ($row2 = mysql_fetch_assoc($rs2)) {
			$ArrTemp[] = $row2['app_id'];
		}
		if($ArrTemp){
			$t = time() - 3600*24*7;
			$sql3 = "select count(*) as count,app_id from am_download_history where UNIX_TIMESTAMP(create_time)>$t and app_id in(".implode("," , $ArrTemp).") group by app_id order by count desc,app_id desc limit 1";
			$rs3 = mysql_query($sql3, $conn);
			$row3 = mysql_fetch_assoc($rs3);
			
			if($row3['app_id']){
				$sql4 = "select icon from am_app_device_type where app_id=".$row3['app_id']." order by id";
				$rs4 = mysql_query($sql4, $conn);
				$row4 = mysql_fetch_assoc($rs4);
				
				if($row4['icon']){
					$sql5 = "update am_category_device_type set icon='".$row4['icon']."' where id=".$id;
					mysql_query($sql5, $conn);
					file_put_contents("am_category_icon_".date("Ymd").".txt" , $sql5."\n" ,FILE_APPEND );
				}
				//echo $sql5."<br/>";
			}
		
		}
	}
	
////专题ICON修改
	$sql = "select id,topic_id from am_topic_device_type where topic_id in(select id from am_topic where visible=1)";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
		$id = $row['id'];
		//查询2010.11.11 xxxxxxxxx
		$arrTestDeviceId = array();
		/*$screen = $row['screen'];
		$sqlD = "select app_device_type_id from am_device_type where screen='$screen' and app_device_type_id>0";
		$rs = mysql_query($sqlD, $conn);
		while ($row = mysql_fetch_assoc($rs)) {
			$arrTestDeviceId[] = $row['app_device_type_id'];
		}	
		if(count($arrTestDeviceId)<1) $arrTestDeviceId[] = 0;
		*/
	    $sql2 = "select distinct a.app_id from am_app_topic a where a.topic_id = ".$row['topic_id'];
	    $rs2 = mysql_query($sql2, $conn);
	    $ArrTemp = array();
		while ($row2 = mysql_fetch_assoc($rs2)) {
			$ArrTemp[] = $row2['app_id'];
		}
		if($ArrTemp){
			$t = time() - 3600*24*7;
			$sql3 = "select count(*) as count,app_id from am_download_history where UNIX_TIMESTAMP(create_time)>$t and app_id in(".implode("," , $ArrTemp).") group by app_id order by count desc,app_id desc limit 1";
			$rs3 = mysql_query($sql3, $conn);
			$row3 = mysql_fetch_assoc($rs3);
			
			if($row3['app_id']){
				$sql4 = "select icon from am_app_device_type where app_id=".$row3['app_id']." order by id ";
				$rs4 = mysql_query($sql4, $conn);
				$row4 = mysql_fetch_assoc($rs4);
				
				if($row4['icon']){
					$sql5 = "update am_topic_device_type set icon='".$row4['icon']."' where id=".$id;
					mysql_query($sql5, $conn);
					file_put_contents("am_category_icon_".date("Ymd").".txt" , $sql5."\n" ,FILE_APPEND );
				}
				//echo $sql5."<br/>";
			}
		
		}
	}
	echo "success!";
	@mysql_close($conn);
?>
