package com.market.hjapp.service;

import android.app.AlarmManager;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.Service;
import android.content.Context;
import android.content.Intent;
import android.database.sqlite.SQLiteDatabase;
import android.os.Bundle;
import android.os.Handler;
import android.os.IBinder;
import android.os.Message;
import android.text.format.Time;

import com.market.hjapp.App;
import com.market.hjapp.Category;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.Recommend;
import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.network.NetworkManager;
import com.market.hjapp.ui.activity.BaseActivity;
import com.market.hjapp.ui.activity.RecommendActivity;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;

public class UpdateDataService extends Service implements Runnable {

	private static final String TAG = "UpdateDataService";

	public static boolean sThreadRunning = false;

	private static Object sLock = new Object();

	public static boolean sHasStarted = false;

	public static final int GET_SERVICE_PENDINGINTENT_ID = 20100811;

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
				"$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$");
		MyLog.d(TAG,
				"$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$");
		MyLog.d(TAG,
				"$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$");

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
				"$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$");
		MyLog.d(TAG,
				"$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$");
		MyLog.d(TAG,
				"$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$");

		// Date date = new Date(System.currentTimeMillis());
		// int h = date.getHours();
		//
		// boolean updateDailyData = false;
		// for (int i=0; i< UPDATE_TIME.length; i++)
		// {
		// if (UPDATE_TIME[i] == h)
		// updateDailyData = true;
		// }
		//
		// MyLog.d(TAG, "hour:" + h + "===" + updateDailyData);

		if (GeneralUtil.needUpdateDailyData(this)
				&& !BaseActivity.isMarketStarted) {
			GeneralUtil.saveHasUpdated(this,
					ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_1, false);
			GeneralUtil.saveHasUpdated(this,
					ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_2, false);
			GeneralUtil.saveHasUpdated(this,
					ConstantValues.PREF_KEY_LAST_UPLOAD_APPLIST, false);
			GeneralUtil.saveHasUpdated(this,
					ConstantValues.PREF_KEY_LAST_UPLOAD_USERLOG, false);
			GeneralUtil.saveUpdateDailyDataTime(this);
		}

		// upload local app list
		if (GeneralUtil.hasInitialized(this)
				&& !GeneralUtil.hasUpdated(this,
						ConstantValues.PREF_KEY_LAST_UPLOAD_APPLIST)
				&& !BaseActivity.isMarketStarted) {
			MyLog.d(TAG,
					"%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%");
			MyLog.d(TAG,
					"%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%");
			MyLog.d(TAG,
					"%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%%");

			boolean success = uploadApplist();
			if (success) {
				// set upload time
				GeneralUtil.saveHasUpdated(this,
						ConstantValues.PREF_KEY_LAST_UPLOAD_APPLIST, true);

				// GeneralUtil.checkAppUpdate(this);
			}
		}

		// upload user browse log
		if (GeneralUtil.hasInitialized(this)
				&& !GeneralUtil.hasUpdated(this,
						ConstantValues.PREF_KEY_LAST_UPLOAD_USERLOG)
				&& !BaseActivity.isMarketStarted) {
			MyLog.d(TAG,
					"#################################################################################");
			MyLog.d(TAG,
					"#################################################################################");
			MyLog.d(TAG,
					"#################################################################################");

			boolean success = uploadUserLog();
			if (success) {
				// set upload time
				GeneralUtil.saveHasUpdated(this,
						ConstantValues.PREF_KEY_LAST_UPLOAD_USERLOG, true);
				GeneralUtil.clearUserLog(this);
			}
		}

		// if (GeneralUtil.needCallBackToday(this))
		// {
		// Date date = new Date(System.currentTimeMillis());
		// int h = date.getHours();
		// if (GeneralUtil.getCallBackTime(this) == h)
		// {
		// // call back
		// GeneralUtil.callBack(this);
		// GeneralUtil.saveNeedCallBackToday(this, false);
		// }
		// }

		// 8-22 show recommend notification

		Time currentTime = new Time(); // or Time currentTime=new Time("GMT+8");
		currentTime.setToNow();
		int currentHour = currentTime.hour; // 0-24
		int currentDay = currentTime.yearDay; // 0-365
		if (GeneralUtil.getRecommendLastUpdateDay(this) != currentDay
				&& currentHour < 22 && !BaseActivity.isMarketStarted
				&& GeneralUtil.needShowRecommendAppNotificattion(this)) {
			// get recommend list
			String desc = getRecommendList();
			if (desc != null) {
				MyLog.d(TAG,
						"call backcall backcall backcall backcall backcall backcall backcall backcall back");
				MyLog.d(TAG,
						"call backcall backcall backcall backcall backcall backcall backcall backcall back");
				MyLog.d(TAG,
						"call backcall backcall backcall backcall backcall backcall backcall backcall back");

				Message message = mHandler
						.obtainMessage(MESSAGE_SHOW_NOTIFICATION);
				Bundle data = new Bundle();
				data.putString("desc", desc);
				message.setData(data);
				int max = 22;
				int min = (currentHour >= 8) ? currentHour : 8;
				int internal = min + (int) (Math.random() * (max - min))
						- currentHour;
				long showtime = internal * 60 * 60 * 1000 + 15 * 1000;
				// TEMP
				// showtime = 15*1000;
				mHandler.sendMessageDelayed(message, showtime);
			}

		}

		String updateTime;

		long waittime = 60 * 60 * 1000; // 1 hour;
		// for (int i : UPDATE_TIME) {
		// if (h < i) {
		// waittime = (i - h) * 60 * 60 * 1000;
		// break;
		// }
		// }
		// set times
		updateTime = waittime + "";

		Time time = new Time();
		long nowMillis = System.currentTimeMillis();
		time.set(nowMillis + Long.parseLong(updateTime));
		long updateTimes = time.toMillis(true);
		MyLog.d(TAG, "now is " + nowMillis);
		MyLog.d(TAG, "request next update at " + updateTimes);

		Intent updateIntent = new Intent();
		updateIntent.setClass(this, UpdateDataService.class);
		PendingIntent pending = PendingIntent.getService(this,
				GET_SERVICE_PENDINGINTENT_ID, updateIntent,
				PendingIntent.FLAG_CANCEL_CURRENT);

		AlarmManager alarm = (AlarmManager) getSystemService(Context.ALARM_SERVICE);
		alarm.set(AlarmManager.RTC_WAKEUP, updateTimes, pending);

		sHasStarted = true;
		sThreadRunning = false;

		stopSelf();
	}

	private static final int MESSAGE_SHOW_NOTIFICATION = 101;

	private Handler mHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			int what = msg.what;
			if (what == MESSAGE_SHOW_NOTIFICATION) {

				MyLog.d(TAG,
						"*********************************************************************************");
				MyLog.d(TAG,
						"call backcall backcall backcall backcall backcall backcall backcall backcall back");
				MyLog.d(TAG,
						"*********************************************************************************");

				Time currentTime = new Time();
				currentTime.setToNow();
				int currentDay = currentTime.yearDay; // 0-365
				if (GeneralUtil.getRecommendViewDay(UpdateDataService.this) == currentDay) {
					MyLog.d(TAG, "has been shown");
					return;
				}

				String notificationDesc = msg.getData().getString("desc");

				int icon = R.drawable.app_icon;
				CharSequence tickerText = getString(R.string.recommend_notification);
				long when = System.currentTimeMillis();

				Notification notification = new Notification(icon, tickerText,
						when);

				notification.flags = Notification.FLAG_AUTO_CANCEL;

				Intent notificationIntent = new Intent(UpdateDataService.this,
						RecommendActivity.class);

				notificationIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP
						| Intent.FLAG_ACTIVITY_NEW_TASK);

				CharSequence contentTitle = getString(R.string.recommend_notification);
				CharSequence contentText = notificationDesc;

				PendingIntent contentIntent = PendingIntent.getActivity(
						UpdateDataService.this, 0, notificationIntent, 0);
				;

