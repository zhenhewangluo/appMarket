<?php
	/************************************************************
	 *   协议号：15
     *   服务器接口文件： get_info.php for BajieV2
     *   获取用户信息
     *
     *   Author: Li Xiaan
	 *   Create Time: 2010-01-24
	 *   Updates: 2010-04-19 BajieV2 am_user表换到comm数据库 
	 * 
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
	//输入检查
    if($AM_CURRENT_REQUEST["PROTO"] != 15){
		echo error2json("E002");
		die;
	}
	//连接数据库
	//$conn = connect_db();
	//commented by lixiaan for bajiev2
	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}

	//检查是否已经有过该用户的资料
	$sql = "select id,email,name,phone,balance,frozen_amount from am_registered_user where id=". $AM_CURRENT_REQUEST["UID"];	
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");
		die;
	}
	//若该用户未设置资料,报错
	if(mysql_num_rows($rs) == 0){
		echo error2json("E191");
		die;
	}	
	//否则，输出客户资料
	$row = mysql_fetch_assoc($rs);

	//余额
	//可用余额 = 账户余额 - 止付金额
	$balance =	is_numeric($row["balance"])?$row["balance"]:0;
	$stop_amount = is_numeric($row["frozen_amount"])?$row["frozen_amount"]:0;
	$available   = $balance - $stop_amount;
	//可用余额不得小于0
	if($available < 0){
		$available = 0;
	}
	//2010-3-9, 汇率
	$available = $available * AM_EXCHANGE_RATE;
	
	//username
	if($row['name'])  
	{
		$username = $row['name'];
	}
	else
	{
		$aEmail = explode("@",$row['email']);
		$username = $aEmail[0];
	}
	echo array2json(array(
		"proto" => 15,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
		"name"  => $username,
		"email" => $row["email"],
		"phone" => $row["phone"],
		"balance"=> $available,
	));

	@mysql_close($conn);
	if($memobj)$memobj->close();
?>