<?php
/* * *******************************************
 * Description: 用户注册、登录、修改信息、头像上传
 * Others:      用于一般注册用户
 * Date：       2011-12-06
 * Author：     xiaoguang
 * ********************************************* */

class RegisterUserAction extends AppBaseAction
{

//	private $_layout = 'Layout:layout';
	private $_userbar = '';

	protected function _initialize()
	{
		parent::_initialize();
		//$this->_userbar = GROUP_NAME . ':' . 'Layout' . C('TMPL_FILE_DEPR') . 'userbar';
		$this->_userbar = C('TMPL_FILE_DEPR') == '/' ? TEMPLATE_PATH . C('TMPL_FILE_DEPR') . 'Layout/userbar.html' : 'Layout:userbar';
		Vendor('Ucenter.UcApi');  //载入UcApi扩展
		$this->assign('userbar', $this->_userbar);
		//$this->assign('uid', $this->_uid);
	}

	/*	 * ************************************************
	 * Function:       index()
	 * Description:    初始化用户注册页
	 * Input:          
	 * Return:        
	 * Others:         
	 * Date：         2011-12-06
	 * Author：       xiaoguang
	 * *********************************************** */

	public function index()
	{
		//已登录，又来注册跳转用户中心
		if ($this->_uid != 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/ucenter'));
			$this->success('您已经登录，正在跳转！');
		}
		$this->assign('viewcontent', $this->_tplPrefix . 'index');
		$this->display($this->_layout);
	}
	/*	 * ************************************************
	 * Function:       userinfo()
	 * Description:    初始化用户注册附加信息页
	 * Input:          
	 * Return:        
	 * Others:         
	 * Date：         2011-12-06
	 * Author：       xiaoguang
	 * *********************************************** */
	public function userinfo()
	{
		if ($this->_uid <= 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}
		$userForm = D('RegisterUser');
		$userdata = $userForm->getById($this->_uid);
		//显示当前用户信息
		//$this->assign('userinfo', $this->_userinfo);
		$this->assign('userdata', $userdata);
		$this->assign('viewcontent', $this->_tplPrefix . 'userinfo');
		$this->display($this->_layout);
	}

	//初始化
	public function headUpload()
	{
		if ($this->_uid == 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}
		$userObj = A('user');
		$flash = $userObj->showuploadAction();
		$this->assign('flash', $flash);

		$this->assign('viewcontent', $this->_tplPrefix . 'headUpload');
		$this->display($this->_layout);
	}

	public function login()
	{
		$this->isLogin();
		$this->assign('viewcontent', $this->_tplPrefix . 'login');
		$this->display($this->_layout);
	}

	public function resetPassword()
	{
		
		$this->assign('viewcontent', $this->_tplPrefix . 'resetPassword');
		$this->display($this->_layout);
	}

	public function updatePassword()
	{
		if ($this->_uid == 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}

		$this->assign('viewcontent', $this->_tplPrefix . 'updatePassword');
		$this->display($this->_layout);
	}

	public function updatePasswordReset()
	{
		$resetcode = Filter::act(Req::get('id', 'get'));
		if(empty($resetcode) || strlen($resetcode) != 32)
			$this->error('非法操作！');
		$this->assign('resetcode', $resetcode);
		$this->assign('viewcontent', $this->_tplPrefix . 'updatePasswordReset');
		$this->display($this->_layout);
	}

	public function userAvatar()
	{
		if ($this->_uid == 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}
		$this->assign('viewcontent', $this->_tplPrefix . 'userAvatar');
		$this->display($this->_layout);
	}

	
	/*	 * ************************************************
	 * Function:       ucenter()
	 * Description:    显示用户下载APP更新列表
	 * Input:	
	 * Return:        
	 * Others:         
	 * Date：         2011-12-21
	 * Author：       xiaoguang
	 * *********************************************** */

