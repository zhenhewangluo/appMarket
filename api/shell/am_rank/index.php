<?php
	/*
	作者：xxxxxxxxx
	功能：人工干预排行榜
	日期：2011.07.05
	*/
	header("Content-Type:text/html;charset=utf-8");
	require("../../inc/config.inc.php");
	include(AM_SITE_ROOT ."inc/functions.php");
	
	//连接数据库
	$conn = connect_db();
	
	$do = (__getGet('do'))?__getGet('do'):"index";
	
	if($do == "index"){ ////首页列表
		$sql = "select a.*,b.app_name from am_rank_ad a left join am_appinfo b on a.app_id=b.app_id order by a.id";
		$result = mysql_query($sql, $conn);
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
			$row['strname'] = substr($row['app_name'],0,30);
			$arrR[] = $row;
		}
		include_once("index_html.php");
	}elseif($do == "add"){
		$options = "<option value='0'></option>";
		for($i=1; $i<=20; $i++){
			$options .= "<option value='".$i."'>"."第 ".$i." 位"."</option>";
		}
		include_once("add_html.php");
	}elseif($do == "addSub"){
		$app_id = __getPost('app_id'); 
		
		$sql = "select app_id from am_rank_ad where app_id='$app_id'";
		$result = mysql_query($sql, $conn);
		$row = mysql_fetch_assoc($result);
		if($row['app_id']){
			echo "<script>if(confirm('app_id已经存在，重新添加？')){window.location = 'index.php?do=add';} else{window.location = 'index.php?do=index';}</script>";die;
		}
		$sql = "select app_id from am_appinfo where app_id='$app_id'";
		$result = mysql_query($sql, $conn);
		$row = mysql_fetch_assoc($result);
		if(!$row['app_id']){
			echo "<script>if(confirm('app_id 不存在，重新添加？')){window.location = 'index.php?do=add';} else{window.location = 'index.php?do=index';}</script>";die;
		}
		
		$s_w = __getPost('s_w')?1:0; $s_w_p = $s_w.",".($s_w?__getPost('s_w_p'):0);
		$s_m = __getPost('s_m')?1:0; $s_m_p = $s_m.",".($s_m?__getPost('s_m_p'):0);
		$s_a = __getPost('s_a')?1:0; $s_a_p = $s_a.",".($s_a?__getPost('s_a_p'):0);
		
		$g_w = __getPost('g_w')?1:0; $g_w_p = $g_w.",".($g_w?__getPost('g_w_p'):0);
		$g_m = __getPost('g_m')?1:0; $g_m_p = $g_m.",".($g_m?__getPost('g_m_p'):0);
		$g_a = __getPost('g_a')?1:0; $g_a_p = $g_a.",".($g_a?__getPost('g_a_p'):0);
		
		$a_w = __getPost('a_w')?1:0; $a_w_p = $a_w.",".($a_w?__getPost('a_w_p'):0);
		$a_m = __getPost('a_m')?1:0; $a_m_p = $a_m.",".($a_m?__getPost('a_m_p'):0);
		$a_a = __getPost('a_a')?1:0; $a_a_p = $a_a.",".($a_a?__getPost('a_a_p'):0);
		
		$copyright = __getPost('copyright')?1:0;
		
		$sql = "insert into am_rank_ad set app_id='$app_id',soft_week_pos='$s_w_p',soft_month_pos='$s_m_p',soft_all_pos='$s_a_p',game_week_pos='$g_w_p',game_month_pos='$g_m_p',game_all_pos='$g_a_p',all_week_pos='$a_w_p',all_month_pos='$a_m_p',all_all_pos='$a_a_p',copyright='$copyright',dateline='".time()."'";
		$result = mysql_query($sql, $conn);
		
		header("location:?do=index");
	}elseif($do == "del"){
		$sql = "delete from am_rank_ad where app_id=".__getGet('app_id');
		$result = mysql_query($sql, $conn);
		header("location:?do=index");
	}elseif($do == "upt"){
		$sql = "select * from am_rank_ad where app_id=".__getGet('app_id');
		$result = mysql_query($sql, $conn);
		$row = mysql_fetch_assoc($result);
		
		list($row['s_w'],$row['s_w_p']) = explode("," , $row['soft_week_pos']);
		list($row['s_m'],$row['s_m_p']) = explode("," , $row['soft_month_pos']);
		list($row['s_a'],$row['s_a_p']) = explode("," , $row['soft_all_pos']);
		list($row['g_w'],$row['g_w_p']) = explode("," , $row['game_week_pos']);
		list($row['g_m'],$row['g_m_p']) = explode("," , $row['game_month_pos']);
		list($row['g_a'],$row['g_a_p']) = explode("," , $row['game_all_pos']);
		list($row['a_w'],$row['a_w_p']) = explode("," , $row['all_week_pos']);
		list($row['a_m'],$row['a_m_p']) = explode("," , $row['all_month_pos']);
		list($row['a_a'],$row['a_a_p']) = explode("," , $row['all_all_pos']);
			
		$arrOptions = array();
		for($i=1; $i<=20; $i++) $arrOptions[] = "<option value='".$i."'>"."第 ".$i." 位"."</option>";
		
		include_once("upt_html.php");
	}elseif($do == "uptSub"){
		$old_app_id = __getPost('old_app_id'); 
		$app_id = __getPost('app_id'); 
		$id = __getPost('id'); 
		if($app_id != $old_app_id){
			$sql = "select app_id from am_rank_ad where app_id='$app_id'";
			$result = mysql_query($sql, $conn);
			$row = mysql_fetch_assoc($result);
			if($row['app_id']){
				echo "<script>if(confirm('app_id已经存在，重新编辑？')){window.location = 'index.php?do=upt&app_id=$old_app_id';} else{window.location = 'index.php?do=index';}</script>";die;
			}
			$sql = "select app_id from am_appinfo where app_id='$app_id'";
			$result = mysql_query($sql, $conn);
			$row = mysql_fetch_assoc($result);
			if(!$row['app_id']){
				echo "<script>if(confirm('app_id 不存在，重新编辑？')){window.location = 'index.php?do=upt&app_id=$old_app_id';} else{window.location = 'index.php?do=index';}</script>";die;
			}
		}
		$s_w = __getPost('s_w')?1:0; $s_w_p = $s_w.",".($s_w?__getPost('s_w_p'):0);
		$s_m = __getPost('s_m')?1:0; $s_m_p = $s_m.",".($s_m?__getPost('s_m_p'):0);
		$s_a = __getPost('s_a')?1:0; $s_a_p = $s_a.",".($s_a?__getPost('s_a_p'):0);
		
		$g_w = __getPost('g_w')?1:0; $g_w_p = $g_w.",".($g_w?__getPost('g_w_p'):0);
		$g_m = __getPost('g_m')?1:0; $g_m_p = $g_m.",".($g_m?__getPost('g_m_p'):0);
		$g_a = __getPost('g_a')?1:0; $g_a_p = $g_a.",".($g_a?__getPost('g_a_p'):0);
		
		$a_w = __getPost('a_w')?1:0; $a_w_p = $a_w.",".($a_w?__getPost('a_w_p'):0);
		$a_m = __getPost('a_m')?1:0; $a_m_p = $a_m.",".($a_m?__getPost('a_m_p'):0);
		$a_a = __getPost('a_a')?1:0; $a_a_p = $a_a.",".($a_a?__getPost('a_a_p'):0);
		
		$copyright = __getPost('copyright')?1:0;
		$sql = "update am_rank_ad set app_id='$app_id',soft_week_pos='$s_w_p',soft_month_pos='$s_m_p',soft_all_pos='$s_a_p',game_week_pos='$g_w_p',game_month_pos='$g_m_p',game_all_pos='$g_a_p',all_week_pos='$a_w_p',all_month_pos='$a_m_p',all_all_pos='$a_a_p',copyright='$copyright',dateline='".time()."' where id=".__getPost('id');
		$result = mysql_query($sql, $conn);
		
		header("location:?do=index");
	}
	@mysql_close($conn);
?>
