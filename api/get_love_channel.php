<?php
	/************************************************************
	 *   协议号：38
     *   服务器接口文件： get_love_channel.php 
     *   获得喜欢的频道 
     *
     *   Author: liu jingqi
	 *   Create Time: 2010-10-20
	
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
	//输入检查
    if($AM_CURRENT_REQUEST["PROTO"] != 38){
		echo error2json("E002");die;
	}
	
	$list = "50,64,51,62,53,55,66,57,54"; ////喜欢的分类: 益智休闲,小说与漫画,棋牌天地,音乐和视频,角色扮演,动作冒险,信息和资讯,劲爆网游,经营策略
	echo array2json(array(
		"proto" => 38,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
	    "list"=>$list
	));
if($memobj)$memobj->close();
	

