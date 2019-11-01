<?php
	/************************************************************
	 *   统计软件排行榜，游戏排行榜，全部排行榜
     *   Author: liu jingqi
	 *   Create Time: 2010-12-07
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
		file_put_contents("am_rank.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	
	
	$sql = "select a.screen,a.sdk_version,b.app_id,c.copyright,c.app_downloads  from am_device_type a left join am_app_device_type b on a.app_device_type_id=b.id left join am_appinfo c on b.app_id=c.app_id where c.app_visible=1";
	$result = mysql_query($sql, $conn);
	$arrR = array();$j=0;
	while ($row = mysql_fetch_assoc($result)){
		$arrR[$row['app_id']][] = $row;
		$j++;
	}
	$sql = "select count(*) as c from am_rank_appinfo";
	$result = mysql_query($sql, $conn);
	$res = mysql_fetch_assoc($result);
	if( $j > 0 ){
		$sql = "select a.app_id,a.cate_id,b.parent_id from am_app_category a left join am_category b on a.cate_id=b.id where a.cate_id not in(1,2,3,4,5,6) group by a.app_id";
		$result = mysql_query($sql, $conn);
		while ($row = mysql_fetch_assoc($result)){
			for($i=0; $i<count($arrR[$row['app_id']]); $i++){
				$arrR[$row['app_id']][$i]['cate_id'] = $row['cate_id'];
				$arrR[$row['app_id']][$i]['parent_id'] = $row['parent_id'];
			}
		}
		$w = date('w',time());  //今天星期几
		$w = ($w)?($w-1):6;
		$week = date("Y-m-d" , time()-$w*3600*24);
		$sql = "select count(id) as week,app_id from am_download_history where create_time>='$week' and type <> 'import' group by app_id";
		$result = mysql_query($sql, $conn);
		while ($row = mysql_fetch_assoc($result)){
			for($i=0; $i<count($arrR[$row['app_id']]); $i++){
				$arrR[$row['app_id']][$i]['week'] = $row['week'];
			}
		}
		
		$month = date("Y-m-01"); //本月1号
		$sql = "select count(id) as month,app_id from am_download_history_month where create_time>='$month' and type <> 'import' group by app_id";
		$result = mysql_query($sql, $conn);
		while ($row = mysql_fetch_assoc($result)){
			for($i=0; $i<count($arrR[$row['app_id']]); $i++){
				$arrR[$row['app_id']][$i]['month'] = $row['month'];
			}
		}
		
		$sql = "delete from am_rank_appinfo";
		mysql_query($sql, $conn);
		foreach($arrR as $key =>$value){
			foreach($value as $k => $val){
				$sql = "insert into am_rank_appinfo set screen='$val[screen]',`sdk`='$val[sdk_version]',app_id='$val[app_id]',`copyright`='$val[copyright]',`all`='$val[app_downloads]',cate_id='$val[cate_id]',parent_id='$val[parent_id]',week='$val[week]',month='$val[month]'";
				mysql_query($sql, $conn);
			}
		}
	}
	
	////获取人工排行2011.07.07
	$sql = "select * from am_rank_ad";
	$result = mysql_query($sql, $conn);
	$arr_screen = $arr_copyright = $arr_s_w_p = $arr_s_m_p = $arr_s_a_p = $arr_g_w_p = $arr_g_m_p = $arr_g_a_p = $arr_a_w_p = $arr_a_m_p = $arr_a_a_p = array();
	while ($row = mysql_fetch_assoc($result)){
		list($row['s_w'],$row['s_w_p']) = explode("," , $row['soft_week_pos']);
		list($row['s_m'],$row['s_m_p']) = explode("," , $row['soft_month_pos']);
		list($row['s_a'],$row['s_a_p']) = explode("," , $row['soft_all_pos']);
		list($row['g_w'],$row['g_w_p']) = explode("," , $row['game_week_pos']);
		list($row['g_m'],$row['g_m_p']) = explode("," , $row['game_month_pos']);
		list($row['g_a'],$row['g_a_p']) = explode("," , $row['game_all_pos']);
		list($row['a_w'],$row['a_w_p']) = explode("," , $row['all_week_pos']);
		list($row['a_m'],$row['a_m_p']) = explode("," , $row['all_month_pos']);
		list($row['a_a'],$row['a_a_p']) = explode("," , $row['all_all_pos']);
		if($row['s_w'] && $row['s_w_p']) $arr_s_w_p[$row['app_id']] = $row['s_w_p'];
		if($row['s_m'] && $row['s_m_p']) $arr_s_m_p[$row['app_id']] = $row['s_m_p'];
		if($row['s_a'] && $row['s_a_p']) $arr_s_a_p[$row['app_id']] = $row['s_a_p'];
		
		if($row['g_w'] && $row['g_w_p']) $arr_g_w_p[$row['app_id']] = $row['g_w_p'];
		if($row['g_m'] && $row['g_m_p']) $arr_g_m_p[$row['app_id']] = $row['g_m_p'];
		if($row['g_a'] && $row['g_a_p']) $arr_g_a_p[$row['app_id']] = $row['g_a_p'];
		
		if($row['a_w'] && $row['a_w_p']) $arr_a_w_p[$row['app_id']] = $row['a_w_p'];
		if($row['a_m'] && $row['a_m_p']) $arr_a_m_p[$row['app_id']] = $row['a_m_p'];
		if($row['a_a'] && $row['a_a_p']) $arr_a_a_p[$row['app_id']] = $row['a_a_p'];
		$arr_copyright[$row['app_id']] = $row['copyright'];
		
		$sql = "select screen from am_rank_appinfo where app_id='$row[app_id]' group by screen";
		$result2 = mysql_query($sql, $conn);
		while ($row2 = mysql_fetch_assoc($result2)){
			$arr_screen[$row['app_id']][] = $row2['screen'];
		}
	}
	if($arr_s_w_p) asort($arr_s_w_p); if($arr_s_m_p) asort($arr_s_m_p); if($arr_s_a_p) asort($arr_s_a_p); 	
	if($arr_g_w_p) asort($arr_g_w_p); if($arr_g_m_p) asort($arr_g_m_p); if($arr_g_a_p) asort($arr_g_a_p); 
	if($arr_a_w_p) asort($arr_a_w_p); if($arr_a_m_p) asort($arr_a_m_p); if($arr_a_a_p) asort($arr_a_a_p); 

	////分类获取同screen,sdk,copyright
	$sql = "SELECT screen,sdk,copyright FROM `am_rank_appinfo` group by screen,sdk,copyright";
	$result = mysql_query($sql, $conn);
	$flag = 1;
	while ($row = mysql_fetch_assoc($result)){
		if($flag == 1){
			$sql = "delete from am_rank";
			mysql_query($sql, $conn);
			$flag = 0;
		}
		if($row['copyright']) $copyrightsql = "and `copyright`=1 ";
		else $copyrightsql = "";
		
		////按周排行所有的
		$sql = "select app_id from am_rank_appinfo where screen='$row[screen]' and sdk<='$row[sdk]' $copyrightsql order by week desc limit 20";
		$res = mysql_query($sql, $conn);$arrTest = array();
		while ($row2 = mysql_fetch_assoc($res)){
			$arrTest[] = $row2['app_id'];
		}

		$arrTest = func_arr($arr_a_w_p , $row , $arrTest , $arr_screen , $arr_copyright);
		$str_week_all = implode("," , $arrTest);
		
		////按周排行应用的
		$sql = "select app_id from am_rank_appinfo where screen='$row[screen]' and sdk<='$row[sdk]' $copyrightsql and parent_id=5 order by week desc limit 20";
		$res = mysql_query($sql, $conn);$arrTest = array();
		while ($row2 = mysql_fetch_assoc($res)){
			$arrTest[] = $row2['app_id'];
		}
		
		$arrTest = func_arr($arr_s_w_p , $row , $arrTest , $arr_screen , $arr_copyright);
		$str_week_soft = implode("," , $arrTest);
		
		////按周排行游戏的
		$sql = "select app_id from am_rank_appinfo where screen='$row[screen]' and sdk<='$row[sdk]' $copyrightsql and parent_id=6 order by week desc limit 20";
		$res = mysql_query($sql, $conn);$arrTest = array();
		while ($row2 = mysql_fetch_assoc($res)){
			$arrTest[] = $row2['app_id'];
		}
		$arrTest = func_arr($arr_g_w_p , $row , $arrTest , $arr_screen , $arr_copyright);
		$str_week_game = implode("," , $arrTest);
		
		$sql = "select app_id from am_rank_appinfo where screen='$row[screen]' and sdk<='$row[sdk]' $copyrightsql order by month desc limit 20";
		$res = mysql_query($sql, $conn);$arrTest = array();
		while ($row2 = mysql_fetch_assoc($res)){
			$arrTest[] = $row2['app_id'];
		}
		$arrTest = func_arr($arr_a_m_p , $row , $arrTest , $arr_screen , $arr_copyright);
		$str_month_all = implode("," , $arrTest);
		
		$sql = "select app_id from am_rank_appinfo where screen='$row[screen]' and sdk<='$row[sdk]' $copyrightsql and parent_id=5 order by month desc limit 20";
		$res = mysql_query($sql, $conn);$arrTest = array();
		while ($row2 = mysql_fetch_assoc($res)){
			$arrTest[] = $row2['app_id'];
		}
		$arrTest = func_arr($arr_s_m_p , $row , $arrTest , $arr_screen , $arr_copyright);
		$str_month_soft = implode("," , $arrTest);
		
		$sql = "select app_id from am_rank_appinfo where screen='$row[screen]' and sdk<='$row[sdk]' $copyrightsql and parent_id=6 order by month desc limit 20";
		$res = mysql_query($sql, $conn);$arrTest = array();
		while ($row2 = mysql_fetch_assoc($res)){
			$arrTest[] = $row2['app_id'];
		}
		$arrTest = func_arr($arr_g_m_p , $row , $arrTest , $arr_screen , $arr_copyright);
		$str_month_game = implode("," , $arrTest);
		
		$sql = "select app_id from am_rank_appinfo where screen='$row[screen]' and sdk<='$row[sdk]' $copyrightsql order by `all` desc limit 20";
		$res = mysql_query($sql, $conn);$arrTest = array();
		while ($row2 = mysql_fetch_assoc($res)){
			$arrTest[] = $row2['app_id'];
		}
		$arrTest = func_arr($arr_a_a_p , $row , $arrTest , $arr_screen , $arr_copyright);
		$str_all_all = implode("," , $arrTest);
		
		$sql = "select app_id from am_rank_appinfo where screen='$row[screen]' and sdk<='$row[sdk]' $copyrightsql and parent_id=5 order by `all` desc limit 20";
		$res = mysql_query($sql, $conn);$arrTest = array();
		while ($row2 = mysql_fetch_assoc($res)){
			$arrTest[] = $row2['app_id'];
		}
		$arrTest = func_arr($arr_s_a_p , $row , $arrTest , $arr_screen , $arr_copyright);
		$str_all_soft = implode("," , $arrTest);
		
		$sql = "select app_id from am_rank_appinfo where screen='$row[screen]' and sdk<='$row[sdk]' $copyrightsql and parent_id=6 order by `all` desc limit 20";
		$res = mysql_query($sql, $conn);$arrTest = array();
		while ($row2 = mysql_fetch_assoc($res)){
			$arrTest[] = $row2['app_id'];
		}
		$arrTest = func_arr($arr_g_a_p , $row , $arrTest , $arr_screen , $arr_copyright);
		$str_all_game = implode("," , $arrTest);
		
		////插入数据库
		$sql = "insert into am_rank set screen='$row[screen]',sdk='$row[sdk]',`copyright`='$row[copyright]',`type`='soft',`week`='$str_week_soft',`month`='$str_month_soft',`all`='$str_all_soft'";
		mysql_query($sql, $conn);
		$sql = "insert into am_rank set screen='$row[screen]',sdk='$row[sdk]',`copyright`='$row[copyright]',`type`='game',`week`='$str_week_game',`month`='$str_month_game',`all`='$str_all_game'";
		mysql_query($sql, $conn);
		$sql = "insert into am_rank set screen='$row[screen]',sdk='$row[sdk]',`copyright`='$row[copyright]',`type`='all',`week`='$str_week_all',`month`='$str_month_all',`all`='$str_all_all'";
		mysql_query($sql, $conn);
	}
	@mysql_close($conn);
	echo "success!";
	
	
	function func_arr(&$arr_a_w_p , &$row , $arrTest , &$arr_screen , &$arr_copyright){
		if($arr_a_w_p){
			$arrid = $arrTT = array();
			foreach($arr_a_w_p as $hz_appid => $pos){
				if($row['copyright'] && !$arr_copyright[$hz_appid]) continue;
				if($row['screen'] != '480x800' && !in_array($row['screen'] , $arr_screen["$hz_appid"])) continue;
				
				$arrTT[$pos-1] = $hz_appid;
				$arrid[] = $hz_appid;
			}
			$k = 0;
			for($m=0; $m<count($arrTest); $m++){
				if( !in_array( $arrTest[$m] , $arrid )){
					while(1){
						if($arrTT[$k]) $k++;
						else{
							$arrid[] = $arrTT[$k] = $arrTest[$m];
							$k++;break;
						}
					}
				}
			}
			$arrTest = array_unique($arrTT);
			$arrTest = array_slice($arrTest, 0 ,20);
		}
		return $arrTest;
	}
?>
