
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

import com.market.hjapp.Category;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseUtils;

public class RankListAdapter extends BaseAdapter {
    private static final String TAG = "RankListAdapter";

    ArrayList<Category> mData;

    LayoutInflater mInflater;

    private Context mContext;
    
    public RankListAdapter(Context ctx) {
        mContext = ctx;
        
        mInflater = LayoutInflater.from(ctx);

        mData = new ArrayList<Category>();
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
		ViewHolder holder;//排行榜界面赋值
		if (convertView == null) {
			holder = new ViewHolder();
			convertView = mInflater.inflate(R.layout.rank_item, null);
			holder.icon = (ImageView) convertView.findViewById(R.id.icon);
			holder.cate_rank_name = (TextView) convertView
					.findViewById(R.id.cate_rank_name);
			holder.cate_rank_description = (TextView) convertView
					.findViewById(R.id.cate_rank_description);
			convertView.setTag(holder);
		} else {
			holder = (ViewHolder) convertView.getTag();
		}
		Category cate = mData.get(position);
		MyLog.d(TAG, "start showing app " + cate.getName());

		String iconUrl = cate.getIconUrl();
		String iconpath = DatabaseUtils.getLocalPathFromUrl(iconUrl);

//		if (GeneralUtil.needDisplayImg(mContext)) {
//			if (iconpath != null && !"".equals(iconpath)
//					&& new File(iconpath).exists()) {
//				holder.icon.setImageBitmap(DatabaseUtils.getImage(iconpath));
//			} else {
				// reset first
				holder.icon.setImageResource(R.drawable.app_icon);
				ImageLoader.getInstance().loadBitmapOnThread(iconUrl, mContext, holder.icon);
//			}
//		} else{
//			holder.icon.setImageResource(R.drawable.app_icon);
//		}

		// set cate_rank_name
		holder.cate_rank_name.setText(cate.getName());
		// set cate_rank_description
		holder.cate_rank_description.setText(cate.getDescription());
		return convertView;
		
		
		
//        View itemView;
//        if (convertView == null) {
//            itemView = mInflater.inflate(R.layout.rank_item, null);
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
//        // set cate_rank_name
//        TextView cate_rank_name = (TextView)itemView.findViewById(R.id.cate_rank_name);
//        cate_rank_name.setText(cate.getName());
//        
//        // set cate_rank_description
//        TextView  cate_rank_description= (TextView)itemView.findViewById(R.id.cate_rank_description);
//        cate_rank_description.setText(cate.getDescription());
//
//
//        return itemView;
    }
	static class ViewHolder {
		ImageView icon;
		TextView cate_rank_name;
		TextView  cate_rank_description;
	}
    public void setData(ArrayList<Category> data) {
        mData = data;
        notifyDataSetChanged();
    }

}
