<?php

/* * **********************************************************
 *   Interface 46
 *   获取分类列表 get_cate_list_new.php
 * 	 增加时间戳
 *   Author: xxxxxxxxx
 * *********************************************************** */
header("Content-Type:text/html;charset=utf-8");
require("./inc/init.php");
if ($AM_CURRENT_REQUEST["PROTO"] != 46)
{
	echo error2json("E002");
	die;
}

$current_channel_id = $AM_CURRENT_REQUEST["CHANNEL"];
$current_device = $AM_CURRENT_REQUEST["MODEL"];

//判断是否传值班screen,sdk---2010.11.11
if (!($screen = __getPost('screen_size')) || !($sdk = __getPost('sdk')))
{
	$conn = connect_comm_db();
	$res = mysql_query("select screen_size,sdk from am_terminal_info where mid=" . $mid, $conn);
	if (mysql_num_rows($res) == 0)
	{
		$screen = "480x800";
		$sdk = 4;
	}
	else
	{
		$result = mysql_fetch_assoc($res);
		$screen = $result['screen_size'];
		$sdk = $result['sdk'];
	}
	mysql_close($conn);
}

$conn = connect_db();
if ($conn === FALSE)
{
	echo error2json("S001");
	die;
}
//查询2010.11.11 xxxxxxxxx
$arrTestDeviceId = array();
$isflagexists = 0;
////memcache缓存
$keyName = $AM_MEMCACHE["am_device_type"][0] . $screen . $sdk;
if ($AM_MEMCACHE["am_device_type"][2])
{
	if (!($arrTestDeviceId = $memobj->get($keyName)))
		$isflagexists = 2;
}else
	$isflagexists = 1;
if ($isflagexists)
{
	$sqlD = "select app_device_type_id from am_device_type where screen='$screen' and sdk_version<='$sdk' and app_device_type_id>0";
	$rs = mysql_query($sqlD, $conn);
	while ($row = mysql_fetch_assoc($rs))
	{
		$arrTestDeviceId[] = $row['app_device_type_id'];
	}
	if ($isflagexists == 2)
		$memobj->set($keyName, $arrTestDeviceId, 0, $AM_MEMCACHE["am_device_type"][1]);
}
if (count($arrTestDeviceId) < 1)
	$arrTestDeviceId[] = 0;
////如果是TCL接口，判断正版

if (in_array($_SESSION["channel"], $xxxxcopyrightchekChannelid))
{
	$sqlTcl = ""; // " and am_appinfo.copyright=1 ";
	$xxxx_iscopyright = 1;
	$xxxxArrCategoryType['xxxx_9994']['name'] = "装机必备";
	$sql = "select count(*) as c from am_app_topic JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id
				WHERE am_appinfo.app_visible =1 $sqlTcl  AND am_app_topic.topic_id =102";

	$rs2 = mysql_query($sql, $conn);
	$row2 = mysql_fetch_assoc($rs2);
	$xxxxArrCategoryType['xxxx_9994']['appcount'] = $row2['c'];

	$xxxxArrCategoryType['xxxx_9995']['name'] = "TCL推荐";
	$xxxxArrCategoryType['xxxx_9995']['order'] = "1";
	$xxxxArrCategoryType['xxxx_9994']['order'] = "2";
	$xxxxArrCategoryType['xxxx_3']['order'] = "3";
	////TCL推荐个数
	$sql = "select count(*) as c from am_app_topic JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id
				WHERE am_appinfo.app_visible =1 AND am_app_topic.topic_id =103";
	$rs2 = mysql_query($sql, $conn);
	$row2 = mysql_fetch_assoc($rs2);
	$xxxxArrCategoryType['xxxx_9995']['appcount'] = $row2['c'];

	////如果是TCL
	$tcl_or = "or a.id=103 ";
}
if ($_SESSION["channel"] == 27)
{
	$xxxxArrCategoryType['xxxx_9994']['name'] = "硬件推荐";
	$xxxxArrCategoryType['xxxx_9994']['order'] = "3";
	$xxxxArrCategoryType['xxxx_9995']['order'] = "1";
	$xxxxArrCategoryType['xxxx_3']['order'] = "2";
}

if ($xxxxChannelidTopic93[$_SESSION["channel"]])
	$xxxxTopicName = $xxxxChannelidTopic93[$_SESSION["channel"]] . "推荐";
else
	$xxxxTopicName = "";

$updatetime = (__getPost('updatetime')) ? __getPost('updatetime') : 0;

