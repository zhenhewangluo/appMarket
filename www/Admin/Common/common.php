<?php
/*
 * 生成上传文件名函数
 */
function getUpFileName()
{
	return date('YmdHis').rand(10000, 99999);
}
?>
