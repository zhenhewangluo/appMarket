package com.market.hjapp.service;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Service;
import android.content.Intent;
import android.database.sqlite.SQLiteDatabase;
import android.os.IBinder;

import com.market.hjapp.App;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.network.NetworkManager;

public class UploadLocalAppService extends Service implements Runnable {

	private static final String TAG = "UploadLocalAppService";

	public static boolean sThreadRunning = false;

	private static Object sLock = new Object();

	@Override
	public void onCreate() {
		super.onCreate();
	}

	@Override
	public IBinder onBind(Intent intent) {
		// TODO Auto-generated method stub
		return null;
	}

	@Override
	public void onDestroy() {
		MyLog.d(TAG,
				"DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!DIE!");
		super.onDestroy();
	}

	@Override
	public void onStart(Intent intent, int startId) {
		MyLog.d(TAG,
				"*********************************************************************************");
		MyLog.d(TAG,
				"*********************************************************************************");
		MyLog.d(TAG,
				"*********************************************************************************");

		super.onStart(intent, startId);

		synchronized (sLock) {
			if (!sThreadRunning) {
				sThreadRunning = true;
				new Thread(this).start();
			}
		}
	}

	@Override
	public void run() {
		MyLog.d(TAG,
				"*********************************************************************************");
		MyLog.d(TAG,
				"*********************************************************************************");
		MyLog.d(TAG,
				"*********************************************************************************");
		// upload local app list
		boolean success = uploadApplist();
		if (success) {
			// set upload time
			GeneralUtil.saveHasUpdated(this,
					ConstantValues.PREF_KEY_LAST_UPLOAD_APPLIST, true);

			// GeneralUtil.checkAppUpdate(this);
		}

		sThreadRunning = false;

		stopSelf();
	}

	private boolean uploadApplist() {
		try {
			HashMap<String, String> pkgList = GeneralUtil.getUploadApps(this);
			String applist = pkgList.get("applist");

			if (applist != null && !applist.equals("")) {
				HashMap<String, Object> res;
				boolean success;

				res = NetworkManager.getLocalApplist(this, applist);
				if (res != null && !res.isEmpty()) {
					success = (Boolean) res.get("reqsuccess");
					if (success) {
						final ArrayList<App> list = (ArrayList<App>) res
								.get("list");

						if (list == null || list.size() == 0) {
							// no unstable info app
						} else {
							SQLiteDatabase db = new DatabaseHelper(this)
									.getWritableDatabase();

							// Clear local data base
							DatabaseUtils.clearInstalledToInit(db);

							String pkgName;
							for (App app : list) {
								MyLog.d(TAG,
										"app.getPackgeName(): "
												+ app.getPackgeName());

								int version = 0;
								pkgName = app.getPackgeName();

								if (pkgName != null && !pkgName.equals("")
										&& !pkgName.equals("null")) {
									version = Integer.parseInt(pkgList
											.get(pkgName));

									if (version < app.getVersion()) {
										app.setStatus(App.HAS_UPDATE);
									} else {
										app.setStatus(App.INSTALLED);
									}

									// insert or update App
									if (DatabaseUtils.isAppInLocalDB(db, app))
										DatabaseUtils.insertOrUpdateOneApp(db,
												app, false);
									else
										DatabaseUtils.insertOrUpdateOneApp(db,
												app, true);
								}
							}

							// // save uploaded local apk list
							// String uploaded =
							// GeneralUtil.getUploadedAPKList(this);
							// String uploadAPKList = pkgList.get("pkglist");
							//
							// if (uploaded == null)
							// uploaded = uploadAPKList;
							// else
							// uploaded += "," + uploadAPKList;
							//
							// GeneralUtil.saveUploadedAPKList(this, uploaded);

							db.close();
						}

						return true;
					} else {
						String error = (String) res.get("errno");
						if (error.equals("E120")) {
							return true;
						} else {
							return false;
						}
					}
				}
			} else
				return true;

		} catch (ClientProtocolException e) {
			MyLog.e(TAG, "", e);
		} catch (IOException e) {
			MyLog.e(TAG, "", e);
		} catch (JSONException e) {
			MyLog.e(TAG, "", e);
		}
		return false;
	}
}