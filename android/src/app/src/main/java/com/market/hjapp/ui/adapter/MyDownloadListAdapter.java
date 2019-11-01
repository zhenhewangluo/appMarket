
package com.market.hjapp.ui.adapter;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;


import android.app.Activity;
import android.app.NotificationManager;
import android.content.Context;
import android.content.Intent;
import android.graphics.Color;
import android.text.TextUtils;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.View.OnClickListener;
import android.widget.BaseAdapter;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.App;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.service.download.FileManager;
import com.market.hjapp.service.download.FileManipulation;
import com.market.hjapp.ui.tasks.ProcessInstallTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.AppItemView;
import com.market.hjapp.ui.view.LongButton;

public class MyDownloadListAdapter extends BaseAdapter {
    private static final String TAG = "MyDownloadListAdapter";
    
    private LayoutInflater mInflater;
    
    private Context mContext;
    
    ArrayList<App> section1List;
    ArrayList<App> section2List;
    ArrayList<App> section3List;
    ArrayList<App> section4List;
    
    int section1Index;
    int section2Index;
    int section3Index;
    int section4Index;
    int total;
    
    ArrayList<App> recommendAppList;
    
    public int getHasUpdateCateIndex()
    {
    	if (section3Index != -1)
    		return section3Index;
    	else
    		return 0;
    }
    
    public MyDownloadListAdapter(Context context) {
    	
    	mContext = context;
        mInflater = LayoutInflater.from(context);
        
        section1List = new ArrayList<App>();
        section2List = new ArrayList<App>();
        section3List = new ArrayList<App>();
        section4List = new ArrayList<App>();
        
        total = 0;
    }

    @Override
    public int getCount() {
        //MyLog.d(TAG, "data count: " + mData.size());
        return total;
    }

    @Override
    public App getItem(int pos) {
        return getApp(pos);
    }
    
    private App getApp(int pos)
    {
    	if (recommendAppList != null)
    		return recommendAppList.get(pos);
    		
    	 MyLog.d(TAG, "getApp pos = " + pos);
    	 App app = null;
    	 
    	 if (pos == section1Index ||
			 pos == section2Index ||
			 pos == section3Index ||
			 pos == section4Index )
    		 return null;
    	 
    	 if (section1Index != -1 && pos <= section1Index + section1List.size())
    		 app = section1List.get(pos - section1Index - 1);
    	 else if (section2Index != -1 && pos <= section2Index + section2List.size())
    		 app = section2List.get(pos - section2Index - 1);
    	 else if (section3Index != -1 && pos <= section3Index + section3List.size())
    		 app = section3List.get(pos - section3Index - 1);
    	 else if (section4Index != -1 && pos <= section4Index + section4List.size())
    		 app = section4List.get(pos - section4Index - 1);
    		 
    		 
    	 return app;
    }
    
