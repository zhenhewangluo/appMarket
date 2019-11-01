<?php
	/************************************************************
	 *   Interface 41
     *   ��ȡÿ���Ƽ���ݣ�����ʾ����ʮ����Ƽ���¼��id,�����ƣ��Ƽ�ͼ��������ܣ����ID�����ICON������ʱ��2010-12-15��
	 *   
     *   Author: liu jingqi
	 *   Create Time: 2010-12-15
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("./inc/init.php");	
	if($AM_CURRENT_REQUEST["PROTO"] != 41){
		echo error2json("E002");
		die;
	}
	
		//�ж��Ƿ�ֵ��screen,sdk---2010.11.11
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
		$sqlTcl = " and copyright=1 ";
		$xxxx_iscopyright = 1;
	}
	
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}
	
	$id = (__getPost('id'))?__getPost('id'):0;
	
	$keyName = $AM_MEMCACHE["am_category"][0]."_rec_".$screen."_".$sdk."_".$id."_".$xxxx_iscopyright;
	$isflagexists = 0;  $arrTest = $arrRec = array();	
	if($AM_MEMCACHE["am_category"][2]){
		if(!($strrec = $memobj->get($keyName))) $isflagexists = 2;
		else{
			list($alist,$blist) = explode("|@(&*|" , $strrec);
			$arrRec = unserialize($alist);
			$arrTest = unserialize($blist);
		}
	}else $isflagexists = 1;
	if($isflagexists){
		$sql =  "select * from am_rec_daily_day  where visible=1 and addtime<='".date("Y-m-d 23:59:59")."' order by `addtime` desc,id desc  limit 20";
		$rs = mysql_query($sql, $conn);
		if($rs === FALSE){
			echo error2json("S002");
			die;
		}
		$j = 0;
		while($row = mysql_fetch_assoc($rs)){
			$sql2 = "select screen from am_rank_appinfo where screen='$screen' and sdk<='$sdk' $sqlTcl and app_id='".$row['typeid']."'";
			$rs2 = mysql_query($sql2, $conn);
			if(mysql_num_rows($rs2) <= 0) continue;			
			$arrTest[] = $row['id'];
			if(($row['addtime'] != $row['edittime']) || $row['id']>$id){
				$arrRec[$j]['id'] = $row['id'];
				$arrRec[$j]['type'] = $row['type'];
				$arrRec[$j]['typeid'] = $row['typeid'];
				$arrRec[$j]['typename'] = $row['typename'];
				$arrRec[$j]['desc'] = $row['desc'];
				$arrRec[$j]['image'] = $AM_APP_REC_LOGO_LOC_PREFIX.$row['image'];
				$arrRec[$j]['icon'] = $AM_APP_REC_LOGO_LOC_PREFIX.$row['icon'];
				$arrRec[$j]['addtime'] = date("y/m/d",strtotime($row['addtime']));
				$j++;
			}
		}
		if($isflagexists == 2) $memobj->set($keyName ,serialize($arrRec)."|@(&*|".serialize($arrTest) , 0 ,3600*12);
	}
	
	
	echo array2json(array(
		"proto" => 41,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
		"list"  => $arrRec,
		"display" => implode(",",$arrTest),
	));

	@mysql_close($conn);
	if($memobj)$memobj->close();
?>

