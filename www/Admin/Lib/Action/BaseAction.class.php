<?php
class BaseAction extends Action
{
	protected $_uid = 0; //设定当前用户登录id（全局）0=未登录
	protected $_userinfo = array();
	protected $_userhead = array();
	protected $_usertype = 0;
	protected function _empty()
	{
		header('Content-Type:text/html; charset=utf-8');
		header("HTTP/1.1 404 Not Found");
		header("Status: 404 Not Found");
		$this->error('404-Document Not Found');
	}
	protected function _initialize()
	{
		header('Content-Type:text/html; charset=utf-8');
		//用户登录状态
		$this->_userinfo = $this->checkLoginSession();
		if ($this->_userinfo > 0)
		{
			$this->_uid = $this->_userinfo['id'];
			$this->_usertype =  $this->_userinfo['type'];
			$this->assign('webUname', $this->_userinfo['name']);
			$this->assign('webUname_role', ($this->_usertype ==1?"管理员":"其他后台用户"));	
		}
		else
		{
			$this->redirect('Simple/login');
		}
	}

	/*	 * *********************************************
	 * Function:       checkLoginSession
	 * Description:    检查SESSION 登录状态
	 * Input:          cookie
	 * Return:         array
	 * Others:         
	 * Date：         2011-12-26
	 * Author：       xiaoguang
	 * ************************************************** */

	public function checkLoginSession()
	{
		if (!Session::is_set(C('SESSION_NAME')))
		{
			//Session not exist
			return 0;
		}
		import('ORG.Crypt.Crypt');
		$userInfo = Crypt::decrypt(Session::get(C('SESSION_NAME')), C('DES_KEY'), 1);
		$userInfo = explode('|', $userInfo);
		$userForm = D('Sysuser');
		$list = $userForm->getByName($userInfo[0]);
		if (!empty($list))
		{
			//找到此用户 
			if ($userInfo[1] == $list['password'])
			{
				return $list;
			}
		}
		Session::delete(C('SESSION_NAME'));
		return 0;
	}
	
	
	/*	 * *********************************************
	 * Function:       clearSession
	 * Description:    清除SESSION 
	 * Input:          
	 * Return:         
	 * Others:         
	 * Date：         2011-12-26
	 * Author：       xiaoguang
	 * ************************************************** */

	public function clearSession()
	{
		Session::clear();
		$this->_userinfo = 0;
		$this->_uid = 0;
	}	
}
?>
