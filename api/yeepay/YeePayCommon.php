<?php
/*
 * @Description �ױ�֧�������п�֧��רҵ��ӿڷ��� 
 * @V3.0
 * @Author yang.xu
 */
 
function getReqHmacString($p0_Cmd,$p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pr_NeedResponse,$pz_userId,$pz1_userRegTime)
{
	
	include 'merchantProperties.php';
	
	#���м��ܴ�����һ����������˳�����
	$sbOld		=	"";
	#����ҵ������
	$sbOld		=	$sbOld.$p0_Cmd;
	#�����̻�����
	$sbOld		=	$sbOld.$p1_MerId;
	#�����̻�������
	$sbOld		=	$sbOld.$p2_Order;
	#����֧�������
	$sbOld		=	$sbOld.$p3_Amt;
	#�Ƿ���鶩�����
	$sbOld		=	$sbOld.$p4_verifyAmt;
	#��Ʒ����
	$sbOld		=	$sbOld.$p5_Pid;
	#��Ʒ����
	$sbOld		=	$sbOld.$p6_Pcat;
	#��Ʒ����
	$sbOld		=	$sbOld.$p7_Pdesc;
	#�����̻����ս��׽��֪ͨ�ĵ�ַ
	$sbOld		=	$sbOld.$p8_Url;
	#������ʱ��Ϣ
	$sbOld 		= $sbOld.$pa_MP;
	#���뿨�����
	$sbOld 		= $sbOld.$pa7_cardAmt;
	#���뿨����
	$sbOld		=	$sbOld.$pa8_cardNo;
	#���뿨����
	$sbOld		=	$sbOld.$pa9_cardPwd;
	#����֧��ͨ������
	$sbOld		=	$sbOld.$pd_FrpId;
	#����Ӧ�����
	$sbOld		=	$sbOld.$pr_NeedResponse;
	#�����û�ID
	$sbOld		=	$sbOld.$pz_userId;
	#�����û�ע��ʱ��
	$sbOld		=	$sbOld.$pz1_userRegTime;
	#echo "localhost:".$sbOld;

	//logstr($p2_Order,$sbOld,HmacMd5($sbOld,$merchantKey),$merchantKey);
	return HmacMd5($sbOld,$merchantKey);
    
} 


function annulCard($p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pz_userId,$pz1_userRegTime)
{
	
	include 'merchantProperties.php';
 	include_once 'HttpClient.class.php';
 	
	# �����п�֧��רҵ��֧�����󣬹̶�ֵ "ChargeCardDirect".		
	$p0_Cmd					= "ChargeCardDirect";

	#Ӧ�����.Ϊ"1": ��ҪӦ�����;Ϊ"0": ����ҪӦ�����.			
	$pr_NeedResponse	= "1";
	
	#����ǩ����������ǩ����
	$hmac	= getReqHmacString($p0_Cmd,$p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pr_NeedResponse,$pz_userId,$pz1_userRegTime);
	
	#���м��ܴ�����һ����������˳�����
	$params = array(
		#����ҵ������
		'p0_Cmd'						=>	$p0_Cmd,
		#�����̼�ID
		'p1_MerId'					=>	$p1_MerId,
		#�����̻�������
		'p2_Order' 					=>	$p2_Order,
		#����֧�������
		'p3_Amt'						=>	$p3_Amt,
		#�����Ƿ���鶩�����
		'p4_verifyAmt'						=>	$p4_verifyAmt,
		#�����Ʒ����
		'p5_Pid'						=>	$p5_Pid,
		#�����Ʒ����
		'p6_Pcat'						=>	$p6_Pcat,
		#�����Ʒ����
		'p7_Pdesc'						=>	$p7_Pdesc,
		#�����̻����ս��׽��֪ͨ�ĵ�ַ
		'p8_Url'						=>	$p8_Url,
		#������ʱ��Ϣ
		'pa_MP'					  	=> 	$pa_MP,
		#���뿨�����
		'pa7_cardAmt'				=>	$pa7_cardAmt,
		#���뿨����
		'pa8_cardNo'				=>	$pa8_cardNo,
		#���뿨����
		'pa9_cardPwd'				=>	$pa9_cardPwd,
		#����֧��ͨ������
		'pd_FrpId'					=>	$pd_FrpId,
		#����Ӧ�����
		'pr_NeedResponse'		=>	$pr_NeedResponse,
		#����У����
		'hmac' 							=>	$hmac,
		#�û�Ψһ��ʶ
		'pz_userId'			=>	$pz_userId,
		#�û���ע��ʱ��
		'pz1_userRegTime' 		=>	$pz1_userRegTime
	);
	
	$pageContents	= HttpClient::quickPost($reqURL_SNDApro, $params);
	//echo "pageContents:".$pageContents;
	$result 				= explode("\n",$pageContents);
	
	$r0_Cmd				=	"";							#ҵ������
	$r1_Code			=	"";							#֧�����
	$r2_TrxId			=	"";							#�ױ�֧��������ˮ��
	$r6_Order			=	"";							#�̻�������
	$rq_ReturnMsg	    =	"";							#������Ϣ
	$hmac				=	"";							#ǩ������
	$unkonw				=	"";							#δ֪����  	


	for($index=0;$index<count($result);$index++){		//����ѭ��
		$result[$index] = trim($result[$index]);
		if (strlen($result[$index]) == 0) {
			continue;
		}
		$aryReturn		= explode("=",$result[$index]);
		$sKey			= $aryReturn[0];
		$sValue			= $aryReturn[1];
		if($sKey =="r0_Cmd"){				#ȡ��ҵ������  
			$r0_Cmd				= $sValue;
		}elseif($sKey == "r1_Code"){			        #ȡ��֧�����
			$r1_Code			= $sValue;
		}elseif($sKey == "r2_TrxId"){			        #ȡ���ױ�֧��������ˮ��
			$r2_TrxId			= $sValue;
		}elseif($sKey == "r6_Order"){			        #ȡ���̻�������
			$r6_Order			= $sValue;
		}elseif($sKey == "rq_ReturnMsg"){				#ȡ�ý��׽��������Ϣ
			$rq_ReturnMsg		= $sValue;
		}elseif($sKey == "hmac"){						#ȡ��ǩ������
			$hmac 				= $sValue;	      
		} else{
			return $result[$index];
		}
	}
	

	#����У������ ȡ�ü���ǰ���ַ���
	$sbOld="";
	#����ҵ������
	$sbOld = $sbOld.$r0_Cmd;                
	#����֧�����
	$sbOld = $sbOld.$r1_Code;
	#�����ױ�֧��������ˮ��
	#$sbOld = $sbOld.$r2_TrxId;                
	#�����̻�������
	$sbOld = $sbOld.$r6_Order;                
	#���뽻�׽��������Ϣ
	$sbOld = $sbOld.$rq_ReturnMsg;                   
	$sNewString = HmacMd5($sbOld,$merchantKey);      
  //logstr($r6_Order,$sbOld,HmacMd5($sbOld,$merchantKey),$merchantKey);
	
	#У������ȷ
	if($sNewString != $hmac) {
		$r1_Code = -1;
	}

	$return = array(
		"r1_Code" => $r1_Code,
		"r0_Cmd" => $r0_Cmd,
		"r6_Order" => $r6_Order,
		"rq_ReturnMsg" => $rq_ReturnMsg,
		"hmac" => $hmac
	);
	return $return;
}

