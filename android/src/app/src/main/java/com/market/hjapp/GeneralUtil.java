package com.market.hjapp;

import android.app.Activity;
import android.app.Notification;
import android.app.NotificationManager;
import android.app.PendingIntent;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.SharedPreferences;
import android.content.SharedPreferences.Editor;
import android.content.pm.PackageInfo;
import android.content.pm.PackageManager;
import android.content.pm.PackageManager.NameNotFoundException;
import android.database.Cursor;
import android.database.sqlite.SQLiteDatabase;
import android.graphics.Rect;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.os.Build;
import android.os.Environment;
import android.preference.PreferenceManager;
import android.telephony.TelephonyManager;
import android.text.TextUtils;
import android.text.format.Time;
import android.view.Display;
import android.view.WindowManager;
import android.widget.ImageView;

import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseSchema;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.network.NetworkManager;
import com.market.hjapp.service.AppService;
import com.market.hjapp.tyxl.object.CustomDialog;
import com.market.hjapp.ui.activity.BaseActivity;
import com.market.hjapp.ui.activity.MyDownloadsActivity;
import com.market.hjapp.ui.activity.SplashActivity;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import java.io.File;
import java.io.IOException;
import java.net.MalformedURLException;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.Set;
import java.util.regex.Matcher;
import java.util.regex.Pattern;
import java.util.zip.ZipFile;

public class GeneralUtil {

	private static final String TAG = "GeneralUtil";

	public static Set<String> savedPackageNameSet = null;

	public static String clientFileName = null;
	public static final String CLIENT_FILE_NAME = "client_file_name";

	public static String getIMEI(Context ctx) {// 获取设备编号
		TelephonyManager mTelephonyMgr = (TelephonyManager) ctx
				.getSystemService(Context.TELEPHONY_SERVICE);
		String imei = mTelephonyMgr.getDeviceId();

		return imei;
	}

	public static String getIMSI(Context ctx) {// 获取用户编号
		TelephonyManager mTelephonyMgr = (TelephonyManager) ctx
				.getSystemService(Context.TELEPHONY_SERVICE);
		String imsi = mTelephonyMgr.getSubscriberId();

		return imsi;
	}

	public static String getScreenSize(Context ctx) {// 获取屏幕尺寸
		Display screenSize = ((WindowManager) ctx
				.getSystemService(Context.WINDOW_SERVICE)).getDefaultDisplay();
		int width = screenSize.getWidth();
		int height = screenSize.getHeight();

		return width + "x" + height;
	}

	public static Rect getScreenRect(Context ctx) {// 获取屏幕覆盖尺寸
		Display screenSize = ((WindowManager) ctx
				.getSystemService(Context.WINDOW_SERVICE)).getDefaultDisplay();
		int width = screenSize.getWidth();
		int height = screenSize.getHeight();
		return new Rect(0, 0, width, height);
	}

	public static String getDeviceName() {
		return Build.DEVICE;
	}

	public static String getDeviceModel() {
		return Build.MODEL;
	}

	public static String getDeviceBrand() {
		return Build.BRAND;
	}

	public static String getPN() {
		return getDeviceModel();
	}

	public static void setPackageNameSet(Set<String> packageNameSet) {
		savedPackageNameSet = packageNameSet;
	}

	public static Set<String> getPackageNameSet() {
		return savedPackageNameSet;
	}

	public static HashMap<String, Object> hasNewVersion(Context context)
			throws ClientProtocolException, IOException, JSONException {// 是否存在新版本
		HashMap<String, Object> dataMap = NetworkManager
				.getSelfVerUpgrade(context);

		if (dataMap == null) {
			return null;
		} else {
			if (dataMap.size() == 0) {
				return null;
			} else {
				if (!(Boolean) dataMap.get("reqsuccess")) {
					final String errorMes = (String) dataMap.get("errmsg");
					MyLog.i(TAG, ">>> check version error: " + errorMes);

					return null;
				}
			}
		}

		// App app = (App) dataMap.get("client_app");
		int verRemote = (Integer) dataMap.get("version");
		int verLocal = ConstantValues.CLIENT_VERSION_NUMBER;
		MyLog.d(TAG, "client ver: " + verLocal + ", remote ver: " + verRemote);
		if (verLocal < verRemote) {
			// clientFileName = app.getFileName();
			return dataMap;
		}

		return null;
	}

	/**
	 * 获得夺宝的下载地址
	 * 
	 * @param context
	 * @return
	 * @throws ClientProtocolException
	 * @throws IOException
	 * @throws JSONException
	 */
	public static String getDuoBaoUrl(Context context)
			throws ClientProtocolException, IOException, JSONException {// 是否存在新版本
		HashMap<String, Object> dataMap = NetworkManager.getDuoBaoUrl(context);

		if (dataMap == null) {
			return null;
		} else {
			if (dataMap.size() == 0) {
				return null;
			} else {
				if ((Boolean) dataMap.get("reqsuccess")) {
					final String url = (String) dataMap.get("url");
					MyLog.i(TAG, ">>> check duobao url: " + url);
					return url;
				}
			}
		}
		return null;
	}

	/**
	 * 
	 * @param ctx
	 * @param localPath
	 */
	public static String getPackageName(Context ctx, String localPath) {// 获取已下载的APK的包名
		MyLog.d(TAG, "getPackageName: " + localPath);
		File file = new File(localPath);
		if (!file.exists()) {
			MyLog.e(TAG, "file NOT exists: " + localPath);
			return null;
		}

		try {
			new ZipFile(new File(localPath), ZipFile.OPEN_READ);
		} catch (IOException e) {
			MyLog.e(TAG, "Got IOException when create zip file for: "
					+ localPath, e);
			return null;
		}

		PackageManager pm = ctx.getPackageManager();
		PackageInfo info = pm.getPackageArchiveInfo(localPath,
				PackageManager.GET_ACTIVITIES);

		if (info == null) {
			MyLog.e(TAG, "Can't get info for: " + localPath);
			return null;
		} else {
			return info.packageName;
		}
	}

	/**
	 * @return
	 */
	public static boolean isSdcardMounted() {// 是否有SD卡

		String info = Environment.getExternalStorageState();

		if (info != null) {
			if (info.equals("mounted")) {
				return true;
			}
		}

		return false;
	}

	/**
	 * 
	 * @return
	 * @throws ClientProtocolException
	 * @throws MalformedURLException
	 * @throws IOException
	 */

	public static void createIconDownloadFolder() {// 如果不存在，创建图表存放的文件夹
		File iconFolder = new File(ConstantValues.ICON_FILE_PATH);

		if (!iconFolder.exists()) {
			iconFolder.mkdir();
		}
	}

