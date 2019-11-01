<?php

class AjaxAction extends BaseAction
{

	public $_detail = array(
		1 => 'auto',
		2 => '240x320',
		3 => '320x480',
		4 => '480x800'
	);
	private $uploadPrefix = '';
	public function _initialize()
	{
		parent::_initialize();
		$this->uploadPrefix = '.' . C('UPLOAD_APP_PATH');
	}

	/**	 * ***********************************************
	 * Function:       validCateName()
	 * Description:    添加分类检测是否重名
	 * Input:          name
	 * Return:         array{suc:false/true}
	 * Others:
	 * Date：         2011-12-26
	 * Author：       xiaoguang
	 * *********************************************** */
	public function validCateName()
	{
		//先定义为false
		$data = array(
			'name' => 0,
		);
		//检测是否有重复值
		$name = trim(Filter::act($_POST['name']));
		$id = Filter::act($_POST['id']); //排除当前id
		$cateInfo = D('Category');


		if (!empty($id))
			$list = $cateInfo->where("`id`!={$id} and `name`='{$name}'")->find();
		else
			$list = $cateInfo->getByName($name);
		if (!empty($list))
		{
			$data = array(
				'name' => 1,
			);
		}
		echo json_encode($data);
	}

	/**	 * ***********************************************
	 * Function:       updateCateSelect()
	 * Description:    更新添加应用时分类下拉列表
	 * Input:          id
	 * Return:
	 * Others:
	 * Date：         2011-12-27
	 * Author：       xiaoguang
	 * *********************************************** */
	public function updateCateSelect()
	{
		//先定义为false
		$data = array(
			'list' => array(),
		);
		$id = Filter::act($_POST['id']);
		$cateInfo = D('Category');
		$list = $cateInfo->field('id,name')->where("`parent_id`={$id}")->findAll();
		if (!empty($list))
		{
			$data['list'] = $list;
		}
		echo json_encode($data);
	}

	/**	 * ***********************************************
	 * Function:       validAppName()
	 * Description:    添加app检测是否重名
	 * Input:          name
	 * Return:         array{suc:false/true}
	 * Others:
	 * Date：         2011-12-27
	 * Author：       xiaoguang
	 * *********************************************** */
	public function validAppName()
	{
		//先定义为false
		$data = array(
			'name' => 0,
		);
		//检测是否有重复值
		$name = trim(Filter::act($_POST['name']));
		$id = Filter::act($_POST['id']); //排除当前id
		$appInfo = D('Appinfo');

		if (empty($name))
		{
			die(json_encode($data));
		}
		if (!empty($id))
			$count = $appInfo->where("`app_id`!={$id} and `app_name`='{$name}'")->count();
		else
			$count = $appInfo->where("`app_name`='{$name}'")->count();
		if ($count > 0)
		{
			$data = array(
				'name' => 1,
			);
		}
		echo json_encode($data);
	}

	public function checkDir()
	{
		$data = array(
			'status' => 1,
			'msg' => ''
		);
		$dirTypeArr = array(
			'apk', 'screenshot', 'icon'
		);
		$typeArr = array(
			'1', '2', '3', '4', '0'
		);
		$appId = (int) Req::get('appId', 'get');
		$dir_type = Filter::act(Req::get('dir_type', 'get'));
		$type = Filter::act(Req::get('type', 'get'), 'text');
		if (!in_array($dir_type, $dirTypeArr) || !in_array($type, $typeArr))
		{
			$data['status'] = -1;
			$data['msg'] = '非法操作！';
			die(json_encode($data));
		}
		if($dir_type != 'apk')$this->_detail[$type] = '';

		$path = $this->uploadPrefix . $this->initPath($appId) . '/' . $dir_type . '/' . $this->_detail[$type];
//		$path = './' . C('APP_FILE_PATH') . $appId . '/' . $dir_type . '/' . $this->_detail[$type];
		import('ORG.Io.Dir');
		$dir = new Dir();
		//$apkPath_isDir = './TempFile/6016997/screenshot/240x320';
//		die(var_dump($path));
		if (is_dir($path))//
		{
			$data['status'] = -1;
			if (!$dir->isEmpty($path))
			{

				$data['msg'] = ( $dir_type == $dirTypeArr[0] ) ? '此应用的apk文件已存在，确定要覆盖吗？' : '此应用的截图文件已存在，确定要覆盖吗？';
			}
			else
			{
				$data['status'] = 1;
			}
		}
		die(json_encode($data));
	}

