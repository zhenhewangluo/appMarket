<?php
/**
 * 用户中心类
 * @file	IndexAction.class.php
 * @name	IndexAction
 * @author	xuhao
 * @desc	用户中心类（负责用户修改资料，头像等操作）
 * @date	2012-3-20
 */
class PanelAction extends UcenterBaseAction
{
	private $_userbar = '';
	private $_listRow = 20; //默认每页显示历史列表信息个数
	protected function _initialize()
	{
		parent::_initialize();
		$this->mustLogin();
		$this->_userbar = C('TMPL_FILE_DEPR') == '/' ? TEMPLATE_PATH . C('TMPL_FILE_DEPR') . 'Layout/userbar.html' : 'Layout:userbar';
		Vendor('Ucenter.UcApi');  //载入UcApi扩展
		$this->assign('userbar', $this->_userbar);
	}

	public function updatePassAct()
	{
		if($this->_uid <= 0)
		{
			$this->error('请先登录');
		}
		$password = md5(Filter::act(Req::get('pwd', 'post')));
		$newpassword = md5(Filter::act(Req::get('newpwd', 'post')));
		$newrepassword = md5(Filter::act(Req::get('newrepwd', 'post')));
		if($newpassword != $newrepassword)
		{
			$this->error('两次密码不一致！');
		}
		$data = array(
			'uid'			=> $this->_uid,
			'password'		=> $password,
			'newpassword'	=> $newpassword
		);
		$result = $this->postCurl('User', 'updatePassAct', $data);
		if(isset($result['status']))
		{
			if($result['status'])
			{
				Cookie::set(C("COOKIE_NAME"), '', -86400);//页端cookie设置
				$this->assign('jumpUrl', U('Ucenter-Index/login', array('acttype' => 'market')));
				$this->success($result['msg']);
				die;
			}
			$this->error($result['msg']);			
		}
		$this->error('密码修改失败！');
	}
	/**
	 * 初始化用户注册附加信息页
	 * @method	userinfo()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xiaoguang
	 * @date	2011-12-06
	 */
	public function userinfo()
	{
		$userObj = A('user');
		$flash = $userObj->showuploadAction();
		$this->assign('flash', $flash);

		$userForm = D('RegisterUser');
		$userdata = $userForm->getById($this->_uid);

		import("ORG.Util.Page");
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$historyList = D('Comment');
		$table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'comment`';
		$join_table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'appinfo`';
		
		$appList = $historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					->field("{$table_name}.app_id,{$table_name}.update_time,{$table_name}.content,{$table_name}.score_www,{$join_table_name}.app_name ")
					->order('update_time DESC')
					->page($p . ','  . $this->_listRow)->select();
				
		$count =	$historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					->count();		
		$Page = new Page($count, $this->_listRow);
		$Page->setAjax();
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
		$show = $Page->show();
		$this->assign('list', $appList);
		$this->assign('page', $show);		
		
		$this->assign('userdata', $userdata);
		$this->assign('viewcontent', $this->_tplPrefix . 'userinfo');
		$this->display($this->_layout);
	}
	/**
	 * 修改密码
	 * @method	updatePassword()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xiaoguang
	 * @date	2011-12-06
	 */
	public function updatePass()
	{
		$this->assign('viewcontent', $this->_tplPrefix . 'updatePass');
		$this->display($this->_layout);
	}	
	/**
	 * 我的更新
	 * @method	updateHistory()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xiaoguang
	 * @date	2011-12-06
	 */
	public function updateHistory()
	{
		//$id = $this->_uid;
		import("ORG.Util.Page");
		$historyList = D('DownloadHistory');
		$table_name = '`' . C('DB_PREFIX') . 'download_history`';
		$join_table_name = '`' . C('DB_PREFIX') . 'appinfo`';
		$db_name = '`_v2_android`';

		$p = isset($_GET['p']) ? $_GET['p'] : 1;

		$appList = $historyList->page($p . ','  . $this->_listRow)
				->field("{$db_name}.{$table_name}.`app_id`,{$db_name}.{$table_name}.`create_time`,{$db_name}.{$join_table_name}.`app_name`,{$db_name}.{$join_table_name}.`app_update_date`,{$db_name}.{$join_table_name}.`app_version`")
				->order('create_time DESC')
				->join("{$db_name}.{$join_table_name} ON {$db_name}.{$table_name}.app_id = {$db_name}.{$join_table_name}.app_id")
				->where("user_id = {$this->_uid} and {$db_name}.{$join_table_name}.app_visible = 1 and {$db_name}.{$join_table_name}.app_update_date > {$db_name}.{$table_name}.create_time")
				->findAll();
 
		$count= $historyList->join("{$db_name}.{$join_table_name} ON {$db_name}.{$table_name}.app_id = {$db_name}.{$join_table_name}.app_id")
		->where("user_id = {$this->_uid} and {$db_name}.{$join_table_name}.app_visible = 1 and {$db_name}.{$join_table_name}.app_update_date > {$db_name}.{$table_name}.create_time")
		->count();

		$Page = new Page($count, $this->_listRow);
		$Page->setAjax($p);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
		$show = $Page->show();
		$this->assign('list', $appList);
		$this->assign('page', $show);
		$html = $this->fetch('updateHistory');
		die(json_encode(array('html' => $html)));
	}
	/**
	 * 评论历史
	 * @method	commentHistory()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xiaoguang
	 * @date	2011-12-06
	 */
	public function commentHistory()
	{
		import("ORG.Util.Page");
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$historyList = D('Comment');
		$table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'comment`';
		$join_table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'appinfo`';
		
		$appList = $historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					->field("{$table_name}.app_id,{$table_name}.update_time,{$table_name}.content,{$table_name}.score_www,{$join_table_name}.app_name ")
					->order('update_time DESC')
					->page($p . ','  . $this->_listRow)->select();
				
		$count =	$historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					->count();
		$Page = new Page($count, $this->_listRow);
		$Page->setAjax($p);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
		$show = $Page->show();
		$this->assign('list', $appList);
		$this->assign('page', $show);
		$html = $this->fetch('commentHistory');
		die(json_encode(array('html' => $html)));
	}
	/**
	 * 下载历史
	 * @method	downloadHistory()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xiaoguang
	 * @date	2011-12-06
	 */
	public function downloadHistory()
	{
		import("ORG.Util.Page");
		$historyList = D('DownloadHistory');

		$p = isset($_GET['p']) ? $_GET['p'] : 1;

		$table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'download_history`';
		$join_table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'appinfo`';
		
		$appList = $historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					->field("{$table_name}.app_id,{$table_name}.create_time,{$join_table_name}.app_name ")
					->order('create_time DESC')
					->page($p . ','  . $this->_listRow)->select();
				
		$count =	$historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					->count();			
		$Page = new Page($count, $this->_listRow);
		$Page->setAjax($p);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
		$show = $Page->show();
		$this->assign('list', $appList);
		$this->assign('page', $show);
		$html = $this->fetch('downloadHistory');
		die(json_encode(array('html' => $html)));
	}
}

?>