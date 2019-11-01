<?php
	/************************************************************
	 *   Interface 32
     *   取最新分类 cate=3 的应用详细信息 get_newest_applist.php
	 *   
     *   Author: bluesie
	 *   Create Time: 2010-09-07
	 *	 Update Records:
	 *	 param  appid		  
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 32){
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
	$cate_id = 3;//最新

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
	
	$aId = array();
	$keyName = $AM_MEMCACHE["am_category"][0].$screen.$sdk."_".$xxxx_iscopyright."_".$cate_id;
	$isflagexists = 0; 
	if($AM_MEMCACHE["am_category"][2]){
		if(!($aId = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){			
		$sql = "SELECT distinct am_app_category.app_id
						FROM am_app_category
						JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
						WHERE am_appinfo.app_visible =1 $sqlTcl
						AND cate_id =".$cate_id;
		 $sql .= " order by am_app_category.order,am_appinfo.app_update_date desc ";
	//echo $sql;exit();
		$rs = mysql_query($sql, $conn);
		if($rs === FALSE){
			echo error2json("S002");
			die;
		}
		while ($row = mysql_fetch_assoc($rs)){
			$aId[] = $row['app_id'];
		}
		if($isflagexists == 2){
				$timeout2 = ($cate_id == 3)?3600*2:$AM_MEMCACHE["am_category"][1];
				$memobj->set($keyName ,$aId , 0 ,$timeout2);
		}
	}
	if(!$aId){
		echo error2json("E116");
	}else{
		//print_r($aId);//exit();
		$appcnt = count($aId);
		$strTimeApp = join(",",$aId);
		$appid = intval(__getPost('appid'));
		//$appid = "60002262";
		if(in_array($appid,$aId))
		{
			$key = array_search($appid, $aId);
			if(!$key)
			{
				echo error2json("E116");
				die;
			}
			elseif($key <= 9)
			{
				$keyR = $key;
			}
			else
			{
				$keyR = 10;
			}
		}
		else
		{
			$keyR = 10;
		}
		$aIdTmp = array_chunk($aId, $keyR);
		//print_r($aIdTmp);
		$aIdR = $aIdTmp[0];
		//print_r($aIdR);exit();
		$strappid = join(",",$aIdR);

		$sql = "select am_appinfo.*, b.icon,b.logo,b.pkg_name,b.screenshots,b.app_size from am_appinfo am_appinfo left join am_app_device_type b on am_appinfo.app_id=b.app_id where am_appinfo.app_id in (".$strappid.") ".$sqlTcl." group by am_appinfo.app_id ";
		$rs = mysql_query($sql, $conn);
		while ($row = mysql_fetch_assoc($rs)) {
			list($s1 , $s2) = explode("|",$row['screenshots']);
			if($_SESSION["channel"] == 10028){ ////T-PARK渠道
				$s1 = str_replace("/" , "_" , $s1);
				$s2 = str_replace("/" , "_" , $s2);
				$arr_s[0] = ($s1)?$AM_APP_LOGO_LOC_PREFIX."nosign/".$s1:"";
				$arr_s[1] = ($s2)?$AM_APP_LOGO_LOC_PREFIX."nosign/".$s2:"";
			}else{
				$arr_s[0] = ($s1)?$AM_APP_LOGO_LOC_PREFIX.$s1:"";
				$arr_s[1] = ($s2)?$AM_APP_LOGO_LOC_PREFIX.$s2:"";
			}
			$screenshots = implode("," , $arr_s);
			////评论总分不能大于总次数*5 by xxxxxxxxx 2011-03-31
			checkScoreAndNum($row["total_score"] , $row["total_num"]);
	
                                         $appdesc= str_replace("<br>","\n",$row["app_desc"]);

			$applist[] = array(
				"appid"      => $row["app_id"], 
				"screenshot" => $AM_APP_LOGO_LOC_PREFIX  . $row["logo"], 
				"icon"       => $AM_CATE_ICON_LOC_PREFIX . $row["icon"], 
				"name"       => $row["app_name"], 
				"author"     => $row["author_name"], 
				"short_desc" => deleteHtmlTags($row["app_slogan"]), 
				//"desc"       => $row["app_desc"], 
				"desc"       => deleteHtmlTags($appdesc), 
				"rating_up"  => $row["app_rate_up"], 
				"rating_down"=> $row["app_rate_down"], 
				//"rating"     => $row["app_rating"], 
				"download_cnt"=>$row["app_downloads"]*AM_DOWNLOAD_CNT, 
				"price"      => $row["app_price"] * AM_EXCHANGE_RATE, 
				"version"    => $row["version"], 
				"appver"     => $row["app_version"], 
				"infover"    => $row["infover"],
				"size"       => $row["app_size"],
				"pkg_name"   => $row["pkg_name"],
				"is_english" =>$row["is_english"],
				"total_score" =>$row["total_score"],
				"total_num" =>$row["total_num"],
				"screenshots" => $screenshots, 
				"total_rate_num" => $row["total_comments"], 
			);		
		}

		//print_r($applist);exit();
		@mysql_free_result($rs);
		$response = array(
			"proto"      => 32,
			"reqsuccess" => AM_REQUEST_SUCCESS,
			"appcnt"     => $appcnt,
			"time_applist" => $strTimeApp,
			"list"       => $applist,
				);

		echo array2json($response);
		@mysql_close($conn);
	}
	if($memobj)$memobj->close();
?>

