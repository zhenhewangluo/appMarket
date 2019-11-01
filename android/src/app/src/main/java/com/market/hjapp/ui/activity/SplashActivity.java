package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.Date;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.app.AlertDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.Intent.ShortcutIconResource;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.format.DateFormat;
import android.text.format.Time;
import android.widget.ImageView;
import android.widget.Toast;

import com.market.hjapp.App;
import com.market.hjapp.AppTabSpec;
import com.market.hjapp.Category;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.Recommend;
import com.market.hjapp.AppTabSpec.AppLoadResultListener;
import com.market.hjapp.database.DatabaseHelper;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.network.NetworkManager;
import com.market.hjapp.service.UpdateDataService;
import com.market.hjapp.service.UploadLocalAppService;
import com.market.hjapp.ui.tasks.AnonymousLoginTask;
import com.market.hjapp.ui.tasks.GetCategoryListTask;
import com.market.hjapp.ui.tasks.GetFavoriteCateListTask;
import com.market.hjapp.ui.tasks.GetRecommendTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.FancyProgressBar;

//import dalvik.system.VMRuntime;

public class SplashActivity extends BaseActivity {
	private final static int CWJ_HEAP_SIZE = 6* 1024* 1024 ;
	private final static float TARGET_HEAP_UTILIZATION = 0.75f;
	private static final String TAG = "SplashActivity";
	private final Context mContext = SplashActivity.this;
	private AppLoadResultListener mAppLoadResultListener = new AppLoadResultListener() {
		@Override
		public void onAppLoadResult(ArrayList<App> data) {

			// GeneralUtil.saveUpdateDailyDataTime(SplashActivity.this);
			// GeneralUtil.saveHasUpdated(SplashActivity.this,
			// ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_1, true);
			// GeneralUtil.saveHasUpdated(SplashActivity.this,
			// ConstantValues.PREF_KEY_LAST_CATEGORY_UPDATE_2, true);
			// GeneralUtil.saveHasUpdated(SplashActivity.this,
			// ConstantValues.PREF_KEY_LAST_UPLOAD_APPLIST, true);
			// GeneralUtil.saveHasUpdated(SplashActivity.this,
			// ConstantValues.PREF_KEY_LAST_UPLOAD_USERLOG, true);

			afterLoadFirstScreen();
		}

	};

	// private TaskResultListener mAnonymousLoginTaskResultListener = new
	// TaskResultListener() {
	//
	// @Override
	// public void onTaskResult(boolean success, HashMap<String, Object> res) {
	// if (!success) {
	// Toast.makeText(getApplicationContext(), R.string.network_error_msg,
	// Toast.LENGTH_LONG).show();
	// finish();
	// } else {
	//
	// // splash activity is not exist
	// if (!mDb.isOpen())
	// return;
	//
	// try
	// {
	// new GetFavoriteCateListTask(SplashActivity.this,
	// mGetFavoriteCateListTaskResltListener, false).execute();
	// }
	// catch (RejectedExecutionException e) {
	// MyLog.e(TAG, "Got exception when execute asynctask!", e);
	// }
	// }
	// }
	//
	// };
	//
	// private TaskResultListener mGetFavoriteCateListTaskResltListener = new
	// TaskResultListener() {
	//
	// @Override
	// public void onTaskResult(boolean success, HashMap<String, Object> res) {
	// if (!success) {
	// Toast.makeText(getApplicationContext(), R.string.network_error_msg,
	// Toast.LENGTH_LONG).show();
	// finish();
	// } else {
	//
	// // splash activity is not exist
	// if (!mDb.isOpen())
	// return;
	//
	// String list = (String)res.get("list");
	// MyLog.d(TAG, "favorite cate list: " + list);
	// GeneralUtil.saveFavoriteCateList(SplashActivity.this, list);
	//
	// try
	// {
	// new GetCategoryListTask(SplashActivity.this,
	// mGetCategoryListTaskResltListener).execute();
	// }
	// catch (RejectedExecutionException e) {
	// MyLog.e(TAG, "Got exception when execute asynctask!", e);
	// }
	//
	// }
	// }
	//
	// };

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
//		VMRuntime.getRuntime().setMinimumHeapSize(CWJ_HEAP_SIZE); //设置最小heap内存为6MB大小
//		VMRuntime.getRuntime().setTargetHeapUtilization(TARGET_HEAP_UTILIZATION);
		// log the channel and device id
		MyLog.i("Market", "c -> " + ConstantValues.CHANNEL_ID_CURRENT + ", d -> "
				+ ConstantValues.DEVICE_ID_CURRENT);

