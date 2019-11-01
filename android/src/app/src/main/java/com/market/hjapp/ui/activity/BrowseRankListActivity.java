
package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;

import android.content.Intent;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.widget.AdapterView;
import android.widget.Button;
import android.widget.ListView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.TextView;

import com.market.hjapp.Category;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.adapter.RankListAdapter;

public class BrowseRankListActivity extends BaseBottomTabActivity {//排行
    private static final String TAG = "BrowseRankListActivity";
    private ListView mContentList;
    private HashMap<Integer, ArrayList<Category>> mRankListMap;
    private RankListAdapter mListAdapter;
    private int mCurTab = -1;

    private OnItemClickListener mListItemClickListener = new OnItemClickListener() {

        @Override
        public void onItemClick(AdapterView<?> parent, View view, int position, long id) {
            
        	saveUserLog(1);
        	
        	Category cate = mRankListMap.get(mCurTab).get(position);
            
            Intent i = new Intent(getApplicationContext(), RankAppListActivity.class);
            i.putExtra(CategoryAppListActivity.EXTRA_KEY_CATEID, cate.getSig());
            i.putExtra(CategoryAppListActivity.EXTRA_KEY_ISCHART, mCurTab == 0);
            i.putExtra(EXTRA_KEY_PARENTNAME, cate.getName());
            startActivity(i);
        }
        
    };

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        MyLog.d(TAG, "BrowseRankListActivity onCreate>>>>>>>>>>>>>>>>>>>>");
        
        initHeaderTabs();
        
        mContentList = (ListView)findViewById(R.id.contentList);
//        mContentList.setSelector(R.drawable.c5);
    
        mRankListMap = new HashMap<Integer, ArrayList<Category>>();
        mRankListMap.put(0, new ArrayList<Category>());

        
        mListAdapter = new RankListAdapter(BrowseRankListActivity.this);
        mContentList.setAdapter(mListAdapter);
        mContentList.setOnItemClickListener(mListItemClickListener);

        setSelectedFooterTab(1);
        setSelectedHeaderTab(0);
    }

    private void initHeaderTabs() {
    	MyLog.d(TAG, "initHeaderTabs>>>>>>>>>>>>>>>>>>>>>>>>");
//    	TextView title = (TextView)findViewById(R.id.top_title);
//    	title.setText("排行分类 >");
    	
    	Button button = (Button)findViewById(R.id.btn_right);
		button.setVisibility(View.VISIBLE);
		button.setOnClickListener(this.backBtnOnClickListener);
    }

    protected void setSelectedHeaderTab(int i) {
    	if (mCurTab == i) return;
    	
    	ImageLoader.getInstance().clearDownloadList();
    	
        mCurTab = i;
       	saveUserLog(0);
        // 1. is there data in memory?
        ArrayList<Category> data = mRankListMap.get(i);
        if (data.size() != 0) {
            mListAdapter.setData(data);
            mContentList.setSelection(0);
            return;
        }
        
        // 2. is there data in db?
        data = DatabaseUtils.getRankList(mDb);
        if (data.size() == 0) {
             //throw new RuntimeException("Can't get category for tab: " + i);
        	MyLog.e(TAG, "Can't get rank category for tab: " + i);
        }

        mRankListMap.get(i).addAll(data);
        mListAdapter.setData(data);
        mContentList.setSelection(0);
    }

    
    @Override
    protected int getLayout() {
        return R.layout.browse_rank_list_activity;
    }

    // 	save user log
    private void saveUserLog(int action)
    {
//    	if (mCurTab == 0)
//    	{  		
//    		GeneralUtil.saveUserLogType3(getApplicationContext(), 39, action);
//    		if (action==0) {
//				tracker.trackPageView("/"+TAG);
//			}
//    		else {
//    			tracker.trackEvent(""+3, ""+39, "", action);
//			}
//    		MyLog.d(TAG, "saveUserLogType3,logid=3,page=39,action="+action);
//    	}
		
    }
}
