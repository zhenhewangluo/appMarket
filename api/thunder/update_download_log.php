<?php
	/************************************************************
	 *   Interface 19
     *   下载成功更新数据库记录   update_download_log.php  
	 *
     *   Author:  bluesie
	 *   Time:    2010-7-1
	 *	 paras    proto uid mid sid  list
	 *   list--格式  appid=6000035,download_id=2656,download_time=2010-06-23 11:57:14|appid=6000037,download_id=2653,download_time=2010-06-18 17:33:01
	 *************************************************************/
	require("../inc/init_interface.php");
	
    if($AM_CURRENT_REQUEST["PROTO"] != 19){
		echo error2json("E002");
		die;
	}
	//paras
	if(!isset($_POST['list']) || empty($_POST['list'])){
		echo(error2json("E205"));
		die;
	}
	$channel_config_info = isset($CHANNEL_CONFIG_ARR[$AM_CURRENT_REQUEST["CHANNEL"]]) ? $CHANNEL_CONFIG_ARR[$AM_CURRENT_REQUEST["CHANNEL"]] : $CHANNEL_CONFIG_ARR[0];
	/*
	if(!isset($channel_config_info["logging_actions_flag"])){
		echo error2json("S201");
		die;
	}
	*/
	//解析参数为数组
	$list_str = stopSql(__getPost('list'));
	$arr_download_info    = array();
	foreach(explode("|", $list_str) as $dld_info){
		$arr_download_info[] = str2array($dld_info);	
	}

	$failed_download_info = array();  //更新失败的记录都进入这里
	$filtered_download_info = array();//过滤后的记录进入这里
	for($i = 0; $i < count($arr_download_info); $i++){
		
		//过滤，每个下载必须为数组
		$dld = $arr_download_info[$i];	
		if(!is_array($dld)){
			$failed_download_info[] = array(
				'appid'     => $dld['appid'],	
				'download_id'     => $dld['download_id'],	
				'errcode' => 'E207',
				'errmsg'  => $AM_ERRORS['E207'],
			);
			continue;
		}

		//check appid
		if(!isset($dld["appid"]) || !is_numeric($dld["appid"])){
			$failed_download_info[] = array(
				'appid'     => $dld['appid'],	
				'download_id'   => $dld['download_id'],	
				'errcode' => 'E106',
				'errmsg'  => $AM_ERRORS['E106'],
			);
			continue;
		}
		//check dlid
		if(!isset($dld["download_id"]) || !is_numeric($dld["download_id"])){
			$failed_download_info[] = array(
				'appid'     => $dld['appid'],	
				'download_id'   => $dld['download_id'],	
				'errcode' => 'E125',
				'errmsg'  => $AM_ERRORS['E125'],
			);
			continue;
		}
		//check dl_time
		if(!isset($dld["download_time"]) || empty($dld["download_time"])){
			$failed_download_info[] = array(
				'appid'     => $dld['appid'],	
				'download_id'   => $dld['download_id'],	
				'errcode' => 'E208',
				'errmsg'  => $AM_ERRORS['E208'],
			);
			continue;
		}
		$filtered_download_info[] = $dld;

	}
	if(empty($filtered_download_info)){
		echo array2json(
			array(
				"proto"	     =>	 12,
			    "reqsuccess" =>  AM_REQUEST_FAIL,
				"success_count" => 0,
				"failed_count"=> count($failed_download_info),
				"failed_list" => $failed_download_info,
			)	
		);	
		die;
	}		
	//print_r($filtered_download_info);
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
		
	$success_dld_count = 0;
	foreach($filtered_download_info as $dld)
	{
	
		$sql = "select * from am_download_history where id=". $dld["download_id"] ." and app_id=". $dld["appid"];

		$rs = mysql_query($sql, $conn);
		if($rs === FALSE){
			log_message($sql, 'S');
			echo error2json("S002");
			die;
		}
		//NOT FOUND
		if(mysql_num_rows($rs) == 0){
			$failed_download_info[] = array(
				'appid'     => $dld['appid'],	
				'download_id' => $dld['download_id'],	
				'errcode' => 'E126',
				'errmsg'  => $AM_ERRORS['E126'],
			);
			continue;
		}

		//2010-4-23 Bajiev1bug1 
		$row = mysql_fetch_assoc($rs);
		if($row["dl_status"] == 1){
			$success_dld_count += 1;
			continue;
		}

		@mysql_free_result($rs);
	
		$sql = "update am_download_history set status=1 where id=". $dld["download_id"];

		if(mysql_query($sql, $conn) === FALSE){
				log_message($sql, 'S');
				echo error2json("S005");
				die;
		}

		//如果该渠道需要记载下载行为
		//if($channel_config_info["logging_actions_flag"]){

			$sql = "update am_appinfo set app_downloads=app_downloads+1 where app_id=". $dld["appid"];

			if(mysql_query($sql, $conn) === FALSE){
				log_message($sql, 'S');
				echo error2json("S005");
				die;
			}
			//2010-03-01 
			$sql = "update am_channel_stats set channel_stats_dld_cnt=channel_stats_dld_cnt+1 where channel_id=".  $AM_CURRENT_REQUEST["CHANNEL"]." and app_id=". $dld["appid"]."  and channel_stats_date=CURDATE()";

			if(mysql_query($sql, $conn) === FALSE){
				log_message($sql, 'S');
				echo error2json("S005");
				die;
			}

			if(mysql_affected_rows() == 0){
				$sql = "insert into am_channel_stats (channel_id, app_id, channel_stats_date,channel_stats_view_cnt,channel_stats_dld_cnt) values(".    $AM_CURRENT_REQUEST["CHANNEL"].",". $dld["appid"] .",CURDATE(),1,1)";

				if(mysql_query($sql, $conn) === FALSE){
					log_message($sql, 'S');
					echo error2json("S002");
					die;
				}
			}	
		//}
		$success_dld_count += 1;
	}

	$response = array(
		"proto"	     =>	 19,
	    "reqsuccess" =>  count($failed_download_info) == 0 ? AM_REQUEST_SUCCESS : AM_REQUEST_FAIL,
		"success_count" => intval($success_dld_count),
		"failed_count"=> count($failed_download_info),
		"failed_list" => $failed_download_info,
		);	
	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
