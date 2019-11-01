
package com.market.hjapp.ui.adapter;

import java.io.File;
import java.util.ArrayList;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.ImageView;
import android.widget.TextView;

import com.market.hjapp.ChargeChannel;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;

public class ChargeListAdapter extends BaseAdapter {
    private static final String TAG = "ChargeListAdapter";

    ArrayList<ChargeChannel> mData;

    LayoutInflater mInflater;

    private Context mContext;
    
    public ChargeListAdapter(Context ctx) {
        mContext = ctx;
        
        mInflater = LayoutInflater.from(ctx);
        mData = new ArrayList<ChargeChannel>();
    }

    @Override
    public int getCount() {
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
    		convertView = mInflater.inflate(R.layout.charge_item, null);
    		holder.icon = (ImageView) convertView.findViewById(R.id.icon);
    		holder.name = (TextView) convertView.findViewById(R.id.name);
    		convertView.setTag(holder);
    	} else {
    		holder = (ViewHolder) convertView.getTag();
    	}
    	ChargeChannel chargeChannel = mData.get(position);
    	MyLog.d(TAG, "start showing charge channel " + chargeChannel.getName());

    	// set icon
    	String iconUrl = chargeChannel.getIconUrl();
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
    	holder.name.setText(chargeChannel.getName());
    	return convertView;
		
		
//        View itemView;
//        if (convertView == null) {
//            itemView = mInflater.inflate(R.layout.charge_item, null);
//        } else {
//            itemView = convertView;
//        }
//
//        ChargeChannel chargeChannel = mData.get(position);
//        MyLog.d(TAG, "start showing charge channel " + chargeChannel.getName());
//        
//        // set icon
//        ImageView icon = (ImageView)itemView.findViewById(R.id.icon);
//        
//        String iconUrl = chargeChannel.getIconUrl();
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
//        name.setText(chargeChannel.getName());
//        
//
//        return itemView;
    }
	static class ViewHolder {
		ImageView icon;
		TextView name;
	}
    public void setData(ArrayList<ChargeChannel> data) {
        mData = data;
        notifyDataSetChanged();
    }
    

}
