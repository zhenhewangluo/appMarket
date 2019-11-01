<?php
	/************************************************************
	 *   Interface 31
     *   获取分类列表 get_cate_list.php
	 *
     *   Author: 
	 *	 Update by  bluesie   2010-06-11 Fit to xxxx_ServiceAPI_v2.14 
	 *	 paras  proto  uid  sid  mid 	  
	 *************************************************************/
	require("./inc/init.php");	
    	if($AM_CURRENT_REQUEST["PROTO"] != 31){
		echo error2json("E002");
		die;
	}

	$current_channel_id = $AM_CURRENT_REQUEST["CHANNEL"];
	$current_device = $AM_CURRENT_REQUEST["MODEL"];
	
	if($current_device == 0){
		echo error2json("E195");
		die;
	}
		//判断是否传值班screen,sdk---2010.11.11
	if(!($screen=__getPost('screen_size')) || !($sdk=__getPost('sdk'))){
			$conn = connect_comm_db();
			$res = mysql_query("select screen_size,sdk from am_terminal_info where mid=". $mid, $conn);		
			if(mysql_num_rows($res) == 0){
				$screen = "480x800";
				$sdk = 4;
			}else{
				$result = mysql_fetch_assoc($res);
				$screen	= $result['screen_size'];
				$sdk	= $result['sdk'];
			}
			mysql_close($conn);
	}
	
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	//查询2010.11.11 xxxxxxxxx
	$arrTestDeviceId = array(); $isflagexists = 0;
	////memcache缓存
	$keyName = $AM_MEMCACHE["am_device_type"][0].$screen.$sdk;
	if($AM_MEMCACHE["am_device_type"][2]){
		if(!($arrTestDeviceId = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sqlD = "select app_device_type_id from am_device_type where screen='$screen' and sdk_version<='$sdk' and app_device_type_id>0";
		$rs = mysql_query($sqlD, $conn);
		while ($row = mysql_fetch_assoc($rs)) {
			$arrTestDeviceId[] = $row['app_device_type_id'];
		}
		if($isflagexists == 2) $memobj->set($keyName ,$arrTestDeviceId , 0 ,$AM_MEMCACHE["am_device_type"][1]);
	}
	if(count($arrTestDeviceId)<1) $arrTestDeviceId[] = 0;
	

	///判断正版
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)) {
		$sqlTcl ="";// " and am_appinfo.copyright=1 ";
		//$xxxx_iscopyright =1;
		$xxxx_iscopyright =0;
	}
        $xxxx_iscopyright =0;
        
	if($xxxxChannelidTopic93[$_SESSION["channel"]]) $xxxxTopicName = $xxxxChannelidTopic93[$_SESSION["channel"]]."推荐";
	else $xxxxTopicName = "";
	
	// complex sql to get all categories 
	$sql =  "SELECT am_category.*, app_count ";
	$sql .= "FROM am_category ";
	$sql .= "LEFT JOIN (";
	$sql .= 	"SELECT cate_id, count( am_app_category.app_id ) AS app_count ";
	$sql .=		"FROM am_app_category ";
	$sql .=		"JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id ";
	$sql .=		"WHERE am_appinfo.app_visible =1 ". $sqlTcl;	
	$sql .=		" GROUP BY cate_id ";
	$sql .=	") AS stats_table ON am_category.id = stats_table.cate_id ";
	$sql .=	"WHERE am_category.visible =1 ";
	$sql .=	"AND parent_id !=0 ";
	$sql .=	"ORDER BY parent_id, `order`";

	$data = array(); $isflagexists = 0;
	////memcache缓存
	$keyName = $AM_MEMCACHE["am_category"][0].$screen.$sdk."_".$xxxx_iscopyright;
	if($AM_MEMCACHE["am_category"][2]){
		if(!($data = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$rs = mysql_query($sql, $conn);
		if($rs === FALSE){
			echo error2json("S002");	die;
		}	
		while ($row = mysql_fetch_assoc($rs)) {
			$cate_info = array(
				'name' => $row["name"],
				'desc' => $row["description"],
				'icon' => $AM_CATE_ICON_LOC_PREFIX . $row["icon"], // TODO
				'parent'   => $row["parent_id"],
				'sig'  => $row["id"],
				'appcnt' => is_numeric($row["app_count"])?$row["app_count"]:0, 
				'is_chart' => AM_REQUEST_FAIL,
			);
			$data[] = $cate_info;
		}
		if($isflagexists == 2)$memobj->set($keyName ,$data , 0 ,$AM_MEMCACHE["am_category"][1]);
	}
	@mysql_free_result($rs);

	// complex sql to get all topics 
	if($_SESSION["channel"] == $xxxxcopyrightchekChannelid[0]) $sqlor = " or am_topic.id = '".$xxxxcopyrightchekTopicid[0]."' ";
	else $sqlor = "";
	$sql =  "SELECT am_topic.*, app_count ";
	$sql .= "FROM am_topic ";
	$sql .= "LEFT JOIN ( ";
	$sql .= 	"SELECT topic_id, count( am_app_topic.app_id ) AS app_count ";
	$sql .= 	"FROM am_app_topic ";
	$sql .= 	"JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id ";
	$sql .= 	"WHERE am_appinfo.app_visible = 1 ". $sqlTcl;
	$sql .= 	" GROUP BY topic_id ";
	$sql .= ") AS stats_table ON am_topic.id = stats_table.topic_id ";
	$sql .= "WHERE am_topic.visible = 1 ".$sqlor;
	//$sql .= "AND FIND_IN_SET( '". $current_channel_id ."', visible_channels ) >0 ";
	$sql .= "ORDER BY `order`,am_topic.id desc";

	////memcache缓存
	$keyName = $AM_MEMCACHE["am_topic"][0].$screen.$sdk."_".$xxxx_iscopyright."_".$xxxxTopicName;
	$isflagexists = 0; $data3 = array();
	if($AM_MEMCACHE["am_topic"][2]){
		if(!($data3 = $memobj->get($keyName))) $isflagexists = 2;
		else $data = array_merge($data , $data3);
	}else $isflagexists = 1;
	if($isflagexists){
		$data2 = array();
		$rs1 = mysql_query($sql, $conn);
		if($rs1 === FALSE){
			echo error2json("S002");
			die;
		}
		while ($row = mysql_fetch_assoc($rs1)) {
			////2011.01.07新加
			if($row["id"] == 93) 	$row["name"] = ($xxxxTopicName)?$xxxxTopicName:$row["name"];
			
			$cate_info = array(
				'name' => $row["name"],
				'desc' => $row["description"],
				'icon' => $AM_CATE_ICON_LOC_PREFIX . $row["icon"], // TODO: add icon support
				'parent'   => $row["parent_id"],
				'sig'  => $row["id"],
				'appcnt' => is_numeric($row["app_count"])?$row["app_count"]:0, 
				'is_chart' => AM_REQUEST_SUCCESS,
			);
			$data[] = $cate_info;
			$data2[] = $cate_info;
		}
		if($isflagexists == 2)$memobj->set($keyName ,$data2 , 0 ,$AM_MEMCACHE["am_topic"][1]);
	}
	@mysql_free_result($rs1);
	// query topic icon
	foreach($data as $key=>$value) {
		$table = $data[$key]['is_chart'] == 1 ? "am_topic_device_type" : "am_category_device_type";
		$field = $data[$key]['is_chart'] == 1 ? "topic_id" : "category_id";
		$sql = "SELECT icon FROM ". $table ." WHERE ". $field ."=". $value['sig'] ;

		$rs = mysql_query($sql, $conn);

		if ($rs != FALSE) {
			$row = mysql_fetch_assoc($rs);

			$data[$key]['icon'] = $AM_CATE_ICON_LOC_PREFIX. $row['icon'];
		}
		$data[$key]['update_interval'] = 24*60*60;
		//查询此分类下所有appid
		//$sqlApp = "SELECT app_id FROM am_app_category  WHERE cate_id=". $value['sig'];
		if($data[$key]['is_chart'] == 1)
		{
			$sqlApp = "SELECT distinct am_app_topic.app_id
						FROM am_app_topic
						JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id
						WHERE am_appinfo.app_visible =1 $sqlTcl	
						AND topic_id =".$value['sig'];
			 $order_time = " order by am_app_topic.order,am_appinfo.app_update_date desc ";
			 $order_down = " order by am_app_topic.order,am_appinfo.app_downloads desc";
			 $sqlAppByTime = $sqlApp.$order_time;
			 $sqlAppByDown = $sqlApp.$order_down;
		}
		else
		{
			$sqlApp = "SELECT distinct am_app_category.app_id
						FROM am_app_category
						JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
						WHERE am_appinfo.app_visible =1 $sqlTcl
						AND cate_id =".$value['sig'];
			 $order_time = " order by am_app_category.order,am_appinfo.app_update_date desc ";
			 $order_down = " order by am_app_category.order,am_appinfo.app_downloads desc";
			 $sqlAppByTime = $sqlApp.$order_time;
			 $sqlAppByDown = $sqlApp.$order_down;
			 //最新应用更新时间间隔 1 小时
			 if($data[$key]['sig'] == 3)
				 $data[$key]['update_interval'] = 4*60*60;
		}
		
		////memcache缓存
		$arrAppidTime = array();
		$keyName = $AM_MEMCACHE["am_category"][0].$screen.$sdk."_".$xxxx_iscopyright."_".$value['sig'];
		$isflagexists = 0; 
		if($AM_MEMCACHE["am_category"][2]){
			if(!($arrAppidTime = $memobj->get($keyName))) $isflagexists = 2;
		}else $isflagexists = 1;
		if($isflagexists){
			$rsAppTime = mysql_query($sqlAppByTime, $conn);		
			if ($rsAppTime !== FALSE) {
				while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
					$arrAppidTime[] = $rowAppTime['app_id'];
				}
				
				if($isflagexists == 2){
						$timeout2 = ($value['sig'] == 3)?3600*2:$AM_MEMCACHE["am_category"][1];
						$memobj->set($keyName ,$arrAppidTime , 0 ,$timeout2);
				}
			}
		}
		////判断是否云库渠道
		if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
			if($data[$key]['is_chart'] == 1){ //专辑
				$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['topic']) , true);
				if($arrYunlibNew['yunlib_'.$value['sig']]){
					$aAppid = $arrYunlibNew['yunlib_'.$value['sig']];
				}
				if($aAppid){
					$arrAppidTime = array_unique(array_merge($aAppid , $arrAppidTime));
				}
			}else{ ////分类
				if($value['sig'] == 3){
					$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['new']) , true);
					if($arrYunlibNew['yunlib_3']){
						$aAppid = $arrYunlibNew['yunlib_3'];
					}
					if($aAppid){
						$arrAppidTime = array_unique(array_merge($aAppid , $arrAppidTime));
					}
				}else{
					$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['cate']) , true);
					if($arrYunlibNew['yunlib_'.$value['sig']]){
						$aAppid = $arrYunlibNew['yunlib_'.$value['sig']];
					}
					if($aAppid){
						$arrAppidTime = array_unique(array_merge($aAppid , $arrAppidTime));
					}
				}
			}
			$data[$key]['appcnt'] = count($arrAppidTime);
		}
		//
		$arrAppidDown = array();
		$keyName = $AM_MEMCACHE["am_topic"][0].$screen.$sdk."_".$xxxx_iscopyright."_".$value['sig'];
		$isflagexists = 0; 
		if($AM_MEMCACHE["am_topic"][2]){
			if(!($arrAppidDown = $memobj->get($keyName))) $isflagexists = 2;
		}else $isflagexists = 1;
		if($isflagexists){
			$rsAppDown = mysql_query($sqlAppByDown, $conn);			
			if ($rsAppDown !== FALSE) {
				while ($rowAppDown = mysql_fetch_assoc($rsAppDown)){
					$arrAppidDown[] = $rowAppDown['app_id'];
				}
				if($isflagexists == 2)$memobj->set($keyName ,$arrAppidDown , 0 ,$AM_MEMCACHE["am_topic"][1]);
			}
		}
		$data[$key]['time']['applist'] = @join(",",$arrAppidTime);
		$data[$key]['down']['applist'] = @join(",",$arrAppidDown);
		$arrCateId[] = $data[$key]['sig'];
	}

	//每周下载排行
	//获取 download_history表中的appid
	$lastweek = date('Y-m-d' , strtotime('-1 week'));
	
	//获取全部应用/游戏  列表  cate_id = 5 6
	$parentid = 5;
	$sqlCateId = "select id from am_category where parent_id = ".$parentid;
	$rsCateId = mysql_query($sqlCateId, $conn);
	$aCateId = array();
	if ($rsCateId !== FALSE) 
	{
		while ($rowCateId = mysql_fetch_assoc($rsCateId))
		{
			$aCateId[] = $rowCateId['id'];
		}
	}
	//$data[$key]['time']['applist'] = @join(",",$arrAppidTime);
	if($aCateId)
	{
		//查询改分类下所有applist downlist
		$sqlAllApp = "SELECT distinct am_app_category.app_id
						FROM am_app_category
						JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
						WHERE am_appinfo.app_visible =1 $sqlTcl
						AND cate_id in(".join(",",$aCateId).")";
		 $order_time = " order by am_app_category.order,am_appinfo.app_update_date desc ";
		 $order_down = " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc ";
		 $sqlAllAppByTime = $sqlAllApp.$order_time;
		 $sqlAllAppByDown = $sqlAllApp.$order_down;
		 
		  ////memcache缓存
		$keyName = $AM_MEMCACHE["am_category"][0].$screen.$sdk."_".md5(implode(",",$aCateId));
		$isflagexists = 0; 
		if($AM_MEMCACHE["am_category"][2]){
			if(!($arrAppidTime = $memobj->get($keyName))) $isflagexists = 2;
		}else $isflagexists = 1;
		if($isflagexists){
			$rsAppTime = mysql_query($sqlAllAppByTime, $conn);		 
			 if ($rsAppDown !== FALSE){
				while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
					$arrAppidTime[] = $rowAppTime['app_id'];
				}
				$arrAppidTime = array_unique($arrAppidTime);
				if($isflagexists == 2)$memobj->set($keyName ,$arrAppidTime , 0 ,$AM_MEMCACHE["am_category"][1]);
			 }
		}
		 ////memcache缓存 down
		 $arrAppidDown = array();
		$keyName = $AM_MEMCACHE["am_topic"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
		$isflagexists = 0; 
		if($AM_MEMCACHE["am_topic"][2]){
			if(!($arrAppidDown = $memobj->get($keyName))) $isflagexists = 2;
		}else $isflagexists = 1;
		if($isflagexists){
			 $rsAppDown = mysql_query($sqlAllAppByDown, $conn);			 
			 if ($rsAppDown !== FALSE){
				while ($rowAppDown = mysql_fetch_assoc($rsAppDown)){
					$arrAppidDown[] = $rowAppDown['app_id'];
				}
			 }
			 $arrAppidDown = array_unique($arrAppidDown);
			 if($isflagexists == 2)$memobj->set($keyName ,$arrAppidDown , 0 ,$AM_MEMCACHE["am_topic"][1]);
		}
		
		 $count = count($arrAppidTime);
		 $strTimeList = @join(",",$arrAppidTime);
		 $strDownList = @join(",",$arrAppidDown);
		 //
		 /*****  前两个应用的名字  bluesie  2010-8-17  ********/
		 $aAppNameT = array();
		 $aAppNameD = array();

		 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".$arrAppidTime[0];
		 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
		 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
	     $aAppNameT[0] = $rowAppNameT1['app_name'];
		 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".$arrAppidTime[1];
		 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
		 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
	     $aAppNameT[1] = $rowAppNameT2['app_name'];

		 $sqlAppNameD1 = "select app_name from am_appinfo where app_id = ".$arrAppidDown[0];
		 $rsAppNameD1 = mysql_query($sqlAppNameD1, $conn);
		 $rowAppNameD1 = mysql_fetch_assoc($rsAppNameD1);
         $aAppNameD[0] = $rowAppNameD1['app_name'];
		 $sqlAppNameD2 = "select app_name from am_appinfo where app_id = ".$arrAppidDown[1];
		 $rsAppNameD2 = mysql_query($sqlAppNameD2, $conn);
		 $rowAppNameD2 = mysql_fetch_assoc($rsAppNameD2);
         $aAppNameD[1] = $rowAppNameD2['app_name'];
		 /*****  前两个应用的名字  bluesie  2010-8-17  THE END********/

		 $allAppList[0]['name'] = "所有应用";
		 if($current_device_id == 1)
			$allAppList[0]['icon'] = $AM_CATE_ICON_LOC_PREFIX."cate/480x800/cate_softwareW0.png";
		 else
			$allAppList[0]['icon'] = $AM_CATE_ICON_LOC_PREFIX."cate/320x480/cate_softwareH0.png";
		 $allAppList[0]['parent'] = $parentid;
		 $allAppList[0]['sig'] = "9998";
		 $allAppList[0]['appcnt'] = $count;
		 $allAppList[0]['is_chart'] = false;
		 $allAppList[0]['desc'] = $xxxxArrCategoryType["xxxx_9998"]["desc"];
		 $allAppList[0]['update_interval'] = 24*60*60;
		 $allAppList[0]['time_applist'] = $strTimeList;
		 $allAppList[0]['time_appname'] = join(",",$aAppNameT);
		 $allAppList[0]['down_applist'] = $strDownList;
		 $allAppList[0]['down_appname'] = join(",",$aAppNameD);
	}
	//获取所有游戏列表
	//
	//获取全部应用/游戏  列表  cate_id = 5 6
	$parentid = 6;
	$sqlCateId = "select id from am_category where parent_id = ".$parentid;
	$rsCateId = mysql_query($sqlCateId, $conn);
	$aCateId = array();
	if ($rsCateId !== FALSE) 
	{
		while ($rowCateId = mysql_fetch_assoc($rsCateId))
		{
			$aCateId[] = $rowCateId['id'];
		}
	}
	if($aCateId)
	{
		//查询改分类下所有applist downlist
		$sqlAllGame = "SELECT distinct am_app_category.app_id
						FROM am_app_category
						JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
						WHERE am_appinfo.app_visible =1 $sqlTcl
						AND cate_id in(".join(",",$aCateId).")";
		 $order_time = " order by am_app_category.order,am_appinfo.app_update_date desc ";
		 $order_down = " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc ";
		 $sqlAllGameByTime = $sqlAllGame.$order_time;
		 $sqlAllGameByDown = $sqlAllGame.$order_down;
		 
		 ////memcache缓存 time
		 $arrGameidTime = array();
		$keyName = $AM_MEMCACHE["am_category"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
		$isflagexists = 0; 
		if($AM_MEMCACHE["am_category"][2]){
			if(!($arrGameidTime = $memobj->get($keyName))) $isflagexists = 2;
		}else $isflagexists = 1;
		if($isflagexists){
			 $rsGameTime = mysql_query($sqlAllGameByTime, $conn);
			 if ($rsGameDown !== FALSE){
				while ($rowGameTime = mysql_fetch_assoc($rsGameTime)){
					$arrGameidTime[] = $rowGameTime['app_id'];
				}
				if($isflagexists == 2)$memobj->set($keyName ,$arrGameidTime , 0 ,$AM_MEMCACHE["am_category"][1]);
			 }
		}

		  ////memcache缓存 down
		 $arrGameidDown = array();
		$keyName = $AM_MEMCACHE["am_topic"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
		$isflagexists = 0; 
		if($AM_MEMCACHE["am_topic"][2]){
			if(!($arrGameidDown = $memobj->get($keyName))) $isflagexists = 2;
		}else $isflagexists = 1;
		if($isflagexists){
			 $rsGameDown = mysql_query($sqlAllGameByDown, $conn);
			 if ($rsGameDown !== FALSE){
				while ($rowGameDown = mysql_fetch_assoc($rsGameDown)){
					$arrGameidDown[] = $rowGameDown['app_id'];
				}
				if($isflagexists == 2)$memobj->set($keyName ,$arrGameidDown , 0 ,$AM_MEMCACHE["am_topic"][1]);
			 }			 
		}
		 /*****  前两个应用的名字  bluesie  2010-8-17  ********/
		 $aAppNameT = array();
		 $aAppNameD = array();

		 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".$arrGameidTime[0];
		 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
		 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
	     $aAppNameT[0] = $rowAppNameT1['app_name'];
		 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".$arrGameidTime[1];
		 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
		 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
	     $aAppNameT[1] = $rowAppNameT2['app_name'];

		 $sqlAppNameD1 = "select app_name from am_appinfo where app_id = ".$arrGameidDown[0];
		 $rsAppNameD1 = mysql_query($sqlAppNameD1, $conn);
		 $rowAppNameD1 = mysql_fetch_assoc($rsAppNameD1);
         $aAppNameD[0] = $rowAppNameD1['app_name'];
		 $sqlAppNameD2 = "select app_name from am_appinfo where app_id = ".$arrGameidDown[1];
		 $rsAppNameD2 = mysql_query($sqlAppNameD2, $conn);
		 $rowAppNameD2 = mysql_fetch_assoc($rsAppNameD2);
         $aAppNameD[1] = $rowAppNameD2['app_name'];

		 /*****  前两个应用的名字  bluesie  2010-8-17  THE END********/

		 $count = count($arrGameidTime);
		 $strTimeListGame = @join(",",$arrGameidTime);
		 $strDownListGame = @join(",",$arrGameidDown);
		 //
		 $allAppList[1]['name'] = "所有游戏";
		 if($current_device_id == 1)
			$allAppList[1]['icon'] = $AM_CATE_ICON_LOC_PREFIX."cate/480x800/cate_gameW0.png";
		 else
			$allAppList[1]['icon'] = $AM_CATE_ICON_LOC_PREFIX."cate/320x480/cate_gameH0.png";
		// $allAppList[1]['icon'] = "";
		 $allAppList[1]['parent'] = $parentid;
		 $allAppList[1]['sig'] = "9999";
		 $allAppList[1]['appcnt'] = $count;
		 $allAppList[1]['is_chart'] = false;
		 $allAppList[1]['desc'] = $xxxxArrCategoryType["xxxx_9999"]["desc"];
		 $allAppList[1]['update_interval'] = 24*60*60;
		 $allAppList[1]['time_applist'] = $strTimeListGame;
		 $allAppList[1]['time_appname'] = @join(",",$aAppNameT);
		 $allAppList[1]['down_applist'] = $strDownListGame;
		 $allAppList[1]['down_appname'] = @join(",",$aAppNameD);		 
	}
	//print_r($weekAppList);exit();
	//去除 count=0 的记录  substr($aa,0,15)
	$j = 0;
	for($i=0;$i<count($data);$i++)
	{
		if($data[$i][appcnt] > 0)
		{
			/*****  前两个应用的名字  bluesie  2010-8-17  ********/
			 $aAppNameT = array();
			 $aAppNameD = array();

			 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".substr($data[$i][time]['applist'],0,7);
			 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
			 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
			 $aAppNameT[0] = $rowAppNameT1['app_name'];
			 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".substr($data[$i][time]['applist'],8,7);
			 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
			 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
			 $aAppNameT[1] = $rowAppNameT2['app_name'];


			 /*****  前两个应用的名字  bluesie  2010-8-17  THE END********/
			$aResult[$j][name] = $data[$i][name];
			 $aResult[$j][desc] = $data[$i][desc];
			$aResult[$j][icon] = $data[$i][icon];
			$aResult[$j][parent] = $data[$i][parent];
			$aResult[$j][sig] = $data[$i][sig];
			$aResult[$j][appcnt] = $data[$i][appcnt];
			$aResult[$j][is_chart] = $data[$i][is_chart];
			$aResult[$j][update_interval] = $data[$i][update_interval];
			$aResult[$j][time_applist] = $data[$i][time]['applist'];
			$aResult[$j][time_appname] = join(",",$aAppNameT);
			if(!$data[$i][is_chart]) 
			{
				$aResult[$j][down_applist] = $data[$i][down]['applist'];
				$sqlAppNameD1 = "select app_name from am_appinfo where app_id = ".substr($data[$i][down]['applist'],0,7);
				$rsAppNameD1 = mysql_query($sqlAppNameD1, $conn);
				$rowAppNameD1 = mysql_fetch_assoc($rsAppNameD1);
				$aAppNameD[0] = $rowAppNameD1['app_name'];
				$sqlAppNameD2 = "select app_name from am_appinfo where app_id = ".substr($data[$i][down]['applist'],8,7);
				$rsAppNameD2 = mysql_query($sqlAppNameD2, $conn);
				$rowAppNameD2 = mysql_fetch_assoc($rsAppNameD2);
				$aAppNameD[1] = $rowAppNameD2['app_name'];
				$aResult[$j][down_appname] = join(",",$aAppNameD);
			}
			$j++;
		}elseif($data[$i]['sig'] == $xxxxcopyrightchekTopicid[0]){
			$aResult[$j][name] = $data[$i][name];
			$aResult[$j][desc] = $data[$i][desc];
			$aResult[$j][icon] = $data[$i][icon];
			$aResult[$j][parent] = $data[$i][parent];
			$aResult[$j][sig] = $data[$i][sig];
			$aResult[$j][appcnt] = $data[$i][appcnt];
			$aResult[$j][is_chart] = $data[$i][is_chart];
			$aResult[$j][update_interval] = $data[$i][update_interval];
			$aResult[$j][time_applist] = $data[$i][time]['applist'];
			$aResult[$j][time_appname] = join(",",$aAppNameT);
			$j++;
		}
	}
	
	
	////软件，游戏，全部排行榜 2010.12.07日 sig:10001,10002,10003
	$arrRank = array();
	$copyright = ($xxxx_iscopyright)?1:0;
	$keyName = $AM_MEMCACHE["am_rank"][0].$screen.$sdk."_".$xxxx_iscopyright;
	$isflagexists = 0; $arrWAppidDown2 = array();
	if($AM_MEMCACHE["am_rank"][2]){
		if(!($arrRank = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sqlrank = "select * from am_rank where `screen`='$screen' and `sdk`<='$sdk'  order by `sdk` desc limit 3";
		$rsrank = mysql_query($sqlrank, $conn);
		$numrank = 0;
		while ($row = mysql_fetch_assoc($rsrank)){
			if($row['type'] == "soft"){
				$arrRank[$numrank]['sig'] = 10001;
				$arrRank[$numrank]['icon'] = $AM_CATE_ICON_LOC_PREFIX."top/soft_top.png";
				$arrRank[$numrank]['name'] = "应用类排行";
				$arrRank[$numrank]['desc'] = "没有最好，只有更好，这次谁能鹤立鸡群？";
				
			}elseif($row['type'] == "game"){
				$arrRank[$numrank]['sig'] = 10002;
				$arrRank[$numrank]['icon'] = $AM_CATE_ICON_LOC_PREFIX."top/game_top.png";
				$arrRank[$numrank]['name'] = "游戏类排行";
				$arrRank[$numrank]['desc'] = "小鸟僵尸，群雄争霸，今日谁又独孤求败？";
				
			}elseif($row['type'] == "all"){
				$arrRank[$numrank]['sig'] = 10003;
				$arrRank[$numrank]['icon'] = $AM_CATE_ICON_LOC_PREFIX."top/all_top.png";
				$arrRank[$numrank]['name'] = "下载总排行";	
				$arrRank[$numrank]['desc'] = "风云变幻，哪款应用笑傲江湖，独领风骚？";		
			}else continue;
			
			$arrRank[$numrank]['week'] = $row['week'];
			$arrRank[$numrank]['month'] = $row['month'];
			$arrRank[$numrank]['all'] = $row['all'];
			$numrank ++;			
		}
		////缺省的排行榜,自定义的写死在配置文件中，以后会更改
		require("./inc/rank.inc.php");	
		if($copyright) $strcopyright = " and `copyright`=".$copyright;
		for($nRank=0; $nRank<count($arrRankDefaultRow); $nRank++){
			$arrRank[$numrank] = $arrRankDefaultRow[$nRank];			
			$sql2 = "select app_id from am_rank_appinfo where app_id in(".$arrRank[$numrank]['week'].") and `screen`='$screen' and `sdk`<='$sdk'  group by app_id";
			$rsrank2 = mysql_query($sql2, $conn);
			$arr_a = explode("," , $arrRank[$numrank]['week']);
			$arr_b = array();
			while ($row2 = mysql_fetch_assoc($rsrank2)){
				$arr_b[] = $row2['app_id'];
			}
			$arrRank[$numrank]['week'] = $arrRank[$numrank]['month'] = $arrRank[$numrank]['all'] = implode("," , array_intersect($arr_a , $arr_b));
			$numrank++;
		}
		if($isflagexists == 2)$memobj->set($keyName ,$arrRank , 0 ,$AM_MEMCACHE["am_rank"][1]);
	}
	
	////判断是否云库渠道
	if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
		for($k=0; $k<count($arrRank); $k++){
			$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['rank_'.$arrRank[$k]['sig']]) , true);
			$aAppWeek = ($arrYunlibNew['yunlib_week'])?$arrYunlibNew['yunlib_week']:array();
			$aAppMonth = ($arrYunlibNew['yunlib_month'])?$arrYunlibNew['yunlib_month']:array();
			$aAppAll = ($arrYunlibNew['yunlib_all'])?$arrYunlibNew['yunlib_all']:array();
			$arr_applist = array_unique(array_merge($aAppWeek , @explode("," , $arrRank[$k]['week'])));
			$arrRank[$k]['week'] = @implode("," , array_slice($arr_applist , 0 ,20));
			
			$arr_applist = array_unique(array_merge($aAppMonth , @explode("," , $arrRank[$k]['month'])));
			$arrRank[$k]['month'] = @implode("," , array_slice($arr_applist , 0 ,20));
			
			$arr_applist = array_unique(array_merge($aAppAll , @explode("," , $arrRank[$k]['all'])));
			$arrRank[$k]['all'] = @implode("," , array_slice($arr_applist , 0 ,20));
		}
	}
	/*
	 * 添加排序功能  分类按照  am_app_category.order, am_appinfo.app_update_date 顺序返回  
	 *    增加一条记录    按照 am_appinfo.app_downloads  ,appid 顺序会不同，并标记此记录是按照 哪种排序方式 ['sorting']
	 *
	 * 2010-8-12  update  add commend_list
	 */
	 require('get_commend_list.php'); //9995 猜我喜欢
	 //增加 返回 100个 热词
	 require('get_hotwords_all.php'); 
	 //增加 返回 别人下载 30 个应用
	 require('get_other_downloads.php'); //9994 下载动态
	 
	//合并两个数组
	if($allAppList)
		$aResult = array_merge($allAppList,$aResult);
	if($weekAppList)
		$aResult = array_merge($weekAppList,$aResult);
	if($commendList){
		$commendList[0]['desc'] = "";
		$aResult = array_merge($commendList,$aResult);
	}
	if($downList)
		$aResult = array_merge($downList,$aResult);
	//print_r($aResult);exit();
	
	
	
	
	
	////2010.10.21是否对用户进行召回
	$mid =  isset($_POST['mid']) ? intval(__getPost('mid')) : 0;
	$uid =  isset($_POST['uid']) ? intval(__getPost('uid')) : 0;
	$isSend = "false";
	$callback_time = 0;
	////第一种召回，次日召回
	@mysql_close($conn);
	$conn = connect_comm_db();
	$sql3 = "select create_time from am_terminal where mid='$mid' and status=1";
	$rsAppNameD3 = mysql_query($sql3, $conn);
	$rowAppNameD3 = mysql_fetch_assoc($rsAppNameD3);
	if(substr($rowAppNameD3['create_time'],0,10) == date("Y-m-d",time()-3600*24)){
		$t1 = strtotime(date("Y-m-d 00:00:00",time()-3600*24));
		$t2 = time();
		@mysql_close($conn);
		$conn = connect_db();
		$sql3 = "select id from am_view_history where mid='$mid' and viewtime>".$t1." and viewtime<=".$t2;
		$rsAppNameD3 = mysql_query($sql3, $conn);
		if(mysql_num_rows($rsAppNameD3) == 0){
			$isSend = "true";
			$callback_time = 18;
			$sql4 = "insert into am_send_log set uid='$uid',mid='$mid',type=1,dateline='".time()."'";
			mysql_query($sql4, $conn);
		}
	}
	
	////第二种召回方式,超过一周以上未登录用户信息召回
	if($isSend  != "true"){
		$today = getdate();
		if($today['wday'] == 3){
			@mysql_close($conn);
			$conn = connect_db();
			$sql4 = "select id from am_send_log where mid='$mid' and type=2";
			$rsAppNameD4 = mysql_query($sql4, $conn);
			if(mysql_num_rows($rsAppNameD4) == 0){ ////之前没有发送过此类召回
				$t1 = strtotime(date("Y-m-d 00:00:00" , time()-3600*24*7));
				$sql5 = "select id from am_view_history where mid='$mid' and viewtime>".$t1;
				$rsAppNameD5 = mysql_query($sql5, $conn);
				if(mysql_num_rows($rsAppNameD5) == 0){
					$isSend = "true";
					$callback_time = 8;
					$sql4 = "insert into am_send_log set uid='$uid',mid='$mid',type=2,dateline='".time()."'";
					mysql_query($sql4, $conn);
				}
			}
		}
	}
		
//	print_r($aResult);print_r($arrRank);die;
	
	if(count($aResult)){

		$json_arr = array(
			"proto" => 31,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => $aResult,
			'hotwords'=>$strHot,
			'callback'=>$isSend,
			'callback_time'=>$callback_time,
			'rank' => $arrRank,
		);

	}else{

		$json_arr = array(
			"proto" => 31,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => '[]',
			'hotwords'=>$strHot,
			'callback'=>$isSend,
			'callback_time'=>$callback_time,
			'rank' => $arrRank,
		);
	}

	echo array2json($json_arr); 
	@mysql_free_result($rs);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

