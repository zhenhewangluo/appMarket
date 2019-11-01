<?php

return array(
	// 数据库常用配置

	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_HOST' => '172.18.0.1', // 数据库服务器地址
	'DB_NAME' => '_v2_common', // 数据库名
	'DB_USER' => 'root', // 数据库用户名
	'DB_PWD' => '123456', // 数据库密码

	'DB_PORT' => 3306, // 数据库端口
	'DB_PREFIX' => 'am_', // 数据库表前缀（因为漫游的原因，数据库表前缀必须写在本文件）
	'DB_CHARSET' => 'utf8', // 数据库编码
	'APP_AUTOLOAD_PATH' => 'Think.Util.,ORG.Util.', //自动加载目录 cookie等class
	// 是否开启URL Rewrite
	'URL_ROUTER_ON' => true,
	// 是否开启调试模式 (开启AllInOne模式时该配置无效, 将自动置为false)
	'APP_DEBUG' => true,
	'DEFAULT_THEME'=> 'pit',
	//公共全局主题路径
	'URL_MODEL' => 2,
	'AVATAR_API' => '/',
	'UC_UPLOAD_SIZE' => '1024',
	'UC_AUTH_KEY' => 'safsdfsda5643dgsdfgrew',
	'UC_IMAGE_TYPE' => array(1 => '.gif', 2 => '.jpg', 3 => '.png'), //WEB_PUBLIC_PATH
	'UC_TMP_DIR' => 'Upload/TempUserFace',
	'UC_AVA_DIR' => 'Upload/UserFace',
	'APP_FILE_PATH' => 'AppFile/',
	'PICTURE_HOST' => '',
	'UPLOAD_PATH' => './Upload/images/',
	'APP_ORDER_TYPE' => array('default' => '默认排序', 'update' => '更新时间', 'download' => '下载次数'),
	'APP_LIST_ROW' => array('5', '10', '25', '50', 'default' => '10'),
	'TMPL_JUMP_LAYOUT'		=> true,		// 成功，错误跳转页面是否加载布局
	'DES_KEY'		=> '*****',
	'COOKIE_NAME'		=> '****_id',
	'COOKIE_DOMAIN'		=> '.****.com',
	'MAIL_SMTPHOST' => 'ssl://smtp.gmail.com',
	'MAIL_SMTPHOST_PORT' => 465,
	'MAIL_USER' => '******',
	'MAIL_PASS' => '*****',
	'D_COOKIENAME' => 'D_COOKIE',
	'MAIL_ADD' => '******@gmail.com',
//	'TMPL_FILE_DEPR' => '/',
	'APP_GROUP_LIST'=>'App,Activity,Ucenter,Block,Developer,Developercp,Admin',
	'DEFAULT_GROUP'=>'App',
	'APP_SUB_DOMAIN_DEPLOY'=>1, // 开启子域名配置

	'TMPL_ACTION_ERROR'     => 'Public:error', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   => 'Public:success',
	'PHONEBOOK_PATH'	=> './Upload/PhoneBook/',
	'URL_HTML_SUFFIX'=>      '.html'

);
?>