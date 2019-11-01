<?php
/**************************************************
* Description:     更新排行数据
* Others:         
* Date：         20120327
* Author：       xg
*************************************************/

define(DB_HOST, '192.168.1.145');
define(DB_NAME, '_kid_android');
define(DB_USER, 'kid');
define(DB_PWD, 'jinantianyi');

//define(DB_HOST,'localhost');
//define(DB_NAME,'_kid_android');
//define(DB_USER,'root');
//define(DB_PWD,'');

$limit = 20;

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

/**
 * Retrieve a app_id list by certain method.
 * 
 * @param $cateid 	5=soft,6=game,-1=all.
 * @param $limit 	total result num.
 * @param $type 	1=week,2=month,3/others=all.
 * @return Returns app_id list by certain method.
 */
function getRankList($cate_id, $limit, $type,$conn)
{
	if($cate_id!=-1)//game/soft
	{
		$cateWhere = "select a.app_id from am_appinfo a left join am_app_category b on a.app_id=b.app_id where a.app_visible=1 and a.relationid>0 and "
			."b.cate_id in (select id from am_category where parent_id =$cate_id)";
		$cateWhere2 = "left join am_app_category b on a.app_id=b.app_id where a.app_visible=1 and a.relationid>0 and "
					. "b.cate_id in (select id from am_category where parent_id =$cate_id)";
	}
	else//total
	{
		$cateWhere = "select app_id from am_appinfo where app_visible=1 and relationid>0 ";
		$cateWhere2 = " where a.app_visible=1 and a.relationid>0 ";
							
	}
	if($type==1)//week
	{
		$t = strtotime(date("Y-m-d 00:00:00")) - 3600 * 24 * 7; //前7天
		$t2 = strtotime(date("Y-m-d 00:00:00")) - 3600 * 24 * 14; //前14天
		$sql1 = "select c.app_id from am_download_history c where status=1 and UNIX_TIMESTAMP(c.create_time)>$t and c.app_id in("
			.$cateWhere.") group by c.app_id order by count(c."
			. "app_id) desc limit $limit";		
		$sql2 = "select c.app_id from am_download_history c where status=1 and UNIX_TIMESTAMP(c.create_time)>$t2 and c.app_id in("
				.$cateWhere.") group by c.app_id order by count(c."
				. "app_id) desc limit $limit";		
	}
	else if($type==2)//month
	{
		$t = strtotime(date("Y-m-1 00:00:00")); //当月
		if(@date("m")=='01')
		{
			$time=intval(@date("Y")-1).'-12';
		}
		else
		{
			$time=@date("Y-").str_pad(intval(@date("m")-1),2,0,STR_PAD_LEFT);
		}
		$t2 = strtotime(date($time)); //近两月
		$sql1 = "select c.app_id from am_download_history c where status=1 and UNIX_TIMESTAMP(c.create_time)>$t and c.app_id in("
			.$cateWhere.") group by c.app_id order by count(c."
			. "app_id) desc limit $limit";		
		$sql2 = "select c.app_id from am_download_history c where status=1 and UNIX_TIMESTAMP(c.create_time)>$t2 and c.app_id in("
				.$cateWhere.") group by c.app_id order by count(c."
				. "app_id) desc limit $limit";			
	}
	else //all ,not neet query download_history
	{
		$sql1 = "select a.app_id from am_appinfo a ".$cateWhere2." order by app_downloads desc limit $limit";
		$sql2="";
	}
	$rs = mysql_query($sql1, $conn);
	$applist = array();
	//结果不够，舍弃
	if(mysql_num_rows($rs) >= $limit)
	{
		while ($row = mysql_fetch_assoc($rs))
		{
			$applist[] = $row["app_id"];
		}	
	}
	else//结果<$limit,另加上一周期 （14天or 两月）
	{
		if(sql2=="")//总排行不到limit个，正常不会到此处
			return $applist;
		$rs = mysql_query($sql2, $conn);
		while ($row = mysql_fetch_assoc($rs))
		{
			$applist[] = $row["app_id"];
		}				
		if (count($applist) < $limit)//结果<$limit,另加总排行 
		{
			if(count($applist)>0)
				$sql3 = "select a.app_id from am_appinfo a ".$cateWhere2." and a.app_id not in (" . join(",", $applist) . ") order by app_downloads desc limit $limit";
			else
				$sql3 = "select a.app_id from am_appinfo a ".$cateWhere2." order by app_downloads desc limit $limit";				
			$rs = mysql_query($sql3, $conn);
			while ($row = mysql_fetch_assoc($rs))
			{
				$applist[] = $row["app_id"];
			}			
		}
	}
	//结果超出limit 减去
	if (count($applist) > $limit)
		$applist = array_slice($applist, 0, $limit);
	return $applist;
}

