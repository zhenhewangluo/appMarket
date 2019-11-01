<?php
/**
 +------------------------------------------------------------------------------
 * @description:	UC头像上传类
 +------------------------------------------------------------------------------
 * @others:			NULL
 * @file:			UcCommonAction.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 +------------------------------------------------------------------------------
 */
class TeamdevpAction extends BasedevpAction
{
//	private $_layout = 'Layout:layout'; //当前模块模板页
	public $input = array();
	protected function _initialize()
	{
		parent::_initialize();
	}
	/**
     * 获取显示上传flash的代码(ok)
     * 来源：Ucenter的uc_avatar函数
     * 依赖性：
     *     逻辑代码上为依赖本类和common类；实际操作中还须配合如下文件/组件：
     *         - Ucenter的头像上传flash文件（swf文件）
     */
    public function showuploadAction() {
    	
    	/* $uid =abs((int)($this->_uid));// */
        $uid = Cookie::get(team_id);
		//abs((int)Req::get('uid', 'get'));

        if( $uid === null || $uid == 0 ){
            return -1;
        }
        $returnhtml = Req::get('returnhtml', 'get');
        if( $returnhtml === null  ){
            $returnhtml =  1;
        }
        
        $uc_input = urlencode($this->authcode('uid='.$uid.
                                               '&agent='.md5($_SERVER['HTTP_USER_AGENT']).
                                               "&time=".time(), 
                                                   'ENCODE', C('UC_AUTH_KEY'))
                             );
        
        $uc_avatarflash = C('AVATAR_API').'/Public/flash/camera.swf?nt=1&inajax=1&input='.$uc_input.'&agent='.md5($_SERVER['HTTP_USER_AGENT']).'&ucapi='.urlencode(C('AVATAR_API').'/index.php').'&uploadSize='.C('UC_UPLOAD_SIZE');
        if( $returnhtml == 1 ) {
            $result = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="450" height="253" id="mycamera" align="middle">
			<param name="allowScriptAccess" value="always" />
			<param name="scale" value="exactfit" />
			<param name="wmode" value="transparent" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
			<param name="movie" value="'.$uc_avatarflash.'" />
			<param name="menu" value="false" />
			<embed src="'.$uc_avatarflash.'" quality="high" bgcolor="#ffffff" width="450" height="253" name="mycamera" align="middle" allowScriptAccess="always" allowFullScreen="false" scale="exactfit"  wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>';
            return $result;
        } else {
            return array(
            'width', '450',
            'height', '253',
            'scale', 'exactfit',
            'src', $uc_avatarflash,
            'id', 'mycamera',
            'name', 'mycamera',
            'quality','high',
            'bgcolor','#ffffff',
            'wmode','transparent',
            'menu', 'false',
            'swLiveConnect', 'true',
            'allowScriptAccess', 'always'
            );
        }
    }
    /**
     * 获取显示上传flash的代码(ok)
     * 来源：Ucenter的uc_avatar函数
     * 依赖性：
     *     逻辑代码上为依赖本类和common类；实际操作中还须配合如下文件/组件：
     *         - Ucenter的头像上传flash文件（swf文件）
     */
    public function devpshowuploadAction() {
    	
    	if (Cookie::is_set(team_id))
    	//$uid =abs((int)($this->_uid));//
    	//abs((int)Req::get('uid', 'get'));
    	$uid=Cookie::get(team_id);
    	if( $uid === null || $uid == 0 ){
    		return -1;
    	}
    	$returnhtml = Req::get('returnhtml', 'get');
    	if( $returnhtml === null  ){
    		$returnhtml =  1;
    	}
    
    	$uc_input = urlencode($this->authcode('uid='.$uid.
    			'&agent='.md5($_SERVER['HTTP_USER_AGENT']).
    			"&time=".time(),
    			'ENCODE', C('UC_AUTH_KEY'))
    	);
    
    	$uc_avatarflash = C('AVATAR_API').'/Public/flash/camera.swf?nt=1&inajax=1&input='.$uc_input.'&agent='.md5($_SERVER['HTTP_USER_AGENT']).'&ucapi='.urlencode(C('AVATAR_API').'/index.php').'&uploadSize='.C('UC_UPLOAD_SIZE');
    	if( $returnhtml == 1 ) {
    		$result = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="450" height="253" id="mycamera" align="middle">
			<param name="allowScriptAccess" value="always" />
			<param name="scale" value="exactfit" />
			<param name="wmode" value="transparent" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
			<param name="movie" value="'.$uc_avatarflash.'" />
			<param name="menu" value="false" />
			<embed src="'.$uc_avatarflash.'" quality="high" bgcolor="#ffffff" width="450" height="253" name="mycamera" align="middle" allowScriptAccess="always" allowFullScreen="false" scale="exactfit"  wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>';
    		return $result;
    	} else {
    		return array(
    				'width', '450',
    				'height', '253',
    				'scale', 'exactfit',
    				'src', $uc_avatarflash,
    				'id', 'mycamera',
    				'name', 'mycamera',
    				'quality','high',
    				'bgcolor','#ffffff',
    				'wmode','transparent',
    				'menu', 'false',
    				'swLiveConnect', 'true',
    				'allowScriptAccess', 'always'
    		);
    	}
    }
    
