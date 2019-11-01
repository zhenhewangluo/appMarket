package com.market.hjapp.tyxl.adapter;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import com.market.hjapp.R;
import com.market.hjapp.tyxl.object.Friend;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.TextView;

public class SupportFriendAdapter extends BaseAdapter {
	private Context context;
	private LayoutInflater layoutInflater;
	ArrayList<Friend> list;

	class ListItem {
		public TextView name;
		public TextView number;
		public TextView text;
	}

	public SupportFriendAdapter(Context context, ArrayList<Friend> list) {
		this.context = context;
		layoutInflater = LayoutInflater.from(context);
		this.list = list;
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub
		return this.list != null ? this.list.size() : 0;
	}

	@Override
	public Object getItem(int position) {
		// TODO Auto-generated method stub
		return this.list.get(position);
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub
		return position;
	}

	@Override
	public View getView(int position, View convertView, ViewGroup parent) {
		ListItem listitem = null;
		if (convertView == null) {
			listitem = new ListItem();
			convertView = layoutInflater.inflate(R.layout.support_item, null);
			listitem.name = (TextView) convertView.findViewById(R.id.textView1);
			listitem.number = (TextView) convertView
					.findViewById(R.id.textView2);
			listitem.text = (TextView) convertView.findViewById(R.id.textView3);
			convertView.setTag(listitem);
		} else {
			listitem = (ListItem) convertView.getTag();
		}
		listitem.name.setText(list.get(position).friedsName);
		listitem.number.setText(list.get(position).friedsNumber);
		if (list.get(position).isSupport == 1) {
			listitem.text.setText("已投票");
		} else {
			listitem.text.setText("请支持我");
		}
		return convertView;
	}
}
