<?php
	/************************************************************
	 *   Interface 44
     *  
     *   Author: liu jingqi
	 *   Create Time: 2010-12-15
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	if($AM_CURRENT_REQUEST["PROTO"] != 44){
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
	if(in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid)) {
		$sqlTcl ="";// " and copyright=1 ";
		$xxxx_iscopyright = 1;
	}
	
	$arrYunlibRec = array();
	////判断是否云库渠道
	if(in_array($_SESSION["channel"], $xxxxYunlibChannelid)) {
		$arrYunlibRec = json_decode(@file_get_contents($xxxxYunlibDataFile['rec']) , true);
	}

	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");die;
	}
	
	$time2 = (__getPost('time'))?__getPost('time'):0;
	
	$sql =  "select * from am_rec_daily_day a left join am_appinfo b on a.typeid=b.app_id where a.visible=1 and b.app_visible=1 and a.edittime<='".date("Y-m-d 23:59:59")."' order by a.`edittime` desc,a.id desc  limit 20";
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE){
		echo error2json("S002");
		die;
	}
	$k = $j = 0; $arrappTest = $arrTest = $arrRec = array();
	while($row = mysql_fetch_assoc($rs)){
		if(count($arrTest)>=20) break;
//		$flag = 0;
//		$sql2 = "select screen from am_rank_appinfo where screen='$screen' and sdk<='$sdk' $sqlTcl and app_id='".$row['typeid']."'";
//		$rs2 = mysql_query($sql2, $conn);
//		if(mysql_num_rows($rs2) <= 0) continue;
//		if($arrYunlibRec[$k]){ ////云库推荐
//			for($i=$k; $i<count($arrYunlibRec); $i++){
//				if(count($arrTest)>=20) break;
//
//				if($arrYunlibRec[$i]['edittime']<=date("Y-m-d 23:59:59")){
//					if($arrYunlibRec[$i]['edittime'] > $row['edittime']){
//						if(!in_array($arrYunlibRec[$i]['typeid'] , $arrappTest)){
//							$arrappTest[] = $arrYunlibRec[$i]['typeid'];
//							$arrTest[] = $arrYunlibRec[$i]['id'];
//							if($arrYunlibRec[$i]['edittime'] > $time2){
//								$arrRec[$j] = getArrayRec($arrYunlibRec[$i]);
//								$j++;
//							}
//						}
//					}else{
//						if(!in_array($row['typeid'] , $arrappTest)){
//							$arrappTest[] = $row['typeid'];
//							$arrTest[] = $row['id'];
//							if($row['edittime'] > $time2){
//								$arrRec[$j] = getArrayRec($row);
//								$j++;
//							}
//						}
//						$flag = 1;
//						break;
//					}
//				}
//			}
//			if($flag != 1 && count($arrTest)<20){
//				if(!in_array($row['typeid'] , $arrappTest)){
//					$arrappTest[] = $row['typeid'];
//					$arrTest[] = $row['id'];
//					if($row['edittime'] > $time2){
//						$arrRec[$j] = getArrayRec($row);
//						$j++;
//					}
//				}
//			}
//			$k = $i;
//		}else{
			if(!in_array($row['typeid'] , $arrappTest)){
				$arrappTest[] = $row['typeid'];
				$arrTest[] = $row['id'];
				if($row['edittime'] > $time2){
					$arrRec[$j] = getArrayRec($row);
					$j++;
				}
			}
//		}
	}
	if(empty($arrRec))
	{
		echo error2json("E120");
		die;	
	}
	
	echo array2json(array(
		"proto" => 44,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
		"list"  => $arrRec,
		"display" => implode(",",$arrTest),
		"time" => date("Y-m-d H:i:s")
	));

	@mysql_close($conn); 
	if($memobj)$memobj->close();
	
	
	function getArrayRec($row){
		global $AM_APP_REC_LOGO_LOC_PREFIX;
		$arrRec = array();
		$arrRec['id'] = $row['id'];
		$arrRec['type'] = $row['type'];
		$arrRec['typeid'] = $row['typeid'];
		$arrRec['typename'] = $row['typename'];
		$arrRec['desc'] = deleteHtmlTags($row['desc']);
		$arrRec['rec_desc'] = deleteHtmlTags(($row['rec_desc'])?$row['rec_desc']:"");
		$arrRec['image'] = $AM_APP_REC_LOGO_LOC_PREFIX.$row['image'];
		$arrRec['icon'] = $AM_APP_REC_LOGO_LOC_PREFIX.$row['icon'];
		$arrRec['addtime'] = date("y/m/d",strtotime($row['edittime']));
		return $arrRec;
	}
?>

