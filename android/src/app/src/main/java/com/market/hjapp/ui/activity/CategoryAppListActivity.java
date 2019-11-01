
package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;
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
import android.view.View.OnClickListener;
import android.view.ViewGroup.LayoutParams;
import android.widget.AbsListView;
import android.widget.AdapterView;
import android.widget.GridView;
import android.widget.LinearLayout;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.AbsListView.OnScrollListener;
import android.widget.AdapterView.OnItemClickListener;

import com.market.hjapp.App;
import com.market.hjapp.AppTabSpec;
import com.market.hjapp.Category;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.AppTabSpec.AppLoadResultListener;
import com.market.hjapp.database.DatabaseSchema;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.ui.adapter.CategoryAppListAdapter;
import com.market.hjapp.ui.adapter.CategoryListAdapter;
import com.viewpagerindicator.TitlePageIndicator;

public class CategoryAppListActivity extends BaseActivity implements OnScrollListener {
    private static final String TAG = "CategoryAppListActivity";
    
    public static final String EXTRA_KEY_CATEID     = "key_cateid";
    public static final String EXTRA_KEY_ISCHART    = "key_ischart";
    public static final String EXTRA_KEY_CATE_TYPE     = "key_type";

//    private View mFirstHeaderTab;
//    private View mFirstPressedHeaderTab;
//    private View mSecondHeaderTab;
//    private View mSecondPressedHeaderTab;
    
    int mCurHeaderTab = -1;
    protected static final int MESSAGE_LOAD_MORE_APPS = 101;
	private List<View> mViews = new ArrayList<View>();
	private List<GridView> mListViews = new ArrayList<GridView>();
    private GridView mContentList1;
    private GridView mContentList2;
    private GridView mContentList3;

    private View mLoadingBackground;

    private HashMap<Integer, AppTabSpec> mTabSpecMap;
    AppTabSpec spec ;

	private MainViewPagerAdapter mAdapter;
	private TitlePageIndicator mIndicator;
	
	private ViewPager mPager;// 页卡内容
    
	private List<CategoryAppListAdapter> mListAdapter; // Tab页面列表
    private CategoryAppListAdapter mListAdapter1;
    private CategoryAppListAdapter mListAdapter2;  
    private CategoryAppListAdapter mListAdapter3;   
    
    OnItemClickListener mListItemClickListener = new OnItemClickListener() {

        @Override
        public void onItemClick(AdapterView<?> arg0, View arg1, int arg2, long arg3) {
        	
        	saveUserLog(2);
        	
            App app = null;
            //if it is the first TextView, that will not be changed when u click it
//        	if (mType == Category.CATE_TYPE_TOPIC)
//    		{
//	        	if(arg2==0){
//	        		return;
//	        	}
        	
        		app = (App)mListAdapter.get(mCurHeaderTab).getItem(arg2);
        		
//    		}
//    		else
//    		{
//    			app = (App)mListAdapter2.getItem(arg2);
//    		}
            
            Intent i = new Intent(getApplicationContext(), AppDetailActivity.class);
            i.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app.getId());
            
            mSelectAppId = app.getId();
            
            i.putExtra(EXTRA_KEY_PARENTNAME, mParent);
            
            startActivityForResult(i, AppDetailActivity.REQUEST_SHOW_DETAILS);
        }
        
    };

    private int mCateId;
    private int mType;
    
    private String mCateDesc;

    private DownloadStatusReceiver mDownloadReceiver;


    private View mFooterView;
    
    /**
     * handler network is not available
     * @param mCurHeaderTab
     */
    private void doNetworkInterrupt(int mCurHeaderTab){
    	MyLog.i(TAG, "++++++++network is not available!++++++");
    	Toast.makeText(getApplicationContext(), getString(R.string.error_http_timeout), Toast.LENGTH_LONG).show();
    	AppTabSpec spec = mTabSpecMap.get(mCurHeaderTab);
    	spec.reSetTotalPage(getApplicationContext());
//    	int footerViewsCount=mContentList.getFooterViewsCount();
//    	if (footerViewsCount>0) {
//    		for (int i = 0; i < footerViewsCount; i++) {
//    			MyLog.d(TAG, "------> Remove Footerview	");
//            	 mContentList.removeFooterView(mFooterView);
//			}
//  		}
    }
    
    private AppLoadResultListener mAppLoadResultListener0 = new AppLoadResultListener()
    {

		@Override
		public void onAppLoadResult(ArrayList<App> data) {
			
			 if (data==null) {
				 doNetworkInterrupt(0);
			  }
			 else {
				 if (mCurHeaderTab == 0){
					 setContentListVisibility(true, 0);				
				  }
			 }
		}
    	
    };
    
    private AppLoadResultListener mAppLoadResultListener1 = new AppLoadResultListener()
    {

		@Override
		public void onAppLoadResult(ArrayList<App> data) {
			
			 if (data==null) {	 
				 doNetworkInterrupt(1);
			 }
			 else {
				 if (mCurHeaderTab == 1){
						setContentListVisibility(true, 1);
				 }
			 }
		}
    	
    };
    
    private AppLoadResultListener mAppLoadResultListener2 = new AppLoadResultListener()
    {

		@Override
		public void onAppLoadResult(ArrayList<App> data) {
			 if (data==null) {				 
				 doNetworkInterrupt(2);
			 }
			 else {
				 if (mCurHeaderTab == 2){
					setContentListVisibility(true, 2);
				 }	
				 
			 }
		}
    	
    };
   
    
    private String mParent;

    @Override   
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        
        setContentView(R.layout.category_applist_activity);
        
        Intent intent = getIntent();
        mCateId = intent.getIntExtra(EXTRA_KEY_CATEID, -1);
        mType = intent.getIntExtra(EXTRA_KEY_CATE_TYPE, -1);
        mCateDesc = intent.getStringExtra("desc");
        
        MyLog.d(TAG, "mType:" + mType);

        // set title
		TextView title = (TextView)findViewById(R.id.top_title);
        mParent = intent.getStringExtra(EXTRA_KEY_PARENTNAME);
        title.setText(mParent+" >");
        
   
