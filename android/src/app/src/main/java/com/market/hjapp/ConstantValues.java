
package com.market.hjapp;

public final class ConstantValues {
    public static final String FLURRY_TICKET = "93EM8BQZYN71AE95IVD9";
    
    // Definition of the channels
    private static final int CHANNEL_ID_INTERNAL    = 1;
    private static final int CHANNEL_ID_AISHIDE     = 2;
    private static final int CHANNEL_ID_COOLREN     = 3;
    private static final int CHANNEL_ID_DANGLE      = 4;
    private static final int CHANNEL_ID_CASEE		= 5;
    private static final int CHANNEL_ID_KAIQI		= 6;
    private static final int CHANNEL_ID_SOHU  		= 7;
    public static final int CHANNEL_ID_CURRENT     = CHANNEL_ID_INTERNAL;
    
    // Definition of the client version		   
    public static int CLIENT_VERSION_NUMBER  = 2012062701;
    public static String CLIENT_VERSION_NAME = "2.0.2.627";
    
    // Definition of the device, use HTC Hero for HVGA
    public static final int DEVICE_ID_CURRENT = 2;

    // Definition of the service APIs
    
    
    public static final String HOST ="http://api.market.com";// "http://api.oi3g.com";//"http://api.hjapp.com";//

    public static final String HOST_USERLOG = "http://api.hjapp.com/statlog/getmarketclientlog.php";
    
//    public static final String HOST = "http://www.hjapk.com";
////    public static final String HOST = "http://www.lovelogger.cn";
//
//    public static final String HOST_USERLOG = " http://xxxxxxx/t/getxxxxxclientlog.php";
    
//    public static final String HOST = "http://www.lovelogger.cn";
//
//    public static final String HOST_USERLOG = "http://www.lovelogger.cn";
    
    public static final String HOST_CURRENT =  HOST;
    public static final String HOST_USERLOG_CURRENT =  HOST_USERLOG;
    
    public static final boolean HAVE_USER_PAY_FUNTION = true;//控制个人中心里面有没有支付功能  true为有 false为没有
    public static final boolean HAVE_CUSTOM_MAIN_PAGE_CHANNEL = false;	

    public static final String URL_ANONYMOUS_LOGIN         = HOST_CURRENT + "/anonymous_login.php";
    public static final String URL_GET_DAILY_DATA      	   = HOST_CURRENT + "/get_daily_data";

    public static final String URL_DOWNLOAD                = HOST_CURRENT + "/download.php";
    public static final String URL_GET_HOTWORDS            = HOST_CURRENT + "/get_hotwords.php";
    public static final String URL_REGISTER                = HOST_CURRENT + "/register.php";
    public static final String URL_LOGIN                   = HOST_CURRENT + "/login.php";
    public static final String URL_LOGOUT                  = HOST_CURRENT + "/logout.php";
    public static final String URL_SET_USERINFO            = HOST_CURRENT + "/set_userinfo.php";
    public static final String URL_SEND_VERIFY            = HOST_CURRENT + "/send_verify.php";
    public static final String URL_BIND_PHONE            = HOST_CURRENT + "/bind_phone.php";
    public static final String URL_CHARGE                  = HOST_CURRENT + "/depositeapp.php";
    public static final String URL_BUY                     = HOST_CURRENT + "/buy.php";
    public static final String URL_GET_CHARGE_LIST         = HOST_CURRENT + "/get_chargetype_list.php"; 
    public static final String URL_GET_SCREENSHOT_LIST     = HOST_CURRENT + "/get_screenshot.php";
    public static final String URL_FIND_PASSWORD           = HOST_CURRENT + "/send_password.php";
    public static final String URL_CHANGE_PASSWORD         = HOST_CURRENT + "/change_password.php";
    public static final String URL_GET_COMMENTS            = HOST_CURRENT + "/get_comments.php";
    public static final String URL_COMMENT                 = HOST_CURRENT + "/comment.php";
    public static final String URL_COMMENT_SCORE           = HOST_CURRENT + "/score.php";
    public static final String URL_GET_MY_RATING           = HOST_CURRENT + "/get_my_rating.php";
    public static final String URL_SEARCH                  = HOST_CURRENT + "/search.php";
    public static final String URL_UPGRADE                 = HOST_CURRENT + "/upgrade.php";
    public static final String URL_GET_APPINFO_LIST        = HOST_CURRENT + "/get_appinfolist.php";
    public static final String URL_GET_APPSTAT_LIST        = HOST_CURRENT + "/get_appstat_list.php";
    public static final String URL_AUTHENTICATE            = HOST_CURRENT + "/download_check.php";
    public static final String URL_UPDATE_DOWNLOAD_LOG     = HOST_CURRENT + "/update_download_log.php";

