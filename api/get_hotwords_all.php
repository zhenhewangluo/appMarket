<?php
	/************************************************************
	 *   Interface 6
     *   获取分类列表 get_cate_list  require  get_commend_list.php
	 *
     *   Author:  bluesie
	 *	 mark: 根据用户下载应用的数据推荐应用列表
	     rule  根据mid 查询  am_download_history表，获得此mid下载的应用个数 $count
		       如果 $count <10 ,返回 am_app_category 中所有cate的一条应用 total=30+-
			   否则
	           获取此mid下载的应用 app1,app2....
			   查询与app1,app2,app3 属于相同专题的应用 app1_1,app2_1,app3_1..
			   过滤后$num++ ，如果 num 不够20，
			   查询与app1,app2,app3 属于相同作者的应用 app1_2,app2_2,app3_2..
			   过滤后$num++ ，如果 num 不够20，
			   查询与app1,app2,app3 属于相同分类的应用 app1_3,app2_3,app3_3..
			   过滤后$num++
	 *	 paras  proto  uid  sid  mid 	  
	 *************************************************************/
	//require("./inc/init.php");	
	//require("./inc/functions.php");
	//数据库连接
	define("TOTAL_KEYWORDS",100);

	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	//查询  am_hotwords 表，获得 TOTAL_KEYWORDS hotwords
	$mid = intval(__getPost('mid'));
	////memcache
		$keyName = $AM_MEMCACHE["am_hotwords"][0].TOTAL_KEYWORDS;
		$isflagexists = 0;
		if($AM_MEMCACHE["am_hotwords"][2]){
			if(!($aHotword = $memobj->get($keyName))) $isflagexists = 2;
		}else $isflagexists = 1;
		if($isflagexists){				
			$sql = "select keyword from am_hotwords order by `order` asc,count desc limit ".TOTAL_KEYWORDS;
			$result = mysql_query($sql, $conn);
			while ($row = mysql_fetch_assoc($result))	{
				$aHotword[] = $row['keyword'];
			}
			if($isflagexists == 2)$memobj->set($keyName ,$aHotword , 0 ,$AM_MEMCACHE["am_hotwords"][1]);
		}
	////判断是否云库渠道
	if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
		$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['hotwords']) , true);
		if($arrYunlibNew){
			$aHotword = array_unique(array_merge($arrYunlibNew , $aHotword));
			$aHotword = array_slice($aHotword , 0 , TOTAL_KEYWORDS);
		}
	}
	 $strHot = @join(",",$aHotword);
	// echo $strHot;exit();
?>
