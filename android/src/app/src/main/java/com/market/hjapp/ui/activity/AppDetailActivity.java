package com.market.hjapp.ui.activity;

import java.io.File;
import java.math.BigDecimal;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;
import java.util.concurrent.RejectedExecutionException;

import android.app.AlertDialog;
import android.app.NotificationManager;
import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.support.v4.view.ViewPager;
import android.support.v4.view.ViewPager.OnPageChangeListener;
import android.text.Html;
import android.text.TextUtils;
import android.text.method.LinkMovementMethod;
import android.text.method.ScrollingMovementMethod;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.App;
import com.market.hjapp.AppTabSpec;
import com.market.hjapp.AppTabSpec.AppLoadResultListener;
import com.market.hjapp.Comment;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.UserInfo;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.service.download.FileManager;
import com.market.hjapp.service.download.FileManipulation;
import com.market.hjapp.ui.adapter.AppListAdapter;
import com.market.hjapp.ui.adapter.CommentListAdapter;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.tasks.GetAppInfoVersionTask;
import com.market.hjapp.ui.tasks.GetCommentListTask;
import com.market.hjapp.ui.tasks.GetRelateAppListTask;
import com.market.hjapp.ui.tasks.ProcessInstallTask;
import com.market.hjapp.ui.view.FancyProgressBar;
import com.market.hjapp.ui.view.LongButton;
import com.market.hjapp.ui.view.RatingStars;
import com.viewpagerindicator.PageIndicator;
import com.viewpagerindicator.TitlePageIndicator;

public class AppDetailActivity extends BaseActivity {
	private static final String TAG = "AppDetailActivity";

	protected static final String EXTRA_KEY_APPID = "appid";

	public static final int REQUEST_LOGIN = 101;
	public static final int REQUEST_SHOW_DETAILS = 102;
	public static final int REQUEST_PAY = 103;
	public static final int REQUEST_COMMENT = 104;
	public static final int REQUEST_COMMENT_GRADE = 105;

	static final int MESSAGE_UPDATE_STATUS = 201;

	// View mFirstHeaderTab;
	// View mFirstPressedHeaderTab;
	// View mSecondHeaderTab;
	// View mSecondPressedHeaderTab;
	// View mThirdHeaderTab;
	// View mThirdPressedHeaderTab;

	/** 软件介绍 */
	View mAppdetail;
	/** 查看评论 */
	View mAppcomment;
	/** 相关软件 */
	View mRelatedapp;
	/** 底部按钮 */
	View mBottomBar;

	TextView mBgText;
	// private LongButton mRateButton;
	// private LongButton mViewRatingButton;

	ImageView app_icon;
	TextView app_name;
	TextView app_author;
	TextView app_version;
	TextView app_size;
	TextView app_desc;
	TextView app_language;
	ImageView app_screenshot;
	ImageView app_screenshot2;
	RatingStars app_rating;
	TextView app_ratingCnt;
	TextView app_avgPoint;
	TextView app_commentCnt;

	private App mApp;

	ListView mContentList;
	CommentListAdapter mListAdatper;
	ArrayList<Comment> data;

	private View mBottomButtons;
	private View mBottomProgress;
	private TextView mDownloadCount;
	View mLoadingBackground;
	View mRelativeAppLoadingBg;

	private View mOneBtn;
	private View mTwoBtn;
	private View mThreeBtn;

	private LongButton mBtn1;
	private LongButton mBtn2;
	private LongButton mBtn3;
	ImageView stars_grade;
	ImageView mShareButton;
	// View stars_grade1;
	// View stars_grade2;
	// View starts_click1;
	// View starts_click2;

	private ArrayList<App> mList;
	private View mPauseBtn;
	private View mResumeBtn;
	private View mStopBtn;
	private static int mCurHeaderTab = -1;
	private TextView mPrice;
	private AppListAdapter mAdapter;
	// private ListView mAppRelatelist;

	private FancyProgressBar mProgressBar;

	private SharedPreferences mNotificationSharedPreferences;
	private NotificationManager mNotificationManager;

	private Handler mHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			int what = msg.what;
			if (what == MESSAGE_UPDATE_STATUS) {
				int appstatus = msg.getData().getInt("status");
				int progress = msg.getData().getInt("progress");

				if (appstatus == App.DOWNLOADING)
					setDownloadProgress(false, progress);
				else
					updateStatus(appstatus, progress);
				return;
			}

