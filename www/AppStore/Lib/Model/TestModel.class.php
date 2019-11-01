<?php
class TestModel extends Model
{
	protected $dbName = 'test';//指定数据库名称
	protected $trueTableName = '`test`.email';
	public function testSaveAll($data)
	{
		var_dump($data);
		var_dump($this->where(1)->save($data));
		var_dump($this->getLastSql());
//		return $this->getError();
		return $this->getLastSql();
	}
}
?>