	public function clear_dir()
	{
		$data = array(
			'status' => -1,
			'msg' => '删除失败'
		);
		$dirTypeArr = array(
			'apk', 'screenshot', 'icon'
		);
		$typeArr = array(
			'1', '2', '3', '4', '0'
		);
		$appId = (int) Req::get('appId', 'get');
		$dir_type = Filter::act(Req::get('dir_type', 'get'));
		$type = Filter::act(Req::get('type', 'get'), 'text');
		if (!in_array($dir_type, $dirTypeArr) || !in_array($type, $typeArr))
		{
			$data['status'] = -1;
			$data['msg'] = '非法操作！';
			die(json_encode($data));
		}
		if($dir_type != 'apk')$this->_detail[$type] = '';

		$path = $this->uploadPrefix . $this->initPath($appId) . '/' . $dir_type . '/' . $this->_detail[$type];
		//$path = './' . C('APP_FILE_PATH') . $appId . '/' . $dir_type . '/' . $this->_detail[$type];
		import('ORG.Io.Dir');
		$dir = new Dir();
		$flag = false;
		if ($dir->isEmpty($path))
		{
			$data['status'] = 1;
			$data['msg'] = '删除成功';
		}
		if (is_dir($path) && !$dir->isEmpty($path))//
		{
			$flag = $dir->del($path);
			if ($flag == true)
			{
				$data['status'] = 1;
				$data['msg'] = '删除成功';
			}
		}
		//删除成功,如果文件信息存在数据库中，更新数据库
		if ($data['status'] == 1)
		{
			$appDeviceTypeObj = D('AppDeviceType');
			$list = $appDeviceTypeObj->where("app_id={$appId}")->select();
//			$resolution = array('1' => 'auto', '2' => '/240x320/', '3' => '/320x480/', '4' => '/480x800/');
//			$fieldName = $dir_type == 'apk' ? array('apk_path', 'screenshots') : array('screenshots', 'apk_path');
			$resolution = '/auto/';
			$resolutionArr = array(
				'1'	=> '/auto/',
				'2' => '/240x320/',
				'3' => '/320x480/',
				'4' => '/480x800/'
			);
			foreach($resolutionArr as $k => $v)
			{
//				if(strpos($v[$fieldName[0]],$resolution)!==false)
				if(strpos($path, $v) !== false)
				{
					$resolution = $v;
				}
			}
			foreach ($list as $k => $v)
			{

				if (strpos($v[$fieldName[0]], $resolution[$type]) !== false)
				{//找到该列
					if (empty($v[$fieldName[1]]))//另一列为空,删除，待定义？还删除了其他信息
						$appDeviceTypeObj->where("id={$v['id']}")->delete();
					else
					{ //另一列不为空,更新
						$appDeviceTypeObj->where("id={$v['id']}")->setField($fieldName[0], '');
					}
					//无需判断返回，因文件已经删除
				}
				if($dir_type == 'apk')
				{
					if(strpos($v['apk_path'],$resolution) !== false)
					{
						$appDeviceTypeObj->where("id={$v['id']}")->delete();
					}
				}
				else
				{
					if(strpos($v[$dir_type],';') !== false)//多个图片,删一个
					{
						$imglist = explode(';',$v[$dir_type]);
						unset($imglist[array_search($path,$imglist)]);
						$appDeviceTypeObj->where("id={$v['id']}")->setField($dir_type,implode(';',$imglist));
					}
					else//单个图
					{
						if(empty($v['apk_path']))//另一列为空,删除，待定义？还删除了其他信息
						{
							$appDeviceTypeObj->where("id={$v['id']}")->delete();
						}
						else
						{	//另一列不为空,更新
							$appDeviceTypeObj->where("id={$v['id']}")->setField($dir_type,'');
						}
					}
				}
			}
		}
		die(json_encode($data));
	}

