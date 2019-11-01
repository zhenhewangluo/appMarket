<?php

class AppsAction extends BaseAction
{

	private $_layout = 'Layout:layout';

	public function _initialize()
	{
		parent::_initialize();
		if ($this->_uid == 0 || $this->_usertype != 1)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://Sysuser/login'));
			$this->error('未登录或无权限，请重新登录！');
		}
	}

	/*	 * *********************************************
	 * Function:       appList
	 * Description:    显示应用列表，
	 * Input:
	 * Return:
	 * Others:
	 * Date：         2011-12-24
	 * Author：       xiaoguang
	 * ************************************************** */

	public function appList()
	{
		$order = "app_update_date DESC";
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$appInfoModel = D('Appinfo');
		$list = $appInfoModel->order("{$order}")->relation('AppCategory')
				->field('app_id,app_name,author_name,app_version,app_update_date,app_downloads,app_score_www,app_starcount')
				->page($p . ',20')
				->where("app_visible = '1'")
				->select();
		$count = $appInfoModel->where("app_visible = '1'")->count();
		$cateObj = D('Category');
		$typeList = $cateObj->where("parent_id<=1")->findAll();
		foreach ($typeList as $k => $v)
		{
			$typeArr[] = $v['id'];
		}
		$typeArr[] = ''; //非子分类数组
		if (!empty($list))
		{
			foreach ($list as $k => &$v)
			{
				$v['app_score_average'] = number_format($list['app_score_www'] / ($list['app_starcount'] + 1e-5), 2);
				$v['app_isrecommon'] = '否';
				$v['app_cate_name'] = '暂无分类';
				if (!empty($v['AppCategory']))
				{
					foreach ($v['AppCategory'] as $vcate)
					{
						if ($vcate['cate_id'] == 1)
						{
							$v['app_isrecommon'] = '是';
							break;
						}
					}
					if (!in_array($v['AppCategory'][0]['cate_id'], $typeArr))
					{
						$v['app_cate_name'] = $v['AppCategory'][0]['cate_id'];
						$cateList[] = $v['app_cate_name'];
					}
				}
			}
			//取分类名字
			$cateWhere = implode(',', $cateList);

			$catelistResult = $cateObj->field('id,name')->where("`id` in ( {$cateWhere} )")->findALL();

			if (!empty($catelistResult))
				foreach ($list as &$v1)
				{
					foreach ($catelistResult as $v2)
						if ($v1['app_cate_name'] == $v2['id'])
						{
							$v1['app_cate_name'] = $v2['name'];
							break;
						}
				}

			$Page = new Page($count, 20);
			$show = $Page->show();
			$this->assign('applist', $list);
			$this->assign('page', $show);
		}
		$this->assign('viewcontent', MODULE_NAME . ':appList');
		$this->display($this->_layout);
	}

	/*	 * *********************************************
	 * Function:       recycleList
	 * Description:    显示回收站应用列表，
	 * Input:
	 * Return:
	 * Others:
	 * Date：         2011-12-24
	 * Author：       xiaoguang
	 * ************************************************** */

	public function appRecycleList()
	{
		$order = "app_update_date DESC";
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$appInfoModel = D('Appinfo');
		$list = $appInfoModel->order("{$order}")->relation('AppCategory')
				->field('app_id,app_name,author_name,app_version,app_update_date,app_downloads,app_score_www,app_starcount')
				->page($p . ',20')
				->where("app_visible = '0'")
				->select();
		$count = $appInfoModel->where("app_visible = '0'")->count();
		$typeArr = array('1', '2', '3', '4', ''); //不属于分类
		if (!empty($list))
		{
			foreach ($list as $k => &$v)
			{
				$v['app_score_average'] = number_format($list['app_score_www'] / ($list['app_starcount'] + 1e-5), 2);
				$v['app_isrecommon'] = '否';
				$v['app_cate_name'] = '暂无分类';
				if (!empty($v['AppCategory']))
				{
					foreach ($v['AppCategory'] as $vcate)
					{
						if ($vcate['cate_id'] == 1)
						{
							$v['app_isrecommon'] = '是';
							break;
						}
					}
					if (!in_array($v['AppCategory'][0]['cate_id'], $typeArr))
					{
						$v['app_cate_name'] = $v['AppCategory'][0]['cate_id'];
						$cateList[] = $v['app_cate_name'];
					}
				}
			}
			//取分类名字
			$cateWhere = implode(',', $cateList);
			$cateObj = D('Category');
			$catelistResult = $cateObj->field('id,name')->where("`id` in ( {$cateWhere} )")->findALL();

			if (!empty($catelistResult))
				foreach ($list as &$v1)
				{
					foreach ($catelistResult as $v2)
						if ($v1['app_cate_name'] == $v2['id'])
						{
							$v1['app_cate_name'] = $v2['name'];
							break;
						}
				}

			$Page = new Page($count, 20);
			$show = $Page->show();
			$this->assign('applist', $list);
			$this->assign('page', $show);
		}
		$this->assign('viewcontent', MODULE_NAME . ':appRecycleList');
		$this->display($this->_layout);
	}

	/*	 * *********************************************
	 * Function:       appCateList
	 * Description:    显示分类列表，
	 * Input:
	 * Return:
	 * Others:
	 * Date：         2011-12-24
	 * Author：       xiaoguang
	 * ************************************************** */

	public function appCateList()
	{
		$order = "id";
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$cateModel = D('Category');
		$list = $cateModel->order("{$order}")
				->page($p . ',20')
				->select();
		$count = $cateModel->count();
		if (!empty($list))
		{

			$Page = new Page($count, 20);
			$show = $Page->show();
			$this->assign('applist', $list);
			$this->assign('page', $show);
		}
		$this->assign('viewcontent', MODULE_NAME . ':appCateList');
		$this->display($this->_layout);
	}

	/*	 * *********************************************
	 * Function:       文件上传
	 * Description:    apk文件上传
	 * Input:          文件路径
	 * Return:
	 * Others:
	 * Date：         2011-12-21
	 * Author：       xiaoguang
	 * ************************************************** */

	protected function upload()
	{
		import("ORG.Net.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小,50M 测试！
		$upload->maxSize = 50 * 1049000; //50M 测试！
		//设置上传文件类型
		$upload->allowExts = explode(',', 'apk,APK');
		//设置附件上传目录
		$upload->savePath = C('APP_FILE_PATH');
		//设置需要生成缩略图，仅对图像文件有效
		$upload->thumb = false;

		if (!$upload->upload())
		{
			//捕获上传异常
			$this->error($upload->getErrorMsg());
		}
		else
		{
			//取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo();
			$this->assign('apkpath', C('APP_FILE_PATH') . $uploadList[0]['savename']);
			$this->assign('apkfilename', C('APP_FILE_PATH') . $uploadList[0]['savename']);
		}
	}

	public function apkUpload()
	{
		if (!empty($_FILES['apkfile']))
		{
			//如果有文件上传 上传附件
			$this->upload();

			$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/appAdd'));
			$this->success('已经上传，正在跳转！');
		}
	}

	public function appAdd()
	{

		//$_SESSION['file_id'] = md5(time() . rand(10000, 99999));
		//var_dump($_SESSION['file_id']);
		$apptypeObj = D("Category");
		$typelist = $apptypeObj->where("`id`!='1' and `parent_id`=0")->findAll();
		$this->assign('apptypelist', $typelist);

		$catelist = $apptypeObj->where("`parent_id`={$typelist[0]['id']}")->findAll();
		$this->assign('appcatelist', $catelist);


		$devicetypeObj = D("DeviceType");
		$sdklist = $devicetypeObj->group('sdk_version')->order('sdk_version')->field('sdk_version')->findAll();

		$this->assign('sdklist', $sdklist);

		$this->assign('viewcontent', MODULE_NAME . ':appAdd');
		$this->display($this->_layout);
	}

	public function appEdit()
	{
		$id = Filter::act(Req::get('id', 'get'));

		$appinfoObj = D("Appinfo");
		$appinfo = $appinfoObj->where("app_id={$id}")->find();
		if (empty($appinfo))
		{
			$this->error('无此应用ID！');
		}
		$appCateObj = D("AppCategory");
		$cate = $appCateObj->field('cate_id')->where("app_id = {$id}")->order('cate_id DESC')->findAll();
		$appinfo['appcate'] = $cate[0]['cate_id'];
		$appinfo['app_isrecommon'] = 0;
		foreach ($cate as $k => $v)
		{
			if ($v['cate_id'] == 1)
			{
				$appinfo['app_isrecommon'] = 1;
				break;
			}
		}
		$apptypeObj = D("Category");
		$typelist = $apptypeObj->where("`id`!='1' and `parent_id`=0")->findAll();
		$this->assign('apptypelist', $typelist);

		$type = $apptypeObj->field('parent_id')->where("`id`={$appinfo['appcate']}")->find();
		$appinfo['apptype'] = $type['parent_id'];

		$catelist = $apptypeObj->where("`parent_id`={$appinfo['apptype']}")->findAll();
		$this->assign('appcatelist', $catelist);

		$devicetypeObj = D("DeviceType");
		$sdklist = $devicetypeObj->group('sdk_version')->order('sdk_version')->field('sdk_version')->findAll();

		$this->assign('sdklist', $sdklist);
		$this->assign('appinfo', $appinfo);

		//适配信息
		$appDevicetypeObj = D("AppDeviceType");
		$deviceList = $appDevicetypeObj->where("app_id='{$id}'")->findAll();
		$screen = array(1 => 0, 2 => 0, 3 => 0, 4 => 0);
		$resolution = array(1 => '/auto/', 2 => '/240x320/', 3 => '/320x480/', 4 => '/480x800/'); //对应2，3，4分辨率
		$apkimglist = array();

//		die(var_dump($deviceList));
//		$screenshots =
		$apkimglist['screenshots'] = !empty($deviceList[0]['screenshots']) ? explode(';', rtrim($deviceList[0]['screenshots'], ';')) : NULL;
		$apkimglist['icon'] = !empty($deviceList[0]['icon']) ? $deviceList[0]['icon'] : NULL;
		$apkimglist['app_size'] = !empty($deviceList[0]['app_size']) ? $deviceList[0]['app_size'] : NULL;
//var_dump($apkimglist['screenshots']);
		if(count($deviceList) == 1 && $appinfo['relationid'] > 0)
		{
			$screen[1] = 1;
			$apkimglist[1]['apk_path'] = !empty($deviceList[0]['apk_path']) ? $deviceList[0]['apk_path'] : NULL;;
			$apkimglist[1]['apk_name'] = substr(strrchr($deviceList[0]['apk_path'], '/'), 1);
		}
		else
		{
			foreach ($deviceList as $k => $v)
			{
				$apk_path = $v['apk_path'];
	
				foreach ($screen as $k2 => $v2)
				{
					if ($screen[$k2] == 1)
						continue;
					if ($screen[$k2] == 0)
						$screen[$k2] = (strpos($apk_path, $resolution[$k2]) !== false) ? 1 : 0;
	//				if ($screen[$k2] == 0)
	//					$screen[$k2] = (strpos($screenshots, $resolution[$k2]) !== false) ? 1 : 0;
					if ($screen[$k2] == 1)
					{
						if (!empty($apk_path) && empty($apkimglist[$k2]['apk_path']))
						{
							$apkimglist[$k2]['apk_name'] = substr(strrchr($apk_path, '/'), 1);
							$apkimglist[$k2]['apk_path'] = $apk_path;
						}
	//					if (!empty($screenshots) && empty($apkimglist[$k2]['screenshots']))
	//						$apkimglist[$k2]['screenshots'] = explode(';', $screenshots);
					}
				}
			}
		}
//var_dump(  $screen)	;
//die(var_dump(  $apkimglist));

		$this->assign('screen', $screen);
		$this->assign('apkimglist', $apkimglist);

		$this->assign('viewcontent', MODULE_NAME . ':appEdit');
		$this->display($this->_layout);
	}

	public function appCateAdd()
	{
		$cateObj = D("Category");
		$list = $cateObj->where("`parent_id`=0")->field('name,id')->findAll();
		$list = array_merge(array(array('id' => 0, 'name' => '根类别')), $list);
		$this->assign('parentlist', $list);
		$this->assign('viewcontent', MODULE_NAME . ':appCateAdd');
		$this->display($this->_layout);
	}

	public function appCateEdit()
	{
		$id = Filter::act(Req::get('id', 'get'));

		$cateObj = D("Category");
		$list = $cateObj->where("`parent_id`=0")->field('name,id')->findAll();
		$list = array_merge(array(array('id' => 0, 'name' => '根类别')), $list);
		$this->assign('parentlist', $list);

		$current = $cateObj->where("`id`= {$id}")->find();
		if (!empty($current))
			$this->assign('cateinfo', $current);
		else
		{
			$this->error('无此分类！');
		}
		$this->assign('viewcontent', MODULE_NAME . ':appCateEdit');
		$this->display($this->_layout);
	}

	public function appCateEditAction()
	{
		$data = array(
			'id' => Filter::act($_POST['id']),
			'name' => Filter::act($_POST['name']),
			'description' => Filter::act($_POST['cate_desc']),
			'parent_id' => Filter::act($_POST['parent']),
			'visible' => Filter::act($_POST['visible']),
			'order' => Filter::act($_POST['order']),
			'updatetime' => date("Y-m-d H:i:s"),
		);
		if ($data['id'] == $data['parent_id'])
			$this->error('修改失败！父类不能选当前类别。');
		$cateObj = D("Category");
		$list = $cateObj->data($data)->save();
		if ($list !== false)
		{
			$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/appCateList'));
			$this->success('修改成功！');
		}
		$this->error('修改失败！');
	}

	public function appCateAddAction()
	{
		$data = array(
			'name' => Filter::act($_POST['name']),
			'description' => Filter::act($_POST['cate_desc']),
			'parent_id' => Filter::act($_POST['parent']),
			'visible' => Filter::act($_POST['visible']),
			'order' => Filter::act($_POST['order']),
			'updatetime' => date("Y-m-d H:i:s"),
		);
		$cateObj = D("Category");
		$list = $cateObj->data($data)->add();
		if ($list !== false)
		{
			$this->success('添加成功！');
		}
		$this->error('添加失败！');
	}

	public function appEditAction()
	{
//        if( $this->_uid==0)
//        {
//             $this->assign('jumpUrl',  U(APP_NAME.'://'.MODULE_NAME.'/login'));
//             $this->error('未登录，请重新登录！');
//        }


		$data = array(
			//appinfo
			'app_id' => Filter::act($_POST['app_id']),
			'app_name' => Filter::act($_POST['app_name']),
			'app_version' => Filter::act($_POST['app_version']),
			'app_price' => Filter::act($_POST['app_price']),
			'app_slogan' => Filter::act($_POST['app_slogan']),
			'app_desc' => Filter::act($_POST['app_desc'], 'text'),
			'app_visible' => Filter::act($_POST['app_visible']),
			'is_english' => Filter::act($_POST['is_english']),
//			//自动填
			'app_create_date' => date("Y-m-d H:i:s"),
			'app_update_date' => date("Y-m-d H:i:s"),
			'author_name' => Filter::act($_POST['app_author']),
			'author_id' => $this->_uid,
			'source' => ($this->_usertype == 1 ? 'Admin' : 'Developer'), //临时
//
//			//推荐？？
//			'app_isrecommon'=> Filter::act($_POST['app_isrecommon']),
//
//			//分类
//			'AppCategory' =>array(
//								array('cate_id'=>Filter::act(Req::get('appcate')),
//										'order'  =>99,)
//								),//因为HAS_MANY 必须加两级array
//
//			//app_device_type
//			'icon'=> Filter::act($_POST['icon']),
//			'logo'=> Filter::act($_POST['logo']),
//			'screenshots'	=> Filter::act($_POST['screenshots']),
//			'app_size'		=> Filter::act($_POST['app_size']),
//			'apk_path'		=> Filter::act($_POST['apk_path']),
//
//			//device_type
//			'sdk_version'=> Filter::act($_POST['sdk_version']),
//			'screen'	 => Filter::act($_POST['apk_path']),
		);
		//SDK未做处理
		//推荐
		$app_isrecommon = Filter::act(Req::get('app_isrecommon'));
		if ($app_isrecommon)
		{
			$data['AppCategory'] = array(
				array('cate_id' => Filter::act(Req::get('appcate')),
					'order' => 99),
				array('cate_id' => 1, //推荐 id=1
					'order' => 99)
			); //因为HAS_MANY 必须加两级array
		}
		else
		{
			//删除已有推荐
			$appCateObj = D('AppCategory');
			$appCateObj->where("`app_id`='{$data['app_id']}' and `cate_id`=1")->delete();
			$data['AppCategory'] = array(
				array('cate_id' => Filter::act(Req::get('appcate')),
					'order' => 99)
			); //因为HAS_MANY 必须加两级array
		}

		$appinfoObj = D('Appinfo');
//		$appinfoObj = new AppinfoModel;
		//更新取消重名判断
//		$appId = $appinfoObj->where("`app_name`='{$data['app_name']}'")->find();
//
//		if(!empty($appId))
//		{
//			$return['msg'] = 'APP名称已经存在，添加失败';
//			die(json_encode($return));
//		}
		$appId = $appinfoObj->relation('AppCategory')->save($data, array(), true); //覆盖
//		die(var_dump($appId));
		if ($appId == 1)//更新成功返回1
		{
			//如果存在 删除原来已有app子分类，
			$cateObj = D('Category');
			$childCatelist = $cateObj->getChildCategorys();
			$childCatelist = implode(',', $childCatelist);
			$appcateObj = D('AppCategory');
			$result = $appcateObj
							->where("`app_id`='{$data['app_id']}' and `cate_id`!='{$data['AppCategory'][0]['cate_id']}' and `cate_id` in ({$childCatelist})")->delete();
			$list = '';
			//修改device_id信息
			$relationid = Filter::act(Req::get('relationid', 'post'));
//			die(var_dump($relationid));
//			if($relationid > 0)
//			{
//				$list = $result;
//			}
//			else
//			{
				$app_id = Filter::act($_POST['app_id']);
//die(var_dump( $app_id ));
				$app_size = Filter::act($_POST['app_size']);
				$screen_focus = Filter::act($_POST['screen_focus']);
				$screen = Filter::act($_POST["apk_image"]);
				$icon = Filter::act($_POST["apk_icon"]);
				$appDeviceTypeObj = D("AppDeviceType");
				//如果存在删除已有数据
				$appDeviceTypeObj->where("`app_id`={$app_id}")->delete();
				$addData = array();

				if(!empty($screen_focus))
				{

					foreach ($screen_focus as $k => $v)
					{
						$apk = Filter::act($_POST["apk_{$v}_file"]);
						if (empty($apk))//需要再定义
							break;
						$data = array(
							'app_id' => $app_id,
							'device_type_id' => 0, //需要再定义
							'apk_path' => $apk,
							'screenshots' => empty($screen) ? '' : implode(';', $screen),
							'app_size' => $app_size,
							'icon' => empty($icon) ? '' : $icon,
							'logo' => '',
							'pkg_name' => '',
						);
						$addData[$k] = $data;
					}
				}
				else
				{
					$addData[0] = array(
						'app_id' => $app_id,
						'device_type_id' => 0, //需要再定义
						'apk_path' => $apk,
						'screenshots' => empty($screen) ? '' : implode(';', $screen),
						'app_size' => $app_size,
						'icon' => empty($icon) ? '' : $icon,
						'logo' => '',
						'pkg_name' => '',
					);
				}

				$list = $appDeviceTypeObj->addAll($addData, array(), true);
//				echo $appDeviceTypeObj->getLastSql();
//				die(var_dump());
//			}
			if ($list !== false)
			{
				$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/appList'));
				$this->success('已经修改，正在跳转！');
			}
			else
			{
				$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/appList'));
				$this->error('App信息修改成功，但添加文件失败！可以在App列表为该应用添加文件。');
			}
		}
		$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/appList'));
		$this->error('修改失败！');
	}

	/*	 * *********************************************
	 * Function:       appsDel
	 * Description:    把应用移动到回收站，
	 * Input:
	 * Return:
	 * Others:
	 * Date：         2011-12-24
	 * Author：       xiaoguang
	 * ************************************************** */

	public function appsDel()
	{
		$appIds = Req::get('id');
		if (is_array($appIds))
		{
			$delList = implode(",", $appIds);
		}
		else
		{
			$delList = $appIds;
		}
		$appinfoObj = D('Appinfo');
		$list = $appinfoObj->where("`app_id` in ({$delList})")->save(array('app_visible' => 0, 'app_update_date' => date("Y-m-d H:i:s")));
//		if ($list !== false)
//		{
//			$this->success('已移入回收站！');
//		}
//		$this->error('操作失败！');
		if ($list !== false)
		{
			$return['status'] = 1;
			$return['msg'] = '删除成功！';
			//$this->success('已移入回收站！');
		}
		//$this->error('操作失败！');
		die(json_encode($return));
	}

	/*	 * *********************************************
	 * Function:       appsRecycleRestore
	 * Description:    把应用从回收站还原到应用列表，
	 * Input:
	 * Return:
	 * Others:
	 * Date：         2011-12-24
	 * Author：       xiaoguang
	 * ************************************************** */

	public function appsRecycleRestore()
	{
		//权限判断

		$restoreList = implode(",", $_POST['id']);
		;
		$appinfoObj = D('Appinfo');
		$list = $appinfoObj->where("`app_id` in ({$restoreList})")->save(array('app_visible' => '1'));
		if ($list !== false)
		{
			$this->success('已还原！');
		}
		$this->error('操作失败！');
	}

	/*	 * *********************************************
	 * Function:       appsRecycleDel
	 * Description:    把应用从回收站移除，
	 * Input:
	 * Return:
	 * Others:
	 * Date：         2011-12-24
	 * Author：       xiaoguang
	 * ************************************************** */

	public function appsRecycleDel()
	{
		$appIds = Req::get('id');
		if (is_array($appIds))
		{
			$delList = implode(",", $appIds);
		}
		else
		{
			$delList = $appIds;
		}
		$appinfoObj = D('Appinfo'); //'AppDeviceType,AppCategory'
		$map['app_id']  = array('in',$appIds);
		$list = $appinfoObj->where("`app_id` in ({$delList})")->delete();
		$appCategoryObj = D('AppCategory');
		$appCategoryObj->where("`app_id` in ({$delList})")->delete();
		$appDeviceTypeObj = D('AppDeviceType');
		$appDeviceTypeObj->where("`app_id` in ({$delList})")->delete();
		$appCommentObj = D('Comment');
		$appCommentObj->where("`app_id` in ({$delList})")->delete();
		if ($list !== false)
		{
			$this->success('已彻底删除！');
		}
		$this->error('操作失败！');
	}

	/*	 * *********************************************
	 * Function:       appsRecommendup
	 * Description:    推荐应用
	 * Input:
	 * Return:
	 * Others:
	 * Date：         2011-12-24
	 * Author：       xiaoguang
	 * ************************************************** */

	public function appsRecommendup()
	{
		//权限判断

		$recommenList = implode(",", $_POST['id']);
		$appCateObj = D('AppCategory');
		//取已经推荐的id;
		$list = $appCateObj->where("`app_id` in ({$recommenList}) and `cate_id`='1' ")->select();
		foreach ($list as $v)
		{
			$allready[] = $v['app_id'];
		}
		$recommenList = isset($allready) ? array_diff($_POST['id'], $allready) : $_POST['id'];
		if (!empty($recommenList))
		{
			foreach ($recommenList as $k => $v)
			{
				$data[$k]['app_id'] = $v;
				$data[$k]['cate_id'] = '1';
			}
			$data = array_values($data);

			$list = $appCateObj->addAll($data, array(), true);
			if ($list !== false)
			{
				$this->success('推荐成功！');
			}
			$this->error('推荐失败！');
		}
		//无需更新推荐
		$this->success('推荐成功！');
	}

	/*	 * *********************************************
	 * Function:       appsRecommenddown
	 * Description:    取消推荐应用
	 * Input:
	 * Return:
	 * Others:
	 * Date：         2011-12-24
	 * Author：       xiaoguang
	 * ************************************************** */

	public function appsRecommenddown()
	{

		$recommList = implode(",", $_POST['id']);
		;
		$appCateObj = D('AppCategory');

		$list = $appCateObj->where("`app_id` in ({$recommList}) and `cate_id`='1'")->delete();
		if ($list !== false)
		{
			$this->success('取消推荐成功！');
		}
		$this->error('取消推荐失败！');
	}

	public function appUpload()
	{
		$ieFlag = 'others';
		if(strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'msie') !== false)
		{
			$ieFlag = 'msie';
		}
		$type = Filter::act(Req::get('type', 'get'));
		$typeArr = explode('_', $type);
		$type = $typeArr[0];
		$app_id = $typeArr[1];
		$dir_type = $typeArr[2];
		$this->assign('ieFlag', $ieFlag);
		$this->assign('dir_type', $dir_type);
		$this->assign('app_id', $app_id);
		$this->assign('type', $type);
		$this->display();
	}

	public function appDeviceAdd()
	{
		$app_id = Filter::act($_POST['app_device_id']);
		$screen_focus = Filter::act($_POST['screen_focus']);
		$app_size = Filter::act($_POST['app_size']);
		$appDeviceTypeObj = D("AppDeviceType");
		//如果存在删除已有数据,需要定义是否保留不同分辨率
		$appDeviceTypeObj->where("`app_id`={$app_id}")->delete();
		$addData = array();
		$screen = Filter::act($_POST["apk_image"]);
		$icon = Filter::act($_POST["apk_icon"]);
		if(!empty($screen_focus))
		{
			foreach ($screen_focus as $k => $v)
			{
				$apk = Filter::act($_POST["apk_{$v}_file"]);

				if (empty($apk))//需要再定义
					break;
				$data = array(
					'app_id' => $app_id,
					'device_type_id' => 0, //需要再定义
					'apk_path' => $apk,
					'screenshots' => empty($screen) ? '' : implode(';', $screen),
					'app_size' => $app_size,
					'icon' => empty($icon) ? '' : $icon,
					'logo' => '',
					'pkg_name' => ''
				);
				$addData[$k] = $data;
			}
		}
		else
		{
			$addData[0] = array(
				'app_id' => $app_id,
				'device_type_id' => 0, //需要再定义
				'apk_path' => '',
				'screenshots' => empty($screen) ? '' : implode(';', $screen),
				'app_size' => $app_size,
				'icon' => empty($icon) ? '' : $icon,
				'logo' => '',
				'pkg_name' => ''
			);
		}

		$list = $appDeviceTypeObj->addAll($addData, array(), true);

		if ($list !== false)
			$this->success('添加成功！');

		//跳到列表页
		$this->assign('jumpUrl', U(APP_NAME . '://' . MODULE_NAME . '/appList'));
		$this->error('添加文件失败！可以在App列表为该应用添加文件。');
	}
}

?>
