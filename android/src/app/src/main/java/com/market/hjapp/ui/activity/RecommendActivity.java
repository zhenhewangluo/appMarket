package com.market.hjapp.ui.activity;

import com.market.hjapp.App;
import com.market.hjapp.ConstantValues;
import com.market.hjapp.GeneralUtil;
import com.market.hjapp.ImageLoader;
import com.market.hjapp.MyLog;
import com.market.hjapp.R;
import com.market.hjapp.Recommend;
import com.market.hjapp.database.DatabaseUtils;
import com.market.hjapp.service.AppService;
import com.market.hjapp.service.download.FileManager;
import com.market.hjapp.service.download.FileManipulation;
import com.market.hjapp.ui.adapter.ImageAdapter;
import com.market.hjapp.ui.tasks.LaunchAppTask;
import com.market.hjapp.ui.tasks.ProcessInstallTask;
import com.market.hjapp.ui.tasks.BaseAsyncTask.TaskResultListener;
import com.market.hjapp.ui.view.FancyProgressBar;
import com.market.hjapp.ui.view.LongButton;

import java.io.File;
import java.util.ArrayList;
import java.util.HashMap;
import java.util.concurrent.RejectedExecutionException;

import android.app.AlertDialog;
import android.app.NotificationManager;
import android.app.ProgressDialog;
import android.content.BroadcastReceiver;
import android.content.ComponentName;
import android.content.Context;
import android.content.DialogInterface;
import android.content.Intent;
import android.content.IntentFilter;
import android.content.SharedPreferences;
import android.net.ConnectivityManager;
import android.net.NetworkInfo;
import android.net.Uri;
import android.os.Bundle;
import android.os.Handler;
import android.os.Message;
import android.text.Html;
import android.text.TextUtils;
import android.text.method.LinkMovementMethod;
import android.view.View;
import android.view.View.OnClickListener;
import android.view.animation.AnimationUtils;
import android.widget.AdapterView;
import android.widget.Gallery;
import android.widget.ImageSwitcher;
import android.widget.ImageView;
import android.widget.TextView;
import android.widget.Toast;
import android.widget.ViewSwitcher;
import android.widget.Gallery.LayoutParams;

/**
 * RecommendActivity UI
 * @author Administrator
 *
 */
public class RecommendActivity extends BaseBottomTabActivity implements
		AdapterView.OnItemSelectedListener,ViewSwitcher.ViewFactory {
	/** Called when the activity is first created. */
	private static final String TAG = "RecommendActivity";
	private int is_first_sliding=0;
	Context mContext=RecommendActivity.this;  
	
	private ArrayList<Recommend> imageList;
	private ImageAdapter galleryAdapter;
	private ImageSwitcher mSwitcher;
    private Gallery mGallery;
    private DownloadStatusReceiver mDownloadReceiver;
    private Recommend mRecommend;
    private App mApp;
    private int mAppid;
    private String mParent;
    private TextView recommend_date ;
    private TextView recommend_app_name ;
    private TextView recommend_app_desc;
    
    protected static final String EXTRA_KEY_APPID = "appid";
    public static final int REQUEST_LOGIN = 101;
    public static final int REQUEST_SHOW_DETAILS = 102;
    public static final int REQUEST_PAY = 103;
    public static final int REQUEST_COMMENT = 104;

    static final int MESSAGE_UPDATE_STATUS = 201;

    View mBottomBar;
    TextView mBgText;
    
    
    private View mBottomButtons;
    private View mBottomProgress;
    
    private View mOneBtn;
    private View mTwoBtn;
    private View mThreeBtn;
    
    private LongButton mBtn1;
    private LongButton mBtn2;
    private LongButton mBtn3;
    
    private View mPauseBtn;
    private View mResumeBtn;
    private View mStopBtn;
    
    private FancyProgressBar mProgressBar;
    
    private SharedPreferences mNotificationSharedPreferences;
    private NotificationManager mNotificationManager;
    
    private Handler mHandler = new Handler() {
        @Override
        public void handleMessage(Message msg) {
            int what = msg.what;
            if (what == MESSAGE_UPDATE_STATUS) {
                int appstatus = msg.getData().getInt("status");
                int progress = msg.getData().getInt("progress");
                
                if (appstatus == App.DOWNLOADING)
                	setDownloadProgress(false, progress);
                else
                	updateStatus(appstatus, progress);
                
                return;
            }
            else if (what == MESSAGE_LOAD_MISSING_APP)
            {
            	int appid = msg.getData().getInt("appid");
				//get mApp by mAppid
			    mApp = DatabaseUtils.getAppById(mDb, appid);
			    //download app
			    doWithDownload();
			    return;
     
            }
            else if(what==MESSAGE_ERROR_NOT_FOUND_APP){
            	Toast.makeText(mContext, getString(R.string.error_not_found_recommend_app), Toast.LENGTH_LONG).show();
            	 return;
			}
            else if (what==MESSAGE_NETWORK_EXXEPTON) {
            	Toast.makeText(getApplicationContext(), getString(R.string.error_http_timeout), Toast.LENGTH_LONG).show();
            	return;
			}
            super.handleMessage(msg);
        }
    };
    private TaskResultListener mInstallTaskResultListener = new TaskResultListener() {

        @Override
        public void onTaskResult(boolean success, HashMap<String, Object> res) {
        	
        	if (!success) {
            	if (res == null)
            	{
            		Toast.makeText(getApplicationContext(), R.string.error_http_timeout, Toast.LENGTH_LONG).show();
            	}
            	else
            	{
            		String error = (String)res.get("errno");
                    if (error.equals("E008")) {
                        // Unauthorized access, need login
                        Intent i = new Intent(getApplicationContext(), LoginDialogActivity.class);
                        
                        i.putExtra("page_no", 32);
                        
                        i.putExtra("hint", getString(R.string.login_hint_download));
                        
                        startActivityForResult(i, REQUEST_LOGIN);
                    } else {
                        Toast.makeText(getApplicationContext(), (String)res.get("errmsg"), Toast.LENGTH_LONG).show();
                    }
            	}
                
                return;
            }

            String downloadPath = (String)res.get("location");
            String downloadId = (String)res.get("download_id");
            MyLog.d(TAG, "download id: " + downloadId + ", download path: " + downloadPath);
            
            if(mApp==null){

            	mApp = DatabaseUtils.getAppById(mDb, mAppid);
            	if (mApp==null) {
            		
				 	getAppInfoByAppid(mContext,mAppid,1);
				}
			}else {
				
	            mApp.setDownloadId(downloadId);
	            mApp.setDownloadPath(downloadPath);	

	            Intent intent = new Intent(getApplicationContext(), AppService.class);
	            intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_DOWNLOAD);
	            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, mApp.getDbId());
	            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_ID, mApp.getDownloadId());
	            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_URL, mApp.getDownloadPath());
	            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPID, mApp.getId());
	            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPNAME, mApp.getName());
	            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_COUNT, mApp.getDownloadCount());
	            
	            startService(intent);
			}
            
