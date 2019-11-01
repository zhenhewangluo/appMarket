<?php
class AjaxAction extends ActivityBaseAction
{
	protected function _initialize()
	{
		parent::_initialize();
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
		$re = $this->postCurl('DUser', 'addSmMt', $data);
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
	 * 获取好友列表
	 * @method	getBuddyList()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-7
	 */
	public function getBuddyList()
	{
		$pageNum = (int)Filter::act(Req::get('pageNum', 'get'));
		$pageNum = empty($pageNum) ? 1 : $pageNum;
		$pageSize = (int)Filter::act(Req::get('pageSize', 'get'));
		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$result = $this->returnBuddyList($pageNum, $pageSize);
//		die(var_dump($result));
		$this->_return['status'] = $result['status'];
		$this->_return['html'] = $result['listHtml'];
		$this->_return['pageHtml'] = $result['pageHtml'];
		
		die(json_encode($this->_return));
	}

	/**
	 * 搜索好友
	 * @method	searchBuddyList()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-4-25
	 */
	public function searchBuddyList()
	{
		$keyword = Filter::act(Req::get('keyword', 'post'));
		$pageNum = (int)Filter::act(Req::get('pageNum', 'get'));
		$pageNum = empty($pageNum) ? 1 : $pageNum;
		$pageSize = (int)Filter::act(Req::get('pageSize', 'get'));
		$pageSize = empty($pageSize) ? 10 : $pageSize;
		$pageSize = 10000;
		$result = $this->returnBuddyList($pageNum, $pageSize, $keyword, true);
		
		$this->_return['status'] = $result['status'];
		$this->_return['html'] = $result['listHtml'];
		die(json_encode($this->_return));
	}
	/**
	 * 获取好友列表，供getBuddyList和searchBuddyList两个方法调用
	 * @method	returnBuddyList()
	 * @access private
	 * @param	int		$pageNum	当前页数
	 * @param	int		$pageSize	每页显示多少个
	 * @param	string	$keyWord	搜索关键词
	 * @param	boolean	$isSearch	是否是搜索动作
	 * @return array
	 * @author	xuhao
	 * @date	2012-4-25
	 */
	private function returnBuddyList($pageNum = 1, $pageSize = 10, $keyWord ='', $isSearch = false)
	{
		$return = array();
		$data = $this->arrToJson(array(
			'userid'	=> $this->_uid,//$this->dUinfo['uid'],
			'password'	=> $this->_pwd,//$this->dUinfo['pwd']
			'keyword'	=> $keyWord,
			'pageNum'	=> $pageNum,
			'pageSize'	=> $pageSize == -1 ? 10000 : $pageSize
		));
		$result = $isSearch == false ? $this->postCurl('Buddy', 'webBuddyList', $data) : $this->postCurl('Buddy', 'searchBuddy', $data);

		//上一页
		$page = $this->getPageShow($pageNum, $pageSize, $result['totalCount']);
		
		if($isSearch == false)
		{
			$this->assign('buddyList', $result['relations']);
			$pageHtml = $this->fetch($this->_tplPrefix . 'buddyListPage');
		}
		else
		{
			 $this->assign('buddyList', $result['buddyList']);
		}
		$html = $this->fetch($this->_tplPrefix . 'buddyList');
		$return['status'] = 1;
		$return['listHtml'] = $html;
		$return['pageHtml'] = $page;
		return $return;
	}
	/**
	 * 编辑好友
	 * @author gaow@skyzine.cn
	 * @time 2012-4-13 上午09:19:10
	 * @return void
	 * @param void
	 */
	public function editBuddy()
	{
		$name = Filter::act(Req::get('name', 'post'));
		$this->_return['msg'] = '您的昵称中有敏感词，请重新输入！';
		if(!checkKeyWord($name))
		{
			$phone = Filter::act(Req::get('phone', 'post'), 'string', 11);
			$newphone = Filter::act(Req::get('newphone', 'post'), 'string', 11);
			$this->checkMobile($newphone, true);
			$buddyid = (int)Filter::act(Req::get('buddyid', 'post'));
			$data = $this->arrToJson(array(
				'userid'		=> $this->_uid,//$this->dUinfo['uid'],
				'password'		=> $this->_pwd,//$this->dUinfo['pwd']
				'buddyName'		=> $name,
				'buddyPhone'	=> enMaskPhone($phone, false),
				'buddyNewPhone'	=> enMaskPhone($newphone, false),
				'groupId' => 0,
			));
			$result = $this->postCurl('Buddy', 'editByPhone', $data);
			//var_dump($data);
			$this->_return['status'] = $result['result'];
			$this->_return['msg'] = $result['msg'];
		}
		
		die(json_encode($this->_return));
	}
	
	/**
	 * 删除好友
	 * @author gaow@skyzine.cn
	 * @time 2012-4-13 下午05:18:53
	 * @return void
	 * @param void
	 */
	public function delBuddy()
	{
		$id = Filter::act(Req::get('id', 'get'));
		$data = $this->arrToJson(array(
			'userid'	=> $this->_uid,//$this->dUinfo['uid'],
			'password'	=> $this->_pwd,//$this->dUinfo['pwd']
			'groupId'	=> 0,
			'buddyids'	=> array(
				array(
					'id'	=> $id
				)
			)			
		));
		$result = $this->postCurl('Buddy', 'del', $data);
		$this->_return['status'] = $result['result'];
		$this->_return['msg'] = $result['msg'];
		die(json_encode($this->_return));
	}
	/**
	 * 批量删除好友
	 * @method	delBuddys()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-23
	 */
	public function delBuddys()
	{
		$id = Filter::act(Req::get('id', 'post'));
		
		$dataId = array();
		foreach($id as $k => $v)
		{
			$dataId[] = array(
				'p'	=> enMaskPhone($v, false),
			);
		}
//		die(var_dump($id));
		$data = $this->arrToJson(array(
			'userid'	=> $this->_uid,//$this->dUinfo['uid'],
			'password'	=> $this->_pwd,//$this->dUinfo['pwd']
			'phone'		=> $dataId,
			'groupId'	=> 0
		));
//		die(var_dump($dataId));
		$result = $this->postCurl('Buddy', 'delByPhone', $data);
		//var_dump($data);
		$this->_return['status'] = $result['result'];
		$this->_return['msg'] = $result['msg'];
		die(json_encode($this->_return));
	}
	/**
	 * 邀请好友
	 * <table border="1">
	 * <tr><th colspan="3">需要POST传递的参数:</th></tr>
	 * <tr>	<th>参数类型</th>	<th>参数名</th>		<th>参数备注</th></tr>
	 * <tr>	<td>string</td>		<td>phone</td>		<td>好友手机号</td></tr>
	 * </table>
	 * @method	adviseBuddy()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-23
	 */
	public function adviseBuddy($phone)
	{
		$phone = Filter::act(Req::get('phone', 'post'));
		$dataId = array();
		$returnValidArr = array();
		foreach($phone as $k => $v)
		{
			$this->checkMobile($v, false);
			$dataId[] = array(
				'phone'	=> enMaskPhone(Filter::act($v), false)
			);
			$returnValidArr[$v] = '邀请成功！';
		}
		$returnValidArr = $this->inviteBuddy($dataId, $returnValidArr);
		if(isset($returnValidArr['status']) && $returnValidArr['status'] == 0)
		{
			$this->_return['msg'] = $returnValidArr['msg'];
			die(json_encode($this->_return));
		}
		$msg = '';
		foreach($returnValidArr as $k => $v)
		{
			$msg .= $k.$v.'<br />';
		}
		$this->_return['status'] = $result['result'];
		$this->_return['msg'] = $msg;
		die(json_encode($this->_return));
	}
	/**
	 * 添加好友
	 * <table>
	 * <tr><th colspan="3">需要POST传递的参数:</th></tr>
	 * <tr>	<th>参数类型</th>	<th>参数名</th>		<th>参数备注</th></tr>
	 * <tr>	<td>string</td>		<td>phone</td>		<td>好友手机号</td></tr>
	 * <tr>	<td>string</td>		<td>buddyName</td>	<td>好友昵称</td></tr>
	 * </table>
	 * @method	addBuddy()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-23
	 */
	public function addBuddy()
	{
		$buddyPhone = Filter::act(Req::get('phone', 'post'));
		$addInvite = Filter::act(Req::get('add_invite', 'post'), 'int');
		
		$data = array(
			'userid'	=> $this->_uid,
			'password'	=> $this->_pwd,
			'users'		=> array()
		);

		$buddyPhone = explode("#", $buddyPhone);
		if(empty($buddyPhone))
		{
			$this->_return['status'] = 0;
			$this->_return['msg'] = '请填写正确的手机号！';
			die(json_encode($this->_return));
		}
		$tmpValidArr = array(); //准备发送给添加接口的合法的手机号
		$tmpValidInviteArr = array();//准备发送给邀请接口的合法的手机号
		
		
		$returnInvalidArr = array(); //给最后的返回值的不合法的手机号
		$returnValidArr = array();//给最后的返回值的合法的手机号
		foreach($buddyPhone as $k => $v)
		{
			$returnValidArr[$v] = $addInvite > 0 ? '添加并邀请成功！' : '添加成功！';
			$tmpValidInviteArr[$v] = '';
		}
		
		foreach($buddyPhone as $k => $v)
		{
			if($this->checkMobile($v, false))
			{
				$tmpValidArr[] = array(
					'toname'	=> '',
					'phone'		=> enMaskPhone($v, false)
				);
			}
			else
			{
				$returnInvalidArr[$v] = '手机号不合法！';
			}
		}
		$msg = '';
		if(!empty($returnInvalidArr))
		{
			foreach($returnInvalidArr as $k => $v)
			{
				$msg .= $k.$v.'<br />';
			}
		}
		if(empty($tmpValidArr))
		{
			$this->_return['status'] = $status;
			$this->_return['msg'] = $msg;
			die(json_encode($this->_return));
		}
		
		$data['users'] = $tmpValidArr;
		$addResult = $this->postCurl('Buddy', 'importContactList', $this->arrToJson($data));
		if(isset($addResult['result']) && $addResult['result'] == 0)
		{
			$this->_return['msg'] = $addResult['msg'];
			die(json_encode($this->_return));
		}
		//拼接处理接口的返回值供页面调用
		if(isset($addResult['myselfuser']))
		{
			foreach($returnValidArr as $k => $v)
			{
				if($k == $addResult['myselfuser']['user'])
				{
					$returnValidArr[$k] = '不能增加自己为好友！';
					unset($tmpValidInviteArr[$k]);
				}
			}
		}
		if (isset($addResult['shielduser']))
		{
			foreach($returnValidArr as $k => $v)
			{
				foreach($addResult['shielduser']['user'] as $ke => $va)
				{
					if($k == $va['phone'])
					{
						$returnValidArr[$k] = '用户已经被禁用！';
						unset($tmpValidInviteArr[$k]);
					}
				}
			}
		}
		
		if (isset($addResult['adduser']))
		{
			foreach($returnValidArr as $k => $v)
			{
				foreach($addResult['adduser']['user'] as $ke => $va)
				{
					if($k == $va['phone'])
					{
						$returnValidArr[$k] = '好友增加失败！';
						unset($tmpValidInviteArr[$k]);
					}
				}
			}
		}
		if (isset($addResult['repeatuser']))
		{
			foreach($returnValidArr as $k => $v)
			{
				foreach($addResult['repeatuser']['user'] as $ke => $va)
				{
					if($k == $va['phone'])
					{
						$returnValidArr[$k] = '好友已存在！';
						unset($tmpValidInviteArr[$k]);
					}
				}
			}
		}
		foreach($tmpValidInviteArr as $k => $v)
		{
			
		}
		if($addInvite > 0 && !empty($tmpValidInviteArr))
		{
			$inviteData = array();
			foreach ($tmpValidInviteArr as $k => $v)
			{
				$inviteData[] = array(
					'phone'	=> enMaskPhone($k, false)
				);
			}
			
			$returnValidArr = $this->inviteBuddy($inviteData, $returnValidArr);
		}
		
		foreach($returnValidArr as $k => $v)
		{
			$msg .= $k.$v.'<br />';
		}
//		die(var_dump($addInvite));
		$this->_return['status'] = $status;
		$this->_return['msg'] = $msg;
		die(json_encode($this->_return));
	}
	/**
	 * 邀请好友
	 * @method	inviteBuddy()
	 * @access private
	 * @param	array	$phone			要邀请的手机号，格式为一个二维数组
	 * @param	array	$returnValidArr 返回的数组格式为一维数组，key为$phone里面的手机号，val为空或初始化的值
	 * @return array
	 * @author	xuhao
	 * @date	2012-3-23
	 */
	private function inviteBuddy($phone, $returnValidArr = array())
	{
		$data = $this->arrToJson(array(
			'userid'	=> $this->_uid,//$this->dUinfo['uid'],
			'password'	=> $this->_pwd,//$this->dUinfo['pwd']
			'userlist'	=> $phone,
			'groupid'	=> 0
		));
//		die(var_dump($data));
		$inviteResult = $this->postCurl('Advise', 'advise', $data);
		if($inviteResult['result'] == 0)
		{
			return $inviteResult;
		}
		if(isset($inviteResult['myselfuser']))
		{
			foreach($returnValidArr as $k => $v)
			{
				if($k == $inviteResult['myselfuser']['users'])
				{
					$returnValidArr[$k] = '用户不能给自己拉票！';
				}
			}
		}
		if (isset($inviteResult['shielduser']))
		{
			foreach($returnValidArr as $k => $v)
			{
				foreach($inviteResult['shielduser']['users'] as $ke => $va)
				{
					if($k == $va['phone'])
					{
						$returnValidArr[$k] = '用户已经被禁用！';
					}
				}
			}
		}
		if (isset($inviteResult['advisedusers']))
		{
			foreach($returnValidArr as $k => $v)
			{
				foreach($inviteResult['advisedusers']['users'] as $ke => $va)
				{

					if($k == $va['phone'])
					{
						$returnValidArr[$k] = '用户今天已经被拉过票！';
					}
				}
			}
		}

		if (isset($inviteResult['adviseerrorusers']))
		{
			foreach($returnValidArr as $k => $v)
			{
				foreach($inviteResult['adviseerrorusers']['users'] as $ke => $va)
				{
					if($k == $va['phone'])
					{
						$returnValidArr[$k] = '邀请失败！';
					}
				}
			}
		}
		return $returnValidArr;
	}
	/**
	 * 获取最新的投票信息
	 * @method	getVoteOne()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-4-19
	 */
	public function getVoteOne()
	{
		$this->_return['msg'] = 'No Msg';
		$result = $this->postCurl('Vote', 'voteOneList');
		if(isset($result['voteLists']) && !empty($result['voteLists']))
		{
			//取得最新一条投票记录的时间session，与当前取得的数据的时间做比对，如果当前记录时间比session新，那么返回给页面轮询。
			//并把session设置成最新的时间
			$time = strtotime($result['voteLists'][0]['createon']);
			if(Session::is_set('newVoteDate') && $time > Session::get('newVoteDate'))
			{
				Session::set('newVoteDate', $time);
				$this->_return['status'] = 1;
				$this->_return['result'] = $result['voteLists'];
			}
		}
		die(json_encode($this->_return));
	}
	/**
	 * 获取用户概览信息，即页面点击我要夺宝后滑动到的页面。
	 * @method	getUserOverview()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-4-19
	 */
	public function getUserOverview()
	{
		$this->_return['status'] = 0;
		$this->_return['msg'] = '请先登录！';
		if($this->_uid > 0)
		{
			$this->getVoteAllList();
			$activeInfo = $this->getActiveInfo();
			$voteMeInfo = $this->getVoteMeList();
			$inviteMeInfo = $this->getInviteList();
			$dbUserinfo = $this->getUserInfo();
			
			$result = $this->returnBuddyList();
			$this->assign('page', $result['pageHtml']);
			$this->assign('activeInfo', $activeInfo['active']);
			$this->assign('voteMeInfo', $voteMeInfo['votelist']);

			$this->assign('inviteMeInfo', $inviteMeInfo['advisedusers']);
//			die(var_dump($inviteMeInfo['advisedusers']));
			$this->assign('dbUserinfo', $dbUserinfo['user']);
			$html = $this->fetch($this->_tplPrefix . 'userOverview');
			$this->_return['status'] = 1;
			$this->_return['html'] = $html;
		}
//		die(var_dump($this->_return));
		die(json_encode($this->_return));
	}
	/**
	 * 给好友投票
	 * @method	voteBuddy()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-4-19
	 */
	public function voteBuddy()
	{
		$this->_return['status'] = 0;
		$this->_return['msg'] = '请先登录！';
		if($this->_uid > 0)
		{
			$id = Filter::act(Req::get('id', 'post'));
			$dataId = array();
			foreach($id as $k => $v)
			{
				$dataId[] = array(
					'phone'	=> enMaskPhone(Filter::act($v), false)
				);
			}

			$data = $this->arrToJson(array(
				'userid'	=> $this->_uid,//$this->dUinfo['uid'],
				'password'	=> $this->_pwd,//$this->dUinfo['pwd']
				'userlist'	=> $dataId,
				'isclient'	=> 0
			));
			$result = $this->postCurl('Vote', 'vote', $data);
//			die(var_dump($result));
			$this->_return['msg'] = $result['msg'];
			if ($result['result'] == 2)
			{
				//不能给自己投票
				if (isset($result['myselfuser']))
				{
					$this->_return['msg'] = ' 不能给自己投票！';
				}
				//屏蔽用户
				if (isset($result['shielduser']))
				{
					$this->_return['msg'] = ' 用户已经被禁用！';
				}
				//今天已经投票的用户
				if (isset($result['voteduser']))
				{
					$this->_return['msg'] = ' 用户今天已经被投过票！';
				}
				//投票失败
				if (isset($result['voteerroruser']))
				{
					$this->_return['msg'] = ' 投票失败！';
				}
			}
			$this->_return['status'] = $result['result'];
		
		}
		die(json_encode($this->_return));
	}
	public function getAboutActivity()
	{
		$this->assign('viewcontent', $this->_tplPrefix . 'about');
		die(json_encode($this->fetch($this->_layout)));
	}
	public function getAward()
	{
		$this->assign('viewcontent', $this->_tplPrefix . 'award');
		die(json_encode($this->fetch($this->_layout)));
	}
	/**
	 * 获取被投票记录
	 * @method	getVoteMeList()
	 * @access protected
	 * @param	$pageNum	页号
	 * @param	$pageSize	每页大小
	 * @return 被投票记录
	 * @author	zxg
	 * @date	20120426
	 */
	protected function getVoteMeList($pageNum = 1, $pageSize = 9999)
	{
		$data = $this->arrToJson(array(
			'userid' => $this->_uid,
			'password' => $this->_pwd,
			'pageNum' => $pageNum,
			'pageSize' => $pageSize
		));
		$result = $this->postCurl('Vote', 'voteMeList', $data);
//		die(var_dump($result));
		if (isset($result['votelist']) && !empty($result['votelist']))
		{
			
			return $result;
		}
		
		return false;
	}
	/**
	 * 获取邀请记录
	 * @method	getInviteList()
	 * @access protected
	 * @param	$pageNum	页号
	 * @param	$pageSize	每页大小
	 * @return 邀请记录
	 * @author	zxg
	 * @date	20120426
	 */
	protected function getInviteList($pageNum = 1, $pageSize = 9999)
	{
		$data = $this->arrToJson(array(
			'userid' => $this->dUinfo['uid'],
			'password' => $this->dUinfo['pwd'],
			'pageNum' => $pageNum,
			'pageSize' => $pageSize
		));
		$result = $this->postCurl('Advise', 'advisedUserList', $data);
		
		if (isset($result['advisedusers']) && !empty($result['advisedusers']))//需解码
		{
//			die(var_dump($result));
			return $result;
		}
		return false;
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
	private function getPageShow($pageNum, $pageSize, $count)
	{
		$Page = new Page($count, $pageSize);
		$Page->setAjax($pageNum);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
//		die(var_dump($Page->show()));
		return $Page->show();
	}
}

?>
