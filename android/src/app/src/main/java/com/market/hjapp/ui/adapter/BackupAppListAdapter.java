package com.market.hjapp.ui.adapter;

import java.io.File;
import java.util.ArrayList;

import com.market.hjapp.App;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;

import android.content.Context;
import android.database.sqlite.SQLiteDatabase;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.view.View.OnClickListener;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

public class BackupAppListAdapter extends BaseAdapter {
	private static final String TAG = "BackupAppListAdapter";

	private LayoutInflater mInflater;

	private Context mContext;
	protected SQLiteDatabase mDb;
	
	ArrayList<App> mData;


	public BackupAppListAdapter(Context context) {

		mContext = context;
		mInflater = LayoutInflater.from(context);

		mData = new ArrayList<App>();
	}

	@Override
	public int getCount() {
		// TODO Auto-generated method stub
		return mData.size();
	}

	@Override
	public Object getItem(int position) {
		// TODO Auto-generated method stub
		return mData.get(position);
	}

	@Override
	public long getItemId(int position) {
		// TODO Auto-generated method stub
		return mData.get(position).getId();
	}

	@Override
	public View getView(final int position, View convertView, ViewGroup parent) {
		// TODO Auto-generated method stub
		MyLog.d(TAG, "getView position=" + position);
		ViewHolder holder;
		if (convertView == null) {
			holder = new ViewHolder();
			convertView = mInflater.inflate(R.layout.backup_app_item, null);
			holder.icon = (ImageView) convertView.findViewById(R.id.icon);
			holder.author = (TextView) convertView.findViewById(R.id.author);
			holder.name = (TextView) convertView.findViewById(R.id.name);
			holder.is_backup_img=(ImageView) convertView.findViewById(R.id.is_backup_img);
			convertView.setTag(holder);
		} else {
			holder = (ViewHolder) convertView.getTag();
		}
		
			App app = mData.get(position);
			MyLog.d(TAG,"show app " + app.getId() + " status: " + app.getStatus());
			MyLog.d(TAG, "start showing app " + app.getName());

			// set icon
			String iconUrl = app.getIconUrl();
			String iconpath = DatabaseUtils.getLocalPathFromUrl(iconUrl);

			if (GeneralUtil.needDisplayImg(mContext)) {
				if (iconpath != null && !"".equals(iconpath)
						&& new File(iconpath).exists()) {
					holder.icon.setImageBitmap(DatabaseUtils.getImage(iconpath));
				} else {
					// reset first
					holder.icon.setImageResource(R.drawable.app_icon);
					ImageLoader.getInstance().loadBitmapOnThread(iconUrl, mContext,
							holder.icon);
				}
			} else {
				holder.icon.setImageResource(R.drawable.app_icon);
			}

			// set app count
			holder.author.setText(app.getAuthorName());

			// set name
			holder.name.setText(app.getName());
			
			//set is_check img
			MyLog.d(TAG, ":::isSelected["+position+"]:"+isSelected[position]);
			int resid=isSelected[position]==true?R.drawable.checked_img:R.drawable.no_checked_img;
			holder.is_backup_img.setImageResource(resid);
			holder.is_backup_img.setOnClickListener(new OnClickListener() {

				@Override
				public void onClick(View v) {

					saveUserLog(2);
					
			        isSelected[position]=!isSelected[position];
			        notifyDataSetChanged();
			        
				}
			});
			
		return convertView;
	}

	static class ViewHolder {
		ImageView icon;
		TextView author;
		TextView name;
		ImageView is_backup_img;
	}

	public boolean [] isSelected;
	public void setData(ArrayList<App> appList1) {
		 mData = appList1;
		 int count=mData.size();
		 isSelected=new boolean [count];
	     for (int i = 0; i < isSelected.length; i++) {
	    	 isSelected[i]=true;
		  }
		notifyDataSetChanged();
	}

	public void checkAll(boolean is_all_selected) {
		 isSelected=new boolean [mData.size()];
	     for (int i = 0; i < isSelected.length; i++) {
	    	 isSelected[i]=!is_all_selected;
		 }
		notifyDataSetChanged();
	}
	// save user log
	private void saveUserLog(int action) {
		GeneralUtil.saveUserLogType3(mContext, 43, action);
		
//		tracker.trackEvent(""+3, ""+42, "", action);
	}
}
