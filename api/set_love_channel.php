<?php
	/************************************************************
	 *   协议号：39
     *   服务器接口文件： set_love_channel.php 
     *   设置喜欢的频道 
     *
     *   Author: liu jingqi
	 *   Create Time: 2010-10-20
	
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
	//输入检查
    if($AM_CURRENT_REQUEST["PROTO"] != 39){
		echo error2json("E002");die;
	}
	//连接数据库
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S100");die;
	}
	$uid =  isset($_POST['uid']) ? intval(__getPost('uid')) : 0;
	$mid =  isset($_POST['mid']) ? intval(__getPost('mid')) : 0;
	$channel_id = isset($_POST["channel_list"]) ? __getPost("channel_list") : "";
	if(!$channel_id){
		$sql = "delete from am_love_channel where mid=". $mid;
	}else{
		$sql = "select id from am_love_channel where mid=". $mid;
		$rs1 = mysql_query($sql, $conn);
		if(mysql_num_rows($rs1) == 0){
			$sql = "insert into am_love_channel set uid=$uid,mid='$mid',channel_id='$channel_id'";
		}else{
			$sql = "update am_love_channel set uid=$uid,channel_id='$channel_id' where mid =".$mid;
		}
	}
	mysql_query($sql, $conn);
	echo array2json(array(
		"proto" => 39,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
	));

	@mysql_close($conn);
	if($memobj)$memobj->close();

