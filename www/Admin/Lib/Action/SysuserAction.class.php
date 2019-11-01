<?php

/* * *******************************************
 * Description: 管理员用户操作
 * Others:      
 * Date：       2011-12-26
 * Author：     xiaoguang
 * ********************************************* */

class SysuserAction extends BaseAction
{

	private $_layout = 'Layout:layout';

	public function _initialize()
	{
		parent::_initialize();
		//$this->assign('userbar', $this->_userbar);
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
			$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/ucenter'));
			$this->success('您已经登录，正在跳转！');
		}
		$this->assign('viewcontent', MODULE_NAME . ':index');
		$this->display($this->_layout);
	}

	public function login()
	{

		$this->assign('viewcontent', MODULE_NAME . ':login');
		$this->display($this->_layout);
	}

	public function resetPassword()
	{
		
		$this->assign('viewcontent', MODULE_NAME . ':resetPassword');
		$this->display($this->_layout);
	}

	public function updatePassword()
	{
		if ($this->_uid == 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}

		$this->assign('viewcontent', MODULE_NAME . ':updatePassword');
		$this->display($this->_layout);
	}

	public function updatePasswordReset()
	{
		$resetcode = Filter::act(Req::get('id', 'get'));
		if(empty($resetcode) || strlen($resetcode) != 32)
			$this->error('非法操作！');
		$this->assign('resetcode', $resetcode);
		$this->assign('viewcontent', MODULE_NAME . ':updatePasswordReset');
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
		if ($this->_uid != 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/ucenter'));
			$this->success('您已经登录，正在跳转！');
		}


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
		$_POST['password'] = (Filter::act($_POST['password'])); //密码
		$_POST['email'] = Filter::act($_POST['email']); //邮箱
		$_POST['email_ispublic'] = Filter::act($_POST['email_ispublic'], 'int'); //邮箱是否公开
		//保存到数据库
		$Form = D("Sysuser");
		$data = $Form->create();
		if ($data)
		{
			$data['password']=md5($data['password']);
			//设置默认头像
			$data['RegisteredUserHead'] = array(
				'path' => 'Public\images\UserFace\noavatar_big.gif,Public\images\UserFace\noavatar_middle.gif,Public\images\UserFace\noavatar_small.gif',
				'type' => '3',
				'uploaddate' => date("Y-m-d H:i:s"),
			);
			$currentUserID = $Form->relation(true)->add($data);

			if ($currentUserID !== false)
			{
				//发送激活邮件
				$mailContent = file_get_contents(TEMPLATE_PATH . "/Templetes/mail.html");
				$pattern = array(
					'{{user_name}}',
					'{{mail_action_name}}',
					'{{URL}}',
					'{{notice}}',
				);
				// 加密UserID

				import('ORG.Crypt.Crypt');
				$currentUserID = Crypt::encrypt($currentUserID, C('DES_KEY'), 1);


				$replacement = array(
					$_POST['name'],
					"邮箱激活",
					$_SERVER['HTTP_HOST'] . __APP__."?m=Sysuser&a=activeAccount&id=".$currentUserID . "&randcode=" . $Form->email_randnumber,
					''
				);			
				$mailContent = str_ireplace($pattern, $replacement, $mailContent);

				$this->mail($_POST['email'] , "***邮箱激活通知", $mailContent);

				//跳转
				$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/login'));
				$this->success('注册成功！请尽快激活邮箱');
			}
			else
			{
				$this->error('注册失败！');
			}
		}
		else
		{
			$this->error($Form->getError());
		}
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
		if ($this->_uid == 0)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/login'));
			$this->error('未登录，请重新登录！');
		}
		$id = $this->_uid;
		//$email =  Filter::act($_POST['email']);
		$oldPassword = md5(Filter::act($_POST['user_pwd']));
		$newPassword = md5(Filter::act($_POST['user_new_pwd1']));
		//$email_ispublic = Filter::act($_POST['email_ispublic'],'int');//邮箱是否公开
		$registerInfo = D('Sysuser');
		//$list = $registerInfo->getById($id); 
		$list = $this->_userinfo;

		if ($list['password'] == $oldPassword)
		{
			if ($registerInfo->where('id=' . $id)->setField('password', $newPassword) !== false)
			{
				$this->clearCookie();
				$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/login'));
				$this->error('密码修改成功！请重新登录');
			}
			else
				$this->error('密码修改失败！');
		}
		else
		{
			$this->error('密码错误！');
		}
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
					$this->error("重置链接失效1");
				}
				else
				{  //修改密码
					$newPassword = md5(Filter::act($_POST['password']));

					$registerInfo = D('Sysuser');
					$userlist = $registerInfo->getById($list['user_id']);

					//检测是否有此用户
					if (!empty($userlist))
					{//存在
						if ($registerInfo->where('id=' . $userlist['id'])->setField('password', $newPassword) !== false)
						{
							//密码修改成功
							//修改重置表状态
							$resetInfo->where("reset_code='{$resetcode}'")->setField('status', 0);

							$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/login'));
							$this->error('密码修改成功！');
						}
						$this->error('密码修改失败！');
					}
					$this->error('用户不存在！重置链接失效');
				}
			}
			$this->error("重置链接失效2");
		}
		$this->error("重置链接失效3");
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
		vendor('PHPMailer.class#PHPMailer');
		$mail = new PHPMailer(); // the true param means it will throw exceptions on errors, which we need to catch
		$mail->IsSMTP(); // telling the class to use SMTP
		//邮件发送
		try
		{
			$mail->Host = "smtp.qq.com"; // SMTP server
			$mail->SMTPDebug = 2;   // enables SMTP debug information (for testing)
			$mail->SMTPAuth = true;   // enable SMTP authentication
			$mail->Host = "smtp.qq.com"; // sets the SMTP server
			$mail->Port = 25;  // set the SMTP port for the GMAIL server
			$mail->Username = "861666916"; // SMTP account username
			$mail->Password = "0537-6345268";  // SMTP account password
			//$mail->AddReplyTo('2668176850@qq.com', 'First Last');
			$mail->AddAddress($Address, 'Mr Dear');
			$mail->SetFrom('861666916@qq.com', 'First Last');
			$mail->Subject = $Subject;
			$body = $Msg;
			$mail->MsgHTML($body);
			$mail->Send();
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
		$userId = Filter::act($_GET['id']); //用户ID
		$identityCode = Filter::act($_GET['randcode']); //邮箱激活随机码
//decode userid here
		import('ORG.Crypt.Crypt');
		$userId = Crypt::decrypt($userId, C('DES_KEY'), 1);

		//查找
		$userForm = M('registered_user');
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
						$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/login'));

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
		//防止页面乱码，验证随即码
		//header('Content-type:text/html;charset=utf-8');
		if (md5($_POST['verifyCard']) != Session::get('verify'))
		{
			$this->error('验证码错误！');
		}
		//判断是否有cookie
		if(Session::is_set(C('SESSION_NAME')))
			Session::set (C('SESSION_NAME'), '');

		$name = Filter::act($_POST['name']); //账户邮箱

		$password = md5(Filter::act($_POST['password'])); //密码

		$userForm = D('Sysuser');
		$list = $userForm->getByName($name);
	
		if (!empty($list))
		{
			//找到此用户 
			if ($password == $list['password'])
			{
				
//				if ($list['status'] == 0)
//				{//用户不可用
//					$this->error('用户状态不可用，未激活或已被禁用！');
//				}

				//写COOKIE,设置登录状态//30天超时
				//$this->setLoginCookie($name, $password);
				import('ORG.Crypt.Crypt');
				Session::set(C('SESSION_NAME'), Crypt::encrypt($name . '|' . $password, C('DES_KEY'), 1));

				//记录登录日志
				$this->addLoginLog($list['id']);
				//记录last_login_time
				//$userForm->where('id =' . $list['id'])->setField('last_login_time', date("Y-m-d H:i:s"));

				//跳转页
				$this->assign('jumpUrl', U(APP_NAME . '://Apps/appList'));
				$this->success('登录成功！');
			}
			else
			{
				$this->error('用户名密码不匹配，请输入正确的用户名密码！');
			}
		}
		else
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
		$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/login'));
		if ($this->_uid > 0)
		{
			
			Session::clear();
			$this->success('注销成功！');die;
			
			//设置用户信息，退出时间为当前时间
			$data = array(
				'id' => $this->_uid,
				'last_logout_time' => date("Y-m-d H:i:s"), //getdate("yyyy-MM-dd HH:mm:ss")                  
			);
			$userForm = D(registered_user);
			if ($userForm->data($data)->save() !== false)
			{
				

				$this->_userinfo = 0;
				$this->_uid = 0;

				
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
		//防止页面乱码，验证随即码
		//header('Content-type:text/html;charset=utf-8');
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
				);
				$replacement = array(
					$list['name'],
					"密码重置",
					$_SERVER['HTTP_HOST'] . __APP__."?m=Sysuser&a=validResetUrl&id=".$resetCode,
					'请注意：此链接<b>' . $resetData["expire_time"] . '</b>之内有效。'
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
				$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/updatePasswordReset?id=' . $resetcode));
				$this->success('页面跳转中！');
			}
			$this->error("链接失效");
		}
		$this->error("链接失效");
	}

	/*	 * *********************************************
	 * Function:       setLoginCookie
	 * Description:    设置COOKIE 登录状态
	 * Input:          name/password
	 * Return:         
	 * Others:         
	 * Date：         2011-12-26
	 * Author：       xiaoguang
	 * ************************************************** */
	public function setLoginCookie($name, $password)
	{
		import('ORG.Crypt.Crypt');
		
		Cookie::set(C('COOKIE_PASS'), Crypt::encrypt($password, C('DES_KEY'), 1),30 * 24 * 3600);
	}

}

?>