//            mApp.setDownloadId(downloadId);
//            mApp.setDownloadPath(downloadPath);	
//
//            Intent intent = new Intent(getApplicationContext(), AppService.class);
//            intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_DOWNLOAD);
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, mApp.getDbId());
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_ID, mApp.getDownloadId());
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_URL, mApp.getDownloadPath());
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPID, mApp.getId());
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_APPNAME, mApp.getName());
//            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_COUNT, mApp.getDownloadCount());
//            
//            startService(intent);

        }
        
    };
    

    private OnClickListener mInstallListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
        	saveUserLog(6);
        	
            int appId = mApp.getId();
//            int eventId = mNotificationSharedPreferences.getInt(appId, 1);
             
            mNotificationManager.cancel(appId);
            //  Start installing manual
            String path = DatabaseUtils.getInstallingAppLocalPath(mContext, appId);
            try {
                FileManager.install(path, mContext);
            } catch (Exception e) {
                MyLog.e(TAG, "Failed to install the file " + path);
            }                         
        }
        
    };

    private OnClickListener mPauseProgressListener = new OnClickListener() {
        @Override
        public void onClick(View v) {
        	saveUserLog(3);
        	synchronized(RecommendActivity.this)
        	{
	        	mPauseBtn.setVisibility(View.GONE);
	        	mResumeBtn.setVisibility(View.VISIBLE);
	        	
	        	
	        	Intent intent = new Intent(getApplicationContext(), AppService.class);
	            intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_PAUSE);
	            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, mApp.getDbId());
	            
	            startService(intent);
        	}
        }
    };

    private OnClickListener mResumeProgressListener = new OnClickListener() {
        @Override
        public void onClick(View v) {
        	saveUserLog(4);
        	
        	synchronized(RecommendActivity.this)
        	{
	        	mPauseBtn.setVisibility(View.VISIBLE);
	        	mResumeBtn.setVisibility(View.GONE);
	        	
	        	
	        	Intent intent = new Intent(getApplicationContext(), AppService.class);
	            intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_RESUME);
	            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, mApp.getDbId());
	            
	            startService(intent);
        	}
        }
    };

    private OnClickListener mStopProgressListener = new OnClickListener() {
        @Override
        public void onClick(View v) {
        	
        	saveUserLog(5);
        	
        	DatabaseUtils.resetAppToInit(mContext, mApp.getDbId());
            
            String localPath = DatabaseUtils.getInstallingAppLocalPath(mContext, mApp.getId());
            if (!TextUtils.isEmpty(localPath)) {
            	File currFile = new File(localPath);
                currFile.delete();
            }
            
        	updateStatus(App.INIT, 0);
        	
        	Intent intent = new Intent(getApplicationContext(), AppService.class);
            intent.putExtra(AppService.EXTRA_KEY_COMMAND, AppService.COMMAND_CANCEL);
            intent.putExtra(AppService.EXTRA_KEY_DOWNLOAD_DBID, mApp.getDbId());
            
            startService(intent);
        }
    };

    private OnClickListener mLaunchListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
        	
        	saveUserLog(10);
        	
        	try
        	{
	            new LaunchAppTask(RecommendActivity.this, new TaskResultListener() {
	
	                @Override
	                public void onTaskResult(boolean success, HashMap<String, Object> res) {
	                    if (success) {
	                        String pname = (String)res.get("package_name");
	                        String name = (String)res.get("name");
	                        
	                        Intent i = new Intent();
	                        i.setAction(Intent.ACTION_MAIN);
	                        i.addCategory(Intent.CATEGORY_LAUNCHER);
	                        i.setFlags(Intent.FLAG_ACTIVITY_NEW_TASK | Intent.FLAG_ACTIVITY_RESET_TASK_IF_NEEDED);
	                        i.setComponent(new ComponentName(pname, name));
	                        
	                        startActivity(i);
	                    }
	                }
	                
	            }).execute(mApp.getPackgeName());
        	} catch (RejectedExecutionException e) {
            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
            }
        }
        
    };

    private OnClickListener mUninstallListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
        	saveUserLog(8);
        	
            FileManipulation.uninstall(mContext, mApp.getPackgeName());
        }
        
    };
    
    private OnClickListener mDeleteListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
        	saveUserLog(7);
        	
            DatabaseUtils.resetAppToInit(mContext, mApp.getDbId());
            
            String localPath = DatabaseUtils.getInstallingAppLocalPath(mContext, mApp.getId());
            if (!TextUtils.isEmpty(localPath)) {
            	File currFile = new File(localPath);
                currFile.delete();
            }
            
            if (mApp.getDbId() != ConstantValues.CLIENT_DBID) {
                DatabaseUtils.resetAppToInit(mContext, mApp.getDbId());
            }
            
            int appId = mApp.getId();
            int eventId = appId;
             
            mNotificationManager.cancel(eventId);
            
            updateStatus(App.INIT, 0);
        }
        
    };

    private OnClickListener mUpdateListener = new OnClickListener() {

        @Override
        public void onClick(View v) {
        	
        	saveUserLog(9);
        	
        	if (System.currentTimeMillis() - downloadBtnCanBePressed < 2000)
        		return;
        	
        	downloadBtnCanBePressed = System.currentTimeMillis();
        	
        	try
        	{
	        	new ProcessInstallTask(RecommendActivity.this, mInstallTaskResultListener)
	            .execute(mApp.getId()+"", mApp.getPayId(), mParent);
        	} catch (RejectedExecutionException e) {
            	MyLog.e(TAG, "Got exception when execute asynctask!", e);
            }
        }
        
    };
    
    private long downloadBtnCanBePressed;
    
	private OnClickListener mDownloadListener = new OnClickListener() {

		@Override
		public void onClick(View v) {

			synchronized(RecommendActivity.this)
			{
				saveUserLog(2);
				if (mApp == null) {
	//				Toast.makeText(mContext,
	//						getString(R.string.error_not_found_recommend_app),
	//						Toast.LENGTH_LONG).show();
					getAppInfoByAppid(mContext,mAppid,1);
				} else {
					doWithDownload();
				}
			}
		}

	};
	/**
	 * do with download;
	 */
	private void doWithDownload() {
		if (System.currentTimeMillis() - downloadBtnCanBePressed < 2000)
			return;

		if (null==mApp) {
			Toast.makeText(this, "服务器无数据，请稍后再试", Toast.LENGTH_SHORT).show();
			return;
		}
		boolean isBigFile = GeneralUtil.isBigFile(mApp.getSize());

		NetworkInfo info = ((ConnectivityManager) getSystemService(Context.CONNECTIVITY_SERVICE))
				.getActiveNetworkInfo();

		// TODO:deal with no network
		if (info == null)
			return;

		if (isBigFile
				&& info.getType() != ConnectivityManager.TYPE_WIFI) {
			new AlertDialog.Builder(mContext).setTitle(
					R.string.prompt_title).setMessage(
					R.string.bigsize_file).setPositiveButton(
					R.string.network_setting,
					new DialogInterface.OnClickListener() {

						@Override
						public void onClick(DialogInterface dialog,
								int which) {
							startActivityForResult(
									new Intent(
											android.provider.Settings.ACTION_WIRELESS_SETTINGS),
									0);
						}
					}).setNegativeButton(R.string.continue_download,
					new DialogInterface.OnClickListener() {

						@Override
						public void onClick(DialogInterface dialog,
								int which) {
							doDownload();
						}

					}).create().show();
		} else
			doDownload();
	}
	private void doDownload() {
		// app is null,do nothing,

		downloadBtnCanBePressed = System.currentTimeMillis();

		float price = Float.parseFloat(mApp.getPrice());
		MyLog.d(TAG, "price is: " + price);

		if (price > 0) {
			processPayment(mApp.getId()+"", price + "");
		} else {
			try {
				updateStatus(App.DOWNLOADING, 0);
				new ProcessInstallTask(RecommendActivity.this,
						mInstallTaskResultListener).execute(mApp.getId()+"", mApp
						.getPayId(), mParent);
			} catch (RejectedExecutionException e) {
				MyLog.e(TAG, "Got exception when execute asynctask!", e);
			}
		}

	}
	@Override
	public void onCreate(Bundle savedInstanceState) {
		super.onCreate(savedInstanceState);
		MyLog.d(TAG, "Activity onCreate");
		//setContentView(R.layout.recommend_activity);
        //set init view
		saveUserLog(0);
        initView();
        mNotificationSharedPreferences = getSharedPreferences(ConstantValues.APP_INSTALL_NOTIFICATION, MODE_PRIVATE);
        mNotificationManager = (NotificationManager)getSystemService(NOTIFICATION_SERVICE);

        downloadBtnCanBePressed = 0;
        
//        GeneralUtil.saveUploadTime(this, ConstantValues.PREF_KEY_LAST_VIEW_RECOMMEND_PAGE_DAY);
        
     // into cateId=9995 SelectedHeaderTab
        Bundle bunde = this.getIntent().getExtras();
        MyLog.d(TAG, ""+this.getIntent().getExtras());
        if (bunde!=null) {
        	Boolean is_notification=bunde.getBoolean("is_notification");
        	MyLog.d(TAG, "+++++++++is_notification:"+is_notification);
        	if (is_notification) {
        		saveUserLog(12);
			}
        }
        setSelectedFooterTab(7);
        
        GeneralUtil.saveRecommendViewDay(this);

	}
	
	
	@Override
    protected void onResume() {
        super.onResume();
        MyLog.d(TAG, "Activity onResume");
        if(mApp!=null){
        	mApp = DatabaseUtils.getAppById(mDb, mAppid);
        	if (mApp != null)
        	{
        		int progress = Math.round((((float) mApp.getDownloadedSize() / mApp.getSize()) * 100));	        
        		updateStatus(mApp.getStatus(), progress);
        	}
        }
    }

    @Override
    protected void onStart() {
        super.onStart();
        MyLog.d(TAG, "Activity onStart");
        mDownloadReceiver = new DownloadStatusReceiver();
        IntentFilter downloadFilter = new IntentFilter();
        downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_COMPLETE);
        downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_UPDATE_FOR_DETAIL);
        downloadFilter.addAction(AppService.BROADCAST_DOWNLOAD_ERROR);
        
        registerReceiver(mDownloadReceiver, downloadFilter);
    }
    
    @Override
    protected void onStop() {
        unregisterReceiver(mDownloadReceiver);
        
        super.onStop();
        MyLog.d(TAG, "Activity onStop");
    }
    
    @Override
    protected void onDestroy() {
        super.onDestroy();
        MyLog.d(TAG, "Activity onDestroy");
    }
	/**
	 * init View
	 */
	 protected void initView() {
		    MyLog.d(TAG, "init view >>>");
	        // bottom buttons
	        mBottomBar = findViewById(R.id.bottombar);
	        mBottomButtons = findViewById(R.id.bottombar_buttons);
	        mBottomProgress = findViewById(R.id.bottombar_progress);
	        
	        mPauseBtn = findViewById(R.id.pause_btn);
	        mPauseBtn.setOnClickListener(mPauseProgressListener);
	        
	        mResumeBtn = findViewById(R.id.resume_btn);
	        mResumeBtn.setOnClickListener(mResumeProgressListener);
	        
	        mStopBtn = findViewById(R.id.stop_btn);
	        mStopBtn.setOnClickListener(mStopProgressListener);
	        
	        mProgressBar = (FancyProgressBar)findViewById(R.id.progress);

	        mOneBtn = (View)findViewById(R.id.one_button);
	        mTwoBtn = (View)findViewById(R.id.two_button);
	        mThreeBtn = (View)findViewById(R.id.three_button);

	        //xml Switcher 
			mSwitcher = (ImageSwitcher) findViewById(R.id.img_switcher);
			// ViewSwitcher.ViewFactory
			mSwitcher.setFactory(this);
			//set Animation
			mSwitcher.setInAnimation(AnimationUtils.loadAnimation(this,
					android.R.anim.slide_in_left));

			mSwitcher.setOutAnimation(AnimationUtils.loadAnimation(this,
					android.R.anim.slide_out_right));
			// set Padding
            mSwitcher.setPadding(10, 10, 10, 10);
			mSwitcher.setOnClickListener(new OnClickListener() {
				
				@Override
				public void onClick(View v) {
					
					saveUserLog(1);
					// TODO Auto-generated method stub
					MyLog.d(TAG, "mSwitcher appid="+mAppid+">>>mParent="+mParent);
					Intent i;
					if(mApp==null){
//						Toast.makeText(mContext, getString(R.string.error_not_found_recommend_app), Toast.LENGTH_LONG).show();
						getAppInfoByAppid(mContext,mAppid,0);
					}
					else {
						int target_type=mRecommend.getTargetType();// 0 is appId, other is cateId
						if (target_type==0 || "0".equals(target_type)) {
				           i = new Intent(getApplicationContext(), AppDetailActivity.class);						
						}else {
						   i = new Intent(getApplicationContext(), CategoryAppListActivity.class);
						}
			            i.putExtra(EXTRA_KEY_APPID, mAppid);
			            i.putExtra(EXTRA_KEY_PARENTNAME, mParent);
			            startActivity(i);
					}

					
				}
			});
			// xml Gallery
			mGallery= (Gallery) findViewById(R.id.mygallery);
			
	        // 1. is there data in memory?
		    imageList =new ArrayList<Recommend>();
	        // 2. is there data in db?
		    imageList = DatabaseUtils.getRecommendList(RecommendActivity.this, mDb); 
	        if (imageList==null || imageList.size()== 0) {
	            //throw new RuntimeException("Can't get recommend list");
	        	MyLog.e(TAG, "not found data ");
	        	View detail_layout= (View) findViewById(R.id.detail);
	        	detail_layout.setVisibility(View.INVISIBLE);
	        	return;
	        	
	        }			 
			//set galleryAdapter
	        mGallery.setOnItemSelectedListener(this);
	        MyLog.d(TAG, "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
			galleryAdapter=new ImageAdapter(this, imageList);
			mGallery.setAdapter(galleryAdapter);
			MyLog.d(TAG, "!!!!!!!!!!!!!!!!!!!!!!!!!!!!!");
//			mGallery.setSelection(imageList.size()/2);	
//	        mGallery.setSelection(0);
			

	 }
	 static final int MESSAGE_LOAD_MISSING_APP = 202;
	 static final int MESSAGE_ERROR_NOT_FOUND_APP = 203;
	 static final int MESSAGE_NETWORK_EXXEPTON = 204;
	 /**
	  * get app form server when the app not in app_table;
	  * @param ctx
	  * @param appid
	  * @param type   0 is click ImageSwitcher pic,1 is click download button
	  */
	 public void getAppInfoByAppid(final Context ctx,final int appid,final int type){
		 final ProgressDialog progressDialog= ProgressDialog.show(ctx, null, getText(R.string.processing), true);
		 new Thread( new Runnable() {
	    		public void run() {
	    			Boolean success = false;
	    			try {
		    			success=GeneralUtil.getAppInfoByAppid(ctx, String.valueOf(appid));
					} catch (Exception e) {
						// TODO: handle exception
						MyLog.e(TAG, "get exception>>>"+e.toString());
						MyLog.d(TAG, "--------------- GET EXCEPTION! --------- ");
					    Message m = mHandler.obtainMessage(MESSAGE_NETWORK_EXXEPTON); 
					    mHandler.sendMessage(m);
						if (progressDialog!=null) {
						    progressDialog.dismiss();
					     }
					}

	    			if (success) {
	    				if (type==0) {//stands for clickImageSwitcher pic;
		    				Intent i= new Intent(getApplicationContext(), AppDetailActivity.class);						
				            i.putExtra(EXTRA_KEY_APPID, appid);
				            i.putExtra(EXTRA_KEY_PARENTNAME, mParent);
				            startActivity(i);
						}else if(type==1){//stands for click download Button;
			    			if (progressDialog!=null) {
			    				 progressDialog.dismiss();
							}
			    			
			                Message m = mHandler.obtainMessage(MESSAGE_LOAD_MISSING_APP);
			        	    Bundle data = new Bundle();
			        	    data.putInt("appid", appid);
			        	    m.setData(data);
			        	    mHandler.sendMessage(m);
						}else {
							MyLog.d(TAG, "this is reserved for the interface,type="+type);
						}

					}
	    			else {
	    				// error not found recommend app;
		                Message m = mHandler.obtainMessage(MESSAGE_ERROR_NOT_FOUND_APP);
		        	    mHandler.sendMessage(m);
					}
	    			
	    			if (progressDialog!=null) {
	    				 progressDialog.dismiss();
					}

	    		}
	    	}).start();
		 
	 }
	@Override
	public void onItemSelected(AdapterView<?> parent, View view, int position,
			long id) {
	    MyLog.d(TAG, "Gallery onItemSelected>>>>>>>>>>>>>position="+position);
	    if (imageList==null || imageList.size()==0) {
			return;
		}
	    if (position!=0) {
	    	if(is_first_sliding==0){
	    		MyLog.d(TAG, "this is first sliding,need to save UserLog here");
//	    		saveUserLog(11);
	    	}
	    	is_first_sliding++;
		}
		// get Recommend app 		
		mRecommend=imageList.get(position);
		//set appid
		mAppid= Integer.parseInt(mRecommend.getTargetId());
		
		synchronized(RecommendActivity.this)
		{
			//get mApp by mAppid
			mApp = DatabaseUtils.getAppById(mDb, mAppid);
		}	
			
	    if(mApp==null){
	    	MyLog.e(TAG, "appid="+mAppid+",this mApp is null;");
	    	showProgress(false);
	        setButtonNumber(1);
	        mBtn1.setText(R.string.recommend_bottom_button_download);
	        mBtn1.setOnClickListener(mDownloadListener);
	    }
	    else {
	    	int app_status=mApp.getStatus();
	 	    MyLog.d(TAG, "mAppid="+mAppid+">>>>downloadedStatus="+app_status);
	 	    if (app_status==App.INIT) {
	 		    showProgress(false);
	 	        setButtonNumber(1);
	 	        mBtn1.setText(R.string.recommend_bottom_button_download);
	 	        mBtn1.setOnClickListener(mDownloadListener);
	 		}
	 	    else {
	 	        int progress = Math.round((((float) mApp.getDownloadedSize() / mApp.getSize()) * 100));
	 	        
	 	        updateStatus(mApp.getStatus(), progress);
	 		}
		}
		//set mParent
//		mParent=mRecommend.getName();
	    mParent=getString(R.string.daily_recommend);
	    
		String imageUrl = mRecommend.getImageUrl();
		String imagepath = DatabaseUtils.getLocalPathFromUrl(imageUrl);
		MyLog.d(TAG, "position:"+String.valueOf(position)+",imageUrl="+imageUrl+">>imagepath="+imagepath);
		
		//  set ImageSwitcher
		if (GeneralUtil.needDisplayImg(mContext))
		{
			if (imagepath != null && !"".equals(imagepath) && new File(imagepath).exists()) {
				//Bitmap b = BitmapFactory.decodeFile(imagepath);
				//mSwitcher.setImageBitmap(b);
				mSwitcher.setImageURI(Uri.parse(imagepath)); 
			} else {
				// reset first
				mSwitcher.setImageResource(R.drawable.default_recommend_img);
				ImageLoader.getInstance().loadBitmapOnThread(imageUrl, mContext, mSwitcher);
			}
		}
		else{
			mSwitcher.setImageResource(R.drawable.default_recommend_img);
		}
		
		// set recommend_date
		recommend_date = (TextView)findViewById(R.id.recommend_date);
		//position==0 is today recommend app,other is other day recommend app
		if (position==0) {
			recommend_date.setText(R.string.today_recommend);
		}
		else{
//			SimpleDateFormat format = new SimpleDateFormat("yyyy-MM-dd");
//			String formatTime=format.format(mRecommend.getDate());
//			recommend_date.setText(formatTime);
			recommend_date.setText(getString(R.string.date_recommend, mRecommend.getDate()));
		}

		// set recommend_app_name
		recommend_app_name = (TextView)findViewById(R.id.recommend_app_name);
		recommend_app_name.setText(mRecommend.getName());

		// set app_desc
		recommend_app_desc= (TextView)findViewById(R.id.recommend_app_desc);
		recommend_app_desc.setText(Html.fromHtml(mRecommend.getDesc()));
		recommend_app_desc.setMovementMethod(LinkMovementMethod.getInstance());
	}

	@Override
	public void onNothingSelected(AdapterView<?> parent) {

	}

	@Override
	public View makeView() {

//		ImageView image = new ImageView(this);
//
//		image.setBackgroundColor(0xFF000000);
//
//		image.setScaleType(ImageView.ScaleType.FIT_CENTER);
//
//		image.setLayoutParams(new ImageSwitcher.LayoutParams(
//				LayoutParams.FILL_PARENT, LayoutParams.FILL_PARENT));
//		return image;
		ImageView image = new ImageView(this);
		//image.setMinimumHeight(200);
		//image.setMinimumWidth(200);
		image.setScaleType(ImageView.ScaleType.FIT_XY);
		image.setLayoutParams(new ImageSwitcher.LayoutParams(
				LayoutParams.FILL_PARENT, LayoutParams.FILL_PARENT));
		return image;
	}

	@Override
	protected int getLayout() {
		// TODO Auto-generated method stub
//		requestWindowFeature(Window.FEATURE_NO_TITLE); //no title		
//		getWindow().setFlags(WindowManager.LayoutParams.FLAG_FULLSCREEN, 
//				WindowManager.LayoutParams.FLAG_FULLSCREEN);//full screen
		
//		int screenWidth; 
//		int screenHeight; 
//		WindowManager windowManager = getWindowManager(); 
//		Display display = windowManager.getDefaultDisplay(); 
//		screenWidth = display.getWidth(); 
//		screenHeight = display.getHeight(); 
//		MyLog.d(TAG, screenWidth+"×"+screenHeight);
		
		return R.layout.recommend_activity;
	}
	    
	    private void updateStatus(int status, int progress) {
	        MyLog.d(TAG, "update status " + status + " and progress " + progress);
	        mApp.setStatus(status);

	        // status
	        switch (status) {
	            case App.INIT:
	                showProgress(false);
	                setButtonNumber(1);
	                mBtn1.setText(R.string.bottom_button_download);
	                mBtn1.setOnClickListener(mDownloadListener);
	                break;
	            case App.DOWNLOADING:
	            	mPauseBtn.setVisibility(View.VISIBLE);
	            	mResumeBtn.setVisibility(View.GONE);
	            	
	                showProgress(true);
	                setDownloadProgress(false, progress);
	                break;
	            case App.PAUSED:
	            	mPauseBtn.setVisibility(View.GONE);
	            	mResumeBtn.setVisibility(View.VISIBLE);
	            	
	                showProgress(true);
	                setDownloadProgress(true, progress);
	                break;
	            case App.DOWNLOADED:
	            	MyLog.d(TAG, "mApp.getDownloadCount()" + mApp.getDownloadCount());
	                showProgress(false);
	                setButtonNumber(2);
	                mBtn1.setText(R.string.bottom_button_install);
	                mBtn1.setOnClickListener(mInstallListener);
	                mBtn2.setText(R.string.bottom_button_delete);
	                mBtn2.setOnClickListener(mDeleteListener);
	                break;
	            case App.INSTALLED:
	                showProgress(false);
	                setButtonNumber(2);
	                mBtn1.setText(R.string.bottom_button_launch);
	                mBtn1.setOnClickListener(mLaunchListener);
	                mBtn2.setText(R.string.bottom_button_uninstall);
	                mBtn2.setOnClickListener(mUninstallListener);
	                break;
	            case App.HAS_UPDATE:
	                showProgress(false);
	                setButtonNumber(3);
	                mBtn1.setText(R.string.bottom_button_update);
	                mBtn1.setOnClickListener(mUpdateListener);
	                mBtn2.setText(R.string.bottom_button_launch);
	                mBtn2.setOnClickListener(mLaunchListener);
	                mBtn3.setText(R.string.bottom_button_uninstall);
	                mBtn3.setOnClickListener(mUninstallListener);
	                break;
	            default:
	                //throw new RuntimeException("Unknown status: " + mApp.getStatus());
	                MyLog.e(TAG, "Unknown status: " + mApp.getStatus());
	        }
	    }

	    private void setDownloadProgress(boolean isPaused, int progress) {
	        mProgressBar.setProgress(isPaused, progress);
	    }

	    private void setButtonNumber(int i) {
	        MyLog.d(TAG, "set button number " + i);
	        
	        switch (i){
	            case 1:
	            	
	            	mOneBtn.setVisibility(View.VISIBLE);
	            	mTwoBtn.setVisibility(View.GONE);
	            	mThreeBtn.setVisibility(View.GONE);
	            	
	            	mBtn1 = (LongButton)findViewById(R.id.btn11);
	            	mBtn1.setBackgroundResource(R.drawable.btn_long_selector);
	                break;
	            case 2:
	            	mOneBtn.setVisibility(View.GONE);
	                mTwoBtn.setVisibility(View.VISIBLE);
	                mThreeBtn.setVisibility(View.GONE);
	                
	                mBtn1 = (LongButton)findViewById(R.id.btn21);
	                mBtn1.setBackgroundResource(R.drawable.btn_long_selector);
	                mBtn2 = (LongButton)findViewById(R.id.btn22);
	                mBtn2.setBackgroundResource(R.drawable.btn_long_selector);
	                
	                break;
	            case 3:
	            	mOneBtn.setVisibility(View.GONE);
	                mTwoBtn.setVisibility(View.GONE);
	                mThreeBtn.setVisibility(View.VISIBLE);

	                mBtn1 = (LongButton)findViewById(R.id.btn31);
	                mBtn2 = (LongButton)findViewById(R.id.btn32);
	                mBtn3 = (LongButton)findViewById(R.id.btn33);
	                mBtn1.setBackgroundResource(R.drawable.btn_long_selector);
	                mBtn2.setBackgroundResource(R.drawable.btn_long_selector);
	                mBtn3.setBackgroundResource(R.drawable.btn_long_selector);
	                break;
	            default:
	                //throw new RuntimeException("Unknow button number: " + i);
	            	MyLog.e(TAG, "Unknow button number: " + i);
	        }
	    }

	    private void showProgress(boolean showProgress) {
	        mBottomButtons.setVisibility(showProgress ? View.GONE : View.VISIBLE);
	        mBottomProgress.setVisibility(showProgress ? View.VISIBLE : View.GONE);
	    }

	    protected void processPayment(String appid, String price) {
	    	Intent i = new Intent(getApplicationContext(), PayActivity.class);
	        
	    	MyLog.d(TAG, "price is " + price);
	        MyLog.d(TAG, "appid is " + price);
	        
	        i.putExtra("price", price);
	        i.putExtra("shopId", "0");
	        i.putExtra("productId", appid);
	    	
	        startActivityForResult(i, REQUEST_PAY);
	    }
	    
	    @Override
	    protected void onActivityResult(int requestCode, int resultCode, Intent data) {
	    	
	    	
	    	if (requestCode == REQUEST_LOGIN) {
	            if (resultCode == RESULT_OK) {
	            	try
	            	{
		                new ProcessInstallTask(this, mInstallTaskResultListener)
		                    .execute(mApp.getId()+"", mApp.getPayId(), mParent);
	            	} catch (RejectedExecutionException e) {
	                	MyLog.e(TAG, "Got exception when execute asynctask!", e);
	                }
	            }
	        }
	        else if (requestCode == REQUEST_PAY)
	        {
	        	if (resultCode == RESULT_OK) {

	        		try
	        		{
		                new ProcessInstallTask(this, mInstallTaskResultListener)
		                    .execute(mApp.getId()+"", data.getStringExtra("payid"), mParent);
	        		} catch (RejectedExecutionException e) {
	                	MyLog.e(TAG, "Got exception when execute asynctask!", e);
	                }
	            }
	        	else
	        	{
	        		Toast.makeText(getApplicationContext(), R.string.error_pay_failed, Toast.LENGTH_LONG).show();
	        	}
	        	
	        }
	        else if (requestCode == REQUEST_COMMENT)
	        {
	        	if (resultCode == RESULT_OK) {
	        		// refresh data
	        		initView();
	            }
	        }

	        super.onActivityResult(requestCode, resultCode, data);
	    }

	    private class DownloadStatusReceiver extends BroadcastReceiver {

	        @Override
	        public void onReceive(Context context, Intent intent) {
	            final String action = intent.getAction();
	            MyLog.d(TAG, "DownloadStatusReceiver >>> onReceive >>> action: " + action);
	            
	            final int appid = intent.getIntExtra(AppService.DOWNLOAD_APP_PID, -1);
	            MyLog.d(TAG, "appid: " + appid + ", curid: " + mAppid);
	            
	            if (appid != mAppid) {
	            	MyLog.d(TAG, "appid not match, inId: " + appid + ", curId: " + mAppid);
	            	return;
	            }

	            Message m = mHandler.obtainMessage(MESSAGE_UPDATE_STATUS);
	            Bundle data = new Bundle();
	            data.putInt("appid", appid);
	            data.putString("action", action);

	            int appstatus = -1;
	            int progress = 0;
	            if (AppService.BROADCAST_DOWNLOAD_UPDATE_FOR_DETAIL.equals(action)) {
	                appstatus = App.DOWNLOADING;
	                progress = intent.getIntExtra(AppService.DOWNLOAD_PROGRESS_VALUE, 0);
	                data.putInt("progress", progress);
	                
	                MyLog.d(TAG, "update progress " + progress);
	            } else if (AppService.BROADCAST_DOWNLOAD_COMPLETE.equals(action)) {
	                appstatus = App.DOWNLOADED;
	                mApp.setDownloadCount(mApp.getDownloadCount() + 1);
	            } else if (AppService.BROADCAST_DOWNLOAD_ERROR.equals(action)) {
	                appstatus = App.INIT;
	            } else {
	                throw new RuntimeException("got unknown action: " + action);
	            }

	            data.putInt("status", appstatus);
	            m.setData(data);
	            
	            mHandler.sendMessage(m);
	        }
	        
	    }
	    
	    /**
	     * save user log
		 * @param action
		 * @return
	     */
	    private void saveUserLog(int action)
	    {

	    	GeneralUtil.saveUserLogType3(mContext, 41, action);
//			if (action==0) {
//				tracker.trackPageView("/"+TAG);
//			}
//			else {
//				tracker.trackEvent(""+3, ""+41, "", action);
//			}
	    	
	    }

}
