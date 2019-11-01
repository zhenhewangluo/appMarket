<?php
/**
 +------------------------------------------------------------------------------
 * @description:	应用评论Model
 +------------------------------------------------------------------------------
 * @ohters:			NULL
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
     * @description:	根据应用id获取应用评论信息
	 * @method:		getAppCommentById()
     +----------------------------------------------------------
     * @access			public
     +----------------------------------------------------------
     * @param:			int	$aid
     +----------------------------------------------------------
     * @return:		array
     +----------------------------------------------------------
	 * @date:			2011-12-8
     +----------------------------------------------------------
	 * @author:		xuhao
     +----------------------------------------------------------
     */
	public function getAppCommentsByAid($aid)
	{
		//return $this->field('app_id')->relation('Comment')->where("app_id = {$aid}")->find();
		return $this->field('user_name, create_time, score_www, content')->where("app_id = {$aid}")->findAll();
	}
	/**
     +----------------------------------------------------------
     * @description:	添加应用评论
	 * @method:		addAppComment()
     +----------------------------------------------------------
     * @access			public
     +----------------------------------------------------------
     * @param:			array	$data
     +----------------------------------------------------------
     * @return:		boolean
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
     * @description:	根据评论id更新应用评论信息
	 * @method:		updateAppCommentByCommentId()
     +----------------------------------------------------------
     * @access			public
     +----------------------------------------------------------
     * @param:			int	$commentId
     +----------------------------------------------------------
     * @return:		mixed
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
     * @description:	根据应用id获取应用评分信息
	 * @method:		getAppScoreDetailByAid()
     +----------------------------------------------------------
     * @access			public
     +----------------------------------------------------------
     * @param:			int	$aid
     +----------------------------------------------------------
     * @return:		array
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
     * @description:	根据用户id和应用id获取应用评论信息
	 * @method:		getAppCommentByUidAndAid()
     +----------------------------------------------------------
     * @access:		public
     +----------------------------------------------------------
     * @param:			int	$uid
	 * @param:			int $aid
	 * @param:			string $fields
     +----------------------------------------------------------
     * @return:		mixed
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
