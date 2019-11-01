<?php
	/************************************************************
	 *   Interface 42
     *   应用评分,每个人对某个应用又只有一条记录，如果对某个应用多次操作就只能修改，并修改总 评分数量
	 *   
     *   Author: liu jingqi
	 *   Create Time: 2010-12-15
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
	if($AM_CURRENT_REQUEST["PROTO"] != 42){
		echo error2json("E002");
		die;
	}

	if(!isset($_POST['appid'])){
		echo(error2json("E106"));
		die;
	}
	if(empty($_POST['appid']) || !is_numeric($_POST['appid'])){
		echo(error2json("E107"));
		die;
	}
	$appid = $AM_CURRENT_REQUEST["APPID"] = intval(__getPost('appid'));

	if($score = intval(__getPost("score"))){
		if($score>=1 && $score<=2){
			$rate = "down";
		}elseif($score>=3 && $score<=5){
			$rate = "up";
		}else{
			echo error2json("E203");
			die;
		}
	}else{
		if(!isset($_POST['rate'])){
			echo error2json("E203");	
			die;
		}else if(empty($_POST['rate']) || !in_array(__getPost('rate'), array("up", "down"))){
			echo error2json("E203");
			die;
		}
		$rate = trim(__getPost('rate'));
		if($rate == "up"){
			$score = 5;
		}else{
			$score = 2;
		}
	}
	
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}

	$sql = "select app_id from am_appinfo where app_id=". $AM_CURRENT_REQUEST["APPID"];	
	$rs1 = mysql_query($sql, $conn);
	//NOT FOUND
	if(mysql_num_rows($rs1) == 0){
		echo error2json("E107");
		die;
	}
	
	$create_time = date("Y-m-d H:i:s");
	
	$mid = __getPost('mid');
	$sql = "select id,rate,score from am_score_log where app_id='$appid' and mid='$mid'";
	$rs = mysql_query($sql, $conn);
	$row = mysql_fetch_assoc($rs);
	
	if($row['id']){
		$sql = "update am_score_log set score='$score',rate='$rate',dateline='".time()."' where id=".$row['id'];
		mysql_query($sql);
		
		if($row['rate'] != $rate){
			if($rate == "up")	$sqlrate = ",app_rate_up=app_rate_up+1,app_rate_down=app_rate_down-1";
			else	$sqlrate = ",app_rate_down=app_rate_down+1,app_rate_up=app_rate_up-1";
		}
		$dscore = $score - $row['score'];
		$sql = "update am_appinfo set total_score=total_score+".$dscore." $sqlrate where app_id=".$appid;
		mysql_query($sql);
	}else{
		$sql = "insert into am_score_log set uid='".__getPost('uid')."',mid='$mid',app_id='$appid',type=0,score='$score',rate='$rate',dateline='".time()."'";
		mysql_query($sql);
		
		$field = ($rate == "up" ? "app_rate_up" : "app_rate_down");	
		$sql = sprintf("update am_appinfo set %s=%s+1,total_score=total_score+%d,total_num=total_num+1 where app_id=%s", $field, $field, $score,$AM_CURRENT_REQUEST["APPID"]);
		mysql_query($sql);
	}

	// Get the latest "up" and "down" and send back to client
	$sql = "select total_score,total_num from am_appinfo where app_id=". $AM_CURRENT_REQUEST["APPID"];
	$rs = mysql_query($sql);
	if(mysql_num_rows($rs) > 0) {
		$row = mysql_fetch_assoc($rs);
	} else {
		echo error2json("S002");
		die;
	}
	////评论总分不能大于总次数*5 by xxxxxxxxx 2011-03-31
	checkScoreAndNum($row["total_score"] , $row["total_num"]);
	echo array2json(array(
			"proto" => 42,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			"total_score" => $row["total_score"],
			"total_num" => $row["total_num"],
		));
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

