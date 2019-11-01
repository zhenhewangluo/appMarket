<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of ValidateAction
 *
 * @author Administrator
 */
class ValidateAction
{
	private $_validateInfo = array(
		'username'	=> '昵称',
		'password'	=> '密码',
	);
	public static function checkValidPostInfo($data)
	{
		foreach($data as $k => $v)
		{
			if(array_key_exists($k, $this->_validateInfo))
			{
				
			}
		}
	}
}

?>
