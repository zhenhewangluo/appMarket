package com.market.hjapp.tyxl.adapter;

import java.util.List;
import java.util.Map;
import android.content.Context;
import android.graphics.drawable.Drawable;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseExpandableListAdapter;
import android.widget.ExpandableListView;
import android.widget.ImageView;
import android.widget.TextView;


import com.market.hjapp.R;
import com.market.hjapp.tyxl.AsyncImageLoader;
import com.market.hjapp.tyxl.AsyncImageLoader.ImageCallback;
public class ExpandableListAdapter extends BaseExpandableListAdapter {

	private List<? extends Map<String, ?>> mGroupData;
	private int mExpandedGroupLayout;
	private int mCollapsedGroupLayout;
	private String[] mGroupFrom;
	private int[] mGroupTo;
	private List<? extends List<? extends Map<String, ?>>> mChildData;
	private int mChildLayout;
	private int mLastChildLayout;
	private String[] mChildFrom;
	private int[] mChildTo;
	private LayoutInflater mInflater;
	private AsyncImageLoader asyncImageLoader;
	ExpandableListView elv;

	public ExpandableListAdapter(Context context,
			List<? extends Map<String, ?>> groupData, int groupLayout,
			String[] groupFrom, int[] groupTo,
			List<? extends List<? extends Map<String, ?>>> childData,
			int childLayout, String[] childFrom, int[] childTo,
			ExpandableListView elv) {
		this(context, groupData, groupLayout, groupLayout, groupFrom, groupTo,
				childData, childLayout, childLayout, childFrom, childTo, elv);
	}

	public ExpandableListAdapter(Context context,
			List<? extends Map<String, ?>> groupData, int expandedGroupLayout,
			int collapsedGroupLayout, String[] groupFrom, int[] groupTo,
			List<? extends List<? extends Map<String, ?>>> childData,
			int childLayout, int lastChildLayout, String[] childFrom,
			int[] childTo, ExpandableListView elv) {
		mGroupData = groupData;
		mExpandedGroupLayout = expandedGroupLayout;
		mCollapsedGroupLayout = collapsedGroupLayout;
		mGroupFrom = groupFrom;
		mGroupTo = groupTo;
		mChildData = childData;
		mChildLayout = childLayout;
		mLastChildLayout = lastChildLayout;
		mChildFrom = childFrom;
		mChildTo = childTo;
		mInflater = (LayoutInflater) context
				.getSystemService(Context.LAYOUT_INFLATER_SERVICE);
		asyncImageLoader = new AsyncImageLoader();
		this.elv = elv;
	}

	@Override
	public Object getChild(int groupPosition, int childPosition) {
		// TODO Auto-generated method stub
		// 取得与指定分组、指定子项目关联的数据。
		return mChildData.get(groupPosition).get(childPosition);
	}

	@Override
	public long getChildId(int groupPosition, int childPosition) {
		// TODO Auto-generated method stub
		// 取得给定分组中给定子视图的ID。 该组ID必须在组中是唯一的。组合的ID （参见getCombinedGroupId(long)）
		// 必须不同于其他所有ID（分组及子项目的ID）。
		return childPosition;
	}

	@Override
	public View getChildView(int groupPosition, int childPosition,
			boolean isLastChild, View convertView, ViewGroup parent) {
		// TODO Auto-generated method stub
		// 取得显示给定分组给定子位置的数据用的视图。
		View v;
		if (convertView == null) {
			v = newChildView(isLastChild, parent);
		} else {
			v = convertView;
		}
		bindChildView(v, mChildData.get(groupPosition).get(childPosition),
				mChildFrom, mChildTo);
		return v;
	}

	@Override
	public int getChildrenCount(int groupPosition) {
		// TODO Auto-generated method stub
		// 取得指定分组的子元素数。
		return mChildData.get(groupPosition).size();
	}

	@Override
	public Object getGroup(int groupPosition) {
		// 取得与给定分组关联的数据。
		return mGroupData.get(groupPosition);
	}

