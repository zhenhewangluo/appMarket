package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import com.market.hjapp.App;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.ui.adapter.RecoveryAppListAdapter;
import com.market.hjapp.ui.tasks.DownloadAppListTask;
import com.market.hjapp.ui.tasks.GetAppInfoListTask;
import com.market.hjapp.ui.tasks.GetBackupAndRecoveryListTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

import android.app.Activity;
import android.content.Context;
import android.content.Intent;
import android.os.Bundle;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.AdapterView.OnItemClickListener;

public class RecoveryAppListActivity extends BaseActivity {

	public static final String TAG = "RecoveryAppListActivity";
	
	public Context mContext=RecoveryAppListActivity.this;
    
	public static final int REQUEST_SHOW_DETAILS_FROM_RECOVERY = 101;
	
    private ListView mContentList;
    private RecoveryAppListAdapter mListAdapter;
    
    ImageView all_selected_img;
    private TextView mEmptyText;
    private TextView count_text;
    private int mCurHeaderTab = -1;
//    private String applist="";	
	
    
    private OnItemClickListener mListItemClickListener = new OnItemClickListener() {

        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
        	
        	MyLog.d(TAG, "+++++++++++++OnItemClickListener+++++++++++++");
        	
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
                    resid = R.string.title_recovery;
                    requestid = REQUEST_SHOW_DETAILS_FROM_RECOVERY;
                    break;
                    
                default:
                    throw new RuntimeException("Invalid cur tab: " + mCurHeaderTab);
            }
            i.putExtra(EXTRA_KEY_PARENTNAME, getString(resid));
            startActivityForResult(i, requestid);
        }        
    };
	boolean is_all_selected=true;
    private OnClickListener mCheckAllListener = new OnClickListener(){

		@Override
		public void onClick(View v) {
			
			saveUserLog(3);
			if(is_all_selected==false){
				mListAdapter.checkAll(is_all_selected);
				all_selected_img.setImageResource(R.drawable.no_checked_all_img);
				is_all_selected=true;
			}
			else{
				mListAdapter.checkAll(is_all_selected);
				all_selected_img.setImageResource(R.drawable.checked_all_img);
				is_all_selected=false;
			}
			mListAdapter.checkAll(is_all_selected);
			
			
		}
    	
    };
	
	private OnClickListener mRecoveryListener = new OnClickListener(){

		@Override
		public void onClick(View v) {
			MyLog.d(TAG, "=========click recovery botton====== ");
			saveUserLog(4);
			boolean flag;
			flag = recoveryApp();
			MyLog.d(TAG, "++++++++++flag:"+flag);
			if (!flag) {
				
				Toast.makeText(mContext, mContext.getString(R.string.no_selected_revovery_app), Toast.LENGTH_LONG).show();	
			}
			
		}
    	
    };
    private TaskResultListener mRecoveryTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {
				
                MyLog.e(TAG, "Got error when get reconvery  list");
            	Toast.makeText(mContext, R.string.network_error_msg, Toast.LENGTH_LONG).show();
                return;
				
			}else {
				String applist=(String)res.get("applist");
            	
            	MyLog.d(TAG, "get recovery app list="+applist);
                GeneralUtil.saveBackupList(mContext, applist);
                
                if (applist == null || "".equals(applist)) {
                	
                    MyLog.d(TAG, "+++++++recovery app list no data+++++++++");
    	        	showList(false);
    	            mEmptyText.setText(R.string.empty_recovery_list);
                    
                }else {
                    try
    	    		{
                    	String uncachedAppList = null;
                    	uncachedAppList = DatabaseUtils.getUncachedAppList(mDb, applist);
                    	if (uncachedAppList==null || "".equals(uncachedAppList)) {
                    		 updateRecovryAppList(applist);
						}else {	
                    		  new GetAppInfoListTask(RecoveryAppListActivity.this, mGetAppInfoListTaskResultListener).execute(uncachedAppList);
						}
    	    		} catch (Exception e) {
    	            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
    	            	Toast.makeText(mContext, R.string.network_error_msg, Toast.LENGTH_LONG).show();
    	            }
				}
			}
		}
    	
    };
    
    private TaskResultListener mGetAppInfoListTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {
				
                MyLog.e(TAG, "Got error when get reconvery  list");
                Toast.makeText(mContext, R.string.backup_failed, Toast.LENGTH_LONG).show();
                return;
				
			}else {
				 ArrayList<App> list=(ArrayList<App>)res.get("list");

                if (list == null || list.size()==0) {
                	MyLog.d(TAG, "+++++++get app info list no data+++++++++");
                	
                	String cachedApplist=GeneralUtil.getBackupList(mContext);
                	cachedApplist=DatabaseUtils.getCacheAppList(mDb, cachedApplist);
                	if(cachedApplist==null || "".equals(cachedApplist))
                	{
        	        	showList(false);
        	            mEmptyText.setText(R.string.empty_recovery_list);
        	            count_text.setText(getString(R.string.count_need_recovery_app,0));
                	}
                	else{
                 	   updateRecovryAppList(cachedApplist);
                	}

                    return;
                }
                else {
                	DatabaseUtils.saveAppList(mDb, list);
                	String applist=GeneralUtil.getBackupList(mContext);
                	updateRecovryAppList(applist);
				}
			}
		}
    	
    };
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.recovery_app_list_activity);
		
