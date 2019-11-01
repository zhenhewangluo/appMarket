<?php

	require("./inc/init.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 56){
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
//session_id(__getPost("sid"));
//check time
	if(isset($_SESSION["svTime"])&&(time() - $_SESSION["svTime"] < 60))
	{
			echo error2json("E406");//send_validate_fail_frequently
			die;
	}


	$_SESSION['mobileCode'] = sprintf("%04d",mt_rand(1,9999));
	$_SESSION['mobileNum'] = $phone;

	$data = array(
		'phone'=>$phone,
		'messageContent'=>$_SESSION['mobileCode']
		);
		$re = postCurl("http://www.hjapk.com/UserCenter/index.php?m=DUser&a=addSmMt", $data);
	
	//$re = json_decode($re, true);	
	if(!empty($re) && isset($re['result']))
	{
		if($re['result'] == 1)
		{

			$_SESSION["svTime"] = time();
			echo array2json(array(
				"proto" => 56,
				"reqsuccess" =>  AM_REQUEST_SUCCESS,
				'code'=>$_SESSION['mobileCode'].$re['result']
			));
			die;
		}
	}
	
	echo error2json_withlog("E407",true,$_SESSION['mobileCode'].$re['result']);//send_validate_fail


?>
