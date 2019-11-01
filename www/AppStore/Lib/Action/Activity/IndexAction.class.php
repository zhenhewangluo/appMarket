<?php
/**
 * 夺宝活动控制器
 * @file	IndexAction.class.php
 * @name	IndexAction
 * @author	xuhao
 * @desc	夺宝活动控制器
 * @date	2011-12-08
 */
class IndexAction extends ActivityBaseAction
{	
	protected $dUinfo = array(
		'uid' => 0,
		'pwd' => '',
		'activeId' => 0
	);
	
	protected $_return = array(
		'status' => -1,
		'msg'	=> '发送失败！'
	);
	
	private $_serverData = array(
		'209' => '用户未注册返回',
		'208' => 'signature参数不正确 ',
		'207' => '该好友每天只能邀请一次',
		'206' => '不能给自己拉选票',
		'205' => '用户不存在',
		'204' => '手机号码不得为空',
		'203' => '不符合手机号码规则',
		'202' => '数据库操作失败',
		'201' => '用户已经注册',
		'200' => '成功',
		//'2'	=> '不能给自己拉选票'
	);
	
	private $_prefixUrl = 'http://www.hjapk.com:8080/kxdbsvr/';

	private $actArr = array(
		'pull' => 'pageadviseds', //当前用户邀请信息
		'push' => 'pagevotetootherlist', //当前用户投票信息
		'vote' => 'pagevotetomelist', //当前用户被投票信息
		'pageregister', //注册
		'registercode', //发送验证码
		'all'	=> 'pagevotealllist', //全部投票信息
		'rank'	=> 'pageuserranking', //全部排名信息
		'pageresetpassword', //重置密码
		'pagelogin'//登录
	);
	private $_xmlPath = '';
	/**
	 * 初始化夺宝活动相关信息
	 * @method	_initialize()
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	protected function _initialize()
	{
		$this->_xmlPath = TEMPLATE_PATH . '/' . GROUP_NAME . '/' . MODULE_NAME . C('TMPL_FILE_DEPR');
		$this->_tplPrefix = GROUP_NAME . ':' . MODULE_NAME . C('TMPL_FILE_DEPR');
		header('Content-Type:text/html; charset=utf-8');
		$this->assign('acttype', C('ACTIVITY_NAME'));
		$this->getCss();
		if(Cookie::is_set(C('COOKIE_NAME')))
		{
			import('ORG.Crypt.Crypt');
			$cookie = Crypt::decrypt(Cookie::get(C('COOKIE_NAME')), C('DES_KEY'), 1);
			$cookie = explode("\t", $cookie);
//			var_dump($cookie);die;
			$this->dUinfo['uid'] = $cookie[0];
			$this->dUinfo['username'] = $cookie[1];
			$this->dUinfo['pwd'] = $cookie[2];
			$this->dUinfo['email'] = $cookie[3];
			$this->dUinfo['mobile'] = $cookie[4];
//			var_dump($this->dUinfo);
			$this->beforeAct();
			$data = array(
				'json' => json_encode(array(
					'phone' => $this->dUinfo['mobile'],
					'password' => $this->dUinfo['pwd']
				))
			);
			$re = $this->getUserInfo($data);
			$re['mobile'] = $this->dUinfo['mobile'];
			$this->assign('dUinfo', $this->dUinfo);
			$this->assign('userinfo', $this->dUinfo);
		}
	}
	/**
	 * 夺宝活动空页面跳转
	 * @method	_empty()
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-6
	 */
	protected function _empty()
	{
		header('Content-Type:text/html; charset=utf-8');
		header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
		$this->error('404-Document Not Found');
	}
	/**
	 * 判断用户是否已登录
	 * @method	isLogin()
	 * @access public
	 * @param	string	$msg	消息
	 * @param	boolean	$type	true返回json false返回xml 
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-6
	 */
	public function isLogin($msg = '', $type = true)
	{
		if($this->dUinfo['uid'] > 0)
		{
			$this->_return['msg'] = $msg;
			($type == true) ? die(json_encode($this->_return)) : die(xml_encode($this->_return));
		}
	}
	/**
	 * 夺宝活动主页展示
	 * @method	index()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-6
	 */
	public function index()
	{
		$rank = $this->getInfoList('vote', 'rank', false, false, true);
		if(!empty($rank) && count($rank['voteLists']) > 0)
		{
			$this->assign('rankLists', $rank);
		}
		$allVoteList = $this->getInfoList('vote', 'all', false, false, false);//获取全部投票信息【动态】
		$timeFlag = 0;
		Session::set('newVoteDate', $timeFlag);
		if(!empty($allVoteList) && count($allVoteList['voteLists']) > 0)
		{
			$timeFlag = strtotime($allVoteList['voteLists'][0]['adviseDate']);
			Session::set('newVoteDate', $timeFlag);
			$this->assign('allVoteLists', $allVoteList['voteLists']);
		}
		$this->display($this->_tplPrefix . 'index');
	}
	/**
	 * 夺宝弹窗注册页
	 * @method	reg()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-7
	 */
	public function reg()
	{
		$this->isLogin('您已经注册过了！', false);
		$this->display($this->_xmlPath . 'reg.xml', 'utf-8', 'text/xml');
	}
	/**
	 * 夺宝弹窗注册动作
	 * @method	regAct()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-7
	 */
	public function regAct()
	{
		$this->isLogin('您已经注册过了！');
		$this->_return['msg'] = '注册失败！';
		$code = Filter::act(Req::get('verifyCode', 'post'), 'int', 4); //发送短信的验证码
		$this->checkMobile(Filter::act(Req::get('mobile', 'post'), 'string', 11));
		$password = $this->checkPassword();
		$this->checkHash(Filter::act(Req::get('__hash__', 'post')));
		if(Session::is_set('mobileCode') && Session::get('mobileCode') == $code)
		{
			$re = file_get_contents($this->_prefixUrl . "user/pageregister.action?phone={$_SESSION['mobileNum']}&password={$password}");
			$re = json_decode($re, true);
			if($re['result'] == 200)
			{
				$this->_return['status'] = 1;
				$this->_return['msg'] = '注册成功！';
				unset($_SESSION['mobileCode']);
				die(json_encode($this->_return));
			}
			$this->_return['msg'] = $this->_serverData[$re['result']];//
		}
		else
		{
			$this->_return['msg'] = '手机验证码错误！';
		}
		unset($_SESSION['mobileCode']);
		die(json_encode($this->_return));
	}
	/**
	 * 夺宝登录弹窗页
	 * @method	login()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-7
	 */
	public function login()
	{
		$this->isLogin('您已经登录了！', false);
		$this->display($this->_tplPrefix . 'login');
	}
	/**
	 * 夺宝登录动作
	 * @method	loginAct()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-7
	 */
	public function loginAct()
	{
		$this->isLogin('您已经登录了！');
		$this->_return['msg'] = '登录失败！';
		$mobile = Filter::act(Req::get('mobile', 'post'));
		$this->checkMobile($mobile);
		$password = $this->checkPassword();
		$this->checkVerifyCard(Filter::act(Req::get('verifyCard', 'post')));
		$this->checkHash(Filter::act(Req::get('__hash__', 'post')));
		$data = array(
				'json' => json_encode(array(
					'phone' => $mobile,
					'password' => $password
				))
			);
		$re = $this->getUserInfo($data);
		if(!empty($re) && $re['loginflag'] == 1)
		{
			Cookie::delete(C('D_COOKIENAME'));
			import('ORG.Crypt.Crypt');
			$cookie = $re['userid'] . '|' . $mobile . '|' . $password . '|' . $re['pageUserActive'][0]['activeid'];
			Cookie::set(C('D_COOKIENAME'), Crypt::encrypt($cookie, C('DES_KEY'), 1), 30 * 24 * 3600);
			$this->_return['status'] = 1;
			$this->_return['msg'] = '登录成功！';
		}
		die(json_encode($this->_return));
	}
	/**
	 * 夺宝登出
	 * @method	logout()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-7
	 */
	public function logout()
	{
		$this->_return['msg'] = '操作失败！';
		if(Cookie::is_set(C('D_COOKIENAME')))
		{
			Cookie::delete(C('D_COOKIENAME'));
			$this->_return['status'] = 1;
			$this->_return['msg'] = '退出成功！';
		}
		die(json_encode($this->_return));
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
		$sign = Filter::act(Req::get('sign', 'get'), 'int', 1);
		$sign = $sign == 2 ? 2 : 1; //1为注册新用户，2为修改密码
		if($sign ==1)
		{
			$this->isLogin('您已经注册过了！');
		}
		$this->checkMobile($mobile);

		import('ORG.Util.String');
		$_SESSION['mobileCode'] = String::rand_string(4, 1);
		if($sign == 1)$_SESSION['mobileNum'] = $mobile;
		$re = file_get_contents($this->_prefixUrl . "user/registercode.action?phone={$mobile}&code={$_SESSION['mobileCode']}&signature={$sign}");
		$re = json_decode($re, true);
		if(!empty($re) && isset($re['result']))
		{
			if($re['result'] == 200)
			{
				$this->_return['status'] = 1;
				$this->_return['msg'] = $this->_serverData[$re['result']];
				Session::set('svTime', time());
				die(json_encode($this->_return));
			}
			$this->_return['msg'] = $this->_serverData[$re['result']];
		}

		die(json_encode($this->_return));
	}
	/**
	 * 邀请好友页面
	 * @method	invite()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	public function invite()
	{
		$this->checkTime(true);
		$this->checkLogin(false);
		$this->display($this->_xmlPath . 'invite.xml', 'utf-8', 'text/xml');
	}
	/**
	 * 邀请好友动作
	 * @method	inviteAct()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	public function inviteAct()
	{
		$this->checkTime();
		$this->checkLogin(true);
		$this->_return['msg'] = '';
		$mobile = Filter::act(Req::get('mobile'), 'post');
		$mobileArr = explode('#', $mobile);
		$this->checkHash(Filter::act(Req::get('__hash__'), 'post'));
		$this->_return['status'] = 1;
		load('extend');
		$ip = get_client_ip();
		$logStatus = 'Success';
		$msgStatus = '邀请成功！';
		$msg = '';
		foreach($mobileArr as $k => $v)
		{
			$this->checkMobile($v);
			$data = array(
				'json' => json_encode(array(
				'userid' => $this->dUinfo['uid'],
				'password' => $this->dUinfo['pwd'],
				'activeid' => $this->dUinfo['activeId'],
				'phone' => $v//手机号
				))
			);
			$re = $this->postCurl($this->_prefixUrl . 'vote/pageadvisevote.action', $data);
			if($re['result'] != 200)
			{
				$reval = $re['result'];
				$logStatus = 'Failed';
				$this->_return['status'] = -1;
				$msgStatus = '邀请失败！';
			}
			$message = "Username : {$this->dUinfo['mobile']} & Uid : {$this->dUinfo['uid']} @ ip : {$ip}, send sms to {$v} @ Action : invite @ Status:{$logStatus} @ ReturnCode : {$reval}";
			Log::write($message, 'INFO', 3, LOG_PATH.'sms_'.date('y_m_d').".log");
			
			if ($this->_serverData[$re['result']])
			{
				$msg .= $v . $this->_serverData[$re['result']] . '<br />';
			}
			else
			{
				$msg .= "您的手机号为{$v}的好友{$msgStatus}<br />";
			}
		}
		$this->_return['msg'] = $msg != '' ? $msg : '邀请失败！';
		die(json_encode($this->_return));
	}
	/**
	 * POST发送数据
	 * @method	postCurl()
	 * @access private
	 * @param	string	$url	要post数据的地址
	 * @param	array	$data	要post的数据
	 * @return array
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function postCurl($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}
	/**
	 * 检查用户是否登录
	 * @method	checkLogin()
	 * @access private
	 * @param	boolean	$type	是否输出提示信息 true 输出 false 跳转页
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function checkLogin($type = true)
	{
		if($this->dUinfo['uid'] <= 0)
		{
			$this->_return['msg'] = '请先登录！';
			($type == true) ? die(json_encode($this->_return)) : $this->error('请先登录！');
		}
	}
	/**
	 * 检查手机号合法性
	 * @method	checkMobile()
	 * @access private
	 * @param	string	$mobile
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function checkMobile($mobile)
	{
		if(!preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|147[0-9]{8}$/",$mobile))
		{
			$this->_return['status'] = -1;
			$this->_return['msg'] = '请填写正确的手机号！';
			die(json_encode($this->_return));
		}
	}
	/**
	 * 解密手机号
	 * @method	deMaskPhone()
	 * @access public
	 * @param	string	$str	手机号
	 * @param	boolean	$isStar	是否替换中间四位为****
	 * @return string
	 * @author	xiaoguang
	 * @date	2012-2-8
	 */
	public function deMaskPhone($str, $isStar = true)
	{
		$mobile = sprintf("%05x",'0x'.substr($str,0,5)-0x66666).sprintf("%06x",'0x'.substr($str,5)-0x666666);
		if(!$isStar)
		{
			return $mobile;
		}
		return substr_replace($mobile, '****', 3, 4);
	}
	/**
	 * 重置密码页
	 * @method	resetPass()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	public function resetPass()
	{
		$this->display($this->_xmlPath . 'resetPass.xml', 'utf-8', 'text/xml');
		//$this->display(MODULE_NAME . ':resetPass');
	}
	/**
	 * 重置密码
	 * @method	resetPassAct()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	public function resetPassAct()
	{
		$this->_return['msg'] = '修改失败！';
		$mobile = Filter::act(Req::get('mobile', 'post'));
		$code = Filter::act(Req::get('verifyCode', 'post'), 'int', 4); //发送短信的验证码
		$__hash__ = Filter::act(Req::get('__hash__', 'post'));
		$this->checkMobile($mobile);
		$this->checkHash($__hash__);
		$password = $this->checkPassword();
		if(Session::is_set('mobileCode') && Session::get('mobileCode') == $code)
		{
			$re = file_get_contents($this->_prefixUrl . "user/pageresetpassword.action?phone={$mobile}&password={$password}&signature=2");
			$re = json_decode($re, true);
			if($re['result'] == 200)
			{
				$this->_return['status'] = 1;
				$this->_return['msg'] = '修改成功！';
				unset($_SESSION['mobileCode']);
				if(Cookie::is_set(C('D_COOKIENAME')))Cookie::delete(C('D_COOKIENAME'));
				die(json_encode($this->_return));
			}
			$this->_return['msg'] = $this->_serverData[$re['result']];//
		}
		else
		{
			$this->_return['msg'] = '手机验证码错误！';
		}
		unset($_SESSION['mobileCode']);
		die(json_encode($this->_return));
	}
	/**
	 * 检查表单提交的hash码是否正确
	 * @method	checkHash()
	 * @access private
	 * @param	string	$__hash__	hash码
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function checkHash($__hash__)
	{
		if($__hash__ != Session::get('__hash__'))
		{
			die(json_encode($this->_return));
		}
	}
	/**
	 * 检查表单提交的验证码是否正确
	 * @method	checkVerifyCard()
	 * @access private
	 * @param	string	$verifyCard	验证码
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function checkVerifyCard($verifyCard)
	{
		if (md5($verifyCard) != Session::get('verify'))
		{
			$this->_return['status'] = -1;
			$this->_return['msg'] = '验证码错误！';
			die(json_encode($this->_return));
		}
	}
	/**
	 * 获取用户活动信息
	 * @method	getUserInfo()
	 * @access private
	 * @param	array	$data	包含用户信息的数组
	 * @return array
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function getUserInfo($data)
	{
		return $this->postCurl($this->_prefixUrl . 'user/pagelogin.action', $data);
	}
	/**
	 * 获取用户相关列表信息
	 * @method	getAllUserInfo()
	 * @access public
	 * @param	string	$action	操作名
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	public function getAllUserInfo($action)
	{
		$this->checkLogin();
		$p = isset($_GET['p']) && ((int)$_GET['p'] > 0) ? $_GET['p'] : 1;
		$p = $p - 1;
		$action = isset($_GET['action']) ? $_GET['action'] : '';
		if(array_key_exists($action, $this->actArr))
		{
			$this->_return['msg'] = '没有找到相关信息！';
			$infoList = $this->getInfoList('vote', $action, true, true, true, $p);
			if(!empty($infoList))
			{
				if(($action == 'rank' && count($infoList['voteLists']) <= 0) || ($action != 'rank' && count($infoList['advises']) <= 0))
				{
					die(json_encode($this->_return));
				}
				$this->assign('infoList', $infoList);
				$content = $this->fetch(MODULE_NAME . ':' . $action . 'List');
				$this->_return['status'] = 1;
				$this->_return['msg'] = $content;
			}
			die(json_encode($this->_return));
		}
		$pullList = $this->getInfoList('vote', 'pull', true, false, true, 0);//当前用户邀请信息
		$pushList = $this->getInfoList('vote', 'push', true, false, true, 0);//当前用户投票信息
		$voteList = $this->getInfoList('vote', 'vote', true, false, true, 0);//当前用户被投票信息
		$this->assign('pullList', $pullList);
		$this->assign('pushList', $pushList);
		$this->assign('voteList', $voteList);
		$this->display($this->_xmlPath . 'infoList.xml', 'utf-8', 'text/xml');
	}
	/**
	 * 获取列表信息
	 * @method	getInfoList()
	 * @access private
	 * @param	string	$page			请求的应用页
	 * @param	string	$action			操作名称
	 * @param	boolean	$checkLogin		是否检查登录
	 * @param	boolean	$checkLoginType	false跳转错误页，true为json输出
	 * @param	int		$isPage			是否分页
	 * @param	int		$pageNum		页码
	 * @return array
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function getInfoList($page, $action, $checkLogin = true, $checkLoginType = false, $isPage = false, $pageNum = 0)
	{
		$pageStr = '';
		$pageArr = array(
			'user',
			'vote'
		);
		if($checkLogin)$this->checkLogin(false);
		if($pageNum != -1)$pageStr = "&pageNum={$pageNum}";
		$re = file_get_contents($this->_prefixUrl . "{$page}/{$this->actArr[$action]}.action?userid={$this->dUinfo['uid']}&password={$this->dUinfo['pwd']}&activeid={$this->dUinfo['activeId']}" . $pageStr);
		$re = json_decode($re, true);
		if($isPage)
		{
			$pageNum = $pageNum + 1;
			$re['pageShow'] = $this->getPageShow($re, $pageNum, $action);
		}
		return $re;
	}
	/**
	 * 获取分页显示
	 * @method	getPageShow()
	 * @access private
	 * @param	array	$info	数据相关信息
	 * @param	int		$p		页码
	 * @param	string	$action	操作名
	 * @return mixed
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function getPageShow($info, $p, $action)
	{
		$count = $info['pageCount'] * $info['pagePerCount'];
		$Page = new Page($count, $info['pagePerCount']);
		$Page->setAjax($p, $action);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
		return $Page->show();
	}
	/**
	 * 获取前端页面ajax轮询动态
	 * @method	getDynamic()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	public function getDynamic()
	{
		$this->checkTime();
		$this->_return['msg'] = '没有动态了！';
		$re = file_get_contents($this->_prefixUrl . 'vote/pagevoteonlyone.action');
		$re = json_decode($re, true);

		if(!empty($re) && count($re['voteLists']) > 0)
		{
			if(Session::is_set('newVoteDate'))
			{
				$newVoteDate = Session::get('newVoteDate');
				$nowVoteDate = strtotime($re['voteLists'][0]['adviseDate']);

				if($nowVoteDate > $newVoteDate)
				{
					Session::set('newVoteDate', $nowVoteDate);
					$phone = deMaskPhone($re['voteLists'][0]['phone']);
					$tphone = deMaskPhone($re['voteLists'][0]['tuserPhone']);
					$this->_return['status'] = 1;
					$this->_return['msg'] = "<li class=\"slide{$nowVoteDate}\"><em>{$re['voteLists'][0]['adviseDate']}</em><span style=\"color:#703206;\">{$phone}</span>给<span style=\"color:#c95e1c;\">{$tphone}</span>投票了</li>";
					$this->_return['infoId'] = $nowVoteDate;
					
				}
			}
		}
		die(json_encode($this->_return));
	}
	/**
	 * 检查密码是否合法
	 * @method	checkPassword()
	 * @access private
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function checkPassword()
	{
		$password = Filter::act(Req::get('password', 'post'));
		if(strlen($password) >= 6)
		{
			return md5($password);
		}
		$this->_return['msg'] = '请填写密码，并保证密码长度大于6位';
		die(json_encode($this->_return));
	}
	/**
	 * 根据浏览器不同加载不同css（只针对ie6，苦逼啊！！）
	 * @method	getCss()
	 * @access private
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function getCss()
	{
		$css = 'store';
		if(strpos($_SERVER["HTTP_USER_AGENT"],"MSIE 6.0"))
		$css = 'aero';
		$this->assign('css', $css);
	}
	/**
	 * 夺宝活动校验是否已超出活动时间
	 * @method	checkTime()
	 * @access private
	 * @param	boolean	isXml	是否是xml显示
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-6
	 */
	private function checkTime($isXml = false)
	{
		$before = strtotime('2012-02-27 17:00:00');
		$after = strtotime('2012-03-09 17:00:00');
		$time = time();
		if($time < $before)
		{
			$this->_return['msg'] = '活动还没开始呢！着急了？泡杯茶等一会吧！';
			die($isXml ? xml_encode(array($this->_return['msg']), $encoding='utf-8', $root="root") : json_encode($this->_return));
		}
		if($time > $after)
		{
			$this->_return['msg'] = '活动已经结束啦！没赶上？下次早来占座吧！';
			die($isXml ? xml_encode(array($this->_return['msg']), $encoding='utf-8', $root="root") : json_encode($this->_return));
		}
	}
	/**
	 * 夺宝活动检前置操作（主要功能提示用户绑定手机号）
	 * @method	beforeAct()
	 * @access private
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-28
	 */
	private function beforeAct()
	{
		if(isset($this->dUinfo['mobile']) && strlen($this->dUinfo['mobile']) != 11)
		{
			$this->redirect('AppStore://Common-Index/bind', array('acttype' =>  C('ACTIVITY_NAME')));
		}
	}
}
?>