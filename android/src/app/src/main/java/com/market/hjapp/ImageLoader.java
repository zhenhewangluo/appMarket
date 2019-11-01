
package com.market.hjapp;
/**
 * <b>Description:</b><br />
 * <br />
 * <b>History:</b><br />
 * 1: 0927-change drawable to bitmap
 *
 * <br />
 * <b>Copyright:</b>
 * <br />
 * Copyright © 2009 VanceInfo  Ltd. 
 * <br />
 * All Rights Reserved.
 * @author stephen he
 * @version 
 * <br />
 */
import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.app.Activity;
import android.app.ProgressDialog;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.DialogInterface.OnCancelListener;
import android.graphics.Bitmap;
import android.graphics.BitmapFactory;
import android.graphics.drawable.BitmapDrawable;
import android.net.Uri;
import android.os.AsyncTask;
import android.text.TextUtils;
import android.widget.ImageSwitcher;
import android.widget.ImageView;

import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.ui.tasks.ImageLoaderTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;

/**
 * the imageloader is async to load image and update imageview.
 */
public class ImageLoader {
    private static final String TAG = "ImageLoader" ;
	
	public static final String IMAGELOADER_ACTION_LOAD_COMPLETE       = "imageload_complete";
	//herokf add
	protected HashMap<ImageView, Bitmap> mBitmaps  = new HashMap<ImageView, Bitmap>();
	protected HashMap<String, ImageView> mImageViewMap  = new HashMap<String, ImageView>();
	protected HashMap<String, ImageSwitcher> mImageSwitcherMap = new HashMap<String, ImageSwitcher>();
	protected HashMap<String, String> mImageViewTypeMap = new HashMap<String, String>();
	
	private final String IMAGE_VIEW = "image_view";
	private final String IMAGE_SWITCHER = "image_switcher";
	
	private Context mCtx;
	
//    public ImageLoader() {
//        mImageSwitcherMap=new HashMap<ImageSwitcher, String>();
//    }
    
    private static final ImageLoader instance = new ImageLoader();  
    
    private ImageLoader() {   
    }   
    
    public static ImageLoader getInstance() {   
        return instance;   
    }    
    
    
    private ArrayList<String> URLReadyList = new ArrayList<String>();
    private ArrayList<String> URLDownloadingList = new ArrayList<String>();
    
    
    private static boolean allImageViewsHasDownloadOver;
    /**所有的图片已经下载完成*/
    public static boolean isAllImageViewsHasDownloadOver() {
		return allImageViewsHasDownloadOver;
	}


	private int mAllDownloadCount;
    private int mDownloadingCount;
    private final int MAX_DOWNALOAD = 3;
    private DownOverListener downOverListener;
    public synchronized void loadBitmapOnThread(String url,
    		final Context ctx, ImageView imageView,DownOverListener downOverListener) {
    	MyLog.d(TAG, "start load image_the top: '" + url + "', imageview: " + imageView) ;
    	this.downOverListener = downOverListener;
    	if (imageView == null) {
    		MyLog.e(TAG, "image view is null!");
    		return;
    	}
    	
    	if (TextUtils.isEmpty(url) || url.equals("null")) {
    		MyLog.e(TAG, "Invalid url: " + url);
    		return ;
    	}
    	
    	mCtx = ctx;
    	
    	url = url.trim();
    	for (String u : mImageViewMap.keySet()) 
    	{
    		if (mImageViewMap.get(u) == imageView)
    		{
    			mImageViewMap.remove(u);
    			break;
    		}
    	}
    	mImageViewMap.put(url, imageView);
    	
    	if (mImageViewMap.get(url) == null)
    		mImageViewMap.put(url, imageView);
    	
    	mImageViewTypeMap.put(url, IMAGE_VIEW);
    	
    	
    	startDownload(url);
    	
    }
    public synchronized void loadBitmapOnThread(String url,
            final Context ctx, ImageView imageView) {
        MyLog.d(TAG, "start load image_the top: '" + url + "', imageview: " + imageView) ;
        	
        if (imageView == null) {
            MyLog.e(TAG, "image view is null!");
            return;
        }

        if (TextUtils.isEmpty(url) || url.equals("null")) {
            MyLog.e(TAG, "Invalid url: " + url);
            return ;
        }
        
        mCtx = ctx;

        url = url.trim();
        for (String u : mImageViewMap.keySet()) 
        {
        	if (mImageViewMap.get(u) == imageView)
        	{
        		mImageViewMap.remove(u);
        		break;
        	}
        }
    	mImageViewMap.put(url, imageView);
    	
    	if (mImageViewMap.get(url) == null)
    		mImageViewMap.put(url, imageView);
    		
    	mImageViewTypeMap.put(url, IMAGE_VIEW);
        
    	
    	startDownload(url);
        
    }
//    
//    public synchronized void loadImage(String url, ImageView imageView, int resId) 
//    { 
//    	
//    	String iconPath = DatabaseUtils.getLocalPathFromUrl(url);
//    	
//    	if (iconPath != null && !"".equals(iconPath) && new File(iconPath).exists()) {
////	            Bitmap b = BitmapFactory.decodeFile(iconPath);
//    		    imageView.setImageBitmap(DatabaseUtils.getImage(iconPath));
//        } else {
//	            // reset first
//        	imageView.setImageResource(resId);
//	        ImageLoader.getInstance().loadBitmapOnThread(url, mCtx, imageView);
//        }
//    }
    
