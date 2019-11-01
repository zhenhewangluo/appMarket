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
		array('phone', 'mobi', '请输入正确的电话号码！', self::VALUE_VAILIDATE, '', self::MODEL_BOTH),
		array('qq', '/^[1-9][0-9]{4,}$/i', '请输入正确的QQ号码！', self::VALUE_VAILIDATE, '', self::MODEL_BOTH),
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

}

?>