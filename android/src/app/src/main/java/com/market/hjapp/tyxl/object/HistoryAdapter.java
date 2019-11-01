package com.market.hjapp.tyxl.object;import java.util.List;import java.util.Map;import android.content.Context;import android.view.LayoutInflater;import android.view.View;import android.view.ViewGroup;import android.widget.BaseAdapter;import android.widget.ImageView;import android.widget.TextView;import com.market.hjapp.R;public class HistoryAdapter extends BaseAdapter{	private Context context;	private LayoutInflater layoutInflater;	List<Map<String, Object>> list;		public HistoryAdapter(Context context, List<Map<String, Object>> list) {		this.context = context;		layoutInflater = LayoutInflater.from(context);		this.list = list;	}		@Override	public int getCount() {		// TODO Auto-generated method stub		return this.list != null ? this.list.size() : 0;	}	@Override	public Object getItem(int position) {		// TODO Auto-generated method stub		return this.list.get(position);	}	@Override	public long getItemId(int position) {		// TODO Auto-generated method stub		return position;	}	@Override	public View getView(int position, View convertView, ViewGroup parent) {		// TODO Auto-generated method stub		ListItem listitem = null;		if (convertView == null) {			listitem = new ListItem();			convertView = layoutInflater.inflate(R.layout.invite_item, null);			listitem.name = (TextView) convertView.findViewById(R.id.name);//			listitem.number = (TextView) convertView.findViewById(R.id.number);//			listitem.check = (CheckBox) convertView.findViewById(R.id.check);			convertView.setTag(listitem);		} else {			listitem = (ListItem) convertView.getTag();		}		listitem.name.setText((String) list.get(position).get("name"));//		listitem.number.setText((String) list.get(position).get("number"));//		listitem.Id=(String) list.get(position).get("Id");//		listitem.check.setChecked(isSelected.get(position));//		listitem.check.setOnCheckedChangeListener(new checkChangeListener());		return convertView;	}	public class ListItem {		public ImageView photo;		public TextView name;		public TextView time;		public TextView state;	}}