    public synchronized void loadBitmapOnThread(String url,
          final Context ctx, ImageSwitcher imageSwitcher) {
    	
    	 MyLog.d(TAG, "start load image_the down: '" + url + "', imageSwitcher: " + imageSwitcher) ;
     	
         if (imageSwitcher == null) {
             MyLog.e(TAG, "image view is null!");
             return;
         }

         if (TextUtils.isEmpty(url) || url.equals("null")) {
             MyLog.e(TAG, "Invalid url: " + url);
             return ;
         }
         
         mCtx = ctx;
         
         url = url.trim();
         
         // remove switcher has been in ready list 
         for (int i=0; i < URLReadyList.size(); i++) 
         {
        	 String u = URLReadyList.get(i);
             if (mImageSwitcherMap.containsKey(u)) 
             {
            	 mImageSwitcherMap.remove(u);
            	 URLReadyList.remove(u);
            	 mImageViewTypeMap.remove(u);
             }
         }
         
         for (String u : mImageSwitcherMap.keySet()) 
         {
         	if (mImageSwitcherMap.get(u) == imageSwitcher)
         	{
         		mImageSwitcherMap.remove(u);
         		break;
         	}
         }
         mImageSwitcherMap.put(url, imageSwitcher);
         mImageViewTypeMap.put(url, IMAGE_SWITCHER);
         
         startDownload(url);
    }
    
    private void startDownload(String url)
    {
    	boolean hasInQue = false;
        
        for (String u : URLReadyList) {
            if (u.equals(url)) {
            	hasInQue = true;
            	break;
            }
        }
        
        for (String u : URLDownloadingList) {
            if (u.equals(url)) {
            	hasInQue = true;
            	break;
            }
        }
        
        if (hasInQue)
        {
        	MyLog.d(TAG, url + " has in Que") ;
        	return;
        }
        else
        {
        	URLReadyList.add(url);
        	
        	downloadNext();
        }
    }
    