	public static void showQuitConfirmDialog(final Activity activity) {// 欢聚宝退出提示
		// save user log
		GeneralUtil.saveUserLogType3(activity, 31, 0);
		CustomDialog.Builder quitBuilder = new CustomDialog.Builder(activity,
				R.layout.alertdialog);
		quitBuilder
				.setTitle(R.string.prompt_title)
				.setMessage("\n亲﹋ ﹋  要离开欢聚宝吗？\n")
				.setNegativeButton("取消", new DialogInterface.OnClickListener() {
					public void onClick(DialogInterface dialog, int which) {
						dialog.dismiss();
						return;
					}
				})
				.setPositiveButton(R.string.ok,
						new DialogInterface.OnClickListener() {
							public void onClick(DialogInterface dialog,
									int which) {
								// 下载新版本
								dialog.dismiss();
								BaseActivity.isMarketStarted = false;
								BaseActivity.isMarketClosed = true;
								clearAppUpdateNotification(activity);
								activity.finish();
								int nPid = android.os.Process.myPid();  
								android.os.Process.killProcess(nPid); 
								System.exit(0);
							}
						}).create().show();
	}

	public static void setImageResource(ImageView iv, int resId) {// 给XML中ImageView设置资源
		try {
			iv.setImageResource(resId);
		} catch (OutOfMemoryError e) {
			MyLog.e(TAG, "Got OOM when set image resource.", e);
		}
	}

	/*****************************************************************/
	/** V2 **/
	/*****************************************************************/
	private static final String PREF_NAME = "market_pref"; // 存档名
	private static final String PREF_KEY_INITIALIZED = "pref_key_initialized";// 初始化
	private static final String PREF_KEY_UID = "pref_key_uid";
	private static final String PREF_KEY_MID = "pref_key_mid";
	private static final String PREF_KEY_SID = "pref_key_sid";
	private static final String PREF_KEY_HAS_LOGGED_IN = "pref_key_hasloggedin";// 是否已经登录
	private static final String PREF_KEY_USER_NAME = "pref_key_username";// 用户名
	private static final String PREF_KEY_USER_PHONE = "pref_key_userphone";// 手机号
	private static final String PREF_KEY_USER_EMAIL = "pref_key_useremail";// 注册邮箱
	private static final String PREF_KEY_USER_BALANCE = "pref_key_userbalance";// 余额

	// private static final String PREF_KEY_APPID = "pref_key_appid";
	// private static final String PREF_KEY_PARENT = "pref_key_parent";
	// private static final String PREF_KEY_DATA = "pref_key_data";

	private static final String PREF_KEY_DEFAULT_CHARGE_CHANNEL_NAME = "pref_key_charge_channel_name";
	private static final String PREF_KEY_DEFAULT_CHARGE_CHANNEL_ID = "pref_key_charge_channel_id";

	private static SharedPreferences sPref;

	public static String getMid(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getString(PREF_KEY_MID, null);
	}

	public static String getUid(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getString(PREF_KEY_UID, null);
	}

	public static String getSid(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getString(PREF_KEY_SID, null);
	}

	// public static String getAppid(Context ctx) {
	// if (sPref == null) {
	// sPref = ctx.getSharedPreferences(PREF_NAME, 0);
	// }
	//
	// return sPref.getString(PREF_KEY_APPID, null);
	// }
	//
	// public static void saveAppid(Context ctx, String appid) {
	// if (sPref == null) {
	// sPref = ctx.getSharedPreferences(PREF_NAME, 0);
	// }
	//
	// SharedPreferences.Editor editor = sPref.edit();
	//
	// editor.putString(PREF_KEY_APPID, appid);
	//
	// editor.commit();
	// }

	public static void saveUid(Context ctx, String uid) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();

		editor.putString(PREF_KEY_UID, uid);

