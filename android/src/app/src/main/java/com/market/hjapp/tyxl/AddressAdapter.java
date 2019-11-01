package com.market.hjapp.tyxl;

import java.util.ArrayList;
import java.util.HashMap;
import java.util.Map;

import com.market.hjapp.R;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.CheckBox;
import android.widget.ImageView;
import android.widget.TextView;

public class AddressAdapter extends BaseAdapter {

	private Context context;
	private LayoutInflater mInflater;
	ArrayList<AddressInfo> itemList;
	public static Map<Integer, Boolean> isSelected;
	public boolean is_select[];

	public AddressAdapter(Context context, ArrayList<AddressInfo> itemList) {
		this.context = context;
		mInflater = LayoutInflater.from(context);
		this.itemList = itemList;
		is_select = new boolean[itemList.size()];
		init();
	}

	public void init() {
		isSelected = new HashMap<Integer, Boolean>();
		for (int i = 0; i < itemList.size(); i++) {
			isSelected.put(i, false);
		}
	}

	public ArrayList<AddressInfo> getItemList() {
		return itemList;
	}

	public void setItemList(ArrayList<AddressInfo> itemList) {
		this.itemList = itemList;
	}

	public int getCount() {
		return itemList.size();
	}

	public Object getItem(int position) {
		return position;
	}

	public long getItemId(int position) {
		return position;
	}

	ViewHolder holder;
	public View getView(int position, View convertView, ViewGroup parent) {
		final int pos = position;
		if (convertView == null) {
			convertView = mInflater.inflate(R.layout.address_list, null);
			holder = new ViewHolder();
			holder.id = (TextView) convertView.findViewById(R.id.id);
			holder.address = (TextView) convertView.findViewById(R.id.address);
			holder.receiver = (TextView) convertView
					.findViewById(R.id.receiver);
			holder.tel = (TextView) convertView.findViewById(R.id.tel);
			holder.check = (CheckBox) convertView.findViewById(R.id.check);
			holder.modifyMap = (ImageView) convertView
					.findViewById(R.id.modify_map);
			holder.deleteMap = (ImageView) convertView
					.findViewById(R.id.delete_map);

			convertView.setTag(holder);
		} else {
			holder = (ViewHolder) convertView.getTag();
		}
		holder.id.setText(itemList.get(position).getAddressId());
		holder.Id=itemList.get(position).getAddressId();
		holder.address.setText(itemList.get(position).getUserAddress());
		holder.receiver.setText(itemList.get(position).getReceiver());
		holder.tel.setText(itemList.get(position).getTel());
		holder.check.setChecked(isSelected.get(position));
		holder.modifyMap.setImageBitmap(itemList.get(position).getModifyMap());
		holder.modifyMap.setOnClickListener(new checkListener(position));
		holder.deleteMap.setImageBitmap(itemList.get(position).getDeleteMap());
		holder.deleteMap.setOnClickListener(new checkListener(position));
		return convertView;
	}

	class ViewHolder {
		TextView id;
		TextView address;
		TextView receiver;
		TextView tel;
		CheckBox check;
		ImageView modifyMap;
		ImageView deleteMap;
		String Id;
	}
	@Override
	public void notifyDataSetChanged() {
		super.notifyDataSetChanged();
	}

	class checkListener implements OnClickListener {
		private int position;
		checkListener(int pos) {
			position = pos;
		}

		@Override
		public void onClick(View view) {
			int vid = view.getId();
			if (vid == holder.modifyMap.getId()) {
				UpdateAddress.setAddressItem(position);
				UpdateAddress.address.gotoModify();
			}
			if (vid == holder.deleteMap.getId()) {
				UpdateAddress.setAddressItem(position);
				UpdateAddress.address.gotoDelete();
			}
		}
	}
}
