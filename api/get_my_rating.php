<?php
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
	if($AM_CURRENT_REQUEST["PROTO"] != 27){
		echo error2json("E002");
		die;
	}
	if(!isset($_POST['appid']) || !is_numeric($_POST['appid'])){
		echo error2json("E106");
		die;
	}
	$appid = intval(__getPost('appid'));

	//connect to database
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	$mid = __getPost('mid');
	//$AM_CURRENT_REQUEST["UID"] = 45;
	$sql =  "select * from am_score_log where app_id='$appid' and mid='$mid'";
	
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");
		die;
	}
	if(mysql_num_rows($rs) == 0){
		echo error2json("E211");
		die;
	}
	$row = mysql_fetch_assoc($rs);

	echo array2json(array(
		"proto" => 27,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
		"rating"  => $row["rate"] == '' ? "no" : $row["rate"],
		"comment" => "",
		"time" => date("Y-m-d H:i:s" , $row["dateline"]),
		"score"=> ($row["score"])?$row["score"]:0,
	));

	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

