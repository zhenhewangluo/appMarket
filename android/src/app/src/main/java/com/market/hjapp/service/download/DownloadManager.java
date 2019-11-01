package com.market.hjapp.service.download;

import java.io.BufferedInputStream;
import java.io.BufferedOutputStream;
import java.io.File;
import java.io.FileInputStream;
import java.io.FileNotFoundException;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.io.RandomAccessFile;
import java.net.HttpURLConnection;
import java.net.InetSocketAddress;
import java.net.Proxy;
import java.net.URL;
import java.net.UnknownHostException;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.content.Context;
import android.content.Intent;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.text.TextUtils;
import android.util.Log;

import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.network.HTTParser;
import com.market.hjapp.network.NetworkManager;
import com.market.hjapp.service.AppService;
import com.market.hjapp.service.download.entity.DownloadErrorInfo;
import com.market.hjapp.service.download.entity.DownloadInfo;

public class DownloadManager implements Runnable {
	private static final String TAG = "DownloadManager";

	// Max size of download buffer.
	public static final int MAX_BUFFER_SIZE = 1024;
	// public static final String DOWNLOAD_APP_PATH = "/sdcard/download/";
	private static final int countTime = 50;

	// These are the status codes.
	public static final int DOWNLOADING = 1;
	public static final int PAUSED = 2;
	public static final int COMPLETE = 3;
	public static final int CANCELLED = 4;
	public static final int ERROR = 5;

	private int appDbId;
	private URL url;
	private int downloaded;
	private int appSize;
	private int status;
	private Context ctx;
	private String localPath;
	private String fileName;
	private String appName;
	private String downloadId;
	private int pid;
	private int downloadCount;

	private OnDownloadStatusChangedListener changedListener;

	public DownloadManager(int appDbId, URL url, Context ctx,
			String downloadId, String appName, int pid, int downloadCount) {
		MyLog.d(TAG, "DownloadManager >>> app db id: " + appDbId);
		this.appDbId = appDbId;
		this.url = url;
		downloaded = 0;
		appSize = -1;
		status = DOWNLOADING;
		this.ctx = ctx;

		String fileName = pid + ".apk";
		localPath = GeneralUtil.LOCAL_DOWNLOAD_APP_PATH + fileName;

		this.fileName = fileName;
		this.appName = appName;
		this.downloadId = downloadId;
		this.pid = pid;
		this.downloadCount = downloadCount;

	}

	public void startDownload() {
		Thread thread = new Thread(this);

		if (changedListener != null) {
			changedListener.onPreDownload();
		}
		thread.start();
	}

	public void setOnDownloadStatusChangedListener(
			OnDownloadStatusChangedListener changedListener) {
		this.changedListener = changedListener;
	}

	private long lastUpdateTime = 0;
	private long updateTime;
	private final int updateInterval = 500;

