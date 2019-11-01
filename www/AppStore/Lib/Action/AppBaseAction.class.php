<?php

class AppBaseAction extends BaseAction
{
	protected	$_cid = 0; //设定页面访问分类id（全局）
	protected	$_tid = 0; //设定页面访问专题id（全局）
	protected	$_aid = 0; //设定页面访问应用id（全局）
	protected	$_catebar = 'Layout:catebar';
	protected	$_topicbar = 'Layout:topicbar';

	protected	$ACT_TYPE = array(
		"Feature" =>	 1,
		"LatestTest" =>  2,
		"Recommend" =>	 3,
		"HotDownload" => 4,
		"TopRank" =>	 5,
		"MostRank" =>	 6,
		"Newcomer" =>	 7,
	);
	protected function _initialize()
	{
		parent::_initialize();
		$this->_aid = (int)Filter::act(Req::get('aid'));
		$this->_cid = (int) Req::get('cid', 'get') > 0 ? (int) Req::get('cid', 'get') : 6;
		
		//根据应用id查找所属分类信息，用于面包屑导航
		if ($this->_aid > 0)
		{
			$appCateObj = D('AppCategory');
			$this->_cid = $appCateObj->getAppCidByAid($this->_aid);
		}
		
		$cateObj = D('Category');
		
		$cateInfo = $cateObj->getCateInfoByCid($this->_cid, 'name, description');
		if(!empty($cateInfo))
		{
			$this->assign('meta_description', $cateInfo['description']);
		}
		$breadCrumbs = getCateArr($this->_cid);
		$page_title = '';
		foreach($breadCrumbs as $k => $v)
		{
			$page_title .= ' - ' . $v['name'];
		}
		$this->assign('cateName', $cateInfo['name']);
		if(MODULE_NAME != 'Index')$this->assign('page_title', $page_title);
		$this->assign('breadcrumbs', $breadCrumbs);
	}

	/**
	 * 获取特色应用列表
	 * @method	getFeatureList()
	 * @access protected
	 * @param	perpage	每页应用数
	 * @param	p		页...-3,-2,-1,0,1,2,3...
	 * @return total:应用总数,list:应用列表
	 * @author	zxg
	 * @date	20120514
	 */
	protected function getFeatureList($perPage,$p,$page=false)
	{
		$daily_rec = D("RecDailyDay");
		$db_name = '`_v2_android`';
		$table_name = $db_name.'.`' . C('DB_PREFIX') . 'rec_daily_day`';
		$join_table_name = $db_name.'.`' . C('DB_PREFIX') . 'appinfo`';
		$join_table_name2 = $db_name.'.`' . C('DB_PREFIX') . 'app_device_type`';
		$ids = $daily_rec->field("{$join_table_name}.`app_id`")
				->join("{$join_table_name} ON {$table_name}.`typeid` = {$join_table_name}.`app_id`")
				->where("{$table_name}.`visible`=1 and {$join_table_name}.`app_visible`=1  and {$table_name}.`edittime`<='".date("Y-m-d 23:59:59")."'")//今天以前的推荐
				->group("{$join_table_name}.`app_id`") //icon需要1对1,否则重复
				->order("{$table_name}.`edittime` desc,{$join_table_name}.`app_id` desc")
				->limit($this->mMaxCount)
				->findAll();
				
		$count = count($ids);
		if($count<=$perPage)
		{
			if($p != 0||$count==0)
				return array("total"=>$count,"list"=>array());
			//$p = 0;
		}
		else
			$p = $p>=0?$p*$perPage%$count:$p*$perPage%$count+$count;
		$tmp = array();
		foreach($ids as $k)
			$tmp[] = $k['app_id'];	
		$tmpWhereArray = array_slice($tmp,$p,$perPage);	
		$limit = 0;
		if($count>$perPage&&count($tmpWhereArray)<$perPage&&$page==false)
		{
			$limit = $perPage - count($tmpWhereArray);
			$tmpWhereArray = array_merge($tmpWhereArray,array_slice($tmp,0,$limit));

		}			
		$where = " {$join_table_name}.`app_id` in (".implode(',',$tmpWhereArray).') ';			
		$dailylist = $daily_rec->field("{$join_table_name}.`app_id`,{$join_table_name}.`app_desc`,{$join_table_name}.`app_score_www`,{$join_table_name}.`app_starcount`,{$join_table_name}.`app_name`, {$join_table_name2}.`apk_path`,{$join_table_name2}.`icon`")
				->join("{$join_table_name} ON {$table_name}.`typeid` = {$join_table_name}.`app_id`")
				->join("{$join_table_name2} ON {$table_name}.`typeid` = {$join_table_name2}.`app_id`")
				->where($where." and {$table_name}.`visible`=1 and {$join_table_name}.`app_visible`=1  and {$table_name}.`edittime`<='".date("Y-m-d 23:59:59")."'")
				->group("{$join_table_name}.`app_id`") //icon需要1对1,否则重复						
				->order("{$table_name}.`edittime` desc,{$join_table_name}.`app_id` desc")//
				//->limit($p.','.$perPage)
				->findAll();				
		if($limit>0&&count($dailylist)==$perPage)
				$dailylist = array_merge(array_slice($dailylist,$limit,$perPage-$limit),array_slice($dailylist,0,$limit));						
//$f= fopen("./logsql.txt","w");
//fwrite($f, $daily_rec->getLastSql());
//fclose($f);					
//		if($count>$perPage&&count($dailylist)<$perPage)
//		{
//				$limit = $perPage - count($dailylist);
//				$dailylist2 = $daily_rec->field("{$join_table_name}.`app_id`,{$join_table_name}.`app_desc`,{$join_table_name}.`app_score_www`,{$join_table_name}.`app_starcount`,{$join_table_name}.`app_name`, {$join_table_name2}.`apk_path`,{$join_table_name2}.`icon`")
//				->join("{$join_table_name} ON {$table_name}.`typeid` = {$join_table_name}.`app_id`")
//				->join("{$join_table_name2} ON {$table_name}.`typeid` = {$join_table_name2}.`app_id`")
//				->where("{$table_name}.`visible`=1 and {$join_table_name}.`app_visible`=1  and {$table_name}.`edittime`<='".date("Y-m-d 23:59:59")."'")//今天以前的推荐
//				->order("{$table_name}.`edittime` desc")
//				->limit('0,'.$limit)
//				->group("{$join_table_name}.`app_id`") //icon需要1对1,否则重复
//				->findAll();
//				$dailylist = array_merge($dailylist,$dailylist2);
//		}
	
		return array("total"=>$count,"list"=>$dailylist);
	}


