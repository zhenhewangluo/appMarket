package com.market.hjapp.ui.activity;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.R.color;
import android.graphics.Bitmap;
import android.os.Bundle;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.GridView;
import android.widget.ImageView;
import android.widget.Toast;

import com.market.hjapp.Category;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ImageLoader.DownOverListener;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.adapter.InterestListAdapter;
import com.market.hjapp.ui.adapter.InterestListAdapter.InterestSort;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.tasks.GetFavoriteCateListTask;
import com.market.hjapp.ui.tasks.SetFavoriteCateListTask;
import com.market.hjapp.ui.view.LongButton;

public class SelectFavoriteCateActivity extends BaseActivity{
	
	private static final String TAG = "SelectFavoriteCateActivity";
	
	private LongButton mSubmit;
	private LongButton mJump;
	
	/** 感兴趣的分类 */
	private ArrayList<String> mCate = new ArrayList<String>();
	private InterestListAdapter interestListAdapter;
	private ArrayList<InterestSort> mInterestSorts;
	private GridView sortGridView;
	private ImageView[] mViews;
	
	@Override
	protected void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		setContentView(R.layout.lead2);
		saveUserLog(0);
		init();
	}
	
	private TaskResultListener mGetFavoriteCateListTaskResltListener = new TaskResultListener() {

        @Override
        public void onTaskResult(boolean success, HashMap<String, Object> res) {
            if (!success) {
                Toast.makeText(SelectFavoriteCateActivity.this, R.string.network_error_msg, Toast.LENGTH_LONG).show();
                finish();
            } else {
            	String list = (String)res.get("list");
                MyLog.d(TAG, "favorite cate list: " + list);
                GeneralUtil.saveFavoriteCateList(SelectFavoriteCateActivity.this, list);
                
                refreshView();
            }
        }
        
    };

	private void init() {
		mSubmit = (LongButton) findViewById(R.id.submit);
		mSubmit.setBackgroundResource(R.drawable.btn_ok_selector);
		mSubmit.setText(R.string.submit);
		mSubmit.setOnClickListener(mSubmitListener);
		mJump = (LongButton) findViewById(R.id.jump);
		mJump.setBackgroundResource(R.drawable.btn_cancel_selector);
		mJump.setText(R.string.jump);
		mJump.setOnClickListener(mJumpListener);
		// herokf add
		sortGridView = (GridView) findViewById(R.id.interest_sorts);
		interestListAdapter = new InterestListAdapter(this, null);
		mInterestSorts = new ArrayList<InterestSort>();
		sortGridView.setAdapter(interestListAdapter);
		sortGridView.setSelector(color.transparent);
		sortGridView.setNumColumns(4);
		sortGridView.setSelection(0);
		sortGridView.setOnItemClickListener(new OnItemClickListener() {
			@Override
			public void onItemClick(AdapterView<?> parent, View view,
					int position, long id) {
				InterestSort sort = ((InterestSort) interestListAdapter
						.getItem(position));
				sort.setSelect(!sort.isSelect());// 设置是否选中
				interestListAdapter.notifyDataSetChanged();
			}
		});
		try
        {
        	new GetFavoriteCateListTask(SelectFavoriteCateActivity.this, mGetFavoriteCateListTaskResltListener, true).execute();
        } 
        catch (RejectedExecutionException e) {
        	MyLog.e(TAG, "Got exception when execute asynctask!", e);
        }
		
	}
	
	private void refreshView()
	{
		/** 获得感兴趣的分类列表 */
		String cateList = GeneralUtil.getFavoriteCateList(SelectFavoriteCateActivity.this);
		MyLog.e(TAG, "get cateList:" + cateList);
		String[] cateStrings = cateList.split(",");
		for (int i = 0; i < cateStrings.length; i++) {
			mCate.add(cateStrings[i]);
		}
		mViews = new ImageView[mCate.size()];
		// 遍历添加所有兴趣分类
		for (int i = 0; i < mCate.size(); i++) {
			MyLog.d(TAG, "mCate[i]: " + i + " : " + mCate.get(i));
			Category c = DatabaseUtils.getCategory(mDb,
					Integer.parseInt(mCate.get(i)));

			if (cateList.indexOf(mCate.get(i)) != -1)
			{
				
			}
			// 某一项兴趣分类
			InterestSort sort = new InterestSort();
			sort.setId(i);// 设置id
			sort.setSelect(false);
			mViews[i] = new ImageView(this);
			sort.setInterestIcon(null);// 设置分类图标
			sort.setInterestName(c.getName());// 设置分类名字
			String iconUrl = c.getIconUrl();
			String iconpath = DatabaseUtils.getLocalPathFromUrl(iconUrl);

			if (iconpath != null && !"".equals(iconpath)
					&& new File(iconpath).exists()) {
				sort.setInterestIcon(DatabaseUtils.getImage(iconpath));// 设置分类图标
			} else {
				// reset first 设置分类图标
				ImageLoader.getInstance().loadBitmapOnThread(iconUrl, this,
						mViews[i], downOverListener);
			}
			mInterestSorts.add(sort);// 添加分类到列表
		}
		interestListAdapter.setData(mInterestSorts);		
	}

	private DownOverListener downOverListener = new DownOverListener() {

		@Override
		public void onTaskResult(boolean success,HashMap<ImageView, Bitmap> mBitmaps) {
			if (success) {
				MyLog.e("下载提示", "所有下载已完成，要更新。");
				for (int i = 0; i < mBitmaps.size(); i++) {
					if (null!=mBitmaps.get(mViews[i])) {
						interestListAdapter.getDataList().get(i)
						.setInterestIcon(mBitmaps.get(mViews[i]));
					}
				}
				interestListAdapter.notifyDataSetChanged();
			}
		}
	};
	private OnClickListener mSubmitListener = new OnClickListener() {
		
		@Override
		public void onClick(View v) {
			
			saveUserLog(1);
			StringBuffer myCateList = new StringBuffer();
			for (int i = 0; i < mInterestSorts.size(); i++) {
				if (mInterestSorts.get(i).isSelect()) {
					if (!myCateList.equals(""))
						myCateList.append(",");

					myCateList.append(mCate.get(i));
				}
			}

			if (myCateList.equals("")) {
				Toast.makeText(getApplicationContext(),
						R.string.select_favor_cate_null, Toast.LENGTH_LONG)
						.show();
				return;
			}
			
			MyLog.d(TAG, "select myCateList:" + myCateList);
			GeneralUtil.saveMyFavoriteCateList(SelectFavoriteCateActivity.this, myCateList.toString());
			new SetFavoriteCateListTask(SelectFavoriteCateActivity.this, mSetFavoriteCateListTaskResltListener).execute(myCateList.toString());
		}
	};
	
	private TaskResultListener mSetFavoriteCateListTaskResltListener = new TaskResultListener() {

        @Override
        public void onTaskResult(boolean success, HashMap<String, Object> res) {
        	finish();
        }
    };
	
	private OnClickListener mJumpListener = new OnClickListener() {
		
		@Override
		public void onClick(View v) {
			saveUserLog(2);
			finish();
		}
	};
	
	@Override
    public boolean onKeyDown(int keyCode, KeyEvent event) {
        if (keyCode == KeyEvent.KEYCODE_BACK) {
        	saveUserLog(3);
        }

        return super.onKeyDown(keyCode, event);
    }

	private void saveUserLog(int action)
    {
    	// save user log
		GeneralUtil.saveUserLogType3(this, 28, action);
//		if (action==0) {
//			tracker.trackPageView("/"+TAG);
//		}
//		else {
//			tracker.trackEvent(""+3, ""+28, "", action);
//		}
    }

}