		BaseActivity.isMarketStarted = true;

		if (!GeneralUtil.isNetworkConnected(SplashActivity.this)) {// 检测网络连接
			new AlertDialog.Builder(SplashActivity.this)
					.setTitle(R.string.prompt_title)
					.setMessage(R.string.no_network)
					.setPositiveButton(R.string.network_setting,
							new DialogInterface.OnClickListener() {

								@Override
								public void onClick(DialogInterface dialog,
										int which) {
									startActivityForResult(
											new Intent(
													android.provider.Settings.ACTION_WIRELESS_SETTINGS),
											0);
									finish();
								}
							})
					.setNegativeButton(R.string.cancel,
							new DialogInterface.OnClickListener() {

								@Override
								public void onClick(DialogInterface dialog,
										int which) {
									finish();
								}

							}).create().show();

			return;
		}

		// Save user lunch from call back
		boolean lunchFromCallback = getIntent().getBooleanExtra("callback",
				false);
		if (lunchFromCallback)
			GeneralUtil.saveUserLogType3(SplashActivity.this, 36, 0);

		// Log
		// tracker.trackPageView("/" + TAG);

		if (GeneralUtil.needCallBackToday(SplashActivity.this)) {
			GeneralUtil.saveNeedCallBackToday(SplashActivity.this, false);
		}

		loadData();
		setSplashView();

	}

	private void loadData() {
		new Thread(new Runnable() {
			public void run() {

				GeneralUtil.markEnteredUserGuidePage(SplashActivity.this, true);
				if (!GeneralUtil.hasInitialized(SplashActivity.this)) {
					GeneralUtil.markEnteredUserGuidePage(SplashActivity.this,
							false);
					GeneralUtil.saveNeedScanLocalApp(SplashActivity.this, true);

					MyLog.d(TAG, "==== anonymousLogin ====");
					if (!anonymousLogin())
						return;

					if (!getFavoriteCateList())
						return;

					if (!getHotWords())
						return;
				}

				mProgressMax = PROGRESS_STEP2;
				mProgress = PROGRESS_STEP1;

				if (GeneralUtil.needUpdate(SplashActivity.this,
						ConstantValues.PREF_KEY_CATEGORY_UPDATE_TIME)) {
					MyLog.d(TAG, "==== update Cate ====");
					if (!getCateList())
						return;

					GeneralUtil.saveUploadTime(SplashActivity.this,
							ConstantValues.PREF_KEY_CATEGORY_UPDATE_TIME);
				}

				mProgressMax = PROGRESS_STEP3;
				mProgress = PROGRESS_STEP2;

				Time currentTime = new Time();
				currentTime.setToNow();
				int currentDay = currentTime.yearDay; // 0-365
				if (GeneralUtil.getRecommendLastUpdateDay(SplashActivity.this) != currentDay) {
					if (!getRecommendList())
//						mDisplayRecommend = false;
						afterLoadFirstScreen();//herokf add 
						return;
				} else {
					if (GeneralUtil.getRecommendViewDay(SplashActivity.this) == currentDay)
						mDisplayRecommend = false;

					afterLoadFirstScreen();
				}

			}
		}).start();
	}

	private void afterLoadFirstScreen() {

		if (!UpdateDataService.sHasStarted) {
			Intent i = new Intent();
			i.setClass(getApplicationContext(), UpdateDataService.class);

			SplashActivity.this.startService(i);

			MyLog.d(TAG, "==== service crash, start it again ====");
			startMainActivity();
			return;
		}

		if (!UpdateDataService.sThreadRunning) {
			MyLog.d(TAG, "==== start normal ====");
			startMainActivity();
			return;
		}

		new Thread(new Runnable() {
			public void run() {
				int timeout = 5000;
				long startTime = System.currentTimeMillis();
				while (UpdateDataService.sThreadRunning) {
					MyLog.d(TAG, "======== System.currentTimeMillis() "
							+ System.currentTimeMillis());
					MyLog.d(TAG, "========================= startTime "
							+ startTime);

					if ((System.currentTimeMillis() - startTime) < timeout) {
						try {
							Thread.sleep(500);
						} catch (Exception ex) {
							MyLog.e(TAG, ex.toString());
						}
					} else {

						// Intent i = new Intent();
						// i.setClass(SplashActivity.this,
						// UpdateDataService.class);
						// MyLog.d(TAG,
						// "===================== STOP SERVICE1 ==============");
						// SplashActivity.this.stopService(i);
						// MyLog.d(TAG,
						// "===================== STOP SERVICE2 ==============");
						// SplashActivity.this.startService(i);
						break;
					}
				}

				startMainActivity();
			}
		}).start();
	}

