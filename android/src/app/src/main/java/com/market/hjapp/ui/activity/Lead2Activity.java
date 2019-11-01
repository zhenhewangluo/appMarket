package com.market.hjapp.ui.activity;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.List;

import android.R.color;
import android.content.ComponentName;
import android.content.Context;
import android.content.Intent;
import android.content.Intent.ShortcutIconResource;
import android.content.pm.PackageManager;
import android.content.pm.PackageManager.NameNotFoundException;
import android.content.pm.ResolveInfo;
import android.graphics.Bitmap;
import android.os.Bundle;
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
import com.market.hjapp.ImageLoader.DownOverListener;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.adapter.InterestListAdapter;
import com.market.hjapp.ui.adapter.InterestListAdapter.InterestSort;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.tasks.SetFavoriteCateListTask;
import com.market.hjapp.ui.view.LongButton;

/**
 * 选择感兴趣的分类页
 * 
 * @author herokf modify @ 2012.05.18
 * 
 */
public class Lead2Activity extends BaseActivity {
	private static final String TAG = "Lead2Activity";
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
		if (!GeneralUtil.hasCreatedShortCut(this)) {
			String pkg_name = getIntent().getStringExtra("pkg_name");
			String class_name = getIntent().getStringExtra("class_name");
			// addShortCut(pkg_name, class_name);
			addShortcut(this, "欢聚宝");
			GeneralUtil.saveCreatedShortCut(this, true);
		}
	}

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
				MyLog.d("选中喜好分类项目： ", "分类"+position);
				InterestSort sort = ((InterestSort) interestListAdapter
						.getItem(position));
				sort.setSelect(!sort.isSelect());// 设置是否选中
				interestListAdapter.notifyDataSetChanged();
			}
		});

		/** 获得感兴趣的分类列表 */
		String cateList = GeneralUtil.getFavoriteCateList(Lead2Activity.this);
		MyLog.e(TAG, "get cateList:" + cateList);
		String[] cateStrings = cateList.split(",");
		for (int i = 0; i < cateStrings.length; i++) {
			mCate.add(cateStrings[i]);
		}
		mViews = new ImageView[mCate.size()];
		MyLog.d(TAG, "mCate.size(): "  + " : " + mCate.size());
		// 遍历添加所有兴趣分类
		for (int i = 0; i < mCate.size(); i++) {
			MyLog.d(TAG, "mCate[i]: " + i + " : " + mCate.get(i));
			Category c = DatabaseUtils.getCategory(mDb,
					Integer.parseInt(mCate.get(i)));
			if (c!=null) {
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
					MyLog.e(TAG, "从缓冲中取图片。");
				} else {
					// reset first 设置分类图标
					MyLog.e(TAG, "从网络中取图片。");
					ImageLoader.getInstance().loadBitmapOnThread(iconUrl, this,
							mViews[i], downOverListener);
				}
				mInterestSorts.add(sort);// 添加分类到列表
			}
		}

		interestListAdapter.setData(mInterestSorts);
	}
	private DownOverListener downOverListener = new DownOverListener() {

		@Override
		public void onTaskResult(boolean success,HashMap<ImageView, Bitmap> mBitmaps) {
			if (success) {
				try {
					MyLog.e("下载提示", "所有下载已完成，要更新。");
					for (int i = 0; i < mBitmaps.size(); i++) {
						if (null!=mBitmaps.get(mViews[i])) {
							interestListAdapter.getDataList().get(i)
							.setInterestIcon(mBitmaps.get(mViews[i]));
						}
					}
					interestListAdapter.notifyDataSetChanged();
				} catch (Exception e) {
					e.printStackTrace();
				}
			}
		}
	};

	/** 提交监听 */
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
			GeneralUtil.saveMyFavoriteCateList(Lead2Activity.this,
					myCateList.toString());

			new SetFavoriteCateListTask(Lead2Activity.this,
					mSetFavoriteCateListTaskResltListener).execute(myCateList
							.toString());
		}
	};

	/** 保存感兴趣的分类：任务结果 */
	private TaskResultListener mSetFavoriteCateListTaskResltListener = new TaskResultListener() {

		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res) {
			goNextPage();
			finish();
		}
	};
	/** 跳过取消按钮监听 */
	private OnClickListener mJumpListener = new OnClickListener() {
		@Override
		public void onClick(View v) {
			saveUserLog(2);

			goNextPage();
		}
	};
	/** 前往下一页 */
	private void goNextPage() {
		switch (GeneralUtil.getUserGuideFunc(Lead2Activity.this)) {
			case 0 :
				// Intent intent1 = new Intent(getApplicationContext(),
				// BrowseSuggestedAppListActivity.class);
				Intent intent1 = new Intent(getApplicationContext(),
						RecommendActivity.class);
				startActivity(intent1);
				finish();
				break;
			case 1 :
				Intent intent2 = new Intent(getApplicationContext(),
						LeadActivity.class);
				startActivity(intent2);
				finish();
				break;
			case 2 :
				// Intent intent3 = new Intent(getApplicationContext(),
				// BrowseSuggestedAppListActivity.class);
				Intent intent3 = new Intent(getApplicationContext(),
						RecommendActivity.class);
				startActivity(intent3);
				finish();
				break;
		}

	}

	private void saveUserLog(int action) {
		// save user log
		// GeneralUtil.saveUserLogType2(Lead2Activity.this, 4, action);
		// if (action==0) {
		// tracker.trackPageView("/"+TAG);
		// }
		// else {
		// tracker.trackEvent(""+2, ""+4, "", action);
		// }
	}

	/** 添加快捷按钮到桌面 */
	private void addShortCut(String pkg_name, String class_name) {
		try {
			// The Intent of the shortcut
			Intent shortcut = new Intent(
					"com.android.launcher.action.INSTALL_SHORTCUT");
			// The name of the shortcut
			shortcut.putExtra(Intent.EXTRA_SHORTCUT_NAME,
					getString(R.string.app_name));
			// Does not allow to create duplicate
			shortcut.putExtra("duplicate", false);
			ComponentName comp = new ComponentName(pkg_name, "." + class_name);
			shortcut.putExtra(Intent.EXTRA_SHORTCUT_INTENT, new Intent(
					Intent.ACTION_MAIN).setComponent(comp));
			// Shortcut icon
			ShortcutIconResource iconRes = ShortcutIconResource
					.fromContext(Lead2Activity.this, R.drawable.app_icon);
			shortcut.putExtra(Intent.EXTRA_SHORTCUT_ICON_RESOURCE, iconRes);
			// send shortcut Broadcast
			sendBroadcast(shortcut);
		} catch (Exception e) {
			MyLog.e(TAG, e.toString());
		}
	}

	/** 添加快捷方式 */
	public boolean addShortcut(Context context, String pkg) {
		// 快捷方式名
		String title = "unknown";
		// MainActivity完整名
		String mainAct = null;
		// 应用图标标识
		int iconIdentifier = 0;
		// 根据包名寻找MainActivity
		PackageManager pkgMag = context.getPackageManager();
		Intent queryIntent = new Intent(Intent.ACTION_MAIN, null);
		queryIntent.addCategory(Intent.CATEGORY_LAUNCHER);
		List<ResolveInfo> list = pkgMag.queryIntentActivities(queryIntent,
				PackageManager.GET_ACTIVITIES);
		for (int i = 0; i < list.size(); i++) {
			ResolveInfo info = list.get(i);
			if (info.activityInfo.packageName.equals(pkg)) {
				title = info.loadLabel(pkgMag).toString();
				mainAct = info.activityInfo.name;
				iconIdentifier = info.activityInfo.applicationInfo.icon;
				break;
			}
		}
		if (mainAct == null) {
			// 没有启动类
			return false;
		}
		Intent shortcut = new Intent(
				"com.android.launcher.action.INSTALL_SHORTCUT");
		// 快捷方式的名称
		shortcut.putExtra(Intent.EXTRA_SHORTCUT_NAME, title);
		// 不允许重复创建
		// shortcut.putExtra("duplicate", false);
		ComponentName comp = new ComponentName(pkg, mainAct);
		shortcut.putExtra(Intent.EXTRA_SHORTCUT_INTENT, new Intent(
				Intent.ACTION_MAIN).setComponent(comp));
		// 快捷方式的图标
		Context pkgContext = null;
		if (context.getPackageName().equals(pkg)) {
			pkgContext = context;
		} else {
			// 创建第三方应用的上下文环境，为的是能够根据该应用的图标标识符寻找到图标文件。
			try {
				pkgContext = context.createPackageContext(pkg,
						Context.CONTEXT_IGNORE_SECURITY
						| Context.CONTEXT_INCLUDE_CODE);
			} catch (NameNotFoundException e) {
				e.printStackTrace();
			}
		}
		if (pkgContext != null) {
			ShortcutIconResource iconRes = ShortcutIconResource
					.fromContext(pkgContext, iconIdentifier);
			shortcut.putExtra(Intent.EXTRA_SHORTCUT_ICON_RESOURCE, iconRes);
		}
		// 发送广播，让接收者创建快捷方式
		// 需权限<uses-permission
		// android:name="com.android.launcher.permission.INSTALL_SHORTCUT" />
		context.sendBroadcast(shortcut);
		return true;
	}

}
