package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.sqlite.SQLiteDatabase;
import android.os.Bundle;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ListView;
import android.widget.Toast;
import android.widget.AdapterView.OnItemClickListener;

import com.market.hjapp.App;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.ui.adapter.AppListAdapter;
import com.market.hjapp.ui.adapter.AppListAdapter2;
import com.market.hjapp.ui.tasks.SearchTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;

public class SearchedResultActivity extends BaseActivity {
	private static final String TAG = "SearchedResultActivity";

	ListView mContentList;

	AppListAdapter2 mResultListAdapter;

	private OnItemClickListener mListItemClickListener = new OnItemClickListener() {

		@Override
		public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
				long arg3) {

			saveUserLog(1);

			App app;
			app = (App) mResultListAdapter.getItem(arg2);

			Intent i = new Intent(getApplicationContext(),
					AppDetailActivity.class);
			i.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app.getId());

			mSelectAppId = app.getId();

			i.putExtra(EXTRA_KEY_PARENTNAME,
					getString(R.string.tabtitle_searchresult));
			startActivityForResult(i, AppDetailActivity.REQUEST_SHOW_DETAILS);
		}

	};

	private TaskResultListener mSearchTaskResultListener = new TaskResultListener() {

		@SuppressWarnings("unchecked")
		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {

				int errMsg;
				if (res == null) {
					errMsg = R.string.error_http_timeout;
				} else {
					String error = (String) res.get("errno");
					if (error.equals("E116"))
						errMsg = R.string.error_search_no_app;
					else
						errMsg = R.string.error_search_failed;
				}

				Toast.makeText(getApplicationContext(), errMsg,
						Toast.LENGTH_LONG).show();

				finish();
			} else {
				ArrayList<App> applist = (ArrayList<App>) res.get("list");

				totalPage = (Integer) res.get("totalpage");

				SQLiteDatabase db = new DatabaseHelper(
						SearchedResultActivity.this).getWritableDatabase();
				DatabaseUtils.saveAppList(db, applist);
				db.close();

				if (mDb.isOpen()) {
					mResultApplist.addAll(applist);
					mResultListAdapter.setData(mResultApplist);
				}
			}
		}

	};

	private ArrayList<App> mResultApplist;
	private int totalPage = 1;

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		setContentView(R.layout.search_result);

		saveUserLog(0);

		mContentList = (ListView) findViewById(R.id.contentList);
		mContentList.setSelector(R.drawable.c5);
		mContentList.setOnItemClickListener(mListItemClickListener);

		mResultListAdapter = new AppListAdapter2(SearchedResultActivity.this);
		mContentList.setAdapter(mResultListAdapter);

		mResultApplist = new ArrayList<App>();

		String keyWord = getIntent().getStringExtra("key");

		try {
			new SearchTask(SearchedResultActivity.this,
					mSearchTaskResultListener).execute(keyWord);
		} catch (RejectedExecutionException e) {
			MyLog.e(TAG, "Got exception when execute asynctask!", e);
		}

	}

	private int mSelectAppId;

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		if (requestCode == AppDetailActivity.REQUEST_SHOW_DETAILS) {

			// update selected app status
			for (App app : mResultApplist) {
				if (app.getId() == mSelectAppId) {
					HashMap<String, Integer> result = DatabaseUtils.getAppInfo(
							SearchedResultActivity.this, mSelectAppId);
					app.setStatus(result.get("status"));
					app.setScore(result.get("score"));
					app.setScoreCount(result.get("score_cnt"));
					mResultListAdapter.notifyDataSetChanged();
				}
			}
		}
		super.onActivityResult(requestCode, resultCode, data);
	}

	private DownloadStatusReceiver mDownloadReceiver;

	@Override
	protected void onStart() {
		mDownloadReceiver = new DownloadStatusReceiver();
		IntentFilter downloadFilter = new IntentFilter();
		downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_COMPLETE);
		downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_UPDATE);
		downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_ERROR);

		registerReceiver(mDownloadReceiver, downloadFilter);

		super.onStart();
	}

	@Override
	protected void onStop() {
		unregisterReceiver(mDownloadReceiver);

		super.onStop();
	}

	private class DownloadStatusReceiver extends BroadcastReceiver {

		@Override
		public void onReceive(Context context, Intent intent) {
			final String action = intent.getAction();
			MyLog.d(TAG, "DownloadStatusReceiver >>> onReceive >>> action: "
					+ action);

			final int appid = intent.getIntExtra(AppService.DOWNLOAD_APP_PID,
					-1);
			MyLog.d(TAG, "appid: " + appid);

			int appstatus = -1;
			if (AppService.BROADCAST_DOWNLOAD_UPDATE.equals(action)) {
				appstatus = App.DOWNLOADING;
			} else if (AppService.BROADCAST_DOWNLOAD_COMPLETE.equals(action)) {
				appstatus = App.DOWNLOADED;
			} else if (AppService.BROADCAST_DOWNLOAD_ERROR.equals(action)) {
				appstatus = App.INIT;
			} else {
				throw new RuntimeException("got unknown action: " + action);
			}

			ArrayList<App> applist = mResultApplist;
			MyLog.d(TAG, "data size: " + applist.size());
			for (App app : applist) {
				MyLog.d(TAG, "existed app id:" + app.getId());
				if (app.getId() != appid) {
					continue;
				}

				MyLog.d(TAG, "app status: " + app.getStatus());
				if (appstatus != app.getStatus()) {
					app.setStatus(appstatus);

					mResultListAdapter.notifyDataSetChanged();
				}
			}

		}

	}

	private void saveUserLog(int action) {
		// save user log
		GeneralUtil.saveUserLogType3(SearchedResultActivity.this, 34, action);
		// if (action==0) {
		// tracker.trackPageView("/"+TAG);
		// }
		// else {
		// tracker.trackEvent(""+3, ""+34, "", action);
		// }
	}

}