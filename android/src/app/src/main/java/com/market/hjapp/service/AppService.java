package com.market.hjapp.service;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.Set;
import java.util.concurrent.RejectedExecutionException;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Activity;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.app.Service;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.net.Uri;
import android.os.IBinder;
import android.text.TextUtils;
import android.widget.RemoteViews;

import com.market.hjapp.App;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.DownloadItem;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.download.DownloadManager;
import com.market.hjapp.service.download.OnDownloadStatusChangedListener;
import com.market.hjapp.service.download.entity.DownloadErrorInfo;
import com.market.hjapp.ui.activity.AppDetailActivity;
import com.market.hjapp.ui.activity.MyDownloadsActivity;
import com.market.hjapp.ui.activity.UpdateActivity;
import com.market.hjapp.ui.tasks.ImageLoaderTask;

public class AppService extends Service {

	public static final String TAG = "AppService";

	public static final String DOWNLOAD_APP_DBID = "app_db_id";
	public static final String DOWNLOAD_ERROR_APPNAME = "error_appname";
	public static final String DOWNLOAD_PROGRESS_VALUE = "progress_value";
	public static final String DOWNLOAD_APP_PID = "app_pid";

	public static final String BROADCAST_PACKAGE_INSTALLED = "com.market.hjapp.package.installed";
	public static final String BROADCAST_PACKAGE_UNINSTALLED = "com.market.hjapp.package.uninstalled";
	public static final String BROADCAST_PACKAGE_REPLACED = "com.market.hjapp.package.replaced";

	public static final String BROADCAST_DOWNLOAD_UPDATE = "com.market.hjapp.download.update";
	public static final String BROADCAST_DOWNLOAD_COMPLETE = "com.market.hjapp.download.complete";
	public static final String BROADCAST_DOWNLOAD_ERROR = "com.market.hjapp.download.error";
	public static final String BROADCAST_DOWNLOAD_UPDATE_FOR_DETAIL = "com.market.hjapp.download.update.detail";

	public static final String CHECK_CMD = "check";
	public static final String UPDATE_SYSTEM_PACKAGE = "update_system_package";
	public static final String UPDATE_SELF = "update_self";

	public static final String POST_REVIEW = "post_review";
	public static final String MYREVIEW_PID = "myreview_pid";
	public static final String MYREVIEW_RATE = "myreview_rate";
	public static final String MYREVIEW_COMMENT = "myreview_comment";

	public static final String IS_CHECK_SELF_FIRST = "IS_CHECK_SELF_FIRST";

	private boolean isNotInterrupt = true;

	private BroadcastReceiver mUnmountReceiver = null;
	private NotificationManager mNotificationManager;

	private Thread updateSelfThread = null;

	private HashMap<Integer, DownloadManager> downloadPools = new HashMap<Integer, DownloadManager>();

	@Override
	public IBinder onBind(Intent arg0) {

		return null;
	}

	@Override
	public void onCreate() {
		super.onCreate();

		mNotificationManager = (NotificationManager) getSystemService(Context.NOTIFICATION_SERVICE);

	}

