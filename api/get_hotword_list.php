<?php
	/************************************************************
	 *   Interface 49
     *   获取热词列表 默认100个
     *   Author:  xxxxxxxxx
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 49){
		echo error2json("E002");
		die;
	}

	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");die;
	}
	
	$total_keywords = (intval(__getPost('count')) > 0)?intval(__getPost('count')):100;
	//查询  am_hotwords 表，获得 TOTAL_KEYWORDS hotwords	////memcache
	$keyName = $AM_MEMCACHE["am_hotwords"][0].$total_keywords;
	$isflagexists = 0;
	if($AM_MEMCACHE["am_hotwords"][2]){
		if(!($aHotword = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){				
		$sql = "select keyword from am_hotwords order by `order` asc,count desc limit ".$total_keywords;
		$result = mysql_query($sql, $conn);
		while ($row = mysql_fetch_assoc($result))	{
			$aHotword[] = $row['keyword'];
		}
		if($isflagexists == 2)$memobj->set($keyName ,$aHotword , 0 ,$AM_MEMCACHE["am_hotwords"][1]);
	}
	////判断是否云库渠道
	if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
		$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['hotwords']) , true);
		if($arrYunlibNew){
			$aHotword = array_unique(array_merge($arrYunlibNew , $aHotword));
			$aHotword = array_slice($aHotword , 0 , $total_keywords);
		}
	}
	 $list = @join(",",$aHotword);
	
	$response = array(
		"proto" => 49,
		"reqsuccess" => AM_REQUEST_SUCCESS,
		"list" => $list
	);
	 
	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
