package com.market.hjapp;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.util.Log;
import android.widget.Toast;

import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.network.NetworkManager;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import java.io.IOException;
import java.util.ArrayList;
import java.util.HashMap;
public class AppTabSpec implements Runnable {

	private static final String TAG = "AppTabSpec";

	public ArrayList<App> data;

	private String mApplist;

	public int mCateId;
	private int mType;
	private int mApplistOrderType;

	private int curpage;
	private int totalpage;
	private int numPerPage;
	/** 当前页应用总量 */
	private int appCount;

	private long updateInterval;
	private long lastUpdateTime;

	SQLiteDatabase mDb;
	Context mContext;

	protected AppLoadResultListener mListener;

	public AppTabSpec(Context context, int cateId, int type,
			int applistOrderType, SQLiteDatabase db,
			AppLoadResultListener listener) {
		mContext = context;
		data = new ArrayList<App>();

		mCateId = cateId;
		MyLog.e("mCateId赋值", "-----------------------------------");
		mType = type;
		mApplistOrderType = applistOrderType;

		mDb = db;
		mListener = listener;
		numPerPage = ConstantValues.NUM_PER_PAGE;

		curpage = 0;

		Category c = DatabaseUtils.getCategory(mDb, cateId);
		appCount = c.getAppCount();
		totalpage = appCount / numPerPage;
		if (appCount > totalpage * numPerPage) {
			totalpage += 1;
		}

		updateInterval =0;// c.getUpdateInterval();

		lastUpdateTime = DatabaseUtils.getAppListLastUpdateTime(mDb, cateId,
				applistOrderType);
	}

	public AppTabSpec(Context context, String appidlist, SQLiteDatabase db,
			AppLoadResultListener listener) {
		mContext = context;
		data = new ArrayList<App>();
		mDb = db;
		mListener = listener;
		numPerPage = ConstantValues.NUM_PER_PAGE;
		mApplist = appidlist;

		curpage = -1;

		// String[] appid = appidlist.split(",");
		// appCount = appid.length;
		// totalpage = appCount/numPerPage;
		// if (appCount > totalpage * numPerPage) {
		// totalpage += 1;
		// }

	}

	// private TaskResultListener mGetAppInfoListTaskResultListener = new
	// TaskResultListener() {
	//
	// @SuppressWarnings("unchecked")
	// @Override
	// public void onTaskResult(boolean success, HashMap<String, Object> res) {
	// if (!success) {
	// if (res == null)
	// {
	// Toast.makeText(mContext, R.string.error_http_timeout,
	// Toast.LENGTH_LONG).show();
	// }
	//
	// MyLog.e(TAG, "Can't get applist!");
	// loading = false;
	// return;
	// }
	//
	// final ArrayList<App> applist = (ArrayList<App>)res.get("list");
	// MyLog.d(TAG, "applist size: " + applist.size());
	// if (applist == null || applist.size() == 0) {
	// MyLog.e(TAG, "Can't get applist!");
	// loading = false;
	// return;
	// }
	//
	// try
	// {
	// new SaveAppListTask(applist).execute();
	// } catch (RejectedExecutionException e) {
	// MyLog.e(TAG, "Got exception when execute asynctask!", e);
	// }
	//
	// }
	//
	// };

	// public String getUncachedAppList()
	// {
	// String curpageAppList = getCurAppListString();
	// String uncachedAppList = null;
	// if (curpageAppList !=null )
	// {
	// uncachedAppList = DatabaseUtils.getUncachedAppList(mDb, curpageAppList);
	// }
	// return uncachedAppList;
	// }

	// private void loadCurPageAppList()
	// {
	// if (curpageAppList !=null )
	// {
	// String uncachedAppList = null;
	// uncachedAppList = DatabaseUtils.getUncachedAppList(mDb, curpageAppList);
	//
	// if (uncachedAppList != null && !uncachedAppList.equals(""))
	// {
	// MyLog.d(TAG, "---------------- need load uncached app");
	// MyLog.d(TAG, "---------------- load from web " + curpage);
	//
	// try
	// {
	// // need got those app info
	// new GetAppInfoListTask((Activity)ctx, mGetAppInfoListTaskResultListener).
	// execute(uncachedAppList);
	// } catch (RejectedExecutionException e) {
	// MyLog.e(TAG, "Got exception when execute asynctask!", e);
	// }
	// }
	// else
	// {
	// MyLog.d(TAG, "----------------  load from local ");
	// refreshData(DatabaseUtils.getAppList(mDb, curpageAppList));
	// }
	// }
	// else
	// {
	// mListener.onAppLoadResult(null);
	// loading = false;
	// }
	// }

