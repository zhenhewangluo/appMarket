package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.content.BroadcastReceiver;
import android.content.Context;
import android.content.Intent;
import android.content.IntentFilter;
import android.database.Cursor;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextUtils;
import android.text.TextWatcher;
import android.view.KeyEvent;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.animation.Animation;
import android.view.animation.TranslateAnimation;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemClickListener;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageView;
import android.widget.ListView;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.App;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.ui.adapter.SearchResultListAdapter;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.tasks.GetHotwordsListTask;
import com.market.hjapp.ui.view.LongButton;

public class SearchedAppListActivity extends BaseBottomTabActivity {
	private static final String TAG = "SearchedAppListActivity";
	private final Context mContext = SearchedAppListActivity.this;
	public static final int REQUEST_SEARCH_RESULT = 101;
	public static final String COUNT = "100";

	ListView mContentList;
	SearchResultListAdapter mListAdapter;
	View mHotKeyLayout;
	ImageView searchIbtn;
//	View mLoadingView;
	EditText mSearchInput;

	int mCurHotwordsPage;

	private ArrayList<String> mPreviousHotwords;

	private Cursor mCursor;

	private TaskResultListener mGetHotwordsListTaskResltListener = new TaskResultListener() {
		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {

			if (!success) {
				MyLog.e(TAG, "Got error when get category list");
				Toast.makeText(mContext, R.string.network_error_msg,
						Toast.LENGTH_LONG).show();
				finish();
			} else {

				final String hotwordsList = (String) res.get("list");

				if (hotwordsList == null || "".equals(hotwordsList)) {
					MyLog.e(TAG, "Got error when get hot words list");
					return;
				} else {
					GeneralUtil.saveHotwords(mContext, hotwordsList);
				}
			}
		}

	};
	private OnClickListener mLoadMoreAppsListener = new OnClickListener() {

		@Override
		public void onClick(View v) {
			// TODO Auto-generated method stub

		}

	};

	private OnItemClickListener mListItemClickListener = new OnItemClickListener() {

		@Override
		public void onItemClick(AdapterView<?> arg0, View arg1, int arg2,
				long arg3) {

			saveUserLog(3);

			App app = mListAdapter.getAppItem(arg2);

			Intent i = new Intent(getApplicationContext(),
					AppDetailActivity.class);
			i.putExtra(AppDetailActivity.EXTRA_KEY_APPID, app.getId());

			i.putExtra(EXTRA_KEY_PARENTNAME,
					getString(R.string.tabtitle_searchresult));
			startActivityForResult(i, AppDetailActivity.REQUEST_SHOW_DETAILS);
		}

	};

	OnClickListener mSearchMoreBtnListener = new OnClickListener() {

		@Override
		public void onClick(View arg0) {
			saveUserLog(4);

			Intent i = new Intent(getApplicationContext(),
					SearchedResultActivity.class);

			i.putExtra("key", mSearchInput.getText().toString().trim());

			startActivityForResult(i, REQUEST_SEARCH_RESULT);
		}

	};

