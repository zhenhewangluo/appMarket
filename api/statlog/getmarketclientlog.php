<?php
/*
		@Authors   : lijinhou  English_name:jessy
		@Email     : ljhynlp@sina.com,ljhynlp@126.com
		@qq        : 298004051
		@msn       : ljhynlp@hotmail.com
		@Write Time: 2010/11/3
	*/
/*
	提交案例：
*/
define('CLINTLOGDIR', "client/");     

$action = $_POST["action"];
/*
if($action !="aimi8client"){
	errormsg();
}
*/
$mid = $_POST["mid"];
$uid = $_POST["uid"];
$filename = $mid."_".date("Y-m-d_H_i_s").".txt";
$filecontents = $_POST["fcode"];
if($filecontents!=""){
	$filecontentsarr = explode("^",$filecontents);
	$filecontents = implode("\n",$filecontentsarr);
}

$todaydirlog = CLINTLOGDIR.date("Y-m-d");
if(!is_dir($todaydirlog)){
	@mkdir($todaydirlog, 0777);
}
file_put_contents($todaydirlog."/".$filename, $filecontents);

echo array2json(array(
	"proto" => $_POST["proto"],
	"reqsuccess" =>  true
));

function errormsg(){
	exit("error");
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
