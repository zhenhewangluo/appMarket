<?php
/**
 +------------------------------------------------------------------------------
 * @description:	Ajax操作Action
 +------------------------------------------------------------------------------
 * @others:			NULL
 * @file:			AjaxAction.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 +------------------------------------------------------------------------------
 */
class AjaxAction extends AppBaseAction
{
	private $_xmlPath = '';
	/**
     +----------------------------------------------------------
     * 初始化操作
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
	public function _initialize()
	{
		parent::_initialize();
		$this->_xmlPath = TEMPLATE_PATH . '/' . GROUP_NAME . '/AjaxPage' . C('TMPL_FILE_DEPR');
		$this->_tplPrefix = GROUP_NAME . ':' . MODULE_NAME . 'Page' . C('TMPL_FILE_DEPR');
	}
	/**
     +----------------------------------------------------------
     * 获取评论信息
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
	public function appComment()
	{
		//初始化返回信息
		$data = array(
			'status' => -1,
			'msg' => '评论失败'
		);
		//判断get数据是否合法
		$appScore = (int) Req::get('score', 'get');
		$appComment = Filter::act(Req::get('comment_content'), 'get');
		$active = (int) Req::get('active', 'get');
		if ($active == 1 && ($appScore <= 0 || $appScore > 5))
		{
			$data['msg'] = '评分只能是1-5分';
			die(json_encode($data));
		}
		if ($appComment == '')
		{
			$data['msg'] = '请填写评论内容';
			die(json_encode($data));
		}
		//判断用户是否已登录
		if ($this->_uid <= 0)
		{
			$data['msg'] = '请先登录';
			die(json_encode($data));
		}
		//判断是否有此应用的id
		$aid = (int) Req::get('app_id', 'get');
		$appinfoObj = D('Appinfo');

		$appinfoArr = $appinfoObj->getAppInfoById($aid, 'app_id, app_score_www, app_starcount', false);
		if ($aid <= 0 || empty($appinfoArr))
		{
			$data['msg'] = '没有此应用';
			die(json_encode($data));
		}

		//判断用户是否已评论过
		$commentObj = D('Comment');

		$userCommentId = $commentObj->getAppCommentByUidAndAid($this->_uid, $aid);
		//格式化时间
		$dateObj = new Date();
		$datetime = $dateObj->format();

		//初始化评论信息
		$commentArr = array(
			'content' => $appComment,
			'update_time' => $datetime,
		);
		//初始化评论表操作返回结果
		$commentresult = 0;
		//初始化评论方式（新增还是更新）
		$changeAppType = '';

		//初始化评论返回前段页面的内容
		$returnHtml = '';
		if ($userCommentId <= 0)//如果用户没有评论过
		{
			$changeAppType = 'add';//新增数据
			$commentArr['app_id'] = $aid;
			$commentArr['user_id'] = $this->_uid;
			$commentArr['user_name'] = $this->_username;
			$commentArr['create_time'] = $datetime;
			$commentArr['score_www'] = $appScore;
			$datetime = date('Y年m月d日', strtotime($datetime));
			$appScoreShow = $appScore * 2;
			$appComment = keyWordFilter($appComment);
			$commentresult = $commentObj->addAppComment($commentArr);
			//{~$headPathArr = explode(',', $vo['headPath'])}
			//$appScoreShow * 2
			$root = __ROOT__;
			$returnHtml = <<<EOT
			<div class="comments slide{$commentresult}" style="display:none;">
				<div class="user">
					<img class="market" src="{$root}/{$this->_userhead[2]}" alt="" /><br />
					{$this->_username}
				</div>
				<div class="bubble">
					<div class="text">
						<div class="stars whiteStars"><div class="RatingStarsWhite rating-{$appScoreShow}"></div></div>
						<br/>
						<span class="date">{$datetime}</span>
						<p>{$appComment}</p>
					</div>
				</div>
			</div>
			<div class="clear"></div>
EOT;
		}
		else
		{
			$data['msg'] = '您已经评论过了！请勿重复评论！';
			die(json_encode($data));
//			$changeAppType = 'update';
//			$commentresult = $commentObj->updateAppCommentByCommentId($userCommentId, $commentArr);
		}
		$result = 0;
		if ($commentresult > 0)
		{
			if ($changeAppType == 'add')
			{
				$appExpArr = array(
					'app_score_www' => array('exp', 'app_score_www + ' . $appScore),
					'app_starcount' => array('exp', 'app_starcount + 1')
				);
				$result = $appinfoObj->updateAppInfo($aid, $appExpArr);
			}
//			if ($changeAppType == 'update')
//			{
//				$result = 1;
//			}
		}

		if ($result > 0)
		{
			$postData = $this->arrToJson(array(
				'userid'	=> $this->_uid,
				'password'	=> $this->_userinfo['password'],
				'mid'		=> 0,
				'type'		=> 'comment',
				'appid'		=> $aid,
			));
			$this->postCurl('AppScore', 'addScore', $postData);
			$appScoreRow = $commentObj->getAppScoreDetailByAid($aid);

			$data['app_score'] = round(($appinfoArr['app_score_www'] + $appScore)/($appinfoArr['app_starcount']+1), 2);

			$data['score_count'] = number_format(($appinfoArr['app_starcount']+1));
			$data['dlflag'] = $commentresult;
			$data['returnhtml'] = (!empty($returnHtml)) ? $returnHtml : '';
			$data['msg'] = '评论成功';
			$data['status'] = 1;

			$appScoreArr = array(
				'app_starcount' => ($appinfoArr['app_starcount']+1),
				'app_score_www' => ($appinfoArr['app_score_www'] + $appScore)
			);
			$this->assign('appScore', $appScoreRow);
			$this->assign('approw', $appScoreArr);
			$this->assign('userScore', $appScore);
			$data['html'] = $this->fetch($this->_tplPrefix . 'appCommentDetail');
		}
		die(json_encode($data));
	}

	/**
     +----------------------------------------------------------
     * ajax获取应用列表 首页
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
	 * @date:			2012-1-18
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function appList()
	{
		import("ORG.Util.Page");
		$listArr = array('12', '20', '28', '36');
		$orderArr = array('default', 'update', 'download');
		$orderType = Filter::act(Req::get('order', 'get'));
		$listRow = (int)Req::get('listRow', 'get');
		$orderType = in_array($orderType, $orderArr) ? $orderType : 'default';
		$listRows = in_array($listRow, $listArr) ? $listRow : 14;
		if($orderType == 'default') $order = '';
		if($orderType == 'update') $order = 'app_update_date DESC';
		if($orderType == 'download') $order = 'app_downloads DESC';

		$p = isset($_GET['p']) && ((int)$_GET['p'] > 0) ? $_GET['p'] : 1;
		$appInfoModel = D('Appinfo');
		//图片另加//
		$list = $appInfoModel->field('app_name,author_name,app_id,app_downloads,app_score_www,app_starcount')->relation('AppDeviceType')->where("`app_visible` = '1'")->order("{$order}")->page($p . ',' . $listRows)->findAll();
		//die(var_dump($appInfoModel->getLastSql()));
		$count = $appInfoModel->where("`app_visible` = '1'")->count();
		if (!empty($list))
		{
			$Page = new Page($count, $listRows);
			$Page->setAjax($p);
			$Page->isSimple = true;
			$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
			$Page->setConfig('prev', '');
			$Page->setConfig('next', '');
			$do = Filter::act(Req::get('do', 'get'));
			$this->assign('riselist', $list);
			if(!empty($do))
			{
				$data = array();
				$data['content'] = $this->fetch($this->_tplPrefix . 'index');
				$data['pageInfo'] = $Page->show();
				$data['pageNum'] = $p;
				die(json_encode($data));
			}

			//$this->assign('pageInfo', $Page->showAjax('ajax_page'));
			//$pageInfo = json_encode(array('page'=>$this->ajax_page($Page->show())));

		}
		return false;
	}
	/**
     +----------------------------------------------------------
     * ajax登录 [弹出登录]
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
	 * @date:			2012-1-18
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function login()
	{
		$return = array(
			'status'	=> '0', // -1已登录 0 登录失败 1登录成功
			'msg'		=> '登录失败！',
			'loginAct'	=> ''
		);
		//构造数据
		//需要判断uid是否存在 ，否-》请重新登录
		if ($this->_uid > 0)
		{
			$return['status'] = -1;
			die(json_encode($return));
		}
		if($_POST['__hash__'] != Session::get('__hash__'))
			die(json_encode($return));
		if (md5($_POST['verifyCard']) != Session::get('verify'))
		{
			$return['status'] = -1;
			$return['msg'] = '验证码错误！';
			die(json_encode($return));
		}
		//判断是否有cookie
		if (Cookie::is_set(C('COOKIE_NAME')))
			Cookie::delete(C('COOKIE_NAME'));
//		if (Cookie::is_set(C('COOKIE_PASS')))
//			Cookie::delete(C('COOKIE_PASS'));

		$mail = Filter::act($_POST['email']); //账户邮箱
		$password = md5(Filter::act($_POST['password'])); //密码

		$userForm = D('RegisterUser');
		$list = $userForm->getByEmail($mail);
		$ucLogin = '';

		if (!empty($list))
		{
			//找到此用户
			if ($password == $list['password'])
			{
				if ($list['status'] == 0)
				{//用户不可用
					$return['msg'] = '用户状态不可用，未激活或已被禁用！';
					die(json_encode($return));
					//$this->error();
				}
				$login = UcApi::login($mail, $_POST['password'],2);

				if($login === FALSE) {
					$return['msg'] = 'UC登录失败';//UcApi::getError();
					die(json_encode($return));
					//$this->error();
				} else {
					$ucLogin = $login['synlogin'];
				}
				//写COOKIE,设置登录状态//30天超时
				$userObj = A('App.RegisterUser');
				$userObj->addLoginLog($list['id']);
				//记录last_login_time
				$userForm->where('id =' . $list['id'])->setField('last_login_time', date("Y-m-d H:i:s"));
				$return['status'] = 1;
				$return['msg'] = "登录成功！";
				$return['loginAct'] = $ucLogin;
				die(json_encode($return));
			}
			$return['msg'] = '用户名密码不匹配，请输入正确的用户名密码！';
			die(json_encode($return));
		}
		elseif(!empty($ucLogin))
		{
			$data = array(
				'id' => $login['uid'],
				'name' => $login['username'],
				'password' => md5($login['password']),
				'email' => $login['email'],
				'RegisteredUserHead' => array(
					'path' => 'Public/images/UserFace/noavatar_big.gif,Public/images/UserFace/noavatar_middle.gif,Public/images/UserFace/noavatar_small.gif',
					'type' => '3',
					'uploaddate' => date("Y-m-d H:i:s"),
				)
			);
			$currentUserID = $userForm->relation(true)->add($data);
			if($currentUserID !== false)
			{
				$return['status'] = 1;
				$return['msg'] = "登录成功！";
				$return['loginAct'] = $ucLogin;
				die(json_encode($return));
			}
			die(json_encode($return));
		}
		$return['msg'] = '无此用户，请输入正确的用户名密码！';
		die(json_encode($return));
	}
	public function loginWindow()
	{
		//$this->display('AjaxPage:login');
//		die(var_dump($this->_xmlPath));
		$this->display($this->_xmlPath . 'login.xml', 'utf-8', 'text/xml');
		//$this->display('AjaxPage:login');
	}
	/**
	 * 根据appid增加app下载次数
	 * @method	setIncDownloadsByAid()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-9
	 */
	public function setIncDownloadsByAid()
	{
		$aid = (int) Filter::act(Req::get('aid'));
		$return = array(
			'status' => 0,
			'msg'	=> '操作失败！',
		);
		$appObj = new AppinfoModel;
		$appRow = $appObj->getAppInfoById($aid, 'app_id');

		if($appObj->setIncDownloadsByAid($aid))
		{
			$return['status'] = 1;
			$return['url'] = C('PICTURE_HOST').$appRow['AppDeviceType'][0]['apk_path'];
			if($this->_uid > 0)
			{
				Load('extend');
				$historyList = D('DownloadHistory');
				$data = array(
					'user_id'=> $this->_uid,
					'app_id'=> $aid,
					'session'=>get_client_ip() ,
					'mid'=> 0,
					'channel_id'=> 0,
					'create_time'=> date('Y-m-d H:i:s'),
					'status'=> 1 ,
					'type'=> 'web',
					'source'=> '',
					'end_time'=> date('Y-m-d H:i:s')
				);
				$check = $historyList->where("user_id='{$data['user_id']}' and app_id='{$data['app_id']}'")->find();

				if(!empty($check))
					$historyList->where("id='{$check['id']}'")->save($data);
				else
					$historyList->add($data);

				//积分接口
				$postData = $this->arrToJson(array(
					'userid'	=> $this->_uid,
					'password'	=> $this->_userinfo['password'],
					'mid'		=> 0,
					'type'		=> 'download',
					'appid'		=> $aid,
				));
				$this->postCurl('AppScore', 'addScore', $postData);
			}
		}
		die(json_encode($return));
	}
	public function getPopupAppinfo()
	{	$res = array("result"=>0,"content"=>"");
		$id = Filter::act(Req::get('id', 'post'), 'int');
		$model = D("Appinfo");
		$app = $model->field("app_id,app_name,app_desc,app_score_www,app_starcount,app_downloads")
				->relation('AppDeviceType')
				->where("app_id={$id} and app_visible=1")->find();
		$html ="";
		if($app!==false&&$app!=null)
		{
			$res["result"] = 1;
			$this->assign("app",$app);
			$html = $this->fetch($this->_tplPrefix . 'popupappinfo');
		}
		die($html);
		//die(json_encode($res));
	}