    public static final String URL_GET_LOCAL_APPLIST       = HOST_CURRENT + "/get_local_applist.php";
    public static final String URL_UPLOAD_USER_LOG_LIST    = HOST_CURRENT + "/update_view_log.php";
    public static final String URL_GET_NEWEST_CATE         = HOST_CURRENT + "/get_newest_applist.php";
    public static final String URL_GET_ONE_CATE            = HOST_CURRENT + "/get_onecate_applist.php";
    public static final String URL_SENDSUGGESTION          = HOST_CURRENT + "/feedback.php";
    public static final String URL_GETRELATEAPPLIST        = HOST_CURRENT + "/get_related_applist.php";
    public static final String URL_GET_FAVORITE_CHANNEL    = HOST_CURRENT + "/get_love_channel.php";
    public static final String URL_SET_FAVORITE_CHANNEL    = HOST_CURRENT + "/set_love_channel.php";
    
    public static final String URL_GET_RECOMMEND           = HOST_CURRENT + "/get_am_rec_dailyday.php";
    public static final String URL_GET_RECOMMEND_BY_TIME   = HOST_CURRENT + "/get_am_rec_dailyday_time.php";
    public static final String URL_GET_ONECATE_APPLIST_PAGE   = HOST_CURRENT + "/get_onecate_applist_page.php";
    public static final String URL_GET_DOWNLOADS_TIME      = HOST_CURRENT + "/get_downloads_time.php";
    public static final String URL_GET_INFO_VERSION        = HOST_CURRENT + "/infoversion.php";
    
    public static final String URL_GET_BACKUP_AND_RECOVERY_APPLIST   = HOST_CURRENT + "/backandrecover_applist.php";
    
    public static final String URL_CLIENT_CHECK            = HOST_CURRENT + "/client_check.php";
    
    
    //onecate_applist_new url
    public static final String URL_GET_ONECATE_APPLIST_NEW = HOST_CURRENT + "/get_onecate_applist_new.php";
    //cate_list_new url
    public static final String URL_GET_CATE_LIST_NEW       = HOST_CURRENT + "/get_cate_list_new.php";
    // hotwords_list url
    public static final String URL_GET_HOTWORDS_LIST_NEW   = HOST_CURRENT + "/get_hotword_list.php";
    
    public static final String URL_DOWNLOAD_LIST           = HOST_CURRENT + "/download_list.php";
    
    
    // Definition of the category ids
    // Hotest -> 3
    // Latest -> 2
    // Must Have -> 4
//    public static int[] SUGGESTED_CATE_IDLIST = {9995, 3, 9994};
    public static final int DOWNLOAD_CATE_ID = 9994; 
    public static final int SUGGESTED_CATE_ID = 9995; 

    // Defined number of apps per page
    public static final int NUM_PER_PAGE = 14;
        
