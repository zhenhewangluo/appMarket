<?php
/**
 +------------------------------------------------------------------------------
 * @description:	开发者，注册，登录
 +------------------------------------------------------------------------------
 * @others:			NULL
 * @file:			DeveloperAction.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 +------------------------------------------------------------------------------
 */
class DeveloperAction extends AppBaseAction
{
//	private $_layout = 'App:Layout_layout'; //当前模块模板页
	public function _initialize() {
		if (!Cookie::is_set(team_id))
		{
			$this->assign('jumpUrl',"__APP__/Developercp/index/login");
			$this->error("请先登录");
		}
	}
	public function index ()
	{
		//$this->assign('viewcontent', $this->_tplPrefix . ':index');
		$this->display($this->_layout);
		$this->display();
	}
	public function resetPass()
	{
		
		$this->assign('viewcontent', $this->_tplPrefix . ':resetPass');
		$this->display($this->_layout);
	}
	public function loginAct()
	{	
			$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-Index/index'));
			$this->success('建设中，正在跳转首页！');
		//$this->assign('viewcontent', MODULE_NAME . ':index');
		//$this->display($this->_layout);	
	}	
	
	public function resetPassAction()
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
		//$userForm = M("Developer");
		$list = 1; //$userForm->where("email ='{$email}'")->find();
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
					'http://' . $_SERVER['HTTP_HOST'] . U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/validResetUrl', array('id' => $resetCode)),//$_SERVER['HTTP_HOST'] . __APP__."?g=App&m=RegisterUser&a=validResetUrl&id=".$resetCode,
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
	public function register()
	{
		$currentStep= Filter::act($_GET['step']); //邮箱	
		$step=array('','none','none','none','none');
		if(!empty($currentStep))
		{
			$stepimg[$currentStep]= '_'.$currentStep;
			$step[$currentStep]='block';
		}
		else
		{
			$stepimg[1]= '_1';
			$step[1]='block';
		}	
		$this->assign('stepimg',$stepimg);
		$this->assign('step',$step);
		$this->assign('viewcontent', $this->_tplPrefix . ':register');
		$this->display($this->_layout);	
	}
	public function registerInfo()
	{
		$this->assign('viewcontent', $this->_tplPrefix . ':registerInfo');
		$this->display($this->_layout);	
	}
	
