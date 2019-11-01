<?php
	/************************************************************
	 *   Interface 33
     *   获取某个分类下 的应用详细信息 get_newest_applist.php
	 *
     *   Author: bluesie
	 *   Create Time: 2010-09-07
	 *	 Update Records:
	 *	 param  cateid
	 *************************************************************/
	 var_dump('123');
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");

    if($AM_CURRENT_REQUEST["PROTO"] != 33){
		echo error2json("E002");
		die;
	}

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

	//分类
	$cate_id = intval(__getPost('cateid'));
	if($cate_id == '9994')
	{
		require('get_other_downloads.php');
		$count = $downList[0]['appcnt'];
		$is_chart = true;
		$strTimeList = $downList[0]['time_applist'];
		$strDownTime = $downList[0]['downtime_list'];
	}elseif($cate_id == '9995'){
		require('get_commend_list.php');
		$count = $commendList[0]['appcnt'];
		$is_chart = true;
		$strTimeList = $commendList[0]['time_applist'];
		$strDownTime = $commendList[0]['downtime_list'];
		$aAppNameT = explode("," , $commendList[0]['time_appname']);
	}elseif($cate_id == '9996'){
		require('get_onecate_app_speicel.php');
		$is_chart = true;
	}elseif($cate_id == '9997'){
		require('get_onecate_app_speicel.php');
		$is_chart = true;
	}elseif($cate_id == '9998'){
		require('get_onecate_app_speicel.php');
		$is_chart = false;
	}elseif($cate_id == '9999'){
		require('get_onecate_app_speicel.php');
		$is_chart = false;
	}else{
		$sql = "select count(id) from am_category where id=".$cate_id;
		$rs = mysql_query($sql, $conn);
		if(mysql_result($rs,0)){
			//category
			$is_chart = false;
			$sqlAllApp = "SELECT distinct am_app_category.app_id
							FROM am_app_category
							JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1 $sqlTcl
							AND cate_id =".$cate_id;
			 $order_time = " order by am_app_category.order,am_appinfo.app_update_date desc ";
			 $order_down = " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc ";
			 $sqlAllAppByTime = $sqlAllApp.$order_time;
			 $sqlAllAppByDown = $sqlAllApp.$order_down;
			 //time
			 //echo $sqlAllAppByTime;
			 $keyName = $AM_MEMCACHE["am_category"][0].$screen.$sdk."_".$xxxx_iscopyright."_".$cate_id;
			$isflagexists = 0;
			if($AM_MEMCACHE["am_category"][2]){
				if(!($arrAppidTime = $memobj->get($keyName))) $isflagexists = 2;
			}else $isflagexists = 1;
			if($isflagexists){
				 $rsAppTime = mysql_query($sqlAllAppByTime, $conn);
				 $arrAppidTime = array();
				 if ($rsAppDown !== FALSE){
					while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
						$arrAppidTime[] = $rowAppTime['app_id'];
					}
					if($isflagexists == 2){
						$timeout2 = ($cate_id == 3)?3600*2:$AM_MEMCACHE["am_category"][1];
						$memobj->set($keyName ,$arrAppidTime , 0 ,$timeout2);
					}
				 }
			}
			////判断是否云库渠道
			if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
				if($cate_id == 3){
					$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['new']) , true);
					if($arrYunlibNew['yunlib_3']){
						$aAppid = $arrYunlibNew['yunlib_3'];
					}
					if($aAppid){
						$arrAppidTime = array_unique(array_merge($aAppid , $arrAppidTime));
					}
				}else{
					$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['cate']) , true);
					if($arrYunlibNew['yunlib_'.$cate_id]){
						$aAppid = $arrYunlibNew['yunlib_'.$cate_id];
					}
					if($aAppid){
						$arrAppidTime = array_unique(array_merge($aAppid , $arrAppidTime));
					}
				}
			}

			 //down
			 $keyName = $AM_MEMCACHE["am_topic"][0].$screen.$sdk."_".$xxxx_iscopyright."_".$cate_id;
			$isflagexists = 0;
			if($AM_MEMCACHE["am_topic"][2]){
				if(!($arrAppidDown = $memobj->get($keyName))) $isflagexists = 2;
			}else $isflagexists = 1;
			if($isflagexists){
				 $rsAppDown = mysql_query($sqlAllAppByDown, $conn);
				 $arrAppidDown = array();
				 if ($rsAppDown !== FALSE){
					while ($rowAppDown = mysql_fetch_assoc($rsAppDown)){
						$arrAppidDown[] = $rowAppDown['app_id'];
					}
					if($isflagexists == 2)$memobj->set($keyName ,$arrAppidDown , 0 ,$AM_MEMCACHE["am_topic"][1]);
				 }
			}
			 $count = count($arrAppidTime);
			 $strTimeList = @join(",",$arrAppidTime);
			 $strDownList = @join(",",$arrAppidDown);
			 /*****  前两个应用的名字  ************/
			 $aAppNameT = array();

			 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".$arrAppidTime[0];
			 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
			 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
			 $aAppNameT[0] = $rowAppNameT1['app_name'];
			 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".$arrAppidTime[1];
			 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
			 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
			 $aAppNameT[1] = $rowAppNameT2['app_name'];
			 /*****  前两个应用的名字  THE END ********/
		}else{
			$sql = "select count(id) from am_topic where id=".$cate_id;
			$rs = mysql_query($sql, $conn);
			if(mysql_result($rs,0))
			{
				//专题
				$is_chart = true;
				$sqlApp = "SELECT distinct am_app_topic.app_id
							FROM am_app_topic
							JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1 $sqlTcl
							AND topic_id =".$cate_id;
				 $order_time = " order by am_app_topic.order,am_appinfo.app_update_date desc ";
				 $order_down = " order by am_app_topic.order,am_appinfo.app_downloads desc";
				 $sqlAllAppByTime = $sqlApp.$order_time;
				 $sqlAllAppByDown = $sqlApp.$order_down;
				 //time
				 $keyName = $AM_MEMCACHE["am_category"][0].$screen.$sdk."_".$xxxx_iscopyright."_".$cate_id;
				$isflagexists = 0;
				if($AM_MEMCACHE["am_category"][2]){
					if(!($arrAppidTime = $memobj->get($keyName))) $isflagexists = 2;
				}else $isflagexists = 1;
				if($isflagexists){
					 $rsAppTime = mysql_query($sqlAllAppByTime, $conn);
					 $arrAppidTime = array();
					 if ($rsAppDown !== FALSE){
						while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
							$arrAppidTime[] = $rowAppTime['app_id'];
						}
						if($isflagexists == 2)$memobj->set($keyName ,$arrAppidTime , 0 ,$AM_MEMCACHE["am_category"][1]);
					 }
				}
				////判断是否云库渠道
				if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
					$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['topic']) , true);
					if($arrYunlibNew['yunlib_'.$cate_id]){
						$aAppid = $arrYunlibNew['yunlib_'.$cate_id];
					}
					if($aAppid){
						$arrAppidTime = array_unique(array_merge($aAppid , $arrAppidTime));
					}
				}
				 //down
				  $keyName = $AM_MEMCACHE["am_topic"][0].$screen.$sdk."_".$xxxx_iscopyright."_".$cate_id;
				$isflagexists = 0;
				if($AM_MEMCACHE["am_topic"][2]){
					if(!($arrAppidDown = $memobj->get($keyName))) $isflagexists = 2;
				}else $isflagexists = 1;
				if($isflagexists){
					 $rsAppDown = mysql_query($sqlAllAppByDown, $conn);
					 $arrAppidDown = array();
					 if ($rsAppDown !== FALSE){
						while ($rowAppDown = mysql_fetch_assoc($rsAppDown)){
							$arrAppidDown[] = $rowAppDown['app_id'];
						}
						if($isflagexists == 2)$memobj->set($keyName ,$arrAppidDown , 0 ,$AM_MEMCACHE["am_topic"][1]);
					 }
				}
				 $count = count($arrAppidTime);
				 $strTimeList = @join(",",$arrAppidTime);
				 $strDownList = @join(",",$arrAppidDown);
				 /*****  前两个应用的名字  ************/
				 $aAppNameT = array();

				 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".$arrAppidTime[0];
				 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
				 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
				 $aAppNameT[0] = $rowAppNameT1['app_name'];
				 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".$arrAppidTime[1];
				 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
				 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
				 $aAppNameT[1] = $rowAppNameT2['app_name'];
				 /*****  前两个应用的名字  THE END ********/
			}
			else
			{
				echo error2json("E111");
				die;
			}
		}
	}

	//echo $strTimeList,"<br />",$strDownList;exit();
	@mysql_free_result($rs);
	$response = array(
		"proto" => 33,
		"reqsuccess" => AM_REQUEST_SUCCESS,
		"sig"        => $cate_id,
		"appcnt"     => $count,
		"is_chart"   => $is_chart,
		"time_applist"=>$strTimeList,
		"time_appname"=>join(",",$aAppNameT),
		"down_applist"=>$strDownList,
		"downtime_list"=>$strDownTime,
			);

	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

