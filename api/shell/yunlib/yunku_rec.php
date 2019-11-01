<?php
header("Content-Type:text/html;charset=utf-8");
session_start();
if(!empty($_SESSION['uid'])&&!empty($_SESSION['username'])){

	require_once("configs.php");
	$conn = connect_db();
	$get_path=$_SERVER['AM_SITE_ROOT'].'data/yunlib_rec.txt';
	$content=file_get_contents($get_path);
	if($content){
		$contents=json_decode($content,true);
		//print_r($contents);
		foreach($contents as $key=>$val){
			$fis[$key] = strtotime($val['edittime']);
			$sec[$key] = $val['id']; 
			
		}
		//array_multisort($fis,SORT_DESC,$sec, SORT_DESC,$contents); 
		//print_r($contents);
		$lastval=max($sec);
	}else{
		$contents=array();
		$lastval=1000001;
	}

$action = $_POST["action"];
if($action=="add"){
		$app_id = $_POST["app_id"];
		$app_name = $_POST["app_name"];	
		$desc = $_POST["desc"];
		$rec_desc = $_POST["rec_desc"];
		$icon = $_POST["icon"];
		$type = $_POST["type"];
		$visible = $_POST["visible"];
		$addtime = $_POST["addtime"];
		$getimage = $_FILES['image']['name'];
		$geticon = $_FILES['icon']['name'];
		if($getimage ==""){
			$msg = "上传应用切图失败，请重试！";
		}else{
			$image_path_document = $_SERVER['AM_SITE_ROOT']."res/";
			$image_show_path = "recimage/".time()."_".$getimage;
			$image_path = $image_path_document.$image_show_path;

			$icon_show_path = "recimage/".time()."_icon_".$geticon;
			$icon_path = $image_path_document.$icon_show_path;

			if(move_uploaded_file($_FILES['image']['tmp_name'], $image_path)){
				move_uploaded_file($_FILES['icon']['tmp_name'], $icon_path);
				$insertarray['id']=$lastval+1;
				$insertarray['type']=0;
				$insertarray['typeid']=$app_id;
				$insertarray['typename']=$app_name;
				$insertarray['desc']=$desc;
				$insertarray['rec_desc']=$rec_desc;
				$insertarray['image']=$image_show_path;
				$insertarray['icon']=$icon_show_path;
				$insertarray['addtime']=$addtime;
				$insertarray['edittime']=$addtime;

				$contents[]=$insertarray;
				foreach($contents as $key=>$val){
					$fis[$key] = $val['edittime'];
					$sec[$key] = $val['id']; 
				}
				array_multisort($fis,SORT_DESC,$sec, SORT_DESC,$contents); 
				
				$result=file_put_contents($get_path,json_encode($contents));
				if($result)$msg = "添加推荐应用成功！！";
				else $msg = "添加推荐应用失败，请重试！！";
				
			}else{
				$msg = "上传应用切图失败，请重试！";
			}
			
		}
}
if($action=="edit"){
		$e_id = $_POST["editid"];
		$app_id = $_POST["app_id"];
		$app_name = $_POST["app_name"];	
		$desc = $_POST["desc"];
		$rec_desc = $_POST["rec_desc"];
		$icon = $_POST["icon"];
		$type = $_POST["type"];
		$visible = $_POST["visible"];
		$addtime = $_POST["addtime"];
		$getimage = $_FILES['image']['name'];
		$geticon = $_FILES['icon']['name'];
		
		$image_path_document = $_SERVER['AM_SITE_ROOT']."res/";
		$image_show_path = "recimage/".time()."_".$getimage;
		$image_path = $image_path_document.$image_show_path;

		$icon_show_path = "recimage/".time()."_icon_".$geticon;
		$icon_path = $image_path_document.$icon_show_path;
			
		
		$ek=array_search($e_id,$sec);
		
		$contents[$ek]['id']=$e_id;
		$contents[$ek]['type']=0;
		$contents[$ek]['typeid']=$app_id;
		$contents[$ek]['typename']=$app_name;
		$contents[$ek]['desc']=$desc;
		$contents[$ek]['rec_desc']=$rec_desc;
		
		
		$contents[$ek]['addtime']=$contents[$ek]['addtime'];
		$contents[$ek]['edittime']=$addtime;

		

		
		if($getimage){
			if(move_uploaded_file($_FILES['image']['tmp_name'], $image_path)){
				$contents[$ek]['image']=$image_show_path;
			}else{
				$msg = "上传应用切图失败，请重试！";
				echo "<script>alert('".$msg."');window.location.href='./yunku_rec.php'</script>";
				exit;
			}
		}
		if($geticon){
			if(move_uploaded_file($_FILES['icon']['tmp_name'], $icon_path)){
				$contents[$ek]['icon']=$icon_show_path;
			}else{
				$msg = "上传应用ICON失败，请重试！";
				echo "<script>alert('".$msg."');window.location.href='./yunku_rec.php'</script>";
				exit;
			}
		}
		
		
		
		foreach($contents as $key=>$val){
			$fis[$key] = $val['edittime'];
			$sec[$key] = $val['id']; 
		}
		array_multisort($fis,SORT_DESC,$sec, SORT_DESC,$contents); 
		
		$inres=file_put_contents($get_path,json_encode($contents));
		
		if($inres){
			$msg = "编辑推荐应用成功！！";
		}else{
			$msg = "编辑推荐应用失败，请重试！！";
		}
}
if($_GET["action"]=="del"){
	$k=array_search($_GET['did'],$sec);
	unset($contents[$k]);
	//print_r(array_merge($contents));
		$contents=array_merge($contents);
		
		foreach($contents as $key=>$val){
			$fis[$key] = $val['edittime'];
			$sec[$key] = $val['id']; 
		}
		array_multisort($fis,SORT_DESC,$sec, SORT_DESC,$contents); 
		
		$delres=file_put_contents($get_path,json_encode($contents));
	if($delres){
		$msg = "删除推荐应用成功！！";
	}else{
		$msg = "删除推荐应用失败，请重试！！";
	}
}
if($_GET["action"]=="edit"){
	
	$k=array_search($_GET['did'],$sec);
	
	$e_app_id		= $contents[$k]['typeid'];
	$e_typename		= $contents[$k]['typename'];
	$e_desc		= $contents[$k]['desc'];
	$e_rec_desc		=$contents[$k]['rec_desc']; 

	$e_id		= $contents[$k]['id'];
	$e_image		=$contents[$k]['image']; 
	$e_icon		= $contents[$k]['icon'];
	$e_edittime = $contents[$k]['edittime'];
	
}

if($msg) echo "<script>alert('".$msg."');window.location.href='./yunku_rec.php'</script>";
//add
?>

添加今日推荐：<br>
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
	
	　推荐应用id：<input type="text" name="app_id" id="app_id" value="<?php if(isset($e_app_id))echo $e_app_id;?>" onblur='get_name()'><div id="show"></div><br>
	推荐应用名称：<input type="text" name="app_name" id="app_name" value="<?php if(isset($e_typename))echo $e_typename;?>"><br>	
	推荐应用切图：<input type="file" name="image" id="image" ><?php if(isset($e_image))echo '<a href="'.$AM_APP_REC_LOGO_LOC_PREFIX.$e_image.'" target="_blank"><img  src="'.$AM_APP_REC_LOGO_LOC_PREFIX.$e_image.'" width="50" height="50"/></a>';?><br>
	推荐应用icon：<input type="file" name="icon" id="icon"><?php if(isset($e_icon))echo '<a href="'.$AM_APP_REC_LOGO_LOC_PREFIX.$e_icon.'" target="_blank"><img  src="'.$AM_APP_REC_LOGO_LOC_PREFIX.$e_icon.'" width="50" height="50" /></a>';?><br>
	　　推荐时间：<input type="text" name="addtime" value="<?php if(isset($e_edittime) && $e_edittime!='') echo $e_edittime; else echo date('Y-m-d H:i:s');?>" id="addtime">( * 注意时间格式，否则推荐的应用将不会显示（时间格式为：xxxx-xx-xx xx:xx:xx）)<br>
	推荐应用简介：<textarea rows="5" cols="50" name="desc" id="desc" ><?php if(isset($e_desc))echo $e_desc;?></textarea><br>
	推荐应用说明：<textarea rows="5" cols="50" name="rec_desc" id="rec_desc" ><?php if(isset($e_rec_desc))echo $e_rec_desc;?></textarea>*新增加字段，请注册<br>

	<input type="hidden" name="type" value="0">
	
	<?php if(isset($e_app_id)){?>
	<input type="hidden" name="action" value="edit">
	<input type="hidden" name="editid" value="<?php  echo $e_id?>">
	<input type="submit" name="addrecapp" value="编辑应用">
	<?php }else{	?>
	<input type="hidden" name="action" value="add">
	<input type="submit" name="addrecapp" value="添加应用">
	<?php	}	?>
</form><a href="main.php">返回</a>
<?php



?>
<br>推荐应用列表(默认只显示最新添加的50个应用):<br>
<br><font color="red">推荐应用介绍(只显示前50个字，鼠标移到文字上方显示全部)</font><br>
<table border="1">
<tr>
	<td>推荐时间</td>
	<td>推荐应用id</td>
	<td>推荐应用名称</td>
	<td>推荐应用简介</td>
	<td>推荐说明</td>
	<td>推荐应用切图</td>
	<td>推荐应用icon</td>
	<td>操作</td>
</tr>
<?php
foreach($contents as $key=>$row){	
	$desc = utf_substr($row["desc"],50);
	//$desc = iconv("utf8","gbk",$desc);

	//$alldesc = iconv("utf8","gbk",$row["desc"]);
	$alldesc = $row["desc"];
	
	$rec_desc = utf_substr($row["rec_desc"],30);
	$allrecdesc = $row["rec_desc"];
?>
<tr>
	<td><?php echo $row["edittime"];?></td>
	<td><?php echo $row["typeid"];?></td>
	<td><?php echo $row["typename"];?></td>
	<td><a href="#" title='<?php echo $alldesc;?>'><?php echo $desc;?></a></td>
	<td><a href="#" title='<?php echo $allrecdesc;?>'><?php echo $rec_desc;?></a></td>
	<td><a href="<?php echo $AM_APP_REC_LOGO_LOC_PREFIX.$row["image"];?>" target="_blank">推荐图查看</a></td>
	<td><a href="<?php echo $AM_APP_REC_LOGO_LOC_PREFIX.$row["icon"];?>" target="_blank">icon预览查看</a></td>
	<td><a href="?action=del&did=<?php echo $row["id"];?>" onclick="if(confirm('您确定要删除吗?\n\n删除应用将导致改天的推荐没有显示！')) return ture; else return false;">删除</a>&nbsp;&nbsp;<a href='?action=edit&did=<?php echo $row["id"];?>'>编辑</a></td>
</tr>
<?php
}
?>
</table>

<!doctype html>
<html>
<head>
<title>今日推荐</title>
<meta http-equiv="keywords" content="Android, Android下载，Android软件下载，Android游戏，Android手机软件，Android手机软件下载，Android手机游戏下载，Android评测，Android软件评测，Android游戏评测，手机软件，AndroidMarket, Android开发者" />
<meta name="description" content="软件商店,谷歌手机操作系统Android平台上的应用软件下载平台,包含最新Android手机 应用, Android手机 软件,Android手机游戏,Android手机 主题,Android评测,Android 手机专题,Android手机必备" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>添加推荐应用</title>

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
		<?php if(!isset($e_app_id)){?>
	
		if(document.getElementById("image").value ==""){
			alert("请选择你要上传的应用切图！");
			return false;
		}
		if(document.getElementById("icon").value ==""){
			alert("请选择你要上传的应用的icon！");
			return false;
		}
		<?php } ?>
		if(document.getElementById("addtime").value ==""){
			alert("请填写推荐时间！");
			return false;
		}
		if(document.getElementById("desc").value ==""){
			alert("请输入推荐应用的介绍！");
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