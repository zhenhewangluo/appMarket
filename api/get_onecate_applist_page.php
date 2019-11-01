<?php
	/************************************************************
	 *   Interface 47
     *   获取某个分类下 的应用详细信息 分页
	 *   
     *   Author: xxxxxxxxx
	 *   Create Time: 2011-01-11
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	
    if($AM_CURRENT_REQUEST["PROTO"] != 47){
		echo error2json("E002");
		die;
	}

	//判断是否传值班screen,sdk---2010.11.11
	if(!($screen=__getPost('screen_size')) || !($sdk=__getPost('sdk'))){
			$conn = connect_comm_db();
			$res = mysql_query("select screen_size,sdk from am_terminal_info where mid=". $mid, $conn);		
			if(mysql_num_rows($res) == 0){
				$screen = "480x800";
				$sdk = 4;
			}else{
				$result = mysql_fetch_assoc($res);
				$screen	= $result['screen_size'];
				$sdk	= $result['sdk'];
			}
			mysql_close($conn);
	}
	
	
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}	
	//查询2010.11.11 xxxxxxxxx
	$arrTestDeviceId = array(); $isflagexists = 0;
	////memcache缓存
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
	
	////如果是TCL接口，判断正版
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)) {
		$sqlTcl ="";// " and am_appinfo.copyright=1 ";
		$xxxx_iscopyright = 1;
	}
	
	//分类
	$cate_id = intval(__getPost('cateid'));
	$type = intval(__getPost('type'));
	$orderType  = (intval(__getPost('orderType')))?intval(__getPost('orderType')):1;
	$pageNo  = (intval(__getPost('pageNo')))?intval(__getPost('pageNo')):1;
	$perpage  = (intval(__getPost('perpage')))?intval(__getPost('perpage')):10;
	$offset = ($pageNo-1)*$perpage;
	$applist = "";
	if($type == 1){ ////首页，包括猜你喜欢，下载动态和最新
		$icon = $xxxxArrCategoryType["xxxx_".$cate_id]['icon'];
		$name = $xxxxArrCategoryType["xxxx_".$cate_id]['name'];
		$desc = $xxxxArrCategoryType["xxxx_".$cate_id]['desc'];
		if($cate_id == '9994'){
			if($xxxx_iscopyright){
				$sqlAllApp = "SELECT distinct am_app_topic.app_id FROM am_app_topic JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1 $sqlTcl 
							AND am_app_topic.topic_id =102";
			    $sqlAllApp .= " order by am_app_topic.order,am_appinfo.app_update_date desc,am_appinfo.app_id desc";
			    $rsAppTime = mysql_query($sqlAllApp, $conn);
				$arrAppidTime = array();
				while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
					$arrAppidTime[] = $rowAppTime['app_id'];
				}
				$applist = implode("," , $arrAppidTime);
			}else{
				////20110301 判断该渠道是否允许显示VISIBLE=2的应用
				//if(in_array($_SESSION["channel"], $xxxxnopermitChannelid)) $sql_postfix = " and a.app_id in(select app_id from am_appinfo where app_visible =1)";
				//else $sql_postfix = ' ';
				//20120313 zxg ,must use visible=1
				$sql_postfix = " and a.app_id in(select app_id from am_appinfo where app_visible = 1)";				
				$sqlAllApp = "select a.app_id,a.end_time,a.user_id,a.mid from am_download_history a left join am_app_device_type b on a.app_id = b.app_id where a.mid <>".$mid." and a.status = 1 and a.type <> 'import' $sql_postfix group by a.app_id order by a.end_time desc limit 50";
				$result = mysql_query($sqlAllApp, $conn);
				$i = 0; $arrUsername = $arrMid = $aTime = array();
				while ($row = mysql_fetch_assoc($result)){
					$aDownAppid[$i] = $row['app_id'];
					$aTime[$i] = strtotime($row['end_time']);
					$arrMid[$i]['user_id'] = $row['user_id'];
					$arrMid[$i]['mid'] = $row['mid'];
					$i++;
				}
				
				$channelName = array("26"=>"互联天地","27"=>"云库","10001"=>"蝌蚪");
				if($channelName[$AM_CURRENT_REQUEST["CHANNEL"]]){
					$cname = $channelName[$AM_CURRENT_REQUEST["CHANNEL"]];
				}else $cname = "欢聚宝";
				
				@mysql_close($conn);
				$conn = connect_comm_db();
				for($k=0; $k<count($arrMid); $k++){
					if($arrMid[$k]['user_id']>0){
						$sql = "select name from am_registered_user where id=".$arrMid[$k]['user_id'];
						$result = mysql_query($sql, $conn);
						$row = mysql_fetch_assoc($result);
						if($row['name']) $arrUsername[] = $row['name'];
						else {
							$arrUsername[] = (intval($arrMid[$k]['mid']))?$cname.$arrMid[$k]['mid']:"xxxx8.com";
						}
					}else{
						$arrUsername[] = (intval($arrMid[$k]['mid']))?$cname.$arrMid[$k]['mid']:"xxxx8.com";
					}
				}
				$count = count($aDownAppid);
				$applist = join("," , $aDownAppid);
				$timelist = join("," , $aTime);
				$namelist = join("," , $arrUsername);
			}
		}elseif($cate_id == '9995'){
			require('get_commend_list.php'); 
			$applist = $commendList[0]['time_applist'];
		}elseif($cate_id == '3'){
			$sqlAllApp = "SELECT distinct am_app_category.app_id FROM am_app_category JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1 $sqlTcl 
							AND cate_id =".$cate_id;
			 if($orderType == 1) $sqlAllApp .= " order by am_app_category.order,am_appinfo.app_update_date desc,am_appinfo.app_id desc limit $offset,$perpage";
			 else 				 $sqlAllApp .= " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit $offset,$perpage";
			 $rsAppTime = mysql_query($sqlAllApp, $conn);
			 $arrAppidTime = array();
			 while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
					$arrAppidTime[] = $rowAppTime['app_id'];
			 }
			 ////判断是否云库渠道
			if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
				$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['new']) , true);
				if($arrYunlibNew['yunlib_3']){
					$aAppid = $arrYunlibNew['yunlib_3'];
				}
				if($offset == 0 && $aAppid){
					$arrAppidTime = array_unique(array_merge($aAppid , $arrAppidTime));
				}
			}
			 $applist = implode("," , $arrAppidTime);
		}
	}elseif($type == 2){ ////分类
		if($cate_id == 9998){
			$parentid = $xxxxArrCategoryType["xxxx_".$cate_id]['parentid'];
			$sqlAllApp = "SELECT distinct am_app_category.app_id FROM am_app_category JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
					WHERE am_appinfo.app_visible =1 $sqlTcl  AND cate_id in(select id from am_category where parent_id = $parentid and visible=1)";
			if($orderType == 1) $sqlAllApp .= " order by am_app_category.order,am_appinfo.app_update_date desc,am_appinfo.app_id desc limit $offset,$perpage";
		 	else 				 $sqlAllApp .= " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit $offset,$perpage";
		 	$rsAppTime = mysql_query($sqlAllApp, $conn);
			 $arrAppidTime = array();
			 while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
					$arrAppidTime[] = $rowAppTime['app_id'];
			 }
			 $applist = implode("," , $arrAppidTime);
		}elseif($cate_id == 9999){
			$parentid = $xxxxArrCategoryType["xxxx_".$cate_id]['parentid'];
			$sqlAllApp = "SELECT distinct am_app_category.app_id FROM am_app_category JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
					WHERE am_appinfo.app_visible =1 $sqlTcl AND cate_id in(select id from am_category where parent_id = $parentid and visible=1)";
			if($orderType == 1) $sqlAllApp .= " order by am_app_category.order,am_appinfo.app_update_date desc,am_appinfo.app_id desc limit $offset,$perpage";
		 	else 				 $sqlAllApp .= " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit $offset,$perpage";
		 	$rsAppTime = mysql_query($sqlAllApp, $conn);
			 $arrAppidTime = array();
			 while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
					$arrAppidTime[] = $rowAppTime['app_id'];
			 }
			 $applist = implode("," , $arrAppidTime);
		}else{
			$sqlAllApp = "SELECT distinct am_app_category.app_id FROM am_app_category JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1 $sqlTcl 
							AND cate_id =".$cate_id;
			 if($orderType == 1) $sqlAllApp .= " order by am_app_category.order,am_appinfo.app_update_date desc,am_appinfo.app_id desc limit $offset,$perpage";
			 else 				 $sqlAllApp .= " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit $offset,$perpage";
			 $rsAppTime = mysql_query($sqlAllApp, $conn);
			 $arrAppidTime = array();
			 while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
					$arrAppidTime[] = $rowAppTime['app_id'];
			 }
			 ////判断是否云库渠道
			 if($orderType == 1 && in_array($_SESSION["channel"], $xxxxYunlibChannelid) && $offset == 0){
			 	 $arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['cate']) , true);
				if($arrYunlibNew['yunlib_'.$cate_id]){
					$aAppid = $arrYunlibNew['yunlib_'.$cate_id];
				}
				if($aAppid){
					$arrAppidTime = array_unique(array_merge($aAppid , $arrAppidTime));
				}
			 }
			 $applist = implode("," , $arrAppidTime);
		}
	}elseif($type == 3){ //专题		
		$sqlAllApp = "SELECT distinct am_app_topic.app_id FROM am_app_topic JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id
						WHERE am_appinfo.app_visible =1 $sqlTcl 
						AND topic_id =".$cate_id;
		 if($orderType == 1) $sqlAllApp .= " order by am_app_topic.order,am_appinfo.app_update_date desc,am_appinfo.app_id desc limit $offset,$perpage";
		 else 				 $sqlAllApp .= " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit $offset,$perpage";
		 $rsAppTime = mysql_query($sqlAllApp, $conn);
		 $arrAppidTime = array();
		 while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
				$arrAppidTime[] = $rowAppTime['app_id'];
		 }
		 ////判断是否云库渠道
		 if($orderType == 1 && in_array($_SESSION["channel"], $xxxxYunlibChannelid) && $offset == 0){
		 	 $arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['topic']) , true);
			if($arrYunlibNew['yunlib_'.$cate_id]){
				$aAppid = $arrYunlibNew['yunlib_'.$cate_id];
			}
			if($aAppid){
				$arrAppidTime = array_unique(array_merge($aAppid , $arrAppidTime));
			}
		 }
			 
		 $applist = implode("," , $arrAppidTime);		
	}elseif($type == 4){////排行
		$copyright = ($xxxx_iscopyright)?1:0;
		if($cate_id == 10001){
			$sqlrank = "select * from am_rank where type='soft' and `screen`='$screen' and `sdk`<='$sdk' and `copyright`=".$copyright." order by `sdk` desc";
		}elseif($cate_id == 10002){
			$sqlrank = "select * from am_rank where type='game' and `screen`='$screen' and `sdk`<='$sdk' and `copyright`=".$copyright." order by `sdk` desc";
		}elseif($cate_id == 10003){
			$sqlrank = "select * from am_rank where type='all' and `screen`='$screen' and `sdk`<='$sdk' and `copyright`=".$copyright." order by `sdk` desc";
		}
		$rsAppTime = mysql_query($sqlrank, $conn);
		$row = mysql_fetch_assoc($rsAppTime);		
		
		////判断是否云库渠道
		if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
			$arrYunlibNew = json_decode(@file_get_contents($xxxxYunlibDataFile['rank_'.$cate_id]) , true);
			$aAppWeek = ($arrYunlibNew['yunlib_week'])?$arrYunlibNew['yunlib_week']:array();
			$aAppMonth = ($arrYunlibNew['yunlib_month'])?$arrYunlibNew['yunlib_month']:array();
			$aAppAll = ($arrYunlibNew['yunlib_all'])?$arrYunlibNew['yunlib_all']:array();
			if($orderType == 1)		$arr_applist = array_unique(array_merge($aAppWeek , @explode("," , $row['week'])));
			elseif($orderType == 2)		$arr_applist = array_unique(array_merge($aAppMonth , @explode("," , $row['month'])));
			else $arr_applist = array_unique(array_merge($aAppAll , @explode("," , $row['all'])));

			$applist = @implode("," , array_slice($arr_applist , 0 ,20));
		}else{
			if($orderType == 1)		$applist = $row['week'] ;
			elseif($orderType == 2)		$applist = $row['month'] ;
			else $applist = $row['all'] ;
		}
		
		
	}elseif($type == 5){////年龄 
			$sqlAllApp = "SELECT am_appinfo.app_id FROM am_appinfo
						WHERE am_appinfo.app_visible =1 ";
		 switch($cate_id)
		 {
			 case 20002://
			 case 20003://
			 case 20004://
			 case 20005://
				 $sqlAllApp .= " AND (agetype=".(5-$cate_id+20001)." or agetype=0 or agetype is null )";
				 break;
			 case 20001://全年龄
			 default:	
				$sqlAllApp .= "";
		 }
		 if($orderType == 1) $sqlAllApp .= " order by am_appinfo.agetype desc,am_appinfo.app_update_date desc,am_appinfo.app_id desc limit $offset,$perpage";
		 else 				 $sqlAllApp .= " order by am_appinfo.agetype desc,am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit $offset,$perpage";
		
		 $rsAppTime = mysql_query($sqlAllApp, $conn);
		 $arrAppidTime = array();
		 while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
				$arrAppidTime[] = $rowAppTime['app_id'];
		 }

			 
		 $applist = implode("," , $arrAppidTime);	
		
		
	}
	$applist=str_replace('6047980,','',$applist);
	$applist=str_replace('6022542,','',$applist);
	$applist=str_replace('6017185,','',$applist);
	$applist=str_replace('6049480,','',$applist);
	$applist=str_replace('6017157,','',$applist);
	$applist=str_replace('6026591,','',$applist);
	$applist=str_replace('6040938,','',$applist);
	$applist=str_replace('6047980','',$applist);
	$applist=str_replace('6022542','',$applist);
	$applist=str_replace('6017185','',$applist);
	$applist=str_replace('6049480','',$applist);
	$applist=str_replace('6017157','',$applist);
	$applist=str_replace('6026591','',$applist);
	$applist=str_replace('6040938','',$applist);
	@mysql_free_result($rs);
	if($cate_id == 9994){
		$response = array(
			"proto" => 47,
			"reqsuccess" => AM_REQUEST_SUCCESS,
			"applist"        => $applist,
			"timelist"	=> $timelist,
			"namelist" => $namelist,
			"updatetime"	=> time()
			);
	}else{
		$response = array(
			"proto" => 47,
			"reqsuccess" => AM_REQUEST_SUCCESS,
			"applist"        => $applist
			);
	}

	echo array2json($response);
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