	private void automaticUpdate(final boolean isFirstCalled) {
		MyLog.d(TAG, "automatic update >>>");
		if (updateSelfThread != null && !updateSelfThread.isInterrupted()) {
			updateSelfThread.interrupt();
			updateSelfThread = null;
		}

		if (updateSelfThread == null) {
			updateSelfThread = new Thread(new Runnable() {
				@Override
				public void run() {
					try {
						HashMap<String, Object> res = GeneralUtil
								.hasNewVersion(AppService.this);
						if (res != null) {
							MyLog.d(TAG,
									"--------------- HAVE NEW VERSION! --------- ");
							boolean needUpdateMust = (Boolean) res
									.get("need_upgrade");

							int appid = (Integer) res.get("version");
							String filename = DatabaseUtils
									.getInstallingAppLocalPath(AppService.this,
											appid);

							File apkFile = new File(filename);

							if (needUpdateMust && !apkFile.exists()) {
								// auto download new version
								downloadApp(ConstantValues.CLIENT_DBID, appid,
										(String) res.get("url"), null,
										getString(R.string.app_name), 0);
							} else {
								// notification user there are new version
								int icon = R.drawable.notification_upgrade;
								CharSequence tickerText = getString(
										R.string.android_update_notification,
										getString(R.string.app_name));
								long when = System.currentTimeMillis();

								Notification notification = new Notification(
										icon, tickerText, when);
								if (GeneralUtil
										.needVoiceNotificattion(AppService.this)) {
									notification.defaults |= Notification.DEFAULT_SOUND;
									long[] vibrate = { 0, 100, 200, 300 };
									notification.vibrate = vibrate;
								}
								notification.flags = Notification.FLAG_AUTO_CANCEL;
								Context context = getApplicationContext();
								CharSequence contentTitle = getString(R.string.downloaded_notification);
								CharSequence contentText = tickerText;

								Intent notificationIntent = new Intent(
										AppService.this, UpdateActivity.class);

								notificationIntent.putExtra("version",
										(String) res.get("app_version"));
								notificationIntent.putExtra("changelog",
										(String) res.get("changelog"));
								notificationIntent.putExtra("appid", appid);
								notificationIntent.putExtra("url",
										(String) res.get("url"));

								PendingIntent contentIntent = PendingIntent
										.getActivity(AppService.this, 0,
												notificationIntent, 0);

//								notification.setLatestEventInfo(context,
//										contentTitle, contentText,
//										contentIntent);
								mNotificationManager.notify(
										ConstantValues.CLIENT_NOTIFY_ID,
										notification);
							}

						}
					} catch (ClientProtocolException e) {
						MyLog.e(TAG,
								"Client Protocol exception when update self", e);
					} catch (IOException e) {
						MyLog.e(TAG, "IO exception when update self", e);
					} catch (JSONException e) {
						MyLog.e(TAG, "JSON exception when update self", e);
					}
				}
			});
			updateSelfThread.start();
		}
	}

	public void onDestroy() {
		sHasStarted = false;

		if (mUnmountReceiver != null) {
			unregisterReceiver(mUnmountReceiver);
			mUnmountReceiver = null;
		}

		closeExternalStorageFiles();

		if (isNotInterrupt == true) {
			isNotInterrupt = false;
		}

		super.onDestroy();
	}

	private class DownloadStatusListener implements
			OnDownloadStatusChangedListener {
		private int appDbId;
		private int mAppId;
		private String mAppName;
		private int mNotifyId;

		private RemoteViews downloadingView;
		private Notification downloadingNotification;

		public DownloadStatusListener(int appid, int appDbId) {
			MyLog.d(TAG, "DownloadStatusListener, dbid: " + appDbId
					+ ", appid: " + appid);

			this.appDbId = appDbId;
			mAppId = appid;
			mNotifyId = mAppId;

			if (appDbId != ConstantValues.CLIENT_DBID) {
				App app = DatabaseUtils.getAppById(AppService.this, mAppId);
				mAppName = app.getName();
			} else {
				mAppName = getString(R.string.app_name);
			}

			// init downloading notification
			// init notification
			int icon = R.drawable.notification_downloading1;
			CharSequence tickerText = mAppName + " "
					+ getString(R.string.downloading_notification);
			long when = System.currentTimeMillis();
			downloadingNotification = new Notification(icon, tickerText, when);

			// init notification intent
			Intent downloadingIntent;
			if (appDbId != ConstantValues.CLIENT_DBID) {
				downloadingIntent = new Intent(AppService.this,
						AppDetailActivity.class);
				downloadingIntent.putExtra("appid", mAppId);
				downloadingIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP
						| Intent.FLAG_ACTIVITY_NEW_TASK);
			} else
				downloadingIntent = new Intent();

			// init pending intent
			PendingIntent downloadingPendingIntent = PendingIntent.getActivity(
					AppService.this, mNotifyId, downloadingIntent, 0);
			downloadingNotification.contentIntent = downloadingPendingIntent;

			// init notification view
			downloadingView = new RemoteViews(getPackageName(),
					R.layout.notification_view);
			downloadingView.setTextViewText(R.id.app_name, mAppName);
			downloadingNotification.contentView = downloadingView;

		}

