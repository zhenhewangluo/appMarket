package com.market.hjapp.ui.adapter;

import java.util.ArrayList;

import android.R.color;
import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.drawable.BitmapDrawable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.market.hjapp.R;
import com.market.hjapp.ui.view.AppItemView;

/**
 * 兴趣分类适配器
 * 
 * @author herokf
 * @since 2012.05.18
 * 
 */
public class InterestListAdapter extends BaseAdapter {
	private static final String TAG = "InterestListAdapter";

	private LayoutInflater mInflater;
	public ArrayList<InterestSort> mData=null;
	public ArrayList<InterestSort> getDataList() {
		return mData;
	}

	public void setDataList(ArrayList<InterestSort> mData) {
		this.mData = mData;
	}
	public int lastcount = 0;

	public InterestListAdapter(Context context) {
		mInflater = LayoutInflater.from(context);
		mData = new ArrayList<InterestSort>();
	}

	public InterestListAdapter(Context context, ArrayList<InterestSort> appList) {
		mInflater = LayoutInflater.from(context);
		mData = appList;
		if (null==mData) {
			mData = new ArrayList<InterestSort>();
		}
	}

	public int getLastCount() {
		return lastcount;
	}

	@Override
	public int getCount() {
		if (mData == null) {
			return 0;
		}
		return mData.size();
	}

	@Override
	public Object getItem(int pos) {
		return mData.get(pos);
	}

	@Override
	public long getItemId(int pos) {
		if (null==mData||mData.size()==0||pos<0||pos>=mData.size()) {
			return 0;
		}else{
			return mData.get(pos).getId();
		}
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		ViewHolder holder;

		if (convertView == null) {
			holder = new ViewHolder();
			convertView = (View) mInflater.inflate(
					R.layout.interest_sort_item, null);
			holder.bgView = convertView.findViewById(R.id.bg);
			holder.sortIconView = (ImageView)convertView.findViewById(R.id.image);
			holder.sortNameView = (TextView)convertView.findViewById(R.id.text);
			convertView.setTag(holder);

		} else {
			holder = (ViewHolder) convertView.getTag();
		}
//		holder.sortIconView.setBackgroundDrawable(mData.get(position)
//				.getInterestIcon().getBackground());
		if (null!=mData.get(position).getInterestIcon()) {
			holder.sortIconView.setBackgroundDrawable(new BitmapDrawable(mData.get(position).getInterestIcon()));
		}else{
		}
		holder.sortNameView.setText(mData.get(position).getInterestName());
		if (mData.get(position).isSelect()) {
			holder.bgView.setBackgroundResource(R.drawable.favorite_select);
		}else{
			holder.bgView.setBackgroundColor(color.transparent);
		}
		return convertView;

	}

	static class ViewHolder {
		ImageView sortIconView;
		TextView sortNameView;
		View bgView;
	}

	/**
	 * 一项兴趣分类
	 * 
	 * @author Administrator
	 * 
	 */
	public static class InterestSort {
		Bitmap interestIcon;
		String interestName;
		int id;
		boolean select;// 是否被选中

		public Bitmap getInterestIcon() {
			return interestIcon;
		}
		public void setInterestIcon(Bitmap interestIcon) {
			this.interestIcon = interestIcon;
		}
		public String getInterestName() {
			return interestName;
		}
		public void setInterestName(String interestName) {
			this.interestName = interestName;
		}
		public int getId() {
			return id;
		}
		public void setId(int id) {
			this.id = id;
		}
		public boolean isSelect() {
			return select;
		}
		public void setSelect(boolean select) {
			this.select = select;
		}

	}
	/**
	 * 追加数据到列表 但是清除之前数据
	 * 
	 * @param sortList
	 */
	public void setData(ArrayList<InterestSort> sortList) {
		if (null!=sortList) {
			mData.clear();
			mData.addAll(sortList);
			lastcount = mData.size();
			notifyDataSetChanged();
		}
	}
}
