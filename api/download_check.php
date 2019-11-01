<?php
	/************************************************************
	 *   协议号：29
     *   服务器接口文件： download_check.php
     *   验证接口  验证 某appid 是否在下载表中有记录 返回 ture false
     *   Author:  bluesie
	 *   Create Time: 2010-6-18
	 *************************************************************/
	include_once("./inc/init.php");	
	include_once("./class/des.class.php");	
	//解码上行参数
	$keyEncode = __getRequest('keyEncode'); //key密文
	$stringEncode = __getRequest('stringEncode'); //参数密文
	//$keyEncode = "112623724536206452499091753099076232023515059394255832426022466247608374019519596070748529229662257054012178859830724396340698365119934609832833803430225111643067257205518811780267332874404262579960060699643101193405221689497107442835667262271181969047623212894997851564260421698126622368447398054103292209086";
	//$stringEncode = "DwgLSWZS1wpWztiTuS0lkZkFrWe36VGEzquOAPWWHnhizkdzZ9G1C5mgGQzSb7x7CdmrwF/4htD+dCX+exVFrMhkNVQtErklJXFwT0R2SnE=";

	$result = rsa_des_decoded($stringEncode,$keyEncode);
	$paras = $result['paras'];
	$crypt = new CookieCrypt($result['deskey']);
	$aPara = explode("|",$paras);
	//print_r($aa);exit();
    
	$count = count($aPara);
	//paras 格式：proto|uid|sid|mid|appid
	
	if($count != 5 || $aPara[0] != 29)
	{
		$response = error2json("E137");
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}

	list($proto,$uid,$sid,$mid,$appid) = $aPara;

	/*
	$proto = 29;
	$uid = "10152";
	$sid = "rgug7djotrsabr84u2pu3d5bg4 ";//本地
	//$sid = "ec9195ef512e17ad2fe835daba808fa4";//线上
	$mid = 10;
	$appid = "6000035";
	*/
	//
	/********************记录日志***********************/
	//添加日志记录到 am_deposit 表
	$conn = connect_db();
	if($conn === FALSE)
	{

		$response = error2json("S001");
		$encodeRes = $crypt->encrypt($response);
		echo $encodeRes;
		die;
	}
	$sql = "select id from am_download_history where mid=".$mid." and app_id=".$appid;	
	//echo $sql;
	$rs = mysql_query($sql, $conn);
	if($rs === FALSE)
	{
		die;
	}
	else
	{
		//无下载记录
		if(mysql_num_rows($rs) == 0)
		{
			/*
			$response = array2json(array(
						"proto" => 29,
						"reqsuccess" => AM_REQUEST_FAIL,
						"errorno"=> "E117",
						"errormsg"=> "Empty download history.",
						));	
			*/
			$response = error2json("E117");
			$encodeRes = $crypt->encrypt($response);
			echo $encodeRes;
			//echo "no";
			die;
		}
		else
		{
			//有下载记录
			$row = mysql_fetch_assoc($rs);
			$download_id = $row['id'];
			$response = array2json(array(
						"proto" => 29,
						"reqsuccess" => AM_REQUEST_SUCCESS,
						"downid" => $download_id,
						));	
			$encodeRes = $crypt->encrypt($response);
			echo $encodeRes;
			//echo "yes";
			die;
		}
	}
	@mysql_close($conn);
	if($memobj)$memobj->close();
?>
