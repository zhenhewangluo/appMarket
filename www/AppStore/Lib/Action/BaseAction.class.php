<?php
/**
 * AppStore基础类
 * @file	BaseAction.class.php
 * @name	IndexAction
 * @author	xuhao
 * @desc	AppStore基础类
 * @date	2011-12-08
 */
class BaseAction extends Action
{
	protected	$_uid = 0; //设定当前用户登录id（全局）0=未登录
	protected	$_username = '';
	protected	$_userinfo = array();
	protected	$_userhead = array();
	protected	$_tplPrefix = '';//设定模板前缀
	protected	$_layout = 'Layout:layout'; //当前模块模板页
	private		$_maketClientId = 6055446;
	private		$_urlPrefix = 'http://192.168.1.135/hjapp_UserCenter/index.php?';
	private		$_moduleArr = array(
		'Active'			=> 'm=Active&a=',
		'ActiveWinner'		=> 'm=ActiveWinner&a=',
		'Address'			=> 'm=Address&a=',
		'Advise'			=> 'm=Advise&a=',
		'Buddy'				=> 'm=Buddy&a=',
		'BuddyGroup'		=> 'm=BuddyGroup&a=',
		'DUser'				=> 'm=DUser&a=',
		'ImportDuobaoUser'	=> 'm=ImportDuobaoUser&a=',
		'Vote'				=> 'm=Vote&a=',
		'User'				=> 'm=User&a=',
		'UserPanel'			=> 'm=UserPanel&a=',
		'AppScore'			=> 'm=AppScore&a='
	);
	protected $_return = array(
		'status' => -1,
		'msg'	=> '发送失败！'

	);
	/**
	 * 初始化网站相关信息
	 * @method	_initialize
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	protected function _initialize()
	{
		header('Content-Type:text/html; charset=utf-8');

		$this->checkMobileDevice();


		$this->_tplPrefix = GROUP_NAME . ':' . MODULE_NAME . C('TMPL_FILE_DEPR');
		//用户登录状态
		$this->assign('title', '***');
		$this->getCss();
		$marketClientInfo = $this->initialMarketClientUrl();//初始化***客户端下载链接
		$this->assign('mcInfo', $marketClientInfo);
		$this->_userinfo = $this->checkLoginCookie();
		if (is_array($this->_userinfo))
		{
			$this->_uid = $this->_userinfo['uid'];
			$this->_username = $this->_userinfo['username'];

			if(isset($this->_userinfo['RegisteredUserHead']['path']))
			{
				$this->_userhead = explode(',',$this->_userinfo['RegisteredUserHead']['path']);
			}
			else
			{
				$userHeadObj = new RegisteredUserHeadModel;
				$userHead = $userHeadObj->getHeadByUid($this->_uid);
				$this->_userhead = explode(',',$userHead['path']);
			}
		}
		$this->assign('acttype', 'market');
		$cateObj = D('Category');
		$mainCateRow = $cateObj->where('parent_id = 0 and visible = 1')->select();
		$this->assign('category', $mainCateRow);
		$this->assign('webUid', $this->_uid);
		$this->assign('webUname', $this->_username);
		$this->assign('webUhead', $this->_userhead);
		$this->assign('webUemail', $this->_userinfo['email']);
	}
	/**
	 * 网站空页面跳转
	 * @method	_empty
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	protected function _empty()
	{
		header('Content-Type:text/html; charset=utf-8');
		header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
		$this->error('404-Document Not Found');
	}
	/*	 * *********************************************
	 * Function:       checkLoginCookie
	 * Description:    检查COOKIE 登录状态
	 * Input:          cookie
	 * Return:         array
	 * Others:
	 * Date：         2011-12-12
	 * Author：       xiaoguang
	 * ************************************************** */

	protected function checkLoginCookie()
	{
//		Vendor('Ucenter.UcApi');  //载入UcApi扩展
		if(Cookie::is_set(C('COOKIE_NAME')))
		{
			import('ORG.Crypt.Crypt');
			$usercookie = array();
			list($usercookie['uid'], $usercookie['username'], $usercookie['password'], $usercookie['email'], $usercookie['phone']) = explode("\t", Crypt::decrypt(Cookie::get(C('COOKIE_NAME')), C('DES_KEY'), 1));
			return $usercookie;
		}
		return 0;
	}

	/*	 * *********************************************
	 * Function:       clearCookie
	 * Description:    清除COOKIE
	 * Input:
	 * Return:
	 * Others:
	 * Date：         2011-12-22
	 * Author：       xiaoguang
	 * ************************************************** */
	protected function clearCookie()
	{
		Cookie::delete(C('COOKIE_NAME'));
		if ($this->_uid > 0)
		{
			//设置用户信息，退出时间为当前时间
			$data = array(
				'id' => $this->_uid,
				'last_logout_time' => date("Y-m-d H:i:s"), //getdate("yyyy-MM-dd HH:mm:ss")
			);
			$userForm = D('RegisterUser');
			if ($userForm->data($data)->save() !== false)
			{
				$this->_userinfo = 0;
				$this->_uid = 0;
			}
		}
	}
	/**
	 * 检查用户合法性
	 * @method	checkUser
	 * @access private
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	private function checkUser()
	{
		$userObj = D('RegisterUser');
		$userRow = $userObj->where("id = {$this->_uid} and name = '{$this->_username}'")->find();
		if(empty($userRow)) return false;
		return true;
	}
	/**
	 * 根据浏览器不同加载不同css（只针对ie6，苦逼啊！！）加载不同的js autovalidate
	 * @method	getCss
	 * @access private
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	private function getCss()
	{
		$css = '';
		$js = '';
		$isIe6 = false;
		if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 6.0"))
		{
			$css = 'gif/';
			$js = 'Ie6';
			$returnFalse = 'return false;';
			$isIe6 = true;
		}
		$this->assign('isIe6', $isIe6);
		$this->assign('returnFalse', $returnFalse);//加载下载链接中的js防止链接直接弹出
		$this->assign('ie6js', $js);//加载autovalidate
		$this->assign('css', $css);//加载css
	}
	/*	 * ***********************************************
	 * Function:       verify()
	 * Description:    用户注册的时候自动生成验证码
	 * Input:
	 * Return:         四位验证码数字
	 * Others:
	 * Date：         2011-12-06
	 * Author：       xiaoguang
	 * *********************************************** */

