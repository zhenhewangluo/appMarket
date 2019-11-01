

package com.market.hjapp;

import java.io.File;

import android.app.Application;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.sqlite.SQLiteDatabase;
import android.os.Handler;
import android.os.Message;
import android.os.Handler.Callback;
import android.widget.Toast;

import com.market.hjapp.R;
import com.market.hjapp.service.AppService;

public class AppStoreApplication extends Application{
	
	private DownloadErrorReceiver receiver;
	private Handler handler;
	private Message message;
	
	@Override
	public void onCreate() {
		super.onCreate();
		MyLog.d(TAG, "onCreate");
		
		GeneralUtil.createIconDownloadFolder();
		
		IntentFilter filter = new IntentFilter();
		filter.addAction(AppService.BROADCAST_DOWNLOAD_ERROR);
		receiver = new DownloadErrorReceiver();
		registerReceiver(receiver, filter);
		
//		packageStateReceiver = new PackageProcessingReceiver();
//		packageStateFilter = new IntentFilter();
//		packageStateFilter.addAction(Intent.ACTION_PACKAGE_ADDED);
//		packageStateFilter.addAction(Intent.ACTION_PACKAGE_REMOVED);
//      packageStateFilter.addAction(AppService.BROADCAST_PACKAGE_INSTALLED);
//      packageStateFilter.addAction(AppService.BROADCAST_PACKAGE_UNINSTALLED);
//      packageStateFilter.addAction(AppService.BROADCAST_PACKAGE_REPLACED);
//		packageStateFilter.addDataScheme("package");
//		registerReceiver(packageStateReceiver, packageStateFilter);
//		MyLog.d(TAG, "receiver registered");
//		updatePackageStatusHandler = new Handler(this);
		
		handler = new Handler(new Callback(){

			public boolean handleMessage(Message arg0) {
				Error((String)arg0.obj);
				return false;
			}
		});
	}

	@Override
	public void onTerminate() {
		if(receiver != null){
			unregisterReceiver(receiver);
		}
		super.onTerminate();
	}
	
	private void Error(String errAppName){
		if(errAppName == null){
			errAppName = "";
		}
		Toast.makeText(this, 
				errAppName + getString(R.string.error_happen_on_downloading), Toast.LENGTH_LONG).show();
	}
	
	private class DownloadErrorReceiver extends BroadcastReceiver {
		@Override
		public void onReceive(Context context, Intent intent) {
			if(intent.getAction().equals(AppService.BROADCAST_DOWNLOAD_ERROR)){
				String errAppName = intent.getStringExtra(AppService.DOWNLOAD_ERROR_APPNAME);
				if(errAppName != null) {
					message = handler.obtainMessage();
					message.obj = errAppName;
					handler.sendMessage(message);
				}
			}
		}
	}
	
//	private PackageProcessingReceiver packageStateReceiver;
//	private IntentFilter packageStateFilter;
//	private Handler updatePackageStatusHandler;
//	private static final int UPDATE_PACKAGE_STATUS_ADDED = 10000;
//	private static final int UPDATE_PACKAGE_STATUS_REMOVED = 10001;
//	private static final int UPDATE_PACKAGE_STATUS_TIMEOUT = 500;
	
//	private class PackageProcessingReceiver extends BroadcastReceiver {
//		
//		@Override
//		public void onReceive(Context arg0, Intent arg1) {
//			String action = arg1.getAction();
//			String packageName = arg1.getDataString();
//			packageName = packageName.substring(packageName.indexOf(':') + 1);
//			
//			SQLiteDatabase db = new DatabaseHelper(arg0).getWritableDatabase();
//			App app = DatabaseUtils.getAppByPackageName(db, packageName);
//			db.close();
//			if (app == null) {
//			    return;
//			}
//			
//			MyLog.d(TAG, "on receive >>> action: " + action + ", packageName: " + packageName);
//			Message message = updatePackageStatusHandler.obtainMessage();
//			message.obj = app;			
////			if(action.equals(Intent.ACTION_PACKAGE_ADDED)) 
//			if(action.equals(AppService.BROADCAST_PACKAGE_INSTALLED)) {
//				if(updatePackageStatusHandler.hasMessages(UPDATE_PACKAGE_STATUS_REMOVED)) {
//					updatePackageStatusHandler.removeMessages(UPDATE_PACKAGE_STATUS_REMOVED);
//				}
//				message.what = UPDATE_PACKAGE_STATUS_ADDED;
//			}
////			else if(action.equals(Intent.ACTION_PACKAGE_REMOVED))
//			else if(action.equals(AppService.BROADCAST_PACKAGE_UNINSTALLED)) {
//				message.what = UPDATE_PACKAGE_STATUS_REMOVED;
//			}
//			updatePackageStatusHandler.sendMessageDelayed(message, UPDATE_PACKAGE_STATUS_TIMEOUT);
//		}
//	}

//	public static final String APP_PID = "APP_PID";
//	public static final String APP_DBID = "APP_DBID";
//	public static final String APP_DOWNLOAD_ID = "APP_DOWNLOAD_ID";
    private static final String TAG = "AppStoreApplication";
	
//	public boolean handleMessage(Message msg) {
//		SQLiteDatabase db = new DatabaseHelper(this).getWritableDatabase();
//		boolean messageHandled = false;
		
//		App app = (App) msg.obj;
//		if(msg.what == UPDATE_PACKAGE_STATUS_ADDED) {
//		    MyLog.d(TAG, "hadle package added message");
//			
//			DatabaseUtils.updateInstalledCompleteStatus(db, app.getId());
//
//			String localPath = app.getLocalPath();
//			if (localPath != null && !localPath.equals(""))
//			{
//				try
//				{
//					File apkFile = new File(localPath);
//					if(apkFile.exists()) {
//						apkFile.delete();
//					}
//				}catch(Exception e)
//				{
//					MyLog.e(TAG, e.toString());
//				}
//			}
//			
//		} else if(msg.what == UPDATE_PACKAGE_STATUS_REMOVED) {
//		    DatabaseUtils.updateDeleteCompleteStatus(db, app.getId());
//		}
//		
//		db.close();
//		return messageHandled;
//	}
	
}