	// private OnClickListener mClearTextBtnClickListener = new
	// OnClickListener() {
	//
	// @Override
	// public void onClick(View arg0) {
	// mSearchInput.setText("");
	// }
	// };

	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);

		saveUserLog(0);

		Button button = (Button)findViewById(R.id.btn_right);
		button.setVisibility(View.VISIBLE);
		button.setOnClickListener(this.backBtnOnClickListener);
		
		mCurHotwordsPage = 0;
		mContentList = (ListView) findViewById(R.id.contentList);
		mContentList.setSelector(R.drawable.c5);
		mContentList.setOnItemClickListener(mListItemClickListener);

		// whether need to update hot words list
		if (GeneralUtil.needUpdate(mContext,
				ConstantValues.PREF_KEY_HOTWORDS_UPDATE_TIME)) {
			MyLog.d(TAG, "==== update hot words list ====");
			try {
				new GetHotwordsListTask(SearchedAppListActivity.this,
						mGetHotwordsListTaskResltListener).execute(COUNT);
				MyLog.d(TAG, "get hot words list ,incoming parameter :count="
						+ COUNT);
			} catch (RejectedExecutionException e) {
				MyLog.e(TAG, "Got exception when execute asynctask!", e);
			}
			GeneralUtil.saveUploadTime(mContext,
					ConstantValues.PREF_KEY_HOTWORDS_UPDATE_TIME);
		}

		// if there isn't any input text, show the hot key to user
		mHotKeyLayout = (View) findViewById(R.id.hot_key_layout);
		searchIbtn = (ImageView) findViewById(R.id.search_ibtn);
		//搜索按钮监听
		searchIbtn.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {
				if (TextUtils.isEmpty(mSearchInput.getText())) {
					showHotwords();
				} else {
					search();
				}
			}
		});
//		mLoadingView = findViewById(R.id.loading_bg);
//
//		mListShade = (ImageView) findViewById(R.id.shade_for_list);

		// click next hot key button to get more hot key
		LongButton nextHotKey = (LongButton) findViewById(R.id.nextHotKey_btn);
