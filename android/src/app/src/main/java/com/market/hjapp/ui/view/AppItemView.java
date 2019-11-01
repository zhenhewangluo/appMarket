
package com.market.hjapp.ui.view;

import java.io.File;

import android.content.Context;
import android.content.res.Resources;
import android.database.Cursor;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.util.AttributeSet;
import android.view.View;
import android.widget.ImageView;
import android.widget.LinearLayout;
import android.widget.TextView;

import com.market.hjapp.App;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.database.DatabaseSchema;
import com.market.hjapp.database.DatabaseUtils;

public class AppItemView extends LinearLayout {

    private static final String TAG = "AppItemView";
    private ImageView mIcon;
    private TextView mAuthor;
    private TextView mName;
//    private TextView mSlogan;
    private RatingStars mRating;
    private TextView mPrice;
    private TextView mTime;
    
    private View view_no_download_time,view_show_download_time,view_no_download_time_app_info,view_show_download_time_app_info;
    private TextView mNickname;
    private TextView mName1;
//    private TextView mSlogan1;
    private TextView mPrice1;
    
    private Context mContext;
    
    
    public AppItemView(Context context, AttributeSet attrs) {
        super(context, attrs);
        
        mContext = context;
        
    }

    @Override
    protected void onFinishInflate() {
        super.onFinishInflate();
        mIcon = (ImageView)findViewById(R.id.icon);
        mAuthor = (TextView)findViewById(R.id.author);
        mName = (TextView)findViewById(R.id.name);
//        mSlogan = (TextView)findViewById(R.id.slogan);
        mRating = (RatingStars)findViewById(R.id.rating_stars);
        mPrice = (TextView)findViewById(R.id.price);       
        mTime = (TextView)findViewById(R.id.time);
        
        view_no_download_time=(View)findViewById(R.id.layout_no_downloadtime);
        view_show_download_time=(View)findViewById(R.id.layout_show_downloadtime);
        mNickname = (TextView)findViewById(R.id.nickname);
        mName1 = (TextView)findViewById(R.id.name1);
//        mSlogan1 = (TextView)findViewById(R.id.slogan1);
        view_no_download_time_app_info=(View)findViewById(R.id.app_info);
        view_show_download_time_app_info=(View)findViewById(R.id.app_info1);
        mPrice1 = (TextView)findViewById(R.id.price1);
    }

    public void updateView(Cursor c) {
        String iconUrl = c.getString(c.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_ICON_URL));
        String author = c.getString(c.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_AUTHOR));
        String name = c.getString(c.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_NAME));
