<?php
define("AM_SYSTEM_ID", 1);

define("AM_SITE_ROOT",$_SERVER['AM_SITE_ROOT']);

define("AM_SITE_URL", $_SERVER['AM_SITE_URL']);

define("AM_DOWNLOAD_URL","http://down.hjapk.com/");

define("AM_LANGUAGE", "ENG");

define("AM_UNKNOWN_ERROR", "S999");

define("AM_REQUEST_SUCCESS", true);
define("AM_REQUEST_FAIL",    false);

define("AM_VERSION_FILE", "./inc/version.txt");

define("AM_UPDATE_PHONE_INFO", true);

define("AM_DEFAULT_PAGE_SIZE", 10);

define("AM_DEFAULT_SORT_METHOD", 'hot');

define("AM_EXCHANGE_RATE", 1.00);

define("AM_SYSTEM_CTRF", "\n");

define("AM_AUTH_NEED_LOGON", 3);

define("AM_AUTH_NEED_SESSION", 2);

define("AM_AUTH_FOR_ALL", 1);

define("AM_DEFAULT_AUTH_LEVEL", AM_AUTH_NEED_SESSION);

define("AM_DOWNLOAD_CNT", 1);
$AM_CURRENT_SYS_ID = AM_SYSTEM_ID;

$AM_CURRENT_REQUEST = array(
	"PROTO" =>  99,	 
	"UID"   => 0,
	"SID"   => '',		
);

// Database Config
$AM_DATABASE_INFOR = array(
	"DB_HOST"     => $_SERVER['DB_HOST'],
  	"DB_USER"     => $_SERVER['DB_USER'],
	"DB_PASSWORD" => $_SERVER['DB_PASSWORD'],
	"DB_NAME"     => $_SERVER['DB_NAME'],
);

$AM_COMMON_INFO_DATABASE = array(
	"DB_HOST"     => $_SERVER['COMMON_DB_HOST'],
  	"DB_USER"     => $_SERVER['COMMON_DB_USER'],
	"DB_PASSWORD" => $_SERVER['COMMON_DB_PASSWORD'],
	"DB_NAME"     => $_SERVER['COMMON_DB_NAME'],
);

$AM_VALID_TOP_CATEGORIES = array(
	'app','game','downloads','recommend','chart'
);

$AM_VALID_LEAF_CATEGORIES = array(
	'downloads'
);

// resource prefix
$AM_CATE_ICON_LOC_PREFIX        = $_SERVER['AM_SITE_URL'];
$AM_APP_ICON_LOC_PREFIX         = $_SERVER['AM_SITE_URL'];
$AM_APP_LOGO_LOC_PREFIX         = $_SERVER['AM_SITE_URL'];
$AM_APP_SCREENSHOT_LOC_PREFIX   = $_SERVER['AM_SITE_URL'];
$AM_APP_DOWNLOAD_LOC_PREFIX     = $_SERVER['AM_SITE_DOWN_URL'];

$AM_APP_REC_LOGO_LOC_PREFIX     = "http://down.hjapk.com/";

/*
$AM_CATE_ICON_LOC_PREFIX        = "http://down.xxxx8.com/";
$AM_APP_ICON_LOC_PREFIX         = "http://down.xxxx8.com/";
$AM_APP_LOGO_LOC_PREFIX         = "http://down.xxxx8.com/";
$AM_APP_SCREENSHOT_LOC_PREFIX   = "http://down.xxxx8.com/";
$AM_APP_DOWNLOAD_LOC_PREFIX     = "http://down.xxxx8.com/";
*/

$AM_DOWNLOAD_STATUS = array(
	"START"     => 0,	
	"SUCCESS"   => 1,
	"FAILED"    => 2,
	"ABORTED"   => 3,
	"CANCELED"  => 4,
);

$AM_ERROR_LOG_FILE = "./buy_app_error.log";

