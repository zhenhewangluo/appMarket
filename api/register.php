<?php
	require("./inc/init.php");	
	
         
    	if($AM_CURRENT_REQUEST["PROTO"] != 17){
		echo error2json("E002");
		die;
	}
	//check upload parameters

	if(!isset($_POST['model']) || !is_numeric($_POST['model'])){
		echo error2json("E004");
		die;	
	}
	if(!isset($_POST['ver']) || empty($_POST['ver'])){
		echo error2json("E005");
		die;
	}
	if(!isset($_POST['channel']) || !is_numeric($_POST['channel'])){
		echo error2json("E194");
		die;	
	}	
	if(!isset($_POST['email']) || empty($_POST['email'])){
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
	
	$AM_CURRENT_REQUEST["IMEI"]     = stopSql(__getPost('imei'));
	$AM_CURRENT_REQUEST["MODEL"]    = intval(__getPost('model'));	
	$AM_CURRENT_REQUEST["APP_VER"]  = stopSql(__getPost('ver'));
	$AM_CURRENT_REQUEST["CHANNEL"]  = intval(__getPost('channel'));
	$AM_CURRENT_REQUEST["EMAIL"]    = $email;
	if(isset($_POST['name'])){
		$AM_CURRENT_REQUEST["NAME"] = stopSql(__getPost('name'));
	} else {
		//zxg,20120409,ignore name when reg.
		$AM_CURRENT_REQUEST["NAME"] = "";//$email;
	}

       

        
//	//decrypte passw
//	if(strlen($pwd) != 64){
//		echo error2json("E127");
//		die;		
//	}	
//	$pwd  = pack('H*', $pwd);	
//	$sid = $AM_CURRENT_REQUEST["SID"];
//	$sid = str_repeat($sid, ceil(32/strlen($sid)));
//	$pwd_md5 = $pwd ^ $sid;
//	$AM_CURRENT_REQUEST["PASSWORD"] = $pwd_md5;
//	//=========================================================
        
        $AM_CURRENT_REQUEST["PASSWORD"] = $pwd;
	
	//commented by lixiaan for bajiev2
	$conn = connect_comm_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}
	$sql = "select count(*) from  `ucenter`.`uc_members`  where email='". $email ."'";
	$rs1 = mysql_query($sql, $conn);
	if($rs1 === FALSE){
		echo error2json("S002");
		die;
	}
	if(mysql_result($rs1, 0) > 0){
		echo error2json("E200");
		die;
	}	
        
        $validName= $AM_CURRENT_REQUEST["NAME"];        
        //关键词排除,昵称不能为关键字
        $audit = include('./inc/filter_word.php');     
	if($audit['open'] && $audit['keywords']){
		$replace = $audit['replace']?$audit['replace']:'***';
		$arr_keyword = explode('|', $audit['keywords']);
		foreach ( $arr_keyword as $k=>$v ){
			 $validName = str_replace($v, $replace, $validName);
                }
        }
        $con = explode("***",$name); 
        if (count($con)>1){
  		echo error2json("S600");
		die; 
        }
       
        
        $salt = substr(uniqid(rand()), -6);
        $password1=  md5($AM_CURRENT_REQUEST["PASSWORD"].$salt);
 	$sql = sprintf(
		"insert into  `ucenter`.`uc_members`(`email`,`username`,`phone`,`regtype`,`salt`,`password1`,`password`,`first_login_terminal`,`status`,regdate) values('%s','%s','',1,'%s','%s','%s',%d,1,UNIX_TIMESTAMP())",
		$AM_CURRENT_REQUEST["EMAIL"],
                $AM_CURRENT_REQUEST["NAME"],
                $salt,
		$AM_CURRENT_REQUEST["PASSWORD"],
                $password1,                
		$AM_CURRENT_REQUEST["MID"]		
		   );
        
       // die($sql);
//	
//	$sql = sprintf(
//		"insert into am_registered_user (email,name,password,registered_time,first_login_terminal,last_login_time,last_login_terminal,status) values('%s','%s','%s',NOW(),%d,NOW(),%d,1)",
//		$AM_CURRENT_REQUEST["EMAIL"],
//		$AM_CURRENT_REQUEST["NAME"],
//		$AM_CURRENT_REQUEST["PASSWORD"],
//		$AM_CURRENT_REQUEST["MID"],
//		$AM_CURRENT_REQUEST["MID"]
//		   );

	if(mysql_query($sql, $conn) === FALSE){
                echo mysql_errno();
                echo mysql_error();
                echo $sql;
//                die;
		log_message($sql, 'S');
		echo error2json("E201");
		die;
	}
	$registered_uid = mysql_insert_id();

	//�޸� am_channel_user  �� mid channel_id ��Ӧ��userid
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S100");
		die;
	}
	$sql = "update am_channel_user set user_id=".$registered_uid." where channel_id=".$AM_CURRENT_REQUEST["CHANNEL"]." and mid=".__getPost('mid');
	if(mysql_query($sql) === FALSE) {	
			log_message($sql, 'S');
			echo error2json("S002");
			die;
		}
		@mysql_close($conn);

//
	if(empty($AM_CURRENT_REQUEST["NAME"])) //return error ,need set username
	{
		echo error2json("E405");
		die;
	}
		
		$conn = connect_comm_db();
	$sql = "insert into am_login_log set uid='".$registered_uid."',mid='".__getPost('mid')."',ip='".$_SERVER['REMOTE_ADDR']."',dateline='".time()."'";
	if(mysql_query($sql) === FALSE) {	
			log_message($sql, 'S');
			echo error2json("S002");
			die;
		}
		
	//Auto logon via new session.
	session_regenerate_id();
	$new_sid = session_id();
	$_SESSION["uid"]      = $registered_uid;
	$_SESSION["username"] = $AM_CURRENT_REQUEST["EMAIL"];
	
	$sql = "update am_registered_user set last_login_sid ='$new_sid' where id=".$registered_uid;
	@mysql_query($sql);
	echo array2json(array(
		"proto" => 17,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
		"sid"   => $new_sid,
		"uid"   => $registered_uid,
	));

	@mysql_close($conn);
	if($memobj)$memobj->close();

?>