//	private FancyProgressBar mProgressBar;
	private ImageView mProgressImageView;

	private final int PROGRESS_STEP1 = 25;
	private final int PROGRESS_STEP2 = 80;
	private final int PROGRESS_STEP3 = 95;
	private final int PROGRESS_STEP = 8;

	private int mProgress = 0;
	private int percent = 0;
	private int mProgressMax = PROGRESS_STEP1;

	private void setSplashView() {
		setContentView(R.layout.splash_activity);

		// start loading animation
		mProgressImageView = (ImageView) findViewById(R.id.progress);

//		new Thread(new Runnable() {
//			
//			@Override
//			public void run() {
//				
//				int timeout = 120000;
//				long startTime = System.currentTimeMillis();
//				
//				while (mProgressMax != 100) {
//					// TIME OUT
//					if ((System.currentTimeMillis() - startTime) > timeout) {
//						finish();
//						return;
//					}
//					
//					sendMsg(mProgress);
//					mProgress += Math.random() * PROGRESS_STEP;
//					if (mProgress > mProgressMax)
//						mProgress = mProgressMax;
//					
//					try {
//						Thread.sleep(1000);
//					} catch (InterruptedException e) {
//						e.printStackTrace();
//					}
//				}
//				
//				sendMsg(mProgressMax);
//				
//				sendGoNextPageMessage();
//				
//			}
//		}).start();
		new Thread(new Runnable() {

			@Override
			public void run() {

				int timeout = 120000;
				long startTime = System.currentTimeMillis();

				while (percent < 100) {
					// TIME OUT
					if ((System.currentTimeMillis() - startTime) > timeout) {
						finish();
						return;
					}
					sendMsg(percent);
					percent += 10;
//					mProgress += Math.random() * PROGRESS_STEP;
//					if (mProgress > mProgressMax)
//						mProgress = mProgressMax;

					try {
						Thread.sleep(500);
					} catch (InterruptedException e) {
						e.printStackTrace();
					}
				}
				sendMsg(100);

//				sendGoNextPageMessage();

			}
		}).start();

		// // start loading animation
		// Animation anim = AnimationUtils.loadAnimation(this,
		// R.anim.left_to_right_loop);
		// findViewById(R.id.cursor).startAnimation(anim);

		Date date = new Date(System.currentTimeMillis());
		String helloString = getString(R.string.firsttime_prompt,
				DateFormat.format("yyyy-MM-dd", date));

		// show first time popup
		Toast.makeText(getApplicationContext(), helloString, Toast.LENGTH_LONG)
				.show();
//		Toast.makeText(getApplicationContext(), R.string.firsttime_prompt, Toast.LENGTH_LONG);
	}

	private void sendMsg(int percent) {
		Message m = mHandler.obtainMessage(MESSAGE_UPDATE_PROGRESS);
		Bundle data = new Bundle();

		data.putInt("percent", percent);
		m.setData(data);

		mHandler.sendMessage(m);
	}

	private void closeApp() {
		Message m = mHandler.obtainMessage(MESSAGE_CLOSE_APP);
		mHandler.sendMessage(m);
	}

	static final int MESSAGE_UPDATE_PROGRESS = 201;//更新进展
	static final int MESSAGE_LOAD_FIRST_PAGE_APP = 202;//加载首页应用
	static final int MESSAGE_GO_NEXT_PAGE = 203;//进入下一页
	static final int MESSAGE_CLOSE_APP = 204;// 关闭应用

	private Handler mHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			int what = msg.what;
			if (what == MESSAGE_UPDATE_PROGRESS) {
				int percent = msg.getData().getInt("percent");
				//herokf add for percents update
				switch (percent) {
					case 0 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_00);
						break;
					case 10 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_10);
						break;
					case 20:
						mProgressImageView.setBackgroundResource(R.drawable.loading_20);
						break;
					case 30 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_30);
						break;
					case 40 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_40);
						break;
					case 50 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_50);
						break;
					case 60 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_60);
						break;
					case 70 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_70);
						break;
					case 80 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_80);
						break;
					case 90 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_90);
						break;
					case 100 :
						mProgressImageView.setBackgroundResource(R.drawable.loading_100);
						break;
				}