$AM_LOGGING_INFO = array(
	"LOG_DIR"            => AM_SITE_URL . "logs/",
	"LOG_FILE_NAME"      => "sandroid.log",
	"LOG_LEVELS_SHORT"   => "D,I,W,E,S",
	"LOG_LEVELS_LONG"    => "DEBUG,INFOR,WARNN,ERROR,SYSER",
	"LOG_ALLOW_MIN_LEVEL"=> "D",       //must use short type level here
	"LOG_MAX_SIZE"       => 10 * 1024, //k, max size of single log file is 10M
	"LOG_USE_DAILY_FILE" => true,
	);

// max download time for anoymouse user
define("AM_MAX_ANONY_DOWNLOAD_TIME", 5);

//mailset 
$MailSet['host'] = "ssl://smtp.gmail.com";    //SMTP
$MailSet['port'] = "465";    //SMTP
$MailSet['user'] = "huanjubao@gmail.com";      //SMTP
$MailSet['pass'] = "jinantianyi";          //SMTP

////mailset 
//$MailSet['host'] = "mail.hjapp.com";    //SMTP
//$MailSet['port'] = "25";    //SMTP
//$MailSet['user'] = "appservice";      //SMTP
//$MailSet['pass'] = "HjApp2)1!";          //SMTP




$xxxxcopyrightchekChannelid = array("26"); ////TCl
$xxxxcopyrightchekTopicid = array("95"); ////TCl专题ID
$xxxxChannelidTopic93 = array("10022"=>"瑞信");	

