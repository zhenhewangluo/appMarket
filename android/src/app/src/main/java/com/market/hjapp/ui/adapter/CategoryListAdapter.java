
package com.market.hjapp.ui.adapter;

import java.io.File;
import java.util.ArrayList;

import android.R.integer;
import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.market.hjapp.Category;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;

public class CategoryListAdapter extends BaseAdapter {
    private static final String TAG = "CategoryListAdapter";

    ArrayList<Category> mData=null;
    int lastcount=0;
    LayoutInflater mInflater;

    private Context mContext;
    
    public CategoryListAdapter(Context ctx) {
        mContext = ctx;
        
        mInflater = LayoutInflater.from(ctx);

        mData = null;
    }
    public int getLastCount() {
		return lastcount;
}
    @Override
    public int getCount() {
    	if(mData == null)
    		return 0;
    	else
    		return mData.size();
    }

    @Override
    public Object getItem(int pos) {
        return mData.get(pos);
    }

    @Override
    public long getItemId(int pos) {
        return pos;
    }

    @Override
    public View getView(int position, View convertView, ViewGroup parent) {
    	ViewHolder holder;
		if (convertView == null) {
			holder = new ViewHolder();
			convertView = mInflater.inflate(R.layout.cate_item, null);
			holder.icon = (ImageView)convertView.findViewById(R.id.icon);
			holder.name = (TextView) convertView.findViewById(R.id.name);
			holder.description = (TextView) convertView.findViewById(R.id.cate_description);
			holder.cnt = (TextView) convertView.findViewById(R.id.app_cnt);
			convertView.setTag(holder);
		} else {
			holder = (ViewHolder) convertView.getTag();
		}
		
		Category cate = mData.get(position);
		MyLog.d(TAG, "start showing app " + cate.getName());

		// set icon
		String iconUrl = cate.getIconUrl();
		String iconpath = DatabaseUtils.getLocalPathFromUrl(iconUrl);

		if (GeneralUtil.needDisplayImg(mContext))
		{
			if (iconpath != null && !"".equals(iconpath) && new File(iconpath).exists()) {
				holder.icon.setImageBitmap(DatabaseUtils.getImage(iconpath));
			} else {
				// reset first
				holder.icon.setImageResource(R.drawable.app_icon);
				ImageLoader.getInstance().loadBitmapOnThread(iconUrl, mContext, holder.icon);
			}
		}
		else{
			holder.icon.setImageResource(R.drawable.app_icon);
		}

		// set name
		holder.name.setText(cate.getName());
		
		holder.description.setText(cate.getDescription());

		// set app count
		holder.cnt.setText(mContext.getString(R.string.cate_app_count, cate.getAppCount()));
		
    	return convertView;
    	
    	
//        View itemView;
//        if (convertView == null) {
//            itemView = mInflater.inflate(R.layout.cate_item, null);
//        } else {
//            itemView = convertView;
//        }
//
//        Category cate = mData.get(position);
//        MyLog.d(TAG, "start showing app " + cate.getName());
//        
//        // set icon
//        ImageView icon = (ImageView)itemView.findViewById(R.id.icon);
//        
//        String iconUrl = cate.getIconUrl();
//        String iconpath = DatabaseUtils.getLocalPathFromUrl(iconUrl);
//        
//        if (GeneralUtil.needDisplayImg(mContext))
//        {
//	        if (iconpath != null && !"".equals(iconpath) && new File(iconpath).exists()) {
//	            Bitmap b = BitmapFactory.decodeFile(iconpath);
//	            icon.setImageBitmap(b);
//	        } else {
//	            // reset first
//	            icon.setImageResource(R.drawable.app_icon);
//	            mImageLoader.loadBitmapOnThread(iconUrl, mContext, icon, ImageLoader.IMAGE_TYPE_CATEICON);
//	        }
//        }
//        else
//        	icon.setImageResource(R.drawable.app_icon);
//        
//        // set name
//        TextView name = (TextView)itemView.findViewById(R.id.name);
//        name.setText(cate.getName());
//        
//        // set newest app name in app_item
//        TextView newAppName = (TextView)itemView.findViewById(R.id.newest_app_name);
//        newAppName.setText(mContext.getString(R.string.cate_newest_app_name, cate.getNewAppName()));
//        
//        // set app count
//        TextView cnt = (TextView)itemView.findViewById(R.id.app_cnt);
//        cnt.setText(mContext.getString(R.string.cate_app_count, cate.getAppCount()));
//
//        return itemView;
    }
    static class ViewHolder {
		ImageView icon;
		TextView name;
		TextView description;
		TextView cnt;
	}
    public void setData(ArrayList<Category> data) {
        mData = data;
        lastcount = data.size();
        notifyDataSetChanged();
    }

}
