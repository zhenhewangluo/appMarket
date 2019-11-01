<?php
/**
 * 夺宝活动控制器
 * @file	TestAction.class.php
 * @name	TestAction
 * @author	xuhao
 * @desc	夺宝活动控制器
 * @date	2011-12-08
 */
class TestAction extends ActivityBaseAction
{
	protected function _initialize()
	{
		parent::_initialize();
		$this->_layout = 'Layout:activity';
	}

	/**
	 * 夺宝活动主页展示
	 * @method	index()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-2-6
	 */
	public function index()
	{

		$this->getVoteAllList();

		$dbUserinfo = $this->getUserInfo();
		$this->assign('dbUserinfo', $dbUserinfo['user']);
		$this->display($this->_tplPrefix . 'index');


	}

	/**
	 * 获取投票信息
	 * @method	getActiveInfo()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-28
	 */
	public function getVoteInfo()
	{
		$this->postCurl('DUser', 'userinfo', $data);
//		var_dump($result);
		return $result;
	}
	
	/**
	 * 获取投票信息
	 * @method	getActiveInfo()
	 * @access public
	 * @param	void
	 * @return void
	 * @author	xuhao
	 * @date	2012-3-28
	 */
	public function guestPhoneBook()
	{
//		die('123123');
		$this->display($this->_tplPrefix . 'guestPhoneBook');
	}
	public function uploadPhoneBook()
	{
		$this->_return['isError'] = true;
		import("ORG.Net.UploadFile");
		$upload = new UploadFile();
		//设置上传文件大小,50M 测试！
		$upload->maxSize = 50 * 1049000; //50M 测试！
		//设置上传文件类型
		$upload->allowExts = explode(',', 'csv,CSV');
		//设置附件上传目录
		$upload->savePath = C('PHONEBOOK_PATH') . $this->initPath($this->_uid);
		$upload->uploadReplace = true;
		if(!is_dir($upload->savePath))mk_dir($upload->savePath, 755);
		if (!$upload->upload())
		{
			//捕获上传异常
//			$this->error($upload->getErrorMsg());
			$this->_return['message'] = $upload->getErrorMsg();
			echo '<script type="text/javascript">parent.callbackPhoneBook('.json_encode($this->_return).');</script>';
			die;
		}
		else
		{
			//取得成功上传的文件信息
			$uploadList = $upload->getUploadFileInfo();
		}
//		flush();
		$file = fopen($uploadList[0]['savepath'] . $uploadList[0]['savename'],'r');
		$data = array(
			'userid'	=> $this->_uid,
			'password'	=> $this->_pwd,
			'users'		=> array()
		);
		$i = 0;
		while ($temp = fgetcsv($file)) {    //每次读取CSV里面的一行内容
			if($i == 0)
			{
				$i++;
				continue;
			}
			$data['users'][] = array(
				'toname'	=> iconv('GBK', 'UTF-8', trim($temp[0])),
				'phone'		=> enMaskPhone(iconv('GBK', 'UTF-8', trim($temp[1])), false),
			);
			$i++;
		}
//		die(var_dump($data));		
		$result = $this->postCurl('Buddy', 'importContactList', $this->arrToJson($data));
//		die(var_dump($result));
		$this->_return['isError'] = false;
		echo '<script type="text/javascript">parent.callbackPhoneBook('.json_encode($this->_return).');</script>';
		die;
	}
	private function initPath($path)
	{
		$path = sprintf("%012d", $path);
        $path1 = substr($path, 0, 3);
        $path2 = substr($path, 3, 3);
        $path3 = substr($path, 6, 3);
		$path4 = substr($path, 9, 3);
		return $path = $path1 . '/' . $path2 . '/' . $path3 . '/' . $path4 . '/';
	}
}
?>
