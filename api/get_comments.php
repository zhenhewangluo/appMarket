<?php
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 8){
		echo error2json("E002");
		die;
	}

	if(!isset($_POST['appid'])){
		echo(error2json("E106"));
		die;
	}
	if(empty($_POST['appid']) || !is_numeric($_POST['appid'])){
		echo(error2json("E107"));
		die;
	}
	$AM_CURRENT_REQUEST["APPID"] = intval(__getPost('appid'));
	
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
		
	$sql = "SELECT * FROM am_comment WHERE app_id=". $AM_CURRENT_REQUEST["APPID"] ." ORDER BY update_time DESC";
	$commentlist = array();
	$uidlist     = array();
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");
		die;
	}else {
		while($row = mysql_fetch_assoc($rs)){
			$commentlist[] = $row;
			$uidlist[] = $row["user_id"];
		}
	}
	@mysql_free_result($rs);

	if(empty($commentlist)){
		// Added by Johnny
		// If no result found, should issue an error instead of return an empty list
		echo(error2json("E301"));
		die;
	}

	foreach($commentlist as $key=>$val) {
		//update  2010-06-12
		if(!$val['user_name'])
		{
			$connComm = connect_comm_db();
			if($connComm !== FALSE)
			{
				$sqlUser = "select id,email,name from am_registered_user where id=".$val['user_id'];
				$rs = mysql_query($sqlUser, $connComm);
				$rowUser = mysql_fetch_assoc($rs);
				if($rowUser['name'])  
				{
					$username = $rowUser['name'];
				}
				else
				{
					$aEmail = explode("@",$rowUser['email']);
					$username = $aEmail[0];
				}
			}
		}
		else
		{
			$username = $val['user_name'];
		}
		$dataarr[] = array(
			"name" => $username,
			"time" => $val["update_time"],
			"rate" => $val["rate"],
			"score"=> $val["score"],
			"comment" => $val["content"],
		);
	}
	if(count($dataarr)){	
		$json_arr = array(
			"proto" => 8,
      		"reqsuccess"  => AM_REQUEST_SUCCESS,
			"list" => $dataarr,
		);
	}else{		
		// Added by Johnny
		// If no result found, should issue an error instead of return an empty list
		echo(error2json("E301"));
		die;
	}
	echo array2json($json_arr); 
	@mysql_close($conn);
?>

