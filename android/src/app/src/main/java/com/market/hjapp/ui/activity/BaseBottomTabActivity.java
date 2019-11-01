package com.market.hjapp.ui.activity;

import java.io.File;
import java.io.FileOutputStream;
import java.io.IOException;
import java.io.InputStream;
import java.net.HttpURLConnection;
import java.net.MalformedURLException;
import java.net.URL;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import org.apache.http.client.ClientProtocolException;
import org.json.JSONException;

import android.app.Dialog;
import android.app.ProgressDialog;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.content.pm.PackageManager.NameNotFoundException;
import android.net.Uri;
import android.os.AsyncTask;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.view.KeyEvent;
import android.view.Menu;
import android.view.MenuItem;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.Window;
import android.widget.Toast;

import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.tyxl.object.CustomDialog;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.tasks.LogoutTask;

public abstract class BaseBottomTabActivity extends BaseActivity {

	public static final String TAG = "BaseBottomTabActivity";
	BaseBottomTabActivity bt;
	View mFirstFooterTab;
	View mSecondFooterTab;
	View mThirdFooterTab;
	View mFourthFooterTab;
	View mFifthFooterTab;
	int mCurFooterTab;

	private static final int MENU_UPDATE = 109;
	private static final int MENU_MANAGER = 110;// 新加入 管理
	private static final int MENU_ABOUT = 101;
	private static final int MENU_SETTING = 102;
	private static final int MENU_SUGGESTION = 103;// 意见反馈
	private static final int MENU_EXIT = 104;

	private static final int MENU_LOGOUT = 105;// 注销
	private static final int MENU_MODIFY_PWD = 106;// 修改密码

	private static final int MENU_ACCOUNT = 107;// 个人中心
	private static final int MENU_RECOMMEND = 108;// 今日推荐

	private static final int REQUEST_LOGIN = 100;// 请求登录

	private static final int DIALOG_RUN_DUOBAO = 200;// 运行夺宝
	private static final int DIALOG_DOWNLOAD_DUOBAO = 201;// 下载夺宝
	private static final int DIALOG_INSTALL_DUOBAO = 202;// 安装夺宝

	/** 下载的夺宝app */
	private File duobaoapkFile = null;