    // Define the sorting order for category app list
    public static final String[] CATEGORY_APPLIST_SORTING = {"hot_free", "new_free"};

    
    
    
    // static values of request params
//    public static final String[] PROTO1_KEY = {"proto", "imei", "ver", "model", "screen"};
//    public static final String[] PROTO2_KEY = {"proto", "uid", "ver", "sid", "pn", "screen"};
//    public static final String[] PROTO3_KEY = {"proto", "uid", "load", "paging", "numperpage", "pageno", "sort", "sid"};
//    public static final String[] PROTO4_KEY = {"proto", "uid", "numperpage", "pageno", "sort", "appname", "author", "sid"};
//    public static final String[] PROTO5_KEY = {"proto", "uid", "appid", "sid"};
//    public static final String[] PROTO6_KEY = {"proto", "uid", "appid", "numperpage", "paging", "pageno", "sort", "sid"};
//    public static final String[] PROTO7_KEY = {"proto", "uid", "appid", "sid"};
//    public static final String[] PROTO8_KEY = {"proto", "uid", "appid", "rate", "sid"};
//    public static final String[] PROTO9_KEY = {"proto", "uid", "appid", "imei", "range", "sid"};
//    public static final String[] PROTO10_KEY = {"proto", "uid", "load", "sid"};
//    public static final String[] PROTO11_KEY = {"proto", "uid", "appid", "downloadid", "sid"};
//    public static final String[] PROTO12_KEY = {"proto", "uid", "appid", "sid"};
//    public static final String[] PROTO13_KEY = {"proto", "uid", "appid", "sid", "comment"};
//    public static final String[] PROTO14_KEY = {"proto", "uid", "imei", "sid", "pn", "screen"};
//    public static final String[] PROTO15_KEY = {"proto", "uid", "sid"};
    
    public static final String[] PROTONUM = {"1", "2", "3", "4", "5", "6", "7", "8", "9", "10", "11", "12", "13", "14", "15"};
    
    
    public static final String FREE_PRICE = "0";
    
    // add by Johnny
    public static final String LOADTYPE1_RECOMMEND = "recommend";
    public static final String LOADTYPE1_GAME = "game";
    public static final String LOADTYPE1_APP = "app";

    public static final String LOADTYPE2_NEW = "new";
    public static final String LOADTYPE2_HOT = "hot";
    public static final String LOADTYPE2_MUSTHAVE = "musthave";
    
    public static final String SORT_HOT = "hot";
    public static final String SORT_NEW = "new";
    
    public static final String[] LOAD_TYPE = {"recommend", "game", "app", "downloads"};
    
    public static final String[] RECOMMEND_SORT_TYPE = {"new", "hot", "musthave"};
    
    public static final String[] SORT_TYPE = {"hot", "new"};
    
    public static final String[] PAGING = {"no", "yes"};
    
    // configurations saved in shared preference
    public static final String PREF_CONFIG = "pref_config";
    public static final String PREF_KEY_LASTUPDATETIME = "pref_key_lastupdateday";
    public static final String PREF_KEY_LAST_VIEW_RECOMMEND_PAGE_DAY = "pref_key_last_view_recommend_day";
    
    public static final String PREF_KEY_CATEGORY_UPDATE_TIME = "pref_key_category_update_time"; 
    
//    public static final String PREF_KEY_LAST_CATEGORY_UPDATE_TIME_1 = "pref_key_last_category_update_day_1";
//    public static final String PREF_KEY_LAST_CATEGORY_UPDATE_TIME_2 = "pref_key_last_category_update_day_2";
//    public static final String PREF_KEY_LAST_UPLOAD_APPLIST_TIME = "pref_key_last_upload_applist_day";
//    public static final String PREF_KEY_LAST_UPLOAD_USERLOG_TIME = "pref_key_last_upload_userlog_day";
    public static final String PREF_KEY_LAST_CATEGORY_UPDATE_1 = "pref_key_last_category_update_1";
    public static final String PREF_KEY_LAST_CATEGORY_UPDATE_2 = "pref_key_last_category_update_2";
    public static final String PREF_KEY_LAST_UPLOAD_APPLIST = "pref_key_last_upload_applist";
    public static final String PREF_KEY_LAST_UPLOAD_USERLOG = "pref_key_last_upload_userlog";
    
    public static final String PREF_KEY_LAST_UPDATE_TIME = "pref_key_last_update_daily_data";
    
    public static final String PREF_KEY_IS_LEADED = "pref_key_is_leaded";
    public static final String PREF_KEY_DOWNLOAD_TIME   = "pref_key_download_time";
    public static final String PREF_KEY_UPDATE_TIME   = "pref_key_update_time";
    public static final String PREF_KEY_USER_NICKNAME   = "pref_key_user_nickname";
    
    public static final String PREF_KEY_USER_LOG   = "pref_key_user_log";
    
    public static final String PREF_KEY_HOTWORDS   = "pref_key_hotwords";
      
    public static final String PREF_KEY_USER_GUIDE   = "pref_key_user_guide";
    
    public static final String PREF_KEY_CALL_BACK   = "pref_key_call_back";
    
