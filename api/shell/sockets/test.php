<?php 
$arrData[0]['id'] = $_POST['id'];
$arrData[0]['type'] = $_POST['type'];
$arrData[0]['typeid'] = $_POST['typeid'];
$arrData[0]['typename'] = $_POST['typename'];
$arrData[0]['desc'] = $_POST['desc'];
$arrData[0]['rec_desc'] = $_POST['rec_desc'];
$arrData[0]['image'] = $_POST['image'];
$arrData[0]['icon'] = $_POST['icon'];
$arrData[0]['addtime'] = $_POST['addtime'];
/////JSON编码 
$mes = array2json($arrData);
$message = "{\"imei\":\"xxxxSystem\",\"handler\":\"MessageHandler\",\"action\":\"send\",\"to\":\"all\",\"sms\":".$mes."}";

$gS=new getService();
//61.152.167.18:5525
//172.17.4.65 //黎欣IP
//218.246.22.91
$gS->init("172.17.4.65:55555",10);
$gS->_connect();
$gS->_writeData( $message );
//$gS->_readData();
$gS->_disconnect();
echo $message;
echo "<br/><br/>".rand(1,99);
class getService{
    var $errno=5;
    var $errstr="error";
    function init($str,$_timeout){
        $arr=explode(':',$str);
        $this->_ip=$arr[0];
        $this->_port=$arr[1];
        $this->_timeout=$_timeout;
    }
    function _connect(){
        if (!$this->_socket = @fsockopen("tcp://".$this->_ip, $this->_port, $errno, $errstr, $this->_timeout)){
            return false;
        }else{
            return true;
        }
    }
    
    function _disconnect()
    {
    if (!@fclose($this->_socket)) return false;
    return true;
    }
    //这里就是关键部分，hlds的指令是以\xFF\xFF\xFF\xFF,四个255的控制指令开始，以\x00一个空指令结尾，其中的command可以是info、ping、players、rules、details等等，分别得到不同的信息。
    function _writeData($command){
        if (!@fwrite($this->_socket, $command )){
            return false;
        }else{
            return true;
        }
    }
    function _readData()
    {
        socket_set_timeout($this->_socket, $this->_timeout);
        $data = fread($this->_socket, 8000);
        echo ord($data);
        die;
        //if (socket_timeout($this->_socket)) return false;
        switch (ord($data)) {
        case 255:
            $status = socket_get_status($this->_socket);
            socket_set_timeout($this->_socket, $this->_timeout);
            $data .= fread($this->_socket, $status["unread_bytes"]);
            //if (socket_timeout($this->_socket)) return false;
            break;
        case 254:
            $status = socket_get_status($this->_socket);
            socket_set_timeout($this->_socket, $this->_timeout);
            fread($this->_socket, 7);
            //if (socket_timeout($this->_socket)) return false;
            socket_set_timeout($this->_socket, $this->_timeout);
            $data = fread($this->_socket, 1);
            //if (socket_timeout($this->_socket)) return false;
            $bits = sprintf("%08b",ord($data));
            $count = bindec(substr($bits, -4));
            $x = bindec(substr($bits, 0, 4));
            $status = socket_get_status($this->_socket);
            socket_set_timeout($this->_socket, $this->_timeout);
            $datagrams[$x] = fread($this->_socket, $status["unread_bytes"]);
            //if (socket_timeout($this->_socket)) return false;
            for ($i=1; $i< $count; $i++) {
                socket_set_timeout($this->_socket, $this->_timeout);
                fread($this->_socket, 8);
                //if (socket_timeout($this->_socket)) return false;
                socket_set_timeout($this->_socket, $this->_timeout);
                $data = fread($this->_socket, 1);
                //if (socket_timeout($this->_socket)) return false;
                $x = bindec(substr(sprintf("%08b",ord($data)), 0, 4));
                $status = socket_get_status($this->_socket);
                socket_set_timeout($this->_socket, $this->_timeout);
                $datagrams[$x] = fread($this->_socket, $status["unread_bytes"]);
                //if (socket_timeout($this->_socket)) return false;
            }
            $data = "";
            for ($i=0; $i< $count; $i++) {
                $data .= $datagrams[$i];
            }
            break;
        default:
            break;
        }
        echo $data;
        //return true;
    }

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
?>