	public function verify()
	{
		import("ORG.Util.Image");
		Image::buildImageVerify();
	}
	/**
	 * 生成***客户端下载地址
	 * @method	initialMarketClientUrl
	 * @access private
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	private function initialMarketClientUrl()
	{
		$appObj = new AppinfoModel;
		$appRow = $appObj->getAppInfoById($this->_maketClientId, 'app_id');
		$appRow['url'] = $appRow['AppDeviceType'][0]['apk_path'];
		unset($appRow['AppDeviceType']);
		return $appRow;
	}
	/**
	 * 修改用户cookie值
	 * @method	editCookie
	 * @access protected
	 * @param	array()	$cookie	要修改的cookie值 示例：array('email' => 'email')
	 * @return void
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	protected function editCookie($cookie = array())
	{

		foreach($this->_userinfo as $k => $v)
		{
			foreach($cookie as $key => $val)
			{
				if($k == $key)$this->_userinfo[$k] = $val;
			}
		}
		$usercookie = "{$this->_userinfo['uid']}\t{$this->_userinfo['username']}\t{$this->_userinfo['password']}\t{$this->_userinfo['email']}\t{$this->_userinfo['phone']}";
		Cookie::set(C('COOKIE_NAME'), Crypt::encrypt($usercookie, C("DES_KEY"), 1), 30 * 24 * 3600);
	}
	/**
	 * POST发送数据
	 * @method	postCurl
	 * @access protected
	 * @param	string	$module	要post数据的控制器名称
	 * @param	string	$action	要post数据的操作名称
	 * @param	array	$data	要post的数据
	 * @return array
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function postCurl($module, $action, $data = array())
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $this->_urlPrefix . $this->_moduleArr[$module] . $action);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		//die(var_dump($result));
		curl_close($ch);
		return json_decode($result, true);
	}
	/**
	 * checkMobileDevice
	 * @method	checkMobileDevice
	 * @return
	 * @author	zxg
	 * @date	20120524
	 */
	protected function checkMobileDevice()
	{
		if(Session::is_set('noMobile'))
			return;
		if(isset($_GET['nomobile']))
		{
			Session::set('noMobile',1);
			return;
		}
		$useragent = strtolower($_SERVER['HTTP_USER_AGENT']);
		$deviceList = array(
			'htc',
//			'j2me',
//			'ucweb',
//			' uc ',
//			'mqqbrowser',
			'android',
			'macintosh',
			'ipad',
			'iphone',
			'ipod',
			'windows ce',
			'windows phone',
			'symbian',
			'windows',
			);
		$device = "Unknown Device";
		foreach ($deviceList as $v)
		{
			if(strstr($useragent, $v)){
				$device = $v;
				break;
			}
		}
		if(!in_array($device,array(
			'macintosh',
			'ipad',
			'iphone',
			'ipod',
			'windows ce',
			'windows phone',
			'symbian',
			'windows',"Unknown Device")))
		{
			$model = D("AppDeviceType");
			$res = $model->getAppDeviceInfoById(6055446);
			$this->assign('apk_path',$res['apk_path']);
			$this->display('Layout:doorway');
			die();
		}
		return;

	}
	/**
	 * 封装post数组数据成为json
	 * @method	arrToJson
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-28
	 */
	protected function arrToJson($data = array())
	{
		return array('json'	=> json_encode($data));
	}
	public function mail($Address, $Subject, $Msg)
	{
		vendor('PHPMailer.class#phpmailer');
		$mail = new PHPMailer();
		$mail->IsSMTP();
		try
		{
			$mail->Priority = 1;
			$mail->CharSet = 'UTF-8';
			$mail->Host = C('MAIL_SMTPHOST'); // SMTP server
			$mail->SMTPAuth = true;   // enable SMTP authentication
			$mail->Port = C('MAIL_SMTPHOST_PORT');  // set the SMTP port for the GMAIL server
			$mail->Username =C('MAIL_USER'); // SMTP account username
			$mail->Password = C('MAIL_PASS');  // SMTP account password
			$mail->AddAddress($Address);//, 'Mr Dear');
			$mail->SetFrom(C('MAIL_USER').'@gmail.com');//, 'service');
			$mail->Subject = "=?utf-8?B?" . base64_encode($Subject) . "?=";
			$mail->MsgHTML($Msg);
			$mail->Send();
			if($mail->ErrorInfo != '')
			{
				return $mail->ErrorInfo;
			}
		}
		catch (phpmailerException $e)
		{
			Log::write($e->errorMessage(), 'INFO', 3, LOG_PATH.'cSendEmail_'.date('y_m_d').".log");
		}
		catch (Exception $e)
		{
			Log::write($e->errorMessage(), 'INFO', 3, LOG_PATH.'sSendEmail_'.date('y_m_d').".log");
		}
	}
}
?>