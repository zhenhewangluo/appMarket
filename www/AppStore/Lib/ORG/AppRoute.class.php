<?php
/**
 * App自定义路由类
 * @file	AppRoute.class.php
 * @name	AppRoute
 * @author	xuhao
 * @desc	App自定义路由类
 * @date	2011-12-08
 */
class AppRoute
{
	/**
	 * 返回页面的前一页路由地址
	 * @method	getRefRoute()
	 * @access public
	 * @param	void
	 * @return string 返回页面的前一页路由地址
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	public static function getRefRoute()
	{
		if(isset($_SERVER['HTTP_REFERER']) && (self::getEntryUrl() & $_SERVER['HTTP_REFERER']) == self::getEntryUrl())
		{
			return substr($_SERVER['HTTP_REFERER'], strlen(self::getEntryUrl()));
		}
//		if(isset($_SERVER['HTTP_REFERER']))
//		{
//			return substr($_SERVER['HTTP_REFERER'], strlen(self::getEntryUrl()));
//		}
		else
			return '';
	}
	/**
	 * 获取网站根路径
	 * @method	getHost()
	 * @access public
	 * @param	string $protocol 协议  默认为http协议，不需要带'://'
	 * @return string $baseUrl  网站根路径
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	public static function getHost($protocol='http')
	{
		$port    = $_SERVER['SERVER_PORT'] == 80 ? '' : ':'.$_SERVER['SERVER_PORT'];
		$baseUrl = $protocol.'://'.strtolower($_SERVER['SERVER_NAME']?$_SERVER['SERVER_NAME']:$_SERVER['HTTP_HOST']).$port;
		return $baseUrl;
	}
	/**
	 * 返回入口文件URl地址
	 * @method	getEntryUrl()
	 * @access public
	 * @param	void
	 * @return string 返回入口文件URL地址
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	public static function getEntryUrl()
	{
		return self::getHost().$_SERVER['SCRIPT_NAME'];
	}
}