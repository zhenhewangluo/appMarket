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
class IndexAction extends AppBaseAction
{

	private $actTypeTextConf = array(
		"SPONSORED_APP_DATE"	=> array('order' => 1, 'desc' => '特别推荐'),
		"TEST_REPORT_DATE"		=> array('order' => 2, 'desc' => '最新测试'),
		"STAFF_CHOICE_DATE"		=> array('order' => 3, 'desc' => '我们的推荐'),
		"MARKET_TOP_APP_POINTS" => array('order' => 4, 'desc' => '热门下载'),
		"RATING_WEIGHTS"		=> array('order' => 5, 'desc' => '最受好评'),
		"NUM_MARKET_RATINGS"	=> array('order' => 6, 'desc' => '最多评分'),
		"NEWCOMER_RATING"		=> array('order' => 7, 'desc' => '最新收录')
	);
	private $dateTypeTextConf = array(
		'TODAY'			=> array('calculate' => 1, 'desc' => '今天'),
		'YESTERDAY'		=> array('calculate' => 2, 'desc' => '昨天到现在'),
		'LAST_7_DAYS'	=> array('calculate' => 7, 'desc' => '过去7天'),
		'LAST_30_DAYS'	=> array('calculate' => 30, 'desc' => '过去30天'),
		'LAST_60_DAYS'	=> array('calculate' => 60, 'desc' => '过去60天')
	);
	private $downloadTypeTextConf = array(
		1000,
		5000,
		10000,
		50000,
		100000,
		500000,
		1000000,
		5000000,
		10000000,
		50000000,
		100000000
	);
	private $ajaxParams = array(
			'cate'		=> array(),
			'time'		=> '',
			'rate'		=> 0,
			'download'	=> array('from'	=> -1, 'to' => -1),
			'perpage'	=> 8
	);

	private $maxPerPage = array(5, 10, 25, 50);

	protected function _initialize()
	{
		parent::_initialize();
		$this->ajaxParams['time'] = date("Y-m-d 00:00:00", time() - 3600 * 24 * 365);
		$this->assign('meta_title', '欢聚APP');
		$siteConf = include(APP_PATH . '/Data/site_config.php');
		$this->assign('linksList', $siteConf['links']);
		$this->assign('slideList', unserialize($siteConf['slides']));
	}
	//初始化session数据
	private function initialSessionData()
	{
		if(Session::is_set('app_filter'))
		{
			if(!empty($_SESSION['app_filter'][0]))
				$this->ajaxParams['cate'] = $_SESSION['app_filter'][0];//explode(',', trim($_SESSION['app_filter'][0], ','));

			$this->ajaxParams['rate'] = $_SESSION['app_filter'][1] > 0 ? $_SESSION['app_filter'][1] : 0;
			if(!empty($_SESSION['app_filter'][2]))
			{
				$checkedDate = $_SESSION['app_filter'][2];
				if($_SESSION['app_filter'][2] != 'LAST_365_DAYS')
					$this->ajaxParams['time'] = date("Y-m-d 00:00:00", time() - 3600 * 24 * $this->dateTypeTextConf[$_SESSION['app_filter'][2]]['calculate']);
			}
			if(!empty($_SESSION['app_filter'][3]))
				$this->ajaxParams['download'] = $_SESSION['app_filter'][3];
			$this->assign('checkedCate', $this->ajaxParams['cate']);
			$this->assign('checkedRate', $this->ajaxParams['rate']);
			$this->assign('checkedDate', $checkedDate);
			$this->assign('checkedDownload', $this->ajaxParams['download']);
		}
	}
	/**
	 * 显示首页内容
	 * @method	_initialize
	 * @access	public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2011-12-8
	 */
	public function index()
	{
		$cateObj = D('Category');

		$listRowArr = C('APP_LIST_ROW');
		$orderTypeArr = C('APP_ORDER_TYPE');
		$orderType = Filter::act(Req::get('order', 'get'));
		$listRow = (int)Req::get('listRow', 'get');
		$orderType = in_array($orderType, $orderTypeArr) ? $orderType : 'default';
		$listRow = in_array($listRow, $listRowArr) ? $listRow : 20;


		$actListArr = array();//存放页面展示app列表的数组
		$appTypeText = array();//存放页面展示app列表类型的数组
		$unassignArr = array();//session中不包含的app类表类型的数组

		$this->initialSessionData();

		if(Session::is_set('app_order'))
		{
			//取在页面顶端默认不动的特别推荐的数据。
			$appTypeText['SPONSORED_APP_DATE'] = array('order' => 1, 'desc' => '特别推荐');
			$tmp = $this->get3AppList(1, '', 0, "", '', $this->ajaxParams['perpage'], 0);
//			$actListArr[1]['prev'] = $this->getAppList(1, '', 0, date("Y-m-d 00:00:00", time() - 3600 * 24 * 365), '', $this->ajaxParams['perpage'], -1);
//			$actListArr[1]['active'] = $this->getAppList(1, '', 0, date("Y-m-d 00:00:00", time() - 3600 * 24 * 365), '', $this->ajaxParams['perpage'], 0);
//			$actListArr[1]['next'] = $this->getAppList(1, '', 0, date("Y-m-d 00:00:00", time() - 3600 * 24 * 365), '', $this->ajaxParams['perpage'], 1);
			$actListArr[1]['prev'] = $tmp[0];
			$actListArr[1]['active'] = $tmp[1];
			$actListArr[1]['next'] = $tmp[2];

			unset($this->actTypeTextConf['SPONSORED_APP_DATE']);
			$tmpActArr = explode(',', Session::get('app_order'));
			if(!empty($tmpActArr))
			{
				foreach($tmpActArr as $k => $v)
				{
					foreach($this->actTypeTextConf as $key => $val)
					{
						if($v == $key)
						{
							$appTypeText[$key] = $val;
							$tmp = $this->get3AppList($val['order'], $this->ajaxParams['cate'], $this->ajaxParams['rate'], $this->ajaxParams['time'], $this->ajaxParams['download'], $this->ajaxParams['perpage'], 0);
							$actListArr[$val['order']]['prev'] = $tmp[0];
							$actListArr[$val['order']]['active'] = $tmp[1];
							$actListArr[$val['order']]['next'] = $tmp[2];
//							$actListArr[$val['order']]['prev'] = $this->getAppList($val['order'], $this->ajaxParams['cate'], $this->ajaxParams['rate'], $this->ajaxParams['time'], $this->ajaxParams['download'], $this->ajaxParams['perpage'], -1);
//							$actListArr[$val['order']]['active'] = $this->getAppList($val['order'], $this->ajaxParams['cate'], $this->ajaxParams['rate'], $this->ajaxParams['time'], $this->ajaxParams['download'], $this->ajaxParams['perpage'], 0);
//							$actListArr[$val['order']]['next'] = $this->getAppList($val['order'], $this->ajaxParams['cate'], $this->ajaxParams['rate'], $this->ajaxParams['time'], $this->ajaxParams['download'], $this->ajaxParams['perpage'], 1);
							unset($this->actTypeTextConf[$key]);
						}
					}
				}
				$unassignArr = $this->actTypeTextConf;
			}
		}
		else
		{

				$actCount = count($this->actTypeTextConf);
				$appTypeText = $this->actTypeTextConf;

				for($i = 0; $i < $actCount; $i++)
				{
					$tmp = $this->get3AppList($i + 1, $this->ajaxParams['cate'], $this->ajaxParams['rate'], $this->ajaxParams['time'], $this->ajaxParams['download'], $this->ajaxParams['perpage'], 0);
					$actListArr[$i + 1]['prev'] = $tmp[0];
					$actListArr[$i + 1]['active'] = $tmp[1];
					$actListArr[$i + 1]['next'] = $tmp[2];
				}


		}
		$this->assign('unassignArr', $unassignArr);
		$this->assign('appTypeText', $appTypeText);
		$this->assign('actListArr', $actListArr);
		$this->assign('dateArr', $this->dateTypeTextConf);
		$this->assign('downloadArr', $this->downloadTypeTextConf);

		$appCateTree = $cateObj->getCateTree();
		$this->assign('appCateTree', $appCateTree);
		$this->assign('orderTypeArr', $orderTypeArr);
		$this->assign('listRowArr', $listRowArr);
		$this->assign('viewcontent', $this->_tplPrefix . 'index');
		$this->display($this->_layout);
//		var_dump($_SESSION['app_order']);
	}

