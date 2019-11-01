<?php
	/*
	1.inc目录允许创建文件 chmod 777 inc/
	2.inc/channel_config.php允许重写 chmod 666 inc/channel_config.php
	3.res/repo目录允许创建文件 chmod 777 res/repo/
	4.shell/channel/目录允许创建文件 chmod 777 shell/channel/
	*/
	header("Content-Type:text/html;charset=utf-8");
	require("../../inc/config.inc.php");
	include(AM_SITE_ROOT ."inc/functions.php");
	include(AM_SITE_ROOT ."inc/channel_config.php");
	
	//连接数据库
	$conn = connect_db();
	
	$do = (__getGet('do'))?__getGet('do'):"index";
	if($do == "index"){
		$strv = file_get_contents("app_version.txt");
		$strChannel = ($strv)?"：当前升级渠道版本号：<font color=red><strong>".$strv."</strong></font>&nbsp;&nbsp;<a href='../../inc/channel_config.txt' target='_blank'>查看升级配置文件</a>":"：<font color=red><strong>当前没有生成配置文件，请点击 <a href='?do=create'>生成配置文件</a></strong></font>";
		$sql = "select app_version from am_channel_config where visible=1 group by app_version order by app_version desc limit 10";
		$result = mysql_query($sql, $conn);
		$arrAppversion = array(); $strVersion = "";
		$app_version = (__getGet('app_version'))?__getGet('app_version'):"";
		$j = 0;
		while ($row = mysql_fetch_assoc($result)){
			$arrAppversion[] = $row['app_version'];
			if(!$app_version && $j == 0) {
				$app_version = $row['app_version'];
				$strVersion .= "<a href='?do=index&app_version=".$row['app_version']."'><font color=red><strong>".$row['app_version']."</strong></font></a>&nbsp;&nbsp;";
			}
			elseif($app_version == $row['app_version'])
				$strVersion .= "<a href='?do=index&app_version=".$row['app_version']."'><font color=red><strong>".$row['app_version']."</strong></font></a>&nbsp;&nbsp;";
			else $strVersion .= "<a href='?do=index&app_version=".$row['app_version']."'>".$row['app_version']."</a>&nbsp;&nbsp;";
			$j++;
		}
		
		$REPO_ROOT = $_SERVER['REPO_ROOT'].$app_version."/";
		$sql = "select * from am_channel_config where visible=1 and app_version='$app_version' order by channel_id";
		$result = mysql_query($sql, $conn);
		$arrR = array();
		while ($row = mysql_fetch_assoc($result)){
			$arrR[] = $row;
		}
		include_once("index_html.php");
	}elseif($do == "add"){
		$sql = "select channel_id,channel_name from am_channel order by channel_id";
		$result = mysql_query($sql, $conn);
		$options = "";
		while ($row = mysql_fetch_assoc($result)){
			$options .= "<option value='".$row['channel_name']."|_&|".$row['channel_id']."'>".$row['channel_name']."_".$row['channel_id']."</option>";
		}
		include_once("add_html.php");
	}elseif($do == "addSub"){
		list($name,$channel_id) = explode("|_&|" , __getPost("channel_id"));
		$channel_name = __getPost('channel_name');
		$current_version = __getPost('current_version');
		$app_version = __getPost('app_version');
		$need_upgrade = __getPost('need_upgrade');
		$need_english = __getPost('need_english');
		$device_list = __getPost('device_list');
		$apk_path = "xxxx_".$app_version."_".$channel_name."_commonpkg.apk";
		$createtime = date("Y-m-d H:i:s");
		
		if(!file_exists(AM_SITE_ROOT . "res/repo/".$app_version)) mkdir(AM_SITE_ROOT . "res/repo/".$app_version , 0777);
		$path =  AM_SITE_ROOT . "res/repo/".$app_version."/".$apk_path;
		echo $path;
		if(move_uploaded_file($_FILES['apk_path']['tmp_name'], $path)){
		}else{
			echo "上传文件失败！<a href='?do=index'>返回首页列表</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='?do=add'>返回重新添加</a>";die;
		}
		
		$sql = "insert into am_channel_config set channel_id='".$channel_id."',name='".$name."',channel_name='".$channel_name."',current_version='".$current_version."',app_version='".$app_version."',need_upgrade='".$need_upgrade."',need_english='".$need_english."',device_list='".$device_list."',apk_path='".$apk_path."',createtime='".$createtime."'";
		$result = mysql_query($sql, $conn);
		header("location:?do=index");
	}elseif($do == "del"){
		$sql = "update am_channel_config set visible=0 where id=".__getGet('id');
		$result = mysql_query($sql, $conn);
		header("location:?do=index");
	}elseif($do == "create"){
		$strv = file_get_contents("app_version.txt");
		$strChannel = ($strv)?"：当前升级渠道版本号：<font color=red><strong>".$strv."</strong></font>&nbsp;&nbsp;<a href='../../inc/channel_config.txt' target='_blank'>查看升级配置文件</a>":"：<font color=red><strong>当前没有生成配置文件，请点击 <a href='?do=create'>生成配置文件</a></strong></font>";
		
		$app_version = (__getGet('app_version'))?__getGet('app_version'):$strv;
		if($app_version) $strv_content = file_get_contents(AM_SITE_ROOT . "res/repo/".$app_version."/".$app_version.".txt");
		$sql = "select app_version from am_channel_config where visible=1 group by app_version order by app_version desc limit 10";
		$result = mysql_query($sql, $conn);
		$arrAppversion = array(); $strVersion = "";
		$j = 0;
		while ($row = mysql_fetch_assoc($result)){
			$arrAppversion[] = $row['app_version'];
			if(!$app_version && $j == 0) {
				$app_version = $row['app_version'];
				$strv_content = file_get_contents(AM_SITE_ROOT . "res/repo/".$app_version."/".$app_version.".txt");
				$strVersion .= "<a href='?do=create&app_version=".$row['app_version']."'><font color=red><strong>".$row['app_version']."</strong></font></a>&nbsp;&nbsp;";
			}
			elseif($app_version == $row['app_version'])
				$strVersion .= "<a href='?do=create&app_version=".$row['app_version']."'><font color=red><strong>".$row['app_version']."</strong></font></a>&nbsp;&nbsp;";
			else $strVersion .= "<a href='?do=create&app_version=".$row['app_version']."'>".$row['app_version']."</a>&nbsp;&nbsp;";
			$j++;
		}
		include_once("create_html.php");
	}elseif($do == "createSub"){
		if(!__getPost('password') || __getPost('password') != "ab123") {
			echo "输入密钥错误，生成渠道升级包失败！<a href='?do=index'>返回首页列表</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='?do=create'>重新生成升级配置文件</a>";die;
		}
		
		if(!($app_version = __getPost('app_version')) || !($content = __getPost('content'))) {
			echo "生成渠道升级包失败！<a href='?do=index'>返回首页列表</a>&nbsp;&nbsp;&nbsp;&nbsp;<a href='?do=create'>重新生成升级配置文件</a>";die;
		}
		if(!file_exists(AM_SITE_ROOT . "res/repo/".$app_version)) mkdir(AM_SITE_ROOT . "res/repo/".$app_version , 0777);
		file_put_contents("app_version.txt" , $app_version);
		file_put_contents(AM_SITE_ROOT . "res/repo/".$app_version."/".$app_version.".txt" , $content);
		
		$fcontents = "<?php\n //app_version:$app_version\n\n";
		$fcontents .= "header(\"Content-Type:text/html;charset=utf-8\");\r\n\r\n";
		$fcontents .= "\$REPO_ROOT = \$_SERVER['REPO_ROOT'];\r\n\r\n";
		$fcontents .= "define(\"CHANGELOG\",\"$content\");\r\n\r\n";
		$fcontents .= "\$CHANNEL_CONFIG_ARR = array();\r\n\r\n";
		$fcontents .= "\$CHANNEL_MAP = array(\"10001\"=>'commonpkg',\"10002\"=>'commonpkg',\"10004\"=>'commonpkg',\"71\"=>'commonpkg',\"2\"=>'commonpkg',\"25\"=>'commonpkg');\r\n\r\n";
		
		$sql = "SELECT * FROM `am_channel_config` WHERE id IN (SELECT max(id) FROM am_channel_config WHERE visible =1 and app_version<='$app_version' GROUP BY channel_id ORDER BY createtime DESC )";
		$result = mysql_query($sql, $conn);
		while ($row = mysql_fetch_assoc($result)){
			$fcontents .= "\$CHANNEL_CONFIG_ARR[".$row['channel_id']."] = array( 'channel_id'   => ".$row['channel_id'].",
                'channel_name' => '".$row['channel_name']."',
                'current_version' => '".$row['current_version']."',
                'app_version' => '".$row['app_version']."',
                'device_list' => '".$row['device_list']."',
                'need_upgrade' => ".$row['need_upgrade'].",
                'need_english' => ".$row['need_english'].");\r\n\r\n";
		}
		$fcontents .= "?>\n";
		
		$filename = AM_SITE_ROOT."inc/channel_config.php";
		file_put_contents($filename , $fcontents);
		file_put_contents(AM_SITE_ROOT."inc/channel_config.txt" , $fcontents);
		file_put_contents(AM_SITE_ROOT . "res/repo/".$app_version."/channel_config_".$app_version.".txt" , $fcontents);
		echo "生成渠道升级包成功！ <a href='?do=index'>返回首页列表</a>&nbsp;&nbsp;<a href='?do=create'>重新生成升级配置文件</a>&nbsp;&nbsp;<a href='../../inc/channel_config.txt' target='_blank'>查看升级配置文件</a>";die;
	}
	
	@mysql_close($conn);
?>
