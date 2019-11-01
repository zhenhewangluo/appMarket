<?php
	/************************************************************
	 *   顶踩转化为SCORE
     *   Author: liu jingqi
	 *   Create Time: 2010-10-21
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("../inc/config.inc.php");	
	 require_once(AM_SITE_ROOT ."inc/error.eng.php");
	 
	 include(AM_SITE_ROOT ."inc/functions.php");
	 include(AM_SITE_ROOT ."inc/channel_config.php");
	 include(AM_SITE_ROOT ."inc/interface_config.php");
	 include(AM_SITE_ROOT ."inc/Log.php");	
	//连接数据库
	$conn = connect_db();  
	if($conn === FALSE){
		////记录连接数据库失败
		file_put_contents("am_comment_score_log.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}

	if(!file_exists("am_appinfo_updatelog.txt")){
		$sql = "select app_id,app_rate_up,app_rate_down from am_appinfo order by app_id desc";
		$rs = mysql_query($sql, $conn);
		while ($row = mysql_fetch_assoc($rs)) {
		    $total_score = $row['app_rate_up']*5+$row['app_rate_down']*2;
		    $total_num = $row['app_rate_up']+$row['app_rate_down'];
		    $sql2 = "update am_appinfo set total_score='$total_score',total_num='$total_num' where app_id=".$row['app_id'];
		    file_put_contents("am_appinfo_updatelog.txt",$sql2."\n" , FILE_APPEND);
		    mysql_query($sql2, $conn);
		}
		
		$sql = "select id,rate from am_comment order by id desc";
		$rs = mysql_query($sql, $conn);
		while ($row = mysql_fetch_assoc($rs)) {
		    if($row['rate'] == "up") $score = 5;
		    elseif($row['rate'] == "down") $score = 2;
		    elseif($row['rate'] >=1 && $row['rate'] <=5) $score = $row['rate'];
		    else $score = 3;
		    $sql2 = "update am_comment set score='$score' where id=".$row['id'];
		    file_put_contents("am_comment_updatelog.txt",$sql2."\n" , FILE_APPEND);
		    mysql_query($sql2, $conn);
		}
		echo "success!!!!";
	}else echo "file exists!!!!";
	@mysql_close($conn);
?>
