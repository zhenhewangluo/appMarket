<?php
/**
 * 应用分类Model
 * @file	AppCategoryModel.class.php
 * @name	AppCategoryModel
 * @author	xuhao
 * @desc	有关应用分类表的操作
 * @date	2011-12-08
 */
class AppCategoryModel extends Model
{
	public $_msg = '';  //调试使用返回错误信息
	protected $dbName = '_v2_android';//指定数据库名称

	/**
	 * 根据应用id获取应用分类id信息
	 * @method	getAppCidByAid()
	 * @access public
	 * @param	int	$aid
	 * @return string
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	public function getAppCidByAid($aid)
	{
		$cidRow =  $this->field('cate_id')->where("app_id = {$aid}")->order('cate_id DESC')->find();
		return $cidRow['cate_id'];
	}
	/**
	 * 根据分类id获取多个应用id信息
	 * @method	getAppIdsByCids()
	 * @access public
	 * @param	mixed	$cid
	 * @return array
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	public function getAppIdsByCids($cid)
	{
		if(is_array($cid) && !empty($cid))
		{
			$ids = implode(',', $cid);
		}
		else
		{
			$ids = (int) $cid;
		}
		$appIds =  $this->field('app_id')->where("`cate_id` in ( {$ids} )")->findAll();
		if(empty($appIds))
		{
			$this->_msg = '该分类下无应用';
			return false;
		}
		$appIdsArr = array();
		foreach($appIds as $k => $v)
		{
			$appIdsArr[] = $v['app_id'];
		}
		return $appIdsArr;
	}
	
	
}
?>
