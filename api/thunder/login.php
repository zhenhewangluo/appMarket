<?php
	/************************************************************
	 *   Interface 20
     *   登录  login.php  
	 *
     *   Author:  bluesie
	 *   Time:    2010-7-1
	 *	 paras    proto uid mid sid  email  passwd  
	 *************************************************************/

	require("../inc/init_interface.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 20){
		echo error2json("E002");
		die;
	}

	if(!isset($_POST['email']) && empty($_POST['email'])){
		echo error2json("E198");
		die;
	}
	if(!isset($_POST['passwd']) || empty($_POST['passwd'])){
		echo error2json("E127");
		die;
	}	
	$email =  stopSql(__getPost('email'));

	if(!is_mail_valid($email)){
		echo error2json("E198");
		die;
	}
	$pwd = trim(__getPost('passwd'));
	if(strlen($pwd) != 64){
		echo error2json("E127");
		die;		
	}	
	$pwd  = pack('H*', $pwd);	
	$sid = $AM_CURRENT_REQUEST["SID"];
	$sid = str_repeat($sid, ceil(32/strlen($sid)));
	$pwd_md5 = $pwd ^ $sid;

	//commented by lixiaan for bajiev2
	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}
	$sql = "select * from am_registered_user where email='". $email ."' and status=1";
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");
		die;
	}	
	//NOT FOUND
	if(mysql_num_rows($rs) == 0){
		echo error2json("E130");
		die;
	}
	//Password error
	$row = mysql_fetch_assoc($rs);
	if(strcmp($row["password"], $pwd_md5) != 0){
		echo error2json("E131");
		die;
	}

	//get available balance
	$balance =	is_numeric($row["balance"])?$row["balance"]:0;
	$stop_amount = is_numeric($row["stop_amount"])?$row["stop_amount"]:0;
	$available   = $balance - $stop_amount;
	if($available < 0){
		$available = 0;
	}
	//2010-3-9, exchange rate between money & virtual internal balance.
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
	$login_user = array(
	  "uid"   => $row["id"],
	  "email" => $row["email"],
	  "phone" => $row["phone"],
	  "name"  => $username, 
	  "balance" => $available,
	  );

	//Auto logon via new session.

	# Commented due to:
	# 	<b>Warning</b>:  session_regenerate_id() [<a href='function.session-regenerate-id'>function.session-regenerate-id</a>]: Cannot regenerate session id - headers already sent in <b>/home/vhosts/xxxx365.com/htdocs/android/v2/sandroid/login.php</b> on line <b>80</b><br />
	session_regenerate_id();
	$new_sid = session_id();

	$_SESSION["username"] = $email;
	$_SESSION["uid"]   = $row["id"];	

	log_message(
	sprintf("[LOGIN]CHANNEL=%s,MODEL=%s,MID=%s,UID=%s", $AM_CURRENT_REQUEST["CHANNEL"], $AM_CURRENT_REQUEST["MODEL"], $AM_CURRENT_REQUEST["MID"], $login_user["uid"]),'I');
	
	//update login information
	$sql = sprintf("update am_registered_user set last_login_terminal=%d, last_login_time=NOW(),last_login_sid='%s' where id=%d",$AM_CURRENT_REQUEST["MID"], $new_sid, $login_user["uid"]);

	if(mysql_query($sql) === FALSE){
		log_message($sql, 'S');
		echo error2json('S005');
		die;
	}	

	echo array2json(array(
		"proto" => 20,
	    "reqsuccess" =>  AM_REQUEST_SUCCESS,
		"uid"    => $login_user["uid"],
		"sid"    => $new_sid,
		"name"   => $login_user["name"],
		"email"  => $login_user["email"],
		"phone"  => $login_user["phone"],
		"balance"=> $login_user["balance"],
	));

	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
