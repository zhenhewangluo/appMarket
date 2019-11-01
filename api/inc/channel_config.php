<?php
$REPO_ROOT = $_SERVER['REPO_ROOT'];
define("CHANGELOG", "1、增加新年元素，我们与你一起共庆新年；
2、优化设置选项，可以取消应用下载的声音提示；
3、提高了详情页打开的速度；
4、修复今日推荐无法即时显示的Bug；
5、修复滚轮滚动的Bug；
6、技术上的一些其他优化，提升软件速度。");

$CHANNEL_CONFIG_ARR = array();
//
$CHANNEL_MAP = array("10001"=>"commonpkg","10002"=>"commonpkg","10004"=>"commonpkg","71"=>"commonpkg","2"=>"commonpkg","25"=>"commonpkg");
//location rule
# location rule:
# xxxx_<app_version>_<channel_name>_<CHANNEL_MAP>.apk
$CHANNEL_CONFIG_ARR[0] = array();
$CHANNEL_CONFIG_ARR[1] = array(
                'channel_id'   => 1,
                'channel_name' => 'public_1',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => true,
                'need_english' => true,
                                );
$CHANNEL_CONFIG_ARR[2] = array(
				'channel_id'   => 2,
                'channel_name' => 'aisidi_2',
                'current_version' => '2000000',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[3] = array();
$CHANNEL_CONFIG_ARR[4] = array(
				'channel_id'   => 4,
                'channel_name' => 'dangle_4',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[5] = array();
$CHANNEL_CONFIG_ARR[6] = array(
				'channel_id'   => 6,
                'channel_name' => 'kaiqi_6',
                'current_version' => '2000000',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[7] = array();
$CHANNEL_CONFIG_ARR[8] = array(
				'channel_id'   => 8,
                'channel_name' => 'tw_8',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[9] = array(
				'channel_id'   => 9,
                'channel_name' => '360_9',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[10] = array();
$CHANNEL_CONFIG_ARR[11] = array(
				'channel_id'   => 11,
                'channel_name' => 'paojiao_11',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[12] = array();
$CHANNEL_CONFIG_ARR[13] = array();
$CHANNEL_CONFIG_ARR[14] = array();
$CHANNEL_CONFIG_ARR[15] = array();
$CHANNEL_CONFIG_ARR[16] = array();
$CHANNEL_CONFIG_ARR[17] = array(
				'channel_id'   => 17,
                'channel_name' => 'sina_17',
                'current_version' => '2000000',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);

$CHANNEL_CONFIG_ARR[18] = array(
                'channel_id'   => 18,
                'channel_name' => 'aisidiv1',
                'current_version' => '2000000',
                'app_version' => '2.1.5',
                'device_list' => '10002',
                'need_upgrade' => false,
                'need_english' => true,
                                );

$CHANNEL_CONFIG_ARR[19] = array();
$CHANNEL_CONFIG_ARR[20] = array(
                'channel_id'   => 20,
                'channel_name' => 'internal_20',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,2,25',
                'need_upgrade' => false,
                'need_english' => true,
                                );
$CHANNEL_CONFIG_ARR[21] = array(
				'channel_id'   => 21,
                'channel_name' => 'netease_21',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[23] = array(
				'channel_id'   => 23,
                'channel_name' => 'tencent_23',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[10002] = array(
				'channel_id'   => 10002,
                'channel_name' => 'gm_10002',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);


$CHANNEL_CONFIG_ARR[10005] = array(
				'channel_id'   => 10005,
                'channel_name' => 'jidongwang_10005',
                'current_version' => '2000000',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[10006] = array(
				'channel_id'   => 10006,
                'channel_name' => 'soujizhijia_10006',
                'current_version' => '2000000',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);


$CHANNEL_CONFIG_ARR[10020] = array(
				'channel_id'   => 10020,
                'channel_name' => 'kupai_10020',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
$CHANNEL_CONFIG_ARR[10021] = array(
				'channel_id'   => 10021,
                'channel_name' => 'miui_10021',
                'current_version' => '2011011401',
                'app_version' => '2.1.5',
                'device_list' => '10001,10002,10004,71,2,25',
                'need_upgrade' => false,
                'need_english' => true,
				);
?>

