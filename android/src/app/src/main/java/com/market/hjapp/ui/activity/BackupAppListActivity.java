package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;

import com.market.hjapp.App;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.adapter.BackupAppListAdapter;
import com.market.hjapp.ui.tasks.GetAppInfoListTask;
import com.market.hjapp.ui.tasks.GetBackupAndRecoveryListTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.LongButton;

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

public class BackupAppListActivity extends BaseActivity {
	
	public static final String TAG = "BackupAppListActivity";
	public Context mContext=BackupAppListActivity.this;
    
	public static final int REQUEST_SHOW_DETAILS_FROM_BACKUP = 101;
	
    private ListView mContentList;
    private BackupAppListAdapter mListAdapter;
    
    private TextView mEmptyText;
    private TextView count_text;
    ImageView all_selected_img;
    
    private int mCurHeaderTab = -1;
//    private String applist="";
     
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
                    resid = R.string.title_backup;
                    requestid = REQUEST_SHOW_DETAILS_FROM_BACKUP;
                    break;
                    
                default:
                    throw new RuntimeException("Invalid cur tab: " + mCurHeaderTab);
            }
            i.putExtra(EXTRA_KEY_PARENTNAME, getString(resid));
            startActivityForResult(i, requestid);
        }        
    };
    String backupapplist="";
	private OnClickListener mBackupListener = new OnClickListener(){

		@Override
		public void onClick(View v) {
			
			saveUserLog(4);
			int count=mListAdapter.getCount();
			for(int i=0;i<count;i++){
				if(mListAdapter.isSelected[i]){
					if("".equals(backupapplist))
						backupapplist=""+mListAdapter.getItemId(i);
					else
						backupapplist=backupapplist+","+mListAdapter.getItemId(i);
				}
			}
			
			if(backupapplist==null || "".equals(backupapplist)){
				Toast.makeText(mContext, R.string.no_backup_app, Toast.LENGTH_LONG).show();
				return;
			}
			
			String pref_applist=GeneralUtil.getBackupList(mContext);
			if(!"".equals(pref_applist)){
				backupapplist=pref_applist+","+backupapplist;
			}
			
				
			try
			{
				//TODO	backupapplist
				MyLog.d(TAG, "loading server>>>backup app list="+backupapplist);
				new GetBackupAndRecoveryListTask(BackupAppListActivity.this, mBackupTaskResultListener).execute("1",backupapplist);
			} catch (Exception e) {
	        	MyLog.e(TAG, "Got exception when execute asynctask!", e);
	        	Toast.makeText(mContext, R.string.network_error_msg, Toast.LENGTH_LONG).show();
	        }
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
    private TaskResultListener mRecoveryTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {
				
                MyLog.e(TAG, "Got error when get reconvery  list");
                return;
				
			}else {
				
				String applist=(String)res.get("applist");
                GeneralUtil.saveBackupList(mContext, applist);
                
                if (applist == null || "".equals(applist)) {
                	
                    MyLog.d(TAG, "+++++++recovery app list no data+++++++++");
                    updateBackupAppList(applist);

                    
                }else {
    				MyLog.d(TAG, "get recovery app list="+applist);
                    try
    	    		{   
                    	String uncachedAppList = null;
                    	uncachedAppList = DatabaseUtils.getUncachedAppList(mDb, applist);
                    	if (uncachedAppList==null || "".equals(uncachedAppList)) {
                    		 updateBackupAppList(applist);
						}else {	
                    		  new GetAppInfoListTask(BackupAppListActivity.this, mGetAppInfoListTaskResultListener).execute(uncachedAppList);
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
        	            mEmptyText.setText(R.string.empty_backup_list);
        	            count_text.setText(getString(R.string.count_no_backup_app,0));
                	}
                	else{
                 	   updateBackupAppList(cachedApplist);
                	}
                    return;
                }
                else {
                	DatabaseUtils.saveAppList(mDb, list);
                	String applist=GeneralUtil.getBackupList(mContext);	
            		updateBackupAppList(applist);
				}
			}
		}
    	
    };
    private TaskResultListener mBackupTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {
				
                MyLog.e(TAG, "Got error when get reconvery  list");
                Toast.makeText(mContext, R.string.backup_failed, Toast.LENGTH_LONG).show();
                return;
				
			}else {
				String applist=(String)res.get("applist");

                if (applist == null || "".equals(applist)) {
//                	MyLog.d(TAG, "+++++++backup app list no data+++++++++");
                    return;
                }
                else {
                	MyLog.d(TAG, "backup app list="+applist);
//                    GeneralUtil.saveBackupList(mContext, applist);
				}
                
                Toast.makeText(mContext, R.string.backup_susseccful, Toast.LENGTH_LONG).show();
                BackupAppListActivity.this.finish();
                
			}
		}
    	
    };
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		// TODO Auto-generated method stub
		super.onCreate(savedInstanceState);
		setContentView(R.layout.backup_app_list_activity);
		
