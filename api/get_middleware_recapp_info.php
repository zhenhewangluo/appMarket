<?php
	/*
	proto:53
	��ȡ�м��Ӧ����Ϣ
	author:xxxxxxxxx
	date:2011-03-16
	*/
	require("inc/config.inc.php");	
	require_once("inc/error.eng.php");
	require_once("inc/functions.php");
	
    if(__getPost('proto') != 53){
		echo error2json("E002");		die;
	}
	////�м���汾�ţ���ʱδ��
	$midware_version = __getPost('midware_version');
	$cp_id = __getPost('cp_id');
	
	$pageNo  = (intval(__getPost('pageNo')))?intval(__getPost('pageNo')):1;
	$perpage  = (intval(__getPost('perpage')))?intval(__getPost('perpage')):10;
	$offset = ($pageNo-1)*$perpage;

	//�ж��Ƿ�ֵ��screen,sdk---2010.11.11
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

	//��ѯ2010.11.11 xxxxxxxxx
	$arrTestDeviceId = array(); $isflagexists = 0;
	////memcache����
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


	$sql = "select a.*,b.icon,b.logo,b.pkg_name,b.screenshots,b.app_size from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where a.app_id in (select app_id from am_middleware_recapp) and a.app_visible=1 and a.app_price=0 group by a.app_id order by a.app_id desc limit $offset,$perpage";
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");die;
	}

	$applist = array();	
	while ($row = mysql_fetch_assoc($rs)) {
		list($s1 , $s2) = explode("|",$row['screenshots']);
		if($_SESSION["channel"] == 10028){ ////T-PARK����
			$s1 = str_replace("/" , "_" , $s1);
			$s2 = str_replace("/" , "_" , $s2);
			$arr_s[0] = ($s1)?$AM_APP_LOGO_LOC_PREFIX."nosign/".$s1:"";
			$arr_s[1] = ($s2)?$AM_APP_LOGO_LOC_PREFIX."nosign/".$s2:"";
		}else{
			$arr_s[0] = ($s1)?$AM_APP_LOGO_LOC_PREFIX.$s1:"";
			$arr_s[1] = ($s2)?$AM_APP_LOGO_LOC_PREFIX.$s2:"";
		}
		$screenshots = implode("," , $arr_s);
		////�����ֲܷ��ܴ����ܴ���*5 by xxxxxxxxx 2011-03-31
		checkScoreAndNum($row["total_score"] , $row["total_num"]);
	
		$applist[] = array(
			"appid"      => $row["app_id"], 
			"name"       => $row["app_name"], 
			"icon"       => $AM_CATE_ICON_LOC_PREFIX . $row["icon"], 
			"price"      => $row["app_price"] * AM_EXCHANGE_RATE, 
			"download_cnt"=>$row["app_downloads"]*AM_DOWNLOAD_CNT,
			"author"     => $row["author_name"], 
			"appver"     => $row["app_version"], 
			"size"       => $row["app_size"],
			"language" => $row["is_english"],
			"total_score" =>$row["total_score"],
			"total_num" =>  $row["total_num"],
			"screenshots" =>$screenshots, 
			"desc"       => deleteHtmlTags($row["app_desc"]),
			"pkg_name"=> $row['pkg_name']
		);
		
		////��¼����
		$sqlstr = "insert into statlog.middleware_view set cp_id='$cp_id',app_id='".$row["app_id"]."',dateline='".time()."'";
		mysql_query($sqlstr, $conn);
	}
	
	$sql = "select count(*) as c from am_appinfo a where a.app_id in (select app_id from am_middleware_recapp) and a.app_visible=1 and a.app_price=0  ";
	$rs = mysql_query($sql, $conn);
	$row = mysql_fetch_assoc($rs);
	$count = $row['c'];
		
	echo array2json(array(
		"proto" => 53,
  		"reqsuccess"  => AM_REQUEST_SUCCESS,
  		"count" => $count,
		'list'  => $applist
	));
	
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
