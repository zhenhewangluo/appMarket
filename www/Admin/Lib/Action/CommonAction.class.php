<?php
class CommonAction extends BaseAction
{
	private $_layout = 'Layout:layout';
	protected function _initialize()
	{
		parent::_initialize();
	}
	public function searchApp()
	{
		$searchType = Filter::act(Req::get('type'));//app_name cate_id app_id
		$searchVal = Filter::act(Req::get('sval'));
		$is_visible = Filter::act(Req::get('is_visible'));
		$where = '';
		$cateObj = D('Category');
		$appCateObj = D('AppCategory');

		switch($searchType)
		{
			case 'appName' :
				$where = " and app_name like '%{$searchVal}%' ";
			break;
			case 'appCate' :
				$cateRow = $cateObj->getCidByCateName($searchVal);
				$aids = $appCateObj->getAppIdsByCids($cateRow['id']);
				$aids = implode(',', $aids);
				$where = " and app_id in ( {$aids} ) ";
				break;
			case 'appId' :
				$where = " and app_id = '{$searchVal}'";
			break;
			default :
				break;
		}
//		die(var_dump($cateRow));
		$this->assign('searchType', $searchType);
		$this->assign('searchVal', $searchVal);
		$order= "app_update_date DESC";
		$p = isset($_GET['p']) && (int)$_GET['p'] > 0 ? $_GET['p'] : 1;
		$appInfoModel= D('Appinfo');
		$list = $appInfoModel->order("{$order}")->relation('AppCategory')
				->field('app_id,app_name,author_name,app_version,app_update_date,app_downloads,app_score_www,app_starcount')
				->page($p.',20')
				->where("app_visible = '{$is_visible}' {$where}")
				->select();
		$count = $appInfoModel->where("app_visible = '{$is_visible}' {$where}")->count();
		$typeList = $cateObj->where("parent_id<=1")->findAll();
		foreach($typeList as $k =>$v)
		{
			$typeArr[] = $v['id'];
		}
		$typeArr[] = '';//非子分类数组
		if(!empty($list))
		{
			foreach($list as $k=>&$v)
			{
				$v['app_score_average'] =  number_format($list['app_score_www']/($list['app_starcount']+1e-5),2);
				$v['app_isrecommon'] = '否';
				$v['app_cate_name'] ='暂无分类';
				if(!empty($v['AppCategory']))
				{
					foreach($v['AppCategory'] as $vcate)
					{
						if($vcate['cate_id']==1)
						{
							$v['app_isrecommon'] = '是';
							break;
						}
					}
					if(!in_array($v['AppCategory'][0]['cate_id'], $typeArr))
					{
						$v['app_cate_name'] = $v['AppCategory'][0]['cate_id'];
						$cateList[] = $v['app_cate_name'];
					}
				}
			}
			//取分类名字
			$cateWhere = implode(',',$cateList);

			$catelistResult = $cateObj->field('id,name')->where("`id` in ( {$cateWhere} )")->findALL();

			if(!empty($catelistResult))
				foreach($list as &$v1)
				{
					foreach($catelistResult as $v2)
						if($v1['app_cate_name']==$v2['id'])
						{
							$v1['app_cate_name'] = $v2['name'];
							break;
						}
				}

			$Page = new Page($count,20);
			$show = $Page->show();
			$this->assign('applist',$list);
			$this->assign('page',$show);
		}
        ($is_visible == 1) ? $this->assign('viewcontent', 'Apps:appList') : $this->assign('viewcontent', 'Apps:appRecycleList');
        $this->display($this->_layout);
	}
}
?>