	private void refreshData(ArrayList<App> applist) {
		MyLog.d(TAG, "data addAll before>>>data.length=" + data.size()
				+ ",applist.length=" + applist.size());
		data.addAll(applist);
		MyLog.d(TAG, "data addAll after>>>data.length=" + data.size());
		curpage = data.size() / ConstantValues.NUM_PER_PAGE;
		if (data.size() > curpage * ConstantValues.NUM_PER_PAGE)
			curpage++;
		returnSuccess();
	}

	// private String appIdList = null;

	// private String getCurAppListString()
	// {
	// int numPerPage = Integer.parseInt(ConstantValues.NUM_PER_PAGE);
	//
	// if (appIdList == null)
	// {
	// if(mCateId==null)
	// {
	// appIdList = list;
	// return appIdList;
	// }else{
	//
	// if (mIsRank)
	// {
	// // for rank
	// appIdList = DatabaseUtils.getAppIdListInRank(mDb, mCateId, mSorting);
	// // calculate app account and total page
	// String[] applistArray = appIdList.split(",");
	// appCount = applistArray.length;
	// totalpage = appCount/numPerPage;
	// if (appCount > totalpage * numPerPage) {
	// totalpage += 1;
	// }
	//
	// }
	// else
	// {
	// appIdList = DatabaseUtils.getAppIdList(mDb, mCateId, mSorting);
	// }
	// }
	// }
	//
	// if (appIdList !=null && !"".equals(appIdList) )
	// {
	// String[] applistArray = appIdList.split(",");
	// int length = applistArray.length;
	// int start = numPerPage * curpage;
	// int end = numPerPage * (curpage + 1);
	// if (end > length)
	// end = length;
	// MyLog.d(TAG, "start " + start + " " + "end " + end );
	//
	// String curpageAppList = "";
	// for (int j = start; j< end; j++)
	// curpageAppList += applistArray[j] + ",";
	//
	// return curpageAppList;
	// }
	// else
	// return null;
	// }

	// public String getNewestAppId()
	// {
	// if (appIdList == null)
	// appIdList = DatabaseUtils.getAppIdList(mDb, mCateId, mSorting);
	//
	// if (appIdList !=null && !"".equals(appIdList) )
	// {
	// String[] applistArray = appIdList.split(",");
	// return applistArray[0];
	// }
	//
	// return "";
	// }

	public void reSetTotalPage(Context mContext) {
		MyLog.d(TAG, "curpage=" + curpage + ",totalpage=" + totalpage);
		totalpage = curpage;

	}

	public boolean hasMorePage() {
		MyLog.d(TAG, "hasMorePage >> curpage: " + curpage + ", totalpage: "
				+ totalpage);
		return curpage < totalpage;
	}

	public interface AppLoadResultListener {
		public void onAppLoadResult(ArrayList<App> data);
	}

	// class SaveAppListTask extends AsyncTask<String, Void, HashMap<String,
	// Object>> {
	//
	// private static final String TAG = "BaseAsyncTask";
	//
	// ArrayList<App> applist;
	//
	// public SaveAppListTask(ArrayList<App> applist) {
	//
	// this.applist = applist;
	// }
	//
	// @Override
	// protected void onPreExecute() {
	//
	// }
	//
	// @Override
	// protected HashMap<String, Object> doInBackground(String... params) {
	//
	// SQLiteDatabase db = new DatabaseHelper(mContext).getWritableDatabase();
	// DatabaseUtils.saveAppList(db, applist);
	// applist = DatabaseUtils.getAppList(db, curpageAppList);
	//
	// db.close();
	//
	// return null;
	// }
	//
	// @Override
	// protected void onPostExecute(HashMap<String, Object> result) {
	// if (mDb.isOpen())
	// refreshData(applist);
	// else
	// {
	// loading = false;
	// }
	// }
	//
	// }

