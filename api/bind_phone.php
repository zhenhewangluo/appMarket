<?php
	require("./inc/init.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 57){
		echo error2json("E002");
		die;
	}
	if(!isset($_POST["phone"])||empty($_POST["phone"]))
	{
		echo error2json("E209");
		die;
	}
	if(isset($_POST["phone"]) && !empty($_POST["phone"])){
		$phone =  __getPost("phone");
		if(!is_phone_valid($phone))
		{
			echo error2json("E209");
			die;
		}
	}
	if(!isset($_POST["verify"])||empty($_POST["verify"])
			||!isset($_SESSION['mobileCode'])||$_POST["verify"]!=$_SESSION['mobileCode']
			||$phone!=$_SESSION['mobileNum'])
	{
		echo error2json("E408");
		die;
	}	
	
	// connect to common DB
	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}
	
	// validate uid
	$sql = "select id,name,email,phone from am_registered_user where id=". $AM_CURRENT_REQUEST["UID"];	
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");
		die;
	}
	// Update phone by UID
	if(mysql_num_rows($rs) == 0){
		echo error2json("E130");
		die;
	}else{
		$row = mysql_fetch_assoc($rs);

		// validate phone

		if(strlen($row['phone'])>0&&$row['phone']!=$phone)
		{
			echo error2json("E404");
			die;
		}
		$sql = "select id from am_registered_user where phone='".$phone."' and id<>". $AM_CURRENT_REQUEST["UID"];	
		$rs = mysql_query($sql, $conn);
		if($rs === FALSE){
			echo error2json("S002");
			die;
		}
		if(mysql_num_rows($rs) != 0){
			echo error2json("E404");
			die;
		}			
		$update_name_sql = "update `_kid_ucenter`.`uc_members` set `phone`='".$phone."' where `uid`=". $AM_CURRENT_REQUEST["UID"];		

	
		if(mysql_query($update_name_sql, $conn) === FALSE){
			echo error2json("S005");
			die;
		}
	}
	
	unset($_SESSION['mobileCode']);
	unset($_SESSION['mobileNum']);
	
	echo array2json(array(
		"proto" => 21,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
	));
	
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
