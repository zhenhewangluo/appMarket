<?php
	/************************************************************
	 *   Interface 6
     *   获取别人下载应用列表 get_other_download 
	 *
     *   Author:  bluesie
	 *	 mark: 获取别人下载应用列表
	     rule  过滤自己下载的应用
		       返回 appid  id=9994
	 *	 paras  proto  uid  sid  mid 	  
	 *************************************************************/
	//require("./inc/init.php");	
	//require("./inc/functions.php");
	//数据库连接
	define("TOTAL_DOWNLOADS",50);

	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	//查询  am_download_history 表，获得 TOTAL_KEYWORDS hotwords
	$mid = intval(__getPost('mid'));
	
		
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)){//版权
		////20110301 判断该渠道是否允许显示VISIBLE=2的应用
		if(in_array($_SESSION["channel"], $xxxxnopermitChannelid)) $sql_postfix = ' and app_visible =1 ';
		else $sql_postfix = ' ';
		$sql = "select distinct a.app_id,a.end_time from am_download_history a where a.mid <>".$mid." and a.status = 1 and a.type <> 'import' and a.app_id in(select app_id from am_appinfo where copyright=1 $sql_postfix) order by a.end_time desc limit ".TOTAL_DOWNLOADS;
	}else{
		////20110301 判断该渠道是否允许显示VISIBLE=2的应用
		if(in_array($_SESSION["channel"], $xxxxnopermitChannelid)) $sql_postfix = " and a.app_id in(select app_id from am_appinfo where and app_visible =1)";
		else $sql_postfix = ' ';
		$sql = "select distinct a.app_id,a.end_time from am_download_history a  where a.mid <>".$mid." and a.status = 1 and a.type <> 'import'  $sql_postfix order by a.end_time desc limit ".TOTAL_DOWNLOADS;
	}
	$result = mysql_query($sql, $conn);
	$i = 0;
	while ($row = mysql_fetch_assoc($result))
	{
		$aDownAppid[$i] = $row['app_id'];
		//format
		$datetime1 = $row['end_time'];
		$year=((int)substr($datetime1,0,4));//取得年份 
		$month=((int)substr($datetime1,5,2));//取得月份 
		$day=((int)substr($datetime1,8,2));//取得几号 
		$hour=((int)substr($datetime1,11,2));//时
		$min=((int)substr($datetime1,14,2));//分 
		$second=((int)substr($datetime1,17,2));//秒
		$downtime = mktime($hour,$min,$second,$month,$day,$year);
		$aTime[$i] = $downtime;
		$i++;
	}
	 /*****  前两个应用的名字  bluesie  2010-8-18  ********/
	 $aAppNameT = array($aDownAppid);

	 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".$aDownAppid[0];
	 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
	 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
	 $aAppNameT[0] = $rowAppNameT1['app_name'];
	 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".$aDownAppid[1];
	 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
	 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
	 $aAppNameT[1] = $rowAppNameT2['app_name'];
	 /*****  前两个应用的名字  bluesie  2010-8-18  THE END********/
	 $count = count($aDownAppid);
	 $downList[0]['name'] = "其他用户下载";
	 $downList[0]['icon'] = "";
	 //$downList[1]['icon'] = "";
	 $downList[0]['parent'] = "";
	 $downList[0]['sig'] = "9994";
	 $downList[0]['appcnt'] = $count;
	 $downList[0]['is_chart'] = true;
	 $downList[0]['desc'] = "";
	 $downList[0]['update_interval'] = 60;
	 $downList[0]['time_applist'] = join(",",$aDownAppid);
	 $downList[0]['time_appname'] = join(",",$aAppNameT);
	 $downList[0]['downtime_list'] = join(",",$aTime);
	 //print_r($downList);exit();
?>
