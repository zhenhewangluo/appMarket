<?php
class PointMallAction extends AppBaseAction
{
//	private $_layout = 'Layout:layout'; //当前模块模板页
	protected function _initialize()
	{
		parent::_initialize();
	}
	public function index()
	{
		$this->error('积分商城建设中。。。。。。');die;
		$this->assign('viewcontent', $this->_tplPrefix . 'index');
		$this->display($this->_layout);
	}
}
?>
