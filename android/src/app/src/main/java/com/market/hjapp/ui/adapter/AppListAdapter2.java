
package com.market.hjapp.ui.adapter;

import java.util.ArrayList;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;

import com.market.hjapp.App;
import com.market.hjapp.R;
import com.market.hjapp.ui.view.AppItemView;

public class AppListAdapter2 extends BaseAdapter
{
	private static final String TAG = "AppListAdapter";

	private LayoutInflater mInflater;
	public ArrayList<App> mData = null;
	public int lastcount = 0;

	public AppListAdapter2(Context context)
	{
		mInflater = LayoutInflater.from(context);

		// mData = new ArrayList<App>();
	}

	public AppListAdapter2(Context context, ArrayList<App> appList)
	{
		mInflater = LayoutInflater.from(context);
		// mData = new ArrayList<App>();
		mData = appList;
		// lastcount = appList.size();
	}

	public int getLastCount()
	{
		return lastcount;
	}

	@Override
	public int getCount()
	{
		// MyLog.d(TAG, "data count: " + mData.size());
		if (mData == null)
		{
			return 0;
		}
		return mData.size();
	}

	@Override
	public Object getItem(int pos)
	{
		return mData.get(pos);
	}

	@Override
	public long getItemId(int pos)
	{
		return mData.get(pos).getId();
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent)
	{
		ViewHolder holder;

		if (convertView == null)
		{
			holder = new ViewHolder();
			convertView = (AppItemView) mInflater.inflate(R.layout.app_item,
					null);
			// ((AppItemView)convertView).updateView(mData.get(position));
			holder.itemView = (AppItemView) convertView;

			convertView.setTag(holder);

		} else
		{
			holder = (ViewHolder) convertView.getTag();
		}

		// MyLog.d(TAG, "show app " + app.getId() + " status: " +
		// app.getStatus());
		// MyLog.d(TAG, "Position "+ position);
		holder.itemView.updateView(mData.get(position));
		return convertView;

	}

	static class ViewHolder
	{
		AppItemView itemView;
	}

	public void setData(ArrayList<App> appList)
	{
		// mData.clear();
		mData = appList;
		lastcount = appList.size();
		notifyDataSetChanged();
	}
}
