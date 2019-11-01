<!doctype html>
<html>
<head>
<title>编辑热词</title>
<meta http-equiv="keywords" content="Android, Android下载，Android软件下载，Android游戏，Android手机软件，Android手机软件下载，Android手机游戏下载，Android评测，Android软件评测，Android游戏评测，手机软件，AndroidMarket, Android开发者" />
<meta name="description" content="软件商店,谷歌手机操作系统Android平台上的应用软件下载平台,包含最新Android手机 应用, Android手机 软件,Android手机游戏,Android手机 主题,Android评测,Android 手机专题,Android手机必备" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>添加应用类周排行</title>

</head>
<body>
<?php
header("Content-Type:text/html;charset=utf-8");
session_start();
if(!empty($_SESSION['uid'])&&!empty($_SESSION['username'])){
	require_once("configs.php");
	$conn = connect_db();
	$get_path=$_SERVER['AM_SITE_ROOT'].'data/yunlib_search.txt';
	$content=file_get_contents($get_path);
	if($content){
		$contents=json_decode($content,true);
		$sum=count($contents);
		foreach($contents as $key=>$val){
			$sec[$key] = $val; 
		}
	}else{
		$contents=array();
		
	}

$action = $_POST["action"];
if($action=="add"){
		$app_id=array();
		$app_id[]= $_POST["app_id"];
		$app_sort = $_POST["app_sort"];
		$total=count($contents);
		if(in_array($_POST["app_id"],$contents)){
			$msg = "该热词已存在，请重试！";
		}else{
			if(isset($app_sort)&&$app_sort>$total)$real_pos=$app_sort;
			if(isset($app_sort)&&$app_sort<=$total)$real_pos=$app_sort-1;
			if(empty($app_sort))$real_pos=$total+1;
					

			array_splice($contents,$real_pos,0,$app_id);
			
			
			$result=file_put_contents($get_path,json_encode($contents));
			if($result)$msg = "添加热词成功！！";
			else $msg = "添加热词失败，请重试！！";
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
			$contents[$edit_sort]=$_POST["app_id"];
		
		}else{
			$edit_sort=$edit_sort-1;
			unset($contents[$edit_sort]);
			$contents=array_merge($contents);
			$total=count($contents);
			if(isset($app_sort)&&$app_sort>$total)$real_pos=$app_sort;
			if(isset($app_sort)&&$app_sort<=$total)$real_pos=$app_sort-1;
			if(empty($app_sort))$real_pos=$total+1;
			
			array_splice($contents,$real_pos,0,$e_id);
			
		}
		
		
		
		$inres=file_put_contents($get_path,json_encode($contents));
		
		if($inres){
			$msg = "编辑热词成功！！";
		}else{
			$msg = "编辑推荐应用失败，请重试！！";
		}
}
if($_GET["action"]=="del"){
	$k=array_search($_GET['did'],$sec);
	unset($contents[$k]);
	$contents=array_merge($contents);
	$delres=file_put_contents($get_path,json_encode($contents));
	if($delres){
		$msg = "删除添加热词成功！！";
	}else{
		$msg = "删除添加热词失败，请重试！！";
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

if($msg) echo "<script>alert('".$msg."');window.location.href='./yunku_search'</script>";
//add
?>

添加热词：<br>

<form action="" method="POST" name="addappinfo" enctype="multipart/form-data" onsubmit="return addinfo();"> 
	当前共计<?php if($sum)echo $sum;else echo 0;?>个应用<br>	
	热词：<input type="text" name="app_id" id="app_id" value="<?php if(isset($e_app_id))echo $e_app_id;?>" onblur='get_name()'><div id="show"></div><br>
	
	应用排序：<input type="text" name="app_sort" id="app_sort" value="<?php if(isset($e_app_sort))echo $e_app_sort;?>"><br>	
			
	
	<?php if(isset($e_app_id)){?>
	<input type="hidden" name="action" value="edit">
	<input type="hidden" name="editid" value="<?php  echo $e_app_id?>">
	<input type="hidden" name="edit_sort" id="edit_sort" value="<?php echo $e_app_sort;?>"><br>	
	<input type="submit" name="addrecapp" value="编辑热词">
	<?php }else{	?>
	<input type="hidden" name="action" value="add">
	<input type="submit" name="addrecapp" value="添加热词">
	<?php	}	?>
</form><a href="main.php">返回</a>
<?php
	if(is_array($contents)){
?>
	<table border="1">
	<tr>
	
	<td>添加热词</td>

	
	<td>操作</td>
	</tr>
<?php
	foreach($contents as $key=>$row){	
		
	
		?>
		<tr>
	
			<td><?php echo $row;?></td>
			
			
			<td><a href="?action=del&did=<?php echo $row;?>" onclick="if(confirm('您确定要删除吗?\n\n删除应用将导致改天的推荐没有显示！')) return ture; else return false;">删除</a>&nbsp;&nbsp;<a href='?action=edit&did=<?php echo $row;?>'>编辑</a></td>
		</tr>

		<?php
	
	}





?>


</table>
<?php }?>

<script language="javascript">	
	function addinfo(){
		if(document.getElementById("app_id").value ==""){
			alert("请输入热词！");
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