//				mProgressBar.setProgress(progress);
//				if (progress > 50) {
//					ImageView background = (ImageView) findViewById(R.id.background);
//					background.setImageResource(R.drawable.splash);
//				}
				return;
			} else if (what == MESSAGE_LOAD_FIRST_PAGE_APP) {
				String applist = msg.getData().getString("applist");

				new AppTabSpec(mContext, applist, mDb, mAppLoadResultListener)
						.loadMoreApps();
			} else if (what == MESSAGE_GO_NEXT_PAGE) {
				goNextPage();
				return;
			} else if (what == MESSAGE_CLOSE_APP) {
				Toast.makeText(getApplicationContext(),
						R.string.network_error_msg, Toast.LENGTH_LONG).show();
				finish();
			}

			super.handleMessage(msg);
		}
	};

	private boolean mDisplayRecommend = true;

	private void startMainActivity() {
		if (GeneralUtil.needCheckUpdate(SplashActivity.this)) {
			GeneralUtil.startCheckingUpdateService(SplashActivity.this, false);
		}

		// checking apps update进度条
		new Thread(new Runnable() {

			@Override
			public void run() {

				GeneralUtil.checkAppUpdate(SplashActivity.this);
			}
		}).start();

		mProgressMax = 100;
		sendGoNextPageMessage();//herokf add 
		// // start
		// if (mProgress != 0)
		// {
		// mProgress = 100;
		// }
		// else
		// {
		// sendGoNextPageMessage();
		// }
	}

	private void sendGoNextPageMessage() {
		Message m = mHandler.obtainMessage(MESSAGE_GO_NEXT_PAGE);
		mHandler.sendMessage(m);
	}

	private void goNextPage() {
		if (GeneralUtil.needScanLocalApp(SplashActivity.this)) {
			GeneralUtil.saveNeedScanLocalApp(SplashActivity.this, false);

			// first time, or db is null
			// scan use local app
			new Thread(new Runnable() {
				@Override
				public void run() {

					Intent i = new Intent();
					i.setClass(getApplicationContext(),
							UploadLocalAppService.class);

					SplashActivity.this.startService(i);
				}

			}).start();
		}

		GeneralUtil.sendServerClientOpen(SplashActivity.this);

		if (!GeneralUtil.hasEnteredUserGuidePageBefore(SplashActivity.this)) {

			// first time, user will goto user guide page
			GeneralUtil.markEnteredUserGuidePage(SplashActivity.this, true);

			// switch (GeneralUtil.getUserGuideFunc(SplashActivity.this)) {
			// case 0:
			// startActivity(new Intent(getApplicationContext(),
			// LeadActivity.class));
			// break;
			//
			// case 1:
			// startActivity(new Intent(getApplicationContext(),
			// Lead2Activity.class));
			// break;
			//
			// case 2:
			// startActivity(new Intent(getApplicationContext(),
			// Lead2Activity.class));
			// break;
			//
			// case 3:
			// startActivity(new Intent(getApplicationContext(),
			// RecommendActivity.class));
			// break;
			// }

			Intent i = new Intent(getApplicationContext(), Lead2Activity.class);
			i.putExtra("pkg_name", this.getPackageName());
			i.putExtra("class_name", this.getLocalClassName());
			startActivity(i);

		} else {
			if (mDisplayRecommend)// GeneralUtil.needUpdate(SplashActivity.this,
									// ConstantValues.PREF_KEY_LAST_VIEW_RECOMMEND_PAGE_DAY))
			{
				startActivity(new Intent(getApplicationContext(),
						RecommendActivity.class));
			} else {
				startActivity(new Intent(getApplicationContext(),
						BrowseSuggestedAppListActivity.class));
			}
		}

		finish();
	}

	private boolean anonymousLogin() {
		try {
			HashMap<String, Object> anonymousLoginRes = NetworkManager
					.anonymousLogin(SplashActivity.this);
			if (anonymousLoginRes == null || anonymousLoginRes.isEmpty()
					|| !(Boolean) anonymousLoginRes.get("reqsuccess")) {
				closeApp();
				return false;
			}

			String mid = (String) anonymousLoginRes.get("mid");
			String sid = (String) anonymousLoginRes.get("sid");

			// save status and mid/sid
			GeneralUtil.saveLoginInfo(SplashActivity.this, mid, sid);

			int user_guide = (Integer) anonymousLoginRes.get("user_guide");
			GeneralUtil.saveUserGuideFunc(SplashActivity.this, user_guide);

			return true;

		} catch (Exception e) {
			MyLog.d(TAG, e.toString());
			closeApp();
		}

		return false;
	}

	/**
	 * 获得喜好分类
	 * @return
	 */
	private boolean getFavoriteCateList() {
		try {
			HashMap<String, Object> res = NetworkManager
					.getFavoriteChannel(SplashActivity.this);

			if (res == null) {
				closeApp();

				return false;
			} else {

				String list = (String) res.get("list");
				MyLog.d(TAG, "favorite cate list: " + list);
				GeneralUtil.saveFavoriteCateList(SplashActivity.this, list);

				return true;
			}

		} catch (Exception e) {
			MyLog.d(TAG, e.toString());
			closeApp();
		}

		return false;
	}

	/**
	 * 获得搜索热词
	 * @return
	 */
	private boolean getHotWords() {
		try {
			HashMap<String, Object> res = NetworkManager.getHotwordsList(
					SplashActivity.this, 100 + "");

			if (res == null) {
				closeApp();

				return false;
			} else {
				String hotWordlist = (String) res.get("list");
				GeneralUtil.saveHotwords(SplashActivity.this, hotWordlist);
				return true;
			}
		} catch (Exception e) {
			MyLog.d(TAG, e.toString());
			closeApp();
		}

		return false;
	}

	/**
	 * 获得分类列表
	 * @return
	 */
	private boolean getCateList() {
		try {
			String updateTime = GeneralUtil.getCateTime(SplashActivity.this);
			HashMap<String, Object> res = NetworkManager.getCateListNew(
					SplashActivity.this, updateTime);

			if (res == null) {
				closeApp();

				return false;
			}

			ArrayList<Category> cateList = (ArrayList<Category>) res
					.get("list");

			// no more new cate
			if (cateList == null || cateList.size() == 0) {
				return true;
			}

			// splash activity is not exist
			if (!mDb.isOpen())
				return false;

			MyLog.d(TAG, "cate list size: " + cateList.size());
			DatabaseUtils.saveCategoryList(mContext, cateList);

			updateTime = (String) res.get("updatetime");
			GeneralUtil.saveCateTime(SplashActivity.this, updateTime);

			String cateDisplayList = (String) res.get("showlist");
			GeneralUtil.saveCateDisplayList(SplashActivity.this,
					cateDisplayList);

			return true;

		} catch (Exception e) {
			MyLog.d(TAG, e.toString());
			closeApp();
		}

		return false;
	}

	/**
	 * 获得今日推荐列表
	 * @return
	 */
	private boolean getRecommendList() {
		try {
			String time = GeneralUtil.getRecommendTime(mContext);
			HashMap<String, Object> res = NetworkManager.getRecommmendByTime(
					SplashActivity.this, time);
			if (res == null) {
				closeApp();
				return false;
			}
			if (!(Boolean)res.get("reqsuccess")) {
				return false;
			}
			final ArrayList<Recommend> recommendList = (ArrayList<Recommend>) res
					.get("list");
			String displayList = (String) res.get("display");
			time = (String) res.get("time");
			if (recommendList.size() == 0) {
				MyLog.d(TAG, "recommendList size=0");
				GeneralUtil.saveRecommendDisplayList(mContext, displayList);
				GeneralUtil.saveRecommendTime(mContext, time);
				mDisplayRecommend = false;
				afterLoadFirstScreen();
				return false;//herokf add
			}
			// splash activity is not exist
			if (!mDb.isOpen()) {
				closeApp();
				return false;
			}
			if (recommendList != null && recommendList.size() > 0) {
				DatabaseUtils.saveRecommendList(mContext, recommendList);
				GeneralUtil.saveRecommendDisplayList(mContext, displayList);
				GeneralUtil.saveRecommendTime(mContext, time);
				GeneralUtil.saveRecommendLastUpdateDay(this);
			}
			// String applist = DatabaseUtils.getAppIdListInRecommend(mDb);
			// MyLog.d(TAG, "All app list: " + applist);

			// Message m = mHandler.obtainMessage(MESSAGE_LOAD_FIRST_PAGE_APP);
			// Bundle data = new Bundle();
			//
			// data.putString("applist", applist);
			// m.setData(data);
			//
			// mHandler.sendMessage(m);
			afterLoadFirstScreen();

			return true;

		} catch (Exception e) {
			MyLog.d(TAG, e.toString());
			closeApp();
		}

		return false;
	}

}
