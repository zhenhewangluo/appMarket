<?php
/**
 * 用户中心公共ajax类
 * @file	AjaxAction.class.php
 * @name	AjaxAction
 * @author	xuhao
 * @desc	AppStore公共ajax类（负责接口ajax操作）
 * @date	2012-3-20
 */
class AjaxAction extends UcenterBaseAction
{
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
	public function checkUserExisit($type, $val, $isReturn = false)
	{
		$this->isLogin('', false);//检测用户是否登录
		$checkType = !empty($type) ? $type : Filter::act(Req::get('checkType', 'post'));
		$checkVal = !empty($val) ? $val : Filter::act(Req::get('checkVal', 'post'));
		$return = $this->postCurl('User', 'checkUserExisit', array('checkType'=>$checkType, 'checkVal' => $checkVal, 'isDump' => 1));

		if(!$isReturn)die(json_encode($return));

		return $return;
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
		$regType = Filter::act(Req::get('regType', 'get'));


		if($type != 'forgot' && $type != 'bind')
		{
			if(!in_array($type, $this->_acttypeArr))
			{
				$this->isLogin('', false);
			}
			else
			{
				if(!$this->isLogin('', false, true))
				{
					$this->_return['msg'] = '请先登录！';
					die(json_encode($this->_return));
				}
			}
		}

		$this->checkMobile($mobile);
		if($regType == 1)
		{
			$checkRe = $this->checkUserExisit('phone', $mobile, true);
			if($checkRe['isExisit'] == 1){
				die(json_encode($checkRe));
			}
		}
		import('ORG.Util.String');
		$_SESSION['mobileCode'] = String::rand_string(4, 1);
		$_SESSION['mobileNum'] = $mobile;
		$data = array(
			'json'	=> json_encode(array(
				'phone'				=> $_SESSION['mobileNum'],
				'messageContent'	=> $_SESSION['mobileCode']
			))
		);
		$re = $this->postCurl('DUser', 'addSmMt', $data);

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
		$this->isLogin('', false);//检测用户是否登录
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
//		$this->checkVerifyCard($verifyCard, false);

		//传递给接口的数据
		$data = array(
			'acttype'	=> $this->_acttypeArr[$acttype],
			'username'	=> $username,
			'password'	=> md5($password)
		);
		//开始传递
		$return = $this->postCurl('User', 'loginAct', $data);//与登录接口通信，拿回用户数据
//		die(var_dump($return));
		if(0 == $return['status'])
		{
			$this->_return['status'] = $return['status'];
			$this->_return['msg'] = $return['msg'];
			die(json_encode($this->_return));
		}
		//判断回传的数据是否合法
		if(isset($return['userinfo']['uid']) && $return['userinfo']['uid'] > 0)
		{

			$userinfo = $return['userinfo'];
			//登录成功提示，夹带用户同步登录其他应用信息
			$synlogin = str_ireplace('javascript', 'dialog', $userinfo['synlogin']);
			$msg = "登录成功！";
//			die(var_dump($synlogin));
			unset($userinfo['synlogin']);
			$logoutUrl = U('Ucenter-Index/logout');
			$username = (!empty($userinfo['username'])) ? $userinfo['username'] : $userinfo['phone'];
			//无用户名跳转到绑定用户名页面
			$this->_return['status'] = $userinfo['uid'];
			$this->_return['msg'] = $msg;
			$this->_return['html'] = '';
//			$this->_return['html'] = <<<EOT
//			<dl>
//				<dt><strong>{$username}</strong></dt>
//				<dd><span onclick="updatePass();">修改密码</span></dd>
//				<dd><a href="{$logoutUrl}">退出</a></dd>
//			</dl>
//EOT;
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
		$this->_return['status'] = 0;
		$acttype = Filter::act(Req::get('acttype', 'post'));
		$mobile = Filter::act(Req::get('mobile', 'post'));
//		$email = Filter::act(Req::get('email', 'post'));
		$password = md5(Filter::act(Req::get('password', 'post')));
		$repassword = md5(Filter::act(Req::get('repassword', 'post')));
		$mobileVefiry = Filter::act(Req::get('mobileVefiry', 'post'));
//		$verifyCard = Filter::act(Req::get('verifyCard', 'post'));
		$username = Filter::act(Req::get('username', 'post'));
		$this->_return['msg'] = '您的昵称中有敏感词，请重新输入！';
		if(!checkKeyWord($username))
		{
			$this->isValidAct($acttype);
			$data = array(
				'acttype'	=> $this->_acttypeArr[$acttype],
				'username'	=> $username,
				'mobile'	=> $mobile,
				'email'		=> $email,
				'password'	=> $password,
				'activeUrl' => 'http://' . $_SERVER['HTTP_HOST'] . U(GROUP_NAME . '-' . MODULE_NAME . '/activeAccount', array('uid' => '{uid}', 'randCode' => '{randCode}'))
			);
			$return = $this->postCurl('User', 'regAct', $data);
			if(isset($return['userinfo']['uid']) && $return['userinfo']['uid'] > 0)
			{
	//			$userinfo = $return['userinfo'];
				$return['msg'] = "注册成功！";
				$this->_return['status'] = 1;
	//			$msg = "注册成功！{$userinfo['synlogin']}";
	//			unset($userinfo['synlogin']);
	//			import('ORG.Crypt.Crypt');
	//			$usercookie = "{$userinfo['uid']}\t{$userinfo['username']}\t{$userinfo['password']}\t{$userinfo['email']}\t{$userinfo['phone']}";
	//			Cookie::set(C("COOKIE_NAME"), Crypt::encrypt($usercookie, C("DES_KEY"), 1), 30 * 24 * 3600);
			}
			$this->_return['msg'] = $return['msg'];
		}
		die(json_encode($this->_return));
	}
	/**
	 * 更新用户信息
	 * @method	updateUserInfo()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-4-17
	 */
	public function updateUserInfo()
	{
		$this->_return['msg'] = '请先登录！';

		if ($this->_uid <= 0)
			die(json_encode($this->_return));

		$this->_return['msg'] = '修改失败！';
		$data = array(
			'id'				=> $this->_uid,
			'qq'				=> Filter::act(Req::get('qq', 'post')),
			'weburl'			=> Filter::act(Req::get('webUrl', 'post')),
			'address'			=> Filter::act(Req::get('address', 'post')),
			'email_ispublic'	=> Filter::act(Req::get('emailIsPublic', 'post'), 'int'),//邮箱是否公开
			'qq_ispublic'		=> Filter::act(Req::get('qqIsPublic', 'post'), 'int'),//QQ是否公开
			'weburl_ispublic'	=> Filter::act(Req::get('webUrlIsPublic', 'post'), 'int'),//微博是否公开
			'expressdelivery'	=> Filter::act(Req::get('expressDelivery', 'post'), 'int')
		);
		$userObj = D('RegisteredUser');

		$this->_return['msg'] = '更新失败！';
		if($userObj->updateUserInfo($data))
		{
			$this->_return['status'] = 1;
			$this->_return['msg'] = '更新成功！';
		}
//		$result = $this->postCurl('UserPanel', 'updateUserInfo', $data);
//		if(isset($result['status']))
//		{
//			$this->_return['status'] = $result['status'];
//			$this->_return['msg'] = $result['msg'];
//		}
		die(json_encode($this->_return));
	}
	/**
	 * 登出动作
	 * @method	logout()
	 * @access public
	 * @param	void
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	public function logout()
	{
		$ucLogout = '';
		$this->_return['msg'] = '请先登录！';
		if ($this->_uid > 0)
		{
			$this->_return['msg'] = '注销失败！';
			//设置用户信息，退出时间为当前时间
			$data = array(
				'id' => $this->_uid,
				'last_logout_time' => date("Y-m-d H:i:s"), //getdate("yyyy-MM-dd HH:mm:ss")
			);
			$userForm = D('RegisterUser');
			if ($userForm->data($data)->save() !== false)
			{
				Cookie::delete(C('COOKIE_NAME'));
				$this->_userinfo = 0;
				$this->_uid = 0;
				$return = $this->postCurl('User', 'logoutAct');
				$this->_return['status'] = 1;
				$this->_return['msg'] = '退出成功！';
				$this->_return['synlogout'] = $return['synlogout'];
			}
		}
		die(json_encode($this->_return));
	}
	/**
	 * 修改密码
	 * @method	updatePass()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-4-17
	 */
	public function updatePass()
	{
		$this->_return['msg'] = '请先登录！';

		if ($this->_uid > 0)
		{
			$password = md5(Filter::act(Req::get('pwd', 'post')));
			$newpassword = md5(Filter::act(Req::get('newpwd', 'post')));
			$newrepassword = md5(Filter::act(Req::get('newrepwd', 'post')));
			$this->_return['msg'] = '两次密码不一致！';
			if($newpassword == $newrepassword)
			{
				$data = array(
					'uid'			=> $this->_uid,
					'password'		=> $password,
					'newpassword'	=> $newpassword
				);
				$result = $this->postCurl('User', 'updatePassAct', $data);
				if(isset($result['status']))
				{
					if($result['status'])
						Cookie::set(C("COOKIE_NAME"), '', -86400);//页端cookie设置

					$this->_return['status'] = $result['status'];
					$this->_return['msg'] = $result['msg'];
				}
			}
		}
		die(json_encode($this->_return));
	}
	/**
	 * 修改昵称
	 * @method	editUsername()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-4-17
	 */
	public function editUsername()
	{
		$this->_return['msg'] = '请先登录！';

		if ($this->_uid <= 0)
			die(json_encode($this->_return));
		$username = Filter::act(Req::get('username', 'post'));

		$this->_return['msg'] = '修改失败！';
		$data = array(
			'uid'				=> $this->_uid,
			'username'			=> $username
		);
//		die(var_dump($data));
		$result = $this->postCurl('User', 'editUsernameAct', $data);
//		die(var_dump($result));
//		$result = $this->postCurl('UserPanel', 'updateUserInfo', $data);

		if(isset($result['status']))
		{
			if($result['status'] == 1)$this->editCookie(array('username' => $username));
			$this->_return['status'] = $result['status'];
			$this->_return['msg'] = $result['msg'];
		}
		die(json_encode($this->_return));
	}
}
?>
