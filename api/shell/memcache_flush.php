<?php
	/************************************************************
	 *   清空所有缓存
     *   Author: liu jingqi
	 *   Create Time: 2010-12-30
	 *************************************************************/
	header("Content-Type:text/html;charset=utf-8");
	require("../inc/config.inc.php");	
	$memobj->flush();
	
	echo $memobj->getVersion();
	echo "<br>--<br>";
	print_r($memobj->getStats());
	echo "<br>--<br>";
	print_r($memobj->getServerStatus());
	echo "<br>--<br>";
	print_r($memobj->getExtendedStats());
?>
