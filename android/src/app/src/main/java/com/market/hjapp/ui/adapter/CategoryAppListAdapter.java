package com.market.hjapp.ui.adapter;

import java.util.ArrayList;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

import com.market.hjapp.App;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.ui.view.AppItemView;

public class CategoryAppListAdapter extends BaseAdapter {
	private static final String TAG = "CategoryAppListAdapter";

	private LayoutInflater mInflater;
	private ArrayList<App> mData;
    int lastcount=0;
	public CategoryAppListAdapter(Context context) {
		mInflater = LayoutInflater.from(context);

		mData = new ArrayList<App>();
	}
    public int getLastCount() {
		return lastcount;
}
	@Override
	public int getCount() {
		// MyLog.d(TAG, "data count: " + mData.size());
		//return mData.size();// + 1;
    	if(mData == null)
    		return 0;
    	else
    		return mData.size();
	}

	@Override
	public Object getItem(int pos) {
//		if (pos > 0)
			return mData.get(pos );//- 1);
//		else
//			return null;
	}

	@Override
	public long getItemId(int pos) {
//		if (pos > 0)
			return mData.get(pos).getId();//- 1).getId();
//		else
//			return 0;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		MyLog.d(TAG, "getView>>>>>>>>>>>>>>>>>>>>>>>>>>>");
//		if (position == 0) {
//			TextView tv;
//			if (convertView == null || !(convertView instanceof TextView)) {
//				tv = (TextView) mInflater.inflate(R.layout.list_top_textview,
//						null);
//			} else
//				tv = (TextView) convertView;
//
//			tv.setText(mDesc);
//
//			return tv;
//		}

		ViewHolder holder;
		if (convertView == null ){//|| (convertView instanceof TextView)) {
			holder = new ViewHolder();

			convertView = mInflater.inflate(R.layout.app_item2, null);
			holder.itemView = (AppItemView) convertView;

			convertView.setTag(holder);
		} else {
			holder = (ViewHolder) convertView.getTag();
		}
		App app = mData.get(position);// - 1);

		if (app == null)
			return holder.itemView;

		MyLog.d(TAG, "show app " + app.getId() + " status: " + app.getStatus());
		((AppItemView) holder.itemView).updateView(app);

		return convertView;

		// if (position == 0 )
		// {
		// TextView tv;
		// if (convertView == null || !(convertView instanceof TextView))
		// {
		// tv = (TextView)mInflater.inflate(R.layout.list_top_textview, null);
		// }
		// else
		// tv = (TextView)convertView;
		//
		// tv.setText(mDesc);
		//
		// return tv;
		// }
		//
		// View itemView;
		// if (convertView == null ||(convertView instanceof TextView)) {
		// itemView = mInflater.inflate(R.layout.app_item, null);
		// } else {
		// itemView = (AppItemView)convertView;
		// }
		//
		// App app = mData.get(position-1);
		//
		// if (app == null)
		// return itemView;
		//
		// MyLog.d(TAG, "show app " + app.getId() + " status: " +
		// app.getStatus());
		// ((AppItemView) itemView).updateView(app);
		//
		// return itemView;
	}

	static class ViewHolder {
		AppItemView itemView;
	}

	public void setData(ArrayList<App> appList) {
		mData = appList;
        lastcount = appList.size();
		notifyDataSetChanged();
	}

	private String mDesc;

	public void setTitleDesc(String desc) {
		mDesc = desc;
	}

}