	public void run() {
		MyLog.d(TAG, "run >>> app db id: " + appDbId);
		RandomAccessFile downloadFile = null;
		InputStream inputStream = null;
		MyLog.d("DownloadManager", "URL is + " + url.toString());
		try {

			// HttpURLConnection conn = (HttpURLConnection)url.openConnection();

			HttpURLConnection conn;
			// String proxyHost = android.net.Proxy.getDefaultHost();
			// if (proxyHost != null) {
			// MyLog.d(TAG, "proxyHost:"+proxyHost);
			// java.net.Proxy p = new java.net.Proxy(java.net.Proxy.Type.HTTP,
			// new InetSocketAddress(android.net.Proxy.getDefaultHost(),
			// android.net.Proxy.getDefaultPort()));
			//
			// conn = (HttpURLConnection)url.openConnection(p);
			//
			// } else {
			// conn =(HttpURLConnection)url.openConnection();
			//
			// }

			NetworkInfo info = ((ConnectivityManager) ctx
					.getSystemService(Context.CONNECTIVITY_SERVICE))
					.getActiveNetworkInfo();

			if (info != null && info.getType() != ConnectivityManager.TYPE_WIFI) {
				String proxyHost = android.net.Proxy.getDefaultHost();

				// 20120627,herokf,fix cmwap can't connect.
				if (HTTParser.isCMWAP(ctx)) {
					MyLog.e(TAG, "isCMWAP");

					int contentBeginIdx = url.toString().indexOf('/', 7);
					StringBuffer urlStringBuffer = new StringBuffer(
							"http://10.0.0.172:80");

					urlStringBuffer.append(url.toString().substring(contentBeginIdx));
					conn = (HttpURLConnection) new URL(
							urlStringBuffer.toString()).openConnection();
					conn.setRequestProperty("X-Online-Host",
							url.toString().substring(7, contentBeginIdx));

				}else if(HTTParser.isCTWAP(ctx)){
					Proxy proxy = new Proxy(Proxy.Type.HTTP, new InetSocketAddress("10.0.0.200", 80));
				       conn = (HttpURLConnection) url.openConnection(proxy);
					
				}else if (proxyHost != null) {
					MyLog.d(TAG, "proxyHost:" + proxyHost);
					Proxy p = new Proxy(
							Proxy.Type.HTTP, new InetSocketAddress(
									android.net.Proxy.getDefaultHost(),
									android.net.Proxy.getDefaultPort()));

					conn = (HttpURLConnection) url.openConnection(p);

				} else {
					conn = (HttpURLConnection) url.openConnection();

				}
			} else
				conn = (HttpURLConnection) url.openConnection();

			MyLog.d(TAG, "Range bytes = " + downloaded + "-" + appSize);
			if (downloaded != 0)
				conn.setRequestProperty("Range", "bytes=" + downloaded + "-");
			conn.connect();

			MyLog.i(TAG,
					"-----  get response code -----" + conn.getResponseCode());

			if (conn.getResponseCode() != 200 && conn.getResponseCode() != 206) {
				showError();
			}

			int responseContentLength = conn.getContentLength();
			MyLog.i(TAG, "-----  get response content length -----"
					+ responseContentLength);

			if (responseContentLength < 1) {
				showError();
			}

			if (appSize == -1) {
				appSize = responseContentLength;
			}

			File directory = new File(GeneralUtil.LOCAL_DOWNLOAD_APP_PATH);

			if (!directory.exists()) {
				directory.mkdir();
			}

			downloadFile = new RandomAccessFile(localPath, "rw");
			MyLog.i(TAG, "current download: " + downloaded
					+ "; current file name:" + localPath);

			downloadFile.seek(downloaded);

			inputStream = conn.getInputStream();

			int count = 0;
			int lastValue = -1;
			boolean needBroadcast = true;
			byte buffer[] = new byte[MAX_BUFFER_SIZE];
			while (status == DOWNLOADING) {

				int readStream = inputStream.read(buffer);

				if (readStream == -1) {
					break;
				}

				downloadFile.write(buffer, 0, readStream);
				downloaded += readStream;
				count++;

				// MyLog.i(TAG, "current download: " + downloaded + "; count: "
				// + count);

				// if(changedListener != null && (count == countTime)){
				//
				// count = 0;
				// // TEMP for save/5k
				// DatabaseUtils.updateDownloadingStatus(ctx, appDbId,
				// downloaded);
				//
				// changedListener.onProgressUpdate(getDownloadProgress());
				// MyLog.i(TAG, "appDbId: " + appDbId + "; current download: " +
				// downloaded);
				// }

				updateTime = System.currentTimeMillis();
				if (updateTime - lastUpdateTime < updateInterval) {
					continue;
				}

				lastUpdateTime = updateTime;

				int value = getDownloadProgress();

				if (value - lastValue < 1)
					continue;

				lastValue = value;

				if (count >= countTime) {
					count = 0;
					DatabaseUtils.updateDownloadingStatus(ctx, appDbId,
							downloaded);
				}

				if (changedListener != null) {
					changedListener.onProgressUpdate(value, needBroadcast);
					needBroadcast = false;
				}

			}

			if (status == DOWNLOADING) {
				status = COMPLETE;
			}

		} catch (UnknownHostException e) {

			if (appDbId != ConstantValues.CLIENT_DBID) {
				DatabaseUtils.updateDownloadErrorStatus(appDbId);
				DatabaseUtils.resetAppToInit(ctx, appDbId);
			}

			if (changedListener != null) {
				DownloadInfo downInfo = new DownloadErrorInfo();
				DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
				downErrorInfo.setErrorType(DownloadErrorInfo.UNKNOWN_HOST);
				downErrorInfo.setAppName(appName);
				downErrorInfo.setAppDbId(appDbId);

				changedListener.onDownloadError(downErrorInfo);

				changedListener = null;
			}

			MyLog.e(TAG, "UnknowHost exception when downloading an app", e);
			return;
		} catch (FileNotFoundException e) {
			if (appDbId != ConstantValues.CLIENT_DBID) {
				DatabaseUtils.updateDownloadErrorStatus(appDbId);
				DatabaseUtils.resetAppToInit(ctx, appDbId);
			}

			if (changedListener != null) {
				DownloadInfo downInfo = new DownloadErrorInfo();
				DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
				downErrorInfo.setErrorType(DownloadErrorInfo.FILE_NOT_FOUND);
				downErrorInfo.setAppName(appName);
				downErrorInfo.setAppDbId(appDbId);

				changedListener.onDownloadError(downErrorInfo);

				changedListener = null;
			}

			MyLog.e(TAG,
					"File Not Found exception when writing input stream into file",
					e);

			return;
		} catch (IOException e) {
			Log.e(TAG, "下载出错了：I/O exception when downloading file");
			showError();
			MyLog.e(TAG, "I/O exception when downloading file", e);
		} finally {

			if (downloadFile != null) {
				try {
					downloadFile.close();
				} catch (IOException e) {
					showError();
					MyLog.e(TAG,
							"IO Exception when closing downloading file stream",
							e);
				}
			}

			if (inputStream != null) {
				try {
					inputStream.close();
				} catch (IOException e) {
					showError();
					MyLog.e(TAG, "IO Exception when close input stream", e);
				}
			}

			if (changedListener != null && status == COMPLETE) {
				// changedListener.onPostDownloaded(appDbId, mAppProvider);
				if (prepareInstalling()) {
					changedListener.onPostDownloaded(appName, localPath, pid);
					postDownloadSuccessResult(pid);
				}
			}

			// if (!Thread.currentThread().isInterrupted()) {
			// Thread.currentThread().interrupt();
			// }
		}
	}

