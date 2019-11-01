<?php
	/************************************************************
	 *   协议号：37
     *   服务器接口文件： add_score.php 
     *   积分奖励
     * 
     *   Author: liu jingqi
	 *   Create Time: 2010-10-19
	 type = 1:表示注册，积分+10
	type = 2:表示邀请好友成功，积分+100
	type = 3:表示每次分享，积分+1,每天最多分享10个
	type = 4:表示软件评价，首次评价积分+5；非首次积分+1
	type = 5:表示充值1元，积分+10;参数money:表示充值钱数，
	type = 6:表示登录，连续五天登录：＋40
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
	//输入检查
    if($AM_CURRENT_REQUEST["PROTO"] != 37){
		echo error2json("E002");die;
	}
	//连接数据库
	$conn = connect_comm_db();
	if($conn === FALSE){ 
		echo error2json("S100");die;
	}

	if(!isset($_POST['type']) || empty($_POST['type'])){
		echo error2json("E104");
		die;
	}
	
	$type = __getPost('type');
	$uid =  isset($_POST['uid']) ? intval(__getPost('uid')) : 0;
	$mid =  isset($_POST['mid']) ? __getPost('mid') : 0;
	$sql = "select score from am_registered_user  where id=". $uid;	
	$rs1 = mysql_query($sql, $conn);
	if(mysql_num_rows($rs1) == 0){
		echo error2json("E107");
		die;
	}	
		
	if($type == 1){ 
		////注册时积分+10
		$sql = "update am_registered_user set score=score+".$AM_ADDSCORE_TYPE[$type]." where id=". $uid;	
		mysql_query($sql, $conn);
		@mysql_close($conn);
		
		$conn = connect_db();
		$sql = "insert into am_score_log set uid=".$uid.",mid=".$mid.",type=$type,score=".$AM_ADDSCORE_TYPE[$type].",dateline=".time();
		mysql_query($sql, $conn);
	}elseif($type == 2){
		////邀请好友成功积分+100
		$sql = "update am_registered_user set score=score+".$AM_ADDSCORE_TYPE[$type]." where id=". $uid;	
		mysql_query($sql, $conn);
		@mysql_close($conn);
		
		$conn = connect_db();
		$sql = "insert into am_score_log set uid=".$uid.",mid=".$mid.",type=$type,score=".$AM_ADDSCORE_TYPE[$type].",dateline=".time();
		mysql_query($sql, $conn);
	}elseif($type == 3){
		@mysql_close($conn);
		$conn = connect_db();
		$sql = "select * from am_score_log where uid=$uid and type=$type and FROM_UNIXTIME( dateline, '%Y-%m-%d' ) 
 ='".date("Y-m-d")."'";
		$rs = mysql_query($sql, $conn);
		if(mysql_num_rows($rs) >= 9){
			echo error2json("E206");
			die;
		}
		
		$sql = "insert into am_score_log set uid=".$uid.",mid=".$mid.",type=$type,score=".$AM_ADDSCORE_TYPE[$type].",dateline=".time();
		mysql_query($sql, $conn);
		
		@mysql_close($conn);
		$conn = connect_comm_db();
		////分享时积分+1		
		$sql = "update am_registered_user set score=score+".$AM_ADDSCORE_TYPE[$type]." where id=". $uid;	
		mysql_query($sql, $conn);
		
	}elseif($type == 4){
		@mysql_close($conn);
		$conn = connect_db();
		////软件评价积分+1,首次评价+5
		$arg2 =  isset($_POST['arg']) ? intval(__getPost('arg')) : 0;
		$sql = "select * from am_comment where appid='".$arg2."'";
		$rs = mysql_query($sql, $conn);
		if(mysql_num_rows($rs) ==0){
			$score2 = 5;
		}else{
			$score2 = $AM_ADDSCORE_TYPE[$type];
		}
		$sql = "insert into am_score_log set uid=".$uid.",mid=".$mid.",type=$type,score=".$score2.",dateline=".time();
		mysql_query($sql, $conn);
		
		@mysql_close($conn);
		$conn = connect_comm_db();
		$sql = "update am_registered_user set score=score+".$score2." where id=". $uid;	
		mysql_query($sql, $conn);
		
		
	}elseif($type == 5){
		////充值成功积分+10*钱数		
		$arg2 =  isset($_POST['arg']) ? intval(__getPost('arg')) : 0;
		$score2 = $AM_ADDSCORE_TYPE[$type]*$arg2;
		$sql = "update am_registered_user set score=score+".$score2." where id=". $uid;	
		mysql_query($sql, $conn);
		@mysql_close($conn);
		
		$conn = connect_db();
		$sql = "insert into am_score_log set uid=".$uid.",mid=".$mid.",type=$type,score=".$score2.",dateline=".time();
		mysql_query($sql, $conn);
	}elseif($type == 6){
		////分享时积分+1
		$sql = "update am_registered_user set score=score+".$AM_ADDSCORE_TYPE[$type]." where id=". $uid;	
		mysql_query($sql, $conn);
		@mysql_close($conn);
		
		$conn = connect_db();
		$sql = "insert into am_score_log set uid=".$uid.",mid=".$mid.",type=$type,score=".$AM_ADDSCORE_TYPE[$type].",dateline=".time();
		mysql_query($sql, $conn);
	}else{
		echo error2json("E105");
		die;
	}
	
	


	echo array2json(array(
		"proto" => 37,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
	));

	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
