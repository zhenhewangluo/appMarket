<?php
/**
 * 用户中心公共基础类
 * @file	UcenterBaseAction.class.php
 * @name	UcenterBaseAction
 * @author	xuhao
 * @desc	用户中心公共基础类（负责接口ajax操作）
 * @date	2012-3-20
 */
class UcenterdevpBaseAction extends BasedevpAction
{
	protected $_acttypeArr = array();
	protected $_bindtypeArr = array();
	protected $_bindtypeActArr = array();
	
	protected function _empty()
	{
		parent::_empty();
	}
	/**
	 * 检查手机号合法性
	 * @method	checkMobile()
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
	 * 检查用户名
	 * @method	checkUsername()
	 * @access protected
	 * @param	string	$mobile
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	/**
	 * 检测用户是否登录
	 * @method	isLogin()
	 * @access protected
	 * @param	string	$url		要跳转的url
	 * @param	boolean	$jump		是否直接跳转
	 * @param	boolean	$isReturn	是否需要返回
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-20
	 */
	protected function isLogin($url, $jump = true, $isReturn = false)
	{
		if($this->_uid > 0)
		{
			if($jump)
			{
				$this->assign('jumpUrl', U('AppStore://App-Index/index'));
				$this->error('您已经登录了！');
				die;
			}
			if($isReturn)
				return true;
			
			$this->_return['msg'] = '您已经登录了！';
			die(json_encode($this->_return));
		}
		return false;
	}
	/**
	 * 判断验证码是否正确
	 * @method	checkVerifyCard()
	 * @access protected
	 * @param	string	$verifyCard	验证码
	 * @param	boolean	$isJump		是否跳转
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function checkVerifyCard($verifyCard, $isJump = true)
	{
		if (md5($verifyCard) != Session::get('verify'))
		{
			$this->_return['msg'] = '验证码错误！';
			$isJump ? $this->error('验证码错误！') : die(json_encode($this->_return));
			die;
		}
		return true;
	}
	/**
	 * 判断手机验证码是否正确
	 * @method	checkMobileVerifyCode()
	 * @access protected
	 * @param	string	$mobileVefiry	手机验证码
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function checkMobileVerifyCode($mobileVefiry)
	{
		if(Session::is_set('mobileCode') && Session::get('mobileCode') == $mobileVefiry)
		{
			return true;
		}
		$this->error('手机验证码错误！');
	}
	/**
	 * 判断用户是否登录
	 * @method	checkLogin()
	 * @access protected
	 * @param	void
	 * @return boolean
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	protected function mustLogin()
	{
		if($this->_uid > 0)	return true;
		$this->assign('jumpUrl', U('Ucenter-Index/login', array('acttype' => 'market')));
		$this->error('请先登录！');
	}
}

?>
