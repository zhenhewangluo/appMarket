<?php

/**
  Author: Andrew Chan
  Created date:2007-08-10
  This file contains some common functions
 */
////评论分数不能大于评论次数*5
function checkScoreAndNum(&$total_score, &$total_num)
{
	$total_score = intval($total_score);
	$total_num = intval($total_num);
	if ($total_num == 0)
	{
		$total_score = 0;
	}
	else
	{
		if ($total_score > $total_num * 5)
			$total_score = $total_num * 5;
		elseif ($total_score < $total_num)
			$total_score = $total_num;
	}
}

function __getPost($arg)
{
	global $_POST;
	$arg = $_POST[$arg];
	return $arg;
}

function __getGet($arg)
{
	global $_GET;
	$arg = $_GET[$arg];
	return $arg;
}

function __getRequest($arg)
{
	global $_REQUEST;
	$arg = $_REQUEST[$arg];
	return $arg;
}

function stopSql($parameter)
{
	if (get_magic_quotes_gpc())
	{
		$parameter = stripslashes($parameter);
	}
	else
	{
		$parameter = mysql_escape_string($parameter);
	}
	//	$filter = array("drop","select","delete","truncate","insert","update","alter","table","*","from");
	//	$parameter = str_replace($filter,"",$parameter);	
	return trim(htmlspecialchars($parameter));
}

function getParameter($parameter, $type)
{
	if ($type == "GET")
	{
		if (isset($_GET[$parameter]))
			return stopSql(__getGet($parameter));
		else
			return false;
	}
	else if ($type == "POST")
	{
		if (isset($_POST[$parameter]))
			return stopSql(__getPost($parameter));
		else
			return false;
	}
	return false;
}

function microtime_float()
{
	list($usec, $sec) = explode(" ", microtime());
	return ((float) $usec + (float) $sec);
}

function addIndex2Array(& $arr)
{
	$i = 1;
	$temp = array();
	foreach ($arr as $ele)
	{
		$ele["index"] = $i++;
		$temp[] = $ele;
	}
	return $temp;
}

function debug($var)
{
	echo ">>>>>>>>";
	if (is_string($var) || is_numeric($var))
		echo $var;
	else
		print_r($var);
	echo "<<<<<<<<";
}

function println($str, $is_html=false)
{
	echo $str;
	echo $is_html ? "<br>" : "\n";
}

function rtrim_strcmp(&$str1, &$str2)
{
	if (rtrim($str1) == rtrim($str2))
		return 0;
	else
		return -1;
}

function array2json($arr)
{

	//added by lixiaan to support empty array
	if (count($arr) == 0)
	{
		return '[]';
	}

	if (function_exists('json_encode'))
		return json_encode($arr); //Lastest versions of PHP already has this functionality.
	$parts = array();
	$is_list = false;

	//Find out if the given array is a numerical array
	$keys = array_keys($arr);
	$max_length = count($arr) - 1;
	if (($keys[0] == 0) and ($keys[$max_length] == $max_length))
	{//See if the first key is 0 and last key is length - 1
		$is_list = true;
		for ($i = 0; $i < count($keys); $i++)
		{ //See if each key correspondes to its position
			if ($i != $keys[$i])
			{ //A key fails at position check.
				$is_list = false; //It is an associative array.
				break;
			}
		}
	}

	foreach ($arr as $key => $value)
	{

		if (is_array($value))
		{ //Custom handling for arrays
			if ($is_list)
				$parts[] = array2json($value); /* :RECURSION: */
			else
				$parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */
		} else
		{
			$str = '';
			if (!$is_list)
				$str = '"' . $key . '":';

			//Custom handling for multiple data types
			if (is_numeric($value))
				$str .= $value; //Numbers
			elseif ($value === false)
				$str .= 'false'; //The booleans
			elseif ($value === true)
				$str .= 'true';
			else
				$str .= '"' . addslashes($value) . '"'; //All other things
				
// :TODO: Is there any more datatype we should be in the lookout for? (Object?)

			$parts[] = $str;
		}
	}
	//debug($parts);
	//BUG: author_name字段中的空格被替换成为换行符,在上一句的
	//debug出来的还是空格。
	$json = implode(',', $parts);

	if ($is_list)
		return '[' . $json . ']'; //Return numerical JSON
	return '{' . $json . '}'; //Return associative JSON
}

