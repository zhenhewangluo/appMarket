<?php
/**
 +------------------------------------------------------------------------------
 * @description:	首页控制器
 +------------------------------------------------------------------------------
 * @others:			NULL
 * @file:			IndexAction.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 +------------------------------------------------------------------------------
 */
class TestAction extends AppBaseAction
{

//	private


	protected function _initialize()
	{
		parent::_initialize();
		//$this->_tplPrefix = GROUP_NAME . ':' . MODULE_NAME . C('TMPL_FILE_DEPR');
		$this->assign('meta_title', '欢聚APP');

	}

	/**
     +----------------------------------------------------------
     * 显示首页内容
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function index()
	{

//		var_dump($_SESSION);
	}
	public function appcenter()
	{
		//最热
		$list = $this->getHotList(3, "app_downloads DESC", 20, true);

		if ($list !== false)
			$this->assign('hotlist', $list);
		$this->displayRecommended();
		$this->displayTestList();
		$this->display();
	}
	public function displayRecommended()
	{
		//import("ORG.Util.Page");
		$category = 1;
		$order = "app_downloads DESC";
		$appCategoryModel = D('AppCategory');
		$appidList = $appCategoryModel->getAppIdsByCids($category);
		if ($appList !== false)
		{
			if (is_array($appidList) && !empty($appidList))
			{
				$appidList = implode(',', $appidList);
			}
			else
			{
				$appidList = (int) $appidList;
			}
			$perPage = 12;
			$appInfoModel = D('Appinfo');
			$count = $appInfoModel->where("`app_id` in ( {$appidList} ) and `app_visible` = '1'")->count();
			$pageCount = min(ceil($count/12),4);
			$list = array();
			for($p = 1;$p<=$pageCount;$p++)
			{
				$list[] = $appInfoModel->field('app_name,author_name,app_id,app_downloads,app_score_www,app_starcount')
					->relation('AppDeviceType')
					->where("`app_id` in ( {$appidList} ) and `app_visible` = '1'")
					->order("{$order}")
					->page($p . ','.$perPage)
					->findAll();
			}
			//var_dump($list);die();
			if (!empty($list))
			{
				$this->assign('recommenlist', $list);


			}
		}
		return false;
	}
	public function displayTestList()
	{
		$testModel = D("DeDeAddonarticle");
		$db_name = '`huanjubao`';
		$table_name = $db_name.'.`dede_addonarticle`';
		$join_table_name = $db_name.'.`dede_archives`';
		$db_name2 = '`_v2_android`';
		$appinfo = $db_name2.'.`' . C('DB_PREFIX') . 'appinfo`';
		$app_device_type = $db_name2.'.`' .C('DB_PREFIX') . 'app_device_type`';
		$count = count($testModel->field("{$table_name}.`app_id`")
				->join("{$join_table_name} ON {$table_name}.`aid` = {$join_table_name}.`id`")
				->join("{$appinfo} ON {$table_name}.`app_id` = {$appinfo}.`app_id`")
				->join("{$app_device_type} ON {$table_name}.`app_id` = {$app_device_type}.`app_id`")
				->where("{$join_table_name}.`ismake` = 1 and {$join_table_name}.`arcrank` = 0 and {$appinfo}.`app_visible` = 1")
				->group("{$table_name}.`app_id`")//1个应用可能多个test
				->findAll());
		$pageCount = min(ceil($count/12),4);
		$list = array();
		for($p = 1;$p<=$pageCount;$p++)
		{
			$list[] = $testModel->field("{$table_name}.`app_id`,{$appinfo}.`app_name`,{$appinfo}.`author_name`,{$appinfo}.`app_score_www`,{$appinfo}.`app_starcount`,{$appinfo}.`app_downloads`,{$app_device_type}.`icon`,{$app_device_type}.`apk_path`")
				->join("{$join_table_name} ON {$table_name}.`aid` = {$join_table_name}.`id`")
				->join("{$appinfo} ON {$table_name}.`app_id` = {$appinfo}.`app_id`")
				->join("{$app_device_type} ON {$table_name}.`app_id` = {$app_device_type}.`app_id`")
				->where("{$join_table_name}.`ismake` = 1 and {$join_table_name}.`arcrank` = 0 and {$appinfo}.`app_visible` = 1")
				->order("{$join_table_name}.`pubdate` desc")
				->page($p.','.$perPage)
				->group("{$table_name}.`app_id`")//1个应用可能多个test
				->findAll();
		}
		//var_dump($list);die();
		if (!empty($list))
		{
			$this->assign('testlist', $list);
		}
		return false;
	}
	/**	 * ***********************************************
	 * Function:       getHotList
	 * Description:    获取热门 最新 推荐等列表
	 * Input:          category=2/3 ,order
	 * Return:
	 * Others:
	 * Date：         2011-12-07
	 * Author：       xiaoguang
	 * *********************************************** */
	public function getHotList($category, $order, $limit=5, $bNeedImage=false)
	{
		$appCategoryModel = D('AppCategory');
		$appidList = $appCategoryModel->getAppIdsByCids($category);

		if ($appidList !== false)
		{
			if (is_array($appidList) && !empty($appidList))
			{
				$appidList = implode(',', $appidList);
			}
			else
			{
				$appidList = (int) $appidList;
			}
			$appInfoModel = D('Appinfo');
			//图片另加
			$list = $appInfoModel->field('app_name,author_name,app_id,app_downloads,app_score_www,app_starcount')->where("`app_id` in ( {$appidList} ) and `app_visible` = '1'")->order("{$order}")->relation($bNeedImage)->limit("{$limit}")->findAll();
			if (!empty($list))
				return $list;
			//$this->assign('list',$list);
			//$this->assign('viewcontent', MODULE_NAME . ':index');
		}
		//$this->trace('主页列表打开失败', dump($this->_msg, false));
		return false;
	}
	/**
	  +----------------------------------------------------------
	 * 探针模式
	  +----------------------------------------------------------
	 */
	public function xuhTest()
	{
		var_dump($_SERVER['SCRIPT_NAME']);
		die(var_dump($_SERVER['REQUEST_URI']));

	}
}

?>