	public function registerAction()
	{
		$email= Filter::act($_POST['email']); //邮箱		

//		$Form = D('Developer');
//		$currentUserID = $Form->add($data);
		$currentUserID =1;
		$email_randnumber=1;
//		if ($currentUserID !== false)
		{
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
			$currentUserID = Crypt::encrypt($currentUserID, C('DES_KEY'), 1);


			$replacement = array(
				$email,
				"开发者邮箱激活",
				'http://' . $_SERVER['HTTP_HOST'] . U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/validResetUrl', array('id' => $currentUserID, 'randcode' => $email_randnumber)),//$_SERVER['HTTP_HOST'] . __APP__."?g=App&m=Developer&a=activeAccount&id=".$currentUserID . "&randcode=" . $email_randnumber,
				'',
				C('MAIL_ADD')
			);			
			$mailContent = str_ireplace($pattern, $replacement, $mailContent);

			$this->mail($email , "***邮箱激活通知", $mailContent);
		}
		//跳转
		//$this->assign('jumpUrl',  __APP__."?m=Developer&a=activeAccount&id=".$currentUserID . "&randcode=" . $email_randnumber);
		//$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/register&step='.'2'));
		//$this->success('邮件已发送，请尽快激活！');		
		$this->redirect(U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/register&step='.'2'));
	}
	
	public function registerinfoAction()
	{
		$password= Filter::act($_POST['password']); 
		
		if(1)
		{
			
			
			$this->redirect(U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/register&step='.'4'));	
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
		//$userForm = M('registered_user');
		$list = array('id'=>1,'email_randnumber'=>1);//$userForm->getById($userId);
		if (!empty($list))
		{
			if ($identityCode != (int) $list['email_randnumber'])
			{

				$this->error('无效激活页 ！');
			}
			else
			{
				//已经激活 
				if (0)//$userForm->where('id=' . $userId)->getField('email_activestatus') == 1)
				{
					$this->error('无效激活页 ！');
				}
				else
				{
					$result = 1;//$userForm->where('id=' . $userId)->setField(array('status', 'email_activestatus'), array(1, 1));
					if ($result)
					{
						//跳转页
						$this->assign('jumpUrl', U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/register&step='.'3'));
						$this->success('邮箱开通用户成功！请填写资料');
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
			$mail->Host = C('MAIL_SMTPHOST'); // SMTP server
			//$mail->SMTPDebug = 2;   // enables SMTP debug information (for testing)
			$mail->SMTPAuth = true;   // enable SMTP authentication
			$mail->Port = C('MAIL_SMTPHOST_PORT');  // set the SMTP port for the GMAIL server
			$mail->Username = C('MAIL_USER'); // SMTP account username
			$mail->Password = C('MAIL_PASS');  // SMTP account password
			//$mail->AddReplyTo('2668176850@qq.com', 'First Last');
			$mail->AddAddress($Address, 'Mr Dear');
			$mail->SetFrom(C('MAIL_USER'), 'First Last');
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
	
	/*	 * *********************************************
	 * Function:       appList
	* Description:    待审核显示应用列表，
	* Input:
	* Return:
	* Others:
	* Date：         2012-7-31
	* Author：       sunsir
	* ************************************************** */
	
	public function appList()
	{
		$order = "app_update_date DESC";
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$appInfoModel = D('AppinfoDep');
		$list = $appInfoModel->order("{$order}")->relation('AppCategory')
		->field('app_id,app_name,author_name,app_version,app_update_date,app_downloads,app_score_www,app_starcount')
		->page($p . ',20')
		->where("app_visible = '1' AND app_audit = '0'")
		->select();
		$count = $appInfoModel->where("app_visible = '1'")->count();
		$cateObj = D('Category');
		$typeList = $cateObj->where("parent_id<=1")->findAll();
		foreach ($typeList as $k => $v)
		{
			$typeArr[] = $v['id'];
		}
		$typeArr[] = ''; //非子分类数组
		if (!empty($list))
		{
			foreach ($list as $k => &$v)
			{
				$v['app_score_average'] = number_format($list['app_score_www'] / ($list['app_starcount'] + 1e-5), 2);
				$v['app_isrecommon'] = '否';
				$v['app_cate_name'] = '暂无分类';
				if (!empty($v['AppCategory']))
				{
					foreach ($v['AppCategory'] as $vcate)
					{
						if ($vcate['cate_id'] == 1)
						{
							$v['app_isrecommon'] = '是';
							break;
						}
					}
					if (!in_array($v['AppCategory'][0]['cate_id'], $typeArr))
					{
						$v['app_cate_name'] = $v['AppCategory'][0]['cate_id'];
						$cateList[] = $v['app_cate_name'];
					}
				}
			}
			//取分类名字
			$cateWhere = implode(',', $cateList);
	
			$catelistResult = $cateObj->field('id,name')->where("`id` in ( {$cateWhere} )")->findALL();
	
			if (!empty($catelistResult))
				foreach ($list as &$v1)
				{
					foreach ($catelistResult as $v2)
						if ($v1['app_cate_name'] == $v2['id'])
						{
							$v1['app_cate_name'] = $v2['name'];
							break;
						}
				}
	
				$Page = new Page($count, 20);
				$show = $Page->show();
				$this->assign('applist', $list);
				$this->assign('page', $show);
		}
		$this->assign('viewcontent', MODULE_NAME . ':appList');
		$this->display($this->_layout);
	}
	
	/*	 * *********************************************
	 * Function:      appauditList
	* Description:    审核通过显示应用列表，
	* Input:
	* Return:
	* Others:
	* Date：         2012-8-3
	* Author：       sunsir
	* ************************************************** */
	public function appauditList()
	{
		$order = "app_update_date DESC";
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$appInfoModel = D('AppinfoDep');
		$list = $appInfoModel->order("{$order}")->relation('AppCategory')
		->field('app_id,app_name,author_name,app_version,app_update_date,app_downloads,app_score_www,app_starcount')
		->page($p . ',20')
		->where("app_visible = '1' AND app_audit = '1'")
		->select();
		$count = $appInfoModel->where("app_visible = '1' AND app_audit = '1'")->count();
		$cateObj = D('Category');
		$typeList = $cateObj->where("parent_id<=1")->findAll();
		foreach ($typeList as $k => $v)
		{
			$typeArr[] = $v['id'];
		}
		$typeArr[] = ''; //非子分类数组
		if (!empty($list))
		{
			foreach ($list as $k => &$v)
			{
				$v['app_score_average'] = number_format($list['app_score_www'] / ($list['app_starcount'] + 1e-5), 2);
				$v['app_isrecommon'] = '否';
				$v['app_cate_name'] = '暂无分类';
				if (!empty($v['AppCategory']))
				{
					foreach ($v['AppCategory'] as $vcate)
					{
						if ($vcate['cate_id'] == 1)
						{
							$v['app_isrecommon'] = '是';
							break;
						}
					}
					if (!in_array($v['AppCategory'][0]['cate_id'], $typeArr))
					{
						$v['app_cate_name'] = $v['AppCategory'][0]['cate_id'];
						$cateList[] = $v['app_cate_name'];
					}
				}
			}
			//取分类名字
			$cateWhere = implode(',', $cateList);
	
			$catelistResult = $cateObj->field('id,name')->where("`id` in ( {$cateWhere} )")->findALL();
	
			if (!empty($catelistResult))
				foreach ($list as &$v1)
				{
					foreach ($catelistResult as $v2)
						if ($v1['app_cate_name'] == $v2['id'])
						{
							$v1['app_cate_name'] = $v2['name'];
							break;
						}
				}
	
				$Page = new Page($count, 20);
				$show = $Page->show();
				$this->assign('applist', $list);
				$this->assign('page', $show);
		}
		$this->assign('viewcontent', MODULE_NAME . ':appauditList');
		$this->display($this->_layout);
	}
	/*	 * *********************************************
	 * Function:      appauditList
	* Description:    审核通过显示应用列表，
	* Input:
	* Return:
	* Others:
	* Date：         2012-8-3
	* Author：       sunsir
	* ************************************************** */
	public function appauditfailList()
	{
		$order = "app_update_date DESC";
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$appInfoModel = D('AppinfoDep');
		$list = $appInfoModel->order("{$order}")->relation('AppCategory')
		->field('app_id,app_name,author_name,app_version,app_update_date,app_downloads,app_score_www,app_starcount')
		->page($p . ',20')
		->where("app_visible = '1' AND app_audit = '2'")
		->select();
		$count = $appInfoModel->where("app_visible = '1' AND app_audit = '2'")->count();
		$cateObj = D('Category');
		$typeList = $cateObj->where("parent_id<=1")->findAll();
		foreach ($typeList as $k => $v)
		{
			$typeArr[] = $v['id'];
		}
		$typeArr[] = ''; //非子分类数组
		if (!empty($list))
		{
			foreach ($list as $k => &$v)
			{
				$v['app_score_average'] = number_format($list['app_score_www'] / ($list['app_starcount'] + 1e-5), 2);
				$v['app_isrecommon'] = '否';
				$v['app_cate_name'] = '暂无分类';
				if (!empty($v['AppCategory']))
				{
					foreach ($v['AppCategory'] as $vcate)
					{
						if ($vcate['cate_id'] == 1)
						{
							$v['app_isrecommon'] = '是';
							break;
						}
					}
					if (!in_array($v['AppCategory'][0]['cate_id'], $typeArr))
					{
						$v['app_cate_name'] = $v['AppCategory'][0]['cate_id'];
						$cateList[] = $v['app_cate_name'];
					}
				}
			}
			//取分类名字
			$cateWhere = implode(',', $cateList);
	
			$catelistResult = $cateObj->field('id,name')->where("`id` in ( {$cateWhere} )")->findALL();
	
			if (!empty($catelistResult))
				foreach ($list as &$v1)
				{
					foreach ($catelistResult as $v2)
						if ($v1['app_cate_name'] == $v2['id'])
						{
							$v1['app_cate_name'] = $v2['name'];
							break;
						}
				}
	
				$Page = new Page($count, 20);
				$show = $Page->show();
				$this->assign('applist', $list);
				$this->assign('page', $show);
		}
		$this->assign('viewcontent', MODULE_NAME . ':appauditfailList');
		$this->display($this->_layout);
	}

}
?>