////搜索和下载动态接口不允许app_visible=2(采集的应用)的渠道.26=tcl;27=云库
$xxxxnopermitChannelid = array("26","27"); 
$xxxxYunlibChannelid = array("27"); ////yunlib云库
$xxxxYunlibDataFile = array("rec"=>"./data/yunlib_rec.txt","new"=>"./data/yunlib_9995_3.txt","rank_10001"=>"./data/yunlib_rank_soft.txt","rank_10002"=>"./data/yunlib_rank_game.txt","rank_10003"=>"./data/yunlib_rank_all.txt","hotwords"=>"./data/yunlib_search.txt","cate"=>"./data/yunlib_category_cate.txt","topic"=>"./data/yunlib_category_topic.txt");
/////cateid与type对应关系 cateid type updatetime
$xxxxArrCategoryType = array(
	"xxxx_9994"=>array(9994,1,'2011-04-08 17:00:00','name'=>'下载动态','icon'=>'','desc'=>'','appcount'=>50,'parentid'=>0,'update_interval'=>60,"order"=>3),
	"xxxx_9995"=>array(9995,1,'2011-05-05 17:00:00','name'=>'猜你喜欢','icon'=>$AM_CATE_ICON_LOC_PREFIX."chart/320x480/chart_gameH0.png",'desc'=>'','appcount'=>50,'parentid'=>0,'update_interval'=>24*3600,"order"=>2),
	"xxxx_3"	  =>array(3,1,'2011-04-08 17:00:00','name'=>'最新上线','icon'=>'','desc'=>'','appcount'=>0,'parentid'=>0,'update_interval'=>4*3600,"order"=>1),
	"xxxx_9998"=>array(9998,2,'2011-01-29 00:00:00','name'=>'所有应用','icon'=>$AM_CATE_ICON_LOC_PREFIX."cate/480x800/cate_softwareW0.png",'desc'=>'谷歌百度腾讯大牌云集，聊天音乐交友内容齐全,学习工作资讯必不可少,最全最热，尽在所有软件','appcount'=>0,'parentid'=>5,'update_interval'=>24*3600,"order"=>0),
	"xxxx_9999"=>array(9999,2,'2011-01-29 00:00:00','name'=>'所有游戏','icon'=>$AM_CATE_ICON_LOC_PREFIX."cate/480x800/cate_gameW0.png",'desc'=>'Gameloft3D称雄，EA体育王者无敌，小鸟僵尸华山论剑，群雄逐鹿，尽在所有游戏','appcount'=>0,'parentid'=>6,'update_interval'=>24*3600,"order"=>0),
	"xxxx_10001"=>array(10001,4,'2012-09-01 00:00:00','name'=>'教育类排行','icon'=>$AM_CATE_ICON_LOC_PREFIX."top/soft_top.png",'desc'=>'','appcount'=>20,'parentid'=>0,'update_interval'=>24*3600,"order"=>1),
//	"xxxx_10002"=>array(10002,4,'2011-01-29 00:00:00','name'=>'游戏类排行','icon'=>$AM_CATE_ICON_LOC_PREFIX."top/game_top.png",'desc'=>'小鸟僵尸，群雄争霸，今日谁又独孤求败？','appcount'=>20,'parentid'=>0,'update_interval'=>24*3600,"order"=>2),
//	"xxxx_10003"=>array(10003,4,'2011-01-29 00:00:00','name'=>'下载总排行','icon'=>$AM_CATE_ICON_LOC_PREFIX."top/all_top.png",'desc'=>'风云变幻，哪款应用笑傲江湖，独领风骚？','appcount'=>20,'parentid'=>0,'update_interval'=>24*3600,"order"=>3),

	"xxxx_20001"=>array(20001,5,'2012-08-20 00:00:00','name'=>'所有儿童','icon'=>$AM_CATE_ICON_LOC_PREFIX."top/allage.png",'desc'=>'','appcount'=>0,'parentid'=>32,'update_interval'=>24*3600,"order"=>1),	
	"xxxx_20002"=>array(20002,5,'2012-08-20 00:00:00','name'=>'10-12岁','icon'=>$AM_CATE_ICON_LOC_PREFIX."top/10~12.png",'desc'=>'','appcount'=>0,'parentid'=>32,'update_interval'=>24*3600,"order"=>2),	
	"xxxx_20003"=>array(20003,5,'2012-08-20 00:00:00','name'=>'7-9岁','icon'=>$AM_CATE_ICON_LOC_PREFIX."top/7~9.png",'desc'=>'','appcount'=>0,'parentid'=>32,'update_interval'=>24*3600,"order"=>3),	
	"xxxx_20004"=>array(20004,5,'2012-08-20 00:00:00','name'=>'4-6岁','icon'=>$AM_CATE_ICON_LOC_PREFIX."top/4~6.png",'desc'=>'','appcount'=>0,'parentid'=>32,'update_interval'=>24*3600,"order"=>4),	
	"xxxx_20005"=>array(20005,5,'2012-08-20 00:00:00','name'=>'0-3岁','icon'=>$AM_CATE_ICON_LOC_PREFIX."top/0~3.png",'desc'=>'','appcount'=>0,'parentid'=>32,'update_interval'=>24*3600,"order"=>5),	
		);