    public static final String PREF_KEY_CALL_BACK_TIME   = "pref_key_call_back_time";
    
    public static final String PREF_KEY_RECOMMEND_LIST_LAST_UPDATE_DAY   = "pref_key_recommend_list_last_update_day";
    public static final String PREF_KEY_RECOMMEND_LIST_VIEW_DAY  = "pref_key_recommend_list_view_day";
    
    public static final String PREF_KEY_TOTAL_FAVORITE_CATE_LIST  = "pref_key_total_favor_cate_list";
    public static final String PREF_KEY_MY_FAVORITE_CATE_LIST  = "pref_key_my_favor_cate_list";
    
//    public static final String PREF_KEY_UPLOADED_LOCAL_APK_LIST = "pref_key_uploaded_local_apk_list";
    
    public static final String PREF_KEY_NEED_SCAN_LOCAL_APP = "pref_key_need_scan_local_app";
    
    public static final String PREF_KEY_HAS_CREATED_SHORTCUT = "pref_key_has_created_shortcut";
    
    public static final String PREF_KEY_RECOMMEND_DISPLAY_LIST = "pref_key_recommend_display_list";
    public static final String PREF_KEY_CATE_DISPLAY_LIST = "pref_key_cate_display_list";
    
    public static final String PREF_KEY_RECOMMEND_TIME = "pref_key_recommend_time";
    
    public static final String PREF_KEY_CATE_TIME = "pref_key_cate_time";
    
    public static final String PREF_KEY_IS_MARKETCLOSED_STATUS = "pref_key_is_marketclosed";
    
    public static final String PREF_KEY_HOTWORDS_UPDATE_TIME = "pref_key_hotwords_update_time";
    
    public static final String PREF_KEY_UPLOAD_PACKAGE_INFO = "pref_key_upload_package_info";
    
    public static final String PREF_KEY_BACKUP_LIST = "pref_key_backup_list";
    
    // static user info values for shared preference
    public static final String USERINFO = "user_info";
    public static final String USERID = "uid";
    public static final String USERSTATUS = "status";
    public static final String USERSID = "sid";
    public static final String USER_HASPASSWORD = "has_password";
    public static final String USER_SID_ASSIGN_TIME = "sid_assign_time";
    
    // static info for notifications
    public static final String APP_INSTALL_NOTIFICATION = "app_install_notification";
    
    // Error msg
    public static final String URL_ERROR = "Incompleted URL";
    
    //Transport params
    public static final String LOAD_CATE_1 = "loadcat1";
    public static final String LOAD_CATE_2 = "loadcat2";
    public static final String CATE_NAME = "cateName";
    
    //client app id
    public static final String CLIENT_PID = "0";
    public static final String CLIENT_PNAME = "Market";
    public static final int CLIENT_DBID = 0;
    public static final int CLIENT_NOTIFY_ID = 987;
    public static final int APP_HAS_UPDATE_NOTIFY_ID = 986;
    public static final int CALL_BACK_NOTIFY_ID = 985;
    public static final int RECOMMEND_NOTIFY_ID = 988;

    // static variable for intent passed to permission activity
    public static final String INSTALL_PID = "app_pid";
    public static final String INSTALL_LOCAL_PATH = "app_local_path";
    public static final String INSTALL_DB_PID = "app_db_pid";
    public static final String INSTALL_ERROR_APK = "app_error_apk";
    public static final String INSTALL_DOWNLOAD_ID = "install_download_id";
    
    // static variable for request timeout
    public static final int REQUEST_TIME_OUT = 15000;
    
    // static variable for session time out
    public static final long SESSION_TIME_OUT = 58 * 60 * 1000; // sid expired in 60 min, to be safe, check it with 58 min
    	
    // static variable for downloaded icon path
    public static final String ICON_FILE_PATH = "/data/data/com.moto.mobile.appstore/scicons/";
    
    public static final String APP_DOWNLOAD_STATUS = "downloaded";
    public static final String APP_INSTALL_STATUS = "installed";
    public static final String APP_PAID_STATUS = "payed";
    public static final String NET_TYPE = "net_type";
    public static final String NET_EXTRA = "net_extra";
    
    public static String usrAgent;
     
}
