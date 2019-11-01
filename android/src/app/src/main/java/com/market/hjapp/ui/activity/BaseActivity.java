
package com.market.hjapp.ui.activity;

import com.market.hjapp.ImageLoader;
import com.market.hjapp.database.DatabaseHelper;

import android.app.Activity;
import android.database.sqlite.SQLiteDatabase;
import android.os.Bundle;

public abstract class BaseActivity extends Activity {

    protected static final String EXTRA_KEY_PARENTNAME = "key_parent";
    protected SQLiteDatabase mDb;
    
    public static boolean isMarketStarted = false;
    
    public static boolean isMarketClosed = false;
    
    
//    GoogleAnalyticsTracker tracker;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        

        ImageLoader.getInstance().clearDownloadList();
        
        mDb = new DatabaseHelper(this).getWritableDatabase();
        
        isMarketClosed = false;
    }

    @Override
    protected void onStart() {
        super.onStart();
    }
    
    @Override
    protected void onResume() {
        super.onResume();
        
        if (isMarketClosed)
        {
        	finish();
        }
    }
    
    @Override
    protected void onStop() {
        super.onStop();
        
    }

    @Override
    protected void onDestroy() {
        super.onDestroy();
        
        mDb.close();
        
//        // Stop the tracker when it is no longer needed.
//        tracker.dispatch();
//        tracker.stop();
    }
}