	public function addApp()
	{
		$return = array(
			'status' => -1,
			'msg' => '添加失败'
		);
		$data = array(
			//appinfo
			'app_name' => Filter::act(Req::get('app_name')),
			'app_version' => Filter::act(Req::get('app_version')),
			'app_price' => Filter::act(Req::get('app_price')),
			'app_slogan' => Filter::act(Req::get('app_slogan')),
			'app_desc' => Filter::act(Req::get('app_desc'), 'text'),
			'app_visible' => Filter::act(Req::get('app_visible')),
			'is_english' => Filter::act(Req::get('is_english')),
//			//自动填
			'app_create_date' => date("Y-m-d H:i:s"),
			'app_update_date' => date("Y-m-d H:i:s"),
			'author_name' => Filter::act(Req::get('app_author')),
			'author_id' => 0,
			'source' => ($this->_usertype == 1 ? 'Admin' : 'Developer'), //临时
			'app_downloads' => 0,
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
				array(
					'cate_id' => Filter::act(Req::get('appcate')),
					'order' => 99
				),
				array(
					'cate_id' => 1, //推荐 id=1
					'order' => 99
				)
			); //因为HAS_MANY 必须加两级array
		}
		else
		{
			$data['AppCategory'] = array(
				array(
					'cate_id' => Filter::act(Req::get('appcate')),
					'order' => 99
				)
			); //因为HAS_MANY 必须加两级array
		}

		$appinfoObj = D('Appinfo');
		$appId = $appinfoObj->where("`app_name`='{$data['app_name']}'")->find();
		if (!empty($appId))
		{
			$return['msg'] = 'APP名称已经存在，添加失败';
			die(json_encode($return));
		}
		$appId = $appinfoObj->relation('AppCategory')->add($data, array(), true); //覆盖
		if ($appId !== false)
		{
			//如果存在 删除原来已有app子分类，
			$cateObj = D('Category');
			$childCatelist = $cateObj->getChildCategorys();
			$childCatelist = implode(',', $childCatelist);
			$appcateObj = D('appCategory');
			$result = $appcateObj
							->where("`app_id`='{$appId}' and `cate_id`!='{$data['AppCategory'][0]['cate_id']}' and `cate_id` in ({$childCatelist})")->delete();

			$return['status'] = 1;
			$return['msg'] = '添加成功，请上传APK和截图';
			$return['app_id'] = $appId;
			//添加appinfo成功
//			 $this->assign('jumpUrl',  U(APP_NAME.'://'.MODULE_NAME.'/appAdd'));
//			 $this->success('已经上传，正在跳转！');
		}
//		 $this->assign('jumpUrl',  U(APP_NAME.'://'.MODULE_NAME.'/appAdd'));
//		 $this->error('上传失败，正在跳转！');
		die(json_encode($return));
	}

	public function editApp()
	{
		$return = array(
			'status' => -1,
			'msg' => '修改失败'
		);
		$data = array(
			//appinfo
			'app_id' => Filter::act(Req::get('app_id')),
			'app_name' => Filter::act(Req::get('app_name')),
			'app_version' => Filter::act(Req::get('app_version')),
			'app_price' => Filter::act(Req::get('app_price')),
			'app_slogan' => Filter::act(Req::get('app_slogan')),
			'app_desc' => Filter::act(Req::get('app_desc')),
			'app_visible' => Filter::act(Req::get('app_visible')),
			'is_english' => Filter::act(Req::get('is_english')),
//			//自动填
			'app_create_date' => date("Y-m-d H:i:s"),
			'app_update_date' => date("Y-m-d H:i:s"),
			'author_name' => Filter::act(Req::get('app_author')),
			'author_id' => 0,
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
		//更新取消重名判断
//		$appId = $appinfoObj->where("`app_name`='{$data['app_name']}'")->find();
//
//		if(!empty($appId))
//		{
//			$return['msg'] = 'APP名称已经存在，添加失败';
//			die(json_encode($return));
//		}
		$appId = $appinfoObj->relation('AppCategory')->save($data, array(), true); //覆盖
		if ($appId == 1)//更新成功返回1
		{
			//如果存在 删除原来已有app子分类，
			$cateObj = D('Category');
			$childCatelist = $cateObj->getChildCategorys();
			$childCatelist = implode(',', $childCatelist);
			$appcateObj = D('AppCategory');
			$result = $appcateObj
							->where("`app_id`='{$data['app_id']}' and `cate_id`!='{$data['AppCategory'][0]['cate_id']}' and `cate_id` in ({$childCatelist})")->delete();
			$return['status'] = 1;
			$return['msg'] = '修改成功，请修改APK和截图';
			$return['app_id'] = $data['app_id'];
		}
		die(json_encode($return));
	}
	private function initPath($path)
	{
		$path = sprintf("%012d", $path);
        $path1 = substr($path, 0, 3);
        $path2 = substr($path, 3, 3);
        $path3 = substr($path, 6, 3);
		$path4 = substr($path, 9, 3);
		return $path1 . '/' . $path2 . '/' . $path3 . '/' . $path4;
	}
}

?>