		editor.commit();
	}

	public static void saveSid(Context ctx, String sid) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();

		editor.putString(PREF_KEY_SID, sid);

		editor.commit();
	}

	public static boolean getHasLoggedIn(Context ctx) {// 是否已登录
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getBoolean(PREF_KEY_HAS_LOGGED_IN, false);
	}

	public static UserInfo getUserInfo(Context ctx) {// 获取用户信息
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		UserInfo user = new UserInfo();

		user.setName(sPref.getString(PREF_KEY_USER_NAME, null));
		user.setPhone(sPref.getString(PREF_KEY_USER_PHONE, null));
		user.setEmail(sPref.getString(PREF_KEY_USER_EMAIL, null));
		user.setBalance(sPref.getString(PREF_KEY_USER_BALANCE, null));

		return user;
	}

	public static void saveUserInfo(UserInfo user, Context ctx) {// 保存用户信息
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();

		editor.putString(PREF_KEY_USER_NAME, user.getName());
		editor.putString(PREF_KEY_USER_PHONE, user.getPhone());
		editor.putString(PREF_KEY_USER_EMAIL, user.getEmail());
		editor.putString(PREF_KEY_USER_BALANCE, user.getBalance());

		editor.commit();
	}

	public static void saveLoggedOut(Context ctx) {// 保存登录状态

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();

		editor.putBoolean(PREF_KEY_HAS_LOGGED_IN, false);

		editor.commit();
	}

	public static String getBalance(Context ctx) {// 获取余额

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getString(PREF_KEY_USER_BALANCE, null);
	}

	public static void saveBalance(Context ctx, String balance) {// 保存余额

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();

		editor.putString(PREF_KEY_USER_BALANCE, balance);

		editor.commit();
	}

	public static void saveLoggedIn(UserInfo user, Context ctx) {// 存储用户信息
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();

		editor.putBoolean(PREF_KEY_HAS_LOGGED_IN, true);
		editor.putString(PREF_KEY_USER_NAME, user.getName());
		editor.putString(PREF_KEY_USER_PHONE, user.getPhone());
		editor.putString(PREF_KEY_USER_EMAIL, user.getEmail());
		editor.putString(PREF_KEY_USER_BALANCE, user.getBalance());

		editor.commit();
	}

	public static void saveLoginInfo(Context ctx, String mid, String sid) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		MyLog.d(TAG, "save login info >> mid: " + mid + ", sid: " + sid);
		Editor editor = sPref.edit();

		editor.putBoolean(PREF_KEY_INITIALIZED, true);
		if (mid != null)
			editor.putString(PREF_KEY_MID, mid);
		editor.putString(PREF_KEY_SID, sid);

		editor.commit();
	}

	public static boolean hasInitialized(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		if (!sPref.getBoolean(PREF_KEY_INITIALIZED, false)) {
			return false;
		}

		if (TextUtils.isEmpty(GeneralUtil.getMid(ctx))
				|| TextUtils.isEmpty(GeneralUtil.getSid(ctx))) {
			return false;
		}

		return true;
	}

	public static boolean needInitDB(Context ctx) {

		if (hasCategoryData(ctx)) {
			return false;
		}

		return true;
	}

	private static boolean hasCategoryData(Context ctx) {
		StringBuilder buf = new StringBuilder();
		buf.append("SELECT " + DatabaseSchema.TABLE_CATEGORY.COLUMN_ID);
		buf.append(" FROM " + DatabaseSchema.TABLE_CATEGORY.NAME);

		SQLiteDatabase db = new DatabaseHelper(ctx).getReadableDatabase();

		Cursor c = db.rawQuery(buf.toString(), null);

		boolean hasData = (c != null && c.getCount() > 0);

		c.close();
		db.close();

		return hasData;
	}

	private static final long ONE_KB = 1024;
	private static final long ONE_MB = 1024 * ONE_KB;
	private static final long ONE_GB = 1024 * ONE_MB;

	public static String getReadableSize(long size) {// 获取应用大小
		String displaySize;

		if (size / ONE_GB > 0) {
			displaySize = String.valueOf(size / ONE_GB) + " GB";
		} else if (size / ONE_MB > 0) {
			displaySize = String.valueOf(size / ONE_MB) + " MB";
		} else if (size / ONE_KB > 0) {
			displaySize = String.valueOf(size / ONE_KB) + " KB";
		} else {
			displaySize = String.valueOf(size) + " bytes";
		}

		return displaySize;
	}

	public static boolean isBigFile(long size) {// ================================================================================================================================
		if (size / ONE_GB > 0) {
			return true;
		} else if (size / ONE_MB > 0) {
			return ((Long) (size / ONE_MB)).intValue() >= 5;
		} else
			return false;
	}

	// add by fisher for checking if user logged in
	public static String getUserEmail() {
		String userEmail = null;
		if (sPref != null)
			userEmail = sPref.getString(PREF_KEY_USER_EMAIL, null);

		return userEmail;
	}

	public static String getUserBalance() {
		String userBalance = null;

		if (sPref != null)
			userBalance = sPref.getString(PREF_KEY_USER_BALANCE, null);

		return userBalance;
	}

	public static String getDefaultChargeName() {
		String channelName = null;

		if (sPref != null)
			channelName = sPref.getString(PREF_KEY_DEFAULT_CHARGE_CHANNEL_NAME,
					null);

		return channelName;
	}

	public static String getDefaultChargeID() {
		String channelID = null;

		if (sPref != null)
			channelID = sPref.getString(PREF_KEY_DEFAULT_CHARGE_CHANNEL_ID,
					null);

		return channelID;
	}

	public static void setDefaultChargeChannel(String channelName,
			String channelID) {
		if (sPref != null) {
			Editor editor = sPref.edit();

			editor.putString(PREF_KEY_DEFAULT_CHARGE_CHANNEL_NAME, channelName);
			editor.putString(PREF_KEY_DEFAULT_CHARGE_CHANNEL_ID, channelID);

			editor.commit();
		}
	}

	// public static boolean needCheckCategoryUpdate1(Context ctx) {
	// // return needUpdate(ctx,
	// ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_TIME_1);
	// return !hasUpdated(ctx, ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_1);
	// }
	//
	// public static boolean needCheckCategoryUpdate2(Context ctx) {
	// // return needUpdate(ctx,
	// ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_TIME_2);
	// return !hasUpdated(ctx, ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_2);
	// }

	public static boolean needCheckUpdate(Context ctx) {
		return needUpdate(ctx, ConstantValues.PREF_KEY_LASTUPDATETIME);
	}

	public static void startCheckingUpdateService(Context ctx,
			boolean... isFirstLoad) {
		Intent intent = new Intent();
		intent.setClass(ctx, AppService.class);
		intent.putExtra(AppService.EXTRA_KEY_COMMAND,
				AppService.COMMAND_CHECK_SELFUPDATE);
		if (isFirstLoad != null && isFirstLoad.length == 1) {
			intent.putExtra(AppService.IS_CHECK_SELF_FIRST, isFirstLoad[0]);
		}
		ctx.startService(intent);

		saveUploadTime(ctx, ConstantValues.PREF_KEY_LASTUPDATETIME);
	}

	// public static void setUpdateTime1(Context ctx) {
	// // saveUploadTime(ctx,
	// ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_TIME_1);
	// saveHasUploaded(ctx, ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_1);
	// }
	//
	// public static void setUpdateTime2(Context ctx) {
	// // saveUploadTime(ctx,
	// ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_TIME_2);
	// saveHasUploaded(ctx, ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_2);
	// }

	public static boolean checkEmail(String email) {
		String regex = "^\\w+([-+.]\\w+)*@\\w+([-.]\\w+)*\\.\\w+([-.]\\w+)*$";// "^[a-z0-9]+([._+-]*[a-z0-9])*@([a-z0-9][a-z0-9-]{0,61}[a-z0-9].){1,3}[a-z]{2,6}$";
		Pattern p = Pattern.compile(regex);
		Matcher m = p.matcher(email);

		return m.matches();

	}

	public static boolean checkPhone(String phone) {
		String regex = "^(13[0-9]|1[(47)]|18[0-9]|15[0-9])\\d{8}$";
		Pattern p = Pattern.compile(regex);
		Matcher m = p.matcher(phone);

		return m.matches();
	}

	public static String getBOARD() {
		return Build.BOARD;
	}

	public static String getBRAND() {
		return Build.BRAND;
	}

	public static String getCPU_ABI() {
		return "";
	}

	public static String getDEVICE() {
		return Build.DEVICE;
	}

	public static String getDISPLAY() {
		return Build.DISPLAY;
	}

	public static String getFINGERPRINT() {
		return Build.FINGERPRINT;
	}

	public static String getHOST() {
		return Build.HOST;
	}

	public static String getBuildID() {
		return Build.ID;
	}

	public static String getMANUFACTURER() {
		return "";
	}

	public static String getMODEL() {
		return Build.MODEL;
	}

	public static String getPRODUCT() {
		return Build.PRODUCT;
	}

	public static String getTAGS() {
		return Build.TAGS;
	}

	public static String getTIME() {
		return Build.TIME + "";
	}

	public static String getTYPE() {
		return Build.TYPE;
	}

	public static String getUSER() {
		return Build.USER;
	}

	public static String getCODENAME() {
		return "";
	}

	public static String getINCREMENTAL() {
		return Build.VERSION.INCREMENTAL;
	}

	public static String getRELEASE() {
		return Build.VERSION.RELEASE;
	}

	public static String getSDK() {
		return Build.VERSION.SDK;
	}

	public static String getSDK_INT() {
		return "";
	}

	// public static void startSyncService(Context ctx) {
	// Intent i = new Intent();
	// i.setClass(ctx, AppService.class);
	// i.putExtra(AppService.EXTRA_KEY_COMMAND,
	// AppService.COMMAND_BACKGROUND_SYNC);
	//
	// ctx.startService(i);
	// }

	// public static void saveUploadedAPKList(Context ctx, String apklist) {
	//
	// if (sPref == null) {
	// sPref = ctx.getSharedPreferences(PREF_NAME, 0);
	// }
	//
	// Editor editor = sPref.edit();
	// editor.putString(ConstantValues.PREF_KEY_UPLOADED_LOCAL_APK_LIST,
	// apklist);
	//
	// editor.commit();
	// }
	//
	// public static String getUploadedAPKList(Context ctx) {
	//
	// if (sPref == null) {
	// sPref = ctx.getSharedPreferences(PREF_NAME, 0);
	// }
	//
	// return sPref.getString(ConstantValues.PREF_KEY_UPLOADED_LOCAL_APK_LIST,
	// null);
	// }

	public static HashMap<String, String> getUploadApps(Context ctx) {

		HashMap<String, String> result = new HashMap<String, String>();

		List<PackageInfo> packs = ctx.getPackageManager().getInstalledPackages(
				0);

		String applist = "";
		String pkglist = "";

		String appname;
		String pkgName;
		String versionName;
		String version;
		String path;

		SQLiteDatabase db = new DatabaseHelper(ctx).getWritableDatabase();

		// String updateAPKList = getUploadedAPKList(ctx);

		int count = 0;
		for (int i = 0; i < packs.size(); i++) {

			PackageInfo p = packs.get(i);

			appname = p.applicationInfo.loadLabel(ctx.getPackageManager())
					.toString();
			pkgName = p.packageName;
			versionName = p.versionName;
			version = p.versionCode + "";
			// newInfo.icon = p.applicationInfo.loadIcon(getPackageManager());
			path = p.applicationInfo.publicSourceDir;// p.applicationInfo.dataDir;

			// filter system apk
			if (path.startsWith("/system/app"))
				continue;

			result.put(pkgName, version);

			if (count != 0) {
				applist += "|";
				pkglist += ",";
			}

			applist += appname + "^" + pkgName + "^" + versionName + "^"
					+ version;
			pkglist += pkgName;
			count++;

			// check DB
			// first time
			// if (updateAPKList == null)
			// {
			// if (count != 0)
			// {
			// applist += "|";
			// pkglist += ",";
			// }
			//
			// applist += appname + "^" + pkgName + "^" + versionName + "^" +
			// version ;
			// pkglist += pkgName;
			// count ++;
			// }
			// else if (DatabaseUtils.needUploadToServer(db, pkgName, version))
			// {
			// if (updateAPKList.indexOf(pkgName) == -1)
			// {
			// if (count != 0)
			// {
			// applist += "|";
			// pkglist += ",";
			// }
			//
			// applist += appname + "^" + pkgName + "^" + versionName + "^" +
			// version ;
			// pkglist += pkgName;
			// count ++;
			// }
			// }

		}

		MyLog.d(TAG, "Total count: " + packs.size() + "  upload count: "
				+ count);

		db.close();

		result.put("applist", applist);
		result.put("pkglist", pkglist);

		MyLog.d(TAG, "applist: " + applist);

		return result;

	}

	public static HashMap<String, String> getAppInfoByPkgName(Context ctx,
			String packageName) {

		HashMap<String, String> result = new HashMap<String, String>();

		String appinfo = "";
		String appname = "";
		String pkgName = "";
		String versionName = "";
		String version = "";
		PackageInfo pack = null;
		try {
			pkgName = packageName;
			pack = ctx.getPackageManager().getPackageInfo(pkgName, 0);
			appname = pack.applicationInfo.loadLabel(ctx.getPackageManager())
					.toString();
			versionName = pack.versionName;
			version = pack.versionCode + "";

			appinfo = pkgName + "^" + appname + "^" + versionName + "^"
					+ version;
			MyLog.d(TAG, "appinfo=" + appinfo);
		} catch (NameNotFoundException e) {
			// TODO Auto-generated catch block
			MyLog.e(TAG, "not found package" + e);
			e.printStackTrace();
		}

		result.put("appinfo", appinfo);

		return result;

	}

	public static void saveUploadPackageInfo(Context ctx, String appinfo) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		String applist = appinfo;

		String logHistory = sPref.getString(
				ConstantValues.PREF_KEY_UPLOAD_PACKAGE_INFO, "");
		if (!logHistory.equals(""))
			logHistory += ",";

		applist = logHistory + applist;

		editor.putString(ConstantValues.PREF_KEY_UPLOAD_PACKAGE_INFO, applist);

		editor.commit();
	}

	public static String getUploadPackageInfo(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getString(ConstantValues.PREF_KEY_UPLOAD_PACKAGE_INFO,
				null);
	}

	public static void clearUploadPackageInfo(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}
		Editor editor = sPref.edit();

		editor.putString(ConstantValues.PREF_KEY_UPLOAD_PACKAGE_INFO, "");

		editor.commit();
	}

	public static void uploadPackageInfo(Context ctx, String appinfo,
			String type) {
		try {
			GeneralUtil.saveUploadPackageInfo(ctx, appinfo);
			String applist = GeneralUtil.getUploadPackageInfo(ctx);
			HashMap<String, Object> res;
			boolean success;
			res = NetworkManager.getLocalApplist(ctx, applist);
			if (res != null && !res.isEmpty()) {
				success = (Boolean) res.get("reqsuccess");
				if (success) {
					// if success,clear SharedPreferences;
					GeneralUtil.clearUploadPackageInfo(ctx);
				}
			}

		} catch (ClientProtocolException e) {
			// TODO Auto-generated catch block
			MyLog.e(TAG, "ClientProtocolException>>>" + e);
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			MyLog.e(TAG, "IOException>>>" + e);
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			MyLog.e(TAG, "JSONException>>>" + e);
			e.printStackTrace();
		}
	}

	public static void updateInstalledCompleteStatus(SQLiteDatabase db, App app) {
		DatabaseUtils.updateInstalledCompleteStatus(db, app.getId());

		String localPath = app.getLocalPath();
		if (localPath != null && !localPath.equals("")) {
			try {
				File apkFile = new File(localPath);
				if (apkFile.exists()) {
					apkFile.delete();
				}
			} catch (Exception e) {
				MyLog.e(TAG, e.toString());
			}
		}
	}

	public static void updateDeleteCompleteStatus(SQLiteDatabase db, App app) {
		DatabaseUtils.updateDeleteCompleteStatus(db, app.getId());
	}

	public static void markEnteredUserGuidePage(Context ctx, boolean isleaded) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}
		Editor editor = sPref.edit();
		editor.putBoolean(ConstantValues.PREF_KEY_IS_LEADED, isleaded);

		editor.commit();

	}

	public static boolean hasEnteredUserGuidePageBefore(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}
		boolean isleaded = sPref.getBoolean(ConstantValues.PREF_KEY_IS_LEADED,
				false);
		return isleaded;

	}

	public static void saveUploadTime(Context ctx, String timeType) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();

		Time t = new Time();
		t.setToNow();
		int day = t.yearDay; // 0~365
		editor.putInt(timeType, day);

		editor.commit();

	}

	public static void clearUploadTime(Context ctx, String timeType) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putInt(timeType, 366);

		editor.commit();

	}

	public static void saveHasUpdated(Context ctx, String type, boolean value) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putBoolean(type, value);
		editor.commit();
	}

	public static boolean hasUpdated(Context ctx, String type) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getBoolean(type, false);
	}

	public static boolean needUpdate(Context ctx, String timeType) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Time t = new Time();
		t.setToNow();
		int day = t.yearDay; // 0~365

		int lastUpdateDay = sPref.getInt(timeType, 366);
		MyLog.d(TAG, "current day: " + day + ", last day: " + lastUpdateDay);

		if (day != lastUpdateDay)
			return true;

		return false;
	}

	public static boolean needUpdate(int checkDay) {
		Time t = new Time();
		t.setToNow();
		int day = t.yearDay; // 0~365

		MyLog.d(TAG, "current day: " + day + ", last day: " + checkDay);

		if (day != checkDay)
			return true;

		return false;
	}

	public static int getUpdateTime() {
		Time t = new Time();
		t.setToNow();
		int day = t.yearDay; // 0~365

		return day;
	}

	// public static void setUploadAppListTime(Context ctx) {
	// // saveUploadTime(ctx, ConstantValues.PREF_KEY_LAST_UPLOAD_APPLIST_TIME);
	// saveHasUploaded(ctx, ConstantValues.PREF_KEY_LAST_UPLOAD_APPLIST);
	// }
	//
	// public static boolean needUploadApplist(Context ctx) {
	// // return needUpdate(ctx,
	// ConstantValues.PREF_KEY_LAST_UPLOAD_APPLIST_TIME);
	// return !hasUpdated(ctx, ConstantValues.PREF_KEY_LAST_UPLOAD_APPLIST);
	// }

	// public static String getUserLog(Context ctx) {
	//
	// if (sPref == null) {
	// sPref = ctx.getSharedPreferences(PREF_NAME, 0);
	// }
	//
	// return sPref.getString(ConstantValues.PREF_KEY_USER_LOG, null);
	// }
	//
	// public static void saveUserLog(Context ctx, String userLog) {
	//
	// if (sPref == null) {
	// sPref = ctx.getSharedPreferences(PREF_NAME, 0);
	// }
	//
	// Editor editor = sPref.edit();
	// editor.putString(ConstantValues.PREF_KEY_USER_LOG, userLog);
	//
	// editor.commit();
	// }

	// public static void setUploadUserlogTime(Context ctx) {
	// // saveUploadTime(ctx, ConstantValues.PREF_KEY_LAST_UPLOAD_USERLOG_TIME);
	// saveHasUploaded(ctx, ConstantValues.PREF_KEY_LAST_UPLOAD_USERLOG);
	// }
	//
	// public static boolean needUploadUserlog(Context ctx) {
	// // return needUpdate(ctx,
	// ConstantValues.PREF_KEY_LAST_UPLOAD_USERLOG_TIME);
	// return !hasUpdated(ctx, ConstantValues.PREF_KEY_LAST_UPLOAD_USERLOG);
	// }

	public static void checkAppUpdate(Context ctx) {
		// if (!needAppUpdateNotificattion(ctx))
		// return;

		int count = DatabaseUtils.getNeedUpdateAppCount(ctx);
		MyLog.d(TAG, " count= " + count);
		if (count > 0) {
			// notification user there are new version
			int icon = R.drawable.notification_upgrade;
			CharSequence tickerText = ctx
					.getString(R.string.app_has_update_notification_context);
			long when = System.currentTimeMillis();

			Notification notification = new Notification(icon, tickerText, when);
			notification.flags = Notification.FLAG_AUTO_CANCEL;

			if (count > 1)
				notification.number = count;

			CharSequence contentTitle = ctx.getString(
					R.string.app_has_update_notification_title, count);
			CharSequence contentText = tickerText;

			Intent notificationIntent = new Intent(ctx,
					MyDownloadsActivity.class);
			notificationIntent.putExtra("goto_hasupdate_cate", true);
			notificationIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP
					| Intent.FLAG_ACTIVITY_NEW_TASK);

			PendingIntent contentIntent = PendingIntent.getActivity(ctx, 0,
					notificationIntent, 0);

//			notification.setLatestEventInfo(ctx, contentTitle, contentText,
//					contentIntent);
			NotificationManager mNotificationManager = (NotificationManager) ctx
					.getSystemService(Context.NOTIFICATION_SERVICE);
			mNotificationManager.notify(
					ConstantValues.APP_HAS_UPDATE_NOTIFY_ID, notification);
		}
	}

	public static void clearAppUpdateNotification(Context ctx) {
		NotificationManager mNotificationManager = (NotificationManager) ctx
				.getSystemService(Context.NOTIFICATION_SERVICE);
		mNotificationManager.cancel(ConstantValues.APP_HAS_UPDATE_NOTIFY_ID);
	}

	private static SharedPreferences sSettingPref;

	public static boolean needDisplayImg(Context ctx) {

		if (sSettingPref == null) {
			sSettingPref = PreferenceManager.getDefaultSharedPreferences(ctx);
		}

		return sSettingPref.getBoolean("download_icon", true);
	}

	public static boolean needVoiceNotificattion(Context ctx) {

		if (sSettingPref == null) {
			sSettingPref = PreferenceManager.getDefaultSharedPreferences(ctx);
		}

		return sSettingPref.getBoolean("voice_notification", true);
	}

	public static boolean needShowRecommendAppNotificattion(Context ctx) {

		if (sSettingPref == null) {
			sSettingPref = PreferenceManager.getDefaultSharedPreferences(ctx);
		}

		return sSettingPref.getBoolean("recommend_notification", true);
	}

	public static ArrayList<String> getHotwords(Context ctx, int page) {

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		String hotwordsList = sPref.getString(ConstantValues.PREF_KEY_HOTWORDS,
				null);

		if (hotwordsList == null)
			return null;

		String[] hotwords = hotwordsList.split(",");

		ArrayList<String> currentPage = new ArrayList<String>();
		page = page % (hotwords.length / 10);

		for (int i = 0; i < 10; i++) {
			currentPage.add(hotwords[page * 10 + i]);
		}

		return currentPage;
	}

	public static void saveHotwords(Context ctx, String hotwords) {

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_HOTWORDS, hotwords);

		editor.commit();
	}

	public static void saveCateUpdateInterval(Context ctx, int cateid,
			long updateInterval) {

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putLong(cateid + "_updateInterval", updateInterval);

		editor.commit();
	}

	// public static void saveMainPage3CateUpdateTime(Context ctx) {
	//
	// saveCateUpdateTime(ctx, ConstantValues.SUGGESTED_CATE_IDLIST[0]);
	// saveCateUpdateTime(ctx, ConstantValues.SUGGESTED_CATE_IDLIST[1]);
	// saveCateUpdateTime(ctx, ConstantValues.SUGGESTED_CATE_IDLIST[2]);
	//
	// }

	public static int getCateUpdateInterval(Context ctx, String cateid) {

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getInt(cateid + "_updateInterval", 60 * 60 * 24);
	}

	public static void saveCateUpdateTime(Context ctx, int cateid) {

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putLong(cateid + "_updateTime", System.currentTimeMillis());

		editor.commit();
	}

	public static long getCateUpdateTime(Context ctx, String cateid) {

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getLong(cateid + "_updateTime", 0);
	}

	public static boolean needUpdateData(Context ctx, String cateid) {
		long now = System.currentTimeMillis();
		long lastUpdateTime = getCateUpdateTime(ctx, cateid);
		int updateInterval = getCateUpdateInterval(ctx, cateid);

		MyLog.d(TAG, "***********************cateid:" + cateid);
		MyLog.d(TAG, "***********************now:" + now);
		MyLog.d(TAG, "***********************lastUpdateTime:" + lastUpdateTime);

		int interval = ((Long) ((now - lastUpdateTime) / 1000)).intValue();

		MyLog.d(TAG, "cateid:" + cateid + " interval:" + interval
				+ " updateInterval:" + updateInterval);
		return (interval >= updateInterval || interval < 0);
		// return true;
	}

	public static void saveDownloadTime(Context ctx, String timelist) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_DOWNLOAD_TIME, timelist);

		updateTime = null;
		updateTime = timelist.split(",");

		MyLog.d(TAG, "saveDownloadTime:" + updateTime.length);

		editor.commit();
	}

	public static String[] updateTime;

	public static String getDownloadTime(Context ctx, int index) {
		MyLog.d(TAG, "getDownloadTime:" + index);

		if (updateTime != null)
			return updateTime[index];
		else {
			if (sPref == null) {
				sPref = ctx.getSharedPreferences(PREF_NAME, 0);
			}

			updateTime = sPref.getString(ConstantValues.PREF_KEY_DOWNLOAD_TIME,
					"").split(",");

			return updateTime[index];
		}

	}

	public static void saveNickName(Context ctx, String namelist) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_USER_NICKNAME, namelist);

		nickName = null;
		nickName = namelist.split(",");

		MyLog.d(TAG, "saveNickName:" + nickName.length);

		editor.commit();
	}

	public static String[] nickName;

	public static String getNickName(Context ctx, int index) {
		MyLog.d(TAG, "getNickName:" + index);

		if (nickName != null)
			return nickName[index];
		else {
			if (sPref == null) {
				sPref = ctx.getSharedPreferences(PREF_NAME, 0);
			}

			nickName = sPref.getString(ConstantValues.PREF_KEY_USER_NICKNAME,
					"").split(",");

			return nickName[index];
		}

	}

	public static void saveTextViewUpdateTime(Context ctx, String updatetime) {
		MyLog.d(TAG, "saveTextViewUpdateTime>>>:" + updatetime);
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}
		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_UPDATE_TIME, updatetime);
		editor.commit();
	}

	public static String getTextViewUpdateTime(Context ctx) {

		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}
		MyLog.d(TAG,
				"getTextViewUpdateTime>>>:"
						+ sPref.getString(ConstantValues.PREF_KEY_UPDATE_TIME,
								""));
		return sPref.getString(ConstantValues.PREF_KEY_UPDATE_TIME, "");
	}

	public static void saveUserGuideFunc(Context ctx, int userGuide) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putInt(ConstantValues.PREF_KEY_USER_GUIDE, userGuide);

		editor.commit();
	}

	public static int getUserGuideFunc(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getInt(ConstantValues.PREF_KEY_USER_GUIDE, 0);
	}

	public static void saveNeedCallBackToday(Context ctx, boolean callback) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putBoolean(ConstantValues.PREF_KEY_CALL_BACK, callback);

		editor.commit();
	}

	public static boolean needCallBackToday(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getBoolean(ConstantValues.PREF_KEY_CALL_BACK, false);
	}

	public static void saveCallBackTime(Context ctx, int callbackTime) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putInt(ConstantValues.PREF_KEY_CALL_BACK_TIME, callbackTime);

		editor.commit();
	}

	public static int getCallBackTime(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getInt(ConstantValues.PREF_KEY_CALL_BACK_TIME, 0);
	}

	public static void saveFavoriteCateList(Context ctx, String cateList) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_TOTAL_FAVORITE_CATE_LIST,
				cateList);

		editor.commit();
	}

	public static String getFavoriteCateList(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getString(
				ConstantValues.PREF_KEY_TOTAL_FAVORITE_CATE_LIST, null);
	}

	public static void saveMyFavoriteCateList(Context ctx, String cateList) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_MY_FAVORITE_CATE_LIST,
				cateList);

		editor.commit();
	}

	public static String getMyFavoriteCateList(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getString(ConstantValues.PREF_KEY_MY_FAVORITE_CATE_LIST,
				null);
	}

	public static boolean isNetworkConnected(Context ctx) {
		ConnectivityManager CM = (ConnectivityManager) ctx
				.getSystemService(Context.CONNECTIVITY_SERVICE);

		NetworkInfo info = CM.getActiveNetworkInfo();

		if (info != null) {
			return info.isAvailable();
		}
		return false;
	}

	public static void callBack(Context ctx) {
		MyLog.d(TAG, "call back !!!!");

		// notification user there are new version
		int icon = R.drawable.notification_upgrade;
		CharSequence tickerText = ctx
				.getString(R.string.callback_notification_context);
		long when = System.currentTimeMillis();

		Notification notification = new Notification(icon, tickerText, when);
		notification.flags = Notification.FLAG_AUTO_CANCEL;

		CharSequence contentTitle = ctx
				.getString(R.string.callback_notification_title);
		CharSequence contentText = tickerText;

		Intent notificationIntent = new Intent(ctx, SplashActivity.class);
		notificationIntent.putExtra("callback", true);
		notificationIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP
				| Intent.FLAG_ACTIVITY_NEW_TASK);

		PendingIntent contentIntent = PendingIntent.getActivity(ctx, 0,
				notificationIntent, 0);

