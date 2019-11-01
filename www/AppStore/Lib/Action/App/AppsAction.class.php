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
class AppsAction extends AppBaseAction
{
	private $_appCenterBar = 'Layout:appCenterBar';
	private $_tmpAppCateTree = array();
	protected function _initialize()
	{
		parent::_initialize();
		$cateObj = D('Category');
		$this->_tmpAppCateTree = $cateObj->getCateTree();
		$this->assign('appCateTree', $this->_tmpAppCateTree);
		$this->assign('appCenterBar',$this->_appCenterBar);
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
		//最热
		$list = $this->getHotList(3, "app_downloads DESC", 20, true);

		if ($list !== false)
			$this->assign('hotlist', $list);
		$this->displayRecommended();
		$this->displayTestList();
		$this->assign('viewcontent', 'index');
		$this->display($this->_layout);
	}
	public function appcenter()
	{
		//最热
		$list = $this->getHotList(3, "app_downloads DESC", 20, true);

		if ($list !== false)
			$this->assign('hotlist', $list);
		$this->displayRecommended();
		$this->displayTestList();
		$this->assign('viewcontent', 'appcenter');
		$this->display($this->_layout);
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
	public function getHotList($category, $order, $limit=5, $bNeedImage=false,$exclude=0)
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
			if($exclude>0)
				$whereExclude = " and `app_id`<>'".$exclude."'";

			$list = $appInfoModel->field('app_name,author_name,app_id,app_downloads,app_score_www,app_starcount')->where("`app_id` in ( {$appidList} ) and `app_visible` = '1'".$whereExclude)->order("{$order}")->relation($bNeedImage?'AppDeviceType':false)->limit("{$limit}")->findAll();
			if (!empty($list))
				return $list;
		}
		return false;
	}
	/**
	 +----------------------------------------------------------
	 * 根据不同的条件获取不同的应用列表
	 +----------------------------------------------------------
	 * @access public
	 +----------------------------------------------------------
	 * @param string $type
	 * @param array $appCateTree
	 +----------------------------------------------------------
	 * @return void
	 +----------------------------------------------------------
	 * @date:			2011-12-8
	 +----------------------------------------------------------
	 * @author:		xuhao
	 +----------------------------------------------------------
	 */
	protected function getAppListByCondition($appCateTree = array(), $orderType='default', $listRow = 20, $isAjax = false)
	{
		if($orderType == 'default') $order = '';
		if($orderType == 'update') $order = 'app_update_date DESC';
		if($orderType == 'download') $order = 'app_downloads DESC';
		$bc_type = 0;//面包屑导航类型
		if($this->_cid == 3)
		{
			//$order = 'app_update_date DESC';
			$bc_type = '最新更新';
		}
		if($this->_cid == 4)$bc_type = '必备应用';
		if($this->_cid == 2)
		{
			$order = 'app_downloads DESC';
			$bc_type = '热门应用';
		}
		$subCateArr = array(); //声明当前分类的子分类数组
		//遍历分类树，如果此分类有子类，则生成子类数组
		foreach ($appCateTree as $k => $v)
		{
			if ($this->_cid == $v['id'] && !empty($v['_child']))
			{
				foreach ($v['_child'] as $key => $val)
				{
					$subCateArr[] = $val['id'];
				}
			}
		}
		$appCateObj = D('AppCategory');
		//如果子类数组有值，则根据子类数组查询数据库，找到相关应用id
		//如果没有则按当前分类查询数据库，找到相关应用的id
		if (is_array($subCateArr) && !empty($subCateArr) && $this->_cid != 1)
			$subIds = $appCateObj->getAppIdsByCids($subCateArr);
		else
			$subIds = $appCateObj->getAppIdsByCids($this->_cid);

		//		$appObj = new AppinfoModel();
		//		//如果子类数组有值，则根据子类数组查询数据库，找到相关应用id
		//		//如果没有则按当前分类查询数据库，找到相关应用的id
		//		if(is_array($subCateArr) && !empty($subCateArr))
			//			$subIds = $appObj->getAppIdsByCids($subCateArr);
			//		else
				//			$subIds = $appObj->getAppIdsByCids($this->_cid);
		$subIds = implode(',', $subIds);
		$p = isset($_GET['p']) && $_GET['p'] > 0 ? (int) $_GET['p'] : 1;
		$appObj = D('Appinfo');
		$list = $appObj->relation('AppDeviceType')->where("`app_id` in ( {$subIds} ) and `app_visible` = '1'")->order($order)->page($p . ',' . $listRow)->select();
		import("ORG.Util.Page");
		$count = $appObj->relation('AppDeviceType')->where("`app_id` in ( {$subIds} ) and `app_visible` = '1'")->count();
		$Page = new Page($count, $listRow);
		if($isAjax)$Page->setAjax($p);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');
		//$Page->setConfig('theme', '%totalRow% %header% %nowPage%/%totalPage% 页 %upPage% %first% %linkPage% %end% %downPage%');
		if($p > $Page->totalPages)$this->error('对不起，没有内容了！');
		$show = $Page->show();
		$this->assign('bc_type', $bc_type);
		$this->assign('contentTitle', ( empty($bc_type) ) ? '热门免费' : $bc_type);
		$this->assign('sub_title', ( empty($bc_type) ) ? ' - 热门免费' : $bc_type);
		$this->assign('list', $list);
		$this->assign('page', $show);
	}
	/**
     +----------------------------------------------------------
     * 应用分类，列表页面
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
	public function appCenterList()
	{
		//Debug::mark('runrunrun');
		$listRowArr = C('APP_LIST_ROW');
		$orderTypeArr = C('APP_ORDER_TYPE');
//		$cateObj = D('Category');
//
//		$appCateTree = $cateObj->getCateTree();
//		$typeArr = array('latest', 'necessary', 'recommend', 'hot');
//		$type = Filter::act(Req::get('type', 'get'));
//		//$cid
//		$type = in_array($type, $typeArr) ? $type : 'default';
		$this->_cid = (int)Req::get('cid', 'get');
		$orderType = Filter::act(Req::get('order', 'get'));
		$listRow = (int)Req::get('listRow', 'get');
		$orderType = array_key_exists($orderType, $orderTypeArr) ? $orderType : 'default';
		$listRow = in_array($listRow, $listRowArr) ? $listRow : 14;
		$params = array();
		if(isset($_GET['order']))$params['order'] = $orderType;//列表页排序方式
		if(isset($_GET['listRow']))$params['listRow'] = $listRow;//列表页显示数量
		if(isset($_GET['type']))$params['type'] = $type;//用户中心页左侧栏链接方式
		if(isset($_GET['cid']) && (int)$_GET['cid'] >= 1)$params['cid'] = $this->_cid;//分类id
		//if(isset($_GET['p']) && (int)$_GET['p'] >= 1)$params['p'] = $_GET['p'];//页码

		$this->getAppListByCondition($this->_tmpAppCateTree, $orderType, $listRow, true);
		$this->assign('params', $params);
		$this->assign('orderTypeArr', $orderTypeArr);
		$this->assign('listRowArr', $listRowArr);

		$list = $this->getHotList(3, "app_downloads DESC", 20, true);
		if ($list !== false)
			$this->assign('hotlist', $list);

		$this->assign('cid', $this->_cid);
		$this->assign('viewcontent', 'appCenterList');
		$this->display($this->_layout);
	}
	public function detail()
	{
		//应用id不合法
		if ($this->_aid <= 0)
		{
			$this->error('没有找到相关应用的信息！');
		}
		$appInfoObj = D('Appinfo');
		//获取应用信息
		$appInfoRow = $appInfoObj->getAppInfoById($this->_aid);
		if (empty($appInfoRow))
		{
			$this->error('没有找到相关应用的信息！');
		}
		$appCommentObj = D('Comment');
		$downloadUrl = U(APP_NAME . '://' . GROUP_NAME . '-' . MODULE_NAME . '/appDownload?aid=' . $this->_aid);

		$this->assign('downloadUrl', $downloadUrl);
		//$this->assign('qr', $qr);
		$userCommentDone = false;
		//var_dump($_SERVER);die;
		if ($this->_uid > 0)
		{
			$userComment = $appCommentObj->getAppCommentByUidAndAid($this->_uid, $this->_aid, 'id, score_www');
			if (!empty($userComment))
			{
				$userCommentDone = true;
				$this->assign('userScore', $userComment['score_www']);
			}
		}

		//获取应用相关评论信息
		$appCommentRow = $appCommentObj->getAppCommentsByAid($this->_aid, true);

		$appScoreRow = $appCommentObj->getAppScoreDetailByAid($this->_aid);
//		die(var_dump($appCommentRow));
		//xiaoguang
		//相关推荐,当前类别下的 又属于推荐的
		$list = $this->getHotList($this->_cid, "app_downloads DESC", 7,true,$this->_aid);
		//var_dump($list);die();
		if ($list !== false)
		{
			$this->assign('recommenlist', $list);
		}
		//热门
		$list = $this->getHotList(2, "app_downloads DESC", 7);
		if ($list !== false)
			$this->assign('hotlist', $list);
		//总下载排行
		$this->displayDownloadList();

		$this->assign('userCommentDone', $userCommentDone);
		$this->assign('approw', $appInfoRow);
		$this->assign('app_title', ' - ' . $appInfoRow['app_name']);
		$this->assign('meta_description', $appInfoRow['app_slogan']);
		$this->assign('appComment', (is_array($appCommentRow) && !empty($appCommentRow)) ? $appCommentRow : NULL);
		$this->assign('appScore', (is_array($appScoreRow) && !empty($appScoreRow)) ? $appScoreRow : NULL);

		$this->assign('viewcontent', 'detail');
		$this->display($this->_layout);
	}
	    /** ************************************************
     * Function:       displayDownloadList();
     * Description:    显示总下载列表，周，月，总
     * Input:
     * Return:
     * Others:
     * Date：         2011-12-16
     * Author：       xiaoguang
     * *********************************************** */
      public function displayDownloadList()
    {
	$appInfoModel= D('Appinfo');
	//图片另加
	$list = $appInfoModel->field('app_name,author_name,app_id,app_downloads,app_score_www,app_starcount')->relation('AppDeviceType')->where( "`app_visible` = '1'")->order("app_downloads DESC")->limit("8")->findAll();
	if(!empty($list))
	    $this->assign('downloadlist',$list);
    }
}

?>