	public void resumeDownloading() {
		status = DOWNLOADING;
		startDownload();
	}

	public void pauseDownloading() {
		status = PAUSED;
	}

	public void cancelDownloading() {
		status = CANCELLED;

		if (!TextUtils.isEmpty(localPath)) {
			File currFile = new File(localPath);
			currFile.delete();
		}

		if (appDbId != ConstantValues.CLIENT_DBID) {
			DatabaseUtils.resetAppToInit(ctx, appDbId);
		}

		Thread.currentThread().interrupt();

		changedListener.onStopDownload();
	}

	public int getDownloadProgress() {
		MyLog.d(TAG, "downloaded: " + downloaded + ", appSize: " + appSize);
		return Math.round((((float) downloaded / appSize) * 100));
	}

	public int getAppId() {
		return appDbId;
	}

	public int getAppSize() {
		return appSize;
	}

	public int getStatus() {
		return status;
	}

	public String getAppURL() {
		return url.toString();
	}

	public int getDownloadedSize() {
		return downloaded;
	}

	public String getLocalPath() {
		return localPath;
	}

	public String getAppName() {
		return appName;
	}

	public String getDownloadId() {
		return downloadId;
	}

	public String showError() {
		status = ERROR;

		if (appDbId != ConstantValues.CLIENT_DBID) {
			DatabaseUtils.updateDownloadErrorStatus(appDbId);
			DatabaseUtils.resetAppToInit(ctx, appDbId);
		}

		if (changedListener != null) {
			DownloadInfo downInfo = new DownloadErrorInfo();
			DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
			downErrorInfo.setErrorType(DownloadErrorInfo.IO_ERROR);
			downErrorInfo.setAppName(appName);
			downErrorInfo.setAppDbId(appDbId);

			changedListener.onDownloadError(downErrorInfo);

			changedListener = null;
		}

		return "I/O errors, please try again later.";
	}