		public void onPostDownloaded(String appName, String localPath, int pid) {
			// show notification
			int icon = R.drawable.notification_download_complete;
			CharSequence tickerText = appName + " "
					+ getString(R.string.installing_notification);
			long when = System.currentTimeMillis();

			Notification notification = new Notification(icon, tickerText, when);

			if (GeneralUtil.needVoiceNotificattion(AppService.this)) {
				notification.defaults |= Notification.DEFAULT_SOUND;
				long[] vibrate = { 0, 100, 200, 300 };
				notification.vibrate = vibrate;
			}
			notification.flags = Notification.FLAG_AUTO_CANCEL;

			Context context = getApplicationContext();
			CharSequence contentTitle = getString(R.string.downloaded_notification);
			CharSequence contentText = tickerText;

			Intent notificationIntent = new Intent();

			notificationIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
			notificationIntent.setAction(Intent.ACTION_VIEW);

			String type = "application/vnd.android.package-archive";

			File apkFile = new File(localPath);
			notificationIntent.setDataAndType(Uri.fromFile(apkFile), type);

			PendingIntent contentIntent = PendingIntent.getActivity(
					AppService.this, 0, notificationIntent, 0);

//			notification.setLatestEventInfo(context, contentTitle, contentText,
//					contentIntent);

			mNotificationManager.notify(mNotifyId, notification);

			// Remove this download item, and to do next
			synchronized (downloadPools) {
				DownloadManager currDm = getCurrDownload(appDbId);

				if (currDm != null) {
					downloadPools.remove(appDbId);
				}
			}

			synchronized (downloadWaitingList) {
				removeCurrentDownloadItem(appDbId);
				downloadNext();
			}
		}

		public void onPreDownload() {
		}

		public void onProgressUpdate(int value, boolean needBroadcast) {

			if (listenerLifeIsOver)
				return;

			if (appDbId != ConstantValues.CLIENT_DBID) {

				if (needBroadcast) {
					Intent progress = new Intent(BROADCAST_DOWNLOAD_UPDATE);
					progress.putExtra(DOWNLOAD_APP_DBID, appDbId);
					progress.putExtra(DOWNLOAD_PROGRESS_VALUE, value);
					progress.putExtra(DOWNLOAD_APP_PID, mAppId);
					MyLog.d(TAG, "Send BroadCast with updated progress action:"
							+ BROADCAST_DOWNLOAD_UPDATE + ",which value is :"
							+ value);
					sendBroadcast(progress);
				}

				Intent progress = new Intent(
						BROADCAST_DOWNLOAD_UPDATE_FOR_DETAIL);
				progress.putExtra(DOWNLOAD_APP_DBID, appDbId);
				progress.putExtra(DOWNLOAD_PROGRESS_VALUE, value);
				progress.putExtra(DOWNLOAD_APP_PID, mAppId);
				MyLog.d(TAG, "Send BroadCast with updated progress action:"
						+ BROADCAST_DOWNLOAD_UPDATE + ",which value is :"
						+ value);
				sendBroadcast(progress);
			}

			// show notification
			downloadingView
					.setProgressBar(R.id.progress_bar, 100, value, false);
			downloadingView.setTextViewText(R.id.progress_value, value + "%");

			download_anim = (download_anim + 1) % download_anim_max;
			if (download_anim == 0) {
				downloadingNotification.icon = R.drawable.notification_downloading1;
				downloadingView.setImageViewResource(R.id.downloading_icon,
						R.drawable.notification_downloading1);
			} else if (download_anim == 1) {
				downloadingNotification.icon = R.drawable.notification_downloading2;
				downloadingView.setImageViewResource(R.id.downloading_icon,
						R.drawable.notification_downloading2);
			} else {
				downloadingNotification.icon = R.drawable.notification_downloading3;
				downloadingView.setImageViewResource(R.id.downloading_icon,
						R.drawable.notification_downloading3);
			}

			mNotificationManager.notify(mNotifyId, downloadingNotification);

		};

		public int download_anim = 0;
		public int download_anim_max = 3;

		public void onDownloadError(DownloadErrorInfo downErrorInfo) {
			Intent errorIntent = new Intent(BROADCAST_DOWNLOAD_ERROR);
			errorIntent.putExtra(DOWNLOAD_ERROR_APPNAME,
					downErrorInfo.getAppName());
			errorIntent.putExtra(DOWNLOAD_APP_DBID, appDbId);
			errorIntent.putExtra(DOWNLOAD_APP_PID, mAppId);

			MyLog.d(TAG,
					"Send BroadCast with error action:"
							+ BROADCAST_DOWNLOAD_ERROR + ",which value is :"
							+ downErrorInfo.getErrorType() + "; "
							+ downErrorInfo.getAppName());
			sendBroadcast(errorIntent);

			int appDbId = downErrorInfo.getAppDbId();

			listenerLifeIsOver = true;
			mNotificationManager.cancel(mNotifyId);

			// Remove this download item, and to do next
			synchronized (downloadPools) {
				DownloadManager currDm = getCurrDownload(appDbId);

				if (currDm != null) {
					downloadPools.remove(appDbId);
				}
			}

			synchronized (downloadWaitingList) {
				removeCurrentDownloadItem(appDbId);
				downloadNext();
			}

		}

