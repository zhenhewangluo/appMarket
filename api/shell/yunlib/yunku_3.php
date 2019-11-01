<?php
header("Content-Type:text/html;charset=utf-8");
session_start();
if(!empty($_SESSION['uid'])&&!empty($_SESSION['username'])){
	require_once("configs.php");
	$conn = connect_db();
	$get_path=$_SERVER['AM_SITE_ROOT'].'data/yunlib_9995_3.txt';
	$content=file_get_contents($get_path);
	if($content){
		$contents=json_decode($content,true);
		
		$sum=count($contents['yunlib_3']);
		foreach($contents['yunlib_3'] as $key=>$val){
			$sec[$key] = $val; 
		}
	}else{
		$contents['yunlib_3']=array();
		
	}

$action = $_POST["action"];
if($action=="add"){
		$app_id=array();
		$app_id[]= $_POST["app_id"];
		$app_sort = $_POST["app_sort"];
		$total=count($contents['yunlib_3']);
		if(in_array($_POST["app_id"],$contents['yunlib_3'])){
			$msg = "该应用ID已存在，请重试！";
		}else{
			if(isset($app_sort)&&$app_sort>$total)$real_pos=$app_sort;
			if(isset($app_sort)&&$app_sort<=$total)$real_pos=$app_sort-1;
			if(empty($app_sort))$real_pos=$total+1;
					

			array_splice($contents['yunlib_3'],$real_pos,0,$app_id);
			
			
			$result=file_put_contents($get_path,json_encode($contents));
			if($result)$msg = "添加推荐应用成功！！";
			else $msg = "添加推荐应用失败，请重试！！";
		}
				
	
}
if($action=="edit"){
		
		$e_id= $_POST["editid"];
		$app_id[] = $_POST["app_id"];
		$app_sort = $_POST["app_sort"];
		$edit_sort = $_POST["edit_sort"];
		if($app_sort==$edit_sort){
			$edit_sort=$edit_sort-1;
			echo $edit_sort."<br>";
			$contents['yunlib_3'][$edit_sort]=$_POST["app_id"];
		
		}else{
			$edit_sort=$edit_sort-1;
			unset($contents['yunlib_3'][$edit_sort]);
			$contents['yunlib_3']=array_merge($contents['yunlib_3']);
			$total=count($contents['yunlib_3']);
			if(isset($app_sort)&&$app_sort>$total)$real_pos=$app_sort;
			if(isset($app_sort)&&$app_sort<=$total)$real_pos=$app_sort-1;
			if(empty($app_sort))$real_pos=$total+1;
		
			array_splice($contents['yunlib_3'],$real_pos,0,$e_id);
		}
		
		
		
		$inres=file_put_contents($get_path,json_encode($contents));
		
		if($inres){
			$msg = "编辑推荐应用成功！！";
		}else{
			$msg = "编辑推荐应用失败，请重试！！";
		}
}
if($_GET["action"]=="del"){
	$k=array_search($_GET['did'],$sec);
	unset($contents['yunlib_3'][$k]);
	$contents['yunlib_3']=array_merge($contents['yunlib_3']);
	$delres=file_put_contents($get_path,json_encode($contents));
	if($delres){
		$msg = "删除推荐应用成功！！";
	}else{
		$msg = "删除推荐应用失败，请重试！！";
	}
}
if($_GET["action"]=="edit"){
	$e_app_id=$_GET['did'];
	$sortid=array_search($e_app_id,$sec);
	$e_app_sort=$sortid+1;
	$sql="select app_name from am_appinfo where app_id=".$e_app_id;
	$query = mysql_query($sql,$conn);
	$rs = mysql_fetch_object($query);
	$e_app_name = $rs->app_name;
	}

if($msg) echo "<script>alert('".$msg."');window.location.href='./yunku_3.php'</script>";
//add
?>

添加最新上线：<br>
<script src="jquery.js" type="text/javascript"></script> 
<script type="text/javascript">
 function get_name(){
	var c_appid= document.getElementById('app_id').value;
	var html='';
	$("#show").html(html);
	if(c_appid!==''){
		var opt = {
			url: 'yunku_ajax.php',
					data: {model:1,app_id:c_appid},
					dataType: 'html',
					type: 'POST',
					success: function(msg) {
						if(msg){
							//msg='<font color="#FF0000"><a href="http://www.xxxx8.com/app/'+c_appid+'_1_0.html" target="_blank">'+msg+'</font>';
							
							$("#app_name").val(msg);
							
						}else{
							msg='<font color="#FF0000">暂无该应用</font>';
							$("#show").html(msg);
						}
						
					},
					error:function() {},
					complete:function() {}
				};
				$.ajax(opt);

	}
}
</script> 
<form action="" method="POST" name="addappinfo" enctype="multipart/form-data" onsubmit="return addinfo();"> 
	当前共计<?php if($sum)echo $sum;else echo 0;?>个应用<br>	
	　应用id：<input type="text" name="app_id" id="app_id" value="<?php if(isset($e_app_id))echo $e_app_id;?>" onblur='get_name()'><div id="show"></div><br>
	应用名称：<input type="text" name="app_name" id="app_name" value="<?php if(isset($e_app_name))echo $e_app_name;?>"><br>	
	应用排序：<input type="text" name="app_sort" id="app_sort" value="<?php if(isset($e_app_sort))echo $e_app_sort;?>"><br>	
			
	
	<?php if(isset($e_app_id)){?>
	<input type="hidden" name="action" value="edit">
	<input type="hidden" name="editid" value="<?php  echo $e_app_id?>">
	<input type="hidden" name="edit_sort" id="edit_sort" value="<?php echo $e_app_sort;?>"><br>	
	<input type="submit" name="addrecapp" value="编辑应用">
	<?php }else{	?>
	<input type="hidden" name="action" value="add">
	<input type="submit" name="addrecapp" value="添加应用">
	<?php	}	?>
</form><a href="main.php">返回</a>
<?php
	if(is_array($contents['yunlib_3'])){
?>
	<table border="1">
	<tr>
	
	<td>推荐应用id</td>
	<td>推荐应用名称</td>
	
	<td>操作</td>
	</tr>
<?php
	foreach($contents['yunlib_3'] as $key=>$row){	
		$sql="select app_name from am_appinfo where app_id=".$row;
		
		$query = mysql_query($sql,$conn);
		$rs = mysql_fetch_object($query);
		$res = $rs->app_name;
	
		?>
		<tr>
	
			<td><?php echo $row;?></td>
			<td><?php echo $res;?></td>
			
			<td><a href="?action=del&did=<?php echo $row;?>" onclick="if(confirm('您确定要删除吗?\n\n删除应用将导致改天的推荐没有显示！')) return ture; else return false;">删除</a>&nbsp;&nbsp;<a href='?action=edit&did=<?php echo $row;?>'>编辑</a></td>
		</tr>

		<?php
	
	}
}




?>


</table>

<!doctype html>
<html>
<head>
<title>猜你喜欢</title>
<meta http-equiv="keywords" content="Android, Android下载，Android软件下载，Android游戏，Android手机软件，Android手机软件下载，Android手机游戏下载，Android评测，Android软件评测，Android游戏评测，手机软件，AndroidMarket, Android开发者" />
<meta name="description" content="软件商店,谷歌手机操作系统Android平台上的应用软件下载平台,包含最新Android手机 应用, Android手机 软件,Android手机游戏,Android手机 主题,Android评测,Android 手机专题,Android手机必备" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>添加猜你喜欢</title>

</head>
<body>
<script language="javascript">	
	function addinfo(){
		if(document.getElementById("app_id").value ==""){
			alert("请输入推荐应用的id！");
			return false;
		}
		if(document.getElementById("app_name").value ==""){
			alert("请输入推荐应用的名称！");
			return false;
		}
		
	}
</script>
</body>
</html>
<?php
}else{
	echo "<script>window.location.href='index.php'</script>";
}



function utf_substr($str,$len)
{
for($i=0;$i<$len;$i++)
{
$temp_str=substr($str,0,1);
if(ord($temp_str) > 127)
{
$i++;
if($i<$len)
{
$new_str[]=substr($str,0,3);
$str=substr($str,3);
}
}
else
{
$new_str[]=substr($str,0,1);
$str=substr($str,1);
}
}
return join($new_str);
}

?>