    @Override
    public View getView(final int position, View convertView, ViewGroup parent) {
        if (position == section1Index ||
    		position == section2Index ||
    		position == section3Index ||
    		position == section4Index )
        {
        	TextView tv;
        	if (convertView == null || !(convertView instanceof TextView))
        	{
        		 tv = (TextView)mInflater.inflate(R.layout.group_header, null);
        	}
        	else
        		tv =  (TextView)convertView;
        	
        	MyLog.d(TAG, "section1List.size() " + section1List.size());
        	
        	if (position == section1Index)
        		tv.setText(mContext.getString(R.string.groupheader_downloading, section1List.size()));
        	else if (position == section2Index)
        		tv.setText(mContext.getString(R.string.groupheader_downloaded, section2List.size()));
        	else if (position == section3Index)
        		tv.setText(mContext.getString(R.string.groupheader_update, section3List.size()));
        	else if (position == section4Index)
        		tv.setText(mContext.getString(R.string.groupheader_installed, section4List.size()));
        		
        	return tv;
        }

        AppItemView itemView;
        if (convertView == null || !(convertView instanceof AppItemView))
        {
            itemView = (AppItemView)mInflater.inflate(R.layout.app_item, null);
        } else {
            itemView = (AppItemView)convertView;
        }
        
        View layout_show_downloadtime =(View)itemView.findViewById(R.id.layout_show_downloadtime);
        View info = (View)itemView.findViewById(R.id.app_info);
        View info1 = (View)itemView.findViewById(R.id.app_info1);
        View action =  (View)itemView.findViewById(R.id.app_action);
        LongButton actionBtn = (LongButton)itemView.findViewById(R.id.app_action_btn);
        
        TextView mPrice=(TextView)itemView.findViewById(R.id.price);
        TextView mPrice1=(TextView)itemView.findViewById(R.id.price1);
        mPrice.setVisibility(View.GONE);
        mPrice1.setVisibility(View.GONE);
        
        layout_show_downloadtime.setVisibility(View.GONE);
        info.setVisibility(View.GONE);
        info1.setVisibility(View.GONE);
        action.setVisibility(View.VISIBLE);
        
        MyLog.d(TAG, "position " + position + 
        			" section1Index" + section1Index +
        			" section2Index" + section2Index +
        			" section3Index" + section3Index +
        			" section4Index" + section4Index);
        
        if (section1Index != -1 && position <= section1Index + section1List.size())
        {
        	actionBtn.setHighLightButton(false);
            actionBtn.setTextColor(Color.WHITE);
            actionBtn.setText(R.string.status_downloading_action);
            actionBtn.setOnClickListener(new OnClickListener()
            {

    			@Override
    			public void onClick(View v) {
    				
    				saveUserLog(2);
    				
    				App app = getApp(position);
    				
    				DatabaseUtils.resetAppToInit(mContext, app.getDbId());
    	            
    	            String localPath = DatabaseUtils.getInstallingAppLocalPath(mContext, app.getId());
    	            if (!TextUtils.isEmpty(localPath)) {
    	            	File currFile = new File(localPath);
    	                currFile.delete();
    	            }
    	            
//    	        	updateStatus(App.INIT, 0);
    	        	
    	        	Intent intent = new Intent(mContext, AppService.class);
    	            intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_CANCEL);
    	            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, app.getDbId());
    	            
    	            mContext.startService(intent);
    	            
    	            mAppActionResultListener.onAppActionResult();
    				
    			}
            	
            });
        }
        else if (section2Index != -1 && position <= section2Index + section2List.size())
        {
        	actionBtn.setHighLightButton(true);
        	actionBtn.setTextColor(0xff3f3f3f);
            actionBtn.setText(R.string.status_downloaded_action);
            actionBtn.setOnClickListener(new OnClickListener()
            {

    			@Override
    			public void onClick(View v) {
    				
    				saveUserLog(3);
    				
    				App app = getApp(position);
    				int appId = app.getId();
    	             
    				NotificationManager mNotificationManager = (NotificationManager)mContext.getSystemService(mContext.NOTIFICATION_SERVICE);
    	            mNotificationManager.cancel(appId);
    	            //  Start installing manual
    	            String path = DatabaseUtils.getInstallingAppLocalPath(mContext, appId);
    	            try {
    	                FileManager.install(path, mContext);
    	            } catch (Exception e) {
    	                MyLog.e(TAG, "Failed to install the file " + path);
    	            } 
    				
    			}
            	
            });
        }
        else if (section3Index != -1 && position <= section3Index + section3List.size())
        {
        	actionBtn.setHighLightButton(true);
        	actionBtn.setTextColor(0xff3f3f3f);
            actionBtn.setText(R.string.status_hasupdate_action);
            actionBtn.setOnClickListener(new OnClickListener()
            {

    			@Override
    			public void onClick(View v) {
    				if (!downloadBtnCanBePressed)
    	        		return;
    				
    				saveUserLog(4);
    	        	
    	        	downloadBtnCanBePressed = false;
    	        	
    	        	try
    	        	{
    	        		App app = getApp(position);
    	        		
    		        	new ProcessInstallTask((Activity)mContext, mInstallTaskResultListener)
    		            .execute(app.getId()+"", app.getPayId(), mContext.getString(R.string.tabtitle_downloaded)
    		            		,app.getDbId()+"", app.getName(), app.getDownloadCount()+"");
    	        	} catch (RejectedExecutionException e) {
    	            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
    	            }
    				
    			}
            	
            });
        }
        else if (section4Index != -1 && position <= section4Index + section4List.size())
        {
        	actionBtn.setHighLightButton(false);
        	actionBtn.setTextColor(Color.WHITE);
            actionBtn.setText(R.string.status_installed_action);
            actionBtn.setOnClickListener(new OnClickListener()
            {

    			@Override
    			public void onClick(View v) {
    				
    				saveUserLog(5);
    				
    				App app = getApp(position);
    				FileManipulation.uninstall(mContext, app.getPackgeName());
    				
    			}
            	
            });
        }
        
        
        App app = getApp(position);
        MyLog.d(TAG, "show app " + app.getId() + " status: " + app.getStatus());

        itemView.updateView(app);

        return itemView;
    }
    
    public void setData(ArrayList<App> applist)
    {
    	recommendAppList = applist;
    	
    	section1List = null;
    	section2List = null;
    	section3List = null;
    	section4List = null;
    	section1Index = -1;
    	section2Index = -1;
    	section3Index = -1;
    	section4Index = -1;
    	
    	total = recommendAppList.size();
    	
    	MyLog.d(TAG, "total " + total);
    	
    	notifyDataSetChanged();
        
        downloadBtnCanBePressed = true;
    }

    public void setData(ArrayList<App> appList1,
			    		ArrayList<App> appList2,
			    		ArrayList<App> appList3,
			    		ArrayList<App> appList4) 
    {
    	recommendAppList = null;
    	section1List = appList1;
    	section2List = appList2;
    	section3List = appList3;
    	section4List = appList4;
    	
    	total = 0;
    	if (appList1.size() == 0)
    		section1Index = -1;
    	else
    	{
    		section1Index = total;
    		total = total + appList1.size() + 1; // add 1 for list header view
    	}

    	if (appList2.size() == 0)
    		section2Index = -1;
    	else
    	{
    		section2Index = total;
    		total = total + appList2.size() + 1; // add 1 for list header view
    	}
    	
    	if (appList3.size() == 0)
    		section3Index = -1;
    	else
    	{
    		section3Index = total;
    		total = total + appList3.size() + 1; // add 1 for list header view
    	}
    	
    	if (appList4.size() == 0)
    		section4Index = -1;
    	else
    	{
    		section4Index = total;
    		total = total + appList4.size() + 1; // add 1 for list header view
    	}
    	
    	MyLog.d(TAG, "section1Index " + section1Index +
	    			"section2Index " + section2Index +
	    			"section3Index " + section3Index +
	    			"section4Index " + section4Index + 
	    			"total" + total);
    	
        notifyDataSetChanged();
        
        downloadBtnCanBePressed = true;
    }

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub
		return 0;
	}
	
	public void setAppActionResultListener( AppActionResultListener appActionResultListener)
	{
		mAppActionResultListener = appActionResultListener;
	}
	
	private AppActionResultListener mAppActionResultListener;
	public interface AppActionResultListener {
        public void onAppActionResult();
    }
	
	private boolean downloadBtnCanBePressed = true;


    private TaskResultListener mInstallTaskResultListener = new TaskResultListener() {

        @Override
        public void onTaskResult(boolean success, HashMap<String, Object> res) {
        	
        	
        	
        	if (!success) {
            	
        		downloadBtnCanBePressed = true;
        		
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

            String downloadPath = (String)res.get("location");
            String downloadId = (String)res.get("download_id");
            MyLog.d(TAG, "download id: " + downloadId + ", download path: " + downloadPath);

            // create download app in provider
            int dbid = Integer.parseInt((String)res.get("dbid"));
            int appid = Integer.parseInt((String)res.get("appid"));
            String name = (String)res.get("name");
            int downloadCount = Integer.parseInt((String)res.get("download_count"));
            	

            Intent intent = new Intent(mContext, AppService.class);
            intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_DOWNLOAD);
            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, dbid);
            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_ID, downloadId);
            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_URL, downloadPath);
            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPID, appid);
            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPNAME, name);
            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_COUNT, downloadCount);
            
            mContext.startService(intent);

            mAppActionResultListener.onAppActionResult();
        }
        
    };
    
 // save user log
	private void saveUserLog(int action)
    {
    	GeneralUtil.saveUserLogType3(mContext, 19, action);
    }
    
}
