<?php
	/************************************************************
	 *   Interface 48
     *   version是否更新 如果 infoversion 与库中 版本值相同 则返回易变数据
	 *   如果不同，则返回所有常用数据
     *   Author: xxxxxxxxx
	 *   Create Time: 2011-01-12
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 48){
		echo error2json("E002");die;
	}

	if(!isset($_POST['appid']) || empty($_POST['appid'])){
		echo error2json("E197");die;
	}

	$app_id = intval(__getPost('appid'));
	$infoversion = intval(__getPost('infoversion'));
	
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
		echo error2json("S001");die;
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
	
	$sql = "select * from am_appinfo where app_id=".$app_id;
	$rs = mysql_query($sql, $conn);
	$row = mysql_fetch_assoc($rs);
	////评论总分不能大于总次数*5 by xxxxxxxxx 2011-03-31
	checkScoreAndNum($row["total_score"] , $row["total_num"]);
	
	if($row['infover'] > $infoversion){
		$sql = "select icon,logo,pkg_name,screenshots,app_size from am_app_device_type where app_id =".$app_id." group by app_id ";
		$rs = mysql_query($sql, $conn);
		$row2 = mysql_fetch_assoc($rs);
		list($s1 , $s2) = explode("|",$row2['screenshots']);
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
               
		$response = array(
			"proto" => 48,
			"reqsuccess" => AM_REQUEST_SUCCESS,
			"appid"      => $row["app_id"], 
			"icon"       => $AM_CATE_ICON_LOC_PREFIX . $row2["icon"], 
			"name"       => $row["app_name"], 
			"author"     => $row["author_name"], 
			"short_desc" => deleteHtmlTags($row["app_slogan"]), 
			//"desc"       => $row["app_desc"], 
			"desc"       => deleteHtmlTags($appdesc), 
			"download_cnt"=>$row["app_downloads"]*AM_DOWNLOAD_CNT, 
			"price"      => $row["app_price"] * AM_EXCHANGE_RATE, 
			"version"    => $row["version"], 
			"appver"     => $row["app_version"], 
			"infover"    => $row["infover"],
			"size"       => $row2["app_size"],
			"pkg_name"   => $row2["pkg_name"],
			"is_english" =>$row["is_english"],
			"total_score" =>$row["total_score"],
			"total_num" =>$row["total_num"],
			"screenshots" => $screenshots, 
			"total_rate_num" => $row["total_comments"], 
		);		
	}else{
		$response = array(
			"proto" => 48,
			"reqsuccess"	  => AM_REQUEST_SUCCESS,
			"appid"       	  => $app_id,
			"download_cnt"    => $row["app_downloads"]*AM_DOWNLOAD_CNT,
			"infover"         => $row["infover"],
			"total_score"     => $row["total_score"],
			"total_num"       => $row["total_num"],
			"total_rate_num"  => $row["total_comments"]
		);
	}
	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
