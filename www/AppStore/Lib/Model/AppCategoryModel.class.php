<?php
/**
 +------------------------------------------------------------------------------
 * @description:	应用设备类型Model
 +------------------------------------------------------------------------------
 * @others:			NULL
 * @file:			AppCategoryModel.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 +------------------------------------------------------------------------------
 */
class AppCategoryModel extends Model
{
	public $_msg = '';  //调试使用返回错误信息
	protected $dbName = '_v2_android';//指定数据库名称

	/**
     +----------------------------------------------------------
     * 根据应用id获取应用分类id信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int $aid 应用id
     +----------------------------------------------------------
     * @return int
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function getAppCidByAid($aid)
	{
		$cidRow =  $this->field('cate_id')->where("app_id = {$aid}")->order('cate_id DESC')->find();
		return (int)$cidRow['cate_id'];
	}

	/**
     +----------------------------------------------------------
     * 根据分类id获取多个应用id信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param mixed $cid 单个或多个分类id
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
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
	/**
	 * 根据应用id修改应用分类id
	 * @method	updateAppCidByAid()
	 * @access public
	 * @param	int		$aid	应用id
	 * @param	array	$data	要更新的信息
	 * @return mixed
	 * @author	xuhao
	 * @date	2012-3-9
	 */
	public function updateAppCidByAid($aid, $data = array(), $where = array())
	{
		if(empty($data) || empty($aid))
		{
			$this->_msg = '非法参数';
			return false;
		}
		$where = array_merge(array('app_id' => $aid), $where);
		return $this->where($where)->save($data);
	}
}
?>