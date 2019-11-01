<?php

return array(
	// 数据库常用配置
	'DB_TYPE' => 'mysql', // 数据库类型
	'DB_HOST' => '172.18.0.1', // 数据库服务器地址
	'DB_NAME' => '_v2_common', // 数据库名
	'DB_USER' => 'root', // 数据库用户名
	'DB_PWD' => '123456', // 数据库密码
//	'DB_HOST'			=>	'localhost',			// 数据库服务器地址
//	'DB_NAME'			=>	'_v2_common',			// 数据库名
//	'DB_USER'			=>	'root',		// 数据库用户名
//	'DB_PWD'			=>	'',		// 数据库密码
	'DB_PORT' => 3306, // 数据库端口
	'DB_PREFIX' => 'am_', // 数据库表前缀（因为漫游的原因，数据库表前缀必须写在本文件）
	'DB_CHARSET' => 'utf8', // 数据库编码
	'APP_AUTOLOAD_PATH' => 'Think.Util.,ORG.Util.', //自动加载目录 cookie等class
	// 是否开启URL Rewrite
	'URL_ROUTER_ON' => true,
	// 是否开启调试模式 (开启AllInOne模式时该配置无效, 将自动置为false)
	'APP_DEBUG' => false,
	//公共全局主题路径
	'URL_MODEL' => 0,
	'UC_API' => 'http://localhost/amtp',
	'UC_UPLOAD_SIZE' => '1024',
	'UC_AUTH_KEY' => 'safsdfsda5643dgsdfgrew',
	'UC_IMAGE_TYPE' => array(1 => '.gif', 2 => '.jpg', 3 => '.png'), //WEB_PUBLIC_PATH
	'UC_TMP_DIR' => 'Upload/TempUserFace',
	'UC_AVA_DIR' => 'Upload/UserFace',
	'APP_FILE_PATH' => 'AppFile/',

	'UPLOAD_APP_PATH' => '/download/res/',


	'DES_KEY' => 'skyzine',
	'COOKIE_NAME' => 'skyzine_id',
	'MAIL_SMTPHOST' => 'ssl://smtp.gmail.com',
	'MAIL_SMTPHOST_PORT' => 465,
	'MAIL_USER' => 'huanjubao',
	'MAIL_PASS' => 'jinantianyi',
	'MAIL_ADD' => 'huanjubao@gmail.com',
//	'SESSION_AUTO_START'    => false,    // 是否自动开启Session
	'SESSION_NAME' => 'skyzine_admin',
	'SESSION_EXPIRE' => 30 * 60,

);
?>