function generationTestCallback($p2_Order,$p3_Amt,$p8_Url,$pa7_cardNo,$pa8_cardPwd,$pa_MP,$pz_userId,$pz1_userRegTime)
{
	
	include 'merchantProperties.php';
 	include_once 'HttpClient.class.php';
 	
	# �����п�֧��רҵ��֧�����󣬹̶�ֵ "AnnulCard".		
	$p0_Cmd					= "AnnulCard";

	#Ӧ�����.Ϊ"1": ��ҪӦ�����;Ϊ"0": ����ҪӦ�����.			
	$pr_NeedResponse	= "1";
	
	# �����п�֧��רҵ�������ַ,�������.
	#$reqURL_SNDApro		= "https://www.yeepay.com/app-merchant-proxy/command.action";
	$reqURL_SNDApro		= "http://tech.yeepay.com:8080/robot/generationCallback.action";
	#����ǩ����������ǩ����
	#$hmac	= getReqHmacString($p0_Cmd,$p2_Order,$p3_Amt,$p4_verifyAmt,$p5_Pid,$p6_Pcat,$p7_Pdesc,$p8_Url,$pa_MP,$pa7_cardAmt,$pa8_cardNo,$pa9_cardPwd,$pd_FrpId,$pr_NeedResponse,$pz_userId,$pz1_userRegTime);
	#���м��ܴ�����һ����������˳�����
	$params = array(
		#����ҵ������
		'p0_Cmd'						=>	$p0_Cmd,
		#�����̼�ID
		'p1_MerId'					=>	$p1_MerId,
		#�����̻�������
		'p2_Order' 					=>	$p2_Order,
		#����֧�������
		'p3_Amt'						=>	$p3_Amt,
		#�����̻����ս��׽��֪ͨ�ĵ�ַ
		'p8_Url'						=>	$p8_Url,
		#����֧�������к�
		'pa7_cardNo'				=>	$pa7_cardNo,
		#����֧��������
		'pa8_cardPwd'				=>	$pa8_cardPwd,
		#����֧��ͨ������
		'pd_FrpId'					=>	$pd_FrpId,
		#����Ӧ�����
		'pr_NeedResponse'		=>	$pr_NeedResponse,
		#����Ӧ�����
		'pa_MP'							=>	$pa_MP,
		#�û�Ψһ��ʶ
		'pz_userId'			=>	$pz_userId,
		#�û���ע��ʱ��
		'pz1_userRegTime' 		=>	$pz1_userRegTime);
	
	$pageContents	= HttpClient::quickPost($reqURL_SNDApro, $params);
	return $pageContents;
}