//        initHeaderTabs();

//        mFooterView = LayoutInflater.from(this).inflate(R.layout.loading_footer_view, null);
//        mFooterView.setClickable(false);
//       	MyLog.d(TAG, "------> Add Footerview	");
        
//        mLoadingBackground = findViewById(R.id.loading_bg);
        
        // TODO
        // SET mType
        
        mTabSpecMap = new HashMap<Integer, AppTabSpec>();
        mTabSpecMap.put(0, new AppTabSpec(CategoryAppListActivity.this, 
        								mCateId,
        								mType,
										1,
										mDb,
										mAppLoadResultListener0));
		mTabSpecMap.put(1, new AppTabSpec(CategoryAppListActivity.this, 
										mCateId,
										mType,
										2,
										mDb,
										mAppLoadResultListener1));
//		mTabSpecMap.put(2, new AppTabSpec(CategoryAppListActivity.this, 
//										mCateId,
//										mType,
//										3,
//										mDb,
//										mAppLoadResultListener2));
	    if (mType != Category.CATE_TYPE_TOPIC) {
	        mListAdapter = new ArrayList<CategoryAppListAdapter>();
	        mListAdapter1 = new CategoryAppListAdapter(CategoryAppListActivity.this);
	        mListAdapter2 = new CategoryAppListAdapter(CategoryAppListActivity.this);
	   	 	mListAdapter1.setTitleDesc(mCateDesc);
	   	 	mListAdapter2.setTitleDesc(mCateDesc);
	   	 	
	        mListAdapter.add(mListAdapter1);
	        mListAdapter.add(mListAdapter2);
	        
	
			LayoutInflater mInflater = getLayoutInflater();
				
			View view1 = mInflater.inflate(R.layout.lay1, null);
			mContentList1 = (GridView)view1.findViewById(R.id.contentList1);
//	        mContentList1.setSelector(R.drawable.c5);
	        mContentList1.setAdapter(mListAdapter1);		
	        mContentList1.setOnItemClickListener(mListItemClickListener);		
	        mContentList1.setOnScrollListener(this);   
	        mContentList1.setNumColumns(2);
	        mContentList1.setBackgroundResource(R.drawable.gridview_bg);
			View view2 = mInflater.inflate(R.layout.lay1, null);
			mContentList2 = (GridView)view2.findViewById(R.id.contentList1);
//	        mContentList2.setSelector(R.drawable.c5);
	        mContentList2.setAdapter(mListAdapter2);		
	        mContentList2.setOnItemClickListener(mListItemClickListener);			
	        mContentList2.setOnScrollListener(this);     
	        mContentList2.setNumColumns(2);
	        mContentList2.setBackgroundResource(R.drawable.gridview_bg);
	        //mContentList2.addFooterView(mFooterView, null, false);  
			
	        mViews.add(view1);
	        mViews.add(view2);    
	        mListViews.add(mContentList1);
	        mListViews.add(mContentList2);    
	    
	        mAdapter = new MainViewPagerAdapter(mViews,new String[]{"最新上线","最多下载"});
	    	mPager = (ViewPager) findViewById(R.id.vPager);
			mPager.setAdapter(mAdapter);
			//mPager.setCurrentItem(0);
			//mPager.setOnPageChangeListener(new MyOnPageChangeListener());
			mIndicator = (TitlePageIndicator)findViewById(R.id.indicator);
			mIndicator.setViewPager(mPager);
			mIndicator.setOnPageChangeListener(new MyOnPageChangeListener());	
			mIndicator.setCurrentItem(0);//zxg,20120413
        }
        else {
            mListAdapter = new ArrayList<CategoryAppListAdapter>();
	        mListAdapter1 = new CategoryAppListAdapter(CategoryAppListActivity.this);
	   	 	mListAdapter1.setTitleDesc(mCateDesc);
	        mListAdapter.add(mListAdapter1);
			LayoutInflater mInflater = getLayoutInflater();
				
			View view1 = mInflater.inflate(R.layout.lay1, null);
			mContentList1 = (GridView)view1.findViewById(R.id.contentList1);
//	        mContentList1.setSelector(R.drawable.c5);
	        mContentList1.setAdapter(mListAdapter1);		
	        mContentList1.setOnItemClickListener(mListItemClickListener);		
	        mContentList1.setOnScrollListener(this);   
	        mContentList1.setNumColumns(2);
	        mContentList1.setBackgroundResource(R.drawable.gridview_bg);

	        //mContentList2.addFooterView(mFooterView, null, false);  
			
	        mViews.add(view1);
   
	        mListViews.add(mContentList1);
 
        	
        	mAdapter = new MainViewPagerAdapter(mViews,new String[]{"专题"});
   	    	mPager = (ViewPager) findViewById(R.id.vPager);
   			mPager.setAdapter(mAdapter);
   			//mPager.setCurrentItem(0);
   			//mPager.setOnPageChangeListener(new MyOnPageChangeListener());
   			mIndicator = (TitlePageIndicator)findViewById(R.id.indicator);
   			mIndicator.setViewPager(mPager);
   			mIndicator.setOnPageChangeListener(new MyOnPageChangeListener());
   			mIndicator.setCurrentItem(0);//zxg,20120413
   			mIndicator.setVisibility(View.GONE);
		}



        setSelectedHeaderTab(0);
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
    @Override
    protected void onStart() {
        super.onStart();

        mDownloadReceiver = new DownloadStatusReceiver();
        IntentFilter downloadFilter = new IntentFilter();
        downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_COMPLETE);
        downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_UPDATE);
        downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_ERROR);

        registerReceiver(mDownloadReceiver, downloadFilter);
    }
    
    @Override
    protected void onStop() {
        unregisterReceiver(mDownloadReceiver);
        
        super.onStop();
    }

