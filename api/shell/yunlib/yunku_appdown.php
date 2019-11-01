<?php
	session_start();
if(!empty($_SESSION['uid'])&&!empty($_SESSION['username'])){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> 应用下载信息接口 </TITLE>
  <META HTTP-EQUIV="Content-Type" CONTENT="text/html; charset=UTF-8" /> 
  <META NAME="Generator" CONTENT="EditPlus">
  <META NAME="Author" CONTENT="">
  <META NAME="Keywords" CONTENT="">
  <META NAME="Description" CONTENT="">
 </HEAD>

 <BODY>
 <HR>
<form  method="POST" name="test_common" action="">
<input type="hidden" name="sub" value="1" />
 pid  : <input type="text" name="pid" value="100001"/><input type="hidden" name="proto" value="2" /><br/>
  pname  : <input type="text" name="pname" value="weijiang"/><br/>
   pcode  : <input type="text" name="pcode" value="96999d4ddcb3860d8b21ba826e8eface"/><br/> 
time:<input type="text" name="last_update_time" value="" /><br/>
 <input type="submit">
 <br/>
 <a href="index.php">返回</a>
 </form>
 </BODY>
</HTML>
<?php
	if(!$_POST['sub'])exit;

	require_once("configs.php");
	checkproto(2);//判断接口
	checkID();//判断合作身份
	
	$last_update_time = empty($_POST["last_update_time"])?0:$_POST["last_update_time"];
	if($last_update_time){
		$pre_time=strtotime($last_update_time);
		if(time()<$pre_time){
			$pre_time=time()-3600*24;
		}
	}else{
		$pre_time=time()-3600*24;
	}
	$pre_time=date("Y-m-d",$pre_time);
	$conn = connect_db();
	
	$sql="select count(id) as cnt from am_download_history_all where create_time   like '".$pre_time."%' group by app_id";

	$query = mysql_query($sql,$conn);
	$total = mysql_num_rows($query);
	
	
	$M=0;
	
	$sql="select a.app_id,b.app_name,count(a.app_id) as cnt from am_download_history_all a join am_appinfo b on a.app_id=b.app_id  where create_time   like '".$pre_time."%' group by a.app_id order by cnt desc ";

	$query = mysql_query($sql,$conn);
		while($res = mysql_fetch_array($query)) {
			$applist[$M]['mid'] = $res['mid'];
			$applist[$M]['create_time'] = $res['create_time'];
			$M++;
		}
		
	$json_arr = array(
					"proto" => 2,
					"reqsuccess"  => true,
					"Currydowntotal"=>$total,
					'Currydownlist'  =>$applist,
					'update_time'=>date("Y-m-d",time())
			);
		
	echo array2json($json_arr); 
}else{
	echo "<script>window.location.href='index.php'</script>";
	}

?>