//        TextView title = (TextView)findViewById(R.id.title_text);
//        Intent intent = getIntent();
//        String mParent;
//        mParent = intent.getStringExtra(EXTRA_KEY_PARENTNAME);
//        title.setText(mParent);
		
        mContentList = (ListView)findViewById(R.id.contentList);
        mContentList.setSelector(R.drawable.c5);
        
        mEmptyText = (TextView)findViewById(R.id.empty_text);

        mListAdapter = new RecoveryAppListAdapter(this);
        mContentList.setAdapter(mListAdapter);
        mContentList.setOnItemClickListener(mListItemClickListener);
		
        count_text=(TextView)findViewById(R.id.count_text);
        
        all_selected_img=(ImageView)findViewById(R.id.all_selected_img);
        all_selected_img.setOnClickListener(mCheckAllListener);

        LongButton recoveryBtn = (LongButton)findViewById(R.id.recovery_btn);
        recoveryBtn.setText(R.string.recovery_selected_app);
        recoveryBtn.setBackgroundResource(R.drawable.btn_long_selector);
        recoveryBtn.setOnClickListener(mRecoveryListener);
        
		setSelectedHeaderTab(0);
	}

	 private void updateRecovryAppList(String applist)
	 {
	    	// when activity is not exist, return
	    	if (!mDb.isOpen())
	    		return;
	    		
	    	ArrayList<App> result = DatabaseUtils.getRecoveryCursor(mContext, applist);
	        
	        if (result == null || result.size()==0) {
	        	showList(false);
	            mEmptyText.setText(R.string.empty_recovery_list);
	            count_text.setText(getString(R.string.count_need_recovery_app,0));
	        } else {
	            showList(true);
	            
	            mListAdapter.setData(result);
	    		int count=-1;
	    		count=mListAdapter.getCount();
	    		count_text.setText(getString(R.string.count_need_recovery_app,count));
	            
	        }
	  }
	 
	  protected void setSelectedHeaderTab(int i) {
	        if (mCurHeaderTab == i) return;
	        
	        mCurHeaderTab = i;
	        
	        saveUserLog(0);
	        
	        mContentList.setSelection(0);
	        
	        if (mCurHeaderTab == 0)
	        {
	            try
	    		{
	    			//TODO	
	    			new GetBackupAndRecoveryListTask(RecoveryAppListActivity.this, mRecoveryTaskResultListener).execute("0","");
	    		} catch (Exception e) {
	            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
	            	Toast.makeText(mContext, R.string.network_error_msg, Toast.LENGTH_LONG).show();
	            }
	    		
	        }
	}
		
	 private void showList(boolean showList) {
		        mContentList.setVisibility(showList ? View.VISIBLE : View.GONE);
		        mEmptyText.setVisibility(showList ? View.GONE : View.VISIBLE);
    }   
	 
	 protected void onActivityResult(int requestCode, int resultCode, Intent data) {
	    	if (requestCode == REQUEST_SHOW_DETAILS_FROM_RECOVERY) {
	    		
            	String applist=GeneralUtil.getBackupList(mContext);
            	updateRecovryAppList(applist);
	        }
	    		
	    	super.onActivityResult(requestCode, resultCode, data);
	  }
	    
	 //save user log
	private void saveUserLog(int action)
    {
	    	// save user log
			GeneralUtil.saveUserLogType3(RecoveryAppListActivity.this, 44, action);
//			if (action==0) {
//				tracker.trackPageView("/"+TAG);
//			}
//			else {
//				tracker.trackEvent(""+3, ""+44, "", action);
//			}
    }

	
	public boolean recoveryApp() {
		
		
		if (mListAdapter.isSelected == null || mListAdapter.isSelected.length==0) {
			return false;
		}
		// flag stands for isSelected[] all false
        boolean falg=false;
        for (int i = 0; i < mListAdapter.isSelected.length; i++) {
//			MyLog.d(TAG, "+++++++++isSelected["+i+"]:"+isSelected[i]);
        	if (mListAdapter.isSelected[i]==true) {
        		falg=true;
        		break;
			}
		}
        
        if (!falg) {
        	return false; 
		}
        
        int size = mListAdapter.isSelected.length;
        
        mAppIDList = new int[size];
        mDbIDList = new int[size];
        mNameList = new String[size];
        mDownloadCountList = new int[size];
        	
        String appidList = "";
        for (int i = 0; i < size; i++) {
        	
        	App app = (App)mListAdapter.getItem(i);
			if (app != null) {
				if (mListAdapter.isSelected[i] == true) {
					
					if (appidList.equals(""))
						appidList += app.getId();
					else
						appidList += "," + app.getId();
				}
			}
			
			mAppIDList[i] = app.getId();
			mDbIDList[i] = app.getDbId();
			mNameList[i] = app.getName();
			mDownloadCountList[i] = app.getDownloadCount();
				
    	}
        
        try
    	{
        	new DownloadAppListTask((Activity)mContext, mInstallTaskResultListener)
            .execute(appidList, mContext.getString(R.string.tabtitle_recovery));
    	} catch (RejectedExecutionException e) {
        	 MyLog.e(TAG, "Got exception when execute asynctask!", e);
        }
		
//		for (int i = 0; i < isSelected.length; i++) {
//			if (isSelected[i] == true) {
//
//				App app = mData.get(i);
//				if (app != null) {
//    	        	try
//    	        	{
//    	        		
//    		        	new ProcessInstallTask((Activity)mContext, mInstallTaskResultListener)
//    		            .execute(app.getId()+"", app.getPayId(), mContext.getString(R.string.tabtitle_downloaded)
//    		            		,app.getDbId()+"", app.getName(), app.getDownloadCount()+"");
//    	        	} catch (RejectedExecutionException e) {
//    	            	 MyLog.e(TAG, "Got exception when execute asynctask!", e);
//    	            }
//				}
//			}
//		}
		return true;
	}
	
	private int[] mAppIDList;
	private int[] mDbIDList;
	private String[] mNameList;
	private int[] mDownloadCountList;
	
	private TaskResultListener mInstallTaskResultListener = new TaskResultListener() {

        @Override
        public void onTaskResult(boolean success, HashMap<String, Object> res) {
        	
        	if (!success) {
            	if (res == null)
            	{
            		Toast.makeText(mContext, R.string.error_http_timeout, Toast.LENGTH_LONG).show();
            	}
            	else
            	{
                    Toast.makeText(mContext, (String)res.get("errmsg"), Toast.LENGTH_LONG).show();
            	}
                
                return;
            }
        	
			String appidList =  (String)res.get("appid_list");
        	String urlList = (String)res.get("url_list");
        	String downloadIdList = (String)res.get("download_id_list");
        	
        	MyLog.d(TAG, "appidList: " + appidList + ", urlList: " + urlList + "downloadIdList:" + downloadIdList);
        	
        	String DBIdList = "";
        	String nameList = "";
        	String downloadCountList = "";
        	
        	String[] list = appidList.split(",");
        	for (String id : list)
        	{
        		MyLog.d(TAG, "id: " + id);
        		
        		for (int i =0; i< mAppIDList.length; i++)
        		{
        			MyLog.d(TAG, "mAppIDList: " + mAppIDList[i]);
        			
	        		if (id.equals(mAppIDList[i]+""))
	        		{
	        			if (DBIdList.equals(""))
	        			{
	        				MyLog.d(TAG, "mDbIDList[i]: " + mDbIDList[i]);
	        				DBIdList += mDbIDList[i];
	        				nameList +=  mNameList[i];
	        				downloadCountList += mDownloadCountList[i];
	        			}
	        			else
	        			{
	        				MyLog.d(TAG, "mDbIDList[i]: " + mDbIDList[i]);
	        				DBIdList += "," + mDbIDList[i];
	        				nameList += "," +  mNameList[i];
	        				downloadCountList += "," + mDownloadCountList[i];
	        			}
	        			break;
	        		}
	        		else
	        		{
	        			MyLog.d(TAG, "!=");
	        		}
        		}
        	}
        	
        	MyLog.d(TAG, "DBIdList: " + DBIdList + ", nameList: " + nameList + "downloadCountList:" + downloadCountList);
        	
        	Intent intent = new Intent(mContext, AppService.class);
        	intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_DOWNLOAD_LIST);
        	intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_LIST_DBID, DBIdList);
        	intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_LIST_ID, downloadIdList);
        	intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_LIST_URL, urlList);
        	intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_LIST_APPID, appidList);
        	intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_LIST_APPNAME, nameList);
        	intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_LIST_COUNT, downloadCountList);
          
        	mContext.startService(intent);
        	
        	finish();
        	
//            String downloadPath = (String)res.get("location");
//            String downloadId = (String)res.get("download_id");
//            MyLog.d(TAG, "download id: " + downloadId + ", download path: " + downloadPath);
//
//            // create download app in provider
//            int dbid = Integer.parseInt((String)res.get("dbid"));
//            int appid = Integer.parseInt((String)res.get("appid"));
//            String name = (String)res.get("name");
//            int downloadCount = Integer.parseInt((String)res.get("download_count"));
//            	
//
//            Intent intent = new Intent(mContext, AppService.class);
//            intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_DOWNLOAD);
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, dbid);
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_ID, downloadId);
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_URL, downloadPath);
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPID, appid);
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPNAME, name);
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_COUNT, downloadCount);
//            
//            mContext.startService(intent);
        }
        
    };
}
