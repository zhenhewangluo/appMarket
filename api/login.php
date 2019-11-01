<?php

require("./inc/init.php");

if ($AM_CURRENT_REQUEST["PROTO"] != 18)
{
	echo error2json("E002");
	die;
}
$email = stopSql(__getPost('email'));
$phone = stopSql(__getPost('phone'));
$name = stopSql(__getPost('name'));
$type = intval(stopSql(__getPost('type')));

//0 email login,1 phone login,2 set name and login by email,3 set name and login by phone
if ($type < 0 || $type > 3)
	$type = 0;

if ($type == 0 || $type == 2)
{
	if (!isset($_POST['email']) && empty($_POST['email']))
	{
		echo error2json("E198");
		die;
	}
	if (!is_mail_valid($email))
	{
		echo error2json("E198");
		die;
	}
}
else
{
	if (!isset($_POST['phone']) && empty($_POST['phone']))
	{
		echo error2json("E209", true);
		die;
	}
	if (!is_phone_valid($phone))
	{
		echo error2json("E209", true);
		die;
	}
}
if (!isset($_POST['passwd']) || empty($_POST['passwd']))
{
	echo error2json("E127");
	die;
}




$pwd = trim(__getPost('passwd'));
//	if(strlen($pwd) != 64){
//		echo error2json("E127");
//		die;		
//	}	
//	$pwd  = pack('H*', $pwd);	
//	$sid = $AM_CURRENT_REQUEST["SID"];
//	$sid = str_repeat($sid, ceil(32/strlen($sid)));
//	$pwd_md5 = $pwd ^ $sid;


$pwd_md5 = $pwd;


//commented by lixiaan for bajiev2
$conn = connect_comm_db();
if ($conn === FALSE)
{
	echo error2json("S100");
	die;
}

if ($type == 0 || $type == 2)
	$sql = "select * from am_registered_user where email='" . $email . "' and status=1";
else
	$sql = "select * from am_registered_user where phone='" . $phone . "' and status=1";
$rs = mysql_query($sql, $conn);
if ($rs === FALSE)
{
	echo error2json("S002");
	die;
}

//NOT FOUND
if (mysql_num_rows($rs) == 0)
{
	echo error2json("E130");
	die;
}
//Password error
$row = mysql_fetch_assoc($rs);
if (strcmp($row["password"], $pwd_md5) != 0)
{
	echo error2json("E131");
	die;
}

//get available balance
$balance = is_numeric($row["balance"]) ? $row["balance"] : 0;
$stop_amount = is_numeric($row["stop_amount"]) ? $row["stop_amount"] : 0;
$available = $balance - $stop_amount;
if ($available < 0)
{
	$available = 0;
}

$score = is_numeric($row["score"]) ? $row["score"] : 0;

//2010-3-9, exchange rate between money & virtual internal balance.
$available = $available * AM_EXCHANGE_RATE;
//username
//zxg,20120407,if username null return fail,neet set name.


if ($row['name']) //已存在，忽略$name 
{
	$username = $row['name'];
}
else
{
	if ($type == 2 || $type == 3)//set name
	{
		//关键词排除,昵称不能为关键字
		$audit = include('./inc/filter_word.php');
		if ($audit['open'] && $audit['keywords'])
		{
			$replace = $audit['replace'] ? $audit['replace'] : '***';
			$arr_keyword = explode('|', $audit['keywords']);
			foreach ($arr_keyword as $k => $v)
			{
				$name = str_replace($v, $replace, $name);
			}
		}
		$con = explode("***", $name);
		if (count($con) > 1)
		{
			echo error2json("S600");
			die;
		}
		$sql = "select id from am_registered_user where name='" . $name . "' and id <>" . $row["id"];
		$rs = mysql_query($sql, $conn);
		if ($rs === FALSE)
		{
			echo error2json("S002");
			die;
		}
		if (mysql_num_rows($rs) != 0)
		{
			echo error2json("S600");
			die;
		}
		$update_name_sql = "update `ucenter`.`uc_members` set `username`='" . $name . "' where `uid`=" . $row["id"];
		if (mysql_query($update_name_sql, $conn) === FALSE)
		{
			echo error2json("S005");
			die;
		}
		$username = $name;
	}
	else //return error ,need set username
	{
		echo error2json("E405");
		die;
	}
//		$aEmail = explode("@",$row['email']);
//		$username = $aEmail[0];
}
$login_user = array(
	"uid" => $row["id"],
	"email" => $row["email"],
	"phone" => $row["phone"],
	"name" => $username,
	"balance" => $available,
	"score" => $score,
);

//Auto logon via new session.
# Commented due to:
# 	<b>Warning</b>:  session_regenerate_id() [<a href='function.session-regenerate-id'>function.session-regenerate-id</a>]: Cannot regenerate session id - headers already sent in <b>/home/vhosts/xxxx365.com/htdocs/android/v2/sandroid/login.php</b> on line <b>80</b><br />
session_regenerate_id();
$new_sid = session_id();

$_SESSION["username"] = $row["email"]; //$email;
$_SESSION["uid"] = $row["id"];
$_SESSION["password"] = $pwd_md5;
log_message(
		sprintf("[LOGIN]CHANNEL=%s,MODEL=%s,MID=%s,UID=%s", $AM_CURRENT_REQUEST["CHANNEL"], $AM_CURRENT_REQUEST["MODEL"], $AM_CURRENT_REQUEST["MID"], $login_user["uid"]), 'I');

//update login information
$sql = sprintf("update am_registered_user set last_login_terminal=%d, last_login_time=NOW(),last_login_sid='%s' where id=%d", $AM_CURRENT_REQUEST["MID"], $new_sid, $login_user["uid"]);

if (mysql_query($sql) === FALSE)
{
	log_message($sql, 'S');
	echo error2json('S005');
	die;
}

$sql = "insert into am_login_log set uid='" . $login_user["uid"] . "',mid='" . $AM_CURRENT_REQUEST["MID"] . "',ip='" . $_SERVER['REMOTE_ADDR'] . "',dateline='" . time() . "'";
if (mysql_query($sql) === FALSE)
{
	log_message($sql, 'S');
	echo error2json("S002");
	die;
}
echo array2json(array(
	"proto" => 18,
	"reqsuccess" => AM_REQUEST_SUCCESS,
	"uid" => $login_user["uid"],
	"sid" => $new_sid,
	"name" => $login_user["name"],
	"email" => $login_user["email"],
	"phone" => $login_user["phone"],
	"balance" => $login_user["balance"],
	"score" => $login_user["score"],
));

@mysql_close($conn);
if ($memobj)
	$memobj->close();
?>

