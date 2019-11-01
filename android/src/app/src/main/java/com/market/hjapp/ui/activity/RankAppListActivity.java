
package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Iterator;
import java.util.List;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.os.Bundle;
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
import com.viewpagerindicator.PageIndicator;
import com.viewpagerindicator.TitlePageIndicator;

/**
 * 排行列表
 * 
 * @author Carmack
 * 
 */
public class RankAppListActivity extends BaseActivity implements
OnScrollListener
{
	private static final String TAG = "RankAppListActivity";
	public static final String EXTRA_KEY_CATEID = "key_cateid";
	public static final String EXTRA_KEY_ISCHART = "key_ischart";
	// View mFirstHeaderTab;
	// View mFirstPressedHeaderTab;
	// View mSecondHeaderTab;
	// View mSecondPressedHeaderTab;
	// View mThirdHeaderTab;
	// View mThirdPressedHeaderTab;

	// ListView mContentList;
	AppListAdapter mListAdapter;
	/** 存放各页的数据 key：序号 value：数据 */
	HashMap<Integer, AppTabSpec> mTabSpecMap;
	AppTabSpec spec;
//	 View mLoadingBackground;
	View mNoContent;

	int mCurHeaderTab = -1;
	protected static final int MESSAGE_LOAD_MORE_APPS = 101;

	OnItemClickListener mListItemClickListener = new OnItemClickListener()
	{

		@Override
		public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
				long arg3)
		{

			saveUserLog(3);

			App app = (App) mListAdapter.getItem(arg2);

			Intent i = new Intent(getApplicationContext(),
					AppDetailActivity.class);
			i.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app.getId());

			mSelectAppId = app.getId();

			int resid = -1;
			switch (mCurHeaderTab)
			{
			case 0:
				resid = R.string.tabtitle_rank_week;
				break;
			case 1:
				resid = R.string.tabtitle_rank_month;
				break;
			case 2:
				resid = R.string.tabtitle_rank_all;
				break;
			default:
				// throw new RuntimeException("Invalid cur tab: " +
				// mCurHeaderTab);
				MyLog.e(TAG, "Invalid cur tab: " + mCurHeaderTab);
			}
			i.putExtra(EXTRA_KEY_PARENTNAME, getString(resid));
			startActivityForResult(i, AppDetailActivity.REQUEST_SHOW_DETAILS);
		}

	};
	private int mCateId;

	// private boolean mIsChart;// is sorting
	// private String mCateName;// category name
	private String mParent; // parent category name
	private DownloadStatusReceiver mDownloadReceiver;

	private PackageProcessingReceiver packageStateReceiver;

	// private View mFooterView;

	/**
	 * handler network is not available
	 * 
	 * @param mCurHeaderTab
	 */
	private void doNetworkInterrupt(int mCurHeaderTab)
	{
		MyLog.i(TAG, "++++++++network data return is null!++++++");
		Toast.makeText(getApplicationContext(),
				getString(R.string.error_null_data), Toast.LENGTH_SHORT)
				.show();
//				getString(R.string.error_http_timeout), Toast.LENGTH_LONG)
		AppTabSpec spec = mTabSpecMap.get(mCurHeaderTab);
		spec.reSetTotalPage(getApplicationContext());
		// int footerViewsCount = mContentList.getFooterViewsCount();
		// if (footerViewsCount > 0)
		// {
		// for (int i = 0; i < footerViewsCount; i++)
		// {
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
		setContentView(R.layout.rank_applist_activity);

		Intent intent = getIntent();
		mCateId = intent.getIntExtra(EXTRA_KEY_CATEID, -1);

		// mIsChart = intent.getBooleanExtra(EXTRA_KEY_ISCHART, false);
		// mCateName = intent.getStringExtra(EXTRA_KEY_PARENTNAME);

		// set title
//		TextView title = (TextView) findViewById(R.id.title_text);
//		mParent = intent.getStringExtra(EXTRA_KEY_PARENTNAME);
//		title.setText(mParent);
		
		TextView title = (TextView)findViewById(R.id.top_title);
		mParent = intent.getStringExtra(EXTRA_KEY_PARENTNAME);
		title.setText(mParent+" >");

		MyLog.d(TAG, "******************************");
		MyLog.d(TAG, "mCateId:" + mCateId);
		MyLog.d(TAG, "******************************");

		// initHeaderTabs();

		// mContentList = (ListView)findViewById(R.id.contentList);
		// mContentList.setSelector(R.drawable.c5);
		// mContentList.setOnScrollListener(this);
		// mFooterView =
		// LayoutInflater.from(this).inflate(R.layout.loading_footer_view,
		// null);
		// mFooterView.setClickable(false);

//		 mLoadingBackground = findViewById(R.id.loading_bg);
		mTabSpecMap = new HashMap<Integer, AppTabSpec>();
		mTabSpecMap.put(0, new AppTabSpec(RankAppListActivity.this, mCateId,
				Category.CATE_TYPE_RANK, 1,// week rank
				mDb, mAppLoadResultListener0));
		mTabSpecMap.put(1, new AppTabSpec(RankAppListActivity.this, mCateId,
				Category.CATE_TYPE_RANK, 2,// month rank
				mDb, mAppLoadResultListener1));
		mTabSpecMap.put(2, new AppTabSpec(RankAppListActivity.this, mCateId,
				Category.CATE_TYPE_RANK, 3,// all rank
				mDb, mAppLoadResultListener2));
		// mContentList.addFooterView(mFooterView, null, false);
		// mListAdapter = new AppListAdapter(RankAppListActivity.this);
		// mContentList.setAdapter(mListAdapter);
		// mContentList.setOnItemClickListener(mListItemClickListener);
		initViews();
		initViewPager();

		setSelectedHeaderTab(0);
	}

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
		packageStateFilter.addDataScheme("package");

		registerReceiver(packageStateReceiver, packageStateFilter);
		registerReceiver(mDownloadReceiver, downloadFilter);

		super.onStart();
	}

	@Override
	protected void onStop()
	{
		unregisterReceiver(packageStateReceiver);
		unregisterReceiver(mDownloadReceiver);

		super.onStop();
	}

	/** 无效方法 */
	/*
	 * private void initHeaderTabs() { mFirstHeaderTab =
	 * findViewById(R.id.first_headertab); mFirstPressedHeaderTab =
	 * findViewById(R.id.first_headertab_pressed);
	 * 
	 * mSecondHeaderTab = findViewById(R.id.second_headertab);
	 * mSecondPressedHeaderTab = findViewById(R.id.second_headertab_pressed);
	 * 
	 * mThirdHeaderTab = findViewById(R.id.third_headertab);
	 * mThirdPressedHeaderTab = findViewById(R.id.third_headertab_pressed);
	 * 
	 * // add click listener mFirstHeaderTab.setOnClickListener(new
	 * OnClickListener() {
	 * 
	 * public void onClick(View v) { setSelectedHeaderTab(0); }
	 * 
	 * });
	 * 
	 * mSecondHeaderTab.setOnClickListener(new OnClickListener() {
	 * 
	 * public void onClick(View v) { setSelectedHeaderTab(1); }
	 * 
	 * });
	 * 
	 * mThirdHeaderTab.setOnClickListener(new OnClickListener() {
	 * 
	 * public void onClick(View v) { setSelectedHeaderTab(2); }
	 * 
	 * });
	 * 
	 * // set header tab info TextView pressed, unpressed; View headInfo =
	 * (View) findViewById(R.id.header_info); pressed = (TextView)
	 * headInfo.findViewById(R.id.first_headertab_pressed_text); unpressed =
	 * (TextView) headInfo.findViewById(R.id.first_headertab);
	 * pressed.setText(R.string.tabtitle_rank_week);
	 * unpressed.setText(R.string.tabtitle_rank_week);
	 * 
	 * pressed = (TextView)
	 * headInfo.findViewById(R.id.second_headertab_pressed_text); unpressed =
	 * (TextView) headInfo.findViewById(R.id.second_headertab);
	 * pressed.setText(R.string.tabtitle_rank_month);
	 * unpressed.setText(R.string.tabtitle_rank_month);
	 * 
	 * pressed = (TextView)
	 * headInfo.findViewById(R.id.third_headertab_pressed_text); unpressed =
	 * (TextView) headInfo.findViewById(R.id.third_headertab);
	 * pressed.setText(R.string.tabtitle_rank_all);
	 * unpressed.setText(R.string.tabtitle_rank_all); }
	 */

	boolean s_switchTab = false;

	protected void setSelectedHeaderTab(int i)
	{
		MyLog.d(TAG, "SelectedHeaderTab=" + i);
		if (mCurHeaderTab == i)
			return;

		ImageLoader.getInstance().clearDownloadList();
		mCurHeaderTab = i;
		s_switchTab = true;
		saveUserLog(i);

		// mFirstHeaderTab.setVisibility(i == 0 ? View.GONE : View.VISIBLE);
		// mFirstPressedHeaderTab.setVisibility(i == 0 ? View.VISIBLE:
		// View.GONE);
		//
		// mSecondHeaderTab.setVisibility(i == 1 ? View.GONE : View.VISIBLE);
		// mSecondPressedHeaderTab.setVisibility(i == 1 ? View.VISIBLE:
		// View.GONE);
		//
		// mThirdHeaderTab.setVisibility(i == 2 ? View.GONE : View.VISIBLE);
		// mThirdPressedHeaderTab.setVisibility(i == 2 ? View.VISIBLE:
		// View.GONE);

		// if (mContentList.getFooterViewsCount()==0) {
		// mContentList.addFooterView(mFooterView);
		// }
		if (mTabSpecMap.get(i).data.size() == 0)
		{
			setContentListVisibility(false, i);

			mTabSpecMap.get(i).loadMoreApps();
		} else
		{
			MyLog.d(TAG, "**************** load from memory");
			setContentListVisibility(true, i);
		}
	}

	public boolean hasfootview = false;

	private void setContentListVisibility(boolean showList, int headerIndex)
	{
		// mContentList.setVisibility(showList ? View.VISIBLE : View.GONE);
		 mViews.get(headerIndex).findViewById(R.id.loading_bg).setVisibility(showList ? View.GONE :
			 View.VISIBLE);

		if (showList)
		{
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

			// if (s_switchTab)
			// {
			// s_switchTab = false;
			//
			// if (mCurHeaderTab == mDownloadStatusTabID
			// && canUpdateDownloadDynamic)
			// {
			// Message message = updateHandler
			// .obtainMessage(MESSAGE_UPDATE_DOWNLOAD_DYNAMIC);
			// updateHandler.sendMessageDelayed(message, 10 * 1000);
			// }
			// }
		}
	}

	private int mSelectAppId;

	@Override
	protected void onActivityResult(int requestCode, int resultCode, Intent data)
	{
		if (requestCode == AppDetailActivity.REQUEST_SHOW_DETAILS)
		{

			// update selected app status
			for (App app : mTabSpecMap.get(mCurHeaderTab).data)
			{
				if (app.getId() == mSelectAppId)
				{
					HashMap<String, Integer> result = DatabaseUtils.getAppInfo(
							getApplicationContext(), mSelectAppId);
					app.setStatus(result.get("status"));
					app.setScore(result.get("score"));
					app.setScoreCount(result.get("score_cnt"));
					mListAdapter.notifyDataSetChanged();
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
							mListAdapter.notifyDataSetChanged();
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
					MyLog.d(TAG, "existed app id:" + app.getId());
					if (app.getId() != appid)
					{
						continue;
					}

					MyLog.d(TAG, "app status: " + app.getStatus());
					if (appstatus != app.getStatus())
					{
						app.setStatus(appstatus);

						if (i == mCurHeaderTab)
							mListAdapter.notifyDataSetChanged();
					}
				}
			}
		}

	}

	@Override
	public void onScroll(AbsListView view, int firstVisibleItem,
			int visibleItemCount, int totalItemCount)
	{
		MyLog.d(TAG, "Scroll>>>first: " + firstVisibleItem + ", visible: "
				+ visibleItemCount + ", total: " + totalItemCount);
		if (totalItemCount == 0)
		{
			return;
		}

		if (firstVisibleItem + visibleItemCount == totalItemCount)
		{
			// scrolled to bottom
			spec = mTabSpecMap.get(mCurHeaderTab);
			if (spec.hasMorePage())
			{

				saveUserLog(1);
				spec.loadMoreApps();
			}
		}
	}

	@Override
	public void onScrollStateChanged(AbsListView view, int scrollState)
	{
	}

	// save user log
	// 0 stand for week_rank,1 stand for month_rank,2 stand for all_rank,3 stand
	// for click some a app
	private void saveUserLog(int action)
	{
		// GeneralUtil.saveUserLogType6(this, mCateId, action);
		// if (action==0) {
		// tracker.trackPageView("/"+TAG);
		// }
		// else {
		// tracker.trackEvent(""+6, ""+mCateId, "", action);
		// }

	}

	/******************************** 新增 *****************************************/
	/** 存放各页的view */
	private List<View> mViews = new ArrayList<View>();
	/** 第一页的appListAdapter */
	private AppListAdapter mListAdapter1;
	/** 第二页的appListAdapter */
	private AppListAdapter mListAdapter2;
	/** 第三页的appListAdapter */
	private AppListAdapter mListAdapter3;
	private PageIndicator mIndicator;
	private ViewPager mPager;// 页卡内容
	private MainViewPagerAdapter viewPagerAdapter;
	private final String[] TITLE_CONTENTS =
		{ "周排行", "月排行", "总排行" };// 页眉标题

	/** 构建各个Views */
	private void initViews()
	{
		mListAdapter1 = new AppListAdapter(RankAppListActivity.this);// 第一页view的Adapter
		mListAdapter2 = new AppListAdapter(RankAppListActivity.this);// 第二页view的Adapter
		mListAdapter3 = new AppListAdapter(RankAppListActivity.this);// 第三页view的Adapter
		// 设置第一页的view
		View view1 = LayoutInflater.from(this).inflate(R.layout.lay1, null);
		GridView gridview1 = (GridView) view1.findViewById(R.id.contentList1);
		gridview1.setAdapter(mListAdapter1);// 第一页view所对应的适配器
//		gridview1.setSelector(R.drawable.c5);
		gridview1.setNumColumns(2);
		gridview1.setOnScrollListener(this);
//		gridview1.setBackgroundResource(R.drawable.gridview_bg);
		gridview1.setSelection(0);
		gridview1.setOnItemClickListener(new OnItemClickListener()
		{
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3)
			{
				App app1 = (App) mListAdapter1.getItem(arg2);
				Intent i1 = new Intent(RankAppListActivity.this,
						AppDetailActivity.class);
				i1.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app1.getId());
				i1.putExtra("key_parent", mParent);
				startActivityForResult(i1,
						AppDetailActivity.REQUEST_SHOW_DETAILS);
			}
		});
		// 设置第一页的view
		View view2 = LayoutInflater.from(this).inflate(R.layout.lay1, null);
		GridView gridview2 = (GridView) view2.findViewById(R.id.contentList1);
		gridview2.setAdapter(mListAdapter2);// 第一页view所对应的适配器
