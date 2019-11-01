<?php
	/************************************************************
	 *   Interface 28 for Bajiev2
     *   获取分类列表 get_cate_list.php
	 *
     *   Author: 
	 *	 Update by  bluesie   2010-06-11 Fit to xxxx_ServiceAPI_v2.14 
	 *	 paras  proto  uid  sid  mid 	  
	 *************************************************************/
	require("./inc/init.php");	
    	if($AM_CURRENT_REQUEST["PROTO"] != 6){
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
	
	////如果是TCL接口，判断正版
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)) {
		$sqlTcl = "";// and am_appinfo.copyright=1 ";
		$xxxx_iscopyright = 1;
	}
	if($xxxxChannelidTopic93[$_SESSION["channel"]]) $xxxxTopicName = $xxxxChannelidTopic93[$_SESSION["channel"]]."推荐";
	else $xxxxTopicName = "";
	
	// complex sql to get all categories 
	$sql =  "SELECT am_category.*, app_count ";
	$sql .= "FROM am_category ";
	$sql .= "LEFT JOIN (";
	$sql .= 	"SELECT cate_id, count( am_app_category.app_id ) AS app_count ";
	$sql .=		"FROM am_app_category ";
	$sql .=		"JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id ";
	$sql .=		"WHERE am_appinfo.app_visible =1 ".$sqlTcl;
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
	$sql .= 	"WHERE am_appinfo.app_visible = 1 ".$sqlTcl;
	$sql .= 	" GROUP BY topic_id ";
	$sql .= ") AS stats_table ON am_topic.id = stats_table.topic_id ";
	$sql .= "WHERE am_topic.visible = 1 ".$sqlor;
	//$sql .= "AND FIND_IN_SET( '". $current_channel_id ."', visible_channels ) >0 ";
	$sql .= "ORDER BY `order` ,am_topic.id desc";
		
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
			log_message($sql, 'S');
			echo error2json("S002");
			die;
		}
		while ($row = mysql_fetch_assoc($rs1)) {
			////2011.01.07新加
			if($row["id"] == 93) 	$row["name"] = ($xxxxTopicName)?$xxxxTopicName:$row["name"];
			$cate_info = array(
				'name' => $row["name"],
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
		$sql = "SELECT icon FROM ". $table ." WHERE ". $field ."=". $value['sig'] ." AND screen='$screen'";

		$rs = mysql_query($sql, $conn);

		if ($rs != FALSE) {
			$row = mysql_fetch_assoc($rs);

			$data[$key]['icon'] = $AM_CATE_ICON_LOC_PREFIX. $row['icon'];
		}
		//查询此分类下所有appid
		//$sqlApp = "SELECT app_id FROM am_app_category  WHERE cate_id=". $value['sig'];
		if($data[$key]['is_chart'] == 1)
		{
			$sqlApp = "SELECT am_app_topic.app_id
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
			$sqlApp = "SELECT am_app_category.app_id
						FROM am_app_category
						JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
						WHERE am_appinfo.app_visible =1 $sqlTcl
						AND cate_id =".$value['sig'];
			 $order_time = " order by am_app_category.order,am_appinfo.app_update_date desc ";
			 $order_down = " order by am_app_category.order,am_appinfo.app_downloads desc";
			 $sqlAppByTime = $sqlApp.$order_time;
			 $sqlAppByDown = $sqlApp.$order_down;
		}
		//echo $sqlApp."<br />";
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
	
	//print_r($arrCateId);exit();
	//每周下载排行
	//获取 download_history表中的appid
	$lastweek = date('Y-m-d' , strtotime('-1 week'));
	$aDownAppId = array();
	////memcache缓存
	$keyName = $AM_MEMCACHE["am_download_history"][0].$screen.$sdk."_".$lastweek;
	$isflagexists = 0; 
	if($AM_MEMCACHE["am_download_history"][2]){
		if(!($aDownAppId = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sqlDown = "select distinct app_id from am_download_history where create_time >='".$lastweek."'";
		$rsDown = mysql_query($sqlDown, $conn);
		if ($rsDown !== FALSE) {
			while ($rowDown = mysql_fetch_assoc($rsDown)){
				$aDownAppId[] = $rowDown['app_id'];
			}
			if($isflagexists == 2)$memobj->set($keyName ,$aDownAppId , 0 ,$AM_MEMCACHE["am_download_history"][1]);
		}
	}
	$total = count($aDownAppId);
	
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
		$arrAppidTime = array();		
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
		 //time
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
		 
		 $allAppList[0]['name'] = "所有应用";
		 if($current_device_id == 1)
			$allAppList[0]['icon'] = $AM_CATE_ICON_LOC_PREFIX."cate/480x800/cate_softwareW0.png";
		 else
			$allAppList[0]['icon'] = $AM_CATE_ICON_LOC_PREFIX."cate/320x480/cate_softwareH0.png";
		 $allAppList[0]['parent'] = $parentid;
		 $allAppList[0]['sig'] = "9998";
		 $allAppList[0]['appcnt'] = $count;
		 $allAppList[0]['is_chart'] = false;
		 $allAppList[0]['time_applist'] = $strTimeList;
		 $allAppList[0]['down_applist'] = $strDownList;
		 //查询每周下载应用排行
		 if($total >= 20){
			$keyName = $AM_MEMCACHE["am_download_history"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
			$isflagexists = 0; 
			if($AM_MEMCACHE["am_download_history"][2]){
				if(!($arrWAppidDown = $memobj->get($keyName))) $isflagexists = 2;
			}else $isflagexists = 1;
			if($isflagexists){
				//获取本周应用 列表  cate_id = 5
				$sqlAllApp = "SELECT distinct am_app_category.app_id
								FROM am_app_category
								JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
								WHERE am_appinfo.app_visible =1 $sqlTcl
								AND am_appinfo.app_id
								IN (".join(",",$aDownAppId).")
								AND cate_id in(".join(",",$aCateId).")";
				 $order_down = " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit 0,20";
				 $sqlAllAppByDown = $sqlAllApp.$order_down;
				 //time
				 $rsAppDown = mysql_query($sqlAllAppByDown, $conn);
				 if ($rsAppDown !== FALSE){
					while ($rowAppDown = mysql_fetch_assoc($rsAppDown)){
						$arrWAppidDown[] = $rowAppDown['app_id'];
					}
					if($isflagexists == 2)$memobj->set($keyName ,$arrWAppidDown , 0 ,$AM_MEMCACHE["am_download_history"][1]);
				 }
			}

			 //$arrWAppidDown = array_unique($arrWAppidDown);
			 $strDownList = @join(",",$arrWAppidDown);
			 $count = count($arrWAppidDown);
			 $weekAppList[0]['name'] = "本周应用排行"; 
			 if($current_device_id == 1)
				$weekAppList[0]['icon'] = $AM_CATE_ICON_LOC_PREFIX."chart/480x800/chart_softwareW1.png";
			else
				$weekAppList[0]['icon'] = $AM_CATE_ICON_LOC_PREFIX."chart/320x480/chart_softwareH1.png";

			 //$weekAppList[0]['icon'] = "";
			 $weekAppList[0]['parent'] = "31";
			 $weekAppList[0]['sig'] = "9997";
			 $weekAppList[0]['appcnt'] = $count;
			 $weekAppList[0]['is_chart'] = true;
			 $weekAppList[0]['time_applist'] = $strDownList;
		}
	}
	//获取所有游戏列表
	//
	//获取全部应用/游戏  列表  cate_id = 5 6
	$parentid = 6;
	$sqlCateId = "select id from am_category where parent_id = ".$parentid;
	$rsCateId = mysql_query($sqlCateId, $conn);
	$aCateId = array();
	if ($rsCateId !== FALSE) {
		while ($rowCateId = mysql_fetch_assoc($rsCateId)){
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
		 //time
		 
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
		 $allAppList[1]['time_applist'] = $strTimeListGame;
		 $allAppList[1]['down_applist'] = $strDownListGame;

		 //查询每周下载游戏排行
		 if($total >= 20)
		 {
		 	$keyName = $AM_MEMCACHE["am_download_history"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
			$isflagexists = 0; $arrWAppidDown2 = array();
			if($AM_MEMCACHE["am_download_history"][2]){
				if(!($arrWAppidDown2 = $memobj->get($keyName))) $isflagexists = 2;
			}else $isflagexists = 1;
			if($isflagexists){
				
				//获取本周应用 列表  cate_id = 5
				$sqlAllApp = "SELECT distinct am_app_category.app_id
								FROM am_app_category
								JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
								WHERE am_appinfo.app_visible =1 $sqlTcl
								AND am_appinfo.app_id
								IN (".join(",",$aDownAppId).")
								AND cate_id in(".join(",",$aCateId).")";
				 $order_down = " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit 0,20";
				 $sqlAllAppByDown = $sqlAllApp.$order_down;
				 //time
				 $rsAppDown = mysql_query($sqlAllAppByDown, $conn);
				 if ($rsAppDown !== FALSE){
					while ($rowAppDown = mysql_fetch_assoc($rsAppDown)){
						$arrWAppidDown2[] = $rowAppDown['app_id'];
					}
					if($isflagexists == 2)$memobj->set($keyName ,$arrWAppidDown2 , 0 ,$AM_MEMCACHE["am_download_history"][1]);
				 }
			}
			
			 $strDownList2 = @join(",",$arrWAppidDown2);
			 $count = count($arrWAppidDown);
			 $weekAppList[1]['name'] = "本周游戏排行";
			 if($current_device_id == 1)
				$weekAppList[1]['icon'] = $AM_CATE_ICON_LOC_PREFIX."chart/480x800/chart_gameW0.png";
			 else
				$weekAppList[1]['icon'] = $AM_CATE_ICON_LOC_PREFIX."chart/320x480/chart_gameH0.png";

			 //$weekAppList[1]['icon'] = "";
			 $weekAppList[1]['parent'] = "31";
			 $weekAppList[1]['sig'] = "9996";
			 $weekAppList[1]['appcnt'] = $count;
			 $weekAppList[1]['is_chart'] = true;
			 $weekAppList[1]['time_applist'] = $strDownList2;
		}
		else
		{
			//
		}
	}
	//print_r($weekAppList);exit();
	//echo $strTimeList."<br />".$strDownList;exit();
	//去除 count=0 的记录
	$j = 0;
	for($i=0;$i<count($data);$i++)
	{
		if($data[$i][appcnt] > 0 || $data[$i]['sig'] == $xxxxcopyrightchekTopicid[0])
		{
			$aResult[$j][name] = $data[$i][name];
			$aResult[$j][icon] = $data[$i][icon];
			$aResult[$j][parent] = $data[$i][parent];
			$aResult[$j][sig] = $data[$i][sig];
			$aResult[$j][appcnt] = $data[$i][appcnt];
			$aResult[$j][is_chart] = $data[$i][is_chart];
			$aResult[$j][time_applist] = $data[$i][time]['applist'];
			if(!$data[$i][is_chart]) $aResult[$j][down_applist] = $data[$i][down]['applist'];
			$j++;
		}
	}
	/*
	 * 添加排序功能  分类按照  am_app_category.order, am_appinfo.app_update_date 顺序返回  
	 *    增加一条记录    按照 am_appinfo.app_downloads  ,appid 顺序会不同，并标记此记录是按照 哪种排序方式 ['sorting']
	 *
	 * 2010-8-12  update  add commend_list
	 */
	// require('get_commend_list.php');
	 
	//合并两个数组
	if($allAppList)
		$aResult = array_merge($allAppList,$aResult);
	if($weekAppList)
		$aResult = array_merge($weekAppList,$aResult);
	if($commendList)
		$aResult = array_merge($commendList,$aResult);
	//print_r($aResult);exit();

	if(count($aResult)){
		$json_arr = array(
			"proto" => 6,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => $aResult,		
		);

	}else{

		$json_arr = array(
			"proto" => 6,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			'list'  => '[]',		
		);

	}

	echo array2json($json_arr); 
	@mysql_free_result($rs);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