			super.handleMessage(msg);
		}
	};
	private TaskResultListener mGetCommentTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {

				if (res == null) {
					Toast.makeText(getApplicationContext(),
							R.string.error_http_timeout, Toast.LENGTH_LONG)
							.show();
				} else {
					String error = (String) res.get("errno");
					int errMsg;
					if (error.equals("E301")) {
						mLoadingBackground.setVisibility(View.GONE);
						MyLog.d(TAG, "111");
						// mBgText.setVisibility(View.VISIBLE);
					} else {
						errMsg = R.string.get_comments_failed;
						Toast.makeText(getApplicationContext(), errMsg,
								Toast.LENGTH_LONG).show();

						finish();
					}
				}
			} else {
				ArrayList<Comment> commentList = (ArrayList<Comment>) res
						.get("list");
				MyLog.d(TAG, "comment list size: " + commentList.size());
				if (commentList == null || commentList.size() == 0) {
					MyLog.e(TAG, "Can't get comment list!");
					return;
				}
				// 分开view后不需要再设置这个
				mLoadingBackground.setVisibility(View.GONE);

				mListAdatper.setData(commentList);
			}
			MyLog.e("获得评论列表后", "  ");
			setButtonNumber(1);// 暂时屏蔽
			// 一下的mBtn1需要打开屏蔽
			mBtn1.setText(R.string.bottom_button_comment);
		}

	};

	private TaskResultListener mInstallTaskResultListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {

				if (res == null) {
					Toast.makeText(getApplicationContext(),
							R.string.error_http_timeout, Toast.LENGTH_LONG)
							.show();
				} else {
					String error = (String) res.get("errno");
					if (error.equals("E008")) {
						// Unauthorized access, need login
						Intent i = new Intent(getApplicationContext(),
								LoginDialogActivity.class);

						i.putExtra("page_no", 32);

						i.putExtra("hint",
								getString(R.string.login_hint_download));

						startActivityForResult(i, REQUEST_LOGIN);
					} else {
						Toast.makeText(getApplicationContext(),
								(String) res.get("errmsg"), Toast.LENGTH_LONG)
								.show();
					}
				}

				return;
			}

			String downloadPath = (String) res.get("location");
			String downloadId = (String) res.get("download_id");
			MyLog.d(TAG, "download id: " + downloadId + ", download path: "
					+ downloadPath);

			// 创建下载的应用程序提供商
			mApp.setDownloadId(downloadId);
			mApp.setDownloadPath(downloadPath);

			// if (mDb.isOpen())
			// DatabaseUtils.updateAppDownloadInfo(mDb, mApp.getId(),
			// downloadPath, downloadId);
			// else
			// {
			// MyLog.d(TAG, "closed database");
			// // db has been closed, possible this activity has been destroyed
			// SQLiteDatabase db = new
			// DatabaseHelper(AppDetailActivity.this).getWritableDatabase();
			// DatabaseUtils.updateAppDownloadInfo(db, mApp.getId(),
			// downloadPath, downloadId);
			// db.close();
			// }

			Intent intent = new Intent(getApplicationContext(),
					AppService.class);
			intent.putExtra(AppService.EXTRA_KEY_COMMAND,
					AppService.COMMAND_DOWNLOAD);
			intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, mApp.getDbId());
			intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_ID,
					mApp.getDownloadId());
			intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_URL,
					mApp.getDownloadPath());
			intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPID, mApp.getId());
			intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPNAME,
					mApp.getName());
			intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_COUNT,
					mApp.getDownloadCount());

			startService(intent);

			// finish();//在打开应用内部后点击下载 不结束当前页面 ERROR-APPDETAILACTIVITY-01
		}

	};

	private AppLoadResultListener mAppLoadResultListener = new AppLoadResultListener() {

		@Override
		public void onAppLoadResult(ArrayList<App> data) {
			mRelativeAppLoadingBg.setVisibility(View.GONE);
			// mAppRelatelist.setVisibility(View.VISIBLE);
			if (null != data) {
				mList = data;
				mAdapter.setData(mList);
			} else {
				// Toast.makeText(getApplicationContext(), "获取相关软件失败，请稍后再试！",
				// Toast.LENGTH_LONG).show();
			}
		}

	};
	OnItemClickListener mListItemClickListener = new OnItemClickListener() {

		@Override
		public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
				long arg3) {
			saveUserLog(2);

			App app = (App) mAdapter.getItem(arg2);

			Intent i = new Intent(getApplicationContext(),
					AppDetailActivity.class);
			i.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app.getId());
			// i.putExtra(AppDetailActivity.EXTRA_KEY_INFOVERSION,
			// app.getInfoVersion());

			int resid = R.string.related_app;

			i.putExtra(EXTRA_KEY_PARENTNAME, getString(resid));
			startActivity(i);
			finish();// 新增 销毁此前activity 返回时直接返回至主页
		}
	};
	private TaskResultListener mGetRelateAppListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {
				if (res == null) {
					Toast.makeText(getApplicationContext(),
							R.string.error_http_timeout, Toast.LENGTH_LONG)
							.show();
				} else {
					Toast.makeText(getApplicationContext(),
							R.string.error_relate_list, Toast.LENGTH_LONG)
							.show();
				}
			} else {
				if (!mDb.isOpen()) {
					return;
				}
				String appidList = (String) res.get("list");
				new AppTabSpec(AppDetailActivity.this, appidList, mDb,
						mAppLoadResultListener).loadMoreApps();
			}
		}
	};
	private TaskResultListener mGetInfoVersionAppListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {
				if (res == null) {
					Toast.makeText(getApplicationContext(),
							R.string.error_http_timeout, Toast.LENGTH_LONG)
							.show();
				} else {
					Toast.makeText(getApplicationContext(),
							R.string.error_relate_list, Toast.LENGTH_LONG)
							.show();
				}
			} else {

				MyLog.d(TAG, "mGetInfoVersionAppListener >>>11111");

				if (!mDb.isOpen()) {
					return;
				}

				App app = (App) res.get("app");

				if (mDb.isOpen()) {
					MyLog.d(TAG, "mGetInfoVersionAppListener >>>22222");
					if (mInfoVersion != app.getInfoVersion()) {
						// replace app all data
						DatabaseUtils.saveApp(mDb, app);
					} else {
						// update unstable info
						DatabaseUtils.updateOneAppState(mDb, app);
					}

					MyLog.d(TAG, "mGetInfoVersionAppListener >>>33333");
					RefreshView();
				}
			}
		}

	};

	/** 头部选项卡已失效 */
	private void initHeaderTabs() {/*
									 * mFirstHeaderTab =
									 * findViewById(R.id.first_headertab);
									 * mFirstPressedHeaderTab =
									 * findViewById(R.id
									 * .first_headertab_pressed);
									 * 
									 * mSecondHeaderTab =
									 * findViewById(R.id.second_headertab);
									 * mSecondPressedHeaderTab =
									 * findViewById(R.id
									 * .second_headertab_pressed);
									 * 
									 * mThirdHeaderTab =
									 * findViewById(R.id.third_headertab);
									 * mThirdPressedHeaderTab =
									 * findViewById(R.id
									 * .third_headertab_pressed);
									 * 
									 * // add click listener
									 * mFirstHeaderTab.setOnClickListener(new
									 * OnClickListener() {
									 * 
									 * public void onClick(View v) {
									 * setSelectedHeaderTab(0); }
									 * 
									 * });
									 * 
									 * mSecondHeaderTab.setOnClickListener(new
									 * OnClickListener() {
									 * 
									 * public void onClick(View v) {
									 * setSelectedHeaderTab(1); }
									 * 
									 * });
									 * 
									 * mThirdHeaderTab.setOnClickListener(new
									 * OnClickListener() {
									 * 
									 * public void onClick(View v) {
									 * setSelectedHeaderTab(2); }
									 * 
									 * });
									 */
	}

	protected void setSelectedHeaderTab(int i) {
		if (mCurHeaderTab == i)
			return;
		mCurHeaderTab = i;
		saveUserLog(0);
		/****
		 * 以下已经失效 mFirstHeaderTab.setVisibility(i == 0 ? View.GONE :
		 * View.VISIBLE); mFirstPressedHeaderTab.setVisibility(i == 0 ?
		 * View.VISIBLE : View.GONE);
		 * 
		 * mSecondHeaderTab.setVisibility(i == 1 ? View.GONE : View.VISIBLE);
		 * mSecondPressedHeaderTab .setVisibility(i == 1 ? View.VISIBLE :
		 * View.GONE);
		 * 
		 * mThirdHeaderTab.setVisibility(i == 2 ? View.GONE : View.VISIBLE);
		 * mThirdPressedHeaderTab.setVisibility(i == 2 ? View.VISIBLE :
		 * View.GONE);
		 * 
		 * mBgText.setVisibility(View.GONE);
		 */

		switch (i) {
		case 0:
			MyLog.e("AppDetail", "显示软件详情页");
			// 最新屏蔽
			// mAppdetail.setVisibility(View.VISIBLE);
			// mAppcomment.setVisibility(View.GONE);
			// mRelatedapp.setVisibility(View.GONE);
			mBottomBar.setVisibility(View.VISIBLE);
			RefreshView();
			// 以下原本就屏蔽了
			// int progress = Math.round((((float) mApp.getDownloadedSize()
			// /
			// mApp.getSize()) * 100));
			// updateStatus(mApp.getStatus(), progress);
			break;
		case 1:
			MyLog.e("AppDetail", "显示软件评论页");
			// 最新屏蔽
			// mAppdetail.setVisibility(View.GONE);
			// mAppcomment.setVisibility(View.VISIBLE);
			// mRelatedapp.setVisibility(View.GONE);
			mBottomBar.setVisibility(View.VISIBLE);
			mLoadingBackground.setVisibility(View.VISIBLE);
			new GetCommentListTask(AppDetailActivity.this,
					mGetCommentTaskResultListener, false).execute(mAppid + "");

			showProgress(false);
			setButtonNumber(1);// 暂时屏蔽
			// 一下的mBtn1需要打开屏蔽
			mBtn1.setText(R.string.bottom_button_comment);
			mBtn1.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {
					if (System.currentTimeMillis() - commentBtnCanBePressed < 1000)
						return;
					commentBtnCanBePressed = System.currentTimeMillis();

					saveUserLog(1);

					if (GeneralUtil.getHasLoggedIn(AppDetailActivity.this)) {
						// 如果已经登录就允许评论 否则不能评论
						Intent i = new Intent(getApplicationContext(),
								CommentActivity.class);
						i.putExtra("appid", mApp.getId());
						startActivityForResult(i, REQUEST_COMMENT);
					} else {
						Toast.makeText(AppDetailActivity.this, "请先登录后再评论！",
								Toast.LENGTH_LONG).show();
					}
				}
			});
			// 以下原本就屏蔽过
			// mBtn2.setText(R.string.bottom_button_download);
			// mBtn2.setOnClickListener(mDownloadListener);
			// mContentList.setVisibility(View.GONE);
			break;

		case 2:
			MyLog.e("AppDetail", "显示软件相关页");
			// 最新屏蔽
			// mAppdetail.setVisibility(View.GONE);
			// mAppcomment.setVisibility(View.GONE);
			// mRelatedapp.setVisibility(View.VISIBLE);
			mBottomBar.setVisibility(View.GONE);
			// 原本屏蔽
			// progress = Math.round((((float) mApp.getDownloadedSize() /
			// mApp.getSize()) * 100));
			// updateStatus(mApp.getStatus(), progress);

			mRelativeAppLoadingBg.setVisibility(View.VISIBLE);// 显示进度条
			// mAppRelatelist.setVisibility(View.GONE);
			new GetRelateAppListTask(AppDetailActivity.this,
					mGetRelateAppListener).execute(mApp.getId() + "");
			break;
		default:
			break;
		}

	}

	private OnClickListener mScreenshotOnClickListener = new OnClickListener() {

		@Override
		public void onClick(View arg0) {

			saveUserLog(12);

			Intent i = new Intent(getApplicationContext(),
					ViewScreenshotsActivity.class);
			i.putExtra("appid", mApp.getId());
			i.putExtra(EXTRA_KEY_PARENTNAME, mApp.getName());
			startActivity(i);
		}
	};

	// private OnClickListener mScreenshotOnClickListener2 = new
	// OnClickListener() {
	//
	// @Override
	// public void onClick(View arg0) {
	//
	// saveUserLog(13);
	//
	// Intent i = new Intent(getApplicationContext(),
	// ViewScreenshotsActivity.class);
	// i.putExtra("appid", mApp.getId());
	// i.putExtra(EXTRA_KEY_PARENTNAME, mApp.getName());
	//
	// startActivity(i);
	// }
	//
	// };

	private OnClickListener mInstallListener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			saveUserLog(7);

			int appId = mApp.getId();
			// int eventId = mNotificationSharedPreferences.getInt(appId, 1);

			mNotificationManager.cancel(appId);
			// Start installing manual
			String path = DatabaseUtils.getInstallingAppLocalPath(
					AppDetailActivity.this, appId);
			try {
				FileManager.install(path, AppDetailActivity.this);
			} catch (Exception e) {
				MyLog.e(TAG, "Failed to install the file " + path);
			}
		}

	};

	private OnClickListener mPauseProgressListener = new OnClickListener() {
		@Override
		public void onClick(View v) {
			saveUserLog(4);

			mPauseBtn.setVisibility(View.GONE);
			mResumeBtn.setVisibility(View.VISIBLE);

			mPrice.setText(R.string.status_paused);

			Intent intent = new Intent(getApplicationContext(),
					AppService.class);
			intent.putExtra(AppService.EXTRA_KEY_COMMAND,
					AppService.COMMAND_PAUSE);
			intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, mApp.getDbId());

			startService(intent);
		}
	};

	private OnClickListener mResumeProgressListener = new OnClickListener() {
		@Override
		public void onClick(View v) {
			saveUserLog(5);

			mPauseBtn.setVisibility(View.VISIBLE);
			mResumeBtn.setVisibility(View.GONE);

			mPrice.setText(R.string.status_downloading);

			Intent intent = new Intent(getApplicationContext(),
					AppService.class);
			intent.putExtra(AppService.EXTRA_KEY_COMMAND,
					AppService.COMMAND_RESUME);
			intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, mApp.getDbId());

			startService(intent);
		}
	};

	private OnClickListener mStopProgressListener = new OnClickListener() {
		@Override
		public void onClick(View v) {

			saveUserLog(6);

			DatabaseUtils
					.resetAppToInit(AppDetailActivity.this, mApp.getDbId());

			String localPath = DatabaseUtils.getInstallingAppLocalPath(
					AppDetailActivity.this, mApp.getId());
			if (!TextUtils.isEmpty(localPath)) {
				File currFile = new File(localPath);
				currFile.delete();
			}

			updateStatus(App.INIT, 0);

			Intent intent = new Intent(getApplicationContext(),
					AppService.class);
			intent.putExtra(AppService.EXTRA_KEY_COMMAND,
					AppService.COMMAND_CANCEL);
			intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, mApp.getDbId());

			startService(intent);
		}
	};

	private OnClickListener mLaunchListener = new OnClickListener() {

		@Override
		public void onClick(View v) {

			saveUserLog(11);
			// open other app
			openOtherApp(mApp.getPackgeName());
			/*
			 * try { new LaunchAppTask(AppDetailActivity.this, new
			 * TaskResultListener() {
			 * 
			 * @Override public void onTaskResult(boolean success,
			 * HashMap<String, Object> res) { if (success) { String pname =
			 * (String)res.get("package_name"); String name =
			 * (String)res.get("name"); Intent i = new Intent();
			 * i.setAction(Intent.ACTION_MAIN);
			 * i.addCategory(Intent.CATEGORY_LAUNCHER);
			 * i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK |
			 * Intent.FLAG_ACTIVITY_RESET_TASK_IF_NEEDED); i.setComponent(new
			 * ComponentName(pname, name));
			 * 
			 * startActivity(i); } } }).execute(mApp.getPackgeName());
			 * 
			 * } catch (RejectedExecutionException e) { MyLog.e(TAG,
			 * "Got exception when execute asynctask!", e); }
			 */
		}
	};

	private void openOtherApp(String packageName) {
		try {

			Intent i = getPackageManager().getLaunchIntentForPackage(
					packageName);
			startActivity(i);

		} catch (Exception e) {
			MyLog.e(TAG, "Got exception when open other app!", e);
		}

	}

	private OnClickListener mUninstallListener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			saveUserLog(9);

			FileManipulation.uninstall(AppDetailActivity.this,
					mApp.getPackgeName());
		}

	};

	private OnClickListener mDeleteListener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			saveUserLog(8);

			DatabaseUtils
					.resetAppToInit(AppDetailActivity.this, mApp.getDbId());

			String localPath = DatabaseUtils.getInstallingAppLocalPath(
					AppDetailActivity.this, mApp.getId());
			if (!TextUtils.isEmpty(localPath)) {
				File currFile = new File(localPath);
				currFile.delete();
			}

			if (mApp.getDbId() != ConstantValues.CLIENT_DBID) {
				DatabaseUtils.resetAppToInit(AppDetailActivity.this,
						mApp.getDbId());
			}

			int appId = mApp.getId();
			int eventId = appId;

			mNotificationManager.cancel(eventId);

			updateStatus(App.INIT, 0);
		}

	};

	private OnClickListener mUpdateListener = new OnClickListener() {

		@Override
		public void onClick(View v) {

			saveUserLog(10);

			if (System.currentTimeMillis() - downloadBtnCanBePressed < 2000)
				return;

			downloadBtnCanBePressed = System.currentTimeMillis();

			try {
				new ProcessInstallTask(AppDetailActivity.this,
						mInstallTaskResultListener).execute(mApp.getId() + "",
						mApp.getPayId(), mParent);
			} catch (RejectedExecutionException e) {
				MyLog.e(TAG, "Got exception when execute asynctask!", e);
			}
		}

	};

	private long downloadBtnCanBePressed;
	private long commentBtnCanBePressed;

	private OnClickListener mDownloadListener = new OnClickListener() {

		@Override
		public void onClick(View v) {

			saveUserLog(3);
			if (System.currentTimeMillis() - downloadBtnCanBePressed < 2000)
				return;

			boolean isBigFile = GeneralUtil.isBigFile(mApp.getSize());

			NetworkInfo info = ((ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE))
					.getActiveNetworkInfo();

			// TODO:deal with no network
			if (info == null)
				return;

			if (isBigFile && info.getType() != ConnectivityManager.TYPE_WIFI) {
				new AlertDialog.Builder(AppDetailActivity.this)
						.setTitle(R.string.prompt_title)
						.setMessage(R.string.bigsize_file)
						.setPositiveButton(R.string.network_setting,
								new DialogInterface.OnClickListener() {

									@Override
									public void onClick(DialogInterface dialog,
											int which) {
										startActivityForResult(
												new Intent(
														android.provider.Settings.ACTION_WIRELESS_SETTINGS),
												0);
									}
								})
						.setNegativeButton(R.string.continue_download,
								new DialogInterface.OnClickListener() {

									@Override
									public void onClick(DialogInterface dialog,
											int which) {
										doDownload();
										// add this can updateStstus immediately
										// when you press button
										updateStatus(App.DOWNLOADING, 0);
									}
								}).create().show();
			} else
				doDownload();
			// add this can updateStstus immediately when you press button
			updateStatus(App.DOWNLOADING, 0);
		}
	};

	private void doDownload() {
		downloadBtnCanBePressed = System.currentTimeMillis();

		float price = Float.parseFloat(mApp.getPrice());
		MyLog.d(TAG, "price is: " + price);

		if (price > 0) {
			processPayment(mApp.getId() + "", price + "");
		} else {
			try {
				new ProcessInstallTask(AppDetailActivity.this,
						mInstallTaskResultListener).execute(mApp.getId() + "",
						mApp.getPayId(), mParent);
			} catch (RejectedExecutionException e) {
				MyLog.e(TAG, "Got exception when execute asynctask!", e);
			}
		}
	}

	private DownloadStatusReceiver mDownloadReceiver;

	private int mAppid;
	private int mInfoVersion;
	private String mParent;

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		MyLog.d(TAG, "on create >>>");
		super.onCreate(savedInstanceState);
		setContentView(R.layout.app_detail_activity);

		// mAppdetail = findViewById(R.id.detail);
		// mAppcomment = findViewById(R.id.comment_list);
		// mRelatedapp = findViewById(R.id.relates_app);
		mNotificationSharedPreferences = getSharedPreferences(
				ConstantValues.APP_INSTALL_NOTIFICATION, MODE_PRIVATE);
		mNotificationManager = (NotificationManager) getSystemService(NOTIFICATION_SERVICE);
		mAppid = getIntent().getIntExtra(EXTRA_KEY_APPID, -1);
		if (mAppid == -1) {
			MyLog.e(TAG, "not invalid appid");
			return;
		}
		// set header tab info
		// TextView pressed, unpressed;
		// View headInfo = (View) findViewById(R.id.header_info);
		// pressed = (TextView) headInfo
		// .findViewById(R.id.first_headertab_pressed_text);
		// unpressed = (TextView) headInfo.findViewById(R.id.first_headertab);
		// pressed.setText(R.string.app_detail);
		// unpressed.setText(R.string.app_detail);
		//
		// pressed = (TextView) headInfo
		// .findViewById(R.id.second_headertab_pressed_text);
		// unpressed = (TextView) headInfo.findViewById(R.id.second_headertab);
		// pressed.setText(R.string.comment_list);
		// unpressed.setText(R.string.comment_list);
		//
		// pressed = (TextView) headInfo
		// .findViewById(R.id.third_headertab_pressed_text);
		// unpressed = (TextView) headInfo.findViewById(R.id.third_headertab);
		// pressed.setText(R.string.related_app);
		// unpressed.setText(R.string.related_app);

		MyLog.d(TAG, "App id=" + mAppid);
		initView();
		// set title
		// TextView title = (TextView) findViewById(R.id.top_title);
		// mParent = getIntent().getStringExtra(EXTRA_KEY_PARENTNAME);
		// title.setText(mParent);

		downloadBtnCanBePressed = 0;
		commentBtnCanBePressed = 0;

		MyLog.d(TAG, "on create >>>10");
		mApp = DatabaseUtils.getAppById(mDb, mAppid);
		mInfoVersion = mApp.getInfoVersion();
		if (GeneralUtil.needUpdate(mApp.getLastUpdateTime())) {
			new GetAppInfoVersionTask(AppDetailActivity.this,
					mGetInfoVersionAppListener).execute(mAppid + "",
					mInfoVersion + "");
		}

		MyLog.d(TAG, "on create >>>11");
	}

	@Override
	protected void onResume() {
		super.onResume();
		// initView();
		if (mCurHeaderTab == 0)
			RefreshView();
		// 新添加 以解决恢复后显示内容不一致的问题
		// mPager.setCurrentItem(mViews.size()*100);
		setSelectedHeaderTab(mCurHeaderTab > -1 ? mCurHeaderTab : 0);
	}

	@Override
	protected void onStart() {
		super.onStart();

		MyLog.d(TAG, "on start >>>111");

		mDownloadReceiver = new DownloadStatusReceiver();
		IntentFilter downloadFilter = new IntentFilter();
		downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_COMPLETE);
		downloadFilter
				.addAction(AppService.BROADCAST_DOWNLOAD_UPDATE_FOR_DETAIL);
		downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_ERROR);

		registerReceiver(mDownloadReceiver, downloadFilter);

		MyLog.d(TAG, "on start >>>222");
	}

	@Override
	protected void onStop() {
		unregisterReceiver(mDownloadReceiver);

		super.onStop();
	}

	@Override
	protected void onDestroy() {
		super.onDestroy();
	}

	private void initView() {
		MyLog.d(TAG, "init view >>>");
		// first init header widgets
		initHeaderViews();
		// init appdetail widgets
		initAppDetailView();
		// init app_comments widgets
		initAppCommentView();
		// init app_relates widgets
		initAppRelatesView();
		// init bottom widgets
		initBottomBarView();
		// 设置按钮
		// initHeaderTabs();//无效
		initViewPager();
	}

	/** 更新软件介绍页 */
	private void RefreshView() {// 应用信息

		MyLog.d(TAG, "refresh view >>>");
		mApp = DatabaseUtils.getAppById(mDb, mAppid);

		String iconUrl = mApp.getIconUrl();
		String iconpath = DatabaseUtils.getLocalPathFromUrl(iconUrl);

		if (GeneralUtil.needDisplayImg(AppDetailActivity.this)) {
			if (iconpath != null && !"".equals(iconpath)
					&& new File(iconpath).exists()) {
				app_icon.setImageBitmap(DatabaseUtils.getImage(iconpath));
			} else {
				app_icon.setImageResource(R.drawable.app_icon);

				ImageLoader.getInstance().loadBitmapOnThread(iconUrl, this,
						app_icon);
			}
		} else
			app_icon.setImageResource(R.drawable.app_icon);

		// set name
		app_name.setText(mApp.getName());

		// 设置作者
		app_author.setText(getString(R.string.author, mApp.getAuthorName()));

		// 设置版本
		String v = mApp.getAppVersion();
		if (TextUtils.isEmpty(v)) {
			v = getString(R.string.no_version);
		}
		if (v.length() > 7) {
			v = v.substring(0, 7);
			app_version.setText(getString(R.string.versiontext, v));
		} else {
			app_version.setText(getString(R.string.versiontext, v));
		}

		// set 大小
		app_size.setText(getString(R.string.sizetext,
				GeneralUtil.getReadableSize(mApp.getSize())));

		// set 下载次数
		mDownloadCount.setText(getString(R.string.download_count,
				mApp.getDownloadCount()));

		int progress = Math.round((((float) mApp.getDownloadedSize() / mApp
				.getSize()) * 100));
		updateStatus(mApp.getStatus(), progress);

		// set 描述
		app_desc.setText(Html.fromHtml(mApp.getDescription()));
		app_desc.setMovementMethod(LinkMovementMethod.getInstance());

		// set 语言
		app_language.setText(getString(R.string.languagetext, getString((mApp
				.getLanguage() == 0) ? R.string.language_chinese
				: R.string.language_english)));
		// set 应用截图
		String ssUrl = mApp.getScreenshotUrl();
		MyLog.d(TAG, "URL:" + ssUrl);
		// String url[] = ssUrl.split(",");
		// ssUrl is like:
		// http://img.hjapk.com/1331/screenShot/1331_0.jpg;1331/screenShot/1331_1.jpg;1331/screenShot/1331_2.jpg;1331/screenShot/1331_3.jpg;1331/screenShot/1331_4.jpg
		try {
			if (ssUrl.contains(",")) {// 判断字符串中有没有逗号，如果有，把逗号转成分号
				ssUrl = ssUrl.replace(",", ";");
			}
			String url[] = ssUrl.split(";");
			Uri uri = Uri.parse(url[0]);
			String baseUrl = uri.getScheme() + "://" + uri.getAuthority() + "/";
			String[] fullPathUrl = new String[url.length];
			fullPathUrl[0] = url[0];
			for (int i = 1; i < url.length; i++) {
				fullPathUrl[i] = baseUrl + url[i];
			}
			imageLoading(fullPathUrl);
		} catch (Exception e) {// 最新捕获url无数据
			e.printStackTrace();
		}
		// imageLoading(url);
		// set 评级
		int score = mApp.getScore();
		int scoreCount = mApp.getScoreCount();
		app_rating.setRating((scoreCount == 0) ? 0 : score / scoreCount);

		// 设置多少人参与评分
		app_ratingCnt.setText(getString(R.string.rating_count,
				mApp.getScoreCount()));

		// 设置星级评分
		if (scoreCount != 0) {
			// /设置评级计数
			float scoref = (float) score;
			float scoreCountf = (float) scoreCount;
			String str_avgPoint = String.valueOf(scoref * 2 / scoreCountf);
			BigDecimal bd_avgPoint = new BigDecimal(str_avgPoint);
			bd_avgPoint = bd_avgPoint.setScale(1, BigDecimal.ROUND_HALF_UP);

			app_avgPoint.setText(getString(R.string.rating_unit, bd_avgPoint));
		} else {
			app_avgPoint.setText(getString(R.string.rating_no_score));
		}

		// set 有多少人发表评论 herokf add html format data
		app_commentCnt.setText(Html.fromHtml(getString(R.string.comment_count,
				mApp.getCommentCount())));

	}

	Handler handler = new Handler();

	private void imageLoading(String[] url) {// 应用详情中的两张图片的加载
		String sspath1;
		String sspath2;
		if (url.length > 0) {
			MyLog.d(TAG, "imageLoading >>>1111");
			sspath1 = DatabaseUtils.getLocalPathFromUrl(url[0]);

			MyLog.d(TAG, "imageLoading >>>2222");
			// 加载第一张图
			if (GeneralUtil.needDisplayImg(AppDetailActivity.this)) {
				MyLog.d(TAG, "imageLoading >>>3333");
				if (sspath1 != null && !"".equals(sspath1)
						&& new File(sspath1).exists()) {
					app_screenshot.setImageBitmap(DatabaseUtils
							.getImage(sspath1));
				} else {
					app_screenshot.setImageResource(R.drawable.default_logo);
					ImageLoader.getInstance().loadBitmapOnThread(url[0],
							AppDetailActivity.this, app_screenshot);
				}
				MyLog.d(TAG, "imageLoading >>>4444");
			} else {
				app_screenshot.setImageResource(R.drawable.default_logo);
			}
		}

		if (url.length > 1) {
			MyLog.d(TAG, "imageLoading >>>5555");
			sspath2 = DatabaseUtils.getLocalPathFromUrl(url[1]);

			MyLog.d(TAG, "imageLoading >>>6666");
			// 加载第二张图
			if (GeneralUtil.needDisplayImg(AppDetailActivity.this)) {
				MyLog.d(TAG, "imageLoading >>>7777");
				if (sspath2 != null && !"".equals(sspath2)
						&& new File(sspath2).exists()) {
					app_screenshot2.setImageBitmap(DatabaseUtils
							.getImage(sspath2));
				} else {
					app_screenshot2.setImageResource(R.drawable.default_logo);
					ImageLoader.getInstance().loadBitmapOnThread(url[1],
							AppDetailActivity.this, app_screenshot2);
				}
				MyLog.d(TAG, "imageLoading >>>8888");
			} else {
				app_screenshot2.setImageResource(R.drawable.default_logo);
			}
		}
	}

	private void updateStatus(int status, int progress) {// 更新状态
		MyLog.d(TAG, "update status " + status + " and progress " + progress);
		mApp.setStatus(status);

		// status
		String p = mApp.getPrice();
		switch (status) {
		case App.INIT:
			if ("0".equals(p)) {
				mPrice.setText(R.string.free);
			} else {
				mPrice.setText(getString(R.string.price, p));
			}
			showProgress(false);
			setButtonNumber(1);
			mBtn1.setText(R.string.bottom_button_download);
			mBtn1.setOnClickListener(mDownloadListener);

			break;
		case App.DOWNLOADING:
			mPauseBtn.setVisibility(View.VISIBLE);
			mResumeBtn.setVisibility(View.GONE);

			mPrice.setText(R.string.status_downloading);

			showProgress(true);
			setDownloadProgress(false, progress);
			break;
		case App.PAUSED:
			mPauseBtn.setVisibility(View.GONE);
			mResumeBtn.setVisibility(View.VISIBLE);

			mPrice.setText(R.string.status_paused);
			showProgress(true);
			setDownloadProgress(true, progress);

			break;
		case App.DOWNLOADED:
			mPrice.setText(R.string.status_downloaded);
			MyLog.d(TAG, "mApp.getDownloadCount()" + mApp.getDownloadCount());
			mDownloadCount.setText(getString(R.string.download_count,
					mApp.getDownloadCount()));
			showProgress(false);
			if (mCurHeaderTab == 1) {
				setButtonNumber(1);
			} else {
				setButtonNumber(2);
			}
			mBtn1.setText(R.string.bottom_button_install);
			mBtn1.setOnClickListener(mInstallListener);
			mBtn2.setText(R.string.bottom_button_delete);
			mBtn2.setOnClickListener(mDeleteListener);

			break;
		case App.INSTALLED:
			mPrice.setText(R.string.status_installed);
			showProgress(false);
			setButtonNumber(2);
			mBtn1.setText(R.string.bottom_button_launch);
			mBtn1.setOnClickListener(mLaunchListener);
			mBtn2.setText(R.string.bottom_button_uninstall);
			mBtn2.setOnClickListener(mUninstallListener);

			break;
		case App.HAS_UPDATE:
			mPrice.setText(R.string.status_hasupdate);
			showProgress(false);
			setButtonNumber(3);
			mBtn1.setText(R.string.bottom_button_update);
			mBtn1.setOnClickListener(mUpdateListener);
			mBtn2.setText(R.string.bottom_button_launch);
			mBtn2.setOnClickListener(mLaunchListener);
			mBtn3.setText(R.string.bottom_button_uninstall);
			mBtn3.setOnClickListener(mUninstallListener);

			break;
		default:
			throw new RuntimeException("Unknown status: " + mApp.getStatus());
		}
	}

	private void setDownloadProgress(boolean isPaused, int progress) {
		mProgressBar.setProgress(isPaused, progress);
	}

	private void setButtonNumber(int i) {// 按钮数目
		MyLog.d(TAG, "set button number " + i);

		switch (i) {
		case 1:

			mOneBtn.setVisibility(View.VISIBLE);
			mTwoBtn.setVisibility(View.GONE);
			mThreeBtn.setVisibility(View.GONE);

			mBtn1 = (LongButton) findViewById(R.id.btn11);
			mBtn1.setBackgroundResource(R.drawable.btn_long_selector);
			break;
		case 2:
			mOneBtn.setVisibility(View.GONE);
			mTwoBtn.setVisibility(View.VISIBLE);
			mThreeBtn.setVisibility(View.GONE);

			mBtn1 = (LongButton) findViewById(R.id.btn21);
			mBtn1.setBackgroundResource(R.drawable.btn_long_selector);
			mBtn2 = (LongButton) findViewById(R.id.btn22);
			mBtn2.setBackgroundResource(R.drawable.btn_long_selector);

			break;
		case 3:
			mOneBtn.setVisibility(View.GONE);
			mTwoBtn.setVisibility(View.GONE);
			mThreeBtn.setVisibility(View.VISIBLE);

			mBtn1 = (LongButton) findViewById(R.id.btn31);
			mBtn1.setBackgroundResource(R.drawable.btn_long_selector);
			mBtn2 = (LongButton) findViewById(R.id.btn32);
			mBtn2.setBackgroundResource(R.drawable.btn_long_selector);
			mBtn3 = (LongButton) findViewById(R.id.btn33);
			mBtn3.setBackgroundResource(R.drawable.btn_long_selector);
			break;
		default:
			// throw new RuntimeException("Unknow button number: " + i);
			MyLog.e(TAG, "Unknow button number: " + i);
		}
	}

	private void showProgress(boolean showProgress) {
		mBottomButtons.setVisibility(showProgress ? View.GONE : View.VISIBLE);
		mBottomProgress.setVisibility(showProgress ? View.VISIBLE : View.GONE);
	}

	protected void processPayment(String appid, String price) {
		Intent i = new Intent(getApplicationContext(), PayActivity.class);

		MyLog.d(TAG, "price is " + price);
		MyLog.d(TAG, "appid is " + price);

		i.putExtra("price", price);
		i.putExtra("shopId", "0");
		i.putExtra("productId", appid);

		startActivityForResult(i, REQUEST_PAY);
	}

	private boolean canClickRate = true;

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {

		if (requestCode == REQUEST_LOGIN) {
			if (resultCode == RESULT_OK) {
				try {
					new ProcessInstallTask(this, mInstallTaskResultListener)
							.execute(mApp.getId() + "", mApp.getPayId(),
									mParent);
				} catch (RejectedExecutionException e) {
					MyLog.e(TAG, "Got exception when execute asynctask!", e);
				}
			}
		} else if (requestCode == REQUEST_PAY) {
			if (resultCode == RESULT_OK) {

				try {
					new ProcessInstallTask(this, mInstallTaskResultListener)
							.execute(mApp.getId() + "",
									data.getStringExtra("payid"), mParent);
				} catch (RejectedExecutionException e) {
					MyLog.e(TAG, "Got exception when execute asynctask!", e);
				}
			} else {
				Toast.makeText(getApplicationContext(),
						R.string.error_pay_failed, Toast.LENGTH_LONG).show();
			}

		} else if (requestCode == REQUEST_COMMENT) {
			if (resultCode == RESULT_OK) {
				// refresh data
				RefreshView();
			}
			setSelectedHeaderTab(mCurHeaderTab > -1 ? mCurHeaderTab : 0);
		} else if (requestCode == REQUEST_COMMENT_GRADE) {
			canClickRate = true;

			if (resultCode == RESULT_OK) {
				// refresh data
				RefreshView();
			}
		}

		super.onActivityResult(requestCode, resultCode, data);
	}

	private class DownloadStatusReceiver extends BroadcastReceiver {

		@Override
		public void onReceive(Context context, Intent intent) {
			final String action = intent.getAction();
			MyLog.d(TAG, "DownloadStatusReceiver >>> onReceive >>> action: "
					+ action);

			final int appid = intent.getIntExtra(AppService.DOWNLOAD_APP_PID,
					-1);
			MyLog.d(TAG, "appid: " + appid + ", curid: " + mAppid);

			if (appid != mAppid) {
				MyLog.d(TAG, "appid not match, inId: " + appid + ", curId: "
						+ mAppid);
				return;
			}

			Message m = mHandler.obtainMessage(MESSAGE_UPDATE_STATUS);
			Bundle data = new Bundle();
			data.putInt("appid", appid);
			data.putString("action", action);

			int appstatus = -1;
			int progress = 0;
			if (AppService.BROADCAST_DOWNLOAD_UPDATE_FOR_DETAIL.equals(action)) {
				appstatus = App.DOWNLOADING;
				progress = intent.getIntExtra(
						AppService.DOWNLOAD_PROGRESS_VALUE, 0);
				data.putInt("progress", progress);

				MyLog.d(TAG, "update progress " + progress);
			} else if (AppService.BROADCAST_DOWNLOAD_COMPLETE.equals(action)) {
				appstatus = App.DOWNLOADED;
				mApp.setDownloadCount(mApp.getDownloadCount() + 1);
			} else if (AppService.BROADCAST_DOWNLOAD_ERROR.equals(action)) {
				appstatus = App.INIT;
			} else {
				throw new RuntimeException("got unknown action: " + action);
			}

			data.putInt("status", appstatus);
			m.setData(data);

			mHandler.sendMessage(m);
		}

	}

	// save user log
	private void saveUserLog(int action) {
		if (mCurHeaderTab == 0)

		{
			GeneralUtil.saveUserLogType3(AppDetailActivity.this, 8, action);
			// if (action==0) {
			// tracker.trackPageView("/"+TAG);
			// }
			// else {
			// tracker.trackEvent(""+3, ""+8, "", action);
			// }
		} else if (mCurHeaderTab == 1) {
			GeneralUtil.saveUserLogType3(AppDetailActivity.this, 9, action);
			// if (action==0) {
			// tracker.trackPageView("/"+TAG);
			// }
			// else {
			// tracker.trackEvent(""+3, ""+9, "", action);
			// }
		} else if (mCurHeaderTab == 2) {
			GeneralUtil.saveUserLogType3(AppDetailActivity.this, 10, action);
			// if (action==0) {
			// tracker.trackPageView("/"+TAG);
			// }
			// else {
			// tracker.trackEvent(""+3, ""+10, "", action);
			// }
		}

	}

	/******************************** 新增 *****************************************/
	/** 初始化头部的控件 */
	private void initHeaderViews() {
		app_icon = (ImageView) findViewById(R.id.icon);
		app_rating = (RatingStars) findViewById(R.id.rating_stars);
		app_rating.setStarParam(R.drawable.star_empty, R.drawable.star_fill);
		app_avgPoint = (TextView) findViewById(R.id.avg_Point);
		app_name = (TextView) findViewById(R.id.name);
		mDownloadCount = (TextView) findViewById(R.id.download_cnt);
		mPrice = (TextView) findViewById(R.id.price);
		stars_grade = (ImageView) findViewById(R.id.stars_grade);
		stars_grade.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {

				if (!canClickRate)
					return;

				canClickRate = false;

				saveUserLog(1);
				Intent i = new Intent(getApplicationContext(),
						GradeActivity.class);
				i.putExtra("appid", mApp.getId());
				// i.putExtra("flag", ConstantValues.FLAG);
				startActivityForResult(i, REQUEST_COMMENT_GRADE);

			}
		});

		mShareButton = (ImageView) findViewById(R.id.share_btn);
		mShareButton.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View arg0) {

				saveUserLog(2);

				final Intent shareIntent = new Intent(
						Intent.ACTION_SEND);
				shareIntent.setType("text/plain");

				UserInfo user = GeneralUtil.getUserInfo(AppDetailActivity.this);
				String userName = user.getName();
				if (userName == null)
					userName = getString(R.string.share_via_email_sharer);

				shareIntent.putExtra(
						Intent.EXTRA_SUBJECT,
						getString(R.string.share_via_email_subject,
								mApp.getName(), userName));
				shareIntent.putExtra(
						Intent.EXTRA_TEXT,
						getString(R.string.share_content, mApp.getName(),
								mApp.getId()));

				startActivity(shareIntent.createChooser(shareIntent,
						getString(R.string.choose_share_client)));
			}

		});
	}

	/** 初始化软件介绍页 */
	private void initAppDetailView() {
		mAppdetail = LayoutInflater.from(this).inflate(
				R.layout.app_item_detail, null);
		app_screenshot = (ImageView) mAppdetail.findViewById(R.id.screenshot);
		app_screenshot.setOnClickListener(mScreenshotOnClickListener);
		app_screenshot2 = (ImageView) mAppdetail.findViewById(R.id.screenshot2);
		app_screenshot2.setOnClickListener(mScreenshotOnClickListener);
		app_author = (TextView) mAppdetail.findViewById(R.id.author);
		app_version = (TextView) mAppdetail.findViewById(R.id.version);
		app_size = (TextView) mAppdetail.findViewById(R.id.size);
		app_desc = (TextView) mAppdetail.findViewById(R.id.desc);

		app_language = (TextView) mAppdetail.findViewById(R.id.language);
		app_ratingCnt = (TextView) mAppdetail.findViewById(R.id.rating_cnt);
		app_commentCnt = (TextView) mAppdetail.findViewById(R.id.comment_cnt);
		app_commentCnt.setMovementMethod(LinkMovementMethod.getInstance());
		mViews.add(mAppdetail);
	}

	/** 初始化软件评论页 */
	private void initAppCommentView() {
		// 设置第二页的查看评论view
		mListAdatper = new CommentListAdapter(AppDetailActivity.this);
		mAppcomment = LayoutInflater.from(this).inflate(
				R.layout.app_item_comment_list, null);
		mLoadingBackground = mAppcomment.findViewById(R.id.loading_bg);
		mLoadingBackground.setVisibility(View.GONE);
		mBgText = (TextView) mAppcomment.findViewById(R.id.no_comments);
		mBgText.setVisibility(View.GONE);
		mContentList = (ListView) mAppcomment.findViewById(R.id.comments);
		mContentList.setVisibility(View.VISIBLE);
		mContentList.setAdapter(mListAdatper);
		mContentList.setSelector(R.drawable.c5);
		mViews.add(mAppcomment);
	}

	/** 初始化软件相关页 */
	private void initAppRelatesView() {
		// 设置第三页的相关软件view
		mAdapter = new AppListAdapter(AppDetailActivity.this);
		// 设置第三页的view
		mRelatedapp = LayoutInflater.from(this).inflate(R.layout.lay1, null);
		mRelativeAppLoadingBg = (View) mRelatedapp
				.findViewById(R.id.loading_bg);
		GridView gridview = (GridView) mRelatedapp
				.findViewById(R.id.contentList1);
		gridview.setAdapter(mAdapter);
		gridview.setSelector(R.drawable.c5);
		gridview.setNumColumns(2);
		gridview.setBackgroundResource(R.drawable.gridview_bg);
		gridview.setSelection(0);
		gridview.setOnItemClickListener(mListItemClickListener);
		mViews.add(mRelatedapp);
	}

	/** 初始化底部按钮 */
	private void initBottomBarView() {
		/******************** 以下的相关界面中显示的按钮暂不处理 因为各种view中都会有 但是必须将相关的button添加到自己的view中去 ************/
		mBottomBar = findViewById(R.id.bottombar);
		mBottomButtons = findViewById(R.id.bottombar_buttons);
		mBottomProgress = findViewById(R.id.bottombar_progress);

		mPauseBtn = findViewById(R.id.pause_btn);
		mPauseBtn.setOnClickListener(mPauseProgressListener);

		mResumeBtn = findViewById(R.id.resume_btn);
		mResumeBtn.setOnClickListener(mResumeProgressListener);

		mStopBtn = findViewById(R.id.stop_btn);
		mStopBtn.setOnClickListener(mStopProgressListener);

		mProgressBar = (FancyProgressBar) findViewById(R.id.progress);

		mOneBtn = (View) findViewById(R.id.one_button);
		mTwoBtn = (View) findViewById(R.id.two_button);
		mThreeBtn = (View) findViewById(R.id.three_button);
		/*****************************************************/
	}

	/** 存放各页的view */
	private List<View> mViews = new ArrayList<View>();
	private PageIndicator mIndicator;
	private ViewPager mPager;// 页卡内容
	private MainViewPagerAdapter viewPagerAdapter;
	private final String[] TITLE_CONTENTS = { "软件介绍", "查看评论", "相关软件" };// 页眉标题

	/** 初始化ViewPager */
	private void initViewPager() {
		mPager = (ViewPager) findViewById(R.id.vPager);
		viewPagerAdapter = new MainViewPagerAdapter(mViews, TITLE_CONTENTS);
		mPager.setOffscreenPageLimit(2);
		mPager.setAdapter(viewPagerAdapter);

		mIndicator = (TitlePageIndicator) findViewById(R.id.indicator);
		mIndicator.setViewPager(mPager);
		mIndicator.setOnPageChangeListener(new MyOnPageChangeListener());
		mIndicator.setCurrentItem(0);
	}

	/** 页卡切换监听 */
	public class MyOnPageChangeListener implements OnPageChangeListener {
		int current = 0;

		@Override
		public void onPageSelected(int arg0) {
			// Animation animation = null;
			MyLog.e("arg0", "----" + arg0);
			current = arg0;
		}

		@Override
		public void onPageScrolled(int arg0, float arg1, int arg2) {
		}

		@Override
		public void onPageScrollStateChanged(int arg0) {
			if (arg0 == 0)
				setSelectedHeaderTab(current % mViews.size());
		}
	}

}
