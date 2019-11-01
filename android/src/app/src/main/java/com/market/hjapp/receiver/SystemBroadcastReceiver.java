package com.market.hjapp.receiver;

import java.util.HashMap;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.database.sqlite.SQLiteDatabase;

import com.market.hjapp.App;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.service.UpdateDataService;

public class SystemBroadcastReceiver extends BroadcastReceiver {

	private static final String TAG = "SystemBroadcastReceiver";

	
	@Override
	public void onReceive(Context ctx, Intent intent) {
		MyLog.d(TAG, "onReceive >>> action:" + intent.getAction());
		String action = intent.getAction();

		if (action.equals(Intent.ACTION_BOOT_COMPLETED)
				|| action.equals(Intent.ACTION_TIME_CHANGED)) {

			Intent i = new Intent();
			i.setClass(ctx, UpdateDataService.class);
			ctx.startService(i);
		}
		// install new app Broadcast
		if (action.equals(Intent.ACTION_PACKAGE_ADDED)) {
            String packageName = intent.getDataString();
            String pkgName = packageName.substring(packageName.indexOf(':') + 1);
//			String pkgName = intent.getDataString().substring(8);
            MyLog.d(TAG, "install app > onReceive >> action: " + action + ", packageName: " + pkgName);
            
            SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();
            App targetApp = DatabaseUtils.getAppByPackageName(db, pkgName);
            if (targetApp != null) {
            	GeneralUtil.updateInstalledCompleteStatus(db, targetApp);
            	Intent broadcastIntent = new Intent(AppService.BROADCAST_PACKAGE_INSTALLED);
            	broadcastIntent.putExtra("status", App.INSTALLED);
            	broadcastIntent.putExtra("appid", targetApp.getId());
                MyLog.d(TAG, "status="+targetApp.getStatus()+",appid="+targetApp.getId());
            	ctx.sendBroadcast(broadcastIntent);
            }
            
			String type = "0";
			HashMap<String, String> pkgList = GeneralUtil.getAppInfoByPkgName(ctx, pkgName);
	        String appinfo = type + "^" + pkgList.get("appinfo");
	        MyLog.d(TAG, "install app>>>appinfo=" + appinfo);
//			GeneralUtil.uploadPackageInfo(ctx, appinfo, type);
            db.close();
		}
		// update app Broadcast
		if (Intent.ACTION_PACKAGE_REPLACED.equals(intent.getAction())) {
            String packageName = intent.getDataString();
            String pkgName = packageName.substring(packageName.indexOf(':') + 1);
//			String pkgName = intent.getDataString().substring(8);
            MyLog.d(TAG, "update app >> onReceive >> action: " + action + ", packageName: " + pkgName);
            
//            SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();
//            App targetApp = DatabaseUtils.getAppByPackageName(db, packageName);
//            if (targetApp != null) {
//            	GeneralUtil.updateInstalledCompleteStatus(db, targetApp);
//            	Intent broadcastIntent = new Intent(AppService.BROADCAST_PACKAGE_REPLACED);
//            	broadcastIntent.putExtra("status", App.INSTALLED);
//            	broadcastIntent.putExtra("appid", targetApp.getId());
//              MyLog.d(TAG, "status="+targetApp.getStatus()+",appid="+targetApp.getId());
//            	ctx.sendBroadcast(broadcastIntent);
//
//            }
            
			String type = "1";
			HashMap<String, String> pkgList = GeneralUtil.getAppInfoByPkgName(ctx, pkgName);
	        String appinfo = type + "^" + pkgList.get("appinfo");
	        MyLog.d(TAG, "update app >>>appinfo=" + appinfo);
//			GeneralUtil.uploadPackageInfo(ctx, appinfo, type);
//          db.close();
		}
		// uninstall app Broadcast
		if (action.equals(Intent.ACTION_PACKAGE_REMOVED)) {
            String packageName = intent.getDataString();
            String pkgName = packageName.substring(packageName.indexOf(':') + 1);
//			String pkgName = intent.getDataString().substring(8);
            MyLog.d(TAG, "uninstall app >> onReceive >> action: " + action + ", packageName: " + pkgName);
            
            SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();
            App targetApp = DatabaseUtils.getAppByPackageName(db, pkgName);
            if (targetApp != null) {
            	GeneralUtil.updateDeleteCompleteStatus(db, targetApp);
            	Intent broadcastIntent = new Intent(AppService.BROADCAST_PACKAGE_UNINSTALLED);
            	broadcastIntent.putExtra("status", App.INIT);
            	broadcastIntent.putExtra("appid", targetApp.getId());
                MyLog.d(TAG, "status="+targetApp.getStatus()+",appid="+targetApp.getId());
            	ctx.sendBroadcast(broadcastIntent);
            }
			String type = "2";
	        String appinfo = type + "^" + pkgName;
	        MyLog.d(TAG, "uninstall app >>>appinfo=" + appinfo);
//			GeneralUtil.uploadPackageInfo(ctx, appinfo, type);
            db.close();
		}
	}
 
}