//		nextHotKey.setText(R.string.next_hot_key);
		nextHotKey.setBackgroundResource(R.drawable.more_hotkey_selector);
		nextHotKey.setOnClickListener(new OnClickListener() {
			@Override
			public void onClick(View v) {

				saveUserLog(2);

				ArrayList<String> hotwords = GeneralUtil.getHotwords(
						SearchedAppListActivity.this, mCurHotwordsPage);
				if (hotwords != null) {
					showHotwords(hotwords);
					mCurHotwordsPage++;
				}

				// new LoadingHotwordsTask().execute();
			}
		});

		MyLog.d(TAG, "create hot key layout");

		// add footer view(search more button) for app list
		View footerView = getLayoutInflater().inflate(R.layout.search_footer,
				mContentList, false);
		LongButton mSearchMoreBtn = (LongButton) footerView
				.findViewById(R.id.searchmore_btn);
		mSearchMoreBtn.setText(R.string.search_moreapp);
		mSearchMoreBtn.setBackgroundResource(R.drawable.btn_long_selector);
		mSearchMoreBtn.setOnClickListener(mSearchMoreBtnListener);

		footerView.setOnClickListener(mLoadMoreAppsListener);
		mContentList.addFooterView(footerView);

		// change layout when user input search key
		mSearchInput = (EditText) findViewById(R.id.search_inputbox);
		mSearchInput.addTextChangedListener(new TextWatcher() {

			@Override
			public void afterTextChanged(Editable s) {

				if (TextUtils.isEmpty(s)) {
					showHotwords();
				} else {
					search();
				}

			}

			@Override
			public void beforeTextChanged(CharSequence s, int start, int count,
					int after) {
			}

			@Override
			public void onTextChanged(CharSequence s, int start, int before,
					int count) {
			}

		});

		mFourthFooterTab = findViewById(R.id.fourth_footertab);
		mFourthFooterTab.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View arg0) {
				if (!mSearchInput.getText().toString().equals("")) {
					mSearchInput.setText("");
				}
			}

		});

		// ImageView mClearTextBtn = (ImageView)findViewById(R.id.delete_text);
		// mClearTextBtn.setOnClickListener(mClearTextBtnClickListener);

		setSelectedFooterTab(3);

		ArrayList<String> hotwords = GeneralUtil.getHotwords(
				SearchedAppListActivity.this, mCurHotwordsPage);
		if (hotwords != null)
			showHotwords(hotwords);

		mCurHotwordsPage++;

		// new LoadingHotwordsTask().execute();

		// response to intent-filter such as:
		// (1)market://search?q=pname:packageName
		// (2)market://search?q=keywords
		Intent searchIntent = getIntent();
		if (searchIntent != null) {

			String market_url = searchIntent.getDataString();

			if (market_url != null && !"".equals(market_url)) {

				// save user log from market://search?q=pname:packageName
				saveUserLog(5);

				MyLog.d(TAG, "++++++++++market_url=" + market_url);
				String key = market_url.substring(market_url.indexOf('=') + 1);

				if (key != null && !"".equals(key)) {

					MyLog.d(TAG, "+++++++++++++key=" + key);
					Intent i = new Intent(getApplicationContext(),
							SearchedResultActivity.class);
					i.putExtra("key", key);
					startActivityForResult(i, REQUEST_SEARCH_RESULT);

				}
			}

		}
	}

	@Override
	public boolean onKeyDown(int keyCode, KeyEvent event) {
		if (keyCode == KeyEvent.KEYCODE_BACK) {
			if (!mSearchInput.getText().toString().equals("")) {
				mSearchInput.setText("");
				return true;
			}
		}

		return super.onKeyDown(keyCode, event);
	}

	// create hot key animation: fly from random side position to setting center
	// position
	private Animation getAnimation(float posFromX, float posFromY) {
		Animation anim = null;

		anim = new TranslateAnimation(Animation.RELATIVE_TO_SELF, posFromX,
				Animation.RELATIVE_TO_SELF, 0.0f, Animation.RELATIVE_TO_SELF,
				posFromY, Animation.RELATIVE_TO_SELF, 0.0f);
		anim.setDuration(2000);

		return anim;
	}

	@Override
	protected int getLayout() {
		return R.layout.search;
	}

	private void showSearchResult() {
//		mLoadingView.setVisibility(View.GONE);
		mContentList.setVisibility(View.VISIBLE);
		mHotKeyLayout.setVisibility(View.GONE);
//		mListShade.setVisibility(View.VISIBLE);
	}

	public void showLoadingView() {
//		mLoadingView.setVisibility(View.VISIBLE);
		mContentList.setVisibility(View.GONE);
		mHotKeyLayout.setVisibility(View.GONE);
//		mListShade.setVisibility(View.VISIBLE);
	}

	public void showHotwords() {
		if (mPreviousHotwords != null)
			showHotwords(mPreviousHotwords);
	}

	// FIXME: Current is using existed font size list, should calculate in
	// future
	public void showHotwords(ArrayList<String> hotwordsList) {
		mPreviousHotwords = hotwordsList;

//		mLoadingView.setVisibility(View.GONE);
		mContentList.setVisibility(View.GONE);
//		mListShade.setVisibility(View.GONE);
		mHotKeyLayout.setVisibility(View.VISIBLE);

		// create animation for hot key coming
		TextView word1 = getTextView(R.id.hot_key1);
		word1.setText(hotwordsList.get(0));
		word1.startAnimation(getAnimation(0, -((float) (Math.random() * 5) + 2)));

		TextView word2 = getTextView(R.id.hot_key2);
		word2.setText(hotwordsList.get(8));
		word2.startAnimation(getAnimation((float) ((Math.random() * 5) + 2),
				-((float) (Math.random() * 5) + 2)));

		TextView word3 = getTextView(R.id.hot_key3);
		word3.setText(hotwordsList.get(2));
		word3.startAnimation(getAnimation(-((float) ((Math.random() * 5) + 2)),
				-((float) (Math.random() * 5) + 2)));

		TextView word4 = getTextView(R.id.hot_key4);
		word4.setText(hotwordsList.get(1));
		word4.startAnimation(getAnimation((float) ((Math.random() * 5) + 2), 0));

		TextView word5 = getTextView(R.id.hot_key5);
		word5.setText(hotwordsList.get(7));
		word5.startAnimation(getAnimation(-((float) ((Math.random() * 5) + 2)),
				0));

		TextView word6 = getTextView(R.id.hot_key6);
		word6.setText(hotwordsList.get(6));
		word6.startAnimation(getAnimation((float) ((Math.random() * 5) + 2), 0));

		TextView word7 = getTextView(R.id.hot_key7);
		word7.setText(hotwordsList.get(9));
		word7.startAnimation(getAnimation(-((float) ((Math.random() * 5) + 2)),
				0));

		TextView word8 = getTextView(R.id.hot_key8);
		word8.setText(hotwordsList.get(3));
		word8.startAnimation(getAnimation(-((float) ((Math.random() * 5) + 2)),
				(float) (Math.random() * 5) + 2));

		TextView word9 = getTextView(R.id.hot_key9);
		word9.setText(hotwordsList.get(5));
		word9.startAnimation(getAnimation((float) ((Math.random() * 5) + 2),
				(float) (Math.random() * 5) + 2));

		TextView word10 = getTextView(R.id.hot_key10);
		word10.setText(hotwordsList.get(4));
		word10.startAnimation(getAnimation(0, (float) (Math.random() * 5) + 2));

	}

	private TextView getTextView(int id) {
		final TextView tv = (TextView) findViewById(id);
		tv.setOnClickListener(new OnClickListener() {

			@Override
			public void onClick(View arg0) {

				saveUserLog(1);
				mSearchInput.setText(tv.getText());
				// carmack add to let the cursor at the end of the text
				mSearchInput.setSelection(tv.getText().length());
				search();
			}

		});

		return tv;
	}

	protected void search() {
		String key = mSearchInput.getText().toString();

		String[] keys = key.split("'");
		key = "";
		for (int i = 0; i < keys.length; i++) {
			key = key + keys[i];
		}

		// set search result adapter
		if (mCursor != null)
			mCursor.close();
		mCursor = DatabaseUtils.getSearchResultCursor(mDb, key);

		mListAdapter = new SearchResultListAdapter(this, mCursor);
		mContentList.setAdapter(mListAdapter);

		showSearchResult();
	}

	protected void onActivityResult(int requestCode, int resultCode, Intent data) {
		if (requestCode == REQUEST_SEARCH_RESULT) {
			mSearchInput.setText("");
		} else if (requestCode == AppDetailActivity.REQUEST_SHOW_DETAILS) {

			mCursor.requery();
			mListAdapter.notifyDataSetChanged();
		}

		super.onActivityResult(requestCode, resultCode, data);
	}

	private DownloadStatusReceiver mDownloadReceiver;

	@Override
	protected void onStart() {
		mDownloadReceiver = new DownloadStatusReceiver();
		IntentFilter downloadFilter = new IntentFilter();
		downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_COMPLETE);
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
			if (mCursor == null) {
				// Result hasn't been initialized, return
				return;
			}

			final String action = intent.getAction();
			MyLog.d(TAG, "DownloadStatusReceiver >>> onReceive >>> action: "
					+ action);

			final int appid = intent.getIntExtra(AppService.DOWNLOAD_APP_PID,
					-1);
			MyLog.d(TAG, "appid: " + appid);

			int appstatus = -1;
			if (AppService.BROADCAST_DOWNLOAD_COMPLETE.equals(action)) {
				appstatus = App.DOWNLOADED;
			} else if (AppService.BROADCAST_DOWNLOAD_ERROR.equals(action)) {
				appstatus = App.INIT;
			} else {
				// throw new RuntimeException("got unknown action: " + action);
				MyLog.e(TAG, "got unknown action: " + action);
			}

			mCursor.requery();
			mListAdapter.notifyDataSetChanged();

		}

	}

	private void saveUserLog(int action) {
		// save user log
		GeneralUtil.saveUserLogType3(SearchedAppListActivity.this, 18, action);
		// if (action==0) {
		// tracker.trackPageView("/"+TAG);
		// }
		// else {
		// tracker.trackEvent(""+3, ""+18, "", action);
		// }
	}

}