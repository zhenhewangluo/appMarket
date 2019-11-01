<?php
	/************************************************************
	 *   接口
     *   Author: liu jingqi
	 *   Create Time: 2011-01-04
	 delete from am_appinfo where app_id>6001781;
	 delete from am_app_category where app_id>6001781;
	 delete from am_app_device_type where app_id>6001781;
	 delete from am_device_type where app_device_type_id>7007;
	 delete from sandbox_v2_common.am_author where id>227;
	 //UPDATE smarterspider_test SET icon=REPLACE(icon, '\\', '/');
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
		file_put_contents("am_spider.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	
	$limitcount = 50;
	//获取采集应用信息
	$sql = "SELECT * FROM `smarterspider`.`smarterspider` WHERE `status`='to_be_test' and `icon` != '' and `apk_path` != '' and `screenshot_list` != ''";	
	$result = mysql_query($sql, $conn);
	$arrAmauthor = $arrAuthor = $arrAll = array(); $j = 0;
	while ($row = mysql_fetch_assoc($result)){
		if(count($arrAll) >= $limitcount) break;
		if($row['pkg_name']){////新增判断 包名存在不入库
			$sql3 = "select id from am_app_device_type where pkg_name='".$row['pkg_name']."' limit 1";
			$res3 = mysql_query($sql3);
			$row3 = mysql_fetch_assoc($res3);
			if($row3['id']) continue;
		}
		$row['author'] = trim($row['author']);
		if(!in_array($row['author'] , $arrAuthor)){
			$arrAuthor[] = $row['author'];
			$arrA12mauthor[$j]['id'] = $row['id'];
			$arrAmauthor[$j]['group_name'] = $row['author'];
			$arrAmauthor[$j]['contact_email'] = $row['author_mail'];
			$arrAmauthor[$j]['website'] = $row['author_website '];
			$arrAmauthor[$j]['phone'] = $row['author_phone'];
			$j++;
		}
		if(checkStr($row['author']) && checkStr($row['pkg_name']) && checkStr($row['app_name']) && checkStr($row['app_desc']) && checkStr($row['app_slogan'])){
			$row['is_english'] = 1;
		}else $row['is_english'] = 0;
		$arrAll[] = $row;
		$sql4 = "update `smarterspider`.`smarterspider` set `status`='toproduct' where id=".$row['id'];
		mysql_query($sql4, $conn);
		
	}

	////1.插入作者表common.am_author
	@mysql_close($conn);
	$productconnt = connect_product_db();
	if($productconnt === FALSE){
		////记录连接数据库失败
		file_put_contents("am_spider1.txt" , "time: ".date("Y-m-d H:i:s")."---- error:link error\n" ,FILE_APPEND );
	}
	$arrAuthor = array();
	for($i=0; $i<count($arrAmauthor); $i++){
		$sql = "select id from `xxxx_v2_common`.`am_author` where group_name='".$arrAmauthor[$i]['group_name']."'";
		$res = mysql_query($sql, $productconnt);
		$row = mysql_fetch_assoc($res);
		
		if($row['id']){
			$arrAuthor[$arrAmauthor[$i]['group_name']] = $row['id'];
		}else{		
			$sql = "insert into `xxxx_v2_common`.`am_author` set contact_email='".$arrAmauthor[$i]['contact_email']."',group_name='".$arrAmauthor[$i]['group_name']."',website='".$arrAmauthor[$i]['website']."',phone='".$arrAmauthor[$i]['phone']."'";	
			mysql_query($sql, $productconnt);
			$author_id = mysql_insert_id();
			$arrAuthor[$arrAmauthor[$i]['group_name']] = $author_id;
		}
	}	
	$arrAmauthor = array(); //清空数组

	
	////2.插入am_appinfo表
	for($i=0; $i<count($arrAll); $i++){
		$sql = "select app_id from am_appinfo where app_name='".mysql_escape_string($arrAll[$i]['app_name'])."'";
		$res = mysql_query($sql, $productconnt);
		if(mysql_num_rows($res) == 0){
			$arrAll[$i]['app_name'] = mysql_escape_string($arrAll[$i]['app_name']);
			$arrAll[$i]['app_slogan'] = mysql_escape_string($arrAll[$i]['app_slogan']);
			$arrAll[$i]['app_desc'] = mysql_escape_string($arrAll[$i]['app_desc']);
			$sql = "insert into am_appinfo set author_id='".$arrAuthor[$arrAll[$i]['author']]."',author_name='".$arrAll[$i]['author']."'";
			$sql .= ",app_name='".$arrAll[$i]['app_name']."',app_slogan='未认证',app_desc='".$arrAll[$i]['app_desc']."',source='".$arrAll[$i]['source']."'";
			$sql .= ",app_price=0,app_downloads=0,app_hits=0,app_rate_up=0,app_rate_down=0,total_score=0,total_num=0,total_comments=0,infover=0";
			$sql .= ",is_english=".$arrAll[$i]['is_english'].",app_visible=2,copyright=0,app_status=0";
			$sql .= ",app_version='".$arrAll[$i]['app_version']."',version='".$arrAll[$i]['version_code']."',app_create_date='".date("Y-m-d H:i:s")."',app_update_date='".date("Y-m-d H:i:s")."'";
			mysql_query($sql, $productconnt);
			$arrAll[$i]['id'] = mysql_insert_id();
		}else $arrAll[$i]['id'] = 0;
	}
	
	////3.插入am_app_category表
	$arrCate = array(
		"个人收藏与展示"=>"系统和工具","个性化"=>"系统和工具","主题"=>"系统和工具","交通"=>"生活和其他","休闲游戏"=>"益智休闲",
		"体育"=>"生活和其他","保健与健身"=>"生活和其他","健康"=>"生活和其他","动漫"=>"小说和漫画","医药"=>"生活和其他",
		"参考"=>"学习和工作","图书与工具书"=>"学习和工作","多媒体"=>"音乐和视频","天气"=>"生活和其他","娱乐"=>"生活和其他",
		"媒体与视频"=>"音乐和视频","工具"=>"系统和工具","效率"=>"学习和工作","教育"=>"学习和工作","新闻与杂志"=>"信息和资讯",
		"旅游与本地出行"=>"生活和其他","旅行"=>"生活和其他","游戏"=>"益智休闲","演示"=>"生活和其他","生活方式"=>"生活和其他",
		"益智类游戏"=>"益智休闲","社交"=>"社区和交友","纸牌游戏"=>"棋牌天地","财经"=>"金融和理财","购物"=>"生活和其他",
		"资讯和天气"=>"信息和资讯","软件库"=>"系统和工具","通信"=>"通讯和聊天","音乐与音频"=>"音乐和视频"
	);
	$arrCate_NDUO = array("日常"=>"生活和其他","系统"=>"系统和工具","电话/短信"=>"通讯和聊天","无线/网络"=>"网络和浏览",
		"聊天/IM"=>"通讯和聊天","浏览器"=>"网络和浏览","多媒体"=>"音乐和视频","拍照"=>"系统和工具",
		"图像"=>"系统和工具","地图"=>"系统和工具","趣味"=>"生活和其他","字典"=>"学习和工作",
		"阅读"=>"小说和漫画","输入法"=>"系统和工具","网络"=>"网络和浏览","插件"=>"系统和工具",
		"社区"=>"社区和交友","资讯"=>"信息和资讯","微博"=>"社区和交友","趣味"=>"益智休闲",
		"角色"=>"角色扮演","动作"=>"动作冒险","竞速"=>"体育竞速","射击"=>"飞行射击",
		"运动"=>"体育竞速","益智"=>"益智休闲","棋牌"=>"棋牌天地","策略"=>"经营策略",
		"网游"=>"网络游戏","模拟器"=>"网络游戏"
	);
	for($i=0; $i<count($arrAll); $i++){
		if($arrAll[$i]['id'] == 0) continue;
		if($arrAll[$i]['source'] == "Google") $strcate = $arrCate[$arrAll[$i]['category']];
		elseif($arrAll[$i]['source'] == "Nduo") $strcate = $arrCate_NDUO[$arrAll[$i]['category']];
		else continue;
		$sql = "select id from am_category where name='".$strcate."'";
		$res = mysql_query($sql, $productconnt);
		$row = mysql_fetch_assoc($res);
		if($row['id']<=0) $row['id']=69;
		$sql = "insert into am_app_category set app_id='".$arrAll[$i]['id']."',`cate_id`='".$row['id']."',`order`=99";
		mysql_query($sql, $productconnt);
	}

	////4.插入am_app_device_type表
	$arrAuthor = array();
	for($i=0; $i<count($arrAll); $i++){
		if($arrAll[$i]['id'] == 0) continue;
		$sql = "select id,apk_path,pkg_name from am_app_device_type where app_id='".$arrAll[$i]['id']."'";
		$res = mysql_query($sql, $productconnt);
		$row = mysql_fetch_assoc($res);
		if($row['id']){
			if(!$row['apk_path']){
				/*$sql = "select * from `smarterspider`.`app_source_info` where pkg_name='".$row['pkg_name']."'";
				$res2 = mysql_query($sql, $conn);
				$row2 = mysql_fetch_assoc($res2);*/
				if($row2['apk_path']){
					$sql = "update am_app_device_type set apk_path='".$arrAll[$i]['apk_path']."',icon='".$arrAll[$i]['icon']."',logo='".$arrAll[$i]['logo']."',screenshots='".$arrAll[$i]['screenshot_list']."' where id=".$row['id'];
					mysql_query($sql, $productconnt);
				}
			}
			$arrAuthor[$arrAll[$i][id]] = $row['id'];
		}else{
			/*$sql = "select * from `smarterspider`.`app_source_info` where pkg_name='".$arrAll[$i]['pkg_name']."'";
			$res2 = mysql_query($sql, $conn);
			$row2 = mysql_fetch_assoc($res2);*/
			//$arrAll[$i]['app_size'] = ($files = filesize(AM_SITE_ROOT."res/".$row2['apk_path']))?$files:$arrAll[$i]['app_size'];
			$sql = "insert into am_app_device_type set app_id='".$arrAll[$i]['id']."',`device_type_id`=1,`apk_path`='".$arrAll[$i]['apk_path']."',icon='".$arrAll[$i]['icon']."',logo='".$arrAll[$i]['logo']."',screenshots='".$arrAll[$i]['screenshot_list']."',pkg_name='".$arrAll[$i]['pkg_name']."',app_size='".$arrAll[$i]['app_size']."'";
			mysql_query($sql, $productconnt);
			$id = mysql_insert_id();
			$arrAuthor[$arrAll[$i][id]] = $id;
		}
	}

	////5.插入am_device_type
	$arrScreen = array("240x320","240x400","320x480","360x640","480x800","480x854");
	for($i=0; $i<count($arrAll); $i++){
		if($arrAll[$i]['id'] == 0) continue;
		$sql = "select * from am_device_type where app_device_type_id=".$arrAuthor[$arrAll[$i]['id']];
		$res = mysql_query($sql, $productconnt);
		if(mysql_num_rows($res) == 0){
			for($j=0; $j<count($arrScreen); $j++){
				$sql = "insert into am_device_type set screen='".$arrScreen[$j]."',sdk_version='3',app_device_type_id ='".$arrAuthor[$arrAll[$i]['id']]."'";
				mysql_query($sql, $productconnt);
			}
		}
	}
	@mysql_close($productconnt);
	echo "success!";
	
