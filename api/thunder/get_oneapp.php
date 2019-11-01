<?php
	/************************************************************
	 *   Interface 12
     *   获取应用详情  get_oneapp.php  
	 *
     *   Author:  bluesie
	 *   Time:    2010-6-29
	 *	 paras  proto uid mid sid  appid 	  
	 *************************************************************/
	require("../inc/init_interface.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 12){
		echo error2json("E002");
		die;
	}
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	$current_device = $AM_CURRENT_REQUEST["MODEL"];
	if($current_device == 0){
		echo error2json("E195");
		die;
	}
	//查询 device_type_id
	////memcache缓存
	$current_device_id = $isflagexists = 0;
	$keyName = $AM_MEMCACHE["thunder_am"][0]."_device_".$current_device;
	if($AM_MEMCACHE["thunder_am"][2]){
		if(!($current_device_id = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sql = "select device_type_id from am_device where id = ".$current_device;
		$rs = mysql_query($sql, $conn);
		$row = mysql_fetch_assoc($rs);
		$current_device_id = $row['device_type_id'];
		if($isflagexists == 2) $memobj->set($keyName ,$current_device_id , 0 ,$AM_MEMCACHE["thunder_am"][1]);
	}

	//paras
	$appid = intval(__getPost('appid'));
	$uid = intval(__getPost('uid'));
	////memcache缓存
	$isflagexists = 0; $rsCate = $aResult = array();
	$keyName = $AM_MEMCACHE["thunder_am"][0]."_oneapp_".$uid."_".$appid."_".$current_device_id;
	if($AM_MEMCACHE["thunder_am"][2]){
		if(!($aResult = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sqlCate = "select a.app_id,a.author_id,a.app_name,a.app_desc,a.app_downloads,a.app_version,a.version,b.pkg_name,a.app_price,b.app_size,a.app_rate_up,a.app_rate_down,a.author_name from am_appinfo a left join am_app_device_type b on a.app_id =b.app_id  where b.device_type_id=".$current_device_id." and a.app_id=".$appid;
		$rsCate = mysql_query($sqlCate, $conn);
		$rowCate = mysql_fetch_assoc($rsCate);
		if($rowCate)
		{
			//appinfo
			$aResult['app_id'] = $rowCate['app_id'];
			$aResult['app_name'] = $rowCate['app_name'];
			$aResult['app_desc'] = deleteHtmlTags($rowCate['app_desc']);
			$aResult['app_version'] = $rowCate['app_version'];
			$aResult['version'] = intval($rowCate['version']);
			$aResult['pkg_name'] = $rowCate['pkg_name'];
			$aResult['app_price'] = $rowCate['app_price'];
			$aResult['app_size'] = intval($rowCate['app_size']);
			$aResult['app_rate_up'] = intval($rowCate['app_rate_up']);
			$aResult['app_rate_down'] = intval($rowCate['app_rate_down']);
			//download
			$aResult['download_cnt'] = intval($rowCate['app_downloads']);
			//comment
			$sqlComment = "select count(id) from am_comment where app_id=".$rowCate['app_id']." and stat='normal'";
			$rsComment = mysql_query($sqlComment, $conn);
			$count = mysql_result($rsComment,0,0);
			$aResult['comment_cnt'] = intval($count);
			
			
			//My comment 
			$sqlMycomment = "select id,user_name,app_id,create_time,update_time,rate,content from am_comment where app_id=".$rowCate['app_id']." and stat='normal' and user_id=".$uid;
			//echo $sqlMycomment;
			$rsMycomment = mysql_query($sqlMycomment, $conn);
			$rowMycomment = mysql_fetch_assoc($rsMycomment);

			$aResult['my_comment']['id'] = empty($rowMycomment['id'])?'':$rowMycomment['id'];
			$aResult['my_comment']['name'] = empty($rowMycomment['user_name'])?'':$rowMycomment['user_name'];
			$aResult['my_comment']['time'] = empty($rowMycomment['update_time'])?'':$rowMycomment['update_time'];
			$aResult['my_comment']['rate'] = empty($rowMycomment['rate'])?'':$rowMycomment['rate'];
			$aResult['my_comment']['comment'] = empty($rowMycomment['content'])?'':$rowMycomment['content'];

			//Latest  3 comments 
			$sqlComment = "select id,user_name,app_id,update_time,rate,content from am_comment where app_id=".$rowCate['app_id']." and stat='normal' order by update_time desc limit 3 ";
			//echo $sqlComment;exit();
			$rsComment = mysql_query($sqlComment, $conn);
			//$rowComment = mysql_fetch_assoc($rsComment);
			$i = 0;
			while ($rowComment = mysql_fetch_assoc($rsComment))
			{
				$aComment[$i]['id'] = empty($rowComment['id'])?'':$rowComment['id'];
				$aComment[$i]['name'] = empty($rowComment['user_name'])?'':$rowComment['user_name'];
				$aComment[$i]['time'] = empty($rowComment['update_time'])?'':$rowComment['update_time'];
				$aComment[$i]['rate'] = empty($rowComment['rate'])?'':$rowComment['rate'];
				$aComment[$i]['comment'] = empty($rowComment['content'])?'':$rowComment['content'];
				$i++;
			}
			if($aComment)
				$aResult['comment_list'] = $aComment;

			$aResult['my_comment']['id'] = empty($rowMycomment['id'])?'':$rowMycomment['id'];
			$aResult['my_comment']['name'] = empty($rowMycomment['user_name'])?'':$rowMycomment['user_name'];
			$aResult['my_comment']['time'] = empty($rowMycomment['update_time'])?'':$rowMycomment['update_time'];
			$aResult['my_comment']['rate'] = empty($rowMycomment['rate'])?'':$rowMycomment['rate'];
			$aResult['my_comment']['comment'] = empty($rowMycomment['content'])?'':$rowMycomment['content'];


			//author
			$conn = connect_comm_db();
			$sqlAuth = "select name,email,website from am_author where id=".$rowCate['author_id'];
			$rsAuth = mysql_query($sqlAuth, $conn);
			$rowAuth = mysql_fetch_assoc($rsAuth);
			$aResult['author_id'] = $rowCate['author_id'];
			$aResult['author_name'] = $rowAuth['name'];
			$aResult['author_http'] = $rowAuth['website'];
			$aResult['author_email'] = $rowAuth['email'];
		}
		if($isflagexists == 2) $memobj->set($keyName ,$aResult , 0 ,$AM_MEMCACHE["thunder_am"][1]);
	}
	
	if($aResult){
		$json_arr = array("proto" => 12,"reqsuccess"  => AM_REQUEST_SUCCESS);
		$json_arr = @array_merge($json_arr,$aResult);
		echo array2json($json_arr); 
	}else{
		echo error2json("E107");die;
	}
	@mysql_free_result($rs);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
