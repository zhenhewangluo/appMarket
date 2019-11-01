<?php

		require_once("configs.php");
		$conn = connect_db();
		$model=$_POST['model'];
		if($model==1){
			$app_id=$_POST['app_id'];
			$sql="select app_name from am_appinfo where app_id=".$app_id ;
			$query = mysql_query($sql,$conn);
			$rs = mysql_fetch_object($query);
			$res = $rs->app_name;
			echo  $res;
			exit;
		}
		if($model==2){
			$app_id=$_POST['app_id'];
			$sql="select app_name from am_appinfo where app_id=".$app_id ;
			
			$query = mysql_query($sql,$conn);
			$rs = mysql_fetch_object($query);
			$result['app_name'] = $rs->app_name;
			$sql="select a.name,a.id from am_category a join am_app_category  b on a.id=b.cate_id where b.cate_id not in(1,2,3,4) and  b.app_id=".$app_id ;
			
			$query = mysql_query($sql,$conn);
			$rs = mysql_fetch_object($query);
			if(empty($rs->name))$result['name']='no';
			else $result['name']= $rs->name;
			if(empty($rs->id))$result['id']='no';
			else $result['id']= $rs->id;
			
			$res=json_encode($result);
			

			echo  $res;
			exit;
		}
		if($model==3){
			$app_id=$_POST['app_id'];
			$sql="select app_name from am_appinfo where app_id=".$app_id ;
			
			$query = mysql_query($sql,$conn);
			$rs = mysql_fetch_object($query);
			$result['app_name'] = $rs->app_name;
			$sql="select b.name,b.id from am_app_topic a join am_topic  b on a.topic_id=b.id where  a.app_id=".$app_id ;
			
			$query = mysql_query($sql,$conn);
			$rs = mysql_fetch_object($query);
			if(empty($rs->name))$result['name']='no';
			else $result['name']= $rs->name;
			if(empty($rs->id))$result['id']='no';
			else $result['id']= $rs->id;
			
			$res=json_encode($result);
			

			echo  $res;
			exit;
		}
?>