function error2json($error_no, $need_dump=true)
{

	global $AM_ERRORS, $AM_CURRENT_REQUEST;

	if (!array_key_exists($error_no, $AM_ERRORS))
	{
		$error_no = AM_UNKNOWN_ERROR;
	}
	$resp_json = array2json(array(
		"proto" => $AM_CURRENT_REQUEST["PROTO"],
		"reqsuccess" => AM_REQUEST_FAIL,
		"errno" => $error_no,
		"errmsg" => $AM_ERRORS[$error_no]
			));

	//add logging actions
	$error_level = substr($error_no, 0, 1);
	log_message(sprintf("%s %s", $error_no, $AM_ERRORS[$error_no]), $error_level);

	if ($need_dump)
	{
		$context = array2json(array(
			"REQUEST" => $_REQUEST,
			"SESSION" => array(
				"uid" => isset($AM_CURRENT_REQUEST["UID"]) ? $AM_CURRENT_REQUEST["UID"] : '',
				"channel" => isset($AM_CURRENT_REQUEST["CHANNEL"]) ? $AM_CURRENT_REQUEST["CHANNEL"] : ''
			),
				//"RESPONSE" => str_replace("\n", "", $resp_json),
				));
		log_message("DUMP:" . str_replace("\n", "", $context), $error_level);
	}
	return $resp_json;
}

function error2json_withlog($error_no, $need_dump=true, $error_log="")
{

	global $AM_ERRORS, $AM_CURRENT_REQUEST;

	if (!array_key_exists($error_no, $AM_ERRORS))
	{
		$error_no = AM_UNKNOWN_ERROR;
	}
	$resp_json = array2json(array(
		"proto" => $AM_CURRENT_REQUEST["PROTO"],
		"reqsuccess" => AM_REQUEST_FAIL,
		"errno" => $error_no,
		"errmsg" => $AM_ERRORS[$error_no],
		"errlog" => $error_log
			));

	//add logging actions
	$error_level = substr($error_no, 0, 1);
	log_message(sprintf("%s %s", $error_no, $AM_ERRORS[$error_no]), $error_level);

	if ($need_dump)
	{
		$context = array2json(array(
			"REQUEST" => $_REQUEST,
			"SESSION" => array(
				"uid" => isset($AM_CURRENT_REQUEST["UID"]) ? $AM_CURRENT_REQUEST["UID"] : '',
				"channel" => isset($AM_CURRENT_REQUEST["CHANNEL"]) ? $AM_CURRENT_REQUEST["CHANNEL"] : ''
			),
				//"RESPONSE" => str_replace("\n", "", $resp_json),
				));
		log_message("DUMP:" . str_replace("\n", "", $context), $error_level);
	}
	return $resp_json;
}

function connect_db()
{
	global $AM_DATABASE_INFOR;
	$conn = @mysql_connect($AM_DATABASE_INFOR["DB_HOST"], $AM_DATABASE_INFOR['DB_USER'], $AM_DATABASE_INFOR['DB_PASSWORD']);
	if ($conn === FALSE)
	{
		return FALSE;
	}

	if (!mysql_select_db($AM_DATABASE_INFOR["DB_NAME"], $conn))
	{
		return FALSE;
	}

	if (!mysql_query("SET NAMES UTF8", $conn))
	{
		return FALSE;
	}
	return $conn;
}

function connect_comm_db()
{
	global $AM_COMMON_INFO_DATABASE;
	$conn = @mysql_connect($AM_COMMON_INFO_DATABASE["DB_HOST"], $AM_COMMON_INFO_DATABASE['DB_USER'], $AM_COMMON_INFO_DATABASE['DB_PASSWORD']);
	if ($conn === FALSE)
	{
		return FALSE;
	}

	if (!mysql_select_db($AM_COMMON_INFO_DATABASE["DB_NAME"], $conn))
	{
		return FALSE;
	}

	if (!mysql_query("SET NAMES UTF8", $conn))
	{
		return FALSE;
	}
	return $conn;
}

function & get_singleton($className)
{
	if (check_reg($className))
	{
		return $GLOBALS["OBJECTS"][$className];
	}
	if (!class_exists($className))
		return false;
	$obj = & new $className();
	reg($obj, $className);
	return $obj;
}

function check_reg($name)
{
	return isset($GLOBALS["OBJECTS"][$name]);
}

function reg(& $obj, $name=NULL)
{
	if ($name == NULL && is_object($obj))
		$name = get_class($obj);
	$GLOBALS["OBJECTS"][$name] = $obj;
}

function log_message($msg, $level='I')
{
	static $instance = null;
	if ($instance == null)
	{
		$instance = array();
		$obj = & get_singleton('Bajie_Log');
		$instance = array('obj' => & $obj);
	}
	return $instance['obj']->appendLog($msg, $level);
}

function str2array($str, $equal='=', $spliter=',')
{
	$dict = array();
	$list = explode($spliter, $str);
	if ($list === FALSE)
		return FALSE;

	for ($i = 0; $i < count($list); $i++)
	{
		$pair = explode($equal, $list[$i]);
		if ($pair === FALSE || count($pair) < 2)
			return false;
		list($key, $val) = $pair;
		$dict[$key] = $val;
	}
	return $dict;
}

function array2str($arr, $equal='=', $spliter=',')
{
	$t_a = array();
	foreach ($arr as $key => $val)
	{
		$t_a[] = $key . $equal . $val;
	}
	return join($t_a, $spliter);
}

