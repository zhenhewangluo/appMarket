<?php
/**
 * @description:	应用信息Model
 * @ohters:			NULL
 * @file:			AppinfoModel.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 */
class AppinfoModel extends RelationModel
{
	public $_msg = '';  //调试使用返回错误信息
	protected $dbName = '_v2_android';//指定数据库名称
	protected $pk = 'app_id';	//关联插入需要指定pk
	protected $_link = array(
		'AppDeviceType' => array(
			'mapping_type'		=> HAS_MANY,
			'class_name'		=> 'AppDeviceType',
			'foreign_key'		=> 'app_id',
			'mapping_name'		=> 'AppDeviceType',
			//'mapping_order'	=> 'create_time desc',
			//'mapping_fields'	=> 'app_id'
		),
		'Comment' => array(
			'mapping_type'		=> HAS_MANY,
			'class_name'		=> 'Comment',
			'foreign_key'		=> 'app_id',
			'mapping_name'		=> 'Comment',
			'mapping_fields'	=> 'app_id, user_name, create_time, score, content'
		),
		'AppCategory' => array(
			'mapping_type'		=> HAS_MANY,
			'class_name'		=> 'AppCategory',
			'foreign_key'		=> 'app_id',
			'mapping_name'		=> 'AppCategory',
			//'mapping_fields'	=> 'app_id,cate_id',
			'mapping_order'		=> '`cate_id` DESC'
		)
	);
	/**
	 * @method:		getAppInfoById()
	 * @description:	根据应用id获取应用信息
	 * @param:			int		$aid
	 * @param:			string	$fields
	 * @param:			mixed	$relation
	 * @return:		array
	 * @date:			2011-12-8
	 * @author:		xuhao
	 */
	public function getAppInfoById($aid, $fields = '', $relation = 'AppDeviceType')
	{
		if($fields == '')
		{
			$fields = 'app_id, author_name, app_name, app_desc, app_version, app_update_date, app_downloads, app_score_www, app_starcount';
		}
		return $this->field($fields)->relation($relation)->where("app_id = {$aid}")->find();
	}
	/**
	 * @method:		getAppInfoByIds()
	 * @description:	根据多个应用id获取多个应用信息
	 * @param:			array	$aid
	 * @return:		array
	 * @date:			2011-12-8
	 * @author:		xuhao
	 */
	public function getAppInfoByIds($aid)
	{
		if(!is_array($aid) || empty($aid))
		{
			$this->_msg = '非法参数';
			return false;
		}
		$ids = implode(',', $aid);
		return $this->field('app_id, author_name, app_name, app_desc, app_version, app_update_date, app_twodimensionalcode')->relation(false)->where("app_id in {$aid}")->find();
	}
	public function updateAppInfo($aid, $data)
	{
		if(empty($data) || empty($aid))
		{
			$this->_msg = '非法参数';
			return false;
		}
		return $this->where('`app_id` = ' . $aid)->save($data);
	}
//	/**
//	 * @method:		getAppIdsByCids()
//	 * @description:	根据分类id获取多个应用id信息
//	 * @param:			mixed	$cid
//	 * @return:		array
//	 * @date:			2011-12-8
//	 * @author:		xuhao
//	 */
//	public function getAppIdsByCids($cid)
//	{
//		if(is_array($cid) && !empty($cid))
//		{
//			$ids = implode(',', $cid);
//		}
//		else
//		{
//			$ids = (int) $cid;
//		}
//		$appIds =  M('AppCategory')->field('app_id')->where("`cate_id` in ( {$ids} )")->findAll();
//		if(empty($appIds))
//		{
//			$this->_msg = '该分类下无应用';
//			return false;
//		}
//		$appIdsArr = array();
//		foreach($appIds as $k => $v)
//		{
//			$appIdsArr[] = $v['app_id'];
//		}
//		return $appIdsArr;
//	}
}

?>