function checkStr($str){
    $output = false;
    if(!$str) return $output;
    $a=ereg('['.chr(0xa1).'-'.chr(0xff).']', $str);
    $b=ereg('[0-9]', $str);
    $c=ereg('[a-zA-Z]', $str);
    if($a && $b && $c){  //汉字数字英文的混合字符串
    	$output = false;
    }elseif($a && $b && !$c){ //汉字数字的混合字符串
    	$output = false;
    }elseif($a && !$b && $c){ //汉字英文的混合字符串
    	$output = false;
    }elseif(!$a && $b && $c){ //数字英文的混合字符串
    	$output = true;
    }elseif($a && !$b && !$c){ //纯汉字
    	$output = false;
    }elseif(!$a && $b && !$c){ //纯数字
    	$output = true;
    }elseif(!$a && !$b && $c){ //纯英文
    	$output = true;
    }
    return $output;
}

function connect_product_db(){
	global $AM_COMMON_INFO_DATABASE;
	$conn = @mysql_connect("218.246.22.92", "houhou", "houhou123");
	if($conn === FALSE){
		return FALSE;
	}

	if (!mysql_select_db("xxxx_v2_android", $conn)){
		return FALSE;
	}

	if(!mysql_query("SET NAMES UTF8", $conn)){
		return FALSE;
	}
	return $conn;		
}
?>