	public void clearData() {
		mApplist = null;
		curpage = 0;
		data.clear();

		// temp
		updateInterval = -1;
	}

	private boolean loading = false;
	private String curpageAppList;

	public void loadMoreApps() {
		MyLog.d(TAG, "loadMoreApps()");
		if (!mDb.isOpen())
			return;

		if (loading)
			return;

		loading = true;

		new Thread(this).start();
	}

	@Override
	public void run() {
		if (mApplist == null) {
			// check if need update app list of this cate
			long now = System.currentTimeMillis();
			int interval = ((Long) ((now - lastUpdateTime) / 1000)).intValue();

			MyLog.d(TAG, "=== now:" + now + "=== lastUpdateTime:"
					+ lastUpdateTime);
			MyLog.d(TAG, "=== interval:" + interval + "=== updateInterval:"
					+ updateInterval);

			if (interval >= 0 && interval < updateInterval) {
				// load app list from DB
				MyLog.d(TAG, "=== load app list list from DB ===");
				if (mDb.isOpen()) {
					mApplist = DatabaseUtils.getAppListInOneCate(mDb, mCateId,
							mApplistOrderType);
					MyLog.e("mCateId", "==============" + mCateId);

				} else {
					returnFailed();
					return;
				}
			}

			if ((interval >= updateInterval || interval < 0)
					|| mApplist == null) {
				// load app list from Web
				MyLog.d(TAG, "=== load app list from server ===");
				mApplist = loadAppListFromServer();

				// save app list and lastUpdateTime to DB
				if (mDb.isOpen()) {
					// save lastUpdateTime
					lastUpdateTime = System.currentTimeMillis();
					DatabaseUtils.saveApplistInOneCate(mDb, mCateId,
							mApplistOrderType, mApplist, lastUpdateTime);
				} else {
					SQLiteDatabase db = new DatabaseHelper(mContext)
							.getWritableDatabase();
					lastUpdateTime = System.currentTimeMillis();
					DatabaseUtils.saveApplistInOneCate(db, mCateId,
							mApplistOrderType, mApplist, lastUpdateTime);
					db.close();
				}

			}

			// zxg,20120412,debug for crash
			if (mApplist == null || mApplist.equals("")) {
				returnFailed();
				return;
			}
		}

		// load app all in one time
		if (curpage == -1) {
			curpageAppList = mApplist;
		} else {
			// Load Current page app list
			int numPerPage = ConstantValues.NUM_PER_PAGE;

			String[] applistArray = mApplist.split(",");
			int length = applistArray.length;
			int start = numPerPage * curpage;
			int end = numPerPage * (curpage + 1);
			if (end > length)
				end = length;
			MyLog.d(TAG, "start " + start + " " + "end " + end);

			// if has more on server
			if (start >= end && length < appCount) {
				// load app list from web
				curpageAppList = loadAppListFromServer();
			} else {
				curpageAppList = "";
				for (int j = start; j < end; j++)
					curpageAppList += applistArray[j] + ",";
			}
		}

		if (curpageAppList == null || curpageAppList.equals("")) {
			MyLog.e("-----------------------", "returnFailed();");
			returnFailed();
			return; 
		}

		String uncachedAppList = null;
		if (mDb.isOpen()) {
			try {
				uncachedAppList = DatabaseUtils.getUncachedAppList(mDb,
						curpageAppList);
			} catch (Exception e) {
				e.printStackTrace();
				MyLog.d(TAG, " "+e.getMessage());
			}
		} else {
			SQLiteDatabase db = new DatabaseHelper(mContext)
					.getWritableDatabase();
			uncachedAppList = DatabaseUtils.getUncachedAppList(db,
					curpageAppList);
			db.close();
		}

		if (uncachedAppList != null && !uncachedAppList.equals("")) {
			MyLog.d(TAG, "---------------- need load uncached app");
			MyLog.d(TAG, "---------------- load from web " + curpage);

			if (!loadUncachedAppList(uncachedAppList)) {
				MyLog.d(TAG, "---------------- load app failed " + curpage);
				returnFailed();
				return;
			} else {
				returnSuccess();
				return;
			}
		} else {
			MyLog.d(TAG, "----------------  load from local ");

			if (mDb.isOpen())
				try {
					refreshData(DatabaseUtils.getAppList(mDb, curpageAppList));
				} catch (Exception e) {
					e.printStackTrace();
					MyLog.d(TAG, " "+e.getMessage());
				}
			else {
				SQLiteDatabase db = new DatabaseHelper(mContext)
						.getWritableDatabase();
				refreshData(DatabaseUtils.getAppList(db, curpageAppList));
				db.close();
			}
		}
	}