	protected $typeArr =  array(
				'SPONSORED_APP_DATE'=>1,		//特别推荐
				'TEST_REPORT_DATE'=>2,			//最新测试
				'STAFF_CHOICE_DATE'=>3,		//我们的推荐
				'MARKET_TOP_APP_POINTS'=>4,	//热门下载
				'RATING_WEIGHTS'=>5,			//最受好评
				'NUM_MARKET_RATINGS'=>6,		//最多评分
				'NEWCOMER_RATING'=>7			//最新
			);
	protected	$arrDay = array(
			'TODAY'=>0,
			'YESTERDAY'=>1,
			'LAST_7_DAYS'=>7,
			'LAST_30_DAYS'=>30,
			'LAST_60_DAYS'=>60,
			'LAST_365_DAYS'=>365,
		);
	/**
	 * 根据类型和组合查询参数获取应用列表
	 * @method	ajaxGetAppsList()
	 * @access public
	 * @param	act
	 *	'SPONSORED_APP_DATE',		//特别推荐
	 *	'TEST_REPORT_DATE',			//最新测试
	 *	'STAFF_CHOICE_DATE',		//我们的推荐
	 *	'MARKET_TOP_APP_POINTS',	//热门下载
	 *	'RATING_WEIGHTS',			//最受好评
	 *	'NUM_MARKET_RATINGS',		//最多评分
	 *	'NEWCOMER_RATING'			//最多评分
	 * @param	cate 分类
	 * @param	rate 最低评分
	 * @param	day	该时间内
	 * @param	from	下载最低值
	 * @param	to		下载最高值
	 * @param	perpage	每页应用数
	 * @param	index		页...-3,-2,-1,0,1,2,3...
	 * @return total:应用总数,list:应用列表
	 * @author	zxg
	 * @date	20120514
	 */
	public function ajaxGetAppsList()
	{


		$actType = Filter::act(Req::get('act', 'post'));
		if(!array_key_exists($actType,$this->typeArr))
			$actType = 	'SPONSORED_APP_DATE';
		$actStr = $actType;
		$actType = $this->typeArr[$actType];
		$cate = Filter::act(Req::get('cate', 'post'));
		$rate = Filter::act(Req::get('rate', 'post'), 'int');
		$day = Filter::act(Req::get('day', 'post'));
		if(!array_key_exists($day,$this->arrDay))
			$day = 	'LAST_365_DAYS';
		$time = ($day == 'LAST_365_DAYS')?"":date("Y-m-d 00:00:00", time() - 3600 * 24 * $this->arrDay[$day]); //前?天
		$from = Filter::act(Req::get('from', 'post'), 'int');
		$to = Filter::act(Req::get('to', 'post'), 'int');
		$download = array("from"=>$from,"to"=>$to);
		if($from == -1)
			$download = null;
		$perPage = Filter::act(Req::get('perpage', 'post'), 'int');
		if($perPage <= 0)
			$perPage = 8;
		$p = Filter::act(Req::get('index', 'post'), 'int');
		$_SESSION['app_filter'] = array($cate,$rate, $day, $download);
		$tmp = $this->getAppList($actType, $cate, $rate, $time, $download, $perPage, $p);
		$liststr = "";
		foreach($tmp['list'] as $v)
		{
			$v['icon'] = C('PICTURE_HOST').$v['icon'];
			$v['appdetail'] = U('App-Apps/detail', array('aid' => $v['app_id']));
			$str = <<<EOT
			<li><a href="{$v['appdetail']}" class="appIcon" id="{$actStr}-{$v['app_id']}-{$p}"><img src="{$v['icon']}" width="72" height="72" alt="Icon"><span>{$v['app_name']}</span></a></li>
EOT;
			$liststr.=$str;
		}
		$res= array('total'=>$tmp['total'],
					'list' =>$liststr);
		die(json_encode($res));
	}
	public function ajaxGet3AppsList()
	{
		$actType = Filter::act(Req::get('act', 'post'));
		if(!array_key_exists($actType,$this->typeArr))
			$actType = 	'SPONSORED_APP_DATE';
		$actStr = $actType;
		$actType = $this->typeArr[$actType];
		$cate = Filter::act(Req::get('cate', 'post'));
		$rate = Filter::act(Req::get('rate', 'post'), 'int');
		$day = Filter::act(Req::get('day', 'post'));
		if(!array_key_exists($day,$this->arrDay))
			$day = 	'LAST_365_DAYS';
		$time = ($day == 'LAST_365_DAYS')?"":date("Y-m-d 00:00:00", time() - 3600 * 24 * $this->arrDay[$day]); //前?天
		$from = Filter::act(Req::get('from', 'post'), 'int');
		$to = Filter::act(Req::get('to', 'post'), 'int');
		$download = array("from"=>$from,"to"=>$to);
		if($from == -1)
			$download = null;
		$perPage = Filter::act(Req::get('perpage', 'post'), 'int');
		if($perPage <= 0)
			$perPage = 8;
		$p = Filter::act(Req::get('index', 'post'), 'int');
		$tmp = array();
		$_SESSION['app_filter'] = array($cate,$rate, $day, $download);
//$f= fopen("./log.txt","w");
//fwrite($f, "cate:".$cate."++rate".$rate."++time".$time."++download".$download['from']."++".$download['to']."\n");
//fclose($f);
//		$tmp[] = $this->getAppList($actType, $cate, $rate, $time, $download, $perPage, $p-1);
//		$tmp[] = $this->getAppList($actType, $cate, $rate, $time, $download, $perPage, $p);
//		$tmp[] = $this->getAppList($actType, $cate, $rate, $time, $download, $perPage, $p+1);

//		$memcacheEnable = true;
//		$fromMemcache = false;
//		if($memcacheEnable)
//		{
//			$memcache = new Memcache;
//			$memcache->connect('127.0.0.1', '11211');
//			$memkey = md5($actType.$cate.$rate.$time.$download['from'].$download['to']);
//			$memkey_update = $memkey.'_update';
//			$memUpdate = $memcache->get($memkey_update);
//			$memData = $memcache->get($memkey);
//			if($memUpdate&&$memUpdate['update']>(time() - 3600 * 1)&&$memData)//1小时内
//			{
//				$tmp =  $memData['list'];
//				$fromMemcache = true;
//			}
//			else
//			{
//				$tmp = $this->get3AppList($actType, $cate, $rate, $time, $download, $perPage, $p);
//				$memData = array(
//					'list'	=> $tmp
//				);
//				$memcache->set($memkey, $memData, 0, 0);
//				$memcache->set($memkey_update, array('update'=>time()), 0, 0);
//			}
//		}
//		else
		{
			$tmp = $this->get3AppList($actType, $cate, $rate, $time, $download, $perPage, $p);
		}
		$fromMemcache = (isset($tmp['memcache'])&&$tmp['memcache']==1)?true:false;

//var_dump($tmp);die();
		$liststr = array();
		for($i=0;$i<3;$i++)
		{
			if(empty($tmp[$i]['list']))
				$liststr[$i] = "<li></li>";
			else
			{
				foreach($tmp[$i]['list'] as $v)
				{
					$v['icon'] = C('PICTURE_HOST').$v['icon'];
					$v['appdetail'] = U('App-Apps/detail', array('aid' => $v['app_id']));
					$str = <<<EOT
					<li><a href="{$v['appdetail']}" class="appIcon" id="{$actStr}-{$v['app_id']}-{$p}"><img src="{$v['icon']}" width="72" height="72" alt="Icon"><span>{$v['app_name']}</span></a></li>
EOT;
					$liststr[$i].=$str;
				}
			}
		}
		$res= array('total'=>$tmp[0]['total'],
					'active' =>$liststr[1],
					'prev'	=>$liststr[0],
					'next'	=>$liststr[2],
					'from'=>($fromMemcache?'memcache':'mysql'),
					'url'	=> U('App-Index/appList', array('rid'	=> $actType))
					);
		die(json_encode($res));
	}
	public function saveActOrder()
	{
		$order = Filter::act(Req::get('rids', 'post'));
		$_SESSION['app_order'] = $order;
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
		$cateObj = D('Category');

		$appCateTree = $cateObj->getCateTree();
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

		$this->getAppListByCondition($appCateTree, $orderType, $listRow, true);
		$this->assign('params', $params);
		$this->assign('orderTypeArr', $orderTypeArr);
		$this->assign('listRowArr', $listRowArr);



		$data['content'] = $this->fetch($this->_tplPrefix . 'appCenterList');
		die(json_encode($data));
	}
	/**
	 * 根据类型和组合查询参数获取应用列表(更多)
	 * @method	ajaxGetMoreApps()
	 * @access public
	 * @param	act
	 *	'SPONSORED_APP_DATE',		//特别推荐
	 *	'TEST_REPORT_DATE',			//最新测试
	 *	'STAFF_CHOICE_DATE',		//我们的推荐
	 *	'MARKET_TOP_APP_POINTS',	//热门下载
	 *	'RATING_WEIGHTS',			//最受好评
	 *	'NUM_MARKET_RATINGS',		//最多评分
	 *	'NEWCOMER_RATING'			//最多评分
	 * @param	cate 分类
	 * @param	rate 最低评分
	 * @param	day	该时间内
	 * @param	from	下载最低值
	 * @param	to		下载最高值
	 * @param	perpage	每页应用数
	 * @param	index		页...-3,-2,-1,0,1,2,3...
	 * @return total:应用总数,list:应用列表
	 * @author	zxg
	 * @date	20120514
	 */
	public function ajaxGetMoreApps()
	{
		import("ORG.Util.Page");
//		$_POST['act'] = 'NUM_MARKET_RATINGS';
//		$_POST['cate'] = '5,6';
//		$_POST['rate'] = 0;
//		$_POST['day'] = 	'LAST_365_DAYS';
//		$_POST['from'] = -1;
//		$_POST['to'] = 1000;
//		$_POST['perpage'] = 25;
//		$_POST['index'] = 0;

		$htmlType = Filter::act(Req::get('htmlType'));
		$this->assign('htmlType', $htmlType);

		$actType = Filter::act(Req::get('rid', 'post'));
		if(!array_key_exists($actType,$this->typeArr))
			$actType = 	'SPONSORED_APP_DATE';
		$actStr = $actType;
		$actType = $this->typeArr[$actType];
		$cate = Filter::act(Req::get('cate', 'post'));
		$rate = Filter::act(Req::get('rate', 'post'), 'int');
		$day = Filter::act(Req::get('day', 'post'));
		if(!array_key_exists($day,$this->arrDay))
			$day = 	'LAST_365_DAYS';
		$time = ($day == 'LAST_365_DAYS')?"":date("Y-m-d 00:00:00", time() - 3600 * 24 * $this->arrDay[$day]); //前?天
		$from = Filter::act(Req::get('from', 'post'), 'int');
		$to = Filter::act(Req::get('to', 'post'), 'int');
		$download = array("from"=>$from,"to"=>$to);
		if($from == -1)
			$download = null;
		$perPage = Filter::act(Req::get('max', 'post'), 'int');
		if($perPage <= 0)
			$perPage = 10;
		$p = Filter::act(Req::get('si', 'post'), 'int');
		if($p<=0)$p=1;
		$_SESSION['app_filter'] = array($cate,$rate, $day, $download);
		$tmp = $this->getAppList($actType, $cate, $rate, $time, $download, $perPage, $p-1,true);
		if (!empty($tmp['list']))
		{
			$Page = new Page($tmp['total'], $perPage);
			$Page->setAjax($p);
			$Page->isSimple = true;
			$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
			$Page->setConfig('prev', '');
			$Page->setConfig('next', '');
			$do = Filter::act(Req::get('do', 'post'));
			$this->assign('appslist', $tmp['list']);
			if(!empty($do))
			{
				$html = $this->fetch($this->_tplPrefix . 'moreapps');
				$data = array();
				$data['content'] = $html;
				$data['pageInfo'] = $Page->show();
				$data['pageNum'] = $p;
				die(json_encode($data));
			}
			$this->assign("page",$Page->show());
			$html = $this->fetch($this->_tplPrefix . 'moreapps');
			die($html);
		}
		return false;
	}
	/**
     +----------------------------------------------------------
     * ajax获取应用评论列表 详情页
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param void
     +----------------------------------------------------------
     * @return void
     +----------------------------------------------------------
	 * @date:			2012-1-18
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function appCommentList()
	{
		$aid = (int)Filter::act(Req::get('aid'));
		$commentObj = D('Comment');
		$re = $commentObj->getAppCommentsByAid($aid, true);
		$this->assign('appComment', $re);
		$render = $this->fetch($this->_tplPrefix . 'appCommentList');
		die(json_encode(array('html' => $render)));
	}
	public function getUserInfo()
	{
		$userBarHtml = $this->fetch($this->_tplPrefix . 'userBarBox');
		$loginFormHtml = $this->fetch($this->_tplPrefix . 'loginFormBox');
		$commentHtml = $this->fetch($this->_tplPrefix . 'commentForm');
		die(json_encode(array(
			'commentHtml'	=> $commentHtml,
			'userBarHtml'	=> $userBarHtml,
			'loginFormHtml'	=> $loginFormHtml
		)));
	}
}

?>
