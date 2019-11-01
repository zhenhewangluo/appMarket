<?php
	session_start();
if(!empty($_SESSION['uid'])&&!empty($_SESSION['username'])){
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<HTML>
 <HEAD>
  <TITLE> 用户信息接口 </TITLE>
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
 pid  : <input type="text" name="pid" value="100001"/><input type="hidden" name="proto" value="1" /><br/>
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
	checkproto(1);//判断接口
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
	$conn = connect_comm_db();
	
	
	$sql="select count(id) as cnt from am_registered_user where registered_time  like '".$pre_time."%'";
	
	$query = mysql_query($sql,$conn);
	$rs = mysql_fetch_object($query);
	$total = $rs->cnt;
	$sql="select count(mid) as cnt from am_terminal where create_time  like '".$pre_time."%'  ";
	
	$query = mysql_query($sql);
	$rs = mysql_fetch_object($query);
	$midtotal = $rs->cnt;
	$k=0;
	$M=0;
	$sql="select id,email,name,registered_time from am_registered_user where registered_time like '".$pre_time."%'";
	
	$query = mysql_query($sql,$conn);
		while($faq = mysql_fetch_array($query)) {
			$arrAppid[$k]['id'] = $faq['id'];
			$arrAppid[$k]['email'] = $faq['email'];
			$arrAppid[$k]['name'] = $faq['name'];
			$arrAppid[$k]['registered_time']= $faq['registered_time'];
			$k++;
		}
	$sql="select mid,create_time from am_terminal where create_time like '".$pre_time."%'";
	
	$query = mysql_query($sql,$conn);
		while($res = mysql_fetch_array($query)) {
			$anonymous[$M]['mid'] = $res['mid'];
			$anonymous[$M]['create_time'] = $res['create_time'];
			$M++;
		}
		
	$json_arr = array(
					"proto" => 1,
					"reqsuccess"  => true,
					"newusertotal"=>$total,
					'newuserlist'  =>$arrAppid,
					
					'anonymoustotal'=>$midtotal,
					'anonymous'  => $anonymous,
					'update_time'=>date("Y-m-d",time())
			);
		
	echo array2json($json_arr); 
}else{
	echo "<script>window.location.href='index.php'</script>";
	}

?>