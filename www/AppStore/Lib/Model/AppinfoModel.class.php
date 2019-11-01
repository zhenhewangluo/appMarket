<?php
/**
 +------------------------------------------------------------------------------
 * @desc:	应用信息Model
 +------------------------------------------------------------------------------
 * @others:			NULL
 * @file:			AppinfoModel.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 +------------------------------------------------------------------------------
 */
class AppinfoModel extends RelationModel
{
	public $_msg = '';  //调试使用返回错误信息
	protected $dbName = '_v2_android';//指定数据库名称
	protected $_link = array(
		'AppDeviceType' => array(
			'mapping_type'		=> HAS_MANY,
			'class_name'		=> 'AppDeviceType',
			'foreign_key'		=> 'app_id',
			'mapping_name'		=> 'AppDeviceType',
			//'mapping_order'	=> 'create_time desc',
			//'mapping_fields'	=> 'app_id, user_name, create_time, score, content'
		),
		'Comment' => array(
			'mapping_type'		=> HAS_MANY,
			'class_name'		=> 'Comment',
			'foreign_key'		=> 'app_id',
			'mapping_name'		=> 'Comment',
			'mapping_fields'	=> 'app_id, user_name, create_time, score, content'
		),
//		'App_Category' => array(
//			'mapping_type'		=> BELONGS_TO,
//			'class_name'		=> 'App_Category',
//			'foreign_key'		=> 'cate_id',
//			'mapping_name'		=> 'App_Category',
//			'mapping_fields'	=> 'app_id'
//		)
	);
	/**
     * @desc	根据应用id获取应用信息
	 * @method	getAppInfoById()
     * @access public
     * @param	int		$aid		排序的字段
	 * @param	string	$fields		要查询的字段
	 * @param	mixed	$relation	要关联的表
     * @return	mixed
	 * @author	xuhao
	 * @date	2011-12-8
     */
	public function getAppInfoById($aid, $fields = '', $relation = 'AppDeviceType')
	{
		if($fields == '')
		{
			$fields = 'app_id, author_name, app_name, app_slogan, app_desc, app_version, app_update_date, app_downloads, app_score_www, app_starcount';
		}
		return $this->field($fields)->relation($relation)->where("app_id = {$aid} and app_visible = 1")->find();
	}
	/**
     +----------------------------------------------------------
     * 根据多个应用id获取多个应用信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array $aid 多个应用id组成的数组
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function getAppInfoByIds($aid)
	{
		if(!is_array($aid) || empty($aid))
		{
			$this->_msg = '非法参数';
			return false;
		}
		$ids = implode(',', $aid);
		return $this->field('app_id, author_name, app_name, app_desc, app_version, app_update_date, app_twodimensionalcode')->relation(false)->where("app_id in {$aid} and app_visible = 1")->find();
	}
	/**
     +----------------------------------------------------------
     * 根据应用id更新应用信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int $aid 应用id
	 * @param array $data 应用信息
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function updateAppInfo($aid, $data)
	{
		if(empty($data) || empty($aid))
		{
			$this->_msg = '非法参数';
			return false;
		}
		return $this->where('`app_id` = ' . $aid)->save($data);
	}
	/**
     +----------------------------------------------------------
     * 根据多个关键字查找应用
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
	 * @param array $keyWords 关键字
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
	 * @date:			2012-2-2
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function getAppsByKeyWords($keyWords = array(), $relation=false, $orderType = 'default', $listRow = 20)
	{

		if(empty($keyWords)) return 0;
		$where = '(';
		foreach($keyWords as $key => $val)
		{
			$val = urldecode($val);
			$where .= " app_name like '%{$val}%' or ";
		}
		if($orderType == 'default') $order = '';
		if($orderType == 'update') $order = 'app_update_date DESC';
		if($orderType == 'download') $order = 'app_downloads DESC';
		$where = rtrim($where, 'or ');
		$where .= ') and `app_visible` = 1';
		if($fields == '')
		{
			$fields = 'app_id, author_name, app_name, app_slogan, app_desc, app_version, app_update_date, app_downloads, app_score_www, app_starcount';
		}
		$p = isset($_GET['p']) && $_GET['p'] > 0 ? (int) $_GET['p'] : 1;
		import("ORG.Util.Page");

		$list = $this->field($fields)->relation($relation)->where($where)->page($p . ',' . $listRow)->order($order)->select();
		//var_dump($this->getLastSql());
		if(empty($list)) return 0;
		$count = $this->relation($relation)->where($where)->count();
		$Page = new Page($count, $listRow);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
		//$Page->setConfig('theme', '%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %first% %linkPage% %end% %downPage%');
		$show = $Page->show();
		//var_dump($show);
		return array('appList' => $list, 'pageShow' => $show);
	}
	/**
     +----------------------------------------------------------
     * 根据应用id获取应用评分
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
	 * @param int $aid 应用id
     +----------------------------------------------------------
     * @return int
     +----------------------------------------------------------
	 * @date:			2012-2-6
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function getAppScoreByAid($aid)
	{
		$return = array(
			'score' => 0,
			'count' => 0
		);
		$scoreRow = $this->field('app_score_www, app_starcount')->where("app_id = {$aid}")->find();
		if(!empty($scoreRow))
		{
			$return['score'] = $scoreRow['app_starcount'] <= 0 ? 0 : round($approw['app_score_www']/$approw['app_starcount'], 2);
			$return['count'] = $scoreRow['app_starcount'];
		}
		return $return;
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
	/**
	 * 根据appname获取app信息
	 * @method	getAppInfoByAppName()
	 * @access public
	 * @param	string	$appName	应用名称
	 * @param	string	$fields		查询字段
	 * @param	boolean	$relation	true表示关联其他表，false表示不关联其他表
	 * @return mixed
	 * @author	xuhao
	 * @date	2012-3-9
	 */
	public function getAppInfoByAppName($appName, $fields = 'app_id', $relation = false)
	{
		return $this->field($fields)->where("`app_name` = '{$appName}'")->relation($relation)->find();
	}
	/**
	 * 根据appid增加app下载次数
	 * @method	setIncDownloadsByAid()
	 * @access public
	 * @param	int	$aid	应用id
	 * @return mixed
	 * @author	xuhao
	 * @date	2012-3-9
	 */
	public function setIncDownloadsByAid($aid)
	{
		return $this->setInc('app_downloads', "app_id={$aid}");
	}
}

?>
