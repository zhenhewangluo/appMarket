<?php

/* * ************************************************
 * Description:     更新每日推荐数据
 * Others:         
 * Date：         20120523
 * Author：       xg
 * *********************************************** */

define(DB_HOST, '192.168.1.145');
define(DB_NAME, '_kid_android');
define(DB_USER, 'kid');
define(DB_PWD, 'jinantianyi');

//define(DB_HOST, 'localhost');
//define(DB_NAME, '_kid_android');
//define(DB_USER, 'root');
//define(DB_PWD, '');

$limit = 10;

/**
 * connect to db.
 */
function connect_db()
{
	$conn = @mysql_connect(DB_HOST, DB_USER, DB_PWD);
	if ($conn === FALSE)
	{
		return FALSE;
	}
	if (!mysql_select_db(DB_NAME, $conn))
	{
		return FALSE;
	}
	if (!mysql_query("SET NAMES UTF8", $conn))
	{
		return FALSE;
	}
	return $conn;
}

$conn = connect_db();
if ($conn === FALSE)
{
	echo 'Cannot connect to database';
	die;
}

addRecDailyDate($conn,5,$limit);
addRecDailyDate($conn,6,$limit);

die;
////////////////////////////

function addRecDailyDate($conn,$type,$limit)
{
//准备insert数据
	$sql = "select a.app_id,a.app_name,a.app_desc,b.icon from am_appinfo a left join am_app_device_type b on a.app_id=b.app_id left join am_app_category c on a.app_id=c.app_id where a.app_visible=1 and length(a.app_desc)>0 and c.cate_id in(select d.id from am_category d where d.parent_id={$type}) group by a.app_id order by a.app_downloads desc limit {$limit}";
	$rs = mysql_query($sql, $conn);
	$i = 0;

	while ($row = mysql_fetch_assoc($rs))
	{

		$appData[$i]['typeid'] = $row['app_id'];
		$appData[$i]['typename'] = $row['app_name'];
		$appData[$i]['desc'] = $row['app_desc'];
		$appData[$i]['icon'] = $row['icon'];
		$i++;
	}

	foreach ($appData as $v)
	{
		$sql = "insert into am_rec_daily_day (`typeid`,`typename`,`desc`,`icon`,`type`,`visible`,`addtime`,`edittime`) values "
				. "('" . join("','", $v) . "',0,1,'" . date("Y-m-d H:i:s") . "','" . date("Y-m-d H:i:s") . "')";
		$rs = mysql_query($sql, $conn);
		//echo $sql."\n";
		if ($rs === FALSE)
			echo "insert " . $v['app_id'] . " error\n";
	}
	echo "all done. update " . count($appData) . "type:" . $type."\n";;
}
?>
