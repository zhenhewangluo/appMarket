
package com.market.hjapp.database;

import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;



import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.database.sqlite.SQLiteOpenHelper;

public class DatabaseHelper extends SQLiteOpenHelper {
    private static final String DB_NAME = "market.db";
    private static final int DB_VERSION = 11;
    // DB Version 6, add column: COLUMN_NEW_APP_NAME
    // DB Version 7, remove rate_down, rate_up
    //               add score, score_count, app_language
    // DB Version 8, add rank table
    // DB Version 9, add recommend table
    //               add total_rate_num in APP table
    // DB Version 10, add app list table
    //                remove rank table
    //                add type & last update time into cate table
    //                remove newapp_name from cate table
    //                change cateid in cate_table to unique key
    //                change app_count, parent_id in cate_table to integer
    //                add last update time into app table
    // DB Version 11, add order in cate table

    private Context mContext;
    
    public DatabaseHelper(Context context) {
    	super(context, DB_NAME, null, DB_VERSION);
        
        mContext = context;
    }

    @Override
    public void onCreate(SQLiteDatabase db) {
        // create APP table
        db.execSQL("CREATE TABLE " + DatabaseSchema.TABLE_APP.NAME
                + "(" + DatabaseSchema.TABLE_APP.COLUMN_ID + " INTEGER PRIMARY KEY AUTOINCREMENT, "
                + DatabaseSchema.TABLE_APP.COLUMN_APPID + " INTEGER UNIQUE, "
                + DatabaseSchema.TABLE_APP.COLUMN_AUTHOR + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_DESCRIP + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_CNT + " INTEGER, "
                + DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_PATH + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_DOWNLOAD_ID + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_ICON_URL + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_INT_VERSION + " INTEGER, "
                + DatabaseSchema.TABLE_APP.COLUMN_LOCALPATH + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_MY_RATING + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_NAME + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_PRICE + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_PACKAGENAME + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_SCORE + " INTEGER, "
                + DatabaseSchema.TABLE_APP.COLUMN_SCORE_CNT + " INTEGER, "
                + DatabaseSchema.TABLE_APP.COLUMN_COMMENT_CNT + " INTEGER, "
                + DatabaseSchema.TABLE_APP.COLUMN_LANGUAGE + " INTEGER, "
                + DatabaseSchema.TABLE_APP.COLUMN_SCREENSHOT_URL + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_SLOGAN + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_VERSION + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_SIZE + " TEXT, "
                + DatabaseSchema.TABLE_APP.COLUMN_STATUS + " INTEGER, "
                + DatabaseSchema.TABLE_APP.COLUMN_DOWNLOADED_SIZE + " INTEGER, "
                + DatabaseSchema.TABLE_APP.COLUMN_LAST_UPDATE_TIME + " INTEGER, "                
                + DatabaseSchema.TABLE_APP.COLUMN_VERSION_NUM + " INTEGER);");

        // create Category table
        db.execSQL("CREATE TABLE " + DatabaseSchema.TABLE_CATEGORY.NAME
                + "(" + DatabaseSchema.TABLE_CATEGORY.COLUMN_ID + " INTEGER PRIMARY KEY AUTOINCREMENT, "
                + DatabaseSchema.TABLE_CATEGORY.COLUMN_CATEID + " INTEGER UNIQUE, "
                + DatabaseSchema.TABLE_CATEGORY.COLUMN_TYPE + " INTEGER, "
                + DatabaseSchema.TABLE_CATEGORY.COLUMN_PARENT_ID + " INTEGER, "
                + DatabaseSchema.TABLE_CATEGORY.COLUMN_UPDATE_INTERVAL + " INTEGER, "
                + DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_DESC + " TEXT, "
                + DatabaseSchema.TABLE_CATEGORY.COLUMN_APPCOUNT + " INTEGER, "
                + DatabaseSchema.TABLE_CATEGORY.COLUMN_ICON_URL + " TEXT, "
                + DatabaseSchema.TABLE_CATEGORY.COLUMN_CATE_ORDER + " INTEGER, "
                + DatabaseSchema.TABLE_CATEGORY.COLUMN_NAME + " TEXT);");
        
//        // create rank table
//        db.execSQL("CREATE TABLE " + DatabaseSchema.TABLE_RANK.NAME
//                + "(" + DatabaseSchema.TABLE_RANK.COLUMN_ID + " INTEGER PRIMARY KEY AUTOINCREMENT, "
//                + DatabaseSchema.TABLE_RANK.COLUMN_APPLIST_WEEK + " TEXT, "
//                + DatabaseSchema.TABLE_RANK.COLUMN_APPLIST_MONTH+ " TEXT, "
//                + DatabaseSchema.TABLE_RANK.COLUMN_APPLIST_ALL + " TEXT, "
//                + DatabaseSchema.TABLE_RANK.COLUMN_RANKID + " TEXT, "
//                + DatabaseSchema.TABLE_RANK.COLUMN_ICON_URL + " TEXT, "
//                + DatabaseSchema.TABLE_RANK.COLUMN_NAME + " TEXT, "
//                + DatabaseSchema.TABLE_RANK.COLUMN_RANK_DESC  + " TEXT);");
        
        // create recommend table
        db.execSQL("CREATE TABLE " + DatabaseSchema.TABLE_RECOMMEND.NAME
                + "(" + DatabaseSchema.TABLE_RECOMMEND.COLUMN_ID + " INTEGER PRIMARY KEY AUTOINCREMENT, "
                + DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMENDID + " INTEGER UNIQUE, "
                + DatabaseSchema.TABLE_RECOMMEND.COLUMN_NAME+ " TEXT, "
                + DatabaseSchema.TABLE_RECOMMEND.COLUMN_ICON_URL + " TEXT, "
                + DatabaseSchema.TABLE_RECOMMEND.COLUMN_IMAGEA_URL + " TEXT, "
                + DatabaseSchema.TABLE_RECOMMEND.COLUMN_DATE + " TEXT, "
                + DatabaseSchema.TABLE_RECOMMEND.COLUMN_TARGET_TYPE + " INTEGER, "
                + DatabaseSchema.TABLE_RECOMMEND.COLUMN_TARGET_ID + " TEXT, "
                + DatabaseSchema.TABLE_RECOMMEND.COLUMN_RECOMMEND_DESC  + " TEXT);");
        
        // create applist table
        db.execSQL("CREATE TABLE " + DatabaseSchema.TABLE_CATEGORY_APP_LIST.NAME
                + "(" + DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_ID + " INTEGER PRIMARY KEY AUTOINCREMENT, "
                + DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_CATEID + " INTEGER UNIQUE, "
                + DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST1_LAST_UPDATE_TIME + " INTEGER, "
                + DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST_1 + " TEXT, "
                + DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST2_LAST_UPDATE_TIME + " INTEGER, "
                + DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST_2 + " TEXT, "
                + DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST3_LAST_UPDATE_TIME + " INTEGER, "
                + DatabaseSchema.TABLE_CATEGORY_APP_LIST.COLUMN_APPLIST_3 + " TEXT);");
    }

    @Override
    public void onUpgrade(SQLiteDatabase db, int oldVersion, int newVersion) {
        // drop all table
        db.execSQL("DROP TABLE IF EXISTS " + DatabaseSchema.TABLE_APP.NAME);
        db.execSQL("DROP TABLE IF EXISTS " + DatabaseSchema.TABLE_CATEGORY.NAME);
//        db.execSQL("DROP TABLE IF EXISTS " + DatabaseSchema.TABLE_RANK.NAME);
        db.execSQL("DROP TABLE IF EXISTS " + DatabaseSchema.TABLE_RECOMMEND.NAME);
        db.execSQL("DROP TABLE IF EXISTS " + DatabaseSchema.TABLE_CATEGORY_APP_LIST.NAME);
        
        // re-create all table
        onCreate(db);
        
        //clear some SharedPreferences data
        GeneralUtil.saveCateTime(mContext, null);
        GeneralUtil.saveRecommendTime(mContext, null);
        GeneralUtil.clearUploadTime(mContext, ConstantValues.PREF_KEY_CATEGORY_UPDATE_TIME);
    }
}
