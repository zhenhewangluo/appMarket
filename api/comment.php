<?php
	header("Content-Type:text/html;charset=utf-8");

	require("./inc/init.php");	
	
	if($AM_CURRENT_REQUEST["PROTO"] != 13){
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

	if($score = __getPost("score")){
		if($score>=1 && $score<=2){
			$rate = "down";
		}elseif($score>=3 && $score<=5){
			$rate = "up";
		}else{
			echo error2json("E203");
			die;
		}
	}else{
		if("$score" !== "0"){
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
	}
	if(__getPost("comment")){
		$comment = mysql_escape_string(__getPost("comment"));
	}

    
        
        //关键词排除
        $audit = include('./inc/filter_word.php');   
      
    
	if($audit['open'] && $audit['keywords']){
		$replace = $audit['replace']?$audit['replace']:'[和*谐]';
		$arr_keyword = explode('|', $audit['keywords']);
		foreach ( $arr_keyword as $k=>$v ){
			$comment = str_replace($v, $replace, $comment);
                }
        }
  
        
        
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}

	$sql = "select * from am_appinfo where app_id=". $AM_CURRENT_REQUEST["APPID"];	
	$rs1 = mysql_query($sql, $conn);
	//NOT FOUND
	if(mysql_num_rows($rs1) == 0){
		echo error2json("E107");
		die;
	}
	
	$create_time = date("Y-m-d H:i:s");

	$field = ($rate == "up" ? "app_rate_up" : "app_rate_down");
	
	if("$score" === "0"){
		$sql = sprintf("update am_appinfo set total_comments=total_comments+1 where app_id=%s", $AM_CURRENT_REQUEST["APPID"]);
		mysql_query($sql);
	}else{
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
			$sql = "update am_appinfo set total_score=total_score+".$dscore.",total_comments=total_comments+1 $sqlrate where app_id=".$appid;
			mysql_query($sql);
		}else{
			$sql = "insert into am_score_log set uid='".__getPost('uid')."',mid='$mid',app_id='$appid',type=0,score='$score',rate='$rate',dateline='".time()."'";
			mysql_query($sql);
			
			$field = ($rate == "up" ? "app_rate_up" : "app_rate_down");	
			$sql = sprintf("update am_appinfo set %s=%s+1,total_score=total_score+%d,total_num=total_num+1,total_comments=total_comments+1 where app_id=%s", $field, $field, $score,$AM_CURRENT_REQUEST["APPID"]);
			mysql_query($sql);
		}
	}
	
	
	$channelName = array("26"=>"互联天地","27"=>"云库","10001"=>"蝌蚪");
	if($_SESSION["uid"]>0 && isset($_SESSION["username"])) $username = "";		
	elseif($channelName[$AM_CURRENT_REQUEST["CHANNEL"]]){
		$username = $channelName[$AM_CURRENT_REQUEST["CHANNEL"]].__getPost('mid');
	}else $username = "双肩背".__getPost('mid');
	$muid = ($_SESSION["uid"]>0)?$_SESSION["uid"]:0;
	$score = (intval($score)>0)?intval($score):0;
	$sql = "insert into am_comment(app_id, mid,user_id,user_name, content, create_time, update_time, rate,score) values('".$AM_CURRENT_REQUEST["APPID"]."','".__getPost('mid')."', '".$muid."','$username', '".$comment."', NOW(), NOW(), '". $rate ."','$score')";
	if(mysql_query($sql) === FALSE) {	
		log_message($sql, 'S');
		echo error2json("S002");
		die;
	}

	// Get the latest "up" and "down" and send back to client
	$sql = "select app_rate_up, app_rate_down,total_score,total_num,total_comments from am_appinfo where app_id=". $AM_CURRENT_REQUEST["APPID"];
	$rs = mysql_query($sql);
	if(mysql_num_rows($rs) > 0) {
		$row = mysql_fetch_assoc($rs);
	} else {
		echo error2json("S002");
		die;
	}
	////评论总分不能大于总次数*5 by xxxxxxxxx 2011-03-31
	checkScoreAndNum($row["total_score"] , $row["total_num"]);
	
	//积分
	$data = array("userid"=>$_SESSION["uid"],
		"password"=>$_SESSION["password"],
		"mid"=>$_POST["mid"],
		"type"=>"comment",
		"appid"=>$_POST['appid']
		);
		$re = postCurl("http://www.hjapk.com/UserCenter/index.php?m=AppScore&a=addScore", $data);
		
	echo array2json(array(
			"proto" => 13,
			"reqsuccess"  => AM_REQUEST_SUCCESS,
			"up" => $row["app_rate_up"],
			"down" => $row["app_rate_down"],
			"total_score" => $row["total_score"],
			"total_num" => $row["total_num"],
			"total_rate_num" => $row["total_comments"],
			"time" => $create_time,
		));
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

