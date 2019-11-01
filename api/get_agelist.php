<?php
//$_POST["proto"]= 58;
//$_POST["mid"]= '354932';
//$_POST["sid"]= '354380';
	require("./inc/init.php");	
	
    	if($AM_CURRENT_REQUEST["PROTO"] != 58){
		echo error2json("E002");
		die;
	}
	$agelist = array(
		0=>'所有儿童',
		1=>'0-3岁',
		2=>'4-6岁',
		3=>'7-9岁',
		4=>'10-12岁',
	);
	$list = array();	
	foreach($agelist as $k=>$v)
	{
		$list[$k]['ageid'] = $k;
		$list[$k]['desc'] = $v;		
		$list[$k]['count'] = 0;
	}	
	
	$conn = connect_db();
	if($conn === FALSE){
		echo error2json("S001");
		die;
	}		
	$sql = "select agetype from am_appinfo";
	$result = mysql_query($sql, $conn);

	while ($row = mysql_fetch_assoc($result)){
		if(!isset($row['agetype'])||empty($row['agetype'])||$row['agetype']<0||$row['agetype']>4)
			$row['agetype'] = 0;
		$list[$row['agetype']]['count']++;
	}
	$list[0]['count']+=$list[1]['count']+$list[2]['count']+$list[3]['count']+$list[4]['count'];
//var_dump($list);die();
	echo array2json(array(
		"proto" => 58,
		"reqsuccess" =>  AM_REQUEST_SUCCESS,
		'list'=>$list
	));
	die;	
	
	

?>
