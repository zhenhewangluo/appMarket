<?php
class IndexAction extends BaseAction
{
//	private $_layout = 'Layout:layout'; //当前模块模板页
	private $_blockbar = '';
	protected function _initialize()
	{
		//$this->_blockbar = GROUP_NAME . ':' . 'Layout' . C('TMPL_FILE_DEPR') . 'blockbar';
		$this->_blockbar = C('TMPL_FILE_DEPR') == '/' ? TEMPLATE_PATH . C('TMPL_FILE_DEPR') . 'Layout/blockbar.html' : 'Layout:blockbar';
		$this->assign('blockbar', $this->_blockbar);
		parent::_initialize();
	}
	public function aboutUs()
	{
		$this->assign('blockBarTitle', '关于我们');
		$this->assign('viewcontent', $this->_tplPrefix . 'aboutUs');
		$this->display($this->_layout);
	}
	public function agreement()
	{
		$this->assign('blockBarTitle', '服务条款');
		$this->assign('viewcontent', $this->_tplPrefix . 'agreement');
		$this->display($this->_layout);
	}
	public function devNotes()
	{
		$this->assign('blockBarTitle', '开发者须知');
		$this->assign('viewcontent', $this->_tplPrefix . 'devNotes');
		$this->display($this->_layout);
	}
	public function disclaimer()
	{
		$this->assign('blockBarTitle', '免责声明');
		$this->assign('viewcontent', $this->_tplPrefix . 'disclaimer');
		$this->display($this->_layout);
	}
	public function hr()
	{
		$this->assign('blockBarTitle', '关于我们');
		$this->assign('viewcontent', $this->_tplPrefix . 'hr');
		$this->display($this->_layout);
	}
	public function contactUs()
	{
		$this->assign('blockBarTitle', '联系我们');
		$this->assign('viewcontent', $this->_tplPrefix . 'contactUs');
		$this->display($this->_layout);
	}
}
?>