	@Override
	public int getGroupCount() {
		// 取得分组数
		return mChildData.size();
	}

	@Override
	public long getGroupId(int groupPosition) {
		// 取得指定分组的ID。该组ID必须在组中是唯一的。组合的ID （参见getCombinedGroupId(long)）
		// 必须不同于其他所有ID（分组及子项目的ID）。
		return groupPosition;
	}

	@Override
	public View getGroupView(int groupPosition, boolean isExpanded,
			View convertView, ViewGroup parent) {
		// 取得用于显示给定分组的视图。 这个方法仅返回分组的视图对象， 要想获取子元素的视图对象，
		// 就需要调用 getChildView(int, int, boolean, View, ViewGroup)。
		View v;
		if (convertView == null) {
			v = newGroupView(isExpanded, parent);
		} else {
			v = convertView;
		}
		bindGroupView(v, mGroupData.get(groupPosition), mGroupFrom, mGroupTo);
		return v;
	}

	@Override
	public boolean hasStableIds() {
		// 是否指定分组视图及其子视图的ID对应的后台数据改变也会保持该ID。
		return true;
	}

	@Override
	public boolean isChildSelectable(int groupPosition, int childPosition) {
		// 指定位置的子视图是否可选择。
		return true;
	}

	// 创建新的组视图
	public View newGroupView(boolean isExpanded, ViewGroup parent) {
		return mInflater.inflate((isExpanded) ? mExpandedGroupLayout
				: mCollapsedGroupLayout, parent, false);
	}

	// 创建新的子视图
	public View newChildView(boolean isLastChild, ViewGroup parent) {
		return mInflater.inflate((isLastChild) ? mLastChildLayout
				: mChildLayout, parent, false);
	}

	// 绑定组数据
	private void bindGroupView(View view, Map<String, ?> data, String[] from,
			int[] to) {
		// 绑定组视图的数据，针对Group的Layout都是TextView的情况
		int len = to.length;
		for (int i = 0; i < len; i++) {
			TextView v = (TextView) view.findViewById(to[i]);
			if (v != null) {
				v.setText((String) data.get(from[i]));
			}
		}
	}

	// 绑定子数据
	private void bindChildView(View view, Map<String, ?> data, String[] from,
			int[] to) {
		// R.id.time, R.id.name, R.id.jiangpin,R.id.state
		TextView time = (TextView) view.findViewById(to[0]);
		if (time != null) {
			time.setText("兑换时间：" + (String) data.get(from[0]));
		}
		TextView name = (TextView) view.findViewById(to[1]);
		if (name != null) {
			name.setText((String) data.get(from[1]));
		}
		ImageView jiangpin = (ImageView) view.findViewById(to[2]);
		if (jiangpin != null) {
			// name.setText((String) data.get(from[2]));
			// ImageView imageView = viewCache.getImageView();
			String imageUrl = (String) data.get(from[2]);
			jiangpin.setTag(imageUrl);

			Drawable cachedImage = asyncImageLoader.loadDrawable(imageUrl,
					new ImageCallback() {
						public void imageLoaded(Drawable imageDrawable,
								String imageUrl) {
							ImageView imageViewByTag = (ImageView) elv
									.findViewWithTag(imageUrl);
							if (imageViewByTag != null) {
								imageViewByTag.setImageDrawable(imageDrawable);
							}
						}
					});
			if (cachedImage == null) {
				jiangpin.setImageResource(R.drawable.default_image);
			} else {
				jiangpin.setImageDrawable(cachedImage);
			}
		}
		TextView state = (TextView) view.findViewById(to[3]);
		if (state != null) {
			int flag = Integer.parseInt((String) data.get(from[3]));
			if (flag == 0) {
				state.setText("发货状态：待确定");
			}
			if (flag == 1) {
				state.setText("发货状态：未发货");
			}
			if (flag == 2) {
				state.setText("发货状态：已发货");
			}
		}
	}
}
