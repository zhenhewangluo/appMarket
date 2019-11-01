<?php
class SearchAction extends AppBaseAction
{
	private $_appCenterBar = 'Layout:appCenterBar';
	protected function _initialize()
	{
		parent::_initialize();
		$cateObj = D('Category');
		$appCateTree = $cateObj->getCateTree();
		$this->assign('appCateTree', $appCateTree);
		$this->assign('appCenterBar',$this->_appCenterBar);
		//$this->_tplPrefix = GROUP_NAME . ':' . MODULE_NAME . C('TMPL_FILE_DEPR');
		$this->assign('meta_title', '欢聚APP');
		$this->assign('sub_title', ' - 搜索结果');
		$this->assign('page_title', '');
	}
	public function index()
	{
		$listRowArr = C('APP_LIST_ROW');
		$orderTypeArr = C('APP_ORDER_TYPE');

		$orderType = Filter::act(Req::get('order', 'get'));
		$listRow = (int)Req::get('listRow', 'get');
		$orderType = array_key_exists($orderType, $orderTypeArr) ? $orderType : 'default';
		$listRow = in_array($listRow, $listRowArr) ? $listRow : 10;
		$showKeyWords = urldecode($_GET['key_word']);
		$keyWords = explode(' ', trim($showKeyWords));
		$keyWords = Filter::act($keyWords, 'text');

		$appObj = new AppinfoModel;
		$appRow = $appObj->getAppsByKeyWords($keyWords, $relation = 'AppDeviceType', $orderType, $listRow);
		if(!empty($appRow) && array_key_exists('appList', $appRow))
		{
			$_SESSION['downloadRandom'] = md5(time() . rand(100000, 999999));
			$this->assign('downloadRandom', $_SESSION['downloadRandom']);
			$this->assign('list', $appRow['appList']);
			$this->assign('page', $appRow['pageShow']);
		}
		$this->assign('keyWords', $keyWords);
		$this->assign('orderTypeArr', $orderTypeArr);
		$this->assign('listRowArr', $listRowArr);
		$this->assign('viewcontent', $this->_tplPrefix . 'index');
		$this->display($this->_layout);
	}
}
?>
