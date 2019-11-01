package com.market.hjapp.tyxl.adapter;

import java.util.ArrayList;
import java.util.List;
import java.util.Map;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.BaseAdapter;
import android.widget.CheckBox;
import android.widget.TextView;

import com.market.hjapp.R;
import com.market.hjapp.tyxl.object.Friend;

public class InviteFriendAdapter extends ArrayAdapter {

	public InviteFriendAdapter(Context context, ArrayList<Friend>  list) {
		super(context, 0, 0, list);
		this.context = context;
		layoutInflater = LayoutInflater.from(context);
		this.list = list;
	}

	private Context context;
	private LayoutInflater layoutInflater;
	ArrayList<Friend>  list;

	public class ListItem {
		public TextView name;
		public TextView number;
		public CheckBox check;
		public String Id;
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
		// TODO Auto-generated method stub
		ListItem listitem = null;
		if (convertView == null) {
			listitem = new ListItem();
			convertView = layoutInflater.inflate(R.layout.invite_item, null);
			listitem.name = (TextView) convertView.findViewById(R.id.name);
			listitem.number = (TextView) convertView.findViewById(R.id.number);
			listitem.check = (CheckBox) convertView.findViewById(R.id.check);
			convertView.setTag(listitem);
		} else {
			listitem = (ListItem) convertView.getTag();
		}
		listitem.name.setText((String) list.get(position).friedsName);
		listitem.number.setText((String) list.get(position).friedsNumber);
		listitem.Id= list.get(position).friedsId;
		listitem.check.setChecked(list.get(position).isChecked);   
		return convertView;
	}

	@Override
	public void notifyDataSetChanged() {
		// TODO Auto-generated method stub
		super.notifyDataSetChanged();
	}

	
	
}
