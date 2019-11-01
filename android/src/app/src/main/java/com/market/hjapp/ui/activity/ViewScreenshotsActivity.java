
package com.market.hjapp.ui.activity;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.net.Uri;
import android.os.Bundle;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.AdapterView;
import android.widget.AdapterView.OnItemSelectedListener;
import android.widget.BaseAdapter;
import android.widget.Gallery;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.tasks.LoadScreenshotTask;
import com.market.hjapp.ui.view.ScreenshotIndicatorsView;

public class ViewScreenshotsActivity extends BaseActivity
{
	protected static final String TAG = "ViewScreenshotsActivity";

	private ScreenshotAdapter mAdapter = new ScreenshotAdapter();
	private LayoutInflater mInflater;
	private ScreenshotIndicatorsView mScreenshotIndicators;
	private View mLoadingView;
	private View mGalleryView;

	private TaskResultListener mTaskResultListener = new TaskResultListener()
	{

		@SuppressWarnings("unchecked")
		@Override
		public void onTaskResult(boolean success, HashMap<String, Object> res)
		{
			if (!success)
			{
				if (res == null)
				{
					Toast.makeText(ViewScreenshotsActivity.this,
							R.string.error_http_timeout, Toast.LENGTH_LONG)
							.show();
					finish();
				}

				MyLog.e(TAG, "get screenshot failed!");
				return;
			}

			ArrayList<String> urlList1 = (ArrayList<String>) res.get("list");

			String ssUrl = urlList1.toString();
			if (ssUrl.contains(","))
			{
				ssUrl = ssUrl.replace(",", ";");
			}
			if (ssUrl.contains("["))
			{
				ssUrl = ssUrl.replace("[", "");
			}
			if (ssUrl.contains("]"))
			{
				ssUrl = ssUrl.replace("]", "");
			}
			String url[] = ssUrl.split(";");

			Uri uri = Uri.parse(url[0]);
			String baseUrl = uri.getScheme() + "://" + uri.getAuthority() + "/";
			ArrayList<String> urlList = new ArrayList<String>();
			urlList.add(url[0]);
			for (int i = 1; i < url.length; i++)
			{
				urlList.add(baseUrl + url[i]);
			}

			// urlList.add("http://img.hjapk.com/3698/screenShot/3698_0.jpg");
			// urlList.add("http://img.hjapk.com/3698/screenShot/3698_1.jpg");
			// urlList.add("http://img.hjapk.com/3698/screenShot/3698_2.jpg");

			mAdapter.setData(urlList);
			MyLog.d(TAG, "data size: " + urlList.size());
			mScreenshotIndicators.setIndicatorCount(mAdapter.getCount());

			ssItemView = new ArrayList<View>();

			mGallery.setAdapter(mAdapter);
			MyLog.d(TAG, "====== mAdapter ====");
		}

	};

	private Gallery mGallery;
	private ArrayList<View> ssItemView;

	@Override
	protected void onCreate(Bundle savedInstanceState)
	{
		super.onCreate(savedInstanceState);

		setContentView(R.layout.screenshot);

		mInflater = LayoutInflater.from(this);

		// set title
		TextView title = (TextView) findViewById(R.id.top_title);
		title.setText(getIntent().getStringExtra(EXTRA_KEY_PARENTNAME));

		mScreenshotIndicators = (ScreenshotIndicatorsView) findViewById(R.id.screenshot_indicators);

		mGallery = (Gallery) findViewById(R.id.screenshot_gallery);
		mGallery.setOnItemSelectedListener(new OnItemSelectedListener()
		{

			@Override
			public void onItemSelected(AdapterView<?> arg0, View arg1,
					int arg2, long arg3)
			{
				MyLog.d(TAG, "====== onItemSelected ====");

				mScreenshotIndicators.setHightlightIndicator(arg2);
			}

			@Override
			public void onNothingSelected(AdapterView<?> arg0)
			{
				// Do nothing
			}

		});

		int appid = getIntent().getIntExtra("appid", -1);

		try
		{
			new LoadScreenshotTask(this, mTaskResultListener).execute(appid
					+ "");
		} catch (RejectedExecutionException e)
		{
			MyLog.e(TAG, "Got exception when execute asynctask!", e);
		}

		MyLog.d(TAG, "====== Oncreate ====");
	}

	private class ScreenshotAdapter extends BaseAdapter
	{
		private ArrayList<String> mData = new ArrayList<String>();

		@Override
		public int getCount()
		{
			return mData.size();
		}

		@Override
		public Object getItem(int position)
		{
			return position;
		}

		@Override
		public long getItemId(int position)
		{
			return position;
		}

		@Override
		public View getView(int position, View convertView, ViewGroup parent)
		{
			View screenshot;

			MyLog.d(TAG, "=================================getView" + position);

			if (position < ssItemView.size()
					&& ssItemView.get(position) != null)
				return ssItemView.get(position);

			if (convertView != null)
			{
				screenshot = convertView;
			} else
			{
				screenshot = mInflater.inflate(R.layout.screenshot_item, null);
			}

			ImageView image = (ImageView) screenshot.findViewById(R.id.img);

			String imageUrl = mData.get(position);
			String imagePath = DatabaseUtils.getLocalPathFromUrl(imageUrl);

			MyLog.d(TAG, "=================================position:"
					+ position);
			MyLog.d(TAG, "=================================convertView:"
					+ convertView);
			MyLog.d(TAG, "=================================image:" + image);

			if (GeneralUtil.needDisplayImg(ViewScreenshotsActivity.this))
			{
				// if (imagePath!= null && !imagePath.equals("") && new
				// File(imagePath).exists()) {
				// image.setImageBitmap(DatabaseUtils.getImage(imagePath));
				// } else {
				image.setImageResource(R.drawable.default_screenshot);
				ImageLoader.getInstance().loadBitmapOnThread(
						mData.get(position), ViewScreenshotsActivity.this,
						image);
				// }
			} else
				image.setImageResource(R.drawable.default_screenshot);

			ssItemView.add(screenshot);

			return screenshot;
		}

		public void setData(ArrayList<String> data)
		{
			mData = data;
		}
	}
}
