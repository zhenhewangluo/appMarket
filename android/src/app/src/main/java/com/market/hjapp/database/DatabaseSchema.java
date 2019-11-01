package com.market.hjapp.database;

public class DatabaseSchema {
    private DatabaseSchema() {
        // this class doesn't allow to be instantiated
    }
    
    // Definition of APP table
    public static final class TABLE_APP {//应用列表
        // 定义表的名称
        public static final String NAME = "app_table";
        
        /**
         *定义增量的ID，这是该表的主键。
         */
        public static final String COLUMN_ID = "_id";
        
        /**
         *定义的应用程序ID，这是由服务器保留。
         */
        public static final String COLUMN_APPID = "appid";
        
        /**
         *定义应用程序的名称。
         */
        public static final String COLUMN_NAME = "name";

        /**
         *定义的应用程序图标的URL
         */
        public static final String COLUMN_ICON_URL = "icon_url";

        /**
         *定义应用程序的口号。
         */
        public static final String COLUMN_SLOGAN = "slogan";

        /**
         *定义应用程序的描述。
         */
        public static final String COLUMN_DESCRIP = "descrip";

        /**
         *定义应用程序的作者。
         */
        public static final String COLUMN_AUTHOR = "author";

//        /**
// *定义评价UPS的数字。
//         */
//        public static final String COLUMN_RATE_UP = "rate_up";
//
//        /**
// *定义的评级起伏的数字。
//         */
//        public static final String COLUMN_RATE_DOWN = "rate_down";
        
        /**
         *定义的速率得分
         */
        public static final String COLUMN_SCORE = "scroe";
        
        /**
         *定义评价总得分计数
         */
        public static final String COLUMN_SCORE_CNT = "scroe_count";
        
        /**
         *定义总数的评论
         */
        public static final String COLUMN_COMMENT_CNT = "comment_count";
        
        /**
         *定义的应用程序语言。
         */
        public static final String COLUMN_LANGUAGE = "language";

        /**
         *定义下载的数字。
         */
        public static final String COLUMN_DOWNLOAD_CNT = "download_cnt";

        /**
         *定义应用程序的价格。
         */
        public static final String COLUMN_PRICE = "price";

        /**
         *定义应用程序的版本，这是用于显示。
         */
        public static final String COLUMN_VERSION = "version";
        
        /**
         *定义应用程序的版本，这是用来判断是否
         *此应用程序需要更新。
         */
        public static final String COLUMN_VERSION_NUM = "version_num";

        /**
         *定义内部版本号，这是用来判断是否
         *我们需要更新从服务器应用程序的信息。
         */
        public static final String COLUMN_INT_VERSION = "int_version";

        /**
         *定义截图的网址。
         */
        public static final String COLUMN_SCREENSHOT_URL = "screenshot_url";

        /**
         *定义用户的应用程序的评级。
         */
        public static final String COLUMN_MY_RATING = "my_rating";
        
        /**
         *定义下载路径
         */
        public static final String COLUMN_DOWNLOAD_PATH = "download_path";

        /**
         *定义下载ID
         */
        public static final String COLUMN_DOWNLOAD_ID = "download_id";

        /**
         *定义的状态，它具有以下值：
         * 1。初始化
         * 2。下载的
         * 3。暂停
         * 4。下载
         * 5。安装
         * 6。 HAS_UPDATE
         */
        public static final String COLUMN_STATUS = "status";
        
        /**
         *定义为暂停下载的大小与恢复
         */
        public static final String COLUMN_DOWNLOADED_SIZE = "downloaded_size";

        /**
         *定义应用程序的大小
         */
        public static final String COLUMN_SIZE = "size";

        /**
         *定义的应用程序包名称
         */
        public static final String COLUMN_PACKAGENAME = "packagename";

        /**
         *定义下载APK文件的路径。
         */
        public static final String COLUMN_LOCALPATH = "localpath";
        
        /**
         *定义应用程序的最后更新时间
         */
        public static final String COLUMN_LAST_UPDATE_TIME = "app_last_update_time";
        
    }

    //定义分类表
    public static final class TABLE_CATEGORY {//分类
        //定义表的名称
        public static final String NAME = "category_table";

        /**
         *定义增量的ID，这是该表的主键。
         */
        public static final String COLUMN_ID = "_id";
        
        /**
         *定义类的父ID
         */
        public static final String COLUMN_PARENT_ID = "parent_id";

