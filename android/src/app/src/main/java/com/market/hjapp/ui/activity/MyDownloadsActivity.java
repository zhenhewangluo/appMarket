
package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.sqlite.SQLiteDatabase;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AbsListView;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.AbsListView.OnScrollListener;
import android.widget.AdapterView.OnItemClickListener;

import com.market.hjapp.App;
import com.market.hjapp.AppTabSpec;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.AppTabSpec.AppLoadResultListener;
import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseSchema;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.service.UploadLocalAppService;
import com.market.hjapp.ui.adapter.MyDownloadListAdapter;
import com.market.hjapp.ui.adapter.MyDownloadListAdapter.AppActionResultListener;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;

public class MyDownloadsActivity extends BaseActivity{

	private static final String TAG = "MyDownloadsActivity";
	
	public static final int REQUEST_SHOW_DETAILS_FROM_DOWNLOADS = 101;
	
    private ListView mContentList;
    private MyDownloadListAdapter mListAdapter;

    int mCurHeaderTab = -1;
    
    ArrayList<App> appMyDownloadList;
    ArrayList<App> appDownloadingList;
    ArrayList<App> appPreInstallList;
    ArrayList<App> appHasUpdateList;
    ArrayList<App> appInstalledList;

    private OnItemClickListener mListItemClickListener = new OnItemClickListener() {

        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        	
        	saveUserLog(1);
        	
            App app = (App)mListAdapter.getItem(position);
            
            if (app == null)
            	return;
            
            Intent i = new Intent(getApplicationContext(), AppDetailActivity.class);
            i.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app.getId());
            
            int resid;
            int requestid;
            switch (mCurHeaderTab) {
                case 0:
                    resid = R.string.tabtitle_downloaded;
                    requestid = REQUEST_SHOW_DETAILS_FROM_DOWNLOADS;
                    break;
                    
                default:
                    throw new RuntimeException("Invalid cur tab: " + mCurHeaderTab);
            }
            i.putExtra(EXTRA_KEY_PARENTNAME, getString(resid));
            startActivityForResult(i, requestid);
        }        
    };
    
//    private View mFirstHeaderTab;
//    private View mFirstPressedHeaderTab;
//    private View mSecondHeaderTab;
//    private View mSecondPressedHeaderTab;
    private TextView mEmptyText;
    
    public boolean mDefaultHasUpdate;
    

    
    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.my_downloads_activity);
        MyLog.d(TAG, "MyDownloadsActivity onCreate>>>>>>>>>>>>>>>>>>>>");
        
        saveUserLog(0); 
        initHeaderTabs();
        
//        TextView title = (TextView)findViewById(R.id.top_title);
        
        Intent intent = getIntent();
        mDefaultHasUpdate = intent.getExtras().getBoolean("goto_hasupdate_cate"); 
//        String mParent;
//        mParent = intent.getStringExtra(EXTRA_KEY_PARENTNAME);
//        title.setText(mParent);
        
        if (mDefaultHasUpdate)
        {
        	GeneralUtil.saveUserLogType3(this, 37, 0);
        	GeneralUtil.sendServerClientOpen(this);
        }

        mContentList = (ListView)findViewById(R.id.contentList);
        mContentList.setSelector(R.drawable.c5);
        
        mEmptyText = (TextView)findViewById(R.id.empty_text);

        mListAdapter = new MyDownloadListAdapter(this);
        mListAdapter.setAppActionResultListener(mAppActionResultListener);
        mContentList.setAdapter(mListAdapter);
        mContentList.setOnItemClickListener(mListItemClickListener);
        
        
        // For recommend app list page