	private void returnFailed() {
		Message m = mHandler.obtainMessage(MESSAGE_ONAPPLOADRESULT);
		Bundle data = new Bundle();

		data.putBoolean("load_result", false);
		m.setData(data);

		mHandler.sendMessage(m);
	}

	private void returnSuccess() {
		Message m = mHandler.obtainMessage(MESSAGE_ONAPPLOADRESULT);
		Bundle data = new Bundle();

		data.putBoolean("load_result", true);
		m.setData(data);

		mHandler.sendMessage(m);
	}

	static final int MESSAGE_ONAPPLOADRESULT = 201;
	private Handler mHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			int what = msg.what;
			if (what == MESSAGE_ONAPPLOADRESULT) {
				boolean result = msg.getData().getBoolean("load_result");

				if (result) {
					if (mListener != null)
						mListener.onAppLoadResult(data);
					loading = false;
				} else {
					if (mListener != null)
						mListener.onAppLoadResult(null);
					loading = false;
				}

				return;
			}

			super.handleMessage(msg);
		}
	};

	public String loadAppListFromServer() {// 从服务器取得应用列表
		String result = null;

		try {
			int pageNo;
			int perPage;
			// check curpage
			if (curpage == 0) {
				pageNo = 1;
				perPage = 50;
			} else {
				pageNo = curpage + 1;
				perPage = ConstantValues.NUM_PER_PAGE;
			}

			HashMap<String, Object> res;
			boolean success = false;
			res = NetworkManager.getOneCateApplistPage(mContext, mCateId + "",
					mType + "", mApplistOrderType + "", pageNo + "", perPage
							+ "");
			if (res != null && !res.isEmpty()) {
				success = (Boolean) res.get("reqsuccess");
				if (success) {
					result = (String) res.get("list");
					Log.d(TAG, "loadAppListFromServer: " + result);
				}
			}
		} catch (Exception e) {
			MyLog.e(TAG, e.toString());
		}

		return result;
	}

	private boolean loadUncachedAppList(String appIdList) {
		try {
			HashMap<String, Object> res = null;

			try {
				res = NetworkManager.getAppInfoList(mContext, appIdList);
			} catch (ClientProtocolException e) {
				e.printStackTrace();
			} catch (IOException e) {
				e.printStackTrace();
			} catch (JSONException e) {
				e.printStackTrace();
			}

			if (res == null) {
				Toast.makeText(mContext, R.string.error_http_timeout,
						Toast.LENGTH_LONG).show();
				return false;
			}

			ArrayList<App> applist = (ArrayList<App>) res.get("list");
			if (applist == null || applist.size() == 0) {
				return false;
			}
			MyLog.d(TAG, "applist size: " + applist.size());

			SQLiteDatabase db = new DatabaseHelper(mContext)
					.getWritableDatabase();
			DatabaseUtils.saveAppList(db, applist);
			applist = DatabaseUtils.getAppList(db, curpageAppList);
			db.close();

			MyLog.d(TAG, "data addAll before>>>data.length=" + data.size()
					+ ",applist.length=" + applist.size());
			data.addAll(applist);
			MyLog.d(TAG, "data addAll after>>>data.length=" + data.size());
			curpage = data.size() / ConstantValues.NUM_PER_PAGE;
			if (data.size() > curpage * ConstantValues.NUM_PER_PAGE)
				curpage++;

			return true;
		} catch (Exception e) {
			MyLog.e(TAG, e.toString());
		}
		return false;
	}

}