	public function ucenter()
	{
		if ($this->_uid <= 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}
		//$id = $this->_uid;
		import("ORG.Util.Page");
		$historyList = D('DownloadHistory');
		$table_name = '`' . C('DB_PREFIX') . 'download_history`';
		$join_table_name = '`' . C('DB_PREFIX') . 'appinfo`';
		$db_name = '`_v2_android`';

		$p = isset($_GET['p']) ? $_GET['p'] : 1;

		$appList = $historyList->page($p . ',8')
				->field("{$db_name}.{$table_name}.`app_id`,{$db_name}.{$table_name}.`create_time`,{$db_name}.{$join_table_name}.`app_name`,{$db_name}.{$join_table_name}.`app_update_date`,{$db_name}.{$join_table_name}.`app_version`")
				->order('create_time DESC')
				->join("{$db_name}.{$join_table_name} ON {$db_name}.{$table_name}.app_id = {$db_name}.{$join_table_name}.app_id")
				->where("user_id = {$this->_uid} and {$db_name}.{$join_table_name}.app_visible = 1 and {$db_name}.{$join_table_name}.app_update_date > {$db_name}.{$table_name}.create_time")
				->findAll();
 
		$count= $historyList->join("{$db_name}.{$join_table_name} ON {$db_name}.{$table_name}.app_id = {$db_name}.{$join_table_name}.app_id")
		->where("user_id = {$this->_uid} and {$db_name}.{$join_table_name}.app_visible = 1 and {$db_name}.{$join_table_name}.app_update_date > {$db_name}.{$table_name}.create_time")
		->count();

		$Page = new Page($count, 8);
		$show = $Page->show();
		$this->assign('list', $appList);
		$this->assign('page', $show);
		$this->assign('appCateTree', $appCateTree);
		$this->assign('viewcontent', $this->_tplPrefix . 'ucenter');
		
		$this->display($this->_layout);
	}

	/*	 * ************************************************
	 * Function:       commentHistory()
	 * Description:    显示用户已评论APP列表
	 * Input:	
	 * Return:        
	 * Others:         
	 * Date：         2011-12-06
	 * Author：       xiaoguang
	 * *********************************************** */

	public function commentHistory()
	{
		if ($this->_uid <= 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}
		//$id = $this->_uid;
		import("ORG.Util.Page");
		$p = isset($_GET['p']) ? $_GET['p'] : 1;


		$historyList = D('Comment');


		$table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'comment`';
		$join_table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'appinfo`';
		
		$appList = $historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					->field("{$table_name}.app_id,{$table_name}.update_time,{$table_name}.content,{$table_name}.score_www,{$join_table_name}.app_name ")
					->order('update_time DESC')
					->page($p . ',8')->select();
				
		$count =	$historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					->count();		

//		//评论按更新时间排列
//		$appIds = $historyList->where('user_id = ' . $id)->group('app_id')->field('app_id,update_time,content,score_www')->order('update_time DESC')->page($p . ',8')->select();
//		$count = $historyList->where('user_id = ' . $id)->count(' DISTINCT `app_id` ');
//
//		foreach ($appIds as $k => $v)
//		{
//			$subIds[] = $v['app_id'];
//		}
//		$subIds = implode(',', $subIds);
//
//
//		$appObj = new AppinfoModel();
//
//		//需要添加其他信息field 如图片等要从其他表取得 ！！
//		$list = $appObj->where("`app_id` in ( {$subIds} )")->field('app_id,app_name,author_name')->select();
//
//		foreach ($appIds as $k => $v)
//		{
//			foreach ($list as $k2 => $v2)
//			{
//				if ($v2['app_id'] == $v['app_id'])
//				{
//					$appIds[$k]['app_name'] = $v2['app_name'];
//					$appIds[$k]['author_name'] = $v2['author_name'];
//					break 1;
//				}
//			}
//		}
		$Page = new Page($count, 8);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
		$show = $Page->show();
		$this->assign('list', $appList);
		$this->assign('page', $show);
		$this->assign('appCateTree', $appCateTree);
		$this->assign('viewcontent', $this->_tplPrefix . 'commentHistory');
		$this->display($this->_layout);
	}

	/*	 * ************************************************
	 * Function:       downloadHistory()
	 * Description:    显示用户下载APP列表
	 * Input:	
	 * Return:        
	 * Others:         
	 * Date：         2011-12-06
	 * Author：       xiaoguang
	 * *********************************************** */