$TCLArrAppinfo_A890 = array(
	"tcl_6000650"=>"6000650/apk/NaviDog_A890.apk", //导航犬
	"tcl_6007742"=>"6007742/apk/AM321_10051_tcl_A890.apk", //Am321公信卫士
	"tcl_6000080"=>"6000080/apk/Renren_Android_1.7.1_TCL_A890_101231.apk",  //人人网 
	"tcl_6001353"=>"6001353/apk/A890eStockL2(when110114).apk", //益盟操盘手
	"tcl_6002309"=>"6002309/apk/netease_pmail_v0.4.6_2.0_TCL.apk", //网易手机邮不是正版 X
	"tcl_6000088"=>"6000088/apk/youdaodict.apk",  //有道词典
	"tcl_6000063"=>"6000063/apk/UCBrowser_A890.apk", //UC浏览器
	"tcl_6000110"=>"6000110/apk/QQBrowser_TCL_A890.apk", //QQ浏览器
	"tcl_6000001"=>"6000001/apk/SogouInput_android_oem_tcl_0_5_0_0_0_1801(TCL_A890).apk", //搜狐输入法
	
	////2011.06.14添加
	"tcl_6004649"=>"6004649/apk/SohuNewsClient_TCL_A890.apk", //搜狐新闻
	"tcl_6000100"=>"6004649/apk/Youku_TCL_A890.apk", //优酷视频
	"tcl_6000394"=>"6004649/apk/iReader_TCL_A890.apk", //掌阅书城
		
	"tcl_6000182"=>"6004649/apk/GGBook_Android_TCL.apk", //GGbook
	"tcl_6002055"=>"6004649/apk/GOWeather_TCL.apk", //Go天气
	"tcl_6001911"=>"6004649/apk/GOSmsPro_TCL.apk", //Go短信
	"tcl_6010139"=>"6004649/apk/GOContacts_TCL.apk", //Go联系人
		
	"tcl_6000258"=>"6004649/apk/taobao_TCL_A890.apk", //淘宝
	"tcl_6000124"=>"6004649/apk/digua_TCL_A890.apk", //地瓜游戏
	"tcl_6001314"=>"6004649/apk/CubeDemo_TCL_A890.apk", //网易八方
	"tcl_6000227"=>"6004649/apk/kwplayer_tcl_A890.apk", //酷我听听
		
	"tcl_6000890"=>"6004649/apk/qiyi_TCL_A890.apk", //奇艺视频
	"tcl_6006917"=>"6004649/apk/sohu_blog_TCL_A890.apk", //搜狐微博
	"tcl_6010217"=>"6004649/apk/koubei_TCL_A890.apk", //口碑网
		
		
	"tcl_6001200"=>"6001200/apk/AndroidQQ_tcl_A890.apk",  //腾讯QQ没有应用 X
	"tcl_6000039"=>"6000039/apk/tcl_weibo2.0.4.apk", //新浪微博	
	"tcl_6000555"=>"6000555/apk/BaiduSearch-v1.0.3.33_530b.apk", //百度快搜
	
);
$TCLArrAppinfo_A906 = array(
	"tcl_6000001"=>"6000001/apk/SogouInput__A906.apk", //搜狐输入法
	"tcl_6000080"=>"6000080/apk/Renren_A906.apk",  //人人网 
	"tcl_6000039"=>"6000039/apk/weibo_A906.apk", //新浪微博
	"tcl_6000088"=>"6000088/apk/youdaodict_A906.apk",  //有道词典
	"tcl_6001353"=>"6001353/apk/eStockL2_A906.apk", //益盟操盘手
	"tcl_6001200"=>"6001200/apk/MobileQQ1_A906.apk",  //腾讯QQ没有应用 X
	"tcl_6000555"=>"6000555/apk/BaiduSearch_A906.apk", //百度快搜
	"tcl_6002309"=>"6002309/apk/netease_A906.apk", //网易手机邮不是正版 X
	"tcl_6007742"=>"", //Am321公信卫士
	"tcl_6000650"=>"", //导航犬
	"tcl_6000063"=>"", //UC浏览器
	"tcl_6000110"=>"", //QQ浏览器
	////2011.06.14添加
	"tcl_6004649"=>"", //搜狐新闻
	"tcl_6000100"=>"", //优酷视频
	"tcl_6000394"=>"", //掌阅书城
		
	"tcl_6000182"=>"", //GGbook
	"tcl_6002055"=>"", //Go天气
	"tcl_6001911"=>"", //Go短信
	"tcl_6010139"=>"", //Go联系人
		
	"tcl_6000258"=>"", //淘宝
	"tcl_6000124"=>"", //地瓜游戏
	"tcl_6001314"=>"", //网易八方
	"tcl_6000227"=>"", //酷我听听
		
	"tcl_6000890"=>"", //奇艺视频
	"tcl_6006917"=>"", //搜狐微博
	"tcl_6010217"=>"", //口碑网
);
$AM_SCREEN_EXISTS = array("240x320","240x400","320x480","360x640","480x800","480x854","1280x720");
/*require_once("memcache.inc.php");
$memobj = new Memcache;  ////memcache/
$memobj->connect($_SERVER['MEMCACH_HOST'],$_SERVER['MEMCACH_PORT']);
*/
?>