function is_mail_valid($mail)
{
//	$model = "^[a-z0-9]+([._+-]*[a-z0-9])*@([a-z0-9][a-z0-9-]{0,61}[a-z0-9].){1,3}[a-z]{2,6}$";
	$model = "#^\w+([-+.]\w+)*@\w+([-.]\w+)*\.\w+([-.]\w+)*$#";
	if (preg_match($model, $mail))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function is_phone_valid($phone)
{
	$model = "/^1[3584]\d{9}$/i";

	if (preg_match($model, $phone))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function is_valid_session_id($sid)
{
	$model = "^[a-zA-Z0-9\-]+$";
	if (eregi($model, $sid))
	{
		return true;
	}
	else
	{
		return false;
	}
}

function get_str_length($str)
{

	$len = 0;
	for ($i = 0; $i < strlen($str); $i++)
	{
		if (ord(substr($str, $i, 1)) > 0xA0)
		{
			$i += 2;
		}
		else
		{
			$i += 1;
		}
		$len += 1;
	}

	return $len;
}

/*
 */

function rsa_des_decoded($stringEncode, $keyEncode)
{
	//global AM_SITE_ROOT;
	include_once(AM_SITE_ROOT . "inc/funcRsa.inc.php");
	include_once(AM_SITE_ROOT . "inc/libvar.inc.php");
	include_once(AM_SITE_ROOT . "class/BigInteger.class.php");
	include_once(AM_SITE_ROOT . "class/des.class.php");
	if ($stringEncode == '' || $keyEncode == '')
	{
		return false;
	}

	//解密参数
	$key_private = new Math_BigInteger(RSA_PRIVATE_KEY);
	$modulo = new Math_BigInteger(RSA_MODULO);

	$keyEncodeBI = new Math_BigInteger($keyEncode);
	$keyEncodeByte = $keyEncodeBI->toBytes();

	$keyDecode = rsa_decrypt($keyEncodeByte, $key_private->toString(), $modulo->toString(), 1024); //解码后key

	$aKeyDecode = explode("|", $keyDecode);
	$deskey = $aKeyDecode[1];
	$crypt = new CookieCrypt($deskey);
	$paras = $crypt->decrypt($stringEncode);
	//处理参数
	$array['deskey'] = $deskey;
	$array['paras'] = $paras;
	//$aPara = explode("|",$paras);
	return $array;
}

/**
 * 手机号码的正则验证 (包含 +86 香港澳门台湾)
 * bluesie   2010-6-24
 * @access 
 * @param    mobilephone 
 * @return   true false
 */
//已过时
function check_mobilephone($mobilephone)
{
	$mobilephone = trim($mobilephone);
	$aCode = array(853, 852, 886); //澳门香港台湾
	$first = substr($mobilephone, 0, 1);
	if ($first == '+')
	{
		$str = substr($mobilephone, 1, 3);
		if ($aCode)
			if (in_array($str, $aCode))
			{
				$phone = substr($mobilephone, 4);
				if (preg_match("/^13[0-9]{1}[0-9]{8}$|15[0136789]{1}[0-9]{8}$|18[89]{1}[0-9]{8}$/", $phone))
				{
					return true;
				}
				else
				{
					return false;
				}
			}
			else
			{
				$str = substr($mobilephone, 1, 2);
				if ($str == '86')
				{
					$phone = substr($mobilephone, 3);
					if (preg_match("/^13[0-9]{1}[0-9]{8}$|15[0136789]{1}[0-9]{8}$|18[89]{1}[0-9]{8}$/", $phone))
					{
						return true;
					}
					else
					{
						return false;
					}
				}
				else
				{
					return false;
				}
			}
	}
	else
	{
		if (preg_match("/^13[0-9]{1}[0-9]{8}$|15[0136789]{1}[0-9]{8}$|18[689]{1}[0-9]{8}$/", $mobilephone))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

function postCurl($url, $data)
{
	$data = array('json' => json_encode($data));
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_POST, 1);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$result = curl_exec($ch);
	curl_close($ch);
	return json_decode($result, true);
}

function deleteHtmlTags($string, $br = false)
{
	if(!isset($string)||empty($string))
		return "";
	$currentBeg = strpos($string, '<');
	$currentEnd = strpos($string, '>');	
	$len = strlen($string);
	$index = 0;
	while($currentBeg!==false&&$currentEnd!==false)
	{
		if($currentEnd > $currentBeg)
		{
			$tmpStringBeg = 0==($currentBeg+$index)?"":@substr($string, 0, $currentBeg+$index);
			
			$tmpStringEnd = ($currentEnd + 1+$index)>=$len?"":@substr($string, $currentEnd + 1+$index, strlen($string));	
			$string = $tmpStringBeg.$tmpStringEnd;
		}
		else
		{
			$index = $currentBeg;
			if($index>=$len)
				break;
		}
		$currentBeg = strpos(@substr($string, $index), '<');
		$currentEnd = strpos(@substr($string, $index), '>');	
		$len = strlen($string);
	}
	return trim($string);

}

?>