	public function downloadHistory()
	{
		if ($this->_uid <= 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}
		//$id = $this->_uid;
		import("ORG.Util.Page");
		$historyList = D('DownloadHistory');

		$p = isset($_GET['p']) ? $_GET['p'] : 1;

		$table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'download_history`';
		$join_table_name = '`_v2_android`.'.'`' . C('DB_PREFIX') . 'appinfo`';
		
		$appList = $historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					->field("{$table_name}.app_id,{$table_name}.create_time,{$join_table_name}.app_name ")
					//->group("{$table_name}.app_id") //可删？download_history 无重复user_id--appid
					->order('create_time DESC')
					->page($p . ',8')->select();
				
		$count =	$historyList->join("{$join_table_name} ON {$join_table_name}.app_id = {$table_name}.app_id ")
					->where('user_id = ' . $this->_uid)
					//->group("{$table_name}.app_id") //可删？
					->count();			
//		
//
//		$appIds = $historyList->where('user_id = ' . $id)->group('app_id')->field('app_id,create_time ')->order('create_time DESC')->page($p . ',8')->select();
//	
//		
//		$count = $historyList->where('user_id = ' . $id)->count(' DISTINCT `app_id` ');
//
//		foreach ($appIds as $k => $v)
//		{
//			$subIds[] = $v['app_id'];
//		}
//		$subIds = implode(',', $subIds);
//
//
//
//		$appObj = new AppinfoModel();
//
//		//需要添加其他信息field 如图片等要从其他表取得 ！！
//		$list = $appObj->where("`app_id` in ( {$subIds} )")->field('app_id,app_name,author_name')->select();
//
//		foreach ($appIds as $k => $v)
//		{
//			foreach ($list as $k2 => $v2)
//			{
//				if ($v2['app_id'] == $v['app_id'])
//				{
//					$appIds[$k]['app_name'] = $v2['app_name'];
//					$appIds[$k]['author_name'] = $v2['author_name'];
//					break 1;
//				}
//			}
//		}
		//$count = $appObj->where("`app_id` in ( {$subIdsStr} )")->count(); 

		$Page = new Page($count, 8);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
		$show = $Page->show();
		$this->assign('list', $appList);
		$this->assign('page', $show);
		$this->assign('appCateTree', $appCateTree);
		$this->assign('viewcontent', $this->_tplPrefix . 'downloadHistory');

		$this->display($this->_layout);
	}

	/*	 * ************************************************
	 * Function:       register()
	 * Description:    用户注册，实现登录
	 * Input:          name\email\password\
	 * Return:         uid
	 * Others:         
	 * Date：         2011-12-06
	 * Author：       xiaoguang
	 * *********************************************** */

