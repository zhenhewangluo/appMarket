<?php
	/************************************************************* 
	 *   Interface 34
     *   get_feedback.php 接口：意见反馈
	 *   arg:	proto: 34
   				uid
   				mid
   				sid
   				email
   				feedback
     *   Author: liu jingqi
	 *   Create Time: 2010-10-12
	 *************************************************************/
	require("./inc/init.php");	
	
	if($AM_CURRENT_REQUEST["PROTO"] != 34){
		echo(error2json("E002")); 
		die;
	}
	////意见反馈内容不能为空
	if(!isset($_POST['content']) || empty($_POST['content'])){
		echo error2json("E127");
		die;
	}
	
	$uid = intval(__getPost('uid'));
	$email =  (stopSql(__getPost('email')))?stopSql(__getPost('email')):"";
	$feedback = stopSql(__getPost('content'));
	
	////数据库连接
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	
	$sInsert = "insert into am_feedback (reid,`user_id`, `email`, `feedback`, `addtime`) values (0,'".$uid."','".$email."','".$feedback."','".time()."')";	
	if(mysql_query($sInsert, $conn) === FALSE)
	{	
		echo error2json("S002");
		die;
	}
	$response = array(
		"proto"	     =>	 34,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
		);	
	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