		private boolean listenerLifeIsOver = false;

		@Override
		public void onStopDownload() {

			listenerLifeIsOver = true;
			mNotificationManager.cancel(mNotifyId);

			// Remove this download item, and to do next
			synchronized (downloadPools) {
				DownloadManager currDm = getCurrDownload(appDbId);

				if (currDm != null) {
					downloadPools.remove(appDbId);
				}
			}

			synchronized (downloadWaitingList) {
				removeCurrentDownloadItem(appDbId);
				downloadNext();
			}
		}
	}

	public DownloadManager getCurrDownload(int appDbId) {
		if (appDbId == App.INVALID_ID) {
			return null;
		}

		synchronized (downloadPools) {
			DownloadManager currDownload = downloadPools.get(appDbId);
			return currDownload;
		}
	}

	public void pauseResumeDownload(int appDbId) {
		DownloadManager currDownload = getCurrDownload(appDbId);

		if (currDownload == null) {
			// FIXME:
			return;
		}

		if (currDownload.getStatus() == DownloadManager.DOWNLOADING) {
			currDownload.pauseDownloading();

			MyLog.d(TAG, "downloaded size ====================== "
					+ currDownload.getDownloadedSize());

			DatabaseUtils.updateDownloadPauseStatus(this, appDbId,
					currDownload.getDownloadedSize());
			MyLog.i(TAG, appDbId + " is paused");
		} else if (currDownload.getStatus() == DownloadManager.PAUSED) {
			currDownload.resumeDownloading();

			DatabaseUtils.updateDownloadingStatus(this, appDbId,
					currDownload.getDownloadedSize());
			MyLog.i(TAG, appDbId + " is resumed");

		}
	}

	public void deleteDownload(int appDbId) {
		DownloadManager currDownload = getCurrDownload(appDbId);

		synchronized (downloadWaitingList) {
			if (currDownload == null) {

				if (downloadWaitingList != null) {
					for (int i = 0; i < downloadWaitingList.size(); i++) {
						if (downloadWaitingList.get(i).getDBId() == appDbId) {
							MyLog.d(TAG, "remove from waiting list");
							downloadWaitingList.remove(i);
							notifyWaitingList();
							break;
						}
					}
				}

				return;
			}
		}

		currDownload.cancelDownloading();
		MyLog.i(TAG, appDbId + " is deleted");
	}

	public void cancelDownload(int appDbId) {
		DownloadManager currDownload = getCurrDownload(appDbId);

		if (currDownload == null) {
			return;
		}

		currDownload.cancelDownloading();

		DatabaseUtils.updateDownloadCancelStatus(this, appDbId);
		MyLog.i(TAG, appDbId + " is cancelled");

	}

	public int getDownloadProgress(int appDbId) {
		DownloadManager currDownload = getCurrDownload(appDbId);

		if (currDownload == null) {
			return 0;
		}
		MyLog.i(TAG,
				"service download progress: "
						+ currDownload.getDownloadProgress());
		;
		return currDownload.getDownloadProgress();
	}

	public int getDownloadStatus(int appDbId) {
		DownloadManager currDownload = getCurrDownload(appDbId);

		if (currDownload == null) {
			return DownloadManager.ERROR;
		}

		return currDownload.getStatus();
	}

	public int[] getCurrentDownloadList() {
		synchronized (downloadPools) {
			if (downloadPools.size() == 0) {
				return null;
			}

			Set<Integer> appDbIds = downloadPools.keySet();

			int[] downloadIds = new int[appDbIds.size()];

			int i = 0;
			for (Iterator<Integer> it = appDbIds.iterator(); it.hasNext();) {
				Integer currId = (Integer) it.next();

				int status = getDownloadStatus(currId);

				if (status == DownloadManager.DOWNLOADING
						|| status == DownloadManager.PAUSED
						|| status == DownloadManager.COMPLETE) {
					downloadIds[i] = currId;
					i++;
				}
			}

			return downloadIds;
		}
	}

	/**
	 * Called when we receive a ACTION_MEDIA_EJECT notification.
	 * 
	 * @param storagePath
	 *            path to mount point for the removed media
	 */
	public void closeExternalStorageFiles() {
		// clean up if the SD card is going to be unmounted.
		synchronized (downloadPools) {
			if (downloadPools.size() != 0) {
				int[] ids = getCurrentDownloadList();

				if (ids == null) {
					return;
				}

				int length = ids.length;

				for (int i = 0; i < length; i++) {

					DownloadManager mDownload = getCurrDownload(ids[i]);

					int currItemStatus = mDownload.getStatus();

					if (currItemStatus == DownloadManager.COMPLETE) {
						continue;
					}

					if (currItemStatus == DownloadManager.DOWNLOADING
							|| currItemStatus == DownloadManager.ERROR
							|| currItemStatus == DownloadManager.PAUSED) {
						mDownload.cancelDownloading();
					}

					MyLog.d(TAG, "downloadPools appDbId:" + ids[i]);
					downloadPools.remove(ids[i]);
				}
			}
		}
	}

	/**
	 * Registers an intent to listen for ACTION_MEDIA_EJECT notifications. The
	 * intent will call closeExternalStorageFiles() if the external media is
	 * going to be ejected, so applications can clean up any files they have
	 * open.
	 */
	public void registerExternalStorageListener() {
		if (mUnmountReceiver == null) {
			mUnmountReceiver = new BroadcastReceiver() {
				@Override
				public void onReceive(Context context, Intent intent) {
					String action = intent.getAction();
					if (action.equals(Intent.ACTION_MEDIA_EJECT)) {
						closeExternalStorageFiles();
					}
				}
			};
			IntentFilter iFilter = new IntentFilter();
			iFilter.addAction(Intent.ACTION_MEDIA_EJECT);
			iFilter.addDataScheme("file");
			registerReceiver(mUnmountReceiver, iFilter);
		}
	}

	public static final String EXTRA_KEY_COMMAND = "key_command";

	// keys for download command
	public static final String EXTRA_KEY_DOWNLOAD_DBID = "key_download_dbid";
	public static final String EXTRA_KEY_DOWNLOAD_URL = "key_download_url";
	public static final String EXTRA_KEY_DOWNLOAD_APPID = "key_download_appid";
	public static final String EXTRA_KEY_DOWNLOAD_ID = "key_download_id";
	public static final String EXTRA_KEY_DOWNLOAD_APPNAME = "key_download_appname";
	public static final String EXTRA_KEY_DOWNLOAD_COUNT = "key_download_count";

	// keys for download list command
	public static final String EXTRA_KEY_DOWNLOAD_LIST_DBID = "key_download_list_dbid";
	public static final String EXTRA_KEY_DOWNLOAD_LIST_URL = "key_download_list_url";
	public static final String EXTRA_KEY_DOWNLOAD_LIST_APPID = "key_download_list_appid";
	public static final String EXTRA_KEY_DOWNLOAD_LIST_ID = "key_download_list_id";
	public static final String EXTRA_KEY_DOWNLOAD_LIST_APPNAME = "key_download_list_appname";
	public static final String EXTRA_KEY_DOWNLOAD_LIST_COUNT = "key_download_list_count";

	// defines commands
	public static final String COMMAND_DOWNLOAD = "cmd_download";
	public static final String COMMAND_CHECK_SELFUPDATE = "cmd_check_selfupdate";
	public static final String COMMAND_PAUSE = "cmd_pause";
	public static final String COMMAND_RESUME = "cmd_resume";
	public static final String COMMAND_CANCEL = "cmd_cancel";
	public static final String COMMAND_DOWNLOAD_SELFUPDATE = "cmd_down_selfupdate";
	public static final String COMMAND_DOWNLOAD_LIST = "cmd_download_list";

	public static boolean sHasStarted = false;

	public static ArrayList<DownloadItem> downloadWaitingList = new ArrayList<DownloadItem>();
	public static ArrayList<DownloadItem> downloadingList = new ArrayList<DownloadItem>();

	@Override
	public void onStart(Intent intent, int startId) {
		super.onStart(intent, startId);
		MyLog.d(TAG, "onStart >>> ");
		sHasStarted = true;

		// check whether has command
		String cmd = null;
		try {
			cmd = intent.getStringExtra(EXTRA_KEY_COMMAND);
		} catch (Exception e) {
			MyLog.e(TAG, e.toString());
		}

		if (cmd == null) {
			return;
		}

		MyLog.d(TAG, "onStart >>> cmd: " + cmd);
		if (cmd.equals(COMMAND_DOWNLOAD)) {
			// start download
			int dbid = intent.getIntExtra(EXTRA_KEY_DOWNLOAD_DBID, -1);
			String url = intent.getStringExtra(EXTRA_KEY_DOWNLOAD_URL);
			int appid = intent.getIntExtra(EXTRA_KEY_DOWNLOAD_APPID, -1);
			String downloadid = intent.getStringExtra(EXTRA_KEY_DOWNLOAD_ID);
			String appName = intent.getStringExtra(EXTRA_KEY_DOWNLOAD_APPNAME);
			int downloadCount = intent.getIntExtra(EXTRA_KEY_DOWNLOAD_COUNT, 0);

			startDownload(new DownloadItem(dbid, appid, url, downloadid,
					appName, downloadCount));
			// downloadApp(dbid, appid, url, downloadid, appName,
			// downloadCount);
			return;
		} else if (cmd.equals(COMMAND_DOWNLOAD_LIST)) {
			// start download
			String[] dbidList = (intent
					.getStringExtra(EXTRA_KEY_DOWNLOAD_LIST_DBID)).split(",");
			String[] urlList = intent.getStringExtra(
					EXTRA_KEY_DOWNLOAD_LIST_URL).split(",");
			String[] appidList = intent.getStringExtra(
					EXTRA_KEY_DOWNLOAD_LIST_APPID).split(",");
			String[] downloadidList = intent.getStringExtra(
					EXTRA_KEY_DOWNLOAD_LIST_ID).split(",");
			String[] appNameList = intent.getStringExtra(
					EXTRA_KEY_DOWNLOAD_LIST_APPNAME).split(",");
			String[] downloadCountList = intent.getStringExtra(
					EXTRA_KEY_DOWNLOAD_LIST_COUNT).split(",");

			for (int i = 0; i < appidList.length; i++) {
				startDownload(new DownloadItem(Integer.parseInt(dbidList[i]),
						Integer.parseInt(appidList[i]), urlList[i],
						downloadidList[i], appNameList[i],
						Integer.parseInt(downloadCountList[i])));
			}
			return;
		} else if (cmd.equals(COMMAND_PAUSE)) {
			int dbid = intent.getIntExtra(EXTRA_KEY_DOWNLOAD_DBID, -1);
			pauseResumeDownload(dbid);
		} else if (cmd.equals(COMMAND_RESUME)) {
			int dbid = intent.getIntExtra(EXTRA_KEY_DOWNLOAD_DBID, -1);
			pauseResumeDownload(dbid);
		} else if (cmd.equals(COMMAND_CANCEL)) {
			int dbid = intent.getIntExtra(EXTRA_KEY_DOWNLOAD_DBID, -1);
			deleteDownload(dbid);
		} else if (cmd.equals(COMMAND_CHECK_SELFUPDATE)) {
			automaticUpdate(intent.getBooleanExtra(IS_CHECK_SELF_FIRST, false));
			return;
		} else if (cmd.equals(COMMAND_DOWNLOAD_SELFUPDATE)) {

			return;
		}
		// FIXME: Should be removed?
		String check = intent.getStringExtra(CHECK_CMD);

		if (check != null) {
			if (check.equals(UPDATE_SELF)) {
				automaticUpdate(intent.getBooleanExtra(IS_CHECK_SELF_FIRST,
						false));
			}
		}
	}

	private void startDownload(DownloadItem item) {
		MyLog.d(TAG, "startDownload:" + item.getAppId());
		synchronized (downloadWaitingList) {
			boolean hasInQue = false;
			for (DownloadItem d : downloadWaitingList) {
				if (d.getUrl().equals(item.getUrl())) {
					hasInQue = true;
					break;
				}
			}

			for (DownloadItem d : downloadingList) {
				if (d.getUrl().equals(item.getUrl())) {
					hasInQue = true;
					break;
				}
			}

			if (hasInQue) {
				MyLog.d(TAG, item.getUrl() + " has in Que");
				return;
			} else {
				MyLog.d(TAG, "add to waiting list");
				downloadWaitingList.add(item);

				if (mDownloadingCount >= MAX_DOWNALOAD)
					notifyWaitingList();

				if (item.getDBId() != ConstantValues.CLIENT_DBID)
					DatabaseUtils.updateDownloadingStatus(this, item.getDBId(),
							0);

				downloadNext();
			}
		}
	}

	private static int mDownloadingCount = 0;
	private static final int MAX_DOWNALOAD = 3;

	private void removeCurrentDownloadItem(int appDbId) {
		MyLog.d(TAG, "removeCurrentDownloadItem:" + appDbId);
		if (downloadingList != null) {
			for (int i = 0; i < downloadingList.size(); i++) {
				if (downloadingList.get(i).getDBId() == appDbId) {
					MyLog.d(TAG, "remove from downloading list");
					mDownloadingCount--;
					downloadingList.remove(i);
					break;
				}
			}
		}

	}

	private void downloadNext() {
		MyLog.d(TAG, "mDownloadingCount:" + mDownloadingCount);
		if (downloadWaitingList != null && downloadWaitingList.size() > 0
				&& mDownloadingCount < MAX_DOWNALOAD) {
			mDownloadingCount++;
			MyLog.d(TAG, "do next!");

			DownloadItem item = downloadWaitingList.get(0);
			downloadingList.add(item);
			downloadWaitingList.remove(0);
			notifyWaitingList();
			downloadApp(item.getDBId(), item.getAppId(), item.getUrl(),
					item.getDownloadId(), item.getAppName(),
					item.getDownloadCount());

		}
	}

	private static final int WAITING_LIST_NOTIFY_ID = 98798;
	private static Notification notification;

	private void notifyWaitingList() {
		if (downloadWaitingList.size() == 0) {
			mNotificationManager.cancel(WAITING_LIST_NOTIFY_ID);
			return;
		}

		// show notification
		int icon = R.drawable.app_icon;
		CharSequence tickerText = getString(
				R.string.download_waiting_list_notification,
				downloadWaitingList.size());
		long when = System.currentTimeMillis();

		// Notification notification = new Notification(icon, tickerText, when);
		if (notification == null) {
			notification = new Notification(icon, tickerText, when);
			notification.flags = Notification.FLAG_AUTO_CANCEL;
		}

		Context context = getApplicationContext();
		CharSequence contentTitle = getString(R.string.downloaded_notification);
		CharSequence contentText = tickerText;

		Intent notificationIntent = new Intent();

		notificationIntent = new Intent(AppService.this,
				MyDownloadsActivity.class);
		notificationIntent.putExtra("goto_hasupdate_cate", false);
		notificationIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP
				| Intent.FLAG_ACTIVITY_NEW_TASK);

		PendingIntent contentIntent = PendingIntent.getActivity(
				AppService.this, 0, notificationIntent, 0);
