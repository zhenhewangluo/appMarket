<?php
header("Content-Type:text/html;charset=utf-8");
	$username_array=array('admin');
	$password_array=array('123456');

	/*define("DBHOST", $_SERVER['xxxx8_DB_HOST']);
	define("DBUSER", $_SERVER['xxxx8_DB_USER']);
	define("DBPASS", $_SERVER['xxxx8_DB_PW']);
	define("DBNAME", $_SERVER['xxxx8_DB_NAME']);
	
	
	$conn = mysql_connect(DBHOST, DBUSER, DBPASS) or die(mysql_error());
	mysql_select_db(DBNAME,$conn) or die(mysql_error());
	mysql_query("SET NAMES 'utf8'");*/
	
	require("../../inc/config.inc.php");
	include("../../inc/functions.php");
	
	include_once("cache.class.php");
	 
	 include_once("yun.func.php");
	 include_once("error.php");
	 
	// $cache = new xxxx8cache();
	// $cache->_carcheDir = WEBAPI_INTERFACE_CACE_PATH;
	// $cache->_expireTime = "3600*12";
	//define("WEBAPI_INTERFACE_CACE_PATH", INTERFACE_PATH . "data/");
	//if(!defined('INTERFACE_PATH')) define('INTERFACE_PATH', str_replace("\\", '/', dirname(__FILE__))."/");
	
?>