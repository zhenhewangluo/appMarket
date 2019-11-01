<?php
/**
 * @description:	应用设备类型Model
 * @ohters:			NULL
 * @file:			AppDeviceTypeModel.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 */
class AppDeviceTypeModel extends Model
{
	public $_msg = '';  //调试使用返回错误信息
	protected $dbName = '_v2_android';//指定数据库名称
	/**
	 * @method:		getAppDeviceInfoById()
	 * @description:	根据应用id查找相关应用设备信息
	 * @param:			int	$aid
	 * @return:		array
	 * @date:			2011-12-8
	 * @author:		xuhao
	 */
	public function getAppDeviceInfoById($aid)
	{
		return $this->field('apk_path, icon, logo, screenshots, app_size, pkg_name, app_resolution')->where("app_id = {$aid}")->find();
	}
}

?>
