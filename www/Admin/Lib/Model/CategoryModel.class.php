<?php
class CategoryModel extends RelationModel
{
	public $_msg = '';  //调试使用返回错误信息
	protected $dbName = '_v2_android';//指定数据库名称

	public function getCategorys($order = '`order` ASC')
	{
		return $this->order ($order)->findall ();
	}
	public function getCateTree()
	{
		$appCateRow = $this->getCategorys('`order` DESC'); //查找所有分类信息
		Load('extend');//载入函数扩展
		return list_sort_by(list_to_tree($appCateRow, 'id', 'parent_id', '_child', 0), 'order');//数组转树并排序
	}
	public function getChildCategorys()
	{
		$list = $this->where('parent_id > 1')->findall();
		foreach($list as $v)
		{
			$result[]=$v['id'];
		}
		return $result;
	}
	/**
	 * 根据分类名称获取分类id
	 * @method	getCidByCateName()
	 * @access public
	 * @param	string	$cateName
	 * @return mixed
	 * @author	xuhao
	 * @date	2012-3-1
	 */
	public function getCidByCateName($cateName)
	{
		return $this->field('id')->where("name = '{$cateName}'")->find();
	}
}
?>