//		gridview2.setSelector(R.drawable.c5);
		gridview2.setNumColumns(2);
		gridview2.setOnScrollListener(this);
//		gridview2.setBackgroundResource(R.drawable.gridview_bg);
		gridview2.setSelection(0);
		gridview2.setOnItemClickListener(new OnItemClickListener()
		{
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3)
			{
				App app1 = (App) mListAdapter2.getItem(arg2);
				Intent i1 = new Intent(RankAppListActivity.this,
						AppDetailActivity.class);
				i1.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app1.getId());
				i1.putExtra("key_parent", mParent);
				startActivityForResult(i1,
						AppDetailActivity.REQUEST_SHOW_DETAILS);
			}
		});
		// 设置第一页的view
		View view3 = LayoutInflater.from(this).inflate(R.layout.lay1, null);
		GridView gridview3 = (GridView) view3.findViewById(R.id.contentList1);
		gridview3.setAdapter(mListAdapter3);// 第一页view所对应的适配器
//		gridview3.setSelector(R.drawable.c5);
		gridview3.setNumColumns(2);
		gridview3.setOnScrollListener(this);
//		gridview3.setBackgroundResource(R.drawable.gridview_bg);
		gridview3.setSelection(0);
		gridview3.setOnItemClickListener(new OnItemClickListener()
		{
			@Override
			public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
					long arg3)
			{
				App app1 = (App) mListAdapter3.getItem(arg2);
				Intent i1 = new Intent(RankAppListActivity.this,
						AppDetailActivity.class);
				i1.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app1.getId());
				i1.putExtra("key_parent", mParent);
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
		mPager.setOffscreenPageLimit(mViews.size());
		mPager.setAdapter(viewPagerAdapter);
		//mPager.setCurrentItem(0);

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