//    private void initHeaderTabs() {
//        mFirstHeaderTab = findViewById(R.id.first_headertab);
//        mFirstPressedHeaderTab = findViewById(R.id.first_headertab_pressed);
//
//        mSecondHeaderTab = findViewById(R.id.second_headertab);
//        mSecondPressedHeaderTab = findViewById(R.id.second_headertab_pressed);
//
//        if (mType == Category.CATE_TYPE_TOPIC) {
//            findViewById(R.id.header_tabs).setVisibility(View.GONE);
//            return;
//        }
//        
////        mContentList.setLayoutParams(new LayoutParams();
////        mContentList.setLayoutParams( new AbsListView.LayoutParams(mContentList.getLayoutParams().width, R.dimen.has_title_list_height));
//        
//        // add click listener
//        mFirstHeaderTab.setOnClickListener(new OnClickListener() {
//
//            public void onClick(View v) {
//                setSelectedHeaderTab(0);
//            }
//            
//        });
//
//        mSecondHeaderTab.setOnClickListener(new OnClickListener() {
//
//            public void onClick(View v) {
//                setSelectedHeaderTab(1);
//            }
//            
//        });
//    }

    boolean s_switchTab = false;
    protected void setSelectedHeaderTab(int i) {
        if (mCurHeaderTab == i) return;
        
        ImageLoader.getInstance().clearDownloadList();

        mCurHeaderTab = i;
        s_switchTab = true;
        
        saveUserLog(i);
        
//        mFirstHeaderTab.setVisibility(i == 0 ? View.GONE : View.VISIBLE);
//        mFirstPressedHeaderTab.setVisibility(i == 0 ? View.VISIBLE: View.GONE);
//
//        mSecondHeaderTab.setVisibility(i == 1 ? View.GONE : View.VISIBLE);
//        mSecondPressedHeaderTab.setVisibility(i == 1 ? View.VISIBLE: View.GONE);
        
//        if (mContentList.getFooterViewsCount()==0) {
//        	MyLog.d(TAG, "------> Add Footerview	");
//        	mContentList.addFooterView(mFooterView);
//		}
        
        if (mTabSpecMap.get(i).data.size() == 0)
        {
        	setContentListVisibility(false, i);
        	mTabSpecMap.get(i).loadMoreApps();
        }
        else
        {
        	MyLog.d(TAG, "**************** load from memory");
        	setContentListVisibility(true, i);
        }
    }

    public boolean hasfootview = false;
    private void setContentListVisibility(boolean showList, int headerIndex) {
//        mListViews.get(headerIndex).setVisibility(showList ? View.VISIBLE : View.GONE);
//        mLoadingBackground.setVisibility(showList ? View.GONE : View.VISIBLE);
    	mViews.get(headerIndex).findViewById(R.id.loading_bg).setVisibility(showList ? View.GONE : View.VISIBLE);
        if (showList) {
            spec = mTabSpecMap.get(headerIndex);
            
//            if(mType == Category.CATE_TYPE_TOPIC){
        		if (mListAdapter.get(headerIndex).getLastCount() != spec.data.size())
        				mListAdapter.get(headerIndex).setData(spec.data);
//            }else
//            {
//            	mListAdapter2.setData(spec.data);
//            }
            
            if(s_switchTab)
            {
            	//mListViews.get(headerIndex).setSelection(0);
            	s_switchTab = false;
            }
//            
//            if (!spec.hasMorePage() && mContentList.getFooterViewsCount()>0) {
//            	MyLog.d(TAG, "------> Remove Footerview	");
//            	mContentList.removeFooterView(mFooterView); 
//			}
        }        
    }

    private class DownloadStatusReceiver extends BroadcastReceiver {

        @Override
        public void onReceive(Context context, Intent intent) {
            final String action = intent.getAction();
            MyLog.d(TAG, "DownloadStatusReceiver >>> onReceive >>> action: " + action);
            
            final int appid = intent.getIntExtra(AppService.DOWNLOAD_APP_PID, -1);
            
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
            
            for (int i = 0; i < 2; i++) {
                ArrayList<App> applist = mTabSpecMap.get(i).data;
                for (App app : applist) {
                    if (appid!=app.getId()) {
                        continue;
                    }

                    if (appstatus != app.getStatus()) {
                        app.setStatus(appstatus);
                    	mListAdapter.get(mCurHeaderTab).notifyDataSetChanged();
//                        if (i == mCurHeaderTab) 
//                        {
//                        	if(mType == Category.CATE_TYPE_TOPIC)
//                        		mListAdapter1.notifyDataSetChanged();
//                        	else
//	                   			mListAdapter2.notifyDataSetChanged();
//                        }
                    }
                }
            }
        }
        
    }


    @Override
    public void onScroll(AbsListView view, int firstVisibleItem, int visibleItemCount,
            int totalItemCount) {
        MyLog.d(TAG, "Scroll>>>first: " + firstVisibleItem + ", visible: " + visibleItemCount + ", total: " + totalItemCount);
        
        if (totalItemCount == 0) {
            return;
        }
        
//        MyLog.d(TAG, "-------------->" + mContentList.getFooterViewsCount());
        
        if (firstVisibleItem + visibleItemCount == totalItemCount) {
            // scrolled to bottom
            spec = mTabSpecMap.get(mCurHeaderTab);
            if (spec.hasMorePage()) {
            	
            	saveUserLog(1);
            	spec.loadMoreApps();
            }
        }
    }

    @Override
    public void onScrollStateChanged(AbsListView view, int scrollState) {
    }
    
    private int mSelectAppId;
    @Override
    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
        if (requestCode == AppDetailActivity.REQUEST_SHOW_DETAILS) {
            
        	// update selected app status
            for (App app : mTabSpecMap.get(mCurHeaderTab).data) {
                if (app.getId() == mSelectAppId) {
                    HashMap<String, Integer> result = DatabaseUtils.getAppInfo(CategoryAppListActivity.this, mSelectAppId);
                	app.setStatus(result.get("status"));
                	app.setScore(result.get("score"));
                	app.setScoreCount(result.get("score_cnt"));
                	mListAdapter.get(mCurHeaderTab).notifyDataSetChanged();
//                	if(mType == Category.CATE_TYPE_TOPIC)
//                		mListAdapter1.notifyDataSetChanged();
//                	else
//                		mListAdapter2.notifyDataSetChanged();
                }
            }
        }
        super.onActivityResult(requestCode, resultCode, data);
    }
    
 // save user log
    private void saveUserLog(int action)
    {   
//        if (mType == Category.CATE_TYPE_TOPIC)
//        {
//        	GeneralUtil.saveUserLogType4(this, mCateId, action);
//        	if (action==0) {
//    			tracker.trackPageView("/"+TAG);
//    		}
//    		else {
//    			tracker.trackEvent(""+4, ""+mCateId, "", action);
//    		} 
//        }
//        else
//        {
//
//        	GeneralUtil.saveUserLogType5(this, mCateId, action);
//        	if (action==0) {
//    			tracker.trackPageView("/"+TAG);
//    		}
//    		else {
//    			tracker.trackEvent(""+5, ""+mCateId, "", action);
//    		} 
//        }
    	
		
    }
    
}
