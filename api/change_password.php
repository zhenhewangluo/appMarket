<?php
	require("./inc/init.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 20){
		echo error2json("E002");
		die;
	}
	if(!isset($_POST["old_passwd"]) || empty($_POST["old_passwd"])){
		echo error2json("E127");
		die;
	}	
	if(!isset($_POST["new_passwd"]) || empty($_POST["new_passwd"])){
		echo error2json("E212");
		die;
	}
	$old_pwd_decrypted =  stopSql(__getPost("old_passwd"));
	$new_pwd_decrypted =  stopSql(__getPost("new_passwd"));
        
        log_message("------------------------pws--------------------".$old_pwd_decrypted);
        log_message("------------------------pws--------------------".$new_pwd_decrypted);
        
        
//	if(strlen($old_pwd_encrypted) != 64){
//		echo error2json("E127");
//		die;		
//	}	
//	if(strlen($new_pwd_encrypted) != 64){
//		echo error2json("E212");
//		die;		
//	}	

        
        
        
	//$sid = $AM_CURRENT_REQUEST["SID"];
	//$key = str_repeat($sid, ceil(32/strlen($sid)));
//	$old_pwd_decrypted = pack('H*', $old_pwd_encrypted) ^ $key;
//	$new_pwd_decrypted = pack('H*', $new_pwd_encrypted) ^ $key;

        
        
        
        
	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}
        
        
	$current_user_id    = $AM_CURRENT_REQUEST["UID"];
	$current_user_email = $AM_CURRENT_REQUEST["USERNAME"];

	$sql = "select id,password,email from am_registered_user where email='". $current_user_email ."' and id=". $current_user_id;

	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		log_message($sql);
		echo error2json("S002");
		die;
	}
	if(mysql_num_rows($rs) == 0){
		log_message("User not found, UID=". $current_user_id .",EMAIL=". $current_user_email, 'S');
		echo error2json("E130");
		die;
	}
	$row = mysql_fetch_assoc($rs);
	if(strcmp($row["password"], $old_pwd_decrypted) != 0){
		log_message("Password Error, UID=". $current_user_id .",EMAIL=". $current_user_email .",PWD=". $row["password"] .",UPLOAD_PWD=".$old_pwd_decrypted, 'S');
		echo error2json("E131");
		die;
	}
	
        
        
        $salt = substr(uniqid(rand()), -6);
        //$new_pwd_decrypted1=  md5($AM_CURRENT_REQUEST["PASSWORD"].$salt);
        $new_pwd_decrypted1=  md5($new_pwd_decrypted.$salt);  
//	$sql = sprintf("update am_registered_user set password='%s' where id=%s", $new_pwd_decrypted, $current_user_id);
	$sql = sprintf("update `ucenter`.`uc_members` set password='%s',password1='%s',`salt`='%s' where uid=%s",$new_pwd_decrypted1, $new_pwd_decrypted,$salt, $current_user_id);
       
        if(mysql_query($sql, $conn) === FALSE){
		log_message($sql);
		echo error2json("S005");
		die;
	}
        
     
        
	echo array2json(array(
		"proto" => 20,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
	));
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
