<?php

/* * *******************************************
 * Description: 注册用户表模型 
 * Others:      用于一般注册用户
 * Date：       2011-12-07
 * Author：     xiaoguang
 * ********************************************* */

class RegisterUserModel extends RelationModel
{
	
	protected $tableName = 'registered_user';
	// 自动验证设置
	protected $_validate = array(
		array('name', 'require', '用户名必填！', self::MUST_VALIDATE, '', self::MODEL_INSERT),
		array('name', '', '用户名已经存在', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
		array('password', 'require', '密码必填！', self::MUST_VALIDATE, '', self::MODEL_INSERT),
		array('email', 'require', '邮箱必填！', self::MUST_VALIDATE, '', self::MODEL_INSERT),
		array('email', '', '邮箱已经存在！', self::MUST_VALIDATE, 'unique', self::MODEL_INSERT),
		array('email', 'email', '请输入正确的邮箱地址！', self::MUST_VALIDATE, '', self::MODEL_INSERT),
		array('weburl', 'url', '请输入正确的微博地址！', self::VALUE_VAILIDATE, '', self::MODEL_BOTH),
		array('phone', '/^((\d{3,4})|\d{3,4}-)?\d{7,8}(-\d+)*$/', '请输入正确的电话号码！', self::VALUE_VAILIDATE, '', self::MODEL_BOTH),
		array('QQ', 'number', '请输入正确的QQ号码！', self::VALUE_VAILIDATE, '', self::MODEL_BOTH),
	);
	// 自动填充设置
	protected $_auto = array(
		array('status', '0', self:: MODEL_INSERT),
		array('email_activestatus', '0', self:: MODEL_INSERT),
		array('first_login_terminal', '000000', self::MODEL_INSERT),
		array('last_login_terminal', '000000', self::MODEL_INSERT),
		array('email_randnumber', 'getNumber', self::MODEL_INSERT, 'callback'),
		array('registered_time', 'getDateTime', self::MODEL_INSERT, 'callback'),
		//array('last_login_time', 'getDateTime', self::MODEL_INSERT, 'callback'),
		//默认公开
	    array('mobile_ispublic', '1', self:: MODEL_INSERT),
	    array('qq_ispublic', '1', self:: MODEL_INSERT),	    	
		array('weburl_ispublic', '1', self:: MODEL_INSERT),	    	
		array('expressdelivery', '1', self:: MODEL_INSERT),	
		
		//head头像
//		array('path', 'Public\images\UserFace\noavatar_big.gif,Public\images\UserFace\noavatar_middle.gif,Public\images\UserFace\noavatar_small.gif', self::MODEL_INSERT),	
//		array('type', '2', self:: MODEL_INSERT),	
//		array('uploaddate', 'getDateTime', self:: MODEL_INSERT, 'callback'),
);

	protected $_link = array(
		'RegisteredUserHead' => array(
			'mapping_type'		=> HAS_ONE,
			'class_name'		=> 'RegisteredUserHead',
			'foreign_key'		=> 'uid',
			'mapping_name'		=> 'RegisteredUserHead',
			//'mapping_order'	=> 'create_time desc',
			'mapping_fields'	=> 'path'
		),
	);
	//获得日期 格式 Y-m-d H:i:s
	protected function getDateTime()
	{
		return date("Y-m-d H:i:s");
	}

	//生成随机数字10000-99999
	protected function getNumber()
	{
		return rand(10000, 99999);
	}
	/**
     +----------------------------------------------------------
     * 根据用户id获取用户id信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int $uid 用户id
     +----------------------------------------------------------
     * @return int
     +----------------------------------------------------------
	 * @date:			2011-1-3
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function getUserInfoByUid($uid, $fields = 'id', $relation=true)
	{
		return $this->field($fields)->relation($relation)->where("id = {$uid}")->find();
	}
}

?>