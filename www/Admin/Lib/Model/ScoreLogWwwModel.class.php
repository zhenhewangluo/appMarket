<?php
/**
 * @description:	应用评分Model
 * @ohters:			NULL
 * @file:			ScoreLogWwwModel.class.php
 * @author:		xuhao
 * @date:			2011-12-08
 */
class ScoreLogWwwModel extends Model
{
	protected $dbName = '_v2_android';//指定数据库名称
	/**
	 * @method:		getAppCommentById()
	 * @description:	根据应用id进行评分，动作
	 * @param:			array $data
	 * @return:		boolean
	 * @date:			2011-12-8
	 * @author:		xuhao
	 */
	public function addAppScore($data)
	{
		return $this->add($data);
	}
}
?>
