<?php
	/************************************************************
     *   获取别人下载应用列表
	 *
     *   Author:  liu jingqi
	 *	 mark: 获取别人下载应用列表
	     rule  过滤自己下载的应用
		       返回 appid  id=9996,9997,9998,9999
	 *	 paras  proto  uid  sid  mid 	  
	 *************************************************************/
	if($cate_id == 9997 || $cate_id==9998){
		$parentid = 5;
		$sqlCateId = "select id from am_category where parent_id = ".$parentid;
		$rsCateId = mysql_query($sqlCateId, $conn);
		$aCateId = array();
		if ($rsCateId !== FALSE) {
			while ($rowCateId = mysql_fetch_assoc($rsCateId)){
				$aCateId[] = $rowCateId['id'];
			}
		}
		 ////如果是TCL接口，判断正版
	//if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)) $sqlTcl = " and am_appinfo.copyright=1 ";
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)) $sqlTcl = "";// and am_appinfo.copyright=1 ";
	
		if($aCateId){
			//查询改分类下所有applist downlist
			$sqlAllApp = "SELECT distinct am_app_category.app_id
							FROM am_app_category
							JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1 $sqlTcl
							AND cate_id in(".join(",",$aCateId).")";
			 $order_time = " order by am_app_category.order,am_appinfo.app_update_date desc,am_appinfo.app_id desc ";
			 $order_down = " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc ";
			 $sqlAllAppByTime = $sqlAllApp.$order_time;
			 $sqlAllAppByDown = $sqlAllApp.$order_down;
			 //time
			 if($cate_id == 9998){
				 ////memcache缓存
					$keyName = $AM_MEMCACHE["am_category"][0].$screen.$sdk."_".md5(implode(",",$aCateId));
					$isflagexists = 0; 
					if($AM_MEMCACHE["am_category"][2]){
						if(!($arrAppidTime = $memobj->get($keyName))) $isflagexists = 2;
					}else $isflagexists = 1;
					if($isflagexists){
						$rsAppTime = mysql_query($sqlAllAppByTime, $conn);		 
						 if ($rsAppDown !== FALSE){
							while ($rowAppTime = mysql_fetch_assoc($rsAppTime)){
								$arrAppidTime[] = $rowAppTime['app_id'];
							}
							$arrAppidTime = array_unique($arrAppidTime);
							if($isflagexists == 2)$memobj->set($keyName ,$arrAppidTime , 0 ,$AM_MEMCACHE["am_category"][1]);
						 }
					}
					 ////memcache缓存 down
					 $arrAppidDown = array();
					$keyName = $AM_MEMCACHE["am_topic"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
					$isflagexists = 0; 
					if($AM_MEMCACHE["am_topic"][2]){
						if(!($arrAppidDown = $memobj->get($keyName))) $isflagexists = 2;
					}else $isflagexists = 1;
					if($isflagexists){
						 $rsAppDown = mysql_query($sqlAllAppByDown, $conn);			 
						 if ($rsAppDown !== FALSE){
							while ($rowAppDown = mysql_fetch_assoc($rsAppDown)){
								$arrAppidDown[] = $rowAppDown['app_id'];
							}
						 }
						 $arrAppidDown = array_unique($arrAppidDown);
						 if($isflagexists == 2)$memobj->set($keyName ,$arrAppidDown , 0 ,$AM_MEMCACHE["am_topic"][1]);
					}
		
		
				 $count = count($arrAppidTime);
				 $strTimeList = @join(",",$arrAppidTime);
				 $strDownList = @join(",",$arrAppidDown);
				 //
				 $aAppNameT = array();
				 $aAppNameD = array();

				 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".$arrAppidTime[0];
				 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
				 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
			     $aAppNameT[0] = $rowAppNameT1['app_name'];
				 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".$arrAppidTime[1];
				 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
				 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
			     $aAppNameT[1] = $rowAppNameT2['app_name'];

				 $sqlAppNameD1 = "select app_name from am_appinfo where app_id = ".$arrAppidDown[0];
				 $rsAppNameD1 = mysql_query($sqlAppNameD1, $conn);
				 $rowAppNameD1 = mysql_fetch_assoc($rsAppNameD1);
		         $aAppNameD[0] = $rowAppNameD1['app_name'];
				 $sqlAppNameD2 = "select app_name from am_appinfo where app_id = ".$arrAppidDown[1];
				 $rsAppNameD2 = mysql_query($sqlAppNameD2, $conn);
				 $rowAppNameD2 = mysql_fetch_assoc($rsAppNameD2);
		         $aAppNameD[1] = $rowAppNameD2['app_name'];
				 

				 $strDownTime = join(",",$aAppNameD);
			 }
			 //查询每周下载应用排行
			 elseif($cate_id == 9997){
			 	 $lastweek = date('Y-m-d' , strtotime('-1 week'));
				 $aDownAppId = array();
				////memcache缓存
				$keyName = $AM_MEMCACHE["am_download_history"][0].$screen.$sdk."_".$lastweek;
				$isflagexists = 0; 
				if($AM_MEMCACHE["am_download_history"][2]){
					if(!($aDownAppId = $memobj->get($keyName))) $isflagexists = 2;
				}else $isflagexists = 1;
				if($isflagexists){
					$sqlDown = "select distinct app_id from am_download_history where create_time >='".$lastweek."'";
					$rsDown = mysql_query($sqlDown, $conn);
					if ($rsDown !== FALSE) {
						while ($rowDown = mysql_fetch_assoc($rsDown)){
							$aDownAppId[] = $rowDown['app_id'];
						}
						if($isflagexists == 2)$memobj->set($keyName ,$aDownAppId , 0 ,$AM_MEMCACHE["am_download_history"][1]);
					}
				}
				$total = count($aDownAppId);
				 if($total >= 20){
					//获取本周应用 列表  cate_id = 5
					$keyName = $AM_MEMCACHE["am_download_history"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
					$isflagexists = 0; 
					if($AM_MEMCACHE["am_download_history"][2]){
						if(!($arrWAppidDown = $memobj->get($keyName))) $isflagexists = 2;
					}else $isflagexists = 1;
					if($isflagexists){
						//获取本周应用 列表  cate_id = 5
						$sqlAllApp = "SELECT distinct am_app_category.app_id
										FROM am_app_category
										JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
										WHERE am_appinfo.app_visible =1 $sqlTcl
										AND am_appinfo.app_id
										IN (".join(",",$aDownAppId).")
										AND cate_id in(".join(",",$aCateId).")";
						 $order_down = " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit 0,20";
						 $sqlAllAppByDown = $sqlAllApp.$order_down;
						 //time
						 $rsAppDown = mysql_query($sqlAllAppByDown, $conn);
						 if ($rsAppDown !== FALSE){
							while ($rowAppDown = mysql_fetch_assoc($rsAppDown)){
								$arrWAppidDown[] = $rowAppDown['app_id'];
							}
							if($isflagexists == 2)$memobj->set($keyName ,$arrWAppidDown , 0 ,$AM_MEMCACHE["am_download_history"][1]);
						 }
					}
					
					 $aAppNameD = array();

					 $sqlAppNameD1 = "select app_name from am_appinfo where app_id = ".$arrWAppidDown[0];
					 $rsAppNameD1 = mysql_query($sqlAppNameD1, $conn);
					 $rowAppNameD1 = mysql_fetch_assoc($rsAppNameD1);
					 $aAppNameD[0] = $rowAppNameD1['app_name'];
					 $sqlAppNameD2 = "select app_name from am_appinfo where app_id = ".$arrWAppidDown[1];
					 $rsAppNameD2 = mysql_query($sqlAppNameD2, $conn);
					 $rowAppNameD2 = mysql_fetch_assoc($rsAppNameD2);
					 $aAppNameD[1] = $rowAppNameD2['app_name'];

					 //$arrWAppidDown = array_unique($arrWAppidDown);
					 $strDownList = @join(",",$arrWAppidDown);
					 $count = count($arrWAppidDown);
					 $strDownTime = join(",",$aAppNameD);
				}
				else{
					//
				}
			}
		}
	}elseif($cate_id==9996 ||$cate_id==9999){
		$parentid = 6;
		$sqlCateId = "select id from am_category where parent_id = ".$parentid;
		$rsCateId = mysql_query($sqlCateId, $conn);
		$aCateId = array();
		if ($rsCateId !== FALSE) 
		{
			while ($rowCateId = mysql_fetch_assoc($rsCateId))
			{
				$aCateId[] = $rowCateId['id'];
			}
		}
		if($aCateId)
		{
			if($cate_id == 9999){
				//查询改分类下所有applist downlist
				$sqlAllGame = "SELECT distinct am_app_category.app_id
								FROM am_app_category
								JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
								WHERE am_appinfo.app_visible =1 $sqlTcl
								AND cate_id in(".join(",",$aCateId).")";
				 $order_time = " order by am_app_category.order,am_appinfo.app_update_date desc,am_appinfo.app_id desc ";
				 $order_down = " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc ";
				 $sqlAllGameByTime = $sqlAllGame.$order_time;
				 $sqlAllGameByDown = $sqlAllGame.$order_down;
				 ////memcache缓存 time
					 $arrGameidTime = array();
					$keyName = $AM_MEMCACHE["am_category"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
					$isflagexists = 0; 
					if($AM_MEMCACHE["am_category"][2]){
						if(!($arrGameidTime = $memobj->get($keyName))) $isflagexists = 2;
					}else $isflagexists = 1;
					if($isflagexists){
						 $rsGameTime = mysql_query($sqlAllGameByTime, $conn);
						 if ($rsGameDown !== FALSE){
							while ($rowGameTime = mysql_fetch_assoc($rsGameTime)){
								$arrGameidTime[] = $rowGameTime['app_id'];
							}
							if($isflagexists == 2)$memobj->set($keyName ,$arrGameidTime , 0 ,$AM_MEMCACHE["am_category"][1]);
						 }
					}

					  ////memcache缓存 down
					 $arrGameidDown = array();
					$keyName = $AM_MEMCACHE["am_topic"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
					$isflagexists = 0; 
					if($AM_MEMCACHE["am_topic"][2]){
						if(!($arrGameidDown = $memobj->get($keyName))) $isflagexists = 2;
					}else $isflagexists = 1;
					if($isflagexists){
						 $rsGameDown = mysql_query($sqlAllGameByDown, $conn);
						 if ($rsGameDown !== FALSE){
							while ($rowGameDown = mysql_fetch_assoc($rsGameDown)){
								$arrGameidDown[] = $rowGameDown['app_id'];
							}
							if($isflagexists == 2)$memobj->set($keyName ,$arrGameidDown , 0 ,$AM_MEMCACHE["am_topic"][1]);
						 }			 
					}
				 /*****  前两个应用的名字  bluesie  2010-8-17  ********/
				 $aAppNameT = array();
				 $aAppNameD = array();

				 $sqlAppNameT1 = "select app_name from am_appinfo where app_id = ".$arrGameidTime[0];
				 $rsAppNameT1 = mysql_query($sqlAppNameT1, $conn);
				 $rowAppNameT1 = mysql_fetch_assoc($rsAppNameT1);
			     $aAppNameT[0] = $rowAppNameT1['app_name'];
				 $sqlAppNameT2 = "select app_name from am_appinfo where app_id = ".$arrGameidTime[1];
				 $rsAppNameT2 = mysql_query($sqlAppNameT2, $conn);
				 $rowAppNameT2 = mysql_fetch_assoc($rsAppNameT2);
			     $aAppNameT[1] = $rowAppNameT2['app_name'];

				 $sqlAppNameD1 = "select app_name from am_appinfo where app_id = ".$arrGameidDown[0];
				 $rsAppNameD1 = mysql_query($sqlAppNameD1, $conn);
				 $rowAppNameD1 = mysql_fetch_assoc($rsAppNameD1);
		         $aAppNameD[0] = $rowAppNameD1['app_name'];
				 $sqlAppNameD2 = "select app_name from am_appinfo where app_id = ".$arrGameidDown[1];
				 $rsAppNameD2 = mysql_query($sqlAppNameD2, $conn);
				 $rowAppNameD2 = mysql_fetch_assoc($rsAppNameD2);
		         $aAppNameD[1] = $rowAppNameD2['app_name'];

				 /*****  前两个应用的名字  bluesie  2010-8-17  THE END********/

				 $arrGameidDown = array_unique($arrGameidDown);
				 $count = count($arrGameidTime);			
				 $strTimeList = @join(",",$arrGameidTime);
				 $strDownList = @join(",",$arrGameidDown);
				 
				 $strDownTime = join(",",$aAppNameD);
			}elseif($cate_id == 9996){
				 //查询每周下载游戏排行
				 $lastweek = date('Y-m-d' , strtotime('-1 week'));
				 $aDownAppId = array();
				////memcache缓存
				$keyName = $AM_MEMCACHE["am_download_history"][0].$screen.$sdk."_".$lastweek;
				$isflagexists = 0; 
				if($AM_MEMCACHE["am_download_history"][2]){
					if(!($aDownAppId = $memobj->get($keyName))) $isflagexists = 2;
				}else $isflagexists = 1;
				if($isflagexists){
					$sqlDown = "select distinct app_id from am_download_history where  create_time >='".$lastweek."'";
					$rsDown = mysql_query($sqlDown, $conn);
					if ($rsDown !== FALSE) {
						while ($rowDown = mysql_fetch_assoc($rsDown)){
							$aDownAppId[] = $rowDown['app_id'];
						}
						if($isflagexists == 2)$memobj->set($keyName ,$aDownAppId , 0 ,$AM_MEMCACHE["am_download_history"][1]);
					}
				}
	
				$total = count($aDownAppId);
				 if($total >= 20)
				 {
					$keyName = $AM_MEMCACHE["am_download_history"][0].$screen.$sdk."_".$xxxx_iscopyright."_".md5(implode(",",$aCateId));
					$isflagexists = 0; $arrWAppidDown2 = array();
					if($AM_MEMCACHE["am_download_history"][2]){
						if(!($arrWAppidDown2 = $memobj->get($keyName))) $isflagexists = 2;
					}else $isflagexists = 1;
					if($isflagexists){
						
						//获取本周应用 列表  cate_id = 5
						$sqlAllApp = "SELECT distinct am_app_category.app_id
										FROM am_app_category
										JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
										WHERE am_appinfo.app_visible =1 $sqlTcl
										AND am_appinfo.app_id
										IN (".join(",",$aDownAppId).")
										AND cate_id in(".join(",",$aCateId).")";
						 $order_down = " order by am_appinfo.app_downloads desc,am_appinfo.app_update_date desc limit 0,20";
						 $sqlAllAppByDown = $sqlAllApp.$order_down;
						 //time
						 $rsAppDown = mysql_query($sqlAllAppByDown, $conn);
						 if ($rsAppDown !== FALSE){
							while ($rowAppDown = mysql_fetch_assoc($rsAppDown)){
								$arrWAppidDown2[] = $rowAppDown['app_id'];
							}
							if($isflagexists == 2)$memobj->set($keyName ,$arrWAppidDown2 , 0 ,$AM_MEMCACHE["am_download_history"][1]);
						 }
					}
					 /*****  前两个应用的名字  bluesie  2010-8-17  ********/
					 $aAppNameD = array();

					 $sqlAppNameD1 = "select app_name from am_appinfo where app_id = ".$arrWAppidDown2[0];
					 $rsAppNameD1 = mysql_query($sqlAppNameD1, $conn);
					 $rowAppNameD1 = mysql_fetch_assoc($rsAppNameD1);
					 $aAppNameD[0] = $rowAppNameD1['app_name'];
					 $sqlAppNameD2 = "select app_name from am_appinfo where app_id = ".$arrWAppidDown2[1];
					 $rsAppNameD2 = mysql_query($sqlAppNameD2, $conn);
					 $rowAppNameD2 = mysql_fetch_assoc($rsAppNameD2);
					 $aAppNameD[1] = $rowAppNameD2['app_name'];
					 /*****  前两个应用的名字  bluesie  2010-8-17  THE END********/
					 $strDownList = @join(",",$arrWAppidDown2);
					 $count = count($arrWAppidDown2);
					 $strDownTime = join(",",$aAppNameD);
				}
				else{
					//
				}
			}
		}
	}
?>

