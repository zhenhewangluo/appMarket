<?php

$isMemcacheStart = 1;

$AM_MEMCACHE = array(	
	"am_device_type" => array("AmDeviceType", 3600 ,$isMemcacheStart),   //通过screen,sdk自适应来查询app_device_type_id,（前缀，缓存时间，是否开启MEMCACHE）
	"am_category" => array("AmCategory", 3600*24 ,$isMemcacheStart),   //通过screen,sdk,版权来查询分类
	"am_topic" => array("AmTopic", 3600*24 ,$isMemcacheStart),   //通过screen,sdk,版权来查询主题目
	"am_download_history" => array("AmDownloadHistory", 3600*24 ,$isMemcacheStart),   //通过screen,sdk,版权来查询历史记录
	"am_appinfo" => array("AmAppinfo", 3600*24 ,$isMemcacheStart),   //通过screen,sdk,版权来查询信息表
	"am_hotwords" => array("AmHotwords", 3600*24 ,$isMemcacheStart),   //查询am_hotwords表，
	"search" => array("AmSearch", 3600*24 ,$isMemcacheStart),   //查询am_appinfo表，
	"get_related_applist" => array("AmGetRelatedApplist", 3600*24 ,$isMemcacheStart),   //得到get_related_applist相关软件
	"am_rank" => array("AmRank", 3600 ,$isMemcacheStart),   //查询am_rank表，
	"thunder_am" => array("ThunderAm", 3600*24 ,$isMemcacheStart),   //thunder/所有，
	"NULL" => array()
);

?>

