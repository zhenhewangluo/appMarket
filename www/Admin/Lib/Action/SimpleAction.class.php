<?php

class SimpleAction extends Action
{

	private $_layout = 'Layout:simple';

//	protected function _initialize()
//	{
//		parent::_initialize();
//	}
	public function login()
	{
		$this->assign('viewcontent', MODULE_NAME . ':login');
		$this->display($this->_layout);
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
		if (Session::is_set(C('SESSION_NAME')))
			Session::set(C('SESSION_NAME'), '');

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
//				$this->assign('waitSecond', '-1');
//				$this->assign('jumpUrl', U(APP_NAME . '://'));
//				$this->success('登录成功！');
				$this->redirect('Apps/appList');
			}
			else
			{
				$this->error('用户名密码不匹配，请输入正确的用户名密码！');
			}
		}
		else
			$this->error('无此用户，请输入正确的用户名密码！');
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
			$this->trace('login_log success', dump($data, false));
		}
		else
		{
			$this->trace('login_log failed', dump($data, false));
		}
	}
}

?>