	public DownloadManager getCurrDownload() {
		return this;
	}

	public String getFileName() {
		return fileName;
	}

	public OnDownloadStatusChangedListener getDownloadChangeListener() {
		return changedListener;
	}

	private boolean prepareInstalling() {

		File inputFile = new File(localPath);
		if (!localPath.toLowerCase().endsWith(".apk")) {
			MyLog.d("AppService.java", "AppService.java:" + "run()\n"
					+ "***************\n" + " " + downloadId + " " + appName
					+ " " + localPath + " " + appSize + "\n***************");
			/*
			 * 
			 */
			if (inputFile != null) {
				inputFile.delete();
			}

			if (appDbId != ConstantValues.CLIENT_DBID) {
				DatabaseUtils.updateDownloadErrorStatus(appDbId);
				DatabaseUtils.resetAppToInit(ctx, appDbId);
			}

			DownloadInfo downInfo = new DownloadErrorInfo();
			DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
			downErrorInfo.setErrorType(DownloadErrorInfo.NOT_APK_PACKAGE);
			downErrorInfo.setAppName(appName);
			downErrorInfo.setAppDbId(appDbId);

			changedListener.onDownloadError(downErrorInfo);

			return false;
		}

		localPath = localPath.toLowerCase().replace(".apk", "_temp.apk");

		File outputFile = new File(localPath);
		BufferedInputStream inputStream = null;
		BufferedOutputStream outputStream = null;

		try {
			inputStream = new BufferedInputStream(
					new FileInputStream(inputFile),
					DownloadManager.MAX_BUFFER_SIZE);
			outputStream = new BufferedOutputStream(new FileOutputStream(
					outputFile), DownloadManager.MAX_BUFFER_SIZE);

			// Decrypt decrypt = new Decrypt(GeneralUtil.getIMEI(ctx));
			byte[] buffer = new byte[DownloadManager.MAX_BUFFER_SIZE];

			int mark = inputStream.read(buffer);

			while (mark != -1) {
				// Disable the decrypt since the server hasn't ready.
				// decrypt.decryptData(buffer, 0, mark);

				outputStream.write(buffer, 0, mark);

				mark = inputStream.read(buffer);
			}

			inputStream.close();
			outputStream.close();
		} catch (FileNotFoundException e) {
			DownloadInfo downInfo = new DownloadErrorInfo();
			DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
			downErrorInfo.setErrorType(DownloadErrorInfo.FILE_NOT_FOUND);
			downErrorInfo.setAppName(appName);
			downErrorInfo.setAppDbId(appDbId);

			changedListener.onDownloadError(downErrorInfo);

			MyLog.e(TAG, "File not found when copying apk file", e);
		} catch (IOException e) {

			DownloadInfo downInfo = new DownloadErrorInfo();
			DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
			downErrorInfo.setErrorType(DownloadErrorInfo.IO_ERROR);
			downErrorInfo.setAppName(appName);
			downErrorInfo.setAppDbId(appDbId);

			changedListener.onDownloadError(downErrorInfo);

			MyLog.e(TAG, "IO exception when copying apk file", e);
		}

		/*
		 * 删除原APK文件
		 */
		if (inputFile != null) {
			inputFile.delete();
		}

		// /////////////////////////////////////////
		String packageName = GeneralUtil.getPackageName(ctx, localPath);

		/*
		 * 异常发生
		 */
		if (packageName == null) {

			DownloadInfo downInfo = new DownloadErrorInfo();
			DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
			downErrorInfo.setErrorType(DownloadErrorInfo.NOT_APK_PACKAGE);
			downErrorInfo.setAppName(appName);
			downErrorInfo.setAppDbId(appDbId);

			changedListener.onDownloadError(downErrorInfo);

			/*
			 * 删除temp文件
			 */
			outputFile.delete();

			if (appDbId != ConstantValues.CLIENT_DBID) {
				DatabaseUtils.updateDownloadErrorStatus(appDbId);
				DatabaseUtils.resetAppToInit(ctx, appDbId);
			}

			return false;
		}

		if (appDbId != ConstantValues.CLIENT_DBID) {
			DatabaseUtils.updateDownloadCompleteStatus(ctx, appDbId, localPath,
					packageName, appSize, downloadCount + 1);
		}

		Intent progress = new Intent(AppService.BROADCAST_DOWNLOAD_COMPLETE);
		progress.putExtra(AppService.DOWNLOAD_APP_DBID, appDbId);
		progress.putExtra(AppService.DOWNLOAD_APP_PID, pid);
		MyLog.d(TAG, "Send BroadCast with downloaded complete action:"
				+ AppService.BROADCAST_DOWNLOAD_COMPLETE);

		ctx.sendBroadcast(progress);

		// if (aPid != null) {
		// // TODO Notifycation
		// Intent installIntent = new Intent();
		// // installIntent.setClass(AppService.this,
		// StartInstallationActivity.class);
		// installIntent.putExtra(ConstantValues.INSTALL_PID, aPid);
		// installIntent.putExtra(ConstantValues.INSTALL_LOCAL_PATH, localPath);
		// installIntent.putExtra(ConstantValues.INSTALL_DB_PID, appDbId);
		// installIntent.putExtra(ConstantValues.INSTALL_DOWNLOAD_ID,
		// downloadId);
		// installIntent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
		//
		// if (isServiceDestroying) {
		// if (appDbId != ConstantValues.CLIENT_DBID) {
		// mAppProvider.updateDownloadErrorStatus(appDbId);
		// mAppProvider.deleteApp(appDbId);
		// }
		// new File(localPath).delete();
		// MyLog.d(TAG,
		// "~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~");
		// Thread.currentThread().interrupt();
		// } else {
		// AppService.this.startActivity(installIntent);
		// }
		//
		// }
		// else {
		// // TODO 删除
		// android.util.MyLog.d(
		// "com.moto.mobile.appstore.service.download."
		// + "DownloadManager.java",
		// "DownloadManager.java:run()\n"
		// + "***************\n" +
		// "发生异常，aPid为null"
		// + "\n***************");
		// }

		return true;
	}

