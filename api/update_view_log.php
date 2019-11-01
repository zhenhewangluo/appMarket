<?php
	/************************************************************* 
	 *   Interface 25
     *   update_view_log.php
	 * 
     *   Author: Li Xiaan
	 *   Create Time: 2010-05-14
	 *   update by bluesie  2010-8-6
	 *************************************************************/
	require("./inc/init.php");	
	
	if($AM_CURRENT_REQUEST["PROTO"] != 25){
		echo(error2json("E002"));
		die;
	}

	if(!isset($_POST['list']) || empty($_POST['list'])){
		echo(error2json("E205"));
		die;
	}
	
	$current_channel_id = isset($AM_CURRENT_REQUEST["CHANNEL"]) ? $AM_CURRENT_REQUEST["CHANNEL"] : 0;

	$channel_config_info = isset($CHANNEL_CONFIG_ARR[$current_channel_id]) ? $CHANNEL_CONFIG_ARR[$current_channel_id] : $CHANNEL_CONFIG_ARR[0];

	$list_str = stopSql(__getPost('list'));
	$arr_view_info    = array();
	foreach(explode("|", $list_str) as $view_info){
		$arr_view_info[] = $view_info;	
	}

	if($arr_view_info)
	{
		foreach($arr_view_info as $key=>$val)
		{
			$aTemp = explode(",",$val);
			$arr_info[$key]->appid = $aTemp[0];
			$arr_info[$key]->source = $aTemp[1];
			$arr_info[$key]->viewtime = $aTemp[2];
		}
	}
	/*
	//组成SQL一次插入
	$sql = array();
	foreach($filtered_view_arr as $view_info){
		$sql[] = sprintf("(%d,%d,%d,%d,NOW())", $AM_CURRENT_REQUEST["UID"], $view_info["appid"], $view_info["view_time"]);
	}
	$sql = "insert into am_view_history (mid,user_id, app_id, source,viewtime, createtime) values " . join($sql, ',');
	*/
	$uid = intval(__getPost('uid'));
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	//print_r($arr_info);exit();
	$i = 0;
	if($arr_info)
	{
		foreach($arr_info as $key=>$val)
		{
			$viewtime = substr($val->viewtime,0,10);
			//记录此条信息
			$sInsert = "insert into am_view_history (`mid`,`user_id`,`app_id`, `source`, `viewtime`, `createtime`) values ('".$_SESSION["mid"]."','".$uid."','".$val->appid."','".$val->source."','".$viewtime."','".time()."')";
			//echo "---".$sInsert."<br />";
			if(mysql_query($sInsert, $conn) === FALSE)
			{
				log_message($sInsert, 'S');
				//echo $sInsert."<br />";
				//echo error2json("S002");
				//die;
			}
			else
			{
				$i++;
			}
		}
	}
	$response = array(
		"proto"	     =>	 25,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
		"logged_num"  => $i,  
		);	
	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>