	/**
	 * 获取最新测试应用列表
	 * @method	getLatestTestList()
	 * @access protected
	 * @param	perpage	每页应用数
	 * @param	p		页...-3,-2,-1,0,1,2,3...
	 * @return total:应用总数,list:应用列表
	 * @author	zxg
	 * @date	20120514
	 */
	protected function getLatestTestList($perPage,$p,$page=false)
	{
		$testModel = D("DeDeAddonarticle");
		$db_name = '`huanjubao`';
		$table_name = $db_name.'.`dede_addonarticle`';
		$join_table_name = $db_name.'.`dede_archives`';
		$db_name2 = '`_v2_android`';
		$appinfo = $db_name2.'.`' . C('DB_PREFIX') . 'appinfo`';
		$app_device_type = $db_name2.'.`' .C('DB_PREFIX') . 'app_device_type`';
		$ids = $testModel->field("{$table_name}.`app_id`")
				->join("{$join_table_name} ON {$table_name}.`aid` = {$join_table_name}.`id`")
				->join("{$appinfo} ON {$table_name}.`app_id` = {$appinfo}.`app_id`")
				->join("{$app_device_type} ON {$table_name}.`app_id` = {$app_device_type}.`app_id`")
				->where("{$join_table_name}.`ismake` = 1 and {$join_table_name}.`arcrank` = 0 and {$appinfo}.`app_visible` = 1")
				->group("{$table_name}.`app_id`")//1个应用可能多个test
				->order("{$join_table_name}.`pubdate` desc,{$table_name}.`app_id` desc")
				->limit($this->mMaxCount)				
				->findAll();
		$count = count($ids);
		if($count<=$perPage)
		{
			if($p != 0||$count==0)
				return array("total"=>$count,"list"=>array());
			//$p = 0;
		}
		else
			$p = $p>=0?$p*$perPage%$count:$p*$perPage%$count+$count;
		$tmp = array();
		foreach($ids as $k)
			$tmp[] = $k['app_id'];	
		$tmpWhereArray = array_slice($tmp,$p,$perPage);	
		$limit = 0;
		if($count>$perPage&&count($tmpWhereArray)<$perPage&&$page==false)
		{
			$limit = $perPage - count($tmpWhereArray);
			$tmpWhereArray = array_merge($tmpWhereArray,array_slice($tmp,0,$limit));

		}			
		$where = " {$table_name}.`app_id` in (".implode(',',$tmpWhereArray).') ';				
		$res = $testModel->field("{$table_name}.`app_id`,{$appinfo}.`app_desc`,{$appinfo}.`app_score_www`,{$appinfo}.`app_starcount`,{$appinfo}.`app_name`, {$app_device_type}.`apk_path`, {$app_device_type}.`icon`")
				->join("{$join_table_name} ON {$table_name}.`aid` = {$join_table_name}.`id`")
				->join("{$appinfo} ON {$table_name}.`app_id` = {$appinfo}.`app_id`")
				->join("{$app_device_type} ON {$table_name}.`app_id` = {$app_device_type}.`app_id`")
				->where($where." and {$join_table_name}.`ismake` = 1 and {$join_table_name}.`arcrank` = 0 and {$appinfo}.`app_visible` = 1")
				->order("{$join_table_name}.`pubdate` desc,{$table_name}.`app_id` desc")
				//->limit($p.','.$perPage)
				->group("{$table_name}.`app_id`")//1个应用可能多个test
				->findAll();
		if($limit>0&&count($res)==$perPage)
				$res = array_merge(array_slice($res,$limit,$perPage-$limit),array_slice($res,0,$limit));						
				
//		if($count>$perPage&&count($res)<$perPage)
//		{
//				$limit = $perPage - count($res);
//				$res2 = $testModel->field("{$table_name}.`app_id`,{$appinfo}.`app_desc`,{$appinfo}.`app_score_www`,{$appinfo}.`app_starcount`,{$appinfo}.`app_name`, {$app_device_type}.`apk_path`, {$app_device_type}.`icon`")
//						->join("{$join_table_name} ON {$table_name}.`aid` = {$join_table_name}.`id`")
//						->join("{$appinfo} ON {$table_name}.`app_id` = {$appinfo}.`app_id`")
//						->join("{$app_device_type} ON {$table_name}.`app_id` = {$app_device_type}.`app_id`")
//						->where("{$join_table_name}.`ismake` = 1 and {$join_table_name}.`arcrank` = 0 and {$appinfo}.`app_visible` = 1")
//						->order("{$join_table_name}.`pubdate` desc")
//						->limit('0,'.$limit)
//						->group("{$table_name}.`app_id`")//1个应用可能多个test
//						->findAll();
//				$res = array_merge($res,$res2);
//		}
		return array("total"=>$count,"list"=>$res);
	}
	/**
	 * 根据类型和组合查询参数获取应用列表(内部调用)
	 * @method	getAppList()
	 * @access protected
	 * @param	$actType
	 *	"Feature" =>	 1,
	 *	"LatestTest" =>  2,
	 *	"Recommend" =>	 3,
	 *	"HotDownload" => 4,
	 *	"TopRank" =>	 5,
	 *	"MostRank" =>	 6,
	 *	"Newcomer" =>	 7,
	 * @param	$cate 分类(数组)
	 * @param	$rate 最低评分
	 * @param	$time	时间范围
	 * @param	$download	下载量范围
	 * @param	perpage	每页应用数
	 * @param	p		页...-3,-2,-1,0,1,2,3...
	 * @return total:应用总数,list:应用列表
	 * @author	zxg
	 * @date	20120514
	 */
	private $mMaxCount = 400;
	protected function getAppList(  $actType,$cate,$rate,$time,$download,$perPage,$p,$page=false)
	{

//$f= fopen("./log.txt","w");
//fwrite($f, "cate:".$cate."++rate".$rate."++time".$time."++download".$download['from']."++".$download['to']."++perPage".$perPage."++p".$p."\n");
//fclose($f);				
		$order = "";
		$result = "";
		if($actType == $this->ACT_TYPE["Feature"])
			$result = $this->getFeatureList($perPage,$p,$page);
		else if($actType == $this->ACT_TYPE["LatestTest"])
			$result = $this->getLatestTestList($perPage,$p,$page);
		else
		{
			$app_model = D("Appinfo");
			$db_name = '`_v2_android`';
			$table_name = $db_name.'.`' . C('DB_PREFIX') . 'appinfo`';
			$join_table_name = $db_name.'.`' . C('DB_PREFIX') . 'app_device_type`';
			$combin_table_name = $db_name.'.`' . C('DB_PREFIX') . 'app_category`';
			$whereCate = "";
			if(!empty($cate))
			{
				$cateArray = explode(',',$cate);
				$cate = '('.$cate.')';
				$parentCate = array();
				foreach($cateArray as $k=> $v)
				{
					if($v <= 6 )
					{
						$parentCate[] = $v;
						unset($cateArray[$k]);
					}
				}
				if(!empty($parentCate))
				{
					$parentWhere = '('.implode(',',$parentCate).')';
					$categoryModel = D("Category");
					$tmpChild = $categoryModel->field("`id`")->where("parent_id in {$parentWhere}")->findAll();
					$tmp = array();
					foreach($tmpChild as $k)
						$tmp[] = $k['id'];
					if(!empty($cateArray))
						$tmp = array_merge($cateArray,$tmp);
					$cate = '('.implode(',',$tmp).')';
				}
				$whereCate = " and {$combin_table_name}.`cate_id` in {$cate} ";
			}
			$whereRate="";
			if($rate>0)
				$whereRate = " and {$table_name}.`app_starcount`>0 and  {$table_name}.`app_score_www`>={$table_name}.`app_starcount`*".$rate;
			$whereTime="";
			if(!empty($time))
				$whereTime = " and {$table_name}.`app_update_date`>'{$time}' ";
			$whereDownload = "";
			if(!empty($download))
			{
				if($download['to']>0)
					$whereDownloadTo = " and {$table_name}.`app_downloads`<={$download['to']} ";
				if($download['from']>0)
					$whereDownloadFrom = " and {$table_name}.`app_downloads`>={$download['from']} ";					
				$whereDownload = $whereDownloadFrom.$whereDownloadTo;
			}
				
			$where = "";
			$order = "";
			switch($actType)
			{
				case $this->ACT_TYPE["Recommend"]:
					$where = $whereCate
							.$whereRate
							.$whereTime
							.$whereDownload
							." and {$table_name}.`app_id` in "
							." (select {$combin_table_name}.`app_id` from $combin_table_name where {$combin_table_name}.`cate_id`=1)";
					$order = " order by {$table_name}.`app_downloads` desc";
					break;
				case $this->ACT_TYPE["HotDownload"]:
					$where = $whereCate
							.$whereRate
							.$whereTime;
					$order = " order by {$table_name}.`app_downloads` desc";
					break;
				case $this->ACT_TYPE["TopRank"]:
					$where = $whereCate
							.$whereTime
							.$whereDownload;	
					$order = " order by ({$table_name}.`app_score_www`/({$table_name}.`app_starcount`+1e-5)) desc";
					break;
				case $this->ACT_TYPE["MostRank"]:
					$where = $whereCate
							.$whereRate
							.$whereTime
							.$whereDownload;
					$order = " order by {$table_name}.`app_starcount` desc";
					break;
				case $this->ACT_TYPE["Newcomer"]:
				default:
					$where = $whereCate
							.$whereRate
							.$whereTime
							.$whereDownload;
					$order = " order by {$table_name}.`app_update_date` desc";
					break;
			}
			$order .= ",{$table_name}.`app_id` desc";
//			$count = $app_model->join("{$combin_table_name} on  {$combin_table_name}.`app_id`={$table_name}.`app_id`")
//						->where("{$table_name}.`app_visible`=1 ".$where)
//						->count();
			$ids = $app_model->query("select {$table_name}.`app_id` from {$table_name}"
						//." left join {$join_table_name} on  {$join_table_name}.`app_id`={$table_name}.`app_id`"
						." left join {$combin_table_name} on  {$combin_table_name}.`app_id`={$table_name}.`app_id`"
						." where {$table_name}.`app_visible`=1 "
						.$where
						." group by {$table_name}.`app_id`" //icon需要1对1,否则重复
						.$order
						." limit ".$this->mMaxCount						
						);
			$count = count($ids);
		
			if($count<=$perPage)
			{
				if($p != 0||$count==0)
					return array("total"=>$count,"list"=>array());
				//$p = 0;
			}
			else //if(!$page)
				$p = $p>=0?$p*$perPage%$count:$p*$perPage%$count+$count;
			$tmp = array();
			foreach($ids as $k)
				$tmp[] = $k['app_id'];		
			$tmpWhereArray = array_slice($tmp,$p,$perPage);			
			if($count>$perPage&&count($tmpWhereArray)<$perPage&&$page==false)
			{
				$limit = $perPage - count($tmpWhereArray);
				$tmpWhereArray = array_merge($tmpWhereArray,array_slice($tmp,0,$limit));
				
			}			
			$where = " where {$table_name}.`app_id` in (".implode(',',$tmpWhereArray).') ';			
			$list = $app_model->query("select {$table_name}.`app_id`,{$table_name}.`app_desc`,{$table_name}.`app_score_www`,{$table_name}.`app_starcount`,{$table_name}.`app_name`, {$join_table_name}.`apk_path`, {$join_table_name}.`icon` from {$table_name}"
						." left join {$join_table_name} on  {$join_table_name}.`app_id`={$table_name}.`app_id`"
						//." left join {$combin_table_name} on  {$combin_table_name}.`app_id`={$table_name}.`app_id`"
						//." where {$table_name}.`app_visible`=1 "
						.$where
						." group by {$table_name}.`app_id`" //icon需要1对1,否则重复
						.$order
						//." limit ".$p.','.$perPage
						);		
			if($limit>0&&count($list)==$perPage)
				$list = array_merge(array_slice($list,$limit,$perPage-$limit),array_slice($list,0,$limit));			
//			if($count>$perPage&&count($list)<$perPage&&$page==false)
//			{
//					$limit = $perPage - count($list);
//					$tmpWhereArray = array_slice($tmp,0,$limit);
//					$where = " where {$table_name}.`app_id` in (".implode(',',$tmpWhereArray).') ';						
//					$list2 = $app_model->query("select {$table_name}.`app_id`,{$table_name}.`app_desc`,{$table_name}.`app_score_www`,{$table_name}.`app_starcount`,{$table_name}.`app_name`, {$join_table_name}.`apk_path`, {$join_table_name}.`icon` from {$table_name}"
//						." left join {$join_table_name} on  {$join_table_name}.`app_id`={$table_name}.`app_id`"
//						//." left join {$combin_table_name} on  {$combin_table_name}.`app_id`={$table_name}.`app_id`"
//						//." where {$table_name}.`app_visible`=1 "
//						.$where
//						." group by {$table_name}.`app_id`" //icon需要1对1,否则重复
//						.$order
//						//." limit ".'0,'.$limit
//						);
//					$list = array_merge($list,$list2);
//			}
//if($actType == $this->ACT_TYPE["Recommend"])
//{
//$f= fopen("./logsql.txt","w");
//fwrite($f, $app_model->getLastSql());
//fclose($f);		
//}
			$result = array("total"=>$count,"list"=>$list);
		}
		return $result;
	}
	/**
	 * 获取特色应用列表
	 * @method	get3FeatureList()
	 * @access protected
	 * @param	perpage	每页应用数
	 * @param	p		页...-3,-2,-1,0,1,2,3...
	 * @return total:应用总数,list:应用列表
	 * @author	zxg
	 * @date	20120514
	 */
	protected function get3FeatureList($perPage,$p)
	{
		if($p!=0)
			return;
		$daily_rec = D("RecDailyDay");
		$db_name = '`_v2_android`';
		$table_name = $db_name.'.`' . C('DB_PREFIX') . 'rec_daily_day`';
		$join_table_name = $db_name.'.`' . C('DB_PREFIX') . 'appinfo`';
		$join_table_name2 = $db_name.'.`' . C('DB_PREFIX') . 'app_device_type`';
		$ids = $daily_rec->field("{$join_table_name}.`app_id`")
				->join("{$join_table_name} ON {$table_name}.`typeid` = {$join_table_name}.`app_id`")
				->where("{$table_name}.`visible`=1 and {$join_table_name}.`app_visible`=1  and {$table_name}.`edittime`<='".date("Y-m-d 23:59:59")."'")//今天以前的推荐
				->group("{$join_table_name}.`app_id`") //icon需要1对1,否则重复
				->order("{$table_name}.`edittime` desc,{$join_table_name}.`app_id` desc")
				->limit($this->mMaxCount)	
				->findAll();
		$result = array();
		$count = count($ids);
		$tmp = array();
		foreach($ids as $k)
			$tmp[] = $k['app_id'];	
		if($count<=$perPage)
		{
			$result[0] =  array("total"=>$count,"list"=>array());
			$result[1] = $count==0?array("total"=>$count,"list"=>array()):array("total"=>$count,
				"list"=>$daily_rec->field("{$join_table_name}.`app_id`,{$join_table_name}.`app_desc`,{$join_table_name}.`app_score_www`,{$join_table_name}.`app_starcount`,{$join_table_name}.`app_name`, {$join_table_name2}.`apk_path`,{$join_table_name2}.`icon`")
				->join("{$join_table_name} ON {$table_name}.`typeid` = {$join_table_name}.`app_id`")
				->join("{$join_table_name2} ON {$table_name}.`typeid` = {$join_table_name2}.`app_id`")
				->where("{$table_name}.`visible`=1 and {$join_table_name}.`app_visible`=1  and {$table_name}.`edittime`<='".date("Y-m-d 23:59:59")."'")//今天以前的推荐
				->order("{$table_name}.`edittime` desc,{$join_table_name}.`app_id` desc")
				//->limit($p.','.$perPage)
				->group("{$join_table_name}.`app_id`") //icon需要1对1,否则重复
				->findAll());
			$result[2] = $result[0];				
			return $result;
		}
		$p1 = $count-$perPage;
		$p2 = 0;
		$p3 = $perPage;

		$tmpWhereArray = array_merge(array_slice($tmp,$p2,$perPage*2),array_slice($tmp,-$perPage));
		$where = " {$join_table_name}.`app_id` in (".implode(',',$tmpWhereArray).') ';	
//		$result[0] =  array("total"=>$count,
//				"list"=> $daily_rec->field("{$join_table_name}.`app_id`,{$join_table_name}.`app_desc`,{$join_table_name}.`app_score_www`,{$join_table_name}.`app_starcount`,{$join_table_name}.`app_name`, {$join_table_name2}.`apk_path`,{$join_table_name2}.`icon`")
//				->join("{$join_table_name} ON {$table_name}.`typeid` = {$join_table_name}.`app_id`")
//				->join("{$join_table_name2} ON {$table_name}.`typeid` = {$join_table_name2}.`app_id`")
//				->where("{$table_name}.`visible`=1 and {$join_table_name}.`app_visible`=1  and {$table_name}.`edittime`<='".date("Y-m-d 23:59:59")."'")//今天以前的推荐
//				->order("{$table_name}.`edittime` desc,{$join_table_name}.`app_id` desc")
//				->limit($p1.','.$perPage)
//				->group("{$join_table_name}.`app_id`") //icon需要1对1,否则重复
//				->findAll());				
		$dailylist = $daily_rec->field("{$join_table_name}.`app_id`,{$join_table_name}.`app_desc`,{$join_table_name}.`app_score_www`,{$join_table_name}.`app_starcount`,{$join_table_name}.`app_name`, {$join_table_name2}.`apk_path`,{$join_table_name2}.`icon`")
				->join("{$join_table_name} ON {$table_name}.`typeid` = {$join_table_name}.`app_id`")
				->join("{$join_table_name2} ON {$table_name}.`typeid` = {$join_table_name2}.`app_id`")
				->where($where." and {$table_name}.`visible`=1 and {$join_table_name}.`app_visible`=1  and {$table_name}.`edittime`<='".date("Y-m-d 23:59:59")."'")//今天以前的推荐
				->order("{$table_name}.`edittime` desc,{$join_table_name}.`app_id` desc")
				//->limit($p2.','.$perPage*2)
				->group("{$join_table_name}.`app_id`") //icon需要1对1,否则重复
				->findAll();		
		$result[0] = array("total"=>$count,
						"list"=>array_slice($dailylist,-$perPage));
		$result[1] =  array("total"=>$count,
				"list"=>array_slice($dailylist,0,$perPage));
		$result[2] =  array("total"=>$count,
				"list"=>array_slice($dailylist,$perPage,$perPage));	
		if(count($result[2]["list"])<$perPage)
		{
			$result[2]["list"] = array_merge($result[2]["list"],array_slice($dailylist,0,$perPage-count($result[2]["list"])));
		}
//$f= fopen("./logsql.txt","w");
//fwrite($f, $daily_rec->getLastSql());
//fclose($f);					

		return $result;
	}