////获取列表showlist 所有专题和分类的cateid与type对应关系
$arrTest = $list = $showlist = array();
$lnum = $knum = 0;
foreach ($xxxxArrCategoryType as $key => $value)
{
	$showlist[] = $value[0];
	if ($value[2] >= $updatetime)
	{
		$list[$lnum]['cateid'] = $value[0];
		$list[$lnum]['type'] = $value[1];
		$list[$lnum]['parentid'] = $value['parentid'];
		$list[$lnum]['name'] = $value['name'];
		$list[$lnum]['icon'] = ($value['icon']) ? $value['icon'] : "";
		$list[$lnum]['desc'] = $value['desc'];
		$list[$lnum]['update_interval'] = $value['update_interval'];
		$list[$lnum]['order'] = $value['order'];
		if ($value['appcount'])
			$list[$lnum]['appcount'] = $value['appcount'];
		else
		{
			if ($value[0] == 3)
			{
				$sqlAllApp = "SELECT count(*) as c FROM am_app_category JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1 $sqlTcl  AND cate_id =" . $value[0];
			}
			elseif ($value[0] == 9998)
			{
				$parentid = $value['parentid'];
				$sqlAllApp = "SELECT count(*) as c FROM am_app_category JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1 $sqlTcl  AND cate_id in(select id from am_category where parent_id = $parentid and visible=1)";
			}
			elseif ($value[0] == 9999)
			{
				$parentid = $value['parentid'];
				$sqlAllApp = "SELECT count(*) as c FROM am_app_category JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
							WHERE am_appinfo.app_visible =1 $sqlTcl  AND cate_id in(select id from am_category where parent_id = $parentid and visible=1)";
			}elseif ($value[0] == 20001)//所有年龄
			{
				$parentid = $value['parentid'];
				$sqlAllApp = "SELECT count(*) as c FROM  am_appinfo 
							WHERE am_appinfo.app_visible =1  ";
			}elseif ($value[0] >= 20002 && $value[0] <= 20005 )//年龄
			{
				$parentid = $value['parentid'];
				$sqlAllApp = "SELECT count(*) as c FROM  am_appinfo 
							WHERE am_appinfo.app_visible =1  AND (agetype=".(5-$value[0]+20001)." or agetype=0 or agetype is null )";
			}
			else
			{
				continue;
			}
			$rs2 = mysql_query($sqlAllApp, $conn);
			$row2 = mysql_fetch_assoc($rs2);
			$list[$lnum]['appcount'] = $row2['c'];
		}
		$lnum++;
	}
	$knum++;
}
	
$sql = "select a.id,a.name,a.description,a.updatetime,a.parent_id,a.`order`,b.icon from am_category a left join am_category_device_type b on a.id=b.category_id where a.id not in(1,3,5,6) and a.visible=1 group by a.id";
$rs = mysql_query($sql, $conn);
while ($row = mysql_fetch_assoc($rs))
{
	//$showlist[] = $row['id'];
	//if($row['updatetime'] >= $updatetime){
	$sqlAllApp = "SELECT count(*) as c FROM am_app_category JOIN am_appinfo ON am_app_category.app_id = am_appinfo.app_id
					WHERE am_appinfo.app_visible =1 $sqlTcl  AND cate_id =" . $row['id'];
	$rs2 = mysql_query($sqlAllApp, $conn);
	$row2 = mysql_fetch_assoc($rs2);
	if ($row2['c'] > 0)
	{
		$showlist[] = $row['id'];
		//zxg,20120413,fix display empty cate name.
		if ($row['updatetime'] >= $updatetime)
		{
			if ($row["id"] == 93)
				$row["name"] = ($xxxxTopicName) ? $xxxxTopicName : $row["name"];
			$list[$lnum]['cateid'] = $row['id'];
			$list[$lnum]['type'] = 2;
			$list[$lnum]['parentid'] = $row['parent_id'];
			$list[$lnum]['name'] = $row['name'];
			$list[$lnum]['icon'] = ($row['icon']) ? $AM_CATE_ICON_LOC_PREFIX . $row['icon'] : "";
			$list[$lnum]['desc'] = $row['description'];
			$list[$lnum]['update_interval'] = 24 * 3600;
			$list[$lnum]['appcount'] = $row2['c'];
			$list[$lnum]['order'] = $row['order'];
			$lnum++;
		}
	}
	$knum++;
}

$sql = "select a.id,a.name,a.description,a.updatetime,a.parent_id,a.`order`,b.icon from am_topic a left join am_topic_device_type b on a.id=b.topic_id where a.visible=1 $tcl_or group by a.id";
$rs = mysql_query($sql, $conn);
while ($row = mysql_fetch_assoc($rs))
{
//	$showlist[] = $row['id'];
//	if ($row['updatetime'] >= $updatetime)
//	{
	$sqlAllApp = "SELECT count(*) as c FROM am_app_topic JOIN am_appinfo ON am_app_topic.app_id = am_appinfo.app_id WHERE am_appinfo.app_visible =1 $sqlTcl  AND topic_id =" . $row['id'];

	$rs2 = mysql_query($sqlAllApp, $conn);
	$row2 = mysql_fetch_assoc($rs2);
	if ($row2['c'] > 0)
	{
		$showlist[] = $row['id'];
		//zxg,20120413,fix display empty cate name.
		if ($row['updatetime'] >= $updatetime)
		{
			$list[$lnum]['cateid'] = $row['id'];
			$list[$lnum]['type'] = 3;
			$list[$lnum]['parentid'] = $row['parent_id'];
			$list[$lnum]['name'] = $row['name'];
			$list[$lnum]['icon'] = ($row['icon']) ? $AM_CATE_ICON_LOC_PREFIX . $row['icon'] : "";
			$list[$lnum]['desc'] = $row['description'];
			$list[$lnum]['update_interval'] = 24 * 3600;
			$list[$lnum]['appcount'] = $row2['c'];
			$list[$lnum]['order'] = $row['order'];
			$lnum++;
		}
	}
	$knum++;
}
$json_arr = array(
	"proto" => 46,
	"reqsuccess" => AM_REQUEST_SUCCESS,
	'list' => $list,
	'showlist' => implode(",", $showlist),
	'updatetime' => date("Y-m-d H:i:s")
);
echo array2json($json_arr);
@mysql_free_result($rs);
@mysql_close($conn);
if ($memobj)
	$memobj->close();
?>
