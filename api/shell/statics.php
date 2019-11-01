<?php
	/************************************************************
	 *   统计渠道号10040 的激活量   打开次数  下载量 

     *   Author: liu jingqi
	 *   Create Time: 2011-08-04
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
		file_put_contents("statics.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	$s_date = "2011-04-27";
	$d_date = "2011-08-17";
	$m = $k = 0;
	$channel_id = 10040;
	
	$sql = "select mid  from xxxx_v2_common.am_terminal where imei!='' and channel_id ='$channel_id'";
	$result = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($result)){
		$arrMid[] = $row['mid'];
	}
	
	$arrDate = array();
	$strFile = "日期  激活量 打开数 下载量\n";
	while(1){		
		$d = date("Y-m-d" , strtotime($d_date)-3600*24*$k);
		$k++;
		if($d != $s_date){
			$i_n = $o_n = $d_n = 0;
			////激活数
			$sql = "select count(*) as c from xxxx_v2_common.am_terminal where create_time like '$d %' and imei!='' and channel_id='$channel_id' group by imei";
			$result = mysql_query($sql, $conn);
			while ($row = mysql_fetch_assoc($result)){
				$i_n++;
			}			
			$arrDate[$d]['i_n'] = $i_n;
			
			////打开次数
			$sql = "select count(*) as c from xxxx_v2_android.am_client_open_history where from_unixtime(createtime,'%Y-%m-%d')='$d' and mid in (".@implode("," , $arrMid).")";
			$result = mysql_query($sql, $conn);
			$row = mysql_fetch_assoc($result);
			$o_n = $row['c'];			
			$arrDate[$d]['o_n'] = $o_n;
			
			////下载次数
			$sql = "select count(*) as c from xxxx_v2_android.am_download_history_all  where create_time like '$d %' and mid in (".@implode("," , $arrMid).")";
			$result = mysql_query($sql, $conn);
			$row = mysql_fetch_assoc($result);
			$d_n = $row['c'];			
			$arrDate[$d]['o_n'] = $d_n;
			$strFile .= "$d  $i_n  $o_n  $d_n\n";
		}else break;
		
		if($m++ > 300) break;
	}
	
	file_put_contents("static.txt" , $strFile);
	echo "success";
?>