	public function register()
	{
		//已登录，又来注册跳转用户中心
		$this->isLogin();

		//防止页面乱码，验证随即码
		//header('Content-type:text/html;charset=utf-8');
		if (md5($_POST['verifyCard']) != Session::get('verify'))
		{
			$this->error('验证码错误！');
		}

		//构造数据
		//import('ORG.Util.Input');
		//import('ORG.Util.Filter');

		$_POST['name'] = Filter::act($_POST['name']); //用户名
		$_POST['password'] = (Filter::act($_POST['password1'])); //密码
		$_POST['email'] = Filter::act($_POST['email']); //邮箱
		$_POST['email_ispublic'] = Filter::act($_POST['email_ispublic'], 'int'); //邮箱是否公开
		if(checkKeyWord($_POST['name']))
		{
			$this->error('用户名包含被系统屏蔽的字符！');
		}
		//if(keyWordFilter($_POST['name']))
		
		
		//保存到数据库
		$Form = D("RegisterUser");
		$data = $Form->create();
		if ($data)
		{
			$reg = UcApi::reg($_POST['name'],$_POST['password'],$_POST['email']);
			if($reg <= 0) {
				$this->error(UcApi::getError());
			}
			$email_randnumber = rand(10000, 99999);
			$Form->where("`id` = {$reg}")->save(array('email_randnumber' => $email_randnumber));
			//发送激活邮件
			$mailContent = file_get_contents(TEMPLATE_PATH . "/Templetes/mail.html");
			$pattern = array(
				'{{user_name}}',
				'{{mail_action_name}}',
				'{{URL}}',
				'{{notice}}',
				'{{EMAIL}}'
			);
			// 加密UserID

			import('ORG.Crypt.Crypt');
			$reg = Crypt::encrypt($reg, C('DES_KEY'), 1);
			$replacement = array(
				$_POST['name'],
				"邮箱激活",
				'http://' . $_SERVER['HTTP_HOST'] . U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/activeAccount', array('id' => $reg, 'randcode' => $email_randnumber)),//__APP__."?m=RegisterUser&a=&=".$reg . "&randcode=" . ,
				'',
				C('MAIL_ADD')
			);			
			$mailContent = str_ireplace($pattern, $replacement, $mailContent);

			$this->mail($_POST['email'] , "***邮箱激活通知", $mailContent);

			//跳转
			//$this->assign('waitSecond', '33');
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
			$this->success("注册成功！请尽快激活邮箱");
		}
		else
		{
			$this->error($Form->getError());
		}
		$this->error('注册失败！');
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

	/**	 * ***********************************************
	 * Function:       updatePassword()
	 * Description:    修改用户密码
	 * Input:          name/password/newpassword
	 * Return:         
	 * Others:         
	 * Date：         2011-12-06
	 * Author：       xiaoguang
	 * *********************************************** */
	public function updatePasswordAction()
	{
		if ($this->_uid <= 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}
		if(empty($_POST['user_new_pwd1']))
			$this->error('密码不能为空');		
		//$id = $this->_uid;
		//$email =  Filter::act($_POST['email']);
		$oldPassword = md5(Filter::act($_POST['user_pwd']));
		$newPassword = md5(Filter::act($_POST['user_new_pwd1']));
		//$email_ispublic = Filter::act($_POST['email_ispublic'],'int');//邮箱是否公开
		$registerInfo = D('RegisterUser');
		$list = $registerInfo->getById($this->_uid); 
		//$list = $this->_userinfo;

		if ($list['password'] != $oldPassword)
		{
			$this->error('密码错误！');
		}	
		$up = UcApi::updatePassword($this->_username, $_POST['user_pwd'], $_POST['user_new_pwd1'], $this->_userinfo['email']);
		if($up <= 0)
		{
			$this->error(UcApi::getError());
		}
		$logout = UcApi::logout();
		$this->clearCookie();
		//跳转ucenter页
		$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
		$this->success("{$logout}密码修改成功！请重新登录!");
	}

	/**	 * ***********************************************
	 * Function:       updatePasswordResetAction()
	 * Description:    修改用户密码 (重置)
	 * Input:          name/password/newpassword
	 * Return:         
	 * Others:         
	 * Date：         2011-12-06
	 * Author：       xiaoguang
	 * *********************************************** */
	public function updatePasswordResetAction()
	{

		$resetcode = Filter::act($_POST['resetcode']);
		if(empty($resetcode) || strlen($resetcode) != 32)
			$this->error('非法操作！');
		//检测是否有重复值
		$resetInfo = D('reset_passwd');
		$list = $resetInfo->where("reset_code='{$resetcode}'")->find();
		if (!empty($list))
		{
			if ($list["status"] == 1)
			{
				//判断当前日期是否大于失效日期，如果大与失效日期，退出
				$expireDate = $list["expire_time"];
				$now = date("Y-m-d H:i:s");
				if (strtotime($now) > strtotime($expireDate))
				{
					$this->error("重置链接失效");
				}
//				$newPassword = md5(Filter::act($_POST['password']));

				$registerInfo = D('RegisterUser');
				$userlist = $registerInfo->getById($list['user_id']);

				//检测是否有此用户
				if (!empty($userlist))
				{//存在
//					$oldPass = $userlist['password'];
					$up = UcApi::resetPassword($userlist['name'],  $_POST['password']);
					if($up <= 0)
					{
						$this->error('重置失败！');//UcApi::getError());
					}
					$logout = UcApi::logout();
					//$this->clearCookie();
					//密码修改成功
					//修改重置表状态
					$resetInfo->where("reset_code='{$resetcode}'")->setField('status', 0);

					$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
					$this->success("{$logout}密码修改成功！");

				}
				$this->error('用户不存在！重置链接失效');
			}
		}
		$this->error("重置链接失效");
	}

	/**	 * ***********************************************
	 * Function:       addLoginLog($uid)
	 * Description:    用户注册和登陆的时候记录登陆日志
	 * Input:          uid
	 * Return:         
	 * Others:         
	 * Date：         2011-12-07
	 * Author：       xiaoguang
	 * *********************************************** */
	public function addLoginLog($uid)
	{
		Load('extend');
		//保存到数据库
		$data = array(
			'uid' => $uid,
			'mid' => '000000',
			'ip' => get_client_ip(),
			'dateline' => time(),
		);
		$LogModel = M('login_log');

		if ($LogModel->data($data)->add() !== false)
		{
			//$this->trace('login_log success', dump($data, false));
		}
		else
		{
			//$this->trace('login_log failed', dump($data, false));
		}
	}

	/*	 * ************************************************
	 * Function:       mail
	 * Description:    发送邮件
	 * Input:          $Address,$Subject,$Msg
	 * Return:         
	 * Others:         
	 * Date：         2011-12-07
	 * Author：       xiaoguang
	 * *********************************************** */

	public function mail($Address, $Subject, $Msg)
	{	
		//初始化
		//header('Content-type:textml;charset=utf-8');
		vendor('PHPMailer.class#phpmailer');
		$mail = new PHPMailer(); // the true param means it will throw exceptions on errors, which we need to catch
		$mail->IsSMTP(); // telling the class to use SMTP
		//邮件发送
		try
		{
			//$mail->IsHTML(true);
			//$mail->SetLanguage('zh_cn');
			$mail->Priority = 1;
//			$mail->Hostname = 'hjapp.com';
			
			$mail->CharSet = 'UTF-8';
			$mail->Host = C('MAIL_SMTPHOST'); // SMTP server
			//$mail->SMTPDebug = 2;   // enables SMTP debug information (for testing)
			$mail->SMTPAuth = true;   // enable SMTP authentication
			$mail->Port = C('MAIL_SMTPHOST_PORT');  // set the SMTP port for the GMAIL server
			$mail->Username =C('MAIL_USER'); // SMTP account username
			$mail->Password = C('MAIL_PASS');  // SMTP account password
			//$mail->AddReplyTo('2668176850@qq.com', 'First Last');
			$mail->AddAddress($Address);//, 'Mr Dear');
			$mail->SetFrom(C('MAIL_USER').'@gmail.com');//, 'service');
			$mail->Subject = "=?utf-8?B?" . base64_encode($Subject) . "?=";
			$Body = $Msg;
			$mail->MsgHTML($Body);
			$mail->Send();
//			if($mail->ErrorInfo != '')
//			{	
//				die($mail->ErrorInfo);
//			}
			//$this->success("邮件已经发送，请接收！");

		}
		catch (phpmailerException $e)
		{
			$this->trace($e->errorMessage()); //Pretty error messages from PHPMailer
		}
		catch (Exception $e)
		{
			$this->trace($e->errorMessage());  //Boring error messages from anything else!
		}
	}

	////////////////////////////////////////
	//头像信息
	////////////////////////////////////////

	/*	 * *********************************************
	 * Function:       文件上传
	 * Description:    用户注册或者修改时候，头像文件上传
	 * Input:          文件路径
	 * Return:         
	 * Others:         
	 * Date：         2011-12-06
	 * Author：       xiaoguang
	 * ************************************************** */
	protected function upload()
	{
		import("ORG.Net.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小,1M
		$upload->maxSize = 1049000;
		//设置上传文件类型
		$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
		//设置附件上传目录
		$upload->savePath = './Upload/UserFace/';
		//设置需要生成缩略图，仅对图像文件有效
		$upload->thumb = true;
		// 设置引用图片类库包路径
		$upload->imageClassPath = 'ORG.Util.Image';
		//设置需要生成缩略图的文件后缀
		$upload->thumbPrefix = 'm_';  //生产1张缩略图
		//设置缩略图最大宽度
		$upload->thumbMaxWidth = '120,100';
		//设置缩略图最大高度
		$upload->thumbMaxHeight = '120,100';
		//设置上传文件规则
		$upload->saveRule = uniqid;  //????
		//删除原图
		$upload->thumbRemoveOrigin = true;
		if (!$upload->upload())
		{
			//捕获上传异常
			$this->error($upload->getErrorMsg());
		}
		else
		{
			//取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo();
			import("ORG.Util.Image");
			//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
//            Image::water($uploadList[0]['savepath'].'m_'.$uploadList[0]['savename'], '../Public/Images/logo2.png');
			$_POST['image'] = 'm_' . $uploadList[0]['savename'];
		}
	}

	/*	 * ************************************************
	 * Function:       邮件激活
	 * Description:    用于邮箱激活用户开通状态
	 * Input:          用户ID和随机码
	 * Return:         无
	 * Others:         
	 * Date：         2011-12-07
	 * Author：       xiaoguang
	 * *********************************************** */

	public function activeAccount()
	{
		//获取变量
		$this->isLogin();
		$userId = Filter::act($_GET['id']); //用户ID
		$identityCode = Filter::act($_GET['randcode']); //邮箱激活随机码
//decode userid here
		
		import('ORG.Crypt.Crypt');
		$userId = Crypt::decrypt($userId, C('DES_KEY'), 1);
		//查找
		$userForm = M('registered_user');
		UcApi::resetPassword($userlist['name'],  $_POST['password']);
		$list = $userForm->getById($userId);
		if (!empty($list))
		{
			if ($identityCode != (int) $list['email_randnumber'])
			{

				$this->error('无效激活页 ！');
			}
			else
			{
				//已经激活 
//				$ucMembersObj = new UcMembersModel();
//				$ucMembersRow = $ucMembersObj->getUserStatusByUid($uid);
				if ($userForm->where('id=' . $userId)->getField('email_activestatus') == 1)
				{
					$this->error('无效激活页 ！');
				}
				else
				{
					$result = $userForm->where('id=' . $userId)->setField(array('status', 'email_activestatus'), array(1, 1));
					if ($result)
					{
						//跳转页
						$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));

						$this->success('邮箱开通用户成功！请登录');
					}
					else
						$this->error('邮箱开通用户失败！');
				}
			}
		}
		else
		{ //无效激活页 
			$this->error('无效激活页 ！');
		}
	}

