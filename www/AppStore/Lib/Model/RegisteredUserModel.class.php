<?php
/**
 * 用户数据表类
 * @file	RegisteredUserModel.class.php
 * @name	RegisteredUserModel
 * @author	xuhao
 * @desc	负责修改用户数据，除密码外的个人信息
 * @date	2012-3-20
 */
class RegisteredUserModel extends Model
{

	public function updateUserInfo($data)
	{
		if($this->save($data))
		{
			return true;
		}
		else
		{
			return false;
		}
	}
}

?>
