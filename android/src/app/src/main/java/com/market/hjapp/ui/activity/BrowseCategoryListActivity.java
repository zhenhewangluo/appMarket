
package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import android.content.Intent;
import android.os.Bundle;
import android.support.v4.view.ViewPager;
import android.support.v4.view.ViewPager.OnPageChangeListener;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.AdapterView.OnItemClickListener;

import com.market.hjapp.Category;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.adapter.CategoryListAdapter;
import com.viewpagerindicator.TitlePageIndicator;

public class BrowseCategoryListActivity extends BaseBottomTabActivity {
	private List<View> mViews = new ArrayList<View>();
	private List<ListView> mListViews = new ArrayList<ListView>();
    private ListView mContentList1;
    private ListView mContentList2;
    private ListView mContentList3;
    private HashMap<Integer, ArrayList<Category>> mCategoryListMap;
	private MainViewPagerAdapter mAdapter;
	private TitlePageIndicator mIndicator;
	
	private ViewPager mPager;// 页卡内容
    
	private List<CategoryListAdapter> mListAdapter; // Tab页面列表
    private CategoryListAdapter mListAdapter1;
    private CategoryListAdapter mListAdapter2;  
    private CategoryListAdapter mListAdapter3;   
    private int mCurTab = -1;

    private OnItemClickListener mListItemClickListener = new OnItemClickListener() {
    	
        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
            
        	saveUserLog(1);
        	
        	Category cate = mCategoryListMap.get(mCurTab).get(position);
            
            Intent i = new Intent(getApplicationContext(), CategoryAppListActivity.class);
            i.putExtra(CategoryAppListActivity.EXTRA_KEY_CATEID, cate.getSig());
            i.putExtra(CategoryAppListActivity.EXTRA_KEY_CATE_TYPE, cate.getType());
            i.putExtra(EXTRA_KEY_PARENTNAME, cate.getName());
            i.putExtra("desc", cate.getDescription());
            
            startActivity(i);
        }
        
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        initHeaderTabs();

 
        mCategoryListMap = new HashMap<Integer, ArrayList<Category>>();
        mCategoryListMap.put(0, new ArrayList<Category>());
        mCategoryListMap.put(1, new ArrayList<Category>());
        mCategoryListMap.put(2, new ArrayList<Category>());
        
        mListAdapter = new ArrayList<CategoryListAdapter>();
        mListAdapter1 = new CategoryListAdapter(BrowseCategoryListActivity.this);
        mListAdapter2 = new CategoryListAdapter(BrowseCategoryListActivity.this);
        mListAdapter3 = new CategoryListAdapter(BrowseCategoryListActivity.this);
        mListAdapter.add(mListAdapter1);
        mListAdapter.add(mListAdapter2);
        mListAdapter.add(mListAdapter3);
        

		LayoutInflater mInflater = getLayoutInflater();
		
		View view1 = mInflater.inflate(R.layout.lay2, null);
		mContentList1 = (ListView)view1.findViewById(R.id.contentList);
        mContentList1.setAdapter(mListAdapter1);		
        mContentList1.setOnItemClickListener(mListItemClickListener);		
		View view2 = mInflater.inflate(R.layout.lay2, null);
		mContentList2 = (ListView)view2.findViewById(R.id.contentList);
        mContentList2.setAdapter(mListAdapter2);		
        mContentList2.setOnItemClickListener(mListItemClickListener);		
		View view3 = mInflater.inflate(R.layout.lay2, null);
		mContentList3 = (ListView)view3.findViewById(R.id.contentList);
        mContentList3.setAdapter(mListAdapter3);		
        mContentList3.setOnItemClickListener(mListItemClickListener);		
          
        mViews.add(view1);
        mViews.add(view2);    
        mViews.add(view3);
        mListViews.add(mContentList1);
        mListViews.add(mContentList2);    
        mListViews.add(mContentList3);        
        mAdapter = new MainViewPagerAdapter(mViews,new String[]{"专题","游戏","软件"});
    	mPager = (ViewPager) findViewById(R.id.vPager);
		mPager.setAdapter(mAdapter);
		mPager.setOffscreenPageLimit(2);
		
		mIndicator = (TitlePageIndicator)findViewById(R.id.indicator);
		mIndicator.setViewPager(mPager);
		mIndicator.setOnPageChangeListener(new MyOnPageChangeListener());	
		mIndicator.setCurrentItem(0);
        setSelectedFooterTab(2);
        setSelectedHeaderTab(0);
    }
	/** 页卡切换监听 */
	public class MyOnPageChangeListener implements OnPageChangeListener
	{
		int current = 0;

		@Override
		public void onPageSelected(int arg0)
		{
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
    private void initHeaderTabs() {
    	Button button = (Button)findViewById(R.id.btn_right);
		button.setVisibility(View.VISIBLE);
		button.setOnClickListener(this.backBtnOnClickListener);
    }

    protected void setSelectedHeaderTab(int i) {
    	if (mCurTab == i) return;
    	
    	ImageLoader.getInstance().clearDownloadList();
    	
    	mCurTab = i;
    	saveUserLog(0);
    	
        ArrayList<Category> data = mCategoryListMap.get(i);
        MyLog.e(TAG, "1. is there data in memory?----" +data.size());
        if (data.size() != 0) {
        	if(mListAdapter.get(mCurTab).getLastCount() != data.size() )
        		mListAdapter.get(mCurTab).setData(data);
            return;
        }
        
        data = DatabaseUtils.getCategoryList(BrowseCategoryListActivity.this, mDb, i);
        if (data.size() == 0) {
            throw new RuntimeException("Can't get category for tab: " + i);
        }
        MyLog.e(TAG, "2. is there data in db?----" +data.size());
        mCategoryListMap.get(i).addAll(data);
        if(mListAdapter.get(mCurTab).getLastCount() != data.size() )
        	mListAdapter.get(mCurTab).setData(data);
    }

    
    @Override
    protected int getLayout() {
        return R.layout.browse_category_list_activity;
    }

    // 	save user log
    private void saveUserLog(int action)
    {
		
    }
}