	/*	 * ************************************************
	 * Function:       登录
	 * Description:    登录用户状态，设置cookie
	 * Input:          Email/ password
	 * Return:         无
	 * Others:         
	 * Date：         2011-12-09
	 * Author：       xiaoguang
	 * *********************************************** */

	public function loginAction()
	{
		
		if (md5($_POST['verifyCard']) != Session::get('verify'))
		{
			$this->error('验证码错误！');
		}
		
		//判断是否有cookie	
		if (Cookie::is_set(C('COOKIE_NAME')))Cookie::delete(C('COOKIE_NAME'));
		$mail = Filter::act($_POST['email']); //账户邮箱
		$password = md5(Filter::act($_POST['password'])); //密码
		
		//die(var_dump($ucLogin));
		//查找自身数据库内是否有数据
		$userForm = D('RegisterUser');
		$list = $userForm->getByEmail($mail);
		$ucLogin = '';
		if (!empty($list))
		{
			//找到此用户 
			if ($password == $list['password'])
			{
				if ($list['status'] == 0)
				{//用户不可用
					$this->error('用户状态不可用，未激活或已被禁用！');
				}
				//与uc通信
				$expired = time() + 30 * 24 * 3600;//如果登录成功将设置cookie并设置过期时间为1个月
				$login = UcApi::login($mail, $_POST['password'], 2, $expired);//设置cookie_name和解密串
				
				if($login === FALSE) {
					$this->error('登录失败！');
				} else {
					$ucLogin = $login['synlogin'];
				}
				//记录登录日志
				$this->addLoginLog($list['id']);
				//记录last_login_time
				$userForm->where('id =' . $list['id'])->setField('last_login_time', date("Y-m-d H:i:s"));
				//$this->assign('waitSecond', '999');
				
				$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/ucenter'));
				$this->success("{$ucLogin}登录成功！");
				die;
			}

			$this->error('用户名密码不匹配，请输入正确的用户名密码！');

		}
		elseif(!empty($ucLogin))//如果uc有数据，本地无数据，则写入本地数据
		{
			$data = array(
				'id' => $login['uid'],
				'name' => $login['username'],
				'password' => md5($login['password']),
				'email' => $login['email'],
				'RegisteredUserHead' => array(
					'path' => 'Public/images/UserFace/noavatar_big.gif,Public/images/UserFace/noavatar_middle.gif,Public/images/UserFace/noavatar_small.gif',
					'type' => '3',
					'uploaddate' => date("Y-m-d H:i:s"),
				)
			);
			$currentUserID = $userForm->relation(true)->add($data);
			if($currentUserID !== false)
			{
				$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/ucenter'));
				$this->success("{$ucLogin}登录成功！");
			}
			$this->error('登录失败！');
		}
		$this->error('无此用户，请输入正确的用户名密码！');
	}

