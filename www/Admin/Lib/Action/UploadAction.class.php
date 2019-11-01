<?php

class UploadAction extends BaseAction
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
		if (isset($_POST["PHPSESSID"]) && isset($_POST["ieFlag"]) && $_POST["ieFlag"] != 'msie')
		{
//			Session::destroy();
//			session_id($_POST["PHPSESSID"]);
//			Session::start();
		}
		parent::_initialize();
		$this->uploadPrefix = '.' . C('UPLOAD_APP_PATH');
		//setcookie("FlashCookie", $datetime, time() + 1800);
//		if (isset($_POST["PHPSESSID"]))
//		{
//			session_id($_POST["PHPSESSID"]);
//		}
//		else if (isset($_GET["PHPSESSID"]))
//		{
//			session_id($_GET["PHPSESSID"]);
//		}
//		$datetime = date("M j, Y g:i:s.u");
//		setcookie("FlashCookie", $datetime, time() + 1800);
	}

	public function uploadApk()
	{
//		parent::_initialize();

//		if (isset($_REQUEST['PHPSESSID']))
//		{
//			session_id($_REQUEST['PHPSESSID']);	// 调用 session_id function 放入 session id
//		}
//		if (!empty($_FILES))
//		{
//			$tempFile = $_FILES['Filedata']['tmp_name'];
//			$targetPath = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
//			$targetFile = str_replace('//', '/', $targetPath) . $_FILES['Filedata']['name'];
//
//			// $fileTypes  = str_replace('*.','',$_REQUEST['fileext']);
//			// $fileTypes  = str_replace(';','|',$fileTypes);
//			// $typesArray = split('\|',$fileTypes);
//			// $fileParts  = pathinfo($_FILES['Filedata']['name']);
//			// if (in_array($fileParts['extension'],$typesArray)) {
//			// Uncomment the following line if you want to make the directory if it doesn't exist
//			// mkdir(str_replace('//','/',$targetPath), 0755, true);
//
//			move_uploaded_file($tempFile, $targetFile);
//			echo str_replace($_SERVER['DOCUMENT_ROOT'], '', $targetFile);
//			// } else {
//			// 	echo 'Invalid file type.';
//			// }
//		}
////		if(isset($_REQUEST['PHPSESSID'])) {
////			session_id($_REQUEST['PHPSESSID']);    // 调用 session_id function 放入 session id
////		}
//		return '123123';
		$type = Filter::act(Req::get('type', 'post'));
		$app_id = (int) Req::get('app_id', 'post');
		$style = $this->_detail[$type];
		$this->upload('apk', $style, $app_id);
	}

	public function uploadImg()
	{

		$type = Filter::act(Req::get('type', 'post'));
		$app_id = (int) Req::get('app_id', 'post');
		$style = $this->_detail[$type];
		$this->upload('screenshot', $style, $app_id);
	}
	public function uploadIcon()
	{

		$type = Filter::act(Req::get('type', 'post'));
		$app_id = (int) Req::get('app_id', 'post');
		$style = $this->_detail[$type];
		$this->upload('icon', $style, $app_id);
	}

	/**
	 * 获取指定uid的头像规范存放目录格式
	 * 来源：Ucenter base类的get_home方法
	 *
	 * @param int $uid uid编号
	 * @return string 头像规范存放目录格式
	 */
	public function get_apk_path($aid)
	{
		$uid = sprintf("%012d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		return $dir1 . '/' . $dir2 . '/' . $dir3;
	}

	/**
	 * 在指定目录内，依据uid创建指定的头像规范存放目录
	 * 来源：Ucenter base类的set_home方法
	 *
	 * @param int $uid uid编号
	 * @param string $dir 需要在哪个目录创建？
	 */
	public function make_apk_path($aid, $dir = '.')
	{
		$uid = sprintf("%012d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		!is_dir($dir . '/' . $dir1) && mkdir($dir . '/' . $dir1, 0777);
		!is_dir($dir . '/' . $dir1 . '/' . $dir2) && mkdir($dir . '/' . $dir1 . '/' . $dir2, 0777);
		!is_dir($dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3) && mkdir($dir . '/' . $dir1 . '/' . $dir2 . '/' . $dir3, 0777);
	}

	/**
	 * 获取指定uid的头像文件规范路径
	 * 来源：Ucenter base类的get_avatar方法
	 *
	 * @param int $uid
	 * @param string $size 头像尺寸，可选为'big', 'middle', 'small'
	 * @param string $type 类型，可选为real或者virtual
	 * @return unknown
	 */
	public function get_apk_filepath($uid, $size = 'big', $type = '')
	{
		$size = in_array($size, array('big', 'middle', 'small')) ? $size : 'big';
		$uid = abs(intval($uid));
		$uid = sprintf("%012d", $uid);
		$dir1 = substr($uid, 0, 3);
		$dir2 = substr($uid, 3, 2);
		$dir3 = substr($uid, 5, 2);
		$typeadd = $type == 'real' ? '_real' : '';
		return $dir1 . '/' . $dir2 . '/' . $dir3 . '/' . substr($uid, -2) . $typeadd . "_avatar_$size.jpg";
	}

	protected function upload($type = 'apk', $style='240x320', $app_id)
	{
		$return = array(
			'status' => 1,
			'src'	=> ''
		);
		import('ORG.Net.UploadFile');
		import('ORG.Io.Dir');
		$dir = new Dir;
		$upload = new UploadFile();
		//设置上传文件大小,1M
		$upload->maxSize = 104900000000;
		$upload->saveRule = 'getUpFileName';
		//设置上传文件类型
		//$upload->allowExts = explode(',', 'jpg,gif,png,jpeg');
		//设置附件上传目录
		if($type != 'apk')
			$style = '';
		else
			$style .= '/';
		$upload->savePath = $this->uploadPrefix. $this->initPath($app_id) . '/' . $type . '/' . $style;
		if(!is_dir($upload->savePath))
		{
			if(!mk_dir($upload->savePath, 0766, true))
			{
				$return['status'] = -1;
				$return['src'] = '创建' . $upload->savePath . '文件夹失败！';
				die(json_encode($return));
			}
		}
//		$upload->savePath = __ROOT__ . $app_id . '/' . $type . '/' . $style . '/';
		//设置需要生成缩略图，仅对图像文件有效
		$upload->thumb = false;
		// 设置引用图片类库包路径
		$upload->imageClassPath = 'ORG.Util.Image';
		//设置需要生成缩略图的文件后缀
		$upload->thumbPrefix = 'm_';  //生产1张缩略图
		//设置缩略图最大宽度
		$upload->thumbMaxWidth = '120,100';
		//设置缩略图最大高度
		$upload->thumbMaxHeight = '120,100';
		//设置上传文件规则
		//$upload->saveRule = 'uniqid';  //????
		//删除原图
		$upload->thumbRemoveOrigin = true;

		if (!$upload->upload())
		{
			//捕获上传异常
			echo $upload->getErrorMsg();
			//$this->error($upload->getErrorMsg());
		}
		else
		{
//			echo '123';
//			die;
			//取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo();
			if ($type == 'screenshot')
			{
				echo ltrim($this->initPath($app_id) . '/' . $type . '/' . $style . $uploadList[0]['savename'], '.');
			}
			else
			{
				echo ltrim($this->initPath($app_id) . '/' . $type . '/' . $style . $uploadList[0]['savename'], '.');
			}

//			import("ORG.Util.Image");
//			//给m_缩略图添加水印, Image::water('原文件名','水印图片地址')
////            Image::water($uploadList[0]['savepath'].'m_'.$uploadList[0]['savename'], '../Public/Images/logo2.png');
//			$_POST['image'] = 'm_' . $uploadList[0]['savename'];
		}
	}
	public function delTempFile()
	{

		$data = array(
			'status'	=> -1,
			'msg'		=> '删除失败！',
		);
		$path = Filter::act(Req::get('path', 'get'));
		$appId = (int)Req::get('appId', 'get');
		$type = Filter::act(Req::get('type', 'get'));
		if(file_exists($this->uploadPrefix.$path))
		{
			if(unlink($this->uploadPrefix.$path))
			{
				$data['status'] = 0;
				$data['msg'] = '删除成功！';
			}
		}
		else //不存在，返回成功，为了可以更新页面和保存更新数据库
		{
			$data['status'] = 0;
			$data['msg'] = '删除成功！';
		}

		//删除成功,如果文件信息存在数据库中，更新数据库
		if($data['status'] == 0 && $appId != 0 && !empty($path))//只有更新时会进入此处
		{
//			if(substr($path,-3)=='apk')
//				$dir_type='apk';
//			else
//				$dir_type='screenshot';
//			$tempArray=explode('/',$path);
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

			$appDeviceTypeObj = D('AppDeviceType');
			$list = $appDeviceTypeObj->where("app_id={$appId}")->select();

//			$fieldName = $type=='apk'?array('apk_path','screenshots'):array('screenshots','apk_path');
//			$fieldName = array();
//			if($type == 'apk') $type == 'apk_path';
			foreach($list as $k=>$v)
			{
				if($type == 'apk')
				{
					if(strpos($v['apk_path'],$resolution)!==false)
					{
						$appDeviceTypeObj->where("id={$v['id']}")->delete();
					}
				}
				else
				{
					if(strpos($v[$type],';')!==false)//多个图片,删一个
					{
						$imglist = explode(';',$v[$type]);
						unset($imglist[array_search($path,$imglist)]);
						$appDeviceTypeObj->where("id={$v['id']}")->setField($type,implode(';',$imglist));
					}
					else//单个图
					{
						if(empty($v['apk_path']))//另一列为空,删除，待定义？还删除了其他信息
						{
							$appDeviceTypeObj->where("id={$v['id']}")->delete();
						}
						else
						{	//另一列不为空,更新
							$appDeviceTypeObj->where("id={$v['id']}")->setField($type,'');
						}
					}
				}
			}
		}
		die(json_encode($data));
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