    private void downloadNext()
    {
    	MyLog.d(TAG, "mDownloadingCount:" + mDownloadingCount);
    	if(URLReadyList != null && URLReadyList.size() > 0 && mDownloadingCount < MAX_DOWNALOAD)
    	{
    		mDownloadingCount ++;
    		mAllDownloadCount++;
    		MyLog.d(TAG, "do next!");
    		
    		String url = URLReadyList.get(URLReadyList.size()-1);
    		URLDownloadingList.add(url);
    		URLReadyList.remove(URLReadyList.size()-1);
//    		String ssUrl = URLReadyList.get(URLReadyList.size()-1);
//    		if(ssUrl.contains(",") ){
//    			ssUrl = ssUrl.replace(",", ";");
//    		}
//    		String url[] = ssUrl.split(";");
//    		Uri uri = Uri.parse(url[0]);
//    		String baseUrl = uri.getScheme() + "://" + uri.getAuthority() + "/";		
//    		String[] fullPathUrl = new String[url.length];
//    		fullPathUrl[0] = url[0];
//    		for (int i = 1 ; i < url.length ; i ++){
//    			URLDownloadingList.add(baseUrl + url[i]);
//        		URLReadyList.remove(baseUrl + url[i]);
//        		MyLog.d(TAG, "baseUrl + url[i]:" + baseUrl + url[i]);
//        		try {
//        			MyLog.d(TAG, "url:" + url);
//                	new ImageLoaderTask((Activity)mCtx, mImageLoaderTaskResultListener)
//                    	.execute(url[0]);
//                } catch (RejectedExecutionException e) {
//                	MyLog.e(TAG, "Got exception when load image!", e);
//                }
//    		}
    		
    		try {
    			MyLog.d(TAG, "url:" + url);
    			
            	new ImageLoaderTask((Activity)mCtx, mImageLoaderTaskResultListener)
                	.execute(url);
            } catch (RejectedExecutionException e) {
            	MyLog.e(TAG, "Got exception when load image!", e);
            }
            
    	}
    }
    
    
//    /**
//     * synchronized ImageSwitcher
//     * @param url
//     * @param ctx
//     * @param imageSwitcher
//     * @param imagetype
//     */
//    public void loadBitmapOnThread(final String url,
//            final Context ctx, final ImageSwitcher imageSwitcher, final int imagetype) {
//        MyLog.d(TAG, "start load imageSwitcher: '" + url + "', imageSwitcher: " + imageSwitcher) ;
//        if (imageSwitcher == null) {
//            MyLog.e(TAG, "imageSwitcher is null!");
//            return;
//        }
//
//        if (TextUtils.isEmpty(url) || url.equals("null")) {
//            MyLog.e(TAG, "Invalid url: " + url);
//            return ;
//        }
//
//        final String urlString = url.trim();
//
//        boolean hasInQue = false;
//        for (ImageSwitcher v : mImageSwitcherMap.keySet()) {
//            if (mImageSwitcherMap.get(v).equals(url)) {
//            	hasInQue = true;
//            	break;
//            }
//        }   
//        mImageSwitcherMap.put(imageSwitcher, urlString);
//        
//        if (hasInQue)
//        	return;
//        else
//        {
//        	try {
//            	new ImageLoaderTask((Activity)ctx, mImageSwitcherLoaderTaskResultListener)
//                	.execute(url);
//            } catch (RejectedExecutionException e) {
//            	MyLog.e(TAG, "Got exception when load imageSwitcher!", e);
//            }
//        }
//        
//    }
    
    
    /*
    public InputStream getInputStream(String urlString) throws MalformedURLException, Exception {

	    HttpParams params = new BasicHttpParams();
	    // add the timeout	
	    HttpConnectionParams.setConnectionTimeout(params, TIMEOUT);   
	    HttpConnectionParams.setSoTimeout(params, TIMEOUT);  
	         
        DefaultHttpClient httpClient = new DefaultHttpClient(params);
        HttpGet request = new HttpGet(urlString);
        HttpResponse response = httpClient.execute(request);

        if (response.getStatusLine().getStatusCode() != HttpStatus.SC_OK) {   
            request.abort();   
        } 
        return response.getEntity().getContent();
    }
    //*/
    
    
    private TaskResultListener mImageLoaderTaskResultListener = new TaskResultListener() {

		@Override
		public synchronized void onTaskResult(boolean success, HashMap<String, Object> res) {
			
			String url = (String)res.get("imageUrl");
			
			String type = null;
			if (mImageViewTypeMap != null)
				type = mImageViewTypeMap.get(url);
			
			if (success)
			{
				MyLog.d(TAG, "=== load image done ====");
				Bitmap drawable = (Bitmap)res.get("bitmap");
				if (drawable != null)
				{
					MyLog.d(TAG, "=== load image success ====");
				
//					saveImage(url, drawable);
					new SaveImageAsyncTask(mCtx, url, drawable).execute();
					
					if(type != null && type.equals(IMAGE_VIEW))
					{
						if (mImageViewMap != null)
						{
							ImageView view = mImageViewMap.get(url);
							if (view != null)
							{
								view.setImageBitmap(drawable);
								mBitmaps.put(view, drawable);
								allImageViewsHasDownloadOver = false;
								mAllDownloadCount--;
								MyLog.d(TAG, "set " + url + " to " + view);
							}
							else
								MyLog.d(TAG, "url is not match its view");
						}
						else
							MyLog.d(TAG, "=mImageViewMap2 is null=");
						if (mAllDownloadCount==0) {
							allImageViewsHasDownloadOver = true;
							if (null!=downOverListener) {
								downOverListener.onTaskResult(allImageViewsHasDownloadOver,mBitmaps);
							}
						}
					}
					else if(type != null && type.equals(IMAGE_SWITCHER))
					{
						if (mImageSwitcherMap != null)
						{
							ImageSwitcher switcher = mImageSwitcherMap.get(url);
							if (switcher != null)
							{
								switcher.setImageDrawable(new BitmapDrawable(drawable));
								MyLog.d(TAG, "set " + url + " to " + switcher);
							}
						}
					}
				}
				
			}
			else
			{
				MyLog.d(TAG, "=== load image failed ====");
			}
			
			
			MyLog.d(TAG, "=== mDownloadingCount : ====" + mDownloadingCount);
			mDownloadingCount --;
			
			if(type != null && type.equals(IMAGE_VIEW))
			{
				if (mImageViewMap != null)
				{
					mImageViewMap.remove(url);
					MyLog.d(TAG, "=== remove url & view ===");
				}
			}
			else if(type != null && type.equals(IMAGE_SWITCHER))
			{
				if (mImageSwitcherMap != null)
				{
					mImageSwitcherMap.remove(url);
					MyLog.d(TAG, "=== remove url & view ===");
				}
			}
			
			if(mImageViewTypeMap != null)
			{
				mImageViewTypeMap.remove(url);
			}
			
			if (URLDownloadingList != null && URLDownloadingList.size() > 0)
			{
				for (int i=URLDownloadingList.size()-1; i>=0; i--)
				{
					if (URLDownloadingList.get(i).equals(url))
					{
						URLDownloadingList.remove(i);
						MyLog.d(TAG, "=== download list ===");
						break;
					}
				}
			}
				
			downloadNext();
		}
    	
    };
    
    public void clearDownloadList() {
    	MyLog.d(TAG, "************");
    	MyLog.d(TAG, "clearDownloadList");
    	MyLog.d(TAG, "************");
    	
    	URLReadyList.clear();
    	mImageViewMap.clear();
    	mImageSwitcherMap.clear();
    	mImageViewTypeMap.clear();
    }
    
//    private synchronized void saveImage(String url, Bitmap drawable)
//    {
//    	new SaveImageAsyncTask(mCtx, url, drawable).execute();
//    }
    
    class SaveImageAsyncTask extends AsyncTask<String, Void, HashMap<String, Object>> {

        private Context mContext; 
        private String mUrl; 
        private Bitmap mDrawable; 
        
        public SaveImageAsyncTask(Context c, String url, Bitmap drawable) {
        	mContext = c;
        	mUrl = url;
            mDrawable = drawable;
        }

		@Override
		protected HashMap<String, Object> doInBackground(String... params) {
			DatabaseUtils.saveImage(mContext, mUrl, mDrawable);
			
			return null;
		}
        
    }
    
    
    public static interface DownOverListener {
        public void onTaskResult(boolean success, HashMap<ImageView, Bitmap> mBitmaps);
    }
}

