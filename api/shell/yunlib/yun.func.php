<?php
	/*
		@Used      : 接口公共调用函数文件
		@copyright : 公共接口
		@Authors   : taocheng 
		@Write Time: 2011/3/8
		*************edit histroy********
		@lastmodify:
		
	*/
	////////////////////////////start
	define("WEBSITE_ICON_PATH",  '');
	//define("INTERFACE_LOG_PATH", INTERFACE_PATH . "data/log/");
	$websource=array('xxxx8');
	function checkproto($protoid){
		global $_POST;
		
		if(($_POST["PROTO"] != $protoid) && ($_POST["proto"] != $protoid)){
			echo error2json("E002");		
			exit;
		}
	}
	function checkID(){
		global $_POST;
		if(($_POST["PID"]=='') && empty($_POST["pid"])){
			echo error2json("E003");		
			exit;
		}
		if(($_POST["PNAME"]=='') && empty($_POST["pname"])){
			echo error2json("E003");		
			exit;
		}
		if(($_POST["PCODE"]=='') && empty($_POST["pcode"])){
			echo error2json("E003");		
			exit;
		}
	}


	
	function sqladdslashes($string, $force = 0){
		if(!$GLOBALS['magic_quotes_gpc'] || $force)
		{
		  if(is_array($string)) 
		   {
			  foreach($string as $key => $val) 
				{
				$string[$key] = sqladdslashes($val, $force);
			  }
		   }
		   else
		   {
			  $string = addslashes($string); 
		   }
		}
		return $string;
	}
	function check_souce(){
		global $_POST;
		global $websource;
		if(($_POST["wsouce"]=='') && empty($_POST["pid"])){	
			exit;
		}
		
		if(!in_array($_POST['wsouce'],$websource)){
			exit;
		}
	}

?>