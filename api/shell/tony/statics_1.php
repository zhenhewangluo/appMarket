<?php
	/************************************************************
	 *   产品最近三个月的日均安装量、日均活跃用户数（活跃用户统计规则）、主要功能（TAB）日均用户量（附原因分析）

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
		file_put_contents("statics.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	$s_date = date("Y-m-d" , time()-90*3600*24);
	$d_date = date("Y-m-d" , time()-3600*24);

	$m = $k = 0;
	//$channel_id = 1;
	
	$sql = "select mid  from xxxx_v2_common.am_terminal where imei!=''";
	$result = mysql_query($sql, $conn);
	while ($row = mysql_fetch_assoc($result)){
		$arrMid[] = $row['mid'];
	}
	
	$arrDate = array();
	$strFile = "日期  安装量 活跃用户数(开启的用户为活跃用户) 主要功能用户数（主要功能为下载应用）\n";
	$i_n_all = $o_n_all = $d_n_all = 0;
	while(1){		
		$d = date("Y-m-d" , strtotime($d_date)-3600*24*$k);
		$k++;
		if($d != $s_date){
			$i_n = $o_n = $d_n = 0;
			////安装量
			$sql = "select count(*) as c from xxxx_v2_common.am_terminal where create_time like '$d %' and imei!='' group by imei";
			$result = mysql_query($sql, $conn);
			while ($row = mysql_fetch_assoc($result)){
				$i_n++;
			}			
			$arrDate[$d]['i_n'] = $i_n;
			$i_n_all += $i_n;
			
			////打开次数
			$sql = "select count(*) as c from xxxx_v2_android.am_client_open_history where from_unixtime(createtime,'%Y-%m-%d')='$d' and mid in (".@implode("," , $arrMid).") group by mid";
			$result = mysql_query($sql, $conn);
			while ($row = mysql_fetch_assoc($result)){
				$o_n++;
			}			
			$arrDate[$d]['o_n'] = $o_n;
			$o_n_all += $o_n;
			
			////下载次数
			$sql = "select count(*) as c from xxxx_v2_android.am_download_history_all  where create_time like '$d %' and mid in (".@implode("," , $arrMid).") group by mid";
			$result = mysql_query($sql, $conn);
			while ($row = mysql_fetch_assoc($result)){
				$d_n++;
			}			
			$arrDate[$d]['o_n'] = $d_n;
			$d_n_all += $d_n;
			
			$strFile .= "$d  $i_n  $o_n  $d_n\n";
		}else break;
		
		if($m++ > 300) break;
	}
	$strFile .= round($i_n_all/90)."  ".round($o_n_all/90)."  ".round($d_n_all/90)."\n";
	file_put_contents("static.txt" , $strFile);
	echo "success";
?>