//				notification.setLatestEventInfo(UpdateDataService.this,
//						contentTitle, contentText, contentIntent);
				NotificationManager mNotificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);
				mNotificationManager.notify(ConstantValues.RECOMMEND_NOTIFY_ID,
						notification);

			}

			super.handleMessage(msg);
		}
	};

	private boolean updateData1() {
		try {
			HashMap<String, Object> res;
			boolean success = false;

			// 1. load all cate list
			res = NetworkManager.getCategoryList(this);
			if (res != null && !res.isEmpty()) {
				success = (Boolean) res.get("reqsuccess");
				if (success) {
					String hotwords = (String) res.get("hotwords");
					GeneralUtil.saveHotwords(this, hotwords);

					final ArrayList<Category> cateList = (ArrayList<Category>) res
							.get("list");
					final ArrayList<Category> rankList = (ArrayList<Category>) res
							.get("rank");

					if (cateList == null || cateList.size() == 0) {
						MyLog.e(TAG, "Got error when get category list");
						return false;
					}

					MyLog.d(TAG, "cate list size: " + cateList.size());
					DatabaseUtils.saveCategoryList(this, cateList);

					// DatabaseUtils.saveCategoryListInRank(this, rankList);

				}
			} else
				return false;

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

	private boolean updateData2() {
		try {
			HashMap<String, Object> res;
			boolean success = false;

			// 2. Load app unstable info
			String applistString = DatabaseUtils.getAllAppIdList(this);
			MyLog.d(TAG, "All app list: " + applistString);

			String needDetailList = "";
			if (applistString != null) {
				res = NetworkManager.getAppStateList(this, applistString);
				if (res != null && !res.isEmpty()) {
					success = (Boolean) res.get("reqsuccess");
					if (success) {
						final ArrayList<App> applist = (ArrayList<App>) res
								.get("list");
						MyLog.d(TAG, "app unstable info changed list size: "
								+ applist.size());

						if (applist == null || applist.size() == 0) {
							// no unstable info app
						} else {
							needDetailList = DatabaseUtils.saveAppStateList(
									this, applist);

							if ((needDetailList != null && !needDetailList
									.equals(""))) {
								MyLog.d(TAG, "Detail info changed applist "
										+ needDetailList);
							}
						}
					}
				} else
					return false;
			}

			// 3. load recommend list
			// SQLiteDatabase db = new
			// DatabaseHelper(this).getWritableDatabase();
			// res = NetworkManager.getRecommmend(this,
			// DatabaseUtils.getNewestRecommendId(db));
			// if (res != null && !res.isEmpty())
			// {
			// success = (Boolean)res.get("reqsuccess");
			// if (success)
			// {
			// final ArrayList<Recommend> recommendList =
			// (ArrayList<Recommend>)res.get("list");
			// if (recommendList != null && recommendList.size() > 0)
			// DatabaseUtils.saveRecommendList(this, recommendList);
			//
			// final String displayList = (String)res.get("display");
			// GeneralUtil.saveRecommendDisplayList(this, displayList);
			// }
			// }
			// else
			// return false;

			// 4. load detail of uncached apps in first screen
			// String applistInRecommend =
			// DatabaseUtils.getAppIdListInRecommend(db);

			// String uncachedAppList = new AppTabSpec(this,
			// ConstantValues.SUGGESTED_CATE_IDLIST[0],
			// DatabaseSchema.TABLE_CATEGORY.COLUMN_APPLIST_NEW_FREE,
			// db, null).getUncachedAppList();

			// String uncachedAppList = new AppTabSpec(this, applistInRecommend,
			// db, null).getUncachedAppList();

			// db.close();

			// if (uncachedAppList != null && !uncachedAppList.equals(""))
			// {
			// needDetailList += uncachedAppList;
			// }
			// MyLog.d(TAG, "All need detail app list: " + needDetailList);

			if (needDetailList != null && !needDetailList.equals("")) {
				res = NetworkManager.getAppInfoList(this, needDetailList);
				if (res != null && !res.isEmpty()) {
					success = (Boolean) res.get("reqsuccess");
					if (success) {
						final ArrayList<App> applist = (ArrayList<App>) res
								.get("list");

						if (applist == null || applist.size() == 0) {
							// no unstable info app
						} else {
							SQLiteDatabase db = new DatabaseHelper(this)
									.getWritableDatabase();
							DatabaseUtils.saveAppList(db, applist);
							db.close();
						}
					}
				} else
					return false;
			}

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

	private boolean uploadUserLog() {
		try {
			HashMap<String, Object> res;
			boolean success;

			String userlog = GeneralUtil.getUserLog(this);

			res = NetworkManager.uploadUserLog(this, userlog);
			if (res != null && !res.isEmpty()) {
				success = (Boolean) res.get("reqsuccess");
				if (success) {
					return true;
				}
			}

			return false;

		} catch (ClientProtocolException e) {
			MyLog.e(TAG, "", e);
		} catch (IOException e) {
			MyLog.e(TAG, "", e);
		} catch (JSONException e) {
			MyLog.e(TAG, "", e);
		}

		return false;
	}

	private String getRecommendList() {
		try {
			HashMap<String, Object> res;
			boolean success;

			String time = GeneralUtil.getRecommendTime(this);

			res = NetworkManager.getRecommmendByTime(this, time);
			if (res != null && !res.isEmpty()) {
				success = (Boolean) res.get("reqsuccess");
				if (success) {
					final ArrayList<Recommend> recommendList = (ArrayList<Recommend>) res
							.get("list");
					String displayList = (String) res.get("display");
					time = (String) res.get("time");

					if (recommendList.size() == 0) {
						MyLog.d(TAG, "recommendList size=0");

						GeneralUtil.saveRecommendDisplayList(this, displayList);
						GeneralUtil.saveRecommendTime(this, time);

						// GeneralUtil.saveRecommendLastUpdateDay(this);
						// return "asjflajflasjf;lasjf";
						return null;
					}

					SQLiteDatabase mDb = new DatabaseHelper(this)
							.getWritableDatabase();
					if (recommendList != null && recommendList.size() > 0) {
						DatabaseUtils.saveRecommendList(this, recommendList);
						GeneralUtil.saveRecommendDisplayList(this, displayList);
						GeneralUtil.saveRecommendTime(this, time);

						GeneralUtil.saveRecommendLastUpdateDay(this);

						mDb.close();
						return recommendList.get(0).getRecDesc();
					}

					mDb.close();
					return null;
				}
			}

			return null;

		} catch (ClientProtocolException e) {
			MyLog.e(TAG, "", e);
		} catch (IOException e) {
			MyLog.e(TAG, "", e);
		} catch (JSONException e) {
			MyLog.e(TAG, "", e);
		}

		return null;
	}
}