//		notification.setLatestEventInfo(ctx, contentTitle, contentText,
//				contentIntent);
		NotificationManager mNotificationManager = (NotificationManager) ctx
				.getSystemService(Context.NOTIFICATION_SERVICE);
		mNotificationManager.notify(ConstantValues.CALL_BACK_NOTIFY_ID,
				notification);

	}

	// public static void showDailyRecommendNotificatin(Context ctx)
	// {
	// MyLog.d(TAG, "daily recommended notificatin!!!!");
	//
	// int icon = R.drawable.nb_icon;
	// CharSequence tickerText = ctx.getString(R.string.recommend_notification);
	// long when = System.currentTimeMillis();
	//
	// Notification notification = new Notification(icon, tickerText, when);
	//
	//
	// notification.flags = Notification.FLAG_AUTO_CANCEL;
	//
	//
	// Intent notificationIntent = new Intent(ctx, RecommendActivity.class);
	//
	// notificationIntent.setFlags(Intent.FLAG_ACTIVITY_CLEAR_TOP |
	// Intent.FLAG_ACTIVITY_NEW_TASK);
	//
	// CharSequence contentTitle =
	// ctx.getString(R.string.recommend_notification);
	// CharSequence contentText
	// =contentTitle+":\n"+GeneralUtil.getRecommendNotifitcation(ctx);
	//
	// PendingIntent contentIntent = PendingIntent.getActivity(ctx, 0,
	// notificationIntent, 0);;
	//
	// notification.setLatestEventInfo(ctx, contentTitle, contentText,
	// contentIntent);
	// NotificationManager mNotificationManager =
	// (NotificationManager)ctx.getSystemService(Context.NOTIFICATION_SERVICE);
	// mNotificationManager.notify(ConstantValues.RECOMMEND_NOTIFY_ID,
	// notification);
	//
	//
	//
	// }

	// public static void saveUserLogType1(Context ctx, long startTime, long
	// endTime)
	// {
	// if (sPref == null) {
	// sPref = ctx.getSharedPreferences(PREF_NAME, 0);
	// }
	//
	// Editor editor = sPref.edit();
	// String log = "1" + "|" + startTime + "|" + endTime;
	//
	// String logHistory = sPref.getString(ConstantValues.PREF_KEY_USER_LOG,
	// "");
	// if (!logHistory.equals(""))
	// logHistory += "^";
	//
	// log = logHistory + log;
	//
	// editor.putString(ConstantValues.PREF_KEY_USER_LOG, log);
	//
	// editor.commit();
	// }

	// // For Log user guide
	// public static void saveUserLogType2(Context ctx, int page, int action)
	// {
	// if (sPref == null) {
	// sPref = ctx.getSharedPreferences(PREF_NAME, 0);
	// }
	//
	// Editor editor = sPref.edit();
	// String log = 2 + "|" + GeneralUtil.getUserGuideFunc(ctx) + "|" + page +
	// "|" + action;
	//
	// String logHistory = sPref.getString(ConstantValues.PREF_KEY_USER_LOG,
	// "");
	// if (!logHistory.equals(""))
	// logHistory += "^";
	//
	// log = logHistory + log;
	//
	// editor.putString(ConstantValues.PREF_KEY_USER_LOG, log);
	//
	// editor.commit();
	// }

	// For Log user normal page action
	public static void saveUserLogType3(Context ctx, int page, int action) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		String log = 3 + "|" + page + "|" + action;

		String logHistory = sPref.getString(ConstantValues.PREF_KEY_USER_LOG,
				"");
		if (!logHistory.equals(""))
			logHistory += "^";

		log = logHistory + log;

		editor.putString(ConstantValues.PREF_KEY_USER_LOG, log);

		editor.commit();
	}

	// For Log user action in topic page
	public static void saveUserLogType4(Context ctx, int topicId, int action) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		String log = 4 + "|" + topicId + "|" + action;

		String logHistory = sPref.getString(ConstantValues.PREF_KEY_USER_LOG,
				"");
		if (!logHistory.equals(""))
			logHistory += "^";

		log = logHistory + log;

		editor.putString(ConstantValues.PREF_KEY_USER_LOG, log);

		editor.commit();
	}

	// For Log user action in cate page
	public static void saveUserLogType5(Context ctx, int cateId, int action) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		String log = 5 + "|" + cateId + "|" + action;

		String logHistory = sPref.getString(ConstantValues.PREF_KEY_USER_LOG,
				"");
		if (!logHistory.equals(""))
			logHistory += "^";

		log = logHistory + log;

		editor.putString(ConstantValues.PREF_KEY_USER_LOG, log);

		editor.commit();
	}

	// For Log user action in rank page
	public static void saveUserLogType6(Context ctx, int rankId, int action) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		String log = 6 + "|" + rankId + "|" + action;

		String logHistory = sPref.getString(ConstantValues.PREF_KEY_USER_LOG,
				"");
		if (!logHistory.equals(""))
			logHistory += "^";

		log = logHistory + log;

		editor.putString(ConstantValues.PREF_KEY_USER_LOG, log);

		editor.commit();
	}

	public static String getUserLog(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getString(ConstantValues.PREF_KEY_USER_LOG, null);
	}

	// For Log user action in topic page
	public static void clearUserLog(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_USER_LOG, null);

		editor.commit();
	}

	public static void saveNeedScanLocalApp(Context ctx, boolean need) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}
		Editor editor = sPref.edit();
		editor.putBoolean(ConstantValues.PREF_KEY_NEED_SCAN_LOCAL_APP, need);

		editor.commit();

	}

	public static boolean needScanLocalApp(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		boolean needScanLocalApp = sPref.getBoolean(
				ConstantValues.PREF_KEY_NEED_SCAN_LOCAL_APP, false);
		return needScanLocalApp;

	}

	public static void saveCreatedShortCut(Context ctx, boolean created) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putBoolean(ConstantValues.PREF_KEY_HAS_CREATED_SHORTCUT, created);
		editor.commit();
	}

	public static boolean hasCreatedShortCut(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getBoolean(ConstantValues.PREF_KEY_HAS_CREATED_SHORTCUT,
				false);
	}

	public static void saveUpdateDailyDataTime(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putLong(ConstantValues.PREF_KEY_LAST_UPDATE_TIME,
				System.currentTimeMillis());
		editor.commit();

	}

	public static boolean needUpdateDailyData(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		long now = System.currentTimeMillis();
		long lastUpdateDailyDataTime = sPref.getLong(
				ConstantValues.PREF_KEY_LAST_UPDATE_TIME, 0);
		MyLog.d(TAG, "current time: " + now + ", last time: "
				+ lastUpdateDailyDataTime);

		long interval = 1000 * 60 * 60 * 24;
		if ((now - lastUpdateDailyDataTime) >= interval
				|| (lastUpdateDailyDataTime - now) >= interval)
			return true;

		return false;
	}

	public static void saveRecommendDisplayList(Context ctx, String list) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_RECOMMEND_DISPLAY_LIST, list);
		editor.commit();
	}

	public static String getRecommendDisplayList(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		String displayRecommendList = sPref.getString(
				ConstantValues.PREF_KEY_RECOMMEND_DISPLAY_LIST, null);
		return displayRecommendList;
	}

	public static void sendServerClientOpen(final Context ctx) {
		// save user log
		// GeneralUtil.saveUserLogType1(SplashActivity.this,
		// System.currentTimeMillis(), 0);
		new Thread(new Runnable() {
			public void run() {
				try {
					NetworkManager.clientCheck(ctx);
				} catch (Exception e) {
					MyLog.e(TAG, e.toString());
				}
			}
		}).start();

	}

	/**
	 * save RecommendTime's SharedPreferences
	 * 
	 * @param ctx
	 */
	public static void saveRecommendTime(Context ctx, String time) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_RECOMMEND_TIME, time);
		editor.commit();
	}

	/**
	 * get RecommendTime's SharedPreferences
	 * 
	 * @param ctx
	 * @return
	 */
	public static String getRecommendTime(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		String time = sPref.getString(ConstantValues.PREF_KEY_RECOMMEND_TIME,
				"");
		return time;
	}

	public static void saveCateDisplayList(Context ctx, String list) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		String[] cateList = list.split(",");
		list = "";
		for (String cate : cateList) {
			if (list.equals("")) {
				list += "cate" + cate;
			} else {
				list += ",cate" + cate;
			}
		}

		MyLog.d(TAG, "saveCateDisplayList" + list);

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_CATE_DISPLAY_LIST, list);
		editor.commit();
	}

	public static String getCateDisplayList(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		String displayCateList = sPref.getString(
				ConstantValues.PREF_KEY_CATE_DISPLAY_LIST, null);
		return displayCateList;
	}

	public static void saveCateTime(Context ctx, String time) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_CATE_TIME, time);
		editor.commit();
	}

	public static String getCateTime(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		String time = sPref.getString(ConstantValues.PREF_KEY_CATE_TIME, "");
		return time;
	}

	public static final String LOCAL_DOWNLOAD_APP_PATH = getExternalStorageDirectory()
			+ "/.marketdownload/";
	public static final String LOCAL_DOWNLOAD_IMAGE_PATH = getExternalStorageDirectory()
			+ "/.market/";

	public static String getExternalStorageDirectory() {
		String external = null;
		if (Environment.getExternalStorageState().equals(
				Environment.MEDIA_MOUNTED)) {
			external = Environment.getExternalStorageDirectory()
					.getAbsolutePath();
			MyLog.i(TAG,
					"************************************External Storage Directory Absolute Path = "
							+ external);
		}
		MyLog.i(TAG,
				"************************************External Storage Directory Absolute Path = "
						+ Environment.getDownloadCacheDirectory().toString());
		return external;
	}

	public static Boolean getAppInfoByAppid(final Context ctx, String appid) {
		HashMap<String, Object> res;
		boolean success = false;
		try {
			res = NetworkManager.getAppInfoList(ctx, appid);
			if (res != null && !res.isEmpty()) {
				success = (Boolean) res.get("reqsuccess");
				if (success) {
					ArrayList<App> applist = (ArrayList<App>) res.get("list");

					if (applist == null || applist.size() == 0) {
						// no unstable info app
						MyLog.e("没有推荐数据哦", "no unstable info app");
					} else {
						MyLog.d(TAG, "get app info,appid=" + appid);
						SQLiteDatabase db = new DatabaseHelper(ctx)
								.getWritableDatabase();
						DatabaseUtils.saveAppList(db, applist);
						db.close();
					}
				}
			}
		} catch (ClientProtocolException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (IOException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		} catch (JSONException e) {
			// TODO Auto-generated catch block
			e.printStackTrace();
		}
		return (Boolean) success;
	}

	public static void saveBackupList(Context ctx, String backuplist) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		editor.putString(ConstantValues.PREF_KEY_BACKUP_LIST, backuplist);
		editor.commit();
	}

	public static String getBackupList(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		String backuplist = sPref.getString(
				ConstantValues.PREF_KEY_BACKUP_LIST, "");
		return backuplist;
	}

	public static void saveRecommendLastUpdateDay(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		Time t = new Time();
		t.setToNow();
		int day = t.yearDay; // 0~365
		editor.putInt(ConstantValues.PREF_KEY_RECOMMEND_LIST_LAST_UPDATE_DAY,
				day);

		editor.commit();

	}

	public static int getRecommendLastUpdateDay(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getInt(
				ConstantValues.PREF_KEY_RECOMMEND_LIST_LAST_UPDATE_DAY, 0);
	}

	public static void saveRecommendViewDay(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		Editor editor = sPref.edit();
		Time t = new Time();
		t.setToNow();
		int day = t.yearDay; // 0~365
		editor.putInt(ConstantValues.PREF_KEY_RECOMMEND_LIST_VIEW_DAY, day);

		editor.commit();

	}

	public static int getRecommendViewDay(Context ctx) {
		if (sPref == null) {
			sPref = ctx.getSharedPreferences(PREF_NAME, 0);
		}

		return sPref.getInt(ConstantValues.PREF_KEY_RECOMMEND_LIST_VIEW_DAY, 0);
	}
	public static boolean isMobileNO(String mobiles) {
		String regex = "^(13[0-9]|1[(47)]|18[0-9]|15[0-9])\\d{8}$";
		Pattern p = Pattern.compile(regex);
		Matcher m = p.matcher(mobiles);
		return m.matches();
	}
}