	/** 顶部返回夺宝的按钮 */
	public OnClickListener backBtnOnClickListener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			onClickBackBtn();
		}
	};

	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		// Hide the title bar
		requestWindowFeature(Window.FEATURE_NO_TITLE);

		setContentView(getLayout());

		initViews();
	}

	@Override
	public boolean onCreateOptionsMenu(Menu menu) {// 点击菜单键后的选项

		// user info page
		if (mCurFooterTab == 5 && ConstantValues.HAVE_USER_PAY_FUNTION
				&& GeneralUtil.getHasLoggedIn(BaseBottomTabActivity.this)) {
			menu.add(0, MENU_MODIFY_PWD, 0, R.string.menu_modify_pwd).setIcon(
					android.R.drawable.ic_menu_manage);

			menu.add(0, MENU_LOGOUT, 0, R.string.menu_logout).setIcon(
					android.R.drawable.ic_menu_revert);

		} else {
			menu.add(0, MENU_ACCOUNT, 0, R.string.menu_account).setIcon(
					android.R.drawable.ic_menu_myplaces);
		}

		menu.add(0, MENU_RECOMMEND, 0, R.string.menu_recommend).setIcon(
				android.R.drawable.ic_menu_today);

		menu.add(0, MENU_SETTING, 0, R.string.menu_setting).setIcon(
				android.R.drawable.ic_menu_preferences);
		// Check Update
		menu.add(0, MENU_UPDATE, 0, R.string.menu_update).setIcon(
				android.R.drawable.ic_menu_rotate);

		menu.add(0, MENU_MANAGER, 0, R.string.menu_manager).setIcon(
				android.R.drawable.ic_menu_slideshow);
		// About
		menu.add(0, MENU_ABOUT, 0, R.string.menu_help).setIcon(
				android.R.drawable.ic_menu_info_details);

		menu.add(0, MENU_SUGGESTION, 0, R.string.menu_suggestion).setIcon(
				android.R.drawable.ic_menu_edit);

		menu.add(0, MENU_EXIT, 0, R.string.menu_exit).setIcon(
				android.R.drawable.ic_lock_power_off);

		return super.onCreateOptionsMenu(menu);
	}

	@Override 
	public boolean onOptionsItemSelected(MenuItem item) {// 菜单键中的按键处理
		Intent i;
		switch (item.getItemId()) {

			case MENU_ABOUT :// 关于
				i = new Intent(BaseBottomTabActivity.this, AboutActivity.class);
				startActivity(i);
				break;

			case MENU_UPDATE :// 检查升级

				hasNewVersion(BaseBottomTabActivity.this);

				break;

			case MENU_SETTING :// 设置
				i = new Intent(BaseBottomTabActivity.this,
						SettingActivity.class);

				startActivity(i);
				break;

			case MENU_SUGGESTION :// 意见反馈
				i = new Intent(BaseBottomTabActivity.this,
						SuggestionActivity.class);

				startActivity(i);
				break;

			case MENU_EXIT :// 退出
				GeneralUtil.showQuitConfirmDialog(this);
				break;

			case MENU_LOGOUT :// 注销
				try {
					// save user log
					GeneralUtil.saveUserLogType3(this, 25, 0);

					new LogoutTask(BaseBottomTabActivity.this,
							mLogoutResultListener).execute();
				} catch (RejectedExecutionException e) {
					MyLog.e("", "Got exception when execute asynctask!", e);
				}
				break;

			case MENU_MODIFY_PWD :// 修改密码
				i = new Intent(BaseBottomTabActivity.this,
						ChangePasswordDialogActivity.class);

				startActivity(i);
				break;

			case MENU_MANAGER :
				i = new Intent(BaseBottomTabActivity.this,
						BrowseManageListActivity.class);// 原版功能
				// i = new Intent(BaseBottomTabActivity.this,
				// BrowseSuggestedAppListActivityal.class);//切换夺宝界面
				startActivity(i);
				break;

			case MENU_ACCOUNT :// 个人中心

				if (mCurFooterTab != 6) {
					if (GeneralUtil.getHasLoggedIn(BaseBottomTabActivity.this)) {// 已登录
						if (ConstantValues.HAVE_USER_PAY_FUNTION) {// 有支付功能
							startActivity(MyAccountActivity.class);
						} else {
							i = new Intent(BaseBottomTabActivity.this,
									MyAccountDialogActivity.class);
							startActivity(i);

						}

					} else {// 未登录
						if (ConstantValues.HAVE_USER_PAY_FUNTION) {// 有支付功能
							startActivity(LoginAccountActivity.class);
						} else {
							i = new Intent(BaseBottomTabActivity.this,
									LoginDialogActivity.class);
							i.putExtra("hint",
									getString(R.string.login_hint_welcome));
							startActivityForResult(i, REQUEST_LOGIN);
						}
					}
				}

				break;
			case MENU_RECOMMEND :// 今日推荐
//				i = new Intent(BaseBottomTabActivity.this,
//						RecommendActivity.class);
				startActivity(RecommendActivity.class);
				break;
		}

		return super.onOptionsItemSelected(item);
	}

	static final int MESSAGE_CHECK_HAS_NEW_VERSION = 1001;
	static final int MESSAGE_EXCEPTION_HAS_NEW_VERSION = 1002;
	private Thread updateThread = null;
	private Handler mHandler = new Handler() {
		@Override
		public void handleMessage(Message msg) {
			int what = msg.what;
			if (what == MESSAGE_CHECK_HAS_NEW_VERSION) {
				Toast.makeText(getApplicationContext(),
						getString(R.string.user_info_casino_result),
						Toast.LENGTH_LONG).show();
				return;
			} else if (what == MESSAGE_EXCEPTION_HAS_NEW_VERSION) {
				Toast.makeText(getApplicationContext(),
						getString(R.string.error_http_timeout),
						Toast.LENGTH_LONG).show();
				return;
			}
			super.handleMessage(msg);
		}
	};

	/**
	 * check whether need to update self
	 */
	private void hasNewVersion(final Context ctx) {
		MyLog.d(TAG, "check update >>>");
		if (updateThread != null && !updateThread.isInterrupted()) {
			updateThread.interrupt();
			updateThread = null;
		}

		if (updateThread == null) {
			final ProgressDialog progressDialog = ProgressDialog.show(ctx,
					null, getText(R.string.processing_get_new_version), true);
			updateThread = new Thread(new Runnable() {
				@Override
				public void run() {
					try {
						HashMap<String, Object> res = GeneralUtil
								.hasNewVersion(ctx);
						if (res != null) {
							// need to update self
							MyLog.d(TAG,
									"--------------- HAVE NEW VERSION! --------- ");
							int appid = (Integer) res.get("version");

							Intent updateIntent = new Intent(
									getApplicationContext(),
									UpdateActivity.class);

							updateIntent
									.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
							updateIntent.putExtra("version",
									(String) res.get("app_version"));
							updateIntent.putExtra("changelog",
									(String) res.get("changelog"));
							updateIntent.putExtra("appid", appid);
							updateIntent.putExtra("url",
									(String) res.get("url"));

							startActivity(updateIntent);
						} else {
							// not need to update self
							MyLog.d(TAG,
									"--------------- NO HAVE NEW VERSION! --------- ");
							Message m = mHandler
									.obtainMessage(MESSAGE_CHECK_HAS_NEW_VERSION);
							mHandler.sendMessage(m);
						}

						if (progressDialog != null) {
							progressDialog.dismiss();
						}

					} catch (Exception e) {
						MyLog.e(TAG, "get exception>>>" + e.toString());
						MyLog.d(TAG,
								"--------------- GET EXCEPTION! --------- ");
						Message m = mHandler
								.obtainMessage(MESSAGE_EXCEPTION_HAS_NEW_VERSION);
						mHandler.sendMessage(m);
						if (progressDialog != null) {
							progressDialog.dismiss();
						}
					}
				}

			});
			updateThread.start();
		}
	}

	TaskResultListener mLogoutResultListener = new TaskResultListener() {
		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {
			if (success) {
				// save anonymous uid/sid
				GeneralUtil.saveLoginInfo(BaseBottomTabActivity.this, null,
						(String) res.get("sid"));

				Intent i = new Intent(BaseBottomTabActivity.this,
						LoginAccountActivity.class);

				startActivity(i);

				GeneralUtil.saveLoggedOut(BaseBottomTabActivity.this);

				finish();
			} else {
				if (res == null) {
					Toast.makeText(BaseBottomTabActivity.this,
							R.string.error_http_timeout, Toast.LENGTH_LONG)
							.show();
				}
			}
		}

	};

	private void initViews() {
		mFirstFooterTab = findViewById(R.id.first_footertab);
		mFirstFooterTab.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {// 推荐
				if (mCurFooterTab != 0)
					startActivity(BrowseSuggestedAppListActivity.class);

			}
			
		});

		mSecondFooterTab = findViewById(R.id.second_footertab);
		mSecondFooterTab.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View v) {// 排行
				if (mCurFooterTab != 1)
					startActivity(BrowseRankListActivity.class);
			}

		});

		mThirdFooterTab = findViewById(R.id.third_footertab);
		mThirdFooterTab.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View arg0) {// 分类
				MyLog.d(TAG, "mCurFooterTab " + mCurFooterTab);
				if (mCurFooterTab != 2)
					startActivity(BrowseCategoryListActivity.class);
			}

		});

		mFourthFooterTab = findViewById(R.id.fourth_footertab);
		mFourthFooterTab.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View arg0) {// 搜索
				if (mCurFooterTab != 3)
					startActivity(SearchedAppListActivity.class);
			}

		});

		mFifthFooterTab = findViewById(R.id.fifth_footertab);
		mFifthFooterTab.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View arg0) {// 管理
				if (mCurFooterTab != 4) {
					startActivity(BrowseManageListActivity.class);
					// Intent i = new Intent(BaseBottomTabActivity.this,
					// AdP.class);// ADP
					// Intent i = new Intent(BaseBottomTabActivity.this,
					// BrowseManageListActivity.class);//原版功能
					// Intent i = new Intent(BaseBottomTabActivity.this,
					// BrowseSuggestedAppListActivityal.class);//切换夺宝界面
					// Intent i = new Intent(BaseBottomTabActivity.this,
					// MyDownloadsActivity.class);//直接进入下载管理界面
					// i.putExtra("goto_hasupdate_cate", false);
					// startActivity(i);
					// finish();
				}
				// if (mCurFooterTab != 4) {
				//
				// if (GeneralUtil.getHasLoggedIn(BaseBottomTabActivity.this)) {
				// if (ConstantValues.HAVE_USER_PAY_FUNTION)
				// startActivity(MyAccountActivity.class);
				// else
				// {
				// Intent i = new Intent(BaseBottomTabActivity.this,
				// MyAccountDialogActivity.class);
				// startActivity(i);
				// }
				//
				// } else {
				// if (ConstantValues.HAVE_USER_PAY_FUNTION)
				// {
				// startActivity(LoginAccountActivity.class);
				// }
				// else
				// {
				// Intent i = new Intent(BaseBottomTabActivity.this,
				// LoginDialogActivity.class);
				// i.putExtra("hint", getString(R.string.login_hint_welcome));
				// startActivityForResult(i, REQUEST_LOGIN);
				// }
				// }
				// }
			}

		});
	}

	private boolean mFinishing=false;
	protected void startActivity(Class<?> clazz) {
		if(mFinishing)
			return;
		Intent i = new Intent(this, clazz);
		startActivity(i);
		mFinishing=true;
		finish();
	}

	protected void setSelectedFooterTab(int i) {
		mCurFooterTab = i;

		mFirstFooterTab.setSelected(i == 0);
		mSecondFooterTab.setSelected(i == 1);
		mThirdFooterTab.setSelected(i == 2);
		mFourthFooterTab.setSelected(i == 3);
		mFifthFooterTab.setSelected(i == 4);
	}

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			GeneralUtil.showQuitConfirmDialog(this);
			return true;
		}

		return super.onKeyDown(keyCode, event);
	}

	protected abstract int getLayout();

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data) {

		if (requestCode == REQUEST_LOGIN) {
			if (resultCode == RESULT_OK) {
				Intent i = new Intent(BaseBottomTabActivity.this,
						MyAccountDialogActivity.class);
				startActivity(i);
			}
		}

		super.onActivityResult(requestCode, resultCode, data);
	}

	private void onClickBackBtn() {
		boolean isDBInstalled = this.checkApkExist(this, "com.duobao.hjapp");
		if (isDBInstalled) {
			showDialog(DIALOG_RUN_DUOBAO);
		} else {
			showDialog(DIALOG_DOWNLOAD_DUOBAO);
		}
	}

	/**
	 * 判断某个apk包是否存在
	 * 
	 * @param context
	 * @param packageName
	 * @return
	 */
	private boolean checkApkExist(Context context, String packageName) {
		if (packageName == null || "".equals(packageName)) {
			return false;
		}
		try {
			context.getPackageManager().getApplicationInfo(packageName,
					PackageManager.GET_UNINSTALLED_PACKAGES);
			return true;
		} catch (NameNotFoundException e) {
			return false;
		}
	}

	protected Dialog onCreateDialog(int id) {
		Dialog dialog = null;
		switch (id) {
			case DIALOG_DOWNLOAD_DUOBAO :// 是否下载欢聚宝
				CustomDialog.Builder downloadhjBuilder = new CustomDialog.Builder(
						this, R.layout.alertdialog);
				downloadhjBuilder
						.setTitle("返回夺宝")
						.setMessage("还未安装夺宝，立即下载安装？")
						.setNegativeButton(
								getResources().getString(R.string.cancel),
								new DialogInterface.OnClickListener() {
									public void onClick(DialogInterface dialog,
											int which) {
										dialog.dismiss();
										return;
									}
								})
						.setPositiveButton(
								getResources().getString(R.string.ok),
								new DialogInterface.OnClickListener() {
									public void onClick(DialogInterface dialog,
											int which) {
										// 下载安装hjapp
										dialog.dismiss();
										try {
											
											String url = GeneralUtil
													.getDuoBaoUrl(BaseBottomTabActivity.this);
											
											new DuoBaoApkDownTask().execute(url);
										} catch (ClientProtocolException e) {
											e.printStackTrace();
										} catch (IOException e) {
											e.printStackTrace();
										} catch (JSONException e) {
											e.printStackTrace();
										}
									}
								});
				dialog = downloadhjBuilder.create();
				break;
			case DIALOG_RUN_DUOBAO :// 是否运行欢聚宝
				CustomDialog.Builder runhjBuilder = new CustomDialog.Builder(
						this, R.layout.alertdialog);
				runhjBuilder
						.setTitle("我要夺宝")
						.setMessage("跳转到欢聚夺宝？")
						.setNegativeButton(
								getResources().getString(R.string.cancel),
								new DialogInterface.OnClickListener() {
									public void onClick(DialogInterface dialog,
											int which) {
										dialog.dismiss();
										return;
									}
								})
						.setPositiveButton(
								getResources().getString(R.string.ok),
								new DialogInterface.OnClickListener() {
									public void onClick(DialogInterface dialog,
											int which) {
										// 下载安装hjapp
										dialog.dismiss();
										Intent intent = new Intent();
										intent.setComponent(new ComponentName(
												"com.duobao.hjapp",
												"com.duobao.hjapp.SplashActivity"));
										startActivity(intent);
										BaseBottomTabActivity.this.finish();
									}
								});
				dialog = runhjBuilder.create();
				break;
			case DIALOG_INSTALL_DUOBAO :// 下载完安装欢聚宝
				CustomDialog.Builder installhjBuilder = new CustomDialog.Builder(
						this, R.layout.alertdialog);
				installhjBuilder
						.setTitle("下载完成")
						.setMessage("夺宝下载完成，是否安装？")
						.setNegativeButton(
								getResources().getString(R.string.cancel),
								new DialogInterface.OnClickListener() {
									public void onClick(DialogInterface dialog,
											int which) {
										dialog.dismiss();
										return;
									}
								})
						.setPositiveButton(
								getResources().getString(R.string.ok),
								new DialogInterface.OnClickListener() {
									public void onClick(DialogInterface dialog,
											int which) {
										// 安装hjapp
										dialog.dismiss();
										Intent intent = new Intent();
										intent.addFlags(Intent.FLAG_ACTIVITY_NEW_TASK);
										intent.setAction(Intent.ACTION_VIEW);
										intent.setDataAndType(
												Uri.fromFile(duobaoapkFile),
												"application/vnd.android.package-archive");
										startActivity(intent);
									}
								});
				dialog = installhjBuilder.create();
				break;
		}
		return dialog;
	}

	/**
	 * 下载文件
	 * 
	 * @param httpUrl
	 *            下载地址
	 * @return
	 */
	public File downLoadFile(String httpUrl, String fileName) {
		File tmpFile = new File("//sdcard");
		if (!tmpFile.exists()) {
			tmpFile.mkdir();
		}
		final File file = new File("//sdcard//" + fileName);
		if (file.exists()) {
			file.deleteOnExit();
		}
		try {
			MyLog.e("夺宝下载URL", " "+httpUrl);
			URL url = new URL(httpUrl);
			try {
				HttpURLConnection conn = (HttpURLConnection) url
						.openConnection();
				InputStream is = conn.getInputStream();
				FileOutputStream fos = new FileOutputStream(file);
				byte[] buf = new byte[256];
				conn.connect();
				double count = 0;
				if (conn.getResponseCode() >= 400) {
					return null;
				} else {
					while (count <= 100) {
						if (is != null) {
							int numRead = is.read(buf);
							if (numRead <= 0) {
								break;
							} else {
								fos.write(buf, 0, numRead);
							}
						} else {
							break;
						}
					}
				}
				conn.disconnect();
				fos.close();
				is.close();
				MyLog.e("KF", "file download over.");
			} catch (IOException e) {
				e.printStackTrace();
				MyLog.e("KF", "IOException: " + e.getMessage());
				return null;
			}
		} catch (MalformedURLException e) {
			e.printStackTrace();
			MyLog.e("KF", "MalformedURLException: " + e.getMessage());
			return null;
		}
		return file;
	}
	/** 标题中下载hjapp任务 */
	private class DuoBaoApkDownTask extends AsyncTask<String, Integer, String> {
		boolean hasSDCard = false;

		@Override
		protected String doInBackground(String... params) {
				// 判断sdcard的存在
				if (android.os.Environment.MEDIA_MOUNTED
						.equals(android.os.Environment
								.getExternalStorageState())) {
					hasSDCard = true;
					duobaoapkFile = downLoadFile(params[0], "duoBaoApp_temp.apk");
				} else {
					hasSDCard = false;
				}
			return null;
		}

		@Override
		protected void onPreExecute() {
			super.onPreExecute();
			Toast.makeText(BaseBottomTabActivity.this, "下载进行中，请稍候...",
					Toast.LENGTH_LONG).show();
		}

		@Override
		protected void onPostExecute(String result) {
			super.onPostExecute(result);
			try {
				if (!hasSDCard) {
					Toast.makeText(BaseBottomTabActivity.this, "请插入存储卡！",
							Toast.LENGTH_LONG).show();
					return;
				}
				if (null != duobaoapkFile) {
					showDialog(DIALOG_INSTALL_DUOBAO);
				} else {
					Toast.makeText(BaseBottomTabActivity.this, "下载文件失败，请重试！",
							Toast.LENGTH_LONG).show();
				}
			} catch (Exception e) {
				MyLog.v("E-HJApkDownTask------", e.getMessage());
			}
		}
	}
}