	private void postDownloadSuccessResult(int aPid) {
		if (appDbId == ConstantValues.CLIENT_DBID) {
			// Don't post result for self update
			return;
		}

		try {
			NetworkManager.postDownloadSuccess(ctx, aPid + "", downloadId);
		} catch (ClientProtocolException e) {
			DownloadInfo downInfo = new DownloadErrorInfo();
			DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
			downErrorInfo.setErrorType(DownloadErrorInfo.CLIENT_PROTOCOL);
			downErrorInfo.setAppName(appName);
			downErrorInfo.setAppDbId(appDbId);

			changedListener.onDownloadError(downErrorInfo);

			MyLog.e(TAG,
					"client protocol exception when post downloading success to server",
					e);
		} catch (IOException e) {
			DownloadInfo downInfo = new DownloadErrorInfo();
			DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
			downErrorInfo.setErrorType(DownloadErrorInfo.IO_ERROR);
			downErrorInfo.setAppName(appName);
			downErrorInfo.setAppDbId(appDbId);

			changedListener.onDownloadError(downErrorInfo);

			MyLog.e(TAG,
					"IO exception when post downloading success to server", e);
		} catch (JSONException e) {
			DownloadInfo downInfo = new DownloadErrorInfo();
			DownloadErrorInfo downErrorInfo = (DownloadErrorInfo) downInfo;
			downErrorInfo.setErrorType(DownloadErrorInfo.JSON_ERROR);
			downErrorInfo.setAppName(appName);
			downErrorInfo.setAppDbId(appDbId);

			changedListener.onDownloadError(downErrorInfo);

			MyLog.e(TAG,
					"JSON exception when post downloading success to server", e);
		}

	}

}
