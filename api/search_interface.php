<?php
	/************************************************************
	*   搜索
	 *	 mark: 根据关键词搜索应用列表
	     rule  关键词可以拆分 如 简介为 “加密保护与隐私号码间的通讯记录” 通过 “加密隐私”也可以搜到
		       将 关键词 拆分为 “加” “密” “隐” “私” 分别查询 应用名称 一句话描述 简介 作者名字 ，得到的
			   应用列表的交集 为最终结果集
	 *	 
	 * 新修改方案：不处理分页，按优先级顺序一次性取20个-2011.01.11
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("inc/config.inc.php");	
	require_once("inc/error.eng.php");
	require_once("inc/functions.php");
	
	if(!isset($_REQUEST["word"]) || empty($_REQUEST["word"])){
		echo error2json("E118");die;
	}
	
	$total_perpage = 20; ////显示总个数
	//$keyword = stopSql(_REQUEST["word"]); ////搜索词
	$keyword = stopSql($_REQUEST["word"]); ////搜索词

	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");die;
	}
	//查询2010.11.11 xxxxxxxxx
	$arrTestDeviceId = array(); $isflagexists = 0;
	////memcache缓存
	$screen = "480x800";
	$sdk = 8;
	$keyName = $AM_MEMCACHE["am_device_type"][0].$screen.$sdk;
	if($AM_MEMCACHE["am_device_type"][2]){
		if(!($arrTestDeviceId = $memobj->get($keyName))) $isflagexists = 2;
	}else $isflagexists = 1;
	if($isflagexists){
		$sqlD = "select app_device_type_id from am_device_type where screen='$screen' and sdk_version<='$sdk' and app_device_type_id>0";
		$rs = mysql_query($sqlD, $conn);
		while ($row = mysql_fetch_assoc($rs)) {
			$arrTestDeviceId[] = $row['app_device_type_id'];
		}
		if($isflagexists == 2) $memobj->set($keyName ,$arrTestDeviceId , 0 ,$AM_MEMCACHE["am_device_type"][1]);
	}
	if(count($arrTestDeviceId)<1) $arrTestDeviceId[] = 0;
	//================================================
	
	//=========================start=======================
	$count=strpos($keyword,"pname:");
	if($count !== false && $count == 0){
		$keyword=substr_replace($keyword,"",$count,6); 
		$packname=1;
	}
	
	if($packname){ ////搜索包
		$sql = "select count(*) from am_appinfo a join am_app_device_type b on a.app_id=b.app_id  where b.pkg_name like '%".$keyword."%'";
	}else{
		//get number of records which fit the condition for paging stuffs 
		$sql = "select count(*) from am_appinfo a where (app_name like  '%".$keyword."%' or author_name like '%".$keyword."%' or app_slogan like '%".$keyword."%' or app_desc like '%".$keyword."%'  )";
	}
	$sql_postfix = ' and app_visible in(1,2) ';
	//Add device type adapter
	
	$sql .= $sql_postfix;	
	$rs2 =  mysql_query($sql, $conn);
	if($rs2 === FALSE){
		echo error2json("S002");die;
	}	
	$app_count = mysql_result($rs2, 0);
	if($app_count == 0&&$packname!=1){
		$sql = "select * from am_hotwords_nonexistenct where keyword='". $keyword ."'";	
		if($rs = mysql_query($sql, $conn)){
			if(mysql_num_rows($rs) != 0){
				$row = mysql_fetch_assoc($rs);
				$sql = "update am_hotwords_nonexistenct set	count=count+1,updatetime='".date('Y-m-d H:i:s')."' where id=" . $row["id"];
			}else{
				$sql = "insert into am_hotwords_nonexistenct set keyword='". $keyword ."',count=1,updatetime='".date('Y-m-d H:i:s')."'";
			}
		}
		@mysql_query($sql);	
			
		//拆分关键词
		$len = mb_strlen($keyword,'utf-8'); 
		$arr = array(); 
		for($i=0;$i<$len;$i++) {
			$arr[$i] = mb_substr($keyword,$i,1,'utf-8');
		}
		if($arr){
			$arrAppid = array();
			foreach($arr as $key=>$val){
				if($total_perpage <= count($applist)) break;
				else $limit = $total_perpage-count($applist);
				$sql = "select a.*,b.icon,b.logo,b.screenshots,b.app_size,b.pkg_name from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where (a.app_name like  '%".$val."%'  or a.author_name like '%".$val."%'  or a.app_desc like '%".$val."%' )";
				$sql .= $sql_postfix;
				$sql .= ' group by a.app_id order by a.app_downloads desc ';
				$sql .= ' limit '. $limit;

				$rs = mysql_query($sql, $conn);
				if($rs === FALSE){
					echo error2json("S002");die;
				}
				while ($row = mysql_fetch_assoc($rs)) {
					if(!in_array($row['app_id'] ,$arrAppid)){
						$applist = func_applist($row , $applist);
						$arrAppid[] = $row['app_id'];
					}
				}
				@mysql_free_result($rs);
			}
		}
		$app_count = count($applist);
		if($app_count == 0){
			echo error2json("E116");die;
		}
		$aResult = $applist;
	}else{
		$sql = "select * from am_hotwords where keyword='". $keyword ."'";	
		if($rs = mysql_query($sql, $conn)){
			if(mysql_num_rows($rs) != 0){
				$row = mysql_fetch_assoc($rs);
				$sql = "update am_hotwords set	count=count+1 where id=" . $row["id"];
			}else{
				$sql = "insert into am_hotwords set keyword='". $keyword ."',count=1,`order`=99";
			}
		}
		@mysql_query($sql);	
		$sql = "select keyword from am_hotwords_nonexistenct where keyword='". $keyword ."'";	
		if($rs = mysql_query($sql, $conn)){
			if(mysql_num_rows($rs) != 0){
				$sql = "delete from am_hotwords_nonexistenct where keyword='". $keyword ."'";
				@mysql_query($sql);	
			}
		}
		
		$arrAppid = $applist = array();	
		$limit = $total_perpage;
        
		if($packname){
			$sql = "select a.*,b.icon,b.logo,b.screenshots,b.app_size,b.pkg_name from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where (b.pkg_name like  '%".$keyword."%')  ";
			$sql .= $sql_postfix;
			$sql .= " group by a.app_id order by a.app_name desc limit $limit ";
			$rs = mysql_query($sql, $conn);			
			while ($row = mysql_fetch_assoc($rs)) {
				$applist = func_applist($row , $applist);
				$arrAppid[] = $row['app_id'];
			}
		}else{
			////应用名称搜索
			$sql = "select a.*,b.icon,b.logo,b.screenshots,b.app_size,b.pkg_name from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where (a.app_name like  '%".$keyword."%') ";
			$sql .= $sql_postfix;
			$sql .= " group by a.app_id order by a.app_name desc limit $limit ";
			$rs = mysql_query($sql, $conn);			
			while ($row = mysql_fetch_assoc($rs)) {
				$applist = func_applist($row , $applist);
				$arrAppid[] = $row['app_id'];
			}
			if($total_perpage > count($applist)){
				$limit = $total_perpage-count($applist);
				////作者名搜索
				$sql = "select a.*,b.icon,b.logo,b.screenshots,b.app_size,b.pkg_name from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where (a.author_name like '%".$keyword."%')  ";
				$sql .= $sql_postfix;
				$sql .= " group by a.app_id order by a.app_name desc limit $limit";
				$rs = mysql_query($sql, $conn);
				while ($row = mysql_fetch_assoc($rs)) {
					if(!in_array($row['app_id'] , $arrAppid)){
						$applist = func_applist($row , $applist);
						$arrAppid[] = $row['app_id'];
					}
				}
			}
			if($total_perpage > count($applist)){
				$limit = $total_perpage-count($applist);
				////一句话描述
				$sql = "select a.*,b.icon,b.logo,b.screenshots,b.app_size,b.pkg_name from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where (a.app_slogan like '%".$keyword."%' )  ";
				$sql .= $sql_postfix;
				$sql .= " group by a.app_id order by a.app_name desc limit $limit";
				
				$rs = mysql_query($sql, $conn);
				while ($row = mysql_fetch_assoc($rs)) {
					if(!in_array($row['app_id'] , $arrAppid)){
						$applist = func_applist($row , $applist);
						$arrAppid[] = $row['app_id'];
					}
				}
			}	
			
			if($total_perpage > count($applist)){
				$limit = $total_perpage-count($applist);
				////简介搜索
				$sql = "select a.*,b.icon,b.logo,b.screenshots,b.app_size,b.pkg_name from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id where (a.app_desc like '%".$keyword."%' )  ";
				$sql .= $sql_postfix;
				$sql .= " group by a.app_id order by a.app_name desc limit $limit";
				
				$rs = mysql_query($sql, $conn);
				while ($row = mysql_fetch_assoc($rs)) {
					if(!in_array($row['app_id'] , $arrAppid)){
						$applist = func_applist($row , $applist);
						$arrAppid[] = $row['app_id'];
					}
				}
			}	
		}
		$aResult = $applist;
		@mysql_free_result($rs);
	}
	
	if($isflagexists == 0){
		$sql = "select * from am_hotwords where keyword='". $keyword ."'";	
		if($rs_kw = mysql_query($sql, $conn)){
			if(mysql_num_rows($rs_kw) != 0){
				$row = mysql_fetch_assoc($rs_kw);
				$sql = "select keyword from am_hotwords_nonexistenct where keyword='". $keyword ."'";	
				if($rs = mysql_query($sql, $conn)){
					if(mysql_num_rows($rs) != 0){
						$sql = "delete from am_hotwords_nonexistenct where keyword='". $keyword ."'";
						@mysql_query($sql);	
					}
				}
				$sql = "update am_hotwords set	count=count+1 where id=" . $row["id"];
			}else{
				$sql = "select * from am_hotwords_nonexistenct where keyword='". $keyword ."'";	
				if($rs = mysql_query($sql, $conn)){
					if(mysql_num_rows($rs) != 0){
						$row = mysql_fetch_assoc($rs);
						$sql = "update am_hotwords_nonexistenct set	count=count+1,updatetime='".date('Y-m-d H:i:s')."' where id=" . $row["id"];
					}else{
						$sql = "insert into am_hotwords_nonexistenct set keyword='". $keyword ."',count=1,updatetime='".date('Y-m-d H:i:s')."'";
					}
				}
			}
			@mysql_query($sql);	
		}
	}
	
	if(!$aResult){
			echo error2json("E116");die;
		}
	else
	{
		$response = array(
			"proto" => 9,
			"reqsuccess" => AM_REQUEST_SUCCESS,
			"totalpage"  => $paging ? $total_page : 1,
			"pageno"     => $paging ? $page_no    : 1,
			"list"       => $aResult,
				);
		//debug($response);
		echo array2json($response);
	}
	
	@mysql_close($conn);
	if($memobj)$memobj->close();

function func_applist($row , $applist){
	global $AM_APP_LOGO_LOC_PREFIX;
	global $AM_CATE_ICON_LOC_PREFIX;
	list($s1 , $s2) = explode("|",$row['screenshots']);
	$arr_s[0] = ($s1)?$AM_APP_LOGO_LOC_PREFIX.$s1:"";
	$arr_s[1] = ($s2)?$AM_APP_LOGO_LOC_PREFIX.$s2:"";
	$screenshots = implode("," , $arr_s);
        
                     $appdesc= str_replace("<br>","\n",$row["app_desc"]);
   
	$applist[] = array(
				"appid"      => $row["app_id"], 
				"screenshot" => $AM_APP_LOGO_LOC_PREFIX  . $row["logo"], 
				"icon"       => $AM_CATE_ICON_LOC_PREFIX . $row["icon"], 
				"name"       => $row["app_name"], 
				"author"     => $row["author_name"], 
				"short_desc" => deleteHtmlTags($row["app_slogan"]), 
				//"desc"       => $row["app_desc"], 
				"desc"       => deleteHtmlTags($appdesc), 
				"rating_up"	 => $row["app_rate_up"], 
				"rating_down"=> $row["app_rate_down"], 
				"download_cnt"=>$row["app_downloads"]*AM_DOWNLOAD_CNT, 
				"price"      => $row["app_price"] * AM_EXCHANGE_RATE, 
				"version"    => $row["version"], 
				"appver"     => $row["app_version"], 
				"infover"    => $row["infover"],
				"size"       => $row["app_size"],
				"pkg_name"   => $row["pkg_name"],
				"is_english" =>$row["is_english"],
				"total_score" =>$row["total_score"],
				"total_num" =>$row["total_num"],
				"screenshots" => $screenshots, 
				"total_rate_num" => $row["total_comments"], 
			);	
	return $applist;
}
?>
