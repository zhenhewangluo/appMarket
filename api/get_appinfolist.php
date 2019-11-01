<?php
	/************************************************************
	 *   Interface 05 for Bajiev2
     *   取应用详细信息 get_appinfo.php
	 *
     *   Author: Li Xiaan
	 *   Create Time: 2010-04-29 Fit to xxxx_ServiceAPI_v2.05 
	 *	 Update Records:
	 *			  
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 5){
		echo error2json("E002");
		die;
	}

	if(!isset($_POST['applist'])){
		echo error2json("E197");	
		die;
	}else if(empty($_POST['applist'])){
		echo error2json("E197");
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
	
	//2010-06-11
	$current_device = $AM_CURRENT_REQUEST["MODEL"];
	if($current_device == 0){
		echo error2json("E195");
		die;
	}

	$arr = explode(',', __getPost('applist'));
	$app_id_arr = array();
	if($arr === FALSE){
		echo error2json("E197");
		die;
	}
	foreach($arr as $ele){
		$app_id_arr[] = intval($ele);
	}
	$id_list = join(',', $app_id_arr);
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}	
	
	////如果是TCL接口，判断正版
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)){
		$sqlTcl ="";// " and a.copyright=1 ";
		$xxxx_iscopyright = 1;
	}
	
	//查询2010.11.11 xxxxxxxxx
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
	
	
	$sql = "select a.*,b.icon,b.logo,b.pkg_name,b.screenshots,b.app_size from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where a.app_visible=1 and a.app_id in (". $id_list .") ".$sqlTcl." group by a.app_id";
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002"); 
		die;
	}

	$applist = array();	
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
                
                
                
                 $appdesc= str_replace("<br>","\n",$row["app_desc"]);
                
		////评论总分不能大于总次数*5 by xxxxxxxxx 2011-03-31
		checkScoreAndNum($row["total_score"] , $row["total_num"]);
		$applist[] = array(
			"appid"      => $row["app_id"], 
			"screenshot" => $AM_APP_LOGO_LOC_PREFIX  . $row["logo"], 
			"icon"       => $AM_CATE_ICON_LOC_PREFIX . $row["icon"], 
			"name"       => $row["app_name"], 
			"author"     => $row["author_name"], 
			"short_desc" => deleteHtmlTags($row["app_slogan"]), 
			//"desc"       => $row["app_desc"], 
			"desc"       =>deleteHtmlTags($appdesc),  
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
	@mysql_free_result($rs);
	$response = array(
		"proto" => 5,
		"reqsuccess" => AM_REQUEST_SUCCESS,
		"list"       => $applist,
			);

	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();	
?>