	public function appList()
	{

		$maxPerPage = (int)Filter::act(Req::get('max'));
		if(!in_array($maxPerPage, $this->maxPerPage))
		{
			$maxPerPage = 10;
		}
		import("ORG.Util.Page");
		$cateObj = D('Category');

		$listRowArr = C('APP_LIST_ROW');
		$orderTypeArr = C('APP_ORDER_TYPE');
		$orderType = Filter::act(Req::get('order', 'get'));
		$listRow = (int)Req::get('listRow', 'get');
		$orderType = in_array($orderType, $orderTypeArr) ? $orderType : 'default';
		$listRow = in_array($listRow, $listRowArr) ? $listRow : 20;

		$rid = Filter::act(Req::get('rid', 'get'));
		if(!array_key_exists($rid,$this->actTypeTextConf))
			$rid = 	'SPONSORED_APP_DATE';
		$actType = $this->actTypeTextConf[$rid]['order'];
		$actName = $this->actTypeTextConf[$rid]['desc'];
		$actListArr = array();//存放页面展示app列表的数组
		$appTypeText = array();//存放页面展示app列表类型的数组
		$unassignArr = array();//session中不包含的app类表类型的数组
		$this->ajaxParams['perpage'] = $maxPerPage>0?$maxPerPage:25;
		$this->initialSessionData();

		$p = isset($_GET['p']) && $_GET['p'] > 0 ? (int) $_GET['p'] : 1;
		$p = ($p-1)*$this->ajaxParams['perpage'];

		$actCount = count($this->actTypeTextConf);
		$appTypeText = $this->actTypeTextConf[$rid]['desc'];
		$actListArr['active'] = $this->getAppList($actType, $this->ajaxParams['cate'], $this->ajaxParams['rate'], $this->ajaxParams['time'], $this->ajaxParams['download'], $this->ajaxParams['perpage'], $p,true);

			$Page = new Page($actListArr['active']['total'], $this->ajaxParams['perpage']);
			$Page->setAjax($p/$perPage+1);
			$Page->isSimple = true;
			$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
			$Page->setConfig('prev', '');
			$Page->setConfig('next', '');
		$this->assign('page', $Page->show());
//var_dump($actListArr);die();
		$this->assign('unassignArr', $unassignArr);
		$this->assign('appTypeText', $appTypeText);
		$this->assign('actListArr', $actListArr);
		$this->assign('dateArr', $this->dateTypeTextConf);
		$this->assign('downloadArr', $this->downloadTypeTextConf);
		$this->assign('actName', $actName);
		$this->assign('rid', $rid);
		$cateObj = D('Category');
		$appCateTree = $cateObj->getCateTree();
		$this->assign('appCateTree', $appCateTree);
		$this->assign('orderTypeArr', $orderTypeArr);
		$this->assign('listRowArr', $listRowArr);
		$this->assign('maxPerPage', $maxPerPage);
		$this->assign('viewcontent', $this->_tplPrefix . 'appList');
		$this->display($this->_layout);
	}

}

?>