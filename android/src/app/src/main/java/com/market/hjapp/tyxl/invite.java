package com.market.hjapp.tyxl;

import java.util.List;
import java.util.Map;


import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ArrayAdapter;
import android.widget.CheckBox;
import android.widget.TextView;

import com.market.hjapp.R;
import com.market.hjapp.tyxl.adapter.InviteFriendAdapter.ListItem;
public class invite extends ArrayAdapter{

	private Context context;
	private LayoutInflater layoutInflater;
	List<Map<String, Object>> list;

	public class ListItem {
		public TextView name;
		public TextView number;
		public CheckBox check;
		public String Id;
	}
	public invite(Context context, int textViewResourceId, List<Map<String, Object>> list) {
		super(context, 0, list);
		this.context = context;
		layoutInflater = LayoutInflater.from(context);
		this.list = list;
		// TODO Auto-generated constructor stub
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
		listitem.name.setText((String) list.get(position).get("name"));
		listitem.number.setText((String) list.get(position).get("number"));
		listitem.Id=(String) list.get(position).get("Id");
		listitem.check.setChecked((Boolean) list.get(position).get("isChecked"));
		return convertView;
	}

}