$conn = connect_db();
if ($conn === FALSE)
{
	echo 'Cannot connect to database';
	die;
}
$cate_id = 5;
$soft_week = getRankList($cate_id, $limit, 1,$conn);
$soft_month = getRankList($cate_id, $limit, 2,$conn);
$soft_all = getRankList($cate_id, $limit, 3,$conn);
$sql = "update am_rank set `week`='". join(",", $soft_week) ."',`month`='". join(",", $soft_month) ."',`all`='". join(",", $soft_all) ."' where `type`='soft'";
$rs = mysql_query($sql, $conn);
if ($rs === FALSE)
{
	echo "update soft rank error";
	die;
}
$cate_id = 6;
$game_week = getRankList($cate_id, $limit, 1,$conn);
$game_month = getRankList($cate_id, $limit, 2,$conn);
$game_all = getRankList($cate_id, $limit, 3,$conn);
$sql = "update am_rank set `week`='". join(",", $game_week) ."',`month`='". join(",", $game_month) ."',`all`='". join(",", $game_all) ."' where `type`='game'";
$rs = mysql_query($sql, $conn);
if ($rs === FALSE)
{
	echo "update game rank error";
	die;
}
$cate_id = -1;
$total_week = getRankList($cate_id, $limit, 1,$conn);
$total_month = getRankList($cate_id, $limit, 2,$conn);
$total_all = getRankList($cate_id, $limit, 3,$conn);
$sql = "update am_rank set `week`='". join(",", $total_week) ."',`month`='". join(",", $total_month) ."',`all`='". join(",", $total_all) ."' where `type`='all'";
$rs = mysql_query($sql, $conn);
if ($rs === FALSE)
{
	echo "update total rank error";
	die;
}

//add new applist to am_rank_appinfo 
//maybe need delete
$newIdList = array_merge($soft_week,$soft_month,$soft_all,$game_week,$game_month,$game_all,$total_week,$total_month,$total_all);
$newIdList = @array_unique($newIdList);
$sql = "select app_id from am_rank_appinfo where app_id in (".join(',',$newIdList).")";
$rs = mysql_query($sql, $conn);
while ($row = mysql_fetch_assoc($rs))
{
	$alreadyIn[] = $row['app_id'];
}	
if(count($alreadyIn)>0)
	$newIdList = @array_diff($newIdList,$alreadyIn);
if(count($newIdList)==0)
	die('all done.');
//获取设备类型
$sql = "select screen,sdk from am_rank group by screen,sdk";
$rs = mysql_query($sql, $conn);
$devicelist = array(array());
$i = 0;
while ($row = mysql_fetch_assoc($rs))
{
	$devicelist[$i]['sdk'] = $row["sdk"];
	$devicelist[$i]['screen'] = $row["screen"];
	$i++;
}	

//获取cate_id 对应parent_id
$rs = mysql_query("select id,parent_id from am_category where parent_id>1", $conn);
while ($row = mysql_fetch_assoc($rs))
{
	$parentList[$row['id']]=$row['parent_id'];
}	
//准备insert数据
$sql = "select a.app_id,b.cate_id,a.app_downloads from am_appinfo a left join am_app_category b on a.app_id=b.app_id where a.app_id in (".join(',',$newIdList).") and b.cate_id in (select id from am_category where parent_id>1)";
$rs = mysql_query($sql, $conn);
$i = 0;
while ($row = mysql_fetch_assoc($rs))
{

	$appData[$i]['app_id'] = $row['app_id'];
	$appData[$i]['cate_id'] = $row['cate_id'];
	$appData[$i]['app_downloads'] = $row['app_downloads'];
	$appData[$i]['parent_id'] = $parentList[$row['cate_id']];
	$i++;
}
//数据库分类有异常
if(count($appData)!=count($newIdList))
	echo "db cate date fault!";
foreach($appData as $v)
{
	foreach($devicelist as $v2)
	{
		$insertArray['app_id'] = $v['app_id'];
		$insertArray['sdk'] = $v2['sdk'];
		$insertArray['screen'] = $v2['screen'];
		$insertArray['copyright'] = 0;//copyright
		$insertArray['week'] = 0;//week
		$insertArray['month'] = 0;//month
		$insertArray['parent_id'] = $v['parent_id'];
		$insertArray['cate_id'] = $v['cate_id'];
		$insertArray['all'] = $v['app_downloads'];//all
		$sql = "insert into am_rank_appinfo (`app_id`,`sdk`,`screen`,`copyright`,`week`,`month`,`parent_id`,`cate_id`,`all`) values "
		."('".join("','",$insertArray)."')";
		$rs = mysql_query($sql, $conn);	
		if($rs === FALSE)
			echo "insert ".$v['app_id']." error\n";		
	}	
}
echo "all done. update ".count($appData);
die;
////////////////////////////
?>