//		notification.setLatestEventInfo(context, contentTitle, contentText,
//				contentIntent);

		notification.number = downloadWaitingList.size();

		mNotificationManager.notify(WAITING_LIST_NOTIFY_ID, notification);
	}

	private void downloadApp(int dbid, int appid, String url,
			String downloadid, String appName, int downloadCount) {
		MyLog.d(TAG, "download app >> dbid: " + dbid + ", appid: " + appid
				+ ", url: " + url + ", downloadid: " + downloadid
				+ ", appName: " + appName);
		if (appid == -1 || TextUtils.isEmpty(url))
			return;

		try {
			URL convertedURL = new URL(Uri.encode(url, ":/"));
			MyLog.d(TAG, "download url: " + convertedURL.toString());

			DownloadManager newDownload = new DownloadManager(dbid,
					convertedURL, this, downloadid, appName, appid,
					downloadCount);
			newDownload
					.setOnDownloadStatusChangedListener(new DownloadStatusListener(
							appid, dbid));
			newDownload.startDownload();
			MyLog.d(TAG, "new downloaded link is " + newDownload.getAppURL());

			// if (dbid != ConstantValues.CLIENT_DBID)
			// DatabaseUtils.updateDownloadingStatus(this, dbid, 0);

			synchronized (downloadPools) {
				downloadPools.put(dbid, newDownload);
			}

		} catch (MalformedURLException e) {
			if (dbid != ConstantValues.CLIENT_DBID) {
				DatabaseUtils.updateDownloadErrorStatus(dbid);
				DatabaseUtils.resetAppToInit(this, dbid);
			}
			MyLog.e(TAG, "Download can not be started!", e);
		}
	}

}
