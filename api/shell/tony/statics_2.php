<?php
	/************************************************************
	 *   各渠道来源的安装量

     *   Author: liu jingqi
	 *   Create Time: 2011-08-19
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("../../inc/config.inc.php");	
	 require_once(AM_SITE_ROOT ."inc/error.eng.php");	 
	 include(AM_SITE_ROOT ."inc/functions.php");

	//连接数据库
	$conn = connect_db();
	if($conn === FALSE){
		////记录连接数据库失败
		file_put_contents("statics_2.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	$s_date = date("Y-m-d" , time()-90*3600*24);
	$d_date = date("Y-m-d" , time()-3600*24);

	$m = $k = 0;
	//$channel_id = 1;
	$strFile = "渠道名  安装量\n";
	$sql = "select channel_id,channel_name from xxxx_v2_android.am_channel order by channel_id";
	$result = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($result)){
		$i_n = 0;
		$sql = "select count(*) as c from xxxx_v2_common.am_terminal where channel_id ='".$row['channel_id']."' and imei!='' group by imei";
		$result2 = mysql_query($sql, $conn);
		while ($row2 = mysql_fetch_assoc($result2)){
			$i_n++;
		}
		$strFile .= $row['channel_name']."  ".$i_n."\n";
	}
	file_put_contents("static_2.txt" , $strFile);
	echo "success";
?>
