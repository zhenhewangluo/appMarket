<?php
	/************************************************************
	 *   Interface 35 for xxxx
     *   取应用列表的每一个应用信息 get_related_applist.php
	 *	 参数  proto: 35
			   uid			   mid			   sid			   appid
	 *   Author: liu jingqi
	 *   Create Time: 2010-10-12 
	 *	 Update Records:
	 *			  
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 35){
		echo error2json("E002");
		die;
	}

	if(!isset($_POST['appid']) || empty($_POST['appid'])){
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
	
	//2010-10-12 设备型号 ，条件是必须MID填写正确
	$current_device = $AM_CURRENT_REQUEST["MODEL"];
	if($current_device == 0){
		echo error2json("E195");
		die;
	}
	$app_id = intval(__getPost('appid'));
	
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
		$sqlTcl ="";// " and a.copyright=1 ";
		$xxxx_iscopyright = 1;
	}
	
	//查询2010.11.11 xxxxxxxxx
	$applist = array(); $isflagexists = 0;
	////memcache缓存
	$keyName = $AM_MEMCACHE["get_related_applist"][0].$screen.$sdk."_".$isflagexists."_".$app_id;
	if($AM_MEMCACHE["get_related_applist"][2]){
		if(!($applist = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){		
		$sql = "select a.app_id,a.author_id from am_appinfo a where a.app_id = ". $app_id . $sqlTcl." ";
		$rs = mysql_query($sql, $conn);
		if($rs === FALSE){
			echo error2json("S002");
			die;
		}
		$rowD = mysql_fetch_assoc($rs);
		if($rowD['app_id']){
			//$app_id = 4001679;
			$authorid = ($rowD['author_id'])?$rowD['author_id']:0;
			$arrUser = $arrApp = array();
			$arrApp[] = $app_id;
			////下载｛应用名称｝也下载了：下载过相同软件的用户，下载的其他软件最多在前显示，最多显示五个
			/*$sqlD = "select user_id from am_download_history where app_id=$app_id group by user_id order by id desc limit 20";
			$rs = mysql_query($sqlD, $conn);
			while($row = mysql_fetch_assoc($rs)){
				$arrUser[] = $row['user_id'];
			}
			$strUser = implode("," , $arrUser);
			
			$sqlD = "select a.app_id from am_download_history a left join am_appinfo b on a.app_id=b.app_id where a.user_id in($strUser)  group by a.app_id order by b.app_downloads desc limit 10";
			$rs = mysql_query($sqlD, $conn);
			while($row = mysql_fetch_assoc($rs)){
				$arrApp[] = $row['app_id'];
			}*/
			
			
			
			////同一类别的软件：显示同一分类的软件，最多显示10个
			if($arrApp) $strsql = " and a.app_id not in(".implode(",",$arrApp).")";
			if($sqlTcl)
				$sqlD = "select distinct a.app_id from am_app_category a  where a.cate_id in (select cate_id from am_app_category where app_id=$app_id and cate_id not in(1,2,3,4,5,6)) and a.app_id in(select app_id from am_appinfo where app_visible=1 and copyright=1) $strsql order by a.app_id desc limit 10";
			else
				$sqlD = "select distinct a.app_id from am_app_category a  where a.cate_id in (select cate_id from am_app_category where app_id=$app_id and cate_id not in(1,2,3,4,5,6)) and a.app_id in(select app_id from am_appinfo where app_visible=1) $strsql order by a.app_id desc limit 10";
			
			//echo $sqlD."<br/>";
			$rs = mysql_query($sqlD, $conn);
			while($row = mysql_fetch_assoc($rs)){
				$arrApp[] = $row['app_id'];
			}
			
			
			////同一个专题的软件：显示同一专题的软件，最多显示10个，如该如件无专题，则不显示该项
			if($arrApp) $strsql = " and a.app_id not in(".implode(",",$arrApp).")";
			if($sqlTcl)
				$sqlD = "select distinct a.app_id from am_app_topic a  where a.topic_id in (select topic_id from am_app_topic where app_id=$app_id) and a.app_id in(select app_id from am_appinfo where app_visible=1 and copyright=1) $strsql order by a.app_id desc limit 10";
			else	
				$sqlD = "select distinct a.app_id from am_app_topic a  where a.topic_id in (select topic_id from am_app_topic where app_id=$app_id) and a.app_id in(select app_id from am_appinfo where app_visible=1) $strsql order by a.app_id desc limit 10";
			//echo $sqlD."<br/>";
			$rs = mysql_query($sqlD, $conn);
			while($row = mysql_fetch_assoc($rs)){
				$arrApp[] = $row['app_id'];
			}
			
			
			////同一个作者的软件:显示来自同一个作者的软件，最多显示10个，如该作者无其他软件，则不显示该项
			if($arrApp) $strsql = " and a.app_id not in(".implode(",",$arrApp).")";
			$sqlD = "select distinct a.app_id from am_appinfo a where a.app_visible=1  and  a.author_id =".$authorid. $sqlTcl." $strsql order by a.app_id desc limit 10";		
			//echo $sqlD."<br/>";
			$rs = mysql_query($sqlD, $conn);
			while($row = mysql_fetch_assoc($rs)){
				$arrApp[] = $row['app_id'];
			}
			
			////过滤最初传入app_id
			$result = array_diff($arrApp, array($app_id));
			$result = array_unique($result);
			$applist = implode("," , $result);
		}else $applist = "";
		//$applist = "4001679,4001680,4001682,4001675,4001676,4001677,4001674,4001673,4001672,4001669,4001670,4001666,4001668,4001663,4001658";
		@mysql_free_result($rs);
		if($isflagexists == 2){
			$memobj->set($keyName ,$applist , 0 ,$AM_MEMCACHE["get_related_applist"][1]);
		}
	}
	$response = array(
		"proto" => 35,
		"reqsuccess" => AM_REQUEST_SUCCESS,
		"list"       => $applist,
			);

	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
	die;		
?>