	/*	 * ************************************************
	 * Function:       注销
	 * Description:    页面注销用户状态，清除cookie
	 * Input:          
	 * Return:         无，注销后跳转到首页
	 * Others:         
	 * Date：         2011-12-09
	 * Author：       xiaoguang
	 * *********************************************** */

	public function logout()
	{
		$ucLogout = '';
		$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/login'));
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
				$ucLogout = UcApi::logout();
				//die(var_dump($ucLogout));
				$this->success("{$ucLogout}注销成功！");
			}
			$this->error('注销失败！');
		}
		
		$this->error('未登录！');
	}

	/*	 * *********************************************
	 * Function:       resetPasswordAction
	 * Description:    用户找回密码，检测用户和邮箱是否匹配，如果匹配发送邮件，
	 * Input:          uid、password
	 * Return:         
	 * Others:         
	 * Date：         2011-12-09
	 * Author：       xiaoguang
	 * ************************************************** */

	public function resetPasswordAction($uid)
	{
		if (md5($_POST['verifyCard']) != Session::get('verify'))
		{
			$this->error('验证码错误！');
		}

		$email = Filter::act($_POST['email']); //Email
		//查询用户和email是否存在
		//搜索数据库
		$userForm = M("registered_user");
		$list = $userForm->where("email ='{$email}'")->find();
		if (!empty($list))
		{
			//添加到重置密码表
			//重置密码
			$resetTime = time();
			$expireTime = $resetTime + 86400 * 10; //10天后超时
			$resetCode = md5($resetTime + intval($list['id']));

			//增加数据到密码重置表
			$resetPasswordForm = M("reset_passwd");
			$resetData = array(
				"user_id" => $list['id'],
				"reset_code" => $resetCode,
				"create_time" => date("Y-m-d H:i:s", $resetTime),
				"expire_time" => date("Y-m-d H:i:s", $expireTime),
				"status" => "1",
			);


			if ($resetPasswordForm->data($resetData)->add() !== false)
			{
				//发送邮件,确认
				$mailContent = file_get_contents(TEMPLATE_PATH . "/Templetes/mail.html");
				//写入重置链接
				$pattern = array(
					'{{user_name}}',
					'{{mail_action_name}}',
					'{{URL}}',
					'{{notice}}',
					'{{EMAIL}}'
				);
				$replacement = array(
					$list['name'],
					"密码重置",
					'http://' . $_SERVER['HTTP_HOST'] . U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/validResetUrl', array('id' => $resetCode)),//$_SERVER['HTTP_HOST'] . __APP__."?m=RegisterUser&a=&id=".,
					'请注意：此链接<b>' . $resetData["expire_time"] . '</b>之内有效。',
					C('MAIL_ADD')
				);
				$mailContent = str_ireplace($pattern, $replacement, $mailContent);

				$this->mail( $list['email'], "***密码找回通知", $mailContent);
				$this->success("密码重置邮件已发送，请查收！");
			}
			else
				$this->error('重置失败！');
		}
		$this->error("没有此用户邮箱，请重新输入");
	}

	/*	 * *********************************************
	 * Function:       密码重置链接判断
	 * Description:    用户打开Email邮件的链接后，判断链接是否可用
	 * Input:          uid
	 * Return:         
	 * Others:         
	 * Date：         2011-12-09
	 * Author：       xiaoguang
	 * ************************************************** */

	public function validResetUrl()
	{
		$resetcode = Filter::act(Req::get('id', 'get'));
		if(empty($resetcode) || strlen($resetcode) != 32)
			$this->error('非法操作！');		
		//检测是否有重复值
		$registerInfo = D('reset_passwd');
		$list = $registerInfo->where("reset_code='{$resetcode}'")->find();
		if (!empty($list))
		{
			if ($list["status"] == 1)
			{
				//判断当前日期是否大于失效日期，如果大与失效日期，退出
				$expireDate = $list["expire_time"];
				$now = date("Y-m-d H:i:s");
				if (strtotime($now) > strtotime($expireDate))
				{
					$this->error("链接失效");
				}
				//链接跳转到密码修改页面  ？不能等同密码修改页面
				//$this->redirect('User/list', array('cate_id'=>2), 5,'页面跳转中~');
				$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/updatePasswordReset?id=' . $resetcode));
				$this->success('页面跳转中！');
			}
			$this->error("链接失效");
		}
		$this->error("链接失效");
	}
	public function loginWindow()
	{
		$this->display('AjaxPage:login');
	}
	private function isLogin()
	{
		if($this->_uid > 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/ucenter'));
			$this->error('您已登录！');
		}
	}
}

?>