        /**
         *定义类别ID，这是由服务器保留。
         */
        public static final String COLUMN_CATEID = "cateid";
        
        /**
         *定义类类型。
         */
        public static final String COLUMN_TYPE = "type";
        
        /**
         *定义类别的更新间隔
         */
        public static final String COLUMN_UPDATE_INTERVAL = "update_interval";
        
        /**
         *定义的类别名称。
         */
        public static final String COLUMN_NAME = "name";

        /**
         *定义的类别图标的URL
         */
        public static final String COLUMN_ICON_URL = "icon_url";
        
        /**
         *这一类中定义的应用程序数量
         */
        public static final String COLUMN_APPCOUNT = "appcount";
        
        /**
         *定义类的描述。
         */
        public static final String COLUMN_CATE_DESC = "cate_description";
        /**
         *定义为了描述
         */
        public static final String COLUMN_CATE_ORDER = "cate_order";
    }
    
    //定义类applist表
    public static final class TABLE_CATEGORY_APP_LIST {//分类二级列表
        //定义表的名称
        public static final String NAME = "category_applist_table";

        /**
         *定义增量的ID，这是该表的主键。
         */
        public static final String COLUMN_ID = "_id";

        /**
         *定义类别ID，这是由服务器保留。
         */
        public static final String COLUMN_CATEID = "cateid";
        
        public static final String COLUMN_APPLIST_1 = "applist_1";
        public static final String COLUMN_APPLIST_2 = "applist_2";
        public static final String COLUMN_APPLIST_3 = "applist_3";
        
        public static final String COLUMN_APPLIST1_LAST_UPDATE_TIME = "update_1";
        public static final String COLUMN_APPLIST2_LAST_UPDATE_TIME = "update_2";
        public static final String COLUMN_APPLIST3_LAST_UPDATE_TIME = "update_3";
        
    }
    
 //定义分类表
    public static final class TABLE_RANK {//排行列表
        //定义表的名称
        public static final String NAME = "rank_table";

        /**
         *定义增量的ID，这是该表的主键。
         */
        public static final String COLUMN_ID = "_id";

        /**
         *定义类别ID，这是由服务器保留。
         */
        public static final String COLUMN_RANKID = "rank_id";
        
        /**
         *定义的类别名称。
         */
        public static final String COLUMN_NAME = "name";

        /**
         *定义的类别图标的URL
         */
        public static final String COLUMN_ICON_URL = "icon_url";

        
        /**
         *定义在一个星期下载次数applist的秩序。
         *这是一个有序的ID列表
         *用逗号分隔。
         */
        public static final String COLUMN_APPLIST_WEEK = "applist_week";
        
        /**
         *定义在一个月内下载次数applist的秩序。
         *这是一个有序的ID列表
         *用逗号分隔。
         */
        public static final String COLUMN_APPLIST_MONTH = "applist_month";
        
        /**
         *定义applist为了在所有应用程序的下载时间。
         *这是一个有序的ID列表
         *用逗号分隔。
         */
        public static final String COLUMN_APPLIST_ALL = "applist_all";
        
        /**
         *定义的排名类别的描述。
         * 
         */
        public static final String COLUMN_RANK_DESC = "rank_description";
    }
    
    //定义推荐表
    public static final class TABLE_RECOMMEND {//推荐列表
        //定义表的名称
        public static final String NAME = "recommend_table";

        /**
         *定义增量的ID，这是该表的主键。
         */
        public static final String COLUMN_ID = "_id";

        /**
         *定义的推荐ID，这是由服务器保留。
         */
        public static final String COLUMN_RECOMMENDID = "recommend_id";
        
        /**
         *定义的推荐名称。
         */
        public static final String COLUMN_NAME = "name";
        /**
         *定义描述的排名推荐
         * 
         */
        public static final String COLUMN_RECOMMEND_DESC = "recommend_description";

        /**
         *定义的推荐图标URL
         */
        public static final String COLUMN_ICON_URL = "icon_url";
        /**
         *定义的推荐图标URL
         */
        public static final String COLUMN_IMAGEA_URL = "image_url";
        /**
         *定义的推荐日期
         */
        public static final String COLUMN_DATE = "date";
        /**
         *定义的推荐目标类型
         */
        public static final String COLUMN_TARGET_TYPE= "target_type";
        /**
         *定义的推荐目标ID
         */
        public static final String COLUMN_TARGET_ID= "target_id";
        
    }
}
