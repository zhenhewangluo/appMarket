<?php
	require("./inc/init.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 21){
		echo error2json("E002");
		die;
	}
	//0=both,1=phone,2=name
	$type =  intval(stopSql(__getPost('type')));
	if($type<0||$type>2)
		$type=0;
	
	
	if(isset($_POST['name']) && !empty($_POST['name'])) {
		$name =  stopSql(__getPost('name'));
	}
	if(isset($_POST["phone"]) && !empty($_POST["phone"])){
		$phone =  stopSql(__getPost("phone"));
		if(!is_phone_valid($phone))
		{
			echo error2json("E209");
			die;
		}
	}
           
        //关键词排除,昵称不能为关键字
        $audit = include('./inc/filter_word.php');     
		if($audit['open'] && $audit['keywords']){
		$replace = $audit['replace']?$audit['replace']:'***';
		$arr_keyword = explode('|', $audit['keywords']);
		foreach ( $arr_keyword as $k=>$v ){
			 $name = str_replace($v, $replace, $name);
                }
        }
        $con = explode("***",$name); 
        if (count($con)>1){
  		echo error2json("S600");
		die; 
        }
     
        
        
        

	//if(get_str_length($name) 
	/*check mobilephone number 
	 * update by bluesie at 2010-6-24  15
	 */
	
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
	// Update name by UID
	if(mysql_num_rows($rs) == 0){
		echo error2json("E130");
		die;
	}else{
		$row = mysql_fetch_assoc($rs);

		// validate phone
		if($type==0||$type==1)
		{
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
		}

		// validate name
		if($type==0||$type==1)
		{
			if($name!=$row['name'])
			{
				$sql = "select id from am_registered_user where name='".$name."' and id <>". $AM_CURRENT_REQUEST["UID"];	
				$rs = mysql_query($sql, $conn);
				if($rs === FALSE){
					echo error2json("S002");
					die;
				}
				if(mysql_num_rows($rs) != 0){
					echo error2json("S600");
					die;
				}	
			}
		}
		
		
		//zxg,20120405,
//		$update_name_sql = "update am_registered_user set name='". $name ."',phone='".$phone."' where id=". $AM_CURRENT_REQUEST["UID"];		
		switch($type)
		{
			case 1://only phone
				$update_name_sql = "update `_kid_ucenter`.`uc_members` set `phone`='".$phone."' where `uid`=". $AM_CURRENT_REQUEST["UID"];		
				
				break;
			case 2://only name
				$update_name_sql = "update `_kid_ucenter`.`uc_members` set `username`='". $name ."' where `uid`=". $AM_CURRENT_REQUEST["UID"];		
				
				break;
			default://both
				$update_name_sql = "update `_kid_ucenter`.`uc_members` set `username`='". $name ."',`phone`='".$phone."' where `uid`=". $AM_CURRENT_REQUEST["UID"];		
				
				break;
					
		}
		
	
		if(mysql_query($update_name_sql, $conn) === FALSE){
			echo error2json("S005");
			die;
		}
		$log = sprintf("[USER INFO CHANGE]UID=%s,OLD_NAME=%s,NEW_NAME=%s,OLD_PHONE=%s,NEW_PHONE=%s", $row["id"], $row["name"], $name,$row['phone'],$phone);
		log_message($log, 'I');
	}

/*
 *  ��ݱ�ṹ���޸ģ����Ķ� am_terminal ��
 *  bluesie  2010-06-09
 *
	// validate mid
	$sql = "select * from am_terminal where mid=". $AM_CURRENT_REQUEST["MID"];	
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");
		die;
	}
	// Update phone by MID
	if(mysql_num_rows($rs) == 0){
		echo error2json("E130");
		die;
	}else{		
		$row = mysql_fetch_assoc($rs);

		$update_phone_sql = "update am_terminal set phone='". $phone ."' where mid=". $AM_CURRENT_REQUEST["MID"];		
		if(mysql_query($update_phone_sql, $conn) === FALSE){
			echo error2json("S005");
			die;
		}
		$log = sprintf("[USER INFO CHANGE]MID=%s,OLD_PHONE=%s,NEW_PHONE=%s", $row["mid"], $row["phone"], $phone);
		log_message($log, 'I');
	}
 */
	echo array2json(array(
		"proto" => 21,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
	));
	
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
