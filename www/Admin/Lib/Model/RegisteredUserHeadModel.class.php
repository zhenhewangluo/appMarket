<?php
class RegisteredUserHeadModel extends Model
{
	public $_msg = '';  //调试使用返回错误信息
	
			//head头像
	// 自动填充设置
	protected $_auto = array(	
		array('path', 'Public\images\UserFace\noavatar_big.gif,Public\images\UserFace\noavatar_middle.gif,Public\images\UserFace\noavatar_small.gif', self::MODEL_INSERT),	
		array('type', '3', self:: MODEL_INSERT),	
		array('uploaddate', 'getDateTime', self:: MODEL_INSERT, 'callback'),
);
	//获得日期 格式 Y-m-d H:i:s
	protected function getDateTime()
	{
		return date("Y-m-d H:i:s");
	}	
	public function addHead($data = array())
	{
		if(empty($data))
		{
			$this->_msg = '头像数据错误';
			return false;
		}
		return $this->add($data);
	}
	
	public function updateHeadByUid($uid, $data)
	{
		return $this->where("uid = {$uid}")->save($data);
	}
	
	public function getHeadByUid($uid, $field="type, path")
	{
		return $this->field($field)->where("uid = {$uid}")->find();
	}
	//更新或插入头像,类型2，上传文件
	    /***********************************************
    * Function:        updateHead
     * Description:    更新或插入头像,类型2，上传文件
     * Input:         头像信息array
     * Return:        true:不需要写入/其他：写入结果
     * Others:         
     * Date：         2011-12-19
     * Author：       xiaoguang
     ****************************************************/
	public function updateHead($data)
	{

		$row = $this->getHeadByUid($data['uid']);
		if(!empty($row))
		{
			//已经存在
			//类型已经是2 不写
			if($row['type']==2)
			{
			return true;
			}
				return $this->where("uid = {$data['uid']}")->save($data);
		}
		//注册时写默认头像失败时才会进此处
		return $this->add($data);	

	}	
}
?>
