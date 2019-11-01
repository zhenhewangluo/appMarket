
package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.support.v4.view.ViewPager;
import android.support.v4.view.ViewPager.OnPageChangeListener;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.AbsListView;
import android.widget.AbsListView.OnScrollListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.GridView;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.App;
import com.market.hjapp.AppTabSpec;
import com.market.hjapp.AppTabSpec.AppLoadResultListener;
import com.market.hjapp.Category;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.ui.adapter.AppListAdapter;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.tasks.UpdateDownloadDynamicTask;
import com.viewpagerindicator.PageIndicator;
import com.viewpagerindicator.TabPageIndicator;
import com.viewpagerindicator.TitlePageIndicator;

public class BrowseSuggestedAppListActivity extends
BaseBottomTabActivity implements OnScrollListener
{
	private static final String TAG = "BrowseSuggestedAppListActivity";
	HashMap<Integer, AppTabSpec> mTabSpecMap;
	AppTabSpec spec;
	int mCurHeaderTab = -1;
	int mCateId = -1;
	TextView mDownDesc;
	View mDown_Desc;
	int mDownloadStatusTabID = -1; // if the cateid is 9994
	View loading;
	

	protected static final int MESSAGE_LOAD_MORE_APPS = 101;

	ArrayList<Category> mSuggestedCate;
	public static final int MESSAGE_UPDATE_DOWNLOAD_DYNAMIC = 1000;
	public static boolean canUpdateDownloadDynamic = false;

	OnItemClickListener mListItemClickListener = new OnItemClickListener()
	{

		@Override
		public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
				long arg3)
		{

			// saveUserLog(2);
			// mDown_Desc.setVisibility(View.GONE);
			// if (arg2 == 0) {
			// Intent i = new Intent(BrowseSuggestedAppListActivity.this,
			// AdP.class);//ADP
			// startActivity(i);
			// finish();
			// // startActivity(夺宝);
			// return;
			// }

			MyLog.e("---------", "--------" + arg2);

			// int resid;
			// switch (mCurHeaderTab) {
			// case 0:
			// resid = R.string.tabtitle_suggested_new;
			// break;
			// case 1:
			// resid = R.string.tabtitle_suggested_hot;
			// break;
			// case 2:
			// resid = R.string.tabtitle_suggested_others_download;
			// break;
			// default:
			// throw new RuntimeException("Invalid cur tab: " + mCurHeaderTab);
			// }
			// i.putExtra(EXTRA_KEY_PARENTNAME, getString(resid));
			// startActivityForResult(i,
			// AppDetailActivity.REQUEST_SHOW_DETAILS);
			String parentName;
			switch (mCurHeaderTab)
			{
			case 0:
				App app1 = (App) mListAdapter1.getItem(arg2);
				Intent i1 = new Intent(getApplicationContext(),
						AppDetailActivity.class);
				i1.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app1.getId());
				mSelectAppId1 = app1.getId();
				parentName = mSuggestedCate.get(0).getName();
				i1.putExtra(EXTRA_KEY_PARENTNAME, parentName);
				startActivityForResult(i1,
						AppDetailActivity.REQUEST_SHOW_DETAILS);
				break;
			case 1:
				App app2 = (App) mListAdapter2.getItem(arg2);
				Intent i2 = new Intent(getApplicationContext(),
						AppDetailActivity.class);
				i2.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app2.getId());
				mSelectAppId2 = app2.getId();
				parentName = mSuggestedCate.get(1).getName();
				i2.putExtra(EXTRA_KEY_PARENTNAME, parentName);
				startActivityForResult(i2,
						AppDetailActivity.REQUEST_SHOW_DETAILS);
				break;
			case 2:
				App app3 = (App) mListAdapter3.getItem(arg2);
				Intent i3 = new Intent(getApplicationContext(),
						AppDetailActivity.class);
				i3.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app3.getId());
				mSelectAppId3 = app3.getId();
				parentName = mSuggestedCate.get(2).getName();
				i3.putExtra(EXTRA_KEY_PARENTNAME, parentName);
				startActivityForResult(i3,
						AppDetailActivity.REQUEST_SHOW_DETAILS);
				break;
			default:
				throw new RuntimeException("Invalid cur tab: " + mCurHeaderTab);
			}
		}
	};

	private DownloadStatusReceiver mDownloadReceiver;

	private ImageLoadReceiver mImageLoadReceiver;

	private PackageProcessingReceiver packageStateReceiver;

	private View mFooterView;

	/**
	 * handler network is not available
	 * 
	 * @param mCurHeaderTab
	 */
	private void doNetworkInterrupt(int mCurHeaderTab)
	{
		MyLog.i(TAG, "++++++++network is not available!++++++");
		Toast.makeText(getApplicationContext(),
				getString(R.string.error_http_timeout), Toast.LENGTH_LONG)
				.show();
		AppTabSpec spec = mTabSpecMap.get(mCurHeaderTab);
		spec.reSetTotalPage(getApplicationContext());
		// int footerViewsCount = mContentList.getFooterViewsCount();
		// if (footerViewsCount > 0) {
		// for (int i = 0; i < footerViewsCount; i++) {
		// mContentList.removeFooterView(mFooterView);
		// }
		// }
	}

	private AppLoadResultListener mAppLoadResultListener0 = new AppLoadResultListener()
	{

		@Override
		public void onAppLoadResult(ArrayList<App> data)
		{

			if (data == null)
			{
				doNetworkInterrupt(0);
			} else
			{
				if (mCurHeaderTab == 0)
				{
					setContentListVisibility(true, 0);
				}
			}
		}

	};

	private AppLoadResultListener mAppLoadResultListener1 = new AppLoadResultListener()
	{

		@Override
		public void onAppLoadResult(ArrayList<App> data)
		{

			if (data == null)
			{
				doNetworkInterrupt(1);
			} else
			{
				if (mCurHeaderTab == 1)
				{
					setContentListVisibility(true, 1);
				}
			}
		}

	};
	private AppLoadResultListener mAppLoadResultListener2 = new AppLoadResultListener()
	{

		@Override
		public void onAppLoadResult(ArrayList<App> data)
		{
			if (data == null)
			{
				doNetworkInterrupt(2);
			} else
			{
				if (mCurHeaderTab == 2)
				{
					setContentListVisibility(true, 2);
				}

			}
		}
	};

	@Override
	public void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);
		mSuggestedCate = DatabaseUtils.getCategoryByType(mDb,
				Category.CATE_TYPE_MAIN);
		int length = mSuggestedCate.size();
		MyLog.d(TAG, "mSuggestedCate list length=" + length);
		for (int i = 0; i < length; i++)
		{
			MyLog.d(TAG, "cateid=" + mSuggestedCate.get(i).getSig());
			if (mSuggestedCate.get(i).getSig() == ConstantValues.DOWNLOAD_CATE_ID)
			{
				mDownloadStatusTabID = i;
			}
		}
		// init mCateId
		mCateId = mSuggestedCate.get(0).getSig();
		// initHeaderTabs();

		MyLog.d(TAG, "******************************");
		MyLog.d(TAG, GeneralUtil.getScreenSize(this));
		MyLog.d(TAG, "******************************");

		Button button = (Button)findViewById(R.id.btn_right);
		button.setVisibility(View.VISIBLE);
		button.setOnClickListener(this.backBtnOnClickListener);
		
		
		mFooterView = LayoutInflater.from(this).inflate(
				R.layout.loading_footer_view, null);
		mFooterView.setClickable(false);

		mTabSpecMap = new HashMap<Integer, AppTabSpec>();
		mTabSpecMap.put(0, new AppTabSpec(BrowseSuggestedAppListActivity.this,
				mSuggestedCate.get(0).getSig(), Category.CATE_TYPE_MAIN, 1,
				mDb, mAppLoadResultListener0));
		mTabSpecMap.put(1, new AppTabSpec(BrowseSuggestedAppListActivity.this,
				mSuggestedCate.get(1).getSig(), Category.CATE_TYPE_MAIN, 1,
				mDb, mAppLoadResultListener1));
		mTabSpecMap.put(2, new AppTabSpec(BrowseSuggestedAppListActivity.this,
				mSuggestedCate.get(2).getSig(), Category.CATE_TYPE_MAIN, 1,
				mDb, mAppLoadResultListener2));
		// mContentList.addFooterView(mFooterView, null, false);
		mListAdapter1 = new AppListAdapter(BrowseSuggestedAppListActivity.this);// 第一页view的Adapter
		mListAdapter2 = new AppListAdapter(BrowseSuggestedAppListActivity.this);// 第二页view的Adapter
		mListAdapter3 = new AppListAdapter(BrowseSuggestedAppListActivity.this);// 第三页view的Adapter
		// 先初始化Fragments然后在初始化ViewPagerIndicator
		initViews();
		initViewPager();
		setSelectedFooterTab(0);

		// into cateId=9995 SelectedHeaderTab
		Bundle bunde = this.getIntent().getExtras();
		if (bunde != null)
		{
			int lead = bunde.getInt("suggested_software");
			MyLog.d(TAG, "suggested_software=" + lead);
			if (lead == ConstantValues.SUGGESTED_CATE_ID)
			{
				int suggested_software_headerTab = -1;
				for (int i = 0; i < length; i++)
				{
					if (mSuggestedCate.get(i).getSig() == ConstantValues.SUGGESTED_CATE_ID)
					{
						suggested_software_headerTab = i;
					}
				}

				setSelectedHeaderTab(suggested_software_headerTab);
			} else
			{
				setSelectedHeaderTab(0);
			}
		} else
		{
			setSelectedHeaderTab(0);
		}

	}

	private Handler updateHandler = new Handler()
	{
		public void handleMessage(Message msg)
		{
			try
			{
				MyLog.d(TAG, "===> handleMessage:" + canUpdateDownloadDynamic);

				if (msg.what == MESSAGE_UPDATE_DOWNLOAD_DYNAMIC)
				{
					if (!canUpdateDownloadDynamic)
						return;

					String LastUpdateTime = GeneralUtil
							.getTextViewUpdateTime(BrowseSuggestedAppListActivity.this);
					if (LastUpdateTime == null || "".equals(LastUpdateTime))
					{
						Message message = updateHandler
								.obtainMessage(MESSAGE_UPDATE_DOWNLOAD_DYNAMIC);
						updateHandler.sendMessageDelayed(message, 10 * 1000);
						return;
					}

					MyLog.d(TAG, "===> update downlaod dynamic:"
							+ LastUpdateTime);
					new UpdateDownloadDynamicTask(
							BrowseSuggestedAppListActivity.this,
							UpdateDownloadDynamicListener)
					.execute(LastUpdateTime);

				}
			} catch (Exception e)
			{
				MyLog.e(TAG, e.toString());
			}
			super.handleMessage(msg);
		}
	};

	private int lastCount = -1;

	private TaskResultListener UpdateDownloadDynamicListener = new TaskResultListener()
	{

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res)
		{

			if (!success)
			{
				return;
			} else
			{
				if (!canUpdateDownloadDynamic)
					return;

				if (res == null)
				{
					Message message = updateHandler
							.obtainMessage(MESSAGE_UPDATE_DOWNLOAD_DYNAMIC);
					updateHandler.sendMessageDelayed(message, 10 * 1000);
					return;
				}

				int timeInterval = Integer.valueOf((res.get("update_interval"))
						.toString());
				int count = Integer.valueOf(res.get("count").toString());

				MyLog.d(TAG, "mtimeInterval>>>" + timeInterval);
				MyLog.d(TAG, "mcount>>>" + count);

				if (lastCount != count)
				{
					lastCount = count;

					if (count > 0)
					{
						// mDown_Desc.setVisibility(View.VISIBLE);

						// if (timeInterval >= 60) {
						// if (timeInterval >= 3600)
						// mDownDesc
						// .setText(getString(
						// R.string.per_hour_desc,
						// (timeInterval / 3600) + "",
						// count + ""));
						// else
						// mDownDesc.setText(getString(
						// R.string.per_minute_desc,
						// (timeInterval / 60) + "", count + ""));
						// } else {
						// mDownDesc.setText(getString(
						// R.string.per_second_desc, (timeInterval)
						// + "", count + ""));
						// }

					}
				}

				Message message = updateHandler
						.obtainMessage(MESSAGE_UPDATE_DOWNLOAD_DYNAMIC);
				updateHandler.sendMessageDelayed(message, 10 * 1000);

			}
		}

	};

	@Override
	protected void onStart()
	{
		mDownloadReceiver = new DownloadStatusReceiver();
		IntentFilter downloadFilter = new IntentFilter();
		downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_COMPLETE);
		downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_UPDATE);
		downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_ERROR);

		packageStateReceiver = new PackageProcessingReceiver();
		IntentFilter packageStateFilter = new IntentFilter();
		// packageStateFilter.addAction(Intent.ACTION_PACKAGE_ADDED);
		// packageStateFilter.addAction(Intent.ACTION_PACKAGE_REMOVED);
		packageStateFilter.addAction(AppService.BROADCAST_PACKAGE_INSTALLED);
		packageStateFilter.addAction(AppService.BROADCAST_PACKAGE_UNINSTALLED);
		packageStateFilter.addAction(AppService.BROADCAST_PACKAGE_REPLACED);

		mImageLoadReceiver = new ImageLoadReceiver();
		IntentFilter imageloadFilter = new IntentFilter();
		imageloadFilter.addAction(ImageLoader.IMAGELOADER_ACTION_LOAD_COMPLETE);

		registerReceiver(packageStateReceiver, packageStateFilter);
		registerReceiver(mDownloadReceiver, downloadFilter);
		registerReceiver(mImageLoadReceiver, imageloadFilter);

		super.onStart();
	}

	@Override
	protected void onStop()
	{

		unregisterReceiver(packageStateReceiver);
		unregisterReceiver(mDownloadReceiver);
		unregisterReceiver(mImageLoadReceiver);

		canUpdateDownloadDynamic = false;

		super.onStop();

		MyLog.d(TAG, "============================");
		MyLog.d(TAG, "============================");
		MyLog.d(TAG, "onStop():" + canUpdateDownloadDynamic);
		MyLog.d(TAG, "============================");
		MyLog.d(TAG, "============================");
	}

	boolean s_switchTab = false;

	protected void setSelectedHeaderTab(int i)
	{
		if (mCurHeaderTab == i)
			return;
		ImageLoader.getInstance().clearDownloadList();

		mCurHeaderTab = i;
		s_switchTab = true;

		if (mCurHeaderTab == mDownloadStatusTabID
				&& ConstantValues.HAVE_CUSTOM_MAIN_PAGE_CHANNEL)
		{
			return;
		} else
		{
		}

		if (mCurHeaderTab != mDownloadStatusTabID)
		{
			// mDown_Desc.setVisibility(View.GONE);
			canUpdateDownloadDynamic = false;
		} else
		{
			// mTabSpecMap.get(mCurHeaderTab).clearData();
			// canUpdateDownloadDynamic = true;
		}

		if (mTabSpecMap.get(i).data.size() == 0)
		{
			setContentListVisibility(false, i);
			mTabSpecMap.get(i).loadMoreApps();
		} else
		{
			setContentListVisibility(true, i);
		}
	}

	public boolean hasfootview = false;

	private void setContentListVisibility(boolean showList, int headerIndex)
	{
		if (showList)
		{
			mViews.get(headerIndex).findViewById(R.id.loading_bg).setVisibility(View.GONE);
			AppTabSpec spec = mTabSpecMap.get(headerIndex);
			mCateId = spec.mCateId;
			MyLog.d(TAG, "current HeaderTab=" + headerIndex
					+ ",current spec cateId=" + mCateId);
			if (spec.mCateId == ConstantValues.DOWNLOAD_CATE_ID)
			{
				for (int i = 0; i < spec.data.size(); i++)
				{
					spec.data.get(i).setDownloadTime(
							GeneralUtil.getDownloadTime(this, i));
					spec.data.get(i).setNickName(
							GeneralUtil.getNickName(this, i));
				}
			}
			if (headerIndex == 0)
			{
				if (mListAdapter1.getLastCount() != spec.data.size())
					mListAdapter1.setData(spec.data);
			} else if (headerIndex == 1)
			{
				if (mListAdapter2.getLastCount() != spec.data.size())
					mListAdapter2.setData(spec.data);
			} else if (headerIndex == 2)
			{
				if (mListAdapter3.getLastCount() != spec.data.size())
					mListAdapter3.setData(spec.data);
			}

			if (s_switchTab)
			{
				s_switchTab = false;

				if (mCurHeaderTab == mDownloadStatusTabID
						&& canUpdateDownloadDynamic)
				{
					Message message = updateHandler
							.obtainMessage(MESSAGE_UPDATE_DOWNLOAD_DYNAMIC);
					updateHandler.sendMessageDelayed(message, 10 * 1000);
				}
			}
		}else{
			mViews.get(headerIndex).findViewById(R.id.loading_bg).setVisibility(View.VISIBLE);
		}
	}

	@Override
	protected int getLayout()
	{
		return R.layout.browse_suggested_apps_activity;
	}

	private int mSelectAppId1;
	private int mSelectAppId2;
	private int mSelectAppId3;

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data)
	{
		MyLog.d(TAG, "onActivityResult!!!!!!!!!!!");
		if (requestCode == AppDetailActivity.REQUEST_SHOW_DETAILS)
		{
			if (mCurHeaderTab == mDownloadStatusTabID)
			{
				canUpdateDownloadDynamic = true;
				Message message = updateHandler
						.obtainMessage(MESSAGE_UPDATE_DOWNLOAD_DYNAMIC);
				updateHandler.sendMessageDelayed(message, 10 * 1000);
			}
			// update selected app status
			for (App app : mTabSpecMap.get(mCurHeaderTab).data)
			{
				if (app == null)
					continue;

				if (app.getId() == mSelectAppId1)
				{
					MyLog.d(TAG, "===================== mSelectAppId1 "
							+ mSelectAppId1);

					HashMap<String, Integer> result = DatabaseUtils.getAppInfo(
							getApplicationContext(), mSelectAppId1);
					app.setStatus(result.get("status"));
					app.setScore(result.get("score"));
					app.setScoreCount(result.get("score_cnt"));
					mListAdapter1.notifyDataSetChanged();
				} else if (app.getId() == mSelectAppId2)
				{
					MyLog.d(TAG, "===================== mSelectAppId2 "
							+ mSelectAppId2);

					HashMap<String, Integer> result = DatabaseUtils.getAppInfo(
							getApplicationContext(), mSelectAppId2);
					app.setStatus(result.get("status"));
					app.setScore(result.get("score"));
					app.setScoreCount(result.get("score_cnt"));
					mListAdapter2.notifyDataSetChanged();
				} else if (app.getId() == mSelectAppId3)
				{
					MyLog.d(TAG, "===================== mSelectAppId3 "
							+ mSelectAppId3);

					HashMap<String, Integer> result = DatabaseUtils.getAppInfo(
							getApplicationContext(), mSelectAppId3);
					app.setStatus(result.get("status"));
					app.setScore(result.get("score"));
					app.setScoreCount(result.get("score_cnt"));
					mListAdapter3.notifyDataSetChanged();
				}
			}
		}
		super.onActivityResult(requestCode, resultCode, data);
	}

	private class PackageProcessingReceiver extends BroadcastReceiver

	{

		@Override
		public void onReceive(Context context, Intent intent)
		{
			MyLog.d(TAG, "PackageProcessingReceiver>>>>>>>>>>>>>>>>>>");
			int appid = intent.getIntExtra("appid", -1);
			int status = intent.getIntExtra("status", -1);
			MyLog.d(TAG, "appid=" + appid + ",status=" + status);
			if (appid < 0 || status < 0)
				return;
			// String action = intent.getAction();
			// String packageName = intent.getDataString();
			// packageName = packageName.substring(packageName.indexOf(':') +
			// 1);
			// MyLog.d(TAG, "PackageProcessingReceiver >> onReceive >> action: "
			// + action + ", packageName: " + packageName);
			//
			// SQLiteDatabase db = new
			// DatabaseHelper(context).getWritableDatabase();
			// App targetApp = DatabaseUtils.getAppByPackageName(db,
			// packageName);
			// db.close();
			//
			// if (targetApp == null) {
			// return;
			// }
			//
			// int appid = targetApp.getId();
			// int status = -1;
			//
			// if (action.equals(Intent.ACTION_PACKAGE_ADDED)) {
			// status = App.INSTALLED;
			// } else if (action.equals(Intent.ACTION_PACKAGE_REMOVED)) {
			// status = App.INIT;
			// } else {
			// throw new RuntimeException("got unknown action: " + action);
			// }
			for (int i = 0; i < 3; i++)
			{
				ArrayList<App> applist = mTabSpecMap.get(i).data;
				MyLog.d(TAG, "data size: " + applist.size());
				for (App app : applist)
				{
					MyLog.d(TAG, "existed app id:" + app.getId());
					if (appid != app.getId())
					{
						continue;
					}

					MyLog.d(TAG, "app status: " + app.getStatus());
					if (status != app.getStatus())
					{
						app.setStatus(status);

						if (i == mCurHeaderTab)
						{
							mListAdapter1.notifyDataSetChanged();
							mListAdapter2.notifyDataSetChanged();
							mListAdapter3.notifyDataSetChanged();
							MyLog.d(TAG, "notifyDataSetChanged!!!!!!!!!!!");
						}
					}
				}
			}
		}

	}

	private class DownloadStatusReceiver extends BroadcastReceiver
	{

		@Override
		public void onReceive(Context context, Intent intent)
		{
			final String action = intent.getAction();
			MyLog.d(TAG, "DownloadStatusReceiver >>> onReceive >>> action: "
					+ action);

			final int appid = intent.getIntExtra(AppService.DOWNLOAD_APP_PID,
					-1);
			MyLog.d(TAG, "appid: " + appid + ", mapsize: " + mTabSpecMap.size());

			int appstatus = -1;
			if (AppService.BROADCAST_DOWNLOAD_UPDATE.equals(action))
			{
				appstatus = App.DOWNLOADING;
			} else if (AppService.BROADCAST_DOWNLOAD_COMPLETE.equals(action))
			{
				appstatus = App.DOWNLOADED;
			} else if (AppService.BROADCAST_DOWNLOAD_ERROR.equals(action))
			{
				appstatus = App.INIT;
			} else
			{
				throw new RuntimeException("got unknown action: " + action);
			}

			for (int i = 0; i < 3; i++)
			{
				ArrayList<App> applist = mTabSpecMap.get(i).data;
				MyLog.d(TAG, "data size: " + applist.size());
				for (App app : applist)
				{

					if (app == null || app.getId() != appid)
					{
						continue;
					}

					MyLog.d(TAG, "existed app id:" + app.getId());
					MyLog.d(TAG, "app status: " + app.getStatus());
					if (appstatus != app.getStatus())
					{
						app.setStatus(appstatus);

						if (i == mCurHeaderTab)
						{
							mListAdapter1.notifyDataSetChanged();
							mListAdapter2.notifyDataSetChanged();
							mListAdapter3.notifyDataSetChanged();
							MyLog.d(TAG, "notifyDataSetChanged!!!!!!!!!!!");
						}
					}
				}
			}
		}

	}

	private class ImageLoadReceiver extends BroadcastReceiver
	{

		@Override
		public void onReceive(Context context, Intent intent)
		{
			// TODOif
			mListAdapter1.notifyDataSetChanged();
			mListAdapter2.notifyDataSetChanged();
			mListAdapter3.notifyDataSetChanged();
			MyLog.d(TAG, "notifyDataSetChanged!!!!!!!!!!!");
		}

	}

	@Override
	public void onScroll(AbsListView view, int firstVisibleItem,
			int visibleItemCount, int totalItemCount)
	{
		MyLog.d(TAG + ".Scroll", "first: " + firstVisibleItem + ", visible: "
				+ visibleItemCount + ", total: " + totalItemCount);
		if (totalItemCount == 0)
		{
			return;
		}
		if (firstVisibleItem + visibleItemCount == totalItemCount)
		{
			MyLog.d(TAG,
					"+++++++firstVisibleItem + visibleItemCount >= totalItemCount");
			// scrolled to bottom
			spec = mTabSpecMap.get(mCurHeaderTab);
			if (spec.hasMorePage())
			{
				MyLog.d(TAG, "+++++++load more page+++++++");
				// saveUserLog(1);
				spec.loadMoreApps();
			}
		}
	}

	@Override
	public void onScrollStateChanged(AbsListView view, int scrollState)
	{
	}

	// save user log
	private void saveUserLog(int action)
	{// 今日推荐, 猜你喜欢 , 下载动态,
		MyLog.d(TAG, "save user log,mCateId=" + mCateId + ",action=" + action);
		if (mCateId == 9995)
		{
			GeneralUtil.saveUserLogType3(getApplicationContext(), 5, action);
			// if (action==0) {
			// // when action=0,stands for going into the View;
			// tracker.trackPageView("/"+TAG+"[mCateId"+mCateId+"]");
			// }
			// else {
			// tracker.trackEvent(""+3, ""+5, "", action);
			// }
		} else if (mCateId == 3)
		{
			GeneralUtil.saveUserLogType3(getApplicationContext(), 6, action);
			// if (action==0) {
			// // when action=0,stands for going into the View;
			// tracker.trackPageView("/"+TAG+"[mCateId"+mCateId+"]");
			// }
			// else {
			// tracker.trackEvent(""+3, ""+6, "", action);
			// }
		} else if (mCateId == 9994)
		{
			GeneralUtil.saveUserLogType3(getApplicationContext(), 7, action);
			// if (action==0) {
			// // when action=0,stands for going into the View;
			// tracker.trackPageView("/"+TAG+"[mCateId"+mCateId+"]");
			// }
			// else {
			// tracker.trackEvent(""+3, ""+7, "", action);
			// }
		}
	}

	/**************************新增*********************************/
	private List<View> mViews = new ArrayList<View>();
	/** 第一页的appListAdapter */
	private AppListAdapter mListAdapter1;
	/** 第二页的appListAdapter */
	private AppListAdapter mListAdapter2;
	/** 第三页的appListAdapter */
	private AppListAdapter mListAdapter3;
	private MainViewPagerAdapter viewPagerAdapter;
	private TitlePageIndicator mIndicator;
	private ViewPager mPager;// 页卡内容
	private final String[] TITLE_CONTENTS =
		{ "最新上线", "猜你喜欢", "下载动态" };// 页眉标题

	/** 构建各个Views */
	private void initViews()
	{
		// 设置第一页的view
		View view1 = LayoutInflater.from(this).inflate(R.layout.lay1, null);
		GridView gridview1 = (GridView) view1.findViewById(R.id.contentList1);
		gridview1.setAdapter(mListAdapter1);// 第一页view所对应的适配器
		gridview1.setSelector(R.drawable.c5);
		gridview1.setNumColumns(2);
		gridview1.setOnScrollListener(this);
		gridview1.setBackgroundResource(R.drawable.gridview_bg);
		gridview1.setSelection(0);
		gridview1.setOnItemClickListener(new OnItemClickListener()
		{
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3)
			{
				String parentName;
				App app1 = (App) mListAdapter1.getItem(arg2);
				Intent i1 = new Intent(BrowseSuggestedAppListActivity.this,
						AppDetailActivity.class);
				i1.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app1.getId());
				parentName = mSuggestedCate.get(0).getName();
				i1.putExtra("key_parent", parentName);
				startActivityForResult(i1,
						AppDetailActivity.REQUEST_SHOW_DETAILS);
			}
		});
		// 设置第二页的view
		View view2 = LayoutInflater.from(this).inflate(R.layout.lay1, null);
		GridView gridview2 = (GridView) view2.findViewById(R.id.contentList1);
		gridview2.setAdapter(mListAdapter2);// 第一页view所对应的适配器
		gridview2.setSelector(R.drawable.c5);
		gridview2.setNumColumns(2);
		gridview2.setOnScrollListener(this);
		gridview2.setBackgroundResource(R.drawable.gridview_bg);
		gridview2.setSelection(0);
		gridview2.setOnItemClickListener(new OnItemClickListener()
		{
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3)
			{
				String parentName;
				App app1 = (App) mListAdapter2.getItem(arg2);
				Intent i1 = new Intent(BrowseSuggestedAppListActivity.this,
						AppDetailActivity.class);
				i1.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app1.getId());
				parentName = mSuggestedCate.get(1).getName();
				i1.putExtra("key_parent", parentName);
				startActivityForResult(i1,
						AppDetailActivity.REQUEST_SHOW_DETAILS);
			}
		});
		// 设置第三页的view
		View view3 = LayoutInflater.from(this).inflate(R.layout.lay1, null);
		GridView gridview3 = (GridView) view3.findViewById(R.id.contentList1);
		gridview3.setAdapter(mListAdapter3);// 第一页view所对应的适配器
		gridview3.setSelector(R.drawable.c5);
		gridview3.setNumColumns(2);
		gridview3.setOnScrollListener(this);
		gridview3.setBackgroundResource(R.drawable.gridview_bg);
		gridview3.setSelection(0);
		gridview3.setOnItemClickListener(new OnItemClickListener()
		{
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3)
			{
				String parentName;
				App app1 = (App) mListAdapter3.getItem(arg2);
				Intent i1 = new Intent(BrowseSuggestedAppListActivity.this,
						AppDetailActivity.class);
				i1.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app1.getId());
				parentName = mSuggestedCate.get(2).getName();
				i1.putExtra("key_parent", parentName);
				startActivityForResult(i1,
						AppDetailActivity.REQUEST_SHOW_DETAILS);
			}
		});
		mViews.add(view1);
		mViews.add(view2);
		mViews.add(view3);
	}

	/** 初始化ViewPager */
	private void initViewPager()
	{
		mPager = (ViewPager) findViewById(R.id.vPager);
		viewPagerAdapter = new MainViewPagerAdapter(mViews, TITLE_CONTENTS);
		 mPager.setOffscreenPageLimit(2);
		mPager.setAdapter(viewPagerAdapter);
//		mPager.setCurrentItem(0);

		mIndicator = (TitlePageIndicator) findViewById(R.id.indicator);
		mIndicator.setViewPager(mPager);
		mIndicator.setOnPageChangeListener(new MyOnPageChangeListener());
		mIndicator.setCurrentItem(0);
	}

	/** 页卡切换监听 */
	public class MyOnPageChangeListener implements OnPageChangeListener
	{
		int current = 0;

		@Override
		public void onPageSelected(int arg0)
		{
			// Animation animation = null;
			MyLog.e("arg0", "----" + arg0);
			current = arg0;
		}

		@Override
		public void onPageScrolled(int arg0, float arg1, int arg2)
		{
		}

		@Override
		public void onPageScrollStateChanged(int arg0)
		{
			if (arg0 == 0)
				setSelectedHeaderTab(current%mViews.size());
		}
	}

}