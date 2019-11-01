<?php
/**
 * Market公共ajax类
 * @file	AjaxAction.class.php
 * @name	AjaxAction
 * @author	xuhao
 * @desc	AppStore公共ajax类（负责接口ajax操作）
 * @date	2012-3-20
 */
class AjaxAction extends BaseAction
{
//	private $_urlPrefix = 'http://localhost/UserC/index.php?m=User&a=';
//	private $_dbUrlPrefix = 'http://localhost/UserC/index.php?m=DUser&a=';
	private $_urlPrefix = 'http://www.hjapk.com/UserCenter/index.php?m=User&a=';
	private $_dbUrlPrefix = 'http://www.hjapk.com/UserCenter/index.php?m=DUser&a=';
	private $_acttypeArr = array();
	private $_return = array(
		'status' => -1,
		'msg'	=> '发送失败！'
	);
	/**
	 * 初始化公共类相关信息
	 * @method	_initialize()
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	protected function _initialize()
	{
		parent::_initialize();
		$this->_acttypeArr = array(
			C('ACTIVITY_NAME') => 'phone',
			'market' => 'email'
		);
	}
	public function checkUserExisit()
	{
		$this->isLogin();//检测用户是否登录
		$checkType = Filter::act(Req::get('checkType', 'post'));
		$checkVal = Filter::act(Req::get('checkVal', 'post'));
		$return = $this->postCurl($this->_urlPrefix . 'checkUserExisit', array('checkType'=>$checkType, 'checkVal' => $checkVal, 'isDump' => 1));
		die(json_encode($return));
	}
	/**
	 * 发送短信验证码
	 * @method	sendVerify()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-7
	 */
	public function sendVerify()
	{
//		$this->checkTime();
		if(Session::is_set('svTime') && (time() - Session::get('svTime') < 60))
		{
			$this->_return['msg'] = '请勿频繁发送短信！';
			die(json_encode($this->_return));
		}
		$this->_return['msg'] = '发送失败！';
		$mobile = Filter::act(Req::get('mobile', 'get'), 'string', 11);
		$type = Filter::act(Req::get('type', 'get'));
//		$sign = Filter::act(Req::get('sign', 'get'), 'int', 1);
		$sign = $sign == 2 ? 2 : 1; //1为注册新用户，2为修改密码

		if($type != 'bind')
		{
			$this->isLogin();
		}
		else
		{
//		die(var_dump($type));
			if(!$this->isLogin(true))
			{
				$this->_return['msg'] = '请先登录！';
				die(json_encode($this->_return));
			}
		}
		$this->checkMobile($mobile);

		import('ORG.Util.String');
		$_SESSION['mobileCode'] = String::rand_string(4, 1);
		$_SESSION['mobileNum'] = $mobile;
		$data = array(
			'json'	=> json_encode(array(
				'phone'				=> $_SESSION['mobileNum'],
				'messageContent'	=> $_SESSION['mobileCode']
			))
		);
		$re = $this->postCurl($this->_dbUrlPrefix . 'addSmMt', $data);
//		var_dump($re);
		//$re = file_get_contents($this->_dbUrlPrefix . "user/registercode.action?phone={$mobile}&code={$_SESSION['mobileCode']}&signature={$sign}");
		//$re = json_decode($re, true);
//		var_dump($mobile);

		if(!empty($re) && isset($re['result']))
		{
			if($re['result'] == 1)
			{
				$this->_return['status'] = 1;
				$this->_return['msg'] = $re['msg'];
				Session::set('svTime', time());
				die(json_encode($this->_return));
			}
			$this->_return['msg'] = $re['msg'];
		}
		die(json_encode($this->_return));
	}
	/**
	 * 以curl发送post数据
	 * @method	postCurl()
	 * @access private
	 * @param	string	$url	要发送数据的地址
	 * @param	array	$data	要发送的数据
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	private function postCurl($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);//var_dump($result);
		curl_close($ch);
		return json_decode($result, true);
	}
	/**
	 * 检查手机号合法性
	 * @method	checkMobile()
	 * @access private
	 * @param	string	$mobile
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function checkMobile($mobile)
	{
		if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|147[0-9]{8}$/",$mobile))
		{
			$this->_return['status'] = -1;
			$this->_return['msg'] = '请填写正确的手机号！';
			die(json_encode($this->_return));
		}
		return true;
	}
	/**
	 * 登录动作
	 * @method	loginAct()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function loginAct()
	{
		$this->isLogin();//检测用户是否登录
		Cookie::set(C("COOKIE_NAME"), '', -86400);//页端cookie设置
		$acttype	= Filter::act(Req::get('acttype', 'post'));//必传参数 操作类型
		$username	= Filter::act(Req::get('username', 'post'));//必传参数 登录用户名
		$password	= Filter::act(Req::get('password', 'post'));//必传参数 登录密码
//		$verifyCard	= Filter::act(Req::get('verifyCard', 'post'));无验证码

		//判断用户名是邮箱还是密码
		if(strpos($username, '@') !== false)
		{
			$acttype = 'market';
		}
		elseif($this->checkMobile($username))
		{
			$acttype = C('ACTIVITY_NAME');
		}
		else
		{
			$acttype = '';
		}
		$this->isValidAct($acttype);//验证操作类型
//		$this->checkVerifyCard($verifyCard);

		//传递给接口的数据
		$data = array(
			'acttype'	=> $this->_acttypeArr[$acttype],
			'username'	=> $username,
			'password'	=> md5($password)
		);
		//开始传递
		$return = $this->postCurl($this->_urlPrefix . 'loginAct', $data);//与登录接口通信，拿回用户数据

		//判断回传的数据是否合法
		if(isset($return['userinfo']['uid']) && $return['userinfo']['uid'] > 0)
		{
			$userinfo = $return['userinfo'];
			//登录成功提示，夹带用户同步登录其他应用信息
			$synlogin = str_ireplace('javascript', 'dialog', $userinfo['synlogin']);
			$msg = "登录成功！";
//			die(var_dump($synlogin));
			unset($userinfo['synlogin']);
			//无用户名跳转到绑定用户名页面
			$this->_return['status'] = 1;
			$this->_return['msg'] = $msg;
			$this->_return['html'] = <<<EOT
				<li>{$userinfo['phone']}</li>
				<li><span onclick="updatePass();">修改密码</span></li>
				<li><span onclick="logout();">退出</span></li>
EOT;
			if($userinfo['username'] == '')
			{
				Session::set('userinfo', $userinfo);
				$this->_return['msg'] = '登录成功！';
				$this->_return['bindUsername'] = 1;
				die(json_encode($this->_return));
			}
			import('ORG.Crypt.Crypt');
			//写入cookie
			$usercookie = "{$userinfo['uid']}\t{$userinfo['username']}\t{$userinfo['password']}\t{$userinfo['email']}\t{$userinfo['phone']}";
			Cookie::set(C("COOKIE_NAME"), Crypt::encrypt($usercookie, C("DES_KEY"), 1), 30 * 24 * 3600);
			$this->_return['synlogin'] = $synlogin;
		}
		//错误提示信息
		die(json_encode($this->_return));
	}
	private function isLogin($isReturn = false)
	{
		if($isReturn) return $this->_uid > 0 ? true : false;
		if($this->_uid > 0)
		{
			$this->_return['msg'] = '您已经登录了！';
			die(json_encode($this->_return));
		}
	}
	/**
	 * 判断是否是非法操作
	 * @method	isValidAct()
	 * @access private
	 * @param	string	$acttype	操作名称
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function isValidAct($acttype)
	{
		if(!array_key_exists($acttype, $this->_acttypeArr))
		{
			$this->_return['msg'] = '非法操作！';
			die(json_encode($this->_return));
		}
	}
	/**
	 * 注册动作
	 * @method	regAct()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function regAct()
	{
		$callbackUrl  = Req::get('callbackUrl', 'post');
		$callbackUrl = $callbackUrl ? $callbackUrl : U('AppStore://App-RegisterUser/ucenter');//设置页面跳转地址
//		$url = $callbackUrl ? $callbackUrl : U('AppStore://App-RegisterUser/ucenter');
		$this->isLogin($callbackUrl);//检测用户是否登录
		$acttype = Filter::act(Req::get('acttype', 'post'));
		$mobile = Filter::act(Req::get('mobile', 'post'));
		$email = Filter::act(Req::get('email', 'post'));
		$password = md5(Filter::act(Req::get('password', 'post')));
		$repassword = md5(Filter::act(Req::get('repassword', 'post')));
		$mobileVefiry = Filter::act(Req::get('mobileVefiry', 'post'));
		$verifyCard = Filter::act(Req::get('verifyCard', 'post'));
		$username = Filter::act(Req::get('username', 'post'));
		$this->isValidAct($acttype);

//		if($acttype != C('ACTIVITY_NAME'))
//		{
//			$this->checkVerifyCard($verifyCard);
//		}
//		if($acttype == C('ACTIVITY_NAME'))
//		{
//			$repassword = $password
//			$this->checkMobileVerifyCode($mobileVefiry);
//		}
//		if($password != $repassword)
//		{
//			$this->error('两次输入密码不一致！');
//		}

		$data = array(
			'acttype'	=> $this->_acttypeArr[$acttype],
			'username'	=> $username,
			'mobile'	=> $mobile,
			'email'		=> $email,
			'password'	=> $password,
			'activeUrl' => 'http://' . $_SERVER['HTTP_HOST'] . U('AppStore://Common-Index/activeAccount', array('uid' => '{uid}', 'randCode' => '{randCode}'))
		);
		$return = $this->postCurl($this->_urlPrefix . 'regAct', $data);
		if(isset($return['userinfo']['uid']) && $return['userinfo']['uid'] > 0)
		{
//			$userinfo = $return['userinfo'];
			$msg = "注册成功！";
//			$msg = "注册成功！{$userinfo['synlogin']}";
//			unset($userinfo['synlogin']);
//			import('ORG.Crypt.Crypt');
//			$usercookie = "{$userinfo['uid']}\t{$userinfo['username']}\t{$userinfo['password']}\t{$userinfo['email']}\t{$userinfo['phone']}";
//			Cookie::set(C("COOKIE_NAME"), Crypt::encrypt($usercookie, C("DES_KEY"), 1), 30 * 24 * 3600);

			$this->assign('jumpUrl', $callbackUrl);
			$this->success($msg);
			die;
		}
		$this->error($return['msg']);
	}
}
?>
