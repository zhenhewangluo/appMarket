package com.market.hjapp.ui.adapter;

import java.io.File;
import java.util.ArrayList;

import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.Recommend;
import com.market.hjapp.database.DatabaseUtils;

import android.content.Context;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.view.View;
import android.view.ViewGroup;
import android.widget.BaseAdapter;
import android.widget.Gallery;
import android.widget.ImageView;
/**
 * Gallery Adapter
 * @author Administrator
 *
 */
public class ImageAdapter extends BaseAdapter {
	   private static final String TAG = "ImageAdapter";
	    
	   private Context mContext; 
	   private ArrayList<Recommend> mData;
	   
       public ImageAdapter(Context c,ArrayList<Recommend> list) {  
           mContext = c;  
           mData =list;
       }  
       @Override
       public int getCount() {  
           return mData.size();  
       }  
       @Override
       public Object getItem(int position) {  
    	   return mData.get(position);  
       }  
       @Override
       public long getItemId(int position) {  
    	   return Long.parseLong(""+mData.get(position).getId());
       }  
       
       private ArrayList<View> ssItemView = new ArrayList<View>();;
         
       @Override
       public View getView(int position, View convertView, ViewGroup parent) {
    	   MyLog.d(TAG, "start showing app at " + position + ", convertview is " + convertView);
    	   
    	   if (position < ssItemView.size() && ssItemView.get(position) != null)
           	return ssItemView.get(position);
    	   
    	   ViewHolder holder;
    	   if (convertView==null) {
    		   holder=new ViewHolder();
    		   convertView=new ImageView(mContext);
    		   holder.icon=(ImageView)convertView;
    		   convertView.setTag(holder);
    	   }
    	   else {
    		   holder = (ViewHolder) convertView.getTag();
    	   }
    	   holder.icon.setAdjustViewBounds(true); 
//    	          holder.icon.setAlpha(80);
//    	          holder.icon.setLayoutParams(new Gallery.LayoutParams(  
//    	                   LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT));
    	   int BASE_WIDTH = 56;
    	   final float scale = mContext.getResources().getDisplayMetrics().density;
    	   int width = (int) (BASE_WIDTH * scale + 0.5f);

    	   holder.icon.setLayoutParams(new Gallery.LayoutParams(width, width));
    	   holder.icon.setScaleType(ImageView.ScaleType.FIT_XY); 
//          holder.icon.setPadding(5, 5, 5, 5);
//    	   holder.icon.setBackgroundResource(R.drawable.gallery_selector);  
    	   Recommend recommend = mData.get(position);
    	   MyLog.d(TAG, "name: " + recommend.getName());
    	   String iconUrl = recommend.getIconUrl();
    	   String iconpath = DatabaseUtils.getLocalPathFromUrl(iconUrl);
    	   if (GeneralUtil.needDisplayImg(mContext))
    	   {
    		   if (iconpath != null && !"".equals(iconpath) && new File(iconpath).exists()) {
    			   holder.icon.setImageBitmap(DatabaseUtils.getImage(iconpath));
    		   } else {
    			   // reset first
    			   holder.icon.setImageResource(R.drawable.app_icon);
    			   ImageLoader.getInstance().loadBitmapOnThread(iconUrl, mContext,  holder.icon);
    		   }
    	   }
    	   else{
    		   holder.icon.setImageResource(R.drawable.app_icon);
    	   }
    	   
    	   ssItemView.add(convertView); 
    	   
    	   return convertView;
    	   
    	   
//    	   ImageView icon = null;
//    	   if (convertView != null) {
//    		   icon = (ImageView) convertView;
//    	   } else {
//               icon = new ImageView(mContext);  
//               icon.setAdjustViewBounds(true); 
////             icon.setAlpha(80);
////             icon.setLayoutParams(new Gallery.LayoutParams(  
////                       LayoutParams.WRAP_CONTENT, LayoutParams.WRAP_CONTENT));
//               int BASE_WIDTH = 56;
//               final float scale = mContext.getResources().getDisplayMetrics().density;
//               int width = (int) (BASE_WIDTH * scale + 0.5f);
//               
//               icon.setLayoutParams(new Gallery.LayoutParams(width, width));
//               icon.setScaleType(ImageView.ScaleType.FIT_XY); 
////               icon.setPadding(5, 5, 5, 5);
//               icon.setBackgroundResource(R.drawable.gallery_selector);  
//    	   }
//
//    	   Recommend recommend = mData.get(position);
//           MyLog.d(TAG, "name: " + recommend.getName());
//           String iconUrl = recommend.getIconUrl();
//           String iconpath = DatabaseUtils.getLocalPathFromUrl(iconUrl);
//           if (GeneralUtil.needDisplayImg(mContext))
//           {
//   	        if (iconpath != null && !"".equals(iconpath) && new File(iconpath).exists()) {
//   	            Bitmap b = BitmapFactory.decodeFile(iconpath);
//   	            icon.setImageBitmap(b);
//   	        } else {
//   	            // reset first
//   	            icon.setImageResource(R.drawable.app_icon);
//   	            mImageLoader.loadBitmapOnThread(iconUrl, mContext, icon, ImageLoader.IMAGE_TYPE_CATEICON);
//   	        }
//           }
//           else{
//            	icon.setImageResource(R.drawable.app_icon);
//           }
//           return icon;  
    	   
       }   
       
      static class ViewHolder{
    	   ImageView icon;
   	  }
       public void setData(ArrayList<Recommend> imageList) {
           mData = imageList;
           
           notifyDataSetChanged();
       }
}
