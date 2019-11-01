<?php
 /*
		@Used      : 接口公共调用函数文件
		@copyright : xxxx8(http://www.xxxx8.com)
		@Authors   : lijinhou 
		@Write Time: 2010/02/09		
		*************edit histroy********
		@lastmodify:
		
	*/
////////////////////////////start
/**
 * @param: null
 * @words: 数组打印输出
 * */
 
function androidarrayout($arr){
	return array2json($arr);
}

function javaarrayout($arr){
	return array2xml($arr);
}

function androiderror2json($error_no){
	return error2json($error_no, $need_dump=true);
}

function javaerror2json($error_no){
	return error2xml($error_no, $need_dump=true);
}


function array2xml($arr, $htmlon = FALSE, $isnormal = TRUE, $level = 1) {
 $s = $level == 1 ? "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\r\n<root>\r\n" : '';
 $space = str_repeat("\t", $level);
 foreach($arr as $k => $v) {
  if(!is_array($v)) {
   $s .= $space."<item id=\"$k\">".($htmlon ? '<![CDATA[' : '').$v.($htmlon ? ']]>' : '')."</item>\r\n";
  } else {
   $s .= $space."<item id=\"$k\">\r\n".array2xml($v, $htmlon, $isnormal, $level + 1).$space."</item>\r\n";
  }
 }
 $s = preg_replace("/([\x01-\x08\x0b-\x0c\x0e-\x1f])+/", ' ', $s);
 return $level == 1 ? $s."</root>" : $s;
}

function array2json($arr) {

	//added by lixiaan to support empty array
	if(count($arr) == 0){
		return '[]';
	}

	if(function_exists('json_encode')) return json_encode($arr); //Lastest versions of PHP already has this functionality.
	$parts = array();
	$is_list = false;

	//Find out if the given array is a numerical array
	$keys = array_keys($arr);
	$max_length = count($arr)-1;
	if(($keys[0] == 0) and ($keys[$max_length] == $max_length)) {//See if the first key is 0 and last key is length - 1
		$is_list = true;
		for($i=0; $i<count($keys); $i++) { //See if each key correspondes to its position
			if($i != $keys[$i]) { //A key fails at position check.
				$is_list = false; //It is an associative array.
				break;
			}
		}
	}

	foreach($arr as $key=>$value) {

		if(is_array($value)) { //Custom handling for arrays
			if($is_list) $parts[] = array2json($value); /* :RECURSION: */
			else $parts[] = '"' . $key . '":' . array2json($value); /* :RECURSION: */
		} else {
			$str = '';
			if(!$is_list) $str = '"' . $key . '":';

			//Custom handling for multiple data types
			if(is_numeric($value)) $str .= $value; //Numbers
			elseif($value === false) $str .= 'false'; //The booleans
			elseif($value === true) $str .= 'true';
			else $str .= '"' . addslashes($value) . '"'; //All other things
			// :TODO: Is there any more datatype we should be in the lookout for? (Object?)

			$parts[] = $str;
		}
	}
	//debug($parts);
	//BUG: author_name字段中的空格被替换成为换行符,在上一句的
	//debug出来的还是空格。
	$json = implode(',',$parts);

	if($is_list) return '[' . $json . ']';//Return numerical JSON
	return '{' . $json . '}';//Return associative JSON
} 

function error2json($error_no, $need_dump=true){

	global $_POST,$INTERFACE_ERRORS;
	if(!array_key_exists($error_no, $INTERFACE_ERRORS)){	
		$error_no = INTERFACE_UNKNOWN_ERROR;		
	}
	if($_POST["PROTO"]){
		$proto = $_POST["PROTO"];
	}elseif($_POST["proto"]){
		$proto = $_POST["proto"];
	}
	$resp_json = array2json(array(
		"proto"      => $proto,
		"reqsuccess" => INTERFACE_REQUEST_FAIL,
		"errno"      => $error_no,
		"errmsg"     => $INTERFACE_ERRORS[$error_no]
	));	
	return $resp_json;
}

function error2xml($error_no, $need_dump=true){
	global $_POST,$INTERFACE_ERRORS;
	if(!array_key_exists($error_no, $INTERFACE_ERRORS)){	
		$error_no = INTERFACE_UNKNOWN_ERROR;		
	}
	if($_POST["PROTO"]){
		$proto = $_POST["PROTO"];
	}elseif($_POST["proto"]){
		$proto = $_POST["proto"];
	}
	$resp_xml = array2xml(array(
		"proto"      => $proto,
		"reqsuccess" => INTERFACE_REQUEST_FAIL,
		"errno"      => $error_no,
		"errmsg"     => $INTERFACE_ERRORS[$error_no]
	));	
	return $resp_xml;
}
