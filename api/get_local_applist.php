<?php
	/************************************************************* 
	 *   Interface 30
     *   get_local_applist.php
	 *   获取本地扫描应用列表信息
     *   Author: bluesie
	 *   Create Time: 2010-08-04
	 *   param  proto  mid  uid  sid  list 
	 *   说明 ： Array
				(
					[0] => stdClass Object
						(
							[appName] => Nexus One LED Flashlight
							[pkgName] => com.jazzmoonstudio.android.widget.flashlight
							[appVersion] => 1.2.0
							[version] => 8
						)

					[1] => stdClass Object
						(
							[appName] => 欢聚宝
							[pkgName] => com.eoemobile.market
							[appVersion] => null
							[version] => 1
						)

				)
           根据  pkgName 从am_app_device_type 中查询appid ，由appid从am_appinfo 查询 version v1 跟所传version v2比较，如果 v1>v2，则返回此条appid 对应的详细信息;否则，插入数据库 am_app_unsort

	 *************************************************************/
	require("./inc/init.php");	
	
	if($AM_CURRENT_REQUEST["PROTO"] != 30){
		echo(error2json("E002"));
		die;
	}

	if(!isset($_POST['list']) || empty($_POST['list'])){
		echo(error2json("E205"));die;
	}
	$current_device = $AM_CURRENT_REQUEST["MODEL"];
	if($current_device == 0){
		echo error2json("E195");die;
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
	
	$current_channel_id = isset($AM_CURRENT_REQUEST["CHANNEL"]) ? $AM_CURRENT_REQUEST["CHANNEL"] : 0;

	$list_str = stopSql(__getPost('list'));
	$arr_view_info    = array();
	foreach(explode("|", $list_str) as $view_info){
		$arr_view_info[] = $view_info;	
	}

	if($arr_view_info){
		foreach($arr_view_info as $key=>$val)
		{
			$aTemp = explode("^",$val);
			$arr_info[$key]->appName = $aTemp[0];
			$arr_info[$key]->pkgName = $aTemp[1];
			$arr_info[$key]->appVersion = $aTemp[2];
			$arr_info[$key]->version = $aTemp[3];
		}
	}

	

////如果是TCL接口，判断正版
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)) {
		$sqlTcl = " and a.copyright=1 ";
	}
	$i = 0;
	// var_dump($arr_info);die;
	if($arr_info){
		foreach($arr_info as $key=>$val){
			$unsortId = "";
			$row = array();
			$sSelect  = "select mid,version from am_app_unsort where mid=".$_SESSION["mid"]." and pkg_name='".$val->pkgName."'";
			$rsR = mysql_query($sSelect, $conn);
			$rowR = mysql_fetch_assoc($rsR);
			$unsortId = $rowR['mid'];
			$versionR = $rowR['version'];

			$sql = "select a.*,b.icon,b.logo,b.pkg_name,b.screenshots,b.app_size from am_appinfo a left join  am_app_device_type b on a.app_id=b.app_id where b.pkg_name='".$val->pkgName."'  and a.app_visible =1".$sqlTcl." limit 1";
			$rs = mysql_query($sql, $conn);
			$row = mysql_fetch_assoc($rs);
			if(!$row){
				if($unsortId){
					if($versionR <= $val->version){
						//修改此条记录
						$sUpdate = "update am_app_unsort set app_name='".$val->appName."',app_version='".$val->appVersion."',version='".$val->version."',updatetime=".time()." where mid=".$unsortId." and pkg_name='".$val->pkgName."'";
						if(mysql_query($sUpdate, $conn) === FALSE){
							log_message($sUpdate, 'S');
						}
					}
				}
				else{
					//记录此条信息
					$sInsert = "insert into am_app_unsort (`mid`,`appid`, `app_name`, `pkg_name`, `app_version`, `version`, `updatetime`, `type`) values ('".$_SESSION["mid"]."','0','".$val->appName."','".$val->pkgName."','".$val->appVersion."','".$val->version."','".time()."','1')";
					if(mysql_query($sInsert, $conn) === FALSE){
						log_message($sInsert, 'S');
					}
				}
			}
			else{
				//记录数据库
				if($row['version'] < $val->version){
					if($unsortId){
						if($versionR <= $val->version){
							//修改此条记录
							$sUpdate2 = "update am_app_unsort set app_name='".$val->appName."',app_version='".$val->appVersion."',version='".$val->version."',updatetime=".time()." where mid=".$unsortId;
							if(mysql_query($sUpdate2, $conn) === FALSE){
								log_message($sUpdate2, 'S');
							}
						}
					}
					else{
						$sInsert2 = "insert into am_app_unsort (`mid`,`appid`, `app_name`, `pkg_name`, `app_version`, `version`, `updatetime`, `type`) values ('".$_SESSION["mid"]."','{$row['app_id']}','".$val->appName."','".$val->pkgName."','".$val->appVersion."','".$val->version."','".time()."','2')";
						if(mysql_query($sInsert2, $conn) === FALSE){
							log_message($sInsert2, 'S');
						}
					}
				}
				else{
					$sInsert3 = "insert into am_app_unsort (`mid`,`appid`, `app_name`, `pkg_name`, `app_version`, `version`, `updatetime`, `type`) values ('".$_SESSION["mid"]."','{$row['app_id']}','".$val->appName."','".$val->pkgName."','".$val->appVersion."','".$val->version."','".time()."','0')";
					if(mysql_query($sInsert3, $conn) === FALSE){
						log_message($sInsert3, 'S');
					}
				}
				
				////新增判断，如果TCL那几个应用，暂时不升级
				if(($_SESSION["channel"] == $xxxxcopyrightchekChannelid[0]) && $TCLArrAppinfo_A890['tcl_'.$row['app_id']]){
					$mid = $_SESSION["mid"];
					mysql_close($conn);
					$conn = connect_comm_db();
					$sql = "select mid from am_terminal_info where mid=". $mid." and product like '%A890%'";
					$rsAppTime = mysql_query($sql, $conn);
					$rowTcl = mysql_fetch_assoc($rsAppTime);
					if($rowTcl['mid'] && $TCLArrAppinfo_A890['tcl_'.$row['app_id']]){
						$row['version'] = $val->version;
					}else{
						$sql = "select mid from am_terminal_info where mid=". $mid." and product like '%A906%'";
						$rsAppTime = mysql_query($sql, $conn);
						$rowTcl = mysql_fetch_assoc($rsAppTime);
						if($rowTcl['mid'] && $TCLArrAppinfo_A906['tcl_'.$row['app_id']]) $row['version'] = $val->version;
					}
					
					mysql_close($conn);
					$conn = connect_db();
				}
				//返回
				list($s1 , $s2) = explode("|",$row['screenshots']);
				if($_SESSION["channel"] == 10028){ ////T-PARK渠道
					$s1 = str_replace("/" , "_" , $s1);
					$s2 = str_replace("/" , "_" , $s2);
					$arr_s[0] = ($s1)?$AM_APP_LOGO_LOC_PREFIX."nosign/".$s1:"";
					$arr_s[1] = ($s2)?$AM_APP_LOGO_LOC_PREFIX."nosign/".$s2:"";
				}else{
					$arr_s[0] = ($s1)?$AM_APP_LOGO_LOC_PREFIX.$s1:"";
					$arr_s[1] = ($s2)?$AM_APP_LOGO_LOC_PREFIX.$s2:"";
				}
				$screenshots = implode("," , $arr_s);
				////评论总分不能大于总次数*5 by xxxxxxxxx 2011-03-31
				checkScoreAndNum($row["total_score"] , $row["total_num"]);
				
				$aResult[$i]['appid'] = $row['app_id'];
				$aResult[$i]['icon'] = $AM_CATE_ICON_LOC_PREFIX . $row["icon"];
				$aResult[$i]['name'] = $row['app_name'];
				$aResult[$i]['short_desc'] = $row['app_slogan'];
				$aResult[$i]['desc'] = deleteHtmlTags($row['app_desc']);
				$aResult[$i]['author'] = $row['author_name'];
				$aResult[$i]['rating_up'] = $row['app_rate_up'];
				$aResult[$i]['rating_down'] = $row['app_rate_down'];
				$aResult[$i]['download_cnt'] = $row['app_downloads']*AM_DOWNLOAD_CNT;
				$aResult[$i]['price'] = $row["app_price"] * AM_EXCHANGE_RATE;
				$aResult[$i]['version'] = $row['version'];
				$aResult[$i]['appver'] = $row['app_version'];
				$aResult[$i]['infover'] = intval($row['infover']);
				$aResult[$i]['screenshot'] = $AM_APP_LOGO_LOC_PREFIX  . $row["logo"];
				$aResult[$i]['size'] = $row['app_size'];
				$aResult[$i]['pkg_name'] = $row['pkg_name'];
				$aResult[$i]['total_score'] = $row['total_score'];
				$aResult[$i]['total_num'] = $row['total_num'];
				$aResult[$i]['is_english'] = $row['is_english'];
				$aResult[$i]['screenshots'] = $screenshots;
				$aResult[$i]['total_rate_num'] = $row['total_comments'];
				$i++;
			}
		}
	}
	// print_r($aResult);
	// exit();
	if($aResult){
		$response = array(
			"proto"	     =>	 30,
			"reqsuccess" =>  AM_REQUEST_SUCCESS,
			"list"  => $aResult,  
			);	
		echo array2json($response);
	}else{
		echo error2json("E120");die;
	}
	@mysql_close($conn);

