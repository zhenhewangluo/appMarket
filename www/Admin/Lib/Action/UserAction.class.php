<?php
class UserAction extends BaseAction
{
	private $_layout = 'Layout:layout';
	public function _initialize()
	{
		parent::_initialize();
		if ($this->_uid == 0||$this->_usertype!=1 )
		{
			$this->assign('jumpUrl', U(APP_NAME . '://Sysuser/login'));
			$this->error('未登录或无权限，请重新登录！');
		}			
	}
	
	public function userList()
	{
		$order= "registered_time DESC";
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$userObj= D('RegisterUser');
		$list = $userObj->order("{$order}")
				->where("`status`=1")	
				->page($p.',20')
				->select();
		$count = $userObj->count();
		if(!empty($list))
		{

			$Page = new Page($count,20); 
			$show = $Page->show(); 
			$this->assign('userlist',$list); 
			$this->assign('page',$show); 
		}		
		
		$this->assign('viewcontent', MODULE_NAME . ':userList');
		$this->display($this->_layout);
	}

	public function userRecycleList()
	{
		$order= "registered_time DESC";
		$p = isset($_GET['p']) ? $_GET['p'] : 1;
		$userObj= D('RegisterUser');
		$list = $userObj->order("{$order}")
				->where("`status`!=1")
				->page($p.',20')
				->select();
		$count = $userObj->count();
		if(!empty($list))
		{

			$Page = new Page($count,20); 
			$show = $Page->show(); 
			$this->assign('userlist',$list); 
			$this->assign('page',$show); 
		}		
		
		$this->assign('viewcontent', MODULE_NAME . ':userRecycleList');
		$this->display($this->_layout);
	}
	public function userDel()
	{	
		$ids = Req::get('id');
		if(is_array($ids))
		{
			$delList = implode(",",$ids );
		}
		else
		{
			$delList = $ids;
		}

		$userinfoObj = D('RegisterUser');
		$list = $userinfoObj->where("`id` in ({$delList})")->save(array('status'=>0)); 
		if($list!==false)
		{
			$this->success('已移入回收站！');  
		}
		 $this->error('操作失败！');  		
	
	}

	public function userRecycleDel()
	{	
		$ids = Req::get('id');
		if(is_array($ids))
		{
			$delList = implode(",",$ids );
		}
		else
		{
			$delList = $ids;
		}
		$userinfoObj = D('RegisterUser');
		$list = $userinfoObj->where("`id` in ({$delList})")->relation('RegisteredUserHead' )->delete(); 
		if($list!==false)
		{
			$this->success('已彻底删除！');  
		}
		 $this->error('操作失败！'); 
	}	
	
	public function userRecycleRestore()
	{	
		$ids = Req::get('id');

		$delList = implode(",",$ids );

		$userinfoObj = D('RegisterUser');
		$list = $userinfoObj->where("`id` in ({$delList})")->save(array('status'=>1)); 
		if($list!==false)
		{
			$this->success('已还原！');  
		}
		 $this->error('操作失败！');  		
	
	}	
	
	public function userEdit()
	{
		$uid = (int) Req::get('uid', 'get');
		if($uid > 0)
		{
			$userObj = new RegisterUserModel();
			$userRow = $userObj->getUserInfoByUid($uid, 'id, name, email, phone, registered_time, status, mobile_ispublic, qq, qq_ispublic, weburl, weburl_ispublic, address, expressdelivery, email_ispublic');
			$this->assign('user', $userRow);
		}
		$this->assign('viewcontent', MODULE_NAME . ':userEdit');
		$this->display($this->_layout);
	}
	public function userSave()
	{
		$uid = (int) Req::get('id', 'post');
		$userObj = new RegisterUserModel;
		$data = $userObj->create();
		if($uid > 0 && $data)
		{
			$re = $userObj->relation(true)->save($data);
			if($re)
			{
				$this->success('编辑成功');die;
			}
			$this->error($userObj->getError());die;
		}
		if($data)
		{
			$re = $userObj->relation(true)->add($data);
			if($re)
			{
				$this->success('添加成功');die;
			}
			
		}
		if($userObj->getError())
		{
			$this->error($userObj->getError());die;
		}
		$this->error('编辑失败');die;
	}
}
?>
