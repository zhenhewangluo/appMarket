<?php
	/************************************************************
	 *   评分评论分离后数据的更改
     *   Author: liu jingqi
	 *   Create Time: 2010-12-16
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
		file_put_contents("am_score_log.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	$arrComment = array();
	////1.从am_comment表score导入到am_score_log表中
	/*$sql = "SELECT user_id,app_id,rate,score,create_time  FROM `am_comment` WHERE rate!='' and user_id>0 group by user_id order by `update_time` desc";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
		$row['create_time'] = strtotime($row['create_time']);
		if($row['score'] <= 0) $row['score'] = ($row['rate'] == "down")?2:5;
	    $arrComment[] = $row;
	}
	@mysql_close($conn);
	$conn = connect_comm_db();
	for($i=0; $i<count($arrComment);$i++){
		$sql = "select first_login_terminal as mid from am_registered_user where id='".$arrComment[$i]['user_id']."'";
		$rs = mysql_query($sql, $conn);
		$row = mysql_fetch_assoc($rs);
		$arrComment[$i]['mid'] = $row['mid'];
	}
	@mysql_close($conn);
	$conn = connect_db(); 
	for($i=0; $i<count($arrComment);$i++){
		if($arrComment[$i]['mid']>0){
			$sql = "select id from am_score_log where mid='".$arrComment[$i]['mid']."' and app_id='".$arrComment[$i]['app_id']."'";
			$rs = mysql_query($sql, $conn);
			$row = mysql_fetch_assoc($rs);
			if(!$row['id']){
				$sql = "insert into am_score_log set uid='".$arrComment[$i]['user_id']."',mid='".$arrComment[$i]['mid']."',app_id='".$arrComment[$i]['app_id']."',score='".$arrComment[$i]['score']."',rate='".$arrComment[$i]['rate']."',dateline='".$arrComment[$i]['create_time']."',type=0";
				$rs = mysql_query($sql, $conn);
			}
		}
	}
	
	////2.删除am_comment表中评论为空的记录
	$sql = "delete from am_comment where content = ''";
	$rs = mysql_query($sql, $conn);
	
	////3.从am_download_history_all中更新app_info 的下载量字段
	$sql = "update am_appinfo set app_downloads=0  ";		
	mysql_query($sql, $conn);
	
	$sql = "SELECT app_id, count( * ) AS c FROM `am_download_history_all` GROUP BY app_id";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
		$sql = "update am_appinfo set app_downloads='".$row['c']."' where app_id='".$row['app_id']."'";		
		mysql_query($sql, $conn);
	}*/
	
	
	////4.更新am_appinfo表total_comments字段
	$sql2 = "update am_appinfo set total_comments=0 ";
	mysql_query($sql2, $conn);
	    
	$sql = "select app_id,count(*) as c from am_comment where stat='normal' group by app_id ";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
	    $sql2 = "update am_appinfo set total_comments='".$row['c']."' where app_id=".$row['app_id'];
	    //file_put_contents("am_score.txt",$sql2."\n" , FILE_APPEND);
	    mysql_query($sql2, $conn);
	}
	
	
	/////5.更新am_appinfo 表total_score total_num字段
/*	$sql2 = "update am_appinfo set total_score=0,total_num=0 ";
	mysql_query($sql2, $conn);
	$sql = "SELECT app_id, count( * ) AS c,sum(`score`) as s FROM am_score_log GROUP BY app_id";
	$rs = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($rs)) {
	    $sql2 = "update am_appinfo set total_score='".$row['s']."',total_num='".$row['c']."' where app_id=".$row['app_id'];
	    file_put_contents("am_score.txt",$sql2."\n" , FILE_APPEND);
	    mysql_query($sql2, $conn);
	}*/
	
	echo "success";
	@mysql_close($conn);
?>