#����ǩ����������ǩ����.
function getCallbackHmacString($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,
$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct){

	include 'merchantProperties.php';

	#����У������ ȡ�ü���ǰ���ַ���
	$sbOld="";
	#����ҵ������
	$sbOld = $sbOld.$r0_Cmd;
	$sbOld = $sbOld.$r1_Code;
	$sbOld = $sbOld.$p1_MerId;
	$sbOld = $sbOld.$p2_Order;
	$sbOld = $sbOld.$p3_Amt;
	$sbOld = $sbOld.$p4_FrpId;
	$sbOld = $sbOld.$p5_CardNo;
	$sbOld = $sbOld.$p6_confirmAmount;
	$sbOld = $sbOld.$p7_realAmount;
	$sbOld = $sbOld.$p8_cardStatus;
	$sbOld = $sbOld.$p9_MP;
	$sbOld = $sbOld.$pb_BalanceAmt;
	$sbOld = $sbOld.$pc_BalanceAct;              
            	
	#echo "[".$sbOld."]";
  //logstr($p2_Order,$sbOld,HmacMd5($sbOld,$merchantKey),$merchantKey);
	return HmacMd5($sbOld,$merchantKey);

}


#ȡ�÷��ش��е����в���.
function getCallBackValue(&$r0_Cmd,&$r1_Code,&$p1_MerId,&$p2_Order,&$p3_Amt,&$p4_FrpId,&$p5_CardNo,&$p6_confirmAmount,&$p7_realAmount,
&$p8_cardStatus,&$p9_MP,&$pb_BalanceAmt,&$pc_BalanceAct,&$hmac)
{  

$r0_Cmd = $_REQUEST['r0_Cmd'];
$r1_Code = $_REQUEST['r1_Code'];
$p1_MerId = $_REQUEST['p1_MerId'];
$p2_Order = $_REQUEST['p2_Order'];
$p3_Amt = $_REQUEST['p3_Amt'];
$p4_FrpId = $_REQUEST['p4_FrpId'];
$p5_CardNo = $_REQUEST['p5_CardNo'];
$p6_confirmAmount = $_REQUEST['p6_confirmAmount'];
$p7_realAmount = $_REQUEST['p7_realAmount'];
$p8_cardStatus = $_REQUEST['p8_cardStatus'];
$p9_MP = $_REQUEST['p9_MP'];
$pb_BalanceAmt = $_REQUEST['pb_BalanceAmt'];
$pc_BalanceAct = $_REQUEST['pc_BalanceAct'];
$hmac = $_REQUEST['hmac'];
	
return null;
	
}


#��֤���ز����е�hmac���̻������ɵ�hmac�Ƿ�һ��.
function CheckHmac($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,
$pc_BalanceAct,$hmac){
	if($hmac == getCallbackHmacString($r0_Cmd,$r1_Code,$p1_MerId,$p2_Order,$p3_Amt,
	$p4_FrpId,$p5_CardNo,$p6_confirmAmount,$p7_realAmount,$p8_cardStatus,$p9_MP,$pb_BalanceAmt,$pc_BalanceAct))
		return true;
	else
		return false;
		
}

  
function HmacMd5($data,$key)           {                                      
	# RFC 2104 HMAC implementation for php.
	# Creates an md5 HMAC.                 
	# Eliminates the need to install mhash to compute a HMAC
	# Hacked by Lance Rushing(NOTE: Hacked means written)
	                                       
	#��Ҫ���û���֧��iconv���������Ĳ���������������
	$key = iconv("GBK","UTF-8",$key);  
	$data = iconv("GBK","UTF-8",$data);
	                                       
	$b = 64; # byte length for md5         
	if (strlen($key) > $b) {               
		$key = pack("H*",md5($key));           
	}                                      
	$key = str_pad($key, $b, chr(0x00));   
	$ipad = str_pad('', $b, chr(0x36));    
	$opad = str_pad('', $b, chr(0x5c));    
	$k_ipad = $key ^ $ipad ;               
	$k_opad = $key ^ $opad;                
	                                       
	return md5($k_opad . pack("H*",md5($k_ipad . $data)));
	
}

function logstr($orderid,$str,$hmac,$keyValue){
	include 'merchantProperties.php';
	$james=fopen($logName,"a+");
	fwrite($james,"\r\n".date("Y-m-d H:i:s")."|orderid[".$orderid."]|str[".$str."]|hmac[".$hmac."]|keyValue[".$keyValue."]");
	fclose($james);
}

function arrToString($arr,$Separators){
	$returnString = "";
	foreach ($arr as $value) {
    		$returnString = $returnString.$value.$Separators;
	}
	return substr($returnString,0,strlen($returnString)-strlen($Separators));
}

function arrToStringDefault($arr){
	return arrToString($arr,",");
}

?> 