	/**
	 * 获取最新测试应用列表
	 * @method	get3LatestTestList()
	 * @access protected
	 * @param	perpage	每页应用数
	 * @param	p		页...-3,-2,-1,0,1,2,3...
	 * @return total:应用总数,list:应用列表
	 * @author	zxg
	 * @date	20120514
	 */
	protected function get3LatestTestList($perPage,$p)
	{
		if($p!=0)
			return;
		$testModel = D("DeDeAddonarticle");
		$db_name = '`huanjubao`';
		$table_name = $db_name.'.`dede_addonarticle`';
		$join_table_name = $db_name.'.`dede_archives`';
		$db_name2 = '`_v2_android`';
		$appinfo = $db_name2.'.`' . C('DB_PREFIX') . 'appinfo`';
		$app_device_type = $db_name2.'.`' .C('DB_PREFIX') . 'app_device_type`';
		$ids = $testModel->field("{$table_name}.`app_id`")
				->join("{$join_table_name} ON {$table_name}.`aid` = {$join_table_name}.`id`")
				->join("{$appinfo} ON {$table_name}.`app_id` = {$appinfo}.`app_id`")
				->join("{$app_device_type} ON {$table_name}.`app_id` = {$app_device_type}.`app_id`")
				->where("{$join_table_name}.`ismake` = 1 and {$join_table_name}.`arcrank` = 0 and {$appinfo}.`app_visible` = 1")
				->group("{$table_name}.`app_id`")//1个应用可能多个test
				->order("{$join_table_name}.`pubdate` desc,{$table_name}.`app_id` desc")
				->limit($this->mMaxCount)
				->findAll();
		$result = array();
		$count = count($ids);
		$tmp = array();
		foreach($ids as $k)
			$tmp[] = $k['app_id'];			
		if($count<=$perPage)
		{
			$result[0] =  array("total"=>$count,"list"=>array());

			$result[1] = $count==0?array("total"=>$count,"list"=>array()):array("total"=>$count,
				"list"=>$testModel->field("{$table_name}.`app_id`,{$appinfo}.`app_desc`,{$appinfo}.`app_score_www`,{$appinfo}.`app_starcount`,{$appinfo}.`app_name`, {$app_device_type}.`apk_path`, {$app_device_type}.`icon`")
				->join("{$join_table_name} ON {$table_name}.`aid` = {$join_table_name}.`id`")
				->join("{$appinfo} ON {$table_name}.`app_id` = {$appinfo}.`app_id`")
				->join("{$app_device_type} ON {$table_name}.`app_id` = {$app_device_type}.`app_id`")
				->where("{$join_table_name}.`ismake` = 1 and {$join_table_name}.`arcrank` = 0 and {$appinfo}.`app_visible` = 1")
				->order("{$join_table_name}.`pubdate` desc,{$table_name}.`app_id` desc")
				//->limit($p.','.$perPage)
				->group("{$table_name}.`app_id`")//1个应用可能多个test
				->findAll());
			$result[2] = $result[0];				
			return $result;
		}
		$p1 = $count-$perPage;
		$p2 = 0;
		$p3 = $perPage;
		$tmpWhereArray = array_merge(array_slice($tmp,$p2,$perPage*2),array_slice($tmp,-$perPage));
		$where = " {$table_name}.`app_id` in (".implode(',',$tmpWhereArray).') ';	
//		$result[0] =  array("total"=>$count,
//				"list"=> $testModel->field("{$table_name}.`app_id`,{$appinfo}.`app_desc`,{$appinfo}.`app_score_www`,{$appinfo}.`app_starcount`,{$appinfo}.`app_name`, {$app_device_type}.`apk_path`, {$app_device_type}.`icon`")
//				->join("{$join_table_name} ON {$table_name}.`aid` = {$join_table_name}.`id`")
//				->join("{$appinfo} ON {$table_name}.`app_id` = {$appinfo}.`app_id`")
//				->join("{$app_device_type} ON {$table_name}.`app_id` = {$app_device_type}.`app_id`")
//				->where("{$join_table_name}.`ismake` = 1 and {$join_table_name}.`arcrank` = 0 and {$appinfo}.`app_visible` = 1")
//				->order("{$join_table_name}.`pubdate` desc")
//				->limit($p1.','.$perPage)
//				->group("{$table_name}.`app_id`")//1个应用可能多个test
//				->findAll());				
		$res = $testModel->field("{$table_name}.`app_id`,{$appinfo}.`app_desc`,{$appinfo}.`app_score_www`,{$appinfo}.`app_starcount`,{$appinfo}.`app_name`, {$app_device_type}.`apk_path`, {$app_device_type}.`icon`")
				->join("{$join_table_name} ON {$table_name}.`aid` = {$join_table_name}.`id`")
				->join("{$appinfo} ON {$table_name}.`app_id` = {$appinfo}.`app_id`")
				->join("{$app_device_type} ON {$table_name}.`app_id` = {$app_device_type}.`app_id`")
				->where($where." and {$join_table_name}.`ismake` = 1 and {$join_table_name}.`arcrank` = 0 and {$appinfo}.`app_visible` = 1")
				->order("{$join_table_name}.`pubdate` desc,{$table_name}.`app_id` desc")
				//->limit($p2.','.$perPage*2)
				->group("{$table_name}.`app_id`")//1个应用可能多个test
				->findAll();	
		$result[0] = array("total"=>$count,
				"list"=>array_slice($res,-$perPage));				
		$result[1] =  array("total"=>$count,
				"list"=>array_slice($res,0,$perPage));
		$result[2] =  array("total"=>$count,
				"list"=>array_slice($res,$perPage,$perPage));	
		if(count($result[2]["list"])<$perPage)
		{
			$result[2]["list"] = array_merge($result[2]["list"],array_slice($res,0,$perPage-count($result[2]["list"])));
		}
		return $result;
	}	
//public function Test()
//{
//
//		
//}
	//p must be 0
	protected function get3AppList(  $actType,$cate,$rate,$time,$download,$perPage,$p,$page=false)
	{
		if($p!=0)
			return;		
		$memkey = md5($actType.'-'.$cate.'-'.$rate.'-'.$time.'-'.$download['from'].'-'.$download['to']);
		// $memData = $this->getMemcache($memkey);
		// if($memData!=false)
			// return $memData;
		$order = "";
		//$result = "";
		$result = array();
		if($actType == $this->ACT_TYPE["Feature"])
			$result = $this->get3FeatureList($perPage,$p);
		else if($actType == $this->ACT_TYPE["LatestTest"])
			$result = $this->get3LatestTestList($perPage,$p);
		else
		{
			$app_model = D("Appinfo");
			$db_name = '`_v2_android`';
			$table_name = $db_name.'.`' . C('DB_PREFIX') . 'appinfo`';
			$join_table_name = $db_name.'.`' . C('DB_PREFIX') . 'app_device_type`';
			$combin_table_name = $db_name.'.`' . C('DB_PREFIX') . 'app_category`';
			$whereCate = "";
			if(!empty($cate))
			{
				$cateArray = explode(',',$cate);
				$cate = '('.$cate.')';
				$parentCate = array();
				foreach($cateArray as $k=> $v)
				{
					if($v <= 6 )
					{
						$parentCate[] = $v;
						unset($cateArray[$k]);
					}
				}
				if(!empty($parentCate))
				{
					$parentWhere = '('.implode(',',$parentCate).')';
					$categoryModel = D("Category");
					$tmpChild = $categoryModel->field("`id`")->where("parent_id in {$parentWhere}")->findAll();
					$tmp = array();
					foreach($tmpChild as $k)
						$tmp[] = $k['id'];
					if(!empty($cateArray))
						$tmp = array_merge($cateArray,$tmp);
					$cate = '('.implode(',',$tmp).')';
				}
				$whereCate = " and {$combin_table_name}.`cate_id` in {$cate} ";
			}
			$whereRate="";
			if($rate>0)
				$whereRate = " and {$table_name}.`app_starcount`>0 and  {$table_name}.`app_score_www`>={$table_name}.`app_starcount`*".$rate;
			$whereTime="";
			if(!empty($time))
				$whereTime = " and {$table_name}.`app_update_date`>'{$time}' ";
			$whereDownload = "";
			if(!empty($download))
			{
				if($download['to']>0)
					$whereDownloadTo = " and {$table_name}.`app_downloads`<={$download['to']} ";
				if($download['from']>0)
					$whereDownloadFrom = " and {$table_name}.`app_downloads`>={$download['from']} ";					
				$whereDownload = $whereDownloadFrom.$whereDownloadTo;
			}
				
			$where = "";
			$order = "";
			switch($actType)
			{
				case $this->ACT_TYPE["Recommend"]:
					$where = $whereCate
							.$whereRate
							.$whereTime
							.$whereDownload
							." and {$table_name}.`app_id` in "
							." (select {$combin_table_name}.`app_id` from $combin_table_name where {$combin_table_name}.`cate_id`=1)";
					$order = " order by {$table_name}.`app_downloads` desc";
					break;
				case $this->ACT_TYPE["HotDownload"]:
					$where = $whereCate
							.$whereRate
							.$whereTime
							//.$whereDownload
							;
					$order = " order by {$table_name}.`app_downloads` desc";
					break;
				case $this->ACT_TYPE["TopRank"]:
					$where = $whereCate
							//.$whereRate
							.$whereTime
							.$whereDownload;
					$order = " order by ({$table_name}.`app_score_www`/({$table_name}.`app_starcount`+1e-5)) desc ";
					break;
				case $this->ACT_TYPE["MostRank"]:
					$where = $whereCate
							.$whereRate
							.$whereTime
							.$whereDownload;
					$order = " order by {$table_name}.`app_starcount` desc";
					break;
				case $this->ACT_TYPE["Newcomer"]:
				default:
					$where = $whereCate
							.$whereRate
							//.$whereTime
							.$whereDownload;
					$order = " order by {$table_name}.`app_update_date` desc";
					break;
			}
			$order .= ",{$table_name}.`app_id` desc";			
//			$count = $app_model->join("{$combin_table_name} on  {$combin_table_name}.`app_id`={$table_name}.`app_id`")
//						->where("{$table_name}.`app_visible`=1 ".$where)
//						->count();
			$ids = $app_model->query("select {$table_name}.`app_id` from {$table_name}"
						//." left join {$join_table_name} on  {$join_table_name}.`app_id`={$table_name}.`app_id`"
						." left join {$combin_table_name} on  {$combin_table_name}.`app_id`={$table_name}.`app_id`"
						." where {$table_name}.`app_visible`=1 "
						.$where
						." group by {$table_name}.`app_id`" //icon需要1对1,否则重复
						.$order
						." limit ".$this->mMaxCount
						);					 	
			$count = count($ids);
			$tmp = array();
			foreach($ids as $k)
				$tmp[] = $k['app_id'];
			//$where = " and {$table_name}.`app_id` in (".implode(',',$tmp).') ';			
			if($count<=$perPage)
			{
				$result[0] =  array("total"=>$count,"list"=>array());
				
				$where = " and {$table_name}.`app_id` in (".implode(',',$tmp).') ';
				$result[1] = $count==0?array("total"=>$count,"list"=>array()):array("total"=>$count,
					"list"=>$app_model->query("select {$table_name}.`app_id`,{$table_name}.`app_desc`,{$table_name}.`app_score_www`,{$table_name}.`app_starcount`,{$table_name}.`app_name`, {$join_table_name}.`apk_path`, {$join_table_name}.`icon` from {$table_name}"
						." left join {$join_table_name} on  {$join_table_name}.`app_id`={$table_name}.`app_id`"
						//." left join {$combin_table_name} on  {$combin_table_name}.`app_id`={$table_name}.`app_id`"
						." where {$table_name}.`app_visible`=1 "
						.$where
						." group by {$table_name}.`app_id`" //icon需要1对1,否则重复
						.$order
						//." limit ".$p.','.$perPage
								));
				$result[2] = $result[0];												
				//return $result;
			}
			else
			{
				$p1 = $count-$perPage;
				$p2 = 0;
				$p3 = $perPage;

				$tmpWhereArray = array_merge(array_slice($tmp,$p2,$perPage*2),array_slice($tmp,-$perPage));
				$where = " where {$table_name}.`app_id` in (".implode(',',$tmpWhereArray).') ';						
				$res = $app_model->query("select {$table_name}.`app_id`,{$table_name}.`app_desc`,{$table_name}.`app_score_www`,{$table_name}.`app_starcount`,{$table_name}.`app_name`, {$join_table_name}.`apk_path`, {$join_table_name}.`icon` from {$table_name}"
							." left join {$join_table_name} on  {$join_table_name}.`app_id`={$table_name}.`app_id`"
							//." left join {$combin_table_name} on  {$combin_table_name}.`app_id`={$table_name}.`app_id`"
							//." where {$table_name}.`app_visible`=1 "
							.$where
							." group by {$table_name}.`app_id`" //icon需要1对1,否则重复
							.$order
							//." limit ".$p2.','.$perPage*2
									);		
				$result[0] = array("total"=>$count,
						"list"=>array_slice($res,-$perPage));
				$result[1] =  array("total"=>$count,
						"list"=>array_slice($res,0,$perPage));
				$result[2] =  array("total"=>$count,
						"list"=>array_slice($res,$perPage,$perPage));	
				if(count($result[2]["list"])<$perPage)
				{
					$result[2]["list"] = array_merge($result[2]["list"],array_slice($res,0,$perPage-count($result[2]["list"])));
				}
			}
		}		
		// $this->setMemcache($memkey, $result);
		return $result;
	}
	private $memcacheEnable = true;
	protected function getMemcache($memkey)
	{
		$res = false;
		if($this->memcacheEnable)
		{
			$memcache = new Memcache;
			$memcache->connect('127.0.0.1', '11211');	
			$memkey_update = $memkey.'_update';
			$memUpdate = $memcache->get($memkey_update);	
			$memData = $memcache->get($memkey);
			if($memUpdate&&$memUpdate['update']>(time() - 3600 * 1)&&$memData)//1小时内
			{
				$res =  $memData['list'];
				$res['memcache']=1;
			}
		}
		return $res;
	}
	protected function setMemcache($memkey,$list)
	{	
		if($this->memcacheEnable)
		{
			$memcache = new Memcache;
			$memcache->connect('127.0.0.1', '11211');	
			$memkey_update = $memkey.'_update';
			$memData = array(
				'list'	=> $list
			);
			$memcache->set($memkey, $memData, 0, 0);
			$memcache->set($memkey_update, array('update'=>time()), 0, 0);			
		}
	}
}

?>
