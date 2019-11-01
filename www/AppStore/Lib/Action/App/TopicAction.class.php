<?php
/**
 +------------------------------------------------------------------------------
 * @description:	专题页控制器
 +------------------------------------------------------------------------------
 * @others:			NULL
 * @file:			TopicAction.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 +------------------------------------------------------------------------------
 */
class TopicAction extends AppBaseAction
{
//	private $_layout = 'Layout:layout';//当前模块模板页
	protected function _initialize()
	{
		parent::_initialize();
	}
	/**
     +----------------------------------------------------------
     * 显示专题页内容
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
	public function index()
	{
		$this->assign('viewcontent', $this->_tplPrefix . 'index');
		$this->display($this->_layout);
	}
}
?>