//        Intent intent = getIntent();
//        TextView title = (TextView)findViewById(R.id.title_text);
//        String mParent;
//        mParent = intent.getStringExtra(EXTRA_KEY_PARENTNAME);
//        title.setText(mParent);
        
        mContentList = (ListView)findViewById(R.id.contentList);
        mContentList.setSelector(R.drawable.c5);
        
        mEmptyText = (TextView)findViewById(R.id.empty_text);

        mListAdapter = new BackupAppListAdapter(this);
        mContentList.setAdapter(mListAdapter);
        mContentList.setOnItemClickListener(mListItemClickListener);
        
        count_text=(TextView)findViewById(R.id.count_text);
        
        all_selected_img=(ImageView)findViewById(R.id.all_selected_img);
        all_selected_img.setOnClickListener(mCheckAllListener);
        
        LongButton backupBtn = (LongButton)findViewById(R.id.backup_btn);
        backupBtn.setText(R.string.backup_all_app);
        backupBtn.setBackgroundResource(R.drawable.btn_long_selector);
        backupBtn.setOnClickListener(mBackupListener);

		setSelectedHeaderTab(0);
	}

	@Override
	protected void onStart() {
		super.onStart();
	}

	@Override
	protected void onResume() {
		super.onResume();

//		updateBackupAppList();
	}

	@Override
	protected void onStop() {

		super.onStop();
	}
	
	 private void updateBackupAppList(String applist)
	 {
//		    if (applist==null || "".equals(applist)) {
//				
//			}
	    	// when activity is not exist, return
	    	if (!mDb.isOpen())
	    		return;
	    	MyLog.d(TAG, "Incoming parameters applist="+applist);	
	    	ArrayList<App> appPreBackupList = DatabaseUtils.getBackupCursor(mDb,applist);
	        
	        if (appPreBackupList == null || appPreBackupList.size()==0) {
	        	showList(false);
	            mEmptyText.setText(R.string.empty_backup_list);
	            count_text.setText(getString(R.string.count_no_backup_app,0));
	        } else {
	            showList(true);
	            
	            mListAdapter.setData(appPreBackupList);
	    		int count=-1;
	    		count=mListAdapter.getCount();
	    		count_text.setText(getString(R.string.count_no_backup_app,count));
	            
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
	    			new GetBackupAndRecoveryListTask(BackupAppListActivity.this, mRecoveryTaskResultListener).execute("0","");
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
	    	if (requestCode == REQUEST_SHOW_DETAILS_FROM_BACKUP) {
	            // init applist
	            String pref_applist=GeneralUtil.getBackupList(mContext);
	            MyLog.d(TAG, "onActivityResult>>>pref_applist="+pref_applist);
	    		updateBackupAppList(pref_applist);
	        }
	    		
	    	super.onActivityResult(requestCode, resultCode, data);
	  }
	    
	 //save user log
	 private void saveUserLog(int action)
	 {
	    	// save user log
			GeneralUtil.saveUserLogType3(mContext, 43, action);
//			if (action==0) {
//				tracker.trackPageView("/"+TAG);
//			}
//			else {
//				tracker.trackEvent(""+3, ""+43, "", action);
//			}
	 }
}