    public function devlshowuploadAction()
    {
    	$uc_input = urlencode($this->authcode('uid='.$uid.
    			'&agent='.md5($_SERVER['HTTP_USER_AGENT']).
    			"&time=".time(),
    			'ENCODE', C('UC_AUTH_KEY'))
    	);
    	$uc_avatarflash = '__PUBLIC__/flash/camera.swf?nt=1&inajax=1&input='.$uc_input.'&agent='.md5($_SERVER['HTTP_USER_AGENT']).'&ucapi='.urlencode(C('AVATAR_API').'/index.php').'&uploadSize='.C('UC_UPLOAD_SIZE');
    	$id=$_REQUEST['id'];
    	if( $id > 0 ) {
    		$result = '<object classid="clsid:d27cdb6e-ae6d-11cf-96b8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=9,0,0,0" width="450" height="253" id="mycamera" align="middle">
			<param name="allowScriptAccess" value="always" />
			<param name="scale" value="exactfit" />
			<param name="wmode" value="transparent" />
			<param name="quality" value="high" />
			<param name="bgcolor" value="#ffffff" />
			<param name="movie" value="'.$uc_avatarflash.'" />
			<param name="menu" value="false" />
			<embed src="'.$uc_avatarflash.'" quality="high" bgcolor="#ffffff" width="450" height="253" name="mycamera" align="middle" allowScriptAccess="always" allowFullScreen="false" scale="exactfit"  wmode="transparent" type="application/x-shockwave-flash" pluginspage="http://www.macromedia.com/go/getflashplayer" />
		</object>';
    		return $result;
    	} else {
    		return array(
    				'width', '450',
    				'height', '253',
    				'scale', 'exactfit',
    				'src', $uc_avatarflash,
    				'id', 'mycamera',
    				'name', 'mycamera',
    				'quality','high',
    				'bgcolor','#ffffff',
    				'wmode','transparent',
    				'menu', 'false',
    				'swLiveConnect', 'true',
    				'allowScriptAccess', 'always'
    		);
    	}
    }
	function uploadavatar()
	{
		echo $this->uploadavatarAction();
	}
	function rectavatar()
	{
		echo $this->rectavatarAction();
	}
    /**
     * 头像上传第一步，上传原文件到临时文件夹（ok）
     *
     * @return string
     */
    function uploadavatarAction() {
        header("Expires: 0");
        header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
        header("Pragma: no-cache");
        //header("Content-type: application/xml; charset=utf-8");
        $this->init_input(Req::get('agent', 'get'));
        $uid = $this->input('uid');
        if(empty($uid)) {
            return -1;
        }
        if(empty($_FILES['Filedata'])) {
            return -3;
        }
        $imgType = C('UC_IMAGE_TYPE');
        $imgext = strtolower('.'. $this->fileext($_FILES['Filedata']['name']));
        if(!in_array($imgext, $imgType)) {
            unlink($_FILES['Filedata']['tmp_name']);
            return -2;
        }
        
        if( $_FILES['Filedata']['size'] > (C('UC_UPLOAD_SIZE') * 1024) ){
            unlink($_FILES['Filedata']['tmp_name']);
            return 'Image is TOO BIG, PLEASE UPLOAD NO MORE THAN '. C('UC_UPLOAD_SIZE') .'KB';
        }
        
        list($width, $height, $type, $attr) = getimagesize($_FILES['Filedata']['tmp_name']);
        
        $filetype = $imgType[$type];
        $tmpavatar = realpath(C('UC_TMP_DIR')).'/upload'.$uid.$filetype;
        file_exists($tmpavatar) && unlink($tmpavatar);
		
        if(is_uploaded_file($_FILES['Filedata']['tmp_name']) && move_uploaded_file($_FILES['Filedata']['tmp_name'], $tmpavatar)) {
            list($width, $height, $type, $attr) = getimagesize($tmpavatar);
            if($width < 50 || $height < 50 || $type == 4) {
                unlink($tmpavatar);
                return '图片高度和宽度不得小于50像素！';
            }
        } else {
            unlink($_FILES['Filedata']['tmp_name']);
            return -4;
        }
		
        $avatarurl = C('AVATAR_API') . '/' .  C('UC_TMP_DIR'). '/upload'.$uid.$filetype;
        return $avatarurl;
    }
    
