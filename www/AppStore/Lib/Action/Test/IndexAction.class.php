<?php
class IndexAction extends Action
{
	/**
	 * 修改新增的app数据库中的logo，icon，apk_path，screenshots路径
	 */
	public function changeAppPath()
	{
		ignore_user_abort(); // 后台运行
		set_time_limit(0); // 取消脚本运行时间的超时上限
		$appModel = new AppinfoModel;
		$appIdArr = $appModel->field('app_id')->where("relationid > 0")->select();
		$model = new TestModel;

		for($i = 0; $i < count($appIdArr); $i++)
		{
			$row = $model->field('id, apk_path, icon, logo, screenshots')->where('app_id = ' . $appIdArr[$i]['app_id'])->select();

			if(!empty($row))
			{
				foreach($row as $key => $val)
				{
					if(!empty($val) && substr($val['apk_path'], 0, 1) != '0')
					{
						foreach($val as $k => $v)
						{
							$data[$k] = $this->reWritePath($v);
						}
						unset($data['id']);
						$status = $model->where(array('id' => $val['id']))->save($data);
						$status = ($status > 0) ? 'true' : 'false';

						$message = "Sql : {$model->getLastSql()}@ Status : {$status}";
						C('LOG_FILE_SIZE',10240);
						Log::write($message, 'INFO', 3, LOG_PATH.'rPath_'.date('y_m_d').$status.".log");
					}
				}
			}
		}
	}

