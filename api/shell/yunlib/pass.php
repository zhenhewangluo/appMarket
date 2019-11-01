<?php
session_start();
header("Content-Type:text/html;charset=utf-8");
require_once("configs.php");
if(!empty($_POST['username'])&& !empty($_POST['password'])){
	$adminname =  $_POST['username'];
    $adminpass = trim($_POST['password']);
    if(in_array($adminname,$username_array))$username=$adminname;
	if(in_array($adminpass,$password_array))$id=$adminpass;
	
    if($id&&$username)
	{ 
        $_SESSION['uid']   = $id;
        $_SESSION['username'] = $username; 
		
		echo "<script>window.location.href='main.php'</script>";
		exit;
    }else{
		
		echo '<script>alert("密码或用户名错误,请联系管理员");</script>';
		echo "<script>window.location.href='index.php'</script>";
		exit;
    }    
}

   
?>