    /**
     * 头像上传第二步，上传到头像存储位置
     *
     * @return string
     */
    function rectavatarAction() {
        header("Expires: 0");
        header("Cache-Control: private, post-check=0, pre-check=0, max-age=0", FALSE);
        header("Pragma: no-cache");
        header("Content-type: application/xml; charset=utf-8");
        $this->init_input(Req::get('agent', 'get'));
        $uid = abs((int)$this->input('uid'));
        if( empty($uid) || 0 == $uid ) {
            return '<root><message type="error" value="-1" /></root>';
        }

        $avatarpath = $this->get_avatar_path($uid) ;
        $avatarrealdir  = realpath(C('UC_AVA_DIR')).DIRECTORY_SEPARATOR . $avatarpath;
        if(!is_dir( $avatarrealdir )) {
            $this->make_avatar_path( $uid, realpath(C('UC_AVA_DIR')) );
        }
        $avatartype = Req::get('avatartype', 'get') == 'real' ? 'real' : 'virtual';
        
        $avatarsize = array( 1 => 'big', 2 => 'middle', 3 => 'small');
        
        $success = 1;
        $data = array();
        foreach( $avatarsize as $key => $size ){
            $avatarrealpath = realpath(C('UC_AVA_DIR')) . DIRECTORY_SEPARATOR. $this->get_avatar_filepath($uid, $size, $avatartype);
            $avatarcontent = $this->_flashdata_decode(Req::get('avatar'.$key, 'post'));
            if(!$avatarcontent){
                $success = 0;
                return '<root><message type="error" value="-2" /></root>';
                break;
            }
            $writebyte = file_put_contents( $avatarrealpath, $avatarcontent, LOCK_EX );
            if( $writebyte <= 0 ){
                $success = 0;
                return '<root><message type="error" value="-2" /></root>';
                break;
            }
            $avatarinfo = getimagesize($avatarrealpath);
            if(!$avatarinfo || $avatarinfo[2] == 4 ){
                $this->clear_avatar_file( $uid, $avatartype );
                $success = 0;
                break;
            }
			$data[$key] = C('UC_AVA_DIR') . '/'. $this->get_avatar_filepath($uid, $size, $avatartype);
        }
        //原uc bugfix  gif/png上传之后不能删除
        foreach ( C('UC_IMAGE_TYPE') as $key => $imgtype ){
            $tmpavatar = realpath(C('UC_TMP_DIR')).'/upload'. $uid. $imgtype;
            file_exists($tmpavatar) && unlink($tmpavatar);
        }
		//写入头像信息
       
		$userHeadObj = new TeamHeadModel();
		 
		$success = $userHeadObj->updateHead(array(
										'uid'	=> $uid,
										'type'	=> '2',
										'path'	=> implode(',', $data)
									));

        if($success) {
            return '<?xml version="1.0" ?><root><face success="1"/></root>';
        } else {
            return '<?xml version="1.0" ?><root><face success="0"/></root>';
        }
    }
    
    /**
     * flash data decode
     * 来源：Ucenter
     * 
     * @param string $s
     * @return unknown
     */
    protected function _flashdata_decode($s) {
        $r = '';
        $l = strlen($s);
        for($i=0; $i<$l; $i=$i+2) {
            $k1 = ord($s[$i]) - 48;
            $k1 -= $k1 > 9 ? 7 : 0;
            $k2 = ord($s[$i+1]) - 48;
            $k2 -= $k2 > 9 ? 7 : 0;
            $r .= chr($k1 << 4 | $k2);
        }
        return $r;
    }
    /**
     * 获取指定uid的头像规范存放目录格式
     * 来源：Ucenter base类的get_home方法
     * 
     * @param int $uid uid编号
     * @return string 头像规范存放目录格式
     */
    public function get_avatar_path($uid) {
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        return $dir1.'/'.$dir2.'/'.$dir3;
    }