	private function reWritePath($pathStr)
	{
		if(strlen(trim($pathStr)) <= 0)
		{
			return $pathStr;
		}
		$temp = array();
		if(strpos($pathStr, ';') !== false)
		{

			$data = array();
			$pathArr = explode(';', $pathStr);
			foreach($pathArr as $k => $v)
			{
				$temp = $this->pathToArr($v);
				$this->changePath(&$temp[0]);

				$data[$k] = implode('/', $temp);
			}
			return implode(';', $data);
		}
		else
		{
			$temp = $this->pathToArr($pathStr);
			$this->changePath(&$temp[0]);
			return implode('/', $temp);
		}
	}
	private function pathToArr($path)
	{
		$return = array();
		$pathArr = explode('/', $path);
		$return[0] = array_shift($pathArr);
		$return[1] = implode('/', $pathArr);
		return $return;
	}
	private function changePath($path)
	{
		$path = sprintf("%012d", &$path);
        $path1 = substr($path, 0, 3);
        $path2 = substr($path, 3, 3);
        $path3 = substr($path, 6, 3);
		$path4 = substr($path, 9, 3);
		$path = $path1 . '/' . $path2 . '/' . $path3 . '/' . $path4;
	}
	//以上为修改数据库app文件目录
	//以下为修改数据库app文件分类
	public function changeCate()
	{

		ignore_user_abort(); // 后台运行
		set_time_limit(0); // 取消脚本运行时间的超时上限
		$file = fopen('./123.csv','r');
		$data = array();
		$i = 0;
		while ($temp = fgetcsv($file)) {    //每次读取CSV里面的一行内容
			$data[$i]['app_name'] = iconv('GBK', 'UTF-8', trim($temp[0]));
			$data[$i]['cate_id'] = (int)iconv('GBK', 'UTF-8', trim($temp[1]));
			$i++;
//			print_r($temp); //此为一个数组，要获得每一个数据，访问数组下标即可
		}
		unset($data[0]);
		$appInfoObj = new AppinfoModel;
		$appCateObj = new AppCategoryModel;
		foreach($data as $k => $v)
		{
			$appInfoRow = $appInfoObj->getAppInfoByAppName($v['app_name']);
//			var_dump($appInfoObj->getLastSql());
//			die(var_dump($appInfoRow));
			if(!empty($appInfoRow))
			{
				$saveData = array('cate_id' => $v['cate_id']);
				$where = array(
					'cate_id' => array('in', '50,51,52,53,54,55,56,57,60,61,62,63,64,65,66,67,68,69')
				);
				$status = $appCateObj->updateAppCidByAid($appInfoRow['app_id'], $saveData, $where);
//				die($appCateObj->getLastSql());
				$status = ($status == false) ? 'false' : 'true';
				$message = "Status : {$status}@ Sql : {$appCateObj->getLastSql()}";
				C('LOG_FILE_SIZE',10240);
				if($status == false)Log::write($message, 'INFO', 3, LOG_PATH.'cCateW_'.date('y_m_d').".log");
				Log::write($message, 'INFO', 3, LOG_PATH.'cCate_'.date('y_m_d').".log");
			}
		}
	}
	public function testMsg()
	{
//		$name = iconv('UTF-8', 'GBK', '玖易网');
//		die(var_dump(md5('18660102869短信测试！cnJs4;Jvdskyhivc7wzji[+')));
		var_dump($this->postCurl('http://59.83.32.137/msg.php?m=Msg&a=sendMsg',array('username' => 'skyhi', 'password' => 'vc7wzji[+', 'key' => md5('18660102869短信测试！cnJs4;Jvdskyhivc7wzji[+'), 'mobile' => '18660102869', 'msg' => '短信测试！')));
	}
	/**
	 * POST发送数据
	 * @method	postCurl()
	 * @access private
	 * @param	string	$url	要post数据的地址
	 * @param	array	$data	要post的数据
	 * @return array
	 * @author	xuhao
	 * @date	2012-2-8
	 */
	private function postCurl($url, $data)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		$result = curl_exec($ch);
		curl_close($ch);
		return json_decode($result, true);
	}
	/**
	 * 拷贝已存在的文件
	 */
	public function copyAppFile()
	{
		ignore_user_abort(); // 后台运行
		set_time_limit(0); // 取消脚本运行时间的超时上限
		import('ORG.Io.Dir');
		$dir = new Dir();
		$appModel = new AppinfoModel;
		$model = new TestModel;
		$appIdArr = $appModel->field('app_id, relationid')->where("relationid > 0")->select();
//		die(var_dump(count($appIdArr)));
		foreach($appIdArr as $k => $v)
		{
//			if($v['relationid'] > 5842)
//			{
				$path = './download/res/' . $v['relationid'] . '/';
				$dest = './download/res/' . $this->initPath($v['relationid']) . '/';
				if(!is_dir($dest))mkdir($dest, 0766, 1);
	//			try
	//			{
				if(is_dir($path))
				{
					$dir->copyDir($path, $dest);
					$message = 'status : success ; form : ' . $path . ' & to : ' . $dest;
					Log::write($message, 'INFO', 3, LOG_PATH . 'copyOkPath_'.date('y_m_d').".log");
				}
				else
				{
					$message = 'status : failed ; form : ' . $path . ' & to : ' . $dest;
					Log::write($message, 'INFO', 3, LOG_PATH . 'copyPath_'.date('y_m_d').".log");
				}
//			}


//			}
//			catch(appException $e)
//			{
////				echo $e->getMessage();
////				die;
//				$message = 'status : failed ; form : ' . $path . ' & to : ' . $dest;
//				Log::write($message, 'INFO', 3, LOG_PATH . 'copyPath_'.date('y_m_d').".log");
//				$excemsg = $e->getMessage();
//				Log::write($excemsg, 'INFO', 3, LOG_PATH . 'excePath_'.date('y_m_d').".log");
//			}

		}
	}
	private function initPath($path)
	{
		$path = sprintf("%012d", &$path);
        $path1 = substr($path, 0, 3);
        $path2 = substr($path, 3, 3);
        $path3 = substr($path, 6, 3);
		$path4 = substr($path, 9, 3);
		return $path = $path1 . '/' . $path2 . '/' . $path3 . '/' . $path4;
	}
	public function testSave()
	{
		$data = array(
			array(
				'id'		=> 1,
				'name'		=> '1',
				'admin'		=> '1',
				'username'	=> '1',
				'password'	=> '1',
				'provider'	=> '1'
			),
			array(
				'id'		=> 2,
				'name'		=> '2',
				'admin'		=> '2',
				'username'	=> '2',
				'password'	=> '4',
				'provider'	=> '3'
			)
		);
		$onDuplicateData = array(
			'status'	=> 'status + 1'
		);
		$testModel = new TestModel();
		echo $testModel->addAll($data, array(), false, true, $onDuplicateData);
	}

}
?>