//        mLoadingBackground = findViewById(R.id.loading_bg);

        setSelectedHeaderTab(0);
    }
    
    
    private AppActionResultListener mAppActionResultListener = new AppActionResultListener() {

		@Override
		public void onAppActionResult() {
			updateMyDownloadList();
			
		}
    	
    };

    @Override
    protected void onStart() {
    	mDownloadReceiver = new DownloadStatusReceiver();
        IntentFilter downloadFilter = new IntentFilter();
        downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_COMPLETE);
        downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_UPDATE);
        downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_ERROR);
        
        registerReceiver(mDownloadReceiver, downloadFilter);

        super.onStart();
    }
    
    @Override
    protected void onResume() {
        super.onResume();

        updateMyDownloadList();
    }
    
    @Override
    protected void onStop() {
        unregisterReceiver(mDownloadReceiver);

        super.onStop();
    }
    
    private DownloadStatusReceiver mDownloadReceiver;

    private void initHeaderTabs() {
//        mFirstHeaderTab = findViewById(R.id.first_headertab);
//        mFirstPressedHeaderTab = findViewById(R.id.first_headertab_pressed);

//        mSecondHeaderTab = findViewById(R.id.second_headertab);
//        mSecondPressedHeaderTab = findViewById(R.id.second_headertab_pressed);
        
        // add click listener
//        mFirstHeaderTab.setOnClickListener(new OnClickListener() {
//
//            public void onClick(View v) {
//                setSelectedHeaderTab(0);
//            }
//            
//        });

//        mSecondHeaderTab.setOnClickListener(new OnClickListener() {
//
//            public void onClick(View v) {
//                setSelectedHeaderTab(1);
//            }
//            
//        });
    }

    @SuppressWarnings("unchecked")
	protected void setSelectedHeaderTab(int i) {
        if (mCurHeaderTab == i) return;
        
        mCurHeaderTab = i;
        
//        mFirstHeaderTab.setVisibility(i == 0 ? View.GONE : View.VISIBLE);
//        mFirstPressedHeaderTab.setVisibility(i == 0 ? View.VISIBLE: View.GONE);

//        mSecondHeaderTab.setVisibility(i == 1 ? View.GONE : View.VISIBLE);
//        mSecondPressedHeaderTab.setVisibility(i == 1 ? View.VISIBLE: View.GONE);
        
        mContentList.setSelection(0);
        
        if (mCurHeaderTab == 0)
        {
        	if (UploadLocalAppService.sThreadRunning)
        	{
//        		mLoadingBackground.setVisibility(View.VISIBLE);
        	}
        	else
        	{
//        		mLoadingBackground.setVisibility(View.GONE);
            	
            	updateMyDownloadList();
        	}
        	
        }
    }

    private void showList(boolean showList) {
        mContentList.setVisibility(showList ? View.VISIBLE : View.GONE);
        mEmptyText.setVisibility(showList ? View.GONE : View.VISIBLE);
    }

    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
    	if (requestCode == REQUEST_SHOW_DETAILS_FROM_DOWNLOADS) {
    		updateMyDownloadList();
        }
    		
    	super.onActivityResult(requestCode, resultCode, data);
    }
    
    
//    private View mLoadingBackground;
    
    private void updateMyDownloadList()
    {
    	// when activity is not exist, return
    	if (!mDb.isOpen())
    		return;
    		
    	HashMap<String, Object> result = DatabaseUtils.getMyDownloadCursor(mDb);
        
        if (result == null) {
        	showList(false);
            mEmptyText.setText(R.string.empty_downloaded_list);
        } else {
            showList(true);
            
            mListAdapter.setData((ArrayList<App>)result.get("appDownloadingList"), 
            					(ArrayList<App>)result.get("appPreInstallList"), 
            					(ArrayList<App>)result.get("appHasUpdateList"), 
            					(ArrayList<App>)result.get("appInstalledList"));
            
            if (mDefaultHasUpdate)
            {
            	mContentList.setSelection(mListAdapter.getHasUpdateCateIndex());
            	mDefaultHasUpdate = false;
            }
        }
    }
    
    private class DownloadStatusReceiver extends BroadcastReceiver {

        @Override
        public void onReceive(Context context, Intent intent) {
        	final String action = intent.getAction();
            MyLog.d(TAG, "DownloadStatusReceiver >>> onReceive >>> action: " + action);
            
            final int appid =intent.getIntExtra(AppService.DOWNLOAD_APP_PID, -1);
            MyLog.d(TAG, "appid: " + appid);
            
            int appstatus = -1;
            if (AppService.BROADCAST_DOWNLOAD_UPDATE.equals(action)) {
                appstatus = App.DOWNLOADING;
            } else if (AppService.BROADCAST_DOWNLOAD_COMPLETE.equals(action)) {
                appstatus = App.DOWNLOADED;
            } else if (AppService.BROADCAST_DOWNLOAD_ERROR.equals(action)) {
                appstatus = App.INIT;
            } else {
                throw new RuntimeException("got unknown action: " + action);
            }
            
            if (mCurHeaderTab == 0)
            {
            	updateMyDownloadList();
            }
            
        }
        
    }
	
	// save user log
	private void saveUserLog(int action)
    {
//    	GeneralUtil.saveUserLogType3(MyDownloadsActivity.this, 19, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+19, "", action);
//		}
    }

}