    /**
     * 在指定目录内，依据uid创建指定的头像规范存放目录
     * 来源：Ucenter base类的set_home方法
     * 
     * @param int $uid uid编号
     * @param string $dir 需要在哪个目录创建？
     */
    public function make_avatar_path($uid, $dir = '.') {
        $uid = sprintf("%09d", $uid);
        $dir1 = substr($uid, 0, 3);
        $dir2 = substr($uid, 3, 2);
        $dir3 = substr($uid, 5, 2);
        !is_dir($dir.'/'.$dir1) && mkdir($dir.'/'.$dir1, 0777);
        !is_dir($dir.'/'.$dir1.'/'.$dir2) && mkdir($dir.'/'.$dir1.'/'.$dir2, 0777);
        !is_dir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3) && mkdir($dir.'/'.$dir1.'/'.$dir2.'/'.$dir3, 0777);
    }

    /**
     * 获取指定uid的头像文件规范路径
     * 来源：Ucenter base类的get_avatar方法
     *
     * @param int $uid
     * @param string $size 头像尺寸，可选为'big', 'middle', 'small'
     * @param string $type 类型，可选为real或者virtual
     * @return unknown
     */
	public function get_avatar_filepath($uid, $size = 'big', $type = '') {
		$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
		$uid = abs(intval($uid));
		$uid = sprintf("%09d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		$typeadd = $type == 'real' ? '_real' : '';
		return  $dir1.'/'.$dir2.'/'.$dir3.'/'.substr($uid, -2).$typeadd."_avatar_$size.jpg";
	}
	
	/**
	 * 一次性清空指定uid用户已经存储的头像
	 *
	 * @param int $uid
	 */
	public function clear_avatar_file( $uid ){
	    $avatarsize = array( 1 => 'big', 2 => 'middle', 3 => 'small');
	    $avatartype = array( 'real', 'virtual' );
	    foreach ( $avatarsize as $size ){
	        foreach ( $avatartype as $type ){
	            $avatarrealpath = realpath(C('UC_AVA_DIR')) . DIRECTORY_SEPARATOR. $this->get_avatar_filepath($uid, $size, $type);
	            file_exists($avatarrealpath) && unlink($avatarrealpath);
	        }
	    }
	    return true;
	}
	    /**
     * 初始化输入（ok）
     *
     * @param string $getagent 指定的agent
     */
    public function init_input($getagent = '') {
        $input = Req::get('input', 'req');
        if($input) {
        	
            $input = $this->authcode($input, 'DECODE', 'safsdfsda5643dgsdfgrew');
            parse_str($input, $this->input);
            $this->input = Filter::act($this->input);
			
            $agent = $getagent ? $getagent : $this->input['agent'];

            if(($getagent && $getagent != $this->input['agent']) || (!$getagent && md5($_SERVER['HTTP_USER_AGENT']) != $agent)) {
                exit('Access denied for agent changed');
            } elseif(time() - $this->input('time') > 3600) {
                exit('Authorization has expired');
            }
        }
        if(empty($this->input)) {
            exit('Invalid input');
        }
    }
    
    /**
     * 查找$this->input是否存在指定索引的变量？（ok）
     *
     * @param string $k 要查找的索引
     * @return mixed
     */
	public function input($k) {
		return isset($this->input[$k]) ? (is_array($this->input[$k]) ? $this->input[$k] : trim($this->input[$k])) : NULL;
	}
	/**
     * dz经典加解密函数
     * 来源：Discuz! 7.0
     * 依赖性：可独立提取使用
     *
     * @param string $string 要加密/解密的字符串
     * @param string $operation 操作类型，可选为'DECODE'（默认）或者'ENCODE'
     * @param string $key 密钥，必须传入，否则将中断php脚本运行。
     * @param int $expiry 有效期
     * @return string
     */
    public static function authcode($string, $operation = 'DECODE', $key, $expiry = 0) {

        $ckey_length = 4;	// 随机密钥长度 取值 0-32;
        // 加入随机密钥，可以令密文无任何规律，即便是原文和密钥完全相同，加密结果也会每次不同，增大破解难度。
        // 取值越大，密文变动规律越大，密文变化 = 16 的 $ckey_length 次方
        // 当此值为 0 时，则不产生随机密钥

        //取消UC_KEY，改为必须传入$key才能运行
        if(empty($key)){
            exit('PARAM $key IS EMPTY! ENCODE/DECODE IS NOT WORK!');
        }else{
            $key = md5($key);
        }


        $keya = md5(substr($key, 0, 16));
        $keyb = md5(substr($key, 16, 16));
        $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length): substr(md5(microtime()), -$ckey_length)) : '';

        $cryptkey = $keya.md5($keya.$keyc);
        $key_length = strlen($cryptkey);

        $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
        $string_length = strlen($string);

        $result = '';
        $box = range(0, 255);

        $rndkey = array();
        for($i = 0; $i <= 255; $i++) {
            $rndkey[$i] = ord($cryptkey[$i % $key_length]);
        }

        for($j = $i = 0; $i < 256; $i++) {
            $j = ($j + $box[$i] + $rndkey[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }

        for($a = $j = $i = 0; $i < $string_length; $i++) {
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;
            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;
            $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
        }

        if($operation == 'DECODE') {
            if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
        } else {
            return $keyc.str_replace('=', '', base64_encode($result));
        }

    }
	
	/**
     * 返回文件的扩展名
     * 来源：Discuz!
     * 依赖性：可独立提取使用
     * 
     * @param string $filename 文件名
     * @return string
     */
    public static function fileext($filename) {
        return trim(substr(strrchr($filename, '.'), 1, 10));
    }
}
?>
