<?php
class ActivityBaseAction extends BaseAction
{
	protected $dUinfo = array(
		'uid' => 0,
		'pwd' => '',
		'activeId' => 0
	);
	/**
	 * 初始化夺宝活动相关信息
	 * @method	_initialize
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	protected function _initialize()
	{
		parent::_initialize();
//		Session::destory();
		$this->_xmlPath = TEMPLATE_PATH . '/' . GROUP_NAME . '/' . MODULE_NAME . C('TMPL_FILE_DEPR');
		$this->_tplPrefix = GROUP_NAME . ':' . MODULE_NAME . C('TMPL_FILE_DEPR');

		$activeInfo = $this->getActiveInfo();
		$nowTime = time();
		$startTime = strtotime('2012-02-27 17:00:00');//strtotime($activeInfo['active']['begin']);
		$endTime = strtotime('2012-05-10 17:00:00');//$activeInfo['active']['end'];

		$this->assign('activeInfo', $activeInfo['active']);
		$this->assign('startTime', $startTime);
		$this->assign('endTime', $endTime);
		$this->assign('acttype', C('ACTIVITY_NAME'));

		if(Cookie::is_set(C('COOKIE_NAME')))
		{
			import('ORG.Crypt.Crypt');
			$cookie = Crypt::decrypt(Cookie::get(C('COOKIE_NAME')), C('DES_KEY'), 1);
//			var_dump($cookie);
			$cookie = explode("\t", $cookie);
//			var_dump($cookie);die;
			$this->dUinfo['uid'] = $this->_uid = $cookie[0];
			$this->dUinfo['username'] = $this->_uname = $cookie[1];
			$this->dUinfo['pwd'] = $this->_pwd = $cookie[2];
			$this->dUinfo['email'] = $cookie[3];
			$this->dUinfo['mobile'] = $cookie[4];
//			var_dump($this->dUinfo);

//			$data = array(
//				'json' => json_encode(array(
//				'phone' => $this->dUinfo['mobile'],
//				'password' => $this->dUinfo['pwd']
//				))
//			);
//			$re = $this->getUserInfo($data);
			$re['mobile'] = $this->dUinfo['mobile'];
			$this->assign('dUinfo', $re);
			$this->assign('userinfo', $this->dUinfo);
		}
		$this->beforeAct();
	}
	/**
	 * 夺宝活动空页面跳转
	 * @method	_empty
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
	 * 获取用户信息
	 * @method	getUserInfo
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-28
	 */
	protected function getUserInfo()
	{
		$data = $this->arrToJson(array(
			'userid'	=> $this->_uid,//$this->dUinfo['uid'],
			'password'	=> $this->_pwd//$this->dUinfo['pwd']
		));

		$result = $this->postCurl('DUser', 'userinfo', $data);
		return $result;
	}

	/**
	 * 检查手机号合法性
	 * @method	checkMobile
	 * @access protected
	 * @param	string	$mobile
	 * @param	boolean	$isDump
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function checkMobile($mobile, $isDump = true)
	{
		if(strlen($mobile) != 11 || !preg_match("/^13[0-9]{1}[0-9]{8}$|15[0-9]{1}[0-9]{8}$|18[0-9]{1}[0-9]{8}$|147[0-9]{8}$/",$mobile))
		{
			if($isDump)
			{
				$this->_return['status'] = -1;
				$this->_return['msg'] = '请填写正确的手机号！';
				die(json_encode($this->_return));
			}
			return false;
		}
		return true;
	}
	/**
	 * 夺宝活动检前置操作（主要功能提示用户绑定手机号）
	 * @method	beforeAct
	 * @access private
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-28
	 */
	private function beforeAct()
	{
//		die(var_dump($_SESSION));
//		Session::destroy();
		if(Session::is_set('userinfo') && $_SESSION['userinfo']['username'] == '' && $this->dUinfo['username'] == '')
		{
			$this->redirect('Ucenter-Index/bind', array('acttype' => C('BBS_NAME'), 'callbackUrl' => base64_encode(U('Activity-Test/index'))));
			die;
		}
		if(isset($this->dUinfo['mobile']) && strlen($this->dUinfo['mobile']) != 11)
		{
			$this->redirect('Ucenter-Index/bind', array('acttype' =>  C('ACTIVITY_NAME')));
		}
	}

	/**
	 * 检查表单提交的hash码是否正确
	 * @method	checkHash
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
	 * @method	checkVerifyCard
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
	 * 获取所有投票信息
	 * @method	getVoteAllList
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function getVoteAllList()
	{
		$data = $this->arrToJson(array(
			'pageNum'	=> 1,
			'pageSize'	=> 10
		));
		$result = $this->postCurl('Vote', 'voteAllList', $data);
//		var_dump($result);
		if(isset($result['voteLists']) && !empty($result['voteLists']))
		{
			//生成最新一条投票记录的时间session，方便页面轮询对比。
			Session::set('newVoteDate', strtotime($result['voteLists'][0]['createon']));
		}
		$this->assign('voteAllList', $result['voteLists']);
	}
	/**
	 * 获取活动信息
	 * @method	getActiveInfo
	 * @access protected
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-28
	 */

	protected function getActiveInfo()
	{
		$result = $this->postCurl('Active', 'active');
		return $result;
	}
}

?>
