<?php
	/************************************************************
     *   猜我喜欢 9995  get_commend_list.php
     *   Author:  liu jingqi	  
	 *	 paras  proto  uid  sid  mid
	 *************************************************************/
	$total = 50;


	//查询  am_download_history表，获得此mid下载的应用个数
	//$mid = 127;//126(5) 127(41)
	$mid = intval(__getPost('mid'));
	///recommend start
	$aAppid = array();
	////判断是否云库渠道
	if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
		$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['new']) , true);
		if($arrYunlibNew['yunlib_9995']){
			$aAppid = $arrYunlibNew['yunlib_9995'];
		}
	}

	$content = @file_get_contents("/config/xxxx_commend_9995.conf");
	preg_match_all ("/value=\"(.*?)\"/i",    $content,    $out);
	for($oi=0; $oi<count($out[1]); $oi++){
		$gaAppid[] = $out[1][$oi];
	}
	$id_list = @implode("," , $gaAppid);
		
	$conn = connect_comm_db();
	
	$sql = "select create_time from am_terminal where mid=".$mid;
	$res = mysql_query($sql);
	$result = mysql_fetch_assoc($res);
	$new_old = ($result['create_time']>=date("Y-m-d 00:00:00"))?1:0;

	//判断是否传值班screen,sdk---2010.11.11
	if(!($screen=__getPost('screen_size')) || !($sdk=__getPost('sdk'))){
			
			$res = mysql_query("select screen_size,sdk from am_terminal_info where mid=". $mid, $conn);		
			if(mysql_num_rows($res) == 0){
				$screen = "480x800";
				$sdk = 4;
			}else{
				$result = mysql_fetch_assoc($res);
				$screen	= $result['screen_size'];
				$sdk	= $result['sdk'];
			}
	}
	mysql_close($conn);
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	$downAppId = $aAppNameT = array();
	$sql = "select appid from am_app_unsort where mid=".$mid." and appid>0";
	$result = mysql_query($sql, $conn);
	while ($row = @mysql_fetch_assoc($result)){
		$downAppId[] = $row['appid'];
	}
	
	///recommend end
	$arrTestDeviceId = array();
	$sqlD = "select app_device_type_id from am_device_type where screen='$screen' and sdk_version<='$sdk' and app_device_type_id>0";	
	////memcache缓存
	$keyName = $AM_MEMCACHE["am_device_type"][0].$screen.$sdk;
	if($AM_MEMCACHE["am_device_type"][2]){
		if(!($arrTestDeviceId = $memobj->get($keyName))){
			$rs = mysql_query($sqlD, $conn);
			while ($row = mysql_fetch_assoc($rs)) {
				$arrTestDeviceId[] = $row['app_device_type_id'];
			}
			$memobj->set($keyName ,$arrTestDeviceId , 0 ,$AM_MEMCACHE["am_device_type"][1]);
		}
	}else{

		$rs = mysql_query($sqlD, $conn);
		while ($row = mysql_fetch_assoc($rs)) {
			$arrTestDeviceId[] = $row['app_device_type_id'];
		}
	}
	if(count($arrTestDeviceId)<1) $arrTestDeviceId[] = 0;
	///判断正版 ,如果是TCL，如果是指定机型，则获取TCL推荐内容
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)){
		/*mysql_close($conn);
		$conn = connect_comm_db();
		$sql = "select mid from am_terminal_info where mid=". $mid." and (product like '%A890%' or product like '%A906%')";
		$rsAppTime = mysql_query($sql, $conn);
		$rowTcl = mysql_fetch_assoc($rsAppTime);
		mysql_close($conn);
		$conn = connect_db();*/
		////TCL机型数组
		
		//if($rowTcl['mid']){
//			$sqlAllApp = "SELECT distinct am_app_topic.app_id FROM am_app_topic JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id
//							WHERE am_appinfo.app_visible =1 and am_appinfo.copyright=1 		
//							AND topic_id = 103";
			$sqlAllApp = "SELECT distinct am_app_topic.app_id FROM am_app_topic JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1  		
							AND topic_id = 103";
			$sqlAllApp .= " order by am_app_topic.order,am_appinfo.app_update_date desc,am_appinfo.app_id desc limit $total";
			$rsAppTime = mysql_query($sqlAllApp, $conn);
			 $arrAppidTime = array();
			 while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
					$arrAppidTime[] = $rowAppTime['app_id'];
			 }
			 $strCommend = @join(",",$arrAppidTime);
			 $commendList[0]['name'] = "应用推荐";
			 $commendList[0]['icon'] = $AM_CATE_ICON_LOC_PREFIX."chart/320x480/chart_gameH0.png";
			 $commendList[0]['parent'] = "";
			 $commendList[0]['sig'] = "9995";
			 $commendList[0]['appcnt'] = count($arrAppidTime);
			 $commendList[0]['is_chart'] = true;
			 $commendList[0]['desc'] = "";
			 $commendList[0]['update_interval'] = 24*60*60;
			 $commendList[0]['time_applist'] = $strCommend;
			 $commendList[0]['time_appname'] = "";
			 return;
		//}else	$sqlTcl = " and a.copyright=1 ";
	}
	
	
	$sql = "select a.app_id from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where a.app_visible=1 and a.app_id in (". $id_list .") ".$sqlTcl." group by a.app_id";

	$rs = mysql_query($sql, $conn);
	$applist = array();	
	while ($row = mysql_fetch_assoc($rs)) {
		$aAppid[] = $row["app_id"];
	}

	///recommend end
	
	if( !$new_old ){ //老用户
		$downAppId = $downAppId2 = @array_unique($downAppId);
		$downAppId2 = array_merge($downAppId2,$aAppid);
		$downAppId2 = @array_unique($downAppId2);
		$sql = "select channel_id from am_love_channel where mid=".$mid;
		$result = mysql_query($sql, $conn);
		if(mysql_num_rows($result) == 0){ ////没有设置自选择分类			
			if(count($aAppid) < $total){
				////同分类的应用
				$sql = "select DISTINCT cate_id from am_app_category where app_id in(".join(",",$downAppId).") and cate_id not in(1,2,3,4,5,6)";
				$res2 = mysql_query($sql, $conn); 
				$aAppidTest = array();
				while ($row2 = mysql_fetch_assoc($res2)){
					$aAppidTest[] = $row2['cate_id'];
				}
				if($aAppidTest){
					if($sqlTcl)
						$sqlTopic = "select  DISTINCT  a.app_id from am_app_category a left join am_app_device_type b on a.app_id=b.app_id where a.cate_id in(".join(",",$aAppidTest).")   and a.app_id not in (".join(",",$downAppId2).") and  a.app_id in(select app_id from am_appinfo where copyright=1 and app_visible=1) group by a.app_id order by a.app_id desc limit ".($total-count($aAppid));
					else
						$sqlTopic = "select  DISTINCT  a.app_id from am_app_category a left join am_app_device_type b on a.app_id=b.app_id where a.cate_id in(".join(",",$aAppidTest).")   and a.app_id not in (".join(",",$downAppId2).") and  a.app_id in(select app_id from am_appinfo where app_visible=1) group by a.app_id order by a.app_id desc limit ".($total-count($aAppid));
					$rs = mysql_query($sqlTopic, $conn);
					while ($row = mysql_fetch_assoc($rs)){
						$aAppid[] = $row['app_id'];
						$downAppId2[] = $row['app_id'];
					}
				}
			}
		}else{
			$row = mysql_fetch_assoc($result);
			$strChannel = $row['channel_id'];
			////最近两天更新应用，并且属于自选择分类
			$downAppId2 = $downAppId;
			$t = strtotime(date("Y-m-d 00:00:00"))-3600*24*2;
			$sqlApp = "select  DISTINCT  a.app_id from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where  ";
			if($downAppId2) $sqlApp .= " and a.app_id not in (".join(",",$downAppId2).") ";
			$sqlApp .= " and a.app_visible=1  and UNIX_TIMESTAMP(app_update_date)>$t";
			$sqlApp .= $sqlTcl." group by a.app_id ";
			$rs = mysql_query($sqlApp, $conn);
			while ($row2 = mysql_fetch_assoc($rs)){
				$aAppidTest[] = $row2['app_id'];
			}
			////过滤非自选择分类的应用
			$sqlApp = "select app_id from am_app_category where cate_id in(".$strChannel.") and app_id in(".join(",",$aAppidTest).") limit 50";
			$rs = mysql_query($sqlApp, $conn);
			while ($row2 = mysql_fetch_assoc($rs)){
				$aAppid[] = $row2['app_id'];
				$downAppId2[] = $row2['app_id'];
			}
			
			if(count($aAppid) < $total){
				////最热应用,并且属于自选择分类
				$sqlApp = "select app_id from am_app_device_type where substr( `app_id` , 1, 1 ) =6 group by a.app_id ";
				$rs = mysql_query($sqlApp, $conn);
				$aAppidTest = array();
				while ($row2 = mysql_fetch_assoc($rs)){
					$aAppidTest[] = $row2['app_id'];
				}
				
				$sqlApp = "select  DISTINCT  a.app_id from am_appinfo a left join am_app_category b on a.app_id=b.app_id where b.cate_id in(".$strChannel.") ";
				if($downAppId2) $sqlApp .= " and a.app_id not in (".join(",",$downAppId2).") ";
				$sqlApp .= $sqlTcl." and a.app_visible=1 and a.`app_id` in(".join(",",$aAppidTest).") order by a.app_downloads desc limit ".($total-count($aAppid));
				$rs = mysql_query($sqlApp, $conn);
				while ($row2 = mysql_fetch_assoc($rs)){
					$aAppid[] = $row2['app_id'];
					$downAppId2[] = $row2['app_id'];
				}
			}
			
			if(count($aAppid) < $total){
				////同分类的应用
				$sql = "select DISTINCT cate_id from am_app_category where app_id in(".join(",",$downAppId).") and cate_id not in(1,2,3,4,5,6)";
				$res2 = mysql_query($sql, $conn); 
				$aAppidTest = array();
				while ($row2 = mysql_fetch_assoc($res2)){
					$aAppidTest[] = $row2['cate_id'];
				}
				if($aAppidTest){
					if($sqlTcl)
						$sqlTopic = "select  DISTINCT  a.app_id from am_app_category a left join am_app_device_type b on a.app_id=b.app_id where a.cate_id in(".join(",",$aAppidTest).")   and a.app_id not in (".join(",",$downAppId2).") and  a.app_id in(select app_id from am_appinfo where copyright=1 and app_visible=1) group by a.app_id order by a.app_id desc limit ".($total-count($aAppid));
					else
						$sqlTopic = "select  DISTINCT  a.app_id from am_app_category a left join am_app_device_type b on a.app_id=b.app_id where a.cate_id in(".join(",",$aAppidTest).")   and a.app_id not in (".join(",",$downAppId2).") and  a.app_id in(select app_id from am_appinfo where app_visible=1) group by a.app_id  order by a.app_id desc  limit ".($total-count($aAppid));
					$rs = mysql_query($sqlTopic, $conn);			
					while ($row = mysql_fetch_assoc($rs)){
						$aAppid[] = $row['app_id'];
						$downAppId2[] = $row['app_id'];
					}
				}
			}
			
		}
		//zxg,20120315 debug result always=40
		if(count($aAppid) < $total){
			////再次推送最近更新zxg			
			$sqlApp = "select app_id from am_appinfo where app_visible=1 and relationid>0 order by `app_update_date` desc limit ".$total;
			$rs = mysql_query($sqlApp, $conn);
			while ($row = mysql_fetch_assoc($rs)){
				$aAppid[] = $row['app_id'];
			}				
			$aAppid = @array_unique($aAppid);

		}		
		if(count($aAppid) > $total) $aAppid = array_slice($aAppid, 0, $total); 		
		$aAppNameT = array();
		 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".$aAppid[0];
		 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
		 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
		 $aAppNameT[0] = $rowAppNameT1['app_name'];
		 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".$aAppid[1];
		 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
		 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
		 $aAppNameT[1] = $rowAppNameT2['app_name'];
		
		$aAppid = array_unique($aAppid);
		$strCommend = @join(",",$aAppid);
		$commendList[0]['name'] = "应用推荐";
		 $commendList[0]['icon'] = $AM_CATE_ICON_LOC_PREFIX."chart/320x480/chart_gameH0.png";
		 //$commendList[1]['icon'] = "";
		 $commendList[0]['parent'] = "";
		 $commendList[0]['sig'] = "9995";
		 $commendList[0]['appcnt'] = count($aAppid);
		 $commendList[0]['is_chart'] = true;
		 $commendList[0]['desc'] = "";
		 $commendList[0]['update_interval'] = 24*60*60;
		 $commendList[0]['time_applist'] = $strCommend;
		 $commendList[0]['time_appname'] = implode(",",$aAppNameT);
	}else{ //新用户
		$downAppId = $downAppId2 = @array_unique($downAppId);
		$downAppId2 = array_merge($downAppId2,$aAppid);
		$downAppId2 = @array_unique($downAppId2);
		////memcache
		$keyName = $AM_MEMCACHE["am_appinfo"][0].$screen.$sdk."_".$xxxx_iscopyright."_".$_SESSION["channel"];
		$isflagexists = 0; $commendList = array();
		if($AM_MEMCACHE["am_appinfo"][2]){
			if(!($commendList = $memobj->get($keyName))) $isflagexists = 2;
		}else $isflagexists = 1;
		if($isflagexists){			
			////优先推送"必备"的应用
			$sqlApp = "select a.app_id from am_app_topic a left join am_app_device_type b on a.app_id=b.app_id where a.topic_id =34 ";
			if($sqlTcl) $sqlApp .= " and a.app_id in(select app_id from am_appinfo where copyright=1 and app_visible=1) ";
			else $sqlApp .= " and a.app_id in(select app_id from am_appinfo where app_visible=1) ";
			$sqlApp .= " and a.app_id not in (".join(",",$downAppId2).") and substr( a.`app_id` , 1, 1 ) =6 group by a.app_id limit ".($total-count($aAppid));
			$rs = mysql_query($sqlApp, $conn);
			while ($row = mysql_fetch_assoc($rs)){
				$aAppid[] = $row['app_id'];
			}

			$copyright = ($xxxx_iscopyright)?1:0;
			if(count($aAppid) < $total){
				////其次推送应用下载排行榜中总排行				
				$sqlApp = "select `all` from am_rank where type='soft' and screen='$screen' and `sdk`<='$sdk' and `copyright`=".$copyright." order by `sdk` desc ";
				$rs = mysql_query($sqlApp, $conn);
				$row = mysql_fetch_assoc($rs);
				if($row){
					$arrSoftAll = explode("," , $row['all']);
					$aAppid = array_merge($aAppid,$arrSoftAll);
					$aAppid = @array_unique($aAppid);
				}
			}
			if(count($aAppid) < $total){
				////再次推送游戏下载排行榜中总排行			
				$sqlApp = "select `all` from am_rank where type='game' and screen='$screen' and `sdk`<='$sdk' and `copyright`=".$copyright." order by `sdk` desc ";
				$rs = mysql_query($sqlApp, $conn);
				$row = mysql_fetch_assoc($rs);
				if($row){
					$arrSoftAll = explode("," , $row['all']);
					$aAppid = array_merge($aAppid,$arrSoftAll);
					$aAppid = @array_unique($aAppid);
				}
			}
			//zxg,20120315 debug result always=40
			if(count($aAppid) < $total){
				////再次推送最近更新zxg			
				$sqlApp = "select app_id from am_appinfo where app_visible=1 and relationid>0 order by `app_update_date` desc limit ".$total;
				$rs = mysql_query($sqlApp, $conn);
				while ($row = mysql_fetch_assoc($rs)){
					$aAppid[] = $row['app_id'];
				}				
				$aAppid = @array_unique($aAppid);
				
			}			
			if(count($aAppid) > $total) $aAppid = array_slice($aAppid, 0, $total); 
			$aAppNameT = array();
			$arrayappid=$aAppid;
			$aAppid=array();
			foreach ($arrayappid as $key => $value) {
				if ($value!='6049480'){
					$aAppid[]=$value;
				}
			}
			 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".$aAppid[0];
			 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
			 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
			 $aAppNameT[0] = $rowAppNameT1['app_name'];
			 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".$aAppid[1];
			 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
			 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
			 $aAppNameT[1] = $rowAppNameT2['app_name'];
		 
			$strCommend = @join(",",$aAppid);
			$commendList[0]['name'] = "应用推荐";
			 $commendList[0]['icon'] = $AM_CATE_ICON_LOC_PREFIX."chart/320x480/chart_gameH0.png";
			 //$commendList[1]['icon'] = "";
			 $commendList[0]['parent'] = "";
			 $commendList[0]['sig'] = "9995";
			 $commendList[0]['appcnt'] = count($aAppid);
			 $commendList[0]['is_chart'] = true;
			 $commendList[0]['desc'] = "";
			 $commendList[0]['update_interval'] = 24*60*60;
			 $commendList[0]['time_applist'] = $strCommend;
			 $commendList[0]['time_appname'] = join(",",$aAppNameT);
			 
			 if($isflagexists == 2)$memobj->set($keyName ,$commendList , 0 ,$AM_MEMCACHE["am_appinfo"][1]);
		}
		
	}
?>
