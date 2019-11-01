<?php
/**
 +------------------------------------------------------------------------------
 * @description:	应用评论Model
 +------------------------------------------------------------------------------
 * @others:			NULL
 * @file:			CommentModel.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 +------------------------------------------------------------------------------
 */
class CommentModel extends Model
{
	public $dbName = '_v2_android';
	public $tableName = 'comment';
	/**
     +----------------------------------------------------------
     * 根据应用id获取应用评论信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int $aid 应用id
	 * @param boolean	$page 是否分页
	 * @param string $orderType 排序类型
	 * @param int $listRow 每页显示多少条
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function getAppCommentsByAid($aid, $page = false, $orderType = 'default', $listRow = 30, $fields = '')
	{
		if($fields == '')
		{
			$fields = 'user_id, user_name, create_time, score_www, content';
		}
		$where = "app_id = {$aid}";
		if($orderType == 'default') $order = 'create_time DESC';
		if($orderType == 'update') $order = 'app_update_date DESC';
		if($orderType == 'download') $order = 'app_downloads DESC';
		if(!$page)
			return $this->field($fields)->where($where)->order($order)->findAll();
		$p = isset($_GET['p']) && $_GET['p'] > 0 ? (int) $_GET['p'] : 1;
		import("ORG.Util.Page");

		$list = $this->field($fields)->where($where)->page($p . ',' . $listRow)->order($order)->select();

		if(empty($list)) return 0;
		
		$userIdArr = array();
		foreach($list as $k => $v)
		{
			$userIdArr[] = $v['user_id'];
		}
		$userObj = D('RegisteredUserHead');
		$userHead = $userObj->getHeadByUids($userIdArr);
		foreach($list as $k => $v)
		{
			foreach($userHead as $key => $val)
			{
				if($v['user_id'] == $val['uid'])
				{
					$list[$k]['headPath'] = $val['path'];
				}
			}
		}		
		$count = $this->where($where)->count();
		$Page = new Page($count, $listRow);
		
		$Page->setAjax($p);
		$Page->isSimple = true;
		$Page->setConfig('theme', '%upPage% %first% %linkPage% %end% %downPage%');
		$Page->setConfig('prev', '');
		$Page->setConfig('next', '');

		$show = $Page->show();

		return array('appList' => $list, 'pageShow' => $show);
	}
	/**
     +----------------------------------------------------------
     * 添加应用评论
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param array		$data 要添加的评论信息
     +----------------------------------------------------------
     * @return			boolean
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function addAppComment($data)
	{
		return $this->add($data);
	}
	/**
     +----------------------------------------------------------
     * 根据评论id更新应用评论信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int		$commentId 评论id
	 * @param array		$data 要更新的评论内容
     +----------------------------------------------------------
     * @return mixed
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function updateAppCommentByCommentId($commentId, $data)
	{
		return $this->where("id = {$commentId}")->save($data);
	}
	/**
     +----------------------------------------------------------
     * 根据应用id获取应用评分信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int	$aid 应用id
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function getAppScoreDetailByAid($aid)
	{
		$scoreData = array(
			'5' => 0,
			'4' => 0,
			'3' => 0,
			'2' => 0,
			'1' => 0,
			'0' => 0
		);
		$appScore = $this->field('COUNT(*) as num,score_www')->group('score_www')->order('score_www DESC')->where("app_id = {$aid}")->findAll();
		if(!empty($appScore))
		{
			foreach($appScore as $k => $v)
			{
				if($v['score_www'] >= 0 && $v['score_www'] <= 5)
				{
					$scoreData[$v['score_www']] = $v['num'];
				}
			}
		}
		
		return $scoreData;
	}
	/**
     +----------------------------------------------------------
     * 根据用户id和应用id获取应用评论信息
     +----------------------------------------------------------
     * @access public
     +----------------------------------------------------------
     * @param int	$uid 用户id
     * @param int $aid 应用id
     * @param string $fields 要查询的字段
     +----------------------------------------------------------
     * @return array
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function getAppCommentByUidAndAid($uid, $aid, $fields = 'id')
	{
		$commentArr = $this->where("user_id = {$uid} and app_id = {$aid}")->field($fields)->find();
		if(!empty($commentArr))
		{
			if($fields == 'id')
				return $commentArr['id'];
			
			return $commentArr;
		}
		return 0;
	}
}
?>
