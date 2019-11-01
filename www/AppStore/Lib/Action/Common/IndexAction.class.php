<?php
/**
 * Market公共操作类
 * @file	IndexAction.class.php
 * @name	IndexAction
 * @author	xuhao
 * @desc	AppStore公共类（负责公共操作，登入登出等操作）
 * @date	2012-3-20
 */
class IndexAction extends BaseAction
{
	private $_urlPrefix = 'http://www.hjapk.com/UserCenter/index.php?m=User&a=';
//	private $_urlPrefix = 'http://localhost/UserC/index.php?m=User&a=';
	private $_dbUrlPrefix = 'http://www.hjapk.com:8080/kxdbsvr/';
	private $_acttypeArr = array();
	private $_return = array(
		'status' => -1,
		'msg'	=> '发送失败！'
	);
	private $_bindtypeArr = array();
	private $_bindtypeActArr = array();
	protected function _empty()
	{
		parent::_empty();
	}
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
		$this->_layout = 'Layout:common'; //当前模块模板页
		$this->_acttypeArr = array(
			C('ACTIVITY_NAME') => 'phone',
			'market' => 'email'
		);
		$this->_bindtypeArr = array(
			C('ACTIVITY_NAME') => '手机号',
			C('BBS_NAME') => '昵称',
			'market' => '邮箱'
		);
		$this->_bindtypeActArr = array(
			C('ACTIVITY_NAME') => 'phone',
			C('BBS_NAME') => 'nickname',
			'market' => 'email'
		);
		import('@.ORG.AppRoute');
	}
	/**
	 * 展示登录页面
	 * @method	login()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function login()
	{

		$callbackUrl = Req::get('callbackUrl', 'get');//获取跳转页面地址
		$callbackUrl = $this->getCallbackUrl($callbackUrl);//设置页面跳转地址
		$this->isLogin($callbackUrl);//检测用户是否登录

		$acttype = Filter::act(Req::get('acttype', 'get'));

		$this->isValidAct($acttype);

		$this->assign('acttype', $acttype ? $acttype : 'market');
		$this->assign('callbackUrl', $callbackUrl);
		$this->assign('viewcontent', $this->_tplPrefix . 'login');
		$this->display($this->_layout);
	}
	/**
	 * 展示注册页面
	 * @method	reg()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function reg()
	{

		$callbackUrl = Req::get('callbackUrl', 'get');//获取跳转页面地址
		$callbackUrl = $this->getCallbackUrl($callbackUrl);//设置页面跳转地址

		$this->isLogin($callbackUrl);//检测用户是否登录
		$acttype = Filter::act(Req::get('acttype', 'get'));

		$this->isValidAct($acttype);
		$this->assign('callbackUrl', $callbackUrl);
		$this->assign('acttype', $acttype);
		$this->assign('viewcontent', 'reg');
		$this->display($this->_layout);
	}
	/**
	 * 展示绑定页面
	 * @method	bind()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function bind()
	{
//		var_dump($this->_userinfo);
		$callbackUrl = Req::get('callbackUrl', 'get');
		$callbackUrl = $this->getCallbackUrl(urldecode($callbackUrl));//记录来路，跳转使用

		$acttype = Filter::act(Req::get('acttype', 'get'));
		if($acttype != C('BBS_NAME'))
		{
			$this->checkLogin();
		}
		$this->isValidBindAct($acttype);
		if($acttype == C('ACTIVITY_NAME'))
		{
			$this->isHaveMobile();
		}
		elseif ($acttype != C('BBS_NAME'))
		{
			$this->isHaveEmail();
		}
		$this->assign('acttype', $acttype);
		$this->assign('callbackUrl', $callbackUrl);
		$this->assign('bindtype', $this->_bindtypeArr[$acttype]);
		$this->assign('viewcontent', $this->_tplPrefix . 'bind');
		$this->display($this->_layout);
	}
	/**
	 * 绑定动作
	 * @method	bindAct()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function bindAct()
	{

//		$bindtype = Filter::act(Req::get('acttype', 'get'));
//		$this->checkLogin();
		$callbackUrl = Req::get('callbackUrl', 'post');
		$callbackUrl = $this->getCallbackUrl($callbackUrl);
//		$this->isLogin($callbackUrl);//检测用户是否登录
		$acttype = Filter::act(Req::get('acttype', 'post'));
		if($acttype != C('BBS_NAME'))
		{
			$this->checkLogin();
		}
		$mobile = Filter::act(Req::get('mobile', 'post'));
		$mobileVerify = Filter::act(Req::get('mobileVerify', 'post'));
		$verifyCard = Filter::act(Req::get('verifyCard', 'post'));
		$this->isValidBindAct($acttype);
		$this->checkVerifyCard($verifyCard);
		$username = '';
		$bindact = '';
		$uid = $this->_uid;

		if($acttype == C('ACTIVITY_NAME'))
		{
			$this->isHaveMobile();
			$username = $mobile;
			$bindact = 'bindMobile';
			$callbackUrl = $callbackUrl ? $callbackUrl : U('AppStore://Activity-Index/index');//记录来路，跳转使用
		}
		elseif ($acttype == C('BBS_NAME'))
		{
			if(!Session::is_set('userinfo'))
			{
				$this->error('请先登录');
			}
			$uid = $_SESSION['userinfo']['uid'];
			$username = Filter::act(Req::get('nickname', 'post'));
			$bindact = 'bindNickname';
			$callbackUrl = $callbackUrl ? $callbackUrl : AppRoute::getRefRoute();//记录来路，跳转使用
		}
		else
		{
			$this->isHaveEmail();
			$username = Filter::act(Req::get('email', 'post'));
			$bindact = 'bindEmail';
			$callbackUrl = $callbackUrl ? $callbackUrl : AppRoute::getRefRoute();//记录来路，跳转使用
		}
		$data = array(
			'bindtype'	=> $bindact,
			'username'	=> $username,
			'uid'		=> $uid
		);
//		die(var_dump($data));
		$return = $this->postCurl($this->_urlPrefix . $bindact, $data);
//		die(var_dump($return));
		$synlogin = '';
		if(isset($return['status']) && $return['status'] == 1)
		{

			if($acttype == C('ACTIVITY_NAME'))
			{
				$this->editCookie(array('phone' => $mobile));
			}
			elseif ($acttype == C('BBS_NAME'))
			{
				if(Session::is_set('userinfo'))
				{
					$usercookie = Session::get('userinfo');
					import('ORG.Crypt.Crypt');
					$usercookie = "{$usercookie['uid']}\t{$username}\t{$usercookie['password']}\t{$usercookie['email']}\t{$usercookie['phone']}";
					Cookie::set(C("COOKIE_NAME"), Crypt::encrypt($usercookie, C("DES_KEY"), 1), 30 * 24 * 3600);
					$synlogin = $return['synlogin'];
				}
			}
			else
			{
				$this->editCookie(array('email' => Filter::act(Req::get('email', 'post'))));
			}
//			if(Session::is_set('userinfo') && )
//			import('ORG.Crypt.Crypt');
//			$usercookie = "{$return['uid']}\t{$return['username']}\t{$return['password']}\t{$return['email']}\t{$return['phone']}";
//			Cookie::set(C("COOKIE_NAME"), Crypt::encrypt($usercookie, C("DES_KEY"), 1), 30 * 24 * 3600);
			$this->assign('jumpUrl', $callbackUrl);
			$this->success("绑定成功！{$synlogin}");
			die;
		}
		$this->error($return['msg']);
	}
	/**
	 * 展示忘记密码页面
	 * @method	forgotPass()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function forgotPass()
	{
		$callbackUrl = Req::get('callbackUrl', 'get');//获取跳转页面地址
		$callbackUrl = $this->getCallbackUrl($callbackUrl);//设置页面跳转地址
		$this->assign('callbackUrl', $callbackUrl);

		$acttype = Filter::act(Req::get('acttype', 'get'));

		$this->isValidAct($acttype);

		$this->assign('acttype', $acttype);
		$this->assign('viewcontent', $this->_tplPrefix . 'forgotPass');
		$this->display($this->_layout);
	}
	/**
	 * 展示重置密码页面
	 * @method	resetPass()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function resetPass()
	{

		$callbackUrl = Req::get('callbackUrl', 'get');//获取跳转页面地址
		$callbackUrl = $this->getCallbackUrl($callbackUrl);//设置页面跳转地址

		$resetcode = Filter::act(Req::get('id', 'get'));
		if(empty($resetcode) || strlen($resetcode) != 32)
			$this->error('非法操作！');
		$this->assign('resetcode', $resetcode);
		$this->assign('callbackUrl', $callbackUrl);
		$this->assign('viewcontent', $this->_tplPrefix . 'resetPass');
		$this->display($this->_layout);
	}
	/**
	 * 重置密码动作
	 * @method	resetPassAct()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function resetPassAct()
	{
		$callbackUrl = Req::get('callbackUrl', 'post');//获取跳转页面地址
		$callbackUrl = $this->getCallbackUrl($callbackUrl);//设置页面跳转地址
		$resetcode = Filter::act(Req::get('resetcode', 'post'));
		if(empty($resetcode) || strlen($resetcode) != 32)
			$this->error('非法操作！');
		$password = md5(Filter::act(Req::get('password', 'post')));
		$repassword = md5(Filter::act(Req::get('repassword', 'post')));
		if($password != $repassword)
		{
			$this->error('两次输入密码不一致！');
		}
		$return = $this->postCurl($this->_urlPrefix . 'resetPassAct', array('resetcode'=>$resetcode, 'password' => $password));
//		die(var_dump($return));
		if(isset($return['status']))
		{
			if($return['status'] > 0)
			{
				Cookie::set(C("COOKIE_NAME"), '', -86400);//页端cookie设置
				$this->assign('jumpUrl', U('AppStore://Common-Index/login', array('acttype' => 'market')));
				$this->success($return['msg'] . $return['synlogout']);
				die;
			}
			$this->error($return['msg']);
		}
		$this->error('重置密码失败！');
	}
	/**
	 * 忘记密码动作
	 * @method	forgotPassAct()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function forgotPassAct()
	{

		$callbackUrl = Req::get('callbackUrl', 'post');//获取跳转页面地址
		$callbackUrl = $this->getCallbackUrl($callbackUrl);//设置页面跳转地址
//		$this->isLogin($callbackUrl);//检测用户是否登录
		$acttype = Filter::act(Req::get('acttype', 'post'));

		$this->isValidAct($acttype);
		$verifyCard = Filter::act(Req::get('verifyCard', 'post'));
		$this->checkVerifyCard($verifyCard);
		//声明要post的数据
		$data = array(
			'acttype'	=> $this->_acttypeArr[$acttype],
		);
		if($acttype == C('ACTIVITY_NAME'))
		{
			$mobile = Filter::act(Req::get('mobile', 'post'));
			$password = Filter::act(Req::get('password', 'post'));
			$repassword = Filter::act(Req::get('repassword', 'post'));
			if($password != $repassword)
			{
				$this->error('两次输入密码不一致！');
			}
			$data['username'] = $mobile;
			$data['password'] = md5($password);

		}
		else
		{
			$email = Filter::act(Req::get('email', 'post'));
			$data['username'] = $email;
			$data['resetUrl'] = 'http://' . $_SERVER['HTTP_HOST'] . U('AppStore://Common-Index/resetPass', array('id' => ''));
		}
		$return = $this->postCurl($this->_urlPrefix . 'forgotPassAct', $data);
		if(isset($return['status']))
		{
			if($return['status'] > 0)
			{
				Cookie::set(C("COOKIE_NAME"), '', -86400);//页端cookie设置
				$this->assign('jumpUrl', U('AppStore://Common-Index/login', array('acttype' => 'market')));
				$this->success($return['msg']);
				die;
			}
			$this->error($return['msg']);
			die;
		}
		$this->error('重置失败！');
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
		//设置页面跳转地址
		$callbackUrl = $this->getCallbackUrl($callbackUrl, U('AppStore://Common-Index/login'));//U('AppStore://App-RegisterUser/ucenter')
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

		if($password != $repassword)
		{
			$this->error('两次输入密码不一致！');
		}

		if($acttype != C('ACTIVITY_NAME'))
		{
			$this->checkVerifyCard($verifyCard);
		}

		if($acttype == C('ACTIVITY_NAME'))
		{
			$this->checkMobileVerifyCode($mobileVefiry);
		}

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
			$this->success($return['msg']);
			die;
		}
		$this->error($return['msg']);
	}
	/**
	 * 发送短信验证码
	 * @method	activeAccount()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-7
	 */
	public function activeAccount()
	{
		$data = array(
			'uid'		=> Filter::act(Req::get('uid', 'get')),//用户ID
			'randCode'	=> Filter::act(Req::get('randCode', 'get'))//邮箱激活随机码
		);
		$return = $this->postCurl($this->_urlPrefix . 'activeAccount', $data);
		if(!empty($return))
		{
			if(array_key_exists('status', $return) && $return['status'] > 0)
			{
				$this->assign('jumpUrl', U('AppStore://Common-Index/login', array('acttype' => 'market')));
				$this->success($return['msg']);
				die;
			}
			if(array_key_exists('reActive', $return))
			{
				$reActiveUrl = U('AppStore://Common-Index/reActiveAccount');
				$this->error($return['msg']);
				die;
			}
		}
		$this->error('激活失败！');
	}
	/**
	 * 登录动作
	 * @method	login()
	 * @access	public
	 * @param	void
	 * @return	void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	public function loginAct()
	{

		$callbackUrl = Req::get('callbackUrl', 'post');//获取跳转页面地址
		$callbackUrl = $this->getCallbackUrl($callbackUrl, U('AppStore://App-RegisterUser/ucenter'));//设置页面跳转地址
		$this->isLogin($callbackUrl);//检测用户是否登录

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
//		die(var_dump($return));
		//判断回传的数据是否合法
		if(isset($return['userinfo']['uid']) && $return['userinfo']['uid'] > 0)
		{
			$userinfo = $return['userinfo'];
			//登录成功提示，夹带用户同步登录其他应用信息
			Vendor('Ucenter.UcApi');  //载入UcApi扩展
			$synlogin = uc_user_synlogin($return['userinfo']['uid']);
			$msg = "登录成功！{$synlogin}";
			unset($userinfo['synlogin']);
			//无用户名跳转到绑定用户名页面
			if($userinfo['username'] == '')
			{
				Session::set('userinfo', $userinfo);
				$this->assign('jumpUrl', U('AppStore://Common-Index/bind', array('acttype' => C('BBS_NAME'), 'callbackUrl' => $callbackUrl)));
				$this->success('登录成功！');die;
			}
			import('ORG.Crypt.Crypt');
			//写入cookie
			$usercookie = "{$userinfo['uid']}\t{$userinfo['username']}\t{$userinfo['password']}\t{$userinfo['email']}\t{$userinfo['phone']}";
			Cookie::set(C("COOKIE_NAME"), Crypt::encrypt($usercookie, C("DES_KEY"), 1), 30 * 24 * 3600);
//			var_dump($usercookie);
//			die(var_dump(explode("\t", Crypt::decrypt(Cookie::get(C('COOKIE_NAME')), C('DES_KEY'), 1))));


			$this->assign('jumpUrl', $callbackUrl);
			$this->success($msg);die;
		}
		//错误提示信息
		$this->error($return['msg']);
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
	 * 检测用户是否登录
	 * @method	isLogin()
	 * @access private
	 * @param	string	$url	要跳转的url
	 * @param	boolean	$jump	是否直接跳转
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	private function isLogin($url, $jump = true)
	{
		if($this->_uid > 0)
		{
			if($jump)
			{
				$this->assign('jumpUrl', U('AppStore://App-Index/index'));
				$this->error('您已经登录了！');
				die;
			}
			$this->_return['msg'] = '您已经登录了！';
			die(json_encode($this->_return));
		}
		return false;
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
	 * 发送密码重置邮件
	 * @method	sendResetPassEmail()
	 * @access private
	 * @param	string	$mobile
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function sendResetPassEmail()
	{

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
			$this->error('非法操作');die;
		}
	}
	/**
	 * 判断是否是非法绑定操作
	 * @method	isValidAct()
	 * @access private
	 * @param	string	$bindtype	操作名称
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function isValidBindAct($bindtype)
	{
		if(!array_key_exists($bindtype, $this->_bindtypeArr))
		{
			$this->error('非法操作');die;
		}
	}
	/**
	 * 判断手机验证码是否正确
	 * @method	checkMobileVerifyCode()
	 * @access private
	 * @param	string	$mobileVefiry	手机验证码
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function checkMobileVerifyCode($mobileVefiry)
	{

		if(Session::is_set('mobileCode') && Session::get('mobileCode') == $mobileVefiry)
		{
			return true;
		}
		$this->error('手机验证码错误！');
		die;
	}
	/**
	 * 判断手机验证码是否正确
	 * @method	checkVerifyCard()
	 * @access private
	 * @param	string	$verifyCard	验证码
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function checkVerifyCard($verifyCard)
	{
		if (md5($verifyCard) != Session::get('verify'))
		{
			$this->error('验证码错误！');
			die;
		}
		return true;
	}
	/**
	 * 判断用户是否有手机号
	 * @method	isHaveMobile()
	 * @access private
	 * @param	string	$verifyCard	验证码
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function isHaveMobile()
	{
		$this->checkLogin();
		if(strlen($this->_userinfo['phone']) > 0)
		{
			$this->error('您已经绑定过手机号了！');
		}
		return true;
	}
	/**
	 * 判断用户是否有邮箱
	 * @method	isHaveEmail()
	 * @access private
	 * @param	string	$verifyCard	验证码
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function isHaveEmail()
	{
		$this->checkLogin();
		if(strlen($this->_userinfo['email']) > 0)
		{
			$this->error('您已经绑定过邮箱了！');
		}
		return true;
	}
	/**
	 * 判断用户是否有昵称
	 * @method	isHaveNickname()
	 * @access private
	 * @param	string	$verifyCard	验证码
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function isHaveNickname()
	{
		$this->checkLogin();
		if(strlen($this->_userinfo['username']) > 0)
		{
			$this->error('您已经绑定过昵称了！');
		}
		return true;
	}
	/**
	 * 判断用户是否登录
	 * @method	checkLogin()
	 * @access private
	 * @param	void
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function checkLogin()
	{
//		die(var_dump($this->_uid));
		if($this->_uid > 0)
		{
			return true;
		}
		$this->error('请先登录！');die;
	}
	/**
	 * 登出动作
	 * @method	logout()
	 * @access private
	 * @param	void
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	public function logout()
	{
//		$callbackUrl = Req::get('callbackUrl', 'get');
//		$callbackUrl = $callbackUrl ? $callbackUrl : AppRoute::getRefRoute();//记录来路，跳转使用
		$ucLogout = '';
		$this->assign('jumpUrl', U('AppStore://Common-Index/login', array('acttype' => 'market')));
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
				Cookie::delete(C('COOKIE_NAME'));
//				Cookie::delete(C('COOKIE_PASS'));

				$this->_userinfo = 0;
				$this->_uid = 0;
				$return = $this->postCurl($this->_urlPrefix . 'logoutAct');
				$this->success("{$return['synlogout']}注销成功！");
			}
			$this->error('注销失败！');
		}
		$this->error('未登录！');
	}
	/**
	 * 获取回跳链接
	 * @method	getCallbackUrl()
	 * @access	private
	 * @param	string	$callbackUrl	获取回跳的url
	 * @param	string	$trueJumpUrl	如果$callbackUrl并且$trueJumpUrl为真，则跳转$trueJumpUrl
	 * @return	string
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function getCallbackUrl($callbackUrl, $trueJumpUrl)
	{
		$excludeUrl = array(
			'reg',
			'login',
			'logout',
			'activeAccount',
			'forgotPass',
			'resetPass'
		);
		$httpReferrer = AppRoute::getRefRoute();//自动获取的前一页url
		$jumpUrl = !empty($callbackUrl) ? $callbackUrl : $httpReferrer;

//		$jumpUrl = $callbackUrl;//传参进来的前一页url
		//只有当自动获取的前一页url和传参进来的url都不包含上面数组中的关键字，才返回。
		foreach($excludeUrl as $k => $v)
		{
			if(strpos($jumpUrl, $v) !== false)
			{
				$jumpUrl = '';
				break;
			}
		}
		return (empty($jumpUrl) && !empty($trueJumpUrl)) ? $trueJumpUrl : $jumpUrl;//记录来路，跳转使用
	}
}
?>