//        String slogan = c.getString(c.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_SLOGAN));
        int score = c.getInt(c.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_SCORE));
        int scoreCount = c.getInt(c.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_SCORE_CNT));
        String price = c.getString(c.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_PRICE));
        int status = c.getInt(c.getColumnIndexOrThrow(DatabaseSchema.TABLE_APP.COLUMN_STATUS));
        
        updateView(iconUrl, author, name, score, scoreCount, price, status, null,null);
    }
    
    public void logIconDrawable()
    {
    	MyLog.d(TAG, "===>Icon:" + mIcon);
    }

    public void updateView(App app) {
        updateView(
            app.getIconUrl(), 
            app.getAuthorName(), 
            app.getName(), 
//            app.getSlogan(), 
            app.getScore(), 
            app.getScoreCount(), 
            app.getPrice(), 
            app.getStatus(),
            app.getDownloadTime(),
            app.getNickName());
    }

    private void updateView(String iconUrl, String author, String name,
            int score, int scoreCount, String price, int status, String downloadTime,String nickname) {
        Context ctx = getContext();
        Resources res = ctx.getResources();
        
        String iconPath = DatabaseUtils.getLocalPathFromUrl(iconUrl);
        
        //MyLog.d(TAG, "iconUrl ================ '" + iconUrl + "'");
        
        // set icon
        if (GeneralUtil.needDisplayImg(mContext))
        {
	        if (iconPath != null && !"".equals(iconPath) && new File(iconPath).exists()) {
	            mIcon.setImageBitmap(DatabaseUtils.getImage(iconPath));
	        } else {
	            // reset first
	            mIcon.setImageResource(R.drawable.app_icon);
	            MyLog.d(TAG, "start load image: '" + iconUrl + "', imageview: " + mIcon) ;
	            ImageLoader.getInstance().loadBitmapOnThread(iconUrl, ctx, mIcon);
	        }
        }
        else
        	mIcon.setImageResource(R.drawable.app_icon);
        
//        ImageLoader.getInstance().loadImage(iconUrl, mIcon, R.drawable.app_icon);
        
        if (downloadTime == null) {
        	view_no_download_time.setVisibility(View.VISIBLE);
        	view_no_download_time_app_info.setVisibility(View.VISIBLE);
        	view_show_download_time.setVisibility(View.GONE);
        	view_show_download_time_app_info.setVisibility(View.GONE);
            mAuthor.setText(author);
            mName.setText(name);
//            mSlogan.setText(slogan);
        	mRating.setVisibility(View.VISIBLE);
            // rating star
            mRating.setStarParam(R.drawable.star_empty, R.drawable.star_fill);
            // Up -> 5 stars, Down -> 1 star
            mRating.setRating((scoreCount == 0) ? 0 : score / scoreCount);
            
      	    mTime.setVisibility(View.GONE);

		}
        else {
        	view_no_download_time.setVisibility(View.GONE);
        	view_no_download_time_app_info.setVisibility(View.GONE);
        	view_show_download_time.setVisibility(View.VISIBLE);
        	view_show_download_time_app_info.setVisibility(View.VISIBLE);
        	mNickname.setText(nickname);
            mName1.setText(name);
//            mSlogan1.setText(slogan);
        	mRating.setVisibility(View.GONE);        	
            mTime.setVisibility(View.VISIBLE);
            
            String mUpdateTime = GeneralUtil.getTextViewUpdateTime(getContext());
        	long diff=Long.parseLong(mUpdateTime)- Long.parseLong(downloadTime); 
        	MyLog.d(TAG, "mUpdateTime="+mUpdateTime+",downloadTime="+downloadTime);
        	MyLog.d(TAG, "diff=mUpdateTime-downloadTime>>>"+diff);
//        	long diff = (System.currentTimeMillis()/ 1000 - Long.parseLong(downloadTime)); 
        	
        	if (diff < 0) diff = 1;
        	
        	if (diff > 60 * 60 * 24)
        	{
        		diff = diff / (60 * 60 * 24);
        		mTime.setText(ctx.getString(R.string.download_time_day, diff + ""));
        	}	
        	else if (diff > 60 * 60)
        	{
        		diff = diff / (60 * 60);
        		mTime.setText(ctx.getString(R.string.download_time_hour, diff + ""));
        	}
        	else if (diff > 60)
        	{
        		diff = diff / 60;
        		mTime.setText(ctx.getString(R.string.download_time_min, diff + ""));
        	}
        	else
        		mTime.setText(ctx.getString(R.string.download_time_sec, diff + ""));
		}
//        mAuthor.setText(author);
//        mName.setText(name);
//        mSlogan.setText(slogan);


    	
//        if (downloadTime == null)
//        {
//        	mTime.setVisibility(View.GONE);
//        }
//        else
//        {
//        	mTime.setVisibility(View.VISIBLE);
//        	
//        	long diff = (System.currentTimeMillis()/ 1000 - Long.parseLong(downloadTime) ); 
//        	
//        	if (diff < 0) diff = 1;
//        	
//        	if (diff > 60 * 60 * 24)
//        	{
//        		diff = diff / (60 * 60 * 24);
//        		mTime.setText(ctx.getString(R.string.download_time_day, diff + ""));
//        	}	
//        	else if (diff > 60 * 60)
//        	{
//        		diff = diff / (60 * 60);
//        		mTime.setText(ctx.getString(R.string.download_time_hour, diff + ""));
//        	}
//        	else if (diff > 60)
//        	{
//        		diff = diff / 60;
//        		mTime.setText(ctx.getString(R.string.download_time_min, diff + ""));
//        	}
//        	else
//        		mTime.setText(ctx.getString(R.string.download_time_sec, diff + ""));
//        }

        // status
        switch (status) {
            case App.INIT:
                if ("0".equals(price)) {
                    mPrice.setText(R.string.free);
                    mPrice1.setText(R.string.free);
                } else {
                    mPrice.setText(res.getString(R.string.price, price));
                    mPrice1.setText(res.getString(R.string.price, price));
                }
                break;
            case App.DOWNLOADING:
                mPrice.setText(R.string.status_downloading);
                mPrice1.setText(R.string.status_downloading);
                break;
            case App.PAUSED:
            	mPrice.setText(R.string.status_paused);
            	mPrice1.setText(R.string.status_paused);
                break;
            case App.DOWNLOADED:
                mPrice.setText(R.string.status_downloaded);
                mPrice1.setText(R.string.status_downloaded);
                break;
            case App.INSTALLED:
                mPrice.setText(R.string.status_installed);
                mPrice1.setText(R.string.status_installed);
                break;
            case App.HAS_UPDATE:
                mPrice.setText(R.string.status_hasupdate);
                mPrice1.setText(R.string.status_hasupdate);
                break;
            default:
                throw new RuntimeException("Unknown status: " + status);
        }
    }
    
}
