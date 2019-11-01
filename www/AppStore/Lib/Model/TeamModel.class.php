<?php
class TeamModel extends Model{
	protected $tableName = 'team';
	protected $_link = array(
			'TeamHead' => array(
					'mapping_type'		=> HAS_ONE,
					'class_name'		=> 'TeamHead',
					'foreign_key'		=> 'uid',
					'mapping_name'		=> 'TeamHead',
					//'mapping_order'	=> 'create_time desc',
					'mapping_fields'	=> 'path'
			),
	);
	//获得日期 格式 Y-m-d H:i:s
	protected function getDateTime()
	{
		return date("Y-m-d H:i:s");
	}
	
	//生成随机数字10000-99999
	protected function getNumber()
	{
		return rand(10000, 99999);
	}
	
	}
?>