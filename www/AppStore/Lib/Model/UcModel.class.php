<?php
class UcMembersModel extends Model
{
	protected $dbName = 'ucenter';//指定数据库名称
	// 实际数据表名（包含表前缀）
    protected $trueTableName = 'uc_members';
	public function getUserStatusByUid($uid)
	{
		return $this->field('status')->where("uid = {$uid}")->find();
		$this->save();
	}
}
?>
